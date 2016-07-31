<?
$_POST[emoney] = str_replace(",","",$_POST[emoney]);
$_POST[coupon] = str_replace(",","",$_POST[coupon]);
$_POST[coupon_emoney] = str_replace(",","",$_POST[coupon_emoney]);

include dirname(__FILE__) . "/../_shopTouch_header.php";
@include $shopRootDir . "/lib/cart.class.php";
@include $shopRootDir . "/conf/config.pay.php";

if (!$_POST[ordno]) msg("주문번호가 존재하지 않습니다","order.php");

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
}

$cart = new Cart($_COOKIE[gd_isDirect]);

### 주문서 정보 체크
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
if ($delivery[type]=="후불" && $delivery[freeDelivery] =="1") {
	$msg_delivery = "- 0원";
} else {
	$msg_delivery = $delivery[msg];
}
if($delivery[price] && !$delivery[msg]){
	$msg_delivery = number_format($delivery[price])."원";
}
$cart -> delivery = $delivery[price];
$cart -> totalprice += $delivery[price];

### 장바구니 내역 존재 여부 체크
if (count($cart->item)==0) msg("주문내역이 존재하지 않습니다","-1");

### 적립금 재계산
$_POST['coupon'] = $cart -> coupon;
$discount = $_POST[coupon] + $_POST[emoney] + $cart->dcprice;
if ($cart->totalprice - $discount < 0){
	$_POST[emoney] = $cart->totalprice - $_POST[coupon]-$cart->dcprice;
}

### 주문정보 체크
chkCart($cart);

### 결제금액 설정
$discount = $_POST[coupon] + $_POST[emoney] + $cart->dcprice;
$_POST[settleprice] = $cart->totalprice - $discount;

### 회원 추가 적립금 설정
$addreserve = 0;
if($member['add_emoney']) $addreserve = getDcprice($_POST['settleprice'],$member['add_emoney'].'%');

### 주문금액이 0일 경우 (적립금/할인결제시)
if ($_POST[settleprice]==0 && $discount>0){
	$_POST[settlekind] = "d";	// 할인결제
}

### 결제수단에 따른 설정
switch ($_POST[settlekind]){

	case "a":	// 무통장입금

		### 무통장입금 계좌 리스트
		$res = $db->query("select * from ".GD_LIST_BANK." where useyn='y'");
		while ($data= $db->fetch($res)) $bank[] = $data;

		break;
	case "c":	// 신용카드
	case "o":	// 계좌이체
	case "v":	// 가상계좌
	case "h":	// 핸드폰
	case "p":	// 포인트

		ob_start();
		include $shopRootDir."/order/card/$cfg[settlePg]/mobile/shopTouch_card_gate.php";
		$card_gate = ob_get_contents();
		ob_clean();
		$tpl->assign('card_gate',$card_gate);

		break;

	case "d":	// 할인결제 (결제금액이 0일 경우)

		break;

}

### 주문데이타 가공
$_POST[memo] = htmlspecialchars(stripslashes($_POST[memo]), ENT_QUOTES);

### 템플릿 출력
$tpl->assign($_POST);
$tpl->assign('cart',$cart);
$tpl->define(array(
			'orderitem'	=> '/proc/orderitem.htm',
			));
$tpl->print_('tpl');

?>
