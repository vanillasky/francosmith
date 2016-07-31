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
		fwrite( $logfile,"P_AUTH_DT : ".$P_AUTH_DT."\r\n");
		fwrite( $logfile,"P_OID : ".$P_OID."\r\n");
		fwrite( $logfile,"P_FN_CD1 : ".$P_FN_CD1."\r\n");
		fwrite( $logfile,"P_FN_CD2 : ".$P_FN_CD2."\r\n");
		fwrite( $logfile,"P_FN_NM : ".$P_FN_NM."\r\n");
		fwrite( $logfile,"P_AMT : ".$P_AMT."\r\n");
		fwrite( $logfile,"P_TYPE : ".$P_TYPE."\r\n");
        fwrite( $logfile,"P_RMESG1 : ".$P_RMESG1."\r\n");
        fwrite( $logfile,"P_UNAME : ".$P_UNAME."\r\n");
        fwrite( $logfile,"************************************************\r\n");

        fclose( $logfile );

include "../../../../lib/library.php";
include "../../../../conf/config.php";

$ordno = $P_OID;
if (!$ordno) exit;

$settlelog = "
----------------------------------------
입금확인 : PG단자동입금확인
승인시간 : ".$P_AUTH_DT."
확인시간 : ".date('Y:m:d H:i:s')."
입금금액 : ".$P_AMT."
----------------------------------------
";
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

		### 실데이타 저장
		$db->query("
		update ".GD_ORDER." set cyn='y', cdt=now(),
			step		= '1',
			step2		= '',
			cardtno		= '$P_TID',
			settlelog	= concat(settlelog,'$settlelog')
		where ordno='$ordno'"
		);
		$db->query("update ".GD_ORDER_ITEM." set cyn='y', istep='1' where ordno='$ordno'");

		### 주문로그 저장
		orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

		### 재고 처리
		setStock($ordno);

		### 입금확인메일
		sendMailCase($data[email],1,$data);

		### 입금확인SMS
		$dataSms = $data;
		sendSmsCase('incash',$data[mobileOrder]);
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