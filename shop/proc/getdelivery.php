<?
/*------------------------------------------------------------------------------
* Order's Delivery Price Calculate
date		2008/03/25
$_GET['mode']			0:장바구니,1:other
$_GET['zipcode']		우편번호
$_GET['emoney']			사용적립금
$_GET['deliPoli']		배송정책
$_GET['coupon']			쿠폰할인금액
$_GET['price']			결제금액
------------------------------------------------------------------------------*/
include_once "../lib/library.php";
@include "../conf/config.pay.php";
### 콤마 제거
$_GET['coupon'] = str_replace(',','',$_GET['coupon']);
$_GET['emoney'] = str_replace(',','',$_GET['emoney']);

if($_GET[mode] == 'order'){

	$param = array(
	'mode' => '0',
	'zipcode' => $_GET['zipcode'],
	'emoney' => $_GET['emoney'],
	'deliPoli' => $_GET['deliPoli'],
	'coupon' => $_GET['coupon'],
	'coupon_emoney' => $_GET['coupon_emoney'],
	'road_address'	=> $_GET['road_address'],
	'address'	=> $_GET['address'],
	'address_sub'	=> $_GET['address_sub']
	);

}else if($_GET[mode] == 'mypage'){

	$param = array(
	'mode' => '1',
	'zipcode' => $_GET['zipcode'],
	'emoney' => $_GET['emoney'],
	'deliPoli' => $_GET['deliPoli'],
	'coupon' => $_GET['coupon'],
	'coupon_emoney' => $_GET['coupon_emoney'],
	'price' => $_GET['price']
	);
}
$delivery = getDeliveryMode($param);
?>
/*<script>*/	// color coding 위해서.

<? if($delivery[type]=='후불' && !$delivery[freeDelivery]){ ?>
	document.getElementById('paper_delivery_msg1').style.display='none';
	document.getElementById('paper_delivery_msg2').style.display='block';
	document.getElementById('paper_delivery_msg2').innerHTML = '<?=addslashes($delivery[msg])?>';
	try {
		document.getElementById('paper_delivery_msg_extra').style.display='none';
	}
	catch (e) { }
<? } else { ?>
	document.getElementById('paper_delivery_msg1').style.display='block';
	document.getElementById('paper_delivery_msg2').style.display='none';
	try {
	<? if ($delivery[extra_price] > 0) { ?>
		document.getElementById('paper_delivery_msg_extra').innerHTML = '(지역별 배송비 <?=number_format($delivery[extra_price])?> 원 포함)';
		document.getElementById('paper_delivery_msg_extra').style.display='block';
	<? } else { ?>
		document.getElementById('paper_delivery_msg_extra').style.display='none';
	<? } ?>
	}
	catch (e) { }
<? } ?>
<? if ($delivery['default_name']) { ?>
	try {
		var el = document.getElementById('el-default-delivery');
		var msg = '<?=addslashes($delivery['default_name'])?>';

		<? if ($delivery['default_type_conditional_free']) { ?>
			msg += '<br>(조건부무료)';
		<? } else if ($delivery['default_type']=='선불') { ?>
			msg += '<br>(<?=number_format($delivery['default_price'])?>원)';
		<? } else if ($delivery['default_type']=='후불') { ?>
			msg += '<br>(<?=addslashes($delivery[msg])?>)';
		<? } ?>
		el.innerHTML = msg;
	}
	catch (e) { }
<? } ?>

<? if ($delivery['unableMenu']) { ?>
	if(document.getElementById('paper_delivery_menu'))document.getElementById('paper_delivery_menu').style.display='none';
	document.getElementById('paper_delivery_msg1').style.display='none';
	document.getElementById('paper_delivery_msg2').style.display='block';
	document.getElementById('paper_delivery_msg2').innerHTML = '<?=addslashes($delivery[msg])?>';
<? } ?>


document.getElementById('paper_delivery').innerHTML = '<?=number_format($delivery[price])?>';
calcu_settle();