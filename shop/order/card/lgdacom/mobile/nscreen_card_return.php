<?php
/**
 * LG 유플러스 PG 모듈 (nScreen 용, nScreen 결제의 경우 무조건 PC용 결제 설정을 사용함)
 * 원본 파일명 payres.php , note_url.php
 * LG 유플러스 PG 버전 : LG U+ 표준결제창 2.5 - SmartXPay(V1.2 - 20141212)
 * @author artherot @ godosoft development team.
 */

// 기본 설정 정보
include "../../../../lib/library.php";
include "../../../../conf/config.mobileShop.php";
include "../../../../conf/config.php";
@include "../../../../conf/pg.lgdacom.php";

// 에러 리포트 수정
error_reporting(E_ALL ^ E_NOTICE);

// 페이지 타입
$page_type		= $_GET['page_type'];

// 페이지 타입에 따른 리턴 페이지 처리
if($page_type == 'mobile') {
	$order_end_page		= $cfgMobileShop['mobileShopRootDir'].'/ord/order_end.php';
	$order_fail_page	= $cfgMobileShop['mobileShopRootDir'].'/ord/order_fail.php';
} else {
	$order_end_page		= $cfg['rootDir'].'/order/order_end.php';
	$order_fail_page	= $cfg['rootDir'].'/order/order_fail.php';
}

// 로그 저장
if (function_exists('pg_data_log_write')) {
	$logPath	= '../../../../log/lgdacom/';
	pg_data_log_write($_POST, 'lguplus_nScreen', $logPath);
}

// 동기, 비동기 방식에 따른 설정
$isAsync		= $_GET['isAsync'];			// 동기방식여부 :비동기(ISP) , 그외 동기
$isSuccess		= false;					// 결제성공여부

// 네이버 마일리지 체크
$naverNcashClass	= $_SERVER['DOCUMENT_ROOT'].$cfg['rootDir'].'/lib/naverNcash.class.php';
$naverNcashCheck	= false;
if (is_file($naverNcashClass)) {
	$naverNcashCheck	= true;
}

// 네이버 마일리지 Class가 있는 경우 API 설정
if ($naverNcashCheck === true) {
	include $naverNcashClass;
	$naverNcash	= new naverNcash(true);
	if ($naverNcash->useyn == 'Y') {
		if ($_POST['LGD_PAYTYPE'] == "SC0040") {
			$ncashResult	= $naverNcash->payment_approval($_POST['LGD_OID'], false);
		} else {
			$ncashResult	= $naverNcash->payment_approval($_POST['LGD_OID'], true);
		}
		if ($ncashResult === false) {
			if ($isAsync == 'Y') {	//비동기일 경우(ISP결제)
				echo "ROLLBACK";	//OK가 아닌경우 ROLLBACK처리 됨. (결제 자동승인취소)
				exit();
			} else {
				msg('네이버 마일리지 사용에 실패하였습니다.', $order_fail_page.'?ordno='.$_POST['LGD_OID']);
				exit();
			}
		}
	}
}

// PG결제 위변조 체크 및 유효성 체크
if (function_exists('forge_order_check')) {
	if (forge_order_check($_POST['LGD_OID'], $_POST['LGD_AMOUNT']) === false) {
		if ($isAsync == 'Y') {	//비동기일 경우(ISP결제)
			echo "ROLLBACK";	//OK가 아닌경우 ROLLBACK처리 됨. (결제 자동승인취소)
			exit();
		} else {
			msg('주문 정보와 결제 정보가 맞질 않습니다. 다시 결제 바랍니다.', $order_fail_page.'?ordno='.$_POST['LGD_OID']);
			exit();
		}
	}
}

/*
 *************************************************
 * 1. 비동기 방식의 결제인경우 (ISP결제)
 *************************************************
 */
if ($isAsync == 'Y') {
	// LG 유플러스에서 결제 결과값인 POST를 전부 키이름과 동일한 변수로 처리를 함
	extract($_POST);

	// 상점키
	$LGD_MERTKEY	= $pg['mertkey'];

	// MD5 해쉬암호화
	$LGD_HASHDATA2	= md5($LGD_MID.$LGD_OID.$LGD_AMOUNT.$LGD_RESPCODE.$LGD_TIMESTAMP.$LGD_MERTKEY);

	/*
	 * 상점 처리결과 리턴메세지
	 *
	 * OK   : 상점 처리결과 성공
	 * 그외 : 상점 처리결과 실패
	 *
	 * ※ 주의사항 : 성공시 'OK' 문자이외의 다른문자열이 포함되면 실패처리 되오니 주의하시기 바랍니다.
	 */
	$resultMSG = "결제결과 상점 DB처리(NOTE_URL) 결과값을 입력해 주시기 바랍니다.";

	//해쉬값 검증이 성공하면
	if ($LGD_HASHDATA2 == $LGD_HASHDATA) {
		//결제가 성공이면
		if($LGD_RESPCODE == '0000'){
			$isSuccess	= true;
			$resultMSG	= 'OK';
		}
		//결제가 실패이면
		else {
			$resultMSG	= $LGD_RESPMSG;
		}
	}
	//해쉬값 검증이 실패이면
	else {
		$resultMSG		= "결제결과 상점 DB처리(NOTE_URL) 해쉬값 검증이 실패하였습니다.";
	}
}

/*
 *************************************************
 * 2. 동기 방식의 결제인경우
 *************************************************
 */
else {
	// 기본값 설정
	$configPath			= $_SERVER['DOCUMENT_ROOT'].$cfg['rootDir'].'/conf/lgdacom';		// LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf") 위치 지정.

	// LG유플러스 아이디 처리
	if (empty($pg['serviceType'])) {
		$pg['serviceType']	= 'service';
	}
	if ($pg['serviceType'] == 'test') {
		$LGD_MID	= 't'.$pg['id'];
	} else {
		$LGD_MID	= $pg['id'];
	}

	$CST_PLATFORM		= $pg['serviceType'];
    $CST_MID			= $pg['id'];
    $LGD_PAYKEY			= $_POST['LGD_PAYKEY'];

    require_once("./nscreen_XPayClient.php");
    $xpay = &new XPayClient($configPath, $CST_PLATFORM);
    $xpay->Init_TX($LGD_MID);

    $xpay->Set('LGD_TXNAME', 'PaymentByKey');
    $xpay->Set('LGD_PAYKEY', $LGD_PAYKEY);

    // 금액을 체크하시기 원하는 경우 아래 주석을 풀어서 이용하십시요.
	// $DB_AMOUNT = 'DB나 세션에서 가져온 금액'; //반드시 위변조가 불가능한 곳(DB나 세션)에서 금액을 가져오십시요.
	// $xpay->Set('LGD_AMOUNTCHECKYN', 'Y');
	// $xpay->Set('LGD_AMOUNT', $DB_AMOUNT);

	// 주문번호
	$LGD_OID			= $_POST['LGD_OID'];

	// 동기방식 결제 처리 API 실행
	if ($xpay->TX()) {
		// 결과값
		$Response_Code		= $xpay->Response_Code();
		$Response_Msg		= $xpay->Response_Msg();

		// LG 유플러스에서 결제 결과값을 전부 키이름과 동일한 변수로 처리를 함
		$tmp				= array();
		$tmp_ArrLGResult	= $xpay->Response_Names();
		foreach($tmp_ArrLGResult as $name) {
			$tmp[$name]		= $xpay->Response($name, 0);
		}
		extract($tmp);
		unset($tmp);
		unset($tmp_ArrLGResult);

		if( $xpay->Response_Code() == '0000') {
			$isSuccess		= true;
		} else {
			$resultMSG		= '결제실패';
		}
    } else {
		// API 요청실패 화면처리
		$resultMSG			= '결제실패';
		$resultMSG			.= '결제요청이 실패하였습니다.  <br>';
		$resultMSG			.= 'TX Response_code = ' . $xpay->Response_Code() . '<br>';
		$resultMSG			.= 'TX Response_msg = ' . $xpay->Response_Msg() . '<p>';
    }
}

/*
 *************************************************
 * 3. 결제 로그 처리
 *************************************************
 */
$ordno		= $LGD_OID;					// 주문번호

// 결제 수단
if($LGD_PAYTYPE=='SC0010') $payTypeStr = "신용카드";
if($LGD_PAYTYPE=='SC0030') $payTypeStr = "계좌이체";
if($LGD_PAYTYPE=='SC0040') $payTypeStr = "가상계좌";
if($LGD_PAYTYPE=='SC0060') $payTypeStr = "핸드폰";

// 결제 로그 처리
$tmp_log	= array();
$tmp_log[]	= "LG U+ SmartXPay (표준결제창 2.5) 결제요청에 대한 결과(nScreen)";
if($Response_Code)	$tmp_log[]	= "TX Response_code : ".$Response_Code;
if($Response_Msg)	$tmp_log[]	= "TX Response_msg : ".$Response_Msg;
$tmp_log[]	= "결과코드 : ".$LGD_RESPCODE." (0000(성공) 그외 실패)";
$tmp_log[]	= "결과내용 : ".$LGD_RESPMSG."\n".$resultMSG;
$tmp_log[]	= "해쉬데이타 : ".$LGD_HASHDATA;
$tmp_log[]	= "결제금액 : ".$LGD_AMOUNT;
$tmp_log[]	= "상점아이디 : ".$LGD_MID;
$tmp_log[]	= "거래번호 : ".$LGD_TID;
$tmp_log[]	= "주문번호 : ".$LGD_OID;
$tmp_log[]	= "결제방법 : ".$payTypeStr;
$tmp_log[]	= "결제일시 : ".$LGD_PAYDATE;
$tmp_log[]	= "거래번호 : ".$LGD_TID;
$tmp_log[]	= "에스크로 적용 여부 : ".$LGD_ESCROWYN;
$tmp_log[]	= "결제기관코드 : ".$LGD_FINANCECODE;
$tmp_log[]	= "결제기관명 : ".$LGD_FINANCENAME;

switch ($LGD_PAYTYPE){
	case "SC0010":	// 신용카드
		$tmp_log[]	= "결제기관승인번호 : ".$LGD_FINANCEAUTHNUM;
		$tmp_log[]	= "신용카드번호 : ".$LGD_CARDNUM." (일반 가맹점은 *처리됨)";
		$tmp_log[]	= "신용카드할부개월 : ".$LGD_CARDINSTALLMONTH;
		$tmp_log[]	= "신용카드무이자여부 : ".$LGD_CARDNOINTYN." (1:무이자, 0:일반)";
		break;
	case "SC0030":	// 계좌이체
		if($LGD_CASHRECEIPTNUM)		$tmp_log[]	= "현금영수증승인번호 : ".$LGD_CASHRECEIPTNUM;
		if($LGD_CASHRECEIPTSELFYN)	$tmp_log[]	= "현금영수증자진발급제유무 : ".$LGD_CASHRECEIPTSELFYN." (Y: 자진발급)";
		if($LGD_CASHRECEIPTKIND)	$tmp_log[]	= "현금영수증종류 : ".$LGD_CASHRECEIPTKIND." (0:소득공제, 1:지출증빙)";
		if($LGD_ACCOUNTOWNER)		$tmp_log[]	= "계좌소유주이름 : ".$LGD_ACCOUNTOWNER;
		break;
	case "SC0040":	// 가상계좌
		if($LGD_CASHRECEIPTNUM)		$tmp_log[]	= "현금영수증승인번호 : ".$LGD_CASHRECEIPTNUM;
		if($LGD_CASHRECEIPTSELFYN)	$tmp_log[]	= "현금영수증자진발급제유무 : ".$LGD_CASHRECEIPTSELFYN." (Y: 자진발급)";
		if($LGD_CASHRECEIPTKIND)	$tmp_log[]	= "현금영수증종류 : ".$LGD_CASHRECEIPTKIND." (0:소득공제, 1:지출증빙)";
		if($LGD_ACCOUNTNUM)			$tmp_log[]	= "가상계좌발급번호 : ".$LGD_ACCOUNTNUM;
		if($LGD_PAYER)				$tmp_log[]	= "가상계좌입금자명 : ".$LGD_PAYER;
		if($LGD_CASTAMOUNT)			$tmp_log[]	= "입금누적금액 : ".$LGD_CASTAMOUNT;
		if($LGD_CASCAMOUNT)			$tmp_log[]	= "현입금금액 : ".$LGD_CASCAMOUNT;
		if($LGD_CASFLAG)			$tmp_log[]	= "거래종류 : ".$LGD_CASFLAG." (R:할당,I:입금,C:취소)";
		if($LGD_CASSEQNO)			$tmp_log[]	= "가상계좌일련번호 : ".$LGD_CASSEQNO;
		break;
	case "SC0060":	// 핸드폰
		break;
}

// 최종 결제 로그 내용
$settlelog	= "{$ordno} (" . date('Y:m:d H:i:s') . ")\n----------------------------------------------------\n" . implode( "\n", $tmp_log ) . "\n----------------------------------------------------\n";
unset($tmp_log);

/*
 *************************************************
 * 4. DB 처리
 *************************************************
 */
// 주문 정보
$oData = $db->fetch("SELECT step, vAccount FROM ".GD_ORDER." WHERE ordno='".$ordno."'");

// 중복 결제 체크
if($oData['step'] > 0 || $oData['vAccount'] != '' || !strcmp($LGD_RESPCODE,'S007')){
	// 로그 저장
	$db->query("UPDATE ".GD_ORDER." SET settlelog=CONCAT(IFNULL(settlelog,''),'".$settlelog."') WHERE ordno='".$ordno."'");

	// DB 처리후 이동할 페이지
	$goUrl	= $order_fail_page.'?ordno='.$ordno;
}

// 결제성공
else if($isSuccess === true) {
	// 주문 데이터 추출
	$query	= "SELECT * FROM ".GD_ORDER." a LEFT JOIN ".GD_LIST_BANK." b ON a.bankAccount = b.sno WHERE a.ordno='".$ordno."'";
	$data	= $db->fetch($query);

	// 에스크로 여부 확인
	if($LGD_ESCROWYN == 'Y'){
		$escrowyn	= 'y';
		$escrowno	= $LGD_TID;
	}else{
		$escrowyn	= 'n';
		$escrowno	= '';
	}

	// 결제 정보 쿼리
	$step	= 1;
	$qrc1	= "cyn='y', cdt=now(), cardtno='".$LGD_TID."',";
	$qrc2	= "cyn='y',";

	// 가상계좌 결제시 계좌정보 쿼리
	if ($LGD_PAYTYPE == 'SC0040'){
		$vAccount	= $LGD_FINANCENAME.' '.$LGD_ACCOUNTNUM.' '.$LGD_PAYER;
		$step		= 0;
		$qrc1		= '';
		$qrc2		= '';
	}

	// 현금영수증 정보 쿼리
	if ($LGD_CASHRECEIPTNUM){
		$qrc1 .= "cashreceipt='".$LGD_CASHRECEIPTNUM."',";
	}

	// 주문 데이터 업데이트
	$db->query("
	UPDATE ".GD_ORDER." SET ".$qrc1."
		step		= '".$step."',
		step2		= '',
		escrowyn	= '".$escrowyn."',
		escrowno	= '".$escrowno."',
		vAccount	= '".$vAccount."',
		settlelog	= CONCAT(IFNULL(settlelog,''),'".$settlelog."')
	WHERE ordno='".$ordno."'"
	);

	// 주문 상품 데이터 업데이트
	$db->query("UPDATE ".GD_ORDER_ITEM." SET ".$qrc2." istep='".$step."' WHERE ordno='".$ordno."'");

	// 주문로그 저장
	orderLog($ordno,$r_step2[$data[step2]]." > ".$r_step[$step]);

	// 재고 처리
	setStock($ordno);

	// 상품구입시 적립금 사용
	if ($data['m_no'] && $data['emoney']){
		setEmoney($data['m_no'],-$data['emoney'],"상품구입시 적립금 결제 사용",$ordno);
	}

	// 주문확인메일
	if(function_exists('getMailOrderData')){
		sendMailCase($data['email'],0,getMailOrderData($ordno));
	}

	// SMS 변수 설정
	$dataSms	= $data;

	// 상황별 SMS / Email 전송
	if ($LGD_PAYTYPE != 'SC0040'){
		sendMailCase($data['email'],1,$data);		// 입금확인 메일
		sendSmsCase('incash',$data['mobileOrder']);	// 입금확인 SMS
	} else {
		sendSmsCase('order',$data['mobileOrder']);	// 주문확인 SMS
	}

	// DB 처리후 이동할 페이지
	$goUrl	= $order_end_page.'?ordno='.$ordno.'&card_nm='.$LGD_FINANCENAME;
}

// 결제실패
else {
	// 네이버 마일리지 Class가 있는 경우 API 설정
	if ($naverNcashCheck === true) {
		// 네이버 마일리지 결제 승인 취소 API 호출
		if ($naverNcash->useyn == 'Y') $naverNcash->payment_approval_cancel($ordno);
	}

	// 실패에 대한 주문 데이타와 로그 업데이트
	$db->query("UPDATE ".GD_ORDER." SET step2=54, settlelog=CONCAT(IFNULL(settlelog,''),'".$settlelog."') WHERE ordno='".$ordno."' AND step2=50");
	$db->query("UPDATE ".GD_ORDER_ITEM." SET istep=54 WHERE ordno='".$ordno."' AND istep=50");

	// DB 처리후 이동할 페이지
	$goUrl	= $order_fail_page.'?ordno='.$ordno;
}

//비동기일 경우(ISP결제)
if($isAsync == 'Y'){
	if ( $isSuccess === true && $resultMSG == "OK" ) {
		echo $resultMSG;
	} else {
		// OK가 아닌경우 ROLLBACK처리 됨. 애러로그는 가맹점 관리자에서 결제내역조회>전체거래내역조회>전송실패내역조회  에서 확인 가능
		// LGD_CUSTOM_ROLLBACK 을 C로 처리를 했기 때문에 ROLLBACK 이라고 처리를 해야만 실제 해당 결제가 ROLLBACK 처리됨
		echo 'ROLLBACK';
	}
}

// 동기 방식 결제의 경우 페이지 이동
else {
	//go($goUrl,'parent');
	go($goUrl);
}
?>