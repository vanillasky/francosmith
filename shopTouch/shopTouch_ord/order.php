<?php

include dirname(__FILE__) . "/../_shopTouch_header.php";
@include $shopRootDir . "/lib/cart.class.php";
@include $shopRootDir . "/conf/config.pay.php";
@include $shopRootDir . "/conf/coupon.php";
@include $shopRootDir . "/conf/pg_mobile.$cfg[settlePg].php";

// getordno 함수는 /shop/lib/lib.func.php 파일로 이동

### 회원정보 가져오기
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

### 장바구니 쿠키 설정
if ($_POST[mode]=="addItem" && !$_COOKIE[gd_isDirect]) setcookie('gd_isDirect',1,0,'/');
$isDirect = ($_POST[mode]=="addItem" || $_COOKIE[gd_isDirect]) ? 1 : 0;

$cart = new Cart($isDirect);

// 재고수량 체크 cart class 에서 처리 되나, billi 에서는 cart 에 담기 전 처리 해야 함
$ea = $_POST[ea];
$arr_opt = explode('|', $_POST[opt][0]);
$opt1 = $arr_opt[0];
$opt2 = $arr_opt[1];
$goodsno=$_POST['goodsno'];

if (!$ea) $ea = 1;

if ($_POST[mode]=="addItem"){
	
	/* 재고 체크를 여기서 해야 함 */
	// 쿼리 수정 2011-01-26 by 육승우
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

	extract($db->fetch($query)); //list ($goodsnm,$usestock,$runout, $todaygoods,$min_ea,$max_ea,$stock, $tgsno) = $GLOBALS[db]->fetch($query);	//2011-01-26 by 육승우

	$goodsnm = addslashes($goodsnm);

	### 최소,최대구매수량체크
	if($ea < $min_ea) {
		msg("{$goodsnm} 상품의 최소구매수량은 {$min_ea}개 입니다.",-1);
	}
	else if($max_ea > 0 && $ea > $max_ea) {
				
		msg("{$goodsnm} 상품의 최대구매수량은 {$max_ea}개 입니다.",-1);
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

### s1스킨들을 위해 기본 배송비 가져오기
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

### 잔여 재고 체크........2007-07-18 modify
if ($cart->item) {
	foreach ($cart->item as $v){
		$cart->chkStock($v[goodsno],$v[opt][0],$v[opt][1],$v[ea]);
	}
}

### 비회원일 경우 로그인창으로 이동
if(!$_GET['guest']) {
	chkMemberShopTouch(1);
}

### 주문번호 생성
$ordno = getordno();

$set['emoney']['base'] = pow(10,$set['emoney']['cut']);

### 적립금 사용범위
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