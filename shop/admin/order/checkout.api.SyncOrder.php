<?php
/**
 * �߰輭���� ���, �ֹ����� ����ȭ
 * @author sunny, oneorzero
 */
include "../_header.popup.php";
$naverCheckoutAPI = Core::loader('naverCheckoutAPI');
$orderNo = (int)$_GET['orderNo'];

?>

<div class="title title_top">���̹� üũ�ƿ� �ֹ����� ����ȭ</div>

<br>
�߰輭���� ��� �� ...<br>
<?
flush();
if($naverCheckoutAPI->SyncOrder($orderNo)) {
	echo '����ȭ�� ���������� ó���Ͽ����ϴ�';
}
else {
	echo '����ȭ �۾� �� ������ �߻��߽��ϴ�<br>'.$naverCheckoutAPI->error;
}

?>
<br><br>
<input type="button" value="�ݱ�" onclick="parent.location.href=parent.location.href;">
