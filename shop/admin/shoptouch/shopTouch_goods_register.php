<?
$location = "���θ� App���� > ���θ� App ��ǰ���";
include "../_header.php";

@include_once "../../lib/pAPI.class.php";
$pAPI = new pAPI();
$expire_dt = $pAPI->getExpireDate();
if(!$expire_dt) {
	msg('���� ��û�Ŀ� ��밡���� �޴��Դϴ�.', -1);
}

$now_date = date('Y-m-d 23:59:59');
$tmp_now_date = date('Y-m-d 23:59:59', mktime(0,0,0, substr($now_date, 5, 2), substr($now_date, 8, 2) - 30, substr($now_date, 0, 4)));
if($expire_dt < $tmp_now_date) {
	msg('���� ���Ⱓ ������ 30���� ���� ���񽺰� ���� �Ǿ����ϴ�.\n���񽺸� �ٽ� ��û�� �ֽñ� �ٶ��ϴ�.', -1);
}

# ��ϼ� ���� üũ
list ($cntGoods) = $db->fetch("select count(*) from ".GD_GOODS."");
if ($godo[maxGoods]!="unlimited" && $godo[maxGoods]<=$cntGoods){
	echo "
	<div style='border:5 solid #B8B8DC;padding:8px;background:#f7f7f7'><b>�� ��ǰ�� ����� ���ѵ� �����Դϴ�</b></div><p>
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