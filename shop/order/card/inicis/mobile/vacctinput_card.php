<?php

// ISP, 계좌이체의 경우 card_return.php를 거치지 않기때문에 네이버 마일리지 결제 승인 API 호출
if ($P_TYPE == 'CARD') {
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

if($P_TYPE == "CARD") //kb국민 앱카드 결제시
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
		if ($P_TYPE == 'CARD') {
			if ($naverNcash->useyn == 'Y') $naverNcash->payment_approval_cancel($P_OID);
		}

		// 주문결제실패 상태 변경
		$settlelog = "$ordno (".date('Y:m:d H:i:s').")
----------------------------------------
거래번호 : ".$P_TID."
결과코드 : ".$P_STATUS."
결과내용 : ".$P_RMESG1."
지불방법 : ".$P_TYPE."
승인금액 : ".$P_AMT."
----------------------------------------";

		$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog'),cardtno='".$P_TID."' where ordno='$ordno'");
		$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno'");

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

if($P_TYPE == "CARD"){
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
		if ($data['m_no'] && $data['emoney'] && $P_TYPE == 'CARD') {
			setEmoney($data['m_no'], -$data['emoney'], '상품구입시 적립금 결제 사용', $ordno);
		}

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

?>