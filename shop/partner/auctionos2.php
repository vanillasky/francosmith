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

### �⺻ ȸ�� ������
if($joinset[grp] != ''){
	$memberdc = $db->fetch("select dc,excep,excate from ".GD_MEMBER_GRP." where level='".$joinset[grp]."' limit 1");
}

$url = "http://".$_SERVER['HTTP_HOST'].$cfg[rootDir];

### ī�װ��� �迭
$query = "select * from ".GD_CATEGORY."";
$res = $db->query($query);
while ($data=$db->fetch($res)) $catnm[$data[category]] = $data[catnm];

if($tt != '1'){
	### �����ϰ� ���Ǹ� ��ǰ�ݾ�
	$onemonth = date("Y-m-d h:i:s",(time()-7*24*60*60));
	$query = "select sum(price * ea) from ".GD_ORDER_ITEM." a left join ".GD_ORDER." b on a.ordno=b.ordno where istep < '40' and b.cdt >= '$onemonth'";
	list($tot) = $db->fetch($query);
	if(!$tot)$tot = 1;
}

$delimiter = "<!>";

### ��ǰ ����Ÿ
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

	// �Ǹ� ����(�Ⱓ �� ����)�� ��� ����
	if (! $goodsModel->setData($v)->canSales()) continue;

	$query ="select price,reserve from ".GD_GOODS_OPTION." where goodsno='$v[goodsno]' and link and go_is_deleted <> '1' and go_is_display = '1'  limit 1";
	list($v[price],$v[reserve]) = $db->fetch($query);

	### ��ǰ�� �Ӹ��� ����
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
		###�̺�Ʈ
		$date = date("Ymd");
		$query = "select z.subject from
								".GD_EVENT." z left join ".GD_GOODS_DISPLAY." a on z.sno=substring(a.mode,2) and substring(a.mode,1,1) = 'e'
								left join ".GD_GOODS." b on a.goodsno=b.goodsno
								left join ".GD_GOODS_OPTION." c on a.goodsno=c.goodsno and go_is_deleted <> '1' and go_is_display = '1'
								where link and a.goodsno='$row[goodsno]' and z.sdate <= '$date' and z.edate >= '$date' limit 1";
		list($event) = $db->fetch($query);

		### �����ϰ� �̻�ǰ�� �Ǹ� �ݾ�
		$query = "select sum(a.price * a.ea) from ".GD_ORDER_ITEM." a left join ".GD_ORDER." b on a.ordno=b.ordno where istep < '40' and b.cdt >= '$onemonth' and a.goodsno='".$v['goodsno']."'";
		list($goodstot) = $db->fetch($query);
	}
	$w=$db->fetch($res2);

	### �Ｎ��������
	$coupon = 0;
	list($v[coupon],$v[coupon_emoney]) = getCouponInfo($v[goodsno],$v[price]);
	$v[reserve] += $v[coupon_emoney];
	if($v[coupon])$coupon = getDcprice($v[price],$v[coupon]);

	### ȸ������
	$dcprice = 0;
	if (is_array($memberdc) === true) {
		$mdc_exc = chk_memberdc_exc($memberdc,$v['goodsno']); // ȸ������ ���ܻ�ǰ üũ
		if($mdc_exc === false)$dcprice = getDcprice($v['price'],$memberdc['dc'].'%');
	}

	### ��ٿ� ���� ���뿩�� Ȯ��
	$about_dc_price = 0;
	if ( $aboutcoupon['use_aboutcoupon'] == 'Y' && $aboutcoupon['use_test']=='N' ) {
		$about_dc_price = getDcprice($v[price], '8%');
	}

	### ���� ȸ������ �ߺ� ���� üũ
	if($coupon>0 && $dcprice>0){
		if($cfgCoupon['range'] == 2)$dcprice=0;
		if($cfgCoupon['range'] == 1)$coupon=0;
	}

	### ���� ����
	$coupon += 0;
	$dcprice += 0;
	$price = $v[price] - $coupon - $dcprice - $about_dc_price;

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
	if($tmp[type] =="�ĺ�" || ($tmp['free'] && $tmp['price'])) $deli = -1;
	else{
		$deli = $tmp['price']+0;
	}

	$jj++;

	if($catnm[substr($w[category],0,3)]){
		echo($v[goodsno].$delimiter); 	// 1 ���θ���ǰID
		if ($tt != "1") {	// ��ü �̸�
			echo("C".$delimiter);
		} else {
			echo("U".$delimiter);
		}
		echo($v[goodsnm].$delimiter);		// 3 ��ǰ��
		echo($price.$delimiter);			// 4 ����
		echo($url.'/goods/goods_view.php?inflow=auctionos&goodsno='.$v[goodsno].$delimiter);	// 5 ��URL
		echo($img_url.$delimiter);	// 5 �̹���URL
		for ($i=1;$i<=4;$i++){
			if($i*3 <= strlen($w[category]))echo(substr($w[category],0,$i*3));
			echo($delimiter);
		}
		for ($i=1;$i<=4;$i++){
			if($i*3 <= strlen($w[category]))echo(eSpecialTag($catnm[substr($w[category],0,$i*3)]));
			echo($delimiter);
		}
		echo( strip_tags($v[goodscd]).$delimiter);	// �𵨸�
		echo( strip_tags($v[brandnm]).$delimiter);	// �귣��
		echo( strip_tags($v[maker]).$delimiter);		// ����Ŀ
		echo( strip_tags($v[origin]).$delimiter);		// ������
		echo( substr($v[regdt],0,10).$delimiter);		// ��ǰ�������
		echo( $deli.$delimiter);						// ��ۺ�
		echo( strip_tags($event).$delimiter);			// �̺�Ʈ
		echo( ($coupon+$about_dc_price));
		echo($delimiter);									// ����
		echo(trim($partner[nv_pcard]).$delimiter);		// 23. ������
		echo($v[reserve].$delimiter);						// 24. ������
		echo($delimiter);									// 25. �̹��� �������� 		���� �����ʿ�
		echo($delimiter);									// 26. ��ǰƯ������ 		���� �����ʿ�
		echo(round($goodstot/$tot*100).$delimiter);		// 27. ������ �������
		echo("\r\n");
	}

	flush();
	$num++;

}
?>
