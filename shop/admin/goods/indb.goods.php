<?

include "../lib.php";
require_once("../../lib/upload.lib.php");
require_once("../../lib/load.class.php");
require_once("../../lib/qrcode.class.php");
@include_once "../../conf/config.mobileShop.php";
@include_once "../../conf/config.purchase.php";

if ($_POST['version'] == '2.0') {
	Clib_Application::execute('admin_goods/save');
	exit;
}

if (empty($_POST)) {
	go('./list.php'); exit;
}

$Goods = Core::loader('Goods');
$goodsSort = Core::loader('GoodsSort');

include "indb.goods_fashion.php";

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
		//msg("상품수 등록이 제한되었습니다",-1);
		//exit;
	}

	$db->query("insert into ".GD_GOODS." set regdt	= now()");
	$goodsno = $db->lastID();
	$maxSortIncrease = array();
	$linkSortIncrease = array();
	if ($_POST[category]) foreach ($_POST[category] as $v){
		$hidden = getCateHideCnt($v) > 0 ? 1 : 0;
		$sortList = array();
		foreach ($goodsSort->getManualSortInfoHierarchy($v) as $categorySortSet) {
			if (strlen($v)/3 >= $categorySortSet['depth']) {
				if ($categorySortSet['manual_sort_on_link_goods_position'] === 'FIRST') {
					if (isset($linkSortIncrease[$categorySortSet['category']]) === false) {
						$goodsSort->increaseCategorySort($categorySortSet['category'], $categorySortSet['sort_field']);
						$linkSortIncrease[$categorySortSet['category']] = true;
					}
					$sortList[] = $categorySortSet['sort_field'].'=1';
				}
				else {
					$sortList[] = $categorySortSet['sort_field'].'='.((int)$categorySortSet['sort_max']+1);
				}
				$maxSortIncrease[$categorySortSet['category']] = true;
			}
		}
		$db->query("insert into ".GD_GOODS_LINK." set goodsno='$goodsno',category='$v',hidden='$hidden'".(count($sortList) ? ', '.implode(', ', $sortList) : ''));

		$last_sno = $db->lastID();
		$goods_link_sort = "-unix_timestamp()-".$last_sno;
		$db->query("update ".GD_GOODS_LINK." SET sort=".$goods_link_sort." where sno = ".$last_sno);
	}
	foreach (array_keys($maxSortIncrease) as $category) $goodsSort->increaseSortMax($category);
	$referer = $_SERVER[HTTP_REFERER]."?mode=modify&goodsno=$goodsno";

} else if ($_POST[mode]=="modify"){

	### 정렬순서 최상단 세팅
	if ($_POST[sortTop]) $_POST[sort] = array_map("sortTop",$_POST[sort]);

	### 카테고리 수정
	$p_category = $n_category = $goodsLinkSort = $maxSortIncrease = array();

	$n_category = $_POST[category];
	$query = "select * from ".GD_GOODS_LINK." where goodsno='$goodsno'";
	$res = $db->query($query);
	while ($data=$db->fetch($res)) {
		for ($length = 3; $length <= strlen($data['category']); $length+=3) {
			$goodsLinkSort[substr($data['category'], 0, $length)] = $data['sort'.($length/3)];
		}
		$p_category[] = $data[category];
	}

	$add = @array_diff($n_category,$p_category);
	$del = @array_diff($p_category,$n_category);
	$mod = @array_diff($n_category,$add);

	$linkSortIncrease = array();
	if ($add) foreach ($add as $k=>$v){
		$hidden = getCateHideCnt($v) > 0 ? 1 : 0;
		$sortList = array();
		foreach ($goodsSort->getManualSortInfoHierarchy($v) as $categorySortSet) {
			if (strlen($v)/3 >= $categorySortSet['depth']) {
				if ($goodsLinkSort[$categorySortSet['category']]) {
					$sortList[] = $categorySortSet['sort_field'].'='.$goodsLinkSort[$categorySortSet['category']];
				}
				else {
					if ($categorySortSet['manual_sort_on_link_goods_position'] === 'FIRST') {
						if (isset($linkSortIncrease[$categorySortSet['category']]) === false) {
							$goodsSort->increaseCategorySort($categorySortSet['category'], $categorySortSet['sort_field']);
							$linkSortIncrease[$categorySortSet['category']] = true;
						}
						$sortList[] = $categorySortSet['sort_field'].'=1';
					}
					else {
						$sortList[] = $categorySortSet['sort_field'].'='.((int)$categorySortSet['sort_max']+1);
					}
					$maxSortIncrease[$categorySortSet['category']] = true;
				}
			}
		}
		$db->query("insert into ".GD_GOODS_LINK." set goodsno='$goodsno',category='$v',hidden='$hidden',sort='-{$_POST[sort][$k]}'".(count($sortList) ? ', '.implode(', ', $sortList) : ''));
	}
	foreach (array_keys($maxSortIncrease) as $category) $goodsSort->increaseSortMax($category);
	if ($del) foreach ($del as $v) $db->query("delete from ".GD_GOODS_LINK." where goodsno='$goodsno' and category='$v'");
	if ($mod) foreach ($mod as $k=>$v) $db->query("update ".GD_GOODS_LINK." set sort='-{$_POST[sort][$k]}' where goodsno='$goodsno' and category='$v'");

	if ($_POST['sortTop']) {
		foreach ($n_category as $category) {
			$sortField = $goodsSort->getSortField($category);
			if ($sortField !== 'sort') {
				$db->query('UPDATE '.GD_GOODS_LINK.' SET '.$sortField.'=0 WHERE goodsno='.$goodsno.' AND category="'.$category.'"');
				$goodsSort->optimizeManualSort($category);
			}
		}
	}

	$data = $db->fetch("select * from ".GD_GOODS." where goodsno='$goodsno'");
}

### 옵션이미지 업로드
upload_optimg();

$db->query("delete from ".GD_QRCODE." where qr_type='goods' and contsNo=$goodsno");
if($_POST['qrcode'] == 'y'){
	$db->query("insert into ".GD_QRCODE." set  qr_type='goods' ,contsNo=".$goodsno." ,qr_string = '', qr_name = 'event qr code', qr_size='', useLogo = '', regdt	= now()");
}

/**
	2011-01-21 by x-ta-c
	기존 상품 이미지 업로드 외에 직접 입력한 이미지 url 처리
*/
if ($_POST['image_attach_method'] == 'url') {

	// 기 업로드된 파일은 모두 지운다.
	$_del = array();

	array_push($_del, $data[img_i]);
	array_push($_del, $data[img_s]);
	array_push($_del, $data[img_mobile]);
	array_push($_del, $data[img_w]);
	array_push($_del, $data[img_x]);

	$_del = array_merge($_del, explode("|",$data[img_m]));
	$_del = array_merge($_del, explode("|",$data[img_l]));
	$_del = array_merge($_del, explode("|",$data[img_y]));
	$_del = array_merge($_del, explode("|",$data[img_z]));

	$_dir	= "../../data/goods/";		// 이미지
	$_dirT	= "../../data/goods/t/";	// 썸네일

	foreach($_del as $k => $f) {
		if ($f == '' || $f == '.' || $f == '..') continue;
		@unlink($_dir.$f);
		@unlink($_dirT.$f);
	}

	// 빈 URL 입력란 제거
	$_POST['url_m'] = array_values(array_filter($_POST['url_m']));
	$_POST['url_l'] = array_values(array_filter($_POST['url_l']));
	$_POST['url_y'] = array_values(array_filter($_POST['url_y']));
	$_POST['url_z'] = array_values(array_filter($_POST['url_z']));
}
else {
	// 직접 업로드.

	### 파일체크
	if(!chk_goods_img('img_i'))msg('상품이미지 파일이 올바르지 않습니다.',-1);
	if(!chk_goods_img('img_s'))msg('상품이미지 파일이 올바르지 않습니다.',-1);
	if(!chk_goods_img('img_m'))msg('상품이미지 파일이 올바르지 않습니다.',-1);
	if(!chk_goods_img('img_l'))msg('상품이미지 파일이 올바르지 않습니다.',-1);
	if(!chk_goods_img('img_mobile'))msg('상품이미지 파일이 올바르지 않습니다.',-1);
	if(!chk_goods_img('img_w'))msg('상품이미지 파일이 올바르지 않습니다.',-1);
	if(!chk_goods_img('img_x'))msg('상품이미지 파일이 올바르지 않습니다.',-1);
	if(!chk_goods_img('img_y'))msg('상품이미지 파일이 올바르지 않습니다.',-1);
	if(!chk_goods_img('img_z'))msg('상품이미지 파일이 올바르지 않습니다.',-1);

	### 이미지 업로드
	multiUpload("img_i");
	multiUpload("img_s");
	multiUpload("img_m", $_POST['detailView']);
	multiUpload("img_l");
	multiUpload("img_mobile");
	multiUpload("img_w");
	multiUpload("img_x");
	multiUpload("img_y");
	multiUpload("img_z");

	### 썸네일 생성
	if ($_POST[copy_i]) copyImg("img_i");
	if ($_POST[copy_s]) copyImg("img_s");
	if ($_POST[copy_m]) copyImg("img_m");
	if ($_POST[copy_mobile]) copyImg("img_mobile");
	if ($_POST[copy_w]) copyMobileImg("img_w");
	if ($_POST[copy_x]) copyMobileImg("img_x");
	if ($_POST[copy_y]) copyMobileImg("img_y");
	if ($_POST[copy_z]) copyMobileImg("img_z");
}
// eof 2011-01-21

### 배송비 설정
if((int)$_POST[delivery_type] > 1) $_POST[goods_delivery] = $_POST['goods_delivery'.(int)$_POST[delivery_type]];

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
	'runout'=>$_POST[runout],
	'open'=>$_POST[open],
	'hidden'=>$_POST[hidden],
	'usestock'=>$_POST[usestock],
	'stock'=>$_POST[stock],
	'reserve'=>$_POST[option][reserve][0],
	'img_l'=> ($_POST['image_attach_method'] == 'url') ? $_POST[url_l][0] : $file[img_l][name][0],	// url 직접 입력일때 처리 추가.
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
$r_relation = gd_json_decode(strip_tags($_POST['relation']));

$relation = '';

if (!empty($r_relation)) {

	$relation = 'new_type';	//

	$db->query(" DELETE FROM ".GD_GOODS_RELATED." WHERE goodsno = $goodsno ");

	foreach ($r_relation as $sort => $related) {

		$query = "
		INSERT INTO ".GD_GOODS_RELATED." SET
			goodsno		= '".$goodsno."',
			sort		= '".$sort."',
			r_type		= '".$related['r_type']."',
			r_goodsno	= '".$related['goodsno']."',
			r_start		= ".(!empty($related['r_start']) ? "'".$related['r_start']."'" : 'null' ).",
			r_end		= ".(!empty($related['r_end']) ? "'".$related['r_end']."'" : 'null' ).",
			regdt		= '".$related['r_regdt']."'
		";
		$db->query($query);

		// 서로 등록인 경우 본 상품 정보를 상대 상품에 저장
		if ($related['r_type'] == 'couple') {

			// 상대 상품의 관련상품 데이터 보정.
			fixRelationGoods($related['goodsno']);

			list($last_idx) = $db->fetch("SELECT MAX(sort) FROM ".GD_GOODS_RELATED." WHERE goodsno = '".$related['goodsno']."'");
			$last_idx = is_null($last_idx) ? 0 : $last_idx + 1;

			$query = "
			INSERT INTO ".GD_GOODS_RELATED." SET
				goodsno		= '".$related['goodsno']."',
				sort		= '".$last_idx."',
				r_type		= '".$related['r_type']."',
				r_goodsno	= '".$goodsno."',
				r_start		= ".(!empty($related['r_start']) ? "'".$related['r_start']."'" : 'null' ).",
				r_end		= ".(!empty($related['r_end']) ? "'".$related['r_end']."'" : 'null' ).",
				regdt		= '".$related['r_regdt']."'
			";
			if (!$db->query($query)) {
				$query = "
				UPDATE ".GD_GOODS_RELATED." SET
					sort		= IF(sort >= 0, sort, '".$last_idx."'),
					r_type		= '".$related['r_type']."'
				WHERE
					goodsno = '".$related['goodsno']."'
				AND
					r_goodsno = '".$goodsno."'
				";
				$db->query($query);
			}
		}
		else {

			// 상대 상품이 서로 등록인 경우에만 삭제.
			$query = "
			DELETE FROM ".GD_GOODS_RELATED."
			WHERE
				goodsno		= '".$related['goodsno']."'
			AND
				r_goodsno	= '".$goodsno."'
			AND
				r_type = 'couple'
			";
			$db->query($query);
		}
	}
}

/*### 기본가격설정
if (!$_POST[option][price][0]) $_POST[option][price][0] = $_POST[price];
if (!$_POST[option][consumer][0]) $_POST[option][consumer][0] = $_POST[consumer];
if (!$_POST[option][supply][0]) $_POST[option][supply][0] = $_POST[supply];
if (!$_POST[option][reserve][0]) $_POST[option][reserve][0] = $_POST[reserve];
if (!$_POST[option][stock][0]) $_POST[option][stock][0] = $_POST[stock];
*/

### 사입처 정보 저장
if($_POST['mode'] == "modify") {
	$pchsQr = "SELECT opt1, opt2, pchsno FROM ".GD_GOODS_OPTION." WHERE goodsno = $goodsno";
	$pchsRs = $db->query($pchsQr);
	while($pchsData = $db->fetch($pchsRs)) {
		if($iii > 10) break;
		$ar_tmpPchs[$pchsData['opt1']."|^|^".$pchsData['opt2']] = $pchsData['pchsno'];
	}
}

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

	$pchsno = ($ar_tmpPchs[$opt1."|^|^".$opt2]) ? $ar_tmpPchs[$opt1."|^|^".$opt2] : $_POST['pchsno'];
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
		pchsno	= '$pchsno'
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

			# 추가옵션코드
			$sno = $_sno ? $_sno : $db->lastID();
			$addno = (empty($_POST[addopt][addno][$i])) ? $sno : $_POST[addopt][addno][$i];
			$db->query("update ".GD_GOODS_ADD." set addno='{$addno}' where sno='{$sno}'");

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
if($_POST[launchdt]) $_POST[launchdt] = "'" . @array_shift(explode(' ',Core::helper('Date')->max($_POST[launchdt]))) . "'";
else $_POST[launchdt] = "null";

### 옵션아이콘 및 옵션상품이미지 삭제(폼이없어졌을경우)
deloptimg();

if($cfgMobileShop['vtype_goods']=='0') $_POST[open_mobile] = $_POST[open];

### 상품 정보 제공
if ($_POST['extra_info_title'] && $_POST['extra_info_desc']) {

	$extra_info = array();
	$keys = array_keys($_POST['extra_info_desc']);	// 내용 기준
	$key = 0;

	for ($i=min($keys),$m=max($keys);$i<=$m;$i += 2) {

		if (isset($_POST['extra_info_title'][$i]) && isset($_POST['extra_info_desc'][$i])) {

			for ($j=$i,$k=$i+1;$j<=$k;$j++) {

				$key++;

				if (isset($_POST['extra_info_title'][$j]) && isset($_POST['extra_info_desc'][$j])) {

					if (get_magic_quotes_gpc()) {
						$_POST['extra_info_title'][$j] = stripslashes($_POST['extra_info_title'][$j]);
						$_POST['extra_info_desc'][$j] = stripslashes($_POST['extra_info_desc'][$j]);
						if(isset($_POST['extra_info_inpk_code'][$j])) $_POST['extra_info_inpk_code'][$j] = stripslashes($_POST['extra_info_inpk_code'][$j]);
						if(isset($_POST['extra_info_inpk_type'][$j])) $_POST['extra_info_inpk_type'][$j] = stripslashes($_POST['extra_info_inpk_type'][$j]);
					}

					$_POST['extra_info_title'][$j] = addslashes($_POST['extra_info_title'][$j]);
					$_POST['extra_info_desc'][$j] = addslashes($_POST['extra_info_desc'][$j]);
					$_POST['extra_info_inpk_code'][$j] = addslashes($_POST['extra_info_inpk_code'][$j]);
					$_POST['extra_info_inpk_type'][$j] = addslashes($_POST['extra_info_inpk_type'][$j]);

					$extra_info[$key] = array(
						'title' => trim($_POST['extra_info_title'][$j]),
						'desc' => trim($_POST['extra_info_desc'][$j]),
						'inpk_code' => trim($_POST['extra_info_inpk_code'][$j]),
						'inpk_type' => trim($_POST['extra_info_inpk_type'][$j])
					);

				}

			}

		}

	}
	$extra_info = gd_json_encode($extra_info);

}
else {
	$extra_info = '';
}

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
	longdesc		= '$_POST[longdesc]',
	mlongdesc		= '$_POST[mlongdesc]',
	img_i			= '".( ($_POST['image_attach_method'] == 'url') ? $_POST[url_i][0] : $file[img_i][name][0] )."',
	img_s			= '".( ($_POST['image_attach_method'] == 'url') ? $_POST[url_s][0] : $file[img_s][name][0] )."',
	img_m			= '".( ($_POST['image_attach_method'] == 'url') ? @implode("|",$_POST[url_m]) : @implode("|",$file[img_m][name]) )."',
	img_l			= '".( ($_POST['image_attach_method'] == 'url') ? @implode("|",$_POST[url_l]) : @implode("|",$file[img_l][name]) )."',
	img_mobile		= '".( ($_POST['image_attach_method'] == 'url') ? $_POST[url_mobile][0] : $file[img_mobile][name][0] )."',
	use_mobile_img	= '$_POST[use_mobile_img]',
	img_w			= '".( ($_POST['image_attach_method'] == 'url') ? $_POST[url_w][0] : $file[img_w][name][0] )."',
	img_x			= '".( ($_POST['image_attach_method'] == 'url') ? $_POST[url_x][0] : $file[img_x][name][0] )."',
	img_y			= '".( ($_POST['image_attach_method'] == 'url') ? @implode("|",$_POST[url_y]) : @implode("|",$file[img_y][name]) )."',
	img_z			= '".( ($_POST['image_attach_method'] == 'url') ? @implode("|",$_POST[url_z]) : @implode("|",$file[img_z][name]) )."',
	img_pc_w		= '$_POST[img_pc_w]',
	img_pc_x		= '$_POST[img_pc_x]',
	img_pc_y		= '$_POST[img_pc_y]',
	img_pc_z		= '$_POST[img_pc_z]',
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
	use_stocked_noti= '$_POST[use_stocked_noti]',
	extra_info = '".mysql_real_escape_string($extra_info)."',
	color			= '".strtoupper($_POST['color'])."'

where
	goodsno = '$goodsno'
";
$db->query($query);

### 업데이트 일시
$Goods -> update_date($goodsno);

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
			$sql_pchs = "INSERT INTO ".GD_PURCHASE_GOODS." SET goodsno = '$goodsno', goodsnm = '".$data['goodsnm']."', img_s = '".$data['img_s']."', opt1 = '".$data['opt1']."', opt2 = '".$data['opt2']."', pchsno = '".$_POST['pchsno']."', p_stock = '".$data['stock']."', p_price = '".$data['supply']."', pchsdt = '".$_POST['pchs_pchsdt']."', regdt = NOW()";
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
	if(preg_match("/popup\.register\.php/", $_SERVER['HTTP_REFERER'])) {
		echo "<script>opener.location.href=\"./purchase_goods.php?skey=G.goodsno&sword=$goodsno\";self.close();</script>";
	}
	else {
		$_POST['returnUrl'] = "./purchase_goods.php?skey=G.goodsno&sword=$goodsno";
	}
}
go($_POST[returnUrl]);

?>