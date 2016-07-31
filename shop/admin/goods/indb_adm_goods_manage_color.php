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
alert("색상이 수정되었습니다.");
parent.location.reload();
</script>
';
