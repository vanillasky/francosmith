<?php
/**
 * �߰輭���� ���, �ֹ����� ó��
 * @author sunny, oneorzero
 */
include "../_header.popup.php";
$naverCheckoutAPI = Core::loader('naverCheckoutAPI');
$checkoutapi = $config->load('checkoutapi');
// $stock_PlaceOrder = $checkoutapi['stock_PlaceOrder']; ���?

if(is_array($_POST['orderNo'])) {
	$arOrderNo = $_POST['orderNo'];
}
else {
	$arOrderNo = array($_POST['orderNo']);
}

?>

<div class="title title_top">���̹� üũ�ƿ� �ֹ����� ó��</div>

<br>
�߰輭���� ��� �� ...<br>
<?
flush();
foreach($arOrderNo as $orderNo) {
	$query = $db->_query_print('select ORDER_OrderID,stockProcess from gd_navercheckout_order where orderNo=[s]',$orderNo);
	$result = $db->_select($query);
	echo '�ֹ���ȣ '.$result[0]['ORDER_OrderID'].'�� ���� �ֹ�����ó�� ���Դϴ�<br>';
	flush();
	if($naverCheckoutAPI->PlaceOrder($orderNo)) {
		echo '�ֹ�����ó�� ���������� ó���Ͽ����ϴ�<br><br>';
		flush();
		// ���ó��

	}
	else {
		echo '�ֹ�����ó�� �۾� �� ������ �߻��߽��ϴ�<br>'.$naverCheckoutAPI->error.'<br><br>';
		flush();
	}

}
echo '�Ϸ�Ǿ����ϴ�';
?>
<br><br>
<input type="button" value="�ݱ�" onclick="parent.location.href=parent.location.href;">
