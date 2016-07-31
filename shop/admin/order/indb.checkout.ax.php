<?php
/**
 * ���̹�üũ�ƿ� �ֹ����� �� �߼�ó��
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

$processMessage=':: ���̹�üũ�ƿ� �ֹ� ó�� ���'.PHP_EOL;
foreach($checkoutPlaceOrder as $eachOrderID) {
	$query = $db->_query_print('select orderNo from gd_navercheckout_order where ORDER_OrderID=[s]',$eachOrderID);
	$result = $db->_select($query);
	if($naverCheckoutAPI->PlaceOrder($result[0]['orderNo'])) {
		$processMessage.= '�ֹ���ȣ '.$eachOrderID.'�� ���� �ֹ������� ���������� ó���Ͽ����ϴ�'.PHP_EOL;
	}
	else {
		$processMessage.= '�ֹ���ȣ '.$eachOrderID.'�� ���� �ֹ����� ó���� �����Ͽ����ϴ�'.PHP_EOL.$naverCheckoutAPI->error.PHP_EOL.PHP_EOL;
	}
}

foreach($checkoutShipOrder as $eachOrderID) {
	$query = $db->_query_print('select orderNo from gd_navercheckout_order where ORDER_OrderID=[s]',$eachOrderID);
	$result = $db->_select($query);
	$ShippingCompleteDate[$eachOrderID] = date('Y-m-d\TH:i:s\Z',strtotime('+9 hours',strtotime($ShippingCompleteDate[$eachOrderID])));
	if($naverCheckoutAPI->ShipOrder($result[0]['orderNo'],$ShippingCompleteDate[$eachOrderID],$ShippingCompany[$eachOrderID],$TrackingNumber[$eachOrderID])) {
		$processMessage .= '�ֹ���ȣ '.$eachOrderID.'�� ���� �߼��� ���������� ó���Ͽ����ϴ�'.PHP_EOL;
	}
	else {
		$processMessage .= '�ֹ���ȣ '.$eachOrderID.'�� ���� �߼� ó���� �����Ͽ����ϴ�'.PHP_EOL.$naverCheckoutAPI->error.PHP_EOL.PHP_EOL;
	}
}

echo $processMessage;
?>
