<?php
include "../_header.popup.php";
$naverCheckoutAPI = Core::loader('naverCheckoutAPI_4');

if (!isset($_POST['OldOrderID'])) {
	echo '<script>parent.closeLayer();</script>';
	exit;
}

$api_name = 'GetMigratedProductOrderList';
unset($_POST['x'],$_POST['y']);


?>
<div class="title title_top">���̹� üũ�ƿ� �ֹ����� ��ȯ</div>
<div id="el-screen" style="width:100%;border:1px solid #E6E6E6;height:300px;overflow-y:auto;padding:10px;margin:0 0 10px 0;">
<?
foreach($_POST['OldOrderID'] as $OldOrderID) {

	echo '<strong>'.$OldOrderID.'</strong> �� ��ȯ�� ���� �߰輭���� ������Դϴ�.<br>';
	echo '<script>document.getElementById("el-screen").scrollTop = document.getElementById("el-screen").scrollHeight;</script>';

	$param = array(
		'OldOrderID' => $OldOrderID
	);
	flush();

	if ((($rs = $naverCheckoutAPI->request( $api_name , $param )) !== false) && $db->query("UPDATE gd_navercheckout_order SET migrated = '1' WHERE ORDER_OrderID = '$OldOrderID'")) {

		// �ֹ� ���� ����Ʈ�� �����͸� ���� �Ѵ�
		$db->query("DELETE FROM ".GD_INTEGRATE_ORDER." WHERE channel = 'checkout' AND ordno = '$OldOrderID'");
		$db->query("DELETE FROM ".GD_INTEGRATE_ORDER_ITEM." WHERE channel = 'checkout' AND ordno = '$OldOrderID'");

		echo '���������� ó���Ͽ����ϴ�<br><br>';
	}
	else {
		echo '�۾� �� ������ �߻��߽��ϴ�<br>'.$naverCheckoutAPI->error.'<br><br>';
	}

}

echo '<hr>�Ϸ�Ǿ����ϴ�<br>';
echo '<script>document.getElementById("el-screen").scrollTop = document.getElementById("el-screen").scrollHeight;</script>';
?>
</div>

<input type="button" value="�ݱ�" onclick="parent.location.reload();">
