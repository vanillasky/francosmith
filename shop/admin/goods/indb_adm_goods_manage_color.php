<?
include "../lib.php";

$goods = Clib_Application::getModelClass('goods');

foreach($_POST['setColor'] as $goodsno => $color) {

	$goods->resetData();
	$goods->load($goodsno);
	$goods->setData('color', $color);
	$goods->save();
}

echo '
<script>
alert("������ �����Ǿ����ϴ�.");
parent.location.reload();
</script>
';
