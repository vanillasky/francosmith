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

//--- INIpay 경로
$INIpayHome = realpath(dirname(__FILE__).'/../');      // 이니페이 홈디렉터리

//--- PG IP
$PGIP = $_SERVER['REMOTE_ADDR'];

//--- PG에서 보냈는지 IP로 체크
//if($PGIP == "211.219.96.165" || $PGIP == "118.129.210.25")
{
	// 로그 저장 (이니시스 로그로 파일로 저장 이니시스의 모든 값을 저장)
	$logfile		= fopen( $INIpayHome . '/log/INI_mx_rnoti_'.date('Ymd').'.log', 'a+' );
	$logInfo	 = '------------------------------------------------------------------------------'.chr(10);
	$logInfo	.= 'INFO	['.date('Y-m-d H:i:s').']	START Order log'.chr(10);
	foreach ($_POST as $key => $val) {
		$logInfo	.= 'DEBUG	['.date('Y-m-d H:i:s').']	'.$key.'	: '.$val.chr(10);
	}
	$logInfo	.= 'DEBUG	['.date('Y-m-d H:i:s').']	IP	: '.$_SERVER['REMOTE_ADDR'].chr(10);
	$logInfo	.= 'INFO	['.date('Y-m-d H:i:s').']	END Order log'.chr(10);
	$logInfo	.= '------------------------------------------------------------------------------'.chr(10).chr(10);
	fwrite( $logfile, $logInfo);
	fclose( $logfile );


	// 이니시스 NOTI 서버에서 받은 Value
	$P_TID;				// 거래번호
	$P_MID;				// 상점아이디
	$P_AUTH_DT;			// 승인일자
	$P_STATUS;			// 거래상태 (00:성공, 01:실패)
	$P_TYPE;			// 지불수단
	$P_OID;				// 상점주문번호
	$P_FN_CD1;			// 금융사코드1
	$P_FN_CD2;			// 금융사코드2
	$P_FN_NM;			// 금융사명 (은행명, 카드사명, 이통사명)
	$P_AMT;				// 거래금액
	$P_UNAME;			// 결제고객성명
	$P_RMESG1;			// 결과코드
	$P_RMESG2;			// 결과메시지
	$P_NOTI;			// 노티메시지(상점에서 올린 메시지)
	$P_AUTH_NO;			// 승인번호

	$P_TID			= $_REQUEST[P_TID];
	$P_MID			= $_REQUEST[P_MID];
	$P_AUTH_DT		= $_REQUEST[P_AUTH_DT];
	$P_STATUS		= $_REQUEST[P_STATUS];
	$P_TYPE			= $_REQUEST[P_TYPE];
	$P_OID			= $_REQUEST[P_OID];
	$P_FN_CD1		= $_REQUEST[P_FN_CD1];
	$P_FN_CD2		= $_REQUEST[P_FN_CD2];
	$P_FN_NM		= $_REQUEST[P_FN_NM];
	$P_AMT			= $_REQUEST[P_AMT];
	$P_UNAME		= $_REQUEST[P_UNAME];
	$P_RMESG1		= $_REQUEST[P_RMESG1];
	$P_RMESG2		= $_REQUEST[P_RMESG2];
	$P_NOTI			= $_REQUEST[P_NOTI];
	$P_AUTH_NO		= $_REQUEST[P_AUTH_NO];

	if($P_TYPE == "CARD") //결제수단이 ISP일때
	{
		// PG결제 위변조 체크 및 유효성 체크
		if (forge_order_check($P_OID,$P_AMT) === false) {
			$claimReason = $P_RMESG1."->자동 결제취소(상품금액과 결제금액이 일치하지 않음.)";
			$settlelog	= '';
			$settlelog	.= '===================================================='.chr(10);
			$settlelog	.= 'PG명 : 이니시스 - INIpay Mobile'.chr(10);
			$settlelog	.= '주문번호 : '.$P_OID.chr(10);
			$settlelog	.= '거래번호 : '.$P_TID.chr(10);
			$settlelog	.= '결과코드 : '.$P_STATUS.chr(10);
			$settlelog	.= '결과내용 : '.$claimReason.chr(10);
			$settlelog	.= '지불방법 : '.$P_TYPE.chr(10);
			$settlelog	.= '승인금액 : '.$P_AMT.chr(10);
			$settlelog	.= '승인일자 : '.$P_AUTH_DT.chr(10);
			$settlelog	.= '승인번호 : '.$P_AUTH_NO.chr(10);
			$settlelog	.= ' --------------------------------------------------'.chr(10);
			cancel_inipay($P_OID,$P_TID,$settlelog,$claimReason);
			exit('OK');
		}

		if($P_STATUS != "00") //성공 "00" 이 아니면
		{
			// ISP, 계좌이체 실패 시 네이버 마일리지 결제 승인 취소 API 호출
			if ($P_TYPE == 'CARD') {
				if ($naverNcash->useyn == 'Y') $naverNcash->payment_approval_cancel($P_OID);
			}

			// 주문결제실패 상태 변경
			$settlelog = "$P_OID (".date('Y:m:d H:i:s').")
----------------------------------------
거래번호 : ".$P_TID."
결과코드 : ".$P_STATUS."
결과내용 : ".$P_RMESG1."
지불방법 : ".$P_TYPE."
승인금액 : ".$P_AMT."
----------------------------------------";

			$db->query("update ".GD_ORDER." set step2=54, settlelog=concat(ifnull(settlelog,''),'$settlelog'),cardtno='".$P_TID."' where ordno='$P_OID'");
			$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$P_OID'");

			echo "OK";
			exit();
		}
	}

	$ordno = $P_OID;
	if (!$ordno) exit;

	//--- 결제 방법
	$pgPayMethod	= array(
			'CARD'			=> '신용카드',
			'ISP'			=> '신용카드',
			'BANK'			=> '실시간계좌이체',
			'MOBILE'		=> '핸드폰',
			'VBANK'			=> '무통장입금(가상계좌)',
	);

	//--- 로그 생성
	$settlelog	= '';
	$settlelog	.= '===================================================='.chr(10);
	$settlelog	.= 'PG명 : 이니시스 - INIpay Mobile'.chr(10);
	$settlelog	.= '주문번호 : '.$ordno.chr(10);
	$settlelog	.= '거래번호 : '.$P_TID.chr(10);
	$settlelog	.= '결과코드 : '.$P_STATUS.chr(10);
	$settlelog	.= '결과내용 : '.$P_RMESG1.' '.$P_RMESG2.chr(10);
	$settlelog	.= '지불방법 : '.$P_TYPE.' - '.$pgPayMethod[$P_TYPE].chr(10);
	$settlelog	.= '승인금액 : '.$P_AMT.chr(10);
	$settlelog	.= '승인일자 : '.$P_AUTH_DT.chr(10);
	$settlelog	.= '승인번호 : '.$P_AUTH_NO.chr(10);
	$settlelog	.= ' --------------------------------------------------'.chr(10);

	//--- 승인여부 / 결제 방법에 따른 처리 설정
	switch ($P_TYPE){
		case "CARD":
			$settlelog	.= '카드사명 : '.$P_FN_NM.chr(10);
			break;

		case 'BANK':
			$settlelog	.= '은행명 : '.$P_FN_NM.chr(10);
		break;

		case "VBANK":
			$settlelog	.= '입금확인 : PG단자동입금확인'.chr(10);
			$settlelog	.= '결제고객 : '.$P_UNAME.chr(10);
		break;
	}

	$settlelog	= '===================================================='.chr(10).'결제자동확인 : 결제확인시간('.date('Y-m-d H:i:s').')'.chr(10).$settlelog.'===================================================='.chr(10);

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

	/***********************************************************************************
	' 위에서 상점 데이터베이스에 등록 성공유무에 따라서 성공시에는 "OK"를 이니시스로 실패시는 "FAIL" 을
	' 리턴하셔야합니다. 아래 조건에 데이터베이스 성공시 받는 FLAG 변수를 넣으세요
	' (주의) OK를 리턴하지 않으시면 이니시스 지불 서버는 "OK"를 수신할때까지 계속 재전송을 시도합니다
	' 기타 다른 형태의 echo "" 는 하지 않으시기 바랍니다
	'***********************************************************************************/

	// if(데이터베이스 등록 성공 유무 조건변수 = true)
	echo "OK"; //절대로 지우지 마세요
	// else
	//	 echo "FAIL";
}
?>