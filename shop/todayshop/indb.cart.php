<?
header ("Content-Type: text/html; charset=EUC-KR");
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");

include "../lib/library.php";
@include dirname(__FILE__) . "/../lib/acecounter.class.php";

### ace카운터
$Acecounter = new Acecounter();

$orderitem_mode = "cart";
$_GET['cart_type'] = 'todayshop';
$cart = Core::loader('cart');

$mode = isset($_POST[mode]) ? $_POST[mode] : '';

$_POST = iconv_recursive('UTF-8','EUC-KR',$_POST);

switch ($mode){
	case "addItem":
		$cart->addCart($_POST[goodsno],$_POST[opt],$_POST[addopt],array(),$_POST[ea],$_POST[goodsCoupon]);

		// ace 카운터 상품추가
		if ($_POST['rn'] >= 1301 && $Acecounter->goods_cart_add($cart->item, (array)$_POST['goodsno'], (array)$_POST['ea']) === true) {
			$aceScript = strip_tags($Acecounter->scripts);
		}

		$code = 'ok';
		break;
	default :
		$code = 'error';
	break;
}

if ($_POST['rn'] >= 1301) { //13-01-XX 이후 XML방식 리턴
	header('Content-Type: application/xml;charset=euc-kr');
	echo '<?xml version="1.0" encoding="euc-kr" ?>';
	echo '<result>';
	echo '<code>'.$code.'</code>';
	echo '<aceScript><![CDATA['.$aceScript.']]></aceScript>';
	echo '</result>';
	exit;
} else { // TEXT방식 리턴
	exit($code);
}
?>