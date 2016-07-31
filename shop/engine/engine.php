<?
if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['mode'])) exit;

include "../lib/library.php";
include "../conf/config.php";
include "../conf/config.pay.php";
include "../conf/engine.php";
@include "../conf/coupon.php";

function img_url($url,$src){
	if(preg_match('/http:\/\//',$src))$img_url = $src;
	else $img_url = $url.'/data/goods/'.$src;
	return $img_url;
}

$url = "http://".$_SERVER['HTTP_HOST'].$cfg[rootDir];

$dir = "../conf/engine";
if (!is_dir($dir)) {
	@mkdir($dir, 0707);
	@chmod($dir, 0707);
}

if($_GET[allmode]){
	$arrmode = array("omi","mm","danawa","danawa_new","naver_elec","naver_bea","naver_milk");
	$_GET[allmode]++;
	$tmp_mode = $arrmode[$_GET[allmode]-2];
	$_GET[mode] = $tmp_mode;
	if($tmp_mode == "danawa_new")${$tmp_mode}[chk] = $danawa[chk];
	if($tmp_mode && !${$tmp_mode}[chk]){
		go($PHP_SELF."?allmode=".$_GET[allmode]);
		exit;
	}
	if(!$tmp_mode){
		msg("������Ʈ �Ϸ�!");
		echo "<script>parent.location.reload();</script>";
		exit;
	}
}

if($_GET[mode] && $_GET[modeView]=='y'){

	$_inc = ($_GET[mode] == 'danawa_new') ? $danawa[chk] : ${$_GET[mode]}[chk];
	if($_inc){
		include "../conf/engine/{$_GET[mode]}.php";
	}
	exit;
}

$fp = fopen("../conf/engine/{$_GET[mode]}.php","w");

if($_GET[mode] == "danawa_new"){
	$_GET[mode] = "danawa";
	$mode2 = "new";
}

if(file_exists('../conf/engine/danawa.php') && $mode2 == "new"){
	$updatetime =  date('Y-m-d h:i:s',filectime ( '../conf/engine/danawa.php'));
	$where = "and regdt >= '$updatetime'";
}


$sql = "
				select gg.goodsno,
						gl.category,
						gg.goodsnm,
						gg.maker,
						gg.img_m,
						ggo.price,
						ggo.reserve,
						gg.goodscd,
						gg.shortdesc,
						gg.regdt,
						gg.delivery_type,
						gg.goods_delivery,
						gg.sales_range_start,
						gg.sales_range_end,
						gg.extra_info,
						gg.usestock,
						ggo.stock,
						gg.runout,
						gg.use_emoney

							from ".GD_GOODS." gg
								, (select goodsno, ".getCategoryLinkQuery('category', null, 'max')." from ".GD_GOODS_LINK." c group by c.goodsno) gl
									, ".GD_GOODS_OPTION." ggo
										where gg.open='1'
										and gg.runout=0
										and gg.goodsno=gl.goodsno
										and gg.goodsno=ggo.goodsno
										and ggo.link
										and go_is_deleted <> '1' and go_is_display = '1'
										$where
										order by gg.goodsno desc";


$res = $db->query($sql);
$tcnt = $db->count_($res);

fwrite($fp,'<?'.chr(10));
fwrite($fp,'header("Cache-Control: no-cache, must-revalidate");'.chr(10));
fwrite($fp,'header("Content-Type: text/plain; charset=euc-kr");'.chr(10));
fwrite($fp,'?>'.chr(10));
echo "<script>parent.document.getElementById('progressBar').style.width = '0';</script>";

$goodsModel = Clib_Application::getModelClass('goods');

switch ($_GET[mode]){
 case "danawa" :

	while($row = $db->fetch($res)){

		// �Ǹ� ����(�Ⱓ �� ����)�� ��� ����
		if (! $goodsModel->setData($row)->canSales()) continue;

		$goods_url = "http://{$_SERVER['HTTP_HOST']}{$cfg[rootDir]}/goods/goods_view.php?goodsno=".$row[goodsno]."&category=".$row[category]."&inflow=".$danawa[gubun];

		unset($img_arr);
		$img_arr = explode("|",$row['img_m']);
		$img_url = img_url($url,$img_arr[0]);

		### ��۷�
		$param = array(
			'mode' => '1',
			'deliPoli' => '0',
			'price' => $row[price],
			'goodsno' => $row[goodsno],
			'goods_delivery' => $row[goods_delivery],
			'delivery_type' => $row[delivery_type]
		);
		$tmp = getDeliveryMode($param);
		$d_price = $tmp['price'];

		include_once dirname(__FILE__).'/../lib/json.class.php';
		$json = new Services_JSON(SERVICES_JSON_LOOSE_TYPE);
		$extra_info = $json->decode(stripslashes($row['extra_info']));

		$isRequireCoupon = '';	//�����ٿ�ε� �ʿ俩��
		$dlvDesc = '';	//�����ۺ� ����
		$isDlv = '';	//�����ۺ� ����
		$isAddPrice = '';	//���� ��ġ�� ����
		$isRunout = ''; //�������
		$addPriceDesc = ''; //�߰���ġ��� ����
		$couponTxt = ''; //��������

		if(is_array($extra_info)){
			foreach($extra_info as $key=>$val) {
				if($val['title'] == '��� �� ��ġ���'){
					$dlvDesc = $val['desc'];
				}
				if($val['title'] == '�߰���ġ���'){
					$addPriceDesc = $val['desc'];
				}
			}
		}
		if($dlvDesc) {
			$isDlv = 'Y';
		}
		if($addPriceDesc) {
			$isAddPrice = 'Y';
		}
		### ����� ���� �ڵ� ǰ�� ó��
		if (($row['usestock'] && $row['stock']==0) || $row['runout']){
			$isRunout = "N";
		}
		else{
			$isRunout = "Y";
		}

		### �Ｎ��������
		$coupon = 0;
		if($cfgCoupon['use_yn']){
			list($row[coupon],$row[coupon_emoney]) = getCouponInfo($row[goodsno],$row[price]);
			$row[reserve] += $row[coupon_emoney];
			if($row[coupon])$coupon = getDcprice($row[price],$row[coupon]);
		}

		### ������
		if($row['use_emoney']=='0')
		{
			if( !$set['emoney']['chk_goods_emoney'] ){
				if(!$set['emoney']['goods_emoney']) {
					$set['emoney']['goods_emoney'] = 0;
				}

				$dc=$set['emoney']['goods_emoney']."%";
				$tmp_price = $row['price'];
				if( $set['emoney']['cut'] ) $po = pow(10,$set['emoney']['cut']);
				else $po = 100;
				$tmp_price = (substr($dc,-1)=="%") ? $tmp_price * substr($dc,0,-1) / 100 : $dc;
				$row['reserve'] = floor($tmp_price / $po) * $po;

			}else{
				$row['reserve'] = $set['emoney']['goods_emoney'];
			}
		}

		### ���� ȸ������ �ߺ� ���� üũ
		if($coupon>0 && $dcprice>0){
			if($cfgCoupon['range'] == 2)$dcprice=0;
			if($cfgCoupon['range'] == 1)$coupon=0;
		}

		### ���� ����
		$coupon += 0;
		$dcprice += 0;
		$price = $row['price'] - $coupon;	//�������αݾ��� ������ ��ǰ����
		$mobilePrice = $price;	//����� ��ǰ����

		if($coupon>0) {
			$isRequireCoupon = 'Y';
			$couponTxt = number_format($coupon).'�� �������� ����';
		}

		$clen = strlen($row[category]);
		unset($cate_f);
		for($i=1;$i<=$clen/3;$i++){
			if(strlen(substr($row[category],0,3*$i)) == 3*$i) $cate_f .= getCatename(substr($row[category],0,3*$i))."|";
			else break;
		}
		if($cate_f)$cate_f = substr($cate_f,0,-1);
		$row[goodsnm] = strip_tags($row[goodsnm]);

		$row[regdt] = str_replace('-','',substr($row[regdt],0,10));
		$row[shortdesc] = str_replace(array(chr(13),chr(10)),'',$row[shortdesc]);
		$row[shortdesc] = strip_tags($row[shortdesc]);
		fwrite($fp,$row[goodsno].'^'.$cate_f.'^'.$row[goodsnm].'^'.$row[maker].'^'.$img_url.'^'.$goods_url.'^'.$price.'^'.$row[reserve].'^'.$couponTxt.'^'.$card[cardfree].'^'.'^'.$row[goodscd].'^'.$row[shortdesc].'^'.$row[regdt].'^'.$d_price.'^^^'.$isRequireCoupon.'^'.$mobilePrice.'^'.$isDlv.'^'.$dlvDesc.'^'.$isAddPrice.'^'.$isRunout.chr(10));

		$k++;
		$pwi = $k / $tcnt * 100;
		echo "<script>parent.document.getElementById('progressBar').style.width = '{$pwi}%';</script>";
	}
	break;
 case "mm" :

	fwrite($fp,'<table border=1>'.chr(10));
	fwrite($fp,'<tr>'.chr(10));
	fwrite($fp,'<td>No</td>'.chr(10));
	fwrite($fp,'<td>��ǰ�ڵ� </td>'.chr(10));
	fwrite($fp,'<td>��ǰ�� </td>'.chr(10));
	fwrite($fp,'<td>��ǰ���� </td>'.chr(10));
	fwrite($fp,'<td>��ǰ�з� </td>'.chr(10));
	fwrite($fp,'<td>������ </td>'.chr(10));
	fwrite($fp,'<td> �̹��� </td>'.chr(10));
	fwrite($fp,'</tr>'.chr(10));
	$k=0;
	while($row = $db->fetch($res)){

		// �Ǹ� ����(�Ⱓ �� ����)�� ��� ����
		if (! $goodsModel->setData($row)->canSales()) continue;

		$goods_url = "http://{$_SERVER['HTTP_HOST']}{$cfg[rootDir]}/goods/goods_view.php?goodsno=".$row[goodsno]."&category=".$row[category]."&inflow=".$mm[gubun];

		unset($img_arr);
		$img_arr = explode("|",$row['img_m']);
		$img_url = img_url($url,$img_arr[0]);

		$clen = strlen($row[category]);
		unset($cate_f);
		for($i=1;$i<=$clen/3;$i++){
			if(strlen(substr($row[category],0,3*$i)) == 3*$i) $cate_f .= getCatename(substr($row[category],0,3*$i))."/";
			else break;
		}
		if($cate_f)$cate_f = substr($cate_f,0,-1);
		$row[goodsnm] = strip_tags($row[goodsnm]);
		if(!$row[goodscd])$row[goodscd] = $row[goodsno];
		if(!$row[maker])$row[maker] = $row[brand];

		fwrite($fp,'<tr>'.chr(10));
		fwrite($fp,'<td> '.$k.' </td>'.chr(10));
		fwrite($fp,'<td>'.$row[goodsno].'</td>'.chr(10));
		fwrite($fp,'<td><a href="'.$goods_url.'">'.$row[goodsnm].'</a></td>'.chr(10));
		fwrite($fp,'<td>'.$row[price].'</td>'.chr(10));
		fwrite($fp,'<td>'.$cate_f.'</td>'.chr(10));
		fwrite($fp,'<td>'.$row[maker].'</td>'.chr(10));
		fwrite($fp,'<td>'.$img_url.'</td>'.chr(10));
		fwrite($fp,'</tr>'.chr(10));
		$k++;


		$pwi = $k / $tcnt * 100;
		echo "<script>parent.document.getElementById('progressBar').style.width = '{$pwi}%';</script>";
	}
	fwrite($fp,'</table>'.chr(10));
	break;
 case "omi" :

 	fwrite($fp,"<html>".chr(10));
 	fwrite($fp,"<head>".chr(10));
 	fwrite($fp,'<title>����������</title>'.chr(10));
	fwrite($fp,'</head>'.chr(10));
	fwrite($fp,'<style>'.chr(10));
 	fwrite($fp,'body {font-size:9pt; font-family:"����"; text-decoration: none; line-height: 13pt; color:	#333333}'.chr(10));
	fwrite($fp,'</style>'.chr(10));
	fwrite($fp,'</head>'.chr(10));
	fwrite($fp,'<body topmargin="0" leftmargin="0">'.chr(10));

	while($row = $db->fetch($res)) {

		// �Ǹ� ����(�Ⱓ �� ����)�� ��� ����
		if (! $goodsModel->setData($row)->canSales()) continue;

		//ī�װ�
		$clen = strlen($row[category]);
		unset($cate_f);
		for($i=1;$i<=3;$i++){
			if(strlen(substr($row[category],0,3*$i)) == 3*$i)	$cate_f .= getCatename(substr($row[category],0,3*$i))."^";
			else $cate_f .= "^";
		}
		$cate_f = substr($cate_f,0,-1);
		unset($img_arr);
		$img_arr = explode("|",$row['img_m']);
		$img_url = img_url($url,$img_arr[0]);
		$goods_url = "http://{$_SERVER['HTTP_HOST']}{$cfg[rootDir]}/goods/goods_view.php?goodsno=".$row[goodsno]."&category=".$row[category]."&inflow=".$omi[gubun];

		### ��۷�
		$param = array(
			'mode' => '1',
			'deliPoli' => 0,
			'price' => $row[price],
			'goodsno' => $row[goodsno],
			'goods_delivery' => $row[goods_delivery],
			'delivery_type' => $row[delivery_type]
		);
		$tmp = getDeliveryMode($param);
		$d_price = $tmp['price']+0;

		// ���� ����
		list($row[coupon],$row[coupon_emoney]) = getCouponInfo($row[goodsno],$row[price]);
		$row[reserve] += $row[coupon_emoney];
		if($row[coupon]){
			$couponStr = "&" . $row[coupon];
		}else{
			$couponStr = "";
		}

		$goods_name = strip_tags($row[goodsnm]);
		fwrite($fp,"<p>id=".$row[goodsno]."^".$cate_f."^".$row[maker]."^".$goods_name."^".$goods_url."^".$row[price].$couponStr."^".$d_price."^".$img_url."^^</p>".chr(10));
		$k++;

		$pwi = $k / $tcnt * 100;
		echo $k ."/" .$tcnt."<br>";
		echo "<script>parent.document.getElementById('progressBar').style.width = '{$pwi}%';</script>";
		flush();
	}
	fwrite($fp,"</body>".chr(10));
	fwrite($fp,"</html>".chr(10));


	break;

	case "yahoo" :
		while($row = $db->fetch($res)) {

			// �Ǹ� ����(�Ⱓ �� ����)�� ��� ����
			if (! $goodsModel->setData($row)->canSales()) continue;

			//ī�װ�
			$clen = strlen($row[category]);
			unset($cate_f);
			for($i=1;$i<=3;$i++){
				if(strlen(substr($row[category],0,3*$i)) == 3*$i) $cate_f .= getCatename(substr($row[category],0,3*$i))."%";
				else break;
			}
			if($cate_f)$cate_f = substr($cate_f,0,-1);
			unset($img_arr);
			$img_arr = explode("|",$row['img_m']);
			$img_url = img_url($url,$img_arr[0]);
			$goods_url = "http://{$_SERVER['HTTP_HOST']}{$cfg[rootDir]}/goods/goods_view.php?goodsno=".$row[goodsno]."&category=".$row[category]."&inflow=".${$_GET[mode]}[gubun];

			### ��۷�
			$param = array(
				'mode' => '1',
				'deliPoli' => 0,
				'price' => $row[price],
				'goodsno' => $row[goodsno],
				'goods_delivery' => $row[goods_delivery],
				'delivery_type' => $row[delivery_type]
			);
			$tmp = getDeliveryMode($param);
			$d_price=0;
			if($tmp['free'] && $tmp['price']) $d_price = $tmp['free']."%".$tmp['price'];
			else $d_price = $tmp['price']+0;

			// ���� ����
			list($row[coupon],$row[coupon_emoney]) = getCouponInfo($row[goodsno],$row[price]);
			$row[reserve] += $row[coupon_emoney];

			if($row[coupon])$coupon_str = $row[coupon]." ���� ����";
			else $coupon_str = "";

			//�귣��
			list($row[brandnm]) =  $db->fetch("select brandnm from ".GD_GOODS_BRAND." where sno='$row[brandno]' limit 1");
			$goods_name = strip_tags($row[goodsnm]);
			$specials = Array('{', '}', '[', ']', '?', '-', '~', '!', '@', '#', '$', '%', '^', '&', '*', '(', ')', '_', '+', '|', '\\', '=', '/', ';', ':', '\'', '"', '`', ',', '.', '<', '>');
    		$goods_name = strcut(str_replace($specials, '', $goods_name),255);

			//�̺�Ʈ
			$date = date("Ymd");
			$query = "select z.subject from
									".GD_EVENT." z left join ".GD_GOODS_DISPLAY." a on z.sno=substring(a.mode,2) and substring(a.mode,1,1) = 'e'
									left join ".GD_GOODS." b on a.goodsno=b.goodsno
									left join ".GD_GOODS_OPTION." c on a.goodsno=c.goodsno and go_is_deleted <> '1' and go_is_display = '1'
									where link and a.goodsno='$row[goodsno]' and z.sdate <= '$date' and z.edate >= '$date' limit 1";
			list($event) = $db->fetch($query);

			fwrite($fp,"<%start>".chr(10));
			fwrite($fp,"<%code>".$row[goodsno].chr(10));
			fwrite($fp,"<%product>".$goods_name.chr(10));
			fwrite($fp,"<%price>".$row[price].chr(10));
			fwrite($fp,"<%url>".$goods_url.chr(10));
			fwrite($fp,"<%imgurl>".$img_url.chr(10));
			fwrite($fp,"<%cate>".$cate_f.chr(10));
			fwrite($fp,"<%model>".$row[goodscd].chr(10));
			fwrite($fp,"<%brand>".$row[brandnm].chr(10));
			fwrite($fp,"<%comp>".$row[maker].chr(10));
			fwrite($fp,"<%date>".str_replace('-','',substr($row[regdt],0,10)).chr(10));
			fwrite($fp,"<%event>".strcut($event,255).chr(10));
			fwrite($fp,"<%card>".$yahoo[cardfree].chr(10));
			fwrite($fp,"<%point>".$row[reserve].chr(10));
			fwrite($fp,"<%coupon>".$coupon_str.chr(10));
			fwrite($fp,"<%dprice>".$d_price.chr(10));
			fwrite($fp,"<%end>".chr(10));
			$k++;

			$pwi = $k / $tcnt * 100;
			echo $k ."/" .$tcnt."<br>";
			echo "<script>parent.document.getElementById('progressBar').style.width = '{$pwi}%';</script>";
			flush();
		}
	break;

	case "naver_elec"||"naver_bea"||"naver_milk" :
		while($row = $db->fetch($res)) {

			// �Ǹ� ����(�Ⱓ �� ����)�� ��� ����
			if (! $goodsModel->setData($row)->canSales()) continue;

			//ī�װ�
			$clen = strlen($row[category]);
			unset($cate_f);
			for($i=1;$i<=3;$i++){
				if(strlen(substr($row[category],0,3*$i)) == 3*$i) $cate_f .= getCatename(substr($row[category],0,3*$i))."@";
				else break;
			}
			if($cate_f)$cate_f = substr($cate_f,0,-1);
			unset($img_arr);
			$img_arr = explode("|",$row['img_m']);
			$img_url = img_url($url,$img_arr[0]);
			$goods_url = "http://{$_SERVER['HTTP_HOST']}{$cfg[rootDir]}/goods/goods_view.php?goodsno=".$row[goodsno]."&category=".$row[category]."&inflow=".${$_GET[mode]}[gubun];

			### ��۷�
			$param = array(
				'mode' => '1',
				'deliPoli' => 0,
				'price' => $row[price],
				'goodsno' => $row[goodsno],
				'goods_delivery' => $row[goods_delivery],
				'delivery_type' => $row[delivery_type]
			);
			$tmp = getDeliveryMode($param);
			$d_price=0;
			$d_price = $tmp['price']+0;
			if($d_price != 0)$d_price .= "��";

			// ���� ����
			list($row[coupon],$row[coupon_emoney]) = getCouponInfo($row[goodsno],$row[price]);
			$row[reserve] += $row[coupon_emoney];
			if($row[coupon]){
				$couponStr = "&" . $row[coupon];
			}else{
				$couponStr = "";
			}

			$goods_name = strip_tags($row[goodsnm]);

			fwrite($fp,"<<begin>>".chr(10));
			fwrite($fp,"<<��ǰID>>".$row[goodsno].chr(10));
			fwrite($fp,"<<�з�>>".$cate_f.chr(10));
			fwrite($fp,"<<��ǰ��>>".$goods_name.chr(10));
			fwrite($fp,"<<�𵨸�>>".$row[goodscd].chr(10));
			fwrite($fp,"<<�������>>".chr(10));
			fwrite($fp,"<<����ȸ��>>".$row[maker].chr(10));
			fwrite($fp,"<<����>>".$row[price].chr(10));
			fwrite($fp,"<<��ǰURL>>".$goods_url.chr(10));
			fwrite($fp,"<<����Ʈ>>".$row[reserve].chr(10));
			fwrite($fp,"<<��۷�>>".$d_price.chr(10));
			fwrite($fp,"<<�̺�Ʈ>>".$card[cardfree].chr(10));
			fwrite($fp,"<<end>>".chr(10));
			$k++;

			$pwi = $k / $tcnt * 100;
			echo $k ."/" .$tcnt."<br>";
			echo "<script>parent.document.getElementById('progressBar').style.width = '{$pwi}%';</script>";
			flush();
		}
	break;


}
fclose($fp);
chmod('../conf/engine/'.$_GET[mode].'.php',0707);

if(!$_GET[allmode]){
	msg("������Ʈ �Ϸ�!");
	echo "<script>parent.location.reload();</script>";
}else{
	if($tmp_mode){
		go($PHP_SELF."?allmode=".$_GET[allmode]);
	}
}
?>
