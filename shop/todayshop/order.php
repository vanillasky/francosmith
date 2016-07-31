<?
include "../_header.php";
include "../lib/cart.class.php";
include "../conf/config.pay.php";
@include "../conf/pg.$cfg[settlePg].php";
include "../conf/pg.escrow.php";
include '../lib/lib.func.egg.php';
$egg = getEggConf();

$_GET['cart_type'] = 'todayshop';

// getordno 함수 이동 (shop/lib/lib.func.php)

// 윈도우8 Metro IE로 결제페이지 접속시 Desktop IE로 전환 유도 메시지 출력
if (isset($pg) === true) {
	header( 'X-UA-Compatible: requiresActiveX=true');
}

### ok캐쉬백결제
@include "../conf/pg.cashbag.php";
if( $cashbag['usesettle'] == "on" && $cashbag['code'] && $cashbag['key'] ) $set['use']['p'] = "on";

// 투데이샵 사용중인 경우 PG 설정 교체
// 미설정시 구매할수 없음
$tsPG = resetPaymentGateway();

if ($sess[level] < 80) {	// 관리자 등급은 그냥 지나감.
	if (empty($tsPG['set'])) {
		msg('결제 준비중입니다.',-1);
		exit;
	}
}
else {
	$set['use']['a'] = 'on';
}

### 외부 유입의 경우(야후패션소호) 에스크로 자동실행
if ($_COOKIE[cc_inflow] == 'yahoo_fss'){
	$escrow['use'] = 'Y';
	$escrow['min'] = '0';
}

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
	$style_member = "readonly style='border:0'";
}
else header("location:../member/login.php");

### 장바구니 쿠키 설정
if ($_POST[mode]=="addItem" && !$_COOKIE[gd_isDirect]) setcookie('gd_isDirect',1,0,'/');
$isDirect = ($_POST[mode]=="addItem" || $_COOKIE[gd_isDirect]) ? 1 : 0;

$cart = new Cart($isDirect);
if ($_POST[mode]=="addItem"){
	$cart->addCart($_POST[goodsno],$_POST[opt],$_POST[addopt],array(),$_POST[ea],$_POST[goodsCoupon]);
}
$_POST[idxs] = isset($_POST[idxs]) ? $_POST[idxs] : 'all';
$cart->setOrder($_POST[idxs]);	// $_POST[idxs] 는 , 로 구분된 0 이상의 정수 또는 'all'
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
if ($_GET[guest]) setCookie('guest',1,0,'/');
if (!$sess && !$_GET[guest] && !$_COOKIE[guest]){
	go("../member/login.php?guest=1&returnUrl=$_SERVER[PHP_SELF]");
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

### 오픈스타일 헤더 노출
if($_COOKIE['cc_inflow']=="openstyleOutlink"){
	echo "<script src='http://www.interpark.com/malls/openstyle/OpenStyleEntrTop.js'></script>";
}

### 어바웃 쿠폰
if($about_coupon->use && $_COOKIE['about_cp']=='1'){
	$tpl->assign('view_aboutdc', 1);
	$tpl->assign('about_coupon', (int) $cart->tot_about_dc_price);
}

$tpl->assign('cart',$cart);
$tpl->assign('ordno',$ordno);
$tpl->print_('tpl');

?>