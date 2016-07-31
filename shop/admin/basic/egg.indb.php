<?
include '../lib.php';
include '../../lib/lib.func.egg.php';

$mode = ($_POST[mode]) ? $_POST[mode] : $_GET[mode];
unset($_POST[mode]); unset($_POST[x]); unset($_POST[y]);

switch ($mode) {
	case 'displayEgg':
		saveEgg($_POST);
		break;
	default :
		break;
}

go($_SERVER[HTTP_REFERER]);
?>