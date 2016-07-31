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
<div class="title title_top">네이버 체크아웃 주문정보 변환</div>
<div id="el-screen" style="width:100%;border:1px solid #E6E6E6;height:300px;overflow-y:auto;padding:10px;margin:0 0 10px 0;">
<?
foreach($_POST['OldOrderID'] as $OldOrderID) {

	echo '<strong>'.$OldOrderID.'</strong> 의 변환을 위해 중계서버와 통신중입니다.<br>';
	echo '<script>document.getElementById("el-screen").scrollTop = document.getElementById("el-screen").scrollHeight;</script>';

	$param = array(
		'OldOrderID' => $OldOrderID
	);
	flush();

	if ((($rs = $naverCheckoutAPI->request( $api_name , $param )) !== false) && $db->query("UPDATE gd_navercheckout_order SET migrated = '1' WHERE ORDER_OrderID = '$OldOrderID'")) {

		// 주문 통합 리스트의 데이터를 삭제 한다
		$db->query("DELETE FROM ".GD_INTEGRATE_ORDER." WHERE channel = 'checkout' AND ordno = '$OldOrderID'");
		$db->query("DELETE FROM ".GD_INTEGRATE_ORDER_ITEM." WHERE channel = 'checkout' AND ordno = '$OldOrderID'");

		echo '정상적으로 처리하였습니다<br><br>';
	}
	else {
		echo '작업 중 오류가 발생했습니다<br>'.$naverCheckoutAPI->error.'<br><br>';
	}

}

echo '<hr>완료되었습니다<br>';
echo '<script>document.getElementById("el-screen").scrollTop = document.getElementById("el-screen").scrollHeight;</script>';
?>
</div>

<input type="button" value="닫기" onclick="parent.location.reload();">
