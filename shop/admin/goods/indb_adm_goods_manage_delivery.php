<?
include "../lib.php";

if (!isset($_POST['set_delivery_type'])) {
	msg('��ǰ�� ��ۺ� ������ �ּ���.',-1);
	exit;
}

if ($_POST['set_delivery_type'] > 1 && empty($_POST['set_goods_delivery'.$_POST['set_delivery_type']])) {
	msg('��ۺ� �Է��� �ּ���.',-1);
	exit;
}

$goods = Clib_Application::getModelClass('goods');

foreach($_POST['chk'] as $goodsno) {
	$goods->resetData();
	$goods->load($goodsno);
	$goods->setData('delivery_type', $_POST['set_delivery_type']);
	$goods->setData('goods_delivery', $_POST['set_goods_delivery'.$_POST['set_delivery_type']]);
	daum_goods_diff($goodsno,$goods);
	$goods->save();
}

echo '
<script>
alert("���� �Ǿ����ϴ�.");
parent.location.reload();
</script>
';
