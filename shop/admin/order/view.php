<?
$location = "주문관리 > 주문리스트";
include "../_header.php";


// 통함 주문 설정
	@include(dirname(__FILE__).'/_cfg.integrate.php');


//
	list($channel) = $db->fetch("SELECT channel FROM ".GD_INTEGRATE_ORDER." WHERE ordno = '".$_GET['ordno']."'");

// 동일 주문번호로 서로 다른 채널의 주문건이 있는지 -> 서로 주문번호 형태가 다름.

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