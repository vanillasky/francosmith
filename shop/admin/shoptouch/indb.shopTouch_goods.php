<?

include "../lib.php";
require_once("../../lib/upload.lib.php");
require_once("../../lib/load.class.php");
require_once("../../lib/qrcode.class.php");
@include_once "../../conf/config.mobileShop.php";
@include_once "../../conf/config.purchase.php";
@include "../../lib/pAPI.class.php";
@include "../../lib/json.class.php";

$pAPI = new pAPI();
$json = new Services_JSON(16);

$Goods = Core::loader('Goods');

include "indb.shopTouch_goods_fashion.php";

$goodsno = $_POST[goodsno];
$upload = new upload_file;
function chk_goods_img($type){
	global $upload;
	if($_FILES[$type]){
		$file_array = array();
		$file_array = reverse_file_array($_FILES[$type]);
		foreach($file_array as $k => $v){
			$upload->upload_file($file_array[$k],'','image');
			if(!$upload->file_extension_check())return false;
			if(!$upload->file_type_check())return false;
		}
	}
	return true;
}


if ($_POST[mode]=="register"){

	# 등록수 제한 체크
	list ($cntGoods) = $db->fetch("select count(*) from ".GD_GOODS."");
	if ($godo[maxGoods]!="unlimited" && $godo[maxGoods]<=$cntGoods){
		msg("상품수 등록이 제한되었습니다",-1);
		exit;
	}

	$db->query("insert into ".GD_GOODS." set regdt	= now()");
	$goodsno = $db->lastID();

	### shoptouch 상품 정보 insert ###
	$ins_query_shoptouch = $db->_query_print('INSERT INTO '.GD_SHOPTOUCH_GOODS.' SET goodsno=[i], sregdt=now()', $goodsno);
	$db->query($ins_query_shoptouch);

	if ($_POST[category]) foreach ($_POST[category] as $v){
		$hidden = getCateHideCnt($v) > 0 ? 1 : 0;
		$db->query("insert into ".GD_GOODS_LINK." set goodsno='$goodsno',category='$v',hidden='$hidden',sort=-unix_timestamp()");
	}
	$referer = $_SERVER[HTTP_REFERER]."?mode=modify&goodsno=$goodsno";

} else if ($_POST[mode]=="modify"){
	### 옵션이미지 업로드
	list($data[opt1img], $data[opt1icon], $data[opt2icon]) = upload_optimg();

	### 정렬순서 최상단 세팅
	if ($_POST[sortTop]) $_POST[sort] = array_map("sortTop",$_POST[sort]);

	### 카테고리 수정
	$p_category = $n_category = array();

	$n_category = $_POST[category];
	$query = "select * from ".GD_GOODS_LINK." where goodsno='$goodsno'";
	$res = $db->query($query);
	while ($data=$db->fetch($res)) $p_category[] = $data[category];

	$add = @array_diff($n_category,$p_category);
	$del = @array_diff($p_category,$n_category);
	$mod = @array_diff($n_category,$add);

	if ($add) foreach ($add as $k=>$v){
		$hidden = getCateHideCnt($v) > 0 ? 1 : 0;
		$db->query("insert into ".GD_GOODS_LINK." set goodsno='$goodsno',category='$v',hidden='$hidden',sort='-{$_POST[sort][$k]}'");
	}
	if ($del) foreach ($del as $v) $db->query("delete from ".GD_GOODS_LINK." where goodsno='$goodsno' and category='$v'");
	if ($mod) foreach ($mod as $k=>$v) $db->query("update ".GD_GOODS_LINK." set sort='-{$_POST[sort][$k]}' where goodsno='$goodsno' and category='$v'");

	$data = $db->fetch("select * from ".GD_GOODS." where goodsno='$goodsno'");
}

$db->query("delete from ".GD_QRCODE." where qr_type='goods' and contsNo=$goodsno");
if($_POST['qrcode'] == 'y'){
	$db->query("insert into ".GD_QRCODE." set  qr_type='goods' ,contsNo=".$goodsno." ,qr_string = '', qr_name = 'event qr code', qr_size='', useLogo = '', regdt	= now()");
}

$_POST['img_shoptouch'] = Array();

### 이미지 처리 ###
if ($_POST['del']['img_shoptouch'][$i]){
	for ($i = 0; $i<count($_POST['del']['img_shoptouch'][$i]); $i++) {
		### 클라우드 컨텐츠 삭제 API 들어가야 함
		$_POST['img_shoptouch_old'][$i] = '';
	}
}


### 이미지 등록 ###
## 쇼핑몰 App 이미지 등록 ##
for($i =0; $i<count($_FILES['img_shoptouch']['tmp_name']); $i++) {
	$arr = Array();

	$_dir	= "../../data/shoptouch/tmp_goods";
	if (!is_dir($_dir)) {
		@mkdir($_dir);
		@chmod($_dir, 0707);
	}
	if(is_uploaded_file($_FILES['img_shoptouch']['tmp_name'][$i])) {

		$div = explode(".",$_FILES['img_shoptouch'][name][$i]);
		$tmp_img_nm = date('Ymdhis').$i.'.'.$div[count($div)-1];

		move_uploaded_file($_FILES['img_shoptouch']['tmp_name'][$i],$_dir.'/'.$tmp_img_nm);
		@chmod($_dir.'/'.$tmp_img_nm,0707); // 업로드된 파일 권한 변경

		$tmp_img_url = 'http://'.$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/data/shoptouch/tmp_goods/'.$tmp_img_nm;

		$arr['img_url'] = $tmp_img_url;

		$tmp_ret = $pAPI->contentsUpload($godo['sno'], $arr);

		$ret = $json->decode($tmp_ret);
		if($ret['result']['code'] == '000') {
			$_POST['img_shoptouch_old'][$i] = $ret['img_shoptouch'];
			$arr_tmp_img[$i] = $tmp_img_url;
			$arr_del_nm[$i] = $_dir.'/'.$tmp_img_nm;
		}

		if($_POST['mode'] != 'register') {
			@unlink($_dir.'/'.$tmp_img_nm);
		}
	}
	unset($arr);
}
$_POST['img_shoptouch'] = $_POST['img_shoptouch_old'];

if($_POST['mode'] == 'register') {
## 이나무 이미지 등록 ##
	if(!empty($arr_tmp_img) && is_array($arr_tmp_img)) {

		foreach($arr_tmp_img as $img_shoptouch) {

			$img_chk = getimagesize($img_shoptouch);

			if($img_chk[2]) {

				### 이미지 저장 ###
				$url_stuff = parse_url($img_shoptouch);
				$port = isset($url_stuff['port']) ? $url_stuff['port'] : 80;

				$fp = fsockopen($url_stuff['host'], $port);

				$query  = 'GET ' . $url_stuff['path'] . " HTTP/1.0\n";
				$query .= 'Host: ' . $url_stuff['host'];
				$query .= "\n\n";

				@fwrite($fp, $query);

				while ($tmp = fread($fp, 1024))
				{
					$buffer .= $tmp;
				}

				preg_match('/Content-Length: ([0-9]+)/', $buffer, $parts);

				$img_content = substr($buffer, - $parts[1]);
				@fclose($fp);

				$ext_idx = strrpos($img_shoptouch, '.');
				$ext = '';
				if ($ext_idx !== false) $ext = '.'.substr($img_url, $ext_idx + 1);

				### 이미지 확장자가 없을 경우 이미지 타입으로 확장자 입력
				if(strtoLower($ext) != '.gif' || strtoLower($ext) != '.jpg' || strtoLower($ext) != '.jpeg' || strtoLower($ext) != '.png' || strtoLower($ext) != '.bmp') {
					$arr_img_type = getimagesize($img_shoptouch);

					switch ($arr_img_type[2]) {
						case 1 :
							$ext = '.gif';
							break;
						case 2 :
							$ext = '.jpg';
							break;
						case 3 :
							$ext = '.png';
							break;
						case 6 :
							$ext = '.bmp';
							break;
						default :
							$ext = '';
							break;
					}
				}

				### 이미지 확장자가 없을 경우 이미지 비정상 처리
				if(!$ext) {
					$img_yn='N';
				}

				$img_path = '../../data/goods/';
				if (!is_dir($img_path)) {
					@mkdir($img_path);
					@chmod($img_path, 0707);
				}

				$_POST['img_i'] = time().'i0'.$ext;
				$img_i = $img_path.$_POST['img_i'];

				$w_fh = @fopen($img_i, 'w');
				@fwrite($w_fh, $img_content);
				@chmod($img_i, 0707);
				@fclose($w_fh);

				thumbnail($img_i, $img_path.'t/'.$_POST['img_i'], 45);

				### 이미지 리사이징 ###
				$arr_img_key = Array('img_l', 'img_m', 'img_s', 'img_i');
				foreach($arr_img_key as $val_img_key) {
					if($val_img_key != 'img_i') {
						$_POST[$val_img_key] = time()."_".substr($val_img_key,-1,1)."_0".$ext;
						thumbnail($img_i, $img_path.$_POST[$val_img_key], $cfg[$val_img_key]);

						if($val_img_key != 'img_s') {
							copy($img_path.'t/'.$_POST['img_i'], $img_path.'t/'.$_POST[$val_img_key]);
						}
					}

				}
				break;
			}
		}

		foreach($arr_del_nm as $del_img_nm) {
			@unlink($del_img_nm);

		}

	}
}

### 네이버 지식쇼핑 상품엔진
naver_goods_diff_check();

$ar_naver_diff=array();

$ar_update=array(
	'goodsnm'=>$_POST[goodsnm],
	'brandno'=>$_POST[brandno],
	'origin'=>$_POST[origin],
	'maker'=>$_POST[maker],
	'launchdt'=>date("Y-m-d",strtotime($_POST[launchdt])),
	'delivery_type'=>$_POST[delivery_type],
	'goods_delivery'=>$_POST[goods_delivery],
	'use_emoney'=>$_POST[use_emoney],
	'price'=>$_POST[option][price][0],
	'reserve'=>$_POST[option][reserve][0],
	'img_m'=> ($_POST['image_attach_method'] == 'url') ? $_POST[url_m][0] : $file[img_m][name][0],	// url 직접 입력일때 처리 추가.
);
if($p_category[0]!=$n_category[0]) $ar_update['category']=$n_category[0];

if ($_POST[mode]=="modify"){
	naver_goods_diff($goodsno,$ar_update);
}

### useEx
if($_POST[useEx] == 0) unset($_POST[ex]);

### useAdd
if($_POST[useAdd] == 0) unset($_POST[addoptnm]);

### qrcode defalut
if(!$_POST[qrcode]) $_POST[qrcode] = "n";

### 관련상품
$_POST['relation'] = get_magic_quotes_gpc() ? stripslashes($_POST['relation']) : $_POST['relation'];
$r_relation = gd_json_decode($_POST['relation']);
$relation = '';
if (!empty($r_relation)) {

	$relation = 'new_type';	// 구 데이터와 구분을 위한 값이며, 데이터에 직접적 영향은 없음.

	$db->query(" DELETE FROM ".GD_GOODS_RELATED." WHERE goodsno = $goodsno ");

	foreach ($r_relation as $related) {

			$query = "
			INSERT INTO ".GD_GOODS_RELATED." SET
				goodsno		= '".$goodsno."',
				r_type		= '".$related['r_type']."',
				r_goodsno	= '".$related['goodsno']."',
				r_start		= ".(!empty($related['r_start']) ? "'".$related['r_start']."'" : 'null' ).",
				r_end		= ".(!empty($related['r_end']) ? "'".$related['r_end']."'" : 'null' ).",
				regdt		= '".$related['r_regdt']."'
			";
			$db->query($query);

			// 서로 등록
			if ($related['r_type'] == 'couple') {

				$query = "
				INSERT INTO ".GD_GOODS_RELATED." SET
					goodsno		= '".$related['goodsno']."',
					r_type		= '".$related['r_type']."',
					r_goodsno	= '".$goodsno."',
					r_start		= ".(!empty($related['r_start']) ? "'".$related['r_start']."'" : 'null' ).",
					r_end		= ".(!empty($related['r_end']) ? "'".$related['r_end']."'" : 'null' ).",
					regdt		= '".$related['r_regdt']."'
				";

			}
			else {

				$query = "
				DELETE FROM ".GD_GOODS_RELATED."
				WHERE
					goodsno		= '".$related['goodsno']."'
				AND
					r_goodsno	= '".$goodsno."'

				";
			}

			$db->query($query);
	}

}

/*### 기본가격설정
if (!$_POST[option][price][0]) $_POST[option][price][0] = $_POST[price];
if (!$_POST[option][consumer][0]) $_POST[option][consumer][0] = $_POST[consumer];
if (!$_POST[option][supply][0]) $_POST[option][supply][0] = $_POST[supply];
if (!$_POST[option][reserve][0]) $_POST[option][reserve][0] = $_POST[reserve];
if (!$_POST[option][stock][0]) $_POST[option][stock][0] = $_POST[stock];
*/

### 필수옵션
$optnm = @implode("|",array_notnull($_POST[optnm]));
$idx = -1; $link[0] = 1;
$db->query("delete from ".GD_GOODS_OPTION." where goodsno=$goodsno");
$cnt = count($_POST[opt2]);
$totstock = 0;
foreach ($_POST[option][stock] as $k=>$v){
	$idx++;
	$key = (int)($idx/$cnt);
	$opt1 = str_replace("'","’",$_POST[opt1][$key]);
	$opt2 = str_replace("'","’",$_POST[opt2][$idx%$cnt]);

	if(trim($opt1) == '옵션명1') $opt1='';
	if(trim($opt2) == '옵션명2') $opt2='';
	if(trim($v) == '재고' || trim($v) == '등록 후 재고 입력') $v='';

	$price = trim(str_replace(",","",$_POST[option][price][$key]));
	$consumer = trim(str_replace(",","",$_POST[option][consumer][$key]));
	$supply = trim(str_replace(",","",$_POST[option][supply][$key]));
	$reserve = trim(str_replace(",","",$_POST[option][reserve][$key]));

	if($_POST['opt1kind'] == 'img') $icon1 = $file['opticon_a']['name'];
	else	$icon1 = $_POST['opt1icon'];
	if($_POST['opt2kind'] == 'img') $icon2 = $file['opticon_b']['name'];
	else	$icon2 = $_POST['opt2icon'];

	$query = "
	insert into ".GD_GOODS_OPTION." set
		goodsno = '$goodsno',
		opt1	= '$opt1',
		opt2	= '$opt2',
		price	= '$price',
		consumer= '$consumer',
		supply	= '$supply',
		reserve	= '$reserve',
		stock	= '$v',
		opt1img	= '".$file['opt1img']['name'][$key]."',
		opt1icon = '".$icon1[$key]."',
		opt2icon = '".$icon2[$idx%$cnt]."',
		link	= '$link[$idx]',
		pchsno	= '".$_POST['pchsno']."'
	";
	$db->query($query);
	$totstock += $v;

	# 옵션코드
	ob_start();
	$sno = $db->lastID();
	if ($_POST[option][optno][$k] == '') $optno = $sno;
	else $optno = $_POST[option][optno][$k];
	$db->query("update ".GD_GOODS_OPTION." set optno='{$optno}' where sno='{$sno}'");
	ob_end_clean();
}

### 추가옵션
$idx = 0;
$addoptnm = array_notnull($_POST[addoptnm]);
$db->query("UPDATE ".GD_GOODS_ADD." SET stats = 0 WHERE goodsno = $goodsno");
if ($addoptnm){
	foreach ($addoptnm as $k=>$v){
		$isInsert = 0;
		$_addopt_keys = array_keys($_POST[addopt][opt][$k]);

		for ($i=0,$m=sizeof($_addopt_keys);$i<$m;$i++) {

			$_opt		= isset($_POST[addopt][opt][$k][$i]) ? $_POST[addopt][opt][$k][$i] : '';
			$_sno		= isset($_POST[addopt][sno][$k][$i]) ? $_POST[addopt][sno][$k][$i] : '';
			$_addprice	= isset($_POST[addopt][addprice][$k][$i]) ? $_POST[addopt][addprice][$k][$i] : '';

			if ($_opt == '') continue;

			// 수정
			if ($_sno) {
				$query = "
				UPDATE ".GD_GOODS_ADD." SET
					step	= '$idx', opt		= '$_opt', addprice= '$_addprice', stats	= 1
				WHERE sno = $_sno
				";
			}
			// 등록
			else {
				$query = "
				INSERT INTO ".GD_GOODS_ADD." SET
					goodsno	= '$goodsno', step	= '$idx', opt		= '$_opt', addprice= '$_addprice', stats	= 1
				";
			}
			$rs = $db->query($query);
			if ($rs) $isInsert = 1;
		}

		if ($isInsert){
			$tmp[] = $v."^".$_POST[addoptreq][$k];
			$idx++;
		}

	}
}
$db->query("DELETE FROM ".GD_GOODS_ADD." WHERE stats = 0 AND goodsno = $goodsno");
$addoptnm = @implode("|",$tmp);

$icon = @array_sum($_POST[icon]);

### 추가필드
if($_POST['useEx']) $ex_title = preg_replace("/^(\|)+$/i","",@implode("|",$_POST[title]));

### 공백제거
$_POST[goodsnm] = trim($_POST[goodsnm]);

### mysql 5.0 입력값으면 0000-00-00으로만 들어가는문제
if($_POST[launchdt]) $_POST[launchdt] = "'$_POST[launchdt]'";
else $_POST[launchdt] = "null";

###
if($_POST[delivery_type] == '3') $_POST[goods_delivery] = $_POST[goods_delivery2];

### 옵션아이콘 및 옵션상품이미지 삭제(폼이없어졌을경우)
deloptimg();

if($cfgMobileShop['vtype_goods']=='0') $_POST[open_mobile] = $_POST[open];

### 상품 데이타 수정
$query = "
update ".GD_GOODS." set
	goodsnm			= '$_POST[goodsnm]',
	meta_title		= '$_POST[meta_title]',
	goodscd			= '$_POST[goodscd]',
	maker			= '$_POST[maker]',
	origin			= '$_POST[origin]',
	brandno			= '$_POST[brandno]',
	icon			= '$icon',
	open			= '$_POST[open]',
	open_mobile		= '$_POST[open_mobile]',
	runout			= '$_POST[runout]',
	delivery_type	= '$_POST[delivery_type]',
	goods_delivery	= '$_POST[goods_delivery]',
	keyword			= '$_POST[keyword]',
	strprice		= '$_POST[strprice]',
	tax				= '$_POST[tax]',
	shortdesc		= '$_POST[shortdesc]',
	longdesc		= '$_POST[slongdesc]',
	mlongdesc		= '$_POST[mlongdesc]',
	img_i			= '$_POST[img_i]',
	img_s			= '$_POST[img_s]',
	img_m			= '$_POST[img_m]',
	img_l			= '$_POST[img_l]',
	img_mobile		= '$_POST[img_mobile]',
	optnm			= '$optnm',
	opttype			= '$_POST[opttype]',
	opt1kind			= '$_POST[opt1kind]',
	opt2kind			= '$_POST[opt2kind]',
	use_emoney		= '$_POST[use_emoney]',
	addoptnm		= '$addoptnm',
	memo			= '$_POST[memo]',
	ex_title		= '$ex_title',
	ex1				= '{$_POST[ex][0]}',
	ex2				= '{$_POST[ex][1]}',
	ex3				= '{$_POST[ex][2]}',
	ex4				= '{$_POST[ex][3]}',
	ex5				= '{$_POST[ex][4]}',
	ex6				= '{$_POST[ex][5]}',
	relationis		= '$_POST[relationis]',
	relation		= '$relation',
	usestock		= '$_POST[usestock]',
	totstock		= '$totstock',
	launchdt		= $_POST[launchdt],
	min_ea			= '$_POST[min_ea]',
	max_ea			= '$_POST[max_ea]',
	useblog			= '$_POST[useblog]',
	detailView		= '$_POST[detailView]',
	use_stocked_noti= '$_POST[use_stocked_noti]'
where
	goodsno = '$goodsno'
";
$db->query($query);


### 업데이트 일시
$Goods -> update_date($goodsno);

### 쇼핑몰 App 데이타 수정
$query = "
update ".GD_SHOPTOUCH_GOODS." set
	open_shoptouch	= '$_POST[open_shoptouch]',
	img_shoptouch	= '".@implode("|",$_POST[img_shoptouch])."',
	slongdesc		= '$_POST[slongdesc]',
	supddt = now()
where
	goodsno = '$goodsno'
";
$db->query($query);

### 사입처 관련 - 등록시만 적용
if($_POST['mode'] == "register" && $purchaseSet['usePurchase'] == "Y") {
	list($chkOneOption) = $db->fetch("SELECT COUNT(sno) FROM ".GD_GOODS_OPTION." WHERE goodsno = '$goodsno'");
	if($_POST['pchsno'] && $_POST['pchs_stock'] && $_POST['pchs_pchsdt'] && $chkOneOption == 1) {
		$_POST['pchs_pchsdt'] = substr($_POST['pchs_pchsdt'], 0, 4)."-".substr($_POST['pchs_pchsdt'], 4, 2)."-".substr($_POST['pchs_pchsdt'], 6, 2);
		list($tempData['goodsnm'], $tempData['img_s']) = $db->fetch("SELECT goodsnm, img_s FROM ".GD_GOODS." WHERE goodsno = '$goodsno'");

		$db->query("UPDATE ".GD_GOODS_OPTION." SET stock = stock + ".$_POST['pchs_stock'].",pchsno = '".$_POST['pchsno']."' WHERE goodsno = '$goodsno'");
		list($CurTotStock) = $db->fetch("SELECT SUM(stock) FROM ".GD_GOODS_OPTION." WHERE goodsno = '$goodsno'");
		$db->query("UPDATE ".GD_GOODS." SET totstock = totstock + ".$_POST['pchs_stock']." WHERE goodsno = '$goodsno'");
		$db->query("INSERT INTO ".GD_PURCHASE_GOODS." SET goodsno = '$goodsno', goodsnm = '".$tempData['goodsnm']."', img_s = '".$tempData['img_s']."', pchsno = '".$_POST['pchsno']."', p_stock = '".$_POST['pchs_stock']."', p_price = '".$_POST['supply']."', pchsdt = '".$_POST['pchs_pchsdt']."', regdt = NOW()");
		$db->query("DELETE FROM ".GD_PURCHASE_SMSLOG." WHERE goodsno = '$goodsno'");
	}

	if($_POST['purchaseApplyOption'] == "1") {
		$sql = "SELECT * FROM ".GD_GOODS_OPTION." WHERE goodsno = '$goodsno'";
		$rs = $db->query($sql);
		for($i = 0; $data = $db->fetch($rs); $i++) {
			list($data['goodsnm'], $data['img_s']) = $db->fetch("SELECT goodsnm, img_s FROM ".GD_GOODS." WHERE goodsno = '$goodsno'");
			$sql_pchs = "INSERT INTO ".GD_PURCHASE_GOODS." SET goodsno = '$goodsno', goodsnm = '".$data['goodsnm']."', img_s = '".$data['img_s']."', opt1 = '".$data['opt1']."', opt2 = '".$data['opt2']."', pchsno = '".$_POST['pchsno']."', p_stock = '".$data['stock']."', p_price = '".$data['price']."', pchsdt = '".$_POST['pchs_pchsdt']."', regdt = NOW()";
			$db->query($sql_pchs);
		}
	}
	else if($_POST['purchaseApplyOption'] == "2") {
		msg("상품이 등록되었습니다.\\n입고 상품 등록 페이지로 이동합니다.");
		$_POST['returnUrl'] = "../goods/purchase_goods.php?skey=G.goodsno&sword=$goodsno&pchsDefType=comnm&pchsDefVal=미등록";
	}
}

### 네이버 지식쇼핑 상품엔진
if($_POST[mode]=="register")
{
	naver_goods_diff($goodsno,array(),"I");
}

### 계정용량 계산
setDu('goods');

### 이벤트 카테고리 연결
$res = $db->query("select b.* from ".GD_GOODS_LINK." a, ".GD_EVENT." b where a.category=b.category and a.goodsno='$goodsno'");
$i=0;
while($tmp = $db->fetch($res)){
	$mode = "e".$tmp['sno'];
	list($cnt) = $db->fetch("select count(*) from ".GD_GOODS_DISPLAY." where mode = '$mode' and goodsno='$goodsno'");
	if($cnt == 0){
		list($sort) = $db->fetch("select max(sort) from ".GD_GOODS_DISPLAY." where mode = '$mode'");
		$sort++;
		$query = "
		insert into ".GD_GOODS_DISPLAY." set
			goodsno		= '".$goodsno."',
			mode		= '$mode',
			sort		= '$sort'
		";
		$db->query($query);
	}
}

if (!$_POST[returnUrl]) $_POST[returnUrl] = $_SERVER[HTTP_REFERER];

### 블로그샵 전송
include_once("../../lib/blogshop.class.php");
$blogshop = new blogshop();
if($_POST['useblog']=='y') {
	if($_POST['brandno']) {
		$query = "select * from ".GD_GOODS_BRAND." where sno='{$_POST['brandno']}'";
		$res = $db->query($query);
		$data = $db->fetch($res);
		$brandnm=$data['brandnm'];
	}
	if($_POST['launchdt']!='null') {
		$_POST['launchdt']=preg_replace("/(^\'|\'$)/",'',$_POST['launchdt']);
	}
	else {
		$_POST['launchdt']='';
	}
	$img_upload=false;
	/**
		2011-01-21 by x-ta-c
		url 형식의 이미지를 등록하도록 변경되었으므로,
		등록된 대표 이미지를 읽어 드릴 수 있도록 허용된 곳인지 파악하여, 이미지 정보를 붙여준다.
	*/
	if ($_POST['image_attach_method'] == 'url') {
		$img_upload = $_POST['url_l'][0];
	}
	else {
		if(is_file(dirname(__FILE__)."/../../data/goods/".$file['img_l']['name'][0])) {
			$img_upload=dirname(__FILE__)."/../../data/goods/".$file['img_l']['name'][0];
		}
	}
	// eof 2011-01-21

	$blogshop->send_goods($goodsno,array(
		'goodsnm'=>stripslashes($_POST['goodsnm']),
		'maker'=>stripslashes($_POST['maker']),
		'origin'=>stripslashes($_POST['origin']),
		'launchdt'=>$_POST['launchdt'],
		'brand'=>stripslashes($brandnm),
		'price'=>$_POST['price'],
		'longdesc'=>stripslashes($_POST['longdesc']),
		'part_no'=>$_POST['blog_part_no'],
		'cate_no'=>$_POST['blog_cate_no'],
		'tags'=>stripslashes($_POST['blog_tag']),
		'trackback'=>$_POST['blog_trackback'],
		'img'=>$img_upload
	));
}
else {
	$blogshop->delete_goods($goodsno);
}

### 인터파크 전송
if ($inpkCfg['use'] == 'Y' || $inpkOSCfg['use'] == 'Y'){
	# 전시코드는 상품API 등록전에만 수정가능
	if ($data['inpk_prdno'] == '' && isset($_POST['inpk_dispno'])){
		$query = "update ".GD_GOODS." set inpk_dispno = '$_POST[inpk_dispno]' where goodsno = '$goodsno'";
		$db->query($query);
	}

	go("../interpark/transmit_action.php?goodsno[]={$goodsno}&returnUrl=" . urlencode($_POST[returnUrl]));
}

if($_POST['mode'] == "modify" && $purchaseSet['usePurchase'] == "Y") {
	if(preg_match("/popup\.shopTouch_goods_register\.php/", $_SERVER['HTTP_REFERER'])) {
		echo "<script>opener.location.href=\"../goods/purchase_goods.php?skey=G.goodsno&sword=$goodsno\";self.close();</script>";
	}
	else {
		$_POST['returnUrl'] = "../goods/purchase_goods.php?skey=G.goodsno&sword=$goodsno";
	}
}
go($_POST[returnUrl]);

?>
