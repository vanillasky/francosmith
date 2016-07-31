<?
$channel = preg_replace('/_form\.([a-z]+)\.php/','$1',basename(__FILE__));
// 외부 주문건이므로 중계서버의 데이터를 동기화 한후 보여준다.

/*
$integrate_order = Core::loader('integrate_order');
$integrate_order -> doSync();
*/

// 주문정보
$orderInfo = $db->fetch("SELECT * FROM ".GD_INTEGRATE_ORDER." WHERE channel = '$channel' AND ordno = '".$_GET['ordno']."'",1);

if (!$orderInfo) {
	msg('주문정보가 존재하지 않습니다.',-1);
	exit;
}

?>
<div class="title title_top">주문상세내역</div>
<? @include dirname(__FILE__) . '/../selly/_market_order_form.php';	//마켓주문_인클루드 ?>
<div style="height:20px;"></div>
