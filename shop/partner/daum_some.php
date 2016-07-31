<?php
require "../lib/library.php";
require "../lib/partner.class.php";
require "../conf/config.php";
include '../conf/config.pay.php';
@include "../conf/coupon.php";
@require "../conf/daumCpc.cfg.php";

if ($daumCpc['useYN']!= 'Y') exit;

// �����Ͽ� DB ����
daum_goods_diff_check();

// ���� �Ǹ� �Ⱓ�� ����� ��ǰ
$query = "select goodsno,from_unixtime(sales_range_end) sales_range_end from ".GD_GOODS." where sales_range_end <> 0 and sales_range_end < unix_timestamp(now())"; 
$salesEnd = $db->query($query);
while ($end = $db->fetch($salesEnd,1)) {
	// ���� �ð��� ���� ��¥ �����̰� �ٷ��� �����ð� ������ ��ǰ�� ������ ����
	if ($end['sales_range_end'] > date("Y-m-d") && $end['sales_range_end'] > date("Y-m-d H:i:s",strtotime("-2 hours"))) {
		daum_goods_runout($end['goodsno']);
	}
}

$query = "select no,class,mapid,date_format(utime,'%Y%m%d%H%i%s') utime,pname,price,pgurl,igurl,cate1,cate2,cate3,cate4,caid1,caid2,caid3,caid4,model,brand,maker,deliv,event,point,adult,discount from ".GD_GOODS_UPDATE_DAUM;
$result = $db->query($query);

$goodsModel = Clib_Application::getModelClass('goods');
$discountModel = Clib_Application::getModelClass('Goods_Discount');
$partner = new Partner();

while($row = $db->fetch($result,1))
{
	$query = "select a.goodsno,a.goodsnm,a.maker,a.img_l,a.model_name,a.delivery_type,a.goods_delivery,a.use_emoney,a.goods_reserve,a.use_only_adult,a.naver_event,b.price,c.brandnm,d.category from ".GD_GOODS." as a left join ".GD_GOODS_OPTION." as b on a.goodsno=b.goodsno and go_is_deleted <> '1' and go_is_display = '1' left join ".GD_GOODS_BRAND." as c on a.brandno=c.sno left join ".GD_GOODS_LINK." as d on a.goodsno=d.goodsno where b.link=1 and a.goodsno='$row[mapid]'";
	$_row = $db->fetch($query,1);

	// �ǸźҰ� -> �ǸŰ��� ��ǰ
	if ($row['price'] == null) {
		foreach ($_row as $k => $v) {
			if ($k === 'goodsnm') {
				$row['pname'] = $v;
			}
			else if ($k === 'category') {
				for ($i=1;$i<=4;$i++) {
					$tmp_nm="";
					$tmp_code = substr($v,0,3*$i);
					if (strlen($tmp_code)==$i*3) {
						list($tmp_nm) = $db->fetch("select catnm from ".GD_CATEGORY." where category='$tmp_code'");
						$row['caid'.$i]=strip_tags($tmp_code);
						$row['cate'.$i]=strip_tags($tmp_nm);
					}
				}
			}
			else if ($k === 'brandnm') {
				$row['brand'] = $v;
			}
			else if ($k === 'img_l') {
				if (preg_match('/^http(s)?:\/\//', $v)) {
					$row['igurl'] = $v;
				}
				else {
					$row['igurl'] = 'http://'.$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/data/goods/'.$v;
				}
			}
			else if ($k === 'delivery_type') {
				switch ($v) {
				case "0":
					if ($set['delivery']['free'] <= $_row['price']) $row['deliv']=0;
					else $row['deliv'] = $set['delivery']['default'];

					if ($set['delivery']['deliveryType'] != "�ĺ�") {
						if ($_row['price'] >= $set['delivery']['free'])
							$row['deliv'] = 0;
						else
							$row['deliv'] = $set['delivery']['default'];
					}
					else
						$row['deliv'] = -1;
					break;
				case "1":
					$row['deliv'] = 0;
					break;
				case "3":
					$row['deliv'] = -1;
					break;
				case "4":
					$row['deliv'] = $_row['goods_delivery'];
					break;
				case "5":
					$row['deliv'] = $_row['goods_delivery'];
					break;
				}
			}
			else if ($k === 'naver_event') {
				$row['event'] = $v;
			}
			else if ($k === 'use_emoney' && $v === '0') {
				if (!$set['emoney']['chk_goods_emoney'] && $set['emoney']['goods_emoney']) {
					$row['point'] = getDcprice($row['price'],$set['emoney']['goods_emoney'].'%');
				}
				else {
					$row['point'] = $set['emoney']['goods_emoney'];
				}
			}
			else if ($k === 'use_emoney' && $v === '1') {
				$row['point'] = $_row['goods_reserve'];
			}
			else if ($k === 'use_only_adult') {
				$row['adult'] = $v;
			}
			else if ($k === 'model_name') {
				$row['model'] = $v;
			}
			else {
				$row[$k] = $v;
			}
		}
	}

	// ��ǰ�� ����
	$goodsDiscount = 0;
	$goodsDiscount = $discountModel->getDiscountAmountSearch($_row,0);

	// �Ｎ��������
	list($row['coupon']) = getCouponInfo($row['mapid'],$row['price']);
	$coupon = 0;
	if ($row['coupon']) $coupon = getDcprice($row['price'],$row['coupon']);

	// ȸ������
	$dcprice = 0;
	$memberdc = $partner->getBasicDc();
	if (is_array($memberdc) === true) {
		$mdc_exc = chk_memberdc_exc($memberdc,$row['mapid']); // ȸ������ ���ܻ�ǰ üũ
		if($mdc_exc === false)$dcprice = getDcprice($row['price'],$memberdc['dc'].'%');
	}

	// ���� ȸ������ �ߺ� ���� üũ
	if ($coupon>0 && $dcprice>0) {
		if ($cfgCoupon['range'] == 2) $dcprice=0;
		if ($cfgCoupon['range'] == 1) {
			$coupon=0;
		}
	}

	// ��ǰ�� ���ΰ� ���� ���ο� ���� ���� ���
	$price = 0;
	if ($goodsDiscount && $cfgCoupon['double'] === '1') {	// ��ǰ�� ���ΰ� ���� �ߺ� ��� ����
		$price = $row['price'] - $coupon - $dcprice - $goodsDiscount;
	}
	else if ($goodsDiscount) {	// ��ǰ�� ���θ� ���� ��
		$price = $row['price'] - $dcprice - $goodsDiscount;
	}
	else if ($cfgCoupon['double'] === '1') {	// ��ǰ�� ���θ� ���� ��
		$price = $row['price'] - $dcprice - $coupon;
	}
	else {
		$price = $row['price'] - $dcprice;
	}

	// �� ī�װ� ����
	for ($i=1; $i<5; $i++) {
		if ($row['cate'.$i] == '') {
			unset($row['cate'.$i]);
			unset($row['caid'.$i]);
		}
	}

	// ��ǰ�� �Ӹ���
	$goodsnm = '';
	if ($daumCpc['goodshead']) {
		if ($row['maker'] || $row['brand']) {
			$goodsnm = str_replace(array('{_maker}','{_brand}'),array($row['maker'],$row['brand']),$daumCpc['goodshead']).$row['pname'];
		}
		else {
			$goodsnm = str_replace(array('{_maker}','{_brand}'),array($_row['maker'],$_row['brandnm']),$daumCpc['goodshead']).$row['pname'];
		}
	}
	else {
		$goodsnm = $row['pname'];
	}

	// �������� ��ǰ
	if ($row['adult'] === '1') {
		$row['adult'] = 'Y';
	}
	else
		unset($row['adult']);

	$mapid = $row['mapid'];
	$class = $row['class'];
	$utime = $row['utime'];
	$lprice = $row['price'];
	
	unset($row['no']);
	unset($row['mapid']);
	unset($row['coupon']);
	unset($row['price']);
	unset($row['utime']);
	unset($row['class']);
	unset($row['pname']);
	unset($row['discount']);
	unset($row['goods_delivery']);
	unset($row['goods_reserve']);

header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: text/plain; charset=euc-kr");

	echo "<<<begin>>>\n";
	echo '<<<mapid>>>'.$mapid."\n";
	if ($class != 'D') {
		if ($price != $lprice) {
			echo '<<<lprice>>>'.$lprice."\n";
		}
		echo '<<<price>>>'.$price."\n";
	}
	echo '<<<class>>>'.$class."\n";
	echo '<<<utime>>>'.$utime."\n";
	if( $class != 'D') {
		echo "<<<pname>>>".$goodsnm."\n";
		foreach ($row as $key=>$value) {
			if ($value != null) {
				echo '<<<'.$key.'>>>'.strip_tags($value)."\n";
				if ($key == 'igurl' && $class == 'U') echo '<<<upimg>>>Y'."\n";
			}
		}
	}
	echo "<<<ftend>>>\n";
}

?>
