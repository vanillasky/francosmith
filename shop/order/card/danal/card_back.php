<?php
// from �ٳ� �ŷ� �� ��� > Ȯ��
include( "./inc/function.php" );
include dirname(__FILE__).'/../../../conf/config.mobileShop.php';

$ordno = $_GET['ordno'];		// �ֹ� ��ȣ
$isMobile = $_GET['isMobile'];	// ����� ���� Ȯ��
$isPc = $_GET['isPc'];			// n��ũ�� ���� Ȯ��

$danal->failLog($ordno,'','�ŷ� ���');

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