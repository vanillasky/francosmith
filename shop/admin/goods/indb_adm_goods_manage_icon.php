<?
include "../lib.php";
// icon �� Ű��, customicon �� Ű�� ���ļ� ����.
$ar_goodsno = @array_merge(@array_keys((array)$_POST[icon]),@array_keys((array)$_POST[customicon]));
$ar_goodsno = @array_unique($ar_goodsno);

$goods = Clib_Application::getModelClass('goods');

foreach ($ar_goodsno as $goodsno) {

	$_icon = @array_sum($_POST['icon'][$goodsno]);
	$_icon += (int)$_POST['customicon'][$goodsno];

	$goods->resetData();
	$goods->load($goodsno);
	$goods->setData('icon', $_icon);
	$goods->save();

}

echo '
<script>
alert("����Ǿ����ϴ�.");
parent.location.reload();
</script>
';
