<?
$location = "�ֹ����� > �ֹ�����Ʈ";
include "../_header.php";


// ���� �ֹ� ����
	@include(dirname(__FILE__).'/_cfg.integrate.php');


//
	list($channel) = $db->fetch("SELECT channel FROM ".GD_INTEGRATE_ORDER." WHERE ordno = '".$_GET['ordno']."'");

// ���� �ֹ���ȣ�� ���� �ٸ� ä���� �ֹ����� �ִ��� -> ���� �ֹ���ȣ ���°� �ٸ�.

?>
<style>
.title2 {
	font-weight:bold;
	padding-bottom:5px;
}
</style>

<?
switch($channel) {

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

<? include "../_footer.php" ?>