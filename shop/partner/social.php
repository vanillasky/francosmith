<?
include "../lib/library.php";
error_reporting(0);

$metakey = isset($_GET['meta']) ? trim($_GET['meta']) : '';

$social_meta	= Core::loader('social_meta');
$todayShop		= Core::loader('todayshop');


$tsCfg = $todayShop->cfg;
$tsCfg['metasite'] = unserialize( $tsCfg['metasite'] );


if (!isset($tsCfg['metasite'][$metakey]) || $tsCfg['metasite'][$metakey] != 1) {
	exit('error');
}

if (!$social_meta->auth) exit;

$social_meta->set($metakey);
$xml = $social_meta->make();

if (!empty($xml)) {
	header ("Content-Type: application/xml; charset=".(($social_meta->view->encode) ?  $social_meta->view->encode : 'EUC-KR'));
	header ("Cache-Control: no-cache, must-revalidate");
	header ("Pragma: no-cache");

	echo $xml;
}
else {
	exit('error');
}
?>
