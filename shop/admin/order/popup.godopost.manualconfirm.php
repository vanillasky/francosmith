<?php
include "../_header.popup.php";
include "../../lib/godopost.class.php";

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

$godopost = new godopost();
$result = $godopost->result_confirm();

$changeResult=array(1=>0,2=>0,3=>0,4=>0);
foreach($result as $eachResult) {
	$query = $db->_query_print(
		'select ordno from gd_order where ordno=[s] and deliveryno="100" and  deliverycode=[s]'
		,$eachResult['deligdno'],$eachResult['regino']
	);
	$ordResult = $db->_select($query);
	$ordno = $ordResult[0]['ordno'];
	if(!$ordno) continue;
	
	$eachResult['treatcd']=(int)$eachResult['treatcd'];
	switch($eachResult['treatcd'])  {
		case 71:
			$changeResult[4]++;
			ctlStep($ordno,4);
			break;
	}
	
}

?>
<div class="title title_top">우체국택배 수동확인</div>

<?=count($result)?>건에 대한 결과를 받았습니다.<br>
