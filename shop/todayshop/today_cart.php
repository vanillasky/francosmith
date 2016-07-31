<?

### �����Ҵ�
$orderitem_mode = "cart";
$mode = ($_POST[mode]) ? $_POST[mode] : $_GET[mode];

include "../_header.php";
if(!$set['emoney']) include dirname(__FILE__) . "/../conf/config.pay.php";
include "../lib/cart.class.php";
setcookie('gd_isDirect','',time() - 3600,'/');

require "../lib/load.class.php";
@include "../conf/naverCheckout.cfg.php";
@include "../conf/auctionIpay.cfg.php";

$_GET['cart_type'] = 'todayshop';

$cart = new Cart;

switch ($mode){
	case "modItem":
		// ace ī���� ��ǰ��������
		$Acecounter = new Acecounter();
		if ($Acecounter->goods_cart_mod($cart->item, $_POST['idxs'], $_POST['ea']) === true) {
			$aceScript = $Acecounter->scripts;
		}

		$cart->modCart($_POST[ea]);
		break;
	case "delItem":
		$cart->delCart($_GET[idx]);
		break;
	case "delItems":
		// ace ī���� ��ǰ��������
		$Acecounter = new Acecounter();
		if ($Acecounter->goods_cart_del($cart->item, $_POST['idxs']) === true) {
			$aceScript = $Acecounter->scripts;
		}

		$cart->delCart($_POST[idxs]);
		break;
	case "empty":
		// ace ī���� ��ٱ��Ϻ���
		$Acecounter = new Acecounter();
		if ($Acecounter->goods_cart_dels($cart->item) === true) {
			$aceScript = $Acecounter->scripts;
		}

		$cart->emptyCart();
		break;
}

// ��ٱ��� �׼� �� �̵�ó��
if (empty($mode) === false) {
	if ($aceScript != '') {
		echo $aceScript;
		exit('
		<script>
		window.onload = function() {
			location.replace("today_cart.php");
		}
		</script>
		');
	} else {
		header("location:today_cart.php");
	}
}

$cart->calcu();

### ���½�Ÿ�� ��� ����
if($_COOKIE['cc_inflow']=="openstyleOutlink"){
	$systemHeadTagStart .= "<script src='http://www.interpark.com/malls/openstyle/OpenStyleEntrTop.js'></script>";
	$tpl->assign('systemHeadTagStart',$systemHeadTagStart);
}

### ���̹� üũ�ƿ�
$naverCheckout="";
if($checkoutCfg['useYn']=='y'):
	require "../lib/naverCheckout.class.php";
	$NaverCheckout = Core::loader('NaverCheckout');
	$naverCheckout = $NaverCheckout->get_GoodsCartTag($cart->item);
endif;

### ���� iPay
$auctionIpayBtn="";
if($auctionIpayCfg['useYn']=='y') {
	if (is_array($cart->item) && empty($cart->item) === false) {
		$useIpay = true;
		foreach($cart->item as $item) {
			$tmpImg = explode('|',$item['img']);
			$thumbimg = $tmpImg[0];
			if (!$thumbimg || (!preg_match('/^http(s)?:\/\//',$thumbimg) && !file_exists('../data/goods/'.$thumbimg))) {
				$useIpay = false;
				break;
			}
		}

		if ($useIpay) {
			require "../lib/auctionIpay.class.php";
			$AuctionIpay = Core::loader('AuctionIpay');
			if ($data['runout']) $on=false;
			else $on=true;
			$auctionIpayBtn = $AuctionIpay->get_GoodsCartTag($cart->item);
		}
	}
}

### ��ٿ� ����
if($about_coupon->use && $_COOKIE['about_cp']){
	$tpl->assign('view_aboutdc', 1);
	$tpl->assign('about_coupon', (int) $cart->tot_about_dc_price);
}

// �����̼� ��� ����
$use_todayshop_cart = ($todayShop->cfg['useTodayShop'] == 'y') ? 'y' : 'n';

// ���� ����
$subscribe = unserialize(stripslashes($todayShop->cfg['subscribe']));
$interest = unserialize(stripslashes($todayShop->cfg['interest']));

$tpl->assign('cart',$cart);
$tpl->assign('use_todayshop_cart',$use_todayshop_cart);
$tpl->assign('naverCheckout',$naverCheckout);
$tpl->assign('auctionIpayBtn',$auctionIpayBtn);
$tpl->print_('tpl');

?>
