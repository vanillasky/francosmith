<?
include "../lib.php";
$arTarget = isset($_POST['target']) ? $_POST['target'] : '';
$arStatus = isset($_POST['open']) ? $_POST['open'] : '';

$goods = Clib_Application::getModelClass('goods');

if (is_array($arTarget)) {

	// 모바일샵 상품 노출 설정 가져오기
	$cfgMobileShop = Clib_Application::getLoadConfig('config.mobileShop');

	// where 절 만들기.
	foreach ($arTarget as $key => $goodsno) {

		$open = ($arStatus[$goodsno] == '1') ? true : false;

		$goods->resetData();
		$goods->load($goodsno);
		$goods->setData('open', $open ? '1' : '0');

		if($cfgMobileShop['vtype_goods'] != 1) {
			$goods->setData('open_mobile', $goods->getData('open'));
		}

		daum_goods_diff($goodsno,$goods);	// 다음 요약 EP
		
		$goods->save();

	}

}

echo '
<script>
alert("저장되었습니다.");
parent.location.reload();
</script>
';
