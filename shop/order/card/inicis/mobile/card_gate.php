<?

include dirname(__FILE__)."/../../../../conf/config.mobileShop.php";
include dirname(__FILE__)."/../../../../conf/pg.inicis.php";

$pg_mobile = $pg;

### 에스크로 결제시 pgId 변경
if ($_POST[escrow]=="Y") $pg_mobile[id] = $escrow[id];

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

if(!preg_match('/mypage/',$_SERVER[SCRIPT_NAME])){
	$item = $cart -> item;
}

foreach($item as $v){
	$i++;
	if($i == 1) $ordnm = $v[goodsnm];
}
//상품명에 특수문자 및 태그 제거
$ordnm	= pg_text_replace(strip_tags($ordnm));
if($i > 1)$ordnm .= " 외".($i-1)."건";

switch ($_POST[settlekind]){
	case "c":	// 신용카드
		$actionURL		= "https://mobile.inicis.com/smart/wcard/";
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
?>
<script language="javascript">
function on_load()
{
	curr_date = new Date();
	year = curr_date.getYear();
	month = curr_date.getMonth();
	day = curr_date.getDay();
	hours = curr_date.getHours();
	mins = curr_date.getMinutes();
	secs = curr_date.getSeconds();
	/****************************************************************************
	회원사에서 사용하는 주문번호를 이용하는 경우에는 다음을 주석처리 하세요
	주석처리하신 경우에는 form tag중 P_OID에 값을 넘겨주셔야 합니다 ****************************************************************************/
//	document.btpg_form.P_OID.value = year.toString() + month.toString() + day.toString() + hours.toString() + mins.toString() + secs.toString();
}

function on_card() {
	myform = document.btpg_form;
	/****************************************************************************
	신용카드 action url을 아래와 같이 설정합니다
	****************************************************************************/
	myform.action = "<?=$actionURL;?>";
	myform.submit();
}
</script>

<div style="text-align:center;padding:20px 0;font-size:12px;"><strong><b>잠시후 INIPay Mobile 결제화면으로 이동합니다.</b></strong></div>

<form name="btpg_form" method="post">

<!-- VISA3D 결제 -->
<input type="hidden" name="P_NEXT_URL" value="<?=ProtocolPortDomain()?><?=$cfg['rootDir']?>/order/card/inicis/mobile/card_return.php<?php echo '?ordno='.$_POST['ordno'].'&settlekind='.$_POST['settlekind']; ?>">

<!-- ISP 결제 -->
<input type="hidden" name="P_NOTI_URL" value="http://<?=$url_host?><?=$cfg['rootDir']?>/order/card/inicis/mobile/vacctinput.php">
<input type="hidden" name="P_RETURN_URL" value="<?=ProtocolPortDomain()?><?=$cfgMobileShop['mobileShopRootDir']?>/ord/order_return_url.php?ordno=<?=$_POST['ordno']?>">

<input type="hidden" name="P_EMAIL" value="<?=$_POST["email"]?>">
<input type="hidden" name="P_MOBILE" value="<?=$_POST['mobileOrder']?>">
<input type="hidden" name="P_GOODS" value="<?=$ordnm?>">
<input type="hidden" name="P_OID" value="<?=$_POST['ordno']?>">
<input type="hidden" name="P_NOTI"	value="<?php echo http_build_query(array('P_AMT'=>$_POST['settleprice']))?>">
<input type="hidden" name="P_UNAME" value="<?=$_POST["nameOrder"]?>">
<input type="hidden" name="P_MID" value="<?=$pg_mobile['id']?>">
<input type="hidden" name="P_AMT" value="<?=$_POST['settleprice']?>">
<input type="hidden" name="P_HPP_METHOD" value="2">
<input type="hidden" name="P_QUOTABASE" value="<?php echo $quota_mobile;?>"> <!-- 일반할부기간 -->
<input type="hidden" name="P_RESERVED" value="<?php echo $zerofee_mobile;?>&disable_kpay=Y&block_isp=Y"> <!-- 복합 parameter 정보 -->
<input type="hidden" name="P_TAX" value=""> <!-- 부가세 -->
<input type="hidden" name="P_TAXFREE" value=""> <!-- 비과세 -->
</form>

<script>$(document).ready(on_load);</script>