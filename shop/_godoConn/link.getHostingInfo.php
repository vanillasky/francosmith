<?
include("../lib/library.php");

if (get_magic_quotes_gpc()) stripslashes_all($_POST);

$godo = $config->load('godo');

$action = isset($_SERVER['HTTP_ACTION']) ? strtoupper($_SERVER['HTTP_ACTION']) : '';
$hash	= isset($_SERVER['HTTP_ENAMOO']) ? $_SERVER['HTTP_ENAMOO'] : '';

if (md5($godo['sno']) != $hash) exit;

switch ($action) {
	case 'DISKUSAGE' :
		@include( dirname(__FILE__) .'/../conf/du.php');
		if (isset($du)) {
			echo (int)$du['disk'];
		}
		break;

	default:
		break;

}
?>