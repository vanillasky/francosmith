<?
include "../lib.php";

$mode = isset($_GET['mode']) ? $_GET['mode'] : '';

switch ($mode) {
	case 'delete':
		$query = "DELETE FROM ".GD_TODAYSHOP_SUBSCRIBE." WHERE sno = '".$_GET['sno']."'";
		$db->query($query);
		break;
}


go($_SERVER['HTTP_REFERER']);
?>