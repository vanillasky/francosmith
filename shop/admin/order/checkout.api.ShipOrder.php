<?php
/**
 * �߰輭���� ���, �ֹ��߼� ó��
 * @author sunny, oneorzero
 */
include "../_header.popup.php";
$naverCheckoutAPI = Core::loader('naverCheckoutAPI');
$postRequest = (array)$_POST['request'];

$arRequest=array();
foreach($postRequest as $eachRequest) {
	$eachRequest['ShippingCompleteDate'] = date('Y-m-d\TH:i:s\Z',strtotime('+9 hours',strtotime($eachRequest['ShippingCompleteDate'])));
	$arRequest[]=array(
		'orderNo'=>$eachRequest['orderNo'],
		'ShippingCompleteDate'=>$eachRequest['ShippingCompleteDate'],
		'ShippingCompany'=>$eachRequest['ShippingCompany'],
		'TrackingNumber'=>$eachRequest['TrackingNumber'],
	);
}

?>

<div class="title title_top">���̹� üũ�ƿ� �ֹ��߼� ó��</div>
<br>
�߰輭���� ��� �� ...<br>
<?
flush();
foreach($arRequest as $eachRequest) {
	$query = $db->_query_print('select ORDER_OrderID from gd_navercheckout_order where orderNo=[s]',$eachRequest['orderNo']);
	$result = $db->_select($query);
	echo '�ֹ���ȣ '.$result[0]['ORDER_OrderID'].'�� ���� �ֹ��߼�ó�� ���Դϴ�<br>';
	flush();
	if($naverCheckoutAPI->ShipOrder($eachRequest['orderNo'],$eachRequest['ShippingCompleteDate'],$eachRequest['ShippingCompany'],$eachRequest['TrackingNumber'])) {
		echo '�ֹ��߼�ó�� ���������� ó���Ͽ����ϴ�<br><br>';
	}
	else {
		echo '�ֹ��߼�ó�� �۾� �� ������ �߻��߽��ϴ�<br>'.$naverCheckoutAPI->error.'<br><br>';
	}
	flush();
}
echo '�Ϸ�Ǿ����ϴ�';
?>
<br><br>
<input type="button" value="�ݱ�" onclick="parent.location.href=parent.location.href;">
