<?
@include "../lib.php";
@include "../../conf/config.selly.php";
@include "../../lib/selly.class.php";
@include "../../lib/parsexml.class.php";
$file = "../../conf/godomall.cfg.php";
$file = file($file);
$godo = decode($file[1],1);
@include "./code.php";
$file	= dirname(__FILE__)."/../../conf/godomall.cfg.php";
$file	= file($file);
$godo	= decode($file[1],1);

$goodsno		= ($_GET['goodsno'])		? trim($_GET['goodsno'])		: "";
$origin			= ($_GET['origin'])			? trim($_GET['origin'])			: "";
$delivery_type	= ($_GET['delivery_type'])	? trim($_GET['delivery_type'])	: "";
$delivery_price	= ($_GET['delivery_price'])	? trim($_GET['delivery_price'])	: 0;

$originCode = array_keys($selly['origin'], $origin);
$deliveryTypeCode = array_keys($selly['delivery_type'], $delivery_type);

if(!$goodsno) {
	echo "1||��ǰ��ȣ�� ���۵��� �ʾҽ��ϴ�.||";
}
else {
	$xmlParser = new XMLParser();	// XML�ļ� Ŭ����
	$st = new selly();
	$st->origin = $originCode[0];
	$st->delivery_type = $deliveryTypeCode[0];
	$st->delivery_price = $delivery_price;
	$st->shop_cd = $godo['sno'];
	$st->ajaxGoods($goodsno);
}
?>