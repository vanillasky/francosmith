<?
$mode = $_GET[mode];
if(!$mode)$mode = "orderXls";

$location = "데이터관리 > 주문엑셀설정";
include "../_header.popup.php";
include "set_orderxls_form.php";
?>
