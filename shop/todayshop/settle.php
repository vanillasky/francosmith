<?
$_POST[emoney] = str_replace(",","",$_POST[emoney]);
$_POST[coupon] = str_replace(",","",$_POST[coupon]);
$_POST[coupon_emoney] = str_replace(",","",$_POST[coupon_emoney]);

include "../_header.php";
include "../lib/cart.class.php";
include "../conf/config.pay.php";
include '../lib/lib.func.egg.php';
$egg = getEggConf();

if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
	$_POST = validation::xssCleanArray($_POST, array(
		validation::DEFAULT_KEY	=> 'text'
	));
}

$_GET['cart_type'] = 'todayshop';

### ok캐쉬백결제
@include "../conf/pg.cashbag.php";
if( $cashbag['usesettle'] == "on" && $cashbag['code'] && $cashbag['key'] && $_POST[settlekind] == 'p' ){
	$set['use']['p'] = "on";
	$cfg['settlePg'] = "kcp";
	$pg['id'] = $cashbag['code'];
	$pg['key'] = $cashbag['key'];
}

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
else header("location:../member/login.php");

### 적립금 유효성 체크
chkEmoney($set[emoney],$_POST[emoney]);

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
if (count($cart->item)==0) msg("주문내역이 존재하지 않습니다","../index.php");

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

### 전자보증보험 수수료 재계산
$_POST['eggFee'] = reCalcuEggFee($_POST['eggFee'], ($cart->totalprice - $discount), $egg['feerate'], $_POST['eggFeeRateYn']);

$_POST[settleprice] = $cart->totalprice - $discount + $_POST[eggFee];

### 회원 추가 적립금 설정
switch($member['add_emoney_type']) {
	case 'goods':
		$tmp_price = $cart->goodsprice;
		break;
	case 'settle_amt':
		$tmp_price = $_POST[settleprice];
		break;
	default:
		$tmp_price = 0;
		break;
}
$addreserve = getExtraReserve($member['add_emoney'], $member['add_emoney_type'], $member['add_emoney_std_amt'], $tmp_price, $cart);

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
	case "y":	// 옐로페이

		include "../todayshop/card/$cfg[settlePg]/card_gate.php";
		$tpl->assign('pg',$pg);
		$tpl->define('card_gate',"../../skin_today/$cfg[tplSkinToday]/todayshop/card/{$cfg[settlePg]}.htm");	//
		break;

	case "d":	// 할인결제 (결제금액이 0일 경우)

		break;

}

### 주민등록번호암호화/변수명변경 (전자보증보험)
$isScope = ($egg['scope'] == 'A' || ($egg['scope'] == 'P' && $_POST[eggIssue] == 'Y') ? true : false);
if ($isScope === true && $_POST[resno][0] != '' && $_POST[resno][1] != '' && $_POST[eggAgree] == 'Y'){
	$_POST[eggResno][0] = encode($_POST[resno][0],1);
	$_POST[eggResno][1] = encode($_POST[resno][1],1);
	unset($_POST[resno]);
	if (in_array($_POST[settlekind], array('c','o','v')) && $cfg[settlePg] == 'dacom'){
		$note_query = "eggs[o]={$ordno}&eggs[i]={$_POST[eggIssue]}&eggs[r1]={$_POST[eggResno][0]}&eggs[r2]={$_POST[eggResno][1]}&eggs[a]={$_POST[eggAgree]}";
		$isScope = false;
	}
}
else if ($isScope === true && $_POST[eggBirthYear] != '' && $_POST[eggBirthMon] != '' && $_POST[eggBirthDay] != '' && $_POST[eggSex] != '' && $_POST[eggAgree] == 'Y'){
	$_POST[eggResno][0] = encode(sprintf('%04d%02d%02d', $_POST[eggBirthYear], $_POST[eggBirthMon], $_POST[eggBirthDay]),1);
	$_POST[eggResno][1] = encode(sprintf('%01d', $_POST[eggSex]),1);
	unset($_POST[eggBirthYear]);
	unset($_POST[eggBirthMon]);
	unset($_POST[eggBirthDay]);
	unset($_POST[eggSex]);
	if (in_array($_POST[settlekind], array('c','o','v')) && $cfg[settlePg] == 'dacom'){
		$note_query = "eggs[o]={$ordno}&eggs[i]={$_POST[eggIssue]}&eggs[r1]={$_POST[eggResno][0]}&eggs[r2]={$_POST[eggResno][1]}&eggs[a]={$_POST[eggAgree]}";
		$isScope = false;
	}
}
else $isScope = false;
if ($isScope !== true){
	unset($_POST[eggIssue]);
	unset($_POST[resno]);
	unset($_POST[eggResno]);
	unset($_POST[eggAgree]);
	unset($_POST[eggBirthYear]);
	unset($_POST[eggBirthMon]);
	unset($_POST[eggBirthDay]);
	unset($_POST[eggSex]);
}

### 주문데이타 가공
$_POST[memo] = htmlspecialchars(stripslashes($_POST[memo]), ENT_QUOTES);

### ace 카운터
if ($Acecounter->open_state()) {
	$Acecounter->readySendlog();
}

### 어바웃쿠폰
if($about_coupon->use && $_COOKIE['about_cp']=='1'){
	$_POST['settleprice'] -= (int) $cart->tot_about_dc_price;
	$_POST['coupon'] += (int) $cart->tot_about_dc_price;
	$tpl->assign('view_aboutdc', 1);
	$tpl->assign('about_coupon',(int) $cart->tot_about_dc_price);  	//어바웃쿠폰 할인
}


### 템플릿 출력
$tpl->assign($_POST);
$tpl->assign('cart',$cart);
$tpl->define(array(
			'orderitem'	=> '/proc/orderitem.htm',
			));
$tpl->print_('tpl');
?>
