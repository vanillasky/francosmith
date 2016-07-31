<?

header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");

include "../../lib/library.php";
include "../../lib/cart.class.php";

$isDirect = ($_COOKIE[gd_isDirect]) ? 1 : 0;

$cart = new Cart($isDirect);
$param = array(
	'mode' => '0',
	'zipcode' => $_GET[zipcode],
	'emoney' => 0,
	'deliPoli' => 0,
	'coupon' => 0
);

$delivery = getDeliveryMode($param);
$cart -> delivery = $delivery[price] + 0;
$cart->calcu();
?>

<? if ($_GET[popup]){ ?>
var popupWindow = true;
<? } else { ?>
var popupWindow = false;
<? } ?>
var doc = (popupWindow) ? opener.document : document;

if (doc.getElementById('cart_goodsprice')){
	var goodsprice	= uncomma(doc.getElementById('cart_goodsprice').innerHTML);
	var delivery	= parseInt(<?=$cart->delivery?>);
}

if (doc.getElementById('cart_delivery')){
	doc.getElementById('cart_delivery').innerHTML = comma(delivery);
	doc.getElementById('cart_totalprice').innerHTML = comma(goodsprice+delivery);
}
if (doc.getElementById('paper_delivery')){
	doc.getElementById('paper_delivery').innerHTML = comma(delivery);
	if (popupWindow) opener.calcu_settle();
	else calcu_settle();
}
if (popupWindow) window.close();