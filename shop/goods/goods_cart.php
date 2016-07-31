<?

### 변수할당
$orderitem_mode = "cart";
$mode = isset($_REQUEST['mode']) ? $_REQUEST['mode'] : '';

include "../_header.php";
if(!$set['emoney']) include dirname(__FILE__) . "/../conf/config.pay.php";
include "../lib/cart.class.php";
setcookie('gd_isDirect','',time() - 3600,'/');

require "../lib/load.class.php";
@include "../conf/naverCheckout.cfg.php";
@include "../conf/auctionIpay.cfg.php";

if($_GET['cr']) {
	$cartReminder = Core::loader('CartReminder');
	$cartReminder->setCartReminderVisit($_GET['cr']);
}

$cart = new Cart(null,array("chkRunoutDel"=>true ,"chkKeepPeriod"=>true ) );
switch ($mode){
	case "addItem":
		if ($_POST['preview'] == 'y') {
			chkOpenYn($_POST[goodsno],"D","parentClose");
		}
		else{
			chkOpenYn($_POST[goodsno],"D",-2);	//상품진열여부 체크
		}

		if(!$cart->chkMaxCount()){		//보관설정체크
			msg($cart->msg[maxCount],-1);
		}

		// 멀티옵션
		if(is_array($_POST[goodsno])){	//배코에서 넘어온 세트상품
			chkOpenYn($_POST[goodsno],"D",-2);	//진열여부 체크
			for($i=0;$i<sizeof($_POST[goodsno]);$i++){
				if($_POST[goodsno][$i]) {
					$cart->addCart($_POST[goodsno][$i],array_notnull($_POST[opt][$i]),$_POST[addopt][$i],$_POST[addopt_inputable][$i],$_POST[ea][$i],$_POST[goodsCoupon][$i]);
				}
			}
		}
		else{
			if ($_POST[multi_ea]) {
				$_keys = array_keys($_POST[multi_ea]);

				for ($i=0, $m=sizeof($_keys);$i<$m;$i++) {

					$_opt = $_POST[multi_opt][ $_keys[$i] ];
					$_ea = $_POST[multi_ea][ $_keys[$i] ];
					$_addopt = $_POST[multi_addopt][ $_keys[$i] ];
					$_addopt_inputable = $_POST[multi_addopt_inputable][ $_keys[$i] ];

					$cart->addCart($_POST[goodsno],$_opt,$_addopt,$_addopt_inputable,$_ea,$_POST[goodsCoupon]);
				}
			}
			else {
				$cart->addCart($_POST[goodsno],$_POST[opt],$_POST[addopt],$_POST[_addopt_inputable],$_POST[ea],$_POST[goodsCoupon]);
			}
		}

		// ace 카운터 상품추가
		$Acecounter = new Acecounter();
		if ($Acecounter->open_state()) {
			if ($_POST['multi_ea']) {
				$aceGoodsno = $aceEa = array();
				$_keys = array_keys($_POST['multi_ea']);
				for ($i=0, $m=sizeof($_keys);$i<$m;$i++) {
					array_push($aceGoodsno, $_POST['goodsno']);
					array_push($aceEa, $_POST['multi_ea'][ $_keys[$i] ]);
				}
			} else {
				$aceGoodsno = (array)$_POST['goodsno'];
				$aceEa = (array)$_POST['ea'];
			}

			if ($Acecounter->goods_cart_add($cart->item, $aceGoodsno, $aceEa) === true) {
				$aceScript = $Acecounter->scripts;
			}
		}
		break;
	case "modItem":
		$cart->modCart($_POST[ea]);

		// ace 카운터 상품수량수정
		$Acecounter = new Acecounter();
		if ($Acecounter->goods_cart_mod($cart->item, $_POST['idxs'], $_POST['ea']) === true) {
			$aceScript = $Acecounter->scripts;
		}
		break;
	case "delItem":
		$cart->delCart($_GET[idx]);
		break;
	case "delItems":
		// ace 카운터 상품개개삭제
		$Acecounter = new Acecounter();
		if ($Acecounter->goods_cart_del($cart->item, $_POST['idxs']) === true) {
			$aceScript = $Acecounter->scripts;
		}

		$cart->delCart($_POST[idxs]);
		break;
	case "empty":
		// ace 카운터 장바구니비우기
		$Acecounter = new Acecounter();
		if ($Acecounter->goods_cart_dels($cart->item) === true) {
			$aceScript = $Acecounter->scripts;
		}

		$cart->emptyCart();
		break;
	// 팝업에서 호출
	case "editOption":
		// ace 카운터 상품수량수정
		$Acecounter = new Acecounter();
		if ($Acecounter->goods_cart_mod($cart->item, (array)$_POST['idx'], (array)$_POST['ea']) === true) {
			$aceScript = $Acecounter->scripts;
		}

		$cart->editOption($_POST);
		if ($aceScript != '') {
			echo $aceScript;
			exit('
			<script>
			window.onload = function() {
				opener.location.reload();
				self.close();
			}
			</script>
			');
		} else {
			exit('
			<script>
			opener.location.reload();
			self.close();
			</script>
			');
		}
	break;
}

// 장바구니 액션 후 이동처리
if ($mode == 'addItem' && $_POST['preview'] == 'y') {
	if ($aceScript != '') {
		echo $aceScript;
		exit('
		<script>
		window.onload = function() {
			top.opener.location.href="goods_cart.php";
			top.self.close();
		}
		</script>
		');
	} else {
		exit('
		<script>
		top.opener.location.href="goods_cart.php";
		top.self.close();
		</script>
		');
	}
} else if (empty($mode) === false) {
	if ($aceScript != '') {
		echo $aceScript;
		exit('
		<script>
		window.onload = function() {
			location.replace("goods_cart.php?cart_type='.$_GET['cart_type'].'");
		}
		</script>
		');
	} else {
		header('location:goods_cart.php?cart_type='.$_GET['cart_type']);
	}
}

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

if($member){
 $cart->excep = $member['excep'];
 $cart->excate = $member['excate'];
 $cart->dc = $member[dc]."%";
}


$cart->calcu();

$orderitem_rowspan = get_items_rowspan($cart->item);

### 오픈스타일 헤더 노출
if($_COOKIE['cc_inflow']=="openstyleOutlink"){
	$systemHeadTagStart .= "<script src='http://www.interpark.com/malls/openstyle/OpenStyleEntrTop.js'></script>";
	$tpl->assign('systemHeadTagStart',$systemHeadTagStart);
}

### 네이버 체크아웃
$naverCheckout="";
if($checkoutCfg['useYn']=='y'):
	require "../lib/naverCheckout.class.php";
	$NaverCheckout = Core::loader('NaverCheckout');
	$naverCheckout = $NaverCheckout->get_GoodsCartTag($cart->item);
endif;

//페이코
if(is_file('../lib/payco.class.php')){
	$Payco = Core::loader('payco')->getButtonHtmlCode('CHECKOUT', false, 'goodsCart');
	if($Payco) $tpl->assign('Payco', $Payco);
}

### 옥션 iPay
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

### 어바웃 쿠폰
if($about_coupon->use && $_COOKIE['about_cp']){
	$tpl->assign('view_aboutdc', 1);
	$tpl->assign('about_coupon', (int) $cart->tot_about_dc_price);
}

###크리테오###
$criteo = new Criteo();
if($criteo->begin()) {
	foreach($cart->item as $item) {
		$criteo_cart[]=array(
			'goodsno'=>$item[goodsno],'price'=>$item[price],'ea'=>$item[ea]
		);
	}
	$criteo->get_cart($criteo_cart);
	$systemHeadTagEnd .= $criteo->scripts;
	$tpl->assign('systemHeadTagEnd',$systemHeadTagEnd);
}

##############



// 투데이샵 사용 여부
$use_todayshop_cart = ($todayShop->cfg['useTodayShop'] == 'y') ? 'y' : 'n';

$tpl->assign('orderitem_rowspan',$orderitem_rowspan);
$tpl->assign('cart',$cart);
$tpl->assign('use_todayshop_cart',$use_todayshop_cart);
$tpl->assign('naverCheckout',$naverCheckout);
$tpl->assign('auctionIpayBtn',$auctionIpayBtn);
$tpl->print_('tpl');

?>
