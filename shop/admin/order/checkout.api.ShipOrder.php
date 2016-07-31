<?php
/**
 * 중계서버와 통신, 주문발송 처리
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

<div class="title title_top">네이버 체크아웃 주문발송 처리</div>
<br>
중계서버와 통신 중 ...<br>
<?
flush();
foreach($arRequest as $eachRequest) {
	$query = $db->_query_print('select ORDER_OrderID from gd_navercheckout_order where orderNo=[s]',$eachRequest['orderNo']);
	$result = $db->_select($query);
	echo '주문번호 '.$result[0]['ORDER_OrderID'].'에 대한 주문발송처리 중입니다<br>';
	flush();
	if($naverCheckoutAPI->ShipOrder($eachRequest['orderNo'],$eachRequest['ShippingCompleteDate'],$eachRequest['ShippingCompany'],$eachRequest['TrackingNumber'])) {
		echo '주문발송처리 정상적으로 처리하였습니다<br><br>';
	}
	else {
		echo '주문발송처리 작업 중 오류가 발생했습니다<br>'.$naverCheckoutAPI->error.'<br><br>';
	}
	flush();
}
echo '완료되었습니다';
?>
<br><br>
<input type="button" value="닫기" onclick="parent.location.href=parent.location.href;">
