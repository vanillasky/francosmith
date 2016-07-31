<?
include "../lib.php";
// icon 의 키와, customicon 의 키를 합쳐서 루프.
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
alert("저장되었습니다.");
parent.location.reload();
</script>
';
