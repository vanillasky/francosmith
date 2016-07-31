<?
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");

include "../lib/library.php";
include "../conf/config.php";
include "../conf/config.pay.php";
@include "../conf/coupon.php";
@include "../conf/config.plusCheeseCfg.php";
include '../lib/lib.func.egg.php';
$egg = getEggConf();

$mobilians = Core::loader('Mobilians');
$danal = Core::loader('Danal');

if(class_exists('validation') && method_exists('validation','xssCleanArray')){
	$_POST = validation::xssCleanArray($_POST, array(
		validation::DEFAULT_KEY => 'text',
	));
}

### ��ٿ� ����
$about_coupon = &load_class('about_coupon','about_coupon');

### ���̹� ����Ʈ
$naverNcash = &load_class('naverNcash','naverNcash');
if($_POST['save_mode'] != ''){
	$load_config_ncash = $config->load('ncash');
}else{
	$load_config_ncash = array();
}

### okĳ�������
@include "../conf/pg.cashbag.php";
if($_POST[settlekind] == 'p' && $cashbag[usesettle] == 'on'){
	$cfg['settlePg'] = "kcp";
}
if( $cashbag['use'] == 'on' && $cashbag['code'] && $cashbag['key'] ) $cashbagcard = @implode('',$_POST['cashbag']);

$ordno = $_POST[ordno];
if(!$ordno)	msg('�ֹ���ȣ�� �����ϴ�.',-1); //�ֹ���ȣ ��üũ

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

### ���ں������� �߱޿�û ��������
if (in_array($_POST[settlekind],array("c","o","v")) && $cfg[settlePg] != 'dacom'){
	if ($_POST[eggResno][0] != '' && $_POST[eggResno][1] != '' && $_POST[eggAgree] == 'Y'){
		@session_start();
		$eggData = array('ordno' => $ordno, 'issue' => $_POST[eggIssue], 'resno1' => $_POST[eggResno][0], 'resno2' => $_POST[eggResno][1], 'agree' => $_POST[eggAgree]);
		$_SESSION['eggData']	= $eggData;
	}
}

$cart = & load_class('cart','Cart',$_COOKIE[gd_isDirect]);
$Goods = & load_class('Goods','Goods');
$coupon_price = & load_class('coupon_price','coupon_price');

### �ֹ��� ���� üũ
if($_POST['paycoType']!='CHECKOUT'){ //payco ��������
	$cart -> chkOrder();
}
chkCart(&$cart);

$cart -> reset(); //�ֹ��� ��ǰ���� ������ ��Ȯ�� �ϱ�����

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
	'coupon' => $_POST['coupon'],
	'road_address' => $_POST['road_address'],
	'address' => $_POST['address'],
	'address_sub' => $_POST['address_sub'],
);
$delivery = getDeliveryMode($param);

if($_POST['settlekind'] == 't') {
	//�ֹ���ǰ�� ��ۺ� ���
	$orderDeliveryItem = &load_class('orderDeliveryItem','orderDeliveryItem');
	$orderDeliveryItem->ordno = $ordno;
	$delivery_idxs = $orderDeliveryItem->set_delivery_data($delivery);
	if(isset($delivery_idxs['area_idx'])) {
		$area_idx = $delivery_idxs['area_idx'];
	}
}

$cart -> delivery = $delivery[price];
$cart -> totalprice += $delivery[price];

### �ܿ� ��� üũ
foreach ($cart->item as $v){
	$cart->chkStock($v[goodsno],$v[opt][0],$v[opt][1],$v[ea]);
	$arItemSno[] = $v[goodsno];
}

### ������ ����
$_POST['coupon'] = $cart -> coupon;
$discount = $_POST[coupon] + $_POST[emoney] + $cart->dcprice + $cart->special_discount_amount;
if ($cart->totalprice - $discount < 0){
	$_POST[emoney] = $cart->totalprice - $_POST[coupon]-$cart->dcprice-$cart->special_discount_amount;
}

### ������ ��ȿ�� üũ
chkEmoney($set[emoney],$_POST[emoney]);

### ���� ��� üũ
checkCoupon($cart->item, $_POST['coupon'],$_POST['coupon_emoney'],$_POST['apply_coupon'],$_POST['settlekind']);

### �ֹ�����/���� ��ȿ�� üũ
$coupon_price->set_config($cfgCoupon);
foreach($cart->item as $v){
	$arCategory = $Goods->get_goods_category($v['goodsno']);
	$coupon_price->set_item($v['goodsno'],$v['price'],$v['ea'],$arCategory,$v['opt'][0],$v['opt'][1],$v['addopt'],$v['goodsnm']);
}
$coupon_item = $coupon_price->get_goods_coupon('order');

$result = $coupon_price->check_coupon($_POST['coupon'],$_POST['coupon_emoney'],$_POST['settlekind'],$_POST['apply_coupon']);
if($result == "cash") $errmsg = "������ �����θ� ��밡���� �����Դϴ�.";
if($result == "sale"||$result == "reserve") $errmsg = "���� ��������� �ùٸ��� �ʽ��ϴ�.";
if($result !== true) msg($errmsg,-1);

## ���� �������
if($coupon_price->arCoupon && $sess['m_no']){

		if($coupon_price->arCoupon)foreach($coupon_price->arCoupon as $arCoupon){
			if(!in_array($arCoupon[sno],$_POST['apply_coupon'])) continue;

			if($arCoupon['applysno']){
				$setQuery = ",applysno = '$arCoupon[applysno]'";
			}else if($arCoupon['downsno']){
				$setQuery = ",downloadsno	= '$arCoupon[downsno]'";
				$ArrCouponSql[] = "update ".GD_COUPON_APPLY." set status = '1'  where sno = '$arCoupon[applysno]'";	// �ٿ�ε� ������ ���¸� ����.
			}

			if($arCoupon['sale'])	$couponDc = array_sum($arCoupon['sale']);
			if($arCoupon['reserve']) $couponEmoney = array_sum($arCoupon['reserve']);
			$ArrCouponSql[] = "insert into gd_coupon_order set
							ordno		= '$ordno',
							coupon		= '".mysql_real_escape_string($arCoupon[coupon])."',
							dc			= '$couponDc',
							emoney		= '$couponEmoney',
							regdt		= now(),
							m_no		=	'$sess[m_no]'".$setQuery;
		}
}

### �ֹ�����Ÿ ����
$_POST[phoneOrder]		= @implode("-",$_POST[phoneOrder]);
$_POST[mobileOrder]		= @implode("-",$_POST[mobileOrder]);
$_POST[phoneReceiver]	= @implode("-",$_POST[phoneReceiver]);
$_POST[mobileReceiver]	= @implode("-",$_POST[mobileReceiver]);
$_POST[zipcode]			= @implode("-",$_POST[zipcode]);

### ��ٿ�����
$about_coupon_flag = 0;
if($about_coupon->use && $_COOKIE['about_cp']==1){
	$about_coupon_flag = 1;
	$about_dc_sum = $cart->tot_about_dc_price;
}

//���θ��ּ�
if($_POST[road_address]) {
	$road_address = $_POST[road_address]." ".$_POST[address_sub];
} else {
	$road_address = "";
}

$discount = $_POST[coupon] + $_POST["totalUseAmount".$load_config_ncash['api_id']] + $_POST[emoney] + $cart->dcprice + $cart->special_discount_amount;

### ���ں������� ������ ����
$_POST['eggFee'] = reCalcuEggFee($_POST['eggFee'], ($cart->totalprice - $discount), $egg['feerate'], $_POST['eggFeeRateYn']);

// �����ݾ� ����
$settleprice = $cart->totalprice + $_POST[eggFee] - $discount;
$_POST['settleprice']		= $settleprice;
$_REQUEST['settleprice']	= $settleprice;

### PG��� �̿�� ó�� (������,�����ݰ��� ����)
if (in_array($_POST[settlekind],array("c","o","v","h","p","i","y","t"))){
	$qrTmp = "step2=50,";
	$qrTmp2 = "istep=50,";
}

if(!$set['delivery']['deliverynm']) $a_tmp[] = $set['delivery']['deliverynm'];
else $a_tmp[] = '�⺻ ���';
$b_tmp = @explode('|',$set['r_delivery']['title']);
$r_deli = @array_merge($a_tmp,$b_tmp);

## ���� ������� üũ
if($_POST['apply_coupon'] && $sess['m_no']){
	foreach($_POST['apply_coupon'] as $v){

		// offline ����
		if (preg_match("/^off_/i", $v)) {
			$query = "
			SELECT offdown.*
			FROM ".GD_OFFLINE_DOWNLOAD." AS offdown
			INNER JOIN ".GD_OFFLINE_COUPON." AS offcoupon
			ON offdown.coupon_sno = offcoupon.sno
			INNER JOIN ".GD_COUPON_ORDER." AS coupon_order
			ON coupon_order.downloadsno = offdown.sno AND coupon_order.m_no = '".$sess['m_no']."'
			WHERE offcoupon.sno = '".intval(preg_replace('/[^0-9]/','',$v))."'
			GROUP BY coupon_order.m_no
			";
		}
		// online ���� (�¶��� �ٿ�ε� ���� ����)
		else {
			$query = "
			SELECT
				CP.*,
				COUNT(O.m_no) AS usecnt
			FROM ".GD_COUPON_APPLY." AS CA
			INNER JOIN ".GD_COUPON." AS CP
			ON CA.couponcd = CP.couponcd
			INNER JOIN ".GD_COUPON_ORDER." AS O
			ON O.applysno = CA.sno AND O.m_no = '".$sess['m_no']."'
			WHERE CA.couponcd = '$v'
			GROUP BY O.m_no
			";
		}

		if (($cp = $db->fetch($query,1)) != false) {	// false or null
			if ((int)$cp['coupontype'] === 1) {	// ������ ������ �� �ִ� �ٿ�ε� ����
				if ((int)$cp['dncnt'] > 0 && $cp['dncnt'] <= $cp['usecnt']) {
					msg('�̹� ���� �����Դϴ�.');
					exit;
				}
			}
			else {
				msg('�̹� ���� �����Դϴ�.');
				exit;
			}
		}
		else {
			// valid coupon
		}

	}
}

if ($_POST['settlekind'] == 'i')
{
	$qrTmp .= "`pg`='ipay',";
	$settlePg = 'ipay';
}
else if ($_POST['settlekind'] == 'h' && $cfg['settleCellPg'] === 'mobilians' && $mobilians->isEnabled()) {
	$qrTmp .= "`pg`='mobilians',";
	$settlePg = 'mobilians';
}
else if ($_POST['settlekind'] == 'h' && $cfg['settleCellPg'] === 'danal' && $danal->isEnabled()) {
	$qrTmp .= "`pg`='danal',";
	$settlePg = 'danal';
}
else if ($_POST['settlekind'] == 't') {
	$payco = Core::loader('payco');
	if($payco->checkNcash($load_config_ncash['useyn'], $_POST['totalUseAmount'.$load_config_ncash['api_id']]) == true){
		$payco->msgLocate("PAYCO ���������� ���̹� ���ϸ��� �� ĳ�� ����� �Ұ��մϴ�.", $_POST['paycoType'], '');
	}
	$qrTmp .= $payco->getqrTmp($_POST['paycoType']);
	$settlePg = 'payco';
}
else
{
	$qrTmp .= "`pg`='".$cfg['settlePg']."',";
	$settlePg = $cfg['settlePg'];
}

### �ֹ���ȣ �ߺ����� üũ
list ($chk,$pre_settlekind) = $db->fetch("select ordno,settlekind from ".GD_ORDER." where ordno='$ordno'");
if ($chk){
	if (in_array($_POST[settlekind],array("c","o","v","h","p","u","i","y"))){
		### ��������� ����� ��� ������Ʈ ó��
		$paycoOrderInfoResetQuery = '';
		if($pre_settlekind == 't'){
			$paycoOrderInfoResetQuery = ", settleInflow = NULL, payco_settle_type = NULL, pg='".$cfg['settlePg']."' ";
		}
		if ($_POST[settlekind]!=$pre_settlekind) $db->query("update ".GD_ORDER." set settlekind='$_POST[settlekind]' $paycoOrderInfoResetQuery where ordno='$ordno'");

		// ���հ��� ���� - 15.04.28 - su
		$order = new order();
		$order->load($ordno);
		$tax = $order->getTaxAmount();
		$vat = $order->getVatAmount();
		$taxfree = $order->getTaxFreeAmount();

		$nScreenPayment = Core::Loader('nScreenPayment');
		$nScreenPayment->executeCardGate($settleprice, $_POST[settlekind], '', $tax, $vat, $taxfree);
	} else {
		if($_POST[settlekind] == 't') {
			$payco->msgLocate("������ �ֹ���ȣ�� �����մϴ�", $_POST['paycoType'], '');
		}
		else {
			msg("������ �ֹ���ȣ�� �����մϴ�","order.php","parent");
		}
	}

}

### ������ ������ �����϶� ������ �缳��
if($set['emoney']['chk_goods_emoney'] == '0' && $set['emoney']['emoney_standard'] == '1') {
	$cart->resetReserveAmount($settleprice);
}

### ȸ�� �߰� ������ ����
switch($member['add_emoney_type']) {
	case 'goods':
		$tmp_price = $cart->goodsprice;
		break;
	case 'settle_amt':
		$tmp_price = $settleprice;
		break;
	default:
		$tmp_price = 0;
		break;
}
$cart->bonus += getExtraReserve($member['add_emoney'], $member['add_emoney_type'], $member['add_emoney_std_amt'], $tmp_price, $cart);

if ($delivery[type]=="�ĺ�" && $delivery[freeDelivery] =="1") $delivery['msg'] = "0��";

if(isset($_POST['reqTxId'.$load_config_ncash['api_id']]) && strlen(trim($_POST['reqTxId'.$load_config_ncash['api_id']]))>0)
{
	$qrTmp .= "settlelog='----------------------------------------".PHP_EOL.
	"���̹� ���ϸ��� ".(int)$_POST['mileageUseAmount'.$load_config_ncash['api_id']]."�� �����õ�".PHP_EOL.
	"���̹� ĳ�� ".(int)$_POST['cashUseAmount'.$load_config_ncash['api_id']]."�� �����õ�".PHP_EOL.
	"----------------------------------------',".PHP_EOL;

	$naverMileageTx = Core::loader('NaverMileageTransaction', $ordno);
	$naverMileageTx->setTransactionId($_POST['reqTxId'.$load_config_ncash['api_id']]);
	$naverMileageTx->setOrderAmount($cart->totalprice);
	$naverMileageTx->setCalcAmount($cart->goodsprice);
	$naverMileageTx->setLegacySaveMode($_POST['save_mode']);
	$naverMileageTx->setMileageUseAmount($_POST['mileageUseAmount'.$load_config_ncash['api_id']]);
	$naverMileageTx->setCashUseAmount($_POST['cashUseAmount'.$load_config_ncash['api_id']]);
	$naverMileageTx->setDiscountAmount($_POST['emoney']+$cart->coupon+$cart->dcprice+$cart->special_discount_amount);
	foreach ($cart->item as $item) {
		$naverMileageTx->addOrderItem($item['goodsno'], $item['goodsnm'], $item['ea'], $item['price']);
	}
	$naverMileageTx->createTransaction();
}
else
{
	if($_POST['save_mode']=='unused') $qrTmp .= "`ncash_save_yn`='u',";
}

//nscreen ���� data ����
$nScreenPayment = Core::Loader('nScreenPayment');
if($nScreenPayment->getScreenType() == 'MOBILE') {
	$qrTmp .= "`o_screen_type`='m',";
}
else {
	$qrTmp .= "`o_screen_type`='p',";
}
unset($nScreenPayment);

// ���ų���Ȯ��
if ($cfg['orderDoubleCheck'] == 'y' && isset($_POST['doubleCheck'])) {
	$qrTmp .= sprintf("`doubleCheck`='%s',", ($_POST['doubleCheck'] == 'y' ? 'y' : 'n'));
}

// ������ �߰� ��ۺ� ���� �ΰ� �⺻�� ����
if (isset($set['delivery']['add_extra_fee_duplicate_each']) === false) {
	$set['delivery']['add_extra_fee_duplicate_each']		= 1;			// ������ۻ�ǰ �ֹ��� �⺻���� "�׸� �ߺ� �ΰ�" �� ó�� (���̻� ������� ����)
}
if (isset($set['delivery']['add_extra_fee_duplicate_free']) === false) {
	$set['delivery']['add_extra_fee_duplicate_free']		= 1;			// ������ ��ǰ �ֹ��� �⺻���� "�׸� �ߺ� �ΰ�" �� ó��
}
if (isset($set['delivery']['add_extra_fee_duplicate_fixEach']) === false) {
	$set['delivery']['add_extra_fee_duplicate_fixEach']		= 1;			// ���� ��ۺ� ��ǰ �ֹ��� �⺻���� "�׸� �ߺ� �ΰ�" �� ó��
}

### �ֹ����� ����
$query = "
insert into ".GD_ORDER." set $qrTmp
	ordno			= '$ordno',
	nameOrder		= '".trim($_POST[nameOrder])."',
	email			= '$_POST[email]',
	phoneOrder		= '$_POST[phoneOrder]',
	mobileOrder		= '$_POST[mobileOrder]',
	nameReceiver	= '$_POST[nameReceiver]',
	phoneReceiver	= '$_POST[phoneReceiver]',
	mobileReceiver	= '$_POST[mobileReceiver]',
	zipcode			= '$_POST[zipcode]',
	zonecode		= '$_POST[zonecode]',
	address			= '$_POST[address] $_POST[address_sub]',
	road_address	= '$road_address',
	settlekind		= '$_POST[settlekind]',
	settleprice		= '$settleprice',
	escrowyn		= '$_POST[escrow]',
	prn_settleprice		= '$settleprice',
	goodsprice		= '{$cart->goodsprice}',
	deli_title		= '".$r_deli[$_POST['deliPoli']]."',
	delivery		= '{$cart->delivery}',
	deli_type		= '".$delivery['type']."',
	deli_msg		= '".$delivery['msg']."',
	coupon			= '$_POST[coupon]',
	emoney			= '$_POST[emoney]',
	memberdc		= '".$cart->dcprice ."',
	o_special_discount_amount		= '".$cart->special_discount_amount ."',
	reserve			= '{$cart->bonus}',
	eggFee			= '$_POST[eggFee]',
	bankAccount		= '$_POST[bankAccount]',
	bankSender		= '$_POST[bankSender]',
	m_no			= '$sess[m_no]',
	ip				= '$_SERVER[REMOTE_ADDR]',
	referer			= '$referer',
	memo			= '$_POST[memo]',
	inflow	=	'$_COOKIE[cc_inflow]',
	orddt			= now(),
	coupon_emoney	=	'".$_POST[coupon_emoney]."',
	cashbagcard		= '".$cashbagcard."',
	cbyn			= 'N',
	about_coupon_flag = '$about_coupon_flag',
	about_dc_sum = '$about_dc_sum',
	add_extra_fee_duplicate_each = '".$set['delivery']['add_extra_fee_duplicate_each']."',
	add_extra_fee_duplicate_free = '".$set['delivery']['add_extra_fee_duplicate_free']."',
	add_extra_fee_duplicate_fixEach = '".$set['delivery']['add_extra_fee_duplicate_fixEach']."',
	freeDelivery = '".$set['delivery']['freeDelivery']."',
	goodsDelivery = '".$set['delivery']['goodsDelivery']."'
";
if(!$db->query($query))msg('���������� �ֹ� ������ ���� �ʾҽ��ϴ�.\n�ٽ� �ѹ� �õ��ϼ���!!',0);

$orderSeq = 0;

### �ֹ���ǰ ����
foreach ($cart->item as $k=>$item){

	unset($addopt);

	### ��ǰ ���̺��� ���ް� ��������
	list ($item[supply]) = $db->fetch("select supply from ".GD_GOODS_OPTION." where goodsno='$item[goodsno]' and opt1='{$item[opt][0]}' and opt2='{$item[opt][1]}' and go_is_deleted <> '1' and go_is_display = '1' ");

	### �߰��ɼ�
	if (is_array($item[addopt])){
		foreach ($item[addopt] as $v) $addopt[] = $v[optnm].":".$v[opt];
		$addopt = @implode("^",$addopt);
	}
	$memberdc = $item['memberdc'];

	$item[goodsnm] = addslashes(strip_tags($item[goodsnm]));

	### ������, �귣��
	list($maker, $brandnm, $tax, $delivery_type, $goods_delivery, $usestock) = $db->fetch("select maker, brandnm, tax, delivery_type, goods_delivery, usestock from ".GD_GOODS." left join ".GD_GOODS_BRAND." on brandno=sno where goodsno='{$item[goodsno]}'");
	$maker = addslashes($maker);
	$brandnm = addslashes($brandnm);
	$item_deli_msg = "";
	if($delivery_type == 3){
		$item_deli_msg = "����";
		if($goods_delivery) $item_deli_msg .= " ".number_format($goods_delivery)." ��";
	}
	if($usestock == 'o') $stockable = "y";
	else $stockable = "n";


	// ��ǰ�� ���� �ݾ� (����, ������)
	$coupon = 0;
	$coupon_emoney = 0;

	foreach($coupon_price->arCoupon as $arCoupon) {

		if (!in_array($arCoupon['sno'],$_POST['apply_coupon']) || isset($arCoupon['sale']['order']) || isset($arCoupon['reserve']['order'])) {
			continue;
		}

		$_same_goods_count_cart = 0;
		foreach ($cart->item as $_item) {
			if ($_item['goodsno'] == $item['goodsno']) {
				$_same_goods_count_cart = $_same_goods_count_cart + $_item['ea'];
			}
		}

		$_coupon = ($_coupon = (int)$arCoupon['sale'][$item['goodsno']]) ? $_coupon / $_same_goods_count_cart : 0;
		$_coupon_emoney = ($_coupon_emoney = (int)$arCoupon['reserve'][$item['goodsno']]) ? $_coupon_emoney / $_same_goods_count_cart : 0;

		$coupon += $_coupon;
		$coupon_emoney += $_coupon_emoney;
	}

	$oi_delivery_idx = '';
	if(is_array($delivery_idxs[$item['goodsno']])) $oi_delivery_idx = $delivery_idxs[$item['goodsno']][$item['optno']];
	else $oi_delivery_idx = $delivery_idxs[$item['goodsno']];

	$query = "
	insert into ".GD_ORDER_ITEM." set $qrTmp2
		ordno			= '$ordno',
		goodsno			= '$item[goodsno]',
		goodsnm			= '$item[goodsnm]',
		opt1			= '".mysql_real_escape_string($item[opt][0])."',
		opt2			= '".mysql_real_escape_string($item[opt][1])."',
		`optno`			= '{$item[optno]}',
		addopt			= '".mysql_real_escape_string($addopt)."',
		price			= '".($item[price]+$item[addprice])."',
		supply			= '$item[supply]',
		reserve			= '$item[reserve]',
		extra_reserve		= '{$item[extra_reserve]}',
		memberdc		= '$memberdc',
		ea				= '$item[ea]',
		maker			= '$maker',
		brandnm			= '$brandnm',
		tax				= '$tax',
		deli_msg		= '$item_deli_msg',
		stockable		= '$stockable',
		about_coupon_flag = '$about_coupon_flag',
		about_dc_price = '$item[about_dc_price]',
		coupon = '$coupon',
		coupon_emoney = '$coupon_emoney',
		oi_delivery_type = '{$item[delivery_type]}',
		oi_goods_delivery = '{$item[goods_delivery]}',
		oi_special_discount_amount = '{$item[special_discount_amount]}',
		oi_delivery_idx = '{$oi_delivery_idx}',
		oi_area_idx = '{$area_idx}'
	";

	$db->query($query);

	$orderSeq += 1;
	if(strtoupper($plusCheeseCfg['use']) == "Y" && $_SESSION['plusCheese']['goodsno'] == $item[goodsno]){
		$plusCheeseGoods['orderSeq'][] += $orderSeq;
		$plusCheeseGoods['prdCode'][] = $item['goodsno'];
		$plusCheeseGoods['prdName'][] = $item['goodsnm'];
		$plusCheeseGoods['optionTitle'][] = $item['opt'][0]." ".$item['opt'][1];
		$plusCheeseGoods['prdPrice'][] = $item[price]+$item[addprice];
		$plusCheeseGoods['orderCount'][] = $item['ea'];
		$plusCheeseGoods['orderSum'][] = ($item[price]+$item[addprice]) * $item['ea'];
	}

}

## �÷���ġ�� ���� ����
if(strtoupper($plusCheeseCfg['use']) == "Y" && !empty($_SESSION['plusCheese']['ID']) && !empty($_SESSION['plusCheese']['affiliate'])){
	$sellerID = $_SESSION['plusCheese']['ID'];
	session_unregister("plusCheese");

	$xml = new XMLParser();
	global $godo;
	$plusCheese = &load_class('plusCheese','plusCheese', "1");
	$pcResult = $plusCheese->sendCPSOrder($ordno, $plusCheeseGoods, $cart->delivery, $settleprice, $_POST['settlekind'], $sellerID);

	$xml = new XMLParser();
	$xml->parse($pcResult);
	$data = $xml->parseOut();
	for($i=0;$i<count($data);$i++){
		if($data[$i]['tag'] == "RESPONSECODE"){
			if($data[$i]['val'] != "000"){
				msg("�÷���ġ��� ������� �ʾ� �ֹ��� �� �����ϴ�.","order.php","parent");
				exit;
			}
		}
		if($data[$i]['tag'] == "TEMPORDERNO"){
			$query = "UPDATE ".GD_ORDER." SET pCheeseOrdNo='".$data[$i]['val']."' WHERE ordno='".$ordno."'";
			$db->query($query);
		}
	}
}

## ��ٱ��� �����ε�
if($_SESSION['cartReminder'] > 0) {
	$cartReminder = Core::loader('CartReminder');
	$cartReminder->setCartReminderOrder();
}

## ���� ��� ���� ����
if($ArrCouponSql) foreach($ArrCouponSql as $v)$db->query($v);

## �ֹ����� ������ ����ȭ ����
$integrate_order = &load_class('integrate_order','integrate_order');
register_shutdown_function(array(&$integrate_order, 'reserveSync'));

if (in_array($_POST[settlekind],array("c","o","v","h","p","u","i","y","t"))){
	// ���հ��� ���� - 15.04.28 - su
	$order = new order();
	$order->load($ordno);
	$tax = $order->getTaxAmount();
	$vat = $order->getVatAmount();
	$taxfree = $order->getTaxFreeAmount();

	$nScreenPayment = Core::Loader('nScreenPayment');
	$nScreenPayment->executeCardGate($settleprice, $_POST[settlekind], '', $tax, $vat, $taxfree);
	exit;

} else if ($_POST[settlekind]=="d"){
	//��������,������ üũ
	if($settleprice>0 || $discount==0){	//�� �����ݾ��� 0���� ũ�ų� or ���αݾ��� 0���̸�
		msg('��ȿ�� �����ݾ��� �ƴմϴ�.',-1);
 		exit;
	}

	// ���̹� ���ϸ��� ���� ���� API
	$ncashResult = $naverNcash->payment_approval($ordno, true);
	if($ncashResult===false)
	{
		msg('���̹� ���ϸ��� ��뿡 �����Ͽ����ϴ�.', 'order_fail.php?ordno='.$ordno,'parent');
		exit();
	}
	ctlStep($ordno,1,"stock");
} else if ($_POST[settlekind]=="a"){
	// ���̹� ���ϸ��� ���� ���� API
	$ncashResult = $naverNcash->payment_approval($ordno, false);
	if($ncashResult===false)
	{
		msg('���̹� ���ϸ��� ��뿡 �����Ͽ����ϴ�.', 'order_fail.php?ordno='.$ordno,'parent');
		exit();
	}

	### ���ں������� �߱�
	if ($_POST[eggResno][0] != '' && $_POST[eggResno][1] != '' && $_POST[eggAgree] == 'Y'){
		include '../lib/egg.class.usafe.php';
		$eggData = array('ordno' => $ordno, 'issue' => $_POST[eggIssue], 'resno1' => $_POST[eggResno][0], 'resno2' => $_POST[eggResno][1], 'agree' => $_POST[eggAgree]);
		$eggCls = new Egg( 'create', $eggData );
		if ( $eggCls->isErr == true ){
			$db->query("update ".GD_ORDER." set step2=54 where ordno='$ordno'");
			$db->query("update ".GD_ORDER_ITEM." set istep=54 where ordno='$ordno'");

			echo "<script>parent.location.replace('order_fail.php?ordno=$ordno');</script>";
			exit;
		}
	}

	### ������ �ֹ� �۽�
	include '../lib/bank.class.php';
	$bk = new Bank( 'send', $ordno );
}

### ��� ó��
setStock($ordno);

### ��ǰ���Խ� ������ ���
if ($sess[m_no] && $_POST[emoney]){
	setEmoney($sess[m_no],-$_POST[emoney],"��ǰ���Խ� ������ ���� ���",$ordno);
}

### ���ݿ����� ��û
if ($_POST['cashreceipt'] == 'Y'){
	ob_start();
	include '../lib/cashreceipt.class.php';
	$cashreceipt = new cashreceipt();
	$indata = array();
	$indata['ordno'] = $ordno;
	$indata['useopt'] = $_POST['cashuseopt'];
	$indata['certno'] = $_POST['cashcertno'];
	$resid = $cashreceipt->putUserReceipt($indata);
	ob_end_clean();
}

### �ֹ�Ȯ�θ���
$modeMail = 0;
if ($_POST[email] && $cfg["mailyn_0"]=="y"){
	$_POST['address'] = $_POST['address']. ' ' .$_POST['address_sub'];
	$_POST['str_settlekind'] = $r_settlekind[ $_POST['settlekind'] ];
	$_POST['zipcode'] = ($_POST['zonecode']) ? $_POST['zonecode'] : $_POST['zipcode'];

	include_once "../lib/automail.class.php";
	$automail = new automail();
	$automail->_set($modeMail,$_POST[email],$cfg);
	$automail->_assign($_POST);
	$automail->_assign('cart',$cart);
	$automail->_assign('deli_msg',$delivery['msg']);
	if ($_POST[settlekind]=="a"){
		$data = $db->fetch("select * from ".GD_LIST_BANK." where sno='$_POST[bankAccount]'");
		$automail->_assign($data);
	}
	$automail->_send();
}

### �ֹ�Ȯ�� SMS
sendSmsCase('order',$_POST[mobileOrder]);

### ���˸� SMS
sendSmsStock($ordno);

### �Աݿ�û SMS
if($_POST['settlekind'] == "a"){
	$data = $db->fetch("select * from ".GD_LIST_BANK." where sno='$_POST[bankAccount]'");
	$dataSms['account']		= $data['bank']." ".$data['account']." ".$data['name'];
	$GLOBALS['dataSms']		= $dataSms;
	sendSmsCase('account',$_POST[mobileOrder]);
}


/* ����ġ push ��� �߰� 2012-03-03 dn */
@include '../lib/pAPI.class.php';
$pAPI = new pAPI();
$pAPI->noticePushOrder($ordno);

echo "<script>parent.location.replace('order_end.php?ordno=$ordno');</script>";
//$db->viewLog();

?>
