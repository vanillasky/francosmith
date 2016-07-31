<?php
/**
 * 이니시스 PG 모듈 페이지
 * 이니시스 PG 버전 : INIpayMobile Web (V 2.4 - 20110725)
 */

include dirname(__FILE__)."/../../../../conf/config.mobileShop.php";
include dirname(__FILE__)."/../../../../conf/pg.inipay.php";

// 모바일 변수로 처리
$pg_mobile	= $pg;

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

// 리턴 URL 설정
$P_NEXT_URL		= "http://".$_SERVER['HTTP_HOST'].$cfg['rootDir']."/order/card/inipay/mobile/shopTouch_card_return.php";
$P_NOTI_URL		= "http://".$_SERVER['HTTP_HOST'].$cfg['rootDir']."/order/card/inipay/mobile/vacctinput.php";
$P_RETURN_URL	= "http://".$_SERVER['HTTP_HOST'].$cfgMobileShop['mobileShopRootDir']."/shopTouch_ord/order_end.php?ordno=".$_POST['ordno'];

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
<input type="hidden" name="P_NOTI"			value="">											<!-- 기타주문정보 -->

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
<input type="hidden" name="P_RESERVED"		value="">				<!-- 복합 parameter 정보 -->
<input type="hidden" name="P_TAX"			value="">				<!-- 부가세 -->
<input type="hidden" name="P_TAXFREE"		value="">				<!-- 비과세 -->
</form>
