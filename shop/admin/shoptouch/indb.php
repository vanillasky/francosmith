<?
include "../lib.php";
require_once("../../lib/qfile.class.php");
require_once("../../lib/upload.lib.php");
include_once dirname(__FILE__) . "/../design/webftp/webftp.class_outcall.php";
include "../../conf/config.php";
@include "../../lib/pAPI.class.php";
@include "../../lib/json.class.php";

$pAPI = new pAPI();
$json = new Services_JSON(16);
$qfile = new qfile();
$upload = new upload_file;

$Goods = Core::loader('Goods');

$mode = ($_POST[mode]) ? $_POST[mode] : $_GET[mode];

function copyGoods($goodsno){
	global $db,$Goods;
	$_dir	= "../../data/goods/";
	$_dirT	= "../../data/goods/t/";

	$data = $db->fetch("select * from ".GD_GOODS." where goodsno='{$goodsno}'",1);

	### 이미지 복사
	$time = time() . sprintf("%03d", $GLOBALS[imgIdx]++);
	$div = explode("|",$data[img_l]);
	if ($div){ foreach ($div as $k=>$v){
		if ($v == '') continue;
		if (preg_match('/^http(s)?:\/\/.+$/',$v)) {
			$img_l[] = $v;
		}
		else {
			$img_l[] = $time."_l_".$k.(strrpos($v,".")?substr($v,strrpos($v,".")):"");
			if (is_file($_dir.$v)) copy($_dir.$v,$_dir.$img_l[$k]);
			if (is_file($_dirT.$v)) copy($_dirT.$v,$_dirT.$img_l[$k]);
		}
	}}

	$div = explode("|",$data[img_m]);
	if ($div){ foreach ($div as $k=>$v){
		if ($v == '') continue;
		if (preg_match('/^http(s)?:\/\/.+$/',$v)) {
			$img_m[] = $v;
		}
		else {
			$img_m[] = $time."_m_".$k.(strrpos($v,".")?substr($v,strrpos($v,".")):"");
			if (is_file($_dir.$v)) copy($_dir.$v,$_dir.$img_m[$k]);
			if (is_file($_dirT.$v)) copy($_dirT.$v,$_dirT.$img_m[$k]);
		}
	}}

	if ($data[img_s]){
		if (preg_match('/^http(s)?:\/\/.+$/',$data[img_s])) {
			$img_s = $data[img_s];
		}
		else {
			$img_s = $time."_s".(strrpos($v,".")?substr($v,strrpos($v,".")):"");
			if (is_file($_dir.$data[img_s])) copy($_dir.$data[img_s],$_dir.$img_s);
		}
	}

	if ($data[img_i]){
		if (preg_match('/^http(s)?:\/\/.+$/',$data[img_i])) {
			$img_i = $data[img_i];
		}
		else {
			$img_i = $time."_i".(strrpos($v,".")?substr($v,strrpos($v,".")):"");
			if (is_file($_dir.$data[img_i])) copy($_dir.$data[img_i],$_dir.$img_i);
		}

	}

	### 상품정보
	$except = array("goodsno","img_i","img_s","img_m","img_l","regdt","inpk_dispno","inpk_prdno","inpk_regdt","inpk_moddt");
	foreach ($data as $k=>$v){
		if (!in_array($k,$except)){
			$qr[] = "$k='".addslashes($v)."'";
		}
	}
	$query = "
	insert into ".GD_GOODS." set ".implode(",",$qr).",
		img_i	= '$img_i',
		img_s	= '$img_s',
		img_m	= '".@implode("|",$img_m)."',
		img_l	= '".@implode("|",$img_l)."',
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
	$res = $db->query("select * from ".GD_GOODS_OPTION." where goodsno='{$goodsno}'");
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
	$res = $db->query("select * from ".GD_GOODS_LINK." where goodsno='{$goodsno}'");
	while ($data=$db->fetch($res,1)){ unset($qr);
		if ($data){
			foreach ($data as $k=>$v){
				if($k=='sort')$v = -(time());
				if (!in_array($k,$except)) $qr[] = "$k='".addslashes($v)."'";
			}
			$query = "insert into ".GD_GOODS_LINK." set goodsno='{$cGoodsno}',".implode(",",$qr);
			$db->query($query);
		}
	}

	### 계정용량 계산
	setDu('goods');

	return $cGoodsno;
}

switch($mode){
	case "set":	//사용여부
		
		$sel_android_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', 'shoptouch', 'use_android');
		$res_android = $db->_select($sel_android_query);
		$val_use_android = $res_android[0]['value'];

		$sel_apple_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', 'shoptouch', 'use_apple');
		$res_apple = $db->_select($sel_apple_query);
		$val_use_apple = $res_apple[0]['value'];

		$arr_android = array();
		if($val_use_android == '0' || $val_use_android == '1') {
			
			$arr_android['value'] = $_POST['use_android'];
			$android_query = $db->_query_print('UPDATE gd_env SET [cv] WHERE category=[s] AND name=[s]', $arr_android, 'shoptouch', 'use_android');
		}
		else {
			$arr_android['category'] = 'shoptouch';
			$arr_android['name'] = 'use_android';
			$arr_android['value'] = $_POST['use_android'];
			$android_query = $db->_query_print('INSERT INTO gd_env SET [cv]', $arr_android);
		}
		
		$arr_apple = array();
		if($val_use_apple == '0' || $val_use_apple == '1') {
			$arr_apple['value'] = $_POST['use_apple'];
			$apple_query = $db->_query_print('UPDATE gd_env SET [cv] WHERE category=[s] AND name=[s]', $arr_apple, 'shoptouch', 'use_apple');
		}
		else {
			$arr_apple['category'] = 'shoptouch';
			$arr_apple['name'] = 'use_apple';
			$arr_apple['value'] = $_POST['use_apple'];
			$apple_query = $db->_query_print('INSERT INTO gd_env SET [cv]', $arr_apple);
		}

		$db->query($apple_query);
		$db->query($android_query);
		

		break;
	
	case "design_menu":	//기본설정
		
		/*
		if($_POST['use_title_img'] == 'true') {

			if (isset($_FILES['title_img_up'])) {

				$_BGFILES = array( 'title_img' => $_FILES['title_img_up'] );
				$userori = array( 'title_img' => 'title_img' . strrChr( $_FILES['title_img_up']['name'], "." ) );
				outcallUpload( $_BGFILES, '/../../data/shoptouch/upload_img/', $userori );
			}
		}
		else {
			$_POST['title_img'] = '';
		}
		
		unset($_POST['mode'], $_POST['x'], $_POST['y']);

		$json_res = $pAPI->basicScreenAdd($godo['sno'], $_POST);

		### 기본설정 API 통신 enamoo -> vercoop ###

		break;
		*/

		if($_POST['use_title_img'] == 'true') {
			### 분류이미지 업로드 디렉토리 세팅
			$dir = "../../data/shoptouch/upload_img";
			if (!is_dir($dir)) {
				@mkdir($dir, 0707);
				@chmod($dir, 0707);
			}

			if($_FILES['title_img_up']){
				$file_array = reverse_file_array($_FILES['title_img_up']);

				for($i=0;$i<1;$i++){
					if($_FILES[title_img_up][tmp_name][$i]){
						$tmp = explode('.',$_FILES[title_img_up][name][$i]);
						$ext = strtolower($tmp[count($tmp) - 1]);
						$filename = 'tmp_title'.'.'.$ext;
						$upload->upload_file($file_array[$i],$dir.'/'.$filename,'title_img_up');
						if(!$upload->upload())msg('업로드 파일이 올바르지 않습니다.',-1);
					}
				}

				if($filename) $_POST['title_img_url'] = 'http://'.$_SERVER['HTTP_HOST'].''.$cfg['rootDir'].str_replace('../..', '', $dir).'/'.$filename;
			}
		}
		unset($_POST['mode'], $_POST['x'], $_POST['y']);
		
		$json_res = $pAPI->basicScreenAdd($godo['sno'], $_POST);
		
		@unlink($dir.'/'.$filename);

		break;

	case "design_intro":	//인트로설정

		### 기본설정 API 통신 enamoo -> vercoop ###
		/*
		if($_POST['use'] == 'true') {

			if (isset($_FILES['intro_up'])) {

				$_BGFILES = array( 'intro' => $_FILES['intro_up'] );
				$userori = array( 'intro' => 'intro' . strrChr( $_FILES['intro_up']['name'], "." ) );
				outcallUpload( $_BGFILES, '/../../data/shoptouch/upload_img/', $userori );
			}
		}
		else {
			$_POST['intro'] = '';
		}
		*/

		if($_POST['use'] == 'true') {
			### 분류이미지 업로드 디렉토리 세팅
			$dir = "../../data/shoptouch/upload_img";
			if (!is_dir($dir)) {
				@mkdir($dir, 0707);
				@chmod($dir, 0707);
			}

			if($_FILES['intro_up']){
				$file_array = reverse_file_array($_FILES['intro_up']);
				for($i=0;$i<1;$i++){
					if($_FILES[intro_up][tmp_name][$i]){
						$tmp = explode('.',$_FILES[intro_up][name][$i]);
						$ext = strtolower($tmp[count($tmp) - 1]);
						$filename = 'tmp_intro'.'.'.$ext;
						$upload->upload_file($file_array[$i],$dir.'/'.$filename,'intro_up');
						if(!$upload->upload())msg('업로드 파일이 올바르지 않습니다.',-1);
					}
				}

				if($filename) $arr['img_url'] = 'http://'.$_SERVER['HTTP_HOST'].''.$cfg['rootDir'].str_replace('../..', '', $dir).'/'.$filename;
			}
		}
		unset($_POST['mode'], $_POST['x'], $_POST['y']);
		
		$arr['use'] = $_POST['use'];
		$arr['effect'] = $_POST['effect'];
		
		$json_res = $pAPI->startScreenAdd($godo['sno'], $arr);
		
		@unlink($dir.'/'.$filename);

		break;

	case "mymenu":	//사용여부

		for($i=0; $i<count($_POST['menu_idx']); $i++) {
			if($_POST['menu_idx'][$i]) {
				$arr['menu_idx'] = $_POST['menu_idx'][$i];
				if($_POST['visibility'][$i] == 'true') {
					$arr['visibility'] = 'true';
				}
				else {
					$arr['visibility'] = 'false';
				}
				
				
				$json_res = $pAPI->myMenuModify($godo['sno'], $arr);

				
			}
			else {
				$arr['menu_name'] = $_POST['menu_name'][$i];
				$arr['menu_web_url'] =  $_POST['menu_web_url'][$i];
				
				if($_POST['visibility'][$i] == 'true') {
					$arr['visibility'] = 'true';
				}
				else {
					$arr['visibility'] = 'false';
				}

				$json_res = $pAPI->myMenuAdd($godo['sno'], $arr);
			}
		}

		
		### 나의메뉴 설정 API 통신 enamoo -> vercoop ###

		break;

	case "chgCategoryHidden":

		$arr = Array();

		if($_GET['hidden'] == '1') {
			$arr['visible'] = 'false';
		}
		else {
			$arr['visible'] = 'true';
		}
		$arr['menu_idx'] = $_GET['category'];

		$json_data = $pAPI->getMainMenuItem($godo['sno'],$arr['menu_idx']);
		$data = $json->decode($json_data);
		### 기존 필요 DATA ###
		$arr['parent_idx'] = $data['parent_idx'];
		$arr['menu_name'] = $data['menu_name'];
		$arr['order_number'] = $data['order_number'];
		
		$json_res = $pAPI->mainMenuModify($godo['sno'], $arr);
		$res = $json->decode($json_res);

		if(!empty($data['menu']) && is_array($data['menu'])) {
			
			foreach($data['menu'] as $row_menu){
				$sub_arr = Array();
				$sub_arr['visible'] = $arr['visible'];
				$sub_arr['menu_idx'] = $arr['menu_idx'];
				$sub_arr['parent_idx'] = $row_menu['parent_idx'];
				$sub_arr['order_number'] = $row_menu['order_number'];

				$sub_json_res = $pAPI->mainMenuModify($godo['sno'], $sub_arr);
				$sub_res = $json->decode($sub_json_res);
			}
		}

		if($res['result']['code'] == '000') {
			echo 'OK';
		}
		else {
			echo $res['result']['msg'];
		}
		exit;
		break;

	case "chgCategorySort":

		if ($_POST[cate1]) {
			
			foreach($_POST[cate1] as $k => $v) {
				$arr['menu_idx'] = $v;

				$json_data = $pAPI->getMainMenuItem($godo['sno'],$arr['menu_idx']);

				$data = $json->decode($json_data);
				
				### 기존 필요 DATA ###
				$arr['parent_idx'] = $data['parent_idx'];
				$arr['menu_name'] = $data['name'];
				$arr['order_number'] = $k + 1;
				
				$json_res = $pAPI->mainMenuModify($godo['sno'], $arr);
				$res = $json->decode($json_res);	
				
			}
		}

		if($_POST[cate2]) {
			foreach($_POST[cate2] as $k => $v) {
				$arr['menu_idx'] = $v;

				$json_data = $pAPI->getMainMenuItem($godo['sno'],$arr['menu_idx']);
				$data = $json->decode($json_data);

				### 기존 필요 DATA ###
				$arr['parent_idx'] = $data['parent_idx'];
				$arr['menu_name'] = $data['menu_name'];
				$arr['order_number'] = $k +1;

				$json_res = $pAPI->mainMenuModify($godo['sno'], $arr);
				$res = $json->decode($json_res);
			}
		}

		go("shopTouch_category.php?shopTouch_category=$_POST[category]");
		break;
		exit;
	case "chgCategoryShift":
		
		ob_start();
		$json_var = array('old' => array(), 'new' => array());

		$menu_idx = $_GET['ShiftCategory'];
		$target_idx = $_GET['targetCategory'];
		if(!$target_idx) $target_idx = 0;
		
		$arr = Array(); 
		$arr['menu_idx'] = $menu_idx;

		$json_data = $pAPI->getMainMenuItem($godo['sno'],$arr['menu_idx']);

		$data = $json->decode($json_data);
		
		if($target_idx) {
			$target_arr = Array();
			$target_arr['menu_idx'] = $target_idx;

			$json_target_data = $pAPI->getMainMenuItem($godo['sno'], $target_arr['menu_idx']);
			$target_data = $json->decode($json_target_data);
		}
		
		$order_number = 0;
		
		
		if(!empty($target_data) && is_Array($target_data)) {
			if($target_data['parent_idx']) {
				$msg_arr['msg'] = "최하위분류 밑으로는 이동이 불가능합니다.";
				echo $json->encode($msg_arr);
				exit;
			}
			else {
				if(!empty($data['menu']) && is_Array($data['menu'])) {
					$msg_arr['msg'] = "해당분류로의 이동은 불가능합니다\n이동하게되면 2차분류를 넘어서기 때문입니다.";
					echo $json->encode($msg_arr);
					exit;
				}

				if($target_data['menu'] && is_Array($target_data['menu'])) {
					foreach($target_data['menu'] as $row_target_menu) {
						if($row_target_menu['order_number'] >= $order_number) {
							$order_number = $row_target_menu['order_number'] + 1;
						}
					}
				}

				$data['visible'] = $target_data['visible'];
			}
		}
		
		$mod_arr = Array();
		$mod_arr['menu_idx'] = (int)$menu_idx;
		$mod_arr['menu_name'] = $data['name'];
		$mod_arr['parent_idx'] = $target_idx;
		$mod_arr['order_number'] =$order_number;
		$mod_arr['menu_icon_file'] = '';		
		$mod_arr['visible'] = $data['visible'];
		
		$json_res = $pAPI->mainMenuModify($godo['sno'], $mod_arr);
		$res = $json->decode($json_res);
		
		if($res['result']['code'] == '000') {
			$json_var['old'][] = $menu_idx;
			$json_var['new'][] = $menu_idx;
		}
		else {
			$msg_arr['msg'] = "분류 이동에 실패했습니다. 잠시 후 다시 시도해 주세요";
			echo $json->encode($msg_arr);
			exit;
		}
		$output = $json->encode($json_var);
		$obOut = ob_get_clean();

		if ($obOut != '') echo $obOut;
		else echo $output;
		exit;
		break;

	case "del_category":
		
		### 카테고리 삭제 ###
		if($_POST['category']) {
			$arr = Array();
			$arr['menu_idx'] = $_POST['category'];
			$pAPI->mainMenuDeleteIcon($godo['sno'], $arr);
			$ret = $pAPI->mainMenuDelete($godo['sno'], $arr);
			$arr_ret = $json->decode($ret);
			unset($arr);
		}
		
		go('shopTouch_category.php', 'top');
		exit;
		break;

	case "mod_category":
		### 카테고리 수정 ###
		if($_POST['category']) {
			$arr = Array();
			$arr['menu_idx'] = $_POST['category'];
			$arr['order_number'] = $_POST['order_number'];
			$arr['menu_name'] = $_POST['catnm'];
			$arr['visible'] = $_POST['visible'];
			
			### 분류이미지 업로드 디렉토리 세팅
			$dir = "../../data/shoptouch/category";
			if (!is_dir($dir)) {
				@mkdir($dir, 0707);
				@chmod($dir, 0707);
			}

			### 구분자 정의
			$tail = '_icon';
			$del_tmp = explode('.',$_POST['h_img']);
			$del_ext = strtolower($del_tmp[count($del_tmp) - 1]);
			### 기존 분류이미지
			$imgName = $_POST['category'].$tail.'.'.$del_ext;
			
			### 카테고리 아이콘 삭제
			for($i=0;$i<1;$i++){
				if( $_POST['chkimg_'.$i] || $_FILES[img][tmp_name][$i] ) {
					$del_arr = Array();
					$del_arr['menu_idx'] = $_POST['category'];
					$pAPI->mainMenuDeleteIcon($godo['sno'], $del_arr);
					@unlink($dir.'/'. $imgName);
					$arr['menu_icon_url'] = '';
				}
			}

			### 카테고리 아이콘 업로드
			if($_FILES['img']){
			$file_array = reverse_file_array($_FILES['img']);
				for($i=0;$i<1;$i++){
					if($_FILES[img][tmp_name][$i]){
						$tmp = explode('.',$_FILES[img][name][$i]);
						$ext = strtolower($tmp[count($tmp) - 1]);
						$filename = $_POST['category'].$tail.".".$ext;
						$upload->upload_file($file_array[$i],$dir.'/'.$filename,'image');
						if(!$upload->upload())msg('업로드 파일이 올바르지 않습니다.',-1);
					}
				}

				if($filename) $arr['menu_icon_url'] = 'http://'.$_SERVER['HTTP_HOST'].''.$cfg['rootDir'].str_replace('../..', '', $dir).'/'.$filename;
			}

			$json_res = $pAPI->mainMenuModify($godo['sno'], $arr);
			$res = $json->decode($json_res);
			
			unset($arr);
		}		
		### 하위 분류 생성 ###
		if($_POST['sub']) {
			$arr = Array();
			$arr['menu_name'] = $_POST['sub'];
			if(!$_POST['category']) $_POST['category'] = '0';
			$arr['parent_idx'] = $_POST['category'];
			
			$sub_json_res = $pAPI->mainMenuAdd($godo['sno'], $arr);
			$sub_res = $json->decode($sub_json_res);
		}
		
		$map_arr['menu_idx'] = $_POST['category'];

		$i = 0;

		$json_arr = array();
		$data_arr = array();

		if(is_array($_POST['enamoo_cate']) && !empty($_POST['enamoo_cate'])) {
			foreach($_POST['enamoo_cate'] as $enamoo_cate) {
				$tmp_arr = Array();
				$tmp_arr['type'] = 'category';
				$tmp_arr['value'] = $enamoo_cate;

				$json_arr['data'][] = $tmp_arr;	
			}
		}
		else {
			$tmp_arr = Array();
			$tmp_arr['type'] = 'category';
			$tmp_arr['value'] = '';

			$json_arr['data'][] = $tmp_arr;	
		}
		
		$map_arr['data'] = $json->encode($json_arr);
		
		
		$json_res = $pAPI->menuTemplateAdd($godo['sno'], $map_arr);
		
		$res = $json->decode($json_res);
		
		### API통신이 너무 느려 우선 막음 정렬순서 변경 기능 dn 2012-01-02 ###
		echo "<script>parent.document.forms[0].category.value='$_POST[category]';parent.document.forms[0].submit()</script>";	
		exit;

		go("shopTouch_category.php?ifrmScroll=1&shopTouch_category=$_POST[category]", "parent");
		break;
	
	case "getCategory":
		
		$json_category = $pAPI->getMainMenu($godo['sno']);
		
		echo iconv('utf-8', 'euc-kr', $json_category);
		exit;
		break;

	case "main_popup_use":

		$arr_no = $_POST['no'];
		$tmp_display = $_POST['use_display'];
		
		for($i = 0; $i<count($arr_no); $i++ ) {
			$query = $db->_query_print('UPDATE '.GD_SHOPTOUCH_DISPLAY.' SET use_display=[s] WHERE no=[i]', '0', $arr_no[$i]);
			$db->query($query);
		}
		
		if($_POST['use_display'] != '0') {
			$upd_query = $db->_query_print('UPDATE '.GD_SHOPTOUCH_DISPLAY.' SET use_display=[s] WHERE no=[i]', '1', $_POST['use_display']);
			$db->query($upd_query);
			$arr['notice'] = 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_disp/popup.php';
		}
		else {
			$arr['notice'] = '';

		}
		
		$arr['shop_nm'] = $cfg['shopName'];
		$arr['login'] = 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_mem/login.php';
		$arr['logout'] = 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_mem/logout.php';

		$arr['info'] = 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_disp/company_info.php';
		//debug($arr);
		
		$json_data = $pAPI->setShopInfo($godo['sno'],$arr);
		
		$data = $json->decode($json_data);
		//debug($data);
		//exit;
		break;

	case "del_main_popup" :
		$no = $_POST['no'];
		$query = $db->_query_print('DELETE FROM '.GD_SHOPTOUCH_DISPLAY.' WHERE no=[i] AND mode=[s]', $no, 'popup');
		$db->query($query);

		break;
	
	case "popup_display" :
		
	
		$arr['mode'] = 'popup';
		$arr['link_type'] = $_POST['link_type'];
		
		if($arr['link_type']=='1') {
			$arr['category'] = $_POST['link_path'];
			$arr['sort'] = 0;
			$arr['image_up'] = $_POST['image_up'];
			$arr['popup_nm'] = $_POST['popup_nm'];

			$use_template = $pAPI->getUseTemplate($godo['sno'], $use_arr);

			$arr_use_template = $json->decode($use_template);
			$use_template_idx = $arr_use_template['tp_idx'];

			$arr['tp_idx'] = $use_template_idx;
		}
		else if($arr['link_type'] == '2') {
			$arr['goodsno'] = $_POST['link_path'];
			$arr['sort'] = 0;
			$arr['image_up'] = $_POST['image_up'];
			$arr['popup_nm'] = $_POST['popup_nm'];
		}
		else if($arr['link_type'] == '3') {
			$arr['link_url'] = $_POST['link_url'];
			$arr['sort'] = 0;
			$arr['image_up'] = $_POST['image_up'];
			$arr['popup_nm'] = $_POST['popup_nm'];
		}

		$tail = array('_popup');
		$dir = "../../data/shoptouch/popup";
		if (!is_dir($dir)) {
			@mkdir($dir, 0707);
			@chmod($dir, 0707);
		}
		$main_img = $_POST['main_img'];
		$main_image = Array();

		if($arr['image_up'] == '1') {	
	
		
			if($_FILES['img']){
			$file_array = reverse_file_array($_FILES['img']);
				for($i=0;$i<count($file_array);$i++){
					if($_FILES[img][tmp_name][$i]){
						@unlink($dir.'/'. $main_img[$i]);
						$tmp = explode('.',$_FILES[img][name][$i]);
						$ext = strtolower($tmp[count($tmp) - 1]);
						$filename = date('Ymdhis').$tail[$i].".".$ext;
						$upload->upload_file($file_array[$i],$dir.'/'.$filename,'image');
						if(!$upload->upload())msg('업로드 파일이 올바르지 않습니다.',-1);
						
						$main_image[$i] = $filename;
					}

					if(!$main_image[$i]) $main_image[$i] = $main_img[$i];
				}
			}

		}
		else if($arr['image_up'] == '2') {
			if(!empty($main_img)) {
				for($i=0;$i<count($main_img);$i++){
					@unlink($dir.'/'. $main_img[$i]);
				}
			}
		}
		
		
		
		$arr['main_img'] = $main_image[0];

		if($_POST['no']) {	//수정
			$query = $db->_query_print('UPDATE '.GD_SHOPTOUCH_DISPLAY.' SET [cv] WHERE no=[i]', $arr, $_POST['no']);

			
		}
		else {	//입력

			$query = $db->_query_print('INSERT INTO '.GD_SHOPTOUCH_DISPLAY.' SET [cv]', $arr);
		}

		$db->query($query);

		msg('저장 되었습니다.', 'shopTouch_main_popup.php', 'parent');
		exit;
		break;

	### 템플릿 설정 ###
	case 'design_template' :

		if($_POST['menu_idx'] == 'main') {
			
			$arr['tp_idx'] = $_POST['tp_idx'];
			$json_res = $pAPI->mainTemplateAdd($godo['sno'], $arr);
		}
		else if($_POST['menu_idx'] == 'detail'){
			$arr['tp_idx'] = $_POST['tp_idx'];
			$json_res = $pAPI->detailTemplateAdd($godo['sno'], $arr);
		}
		else {
			$arr['menu_idx'] = $_POST['menu_idx'];
			$arr['tp_idx'] = $_POST['tp_idx'];
			$json_res = $pAPI->menuTemplateAdd($godo['sno'], $arr);
		}

		$res = $json->decode($json_res);
		
		if($res['result']['code'] == '000') {
			$msg = '설정 되었습니다.';
		}
		else {
			$msg = $res['result']['msg'];
		}

		msg($msg);
		break;

	### 템플릿 삭제 ###
	case 'del_template' :
	
		$arr['tp_idx'] = $_POST['tp_idx'];
		$json_res = $pAPI->myTemplateDelete($godo['sno'], $arr);

		$res = $json->decode($json_res);
		
		if($res['result']['code'] == '000') {
			$msg = '삭제 되었습니다.';
		}
		else {
			$msg = $res['result']['msg'];
		}

		msg($msg);
		break;
	
	### 카테고리 자동생성 1,2차 분류 ###
	case 'autoCreateCategory' :
		
		$select_query_1depth = $db->_query_print('SELECT category, catnm, sort, hidden FROM '.GD_CATEGORY.' WHERE length(category)=[i] ORDER BY sort ASC', 3);
		$res_1depth = $db->_select($select_query_1depth);
		
		$cate_cnt1 = 0;
		$cate_cnt2 = 0;
		if(!empty($res_1depth) && is_array($res_1depth)) {
			foreach($res_1depth as $row_1depth) {
				
				$cate_cnt1 ++;
				$arr['menu_name'] = $row_1depth['catnm'];
				$arr['parent_idx'] = '0';				
				$json_res = $pAPI->mainMenuAdd($godo['sno'], $arr);
				$res = $json->decode($json_res);
				unset($arr);
				if($res['result']['code'] != '000') {
					echo 'FAIL';

					//msg('쇼핑몰 App 카테고리 추가 중 오류가 발생 하였습니다. 다시 시도해 주세요', 'shopTouch_category.php', 'parent');
					exit;
				}
				else {
					
					$parent_idx = $res['menu_idx'];

					$map_arr = Array();
					$tmp_arr = Array();
					$json_arr = Array();
					
					$tmp_arr['type'] = 'category';
					$tmp_arr['value'] = $row_1depth['category'];
					$json_arr['data'][] = $tmp_arr;	

					$map_arr['menu_idx'] = $parent_idx;	
					$map_arr['data'] = $json->encode($json_arr);
					
					$json_res = $pAPI->menuTemplateAdd($godo['sno'], $map_arr);
					
					unset($tmp_arr, $map_arr, $json_arr);
				}

				$category_1depth = $row_1depth['category'].'%';
				$select_query_2depth = $db->_query_print('SELECT category, catnm, sort, hidden FROM '.GD_CATEGORY.' WHERE length(category)=[i] AND category like [s] ORDER BY sort ASC', 6, $category_1depth);

				$res_2depth = $db->_select($select_query_2depth);
				
				if(!empty($res_2depth) && is_array($res_2depth)) {
					foreach($res_2depth as $row_2depth) {
						
						$cate_cnt2 ++;

						$arr['menu_name'] = $row_2depth['catnm'];
						$arr['parent_idx'] = $parent_idx;
						$json_sub_res = $pAPI->mainMenuAdd($godo['sno'], $arr);
						$sub_res = $json->decode($json_sub_res);
						unset($arr);
						if($sub_res['result']['code'] != '000') {
							echo 'FAIL';
							//msg('쇼핑몰 App 카테고리 추가 중 오류가 발생 하였습니다. 다시 시도해 주세요', 'shopTouch_category.php', 'parent');
							exit;
						}
						else {
							$child_idx = $sub_res['menu_idx'];

							$map_arr = Array();
							$tmp_arr = Array();
							$json_arr = Array();
							
							$tmp_arr['type'] = 'category';
							$tmp_arr['value'] = $row_2depth['category'];
							$json_arr['data'][] = $tmp_arr;	

							$map_arr['menu_idx'] = $child_idx;	
							$map_arr['data'] = $json->encode($json_arr);

							$json_res = $pAPI->menuTemplateAdd($godo['sno'], $map_arr);
							unset($map_arr);

						}
					}
				}
			}
		}
		
		echo $cate_cnt1.'||'.$cate_cnt2;		
		exit;
		break;
	
	### 상품 출력여부 설정 ###
	case 'open_goods' : 

		$cmd_goodsno = Array();

		if($_POST['range_type1'] == 'query_all') {

			$goods_query = $_POST['query'];

			$goods_query = str_replace("\'", "'", $goods_query);
			$res_goods = $db->_select($goods_query);

			if(!empty($res_goods) && is_array($res_goods)) {
				foreach($res_goods as $row_goods) {
					$cmd_goodsno[] = $row_goods['goodsno'];
				}
			}
		}
		else {
			$cmd_goodsno = $_POST['chk'];
		}
		
		foreach($cmd_goodsno as $val_cmd_goodsno) {
			if($_POST['cmd_open']) {
				$open_upd_query = $db->_query_print('UPDATE '.GD_GOODS.' SET open=[i] WHERE goodsno=[i]', (int)substr($_POST['cmd_open'],-1), $val_cmd_goodsno);
				$db->query($open_upd_query);
			}

			if($_POST['cmd_open_shoptouch']) {
				$open_shoptouch_upd_query = $db->_query_print('UPDATE '.GD_SHOPTOUCH_GOODS.' SET open_shoptouch=[i] WHERE goodsno=[i]', (int)substr($_POST['cmd_open_shoptouch'],-1), $val_cmd_goodsno);
				$db->query($open_shoptouch_upd_query);
			}
		}

		break;

	### 쇼핑몰 App 상품 정보 삭제 ###
	case 'delShoptouchGoods' :
		$goodsno = $_GET['goodsno'];
		$goods_query = $db->_query_print('SELECT * FROM '.GD_SHOPTOUCH_GOODS.' WHERE goodsno=[i]', $goodsno);
		
		$res_goods = $db->_select($goods_query);
		$row_goods = $res_goods[0];
		$img_shoptouch = explode('|', $row_goods['img_shoptouch']);

		// 클라우드 이미지 삭제 처리 들어가야 함. //

		$del_query = $db->_query_print('DELETE FROM '.GD_SHOPTOUCH_GOODS.' WHERE goodsno=[i]', $goodsno);
		$db->query($del_query);

		break;

	### 쇼핑몰 App 상품 복사 ###
	case 'copyShoptouchGoods' :
		$goodsno = $_GET['goodsno'];
		$ngoodsno = copyGoods($goodsno);

		$goods_query = $db->_query_print('SELECT * FROM '.GD_SHOPTOUCH_GOODS.' WHERE goodsno=[i]', $goodsno);
		
		$res_goods = $db->_select($goods_query);
		$row_goods = $res_goods[0];
		$img_shoptouch = explode('|', $row_goods['img_shoptouch']);
		
		$ins_arr = Array();
		$ins_arr['goodsno'] = $ngoodsno;
		$ins_arr['open_shoptouch'] = $row_goods['open_shoptouch'];
		$ins_arr['img_shoptouch'] = implode('|', $img_shoptouch);
		$ins_arr['slongdesc'] = $row_goods['slongdesc'];
				
		$ins_query = $db->_query_print('INSERT INTO '.GD_SHOPTOUCH_GOODS.' SET [cv], sregdt=now()', $ins_arr);
		$db->query($ins_query);

		break;

	### 쇼핑몰 App 상품 전송 ###
	case 'send_goods' :
		$cmd_row_goods = Array();

		if($_POST['range_type1'] == 'query_all') {

			$goods_query = $_POST['query'];
			$res_goods = $db->_select($goods_query);

			if(!empty($res_goods) && is_array($res_goods)) {
				foreach($res_goods as $row_goods) {
					$cmd_row_goods[] = $row_goods['goodsno'];
				}
			}
		}
		else {
			$cmd_goodsno = $_POST['chk'];
		}
		
		foreach($cmd_goodsno as $val_cmd_goodsno) {
			
			$sel_goods_query = $db->_query_print('SELECT * FROM '.GD_GOODS.' WHERE goodsno=[i]', $val_cmd_goodsno);
			$res_sel_goods = $db->_select($sel_goods_query);
			$row_sel_goods = $res_sel_goods[0];

			$tmp_img_shoptouch = explode('|', $row_sel_goods['img_l']);

			// 클라우드 이미지 등록 처리 들어가야 함. //
			$img_shoptouch = Array();
			
			if(!empty($tmp_img_shoptouch) && is_array($tmp_img_shoptouch)) {
				foreach($tmp_img_shoptouch as $img_nm) {
					$arr = Array();
					if (preg_match('/^http(s)?:\/\/.+$/',$img_nm)) {
						$arr['img_url'] = $img_nm;
					}
					else {
						$arr['img_url'] = 'http://'.$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/data/goods/'.$img_nm;
					}
					
					$tmp_ret = $pAPI->contentsUpload($godo['sno'], $arr);
					$ret = $json->decode($tmp_ret);
					if($ret['result']['code'] == '000') {
						$img_shoptouch[] = $ret['img_shoptouch'];
					}					
					unset($arr);
				}
			}

			$arr_ins = Array();
			$arr_ins['goodsno'] = $row_sel_goods['goodsno'];
			$arr_ins['open_shoptouch'] = $row_sel_goods['open'];
			$arr_ins['img_shoptouch'] = implode('|', $img_shoptouch);
			$arr_ins['slongdesc'] = $row_sel_goods['longdesc'];

			$ins_query = $db->_query_print('INSERT INTO '.GD_SHOPTOUCH_GOODS.' SET [cv], sregdt=now()', $arr_ins);
			$db->query($ins_query);
		}

		msg('쇼핑몰 App 상품으로 전송하였습니다.\n전송된상품은 쇼핑몰 App상품리스트 에서 확인하실 수 있습니다.', $_SERVER[HTTP_REFERER], 'parent');

		break;
	
	### 배송정책 안내 설정 ###
	case 'delivery_policy_set' :
		
		## 배송안내 ##
		$chk_query = $db->_query_print('SELECT count(*) as chk_cnt FROM gd_env WHERE category=[s] AND name=[s]', 'shoptouch', 'delivery_info');
		$chk_res = $db->_select($chk_query);
		$arr_ins = Array();
		if($chk_res[0]['chk_cnt'] < 1) {
			$arr_ins['category'] = 'shoptouch';
			$arr_ins['name'] = 'delivery_info';
			$arr_ins['value'] = $_POST['delivery_info'];
			$ins_query = $db->_query_print('INSERT INTO gd_env SET [cv]', $arr_ins);
			
		}
		else {
			$ins_query = $db->_query_print('UPDATE gd_env SET value=[s] WHERE category=[s] AND name=[s]', $_POST['delivery_info'], 'shoptouch', 'delivery_info');
		}
		
		$res = $db->query($ins_query);

		unset($chk_query, $chk_res, $arr_ins, $ins_query);

		## 반품안내 ##
		$chk_query = $db->_query_print('SELECT count(*) as chk_cnt FROM gd_env WHERE category=[s] AND name=[s]', 'shoptouch', 'return_info');
		$chk_res = $db->_select($chk_query);
		$arr_ins = Array();
		if($chk_res[0]['chk_cnt'] < 1) {
			$arr_ins['category'] = 'shoptouch';
			$arr_ins['name'] = 'return_info';
			$arr_ins['value'] = $_POST['return_info'];
			$ins_query = $db->_query_print('INSERT INTO gd_env SET [cv]', $arr_ins);
			
		}
		else {
			$ins_query = $db->_query_print('UPDATE gd_env SET value=[s] WHERE category=[s] AND name=[s]', $_POST['return_info'], 'shoptouch', 'return_info');
		}
	
		$res = $db->query($ins_query);

		unset($chk_query, $chk_res, $arr_ins, $ins_query);

		break;

	### 쇼핑몰 App 카테고리 - e나무 카테고리 매핑 ###
	case 'mapping' :
		
		$arr['menu_idx'] = $_POST['menu_idx'];

		$i = 0;

		$json_arr = array();
		$data_arr = array();

		foreach($_POST['enamoo_cate'] as $enamoo_cate) {
			$tmp_arr = Array();
			$tmp_arr['type'] = 'category';
			$tmp_arr['value'] = $enamoo_cate;

			$json_arr['data'][] = $tmp_arr;	
		}
		
		$arr['data'] = $json->encode($json_arr);
		
		$json_res = $pAPI->menuTemplateAdd($godo['sno'], $arr);
		
		$res = $json->decode($json_res);
		
		break;
	
	### 그룹 Sid 가져오기 Ajax 실행 ###
	case 'getSid' :
		$json_ret = $pAPI->getGroupSid($godo['sno']);
		$ret = $json->decode($json_ret);
		echo $ret['sid'];
		exit;
		break;

	### 쇼핑몰 App 상품 전송 ###
	case 'send_goods_ajax' :

		$goodsno = $_GET['goodsno'];
		
		$sel_goods_query = $db->_query_print('SELECT * FROM '.GD_GOODS.' WHERE goodsno=[i]', $goodsno);
		$res_sel_goods = $db->_select($sel_goods_query);
		$row_sel_goods = $res_sel_goods[0];

		$tmp_img_shoptouch = explode('|', $row_sel_goods['img_l']);

		// 클라우드 이미지 등록 처리 들어가야 함. //
		$img_shoptouch = Array();
		
		if(!empty($tmp_img_shoptouch) && is_array($tmp_img_shoptouch)) {
			foreach($tmp_img_shoptouch as $img_nm) {
				$arr = Array();
				if (preg_match('/^http(s)?:\/\/.+$/',$img_nm)) {
					$arr['img_url'] = $img_nm;
				}
				else {
					$arr['img_url'] = 'http://'.$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/data/goods/'.$img_nm;
				}
				
				$tmp_ret = $pAPI->contentsUpload($godo['sno'], $arr);
				$ret = $json->decode($tmp_ret);
				if($ret['result']['code'] == '000') {
					$img_shoptouch[] = $ret['img_shoptouch'];
				}					
				unset($arr);
			}
		}

		$arr_ins = Array();
		$arr_ins['goodsno'] = $row_sel_goods['goodsno'];
		$arr_ins['open_shoptouch'] = $row_sel_goods['open'];
		$arr_ins['img_shoptouch'] = implode('|', $img_shoptouch);
		$arr_ins['slongdesc'] = $row_sel_goods['longdesc'];

		$ins_query = $db->_query_print('INSERT INTO '.GD_SHOPTOUCH_GOODS.' SET [cv], sregdt=now()', $arr_ins);
		$ret = $db->query($ins_query);

		if($ret) {
			echo '0||전송성공';
		}
		else {
			echo '9||전송실패 다시 시도해주세요';
		}

		exit;

		break;
		
}

go($_SERVER[HTTP_REFERER]);

?>
