<?php
include dirname(__FILE__). '/../lib.php';

$returnArray = array();
$insgoWidgetAdmin = Core::loader("insgoWidgetAdmin");
if($insgoWidgetAdmin->checkInsgoWidgetAble() === true){
	$returnArray = $insgoWidgetAdmin->getIframe($_POST);
}

echo gd_json_encode($returnArray);
?>