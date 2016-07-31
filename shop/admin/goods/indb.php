<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
require_once("../../lib/upload.lib.php");
require_once("../../lib/load.class.php");

$qfile = new qfile();
$upload = new upload_file;
$Goods = Core::loader('Goods');
$goodsSort = Core::loader('GoodsSort');

$_POST[sub] = trim($_POST[sub]);

function delGoodsImg($str)
{
	$_dir	= "../../data/goods/";
	$_dirT	= "../../data/goods/t/";

	$div = explode("|",$str);
	foreach ($div as $v){
		if ($v == '') continue;

		if (is_file($_dir.$v)) @unlink($_dir.$v);
		if (is_file($_dirT.$v)) @unlink($_dirT.$v);
	}
}

function delGoods($goodsno){
	global $db;
	$data = $db->fetch("select * from ".GD_GOODS." where goodsno='{$goodsno}'");

	foreach (array('img_i','img_l','img_m','img_s','img_mobile','img_w','img_x','img_y','img_z') as $key) {
		delGoodsImg($data[$key]);
	}

	$db->query("delete from ".GD_GOODS." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_ADD." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_DISPLAY." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_LINK." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_GOODS_OPTION." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_MEMBER_WISHLIST." where goodsno='{$goodsno}'");
	$db->query("delete from ".GD_SHOPTOUCH_GOODS." where goodsno='{$goodsno}'");

	### 네이버 지식쇼핑 상품엔진
	naver_goods_runout($goodsno);
	
	// 다음 쇼핑하우
	daum_goods_runout($goodsno);

	### 블로그샵 상품삭제
	include_once("../../lib/blogshop.class.php");
	$blogshop = new blogshop();
	$blogshop->delete_goods($goodsno);

	### 계정용량 계산
	setDu('goods');
}

function copyGoods($goodsno){

	global $db,$Goods,$goodsSort;
	static $imgIdx = 0;

	$_dir	= "../../data/goods/";
	$_dirT	= "../../data/goods/t/";

	$data = $db->fetch("select * from ".GD_GOODS." where goodsno='{$goodsno}'",1);

	### 이미지 복사
	$time = time() . sprintf("%03d", $imgIdx++);

	### 이미지 복사
	$ar_images = array(
		'i' => 'img_i',
		's' => 'img_s',
		'm' => 'img_m',
		'l' => 'img_l',
		'e' => 'img_mobile',
		'w' => 'img_w',
		'x' => 'img_x',
		'y' => 'img_y',
		'z' => 'img_z',
	);

	$image_separator = '|';
	$image_qr = array();

	foreach ($ar_images as $key => $image_field) {

		$images = explode($image_separator , $data[$image_field]);
		$images_nums = sizeof($images);
		$images_seq = 0;

		${$image_field} = array();

		if (sizeof($images) > 0) {
			foreach($images as $image_name) {
				if (empty($image_name)) continue;

				if (! preg_match('/^http(s)?:\/\/.+$/', $image_name)) {
					$image_ext = strrpos($image_name,'.') ? substr($image_name, strrpos($image_name,'.')) : '';

					$_image_name  = $time.'_'.$key.( $images_nums > 1 ? '_'.$images_seq++ : '' );
					$_image_name .= $image_ext ? $image_ext : '';

					// 파일 복사
					if (is_file($_dir .$image_name)) @copy($_dir .$image_name, $_dir .$_image_name);
					if (is_file($_dirT.$image_name)) @copy($_dirT.$image_name, $_dirT.$_image_name);

					$image_name = $_image_name;
				}

				${$image_field}[] = $image_name;
			}
		}

		$image_qr[] = "$image_field = '".mysql_real_escape_string(implode($image_separator, ${$image_field}))."'";
	}

	### 상품정보
	$except = array_merge( array("goodsno","regdt","inpk_dispno","inpk_prdno","inpk_regdt","inpk_moddt") , array_values($ar_images) );

	foreach ($data as $k=>$v){
		if (!in_array($k,$except)){
			$qr[] = "$k='".addslashes($v)."'";
		}
	}
	$query = "
	INSERT INTO ".GD_GOODS." SET
		".implode(",",$qr).",
		".implode(",",$image_qr).",
		regdt	= now()
	";
	$db->query($query);
	$cGoodsno = $db->lastID();

	### 업데이트 일시
	$Goods -> update_date($cGoodsno);

	### 추가옵션
	$except = array("sno","goodsno");
	$res = $db->query("select * from ".GD_GOODS_ADD." where goodsno='{$goodsno}' order by sno asc ");
	while ($data=$db->fetch($res,1)){
		if ($data){ unset($qr);
			foreach ($data as $k=>$v){
				if (!in_array($k,$except)) $qr[] = "$k='".addslashes($v)."'";
			}
			$query = "insert into ".GD_GOODS_ADD." set goodsno='{$cGoodsno}',".implode(",",$qr);
			$db->query($query);
		}
	}

	### 가격/재고/옵션
	$res = $db->query("select * from ".GD_GOODS_OPTION." where goodsno='{$goodsno}' order by sno asc");
	while ($data=$db->fetch($res,1)){ unset($qr);
		if ($data){
			foreach ($data as $k=>$v){
				if (!in_array($k,$except)) $qr[] = "$k='".addslashes($v)."'";
			}
			$query = "insert into ".GD_GOODS_OPTION." set goodsno='{$cGoodsno}',".implode(",",$qr);
			$db->query($query);
		}
	}

	### 상품, 카테고리 연결정보
	$maxSortIncrease = array();
	$linkSortIncrease = array();
	$res = $db->query("select * from ".GD_GOODS_LINK." where goodsno='{$goodsno}'");
	while ($data=$db->fetch($res,1)){ unset($qr);
		if ($data){
			unset($data['sort1'], $data['sort2'], $data['sort3'], $data['sort4']);
			foreach ($goodsSort->getManualSortInfoHierarchy($data['category']) as $categorySortSet) {
				if (strlen($data['category'])/3 >= $categorySortSet['depth']) {
					if ($categorySortSet['manual_sort_on_link_goods_position'] === 'FIRST') {
						if (isset($linkSortIncrease[$categorySortSet['category']]) === false) {
							$goodsSort->increaseCategorySort($categorySortSet['category'], $categorySortSet['sort_field']);
							$linkSortIncrease[$categorySortSet['category']] = true;
						}
						$data[$categorySortSet['sort_field']] = 1;
					}
					else {
						$data[$categorySortSet['sort_field']] = ((int)$categorySortSet['sort_max']+1);
					}
					$maxSortIncrease[$categorySortSet['category']] = true;
				}
			}
			foreach ($data as $k=>$v){
				if($k=='sort')$v = -(time());
				if (!in_array($k,$except)) $qr[] = "$k='".addslashes($v)."'";
			}
			$query = "insert into ".GD_GOODS_LINK." set goodsno='{$cGoodsno}',".implode(",",$qr);
			$db->query($query);
		}
	}
	foreach (array_keys($maxSortIncrease) as $category) $goodsSort->increaseSortMax($category);

	### 계정용량 계산
	setDu('goods');

	return $cGoodsno;
}

function reReferer($except, $request){
	return preg_replace("/(&mode=.*)(&page=[0-9]*$)*/", "\${2}" ,$_SERVER[HTTP_REFERER]) . '&' . getVars($except, $request);
}

function __trim(&$var) {

    if(is_array($var)) {
        array_walk($var, '__trim');
    }
	else if ($var != '') {
		$var = trim($var);
    }
}

array_walk($_POST,	'__trim');

switch ($_GET[mode]){

	case "delGoods":
		delGoods($_GET[goodsno]);
		break;

	case "copyGoods":

		copyGoods($_GET[goodsno]);
		break;

	case "getCategory":

		// from ajax.

		header("Content-type: text/html; charset=euc-kr");
		ob_start();
		$opened = explode("|", $_COOKIE[opened]);
		$length = strlen($_GET[category]) + 3;
		$json_var = array();

		### 상품분류 데이타 추출
		$query = "select category, catnm, hidden, sort from ".GD_CATEGORY." where category like '{$_GET[category]}%' and length(category)>={$length} order by category";
		$res = $db->query($query);
		while ($data=$db->fetch($res,1)){
			$data[catnm] = strip_tags( $data[catnm] );
			if (!$data[catnm]) $data[catnm] = "_deleted_";
			$data[id] = $data[category];
			$data[folder] = 'doc';
			switch (strlen($data[category])){
				case ($length + 0):
					$point1 = &$json_var[$data[sort]][];
					$point1 = $data;
					$spot1 = $data[category];
					break;
				case ($len = $length + 3):
				case ($len = $length + 6):
				case ($len = $length + 9):
					$step1 = ($len - $length) / 3;
					$step2 = $step1 + 1;
					if (${"point{$step1}"}[category] == ${"spot{$step1}"}){
						if (in_array(${"spot{$step1}"}, $opened)){
							${"point{$step2}"} = &${"point{$step1}"}[childNodes][$data[sort]][];
							${"point{$step2}"} = $data;
						}
						${"point{$step1}"}['folder'] = 'folder';
					}
					${"spot{$step2}"} = $data[category];
					break;
			}
		}

		### 배열 순서 재정의
		function catesort($arr){
			$arr = resort($arr);
			foreach ($arr as $k => $v){
				if (isset($v[childNodes]) === false) continue;
				$arr[$k][childNodes] = catesort($v[childNodes]);
			}
			return $arr;
		}
		$json_var = catesort($json_var);

		include dirname(__FILE__)."/../../lib/json.class.php";
		$json = new Services_JSON();
		$output = $json->encode($json_var);
		$obOut = ob_get_clean();

		if ($obOut != '') echo $obOut;
		else echo $output;
		exit;
		break;

	case "chgCategoryHidden":

		@include "../../conf/config.mobileShop.php";

		$db->query("update ".GD_CATEGORY." set hidden='$_GET[hidden]' where category='$_GET[category]'");
		setGoodslinkHide($_GET[category], $_GET[hidden]);

		if($cfgMobileShop['vtype_category']==0){
			$db->query("update ".GD_CATEGORY." set hidden_mobile=hidden where category='$_GET[category]'");
		}

		echo 'OK';
		exit;
		break;

	case "chgCategoryShift":

		if ($_GET[targetCategory] != '') {
			list($shiftLen) = $db->fetch("select length(category) from ".GD_CATEGORY." where category like '$_GET[ShiftCategory]%' order by length( category ) desc  limit 1");
			$depth = ($shiftLen - strlen($_GET[ShiftCategory]) + 3 + strlen($_GET[targetCategory])) / 3;
			if ($depth > 4){
				header("Status:depth", true, 400);
				echo "";
				exit;
			}
		}

		ob_start();
		$json_var = array('old' => array(), 'new' => array());

		$length = strlen($_GET[targetCategory])+3;
		list ($max) = $db->fetch("select max(category) from ".GD_CATEGORY." where category like '$_GET[targetCategory]%' and length(category)=$length");
		if (!$max) $max = $_GET[targetCategory]."000";
		$category = sprintf("%0{$length}s",$max+1);

		$res = $db->query("select category from ".GD_CATEGORY." where category like '$_GET[ShiftCategory]%' order by category");
		while ($data=$db->fetch($res)){
			$newCategory = preg_replace("/^{$_GET[ShiftCategory]}/", $category, $data[category]);
			$json_var['old'][] = $data[category];
			$json_var['new'][] = $newCategory;

			if (strlen($_GET[ShiftCategory]) == strlen($data[category])){
				$db->query("update ".GD_CATEGORY." set category='{$newCategory}', sort=unix_timestamp() where category='{$data[category]}'");
			}
			else {
				$db->query("update ".GD_CATEGORY." set category='{$newCategory}' where category='{$data[category]}'");
			}
			$db->query("update ".GD_GOODS_DISPLAY." set mode='{$newCategory}' where mode='{$data[category]}'");
			$db->query("update ".GD_GOODS_LINK." set category='{$newCategory}' where category='{$data[category]}'");
			@rename("../../conf/category/{$data[category]}.php", "../../conf/category/{$newCategory}.php");
		}

		// 상품분류(상품연결 테이블) HIDDEN 처리
		// a. 분류코드 정의 (2차/3차/4차 Parent Category or 신규 1차 Category)
		$tCategory = ($_GET[targetCategory] != '' ? $_GET[targetCategory] : $json_var['new'][0]);

		// b. 분류의 PC 분류감추기, 모바일샵 분류감추기 Catch
		list ($hidden, $hidden_mobile) = $db->fetch("select hidden, hidden_mobile from ".GD_CATEGORY." where category='$tCategory'");

		// c. 모바일샵 카테고리 노출 설정이 '온라인 쇼핑몰(PC버전)과 노출설정 동일하게 적용'인 경우 PC 분류감추기와 동일하게 적용
		@include '../../conf/config.mobileShop.php';
		if ($cfgMobileShop['vtype_category'] == 0) {
			$hidden_mobile = $hidden;
		}
		else {
			list ($hidden) = $db->fetch("select hidden from ".GD_CATEGORY." where category='{$json_var['new'][0]}'");
			setGoodslinkHide($json_var['new'][0], $hidden);
		}

		// d. PC 분류감추기, 모바일샵 분류감추기 처리
		setGoodslinkHide($tCategory, $hidden);
		setGoodslinkHide($tCategory, $hidden_mobile,'mobile');

		include dirname(__FILE__)."/../../lib/json.class.php";
		$json = new Services_JSON();
		$output = $json->encode($json_var);
		$obOut = ob_get_clean();

		if ($obOut != '') echo $obOut;
		else echo $output;
		exit;
		break;
}

$mode = ($_POST[mode]) ? $_POST[mode] : $_GET[mode];

switch ($mode){

	case "sortGoods":

		if(count(array_unique($_POST[sort])) != count($_POST[sort])){
			$min = 0;
			foreach($_POST[sort] as $v)if($v < $min)$min = $v;
			foreach($_POST[sort] as $v){
				$arr[] = $min;
				$min++;
			}
		}else{
			asort($_POST[sort]);
			foreach($_POST[sort] as $v)$arr[] = $v;
		}
		foreach ($_POST[sno] as $k=>$v)$db->query("update ".GD_GOODS_LINK." set sort='".$arr[$k]."' where sno='$v'");
		break;

	case "chgCategorySort":

		### 분류트리순서 저장
		if ($_POST[cate1]) foreach ($_POST[cate1] as $k=>$v) $db->query("update ".GD_CATEGORY." set sort=$k where category='$v'");
		if ($_POST[cate2]) foreach ($_POST[cate2] as $k=>$v) $db->query("update ".GD_CATEGORY." set sort=$k where category='$v'");
		if ($_POST[cate3]) foreach ($_POST[cate3] as $k=>$v) $db->query("update ".GD_CATEGORY." set sort=$k where category='$v'");
		if ($_POST[cate4]) foreach ($_POST[cate4] as $k=>$v) $db->query("update ".GD_CATEGORY." set sort=$k where category='$v'");

		go("category.php?category=$_POST[category]");
		break;

	case "chgBrandSort":

		### 브랜드트리순서 저장
		if ($_POST[brand]) foreach ($_POST[brand] as $k=>$v) $db->query("update ".GD_GOODS_BRAND." set sort=$k where sno='$v'");

		go("brand.php?$_POST[rtn_query]");
		break;

	case "stock":

		include_once("../../conf/config.pay.php");

		### 네이버 지식쇼핑 상품엔진
		naver_goods_diff_check();
		
		// 다음 쇼핑하루 상품엔진
		daum_goods_diff_check();
		
		$ar_goods_update = array();
		foreach ($_POST[stock] as $k=>$v) {
			$data = $db->fetch("select * from ".GD_GOODS_OPTION." where sno=$k");
			$ar_goods_update[$data['goodsno']]['stock'][$k]=$v;

			if($data['link']=='1')
			{
				$ar_goods_update[$data['goodsno']]['price']=$_POST[price][$k];
				$ar_goods_update[$data['goodsno']]['reserve']=$_POST[reserve][$k];
			}
		}

		foreach($ar_goods_update as $key=>$value)
		{
			$res = $db->query("select * from ".GD_GOODS_OPTION." where goodsno='$key'");
			while($row = $db->fetch($res))
			{
				if(!isset($ar_goods_update[$key]['stock'][$row['sno']])) $ar_goods_update[$key]['stock'][$row['sno']]=$row['stock'];
			}
		}

		foreach($ar_goods_update as $key=>$value)
		{
			if(count($ar_goods_update[$key]['stock']))
			{
				$ar_goods_update[$key]['stock']=array_sum($value['stock']);
			}
			naver_goods_diff($key,$ar_goods_update[$key]);
			daum_goods_diff($key,$ar_goods_update[$key]);
		}

		foreach ($_POST[price] as $k=>$v){
			$data = $db->fetch("select * from ".GD_GOODS_OPTION." where sno=$k");
			$query = "
			update ".GD_GOODS_OPTION." set
				price		= '{$_POST[price][$k]}',
				consumer	= '{$_POST[consumer][$k]}',
				supply		= '{$_POST[supply][$k]}',
				reserve		= '{$_POST[reserve][$k]}'
			where
				goodsno		= '$data[goodsno]'
				and opt1	= '$data[opt1]'
			";
			$db->query($query);
			$goodsno[] = $data[goodsno];
		}

		foreach ($_POST[stock] as $k=>$v){
			$data = $db->fetch("select * from ".GD_GOODS_OPTION." where sno='".$k."'");
			list($totstock) = $db->fetch("select totstock from ".GD_GOODS." where goodsno='".$data['goodsno']."'");
			$totstock = $totstock + $v - $data['stock'];
			if($totstock < 0) $totstock = 0;
			$db->query("update ".GD_GOODS_OPTION." set stock='$v' where sno='$k'");
			$db->query("update ".GD_GOODS." set totstock='$totstock' where goodsno='".$data['goodsno']."'");
		}

		if (is_array($goodsno)){
			$goodsno = array_unique($goodsno);
		}

		### 인터파크 전송
		if ($inpkCfg['use'] == 'Y' || $inpkOSCfg['use'] == 'Y'){
			$element = array();
			$element['returnUrl'] = $_SERVER[HTTP_REFERER];
			foreach($goodsno as $k => $v) $element['goodsno['.$k.']'] = $v;
			goPost('../interpark/transmit_action.php', $element, 'parent');
		}

		### 업데이트 일시
		foreach($goodsno as $k => $v){
			$Goods -> update_date($v);
		}

		msg('수정된 내용이 저장되었습니다.');
		echo "<script>parent.location.href='{$_SERVER[HTTP_REFERER]}'; history.back();</script>";
		exit;
		break;

	case "del_brand":
		if (!$_POST[brand]) msg('브랜드 지정이 안되어 있습니다',-1);

		// 등록된 상품이 있는지 체크
		if ($chk = $db->fetch("select goodsno from ".GD_GOODS." where brandno = '$_POST[brand]' limit 1")) {
			msg('등록된 상품이 있는 브랜드는 삭제할 수 없습니다.',-1);
			exit;
		}

		@unlink("../../conf/brand/$_POST[brand].php"); ### 환경파일 삭제
		$db->query("delete from ".GD_GOODS_BRAND." where sno = '$_POST[brand]'");
		go("brand.php", "parent.parent");
		break;

	case "mod_brand":

		### 상품 리스트 레이아웃
		if ( $_POST[brand] ){
			$_POST[lstcfg][page_num] = explode(",",$_POST[lstcfg][page_num]);
			$qfile->open("../../conf/brand/$_POST[brand].php");
			$qfile->write("<? \n");
			$qfile->write("\$lstcfg = array( \n");
			foreach ($_POST[lstcfg] as $k=>$v){
				$v = (!is_array($v)) ? "'$v'" : "array(".implode(",",$v).")";
				$qfile->write("'$k' => $v, \n");
			}
			$qfile->write("); \n");
			$qfile->write("?>");
			$qfile->close();
		}

		### 현브랜드명 업데이트
		$db->query("update ".GD_GOODS_BRAND." set brandnm='$_POST[brandnm]' where sno='$_POST[brand]'");

		### 하위브랜드 추가
		if ($_POST[sub]){
			$dir = "../../conf/brand";
			if (!is_dir($dir)){
				mkdir($dir,0707);
				@chmod($dir,0707);
			}
			$db->query("insert into ".GD_GOODS_BRAND." set brandnm='$_POST[sub]'");
			$addVars = "&focus=sub";
		}

		echo "<script>parent.document.forms[0].rtn_query.value='ifrmScroll=1&brand=$_POST[brand]".$addVars . "';parent.document.forms[0].submit()</script>";
		exit;

		break;

	case "disp_main":

		include "../../conf/config.php";
		# 스킨별 기본 정보
		if(is_file(dirname(__FILE__) . "/../../conf/design_basic_".$_POST['tplSkinWork'].".php")){
			include dirname(__FILE__) . "/../../conf/design_basic_".$_POST['tplSkinWork'].".php";
		}
		$file = "../../conf/design_main.".$_POST['tplSkinWork'].".php";
		if (!is_file($file)) $file = "../../conf/design.main.php";

		$keys = array_keys($_POST['page_num']);

		$save_keys = array(
		'chk','title','tpl','img','size','page_num','cols','e_step',
		'dOpt1','dOpt2','dOpt3','dOpt4','dOpt5','dOpt6','dOpt7','dOpt8','dOpt9','dOpt10','dOpt11',
		'tabNum','tabName1','tabName2','tabName3','tabName4','tabName5','tabName6','tabName7',
		'alphaRate','sort_type','select_date','categoods','price','stock_type','stock_amount','regdt'
		);

		$qfile->open($file);
		$qfile->write("<? \n");
		foreach ($_POST['page_num'] as $k=>$v){
			$qfile->write("\$cfg_step[$k] = array( \n");
			foreach($save_keys as $key) {
				if ($key != 'e_step') {
					if (($key == 'categoods' || $key == 'price' || $key == 'stock_amount') && count($_POST[$key][$k])>0) {
						$qfile->write("'$key' => array(");
						foreach($_POST[$key][$k] as $cateKeys => $cateValue) {
							$qfile->write("'$cateValue', ");
						}
						$qfile->write("), \n");
					} else {
						$qfile->write("'$key' => '{$_POST[$key][$k]}', \n");
					}
				}
			}
			$qfile->write("); \n");
		}
		$qfile->write("?>");
		$qfile->close();

		foreach ($_POST['page_num'] as $k=>$v){
			if ($_POST['sort_type'][$k] == 1) {
				$sort = 0;
				$key = "e_step".$k;
				$_POST[$key] = @array_unique($_POST[$key]);
				if($cfg['shopMainGoodsConf'] == "E" && $_POST['tplSkinWork']){
					$strSQL = "delete from ".GD_GOODS_DISPLAY." where mode='$k' and tplSkin = '".$_POST['tplSkinWork']."'";
				}else{
					$strSQL = "delete from ".GD_GOODS_DISPLAY." where mode='$k' and (tplSkin = '' OR tplSkin IS NULL)";
				}
				$db->query($strSQL);
				if ($_POST[$key]){
					foreach ($_POST[$key] as $v){
						if($cfg['shopMainGoodsConf'] == "E" && $_POST['tplSkinWork']){
							$strSQL = "insert into ".GD_GOODS_DISPLAY." set goodsno='$v',mode='$k',sort='".$sort++."',tplSkin='".$_POST['tplSkinWork']."'";
						}else{
							$strSQL = "insert into ".GD_GOODS_DISPLAY." set goodsno='$v',mode='$k',sort='".$sort++."'";
						}
						$db->query($strSQL);
					}
				}

				# 탭방식의 경우 사용할 상품 진열 목록
				for($j = 1; $j <= 7; $j++) {

					$tabSort = 0;
					$key = "e_step".$k."_".$j;
					$_POST[$key] = @array_unique($_POST[$key]);
					if($cfg['shopMainGoodsConf'] == "E" && $_POST['tplSkinWork']){
						$strSQL = "DELETE FROM ".GD_GOODS_DISPLAY." WHERE mode='{$j}_{$k}' AND tplSkin = '".$_POST['tplSkinWork']."'";
					}else{
						$strSQL = "DELETE FROM ".GD_GOODS_DISPLAY." WHERE mode='{$j}_{$k}' AND (tplSkin = '' OR tplSkin IS NULL)";
					}
					$db->query($strSQL);

					if ($_POST[$key]){
						foreach ($_POST[$key] as $v){
							if($cfg['shopMainGoodsConf'] == "E" && $_POST['tplSkinWork']){
								$strSQL = "INSERT INTO ".GD_GOODS_DISPLAY." SET goodsno='$v',mode='{$j}_{$k}',sort='".$tabSort++."',tplSkin='".$_POST['tplSkinWork']."'";
							}else{
								$strSQL = "INSERT INTO ".GD_GOODS_DISPLAY." SET goodsno='$v',mode='{$j}_{$k}',sort='".$tabSort++."'";
							}
							$db->query($strSQL);
						}
					}
				}
			} else {
				$mainAutoSort = Core::loader('mainAutoSort');
				
				$mainAutoSort -> setMainAutoSort($_POST['sort_type'][$k], $_POST['select_date'][$k], $_POST['categoods'][$k], $_POST['price'][$k], $_POST['stock_type'][$k], $_POST['stock_amount'][$k], $_POST['regdt'][$k]);
			}
		}

		//DB Cache 초기화 141030
		$dbCache = Core::loader('dbcache');
		$dbCache->clearCache('display');

		// 페이지캐시 초기화
		$templateCache = Core::loader('TemplateCache');
		$templateCache->clearCacheByClass('index');
		echo "<script>parent.location.reload();</script>";
		exit;
		break;

	case "disp_search":
		include "../../conf/config.php";
		@include "../../conf/design.search.php";

		# 스킨별 기본 정보
		if(is_file(dirname(__FILE__) . "/../../conf/design_basic_".$_POST['tplSkinWork'].".php")){
			include dirname(__FILE__) . "/../../conf/design_basic_".$_POST['tplSkinWork'].".php";
		}
		$file = "../../conf/design_search.".$_POST['tplSkinWork'].".php";
		if (!is_file($file)) $file = "../../conf/design.search.php";


		unset($_POST['mode'], $_POST['tplSkinWork'], $_POST['sub']);
		$tmp = false;
		if( !isset($_POST['detail_type'])) unset($cfg_search[2]);
		if( !$_POST['keyword_chk']) $_POST['keyword_chk'] = 'off';
		if( !$_POST['disp_sort']) $_POST['disp_sort'] = 'reg';
		if( !$_POST['disp_type'] ) $_POST['disp_type'] = 'gallery';
		if( $_POST['detail_type'] && !$_POST['detail_add_type']) {
			$_POST['detail_add_type'] = array('free_deliveryfee','dc','save','new','event');
		}
		foreach( $_POST as $key=>$val ){
			if( is_array($val) ) {
				for($j=0; $j<count($val); $j++){
					if($val[$j] == '' || $val[$j] == ' ') unset($val[$j]);
				}
				$tmp = implode(',', $val);
			}
			else $tmp = $val;

			switch($key){
				case 'keyword_chk':
				case 'disp_sort':
				case 'pr_text':
				case 'link_url':
					$cfg_search[0][$key] = $tmp;
					break;
				case 'keyword':
					$cfg_search[1][$key] = $tmp;
					break;
				case 'detail_type':
				case 'detail_add_type':
					$cfg_search[2][$key] = $tmp;
					break;
				case 'disp_type':
					$cfg_search[3][$key] = $tmp;
					break;
			}
			unset($tmp);
		}

		$qfile->open($file);
		$qfile->write("<? \n");
		$cnt = max(array_keys($cfg_search));
		for($i=0; $i<=$cnt; $i++){
			$qfile->write("\$cfg_search[$i] = array( \n");
			$cfg_search[$i] = (array)$cfg_search[$i];
			foreach( $cfg_search[$i] as $key=> $val) {
				$qfile->write("'$key' => '{$val}', \n");
			}
			$qfile->write("); \n");
		}
		$qfile->write("?>");
		$qfile->close();

		break;

	case "del_category":
		if (!$_POST[category]) msg('카테고리 지정이 안되어 있습니다',-1);

		### 환경파일 삭제
		$dir = "../../conf/category/";
		if (is_dir($dir)) {
		    if ($dh = opendir($dir)) {
		        while (($file = readdir($dh)) !== false) {
					if ( filetype($dir . $file) != file ) continue;
		        	if ( substr( $file, 0, strlen( $_POST[category] ) ) != $_POST[category] ) continue;
		        	@unlink($dir . $file);
		        }
		        closedir($dh);
		    }
		}

		$db->query("delete from ".GD_CATEGORY." where category like '$_POST[category]%'");
		$db->query("delete from ".GD_GOODS_LINK." where category like '$_POST[category]%'");
		$db->query("delete from ".GD_GOODS_DISPLAY." where mode like '$_POST[category]%'");
		go("category.php", "parent.parent");
		break;

	case "mod_category":

		### 분류이미지 업로드 디렉토리 세팅
		$dir = "../../data/category";
		if (!is_dir($dir)) {
			@mkdir($dir, 0707);
			@chmod($dir, 0707);
		}

		### 구분자 정의
		$tail = array('_basic','_over');

		### 기존 분류이미지
		$arr = getCategoryImg($_POST['category']);
		$imgName = $arr[$_POST['category']];

		### 분류이미지 삭제
		for($i=0;$i<2;$i++){
			if( $_POST['chkimg_'.$i] || $_FILES[img][tmp_name][$i] ) @unlink($dir.'/'. $imgName[$i]);
		}

		### 분류이미지 업로드
		if($_FILES['img']){
		$file_array = reverse_file_array($_FILES['img']);
			for($i=0;$i<2;$i++){
				if($_FILES[img][tmp_name][$i]){
					$tmp = explode('.',$_FILES[img][name][$i]);
					$ext = strtolower($tmp[count($tmp) - 1]);
					$filename = $_POST['category'].$tail[$i].".".$ext;
					$upload->upload_file($file_array[$i],$dir.'/'.$filename,'image');
					if(!$upload->upload())msg('업로드 파일이 올바르지 않습니다.',-1);
				}
			}
		}

		$arr = getCategoryImg($_POST['category']);
		$useimg = count($arr[$_POST['category']]);

		### 상품 리스트 레이아웃
		if ( $_POST[category] ){
			$_POST[lstcfg][page_num] = explode(",",$_POST[lstcfg][page_num]);
			$qfile->open("../../conf/category/$_POST[category].php");
			$qfile->write("<? \n");
			$qfile->write("\$lstcfg = array( \n");
			foreach ($_POST[lstcfg] as $k=>$v){
				if ($k == 'alphaRate' || strpos($k,'dOpt') !== false) {
					$qfile->write("'$k' => array( ");
					foreach($v as $_k => $_v) {
						$qfile->write("'$_k' => '$_v', ");
					}
					$qfile->write("),\n");
				}
				else {
					$v = (!is_array($v)) ? "'$v'" : "array(".implode(",",$v).")";
					$qfile->write("'$k' => $v, \n");
				}
			}
			$qfile->write("); \n");
			$qfile->write("?>");
			$qfile->close();
			@chmod("../../conf/category/$_POST[category].php",0707);
		}

		### 하위 분류 설정 적용하기
		if($_POST[chkdesign]){
			$res = $db->query("select * from gd_category where category like '".$_POST['category']."%' and category != '".$_POST['category']."'");
			while($tmp = $db->fetch($res)){

				$qfile->open("../../conf/category/".$tmp[category].".php");
				$qfile->write("<? \n");
				$qfile->write("\$lstcfg = array( \n");
				foreach ($_POST[lstcfg] as $k=>$v){
					if ($k == 'alphaRate' || strpos($k,'dOpt') !== false) {
						$qfile->write("'$k' => array( ");
						foreach($v as $_k => $_v) {
							$qfile->write("'$_k' => '$_v', ");
						}
						$qfile->write("),\n");
					}
					else {
						$v = (!is_array($v)) ? "'$v'" : "array(".implode(",",$v).")";
						$qfile->write("'$k' => $v, \n");
					}
				}
				$qfile->write("); \n");
				$qfile->write("?>");
				$qfile->close();
				@chmod("../../conf/category/$_POST[category].php",0707);
			}
		}

		// 분류감추기와 동일하게 적용
		@include '../../conf/config.mobileShop.php';
		if ($cfgMobileShop['vtype_category'] == 0) {
			$_POST['hidden_mobile'] = $_POST['hidden'];
		}

		### 현분류명 업데이트
		$_POST[auth_step][] = '1';
		if(!$_POST[level_auth]) $_POST[level_auth] = 0;
		if( $_POST[auth_step]){
			for($i=0; $i<count($_POST[auth_step]); $i++){
				$tmp .= $_POST[auth_step][$i];
				if($i < count($_POST[auth_step])-1) $tmp .= ':';
			}
		}
		if($_POST[level_auth] != '3' && $_POST[level_auth] != '4'){
			$tmp = '1:2:3';
		}
		$db->query("update ".GD_CATEGORY." set catnm='$_POST[catnm]', hidden='$_POST[hidden]', hidden_mobile='$_POST[hidden_mobile]', level='$_POST[level]', level_auth='$_POST[level_auth]', auth_step='$tmp', useimg='$useimg', themeno='$_POST[themeno]' where category='$_POST[category]'");
		if ($_POST['sort_type'] === 'MANUAL') $sortType = 'MANUAL';
		else $sortType = 'AUTO';
		$goodsSort->changeCategorySortType($_POST['category'], $sortType);
		if ($_POST['manual_sort_on_link_goods_position'] === 'LAST') $manualSortOnLinkGoodsPosition = 'LAST';
		else $manualSortOnLinkGoodsPosition = 'FIRST';
		$goodsSort->changeManualSortOnLinkGoodsPosition($_POST['category'], $manualSortOnLinkGoodsPosition);

		### 상품분류 HIDDEN 처리
		setGoodslinkHide($_POST[category], $_POST[hidden]);
		setGoodslinkHide($_POST[category], $_POST[hidden_mobile],'mobile');

		### 하위분류 추가
		if ($_POST[sub]){

			$dir = "../../conf/category";
			if (!is_dir($dir)){
				mkdir($dir,0707);
				@chmod($dir,0707);
			}

			$length = strlen($_POST[category])+3;
			list ($max) = $db->fetch("select max(category) from ".GD_CATEGORY." where category like '$_POST[category]%' and length(category)=$length");
			if (!$max) $max = $_POST[category]."000";
			$category = sprintf("%0{$length}s",$max+1);
			$db->query("insert into ".GD_CATEGORY." set category='$category',catnm='$_POST[sub]',sort=unix_timestamp()");
			$addVars = "&focus=sub";
		}

		### 연결상품 저장
		$db->query("delete from ".GD_GOODS_DISPLAY." where mode = '$_POST[category]'");
		if (is_array($_POST['e_refer'])){
			$_POST['e_refer'] = @array_unique($_POST['e_refer']);
			$sort=0; foreach ($_POST['e_refer'] as $k=>$v){
				$db->query("insert into ".GD_GOODS_DISPLAY." set goodsno='$v',mode='$_POST[category]',sort='".$sort++."'");
			}
		}

		// 페이지캐시 초기화
		$templateCache = Core::loader('TemplateCache');
		$templateCache->clearCacheByClass('goods');

		echo "<script>parent.document.forms[0].category.value='$_POST[category]';parent.document.forms[0].submit()</script>";
		exit;
		//go("category.php?ifrmScroll=1&category=$_POST[category]".$addVars, "parent");
		break;

	case "getEvent":

		header("Content-type: text/html; charset=euc-kr");
		include "../../lib/page.class.php";

		ob_start();
		$json_var = array();
		$json_var[lists] = array();
		$list_tmp = &$json_var[lists];
		if (!$_POST[page_num]) $_POST[page_num] = 10;
		if ($_POST[selValue] != ''){
			list($cnt) = $db->fetch("select count(sno) from ".GD_EVENT." where sno >= '{$_POST[selValue]}'");
			$_POST[page] = ceil($cnt / $_POST[page_num]);
		}

		### 목록
		$pg = new Page($_POST[page],$_POST[page_num]);
		$pg->field = "*";
		$pg->setQuery($db_table="".GD_EVENT."",$where='',$orderby="sno desc");
		$pg->exec();
		$res = $db->query($pg->query);
		while ($data=$db->fetch($res, 'assoc')){
			$data[sdate] = substr($data[sdate], 2);
			$data[sdate] = substr($data[edate], 2);
			$data[subject] = strip_tags($data[subject]);
			$list_tmp[] = array_merge(array('no' => $pg->idx--), $data);
		}

		### 페이징정보
		$json_var[page] = array();
		$json_var[page][now] = $pg->page[now];
		if ($pg->page[now] > 1) $json_var[page][prev] = $pg->page[now] - 1;
		if ($pg->page[now] < $pg->page[total]) $json_var[page][next] = $pg->page[now] + 1;
		ob_end_clean();

		include dirname(__FILE__)."/../../lib/json.class.php";
		$json = new Services_JSON();
		$output = $json->encode($json_var);

		echo $output;
		exit;
		break;

	case "price":

		### 네이버 지식쇼핑 상품엔진
		naver_goods_diff_check();
		
		// 다음 쇼핑하우 상품엔진
		daum_goods_diff_check();
		include("../../conf/config.pay.php");

		if (!$_POST[returnUrl]) $_POST[returnUrl] = reReferer('query,chk', $_POST);

		### 데이타 유효성 검사
		if (in_array($_POST['dtprice'], array('price', 'consumer', 'supply')) === false) break;
		if (is_numeric($_POST['pmnum']) === false || $_POST['pmnum'] < 0) break;
		if (in_array($_POST['pmmark'], array('%', '원')) === false) break;
		if (in_array($_POST['pmtype'], array('down', 'up')) === false) break;
		if (in_array($_POST['tgprice'], array('price', 'consumer')) === false) break;
		if (in_array($_POST['roundunit'], array(1, 10, 100, 1000)) === false) break;
		if (in_array($_POST['roundtype'], array('down', 'halfup', 'up')) === false) break;

		### 대상범위 재구성(검색결과 전체)
		if ($_POST['isall'] == 'Y' && $_POST['query']){
			$_POST['chk'] = array();
			$res = $db->query(stripslashes($_POST['query']));
			while ($data=$db->fetch($res)) $_POST['chk'][] = $data['sno'];
		}

		### 가격 일괄수정
		foreach ($_POST['chk'] as $sno){
			$data = $db->fetch("select * from ".GD_GOODS_OPTION." where sno='{$sno}'");
			$price = $data[$_POST['dtprice']];
			$pmtype = ($_POST['pmtype'] == 'down' ? '-' : '+');
			if ($_POST['pmtype'] == 'down' && $_POST['pmmark'] == '원' && $price < $_POST['pmnum']) continue;

			if ($_POST['pmmark'] == '%') eval("\$price = {$price} * (1 {$pmtype} ({$_POST['pmnum']} / 100));");
			else eval("\$price = {$price} {$pmtype} {$_POST['pmnum']};");

			if ($_POST['roundtype'] == 'down') $price = floor($price / $_POST['roundunit']) * $_POST['roundunit'];
			else if ($_POST['roundtype'] == 'halfup') $price = round($price, -(strlen($_POST['roundunit']) - 1));
			else $price = ceil($price / $_POST['roundunit']) * $_POST['roundunit'];

			### 네이버 지식쇼핑 상품엔진
			if($data['link']==1 && $_POST['tgprice']=='price' && $data['price']!=$price)
			{
				$query = "
				SELECT sum( stock ) as stock , OPEN , runout, delivery_type, use_emoney, usestock, ( SELECT hidden FROM ".GD_GOODS_LINK." WHERE goodsno = b.goodsno LIMIT 1 )  AS hidden
				FROM ".GD_GOODS_OPTION." AS a
				LEFT JOIN ".GD_GOODS." AS b ON a.goodsno = b.goodsno
				WHERE b.goodsno = '{$data['goodsno']}' and go_is_deleted <> '1' and go_is_display = '1'
				GROUP BY a.goodsno
				";
				$sub_data = $db->fetch($query);

				$check=true;
				if($sub_data['runout']=='1' || $sub_data['OPEN']=='0' || $sub_data['hidden']=='1')
				{
					$check=false;
				}
				if($sub_data['usestock']=='o' && $sub_data['stock']==0)
				{
					$check=false;
				}
				if($check)
				{
					$ar_update=array();
					$ar_update['price']=$price;
					$ar_update['mapid']=$data[goodsno];
					$ar_update['class']="U";
					$ar_update['utime']=date("Y-m-d H:i:s");
					if($sub_data[delivery_type]=='0')
					{
						if($set['delivery']['free'] <= $price) $ar_update['deliv']=0;
						else $ar_update['deliv']=$set['delivery']['default'];
					}
					if($sub_data[use_emoney]=='0' && !$set['emoney']['chk_goods_emoney'] && $set['emoney']['goods_emoney'])
					{
						$ar_update['point'] = getDcprice($tmp_price,$set['emoney']['goods_emoney'].'%');
					}

					$ar_str=array();
					foreach($ar_update as $key=>$value)
					{
						$ar_str[]="$key = '$value'";
					}
					$query = "insert into ".GD_GOODS_UPDATE_NAVER." set ".implode(" , ",$ar_str);
					$db->query($query);
					
					$query = "insert into ".GD_GOODS_UPDATE_DAUM." set ".implode(" , ",$ar_str);
					$db->query($query);
				}
			}

			$db->query("update ".GD_GOODS_OPTION." set {$_POST['tgprice']}='{$price}' where sno='{$sno}'");
			$goodsno[] = $data[goodsno];
		}
		if (is_array($goodsno)){
			$goodsno = array_unique($goodsno);
		}

		### 인터파크 전송
		if ($inpkCfg['use'] == 'Y' || $inpkOSCfg['use'] == 'Y'){
			$element = array();
			$element['returnUrl'] = $_POST[returnUrl];
			foreach($goodsno as $k => $v) $element['goodsno['.$k.']'] = $v;
			goPost('../interpark/transmit_action.php', $element, 'parent');
		}

		### 업데이트 일시
		foreach($goodsno as $k => $v){
			$Goods -> update_date($v);
		}

		echo "<script>parent.location.href='{$_POST[returnUrl]}'; history.back();</script>";
		exit;
		break;

	case "reserve":

		if (!$_POST[returnUrl]) $_POST[returnUrl] = reReferer('query,chk', $_POST);

		### 데이타 유효성 검사
		if (in_array($_POST['method'], array('direct', 'price')) === false) break;
		if ($_POST['method'] == 'direct' && (is_numeric($_POST['reserve']) === false || $_POST['reserve'] < 0)) break;
		if ($_POST['method'] == 'price' && in_array($_POST['roundunit'], array(1, 10, 100, 1000)) === false) break;
		if ($_POST['method'] == 'price' && in_array($_POST['roundtype'], array('down', 'halfup', 'up')) === false) break;

		### 대상범위 재구성(검색결과 전체)
		if ($_POST['isall'] == 'Y' && $_POST['query']){
			$_POST['chk'] = array();
			$res = $db->query(stripslashes($_POST['query']));
			while ($data=$db->fetch($res)) $_POST['chk'][] = $data['sno'];
		}

		### 적립금 일괄수정
		foreach ($_POST['chk'] as $sno){
			if ($_POST['method'] == 'direct') $reserve = $_POST['reserve'];
			else {
				$data = $db->fetch("select price from ".GD_GOODS_OPTION." where sno='{$sno}'");
				$reserve = $data['price'] * ($_POST['percent'] / 100);
				if ($_POST['roundtype'] == 'down') $reserve = floor($reserve / $_POST['roundunit']) * $_POST['roundunit'];
				else if ($_POST['roundtype'] == 'halfup') $reserve = round($reserve, -(strlen($_POST['roundunit']) - 1));
				else $reserve = ceil($reserve / $_POST['roundunit']) * $_POST['roundunit'];
			}

			$db->query("update ".GD_GOODS_OPTION." set reserve='{$reserve}' where sno='{$sno}'");
			$goodsno[] = $Goods->get_opt_goodsno($sno);
		}
		if (is_array($goodsno)) $goodsno = array_unique($goodsno);

		### 업데이트 일시
		foreach($goodsno as $k => $v) $Goods -> update_date($v);

		break;

	case "link":

		if (!$_POST[returnUrl]) $_POST[returnUrl] = reReferer('category,chk', $_POST);

		### 데이타 유효성 검사
		$sCategory = array_notnull($_POST[sCate]);
		$sCategory = $sCategory[count($sCategory)-1];
		if ($sCategory == '') break;
		$hidden = getCateHideCnt($sCategory) > 0 ? 1 : 0;

		// 모바일샵 감추기
		@include "../../conf/config.mobileShop.php";
		if ($cfgMobileShop['vtype_category'] == 0) {
			// 모바일샵 카테고리 노출 설정이 '온라인 쇼핑몰(PC버전)과 노출설정 동일하게 적용'인 경우
			$hidden_mobile = $hidden;
		}
		else {
			// 모바일샵 카테고리 노출 설정이 '모바일샵 별도 노출설정 적용'인 경우
			$hidden_mobile = getCateHideCnt($sCategory, 'mobile') > 0 ? 1 : 0;
		}

		### 분류연결
		foreach ($_POST['chk'] as $goodsno){
			$linkSortIncrease = array();
			$sortList = array();
			$goodsLinkSort = array();
			$maxSortIncrease = array();
			$lookupGoodsLink = $db->query('SELECT * FROM '.GD_GOODS_LINK.' WHERE category LIKE "'.substr($sCategory, 0, 3).'%" AND goodsno='.$goodsno);
			while ($goodsLink = $db->fetch($lookupGoodsLink, true)) {
				for ($length = 3; $length <= strlen($goodsLink['category']); $length+=3) {
					$goodsLinkSort[substr($goodsLink['category'], 0, $length)] = $goodsLink['sort'.($length/3)];
				}
			}
			foreach ($goodsSort->getManualSortInfoHierarchy($sCategory) as $categorySortSet) {
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
			foreach (array_keys($maxSortIncrease) as $category) $goodsSort->increaseSortMax($category);
			$db->query("insert into ".GD_GOODS_LINK." set goodsno='{$goodsno}',category='{$sCategory}',hidden='{$hidden}',hidden_mobile='{$hidden_mobile}'".(count($sortList) ? ', '.implode(', ', $sortList) : ''));

			$last_sno = $db->lastID();
		    $goods_link_sort = "-unix_timestamp()-".$last_sno;
		    $db->query("update ".GD_GOODS_LINK." SET sort=".$goods_link_sort." where sno = ".$last_sno);

			if ($_POST['isToday'] == 'Y') $db->query("update ".GD_GOODS." set regdt=now() where goodsno='{$goodsno}'");

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
		}

		break;

	case "unlink":

		if (!$_POST[returnUrl]) $_POST[returnUrl] = reReferer('category,chk', $_POST);
		if ($_POST[category] == '') break;
		foreach ($_POST['chk'] as $goodsno){

			### 이벤트 카테고리 연결 삭제
			$res = $db->query("select b.* from ".GD_GOODS_LINK." a, ".GD_EVENT." b where a.category=b.category and a.goodsno='$goodsno'");
			$i=0;
			while($tmp = $db->fetch($res)){
				$mode = "e".$tmp['sno'];
				list($cnt) = $db->fetch("select count(*) from ".GD_GOODS_DISPLAY." where mode = '$mode' and goodsno='$goodsno'");
				if( $cnt > 0 ){
					$query = "delete from ".GD_GOODS_DISPLAY." where mode = '$mode' and goodsno='$goodsno'";
					$db->query($query);
				}
			}

			$db->query("delete from ".GD_GOODS_LINK." where goodsno='{$goodsno}' and category='{$_POST[category]}'");

		}

		break;

	case "move":

		if (!$_POST[returnUrl]) $_POST[returnUrl] = reReferer('category,chk', $_POST);

		### 데이타 유효성 검사
		$sCategory = array_notnull($_POST[sCate]);
		$sCategory = $sCategory[count($sCategory)-1];
		if ($sCategory == '') break;
		if ($_POST[category] == '') break;
		$hidden = getCateHideCnt($sCategory) > 0 ? 1 : 0;

		// 모바일샵 감추기
		@include "../../conf/config.mobileShop.php";
		if ($cfgMobileShop['vtype_category'] == 0) {
			// 모바일샵 카테고리 노출 설정이 '온라인 쇼핑몰(PC버전)과 노출설정 동일하게 적용'인 경우
			$hidden_mobile = $hidden;
		}
		else {
			// 모바일샵 카테고리 노출 설정이 '모바일샵 별도 노출설정 적용'인 경우
			$hidden_mobile = getCateHideCnt($sCategory, 'mobile') > 0 ? 1 : 0;
		}

		### 분류이동
		foreach ($_POST['chk'] as $goodsno){
			$linkSortIncrease = array();
			$sortList = array();
			$goodsLinkSort = array();
			$maxSortIncrease = array();
			$lookupGoodsLink = $db->query('SELECT * FROM '.GD_GOODS_LINK.' WHERE category LIKE "'.substr($sCategory, 0, 3).'%" AND goodsno='.$goodsno);
			while ($goodsLink = $db->fetch($lookupGoodsLink, true)) {
				for ($length = 3; $length <= strlen($goodsLink['category']); $length+=3) {
					$goodsLinkSort[substr($goodsLink['category'], 0, $length)] = $goodsLink['sort'.($length/3)];
				}
			}
			foreach ($goodsSort->getManualSortInfoHierarchy($sCategory) as $categorySortSet) {
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
			foreach (array_keys($maxSortIncrease) as $category) $goodsSort->increaseSortMax($category);
			$db->query("update ".GD_GOODS_LINK." set category='{$sCategory}',hidden='{$hidden}',hidden_mobile='{$hidden_mobile}'".(count($sortList) ? ', '.implode(', ', $sortList) : '')." where goodsno='{$goodsno}' and category='{$_POST[category]}'");
			if ($_POST['isToday'] == 'Y') $db->query("update ".GD_GOODS." set regdt=now() where goodsno='{$goodsno}'");
		}
		break;

	case "copyGoodses":

		if (!$_POST[returnUrl]) $_POST[returnUrl] = reReferer('category,chk', $_POST);

		### 데이타 유효성 검사
		$sCategory = array_notnull($_POST[sCate]);
		$sCategory = $sCategory[count($sCategory)-1];
		if ($sCategory == '') break;
		if ($_POST[category] == '') break;
		$hidden = getCateHideCnt($sCategory) > 0 ? 1 : 0;

		// 모바일샵 감추기
		@include "../../conf/config.mobileShop.php";
		if ($cfgMobileShop['vtype_category'] == 0) {
			// 모바일샵 카테고리 노출 설정이 '온라인 쇼핑몰(PC버전)과 노출설정 동일하게 적용'인 경우
			$hidden_mobile = $hidden;
		}
		else {
			// 모바일샵 카테고리 노출 설정이 '모바일샵 별도 노출설정 적용'인 경우
			$hidden_mobile = getCateHideCnt($sCategory, 'mobile') > 0 ? 1 : 0;
		}

		### 상품복사
		foreach ($_POST['chk'] as $goodsno){
			$cGoodsno = copyGoods($goodsno);
			$linkSortIncrease = array();
			$sortList = array();
			$goodsLinkSort = array();
			$maxSortIncrease = array();
			$lookupGoodsLink = $db->query('SELECT * FROM '.GD_GOODS_LINK.' WHERE category LIKE "'.substr($sCategory, 0, 3).'%" AND goodsno='.$goodsno);
			while ($goodsLink = $db->fetch($lookupGoodsLink, true)) {
				for ($length = 3; $length <= strlen($goodsLink['category']); $length+=3) {
					$goodsLinkSort[substr($goodsLink['category'], 0, $length)] = $goodsLink['sort'.($length/3)];
				}
			}
			foreach ($goodsSort->getManualSortInfoHierarchy($sCategory) as $categorySortSet) {
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
			foreach (array_keys($maxSortIncrease) as $category) $goodsSort->increaseSortMax($category);
			$db->query("update ".GD_GOODS_LINK." set category='{$sCategory}',hidden='{$hidden}',hidden_mobile='{$hidden_mobile}'".(count($sortList) ? ', '.implode(', ', $sortList) : '')." where goodsno='{$cGoodsno}' and category='{$_POST[category]}'");

			### 이벤트 카테고리 연결
			$res = $db->query("select b.* from ".GD_GOODS_LINK." a, ".GD_EVENT." b where a.category=b.category and a.goodsno='$cGoodsno'");
			$i=0;
			while($tmp = $db->fetch($res)){
				$mode = "e".$tmp['sno'];
				list($cnt) = $db->fetch("select count(*) from ".GD_GOODS_DISPLAY." where mode = '$mode' and goodsno='$cGoodsno'");
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
		}
		break;

	case "delGoodses":

		if (!$_POST[returnUrl]) $_POST[returnUrl] = reReferer('category,chk', $_POST);
		foreach ($_POST['chk'] as $goodsno) delGoods($goodsno);
		break;

	case "linkBrand":

		if (!$_POST[returnUrl]) $_POST[returnUrl] = reReferer('category,chk', $_POST);
		foreach ($_POST['chk'] as $goodsno){
			$db->query("update ".GD_GOODS." set brandno='{$_POST['sbrandno']}' where goodsno='{$goodsno}'");
			### 업데이트 일시
			$Goods -> update_date($goodsno);
		}
		break;

	case "unlinkBrand":

		if (!$_POST[returnUrl]) $_POST[returnUrl] = reReferer('category,chk', $_POST);
		foreach ($_POST['chk'] as $goodsno){
			$db->query("update ".GD_GOODS." set brandno='0' where goodsno='{$goodsno}'");
			### 업데이트 일시
			$Goods -> update_date($goodsno);
		}
		break;

	case "myicon" :
	case "iconRecovery" :
	case "iconRemove" :
		$dir = "../../data/my_icon";
		$a_myicon = array();

		if (!is_dir($dir)) {
			@mkdir($dir, 0707);
			@chmod($dir, 0707);
		}
		@include "../../conf/my_icon.php";

		if($mode == 'myicon'){
			$file_array = array();
			$file_array = reverse_file_array($_FILES['myicon']);
			for($i=0;$i<count($_FILES[myicon][tmp_name]);$i++){
				if($_FILES[myicon][tmp_name][$i]){
					$tmp = explode('.',$_FILES[myicon][name][$i]);
					$ext = strtolower($tmp[count($tmp) - 1]);
					$filename = "my_icon_".time().$i.".".$ext;
					$upload->upload_file($file_array[$i],$dir.'/'.$filename,'image');
					$ret = $upload->upload();
					if($r_myicon[$i]) @unlink($dir.'/'. $r_myicon[$i]);
					$r_myicon[$i] = $filename;
				}else{
					$r_myicon[$i] = ($r_myicon[$i] == '') ? "" : $r_myicon[$i];
				}
			}
		}
		if(in_array($mode,array('iconRecovery','iconRemove'))){
			$idx = $_GET[idx];
			$dir = "../../data/my_icon";
			if (!is_dir($dir)) {
				@mkdir($dir, 0707);
				@chmod($dir, 0707);
			}
			@include "../../conf/my_icon.php";
			@unlink($dir.'/'. $r_myicon[$idx]);
			$r_myicon[$idx] = '';
			$_POST[myicondt] = $r_myicondt;
		}

		foreach($r_myicon as $k => $v){
			if($k > 7 && $v == ''){
				array_splice($r_myicon,$k,1);
				array_splice($_POST[myicondt],$k,1);
			}
		}

		$qfile->open("../../conf/my_icon.php");
		$qfile->write("<? \n");
		$qfile->write("\$r_myicon = array( \n");
		foreach($r_myicon as $k => $v){
			$qfile->write("$k=> '$v', \n");
		}
		$qfile->write("); \n");
		$qfile->write("\$r_myicondt = array( \n");
		foreach($_POST[myicondt] as $k => $v){
			$qfile->write("$k=> '$v', \n");
		}
		$qfile->write("); \n");
		$qfile->write("?>");
		$qfile->close();
		@chmod("../../conf/my_icon.php", 0707);

		break;

	/**
		2011-01-11 by x-ta-c
		- 빠른 상품진열(출력) 수정 처리.
	*/
	case 'quickopen':

		$arTarget = isset($_POST['target']) ? $_POST['target'] : '';
		$arStatus = isset($_POST['open']) ? $_POST['open'] : '';

		$_instr['show'] = '';
		$_instr['hide'] = '';

		if (is_array($arTarget)) {

			// where 절 만들기.
			foreach ($arTarget as $key => $goodsno) {

				$isShow = ($arStatus[$goodsno] == 1) ? true : false;

				if ($isShow) {
					$_instr['show'] .= $goodsno.',';
				}
				else {
					$_instr['hide'] .= $goodsno.',';
				}
			}

			// 끝 콤마 제거 및 쿼리..
			foreach ($_instr as $s => $in) {
				if ($in == '') continue;
				$db->query( "update ".GD_GOODS." set open='".($s == 'show' ? 1 : 0)."' where goodsno IN (".( preg_replace('/,$/','',$in) ).")" );
			}

		}

		break;
	/**
		eof 2011-01-11
	 */

	/**
		2011-01-18 by x-ta-c
		- 빠른 품절 관리 상태 저장
	 */
	case 'quickrunout':

		$arTarget = isset($_POST['target']) ? $_POST['target'] : '';
		$arRunout = isset($_POST['runout']) ? $_POST['runout'] : '';

		$_instr['show'] = '';
		$_instr['hide'] = '';

		if (is_array($arTarget)) {

			// where 절 만들기.
			foreach ($arTarget as $key => $goodsno) {

				$isRunout = ($arRunout[$goodsno] == 1) ? true : false;

				if ($isRunout) {
					$_instr['true'] .= $goodsno.',';
				}
				else {
					$_instr['false'] .= $goodsno.',';
				}
			}

			// 끝 콤마 제거 및 쿼리..
			foreach ($_instr as $s => $in) {
				if ($in == '') continue;
				$db->query( "update ".GD_GOODS." set runout='".($s == 'true' ? 1 : 0)."' where goodsno IN (".( preg_replace('/,$/','',$in) ).")" );
			}

		}
	break;
	/**
		eof 2011-01-18
	 */

	 /**
		2011-01-19 by x-ta-c
		- 빠른 재고 수량 저장
	 */
	case 'quickstock':


		$arTarget = isset($_POST['target']) ? $_POST['target'] : '';
		$arStock = isset($_POST['stock']) ? $_POST['stock'] : '';

		if (is_array($arTarget)) {


			include_once("../../conf/config.pay.php");

			### 네이버 지식쇼핑 상품엔진
			naver_goods_diff_check();
			
			// 다음 쇼핑하우 상품엔진
			daum_goods_diff_check();
			$ar_goods_update = $goodsno = array();
			
			// 넘어온 옵션별 재고 처리..
			foreach ($arTarget as $key => $sno) {

				$stock = preg_replace("/[^0-9]/","",$arStock[$key]);

				$data = $db->fetch("select * from ".GD_GOODS_OPTION." where sno=$sno");

				$ar_goods_update[$data['goodsno']]['stock'][$sno]=$stock;

				$goodsno[] = $data[goodsno];

			}

			// 페이지에 갈려 못넘어온 옵션별 재고 처리.
			foreach($ar_goods_update as $key=>$value)
			{
				$res = $db->query("select * from ".GD_GOODS_OPTION." where goodsno='$key'");
				while($row = $db->fetch($res))
				{
					if(!isset($ar_goods_update[$key]['stock'][$row['sno']])) $ar_goods_update[$key]['stock'][$row['sno']]=$row['stock'];
				}
			}

			foreach($ar_goods_update as $key=>$value)
			{
				if(count($ar_goods_update[$key]['stock']))
				{
					$ar_goods_update[$key]['stock']=array_sum($value['stock']);
				}
				naver_goods_diff($key,$ar_goods_update[$key]);
				daum_goods_diff($key,$ar_goods_update[$key]);
			}


			foreach ($arTarget as $key => $sno) {

				$stock = preg_replace("/[^0-9]/","",$arStock[$key]);

				$data = $db->fetch("select * from ".GD_GOODS_OPTION." where sno='".$sno."'");
				list($totstock) = $db->fetch("select totstock from ".GD_GOODS." where goodsno='".$data['goodsno']."'");
				$totstock = $totstock + $stock - $data['stock'];
				if($totstock < 0) $totstock = 0;
				$db->query("update ".GD_GOODS_OPTION." set stock='$stock' where sno='$sno'");
				$db->query("update ".GD_GOODS." set totstock='$totstock' where goodsno='".$data['goodsno']."'");
			}


			if (is_array($goodsno)){
				$goodsno = array_unique($goodsno);


				### 인터파크 전송
				if ($inpkCfg['use'] == 'Y' || $inpkOSCfg['use'] == 'Y'){
					$element = array();
					$element['returnUrl'] = $_SERVER[HTTP_REFERER];
					foreach($goodsno as $k => $v) $element['goodsno['.$k.']'] = $v;
					goPost('../interpark/transmit_action.php', $element, 'parent');
				}

				### 업데이트 일시
				foreach($goodsno as $k => $v){
					$Goods -> update_date($v);
				}

			}	// if

		}

		// 페이지캐시 초기화
		$templateCache = Core::loader('TemplateCache');
		$templateCache->clearCacheByClass('goods');

	break;


	// 체크된 레코드만 삭제 (빠른 품절관리)
	case 'quickdelete':
		$arTarget = isset($_POST['chk']) ? $_POST['chk'] : '';

		if (is_array($arTarget)) {

			// 상품삭제
			foreach ($arTarget as $key => $goodsno) {
				delGoods($goodsno);
			}

		}

		echo "<script>alert('선택상품을 삭제하였습니다.');</script>";

		break;
	/**
		eof 2011-01-19
	 */
	case 'quickicon' :	// 히든 프레임으로 실행됨.

		// icon 의 키와, customicon 의 키를 합쳐서 루프.
		$ar_goodsno = @array_merge(@array_keys((array)$_POST[icon]),@array_keys((array)$_POST[customicon]));
		$ar_goodsno = @array_unique($ar_goodsno);

		foreach ($ar_goodsno as $goodsno) {

			$_icon = @array_sum($_POST['icon'][$goodsno]);
			$_icon += (int)$_POST['customicon'][$goodsno];

			$query = "UPDATE ".GD_GOODS." SET icon = '$_icon' WHERE goodsno = '$goodsno'";
			$db->query($query);
		}

		msg('저장되었습니다.');
		echo "<script>parent.location.href='{$_SERVER[HTTP_REFERER]}';</script>";
		exit;
		break;

	case 'quickdelivery':

		$arTarget = isset($_POST['chk']) ? $_POST['chk'] : '';

		if (is_array($arTarget)) {

			if (!isset($_POST['set_delivery_type'])) {
				msg('상품별 배송비를 선택해 주세요.',-1);
				exit;
			}

			if ($_POST['set_delivery_type'] > 1 && empty($_POST['set_goods_delivery'.$_POST['set_delivery_type']])) {
				msg('배송비를 입력해 주세요.',-1);
				exit;
			}

			$query = "
			UPDATE ".GD_GOODS." SET
				delivery_type = '".$_POST['set_delivery_type']."',
				goods_delivery = '".$_POST['set_goods_delivery'.$_POST['set_delivery_type']]."'
			WHERE
				goodsno IN (".implode(',',$arTarget).")
			";

			$db->query($query);

		}
		msg('저장되었습니다.');
		break;

	case 'colorModify' :
		foreach($_POST['setColor'] as $k => $v) {
			$query = "UPDATE ".GD_GOODS." SET color = '".$v."' WHERE goodsno = '".$k."'";
			$db->query($query);
		}
		msg("색상이 수정되었습니다.");
		break;

	case 'stockedNotiListDelete' :
		foreach($_POST['chk'] as $v){
			$res = $db->query("SELECT sno, goodsno, opt1, opt2 FROM ".GD_GOODS_STOCKED_NOTI." WHERE sno='".$v."'");
			$data=$db->fetch($res);
			$goods['sno'][] = $data['sno'];
			$goods['goodsno'][] = $data['goodsno'];
			$goods['opt1'][] = $data['opt1'];
			$goods['opt2'][] = $data['opt2'];
		}
		foreach($goods[sno] as $k=>$v){
			$goodsno = $goods['goodsno'][$k];
			$opt1 = $goods['opt1'][$k];
			$opt2 = $goods['opt2'][$k];
			$query = "DELETE FROM ".GD_GOODS_STOCKED_NOTI." WHERE goodsno='".$goodsno."' AND opt1='".$opt1."' AND opt2='".$opt2."'";
			$db->query($query);
		}
		break;

	case 'common_info' :

		foreach($_POST[title] as $key => $val) {

			$idx = $key;

			$query = "INSERT INTO ".GD_COMMON_INFO."(idx, title, info, open, reg_date) values ('$idx','".$_POST['title'][$idx]."', '".$_POST['info'][$idx]."', '".$_POST['open'][$idx]."', now())
					  ON DUPLICATE KEY UPDATE title='".$_POST['title'][$idx]."', info='".$_POST['info'][$idx]."', open='".$_POST['open'][$idx]."'";
			$db->query($query);
		}

		break;
}

if (!$_POST[returnUrl]) $_POST[returnUrl] = $_SERVER[HTTP_REFERER];
go($_POST[returnUrl]);

?>