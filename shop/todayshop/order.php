<?
include "../_header.php";
include "../lib/cart.class.php";
include "../conf/config.pay.php";
@include "../conf/pg.$cfg[settlePg].php";
include "../conf/pg.escrow.php";
include '../lib/lib.func.egg.php';
$egg = getEggConf();

$_GET['cart_type'] = 'todayshop';

// getordno �Լ� �̵� (shop/lib/lib.func.php)

// ������8 Metro IE�� ���������� ���ӽ� Desktop IE�� ��ȯ ���� �޽��� ���
if (isset($pg) === true) {
	header( 'X-UA-Compatible: requiresActiveX=true');
}

### okĳ�������
@include "../conf/pg.cashbag.php";
if( $cashbag['usesettle'] == "on" && $cashbag['code'] && $cashbag['key'] ) $set['use']['p'] = "on";

// �����̼� ������� ��� PG ���� ��ü
// �̼����� �����Ҽ� ����
$tsPG = resetPaymentGateway();

if ($sess[level] < 80) {	// ������ ����� �׳� ������.
	if (empty($tsPG['set'])) {
		msg('���� �غ����Դϴ�.',-1);
		exit;
	}
}
else {
	$set['use']['a'] = 'on';
}

### �ܺ� ������ ���(�����мǼ�ȣ) ����ũ�� �ڵ�����
if ($_COOKIE[cc_inflow] == 'yahoo_fss'){
	$escrow['use'] = 'Y';
	$escrow['min'] = '0';
}

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
	$style_member = "readonly style='border:0'";
}
else header("location:../member/login.php");

### ��ٱ��� ��Ű ����
if ($_POST[mode]=="addItem" && !$_COOKIE[gd_isDirect]) setcookie('gd_isDirect',1,0,'/');
$isDirect = ($_POST[mode]=="addItem" || $_COOKIE[gd_isDirect]) ? 1 : 0;

$cart = new Cart($isDirect);
if ($_POST[mode]=="addItem"){
	$cart->addCart($_POST[goodsno],$_POST[opt],$_POST[addopt],array(),$_POST[ea],$_POST[goodsCoupon]);
}
$_POST[idxs] = isset($_POST[idxs]) ? $_POST[idxs] : 'all';
$cart->setOrder($_POST[idxs]);	// $_POST[idxs] �� , �� ���е� 0 �̻��� ���� �Ǵ� 'all'
if($member){
	$cart->excep = $member['excep'];
	$cart->excate = $member['excate'];
	$cart->dc = $member[dc]."%";
}
$cart -> coupon = $_POST['coupon'];
$cart -> coupon_emoney = $_POST['coupon_emoney'];
$cart->calcu();

### s1��Ų���� ���� �⺻ ��ۺ� ��������
$param = array(
	'mode' => '0',
	'zipcode' => $member[zipcode],
	'emoney' => 0,
	'deliPoli' => 0,
	'coupon' => 0
);

$delivery = getDeliveryMode($param);
$cart -> delivery = $delivery['price'];
$cart -> totalprice += $delivery['price'];

### �ܿ� ��� üũ........2007-07-18 modify
if ($cart->item) {
	foreach ($cart->item as $v){
		$cart->chkStock($v[goodsno],$v[opt][0],$v[opt][1],$v[ea]);
	}
}

### ��ȸ���� ��� �α���â���� �̵�
if ($_GET[guest]) setCookie('guest',1,0,'/');
if (!$sess && !$_GET[guest] && !$_COOKIE[guest]){
	go("../member/login.php?guest=1&returnUrl=$_SERVER[PHP_SELF]");
}

### �ֹ���ȣ ����
$ordno = getordno();

$set['emoney']['base'] = pow(10,$set['emoney']['cut']);

### ������ ������
if(!$set['emoney']['emoney_use_range'])$tmp = $cart->goodsprice;
else $tmp = $cart->totalprice;
$tmp = $tmp - getDcPrice($cart->goodsprice,$cart->dc);
$emoney_max = getDcprice($tmp,$set[emoney][max])+0;

$r_deli = explode('|',$set['r_delivery']['title']);

if ($member){
	$member[zipcode] = explode("-",$member[zipcode]);
	$member[phone] = explode("-",$member[phone]);
	$member[mobile] = explode("-",$member[mobile]);
	$tpl->assign($member);
}

### ���½�Ÿ�� ��� ����
if($_COOKIE['cc_inflow']=="openstyleOutlink"){
	echo "<script src='http://www.interpark.com/malls/openstyle/OpenStyleEntrTop.js'></script>";
}

### ��ٿ� ����
if($about_coupon->use && $_COOKIE['about_cp']=='1'){
	$tpl->assign('view_aboutdc', 1);
	$tpl->assign('about_coupon', (int) $cart->tot_about_dc_price);
}

$tpl->assign('cart',$cart);
$tpl->assign('ordno',$ordno);
$tpl->print_('tpl');

?>