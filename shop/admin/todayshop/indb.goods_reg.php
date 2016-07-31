<?

include "../lib.php";
require_once("../../lib/upload.lib.php");
require_once("../../lib/load.class.php");
@include_once "../../conf/config.mobileShop.php";
require_once("../../lib/todayshop_cache.class.php");

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

$tgsno = $_POST['tgsno'];

$upload = new upload_file;

switch($_POST[mode]) {
	case 'register': {
		# 등록수 제한 체크
		list ($cntGoods) = $db->fetch("select count(*) from ".GD_GOODS."");
		if ($godo[maxGoods]!="unlimited" && $godo[maxGoods]<=$cntGoods){
			msg("상품수 등록이 제한되었습니다",-1);
			exit;
		}

		$db->query("insert into ".GD_GOODS." set regdt	= now()");
		$goodsno = $db->lastID();
		$db->query("insert into ".GD_TODAYSHOP_GOODS." set goodsno = $goodsno, regdt = now()");
		$tgsno = $db->lastID();
		$db->query("insert into ".GD_TODAYSHOP_GOODS_MERGED." set tgsno = $tgsno , goodsno = $goodsno");

		if (is_array($_POST[category]) && empty($_POST[category]) === false) {
			$category = @array_unique($_POST[category]); // 중복 카테고리 걸러내기
			foreach ($category as $v){
				$hidden = 0;//getCateHideCntTS($v) > 0 ? 1 : 0;
				$db->query("insert into ".GD_TODAYSHOP_LINK." set tgsno='$tgsno',category='$v',hidden='$hidden'");
			}
			unset($category);
		}
		$referer = $_SERVER[HTTP_REFERER]."?mode=modify&tgsno=".$tgsno;

		// 리디렉션 주소
		$_POST[returnUrl] = '../todayshop/goods_list.php';

		break;
	}
	case 'modify': {
		### 카테고리 수정
		$p_category = $n_category = array();

		if (is_array($_POST[category]) && empty($_POST[category]) === false) $n_category = @array_unique($_POST[category]); // 중복 카테고리 걸러내기

		$query = "select * from ".GD_TODAYSHOP_LINK." where tgsno='$tgsno'";
		$res = $db->query($query);
		while ($data=$db->fetch($res)) $p_category[] = $data[category];

		$add = @array_diff($n_category,$p_category);
		$del = @array_diff($p_category,$n_category);

		if ($add) foreach ($add as $k=>$v){
			$hidden = 0;//getCateHideCntTS($v) > 0 ? 1 : 0;
			$db->query("insert into ".GD_TODAYSHOP_LINK." set tgsno='$tgsno',category='$v',hidden='$hidden'");
		}
		if ($del) foreach ($del as $v) $db->query("delete from ".GD_TODAYSHOP_LINK." where tgsno='$tgsno' and category='$v'");

		$data = $db->fetch("SELECT g.* FROM ".GD_TODAYSHOP_GOODS." AS tg JOIN ".GD_GOODS." AS g ON tg.goodsno=g.goodsno WHERE tg.tgsno='".$tgsno."'");
		$goodsno = $data['goodsno'];
		break;
	}
	case 'aftersale': {
		// 판매 완료 후 판매수량 노출설정 & 구매달성인원만 변경 가능.
		$sql = "
			UPDATE
				".GD_TODAYSHOP_GOODS." AS tg
				INNER JOIN ".GD_TODAYSHOP_GOODS_MERGED." AS tgm ON tg.tgsno=tgm.tgsno
				INNER JOIN ".GD_GOODS." AS g ON tg.goodsno=g.goodsno
			SET
				tg.limit_ea='".$_POST['limit_ea']."',
				tg.fakestock='".$_POST['fakestock']."',
				tg.fakestock2real='".$_POST['fakestock2real']."',
				g.runout='".$_POST['runout']."',
				tgm.limit_ea='".$_POST['limit_ea']."',
				tgm.fakestock='".$_POST['fakestock']."',
				tgm.fakestock2real='".$_POST['fakestock2real']."',
				tgm.runout='".$_POST['runout']."'
			WHERE tg.tgsno='".$tgsno."'
			";
		$db->query($sql);

		// 캐시 삭제
		todayshop_cache::remove($tgsno,'*');

		if (!$_POST[returnUrl]) $_POST[returnUrl] = $_SERVER[HTTP_REFERER];
		go($_POST[returnUrl], "parent");

		exit;
	}
}


if ($_POST['image_attach_method'] == 'url') {

	// 기 업로드된 파일은 모두 지운다.
	$_del = array();

	array_push($_del, $data[img_i]);
	array_push($_del, $data[img_s]);
	array_push($_del, $data[img_mobile]);

	$_del = array_merge($_del, explode("|",$data[img_m]));
	$_del = array_merge($_del, explode("|",$data[img_l]));

	$_dir	= "../../data/goods/";		// 이미지
	$_dirT	= "../../data/goods/t/";	// 썸네일

	foreach($_del as $k => $f) {
		if ($f == '' || $f == '.' || $f == '..') continue;
		@unlink($_dir.$f);
		@unlink($_dirT.$f);
	}
}
else {
	// 직접 업로드.

	### 파일체크
	if(!chk_goods_img('img_s'))msg('상품이미지 파일이 올바르지 않습니다.',-1);
	if(!chk_goods_img('img_i'))msg('상품이미지 파일이 올바르지 않습니다.',-1);
	if(!chk_goods_img('img_m'))msg('상품이미지 파일이 올바르지 않습니다.',-1);

	### 이미지 업로드
	multiUpload("img_s");
	multiUpload("img_i");
	multiUpload("img_m");

	/*
	### 썸네일 생성
	if ($_POST[copy_s]) copyImg("img_s");
	if ($_POST[copy_m]) copyImg("img_m");
	*/
}






### useEx
if($_POST[useEx] == 0) unset($_POST[ex]);

### useAdd
if($_POST[useAdd] == 0) unset($_POST[addoptnm]);

### 관련상품
if ($_POST[e_refer]) $relation = implode(",",array_unique($_POST[e_refer]));

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

	$price = trim(str_replace(",","",$_POST[option][price][$key]));
	$consumer = trim(str_replace(",","",$_POST[option][consumer][$key]));
	$supply = trim(str_replace(",","",$_POST[option][supply][$key]));
	$reserve = trim(str_replace(",","",$_POST[option][reserve][$key]));

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
		link	= '$link[$idx]'
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
$db->query("delete from ".GD_GOODS_ADD." where goodsno=$goodsno");
if ($addoptnm){
	foreach ($addoptnm as $k=>$v){
		$isInsert = 0;
		foreach ($_POST[addopt][opt][$k] as $k2=>$v2){
			if (!$v2) continue;
			$query = "
			insert into ".GD_GOODS_ADD." set
				goodsno = '$goodsno',
				step	= '$idx',
				opt		= '$v2',
				addprice= '{$_POST[addopt][addprice][$k][$k2]}'
			";
			$db->query($query);
			$isInsert = 1;
		}
		if ($isInsert){
			$tmp[] = $v."^".$_POST[addoptreq][$k];
			$idx++;
		}
	}
}
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

if($cfgMobileShop['vtype_goods']=='0') $_POST[open_mobile] = $_POST[open] = 0;

// 상품 재고량에 따른 품절
if ($_POST['usestock'] && $totstock == 0) $_POST['runout'] = 1;

// 판매량 노출	 (일괄 상품일때 무조건 y)
if ($_POST['processtype'] == 'b') $_POST['showbuyercnt'] = 'y';

### 상품 데이타 수정
$query = "
	update ".GD_GOODS." AS G
		INNER JOIN ".GD_TODAYSHOP_GOODS_MERGED." AS TGM ON G.goodsno = TGM.goodsno
	set
		G.goodsnm			=	 '$_POST[goodsnm]',
		G.meta_title		=	 '$_POST[meta_title]',
		G.goodscd			=	 '$_POST[goodscd]',
		G.maker				=	 '$_POST[maker]',
		G.origin			=	 '$_POST[origin]',
		G.brandno			=	 '$_POST[brandno]',
		G.icon				=	 '$icon',
		G.open				=	 '0',
		G.open_mobile		=	 '$_POST[open_mobile]',
		G.runout			=	 '$_POST[runout]',
		G.delivery_type		=	 '$_POST[delivery_type]',
		G.goods_delivery	=	 '$_POST[goods_delivery]',
		G.keyword			=	 '$_POST[keyword]',
		G.strprice			=	 '$_POST[strprice]',
		G.tax				=	 '$_POST[tax]',
		G.shortdesc			=	 '$_POST[shortdesc]',
		G.longdesc			=	 '$_POST[longdesc]',
		G.mlongdesc			=	 '$_POST[mlongdesc]',
		G.img_i				= '".( ($_POST['image_attach_method'] == 'url') ? $_POST[url_i][0] : $file[img_i][name][0] )."',
		G.img_s				= '".( ($_POST['image_attach_method'] == 'url') ? $_POST[url_s][0] : $file[img_s][name][0] )."',
		G.img_m				= '".( ($_POST['image_attach_method'] == 'url') ? @implode("|",$_POST[url_m]) : @implode("|",$file[img_m][name]) )."',
		G.img_l				= '".( ($_POST['image_attach_method'] == 'url') ? @implode("|",$_POST[url_l]) : @implode("|",$file[img_l][name]) )."',
		G.img_mobile		= '".( ($_POST['image_attach_method'] == 'url') ? $_POST[url_mobile][0] : $file[img_mobile][name][0] )."',
		G.optnm				=	 '$optnm',
		G.opttype			=	 '$_POST[opttype]',
		G.use_emoney		=	 '$_POST[use_emoney]',
		G.addoptnm			=	 '$addoptnm',
		G.memo				=	 '$_POST[memo]',
		G.ex_title			=	 '$ex_title',
		G.ex1				=	 '{$_POST[ex][0]}',
		G.ex2				=	 '{$_POST[ex][1]}',
		G.ex3				=	 '{$_POST[ex][2]}',
		G.ex4				=	 '{$_POST[ex][3]}',
		G.ex5				=	 '{$_POST[ex][4]}',
		G.ex6				=	 '{$_POST[ex][5]}',
		G.relationis		=	 '$_POST[relationis]',
		G.relation			=	 '$relation',
		G.usestock			=	 '$_POST[usestock]',
		G.totstock			=	 '$totstock',
		G.launchdt			=	 $_POST[launchdt],
		G.min_ea			=	 '$_POST[min_ea]',
		G.max_ea			=	 '$_POST[max_ea]',
		G.useblog			=	 '$_POST[useblog]',
		G.detailView		=	 '$_POST[detailView]',
		G.todaygoods		=	 'y',
		G.updatedt			=	 now(),
		TGM.goodsnm			=	'$_POST[goodsnm]',
		TGM.meta_title		=	'$_POST[meta_title]',
		TGM.goodscd			=	'$_POST[goodscd]',
		TGM.maker			=	'$_POST[maker]',
		TGM.origin			=	'$_POST[origin]',
		TGM.brandno			=	'$_POST[brandno]',
		TGM.icon			=	'$icon',
		TGM.open			=	'0',
		TGM.open_mobile		=	'$_POST[open_mobile]',
		TGM.runout			=	'$_POST[runout]',
		TGM.delivery_type	=	'$_POST[delivery_type]',
		TGM.goods_delivery	=	'$_POST[goods_delivery]',
		TGM.keyword			=	'$_POST[keyword]',
		TGM.strprice		=	'$_POST[strprice]',
		TGM.tax				=	'$_POST[tax]',
		TGM.shortdesc		=	'$_POST[shortdesc]',
		TGM.longdesc		=	'$_POST[longdesc]',
		TGM.mlongdesc		=	'$_POST[mlongdesc]',
		TGM.img_i			= '".( ($_POST['image_attach_method'] == 'url') ? $_POST[url_i][0] : $file[img_i][name][0] )."',
		TGM.img_s			= '".( ($_POST['image_attach_method'] == 'url') ? $_POST[url_s][0] : $file[img_s][name][0] )."',
		TGM.img_m			= '".( ($_POST['image_attach_method'] == 'url') ? @implode("|",$_POST[url_m]) : @implode("|",$file[img_m][name]) )."',
		TGM.img_l			= '".( ($_POST['image_attach_method'] == 'url') ? @implode("|",$_POST[url_l]) : @implode("|",$file[img_l][name]) )."',
		TGM.img_mobile		= '".( ($_POST['image_attach_method'] == 'url') ? $_POST[url_mobile][0] : $file[img_mobile][name][0] )."',
		TGM.optnm			=	'$optnm',
		TGM.opttype			=	'$_POST[opttype]',
		TGM.use_emoney		=	'$_POST[use_emoney]',
		TGM.addoptnm		=	'$addoptnm',
		TGM.memo			=	'$_POST[memo]',
		TGM.ex_title		=	'$ex_title',
		TGM.ex1				=	'{$_POST[ex][0]}',
		TGM.ex2				=	'{$_POST[ex][1]}',
		TGM.ex3				=	'{$_POST[ex][2]}',
		TGM.ex4				=	'{$_POST[ex][3]}',
		TGM.ex5				=	'{$_POST[ex][4]}',
		TGM.ex6				=	'{$_POST[ex][5]}',
		TGM.relationis		=	'$_POST[relationis]',
		TGM.relation		=	'$relation',
		TGM.usestock		=	'$_POST[usestock]',
		TGM.totstock		=	'$totstock',
		TGM.launchdt		=	$_POST[launchdt],
		TGM.min_ea			=	'$_POST[min_ea]',
		TGM.max_ea			=	'$_POST[max_ea]',
		TGM.useblog			=	'$_POST[useblog]',
		TGM.detailView		=	'$_POST[detailView]',
		TGM.todaygoods		=	'y',
		TGM.updatedt		=	now()

	where
		G.goodsno = '$goodsno'
";
$db->query($query);

// 투데이샵 저장
if (!($_POST['end_dt'] && $_POST['end_hour'] && $_POST['end_min'])) $_POST['showtimer'] = 'n'; // 종료시간이 없을경우 남은시간 노출안함.

$tgSql = "
	UPDATE ".GD_TODAYSHOP_GOODS." AS TG
		INNER JOIN ".GD_TODAYSHOP_GOODS_MERGED." AS TGM ON TG.tgsno = TGM.tgsno
	SET
";


$tgSql .= " TG.visible='".$_POST['visible']."', ";
$tgSql .= " TG.showtimer='".$_POST['showtimer']."', ";
$tgSql .= " TG.showpercent='".$_POST['showpercent']."', ";
$tgSql .= " TG.showbuyercnt='".$_POST['showbuyercnt']."', ";
$tgSql .= " TG.showstock='".$_POST['showstock']."', ";
$tgSql .= " TG.sms='".$_POST['sms']."',";
$tgSql .= " TG.usememberdc='".$_POST['usememberdc']."',";
$tgSql .= " TG.company='".$_POST['company']."',";
$tgSql .= " TG.goodstype='".$_POST['goodstype']."',";
$tgSql .= " TG.limit_ea='".$_POST['limit_ea']."',";
$tgSql .= " TG.fakestock='".$_POST['fakestock']."',";
$tgSql .= " TG.fakestock2real='".$_POST['fakestock2real']."',";
$tgSql .= " TG.processtype='".$_POST['processtype']."',";
$tgSql .= " TG.usable_spot_name='".$_POST['usable_spot_name']."',";
$tgSql .= " TG.usable_spot_post='".(isset($_POST['zipcode']) ? implode("-",$_POST['zipcode']) : '')."',";
$tgSql .= " TG.usable_spot_address='".$_POST['address']."',";
$tgSql .= " TG.usable_spot_address_ext='".$_POST['address_ext']."',";
$tgSql .= " TG.usable_spot_phone='".$_POST['usable_spot_phone']."',";
$tgSql .= " TG.usable_spot_type='".$_POST['usable_spot_type']."',";
$tgSql .= " TG.extra_header='".$_POST['extra_header']."',";

$tgSql .= " TGM.visible='".$_POST['visible']."', ";
$tgSql .= " TGM.showtimer='".$_POST['showtimer']."', ";
$tgSql .= " TGM.showpercent='".$_POST['showpercent']."', ";
$tgSql .= " TGM.showbuyercnt='".$_POST['showbuyercnt']."', ";
$tgSql .= " TGM.showstock='".$_POST['showstock']."', ";
$tgSql .= " TGM.sms='".$_POST['sms']."',";
$tgSql .= " TGM.usememberdc='".$_POST['usememberdc']."',";
$tgSql .= " TGM.company='".$_POST['company']."',";
$tgSql .= " TGM.goodstype='".$_POST['goodstype']."',";
$tgSql .= " TGM.limit_ea='".$_POST['limit_ea']."',";
$tgSql .= " TGM.fakestock='".$_POST['fakestock']."',";
$tgSql .= " TGM.fakestock2real='".$_POST['fakestock2real']."',";
$tgSql .= " TGM.processtype='".$_POST['processtype']."',";
$tgSql .= " TGM.usable_spot_name='".$_POST['usable_spot_name']."',";
$tgSql .= " TGM.usable_spot_post='".(isset($_POST['zipcode']) ? implode("-",$_POST['zipcode']) : '')."',";
$tgSql .= " TGM.usable_spot_address='".$_POST['address']."',";
$tgSql .= " TGM.usable_spot_address_ext='".$_POST['address_ext']."',";
$tgSql .= " TGM.usable_spot_phone='".$_POST['usable_spot_phone']."',";
$tgSql .= " TGM.usable_spot_type='".$_POST['usable_spot_type']."',";
$tgSql .= " TGM.extra_header='".$_POST['extra_header']."'";


if ($_POST['start_dt'] && $_POST['start_hour'] && $_POST['start_min']) {
	$tgSql .= ", TG.startdt='".substr($_POST['start_dt'],0,4)."-".substr($_POST['start_dt'],4,2)."-".substr($_POST['start_dt'],6,2)." ".$_POST['start_hour'].":".$_POST['start_min'].":00' ";
	$tgSql .= ", TGM.startdt='".substr($_POST['start_dt'],0,4)."-".substr($_POST['start_dt'],4,2)."-".substr($_POST['start_dt'],6,2)." ".$_POST['start_hour'].":".$_POST['start_min'].":00' ";
}
else {
	$tgSql .= ", TG.startdt=null ";
	$tgSql .= ", TGM.startdt=null ";
}

if ($_POST['end_dt'] && $_POST['end_hour'] && $_POST['end_min']) {
	$tgSql .= ", TG.enddt='".substr($_POST['end_dt'],0,4)."-".substr($_POST['end_dt'],4,2)."-".substr($_POST['end_dt'],6,2)." ".$_POST['end_hour'].":".$_POST['end_min'].":00'";
	$tgSql .= ", TGM.enddt='".substr($_POST['end_dt'],0,4)."-".substr($_POST['end_dt'],4,2)."-".substr($_POST['end_dt'],6,2)." ".$_POST['end_hour'].":".$_POST['end_min'].":00'";
}
else {
	$tgSql .= ", TG.enddt=null ";
	$tgSql .= ", TGM.enddt=null ";
}

if ($_POST['goodstype']=='coupon' && $_POST['usestartdt']) {
	$tgSql .= ", TG.usestartdt='".substr($_POST['usestartdt'],0,4)."-".substr($_POST['usestartdt'],4,2)."-".substr($_POST['usestartdt'],6,2)."'";
	$tgSql .= ", TGM.usestartdt='".substr($_POST['usestartdt'],0,4)."-".substr($_POST['usestartdt'],4,2)."-".substr($_POST['usestartdt'],6,2)."'";
}
else {
	$tgSql .= ", TG.usestartdt=null ";
	$tgSql .= ", TGM.usestartdt=null ";
}

if ($_POST['goodstype']=='coupon' &&  $_POST['useenddt']) {
	$tgSql .= ", TG.useenddt='".substr($_POST['useenddt'],0,4)."-".substr($_POST['useenddt'],4,2)."-".substr($_POST['useenddt'],6,2)."'";
	$tgSql .= ", TGM.useenddt='".substr($_POST['useenddt'],0,4)."-".substr($_POST['useenddt'],4,2)."-".substr($_POST['useenddt'],6,2)."'";
}
else {
	$tgSql .= ", TG.useenddt=null ";
	$tgSql .= ", TGM.useenddt=null ";
}

$tgSql .= " WHERE TG.tgsno='".$tgsno."' ";

$db->query($tgSql);

// 캐시 삭제
todayshop_cache::remove($tgsno,'*');

### 계정용량 계산
setDu('goods');

if (!$_POST[returnUrl]) $_POST[returnUrl] = $_SERVER[HTTP_REFERER];

go($_POST[returnUrl], "parent");

?>
