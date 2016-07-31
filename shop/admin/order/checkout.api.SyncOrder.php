<?php
/**
 * 중계서버와 통신, 주문정보 동기화
 * @author sunny, oneorzero
 */
include "../_header.popup.php";
$naverCheckoutAPI = Core::loader('naverCheckoutAPI');
$orderNo = (int)$_GET['orderNo'];

?>

<div class="title title_top">네이버 체크아웃 주문정보 동기화</div>

<br>
중계서버와 통신 중 ...<br>
<?
flush();
if($naverCheckoutAPI->SyncOrder($orderNo)) {
	echo '동기화를 정상적으로 처리하였습니다';
}
else {
	echo '동기화 작업 중 오류가 발생했습니다<br>'.$naverCheckoutAPI->error;
}

?>
<br><br>
<input type="button" value="닫기" onclick="parent.location.href=parent.location.href;">
