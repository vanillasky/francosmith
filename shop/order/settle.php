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

### ����(���� or ����) �ݾ��� ������, ��� ���� ���� �ʱ�ȭ
if ((int)$_POST['coupon'] == 0 && (int)$_POST['coupon_emoney'] == 0) {
    $_POST['apply_coupon'] = array();
}

### okĳ�������
@include "../conf/pg.cashbag.php";
if( $cashbag['usesettle'] == "on" && $cashbag['code'] && $cashbag['key'] && $_POST[settlekind] == 'p' ){
	$set['use']['p'] = "on";
	$cfg['settlePg'] = "kcp";
	$pg['id'] = $cashbag['code'];
	$pg['key'] = $cashbag['key'];
}

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

### ������ ��ȿ�� üũ
chkEmoney($set[emoney],$_POST[emoney]);

$cart = new Cart($_COOKIE[gd_isDirect]);

### �ֹ��� ���� üũ
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
if (count($cart->item)==0) msg("�ֹ������� �������� �ʽ��ϴ�","../index.php");

### ������ ����
if ($_POST['emoney'] > 0 && $set['emoney']['totallimit'] > $cart->totalprice) {
	msg('��ǰ �ֹ� �հ���� '.number_format($set['emoney']['totallimit']).'���̻� �� ��쿡�� �������� ����Ͻ� �� �ֽ��ϴ�.',-1);
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
	$_POST[coupon] = ($_POST[coupon] >= ($cart->totalprice-$cart->dcprice)) ? $cart->totalprice-$cart->dcprice : $_POST[coupon];	// ���� ���� ���
	$_POST[emoney] = $cart->totalprice - $_POST[coupon] - $cart->dcprice - $_POST['mileageUseAmount'.$load_config_ncash['api_id']] - $_POST['cashUseAmount'.$load_config_ncash['api_id']] - $cart->special_discount_amount;
}

### ����, ������ �ߺ� ��� üũ
if (! $set['emoney']['useduplicate']) {
	if ($_POST['emoney'] > 0 && ($_POST['coupon'] > 0 || $_POST['coupon_emoney'] > 0)) {
		msg('�����ݰ� ���� ����� �ߺ�������� �ʽ��ϴ�.',-1);
		exit;
	}
}

### �ֹ����� üũ
chkCart($cart);

### ���� ��� üũ
checkCoupon($cart->item, $_POST['coupon'],$_POST['coupon_emoney'],$_POST['apply_coupon'],$_POST['settlekind']);

### �����ݾ� ����
$discount = $_POST[coupon] + $_POST[emoney] + $_POST['mileageUseAmount'.$load_config_ncash['api_id']] + $_POST['cashUseAmount'.$load_config_ncash['api_id']] + $cart->dcprice + $cart->special_discount_amount;

### ���ں������� ������ ����
$_POST['eggFee'] = reCalcuEggFee($_POST['eggFee'], ($cart->totalprice - $discount), $egg['feerate'], $_POST['eggFeeRateYn']);

$_POST[settleprice] = $cart->totalprice - $discount + $_POST[eggFee];

### ȸ�� �߰� ������ ����
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

### �ֹ��ݾ��� 0�� ��� (������/���ΰ�����)
if ($_POST[settleprice]==0 && $discount>0){
	$_POST[settlekind] = "d";	// ���ΰ���
}

$nScreenPayment = Core::Loader('nScreenPayment');

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
		if ($_POST['settlekind'] == 'h' && $cfg['settleCellPg'] === 'mobilians' && $mobilians->isEnabled() === true) {
			$tpl->assign('MobiliansEnabled', true);
		}
		// �ٳ� �޴��� ������ ������϶�
		else if ($_POST['settlekind'] == 'h' && $cfg['settleCellPg'] === 'danal' && $danal->isEnabled() === true) {
			$tpl->define('>card_gate',$_SERVER['DOCUMENT_ROOT']."/$cfg[rootDir]/blank.php"); // PG�� card_gate ����ȭ 
			break; 
		}
	case "p":	// ����Ʈ
	case "u":	// �߱�ī����� (���� LG u+ �� ������)
	case "y":	// ��������
		if ($nScreenPayment->getScreenType() == 'MOBILE') {
			include("../conf/pg.".$cfg['settlePg'].".php");
			$nScreenPayment->getCardGate($tpl, $cart);
		}
		else if (checkPatchPgStandard($cfg['settlePg']) === true) { // PC PG ǥ�ذ���â ��ġ����
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
	case "d":	// ���ΰ��� (�����ݾ��� 0�� ���)

		break;

	case "t" : //�����ڰ���
		$payco = Core::loader('payco');
		if($payco->checkNcash($load_config_ncash['useyn'], $_POST['totalUseAmount'.$load_config_ncash['api_id']]) == true){
			msg("PAYCO ���������� ���̹� ���ϸ��� �� ĳ�� ����� �Ұ��մϴ�.","order.php");
		}
		if($payco->screenType != 'MOBILE'){
			$Payco = $payco->getVoidPopupOpenScript();
			$tpl->assign('Payco',$Payco);
		}
	break;

}


### �ֹε�Ϲ�ȣ��ȣȭ/�������� (���ں�������)
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

### �ֹ�����Ÿ ����
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

### ace ī����
if ($Acecounter->open_state()) {
	$Acecounter->readySendlog();
}

### ��ٿ�����
if($about_coupon->use && $_COOKIE['about_cp']=='1'){
	$_POST['settleprice'] -= (int) $cart->tot_about_dc_price;
	$_POST['coupon'] += (int) $cart->tot_about_dc_price;
	$tpl->assign('view_aboutdc', 1);
	$tpl->assign('about_coupon',(int) $cart->tot_about_dc_price);  	//��ٿ����� ����
}

### ���̹� Ncash
if($load_config_ncash['useyn'] == 'Y'){

	$load_config_ncash['ncash_emoney'] = $_POST['mileageUseAmount'.$load_config_ncash['api_id']];
	$load_config_ncash['ncash_cash'] = $_POST['cashUseAmount'.$load_config_ncash['api_id']];
	$load_config_ncash['totalAccumRate'] = $_POST['baseAccumRate'] + $_POST['addAccumRate'];
	if(in_array($load_config_ncash['save_mode'],array('choice','both'))) $load_config_ncash['save_mode'] = $_POST['save_mode'];	// ���� ��ġ

	// ���̹� ����Ʈ ������ �� ȸ���߰������� 0������ ����
	if( $load_config_ncash['save_mode'] == 'ncash' ) $addreserve = 0;

	// Ʈ����� ���̵� ��Ű�� ����
	setcookie('reqTxId',$_POST['reqTxId'.$load_config_ncash['api_id']],0);

	$tpl->assign('ncash',$load_config_ncash);
}

### ���ø� ���
$tpl->assign($_POST);
$tpl->assign('cart',$cart);
$tpl->assign('orderitem_rowspan',$orderitem_rowspan);
$tpl->define(array(
			'orderitem'	=> '/proc/orderitem.htm',
			));

### �ֹ�ó��url
if($cfg['ssl_type'] == "free") { //����
	$tpl->assign('orderActionUrl',$sitelink->link('order/indb.php','regular'));
} else { //���� Ȥ�� ���ȼ����Ⱦ�
	$tpl->assign('orderActionUrl',$sitelink->link('order/indb.php','ssl'));
}

$tpl->print_('tpl');

?>