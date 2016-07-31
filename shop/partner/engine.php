<?
include "../lib/library.php";
@include "../conf/config.pay.php";
include "../conf/config.php";
@include "../conf/partner.php";
@include "../conf/coupon.php";
@include dirname(__FILE__).'/../conf/config.mobileShop.php';

$LF = chr(10);	// line feed.

$dir = "../conf/engine";
if (!is_dir($dir)) {
	@mkdir($dir, 0707);
	@chmod($dir, 0707);
}

$score = 0;
$url = "http://".$_SERVER['HTTP_HOST'].$cfg['rootDir'];

### �⺻ ȸ�� ������
@include "../conf/fieldset.php";
if($joinset['grp'] != ''){
	list($mdc) = $db->fetch("select dc from gd_member_grp where level='".$joinset['grp']."' limit 1");
}

### ī�װ��� �迭
$query = "select * from gd_category";
$res = $db->query($query);
while ($data=$db->fetch($res)) $catnm[$data['category']] = strip_tags($data['catnm']);

$querycnt = "select count(*) from gd_goods where runout='0' and open='1'";
list($totnum) = $db->fetch($querycnt);

### ��ǰ ����Ÿ
$query = "select *,category
		from gd_goods a left join gd_goods_brand d on a.brandno=d.sno
		left join (select _grv.goodsno, count(_grv.sno) as review_count from ".GD_GOODS_REVIEW." as _grv group by _grv.goodsno) as grv on a.goodsno=grv.goodsno,
			gd_goods_option b,
			(select goodsno, ".getCategoryLinkQuery('category', null, 'max')." from ".GD_GOODS_LINK." c group by c.goodsno) e
		where a.goodsno=b.goodsno
		  and a.goodsno=e.goodsno
			and b.link and go_is_deleted <> '1' and go_is_display = '1'
			and a.open=1 and a.runout=0";

$res = $db->query($query);

$tscore = 0;
$fp = fopen("../conf/engine/naver_all.php","w");
fwrite($fp,'<?'.$LF);
fwrite($fp,'header("Cache-Control: no-cache, must-revalidate");'.$LF);
fwrite($fp,'header("Content-Type: text/plain; charset=euc-kr");'.$LF);
fwrite($fp,'?>'.$LF);
fclose($fp);

$fp = fopen("../conf/engine/naver_summary.php","w");
fwrite($fp,'<?'.$LF);
fwrite($fp,'header("Cache-Control: no-cache, must-revalidate");'.$LF);
fwrite($fp,'header("Content-Type: text/plain; charset=euc-kr");'.$LF);
fwrite($fp,'?>'.$LF);
fclose($fp);

$goodsModel = Clib_Application::getModelClass('goods');

while ($v=$db->fetch($res)){

	// �Ǹ� ����(�Ⱓ �� ����)�� ��� ����
	if (! $goodsModel->setData($v)->canSales()) continue;

	### ��ǰ�� �Ӹ��� ����
	if($partner['goodshead'])$v['goodsnm'] = str_replace(array('{_maker}','{_brand}'),array($v['maker'],$v['brandnm']),$partner['goodshead']).$v['goodsnm'];

	list($v['img']) = explode("|",$v['img_m']);

	### �Ｎ��������
	$coupon = 0;
	if($cfgCoupon['use_yn']){
		$_cp = getCouponInfo($v['goodsno'],$v['price']);
		$v['coupon'] = $_cp[0];
		$v['coupon_emoney'] = $_cp[1];
		$v[reserve] += $v['coupon_emoney'];
		if($v['coupon'])$coupon = getDcprice($v['price'],$v['coupon']);
	}

	### ȸ������
	$dcprice = 0;
	if($mdc)$dcprice = getDcprice($v['price'],$mdc.'%');

	### ���� ȸ������ �ߺ� ���� üũ
	if($coupon>0 && $dcprice>0){
		if($cfgCoupon['range'] == 2)$dcprice=0;
		if($cfgCoupon['range'] == 1)$coupon=0;
	}

	### ���� ����
	$coupon += 0;
	$dcprice += 0;
	$price = $v['price'] - $coupon - $dcprice;

	### ��۷�
	$param = array(
		'mode' => '1',
		'deliPoli' => 0,
		'price' => $price,
		'goodsno' => $v['goodsno'],
		'goods_delivery' => $v['goods_delivery'],
		'delivery_type' => $v['delivery_type']
	);
	$tmp = getDeliveryMode($param);
	$deli=0;
	if ($tmp['type'] == '�ĺ�' || $tmp['msg'] == '���� ���� ��ۺ�') {
		$deli = '-1';
	} else {
		$deli = $tmp['price'] ? $tmp['price'] : '0';
	}

	$v['goodsnm'] = strip_tags($v['goodsnm']);

	// �̺�Ʈ
	$event = '';
	if ($partner['naver_event_common'] === 'Y' && empty($partner['eventCommonText']) === false) {	// ���� ����
		$event = $partner['eventCommonText'];
	}

	if ($partner['naver_event_goods'] === 'Y' && empty($v['naver_event']) === false) {	// ��ǰ�� ����
		if (empty($event) === false) $event .= ' , ';
		$event .= $v['naver_event'];
	}

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
	if(preg_match('/http:\/\//',$v['img']))$img_url = $v['img'];
	else $img_url = $url.'/data/goods/'.$v['img'];

	$fp = fopen("../conf/engine/naver_all.php","a");
	if($catnm[substr($v['category'],0,3)])
	fwrite($fp,'<<<begin>>>'.$LF);
	fwrite($fp,'<<<mapid>>>'.$v['goodsno'].$LF);
	fwrite($fp,'<<<pname>>>'.$v['goodsnm'].$LF);
	fwrite($fp,'<<<price>>>'.$price.$LF);
	fwrite($fp,'<<<pgurl>>>'.$url.'/goods/goods_view.php?inflow=naver&goodsno='.$v['goodsno'].$LF);
	fwrite($fp,'<<<igurl>>>'.$img_url.$LF);
	for ($i=1;$i<=strlen($v['category'])/3;$i++){
		fwrite($fp,'<<<cate'.$i.'>>>'.$catnm[substr($v['category'],0,$i*3)].$LF);
	}
	fwrite($fp,'<<<model>>>'.$v['goodscd'].$LF);
	fwrite($fp,'<<<brand>>>'.$v['brandnm'].$LF);
	fwrite($fp,'<<<maker>>>'.$v['maker'].$LF);
	fwrite($fp,'<<<origi>>>'.$v['origin'].$LF);
	fwrite($fp,'<<<pdate>>>'.substr($v['launchdt'],0,7).$LF);
	fwrite($fp,'<<<deliv>>>'.$deli.$LF);
	fwrite($fp,'<<<event>>>'.strip_tags($event).$LF);	// �̺�Ʈ ����
	if($coupon) fwrite($fp,'<<<coupo>>>'.$coupon.' �������� ����'.$LF);
	if($partner['nv_pcard']) fwrite($fp,'<<<pcard>>>'.$partner['nv_pcard'].$LF);
	fwrite($fp,'<<<point>>>'.$v['reserve'].$LF);
	fwrite($fp,'<<<score>>>'.$score.$LF);
	fwrite($fp,'<<<revct>>>'.(int)$v['review_count'].$LF);
	if (isset($cfgMobileShop) && $cfgMobileShop['useMobileShop'] == '1') {
		fwrite($fp, '<<<mourl>>>http://'.$_SERVER['HTTP_HOST'].'/m/goods/view.php?goodsno='.$v['goodsno'].'&inflow=naver'.$LF);
	}
	else {
		fwrite($fp, '<<<mourl>>>'.$LF);
	}
	fwrite($fp,'<<<pcpdn>>>'.$isCoupon.$LF);	//�����ٿ�ε��ʿ� ����
	fwrite($fp,'<<<dlvga>>>'.$isDlv.$LF);	//�����ۺ� ����
	fwrite($fp,'<<<dlvdt>>>'.$dlvDesc.$LF);	//�����ۺ� ����
	fwrite($fp,'<<<insco>>>'.$isAddPrice.$LF);	//���� ��ġ�� ����
	fwrite($fp,'<<<ftend>>>'.$LF);
	fclose($fp);

	$fp = fopen("../conf/engine/naver_summary.php","a");
	fwrite($fp,'<<<begin>>>'.$LF);
	fwrite($fp,'<<<mapid>>>'.$v['goodsno'].$LF);
	fwrite($fp,'<<<pname>>>'.$v['goodsnm'].$LF);
	fwrite($fp,'<<<price>>>'.$price.$LF);
	fwrite($fp,'<<<ftend>>>'.$LF);
	fclose($fp);

	$num++;
	$per = round( $num / $totnum * 100 );
	if($tmp != $per) echo("<script>parent.document.getElementById('progressbar').style.width='".$per."%';</script>\n");
	$tmp = $per;
	flush();
}
echo("<script>parent.document.getElementById('progressbar').style.width='100%';</script>\n");
msg("������Ʈ �Ϸ�!");
?>
