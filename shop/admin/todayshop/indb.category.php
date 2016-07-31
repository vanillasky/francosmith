<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
require_once("../../lib/upload.lib.php");
require_once("../../lib/load.class.php");
require_once("../../lib/todayshop_cache.class.php");

$qfile = new qfile();
$upload = new upload_file;
$Goods = & load_class('Goods','Goods');

$_POST[sub] = trim($_POST[sub]);


function reReferer($except, $request){
	return preg_replace("/(&mode=.*)(&page=[0-9]*$)*/", "\${2}" ,$_SERVER[HTTP_REFERER]) . '&' . getVars($except, $request);
}

if ($_GET['mode'] != 'getCategory') {
	// 캐시 삭제
	todayshop_cache::truncate();
}

switch ($_GET['mode']){
	case "getCategory":

		header("Content-type: text/html; charset=euc-kr");
		ob_start();
		$opened = explode("|", $_COOKIE[opened]);
		$length = strlen($_GET[category]) + 3;
		$json_var = array();

		### 상품분류 데이타 추출
		$query = "select category, catnm, hidden, sort from ".GD_TODAYSHOP_CATEGORY." where category like '{$_GET[category]}%' and length(category)>={$length} order by category";
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
			if (is_array($arr) && empty($arr)===false) {
				foreach ($arr as $k => $v){
					if (isset($v[childNodes]) === false) continue;
					$arr[$k][childNodes] = catesort($v[childNodes]);
				}
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

		$db->query("update ".GD_TODAYSHOP_CATEGORY." set hidden='$_GET[hidden]' where category='$_GET[category]'");
		setGoodslinkHide($_GET[category], $_GET[hidden]);

		if($cfgMobileShop['vtype_category']==0){
			$db->query("update ".GD_TODAYSHOP_CATEGORY." set hidden_mobile=hidden where category='$_GET[category]'");
		}

		echo 'OK';
		exit;
		break;

	case "chgCategoryShift":

		if ($_GET[targetCategory] != '') {
			list($shiftLen) = $db->fetch("select length(category) from ".GD_TODAYSHOP_CATEGORY." where category like '$_GET[ShiftCategory]%' order by length( category ) desc  limit 1");
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
		list ($max) = $db->fetch("select max(category) from ".GD_TODAYSHOP_CATEGORY." where category like '$_GET[targetCategory]%' and length(category)=$length");
		if (!$max) $max = $_GET[targetCategory]."000";
		$category = sprintf("%0{$length}s",$max+1);

		$res = $db->query("select category from ".GD_TODAYSHOP_CATEGORY." where category like '$_GET[ShiftCategory]%' order by category");
		while ($data=$db->fetch($res)){
			$newCategory = preg_replace("/^{$_GET[ShiftCategory]}/", $category, $data[category]);
			$json_var['old'][] = $data[category];
			$json_var['new'][] = $newCategory;

			if (strlen($_GET[ShiftCategory]) == strlen($data[category])){
				$db->query("update ".GD_TODAYSHOP_CATEGORY." set category='{$newCategory}', sort=unix_timestamp() where category='{$data[category]}'");
			}
			else {
				$db->query("update ".GD_TODAYSHOP_CATEGORY." set category='{$newCategory}' where category='{$data[category]}'");
			}
//			$db->query("update ".GD_GOODS_DISPLAY." set mode='{$newCategory}' where mode='{$data[category]}'");
			$db->query("update ".GD_GOODS_LINK." set category='{$newCategory}' where category='{$data[category]}'");
			@rename("../../conf/category/{$data[category]}.php", "../../conf/category/{$newCategory}.php");
		}

		if ($_GET[targetCategory] != ''){
			list ($hidden) = $db->fetch("select hidden from ".GD_TODAYSHOP_CATEGORY." where category='$_GET[targetCategory]'");
			setGoodslinkHide($_GET[targetCategory], $hidden);
		}
		else {
			list ($hidden) = $db->fetch("select hidden from ".GD_TODAYSHOP_CATEGORY." where category='{$json_var['new'][0]}'");
			setGoodslinkHide($json_var['new'][0], $hidden);
		}

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
	case "chgCategorySort":

		### 분류트리순서 저장
		if ($_POST[cate1]) foreach ($_POST[cate1] as $k=>$v) $db->query("update ".GD_TODAYSHOP_CATEGORY." set sort=$k where category='$v'");
		if ($_POST[cate2]) foreach ($_POST[cate2] as $k=>$v) $db->query("update ".GD_TODAYSHOP_CATEGORY." set sort=$k where category='$v'");
		if ($_POST[cate3]) foreach ($_POST[cate3] as $k=>$v) $db->query("update ".GD_TODAYSHOP_CATEGORY." set sort=$k where category='$v'");
		if ($_POST[cate4]) foreach ($_POST[cate4] as $k=>$v) $db->query("update ".GD_TODAYSHOP_CATEGORY." set sort=$k where category='$v'");

		go("category.php?category=$_POST[category]");
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

		$db->query("delete from ".GD_TODAYSHOP_CATEGORY." where category like '$_POST[category]%'");
		$db->query("delete from ".GD_TODAYSHOP_LINK." where category like '$_POST[category]%'");
//		$db->query("delete from ".GD_GOODS_DISPLAY." where mode like '$_POST[category]%'");
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
		$arr = getCategoryImgTS($_POST['category']);
		$imgName = $arr[$_POST['category']];

		### 분류이미지 삭제
		for($i=0;$i<2;$i++){
			if( $_POST['chkimg_'.$i] || $_FILES[img][tmp_name][$i] ) unlink($dir.'/'. $imgName[$i]);
		}

		### 분류이미지 업로드
		if($_FILES['img']){
		$file_array = reverse_file_array($_FILES['img']);
			for($i=0;$i<2;$i++){
				if($_FILES[img][tmp_name][$i]){
					$tmp = explode('.',$_FILES[img][name][$i]);
					$ext = strtolower($tmp[count($tmp) - 1]);
					$filename = 'TS'.$_POST['category'].$tail[$i].".".$ext;
					$upload->upload_file($file_array[$i],$dir.'/'.$filename,'image');
					if(!$upload->upload())msg('업로드 파일이 올바르지 않습니다.',-1);
				}
			}
		}

		$arr = getCategoryImgTS($_POST['category']);
		$useimg = count($arr[$_POST['category']]);
/*
		### 상품 리스트 레이아웃
		if ( $_POST[category] ){
			$_POST[lstcfg][page_num] = explode(",",$_POST[lstcfg][page_num]);
			$qfile->open("../../conf/category/$_POST[category].php");
			$qfile->write("<? \n");
			$qfile->write("\$lstcfg = array( \n");
			foreach ($_POST[lstcfg] as $k=>$v){
				$v = (!is_array($v)) ? "'$v'" : "array(".implode(",",$v).")";
				$qfile->write("'$k' => $v, \n");
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
					$v = (!is_array($v)) ? "'$v'" : "array(".implode(",",$v).")";
					$qfile->write("'$k' => $v, \n");
				}
				$qfile->write("); \n");
				$qfile->write("?>");
				$qfile->close();
				@chmod("../../conf/category/$_POST[category].php",0707);
			}
		}
*/
		### 현분류명 업데이트
		$db->query("update ".GD_TODAYSHOP_CATEGORY." set catnm='$_POST[catnm]', hidden='$_POST[hidden]', hidden_mobile='$_POST[hidden_mobile]', level='$_POST[level]', useimg='$useimg' where category='$_POST[category]'");

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
			list ($max) = $db->fetch("select max(category) from ".GD_TODAYSHOP_CATEGORY." where category like '$_POST[category]%' and length(category)=$length");
			if (!$max) $max = $_POST[category]."000";
			$category = sprintf("%0{$length}s",$max+1);
			$db->query("insert into ".GD_TODAYSHOP_CATEGORY." set category='$category',catnm='$_POST[sub]',sort=unix_timestamp()");
			$addVars = "&focus=sub";
		}
/*
		### 연결상품 저장
		$db->query("delete from ".GD_GOODS_DISPLAY." where mode = '$_POST[category]'");
		if (is_array($_POST['e_refer'])){
			$_POST['e_refer'] = @array_unique($_POST['e_refer']);
			$sort=0; foreach ($_POST['e_refer'] as $k=>$v){
				$db->query("insert into ".GD_GOODS_DISPLAY." set goodsno='$v',mode='$_POST[category]',sort='".$sort++."'");
			}
		}
*/
		echo "<script>parent.document.forms[0].category.value='$_POST[category]';parent.document.forms[0].submit()</script>";
		exit;
		//go("category.php?ifrmScroll=1&category=$_POST[category]".$addVars, "parent");
		break;

	case "link":

		if (!$_POST[returnUrl]) $_POST[returnUrl] = reReferer('category,chk', $_POST);
/*
		### 데이타 유효성 검사
		$sCategory = array_notnull($_POST[sCate]);
		$sCategory = $sCategory[count($sCategory)-1];
		if ($sCategory == '') break;
		$hidden = getCateHideCnt($sCategory) > 0 ? 1 : 0;

		### 분류연결
		foreach ($_POST['chk'] as $goodsno){
			$db->query("insert into ".GD_GOODS_LINK." set goodsno='{$goodsno}',category='{$sCategory}',hidden='{$hidden}',sort=-unix_timestamp()");
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
*/
		break;

	case "unlink":

		if (!$_POST[returnUrl]) $_POST[returnUrl] = reReferer('category,chk', $_POST);
		if ($_POST[category] == '') break;
/*
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
*/
		break;

		case 'set_interest' :

		$_POST['interest_area_use'] = isset($_POST['interest_area_use']) ? $_POST['interest_area_use'] : '0';
		$_POST['interest_area_member'] = isset($_POST['interest_area_member']) ? $_POST['interest_area_member'] : '0';
		$_POST['interest_area_subscribe'] = isset($_POST['interest_area_subscribe']) ? $_POST['interest_area_subscribe'] : '0';

		$todayShop = &load_class('todayshop', 'todayshop');

		$todayShop->saveConfig($_POST);

		msg('저장되었습니다.');

		break;
}

if (!$_POST[returnUrl]) $_POST[returnUrl] = $_SERVER[HTTP_REFERER];
go($_POST[returnUrl]);

?>