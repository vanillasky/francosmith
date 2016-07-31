<?php
set_time_limit(0);

include_once "../lib/library.php";
include("../conf/config.php");
include("../conf/config.pay.php");
@include_once("../conf/partner.php");
@include_once("../conf/coupon.php");
@include dirname(__FILE__).'/../conf/config.mobileShop.php';

### 기본 회원 할인율
@include_once "../conf/fieldset.php";
if($joinset[grp] != ''){
	$memberdc = $db->fetch("select dc,excep,excate from ".GD_MEMBER_GRP." where level='".$joinset[grp]."' limit 1");
}

header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: text/plain; charset=euc-kr");

// 카테고리정보 모두 가지고 오기
$query = "select catnm,category from ".GD_CATEGORY;
$result = $db->query($query);
$ar_category=array();
while($row = $db->fetch($result))
{
	$ar_category[$row['category']]=$row['catnm'];
}
$query = "select sno,brandnm from ".GD_GOODS_BRAND;
$result = $db->query($query);
$ar_brand=array();
while($row = $db->fetch($result))
{
	$ar_brand[$row['sno']]=$row['brandnm'];
}

$query = "select
e.goodsno , e.category ,a.totstock as stock,
a.goodsnm,a.img_l,a.img_m,a.brandno,a.origin,a.maker,a.launchdt,a.delivery_type,a.goods_delivery,a.use_emoney,a.usestock,a.sales_range_start, a.sales_range_end,
b.price,b.reserve,d.brandnm,grv.review_count,a.extra_info,a.naver_event
from
".GD_GOODS." a left join ".GD_GOODS_BRAND." d on a.brandno=d.sno
left join (select _grv.goodsno, count(_grv.sno) as review_count from ".GD_GOODS_REVIEW." as _grv group by _grv.goodsno) as grv on a.goodsno=grv.goodsno,
".GD_GOODS_OPTION." b,
(select goodsno, ".getCategoryLinkQuery('category', null, 'max')." from ".GD_GOODS_LINK." c group by c.goodsno) e
where a.goodsno=b.goodsno
	and a.goodsno=e.goodsno
	and b.link and go_is_deleted <> '1' and go_is_display = '1'
	and a.open=1 and a.runout=0
";
$result = $db->query($query);

$goodsModel = Clib_Application::getModelClass('goods');

while($row = $db->fetch($result,1))
{
	// 판매 중지(기간 외 포함)인 경우 제외
	if (! $goodsModel->setData($row)->canSales()) continue;

	if($row['usestock']=='o' && $row['stock']==0)
	{
		continue;
	}

	if(!$row['img_l'] || $row['img_l'] == ''){
		if(!$row['img_m'] || $row['img_m'] == ''){
			continue;
		}else{
			$img_name = $row['img_m'];
		}
	}else{
		$img_name = $row['img_l'];
	}

	$ar_data=array();
	$ar_data['begin']='';
	$ar_data['mapid']=$row['goodsno'];
	 if($partner['goodshead']){
		$ar_data['pname']=str_replace(array('{_maker}','{_brand}'),array($row['maker'],$row['brandnm']),$partner['goodshead']).strip_tags($row['goodsnm']);
	 }else{
		$ar_data['pname']=strip_tags($row['goodsnm']);
	 }
	$ar_data['price']=$row['price'];

	if($partner['unmemberdc'] == 'N'){	// 회원할인적용
		$dcprice = 0;
		if (is_array($memberdc) === true) {
			$mdc_exc = chk_memberdc_exc($memberdc,$row['goodsno']); // 회원할인 제외상품 체크
			if($mdc_exc === false)$dcprice = getDcprice($row['price'],$memberdc['dc'].'%');
		}
	}

	### 즉석할인쿠폰
	$coupon = 0;
	if($cfgCoupon['use_yn'] && $partner['uncoupon'] == 'N'){
		list($row[coupon],$row[coupon_emoney]) = getCouponInfo($row[goodsno],$row[price]);
		$row[reserve] += $row[coupon_emoney];
		if($row[coupon])$coupon = getDcprice($row[price],$row[coupon]);
	}

	### 쿠폰 회원할인 중복 할인 체크
	if($coupon>0 && $dcprice>0){
		if($cfgCoupon['range'] == 2)$dcprice=0;
		if($cfgCoupon['range'] == 1)$coupon=0;
	}

	### 노출 가격
	$coupon += 0;
	$dcprice += 0;
	$ar_data['price'] = $ar_data['price'] - $coupon - $dcprice;

	$ar_data['pgurl']="http://".$_SERVER['HTTP_HOST'].$cfg[rootDir]."/goods/goods_view.php?goodsno={$row['goodsno']}&inflow=naver";

	$tmp = explode("|",$img_name);
	while ($img = array_shift($tmp)) {

		if (preg_match('/^http(s)?:\/\//',$img)) {
			$ar_data['igurl']=$img;
			break;
		}
		elseif ($img != '') {
			$ar_data['igurl']="http://".$_SERVER['HTTP_HOST'].$cfg[rootDir]."/data/goods/".$img;
			break;
		}
	}

	$length = strlen($row['category'])/3;
	for($i=1;$i<=4;$i++)
	{
		$tmp=substr($row['category'],0,$i*3);
		$ar_data['cate'.$i]=($i<=$length) ? strip_tags($ar_category[$tmp]) : '';
		$ar_data['caid'.$i]=($i<=$length) ? $tmp : '';
	}
	if($row['brandno']) $ar_data['brand']=$ar_brand[$row['brandno']];
	if($row['maker']) $ar_data['maker']=$row['maker'];
	if($row['origin']) $ar_data['origi']=$row['origin'];

	switch($row['delivery_type']) {
		case "0":
			if ($set['delivery']['free'] <= $row['price']) {
				$ar_data['deliv'] = '0';
			}
			else {
				if ($set['delivery']['deliveryType'] == '후불') {
					$ar_data['deliv'] = '-1';
				}
				else {
					$ar_data['deliv'] = $set['delivery']['default'] ? $set['delivery']['default'] : '0';
				}
			}
			break;
		case "1":
			$ar_data['deliv']=0;
			break;
		case "2": case "4": case "5":
			$ar_data['deliv'] = $row['goods_delivery'] ? $row['goods_delivery'] : '0';
			break;
		case "3":
			$ar_data['deliv'] = -1;
			break;
	}

	// 이벤트
	$event = '';
	if ($partner['naver_event_common'] === 'Y' && empty($partner['eventCommonText']) === false) {	// 공통 문구
		$event = $partner['eventCommonText'];
	}

	if ($partner['naver_event_goods'] === 'Y' && empty($row['naver_event']) === false) {	// 상품별 문구
		if (empty($event) === false) $event .= ' , ';
		$event .= $row['naver_event'];
	}

	$ar_data['event'] = strip_tags($event);

	if($coupon) $ar_data['coupo'] = $coupon;

	if($partner[nv_pcard]) $ar_data['pcard'] = strip_tags($partner[nv_pcard]);

	if($row['use_emoney']=='0')
	{
		if( !$set['emoney']['chk_goods_emoney'] ){
			if( $set['emoney']['goods_emoney'] ) {
				$dc=$set['emoney']['goods_emoney']."%";
				$tmp_price = $row['price'];
				if( $set['emoney']['cut'] ) $po = pow(10,$set['emoney']['cut']);
				else $po = 100;
				$tmp_price = (substr($dc,-1)=="%") ? $tmp_price * substr($dc,0,-1) / 100 : $dc;
				$ar_data['point'] =  floor($tmp_price / $po) * $po;

			}
		}else{
			$ar_data['point']	= $set['emoney']['goods_emoney'];
		}
	}
	else
	{
		$ar_data['point']=$row['reserve'];
	}

	$ar_data['revct'] = (int)$row['review_count'];

	if (isset($cfgMobileShop) && $cfgMobileShop['useMobileShop'] == '1') {
		$ar_data['mourl'] = 'http://'.$_SERVER['HTTP_HOST'].'/m/goods/view.php?goodsno='.$row['goodsno'].'&inflow=naver';
	}
	else {
		$ar_data['mourl'] = '';
	}

	$extra_info = gd_json_decode(stripslashes($row['extra_info']));
	$dlvDesc = '';
	$addPrice = '';
	$isDlv = '';
	$isAddPrice = '';
	$isCoupon = '';
	if(is_array($extra_info)){
		foreach($extra_info as $key=>$val) {
			if($val['title'] == '배송 · 설치비용'){
				$dlvDesc = $val['desc'];
			}
			if($val['title'] == '추가설치비용'){
				$addPrice = $val['desc'];
			}
		}
	}
	if($dlvDesc) {
		$isDlv = 'Y';
	}
	if($addPrice) {
		$isAddPrice = 'Y';
	}
	$couponData = null;
	$couponData = getCouponInfo($row['goodsno'], $row['price'], 'v');
	foreach($couponData as $key=>$val) {
		if($val['price']>0) {
			$isCoupon = 'Y';
		}
	}

	$ar_data['pcpdn'] = $isCoupon;
	$ar_data['dlvga'] = $isDlv;
	$ar_data['dlvdt'] = $dlvDesc;
	$ar_data['insco'] = $isAddPrice;

	$ar_data['ftend']='';
	foreach($ar_data as $key=>$value)
	{
		echo '<<<'.$key.'>>>'.$value."\n";
	}

}


?>
