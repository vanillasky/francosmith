<?php
/*******************************************************************************************
 *
 *******************************************************************************************/

include "../lib/library.php";
include "../lib/load.class.php";
@include "../conf/naverChecout.cfg.php";
require "../lib/naverCheckout.class.php";
require "../conf/config.php";
class ItemStack {
    var $id;
    var $name;
    var $uprice;
    var $image;
    var $thumb;
    var $url;

    function ItemStack($_id, $_name, $_uprice, $_image, $_thumb, $_url) {
	$this->id = $_id;
	$this->name = $_name;
	$this->uprice = $_uprice;
	$this->image = $_image;
	$this->thumb = $_thumb;
	$this->url = $_url;
    }

    function makeQueryString() {
	$ret .= 'ITEM_ID=' . urlencode($this->id);
	$ret .= '&ITEM_NAME=' . urlencode($this->name);
	$ret .= '&ITEM_UPRICE=' . $this->uprice;
	$ret .= '&ITEM_IMAGE=' . urlencode($this->image);
	$ret .= '&ITEM_THUMB=' . urlencode($this->thumb);
	$ret .= '&ITEM_URL=' . urlencode($this->url);
	$ret .= '&EC_MALL_PID=' . urlencode($this->id);
	return $ret;
    }
};

function get_img($img,$rootDir){
    $tmp = explode('|',$img);
    $img = $tmp[0];
    if(!$img)return false;
    if(!preg_match('/http:\/\//',$img)){
	$img1 = "/data/goods/".$img;
	$img2 = dirname(__FILE__)."/../data/goods/".$img;
	if(file_exists($img2)) {
		$imgUrl = "http://".$_SERVER['HTTP_HOST'].$rootDir.$img1;
	}else{
		return false;
	}
	return $imgUrl;
    }else{
	return $img;
    }
}

$NaverCheckout = Core::loader('NaverCheckout');
if(!$NaverCheckout->check_use()) $errmsg="네이버체크아웃을 사용을 중단 하였습니다.";

$tblGoods = GD_GOODS;
$tblGoodsOption = GD_GOODS_OPTION;
$optEscape = array('/',':');
$addprice = 0;
$totalMoney = 0;

$ea = $_POST['ea'];
$goodsno = $_POST['goodsno'];

$optnm = array();
$query = "select a.goodsno,a.goodsnm,a.img_i,a.img_s,a.img_m,a.img_l,b.price from $tblGoods as a left join $tblGoodsOption as b on a.goodsno=b.goodsno and b.link and go_is_deleted <> '1' and go_is_display = '1' where a.goodsno='$goodsno' limit 1";
$data = $db->fetch($query);
if(!$NaverCheckout->check_banWords($data['goodsnm']))$errmsg="체크아웃 서비스에 사용가능한 상품이 아닙니다.";
if(!$NaverCheckout->check_exceptions($data['goodsno']))$errmsg="체크아웃 서비스에 사용가능한 상품이 아닙니다.";
if(!$data['goodsno'])$errmsg="올바른 접근이 아닙니다.";

$queryString = 'SHOP_ID='.urlencode($checkoutCfg['naverId']);
$queryString .= '&CERTI_KEY='.urlencode($checkoutCfg['connectId']);
$queryString .= '&RESERVE1=&RESERVE2=&RESERVE3=&RESERVE4=&RESERVE5=';

$uid = $data['goodsno'];
$name = strip_tags($data['goodsnm']);
$uprice = $data['price'];

$img_l = get_img($data['img_l'],$cfg['rootDir']);
$img_m = get_img($data['img_m'],$cfg['rootDir']);
$img_s = get_img($data['img_s'],$cfg['rootDir']);
$img_i = get_img($data['img_i'],$cfg['rootDir']);

if($img_i) $image = $img_m;
if($img_s) $image = $img_s;
if($img_m) $image = $img_m;
if($img_l) $image = $img_l;

if($img_l) $thumb = $img_l;
if($img_m) $thumb = $img_m;
if($img_i) $thumb = $img_m;
if($img_s) $thumb = $img_s;

$url = 'http://'.$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/goods/goods_view.php?inflow=naverCheckout&goodsno='.$data['goodsno'];
$item = new ItemStack($uid, $name, $uprice, $image, $thumb, $url);
$queryString .= '&'.$item->makeQueryString();

if($checkoutCfg['testYn']=='y'){
    $req_host = 'test-checkout.naver.com';
    $req_addr = 'ssl://test-checkout.naver.com';
}else{
    $req_host = 'checkout.naver.com';
    $req_addr = 'ssl://checkout.naver.com';
}

$req_url = 'POST /customer/api/CP949/wishlist.nhn HTTP/1.1'; // euc-kr
$req_port = 443;
$nc_sock = @fsockopen($req_addr, $req_port, $errno, $errstr);
if ($nc_sock) {
    fwrite($nc_sock, $req_url."\r\n" );
    fwrite($nc_sock, "Host: ".$req_host.":".$req_port."\r\n" );
    fwrite($nc_sock, "Content-type: application/x-www-form-urlencoded; charset=CP949\r\n");  // euc-kr
    fwrite($nc_sock, "Content-length: ".strlen($queryString)."\r\n");
    fwrite($nc_sock, "Accept: */*\r\n");
    fwrite($nc_sock, "\r\n");
    fwrite($nc_sock, $queryString."\r\n");
    fwrite($nc_sock, "\r\n");

    // get header
    while(!feof($nc_sock)){
	$header=fgets($nc_sock,4096);
	if($header=="\r\n"){
	    break;
	} else {
	    $headers .= $header;
	}
    }

	// get body
	$bodys = '';
	if (strpos($headers,'Transfer-Encoding: chunked') !== false) {
		while($line = fgets($nc_sock)) {
			$size = hexdec(trim($line));

			if($size == 0) break;

			$buffer = '';
			while(strlen($buffer) < $size + 2) {
				$buffer .= fread($nc_sock, $size + 2 - strlen($buffer));
			}

			$bodys .= substr($buffer, 0, strlen($buffer) - 2);
		}
	}
	else {
		while(!feof($nc_sock)){
			$bodys.=fgets($nc_sock);
		}
	}

    fclose($nc_sock);

    $resultCode = substr($headers,9,3);

    if ($resultCode == 200) {
	$itemId = $bodys;
    } else {
	msg('동시에 접속하는 이용자 수가 많거나 네트워크 상태가 불안정하여\n현재 체크아웃 서비스 접속이 불가합니다.\n이용에 불편을 드린 점 진심으로 사과드리며, 잠시 후 다시 접속해 주시기 바랍니다.');
	exit;
    }
}else {
    echo "$errstr ($errno)<br>\n";
    exit(-1);
}

//리턴받은 itemId로 주문서 page를 호출한다.
if(isset($_GET['isMobile']))
{
	if($checkoutCfg['testYn']=='y') $wishlistPopupUrl = "https://test-m.checkout.naver.com/mobile/customer/wishList.nhn";
	else $wishlistPopupUrl = "https://m.checkout.naver.com/mobile/customer/wishList.nhn";
}
else
{
	$wishlistPopupUrl = "https://".$req_host."/customer/wishlistPopup.nhn";
}

?>
<html>
<body>
<form name="frm" method="get" action="<?=$wishlistPopupUrl?>">
<input type="hidden" name="SHOP_ID" value="<?=$checkoutCfg['naverId']?>">
<input type="hidden" name="ITEM_ID" value="<?=$itemId?>">
</form>
</body>
<script>
<? if ($resultCode == 200) { ?>
<?php if(isset($_GET['isMobile'])){ ?>
document.frm.target = "_self";
document.frm.submit();
<?php }else{ ?>
document.frm.target = "naverCheckoutWish";
document.frm.submit();
parent.naverCheckoutWin.focus();
<?php } ?>
<? } ?>
</script>
</html>
