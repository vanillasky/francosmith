<?php
/**
 * LG 유플러스 PG 모듈
 * 원본 파일명 payreq_crossplatform.php
 * LG 유플러스 PG 버전 : LG U+ 표준결제창 2.5 - SmartXPay(V1.2 - 20141212)
 * @author artherot @ godosoft development team.
 */

// 기본 설정 정보
include dirname(__FILE__)."/../../../../conf/config.mobileShop.php";
include dirname(__FILE__)."/../../../../conf/pg_mobile.lgdacom.php";

// LG유플러스 아이디 처리
if (empty($pg_mobile['serviceType'])) {
	$pg_mobile['serviceType']	= 'service';
}
if ($pg_mobile['serviceType'] == 'test') {
	$LGD_MID	= 't'.$pg_mobile['id'];
} else {
	$LGD_MID	= $pg_mobile['id'];
}

// 상품명 처리
if(!preg_match('/mypage/',$_SERVER['SCRIPT_NAME'])){
	$item	= $cart -> item;
}
foreach($item as $v){
	$i++;
	if($i == 1) $ordnm	= $v['goodsnm'];
}

//상품명에 특수문자 및 태그 제거
$ordnm		= pg_text_replace(strip_tags($ordnm));
if($i > 1)$ordnm .= " 외".($i-1)."건";

// 무이자 여부 (Y:1 / N:0)
if ($pg_mobile['zerofee'] == 'yes') {
	$pg_mobile['zerofee']	= '1';
} else {
	$pg_mobile['zerofee']	= '0';
}

// 무이자 할부 설정
if ($pg_mobile['zerofee'] == '0') {
	$pg_mobile['zerofee_period']	= '';
}

// 결제수단 설정
$arrSettlekind	=array(
	'c'	=> 'SC0010',
	'o'	=> 'SC0030',
	'v'	=> 'SC0040',
	'h'	=> 'SC0060',
);

/*
 *************************************************
 * 1. 기본결제 인증요청 정보 변경
 *************************************************
 */

$configPath								= $_SERVER['DOCUMENT_ROOT'].$cfg['rootDir'].'/conf/lgdacom_mobile';		// LG유플러스에서 제공한 환경파일("/conf/lgdacom.conf") 위치 지정.
$lguplusReturnUrl						= ProtocolPortDomain().$cfg['rootDir'].'/order/card/lgdacom';			// LG유플러스 리턴 URL 공통
$payReqMap								= array();
$payReqMap['CST_PLATFORM']				= $pg_mobile['serviceType'];					// LG유플러스 결제 서비스 선택(test:테스트, service:서비스)
$payReqMap['CST_WINDOW_TYPE']			= 'submit';										// 수정불가 (결제방식, 페이지 전환 방식)
$payReqMap['CST_MID']					= $pg_mobile['id'];								// 상점아이디(LG유플러스으로 부터 발급받으신 상점아이디를 입력하세요 - 테스트 아이디는 't'를 반드시 제외하고 입력하세요.)
$payReqMap['LGD_MID']					= $LGD_MID;										// 상점아이디(자동생성 - 테스트 인경우 자동으로 앞에 t를 붙임)
$payReqMap['LGD_OID']					= $_POST['ordno'];								// 주문번호
$payReqMap['LGD_AMOUNT']				= $_POST['settleprice'];						// 결제금액("," 를 제외한 결제금액을 입력하세요)
$payReqMap['LGD_BUYER']					= $_POST['nameOrder'];							// 구매자명
$payReqMap['LGD_PRODUCTINFO']			= $ordnm;										// 상품명
$payReqMap['LGD_BUYEREMAIL']			= $_POST['email'];								// 구매자 이메일
$payReqMap['LGD_CUSTOM_SKIN']			= 'SMART_XPAY2';								// 상점정의 결제창 스킨
$payReqMap['LGD_CUSTOM_PROCESSTYPE']	= 'TWOTR';										// 트랜잭션 처리방식 (TWOTR : 동기 방식 결제 흐름, ONETR : 비동기 방식 결제 흐름)
$payReqMap['LGD_TIMESTAMP']				= date(YmdHms);									// 타임스탬프
$payReqMap['LGD_VERSION']				= 'PHP_SmartXPay_1.0';							// 버전정보 (삭제하지 마세요)
$payReqMap['LGD_CUSTOM_FIRSTPAY']		= $arrSettlekind[$_POST['settlekind']];			// 상점정의 초기결제수단
$payReqMap['LGD_CUSTOM_SWITCHINGTYPE']	= strtoupper($payReqMap['CST_WINDOW_TYPE']);	// 신용카드 카드사 인증 페이지 연동 방식

if( $_POST['settlekind'] == 'c') {
	$payReqMap['LGD_INSTALLRANGE']		= $pg_mobile['quota'];							// 할부개월 범위
	$payReqMap['LGD_NOINTINF']			= $pg_mobile['zerofee_period'];					// 무이자 할부(수수료 상점부담) 적용 : 특정카드/특정개월무이자셋팅
}

if( $_POST['settlekind'] == 'o' || $_POST['settlekind'] == 'v' ) {
	$payReqMap['LGD_CASHRECEIPTYN']		= $pg_mobile['receipt'];						// 현금영수증 미사용여부(Y:미사용,N:사용)
}

$payReqMap['LGD_ESCROW_USEYN']			= $_POST['escrow'];								// 에스크로 여부 : 적용(Y),미적용(N)
if ($payReqMap['LGD_ESCROW_USEYN'] == 'Y') {
	foreach($cart->item as $row) {
		$payReqMap['LGD_ESCROW_GOODID']		= $row['goodsno'];							// 에스크로상품번호
		$payReqMap['LGD_ESCROW_GOODNAME']	= $row['goodsnm'];							// 에스크로상품명
		$payReqMap['LGD_ESCROW_GOODCODE']	= $_POST['escrow'];							// 에스크로상품코드
		$payReqMap['LGD_ESCROW_UNITPRICE']	= ($row['price']+$row['addprice']);			// 에스크로상품가격
		$payReqMap['LGD_ESCROW_QUANTITY']	= $row['ea'];								// 에스크로상품수량
	}
	if($_POST['zonecode']){
		$payReqMap['LGD_ESCROW_ZIPCODE']		= $_POST['zonecode'];					// 에스크로배송지구역번호 (새우편번호)
		$payReqMap['LGD_ESCROW_ADDRESS1']		= $_POST['road_address'];				// 에스크로배송지주소동까지 (도로명주소)
	}
	else {
		$payReqMap['LGD_ESCROW_ZIPCODE']		= implode('-',$_POST['zipcode']);			// 에스크로배송지우편번호
		$payReqMap['LGD_ESCROW_ADDRESS1']		= $_POST['address'];						// 에스크로배송지주소동까지
	}
	$payReqMap['LGD_ESCROW_ADDRESS2']		= $_POST['address_sub'];					// 에스크로배송지주소상세
	$payReqMap['LGD_ESCROW_BUYERPHONE']		= implode('-',$_POST['mobileOrder']);		// 에스크로구매자휴대폰번호
}

/*
 *************************************************
 * 2. MD5 해쉬암호화 (수정하지 마세요)
 *
 * MD5 해쉬암호화는 거래 위변조를 막기위한 방법입니다.
 *************************************************
 *
 * 해쉬 암호화 적용( LGD_MID + LGD_OID + LGD_AMOUNT + LGD_TIMESTAMP + LGD_MERTKEY )
 * LGD_MID          : 상점아이디
 * LGD_OID          : 주문번호
 * LGD_AMOUNT       : 금액
 * LGD_TIMESTAMP    : 타임스탬프
 * LGD_MERTKEY      : 상점MertKey (mertkey는 상점관리자 -> 계약정보 -> 상점정보관리에서 확인하실수 있습니다)
 *
 * MD5 해쉬데이터 암호화 검증을 위해
 * LG유플러스에서 발급한 상점키(MertKey)를 환경설정 파일(lgdacom/conf/mall.conf)에 반드시 입력하여 주시기 바랍니다.
 */
require_once(dirname(__FILE__)."/XPayClient.php");
$xpay	= &new XPayClient($configPath, $payReqMap['CST_PLATFORM']);
$xpay->Init_TX($payReqMap['LGD_MID']);
$payReqMap['LGD_HASHDATA']				= md5($payReqMap['LGD_MID'].$payReqMap['LGD_OID'].$payReqMap['LGD_AMOUNT'].$payReqMap['LGD_TIMESTAMP'].$xpay->config[$payReqMap['LGD_MID']]);	// MD5 해쉬암호값

/*
 *************************************************
 * 3. 경로 설정
 *************************************************
 */
if( $_POST['settlekind'] == 'v'){
	// 가상계좌(무통장) 결제연동을 하시는 경우  할당/입금 결과를 통보받기 위해 반드시 LGD_CASNOTEURL 정보를 LG 유플러스에 전송해야 합니다 .
	$payReqMap['LGD_CASNOTEURL']		= $lguplusReturnUrl.'/cas_noteurl.php?isMobile=Y';		// 가상계좌 NOTEURL
}

// LGD_RETURNURL 을 설정하여 주시기 바랍니다. 반드시 현재 페이지와 동일한 프로트콜 및  호스트이어야 합니다. 아래 부분을 반드시 수정하십시요.
$payReqMap['LGD_RETURNURL']				= $lguplusReturnUrl.'/mobile/card_return.php';			// 응답수신페이지

/*
 ****************************************************
 * 4. 안드로이드폰 신용카드 ISP(국민/BC)결제에만 적용
 ****************************************************

(주의)LGD_CUSTOM_ROLLBACK 의 값을  "Y"로 넘길 경우, LG U+ 전자결제에서 보낸 ISP(국민/비씨) 승인정보를 고객서버의 note_url에서 수신시  "OK" 리턴이 안되면  해당 트랜잭션은  무조건 롤백(자동취소)처리되고,
LGD_CUSTOM_ROLLBACK 의 값 을 "C"로 넘길 경우, 고객서버의 note_url에서 "ROLLBACK" 리턴이 될 때만 해당 트랜잭션은  롤백처리되며  그외의 값이 리턴되면 정상 승인완료 처리됩니다.
만일, LGD_CUSTOM_ROLLBACK 의 값이 "N" 이거나 null 인 경우, 고객서버의 note_url에서  "OK" 리턴이  안될시, "OK" 리턴이 될 때까지 3분간격으로 2시간동안  승인결과를 재전송합니다.
*/
$payReqMap['LGD_CUSTOM_ROLLBACK']		= 'C';						// 비동기 ISP에서 트랜잭션 처리여부

// ISP 카드결제 연동중 모바일ISP방식(고객세션을 유지하지않는 비동기방식)의 경우, LGD_KVPMISPNOTEURL/LGD_KVPMISPWAPURL/LGD_KVPMISPCANCELURL를 설정하여 주시기 바랍니다.
$payReqMap['LGD_KVPMISPNOTEURL']       	= $lguplusReturnUrl.'/mobile/card_return.php?isAsync=Y';								// 비동기 ISP(ex. 안드로이드) 승인결과를 받는 URL
$payReqMap['LGD_KVPMISPWAPURL']			= $lguplusReturnUrl.'/mobile/mispwapurl.php?LGD_OID='.$payReqMap['LGD_OID'];			// 비동기 ISP(ex. 안드로이드) 승인완료후 사용자에게 보여지는 승인완료 URL - ISP 카드 결제시, URL 대신 앱명 입력시, 앱호출함
$payReqMap['LGD_KVPMISPCANCELURL']     	= $lguplusReturnUrl.'/mobile/Cancel.php?isAsync=Y';										// ISP 앱에서 취소시 사용자에게 보여지는 취소 URL

// 안드로이드 에서 신용카드 적용  ISP(국민/BC)결제에만 적용 (선택)
$payReqMap['LGD_KVPMISPAUTOAPPYN']		= 'N';						// Y: 안드로이드에서 ISP신용카드 결제시, 고객사에서 'App To App' 방식으로 국민, BC카드사에서 받은 결제 승인을 받고 고객사의 앱을 실행하고자 할때 사용

// Return URL에서 인증 결과 수신 시 셋팅될 파라미터 입니다.*/
$payReqMap['LGD_RESPCODE']				= '';
$payReqMap['LGD_RESPMSG']				= '';
$payReqMap['LGD_PAYKEY']				= '';

// 처리 페이지에서 유효성 체크를 위한 모든 변수를 세션에 저장
$_SESSION['PAYREQ_MAP']					= $payReqMap;

// 보안서버 사용여부에 따른 LG U+ 결제 스크립트 주소 변경
$xpay_uplus_script_url	= 'xpay.uplus.co.kr/xpay/js/xpay_crossplatform.js';
if ($_SERVER['HTTPS'] == 'on') {
	$xpay_uplus_script_url	= 'https://' . $xpay_uplus_script_url;
} else {
	$xpay_uplus_script_url	= 'http://' . $xpay_uplus_script_url;
}
?>
<script language="javascript" src="<?php echo $xpay_uplus_script_url;?>" type="text/javascript"></script>
<script type="text/javascript">
/*
* 수정불가
*/
var LGD_window_type	= '<?php echo $payReqMap['CST_WINDOW_TYPE'];?>';

/*
* 수정불가
*/
function launchCrossPlatform(){
      lgdwin = open_paymentwindow(document.getElementById('LGD_PAYINFO'), '<?php echo $payReqMap['CST_PLATFORM'];?>', LGD_window_type);
}
/*
* FORM 명만  수정 가능
*/
function getFormObject() {
        return document.getElementById('LGD_PAYINFO');
}
</script>
<form method="post" name="LGD_PAYINFO" id="LGD_PAYINFO" action="">
<?php
foreach ($payReqMap as $key => $value) {
	echo '<input type="hidden" name="'.$key.'" id="'.$key.'" value="'.$value.'" />'.chr(10);
}
?>
<input type="hidden" name="LGD_TAXFREEAMOUNT" id="LGD_TAXFREEAMOUNT" />
</form>