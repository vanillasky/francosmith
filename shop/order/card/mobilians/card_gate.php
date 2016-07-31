<?php

//######################################################################
// 파일명 : mc_web_sample.php
// 작성자 : 기술지원팀
// 작성일 : 2012.09
// 용  도 : 휴대폰 Weblink 방식 결제 연동 페이지
// 버	전 : 0004

// 가맹점의 소스 임의변경에 따른 책임은 모빌리언스에서 책임을 지지 않습니다.
// 요청 파라미터 및 결제 후  가맹점측  Okurl / Notiurl 으로 Return 되는 파라미터와 가맹점 서비스처리 방법은
// 연동 매뉴얼을 반드시 참조하세요.
// 결제실서버 전환시 꼭 모빌리언스 기술지원팀으로 연락바랍니다.

// 암호화 사용시  필수 libCipher 실행파일을 가맹점측 서버에 설치
// 설치방법은 seed.tar 파일과 설치매뉴얼 참조
//######################################################################

include dirname(__FILE__).'/../../../lib/library.php';

$shopConfig = Core::loader('config')->_load_config();
$mobilians = Core::loader('Mobilians');
$cart = Core::loader('cart', $_COOKIE['gd_isDirect']);
$sendData = array();
$mobiliansCfg = $mobilians->getConfig();
$domain = array_shift(explode(':', $_SERVER['HTTP_HOST']));
$address = (isset($_SERVER['HTTPS']) ? 'https' : 'http').'://'.$domain.($_SERVER['SERVER_PORT']!=='80'?':'.$_SERVER['SERVER_PORT']:'');

//######################################################################
// 휴대폰 결제 / 본인인증  구분은  CASH_GB 변수를 통해 구분함
//	$CASH_GB = 'MC' 휴대폰 결제창 호출
//	$CASH_GB = 'CE' 휴대폰 본인인증  호출
//######################################################################
$sendData['CASH_GB'] = 'MC'; 	// 대표결제수단


//######################################################################
// 필수 입력 항목
//######################################################################
if (isset($_GET['mode']) && $_GET['mode'] === 'resettle') {
	$lookupOrderItem = $db->query('SELECT goodsnm, opt1, opt2 FROM gd_order_item WHERE ordno='.$_POST['ordno']);
	$itemList = array();
	while ($orderItem = $db->fetch($lookupOrderItem, true)) {
		$itemList[] = array(
			'goodsnm' => $orderItem['goodsnm'],
			'opt' => array($orderItem['opt1'], $orderItem['opt2']),
		);
	}
	$sendData['Prdtnm'] = Mobilians::makeGoodsName($itemList);	// 상품명 (50byte 이내)
}
else {
	$sendData['Prdtnm'] = Mobilians::makeGoodsName($cart->item);	// 상품명 (50byte 이내)
}
$sendData['Prdtprice'] = $_POST['settleprice'];	// 결제요청금액
$sendData['Siteurl'] = preg_replace('/^(^www\.)?(.{0,20}).*$/', '$2', $_SERVER['SERVER_NAME']);	// 가맹점도메인URL
$sendData['Tradeid'] = $_POST['ordno'];	// 가맹점거래번호 Unique 값으로 세팅 권장
$sendData['PAY_MODE'] = $mobiliansCfg['serviceType'];	// 연동시 테스트,실결제구분 (00 : 테스트결제, 10 : 실거래결제)
$sendData['MC_SVCID'] = $mobiliansCfg['serviceId'];	// 서비스아이디(대표서비스ID SKT용으로 세팅)
$sendData['Okurl'] = $address.$shopConfig['rootDir'].'/order/card/mobilians/card_return.php';	// 성공URL : 결제완료통보페이지 full Url (예:http://www.mcash.co.kr/okurl.php)


//######################################################################
// 선택 입력 항목
//######################################################################
$sendData['MC_FIXNO'] = 'N';	// 사용자폰번호 수정불가여부(N : 수정가능 default, Y : 수정불가)
$sendData['Failurl'] = $address.$shopConfig['rootDir'].'/order/card/mobilians/card_return.php';	// 실패URL : 결제실패시통보페이지 full Url (예:http://www.mcash.co.kr/failurl.asp)
                       // 결제처리에 대한 실패처리 안내를 가맹점에서 제어해야 할 경우만 사용
$sendData['MSTR'] = '';	// 가맹점콜백변수
                        // 가맹점에서 추가적으로 파라미터가 필요한 경우 사용하며 &, % 는 사용불가 (예 : MSTR="a=1|b=2|c=3")
$sendData['Payeremail'] = $_POST['email'];	// 결제자email
$sendData['EMAIL_HIDDEN'] = "N";	// 결제자email 입력창 숨김(N default, Y 인경우 결제창에서 이메일항목 삭제)
$sendData['Userid'] = $sess ? $sess['m_id'] : '';	// 가맹점결제자ID
$sendData['Item'] = '';	// 아이템코드
$sendData['Prdtcd'] = isset($_GET['isMobile']) ? 'MOB' : 'WEB';	// 상품코드(현재는 모바일과 PC결제를 구분하는 용도로 사용중)
$sendData['MC_Cpcode'] = '';	// 리셀러하위상점key
$sendData['MC_AUTHPAY'] = 'N';	// 휴대폰 점유인증만 사용시  'Y' 로 설정 (휴대폰 점유인증후 일반 소켓모듈 결제 연동시 사용)
$sendData['MC_AUTOPAY'] = 'N';	// 자동결제를 위한 최초일반결제 - 자동결제key 발급 (Y:사용, N:미사용 default)
$sendData['MC_PARTPAY'] = 'N';	// 부분취소를 위한 일반결제 - 자동결제key 발급 (Y : 사용, N : 미사용 default)
//$sendData['MC_No'] = $_POST['mobileOrder'][0].$_POST['mobileOrder'][1].$_POST['mobileOrder'][2];	// 사용자 폰번호 (결제창 호출시 세팅할 폰번호)

if (isset($shopConfig['adminEmail']) && strlen(trim($shopConfig['adminEmail'])) > 0) {
	$sendData['Notiemail'] = $shopConfig['adminEmail'];	// 알림email : 입금완료 후 당사와 가맹점간의 연동이 실패한 경우 알람 메일을 받을 가맹점 담당자 이메일주소
}
$sendData['Notiurl'] = ProtocolPortDomain().$shopConfig['rootDir'].'/order/card/mobilians/card_notice.php';	// 결제처리URL : 결제 완료 후, 가맹점측 과금 등 처리할 가맹점측 URL


//######################################################################
//- 디자인 관련 선택항목 ( 향후  변경될 수 있습니다  )
//######################################################################
$sendData['LOGO_YN'] = 'N'; // 가맹점 로고 사용여부 (가맹점 로고 사용시 'Y'로 설정, 사전에 모빌리언스에 가맹점 로고 이미지가 있어야함)
if (isset($_GET['isMobile'])) {
	include dirname(__FILE__).'/../../../conf/config.mobileShop.php';
	$sendData['CALL_TYPE'] = 'I';
	$sendData['IFRAME_NAME'] = '_parent';
	$sendData['Closeurl'] = ProtocolPortDomain().$cfgMobileShop['mobileShopRootDir'].'/ord/order.php';
}
else {
	$sendData['CALL_TYPE'] = 'P';
}
$sendData['CONTRACT_HIDDEN'] = 'Y'; // 이용약관 표시여부(Y/N)
$sendData['MC_DEFAULTCOMMID'] = 'SKT'; // 기본이통사(SKT/KTF/LGT)
$sendData['MC_FIXCOMMID'] = ''; // 이통사고정(SKT/KTF/LGT) 빈값은 사용안함

// 모바일 샵은 결과페이지에 플래그값 추가
if (isset($_GET['isMobile']) && isset($_GET['pc'])) {
	$sendData['Okurl'] .= '?pc=true&isMobile=true';
	$sendData['Failurl'] .= '?pc=true&isMobile=true';
}

//######################################################################
//- 암호화 ( 암호화 사용시 )
// Cryptstring 항목은 금액변조에 대한 확인용으로  반드시 아래와 같이 문자열을 생성하여야 합니다.
//
// 주) 암호화 해쉬키는 가맹점에서 전달하는 거래번호로 부터 추출되어 사용되므로
//           암호화에 이용한 거래번호가  변조되어 전달될 경우 복호화 실패로 결제 진행 불가
//######################################################################
$sendData['Cryptyn'] = 'N';					// "Y" 암호화사용, "N" 암호화미사용

$mobilians->writeLog(
	'Paygate open start'.PHP_EOL.
	'File : '.__FILE__.PHP_EOL.
	'Transaction ID : '.$sendData['Tradeid'].PHP_EOL.
	'Send data : '.http_build_query($sendData)
);

if($sendData['Cryptyn'] == 'Y'){
	$sendData['Okurl'] = Mobilians::encrypt($sendData['Okurl'], $sendData['Tradeid']);
	$sendData['Failurl'] = Mobilians::encrypt($sendData['Failurl'], $sendData['Tradeid']);
	$sendData['Notiurl'] = Mobilians::encrypt($sendData['Notiurl'], $sendData['Tradeid']);
	$sendData['Prdtprice'] = Mobilians::encrypt($sendData['Prdtprice'], $sendData['Tradeid']);
	$sendData['Cryptstring'] = Mobilians::encrypt($sendData['Prdtprice'].$sendData['Okurl'], $sendData['Tradeid']);
}

?>
<!--  가맹점의 결제요청 페이지 -->
<html>
	<head>
		<!--
			/*****************************************************************************************
			 가맹점에서는 아래 js 파일을 반드시 include
			 실 결제환경 구성시 모빌리언스 담당자와 상의 요망
			*****************************************************************************************/
		-->
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $_CFG['global']['charset']; ?>" />
		<script src="https://mcash.mobilians.co.kr/js/ext/ext_inc_comm.js"></script>
		<script type="text/javascript" charset="<?php echo $_CFG['global']['charset']; ?>">
			window.onload = function()
			{
				if (MobiliansPaymentForm.Prdtprice.value === "<?php echo $sendData['Prdtprice']; ?>") {
					MCASH_PAYMENT(MobiliansPaymentForm);
				}
				else {
					alert("결제금액이 올바르지 않습니다.");
				}
			};
		</script>
	</head>

	<body>
		<form name="MobiliansPaymentForm" accept-charset="<?php echo $_CFG['global']['charset']; ?>">
			<?php foreach ($sendData as $name => $value) { ?>
			<input type="hidden" id="<?php echo $name; ?>" name="<?php echo $name; ?>" value="<?php echo $value; ?>"/>
			<?php } ?>
		</form>
	</body>
</html>
