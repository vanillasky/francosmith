<?php
/**
 * 중계서버와 통신, 주문접수 처리
 * @author sunny, oneorzero
 */
include "../_header.popup.php";
$naverCheckoutAPI = Core::loader('naverCheckoutAPI');
$checkoutapi = $config->load('checkoutapi');
// $stock_PlaceOrder = $checkoutapi['stock_PlaceOrder']; 재고?

if(is_array($_POST['orderNo'])) {
	$arOrderNo = $_POST['orderNo'];
}
else {
	$arOrderNo = array($_POST['orderNo']);
}

?>

<div class="title title_top">네이버 체크아웃 주문접수 처리</div>

<br>
중계서버와 통신 중 ...<br>
<?
flush();
foreach($arOrderNo as $orderNo) {
	$query = $db->_query_print('select ORDER_OrderID,stockProcess from gd_navercheckout_order where orderNo=[s]',$orderNo);
	$result = $db->_select($query);
	echo '주문번호 '.$result[0]['ORDER_OrderID'].'에 대한 주문접수처리 중입니다<br>';
	flush();
	if($naverCheckoutAPI->PlaceOrder($orderNo)) {
		echo '주문접수처리 정상적으로 처리하였습니다<br><br>';
		flush();
		// 재고처리

	}
	else {
		echo '주문접수처리 작업 중 오류가 발생했습니다<br>'.$naverCheckoutAPI->error.'<br><br>';
		flush();
	}

}
echo '완료되었습니다';
?>
<br><br>
<input type="button" value="닫기" onclick="parent.location.href=parent.location.href;">
