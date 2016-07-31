<?
include "./lib/library.php";

if ($_GET['cp']) {
	$comebackCoupon = Core::loader('comebackCoupon');
	$comebackCoupon->getVisitSno($_GET['cp']);
}

header("location:/m");
?>