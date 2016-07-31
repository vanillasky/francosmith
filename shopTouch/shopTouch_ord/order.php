<?php

include dirname(__FILE__) . "/../_shopTouch_header.php";
@include $shopRootDir . "/lib/cart.class.php";
@include $shopRootDir . "/conf/config.pay.php";
@include $shopRootDir . "/conf/coupon.php";
@include $shopRootDir . "/conf/pg_mobile.$cfg[settlePg].php";

// getordno �Լ��� /shop/lib/lib.func.php ���Ϸ� �̵�

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
	$style_member = "readonly";
}

$_POST = utf8ToEuckr($_POST);

### ��ٱ��� ��Ű ����
if ($_POST[mode]=="addItem" && !$_COOKIE[gd_isDirect]) setcookie('gd_isDirect',1,0,'/');
$isDirect = ($_POST[mode]=="addItem" || $_COOKIE[gd_isDirect]) ? 1 : 0;

$cart = new Cart($isDirect);

// ������ üũ cart class ���� ó�� �ǳ�, billi ������ cart �� ��� �� ó�� �ؾ� ��
$ea = $_POST[ea];
$arr_opt = explode('|', $_POST[opt][0]);
$opt1 = $arr_opt[0];
$opt2 = $arr_opt[1];
$goodsno=$_POST['goodsno'];

if (!$ea) $ea = 1;

if ($_POST[mode]=="addItem"){
	
	/* ��� üũ�� ���⼭ �ؾ� �� */
	// ���� ���� 2011-01-26 by ���¿�
	$query = "
	select

		a.goodsnm, a.usestock, a.runout, a.todaygoods, a.min_ea, a.max_ea,
		b.stock,
		tg.tgsno, tg.startdt, tg.enddt

	from ".GD_GOODS." as a
	left join ".GD_GOODS_OPTION." as b on a.goodsno=b.goodsno
	left join ".GD_TODAYSHOP_GOODS." AS tg ON a.goodsno=tg.goodsno
	where
		a.goodsno='$goodsno' and opt1='".mysql_real_escape_string($opt1)."' and opt2='".mysql_real_escape_string($opt2)."'
	";

	extract($db->fetch($query)); //list ($goodsnm,$usestock,$runout, $todaygoods,$min_ea,$max_ea,$stock, $tgsno) = $GLOBALS[db]->fetch($query);	//2011-01-26 by ���¿�

	$goodsnm = addslashes($goodsnm);

	### �ּ�,�ִ뱸�ż���üũ
	if($ea < $min_ea) {
		msg("{$goodsnm} ��ǰ�� �ּұ��ż����� {$min_ea}�� �Դϴ�.",-1);
	}
	else if($max_ea > 0 && $ea > $max_ea) {
				
		msg("{$goodsnm} ��ǰ�� �ִ뱸�ż����� {$max_ea}�� �Դϴ�.",-1);
	}

	unset($ea, $opt1, $opt2, $goodsno);

	$cart->addCart($_POST[goodsno],$_POST[opt],array_notnull($_POST[addopt]),$_POST[ea],$_POST[goodsCoupon]);
}

if(!$cart->item) echo "<script>history.back();</script>";

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
if(!$_GET['guest']) {
	chkMemberShopTouch(1);
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

$tpl->assign('cart',$cart);
$tpl->assign('ordno',$ordno);
$tpl->print_('tpl');

?>