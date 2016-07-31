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

	// 주문 상품 수량 수정
	case "modifyEa" :

		$cart = new Cart();
		$cart->modCart($_POST[stock]);

		/*$key	= ($_POST['goodsListKey'])	? $_POST['goodsListKey']	: ""; // 상품 키
		$stock	= ($_POST['stock'])			? $_POST['stock']			: ""; // 수량

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

	// 주문 상품 제거
	case "goodsDelete" :

		$delList = ($_POST['delList']) ? explode(';',$_POST['delList']) : array();

		$cart = new Cart();
		$cart->delCart($delList);

		go("../order/self_order_goods.php");

		break;
	// 주문 상품 추가
	case "addGoods" :
		setcookie('gd_isDirect','',time() - 3600,'/');
		$memID		= ($_POST['memID']) ? trim($_POST['memID']) : "";

		// 카트에 상품을 담기 위해 로그인 정보 위조
		$tmpSess = $sess; // 임시 저장
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

		$sess = $tmpSess; // 원래 로그인 정보로 수정

		echo "<script>parent.opener.location.reload();parent.window.close();</script>";

		break;
	// 주문 상품 수정
	case "editOption" :

		$cart = new Cart();
		$cart->editOption($_POST);

		echo "<script>parent.opener.location.reload();parent.window.close();</script>";
		break;

		$goodsno	= ($_POST['goodsno'])	? trim($_POST['goodsno'])	: "";		// 상품고유번호
		$key		= ($_POST['key'])		? trim($_POST['key'])		: "";		// 수정될 선택 상품 키
		$opt		= ($_POST['opt'])		? trim($_POST['opt'][0])	: "";		// 옵션1|옵션2
		$addopt		= ($_POST['addopt'])	? $_POST['addopt']			: array();	// array(추가옵션[0]:추가옵션1, 추가옵션[1]:추가옵션2, ...
		$ea			= ($_POST['ea'])		? trim($_POST['ea'])		: "";		// 수량

		$ar_key = explode("^", $key);

		// 옵션
			$ar_opt = explode("|", $opt);
			list($optno) = $db->fetch("SELECT optno FROM ".GD_GOODS_OPTION." WHERE goodsno = '$goodsno' AND opt1 = '".$ar_opt[0]."' AND opt2 = '".$ar_opt[1]."' and go_is_deleted <> '1'");

		// 추가 옵션
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

	// 회원 선택
	case "selectMember" :
		$m_id = ($_POST['m_id']) ? trim($_POST['m_id']) : ""; // 회원 아이디

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

	// 배송비 계산
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

		$tmpSess = $sess; // 임시 저장
		list($sess['m_no'], $sess['m_id'], $sess['level'], $sess['groupsno']) = $db->fetch("SELECT m.m_no, m.m_id, m.level, g.sno FROM ".GD_MEMBER." AS m LEFT JOIN ".GD_MEMBER_GRP." AS g ON m.level = g.level WHERE m.m_id = '$memID'"); // 주문자의 회원정보를 세션 변수에 저장

		$deliveryPay = getDeliveryMode($param);
		echo number_format($deliveryPay['price']);
		$sess = $tmpSess; // 원래 세션정보로 수정
		break;

	// 회원 할인 계산
	case "memberDC" :
		setcookie('gd_isDirect','',time() - 3600,'/');
		$memID = ($_GET['memID']) ? $_GET['memID'] : "";

		$db->query("UPDATE ".GD_CART." SET m_id = '$memID' WHERE uid = '".$_SESSION['uid']."'");

		$tmpSess = $sess; // 임시 저장
		list($sess['m_no'], $sess['m_id'], $sess['level'], $sess['groupsno'], $member['excep'], $member['excate'], $member['dc']) = $db->fetch("SELECT m.m_no, m.m_id, m.level, g.sno, g.excep, g.excate, g.dc FROM ".GD_MEMBER." AS m LEFT JOIN ".GD_MEMBER_GRP." AS g ON m.level = g.level WHERE m.m_id = '$memID'"); // 주문자의 회원정보를 세션 변수에 저장

		$cart = new Cart;

		$cart->excep = $member['excep'];
		$cart->excate = $member['excate'];
		$cart->dc = $member['dc']."%";

		$cart->calcu();
		echo number_format($cart->dcprice)." 원";

		$sess = $tmpSess; // 원래 세션정보로 수정
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

		$tmpSess = $sess; // 임시 저장
		list($sess['m_no'], $sess['m_id'], $sess['level'], $sess['groupsno'], $member['excep'], $member['excate'], $member['dc']) = $db->fetch("SELECT m.m_no, m.m_id, m.level, g.sno, g.excep, g.excate, g.dc FROM ".GD_MEMBER." AS m LEFT JOIN ".GD_MEMBER_GRP." AS g ON m.level = g.level WHERE m.m_id = '$memID'"); // 주문자의 회원정보를 세션 변수에 저장

		$deliveryPay = getDeliveryMode($param);

		$cart = new Cart;

		$cart->excep = $member['excep'];
		$cart->excate = $member['excate'];
		$cart->dc = $member['dc']."%";

		$cart->calcu();

		if(!$set['emoney']['emoney_use_range'])$tmp = $cart->goodsprice;
		else $tmp = $cart->totalprice;
		$tmp = $tmp - getDcPrice($cart->goodsprice, $cart->dc);
		$emoney_max = getDcprice($tmp, $set['emoney']['max']) + 0; // 최대 사용 가능 적립금

		if($set['emoney']['emoney_use_range']) $emoney_max = $cart->goodsprice + $deliveryPay['price'] - $cart->dcprice;
		else $emoney_max = $cart->goodsprice - $cart->dcprice;

		list($memEmoney) = $db->fetch("SELECT emoney FROM ".GD_MEMBER." WHERE m_id = '$memID'"); // 소유 적립금

		echo " (보유적립금 : ".number_format($memEmoney)." 원) 적립금은 ".number_format($set['emoney']['min'])."원부터 ".number_format($emoney_max)."까지 사용이 가능합니다.";

		$sess = $tmpSess; // 원래 세션정보로 수정

		break;

		// 주문서 작성 부분 시작
	case "writeOrder" :
		$tmpSess = $sess; // 임시 저장
		list($sess['m_no'], $sess['m_id'], $sess['level'], $sess['groupsno']) = $db->fetch("SELECT m.m_no, m.m_id, m.level, g.sno FROM ".GD_MEMBER." AS m LEFT JOIN ".GD_MEMBER_GRP." AS g ON m.level = g.level WHERE m.m_id = '".$_POST['m_id']."'"); // 주문자의 회원정보를 세션 변수에 저장
		$_SESSION['sess'] = $sess;

		$_POST['settlekind'] = "a"; // 무통장입금
		$ordno = $_POST['ordno'];
		$_POST['coupon'] = str_replace(",", "", $_POST['coupon']);

		### 회원정보 가져오기
			if($sess) $member = $db->fetch("SELECT * FROM ".GD_MEMBER." a LEFT JOIN ".GD_MEMBER_GRP." b ON a.level = b.level WHERE m_no = '".$sess['m_no']."'", 1);
			if($_POST['memType'] == "2") $m_no = "0";
			else $m_no = $sess['m_no'];

		$cart = Core::loader('Cart', $_COOKIE[gd_isDirect]);
		$Goods = Core::loader('Goods');
		$coupon_price = Core::loader('coupon_price');

		### 주문서 정보 체크
			$cart->chkOrder();
			chkCart($cart);

		$cart->reset(); //주문시 상품가격 정보를 정확히 하기위해

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

		### 잔여 재고 체크
			foreach($cart->item as $v) {
				$cart->chkStock($v['goodsno'], $v['opt'][0], $v['opt'][1], $v['ea']);
				$arItemSno[] = $v['goodsno'];
			}

		### 적립금 재계산
			$_POST['coupon'] = $cart->coupon;
			$discount = $_POST['coupon'] + $_POST['emoney'] + $cart->dcprice + $cart->special_discount_amount;
			if($cart->totalprice - $discount < 0){
				msg('할인금액이 결제금액보다 많습니다.',-1);
				exit;
			}

		### 적립금 유효성 체크
			chkEmoney($set['emoney'], $_POST['emoney']);

		### 주문정보/쿠폰 유효성 체크
			$coupon_price->set_config($cfgCoupon);
			foreach($cart->item as $v){
				$arCategory = $Goods->get_goods_category($v['goodsno']);
				$coupon_price->set_item($v['goodsno'], $v['price'], $v['ea'], $arCategory, $v['opt'][0], $v['opt'][1], $v['addopt'], $v['goodsnm']);
			}
			$coupon_item = $coupon_price->get_goods_coupon('order');

			$result = $coupon_price->check_coupon($_POST['coupon'], $_POST['coupon_emoney'], $_POST['settlekind'], $_POST['apply_coupon']);
			if($result == "cash") $errmsg = "무통장 결제로만 사용가능한 쿠폰입니다.";
			if($result == "sale"||$result == "reserve") $errmsg = "쿠폰 사용정보가 올바르지 않습니다.";
			if($result!==true) msg($errmsg,-1);

		## 쿠폰 사용정보
			if($coupon_price->arCoupon && $sess['m_no']) {

				if($coupon_price->arCoupon) foreach($coupon_price->arCoupon as $arCoupon) {
					if (isset($_POST['apply_coupon']) === false || in_array($arCoupon['sno'], (array)$_POST['apply_coupon']) === false) continue;

					if($arCoupon['applysno']) $setQuery = ",applysno = '$arCoupon[applysno]'";
					else if($arCoupon['downsno']) {
						$setQuery = ",downloadsno = '".$arCoupon['downsno']."'";
						$ArrCouponSql[] = "UPDATE ".GD_COUPON_APPLY." SET status = '1' WHERE sno = '".$arCoupon['applysno']."'"; //발급 쿠폰 상태 변경
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

		### 주문데이타 가공
			$_POST['phoneOrder']		= @implode("-", $_POST['phoneOrder']);
			$_POST['mobileOrder']		= @implode("-", $_POST['mobileOrder']);
			$_POST['phoneReceiver']		= @implode("-", $_POST['phoneReceiver']);
			$_POST['mobileReceiver']	= @implode("-", $_POST['mobileReceiver']);
			$_POST['zipcode']			= @implode("-", $_POST['zipcode']);

		$discount = $_POST['coupon'] + $_POST['emoney'] + $cart->dcprice + $cart->special_discount_amount;

		// 결제금액 갱신
		$settleprice = $cart->totalprice - $discount;
		$_POST['settleprice']		= $settleprice;
		$_REQUEST['settleprice']	= $settleprice;

		if(!$set['delivery']['deliverynm']) $a_tmp[] = $set['delivery']['deliverynm'];
		else $a_tmp[] = '기본 배송';
		$b_tmp = @explode('|', $set['r_delivery']['title']);
		$r_deli = @array_merge($a_tmp, $b_tmp);

		## 쿠폰 사용정보 체크
			if($_POST['apply_coupon'] && $sess['m_no']){
				foreach($_POST['apply_coupon'] as $v){

					// offline 쿠폰
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
					// online 쿠폰 (온라인 다운로드 쿠폰 포함)
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
						if ((int)$cp['coupontype'] === 1) {	// 재사용이 가능할 수 있는 다운로드 쿠폰
							if ((int)$cp['dncnt'] > 0 && $cp['dncnt'] <= $cp['usecnt']) {
								msg('이미 사용된 쿠폰입니다.');
								exit;
							}
						}
						else {
							msg('이미 사용된 쿠폰입니다.');
							exit;
						}
					}
					else {
						// valid coupon
					}

				}
			}

		### 주문번호 중복여부 체크
			list($chk, $pre_settlekind) = $db->fetch("SELECT ordno, settlekind FROM ".GD_ORDER." WHERE ordno = '$ordno'");

		### 적립이 결제가 기준일때 적립금 재설정
			if($set['emoney']['chk_goods_emoney'] == '0' && $set['emoney']['emoney_standard'] == '1') {
				$cart->resetReserveAmount($settleprice);
			}

		### 회원 추가 적립금 설정
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

		if($delivery['type'] == "후불" && $delivery['freeDelivery'] == "1") $delivery['msg'] = "0원";

		### 주문정보 저장
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
			if(!$db->query($query)) msg('정상적으로 주문 접수가 되지 않았습니다.\n다시 한번 시도하세요!!',0);

		### 주문상품 저장
		foreach($cart->item as $k=>$item) {

			unset($addopt);

			### 상품 테이블에서 공급가 가져오기
			list($item[supply]) = $db->fetch("SELECT supply FROM ".GD_GOODS_OPTION." WHERE goodsno = '".$item['goodsno']."' AND opt1 = '".$item['opt'][0]."' AND opt2 = '".$item['opt'][1]."' and go_is_deleted <> '1'");

			### 추가옵션
			if(is_array($item['addopt'])) {
				foreach($item['addopt'] as $v) $addopt[] = $v['optnm'].":".$v['opt'];
				$addopt = @implode("^", $addopt);
			}
			$memberdc = $item['memberdc'];

			$item['goodsnm'] = addslashes(strip_tags($item['goodsnm']));

			### 제조사, 브랜드
			list($maker, $brandnm, $tax, $delivery_type, $goods_delivery, $usestock) = $db->fetch("SELECT maker, brandnm, tax, delivery_type, goods_delivery, usestock FROM ".GD_GOODS." LEFT JOIN ".GD_GOODS_BRAND." ON brandno = sno WHERE goodsno = '".$item['goodsno']."'");
			$maker = addslashes($maker);
			$brandnm = addslashes($brandnm);
			$item_deli_msg = "";
			if($delivery_type == 3){
				$item_deli_msg = "착불";
				if($goods_delivery) $item_deli_msg .= " ".number_format($goods_delivery)." 원";
			}
			if($usestock == 'o') $stockable = "y";
			else $stockable = "n";

			// 상품별 쿠폰 금액 (할인, 적립금)
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

		## 쿠폰 사용 정보 저장
		if($ArrCouponSql) foreach($ArrCouponSql as $v)$db->query($v);

		### 무통장 주문 송신
		include '../../lib/bank.class.php';
		$bk = new Bank( 'send', $ordno );

		### 재고 처리
		setStock($ordno);

		### 상품구입시 적립금 사용
		if($sess['m_no'] && $_POST['emoney']){
			setEmoney($sess['m_no'], -$_POST['emoney'], "상품구입시 적립금 결제 사용", $ordno);
		}

		### 주문 완료
		$cart->buy();

		### 주문확인메일
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

		### 주문확인 SMS
		sendSmsCase('order', $_POST['mobileOrder']);

		### 재고알림 SMS
		sendSmsStock($ordno);

		### 입금요청 SMS
			$data = $db->fetch("SELECT * FROM ".GD_LIST_BANK." WHERE sno = '".$_POST['bankAccount']."'");
			$dataSms['account'] = $data['bank']." ".$data['account']." ".$data['name'];
			$GLOBALS['dataSms'] = $dataSms;
			sendSmsCase('account', $_POST['mobileOrder']);

		$sess = $tmpSess; // 원래 세션정보로 수정
		$_SESSION['sess'] = $tmpSess;

		unset($_SESSION['uid']); // 장바구니 uid 초기화

		echo "<script>parent.location.replace('../order/list.php?mode=group&period=0&first=1');</script>";

		break;
		// 주문서 작성 부분 끝

	case 'destroyUniqueId':
		unset($_SESSION['uid']);
		break;

	case 'specialDC':
		$memID = ($_GET['memID']) ? $_GET['memID'] : "";

		$tmpSess = $sess; // 임시 저장
		list($sess['m_no'], $sess['m_id'], $sess['level'], $sess['groupsno'], $member['excep'], $member['excate'], $member['dc']) = $db->fetch("SELECT m.m_no, m.m_id, m.level, g.sno, g.excep, g.excate, g.dc FROM ".GD_MEMBER." AS m LEFT JOIN ".GD_MEMBER_GRP." AS g ON m.level = g.level WHERE m.m_id = '$memID'"); // 주문자의 회원정보를 세션 변수에 저장

		$cart = new Cart;
		$cart->calcu();
		echo number_format($cart->special_discount_amount)." 원";

		$sess = $tmpSess; // 원래 세션정보로 수정
		break;
}
?>