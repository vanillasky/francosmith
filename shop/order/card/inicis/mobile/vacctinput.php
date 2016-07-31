<?php

//*******************************************************************************
// FILE NAME : vacctinput.php
// DATE : 2010.08
// 이니시스 가상계좌 입금내역 처리demon으로 넘어오는 파라메터를 control 하는 부분 입니다.
//*******************************************************************************

//**********************************************************************************
//이니시스가 전달하는 가상계좌이체의 결과를 수신하여 DB 처리 하는 부분 입니다.
//필요한 파라메터에 대한 DB 작업을 수행하십시오.
//**********************************************************************************

@extract($_POST);

//**********************************************************************************
//  이부분에 로그파일 경로를 수정해주세요.

$INIpayHome = realpath(dirname(__FILE__).'/../');      // 이니페이 홈디렉터리

//**********************************************************************************


$TEMP_IP = $_SERVER['REMOTE_ADDR'];
$PG_IP  = substr($TEMP_IP,0, 10);

//if( $PG_IP == "203.238.37" || $PG_IP == "210.98.138" )  //PG에서 보냈는지 IP로 체크
{
        $logfile = fopen( $INIpayHome . "/log/result.log", "a+" );

        fwrite( $logfile,"************************************************\r\n");
        fwrite( $logfile,"ID_MERCHANT : ".$id_merchant."\r\n");
        fwrite( $logfile,"P_TID : ".$P_TID."\r\n");
        fwrite( $logfile,"P_TYPE : ".$P_TYPE."\r\n");
		fwrite( $logfile,"P_STATUS : ".$P_STATUS."\r\n");
		fwrite( $logfile,"P_AUTH_DT : ".$P_AUTH_DT."\r\n");
		fwrite( $logfile,"P_OID : ".$P_OID."\r\n");
		fwrite( $logfile,"P_FN_CD1 : ".$P_FN_CD1."\r\n");
		fwrite( $logfile,"P_FN_CD2 : ".$P_FN_CD2."\r\n");
		fwrite( $logfile,"P_FN_NM : ".$P_FN_NM."\r\n");
		fwrite( $logfile,"P_AMT : ".$P_AMT."\r\n");
        fwrite( $logfile,"P_RMESG1 : ".$P_RMESG1."\r\n");
        fwrite( $logfile,"P_UNAME : ".$P_UNAME."\r\n");
        fwrite( $logfile,"************************************************\r\n");

        fclose( $logfile );

include "../../../../lib/library.php";
include "../../../../conf/config.php";

//비동기 주문 시 결제취소
function cancel_inicis($ordno,$cardtno,$settlelog,$claimReason) {
	include SHOPROOT."/conf/pg.inicis.php";
	global $db;
	require_once(SHOPROOT.'/order/card/inicis/sample/INIpay41Lib.php');
	$inipay = new INIpay41;
	$inipay->m_inipayHome = SHOPROOT.'/order/card/inicis/'; // 이니페이 홈디렉터리
	$inipay->m_type = 'cancel'; // 고정
	$inipay->m_pgId = 'INIpayRECP'; // 고정
	$inipay->m_subPgIp = '203.238.3.10'; // 고정
	$inipay->m_keyPw = '1111'; // 키패스워드(상점아이디에 따라 변경)
	$inipay->m_debug = 'true'; // 로그모드('true'로 설정하면 상세로그가 생성됨.)
	$inipay->m_mid = $pg['id']; // 상점아이디
	$inipay->m_tid = $cardtno; // 취소할 거래의 거래아이디
	$inipay->m_cancelMsg = $claimReason; // 취소사유
	$inipay->m_uip = getenv('REMOTE_ADDR'); // 고정
	$inipay->startAction();

	$query = "update ".GD_ORDER." set settlelog	= concat(IFNULL(settlelog,''),'$settlelog') where ordno=".$ordno;
	$db->query($query);
}

// KB 모바일결제시 P_TYPE 이 ISP가 아닌 CARD로 전달되어 예외처리 (처리하는 순서는 아래와 동일)
if($P_TYPE == 'CARD') {
	include dirname(__FILE__).'/vacctinput_card.php';
	exit;
}

// ISP, 계좌이체의 경우 card_return.php를 거치지 않기때문에 네이버 마일리지 결제 승인 API 호출
if ($P_TYPE == 'ISP' || $P_TYPE == 'BANK') {
	include dirname(__FILE__).'/../../../../lib/naverNcash.class.php';
	$naverNcash = new naverNcash(true);
	if ($naverNcash->useyn == 'Y') {
		$ncashResult = $naverNcash->payment_approval($P_OID, true);
		if ($ncashResult === false) {
			exit("OK");
		}
	}
}

$ordno = $P_OID;
if (!$ordno) exit;

if($P_TYPE == "VBANK") //결제수단이 가상계좌이며
{
	if($P_STATUS != "02") //입금통보 "02" 가 아니면(가상계좌 채번 : 00 또는 01 경우)
	{
		echo "OK";
		exit();
	}
}

if($P_TYPE == "ISP") //결제수단이 ISP일때
{
	// PG결제 위변조 체크 및 유효성 체크
	if (forge_order_check($P_OID,$P_AMT) === false) {
		$claimReason = $P_RMESG1."->자동 결제취소(상품금액과 결제금액이 일치하지 않음.)";
		$settlelog = "
		----------------------------------------
		결제번호 : ".$P_TID."
		결제방식 : ".$P_TYPE."
		결과코드 : ".$P_STATUS."
		승인시간 : ".$P_AUTH_DT."
		주문번호 : ".$P_OID."
		금융사명 : ".$P_FN_NM."
		거래금액 : ".$P_AMT."
		결과내용 : ".$claimReason."
		----------------------------------------
		";
		cancel_inicis($ordno,$P_TID,$settlelog,$claimReason);
		exit('OK');
	}

	if($P_STATUS != "00") //성공 "00" 이 아니면
	{
		// ISP, 계좌이체 실패 시 네이버 마일리지 결제 승인 취소 API 호출
		if ($P_TYPE == 'ISP' || $P_TYPE == 'BANK') {
			if ($naverNcash->useyn == 'Y') $naverNcash->payment_approval_cancel($P_OID);
		}
		echo "OK";
		exit();
	}
}

$settlelog = "
----------------------------------------
입금확인 : PG단자동입금확인
승인시간 : ".$P_AUTH_DT."
결과코드 : ".$P_STATUS."
확인시간 : ".date('Y:m:d H:i:s')."
입금금액 : ".$P_AMT."
----------------------------------------
";

if($P_TYPE == "ISP"){
$settlelog = "
----------------------------------------
결제번호 : ".$P_TID."
결제방식 : ".$P_TYPE."
결과코드 : ".$P_STATUS."
승인시간 : ".$P_AUTH_DT."
주문번호 : ".$P_OID."
금융사명 : ".$P_FN_NM."
거래금액 : ".$P_AMT."
거래결과 : ".$P_RMESG1."
----------------------------------------
";
}
	### item check stock
	include "../../../../lib/cardCancel.class.php";
	$cancel = new cardCancel();
	if(!$cancel->chk_item_stock($ordno) && $cfg['stepStock'] == '1'){
		$cancel -> cancel_db_proc($ordno,$P_TID);
	}else{
		$query = "
		select * from
			".GD_ORDER." a
			left join ".GD_LIST_BANK." b on a.bankAccount = b.sno
		where
			a.ordno='$ordno'
		";
		$data = $db->fetch($query);

		### 결제 정보 저장
		$step = 1;

		### 실데이타 저장
		$db->query("
		update ".GD_ORDER." set cyn='y', cdt=now(),
			step		= '1',
			step2		= '',
			cardtno		= '$P_TID',
			settlelog	= concat(IFNULL(settlelog,''),'$settlelog')
		where ordno='$ordno'"
		);

		$db->query("update ".GD_ORDER_ITEM." set cyn='y', istep='1' where ordno='$ordno'");

		### 주문로그 저장
		orderLog($ordno,$r_step[$data[step]]." > ".$r_step[$step]);

		### 재고 처리
		setStock($ordno);

		// 상품구입시 적립금 사용
		if ($data['m_no'] && $data['emoney'] && $P_TYPE == 'ISP') {
			setEmoney($data['m_no'], -$data['emoney'], '상품구입시 적립금 결제 사용', $ordno);
		}

		### 입금확인메일
		sendMailCase($data[email],1,$data);

		### 입금확인SMS
		$dataSms = $data;
		sendSmsCase('incash',$data[mobileOrder]);

		// 가상계좌 입금통보 일때 네이버 마일리지 거래 확정 API 호출
		if ($P_TYPE == 'VBANK') {
			include dirname()."/../../../../lib/naverNcash.class.php";
			$naverNcash = new naverNcash();
			$naverNcash->deal_done($ordno);
		}
	}

//************************************************************************************

        //위에서 상점 데이터베이스에 등록 성공유무에 따라서 성공시에는 "OK"를 이니시스로
        //리턴하셔야합니다. 아래 조건에 데이터베이스 성공시 받는 FLAG 변수를 넣으세요
        //(주의) OK를 리턴하지 않으시면 이니시스 지불 서버는 "OK"를 수신할때까지 계속 재전송을 시도합니다
        //기타 다른 형태의 PRINT( echo )는 하지 않으시기 바랍니다

//      if (데이터베이스 등록 성공 유무 조건변수 = true)
//      {

                echo "OK";                        // 절대로 지우지마세요

//      }

//*************************************************************************************

}
?>