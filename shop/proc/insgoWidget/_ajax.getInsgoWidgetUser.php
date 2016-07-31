<?php
@include dirname(__FILE__) . "/../../lib/library.php";

$insgoWidgetData = array();
$insgoWidgetUser = Core::loader('insgoWidgetUser');
$insgoWidgetData = $insgoWidgetUser->getInsgoWidgetData($_POST);

echo gd_json_encode($insgoWidgetData);
?>