<?
include "../lib/library.php";
@include "../conf/config.pay.php";
include "../conf/config.php";
@include "../conf/partner.php";
@include "../conf/coupon.php";
@include dirname(__FILE__).'/../conf/config.mobileShop.php';

if ($godo[ecCode]=="self_enamoo_season"){
	if($partner['useYn'] != 'y') exit;
}

if(!$_GET[mode])$_GET[mode]="all";
if($partner['naver_version'] == '2'){
	if(!headers_sent()){
		header("Location:./naver2_".$_GET['mode'].".php");exit;
	}
	exit;
}
else if ($partner['naver_version'] == '3') {
	if(!headers_sent()){
		header("Location:./naver3_".$_GET['mode'].".php");exit;
	}
	exit;
}
if($_GET[mode] != "new"){
	if(is_file("../conf/engine/naver_".$_GET[mode].".php")){
		$handle = fopen("../conf/engine/naver_".$_GET[mode].".php", "r");
		$contents = '';
		while (!feof($handle)) {
			echo fread($handle, 8192);
			flush();
		}
		fclose($handle);
	}
	exit;
}

### �⺻ ȸ�� ������
@include "../conf/fieldset.php";
if($joinset[grp] != ''){
	$memberdc = $db->fetch("select dc,excep,excate from ".GD_MEMBER_GRP." where level='".$joinset[grp]."' limit 1");
}

$url = "http://".$_SERVER['HTTP_HOST'].$cfg[rootDir];

$querycnt = "select count(*) from ".GD_GOODS." where runout='0' and open='1'";
list($totnum) = $db->fetch($querycnt);

### ī�װ��� �迭
$query = "select * from ".GD_CATEGORY;
$res = $db->query($query);
while ($data=$db->fetch($res)) $catnm[$data[category]] = strip_tags($data[catnm]);

### ��ǰ ����Ÿ
$query = "
select a.*,d.*,grv.review_count from
        ".GD_GOODS." a
        left join ".GD_GOODS_BRAND." d on a.brandno=d.sno
		left join (select _grv.goodsno, count(_grv.sno) as review_count from ".GD_GOODS_REVIEW." as _grv group by _grv.goodsno) as grv on a.goodsno=grv.goodsno
";
$where[] = "a.open=1";
$where[] = "a.runout=0";

$yesterday = date("Y-m-d",mktime(0,0,0,date("m"),date("d")-1,date("Y")));
$where[] = "date_format(a.regdt,'%Y-%m-%d') >= '$yesterday'";


if ($where) $where = " where ".implode(" and ",$where);
$query .= $where;

$res = $db->query($query);

if (!$_GET[mode]) $_GET[mode] = "total";


header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: text/plain; charset=euc-kr");

$goodsModel = Clib_Application::getModelClass('goods');

while ($v=$db->fetch($res)){

	// �Ǹ� ����(�Ⱓ �� ����)�� ��� ����
	if (! $goodsModel->setData($v)->canSales()) continue;

	$query ="select price,reserve from ".GD_GOODS_OPTION." where goodsno='$v[goodsno]' and link and go_is_deleted <> '1' and go_is_display = '1'  limit 1";
	list($v[price],$v[reserve]) = $db->fetch($query);

	### ��ǰ�� �Ӹ��� ����
	if($partner['goodshead'])$v[goodsnm] = str_replace(array('{_maker}','{_brand}'),array($v[maker],$v[brandnm]),$partner['goodshead']).$v['goodsnm'];

	$query = "select ".getCategoryLinkQuery('category', null, 'max')." from ".GD_GOODS_LINK." where goodsno='$v[goodsno]' limit 1";
	$res2 = $db->query($query);
	while ($w=$db->fetch($res2)){

		// �̺�Ʈ
		$event = '';
		if ($partner['naver_event_common'] === 'Y' && empty($partner['eventCommonText']) === false) {	// ���� ����
			$event = $partner['eventCommonText'];
		}

		if ($partner['naver_event_goods'] === 'Y' && empty($v['naver_event']) === false) {	// ��ǰ�� ����
			if (empty($event) === false) $event .= ' , ';
			$event .= $v['naver_event'];
		}

		### �Ｎ��������
		$coupon = 0;
		if($cfgCoupon['use_yn']){
			list($v[coupon],$v[coupon_emoney]) = getCouponInfo($v[goodsno],$v[price]);
			$v[reserve] += $v[coupon_emoney];
			if($v[coupon])$coupon = getDcprice($v[price],$v[coupon]);
		}

		### ȸ������
		$dcprice = 0;
		if (is_array($memberdc) === true) {
			$mdc_exc = chk_memberdc_exc($memberdc,$v['goodsno']); // ȸ������ ���ܻ�ǰ üũ
			if($mdc_exc === false)$dcprice = getDcprice($v['price'],$memberdc['dc'].'%');
		}

		### ���� ȸ������ �ߺ� ���� üũ
		if($coupon>0 && $dcprice>0){
			if($cfgCoupon['range'] == 2)$dcprice=0;
			if($cfgCoupon['range'] == 1)$coupon=0;
		}

		### ���� ����
		$coupon += 0;
		$dcprice += 0;
		$price = $v[price] - $coupon - $dcprice;

		### ��۷�
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
		if ($tmp['type'] == '�ĺ�' || $tmp['msg'] == '���� ���� ��ۺ�') {
			$deli = -1;
		} else {
			$deli = $tmp['price']+0;
		}

		// �̹���
		$tmp = explode("|",$v[img_m]);
		while ($v[img] = array_shift($tmp)) {

			if ( preg_match('/^http(s)?:\/\//',$v[img]) ) {
				break;
			}
			elseif ($v[img]) {
				$v[img] = $url.'/data/goods/'.$v[img];
				break;
			}

		}
		$v[goodsnm] = strip_tags($v[goodsnm]);

		$extra_info = gd_json_decode(stripslashes($v['extra_info']));
		$dlvDesc = '';
		$addPrice = '';
		$isDlv = '';
		$isAddPrice = '';
		$isCoupon = '';
		if(is_array($extra_info)){
			foreach($extra_info as $key=>$val) {
				if($val['title'] == '��� �� ��ġ���'){
					$dlvDesc = $val['desc'];
				}
				if($val['title'] == '�߰���ġ���'){
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
		$couponData = getCouponInfo($v['goodsno'], $v['price'], 'v');
		foreach($couponData as $key=>$val) {
			if($val['price']>0) {
				$isCoupon = 'Y';
			}
		}

		if($catnm[substr($w[category],0,3)]){
?>
<<<begin>>>
<<<mapid>>><?=$v[goodsno]."\n"?>
<<<pname>>><?=$v[goodsnm]."\n"?>
<<<price>>><?=$price."\n"?>
<<<pgurl>>><?=$url?>/goods/goods_view.php?goodsno=<?=$v[goodsno]."\n"?>
<<<igurl>>><?=$v[img]."\n"?>
<? for ($i=1;$i<=strlen($w[category])/3;$i++){ ?>
<<<cate<?=$i?>>>><?=$catnm[substr($w[category],0,$i*3)]."\n"?>
<? } ?>
<<<model>>><?=$v[goodscd]."\n"?>
<<<brand>>><?=$v[brandnm]."\n"?>
<<<maker>>><?=$v[maker]."\n"?>
<<<origi>>><?=$v[origin]."\n"?>
<<<deliv>>><?=$deli."\n"?>
<<<event>>><?=strip_tags($event)."\n"?>
<? if ($coupon){ ?><<<coupo>>><?=$coupon?> �������� ����<? echo "\n"; } ?>
<? if ($partner[nv_pcard]){ ?><<<pcard>>><?=$partner[nv_pcard]."\n"?><? } ?>
<<<point>>><?=$v[reserve]."\n"?>
<<<revct>>><?=(int)$v[review_count]."\n"?>
<?php if (isset($cfgMobileShop) && $cfgMobileShop['useMobileShop'] == '1') { ?>
<<<mourl>>>http://<?php echo $_SERVER['HTTP_HOST']; ?>/m/goods/view.php?goodsno=<?php echo $v['goodsno']; ?>&inflow=naver
<?php } else { ?>
<<<mourl>>>
<?php } ?>
<<<pcpdn>>><?=$isCoupon."\n"?>
<<<dlvga>>><?=$isDlv."\n"?>
<<<dlvdt>>><?=$dlvDesc."\n"?>
<<<insco>>><?=$isAddPrice."\n"?>
<<<ftend>>>
<?
		}
	}
}
?>
