<?php
// from 다날 거래 중 취소 > 확인
include( "./inc/function.php" );
include dirname(__FILE__).'/../../../conf/config.mobileShop.php';

$ordno = $_GET['ordno'];		// 주문 번호
$isMobile = $_GET['isMobile'];	// 모바일 여부 확인
$isPc = $_GET['isPc'];			// n스크린 여부 확인

$danal->failLog($ordno,'','거래 취소');

if ($isMobile && !$isPc) {
	go($cfgMobileShop['mobileShopRootDir'].'/ord/order.php');
}
else if($isPc) {
	go($shopConfig['rootDir'].'/order/order.php');
}
else {
	echo "<script>opener.parent.location.replace('".$shopConfig['rootDir']."/order/order.php')</script>";
	echo '<script>window.close();</script>';
}
?>