<?
// deprecated. redirect to new page;
header('location: ./adm_goods_form.php?'.$_SERVER['QUERY_STRING']);
exit;
$location = "상품관리 > 상품등록";
include "../_header.php";

# 등록수 제한 체크
list ($cntGoods) = $db->fetch("select count(*) from ".GD_GOODS."");
if ($godo[maxGoods]!="unlimited" && $godo[maxGoods]<=$cntGoods){
	echo "
	<div style='border:5 solid #B8B8DC;padding:8px;background:#f7f7f7'><b>→ 상품수 등록이 제한된 상태입니다</b></div><p>
	";
}

$returnUrl = ($_GET[returnUrl]) ? $_GET[returnUrl] : $_SERVER[HTTP_REFERER];
$btn_list = "<a href='{$returnUrl}'><img src='../img/btn_list.gif'></a>";

include "_form.php";
include "../_footer.php";

?>
