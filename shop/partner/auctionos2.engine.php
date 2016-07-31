<?
include "../lib/library.php";
@include "../conf/config.pay.php";
include "../conf/config.php";
@include "../conf/auctionos.php";
@include "../conf/fieldset.php";
@include "../conf/coupon.php";

$aboutcoupon = $config->load('aboutcoupon');
$LF = "\r\n";

function check_accept_ip(){
	$out = readurl("http://gongji.godo.co.kr/userinterface/serviceIp/auctionos.php");
	$arr = explode(chr(10),$out);
	$ret = false;
	foreach($arr as $data){
		$data = trim($data);
		if($data&&preg_match('/'.$data.'/',$_SERVER['REMOTE_ADDR']))$ret = true;
	}
	if(preg_match('/admin\/auctionos\/partner.php/',$_SERVER['HTTP_REFERER'])) $ret = true;
	return $ret;
}

if(!check_accept_ip()) exit;

$delimiter = "<!>";
### ���� db url  ���� ���� ###
$file	= "../conf/godomall.cfg.php";
$file	= file($file);
$godo	= decode($file[1],1);
if(!$partner['auctionshopid'])$partner['auctionshopid'] = "GODO".$godo[sno];

$tmpdir = explode('/','../data/auctionos/godo/'.$partner['auctionshopid']);
foreach($tmpdir as $k => $data){
	unset($rdir);
	for($i=0;$i <= $k;$i++) $rdir[] = $tmpdir[$i];
	$dir = implode('/',$rdir);
	if(!is_dir($dir)){
		@mkdir($dir);
		@chmod($dir,0707);
	}
}
$fp = fopen($dir."/auctionos2.php","w");
fwrite($fp,'<?'.$LF);
fwrite($fp,'if($_GET[mode] && $_GET[mode] != "new")	include "../../../../conf/engine/auctionos2_".$_GET[mode].".php";'.$LF);
fwrite($fp,'else	include "../../../../partner/auctionos2.php";'.$LF);
fwrite($fp,'?>'.$LF);
fclose($fp);
@chmod($dir."/auctionos2.php",0707);

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
	$memberdc = $db->fetch("select dc,excep,excate from ".GD_MEMBER_GRP." where level='".$joinset[grp]."' limit 1");
}

$querycnt = "select count(1) from ".GD_GOODS."  where runout=0 and open=1";
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

### ��ǰ ����Ÿ
$query = "
SELECT

	G.goodsno, G.goodsnm, G.goodscd, G.img_m, G.goods_delivery, G.delivery_type, G.maker, G.origin, G.updatedt, G.regdt,
	G.sales_range_start, G.sales_range_end,
	CT.category,
	GO.price,GO.reserve,
	BR.brandnm

FROM ".GD_GOODS." AS G

INNER JOIN ".GD_GOODS_LINK." AS LNK
	ON G.goodsno = LNK.goodsno

INNER JOIN ".GD_CATEGORY." AS CT
	ON LNK.category = CT.category

INNER JOIN ".GD_GOODS_OPTION." AS GO
	ON G.goodsno = GO.goodsno AND GO.link = 1 and go_is_deleted <> '1' and go_is_display = '1'

LEFT JOIN ".GD_GOODS_BRAND." AS BR
	ON G.brandno = BR.sno

WHERE G.open = 1 AND G.runout = 0

GROUP BY G.goodsno

ORDER BY NULL
";
$res = $db->query($query);

for($tt=0;$tt < 2;$tt++){

	switch($tt){
		case "0" : $filename = "auctionos2_all.php";
		break;
		case "1" : $filename = "auctionos2_summary.php";
		break;
	}

	$_filename = '../conf/engine/'.$filename;	// ep ����

	// ���� ��� �ð��� �ٰŷ�, ���� ep ���� ���� ����
	if (($last_ep_update_time = @filectime($_filename)) === false) {
		$last_ep_update_time = time();
	}

	$last_ep_update_time = $last_ep_update_time - 43200;	// �߰� ���� �ð�

	# ���⿡�� �ϴ� ���� ������ �����ϰ� �ٽ� ����.
	$fp = fopen($_filename,"w");
	fwrite($fp,'<?'.$LF);
	fwrite($fp,'header("Cache-Control: no-cache, must-revalidate");'.$LF);
	fwrite($fp,'header("Content-Type: text/plain; charset=euc-kr");'.$LF);
	fwrite($fp,'?>'.$LF);
	fclose($fp);


	// ���� �����͸� �̵�
	mysql_data_seek($res, 0);

	$fp = fopen($_filename,"a");

	$goodsModel = Clib_Application::getModelClass('goods');

	while ($data=$db->fetch($res)){

		// �Ǹ� ����(�Ⱓ �� ����)�� ��� ����
		if (! $goodsModel->setData($data)->canSales()) continue;

		### ��ǰ�� �Ӹ��� ����
		if($partner['goodshead'])$data[goodsnm] = str_replace(array('{_maker}','{_brand}'),array($data[maker],$data[brandnm]),$partner['goodshead']).$data['goodsnm'];
		$data['goodsnm'] = strip_tags($data['goodsnm']);
		$data['goodsnm'] = strcut(eSpecialTag($data['goodsnm']),255);

		/*
			1. ����ī�װ� ��ǰ���� Ȯ��
			2. Ư��ī�װ� ( ���λ�ǰ) ǥ��
		 */

		list($data[img]) = explode("|",$data[img_m]);

		// �̹��� ��� ó�� �� �̹��� ���� �±� ����
		if(preg_match('/http:\/\//',$data[img])) {
			$img_url = $data[img];
			$modimg = ($last_ep_update_time <= strtotime($data['updatedt'])) ? 'Y' : 'N';
		}
		else {
			$img_url = $url.'/data/goods/'.$data[img];
			$modimg = ($last_ep_update_time <= @filectime('../data/goods/'.$data[img])) ? 'Y' : 'N';
		}

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
			$query = "select sum(a.price * a.ea) from ".GD_ORDER_ITEM." a left join ".GD_ORDER." b on a.ordno=b.ordno where istep < '40' and b.cdt >= '$onemonth' and a.goodsno='".$data['goodsno']."'";
			list($goodstot) = $db->fetch($query);
		}


		### �Ｎ��������
		$coupon = 0;
		list($data[coupon],$data[coupon_emoney]) = getCouponInfo($data[goodsno],$data[price]);
		$data[reserve] += $data[coupon_emoney];
		if($data[coupon])$coupon = getDcprice($data[price],$data[coupon]);

		### ȸ������
		$dcprice = 0;
		if (is_array($memberdc) === true) {
			$mdc_exc = chk_memberdc_exc($memberdc,$data['goodsno']); // ȸ������ ���ܻ�ǰ üũ
			if($mdc_exc === false)$dcprice = getDcprice($data['price'],$memberdc['dc'].'%');
		}

		### ��ٿ� ���� ���뿩�� Ȯ��
		$about_dc_price = 0;
		if ( $aboutcoupon['use_aboutcoupon'] == 'Y' && $aboutcoupon['use_test']=='N' ) {
			$about_dc_price = getDcprice($data[price], '8%');
		}

		### ���� ȸ������ �ߺ� ���� üũ
		if($coupon>0 && $dcprice>0){
			if($cfgCoupon['range'] == 2)$dcprice=0;
			if($cfgCoupon['range'] == 1)$coupon=0;
		}

		### ���� ����
		$coupon += 0;
		$dcprice += 0;
		$price = $data[price] - $coupon - $dcprice - $about_dc_price;

		### ��۷�
		$param = array(
			'mode' => '1',
			'deliPoli' => 0,
			'price' => $price,
			'goodsno' => $data[goodsno],
			'goods_delivery' => $data[goods_delivery],
			'delivery_type' => $data[delivery_type]
		);
		$tmp = getDeliveryMode($param);
		$deli=0;
		if($tmp[type] =="�ĺ�" || ($tmp['free'] && $tmp['price'])) $deli = -1;
		else{
			$deli = $tmp['price']+0;
		}

		$EP_DATA = array();
		$EP_DATA[1] = $data['goodsno'];	 	//���θ� ��ǰID
		$EP_DATA[2] = ($tt != "1") ? 'C' : 'U';	 	//��ǰ ����(C/U/D)
		$EP_DATA[3] = $data['goodsnm'];	 	//��ǰ��
		$EP_DATA[4] = $price;	 	//�ǸŰ���
		$EP_DATA[5] = $url.'/goods/goods_view.php?inflow=auctionos&goodsno='.$data['goodsno'];	 	//��ǰ�� �������� �ּ�
		$EP_DATA[6] = $img_url;	 	//�̹��� URL

		/*
		$EP_DATA[7];	 	//��з� ī�װ� �ڵ�
		$EP_DATA[8];	 	//�ߺз� ī�װ� �ڵ�
		$EP_DATA[9];	 	//�Һз� ī�װ� �ڵ�
		$EP_DATA[10];	 	//���з� ī�װ� �ڵ�

		$EP_DATA[11];	 	//��ī�װ���
		$EP_DATA[12];	 	//��ī�װ���
		$EP_DATA[13];	 	//��ī�װ���
		$EP_DATA[14];	 	//��ī�װ���
		*/

		for ($i=1;$i<=4;$i++){
			if ($i*3 <= strlen($data['category'])) {
				$EP_DATA[$i + 6] = substr($data['category'],0,$i *3);	// ī�װ� �ڵ�
				$EP_DATA[$i + 10] = eSpecialTag($catnm[substr($data['category'],0,$i*3)]);	// ī�װ� ��
			}
			else {
				$EP_DATA[$i + 6] = '';	// ī�װ� �ڵ�
				$EP_DATA[$i + 10] = '';	// ī�װ� ��
			}
		}

		$EP_DATA[15] = strip_tags($data[goodscd]);	 	//�𵨸�
		$EP_DATA[16] = strip_tags($data[brandnm]);	 	//�귣��
		$EP_DATA[17] = strip_tags($data[maker]);	 	//����Ŀ
		$EP_DATA[18] = strip_tags($data[origin]);	 	//������
		$EP_DATA[19] = substr($data[regdt],0,10);	 	//��ǰ�������
		$EP_DATA[20] = $deli;	 	//��ۺ�
		$EP_DATA[21] = strip_tags($event);	 	//�̺�Ʈ
		$EP_DATA[22] = ($coupon+$about_dc_price);	 	//�����ݾ�
		$EP_DATA[23] = trim($partner[nv_pcard]);	 	//������
		$EP_DATA[24] = $data[reserve];	 	//������
		$EP_DATA[25] = $modimg;	 	//�̹������濩��
		$EP_DATA[26] = '';	 	//��ǰƯ������
		$EP_DATA[27] = round($goodstot/$tot*100);	 	//������ �������

		$EP_DATA[28] = date('Y-m-d H:i:s');	 	//��ǰ���� ����ð�*/

		ksort($EP_DATA);
		fwrite($fp, implode($delimiter, $EP_DATA).$LF);

		flush();
		$num++;
		if(!$_GET['gengine']){
			$per = round( $num / ($totnum * 2)  * 100 );
			echo("<script>parent.document.getElementById('progressbar').style.width='".$per."%';</script>\n");
		}

	}
	fclose($fp);
	@chmod('../conf/engine/'.$filename,0707);
}
if(!$_GET['gengine']){
	echo("<script>parent.document.getElementById('progressbar').style.width='100%';</script>\n");
	msg("������Ʈ �Ϸ�!");
}else{
	echo("ok!!");
}
?>
