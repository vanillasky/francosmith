<?php

$naverCheckoutV2ButtonPath = dirname(__FILE__).'/NaverCheckout_V2_Button.php';
if (file_exists($naverCheckoutV2ButtonPath)) {
	include $naverCheckoutV2ButtonPath;
	exit;
}

$_GOODSNO = $_GET['goodsno'];

require dirname(__FILE__).'/../lib/library.php';

$goods = $db->fetch('SELECT goodsno, goodsnm, runout, usestock, totstock FROM gd_goods WHERE goodsno='.$_GOODSNO, true);

$naverCheckout = &load_class('naverCheckoutMobile', 'naverCheckoutMobile');
if($naverCheckout->isAvailable() && $naverCheckout->checkGoods($_GOODSNO, $goods['goodsnm']))
{
	if((int)$goods['runout']) $checkoutEnable = false;
	else if($goods['usestock']==='o' && (int)$goods['totstock']===0) $checkoutEnable = false;
	else $checkoutEnable = true;
	echo $naverCheckout->getButtonTag('MULTI_GOODS_VIEW', $checkoutEnable);
}

?>