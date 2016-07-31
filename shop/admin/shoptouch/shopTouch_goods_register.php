<?
$location = "쇼핑몰 App관리 > 쇼핑몰 App 상품등록";
include "../_header.php";

@include_once "../../lib/pAPI.class.php";
$pAPI = new pAPI();
$expire_dt = $pAPI->getExpireDate();
if(!$expire_dt) {
	msg('서비스 신청후에 사용가능한 메뉴입니다.', -1);
}

$now_date = date('Y-m-d 23:59:59');
$tmp_now_date = date('Y-m-d 23:59:59', mktime(0,0,0, substr($now_date, 5, 2), substr($now_date, 8, 2) - 30, substr($now_date, 0, 4)));
if($expire_dt < $tmp_now_date) {
	msg('서비스 사용기간 만료후 30일이 지나 서비스가 삭제 되었습니다.\n서비스를 다시 신청해 주시기 바랍니다.', -1);
}

# 등록수 제한 체크
list ($cntGoods) = $db->fetch("select count(*) from ".GD_GOODS."");
if ($godo[maxGoods]!="unlimited" && $godo[maxGoods]<=$cntGoods){
	echo "
	<div style='border:5 solid #B8B8DC;padding:8px;background:#f7f7f7'><b>→ 상품수 등록이 제한된 상태입니다</b></div><p>
	";
}

$returnUrl = ($_GET[returnUrl]) ? $_GET[returnUrl] : $_SERVER[HTTP_REFERER];
$btn_list = "<a href='{$returnUrl}'><img src='../img/btn_list.gif'></a>";

if($expire_dt < $now_date) {
	@include('shopTouch_expire_msg.php');
}

include "_shopTouch_goods_form.php";
include "../_footer.php";

?>