<?
include "../../../../lib/library.php";
@include "../../../../conf/config.pay.php";
include "../../../../conf/config.php";
@include "../../../../conf/auctionos.php";
@include "../../../../conf/fieldset.php";

function eSpecialTag($str){
	$str = strip_tags($str);
	$tmp = "\" ' < > \ |";
	$arr = explode(' ',$tmp);
	$str = str_replace($arr,'',$str);
	return $str;
}

### 기본 회원 할인율
if($joinset[grp] != ''){
	list($mdc) = $db->fetch("select dc from gd_member_grp where level='".$joinset[grp]."' limit 1");
}

$url = "http://".$_SERVER['HTTP_HOST'].$cfg[rootDir];

### 카테고리명 배열
$query = "select * from ".GD_CATEGORY."";
$res = $db->query($query);
while ($data=$db->fetch($res)) $catnm[$data[category]] = $data[catnm];

if($tt != '1'){
	### 일주일간 총판매 상품금액
	$onemonth = date("Y-m-d h:i:s",(time()-7*24*60*60));
	$query = "select sum(price * ea) from ".GD_ORDER_ITEM." a left join ".GD_ORDER." b on a.ordno=b.ordno where istep < '40' and b.cdt >= '$onemonth'";
	list($tot) = $db->fetch($query);
	if(!$tot)$tot = 1;
}

### 상품 데이타
$query = "
select * from
		".GD_GOODS." a
		left join ".GD_GOODS_BRAND." d on a.brandno=d.sno
";
$where = array();
$where[] = "a.open=1";
$where[] = "a.runout=0";
$yesterday = date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y")));
$where[] = "date_format(a.regdt,'%Y-%m-%d') >= '$yesterday'";

if ($where) $where = " where ".implode(" and ",$where);
$query .= $where;

$res = $db->query($query);

header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: text/plain; charset=euc-kr");

$goodsModel = Clib_Application::getModelClass('goods');

while ($v=$db->fetch($res)){

	// 판매 중지(기간 외 포함)인 경우 제외
	if (! $goodsModel->setData($v)->canSales()) continue;

	$query ="select price,reserve from ".GD_GOODS_OPTION." where goodsno='$v[goodsno]' and link and go_is_deleted <> '1' and go_is_display = '1'  limit 1";
	list($v[price],$v[reserve]) = $db->fetch($query);

	### 상품명에 머릿말 조합
	if($partner['goodshead'])$v[goodsnm] = str_replace(array('{_maker}','{_brand}'),array($v[maker],$v[brandnm]),$partner['goodshead']).$v['goodsnm'];
	$v['goodsnm'] = strip_tags($v['goodsnm']);
	$v['goodsnm'] = strcut(eSpecialTag($v['goodsnm']),255);
	$v['goodsnm'] = str_replace('.','',$v['goodsnm']);

	$query = "select ".getCategoryLinkQuery('category', null, 'max')." from ".GD_GOODS_LINK." where goodsno='$v[goodsno]' limit 1";
	$res2 = $db->query($query);
	$jj=0;

	list($v[img]) = explode("|",$v[img_m]);

	if(preg_match('/http:\/\//',$v[img]))$img_url = $v[img];
	else $img_url = $url.'/data/goods/'.$v[img];

	if(date('Y-m-d',time()) ==  date('Y-m-d',@filectime ( '../data/goods/'.$v[img]))) $modimg = 'Y';
	else $modimg = 'N';

	if($tt != '1'){
		###이벤트
		$date = date("Ymd");
		$query = "select z.subject from
								".GD_EVENT." z left join ".GD_GOODS_DISPLAY." a on z.sno=substring(a.mode,2) and substring(a.mode,1,1) = 'e'
								left join ".GD_GOODS." b on a.goodsno=b.goodsno
								left join ".GD_GOODS_OPTION." c on a.goodsno=c.goodsno and go_is_deleted <> '1' and go_is_display = '1'
								where link and a.goodsno='$row[goodsno]' and z.sdate <= '$date' and z.edate >= '$date' limit 1";
		list($event) = $db->fetch($query);

		### 일주일간 이상품의 판매 금액
		$query = "select sum(a.price * a.ea) from ".GD_ORDER_ITEM." a left join ".GD_ORDER." b on a.ordno=b.ordno where istep < '40' and b.cdt >= '$onemonth' and a.goodsno='".$v['goodsno']."'";
		list($goodstot) = $db->fetch($query);
	}
	while ($w=$db->fetch($res2)){

		### 즉석할인쿠폰
		$coupon = 0;
		list($v[coupon],$v[coupon_emoney]) = getCouponInfo($v[goodsno],$v[price]);
		$v[reserve] += $v[coupon_emoney];
		if($v[coupon])$coupon = getDcprice($v[price],$v[coupon]);

		### 회원할인
		$dcprice = 0;
		if($mdc)$dcprice = getDcprice($v[price],$mdc.'%');

		### 노출 가격
		$coupon += 0;
		$dcprice += 0;
		$price = $v[price] - $coupon - $dcprice;

		### 배송료
		$param = array(
			'mode' => '1',
			'deliPoli' => 0,
			'price' => $price,
			'goodsno' => $v[goodsno],
			'goods_delivery' => $v[goods_delivery],
			'delivery_type' => $v[delivery_type]
		);
		$tmp = getDeliveryMode($param);
		$deli=0;
		if($tmp['free'] && $tmp['price']) $deli = "0/".$tmp['free']."/".$tmp['price'];
		else $deli = $tmp['price']+0;

		$jj++;

		if($catnm[substr($w[category],0,3)]){
			echo('[[_BEGIN]]'.chr(10));
			echo('[[PRODID]]'.'C'.$w[category].'G'.$v[goodsno].chr(10));
			echo('[[PRNAME]]'.$v[goodsnm].chr(10));
			echo('[[_PRICE]]'.$price.chr(10));
			if($tt != "1"){
				echo('[[PRDURL]]'.$url.'/goods/goods_view.php?inflow=auctionos&goodsno='.$v[goodsno].chr(10));
				echo('[[IMGURL]]'.$img_url.chr(10));
				for ($i=1;$i<=4;$i++){
					echo('[[CATE_'.$i.']]');
					if($i*3 <= strlen($w[category]))echo(eSpecialTag($catnm[substr($w[category],0,$i*3)]));
					echo(chr(10));
				}
				echo('[[_MODEL]]'.strip_tags($v[goodscd]).chr(10));
				echo('[[_BRAND]]'.strip_tags($v[brandnm]).chr(10));
				echo('[[_MAKER]]'.strip_tags($v[maker]).chr(10));
				echo('[[ORIGIN]]'.strip_tags($v[origin]).chr(10));
				echo('[[PRDATE]]'.substr($v[regdt],0,10).chr(10));
				echo('[[DELIVR]]'.$deli.chr(10));
				echo('[[_EVENT]]'.strip_tags($event).chr(10));
				echo('[[COUPON]]');
				if($v[coupon])echo($v[coupon].' 할인쿠폰 지급');
				echo(chr(10));
				echo('[[PRCARD]]'.trim($partner[nv_pcard]).chr(10));
				echo('[[_POINT]]'.$v[reserve].chr(10));
				echo('[[MODIMG]]'.$modimg.chr(10));
				echo('[[SRATIO]]'.round($goodstot/$tot*100).chr(10));
			}
			echo('[[___END]]'.chr(10));
		}

		flush();
		$num++;
	}
}
?>
