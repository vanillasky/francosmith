<?php
/**
 * 네이버체크아웃 주문접수 및 발송처리
 * @author sunny, oneorzero
 */
include "../lib.php";
$naverCheckoutAPI = Core::loader('naverCheckoutAPI');
$checkoutapi = $config->load('checkoutapi');


$checkoutPlaceOrder = (array)$_POST['checkoutPlaceOrder'];
$checkoutShipOrder = (array)$_POST['checkoutShipOrder'];
$ShippingCompleteDate = (array)$_POST['ShippingCompleteDate'];
$ShippingCompany = (array)$_POST['ShippingCompany'];
$TrackingNumber = (array)$_POST['TrackingNumber'];

$processMessage=':: 네이버체크아웃 주문 처리 결과'.PHP_EOL;
foreach($checkoutPlaceOrder as $eachOrderID) {
	$query = $db->_query_print('select orderNo from gd_navercheckout_order where ORDER_OrderID=[s]',$eachOrderID);
	$result = $db->_select($query);
	if($naverCheckoutAPI->PlaceOrder($result[0]['orderNo'])) {
		$processMessage.= '주문번호 '.$eachOrderID.'에 대한 주문접수를 정상적으로 처리하였습니다'.PHP_EOL;
	}
	else {
		$processMessage.= '주문번호 '.$eachOrderID.'에 대한 주문접수 처리를 실패하였습니다'.PHP_EOL.$naverCheckoutAPI->error.PHP_EOL.PHP_EOL;
	}
}

foreach($checkoutShipOrder as $eachOrderID) {
	$query = $db->_query_print('select orderNo from gd_navercheckout_order where ORDER_OrderID=[s]',$eachOrderID);
	$result = $db->_select($query);
	$ShippingCompleteDate[$eachOrderID] = date('Y-m-d\TH:i:s\Z',strtotime('+9 hours',strtotime($ShippingCompleteDate[$eachOrderID])));
	if($naverCheckoutAPI->ShipOrder($result[0]['orderNo'],$ShippingCompleteDate[$eachOrderID],$ShippingCompany[$eachOrderID],$TrackingNumber[$eachOrderID])) {
		$processMessage .= '주문번호 '.$eachOrderID.'에 대한 발송을 정상적으로 처리하였습니다'.PHP_EOL;
	}
	else {
		$processMessage .= '주문번호 '.$eachOrderID.'에 대한 발송 처리를 실패하였습니다'.PHP_EOL.$naverCheckoutAPI->error.PHP_EOL.PHP_EOL;
	}
}

echo $processMessage;
?>
