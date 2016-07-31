<?php
	// 다날 결제 시작
	header( "Pragma: No-Cache" );
	include( "./inc/function.php" );
	/********************************************************************************
	 *
	 * 다날 휴대폰 결제
	 *
	 * - 결제 요청 페이지
	 *      CP인증 및 결제 정보 전달
	 *
	 * 결제 시스템 연동에 대한 문의사항이 있으시면 서비스개발팀으로 연락 주십시오.
	 * DANAL Commerce Division Technique supporting Team
	 * EMail : tech@danal.co.kr
	 *
	 ********************************************************************************/

	$goodsNm = $danal->makeGoodsName($cart->item);					// 상품명 (80바이트 이내)
	$domain = array_shift(explode(':', $_SERVER['HTTP_HOST']));		// 상점 도메인
	$address = (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$domain.($_SERVER['SERVER_PORT']!=='80'?':'.$_SERVER['SERVER_PORT']:'');		// 상점 full URL
	$email = $_POST['email'];				// 이메일
	$ordno = $_POST['ordno'];				// 주문번호
	$price = $_POST['settleprice'];			// 주문금액
	$isMobile = $_GET['isMobile'];			// 모바일 여부 확인
	$isPc = $_GET['pc'];					// n스크린 여부 확인
	
	// 회원이면 아이디,아니면 이메일, 이메일도 없으면 guest
	if ($sess['m_id']) {
		$userid = $sess['m_id'];
	}
	else if ($email) {
		$userid = $email;
	}
	else {
		$userid = 'guest';
	}
	/********************************************************************************
	 *
	 * [ 전문 요청 데이터 ] *********************************************************
	 *
	 ********************************************************************************/

	/***[ 필수 데이터 ]************************************/
	$TransR = array();

	/******************************************************
	 ** 아래의 데이터는 고정값입니다.( 변경하지 마세요 )
	 * Command      : ITEMSEND2
	 * SERVICE      : TELEDIT
	 * ItemType     : Amount
	 * ItemCount    : 1
	 * OUTPUTOPTION : DEFAULT 
	 ******************************************************/
	$TransR["Command"] = "ITEMSEND2";
	$TransR["SERVICE"] = "TELEDIT";
	$TransR["ItemType"] = "Amount";
	$TransR["ItemCount"] = "1";
	$TransR["OUTPUTOPTION"] = "DEFAULT";

	/******************************************************
	 *  ID          : 다날에서 제공해 드린 ID( function 파일 참조 )
	 *  PWD         : 다날에서 제공해 드린 PWD( function 파일 참조 )
	 *  CPNAME      : CP 명
	 ******************************************************/
	$TransR["ID"] = $ID;
	$TransR["PWD"] = $PWD;
	$CPName = $shopConfig['shopName'];

	/******************************************************
	 * ItemAmt      : 결제 금액( function 파일 참조 )
	 *      - 실제 상품금액 처리시에는 Session 또는 DB를 이용하여 처리해 주십시오.
	 *      - 금액 처리 시 금액변조의 위험이 있습니다.
	 * ItemName     : 상품명
	 * ItemCode     : 다날에서 제공해 드린 ItemCode
	 ******************************************************/
	$ItemAmt = $price;
	$ItemName = $goodsNm;
	$ItemCode = $danalCfg['serviceItemCode'];
	$ItemInfo = MakeItemInfo( $ItemAmt,$ItemCode,$ItemName );

	$TransR["ItemInfo"] = $ItemInfo;

	/***[ 선택 사항 ]**************************************/
	/******************************************************
	 * SUBCP        : 다날에서 제공해드린 SUBCP ID
	 * USERID       : 사용자 ID
	 * ORDERID      : CP 주문번호
	 * IsPreOtbill  : 자동결제 여부(Y/N) AuthKey 수신 유무 (자동결제를 위한 AuthKey 수신이 필요한 경우 : Y)
	 * IsSubscript	: 월 정액 가입 유무(Y/N) (월 정액 가입을 위한 첫 결제인 경우 : Y)
	 ******************************************************/
	$TransR["SUBCP"] = $danalCfg['S_CPID'];
	$TransR["USERID"] = $userid;
	$TransR["ORDERID"] = $ordno;
	$TransR["IsPreOtbill"] = "N";
	$TransR["IsSubscript"] = "N";

	/********************************************************************************
	 *
	 * [ CPCGI에 HTTP POST로 전달되는 데이터 ] **************************************
	 *
	 ********************************************************************************/

	/***[ 필수 데이터 ]************************************/
	$ByPassValue = array();

	/******************************************************
	 * BgColor      : 결제 페이지 Background Color 설정
	 * TargetURL    : 최종 결제 요청 할 CP의 CPCGI FULL URL
	 * BackURL      : 에러 발생 및 취소 시 이동 할 페이지의 FULL URL
	 * IsUseCI      : CP의 CI 사용 여부( Y or N )
	 * CIURL        : CP의 CI FULL URL
	 ******************************************************/
	$ByPassValue["BgColor"] = "00";
	$ByPassValue["TargetURL"] = $address.$shopConfig['rootDir'].'/order/card/danal/card_return.php';

	// PC, 모바일 결체 취소 url 분기
	if (isset($isMobile)) {
		$ByPassValue["BackURL"] = $address.$shopConfig['rootDir'].'/order/card/danal/card_back.php?ordno='.$ordno.'&isMobile=true&isPc='.$isPc;
	}
	else{
		$ByPassValue["BackURL"] = $address.$shopConfig['rootDir'].'/order/card/danal/card_back.php?ordno='.$ordno;
	}
	$ByPassValue["IsUseCI"] = "N";
	$ByPassValue["CIURL"] = $address.$shopConfig['rootDir'].'/order/card/danal/images/ci.gif';

	/***[ 선택 사항 ]**************************************/

	/******************************************************
	 * Email	: 사용자 E-mail 주소 - 결제 화면에 표기
	 * IsCharSet	: CP의 Webserver Character set
	 ******************************************************/
	$ByPassValue["Email"] = $email;
	$ByPassValue["IsCharSet"] = "";

	/******************************************************
	 ** CPCGI에 POST DATA로 전달 됩니다.
	 **
	 ******************************************************/
	$ByPassValue['ordno'] = $ordno;

	// PC, 모바일 url 분기
	if (isset($isMobile)) {
		$startUrl = 'https://ui.teledit.com/Danal/Teledit/FlexMobile/Start.php';
		$ByPassValue['isMobile'] = $isMobile;	// card_check로 모바일 여부 전송
		$ByPassValue['isPc'] = $isPc;			// card_check로 n스크린 여부 전송
	}
	else {
		$startUrl = 'https://ui.teledit.com/Danal/Teledit/Web/Start.php';
	}

	// 다날 모듈 실행 로그 기록
	$danal->writeLog(
		'Paygate open start'.PHP_EOL.
		'File : '.__FILE__.PHP_EOL.
		'Transaction ID : '.$ordno.PHP_EOL.
		'Send data : '.http_build_query($TransR)
	);

	$Res = CallTeledit( $TransR,false );

	if( $Res["Result"] == "0" ) {
?>
<html>
<head>
<title>다날 휴대폰 결제</title>
<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
</head>
<body>
<form name="Ready" action="<?php echo $startUrl; ?>" method="post">
<?php
	MakeFormInput($Res,array("Result","ErrMsg"));
	MakeFormInput($ByPassValue);
?>
<input type="hidden" name="CPName"      value="<?=$CPName?>">
<input type="hidden" name="ItemName"    value="<?=$ItemName?>">
<input type="hidden" name="ItemAmt"     value="<?=$ItemAmt?>">
<input type="hidden" name="IsPreOtbill" value="<?=$TransR['IsPreOtbill']?>">
<input type="hidden" name="IsSubscript" value="<?=$TransR['IsSubscript']?>">
</form>
<script Language="JavaScript">
	var isMobile = "<?php echo $isMobile; ?>";

	if (isMobile) {
		document.Ready.target="_parent";
	}
	else{
		danalWin = window.open("","danalWin","width=500,height=680,toolbar=no,menubar=no,scrollbars=no,resizable=yes");
		danalWin.focus();
		Ready.target="danalWin";
	}

	document.Ready.submit();
</script>
</body>
</html>
<?php
	} else {
		/**************************************************************************
		 *
		 * 결제 실패에 대한 작업
		 *
		 **************************************************************************/

		$Result		= $Res["Result"];
		$ErrMsg		= $Res["ErrMsg"];
		$AbleBack	= false;
		$BackURL	= $ByPassValue["BackURL"];
		$IsUseCI	= $ByPassValue["IsUseCI"];
		$CIURL		= $ByPassValue["CIURL"];
		$BgColor	= $ByPassValue["BgColor"];
			
		// 실패 로그 작성
		$danal->failLog($ordno, $Result, $ErrMsg);

		if ($Result == '51') {
			msg('다날 결제 금액이 300원을 초과해야 합니다.');
		}
		else {
			msg($ErrMsg);
		}
	}
?>
