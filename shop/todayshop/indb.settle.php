<?
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");

include "../lib/library.php";
include "../conf/config.php";
include "../conf/config.pay.php";
@include "../conf/coupon.php";
include '../lib/lib.func.egg.php';
$egg = getEggConf();

if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
	$_POST = validation::xssCleanArray($_POST, array(
		validation::DEFAULT_KEY	=> 'text'
	));
}

$_GET['cart_type'] = 'todayshop';

### ��ٿ� ����
$about_coupon = Core::loader('about_coupon');

### okĳ�������
@include "../conf/pg.cashbag.php";
if($_POST[settlekind] == 'p' && $cashbag[usesettle] == 'on'){
	$cfg['settlePg'] = "kcp";
}
if( $cashbag['use'] == 'on' && $cashbag['code'] && $cashbag['key'] ) $cashbagcard = @implode('',$_POST['cashbag']);


// �����̼� ������� ��� PG ���� ��ü
resetPaymentGateway();


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

$cart = Core::loader('Cart', $_COOKIE[gd_isDirect]);
$Goods = Core::loader('Goods');
$coupon_price = Core::loader('coupon_price');

### �ֹ��� ���� üũ
$cart -> chkOrder();
chkCart($cart);

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
	'coupon' => $_POST['coupon']
);
$delivery = getDeliveryMode($param);
$cart -> delivery = $delivery[price];
$cart -> totalprice += $delivery[price];

### �ܿ� ��� üũ
foreach ($cart->item as $v){
	$cart->chkStock($v[goodsno],$v[opt][0],$v[opt][1],$v[ea]);
	$arItemSno[] = $v[goodsno];
}

### ������ ����
$_POST['coupon'] = $cart -> coupon;
$discount = $_POST[coupon] + $_POST[emoney] + $cart->dcprice;
if ($cart->totalprice - $discount < 0){
	$_POST[emoney] = $cart->totalprice - $_POST[coupon]-$cart->dcprice;
}

### ������ ��ȿ�� üũ
chkEmoney($set[emoney],$_POST[emoney]);

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
if($msg) msg($errmsg,-1);

## ���� �������
if($coupon_price->arCoupon && $sess['m_no']){

		if($coupon_price->arCoupon)foreach($coupon_price->arCoupon as $arCoupon){
			if(!in_array($arCoupon[sno],$_POST['apply_coupon'])) continue;

			if($arCoupon['applysno']){
				$setQuery = ",applysno = '$arCoupon[applysno]'";
			}else if($arCoupon['downsno']){
				$setQuery = ",downloadsno	= '$arCoupon[downsno]'";
			}

			if($arCoupon['sale'])	$couponDc = array_sum($arCoupon['sale']);
			if($arCoupon['reserve']) $couponEmoney = array_sum($arCoupon['reserve']);
			$ArrCouponSql[] = "insert into gd_coupon_order set
							ordno		= '$ordno',
							coupon		= '$arCoupon[coupon]',
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

$discount = $_POST[coupon] + $_POST[emoney] + $cart->dcprice;

### ���ں������� ������ ����
$_POST['eggFee'] = reCalcuEggFee($_POST['eggFee'], ($cart->totalprice - $discount), $egg['feerate'], $_POST['eggFeeRateYn']);

$settleprice = $cart->totalprice + $_POST[eggFee] - $discount;

### PG��� �̿�� ó�� (������,�����ݰ��� ����)
if (in_array($_POST[settlekind],array("c","o","v","h","p","y"))){
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

### �ֹ���ȣ �ߺ����� üũ
list ($chk,$pre_settlekind) = $db->fetch("select ordno,settlekind from ".GD_ORDER." where ordno='$ordno'");
if ($chk){
	if (in_array($_POST[settlekind],array("c","o","v","h","p","y"))){
		### ��������� ����� ��� ������Ʈ ó��
		if ($_POST[settlekind]!=$pre_settlekind) $db->query("update ".GD_ORDER." set settlekind='$_POST[settlekind]' where ordno='$ordno'");
		switch ($cfg[settlePg])
		{
			case "allat":
				echo "<script>
					if(parent.document.getElementsByName('allat_amt')[0].value == '".$settleprice."'){
						parent.ftn_app();
					}else{
						alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
			case "allatbasic":
				echo "<script>
					if(parent.document.getElementsByName('allat_amt')[0].value == '".$settleprice."'){
						parent.ftn_approval();
					}else{
						alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
			case "inicis":
				echo "<script>
					if(parent.document.getElementsByName('price')[0].value == '".$settleprice."'){
						var fm=parent.document.ini; if (parent.pay(fm)) fm.submit();
					}else{
						alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
			case "inipay":
				echo "<script>
					if(parent.document.getElementsByName('INISettlePrice')[0].value == '".$settleprice."'){
						var fm=parent.document.ini; if (parent.pay(fm)) fm.submit();
					}else{
						alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
			case "agspay":
				echo "<script>
					if(parent.document.getElementsByName('Amt')[0].value == '".$settleprice."'){
						var fm=parent.document.frmAGS_pay; if (parent.Pay(fm)) parent.Pay(fm);
					}else{
						alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
			case "dacom":
				echo "<script>
					if(parent.document.getElementsByName('amount')[0].value == '".$settleprice."'){
						parent.openWindow();
					}else{
						alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
			case "lgdacom":
				echo "<script>
					if(parent.document.getElementsByName('LGD_AMOUNT')[0].value == '".$settleprice."'){
						parent.doPay_ActiveX();
					}else{
						alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
			case "kcp":
				echo "<script>
					if(parent.document.getElementsByName('good_mny')[0].value == '".$settleprice."'){
						var fm=parent.document.order_info; if(parent.jsf__pay(fm))fm.submit();
					}else{
						alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
		}
	} else msg("������ �ֹ���ȣ�� �����մϴ�","order.php","parent");
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

### �ֹ����� ����
$query = "
insert into ".GD_ORDER." set $qrTmp
	ordno			= '$ordno',
	nameOrder		= '$_POST[nameOrder]',
	email			= '$_POST[email]',
	phoneOrder		= '$_POST[phoneOrder]',
	mobileOrder		= '$_POST[mobileOrder]',
	nameReceiver	= '$_POST[nameReceiver]',
	phoneReceiver	= '$_POST[phoneReceiver]',
	mobileReceiver	= '$_POST[mobileReceiver]',
	zipcode			= '$_POST[zipcode]',
	address			= '$_POST[address] $_POST[address_sub]',
	settlekind		= '$_POST[settlekind]',
	settleprice		= '$settleprice',
	prn_settleprice		= '$settleprice',
	goodsprice		= '{$cart->goodsprice}',
	deli_title		= '".$r_deli[$_POST['deliPoli']]."',
	delivery		= '{$cart->delivery}',
	deli_type		= '".$delivery['type']."',
	deli_msg		= '".$delivery['msg']."',
	coupon			= '$_POST[coupon]',
	emoney			= '$_POST[emoney]',
	memberdc		= '".$cart->dcprice ."',
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
	about_dc_sum = '$about_dc_sum'
";
if(!$db->query($query))msg('���������� �ֹ� ������ ���� �ʾҽ��ϴ�.\n�ٽ� �ѹ� �õ��ϼ���!!',0);

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

	$query = "
	insert into ".GD_ORDER_ITEM." set $qrTmp2
		ordno			= '$ordno',
		goodsno			= '$item[goodsno]',
		goodsnm			= '$item[goodsnm]',
		opt1			= '{$item[opt][0]}',
		opt2			= '{$item[opt][1]}',
		addopt			= '$addopt',
		price			= '".($item[price]+$item[addprice])."',
		supply			= '$item[supply]',
		reserve			= '$item[reserve]',
		extra_reserve			= '$item[extra_reserve]',
		memberdc		= '$memberdc',
		ea				= '$item[ea]',
		maker			= '$maker',
		brandnm			= '$brandnm',
		tax				= '$tax',
		deli_msg		= '$item_deli_msg',
		stockable		= '$stockable',
		about_coupon_flag = '$about_coupon_flag',
		about_dc_price = '$item[about_dc_price]'
	";
	$db->query($query);

}

## ���� ��� ���� ����
if($ArrCouponSql) foreach($ArrCouponSql as $v)$db->query($v);

## �ֹ����� ������ ����ȭ ����
$integrate_order = Core::loader('integrate_order');
register_shutdown_function(array(&$integrate_order, 'reserveSync'));

if (in_array($_POST[settlekind],array("c","o","v","h","p","y"))){
	switch ($cfg[settlePg])
	{
		case "allat":
			echo "<script>
				if(parent.document.getElementsByName('allat_amt')[0].value == '".$settleprice."'){
					parent.ftn_app();
				}else{
					alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
					parent.location.replace('order.php');
				}
				</script>";
			exit;
		case "allatbasic":
			echo "<script>
				if(parent.document.getElementsByName('allat_amt')[0].value == '".$settleprice."'){
					parent.ftn_approval();
				}else{
					alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
					parent.location.replace('order.php');
				}
				</script>";
			exit;
		case "inicis":
			echo "<script>
				if(parent.document.getElementsByName('price')[0].value == '".$settleprice."'){
					var fm=parent.document.ini; if (parent.pay(fm)) fm.submit();
				}else{
					alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
					parent.location.replace('order.php');
				}
				</script>";
			exit;
		case "inipay":
			echo "<script>
				if(parent.document.getElementsByName('INISettlePrice')[0].value == '".$settleprice."'){
					var fm=parent.document.ini; if (parent.pay(fm)) fm.submit();
				}else{
					alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
					parent.location.replace('order.php');
				}
				</script>";
			exit;
		case "agspay":
			echo "<script>
				if(parent.document.getElementsByName('Amt')[0].value == '".$settleprice."'){
					var fm=parent.document.frmAGS_pay; if (parent.Pay(fm)) parent.Pay(fm);
				}else{
					alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
					parent.location.replace('order.php');
				}
				</script>";
			exit;
		case "dacom":
			echo "<script>
				if(parent.document.getElementsByName('amount')[0].value == '".$settleprice."'){
					parent.openWindow();
				}else{
					alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
					parent.location.replace('order.php');
				}
				</script>";
			exit;
		case "lgdacom":
			echo "<script>
				if(parent.document.getElementsByName('LGD_AMOUNT')[0].value == '".$settleprice."'){
					parent.doPay_ActiveX();
				}else{
					alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
					parent.location.replace('order.php');
				}
				</script>";
			exit;
		case "kcp":
			echo "<script>
				if(parent.document.getElementsByName('good_mny')[0].value == '".$settleprice."'){
					var fm=parent.document.order_info; if(parent.jsf__pay(fm))fm.submit();
				}else{
					alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
					parent.location.replace('order.php');
				}
				</script>";
			exit;
	}
	exit;
} else if ($_POST[settlekind]=="d"){
	//��������,������ üũ
	if($settleprice>0 || $discount==0){     //�� �����ݾ��� 0���� ũ�ų� or ���αݾ��� 0���̸�
		msg('��ȿ�� �����ݾ��� �ƴմϴ�.',-1);
		exit;
	}
	ctlStep($ordno,1,"stock");
} else if ($_POST[settlekind]=="a"){
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
	//$automail->_send();
}

### �ֹ�Ȯ�� SMS
//sendSmsCase('order',$_POST[mobileOrder]);

### �Աݿ�û SMS
if($_POST['settlekind'] == "a"){
	$data = $db->fetch("select * from ".GD_LIST_BANK." where sno='$_POST[bankAccount]'");
	$dataSms['account']		= $data['bank']." ".$data['account']." ".$data['name'];
	$GLOBALS['dataSms']		= $dataSms;
	//sendSmsCase('account',$_POST[mobileOrder]);
}


echo "<script>parent.location.replace('order_end.php?ordno=$ordno');</script>";
//$db->viewLog();

?>
