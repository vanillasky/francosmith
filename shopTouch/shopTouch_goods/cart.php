<?php
	include dirname(__FILE__) . "/../_shopTouch_header.php";
	@include $shopRootDir . "/conf/config.pay.php";
	@include $shopRootDir . "/lib/cart.class.php";

	chkMemberShopTouch();
	
	setcookie('gd_isDirect','',time() - 3600,'/');

	### 변수할당
	$cartpage = true;
	$orderitem_mode = "cart";
	$mode = ($_POST[mode]) ? $_POST[mode] : $_GET[mode];
	
	$cart = new Cart;

	switch ($mode){
		case "modItem": $cart->modCart($_POST[ea]); break;
		case "delItem": 
			arsort($_POST[idx]);
			foreach($_POST[idx] as $v) $cart->delCart($v); 
		break;
		case "empty": $cart->emptyCart(); break;
	}
	if ($mode) header("location:cart.php");

	$cart->calcu();

	$tpl->assign('cart',$cart);
	$tpl->print_('tpl');
?>