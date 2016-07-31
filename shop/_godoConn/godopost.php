<?php
require("../lib/library.php");

$order_result = (array)$_POST['result']; //수신된 배송 결과
foreach($order_result as $each_result) {
	$query = $db->_query_print(
		'select ordno from gd_order where ordno=[s] and deliveryno="100" and deliverycode=[s]'
		,$each_result['deligdno'],$each_result['regino']
	);
	$ordResult = $db->_select($query);
	$ordno = $ordResult[0]['ordno'];
	if(!$ordno) continue;

	$each_result['treatcd']=(int)$each_result['treatcd'];
	switch($each_result['treatcd'])  {
		case 71:
			ctlStep($ordno,4, 'stock');
			set_prn_settleprice($ordno);
			break;
		case 50:
			ctlStep($ordno,3, 'stock');
			set_prn_settleprice($ordno);
			break;
	}
}

?>
9999