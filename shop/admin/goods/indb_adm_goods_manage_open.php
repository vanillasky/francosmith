<?
include "../lib.php";
$arTarget = isset($_POST['target']) ? $_POST['target'] : '';
$arStatus = isset($_POST['open']) ? $_POST['open'] : '';

$goods = Clib_Application::getModelClass('goods');

if (is_array($arTarget)) {

	// ����ϼ� ��ǰ ���� ���� ��������
	$cfgMobileShop = Clib_Application::getLoadConfig('config.mobileShop');

	// where �� �����.
	foreach ($arTarget as $key => $goodsno) {

		$open = ($arStatus[$goodsno] == '1') ? true : false;

		$goods->resetData();
		$goods->load($goodsno);
		$goods->setData('open', $open ? '1' : '0');

		if($cfgMobileShop['vtype_goods'] != 1) {
			$goods->setData('open_mobile', $goods->getData('open'));
		}

		daum_goods_diff($goodsno,$goods);	// ���� ��� EP
		
		$goods->save();

	}

}

echo '
<script>
alert("����Ǿ����ϴ�.");
parent.location.reload();
</script>
';
