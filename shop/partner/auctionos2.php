<?
include "../../../../lib/library.php";
@include "../../../../conf/config.pay.php";
include "../../../../conf/config.php";
@include "../../../../conf/auctionos.php";
@include "../../../../conf/fieldset.php";
@include "../../../../conf/coupon.php";
$aboutcoupon = $config->load('aboutcoupon');

function eSpecialTag($str){
	$str = strip_tags($str);
	$tmp = "\" ' < > \ |";
	$arr = explode(' ',$tmp);
	$str = str_replace($arr,'',$str);
	return $str;
}

### 기본 회원 할인율
if($joinset[grp] != ''){
	$memberdc = $db->fetch("select dc,excep,excate from ".GD_MEMBER_GRP." where level='".$joinset[grp]."' limit 1");
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

$delimiter = "<!>";

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
	$w=$db->fetch($res2);

	### 즉석할인쿠폰
	$coupon = 0;
	list($v[coupon],$v[coupon_emoney]) = getCouponInfo($v[goodsno],$v[price]);
	$v[reserve] += $v[coupon_emoney];
	if($v[coupon])$coupon = getDcprice($v[price],$v[coupon]);

	### 회원할인
	$dcprice = 0;
	if (is_array($memberdc) === true) {
		$mdc_exc = chk_memberdc_exc($memberdc,$v['goodsno']); // 회원할인 제외상품 체크
		if($mdc_exc === false)$dcprice = getDcprice($v['price'],$memberdc['dc'].'%');
	}

	### 어바웃 쿠폰 적용여부 확인
	$about_dc_price = 0;
	if ( $aboutcoupon['use_aboutcoupon'] == 'Y' && $aboutcoupon['use_test']=='N' ) {
		$about_dc_price = getDcprice($v[price], '8%');
	}

	### 쿠폰 회원할인 중복 할인 체크
	if($coupon>0 && $dcprice>0){
		if($cfgCoupon['range'] == 2)$dcprice=0;
		if($cfgCoupon['range'] == 1)$coupon=0;
	}

	### 노출 가격
	$coupon += 0;
	$dcprice += 0;
	$price = $v[price] - $coupon - $dcprice - $about_dc_price;

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
	if($tmp[type] =="후불" || ($tmp['free'] && $tmp['price'])) $deli = -1;
	else{
		$deli = $tmp['price']+0;
	}

	$jj++;

	if($catnm[substr($w[category],0,3)]){
		echo($v[goodsno].$delimiter); 	// 1 쇼핑몰상품ID
		if ($tt != "1") {	// 전체 이면
			echo("C".$delimiter);
		} else {
			echo("U".$delimiter);
		}
		echo($v[goodsnm].$delimiter);		// 3 상품명
		echo($price.$delimiter);			// 4 가격
		echo($url.'/goods/goods_view.php?inflow=auctionos&goodsno='.$v[goodsno].$delimiter);	// 5 상세URL
		echo($img_url.$delimiter);	// 5 이미지URL
		for ($i=1;$i<=4;$i++){
			if($i*3 <= strlen($w[category]))echo(substr($w[category],0,$i*3));
			echo($delimiter);
		}
		for ($i=1;$i<=4;$i++){
			if($i*3 <= strlen($w[category]))echo(eSpecialTag($catnm[substr($w[category],0,$i*3)]));
			echo($delimiter);
		}
		echo( strip_tags($v[goodscd]).$delimiter);	// 모델명
		echo( strip_tags($v[brandnm]).$delimiter);	// 브랜드
		echo( strip_tags($v[maker]).$delimiter);		// 메이커
		echo( strip_tags($v[origin]).$delimiter);		// 원산지
		echo( substr($v[regdt],0,10).$delimiter);		// 상품등록일자
		echo( $deli.$delimiter);						// 배송비
		echo( strip_tags($event).$delimiter);			// 이벤트
		echo( ($coupon+$about_dc_price));
		echo($delimiter);									// 쿠폰
		echo(trim($partner[nv_pcard]).$delimiter);		// 23. 무이자
		echo($v[reserve].$delimiter);						// 24. 적립금
		echo($delimiter);									// 25. 이미지 변경유무 		추후 수정필요
		echo($delimiter);									// 26. 물품특성정보 		추후 수정필요
		echo(round($goodstot/$tot*100).$delimiter);		// 27. 상점내 매출비율
		echo("\r\n");
	}

	flush();
	$num++;

}
?>
