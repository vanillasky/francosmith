<?php
/**
 * �߰輭���� ���, �Ǹ���� ó��
 * @author sunny, oneorzero
 */
include "../_header.popup.php";
$naverCheckoutAPI = Core::loader('naverCheckoutAPI');

if(is_array($_POST['orderNo'])) {
	$arOrderNo = $_POST['orderNo'];
}
else {
	$arOrderNo = array($_POST['orderNo']);
}

$CancelReason = $_POST['CancelReason'];
$CancelReasonDetail = $_POST['CancelReasonDetail'];


?>

<div class="title title_top">���̹� üũ�ƿ� �Ǹ���� ó��</div>

<br>
�߰輭���� ��� �� ...<br>
<?
flush();
foreach($arOrderNo as $orderNo) {
	$query = $db->_query_print('select ORDER_OrderID from gd_navercheckout_order where orderNo=[s]',$orderNo);
	$result = $db->_select($query);
	echo '�ֹ���ȣ '.$result[0]['ORDER_OrderID'].'�� ���� �Ǹ����ó�� ���Դϴ�<br>';
	flush();
	if($naverCheckoutAPI->CancelSale($orderNo,$CancelReason,$CancelReasonDetail)) {
		echo '�Ǹ����ó�� ���������� ó���Ͽ����ϴ�<br><br>';
	}
	else {
		echo '�Ǹ���� �۾� �� ������ �߻��߽��ϴ�<br>'.$naverCheckoutAPI->error.'<br><br>';
	}
	flush();
}
echo '�Ϸ�Ǿ����ϴ�';
?>
<br><br>
<input type="button" value="�ݱ�" onclick="parent.location.href=parent.location.href;">
