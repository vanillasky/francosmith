<?php

header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");

@include dirname(__FILE__) . "/../lib/library.php";
@include $shopRootDir . "/conf/config.php";
@include $shopRootDir . "/conf/config.pay.php";
@include $shopRootDir . "/lib/cart.class.php";
@include $shopRootDir . "/conf/coupon.php";

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

$discount = $_POST[coupon] + $_POST[emoney] + $cart->dcprice;
$settleprice = $cart->totalprice - $discount;

### PG��� �̿�� ó�� (������,�����ݰ��� ����)
if (in_array($_POST[settlekind],array("c","o","v","h","p"))){
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

	if (in_array($_POST[settlekind],array("c","o","v","h"))){
		### ��������� ����� ��� ������Ʈ ó��
		if ($_POST[settlekind]!=$pre_settlekind) $db->query("update ".GD_ORDER." set settlekind='$_POST[settlekind]' where ordno='$ordno'");
		switch ($cfg[settlePg])
		{
			case "allat":
				echo "<script>
					if(parent.document.getElementsByName('allat_amt')[0].value == '".$settleprice."'){
						parent.approval();
					}else{
						alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
			case "allatbasic":
				echo "<script>
					if(parent.document.getElementsByName('allat_amt')[0].value == '".$settleprice."'){
						parent.approval();
					}else{
						alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
			case "inicis":
				echo "<script>
					if(parent.document.getElementsByName('P_AMT')[0].value == '".$settleprice."'){
						parent.on_card();
					}else{
						alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
			case "lgdacom":
				echo "<script>
					if(parent.document.getElementsByName('LGD_AMOUNT')[0].value == '".$settleprice."'){
						parent.launchCrossPlatform();

					}else{
						alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
			case "agspay":
				echo "<script>
					if(parent.document.getElementsByName('Amt')[0].value == '".$settleprice."'){
						parent.Pay();
					}else{
						alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
						parent.location.replace('order.php');
					}
					</script>";
				exit;
		}
	} else

	msg("������ �ֹ���ȣ�� �����մϴ�","order.php","parent");
}

### ȸ�� �߰� ������ ����
if($member['add_emoney']) {

	$_extra_emoney_criteria = 0;

	if ($member['add_emoney_type'] == 'goods') {
		$_extra_emoney_criteria = $cart->goodsprice;
	}
	else if ($member['add_emoney_type'] == 'settle_amt') {
		$_extra_emoney_criteria = $settleprice;
	}

	$cart->bonus += ($_extra_emoney_criteria >= $member['add_emoney_std_amt']) ? getDcprice($_extra_emoney_criteria ,$member[add_emoney].'%') : 0;
}

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
	prn_settleprice	= '$settleprice',
	goodsprice		= '{$cart->goodsprice}',
	deli_title		= '".$r_deli[$_POST['deliPoli']]."',
	delivery		= '{$cart->delivery}',
	deli_type		= '".$delivery['type']."',
	deli_msg		= '".$delivery['msg']."',
	coupon			= '$_POST[coupon]',
	emoney			= '$_POST[emoney]',
	memberdc		= '".$cart->dcprice ."',
	reserve			= '{$cart->bonus}',
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
	mobilepay		= 'y'
";
if(!$db->query($query))msg('���������� �ֹ� ������ ���� �ʾҽ��ϴ�.\n�ٽ� �ѹ� �õ��ϼ���!!',0);

### �ֹ���ǰ ����
foreach ($cart->item as $k=>$item){

	unset($addopt);

	### ��ǰ ���̺��� ���ް� ��������
	list ($item[supply]) = $db->fetch("select supply from ".GD_GOODS_OPTION." where goodsno='$item[goodsno]' and opt1='{$item[opt][0]}' and opt2='{$item[opt][1]}'");

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
		memberdc		= '$memberdc',
		ea				= '$item[ea]',
		maker			= '$maker',
		brandnm			= '$brandnm',
		tax				= '$tax',
		deli_msg		= '$item_deli_msg',
		stockable		= '$stockable',
		coupon = '$coupon',
		coupon_emoney = '$coupon_emoney',
		oi_delivery_type = '{$item[delivery_type]}',
		oi_goods_delivery = '{$item[goods_delivery]}'
	";
	$db->query($query);

}

## ���� ��� ���� ����
if($ArrCouponSql) foreach($ArrCouponSql as $v)$db->query($v);

if (in_array($_POST[settlekind],array("c","o","v","h"))){
	switch ($cfg[settlePg])
	{
		case "allat":
			echo "<script>
				if(parent.document.getElementsByName('allat_amt')[0].value == '".$settleprice."'){
					parent.approval();
				}else{
					alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
					parent.location.replace('order.php');
				}
				</script>";
			exit;
		case "allatbasic":
			echo "<script>
				if(parent.document.getElementsByName('allat_amt')[0].value == '".$settleprice."'){
					parent.approval();
				}else{
					alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
					parent.location.replace('order.php');
				}
				</script>";
			exit;
		case "inicis":
			echo "<script>
				if(parent.document.getElementsByName('P_AMT')[0].value == '".$settleprice."'){
					parent.on_card();
				}else{
					alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
					parent.location.replace('order.php');
				}
				</script>";
			exit;
		case "lgdacom":
			echo "<script>
				if(parent.document.getElementsByName('LGD_AMOUNT')[0].value == '".$settleprice."'){
					parent.launchCrossPlatform();
				}else{
					alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
					parent.location.replace('order.php');
				}
				</script>";
			exit;
		case "agspay":
			echo "<script>
				if(parent.document.getElementsByName('Amt')[0].value == '".$settleprice."'){
					parent.Pay();
				}else{
					alert('�����ݾ��� �ùٸ��� �ʽ��ϴ�.');
					parent.location.replace('order.php');
				}
				</script>";
			exit;
	}
	exit;
} else

if ($_POST[settlekind]=="d"){
	ctlStep($ordno,1,"stock");
} else if ($_POST[settlekind]=="a"){

	### ������ �ֹ� �۽�
	@include $shopRootDir . '/lib/bank.class.php';
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
	@include $shopRootDir . '/lib/cashreceipt.class.php';
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

	@include_once $shopRootDir . "/lib/automail.class.php";
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

### �Աݿ�û SMS
if($_POST['settlekind'] == "a"){
	$data = $db->fetch("select * from ".GD_LIST_BANK." where sno='$_POST[bankAccount]'");
	$dataSms['account']		= $data['bank']." ".$data['account']." ".$data['name'];
	$GLOBALS['dataSms']		= $dataSms;
	sendSmsCase('account',$_POST[mobileOrder]);
}

echo "<script>parent.location.replace('order_end.php?ordno=$ordno');</script>";
//$db->viewLog();

?>
