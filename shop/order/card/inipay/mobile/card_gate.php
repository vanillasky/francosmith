<?php
/**
 * 이니시스 PG 모듈 페이지
 * 이니시스 PG 버전 : INIpayMobile Web (V 2.4 - 20110725)
 */

include dirname(__FILE__)."/../../../../conf/config.mobileShop.php";
include dirname(__FILE__)."/../../../../conf/pg.inipay.php";

// 모바일 변수로 처리
$pg_mobile	= $pg;

// 일반할부기간 (01:02:03:04:05:06:07:08:09:10:11:12 (구분자 : ))
// Ex) 선택:일시불:2개월:3개월:4개월:5개월:6개월 -> 1:2:3:4:5:6
$quota_mobile = preg_replace(array('/(^선택:)|(개월)/', '/일시불/'), array('', '1'), $pg_mobile['quota']);

// 무이자여부 (merc_noint=Y (일반결제 N, 무이자결제 Y))
// 무이자기간 (noint_quota=12-2:3^14-2:3 (카드-개월수:개월수^카드-개월수))
// Ex) 12-2:3,14-3:4 -> 12-2:3^14-3:4
if ($pg_mobile['zerofee'] == 'yes' && $pg_mobile['zerofee_period'] != '') {
	$period = str_replace(',', '^', $pg_mobile['zerofee_period']);
	$zerofee_mobile = 'merc_noint=Y&noint_quota='.$period;
}
else {
	$zerofee_mobile = '';
}

// 상품명 설정
if(!preg_match('/mypage/',$_SERVER['SCRIPT_NAME'])){
	$item	= $cart -> item;
}
foreach($item as $v){
	$i++;
	if($i == 1) $ordnm = str_replace("`", "'", $v[goodsnm]);
}
if($i > 1)$ordnm .= " 외".($i-1)."건";
$ordnm	= pg_text_replace(strip_tags($ordnm));

// 결제수단 별 URL
switch ($_POST['settlekind']){
	case "c":	// 신용카드
		$actionURL		= "https://mobile.inicis.com/smart/wcard/";
		break;
	case "o":	// 계좌이체
		$actionURL		= "https://mobile.inicis.com/smart/bank/";
		break;
	case "v":	// 가상계좌
		$actionURL		= "https://mobile.inicis.com/smart/vbank/";
		break;
	case "h":	// 핸드폰
		$actionURL		= "https://mobile.inicis.com/smart/mobile/";
		break;
}

$url_noti = parse_url($_SERVER['HTTP_HOST']);
if($url_noti['path']) {
	$url_host = $url_noti['path'];
} else {
	$url_host = $url_noti['host'];
}
// 리턴 URL 설정
$P_NEXT_URL		= ProtocolPortDomain().$cfg['rootDir']."/order/card/inipay/mobile/card_return.php?ordno=".$_POST['ordno']."&settlekind=".$_POST['settlekind'];
$P_NOTI_URL		= "http://".$url_host.$cfg['rootDir']."/order/card/inipay/mobile/vacctinput.php";
$P_RETURN_URL	= ProtocolPortDomain().$cfgMobileShop['mobileShopRootDir']."/ord/order_return_url.php?ordno=".$_POST['ordno'];

// 핸드폰 번호 처리
if (is_array($_POST['mobileOrder'])) {
	$mobileOrder	= implode('-', $_POST['mobileOrder']);
} else {
	$mobileOrder	= $_POST['mobileOrder'];
}
?>
<script language="javascript">
function on_card() {
	myform	= document.btpg_form;
	myform.action	= "<?php echo $actionURL;?>";
	myform.submit();
}
</script>

<div style="text-align:center;padding:20px 0;font-size:12px;"><strong><b>잠시후 INIPay Mobile 결제화면으로 이동합니다.</b></strong></div>

<form name="btpg_form" method="post">
<input type="hidden" name="P_MID"			value="<?php echo $pg_mobile['id'];?>">				<!-- 상점아이디 -->
<input type="hidden" name="P_OID"			value="<?php echo $_POST['ordno'];?>">				<!-- 주문번호 -->
<input type="hidden" name="P_AMT"			value="<?php echo $_POST['settleprice'];?>">		<!-- 거래금액 -->
<input type="hidden" name="P_UNAME"			value="<?php echo $_POST['nameOrder'];?>">			<!-- 결제고객성명 -->
<input type="hidden" name="P_NOTI"			value="<?php echo http_build_query(array('P_AMT'=>$_POST['settleprice']))?>">											<!-- 기타주문정보 -->

<input type="hidden" name="P_NEXT_URL"		value="<?php echo $P_NEXT_URL;?>">					<!-- 인증 성공/실패에 관한 결과 URL (VISA3D, 기타 지불 수단은 필수, ISP,계좌이체는 사용 안함) -->
<input type="hidden" name="P_NOTI_URL"		value="<?php echo $P_NOTI_URL;?>">					<!-- 실제 결제 DB 를 갱신 URL (ISP / 가상계좌 / 계좌이체 결제시 사용되며 필수) -->
<input type="hidden" name="P_RETURN_URL"	value="<?php echo $P_RETURN_URL;?>">				<!-- 가맹점에서 전달한 값을 그대로 반환 URL (ISP / 계좌이체 결제시에만 사용되며 필수) -->

<input type="hidden" name="P_GOODS"			value="<?php echo $ordnm;?>">						<!-- 결제상품명 -->
<input type="hidden" name="P_MOBILE"		value="<?php echo $mobileOrder;?>">					<!-- 사용자 moblie 번호 -->
<input type="hidden" name="P_EMAIL"			value="<?php echo $_POST['email'];?>">				<!-- 사용자 e-mail 계정 -->

<?php if ($_POST['settlekind'] == 'h') {?>
<input type="hidden" name="P_HPP_METHOD"	value="2">				<!-- 상품 컨텐츠 구분 (휴대폰 결제 시 사용 합니다. “1” : 컨텐츠 “2” : 실물) -->
<?php }?>
<input type="hidden" name="P_VBANK_DT"		value="">				<!-- 가상계좌 입금기한 (기본 10일) -->
<input type="hidden" name="P_CARD_OPTION"	value="">				<!-- 카드 선택 옵션 (설정 시 선택된 카드가 우선적으로 설정됩니다, 예)selcode=14 ) -->
<?php if ($_POST['settlekind'] == 'o') {?>
<input type="hidden" name="P_APP_BASE"		value="ON">				<!-- 계좌이체 시 필수 (“ON” (고정)) -->
<?php }?>
<input type="hidden" name="P_MLOGO_IMAGE"	value="">				<!-- 상점 로고 이미지 -->
<input type="hidden" name="P_GOOD_IMAGE"	value="">				<!-- 상품 이미지 -->
<input type="hidden" name="P_QUOTABASE"		value="<?php echo $quota_mobile;?>">				<!-- 일반할부기간 -->
<input type="hidden" name="P_RESERVED"		value="<?php echo $zerofee_mobile;?>&disable_kpay=Y&block_isp=Y">				<!-- 복합 parameter 정보 -->
<input type="hidden" name="P_TAX"			value="">				<!-- 부가세 -->
<input type="hidden" name="P_TAXFREE"		value="">				<!-- 비과세 -->
</form>