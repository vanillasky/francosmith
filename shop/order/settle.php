<?
$_POST[emoney] = str_replace(",","",$_POST[emoney]);
$_POST[coupon] = str_replace(",","",$_POST[coupon]);
$_POST[coupon_emoney] = str_replace(",","",$_POST[coupon_emoney]);

include "../_header.php";
include "../lib/cart.class.php";
include "../conf/config.pay.php";
include '../lib/lib.func.egg.php';
$egg = getEggConf();

$mobilians = Core::loader('Mobilians');
$danal = Core::loader('Danal');

if(class_exists('validation') && method_exists('validation','xssCleanArray')){
	$_POST = validation::xssCleanArray($_POST, array(
		validation::DEFAULT_KEY => 'text',
		'memo'=>'disable',
	));
}

if($_POST['save_mode'] != '' && $_POST['save_mode'] != 'unused'){
	$load_config_ncash = $config->load('ncash');
}else{
	$load_config_ncash = array();
}

### 쿠폰(할인 or 적립) 금액이 없을때, 사용 쿠폰 정보 초기화
if ((int)$_POST['coupon'] == 0 && (int)$_POST['coupon_emoney'] == 0) {
    $_POST['apply_coupon'] = array();
}

### ok캐쉬백결제
@include "../conf/pg.cashbag.php";
if( $cashbag['usesettle'] == "on" && $cashbag['code'] && $cashbag['key'] && $_POST[settlekind] == 'p' ){
	$set['use']['p'] = "on";
	$cfg['settlePg'] = "kcp";
	$pg['id'] = $cashbag['code'];
	$pg['key'] = $cashbag['key'];
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

### 적립금 유효성 체크
chkEmoney($set[emoney],$_POST[emoney]);

$cart = new Cart($_COOKIE[gd_isDirect]);

### 주문서 정보 체크
chkOpenYn($cart,"A",-2);
$cart -> chkOrder();

if($member){
	$cart->excep = $member['excep'];
	$cart->excate = $member['excate'];
	$cart->dc = $member[dc]."%";
}
$cart -> coupon = $_POST['coupon'];
$cart->calcu();

$orderitem_rowspan = get_items_rowspan($cart->item);

$param = array(
	'mode' => '0',
	'zipcode' => @implode("",$_POST['zipcode']),
	'emoney' => $_POST['emoney'],
	'deliPoli' => $_POST['deliPoli'],
	'coupon' => $_POST['coupon'],
	'road_address' => $_POST['road_address'],
	'address' => $_POST['address'],
	'address_sub' => $_POST['address_sub'],
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
if ($_POST['emoney'] > 0 && $set['emoney']['totallimit'] > $cart->totalprice) {
	msg('상품 주문 합계액이 '.number_format($set['emoney']['totallimit']).'원이상 일 경우에만 적립금을 사용하실 수 있습니다.',-1);
	exit;
}
$_POST['coupon'] = $cart -> coupon;

if(isset($_POST['useAmount'.$load_config_ncash['api_id']]))
{
	$_POST['mileageUseAmount'.$load_config_ncash['api_id']] = $_POST['useAmount'.$load_config_ncash['api_id']];
	$_POST['cashUseAmount'.$load_config_ncash['api_id']] = 0;
	$_POST['totalUseAmount'.$load_config_ncash['api_id']] = $_POST['useAmount'.$load_config_ncash['api_id']];
}

$discount = $_POST[coupon] + $_POST['mileageUseAmount'.$load_config_ncash['api_id']] + $_POST['cashUseAmount'.$load_config_ncash['api_id']] + $_POST[emoney] + $cart->dcprice + $cart->special_discount_amount;
if ($cart->totalprice - $discount < 0){
	$_POST[coupon] = ($_POST[coupon] >= ($cart->totalprice-$cart->dcprice)) ? $cart->totalprice-$cart->dcprice : $_POST[coupon];	// 쿠폰 가격 계산
	$_POST[emoney] = $cart->totalprice - $_POST[coupon] - $cart->dcprice - $_POST['mileageUseAmount'.$load_config_ncash['api_id']] - $_POST['cashUseAmount'.$load_config_ncash['api_id']] - $cart->special_discount_amount;
}

### 쿠폰, 적립금 중복 사용 체크
if (! $set['emoney']['useduplicate']) {
	if ($_POST['emoney'] > 0 && ($_POST['coupon'] > 0 || $_POST['coupon_emoney'] > 0)) {
		msg('적립금과 쿠폰 사용이 중복적용되지 않습니다.',-1);
		exit;
	}
}

### 주문정보 체크
chkCart($cart);

### 쿠폰 사용 체크
checkCoupon($cart->item, $_POST['coupon'],$_POST['coupon_emoney'],$_POST['apply_coupon'],$_POST['settlekind']);

### 결제금액 설정
$discount = $_POST[coupon] + $_POST[emoney] + $_POST['mileageUseAmount'.$load_config_ncash['api_id']] + $_POST['cashUseAmount'.$load_config_ncash['api_id']] + $cart->dcprice + $cart->special_discount_amount;

### 전자보증보험 수수료 재계산
$_POST['eggFee'] = reCalcuEggFee($_POST['eggFee'], ($cart->totalprice - $discount), $egg['feerate'], $_POST['eggFeeRateYn']);

$_POST[settleprice] = $cart->totalprice - $discount + $_POST[eggFee];

### 회원 추가 적립금 설정
$addreserve = 0;
$cart->getAddGrpStdAmt($sess['level']);
switch($cart->addEmoneyType){
	case 'goods':
		$tmp_price = $cart->goodsprice;
		break;
	case 'settle_amt':
		$tmp_price = $_POST['settleprice'];
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

$nScreenPayment = Core::Loader('nScreenPayment');

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
		if ($_POST['settlekind'] == 'h' && $cfg['settleCellPg'] === 'mobilians' && $mobilians->isEnabled() === true) {
			$tpl->assign('MobiliansEnabled', true);
		}
		// 다날 휴대폰 결제가 사용중일때
		else if ($_POST['settlekind'] == 'h' && $cfg['settleCellPg'] === 'danal' && $danal->isEnabled() === true) {
			$tpl->define('>card_gate',$_SERVER['DOCUMENT_ROOT']."/$cfg[rootDir]/blank.php"); // PG사 card_gate 무력화 
			break; 
		}
	case "p":	// 포인트
	case "u":	// 중국카드결제 (현재 LG u+ 만 가능함)
	case "y":	// 옐로페이
		if ($nScreenPayment->getScreenType() == 'MOBILE') {
			include("../conf/pg.".$cfg['settlePg'].".php");
			$nScreenPayment->getCardGate($tpl, $cart);
		}
		else if (checkPatchPgStandard($cfg['settlePg']) === true) { // PC PG 표준결제창 패치여부
			include "card/$cfg[settlePg]/card_gate_std.php";
			$tpl->assign('pg',$pg);
			$tpl->define('card_gate',"order/card/{$cfg[settlePg]}_std.htm");
		}
		else {
			include "card/$cfg[settlePg]/card_gate.php";
			$tpl->assign('pg',$pg);
			$tpl->define('card_gate',"order/card/{$cfg[settlePg]}.htm");
		}
		break;
	case "d":	// 할인결제 (결제금액이 0일 경우)

		break;

	case "t" : //페이코결제
		$payco = Core::loader('payco');
		if($payco->checkNcash($load_config_ncash['useyn'], $_POST['totalUseAmount'.$load_config_ncash['api_id']]) == true){
			msg("PAYCO 결제수단은 네이버 마일리지 및 캐쉬 사용이 불가합니다.","order.php");
		}
		if($payco->screenType != 'MOBILE'){
			$Payco = $payco->getVoidPopupOpenScript();
			$tpl->assign('Payco',$Payco);
		}
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

if($_POST['updateMemberInfo']=='y' && $sess[m_no]){
	$zipcode = @implode("-",$_POST['zipcode']);
	$phone = @implode("-",$_POST['phoneReceiver']);
	$mobile = @implode("-",$_POST['mobileReceiver']);

	$query = "update ".GD_MEMBER." set
			zipcode = '$zipcode',
			zonecode = '".$_POST['zonecode']."',
			address = '".$_POST['address']."',
			address_sub = '".$_POST['address_sub']."',
			road_address = '".$_POST['road_address']."',
			phone = '$phone',
			mobile = '$mobile'
			 where m_no=".$sess['m_no'];
	$db->query($query);
}

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

### 네이버 Ncash
if($load_config_ncash['useyn'] == 'Y'){

	$load_config_ncash['ncash_emoney'] = $_POST['mileageUseAmount'.$load_config_ncash['api_id']];
	$load_config_ncash['ncash_cash'] = $_POST['cashUseAmount'.$load_config_ncash['api_id']];
	$load_config_ncash['totalAccumRate'] = $_POST['baseAccumRate'] + $_POST['addAccumRate'];
	if(in_array($load_config_ncash['save_mode'],array('choice','both'))) $load_config_ncash['save_mode'] = $_POST['save_mode'];	// 적립 위치

	// 네이버 포인트 적립일 때 회원추가적립금 0원으로 변경
	if( $load_config_ncash['save_mode'] == 'ncash' ) $addreserve = 0;

	// 트랜잭션 아이디 쿠키값 저장
	setcookie('reqTxId',$_POST['reqTxId'.$load_config_ncash['api_id']],0);

	$tpl->assign('ncash',$load_config_ncash);
}

### 템플릿 출력
$tpl->assign($_POST);
$tpl->assign('cart',$cart);
$tpl->assign('orderitem_rowspan',$orderitem_rowspan);
$tpl->define(array(
			'orderitem'	=> '/proc/orderitem.htm',
			));

### 주문처리url
if($cfg['ssl_type'] == "free") { //무료
	$tpl->assign('orderActionUrl',$sitelink->link('order/indb.php','regular'));
} else { //유료 혹은 보안서버안씀
	$tpl->assign('orderActionUrl',$sitelink->link('order/indb.php','ssl'));
}

$tpl->print_('tpl');

?>