<?
include "../lib/library.php";
@include "../conf/config.pay.php";
include "../conf/config.php";
@include "../conf/auctionos.php";
@include "../conf/fieldset.php";

### ���� db url  ���� ���� ###
$file	= "../conf/godomall.cfg.php";
$file	= file($file);
$godo	= decode($file[1],1);
if(!$partner['auctionshopid'])$partner['auctionshopid'] = "GODO".$godo[sno];

$tmpdir = explode('/','../data/auctionos/godo/'.$partner['auctionshopid']);
foreach($tmpdir as $k => $v){
	unset($rdir);
	for($i=0;$i <= $k;$i++) $rdir[] = $tmpdir[$i];
	$dir = implode('/',$rdir);
	if(!is_dir($dir)){
		@mkdir($dir);
		@chmod($dir,0707);
	}
}
$fp = fopen($dir."/auctionos.php","w");
fwrite($fp,'<?'.chr(10));
fwrite($fp,'if($_GET[mode] && $_GET[mode] != "new")	include "../../../../conf/engine/auctionos_".$_GET[mode].".php";'.chr(10));
fwrite($fp,'else	include "../../../../partner/auctionos.php";'.chr(10));
fwrite($fp,'?>'.chr(10));
fclose($fp);
@chmod($dir."/auctionos.php",0707);

function eSpecialTag($str){
	$str = strip_tags($str);
	$tmp = "\" ' < > \ |";
	$arr = explode(' ',$tmp);
	$str = str_replace($arr,'',$str);
	return $str;
}

$dir = "../conf/engine";
if (!is_dir($dir)) {
	@mkdir($dir, 0707);
	@chmod($dir, 0707);
}

### �⺻ ȸ�� ������
if($joinset[grp] != ''){
	list($mdc) = $db->fetch("select dc from gd_member_grp where level='".$joinset[grp]."' limit 1");
}

$querycnt = "select count(*) from ".GD_GOODS."  where runout=0 and open=1";
list($totnum) = $db->fetch($querycnt);
$httphost=preg_replace("/\:[0-9]+/","",$_SERVER['HTTP_HOST']);
$url = "http://".$httphost.$cfg[rootDir];

### ī�װ��� �迭
$query = "select * from ".GD_CATEGORY."";
$res = $db->query($query);
while ($data=$db->fetch($res)) $catnm[$data[category]] = $data[catnm];

if($tt != '1'){
	### �����ϰ� �Ǹ� ��ǰ �ѱݾ�
	$onemonth = date("Y-m-d h:i:s",(time()-7*24*60*60));
	$query = "select sum(price * ea) from ".GD_ORDER_ITEM." a left join ".GD_ORDER." b on a.ordno=b.ordno where istep < '40' and b.cdt >= '$onemonth'";
	list($tot) = $db->fetch($query);
	if(!$tot)$tot = 1;
}

for($tt=0;$tt < 2;$tt++){

	switch($tt){
		case "0" : $filename = "auctionos_all.php";
		break;
		case "1" : $filename = "auctionos_summary.php";
		break;
	}

	### ��ǰ ����Ÿ
	$query = "
	select * from
			".GD_GOODS." a
			left join ".GD_GOODS_BRAND." d on a.brandno=d.sno
	";
	$where = array();
	$where[] = "a.open=1";
	$where[] = "a.runout=0";

	if ($where) $where = " where ".implode(" and ",$where);
	$query .= $where;

	$res = $db->query($query);

	$fp = fopen("../conf/engine/".$filename,"w");
	fwrite($fp,'<?'.chr(10));
	fwrite($fp,'header("Cache-Control: no-cache, must-revalidate");'.chr(10));
	fwrite($fp,'header("Content-Type: text/plain; charset=euc-kr");'.chr(10));
	fwrite($fp,'?>'.chr(10));
	fclose($fp);

	$goodsModel = Clib_Application::getModelClass('goods');

	while ($v=$db->fetch($res)){

		// �Ǹ� ����(�Ⱓ �� ����)�� ��� ����
		if (! $goodsModel->setData($v)->canSales()) continue;

		$query ="select price,reserve from ".GD_GOODS_OPTION." where goodsno='$v[goodsno]' and link and go_is_deleted <> '1' and go_is_display = '1' limit 1";
		list($v[price],$v[reserve]) = $db->fetch($query);

		### ��ǰ�� �Ӹ��� ����
		if($partner['goodshead'])$v[goodsnm] = str_replace(array('{_maker}','{_brand}'),array($v[maker],$v[brandnm]),$partner['goodshead']).$v['goodsnm'];
		$v['goodsnm'] = strip_tags($v['goodsnm']);
		$v['goodsnm'] = strcut(eSpecialTag($v['goodsnm']),255);

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

			### �����ϰ� �� ��ǰ�� �Ǹ� �ݾ�
			$query = "select sum(a.price * a.ea) from ".GD_ORDER_ITEM." a left join ".GD_ORDER." b on a.ordno=b.ordno where istep < '40' and b.cdt >= '$onemonth' and a.goodsno='".$v['goodsno']."'";
			list($goodstot) = $db->fetch($query);
		}
		while ($w=$db->fetch($res2)){

			### �Ｎ��������
			$coupon = 0;
			list($v[coupon],$v[coupon_emoney]) = getCouponInfo($v[goodsno],$v[price]);
			$v[reserve] += $v[coupon_emoney];
			if($v[coupon])$coupon = getDcprice($v[price],$v[coupon]);

			### ȸ������
			$dcprice = 0;
			if($mdc)$dcprice = getDcprice($v[price],$mdc.'%');

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
			if($tmp['free'] && $tmp['price']) $deli = "0/".$tmp['free']."/".$tmp['price'];
			else{
				if($tmp[type] =="�ĺ�")$deli = -1;
				else $deli = $tmp['price']+0;
			}

			$jj++;
			$fp = fopen("../conf/engine/".$filename,"a");
			if($catnm[substr($w[category],0,3)]){
				fwrite($fp,'[[_BEGIN]]'.chr(10));
				fwrite($fp,'[[PRODID]]'.'C'.$w[category].'G'.$v[goodsno].chr(10));
				fwrite($fp,'[[PRNAME]]'.$v[goodsnm].chr(10));
				fwrite($fp,'[[_PRICE]]'.$price.chr(10));
				if($tt != "1"){
					fwrite($fp,'[[PRDURL]]'.$url.'/goods/goods_view.php?inflow=auctionos&goodsno='.$v[goodsno].chr(10));
					fwrite($fp,'[[IMGURL]]'.$img_url.chr(10));
					for ($i=1;$i<=4;$i++){
						fwrite($fp,'[[CATE_'.$i.']]');
						if($i*3 <= strlen($w[category]))fwrite($fp,eSpecialTag($catnm[substr($w[category],0,$i*3)]));
						fwrite($fp,chr(10));
					}
					fwrite($fp,'[[_MODEL]]'.strip_tags($v[goodscd]).chr(10));
					fwrite($fp,'[[_BRAND]]'.strip_tags($v[brandnm]).chr(10));
					fwrite($fp,'[[_MAKER]]'.strip_tags($v[maker]).chr(10));
					fwrite($fp,'[[ORIGIN]]'.strip_tags($v[origin]).chr(10));
					fwrite($fp,'[[PRDATE]]'.substr($v[regdt],0,10).chr(10));
					fwrite($fp,'[[DELIVR]]'.$deli.chr(10));
					fwrite($fp,'[[_EVENT]]'.strip_tags($event).chr(10));
					fwrite($fp,'[[COUPON]]');
					if($v[coupon])fwrite($fp,$v[coupon].' �������� ����');
					fwrite($fp,chr(10));
					fwrite($fp,'[[PRCARD]]'.trim($partner[nv_pcard]).chr(10));
					fwrite($fp,'[[_POINT]]'.$v[reserve].chr(10));
					fwrite($fp,'[[MODIMG]]Y'.chr(10));
					fwrite($fp,'[[SRATIO]]'.round($goodstot/$tot*100).chr(10));
				}
				fwrite($fp,'[[___END]]'.chr(10));
			}
			fclose($fp);
			flush();
			$num++;
			if(!$_GET['gengine']){
				$per = round( $num / ($totnum * 2)  * 100 );
				echo("<script>parent.document.getElementById('progressbar').style.width='".$per."%';</script>\n");
			}
		}
	}
	@chmod('../conf/engine/'.$filename,0707);
}
if(!$_GET['gengine']){
	echo("<script>parent.document.getElementById('progressbar').style.width='100%';</script>\n");
	msg("������Ʈ �Ϸ�!");
}else{
	echo("ok!!");
}
?>
