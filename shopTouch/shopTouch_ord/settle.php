<?
$_POST[emoney] = str_replace(",","",$_POST[emoney]);
$_POST[coupon] = str_replace(",","",$_POST[coupon]);
$_POST[coupon_emoney] = str_replace(",","",$_POST[coupon_emoney]);

include dirname(__FILE__) . "/../_shopTouch_header.php";
@include $shopRootDir . "/lib/cart.class.php";
@include $shopRootDir . "/conf/config.pay.php";

if (!$_POST[ordno]) msg("�ֹ���ȣ�� �������� �ʽ��ϴ�","order.php");

### ȸ������ ��������
if ($sess){
	$query = "
	select * from
		".GD_MEMBER." a
		left join ".GD_MEMBER_GRP." b on a.level=b.level
	where
		m_no='$sess[m_no]'
	";
	$member = $db->fetch($query,1);
}

$cart = new Cart($_COOKIE[gd_isDirect]);

### �ֹ��� ���� üũ
$cart -> chkOrder();

if($member){
	$cart->excep = $member['excep'];
	$cart->excate = $member['excate'];
	$cart->dc = $member[dc]."%";
}
$cart -> coupon = $_POST['coupon'];
$cart->calcu();

$param = array(
	'mode' => '0',
	'zipcode' => @implode("",$_POST['zipcode']),
	'emoney' => $_POST['emoney'],
	'deliPoli' => $_POST['deliPoli'],
	'coupon' => $_POST['coupon']
);

$delivery = getDeliveryMode($param);
if ($delivery[type]=="�ĺ�" && $delivery[freeDelivery] =="1") {
	$msg_delivery = "- 0��";
} else {
	$msg_delivery = $delivery[msg];
}
if($delivery[price] && !$delivery[msg]){
	$msg_delivery = number_format($delivery[price])."��";
}
$cart -> delivery = $delivery[price];
$cart -> totalprice += $delivery[price];

### ��ٱ��� ���� ���� ���� üũ
if (count($cart->item)==0) msg("�ֹ������� �������� �ʽ��ϴ�","-1");

### ������ ����
$_POST['coupon'] = $cart -> coupon;
$discount = $_POST[coupon] + $_POST[emoney] + $cart->dcprice;
if ($cart->totalprice - $discount < 0){
	$_POST[emoney] = $cart->totalprice - $_POST[coupon]-$cart->dcprice;
}

### �ֹ����� üũ
chkCart($cart);

### �����ݾ� ����
$discount = $_POST[coupon] + $_POST[emoney] + $cart->dcprice;
$_POST[settleprice] = $cart->totalprice - $discount;

### ȸ�� �߰� ������ ����
$addreserve = 0;
if($member['add_emoney']) $addreserve = getDcprice($_POST['settleprice'],$member['add_emoney'].'%');

### �ֹ��ݾ��� 0�� ��� (������/���ΰ�����)
if ($_POST[settleprice]==0 && $discount>0){
	$_POST[settlekind] = "d";	// ���ΰ���
}

### �������ܿ� ���� ����
switch ($_POST[settlekind]){

	case "a":	// �������Ա�

		### �������Ա� ���� ����Ʈ
		$res = $db->query("select * from ".GD_LIST_BANK." where useyn='y'");
		while ($data= $db->fetch($res)) $bank[] = $data;

		break;
	case "c":	// �ſ�ī��
	case "o":	// ������ü
	case "v":	// �������
	case "h":	// �ڵ���
	case "p":	// ����Ʈ

		ob_start();
		include $shopRootDir."/order/card/$cfg[settlePg]/mobile/shopTouch_card_gate.php";
		$card_gate = ob_get_contents();
		ob_clean();
		$tpl->assign('card_gate',$card_gate);

		break;

	case "d":	// ���ΰ��� (�����ݾ��� 0�� ���)

		break;

}

### �ֹ�����Ÿ ����
$_POST[memo] = htmlspecialchars(stripslashes($_POST[memo]), ENT_QUOTES);

### ���ø� ���
$tpl->assign($_POST);
$tpl->assign('cart',$cart);
$tpl->define(array(
			'orderitem'	=> '/proc/orderitem.htm',
			));
$tpl->print_('tpl');

?>
