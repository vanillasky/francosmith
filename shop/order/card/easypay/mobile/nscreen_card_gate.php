<?php
include dirname(__FILE__)."/../../../../conf/config.mobileShop.php";
$shopdir=dirname(__FILE__)."/../../../../";
include($shopdir.'/conf/config.php');
include($shopdir.'/conf/pg.'.$cfg[settlePg].'.php');
require_once($shopdir.'/order/card/easypay/inc/easypay_config.php');
require_once($shopdir.'/order/card/easypay/easypay_client.php');


$page_type = $_GET['page_type'];

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


// 결제수단 별 코드
switch ($_POST['settlekind']){
	case "c":	// 신용카드
		$pay_type="11";
		break;
	case "v":	// 가상계좌
		$pay_type="22";
		break;
	case "h":	// 핸드폰
		$pay_type="31";
		break;
}

// 핸드폰 번호 처리
if (is_array($_POST['mobileOrder'])) {
	$mobileOrder	= implode('-', $_POST['mobileOrder']);
} else {
	$mobileOrder	= $_POST['mobileOrder'];
}

//ssl 보안서버 관련 추가
if($_SERVER['SERVER_PORT'] == 80) {
	$Port = "";
} elseif($_SERVER['SERVER_PORT'] == 443) {
	$Port = "";
} else {
	$Port = $_SERVER['SERVER_PORT'];
}

if (strlen($Port)>0) $Port = ":".$Port;

$Protocol = $_SERVER['HTTPS']=='on'?'https://':'http://';
$host = parse_url($_SERVER['HTTP_HOST']);

if ($host['path']) {
	$Host = $host['path'];
} else {
	$Host = $host['host'];
}

?>
<script language="javascript">

</script>

<!--<div style="text-align:center;padding:20px 0;font-size:12px;"><strong><b>잠시후 Easypay Mobile 결제화면으로 이동합니다.</b></strong></div>-->

<!-- 인증요청 URL 입니다.반드시 테스트/리얼을 구분하시기 바랍니다. -->
<form name="frm_pay" method="post" action="https://sp.easypay.co.kr/main/MainAction.do">   <!-- 리얼 -->

<!-- text 필드 START -->
<!-- [선택]가맹점 몰 이름 미 입력시 KICC에 등록된 가맹점 명 사용-->
<input type='hidden' name="sp_mall_nm"  			value='00'>
<!-- [필수]통화코드 (원화:'00', 달러:'01' ) -->
<input type='hidden' name="sp_currency"  			value='00'>
<!-- [필수]이지페이 버전  -->
<input type="hidden" name="sp_agent_ver"         	value="PHP">
<!-- [필수]사용자 IP   -->
<input type='hidden' name="sp_client_ip"  			value='<?=$_SERVER['REMOTE_ADDR']?>'>
<!-- [필수]결제처리 종류(수정불가) -->
<input type='hidden' name="sp_tr_cd"			    value='00101000'>
<!-- [필수]신용카드 결제구분 (수정불가) -->
<input type='hidden' name="sp_card_txtype"			value='20'>
<!-- [필수]리턴 URL(sample 소스에 있는 easypay_request.php 호출)-->
<input type='hidden' name="sp_return_url"  			value='<?=$Protocol.$Host.$Port?>/shop/order/card/easypay/mobile/nscreen_card_return.php?page_type=<?=$page_type?>'>
<!-- [선택]사용가능카드 리스트  -->
<input type="hidden" name="sp_usedcard_code">
<!-- [선택]가맹점 CI URL  -->
<input type="hidden" name="sp_ci_url"         		value="sp_ci_url">
<!-- [선택]언어구분(한국어/영어 구분)  -->
<input type="hidden" name="sp_lang_flag"         	value="KOR">
<!-- [선택] 모바일 가맹점 예약필드 1  -->
<input type="hidden" name="sp_mobilereserved1"      value="MobileReserved1">
<!-- [선택] 모바일 가맹점 예약필드 2  -->
<input type="hidden" name="sp_mobilereserved2"      value="MobileReserved2">
<!-- [선택] 가맹점 예약필드 1  -->
<input type="hidden" name="sp_reserved1"         	value="Reserved1">
<!-- [선택] 가맹점 예약필드 2  -->
<input type="hidden" name="sp_reserved2"         	value="Reserved2">
<!-- [선택] 가맹점 예약필드 3  -->
<input type="hidden" name="sp_reserved3"         	value="Reserved3">
<!-- [선택] 가맹점 예약필드 4  -->
<input type="hidden" name="sp_reserved4"         	value="Reserved4">
<!-- text 필드 END   -->
<input type="hidden" name="sp_mall_id"			value="<?php echo $pg_mobile['id'];?>">				<!-- 상점아이디 -->
<input type="hidden" name="sp_order_no"			value="<?php echo $_POST['ordno'];?>">				<!-- 주문번호 -->
<input type='hidden' name="sp_user_id"	  value='<?php	echo  $_SESSION['sess']['m_id']; ?>'>
<input type="hidden" name="sp_user_nm"			value="<?php echo $_POST['nameOrder'];?>">			<!-- 결제고객성명 -->
<input type="hidden" name="sp_user_mail"			value="<?php echo $_POST['email'];?>">				<!-- 사용자 e-mail 계정 -->
<input type="hidden" name="sp_product_nm"			value="<?php echo $ordnm;?>">						<!-- 결제상품명 -->
<input type="hidden" name="sp_pay_mny"			value="<?php echo $_POST['settleprice'];?>">		<!-- 거래금액 -->
<input type="hidden" name="sp_pay_type"  value="<?=$pay_type;?>" >
<input type="hidden" name="sp_product_type"	value="0">				<!-- 상품 컨텐츠 구분   -->
<input type="hidden" name="sp_tcode"		value="SKT">			<!--통신사 디폴트-->
 <input type="hidden" name="usedcard_code" value="029"  >
<input type="hidden" name="usedcard_code" value="027"  >
<input type="hidden" name="usedcard_code" value="031"  >
<input type="hidden" name="usedcard_code" value="008"  >
<input type="hidden" name="usedcard_code" value="026"  >
<input type="hidden" name="usedcard_code" value="016"  >
<input type="hidden" name="usedcard_code" value="047"  >
<input type="hidden" name="usedcard_code" value="018"  >
<input type="hidden" name="usedcard_code" value="006"  >
<input type="hidden" name="usedcard_code" value="022"  >
<input type="hidden" name="usedcard_code" value="021"  >
<input type="hidden" name="usedcard_code" value="002"  >
<input type="hidden" name="sp_noint_yn"	value="<?php echo $pg_mobile['zerofee'];?>">				<!-- 무이자 사용여부-->
<input type="hidden" name="sp_noinst_term"	value="<?php echo $pg_mobile['zerofee_period'];?>">				<!-- 무이자 설정-->

<input type="hidden"   name="sp_quota" value="<?php echo $pg_mobile['quota'];?>" size="35" /><!-- 할부 개월 -->
<input type="hidden" name="sp_user_phone2"		value="<?php echo str_replace("-","",$mobileOrder);?>">					<!-- 사용자 moblie 번호 -->
<input type="hidden" name="sp_version" value="0" /><!--웹-->
<input type="hidden" name="sp_user_type" value="1" /><!--사용자구분-->
</form>
<script type="text/javascript">
<!--
function f_submit() {
	var frm_pay = document.frm_pay;

	/* 가맹점사용카드리스트 */
	var usedcard_code = "";
	for( var i=0; i < frm_pay.usedcard_code.length; i++) {

			usedcard_code += frm_pay.usedcard_code[i].value + ":";

	}
	frm_pay.sp_usedcard_code.value = usedcard_code;
	frm_pay.submit();
}
//-->
</script>