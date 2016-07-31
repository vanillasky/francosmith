<?
include "../lib.php";
include "../../lib/cart.class.php";
@include "../../conf/config.php";
@include "../../conf/config.pay.php";
@include "../../conf/coupon.php";

$mode = ($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];

function setSOUID($uid) {
	global $_SESSION, $sess;
	if(!$uid) $_SESSION['uid'] = "sugi__".$sess['m_id']."__".time();

	return $_SESSION['uid'];
}

if ($mode != 'destroyUniqueId') {
	if ($_REQUEST['uid']) {
		$_SESSION['uid'] = $_REQUEST['uid'];
	}
	setSOUID($_SESSION['uid']);
}

switch($mode) {

	// �ֹ� ��ǰ ���� ����
	case "modifyEa" :

		$cart = new Cart();
		$cart->modCart($_POST[stock]);

		/*$key	= ($_POST['goodsListKey'])	? $_POST['goodsListKey']	: ""; // ��ǰ Ű
		$stock	= ($_POST['stock'])			? $_POST['stock']			: ""; // ����

		for($i = 0, $imax = count($key); $i < $imax; $i++) {
			$ar_key = explode("^", $key[$i]);

			$qr = "UPDATE ".GD_CART." SET
				ea = '".$stock[$i]."'
			WHERE
				uid = '".$_SESSION['uid']."'
				AND goodsno = '".$ar_key[1]."'
				AND optno = '".$ar_key[2]."'
				AND addno = '".$ar_key[3]."'
			";
			$db->query($qr);
		}*/

		go("../order/self_order_goods.php");

		break;

	// �ֹ� ��ǰ ����
	case "goodsDelete" :

		$delList = ($_POST['delList']) ? explode(';',$_POST['delList']) : array();

		$cart = new Cart();
		$cart->delCart($delList);

		go("../order/self_order_goods.php");

		break;
	// �ֹ� ��ǰ �߰�
	case "addGoods" :
		setcookie('gd_isDirect','',time() - 3600,'/');
		$memID		= ($_POST['memID']) ? trim($_POST['memID']) : "";

		// īƮ�� ��ǰ�� ��� ���� �α��� ���� ����
		$tmpSess = $sess; // �ӽ� ����
		list($sess['m_no'], $sess['m_id'], $sess['level'], $sess['groupsno']) = $db->fetch("SELECT m.m_no, m.m_id, m.level, g.sno FROM ".GD_MEMBER." AS m LEFT JOIN ".GD_MEMBER_GRP." AS g ON m.level = g.level WHERE m.m_id = '$memID'");

		$cart = new Cart();
		if ($_POST['multi_ea']) {
			$_keys = array_keys($_POST['multi_ea']);
			for ($i=0, $m=sizeof($_keys);$i<$m;$i++) {
				$_opt = $_POST['multi_opt'][ $_keys[$i] ];
				$_ea = $_POST['multi_ea'][ $_keys[$i] ];
				$_addopt = $_POST['multi_addopt'][ $_keys[$i] ];
				$_addopt_inputable = $_POST['multi_addopt_inputable'][ $_keys[$i] ];

				$cart->addCart($_POST['goodsno'],$_opt,$_addopt,$_addopt_inputable,$_ea,$_POST['goodsCoupon']);
			}
		}
		else {
			$cart->addCart($_POST['goodsno'],$_POST['opt'],$_POST['addopt'],$_POST['_addopt_inputable'],$_POST['ea'],$_POST['goodsCoupon']);
		}

		$sess = $tmpSess; // ���� �α��� ������ ����

		echo "<script>parent.opener.location.reload();parent.window.close();</script>";

		break;
	// �ֹ� ��ǰ ����
	case "editOption" :

		$cart = new Cart();
		$cart->editOption($_POST);

		echo "<script>parent.opener.location.reload();parent.window.close();</script>";
		break;

		$goodsno	= ($_POST['goodsno'])	? trim($_POST['goodsno'])	: "";		// ��ǰ������ȣ
		$key		= ($_POST['key'])		? trim($_POST['key'])		: "";		// ������ ���� ��ǰ Ű
		$opt		= ($_POST['opt'])		? trim($_POST['opt'][0])	: "";		// �ɼ�1|�ɼ�2
		$addopt		= ($_POST['addopt'])	? $_POST['addopt']			: array();	// array(�߰��ɼ�[0]:�߰��ɼ�1, �߰��ɼ�[1]:�߰��ɼ�2, ...
		$ea			= ($_POST['ea'])		? trim($_POST['ea'])		: "";		// ����

		$ar_key = explode("^", $key);

		// �ɼ�
			$ar_opt = explode("|", $opt);
			list($optno) = $db->fetch("SELECT optno FROM ".GD_GOODS_OPTION." WHERE goodsno = '$goodsno' AND opt1 = '".$ar_opt[0]."' AND opt2 = '".$ar_opt[1]."' and go_is_deleted <> '1'");

		// �߰� �ɼ�
			for($i = 0, $imax = count($addopt); $i < $imax; $i++) {
				$ar_addopt = explode("^", $addopt[$i]);
				if($tmpAddopt) $tmpAddopt .= ",";
				$tmpAddopt .= $ar_addopt[0];
			}

		$qr = "UPDATE ".GD_CART." SET
			optno = '$optno',
			addno = '$tmpAddopt',
			ea = '$ea'
		WHERE
			uid = '".$_SESSION['uid']."'
			AND goodsno = '$goodsno'
			AND optno = '".$ar_key[2]."'
			AND addno = '".$ar_key[3]."'
		";
		$db->query($qr);

		echo "<script>parent.opener.location.reload();parent.window.close();</script>";
		break;

	// ȸ�� ����
	case "selectMember" :
		$m_id = ($_POST['m_id']) ? trim($_POST['m_id']) : ""; // ȸ�� ���̵�

		$qr = "SELECT email, phone, mobile, zipcode, zonecode, address, road_address, address_sub, emoney FROM ".GD_MEMBER." WHERE m_id = '$m_id' LIMIT 1";
		$data = $db->fetch($qr);

		$data['phone'] = explode("-", $data['phone']);
		$data['mobile'] = explode("-", $data['mobile']);
		$data['zipcode'] = explode("-", $data['zipcode']);

		echo "
<script src=\"../prototype.js\"></script>
<script>
opener.$('email').value = '".$data['email']."';
opener.$('phoneOrder0').value = '".$data['phone'][0]."';
opener.$('phoneOrder1').value = '".$data['phone'][1]."';
opener.$('phoneOrder2').value = '".$data['phone'][2]."';
opener.$('mobileOrder0').value = '".$data['mobile'][0]."';
opener.$('mobileOrder1').value = '".$data['mobile'][1]."';
opener.$('mobileOrder2').value = '".$data['mobile'][2]."';
opener.$('m_zipcode0').value = '".$data['zipcode'][0]."';
opener.$('m_zipcode1').value = '".$data['zipcode'][1]."';
opener.$('m_zonecode').value = '".$data['zonecode']."';
opener.$('m_address').value = '".addslashes($data['address'])."';
opener.$('m_address_sub').value = '".addslashes($data['address_sub'])."';
opener.$('m_road_address').value = '".addslashes($data['road_address'])."';
if(opener.$('m_road_address').value != '') {
	opener.$('m_div_road_address').innerHTML = '".addslashes($data['road_address'])."';
	opener.$('div_road_address_sub').innerHTML = '".addslashes($data['address_sub'])."';
} else {
	opener.$('m_div_road_address').innerHTML = '';
	opener.$('div_road_address_sub').innerHTML = '';
}
opener.my_emoney = ".$data['emoney'].";
opener.setPayInfo();
self.close();
</script>
";
		break;

	// ��ۺ� ���
	case "orderDeliveryPay" :
		setcookie('gd_isDirect','',time() - 3600,'/');
		$memID = ($_GET['memID']) ? $_GET['memID'] : "";

		$param = array(
			'mode' => '0',
			'zipcode' => $_GET['zipcode'],
			'emoney' => $_GET['emoney'],
			'deliPoli' => $_GET['deliPoli'],
			'coupon' => $_GET['coupon'],
			'coupon_emoney' => $_GET['coupon_emoney'],
			'road_address' => $_GET['road_address'],
			'address' => $_GET['address'],
		);

		$tmpSess = $sess; // �ӽ� ����
		list($sess['m_no'], $sess['m_id'], $sess['level'], $sess['groupsno']) = $db->fetch("SELECT m.m_no, m.m_id, m.level, g.sno FROM ".GD_MEMBER." AS m LEFT JOIN ".GD_MEMBER_GRP." AS g ON m.level = g.level WHERE m.m_id = '$memID'"); // �ֹ����� ȸ�������� ���� ������ ����

		$deliveryPay = getDeliveryMode($param);
		echo number_format($deliveryPay['price']);
		$sess = $tmpSess; // ���� ���������� ����
		break;

	// ȸ�� ���� ���
	case "memberDC" :
		setcookie('gd_isDirect','',time() - 3600,'/');
		$memID = ($_GET['memID']) ? $_GET['memID'] : "";

		$db->query("UPDATE ".GD_CART." SET m_id = '$memID' WHERE uid = '".$_SESSION['uid']."'");

		$tmpSess = $sess; // �ӽ� ����
		list($sess['m_no'], $sess['m_id'], $sess['level'], $sess['groupsno'], $member['excep'], $member['excate'], $member['dc']) = $db->fetch("SELECT m.m_no, m.m_id, m.level, g.sno, g.excep, g.excate, g.dc FROM ".GD_MEMBER." AS m LEFT JOIN ".GD_MEMBER_GRP." AS g ON m.level = g.level WHERE m.m_id = '$memID'"); // �ֹ����� ȸ�������� ���� ������ ����

		$cart = new Cart;

		$cart->excep = $member['excep'];
		$cart->excate = $member['excate'];
		$cart->dc = $member['dc']."%";

		$cart->calcu();
		echo number_format($cart->dcprice)." ��";

		$sess = $tmpSess; // ���� ���������� ����
		break;

	case "reserveInfo" :
		setcookie('gd_isDirect','',time() - 3600,'/');
		$memID = ($_GET['memID']) ? $_GET['memID'] : "";

		$param = array(
			'mode' => '0',
			'zipcode' => $_GET['zipcode'],
			'emoney' => $_GET['emoney'],
			'deliPoli' => $_GET['deliPoli'],
			'coupon' => $_GET['coupon'],
			'coupon_emoney' => $_GET['coupon_emoney'],
			'road_address' => $_GET['road_address'],
			'address' => $_GET['address'],
		);

		$tmpSess = $sess; // �ӽ� ����
		list($sess['m_no'], $sess['m_id'], $sess['level'], $sess['groupsno'], $member['excep'], $member['excate'], $member['dc']) = $db->fetch("SELECT m.m_no, m.m_id, m.level, g.sno, g.excep, g.excate, g.dc FROM ".GD_MEMBER." AS m LEFT JOIN ".GD_MEMBER_GRP." AS g ON m.level = g.level WHERE m.m_id = '$memID'"); // �ֹ����� ȸ�������� ���� ������ ����

		$deliveryPay = getDeliveryMode($param);

		$cart = new Cart;

		$cart->excep = $member['excep'];
		$cart->excate = $member['excate'];
		$cart->dc = $member['dc']."%";

		$cart->calcu();

		if(!$set['emoney']['emoney_use_range'])$tmp = $cart->goodsprice;
		else $tmp = $cart->totalprice;
		$tmp = $tmp - getDcPrice($cart->goodsprice, $cart->dc);
		$emoney_max = getDcprice($tmp, $set['emoney']['max']) + 0; // �ִ� ��� ���� ������

		if($set['emoney']['emoney_use_range']) $emoney_max = $cart->goodsprice + $deliveryPay['price'] - $cart->dcprice;
		else $emoney_max = $cart->goodsprice - $cart->dcprice;

		list($memEmoney) = $db->fetch("SELECT emoney FROM ".GD_MEMBER." WHERE m_id = '$memID'"); // ���� ������

		echo " (���������� : ".number_format($memEmoney)." ��) �������� ".number_format($set['emoney']['min'])."������ ".number_format($emoney_max)."���� ����� �����մϴ�.";

		$sess = $tmpSess; // ���� ���������� ����

		break;

		// �ֹ��� �ۼ� �κ� ����
	case "writeOrder" :
		$tmpSess = $sess; // �ӽ� ����
		list($sess['m_no'], $sess['m_id'], $sess['level'], $sess['groupsno']) = $db->fetch("SELECT m.m_no, m.m_id, m.level, g.sno FROM ".GD_MEMBER." AS m LEFT JOIN ".GD_MEMBER_GRP." AS g ON m.level = g.level WHERE m.m_id = '".$_POST['m_id']."'"); // �ֹ����� ȸ�������� ���� ������ ����
		$_SESSION['sess'] = $sess;

		$_POST['settlekind'] = "a"; // �������Ա�
		$ordno = $_POST['ordno'];
		$_POST['coupon'] = str_replace(",", "", $_POST['coupon']);

		### ȸ������ ��������
			if($sess) $member = $db->fetch("SELECT * FROM ".GD_MEMBER." a LEFT JOIN ".GD_MEMBER_GRP." b ON a.level = b.level WHERE m_no = '".$sess['m_no']."'", 1);
			if($_POST['memType'] == "2") $m_no = "0";
			else $m_no = $sess['m_no'];

		$cart = Core::loader('Cart', $_COOKIE[gd_isDirect]);
		$Goods = Core::loader('Goods');
		$coupon_price = Core::loader('coupon_price');

		### �ֹ��� ���� üũ
			$cart->chkOrder();
			chkCart($cart);

		$cart->reset(); //�ֹ��� ��ǰ���� ������ ��Ȯ�� �ϱ�����

		if($member) {
			$cart->excep = $member['excep'];
			$cart->excate = $member['excate'];
			$cart->dc = $member['dc']."%";
		}
		$cart->coupon = $_POST['coupon'];
		$cart->calcu();

		$param = array(
			'mode' => '0',
			'zipcode' => @implode("", $_POST['zipcode']),
			'emoney' => $_POST['emoney'],
			'deliPoli' => $_POST['deliPoli'],
			'coupon' => $_POST['coupon'],
			'road_address' => $_POST['road_address'],
			'address' => $_POST['address'],
		);
		$delivery = getDeliveryMode($param);
		$cart->delivery = $delivery['price'];
		$cart->totalprice += $delivery['price'];

		### �ܿ� ��� üũ
			foreach($cart->item as $v) {
				$cart->chkStock($v['goodsno'], $v['opt'][0], $v['opt'][1], $v['ea']);
				$arItemSno[] = $v['goodsno'];
			}

		### ������ ����
			$_POST['coupon'] = $cart->coupon;
			$discount = $_POST['coupon'] + $_POST['emoney'] + $cart->dcprice + $cart->special_discount_amount;
			if($cart->totalprice - $discount < 0){
				msg('���αݾ��� �����ݾ׺��� �����ϴ�.',-1);
				exit;
			}

		### ������ ��ȿ�� üũ
			chkEmoney($set['emoney'], $_POST['emoney']);

		### �ֹ�����/���� ��ȿ�� üũ
			$coupon_price->set_config($cfgCoupon);
			foreach($cart->item as $v){
				$arCategory = $Goods->get_goods_category($v['goodsno']);
				$coupon_price->set_item($v['goodsno'], $v['price'], $v['ea'], $arCategory, $v['opt'][0], $v['opt'][1], $v['addopt'], $v['goodsnm']);
			}
			$coupon_item = $coupon_price->get_goods_coupon('order');

			$result = $coupon_price->check_coupon($_POST['coupon'], $_POST['coupon_emoney'], $_POST['settlekind'], $_POST['apply_coupon']);
			if($result == "cash") $errmsg = "������ �����θ� ��밡���� �����Դϴ�.";
			if($result == "sale"||$result == "reserve") $errmsg = "���� ��������� �ùٸ��� �ʽ��ϴ�.";
			if($result!==true) msg($errmsg,-1);

		## ���� �������
			if($coupon_price->arCoupon && $sess['m_no']) {

				if($coupon_price->arCoupon) foreach($coupon_price->arCoupon as $arCoupon) {
					if (isset($_POST['apply_coupon']) === false || in_array($arCoupon['sno'], (array)$_POST['apply_coupon']) === false) continue;

					if($arCoupon['applysno']) $setQuery = ",applysno = '$arCoupon[applysno]'";
					else if($arCoupon['downsno']) {
						$setQuery = ",downloadsno = '".$arCoupon['downsno']."'";
						$ArrCouponSql[] = "UPDATE ".GD_COUPON_APPLY." SET status = '1' WHERE sno = '".$arCoupon['applysno']."'"; //�߱� ���� ���� ����
					}

					if($arCoupon['sale'])		$couponDc = array_sum($arCoupon['sale']);
					if($arCoupon['reserve'])	$couponEmoney = array_sum($arCoupon['reserve']);
					$ArrCouponSql[] = "INSERT INTO ".GD_COUPON_ORDER." SET
					ordno	= '$ordno',
					coupon	= '".mysql_real_escape_string($arCoupon['coupon'])."',
					dc		= '$couponDc',
					emoney	= '$couponEmoney',
					regdt	= NOW(),
					m_no	= '".$sess['m_no']."'".$setQuery;
				}
			}

		### �ֹ�����Ÿ ����
			$_POST['phoneOrder']		= @implode("-", $_POST['phoneOrder']);
			$_POST['mobileOrder']		= @implode("-", $_POST['mobileOrder']);
			$_POST['phoneReceiver']		= @implode("-", $_POST['phoneReceiver']);
			$_POST['mobileReceiver']	= @implode("-", $_POST['mobileReceiver']);
			$_POST['zipcode']			= @implode("-", $_POST['zipcode']);

		$discount = $_POST['coupon'] + $_POST['emoney'] + $cart->dcprice + $cart->special_discount_amount;

		// �����ݾ� ����
		$settleprice = $cart->totalprice - $discount;
		$_POST['settleprice']		= $settleprice;
		$_REQUEST['settleprice']	= $settleprice;

		if(!$set['delivery']['deliverynm']) $a_tmp[] = $set['delivery']['deliverynm'];
		else $a_tmp[] = '�⺻ ���';
		$b_tmp = @explode('|', $set['r_delivery']['title']);
		$r_deli = @array_merge($a_tmp, $b_tmp);

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
			list($chk, $pre_settlekind) = $db->fetch("SELECT ordno, settlekind FROM ".GD_ORDER." WHERE ordno = '$ordno'");

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

		if($delivery['type'] == "�ĺ�" && $delivery['freeDelivery'] == "1") $delivery['msg'] = "0��";

		### �ֹ����� ����
			$query = "
			INSERT INTO ".GD_ORDER." SET
				ordno			= '".$ordno."',
				nameOrder		= '".trim($_POST['nameOrder'])."',
				email			= '".$_POST['email']."',
				phoneOrder		= '".$_POST['phoneOrder']."',
				mobileOrder		= '".$_POST['mobileOrder']."',
				nameReceiver	= '".$_POST['nameReceiver']."',
				phoneReceiver	= '".$_POST['phoneReceiver']."',
				mobileReceiver	= '".$_POST['mobileReceiver']."',
				zipcode			= '".$_POST['zipcode']."',
				zonecode		= '".$_POST['zonecode']."',
				address			= '".$_POST['address']."',
				road_address	= '".$_POST['road_address']."',
				settlekind		= '".$_POST['settlekind']."',
				settleprice		= '".$settleprice."',
				prn_settleprice	= '".$settleprice."',
				goodsprice		= '".$cart->goodsprice."',
				deli_title		= '".$r_deli[$_POST['deliPoli']]."',
				delivery		= '".$cart->delivery."',
				deli_type		= '".$delivery['type']."',
				deli_msg		= '".$delivery['msg']."',
				coupon			= '".$_POST['coupon']."',
				emoney			= '".$_POST['emoney']."',
				memberdc		= '".$cart->dcprice."',
				o_special_discount_amount		= '".$cart->special_discount_amount ."',
				reserve			= '".$cart->bonus."',
				bankAccount		= '".$_POST['bankAccount']."',
				bankSender		= '".$_POST['bankSender']."',
				m_no			= '".$m_no."',
				ip				= '".$_SERVER['REMOTE_ADDR']."',
				referer			= '',
				memo			= '',
				inflow			= 'sugi',
				orddt			= NOW(),
				coupon_emoney	= '".$_POST['coupon_emoney']."',
				cashbagcard		= '".$cashbagcard."',
				cbyn			= 'N'
			";
			if(!$db->query($query)) msg('���������� �ֹ� ������ ���� �ʾҽ��ϴ�.\n�ٽ� �ѹ� �õ��ϼ���!!',0);

		### �ֹ���ǰ ����
		foreach($cart->item as $k=>$item) {

			unset($addopt);

			### ��ǰ ���̺��� ���ް� ��������
			list($item[supply]) = $db->fetch("SELECT supply FROM ".GD_GOODS_OPTION." WHERE goodsno = '".$item['goodsno']."' AND opt1 = '".$item['opt'][0]."' AND opt2 = '".$item['opt'][1]."' and go_is_deleted <> '1'");

			### �߰��ɼ�
			if(is_array($item['addopt'])) {
				foreach($item['addopt'] as $v) $addopt[] = $v['optnm'].":".$v['opt'];
				$addopt = @implode("^", $addopt);
			}
			$memberdc = $item['memberdc'];

			$item['goodsnm'] = addslashes(strip_tags($item['goodsnm']));

			### ������, �귣��
			list($maker, $brandnm, $tax, $delivery_type, $goods_delivery, $usestock) = $db->fetch("SELECT maker, brandnm, tax, delivery_type, goods_delivery, usestock FROM ".GD_GOODS." LEFT JOIN ".GD_GOODS_BRAND." ON brandno = sno WHERE goodsno = '".$item['goodsno']."'");
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
			INSERT INTO ".GD_ORDER_ITEM." SET
				ordno		= '$ordno',
				goodsno		= '".$item['goodsno']."',
				goodsnm		= '".$item['goodsnm']."',
				opt1		= '".$item['opt'][0]."',
				opt2		= '".$item['opt'][1]."',
				`optno`			= '{$item[optno]}',
				addopt		= '".mysql_real_escape_string($addopt)."',
				price		= '".($item['price'] + $item['addprice'])."',
				supply		= '".$item['supply']."',
				reserve		= '".$item['reserve']."',
				extra_reserve		= '".$item['extra_reserve']."',
				memberdc	= '$memberdc',
				ea			= '".$item['ea']."',
				maker		= '$maker',
				brandnm		= '$brandnm',
				tax			= '$tax',
				deli_msg	= '$item_deli_msg',
				stockable	= '$stockable',
				coupon = '$coupon',
				coupon_emoney = '$coupon_emoney',
				oi_delivery_type = '{$item[delivery_type]}',
				oi_goods_delivery = '{$item[goods_delivery]}',
				oi_special_discount_amount = '{$item[special_discount_amount]}'
			";
			$db->query($query);

		}

		## ���� ��� ���� ����
		if($ArrCouponSql) foreach($ArrCouponSql as $v)$db->query($v);

		### ������ �ֹ� �۽�
		include '../../lib/bank.class.php';
		$bk = new Bank( 'send', $ordno );

		### ��� ó��
		setStock($ordno);

		### ��ǰ���Խ� ������ ���
		if($sess['m_no'] && $_POST['emoney']){
			setEmoney($sess['m_no'], -$_POST['emoney'], "��ǰ���Խ� ������ ���� ���", $ordno);
		}

		### �ֹ� �Ϸ�
		$cart->buy();

		### �ֹ�Ȯ�θ���
		$modeMail = 0;
		if($_POST['email'] && $cfg["mailyn_0"] == "y"){
			$_POST['address'] = $_POST['address']. ' ' .$_POST['address_sub'];
			$_POST['road_address'] = $_POST['road_address']. ' ' .$_POST['address_sub'];
			$_POST['str_settlekind'] = $r_settlekind[ $_POST['settlekind'] ];

			@include_once "../../lib/automail.class.php";
			$automail = new automail();
			$automail->_set($modeMail, $_POST['email'], $cfg);
			$automail->_assign($_POST);
			$automail->_assign('cart', $cart);
			$automail->_assign('deli_msg', $delivery['msg']);
			$data = $db->fetch("SELECT * FROM ".GD_LIST_BANK." WHERE sno = '".$_POST['bankAccount']."'");
			$automail->_assign($data);
			$automail->_send();
		}

		### �ֹ�Ȯ�� SMS
		sendSmsCase('order', $_POST['mobileOrder']);

		### ���˸� SMS
		sendSmsStock($ordno);

		### �Աݿ�û SMS
			$data = $db->fetch("SELECT * FROM ".GD_LIST_BANK." WHERE sno = '".$_POST['bankAccount']."'");
			$dataSms['account'] = $data['bank']." ".$data['account']." ".$data['name'];
			$GLOBALS['dataSms'] = $dataSms;
			sendSmsCase('account', $_POST['mobileOrder']);

		$sess = $tmpSess; // ���� ���������� ����
		$_SESSION['sess'] = $tmpSess;

		unset($_SESSION['uid']); // ��ٱ��� uid �ʱ�ȭ

		echo "<script>parent.location.replace('../order/list.php?mode=group&period=0&first=1');</script>";

		break;
		// �ֹ��� �ۼ� �κ� ��

	case 'destroyUniqueId':
		unset($_SESSION['uid']);
		break;

	case 'specialDC':
		$memID = ($_GET['memID']) ? $_GET['memID'] : "";

		$tmpSess = $sess; // �ӽ� ����
		list($sess['m_no'], $sess['m_id'], $sess['level'], $sess['groupsno'], $member['excep'], $member['excate'], $member['dc']) = $db->fetch("SELECT m.m_no, m.m_id, m.level, g.sno, g.excep, g.excate, g.dc FROM ".GD_MEMBER." AS m LEFT JOIN ".GD_MEMBER_GRP." AS g ON m.level = g.level WHERE m.m_id = '$memID'"); // �ֹ����� ȸ�������� ���� ������ ����

		$cart = new Cart;
		$cart->calcu();
		echo number_format($cart->special_discount_amount)." ��";

		$sess = $tmpSess; // ���� ���������� ����
		break;
}
?>