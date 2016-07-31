<?php
include '../lib/library.php';
include '../conf/payco.cfg.php';
@include '../conf/config.mobileShop.php';
include '../lib/cart.class.php';

//goodsView - 상품상세페이지, goodsCart - 장바구니
$checkoutType = ($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];
$payco = Core::loader('payco');
if(!$checkoutType) $payco->msgLocate('간편구매 타입을 확인하여 주세요.', 'CHECKOUT', $_GET['isMobile']);
if(!$payco) $payco->msgLocate('payco.class.php 파일을 확인해 주세요.', 'CHECKOUT', $_GET['isMobile']);
if(!function_exists('getordno')) $payco->msgLocate('주문번호를 생성할 수 없습니다.', 'CHECKOUT', $_GET['isMobile']);
if(!$payco->check_level('CHECKOUT', $sess)) $payco->msgLocate('회원은 페이코 간편구매 서비스를 이용할 수 없습니다.', 'CHECKOUT', $_GET['isMobile']);
$imgPath = $cfg['rootDir'] . '/order/card/payco/img/';

//주문생성
$ordno = getordno();

if($_GET['isMobile']){
	$actionUrl = $cfgMobileShop['mobileShopRootDir']. '/ord/indb.php';
}
else {
	$actionUrl = '../order/indb.php';
}

switch($checkoutType){
	case 'goodsView':
		if(!$_POST['goodsno']) $payco->msgLocate('상품번호가 없습니다.', 'CHECKOUT', $_GET['isMobile']);

		//주문가능여부 체크
		$errorMsg = $payco->check_paycoOrderAble('CHECKOUT', $_POST['goodsno'], $_GET['isMobile']);
		if($errorMsg) $payco->msgLocate($errorMsg, 'CHECKOUT', $_GET['isMobile']);

		// 장바구니 모드가 아니면, 구매 상품을 장바구니에 담아 처리 한다.
		if(!$_COOKIE['gd_isDirect']) setcookie('gd_isDirect', 1, 0, '/');

		$cart = new Cart(1);

		// 멀티옵션
		if ($_POST[multi_ea]) {
			$_keys = array_keys($_POST[multi_ea]);
			for ($i=0, $m=sizeof($_keys);$i<$m;$i++) {
				$_opt = $_POST[multi_opt][ $_keys[$i] ];
				$_ea = $_POST[multi_ea][ $_keys[$i] ];
				$_addopt = $_POST[multi_addopt][ $_keys[$i] ];
				$_addopt_inputable = $_POST[multi_addopt_inputable][ $_keys[$i] ];

				$cart->addCart($_POST[goodsno], $_opt, $_addopt, $_addopt_inputable, $_ea, $_POST[goodsCoupon]);
			}
		}
		else {
			$cart->addCart($_POST[goodsno], $_POST[opt], $_POST[addopt], $_POST[_addopt_inputable], $_POST[ea], $_POST[goodsCoupon]);
		}
	break;

	case 'goodsCart':
		$_GET[idxs] = isset($_GET[idxs]) ? $_GET[idxs] : 'all';

		$cart = new Cart();
		$cart->setOrder($_GET[idxs]);

		foreach($cart->item as $key => $goodsData){
			//주문가능여부 체크
			$errorMsg = $payco->check_paycoOrderAble('CHECKOUT', $goodsData['goodsno'], $_GET['isMobile']);
			if($errorMsg) $payco->msgLocate($errorMsg, 'CHECKOUT');
		}

	break;
}
?>
<html>
<head>
	<style>
	body				{ margin: 0px; padding: 0px; overflow: hidden;}
	.layout				{ width: 100%; height: 600px; text-align: center; }
	.layoutTop			{ width: 100%; text-align: left; padding: 20px 0px 20px 20px; }
	.layoutSolid		{ width: 100%; height:4px; background-color: #ff0008; }
	.progressImage		{ width: 100%; text-align: center; margin-top: 150px;}
	.progressImageSub1	{ margin-top: 39px;}
	.progressImageSub2	{ margin-top: 33px;}
	</style>
</head>
<body>
	<script type="text/javascript">
	window.onload = function(){
		document.paycoForm.submit();
	}
	</script>

	<div class="layout">
		<div class="layoutTop"><img src="<?php echo $imgPath; ?>payco_logo.gif"></div>
		<div class="layoutSolid"></div>
		<div class="progressImage">
			<div><img src="<?php echo $imgPath; ?>payco_img.gif"></div>
			<div class="progressImageSub1"><img src="<?php echo $imgPath; ?>payco_icon_loading.gif"></div>
			<div class="progressImageSub2"><img src="<?php echo $imgPath; ?>payco_text_loading.gif"></div>
		</div>
	</div>

	<form name="paycoForm" method="post" action="<?php echo $actionUrl; ?>">
		<input type="hidden" name="ordno" value="<?php echo $ordno; ?>" />
		<input type="hidden" name="isMobile" value="<?php echo $_GET['isMobile']; ?>" />
		<input type="hidden" name="settlekind" value="t" />
		<input type="hidden" name="paycoType" value="CHECKOUT" />
		<input type="hidden" name="paycoCheckoutType" value="<?php echo $checkoutType; ?>" />
	</form>
</body>
</html>