<?
$popup = 1;
include "../_header.popup.php";

list($flag) = $db->fetch("SELECT channel FROM ".GD_INTEGRATE_ORDER." WHERE ordno = '".$_GET['ordno']."'");


switch($flag) {

	case 'checkout':
		$_GET['OrderID'] = isset($_GET['ordno']) ? $_GET['ordno'] : $_GET['OrderID'];
		include "_form.checkout.php";
		break;

	case 'ipay':
		include "_form.ipay.php";
		break;

	case 'shople':
		include "_form.shople.php";
		break;
	case 'selly':
		include "_form.selly.php";
		break;
	case 'enamoo':
	default :
		include "_form.enamoo.php";
		break;
}
?>
<script>table_design_load();</script>