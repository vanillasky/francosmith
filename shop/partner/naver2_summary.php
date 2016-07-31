<?php
set_time_limit(0);
header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: text/plain; charset=euc-kr");

include("../dbconn.php");
include("../lib/lib.func.php");
@include_once("../conf/partner.php");
@include dirname(__FILE__).'/../conf/config.mobileShop.php';
include '../conf/config.pay.php';
@include_once '../conf/coupon.php';
@include_once '../conf/fieldset.php';

// 기본 회원 할인율
if ($joinset['grp'] != '') {
	$memberdc = $db->fetch('SELECT dc, excep, excate FROM '.GD_MEMBER_GRP.' WHERE level="'.$joinset['grp'].'" LIMIT 1');
}

$tmp = date("Y-m-d 00:00:00");
$db->query("delete from ".GD_GOODS_UPDATE_NAVER." where utime < '$tmp'");
$query = "select * from ".GD_GOODS_UPDATE_NAVER." order by no asc";
$result = $db->query($query);

$goodsModel = Clib_Application::getModelClass('goods');

while($row = $db->fetch($result,1))
{
	$query = "select a.goodsnm, b.price, a.maker, c.brandnm, a.sales_range_start, a.sales_range_end from ".GD_GOODS." as a left join ".GD_GOODS_OPTION." as b on a.goodsno=b.goodsno and go_is_deleted <> '1' and go_is_display = '1' left join ".GD_GOODS_BRAND." as c on a.brandno=c.sno where b.link=1 and a.goodsno='$row[mapid]'";
	$_row = $db->fetch($query);

	// 회원할인적용
	$dcprice = 0;
	if ($partner['unmemberdc'] === 'N') {
		if (is_array($memberdc) === true) {
			$mdc_exc = chk_memberdc_exc($memberdc, $row['mapid']); // 회원할인 제외상품 체크
			if ($mdc_exc === false) {
				$dcprice = getDcprice($_row['price'], $memberdc['dc'].'%');
			}
		}
	}

	// 즉석할인쿠폰
	$coupon = 0;
	if ($cfgCoupon['use_yn'] && $partner['uncoupon'] === 'N') {
		list($couponDiscount, $couponEmoney) = getCouponInfo($row['mapid'], $_row['price']);
		if ($couponDiscount) {
			$coupon = getDcprice($_row['price'], $couponDiscount);
		}
	}

	// 쿠폰 회원할인 중복 할인 체크
	if ($coupon > 0 && $dcprice > 0) {
		if ($cfgCoupon['range'] == 2) {
			$dcprice = 0;
		}
		if ($cfgCoupon['range'] == 1) {
			$coupon = 0;
		}
	}

	// 노출 가격
	$coupon += 0;
	$dcprice += 0;
	$_row['price'] = $_row['price'] - $coupon - $dcprice;

	if ($_row) extract($_row);

	// 판매 중지(기간 외 포함)인 경우 제외
	if (! $goodsModel->setData($_row)->canSales()) continue;

	if($partner['goodshead']){
		$goodsnm=str_replace(array('{_maker}','{_brand}'),array($maker,$brandnm),$partner['goodshead']).strip_tags($goodsnm);
	}else{
		$goodsnm=strip_tags($goodsnm);
	}

	// 이벤트
	if ($row['event'] != null) {
		$event = '';
		if ($partner['naver_event_common'] === 'Y' && empty($partner['eventCommonText']) === false) {	// 공통 문구
			$event = $partner['eventCommonText'];
		}

		if ($partner['naver_event_goods'] === 'Y' && empty($row['event']) === false) {	// 상품별 문구
			if (empty($event) === false) $event .= ' , ';
			$event .= $row['event'];
		}

		$row['event'] = strip_tags($event);
	}

	$mapid = $row['mapid'];
	$class = $row['class'];
	$utime = $row['utime'];

	unset($row['no']);
	unset($row['mapid']);
	unset($row['class']);
	unset($row['utime']);
	unset($row['pname']);
	unset($row['price']);

	echo "<<<begin>>>\n";
	echo '<<<mapid>>>'.$mapid."\n";
	if($class != 'D'){
		echo "<<<pname>>>".$goodsnm."\n";
		echo '<<<price>>>'.$price."\n";
		if (isset($cfgMobileShop) && $cfgMobileShop['useMobileShop'] == '1') {
			$row['mourl'] = 'http://'.$_SERVER['HTTP_HOST'].'/m/goods/view.php?goodsno='.$mapid.'&inflow=naver';
		}
		else {
			$row['mourl'] = '';
		}
	}
	foreach($row as $key=>$value)
	{
		if($key == 'pdate') continue;
		if(!is_null($value)) echo '<<<'.$key.'>>>'.$value."\n";
	}
	echo '<<<class>>>'.$class."\n";
	echo '<<<utime>>>'.$utime."\n";
	echo "<<<ftend>>>\n";
}

?>
