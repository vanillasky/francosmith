<?
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");

include "../lib/library.php";
include "../conf/config.php";

$ordno = $_GET['ordno'];
$query = "select step from ".GD_ORDER." where ordno=".$ordno;
list($step) = $db->fetch($query);

if($step==1){
	go($cfg['rootDir']."/order/order_end.php?ordno=".$ordno,"parent");
}
else{
	go($cfg['rootDir']."/order/order_fail.php?ordno=".$ordno,"parent");
}
?>