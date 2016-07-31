<?
require_once('../lib/todayshop_cache.class.php');
$cache = new todayshop_cache(-1);

include "../_header.php";
include "../conf/config.pay.php";
@include "../conf/egg.usafe.php";
@include "../conf/merchant.php";

if (!$sess) header("location:../member/login.php");

### 장바구니 비우기
if ($_COOKIE[gd_isDirect]) setcookie("gd_isDirect",'',time() - 3600,'/');
else setcookie("gd_cart",'',time() - 3600,'/');

/*
	2011-06-22 by x-ta-c
	DB 장바구니의 구매상태를 변경
*/
$_GET['cart_type'] = 'todayshop';
$cart = Core::loader('Cart', $_COOKIE[gd_isDirect]);
if (method_exists($cart, 'buy')) $cart->buy();	// 추가된 메서드 이므로 확인.
//	2011-06-22

$query = "
select * from
	".GD_ORDER." a
	left join ".GD_LIST_BANK." b on a.bankAccount=b.sno
where
	a.ordno='$_GET[ordno]'
";
$data = $db->fetch($query,1);

### 투데이샵 캐시 삭제
$table = GD_ORDER_ITEM;
$query = "
	select
	TG.tgsno from $table AS O
	INNER JOIN ".GD_TODAYSHOP_GOODS." AS TG
	ON O.goodsno = TG.goodsno
	where O.ordno='$_GET[ordno]'
";
$res = $db->query($query);
while($tmp = $db->fetch($res)) {
	$cache->remove($tmp['tgsno'],'indbpageinit');

	### 투데이샵 구매량 카운트
    $query = "
        SELECT

            IFNULL(SUM(OI.ea), 0) AS cnt

        FROM ".GD_ORDER." AS O
        INNER JOIN ".GD_ORDER_ITEM." AS OI
            ON O.ordno=OI.ordno
        INNER JOIN ".GD_TODAYSHOP_GOODS_MERGED." AS TG
            ON OI.goodsno = TG.goodsno

        WHERE
                O.step > 0
            AND O.step2 < 40
            AND TG.tgsno='".$tmp['tgsno']."'

    ";

    $_res = $db->query($query);

    while ($_tmp = $db->fetch($_res)) {

        $query = "
        UPDATE
            ".GD_TODAYSHOP_GOODS_MERGED."        AS TGM
            INNER JOIN ".GD_TODAYSHOP_GOODS."    AS TG    ON TGM.goodsno = TG.goodsno
        SET
            TGM.buyercnt = ".$_tmp['cnt'].",
            TG.buyercnt = ".$_tmp['cnt']."
        WHERE
            TG.tgsno = ".$tmp['tgsno']."
        ";
        $db->query($query);

    }
}

### 현금영수증신청내역
if ($data['settlekind'] == 'a' && $set['receipt']['order'] == 'Y')
{
	$query = "select useopt from ".GD_CASHRECEIPT." where ordno='{$_GET['ordno']}' order by crno limit 1";
	list($data['cashreceipt_useopt']) = $db->fetch($query);
}

### 오버추어 구매전환
if ($cfg[overture_code]) $overture_cc = true;

### 링크프라이스를 구매전환
if($linkprice[chk] &&  $_COOKIE[LPINFO]){
	include "linkprice.php";
	$overture_cc = true;
}

if($overture_cc){
	$tpl->define('overture_cc','proc/overture_cc.htm');
	$tpl->assign('overture_code',$cfg[overture_code]);
	$tpl->assign('linkprice_code',$linkprice_code);
}

### 오픈스타일 헤더 노출
if($_COOKIE['cc_inflow']=="openstyleOutlink"){
	$systemHeadTagStart .= "<script src='http://www.interpark.com/malls/openstyle/OpenStyleEntrTop.js'></script>";
	$tpl->assign('systemHeadTagStart',$systemHeadTagStart);
}

### ace 카운터
if( $Acecounter->open_state() ){
	$table = GD_ORDER_ITEM;
	$res = $db->query("select goodsno,ea,price,goodsnm from $table where ordno='$_GET[ordno]'");
	while($tmp = $db->fetch($res)) $item[] = $tmp;
	$Acecounter->order_end($item, $_GET[ordno]);
	if($Acecounter->scripts){
		$systemHeadTagEnd .= $Acecounter->scripts;
		$tpl->assign('systemHeadTagEnd',$systemHeadTagEnd);
	}
}

if($_COOKIE[nv_pchs]){ // 네이버 지식쇼핑 구매전환율
	$query = "select ea, price from ".GD_ORDER_ITEM." where ordno='".$_GET[ordno]."'";
	$res = $db->query($query);
	while($row = $db->fetch($res)){
		$naverGCnt += $row[ea];
		$naverGPay += $row[price]*$row[ea];
	}
	$naverRoi = '<div id="nv_price" style="display:none" value="'.$naverGCnt.','.$naverGPay.'"></div>';
	if($_SERVER[HTTPS] == 'on')  $naverRoi .=  '<script language=JavaScript src="https://shoppings.naver.com/CPC/purchase_analysis.js"></script>';
	else  $naverRoi .= '<script language=JavaScript src="http://shopping.naver.com/CPC/purchase_analysis.js"></script>';
}

### 어바웃 쿠폰
if ($data['about_coupon_flag'] == '1') {
	$tpl->assign('about_coupon', (int) $data['about_dc_sum']);
}

$tpl->assign($data);
$tpl->print_('tpl');

### 옥션 오픈쇼핑구매전환율
if($_COOKIE['aos_clickid']){
	 unset($i,$tmp);
	$aos_url = "http://openshopping.auction.co.kr/ordercomp.aspx";
	$query = "select goodsno,ea, price from ".GD_ORDER_ITEM." where ordno='".$_GET[ordno]."'";
	$res = $db->query($query);
	while($row = $db->fetch($res)){
		if($row[ea]){
			for($i=0;$i< $row['ea'];$i++){
				$tmp['goodsno'][] = $row['goodsno'];
				$tmp['price'][] = $row['price'];
			}
		}
	}

	switch ( $data['settlekind'] )
	{
		case "c" : $pay_type="CARD";
		break;
		case "h" : $pay_type="MOBI";
		break;
		case "p" : $pay_type="PONT";
		break;
		default : $pay_type="CASH";
		break;
	}
	$aos_url.="?clickid=".$_COOKIE['aos_clickid']."&mode=".@implode(',',$tmp[goodsno])."&cost=".@implode(',',$tmp[price])."&pay_type=".$pay_type;
	$ret = @readurl($aos_url);
}

// 네이버 지식쇼핑 구매전환율 스크립트 출력
echo($naverRoi);
?>
