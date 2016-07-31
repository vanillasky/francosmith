<?
include "../lib.php";
require_once("../../lib/qfile.class.php");
require_once("../../lib/upload.lib.php");
require_once("../../lib/json.class.php");
include_once dirname(__FILE__) . "/webftp/webftp.class_outcall.php";

include "../../conf/config.php";
include "../../conf/config.mobileShop.php";
$cfgMobileShop = (array)$cfgMobileShop;

### 상수 설정
## KHS 추가  2012-10-05
if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

$qfile = new qfile();

// 현재 사용 중인 모바일 스킨이 없을 때 기본 세팅 스킨명 저장.
if(empty($cfg['tplSkinMobile']) === true){

	$cfg['tplSkinMobile'] = $cfg['tplSkinMobileWork'] = "light";

	$cfg = array_map("stripslashes",$cfg);
	$cfg = array_map("addslashes",$cfg);

	$qfile->open( $path = dirname(__FILE__) . "/../../conf/config.php");
	$qfile->write("<?\n\n" );
	$qfile->write("\$cfg = array(\n" );

	foreach ( $cfg as $k => $v ){
		if ( $v === true ) $qfile->write("'$k'\t\t\t=> true,\n" );
		else if ( $v === false ) $qfile->write("'$k'\t\t\t=> false,\n" );
		else $qfile->write("'$k'\t\t\t=> '$v',\n" );
	}

	$qfile->write(");\n\n" );
	$qfile->write("?>" );
	$qfile->close();
	@chMod( $path, 0757 );

	$cfgMobileShop = array_map("stripslashes",$cfgMobileShop);
	$cfgMobileShop = array_map("addslashes",$cfgMobileShop);
	$cfgMobileShop['tplSkinMobile'] = "light";

	$qfile->open($path = dirname(__FILE__) . "/../../conf/config.mobileShop.php");
	$qfile->write("<? \n");
	$qfile->write("\$cfgMobileShop = array( \n");
	foreach ($cfgMobileShop as $k=>$v) $qfile->write("'$k' => '$v', \n");
	$qfile->write(") \n;");
	$qfile->write("?>");
	$qfile->close();
	@chMod( $path, 0757 );
}


$mode = ($_POST[mode]) ? $_POST[mode] : $_GET[mode];
unset($_POST[mode]); unset($_POST[x]); unset($_POST[y]);

switch($mode){

	case "config":

		$cfgMobileShop = array_map("stripslashes",$cfgMobileShop);
		$cfgMobileShop = array_map("addslashes",$cfgMobileShop);

		# 모바일샵 루트경로
		if(!$cfgMobileShop['mobileShopRootDir']) $cfgMobileShop['mobileShopRootDir'] = '/m2';

		# 로고이미지
		if (isset($_FILES['mobileShopLogo_up'])){
			$_BGFILES = array( 'mobileShopLogo_up' => $_FILES['mobileShopLogo_up'] );
			$userori = array( 'mobileShopLogo' => 'mobileShopLogo' . strrChr( $_FILES['mobileShopLogo_up']['name'], "." ) );

			outcallUpload( $_BGFILES, '/../../../data/skin_mobileV2/'.$_POST['tplSkinMobile'].'/', $userori );
			unset($_POST[mobileShopLogo_del]);
		}
		else $_POST[mobileShopLogo] = $cfgMobileShop[mobileShopLogo];

		# 아이콘이미지
		if (isset($_FILES['mobileShopIcon_up'])){
			$_BGFILES = array( 'mobileShopIcon_up' => $_FILES['mobileShopIcon_up'] );
			$userori = array( 'mobileShopIcon' => 'mobileShopIcon' . strrChr( $_FILES['mobileShopIcon_up']['name'], "." ) );

			outcallUpload( $_BGFILES, '/../../../data/skin_mobileV2/'.$_POST['tplSkinMobile'].'/', $userori );
			unset($_POST[mobileShopIcon_del]);
		}
		else $_POST[mobileShopIcon] = $cfgMobileShop[mobileShopIcon];

		# 메인배너이미지
		if (isset($_FILES['mobileShopMainBanner_up'])){
			$_BGFILES = array( 'mobileShopMainBanner_up' => $_FILES['mobileShopMainBanner_up'] );
			$userori = array( 'mobileShopMainBanner' => 'mobileShopMainBanner' . strrChr( $_FILES['mobileShopMainBanner_up']['name'], "." ) );

			outcallUpload( $_BGFILES, '/../../../data/skin_mobileV2/'.$_POST['tplSkinMobile'].'/', $userori );
			unset($_POST[mobileShopMainBanner_del]);
		}
		else $_POST[mobileShopMainBanner] = $cfgMobileShop[mobileShopMainBanner];

		$cfgMobileShop['mobileShopLogo']	= $_POST['mobileShopLogo'];
		$cfgMobileShop['mobileShopIcon']	= $_POST['mobileShopIcon'];
		$cfgMobileShop['mobileShopMainBanner']	= $_POST['mobileShopMainBanner'];
		$cfgMobileShop['useMobileShop']		= $_POST['useMobileShop'];
		$cfgMobileShop['useOffCanvas']		= $_POST['useOffCanvas'];// @qnibus 2015-07 오프캔버스 사용여부 설정 추가
		$cfgMobileShop['offCanvasBtnColor']		= array_shift($_POST['offCanvasBtnColor']);// @qnibus 2015-07 오프캔버스 버튼색상 설정 추가
		$cfgMobileShop['tplSkinMobile']		= $_POST['tplSkinMobile'];

		$qfile->open("../../conf/config.mobileShop.php");
		$qfile->write("<? \n");
		$qfile->write("\$cfgMobileShop = array( \n");
		foreach ($cfgMobileShop as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();

		$cfg['tplSkinMobile'] = $_POST['tplSkinMobile'];

		$cfg = array_map("stripslashes",$cfg);
		$cfg = array_map("addslashes",$cfg);

		$qfile->open( $path = dirname(__FILE__) . "/../../conf/config.php");
		$qfile->write("<?\n\n" );
		$qfile->write("\$cfg = array(\n" );

		foreach ( $cfg as $k => $v ){
			if ( $v === true ) $qfile->write("'$k'\t\t\t=> true,\n" );
			else if ( $v === false ) $qfile->write("'$k'\t\t\t=> false,\n" );
			else $qfile->write("'$k'\t\t\t=> '$v',\n" );
		}

		$qfile->write(");\n\n" );
		$qfile->write("?>" );
		$qfile->close();
		@chMod( $path, 0757 );

		# 사용여부 로그기록
		@readurl("http://gongji.godo.co.kr/userinterface/mobileshop_log.php?use=".$cfgMobileShop['useMobileShop']."&shopSno=".$godo['sno']."&shopHost=".$_SERVER['HTTP_HOST']."&ecCode=".$godo['ecCode']);

		break;

	case "mod_intro":

		$_SERVER[HTTP_REFERER] .= '?' . time();

		{ // 환경파일 저장

			# 기존 정보 비우기
			unset($cfg);

			# 스킨별 기본 정보 design_basicMobileV2_light.php /
			if(is_file(dirname(__FILE__) . "/../../conf/design_basicMobileV2_".$_POST['tplSkinMobileWork'].".php")){
				include dirname(__FILE__) . "/../../conf/design_basicMobileV2_".$_POST['tplSkinMobileWork'].".php";
			}

			# 인트로 사용여부
			$cfg['introUseYNMobile']			= $_POST['introUseYNMobile'];

			# 인트로 종류
			$cfg['custom_landingpageMobile']			= ($cfg['introUseYNMobile'] == 'Y' && !isset($_POST['custom_landingpageMobile'])) ? 1 : $_POST['custom_landingpageMobile'];

			$cfg = array_map("stripslashes",$cfg);
			$cfg = array_map("addslashes",$cfg);

			$qfile->open( $path = dirname(__FILE__) . "/../../conf/design_basicMobileV2_".$_POST['tplSkinMobileWork'].".php");
			$qfile->write("<?\n" );

			foreach ( $cfg as $k => $v ){

				$qfile->write("\$cfg['".$k."']\t\t\t=\"".$v."\";\n" );
			}

			$qfile->write("?>" );
			$qfile->close();
			@chMod( $path, 0757 );
		}

		break;

	case "intro_save" :	// 인트로 디자인 저장

		{ // 디자인코디파일 저장
			$path = dirname(__FILE__) . "/../../data/skin/" . $_POST['tplSkinMobileWork'] . $_POST['skin_file'];
			if (!file_exists(dirname($path))) mkdir(dirname($path), 0757, true);
			$qfile->open( $path );
			if (ini_get('magic_quotes_gpc') == 1) $_POST['content'] = stripslashes( $_POST['content'] );
			$qfile->write($_POST['content'] );
			$qfile->close();
			@chMod( $path, 0757 );

			// 2013-10-13 slowj 히스토리관리
			if ($_POST['gd_preview'] !== '1') {
				save_design_history_file('skin', $_POST['tplSkinMobileWork'], $_POST['skin_file']);
			}
			else {
				echo '<script>parent.preview_popup();</script>';
			}
			// 2013-10-13 slowj 히스토리관리
		}

		break;

	case "config_view_set":

		$cfgMobileShop = array_map("stripslashes",$cfgMobileShop);
		$cfgMobileShop = array_map("addslashes",$cfgMobileShop);

		# 상품노출설정 변경시 db update
		if($_POST['vtype_goods']=='0' && $cfgMobileShop['vtype_goods']!=$_POST['vtype_goods']){
			$query = "update gd_goods set `open_mobile`=`open`;";
			$db->query($query);
		}

		# 카테고리노출설정 변경시 db update
		if($_POST['vtype_category']=='0' && $cfgMobileShop['vtype_category']!=$_POST['vtype_category']){
			$query = "update gd_category set `hidden_mobile`=`hidden`;";
			$db->query($query);
			$query_link = "update gd_goods_link set `hidden_mobile`=`hidden`;";
			$db->query($query_link);
		}

		# 모바일샵 루트경로
		if(!$cfgMobileShop['mobileShopRootDir']) $cfgMobileShop['mobileShopRootDir'] = '/m';


		$cfgMobileShop['vtype_main']		= $_POST['vtype_main'];
		$cfgMobileShop['vtype_goods']		= $_POST['vtype_goods'];
		$cfgMobileShop['vtype_category']	= $_POST['vtype_category'];
		$cfgMobileShop['vtype_goods_view_skin']	= $_POST['vtype_goods_view_skin'];
		$cfgMobileShop['goods_view_quick_menu_useyn']	= $_POST['goods_view_quick_menu_useyn'];

		$qfile->open("../../conf/config.mobileShop.php");
		$qfile->write("<? \n");
		$qfile->write("\$cfgMobileShop = array( \n");
		foreach ($cfgMobileShop as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();

		break;

	case "setVtypeMlongdesc":

		$file = "../../conf/config.mobileShop.php";

		$cfgMobileShop = array_map("stripslashes",$cfgMobileShop);
		$cfgMobileShop = array_map("addslashes",$cfgMobileShop);
		$cfgMobileShop['vtype_mlongdesc'] = $_GET['vtype_mlongdesc'];

		$qfile->open($file);
		$qfile->write("<? \n");
		$qfile->write("\$cfgMobileShop = array( \n");
		foreach ($cfgMobileShop as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();

		echo $_GET['vtype_mlongdesc'];
		exit;

		break;
/*	case "setPg":

		include "../../conf/config.pay.php";

		if (!$_POST['set']){
			unset($set['use_mobile']['c'],$set['use_mobile']['o'],$set['use_mobile']['v'],$set['use_mobile']['h']);
		}else{
			$_POST['set']['use_mobile']['a'] = $set['use']['a'];
			$set = array_merge($set,$_POST['set']);
		}

		$qfile->open("../../conf/config.pay.php");
		$qfile->write("<? \n");
		if ($set) foreach ($set as $k=>$v) foreach ($v as $k2=>$v2) $qfile->write("\$set['$k']['$k2'] = '$v2'; \n");
		$qfile->write("?>");
		$qfile->close();

		$qfile->open("../../conf/pg.escrow.mobile.php");
		$qfile->write("<? \n");
		$qfile->write("\$escrowMobile = array( \n");

		$_POST[escrow]['use'] = $_POST[escrow]['use']?$_POST[escrow]['use']:'N';
		$_POST[escrow][c] = $_POST[escrow][c]?$_POST[escrow][c]:'';
		$_POST[escrow][v] = $_POST[escrow][v]?$_POST[escrow][v]:'';
		$_POST[escrow][o] = $_POST[escrow][o]?$_POST[escrow][o]:'';

		foreach ($_POST[escrow] as $k=>$v) {
			if(in_array($k,array('use','c','o','v'))){
				$qfile->write("'$k' => '$v', \n");
			}
		}
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();

		if ($cfg['settlePg']=="lgdacom"){
			$_POST['pg']['quota']	= str_replace(",",":",$_POST['pg']['quota']);
		}

		break;
		*/
	case "setPg":

		if($_POST['cfg']['settlePg']!=$cfg['settlePg']){
			msg('사용중인 PG사가 일치하지 않습니다.');
			go($_SERVER[HTTP_REFERER]);
			exit;
		}

		include "../../conf/config.pay.php";
		$set = (array)$set;
		$set = array_map('strip_slashes',$set);
		$set = array_map('add_slashes',$set);

		if (!$_POST['set']){
			unset($set['use_mobile']['c'],$set['use_mobile']['o'],$set['use_mobile']['v'],$set['use_mobile']['h']);
		}else{
			$set = array_merge($set,(array)$_POST['set']);
		}

		$qfile->open("../../conf/config.pay.php");
		$qfile->write("<? \n");
		if ($set) foreach ($set as $k=>$v) foreach ($v as $k2=>$v2) $qfile->write("\$set['$k']['$k2'] = '$v2'; \n");
		$qfile->write("?>");
		$qfile->close();

		if ($_POST['cfg']['settlePg']=="lgdacom"){
			$_POST['pg']['quota']	= str_replace(",",":",$_POST['pg']['quota']);
		}

		if ($_POST[cfg][settlePg]){
			$qfile->open("../../conf/pg_mobile.".$_POST[cfg][settlePg].".php");
			$qfile->write("<? \n");
			$qfile->write("\$pg_mobile = array( \n");
			foreach ($_POST[pg] as $k=>$v) $qfile->write("'$k' => '".trim($v)."', \n");
			$qfile->write(") \n;");
			$qfile->write("?>");
			$qfile->close();
			@chMod( "../../conf/pg_mobile.".$_POST[cfg][settlePg].".php", 0757 );
		}

		if ($_POST['cfg']['settlePg']=="lgdacom"){
			$mallConfOri	= "../../conf/lgdacom_mobile/mall.conf.ori";
			$mallConf		= "../../conf/lgdacom_mobile/mall.conf";
			if (is_file($mallConf)) {
				unlink($mallConf);
			}
			if (!copy($mallConfOri, $mallConf)) {
				msg('데이콤 설정 화일이 저장되지 않았습니다.');
			}
			chmod($mallConf,0707);

			$mallConfContents	= file_get_contents($mallConf);
			$mallConfContents	.= chr(10).$_POST['pg']['id']." = ".$_POST['pg']['mertkey'];
			$mallConfContents	.= chr(10)."t".$_POST['pg']['id']." = ".$_POST['pg']['mertkey'];

			$qfile->open($mallConf);
			$qfile->write($mallConfContents);
			$qfile->close();
		}

		break;

	case "mod_css":
	case "mod_js":
	case "mod_goods_list_js":

		$_SERVER[HTTP_REFERER] .= '?' . time();
		include_once dirname(__FILE__) . "/../../conf/config.php";
		include_once dirname(__FILE__) . "/../lib.skin.php";

		{ // 디자인코디파일 저장

			switch($mode) {
				case "mod_css" : {
					$file_nm = "/common/css/style.css";
					break;
				}
				case "mod_js" : {
					$file_nm = "/common/js/common.js";
					$_POST['content'] = str_replace( "&#55203;", "?", $_POST['content'] );
					break;
				}
				case "mod_goods_list_js" : {
					$file_nm = "/common/js/goods_list_action.js";
					break;
				}
			}

			$qfile->open( $path = dirname(__FILE__) . "/../../data/skin_mobileV2/" . $cfg['tplSkinMobileWork'] . $file_nm);

			if ($mode == "mod_js") {
				//정규식 소스 예외 처리
				$_POST['content'] = str_replace('/^(http://)*[.a-zA-Z0-9-]+.[a-zA-Z]+$/', '/^(http\:\/\/)*[.a-zA-Z0-9-]+\.[a-zA-Z]+$/', $_POST['content'] );
				$_POST['content'] = str_replace('/[uAC00-uD7A3]/', '/[\uAC00-\uD7A3]/', $_POST['content'] );
				$_POST['content'] = str_replace('/[uAC00-uD7A3a-zA-Z]/', '/[\uAC00-\uD7A3a-zA-Z]/', $_POST['content'] );
				$_POST['content'] = str_replace('/^[uAC00-uD7A3]*$/', '/^[\uAC00-\uD7A3]*$/', $_POST['content'] );
			}

			$qfile->write($_POST['content'] );
			$qfile->close();
			@chMod( $path, 0757 );

			// 2013-10-13 slowj 히스토리관리
			save_design_history_file('skin_mobileV2', $cfg['tplSkinMobileWork'], $file_nm);
			// 2013-10-13 slowj 히스토리관리
		}


		break;

	case "AppSet":

		$load_config_shoppingApp = $config->load('shoppingApp');

		$e_exceptions = unserialize($load_config_shoppingApp['e_exceptions']);

		switch($_POST['useYN']){
			// 모든 상품을 미진열 상태로 변경
			case "all":
				$e_exceptions = array();
				break;
			// 선택한 상품을 진열 상태로 변경
			case "Y":
				foreach($_POST['goodsno'] as $v){
					$e_exceptions[] = $v;
				}
				$e_exceptions = array_unique($e_exceptions);
				break;
			// 선택한 상품을 미진열 상태로 변경
			case "N":
				$key = array();
				if(count($e_exceptions) > 0){ ## 미진열 상품이 존재할 경우
					foreach( $_POST['goodsno'] as $v ){
						$key = array_search($v,$e_exceptions);
						array_splice($e_exceptions,$key,1);
					}
				}
				break;
		}

		$config_shoppingApp = array(
			'e_exceptions'=>serialize($e_exceptions),
		);

		$config->save('shoppingApp',$config_shoppingApp);

		break;

	case "AppPremium":	// 쇼핑몰 어플 자유주제탭 저장부분

		for($j=0;$j<count($_POST['title']);$j++){
			$data[$j]['title'] = $_POST['title'][$j];
			$data[$j]['description'] = $_POST['description'][$j];
			$data[$j]['link'] = $_POST['link'][$j];
			$data[$j]['thumbnail'] = $_POST['filename'][$j];

			if($_POST['del_file'] && in_array($j,$_POST['del_file'])){
				@unlink('../../data/m/app/'.$_POST['filename'][$j]);
				$data[$j]['thumbnail'] = "";
			}
		}

		if($_FILES['thumbnail']){
			$upload = new upload_file;

			$dir = "../../data/m/app";
			if (!is_dir($dir)) {
				@mkdir($dir, 0707);
				@chmod($dir, 0707);
			}

			$file_array = reverse_file_array($_FILES['thumbnail']);
			for($i=0;$i<count($_FILES['thumbnail']['tmp_name']);$i++){
				if($_FILES[thumbnail][tmp_name][$i]){
					$filename = $_FILES[thumbnail][name][$i];
					$upload->upload_file($file_array[$i],$dir.'/'.$i.'_'.$filename,'image');
					if(!$upload->upload())msg('업로드 파일이 올바르지 않습니다.',-1);
					else $data[$i]['thumbnail'] = $i.'_'.$filename;
				}
			}
		}

		$data_apppremium = array(
			'app_premium'=>serialize($data),
		);

		$config->save('shoppingApp',$data_apppremium);

		break;

	case "AppPremium2":	// 쇼핑몰 어플 자유주제탭 저장부분

		for($j=0;$j<count($_POST['title']);$j++){
			$data[$j]['title'] = $_POST['title'][$j];
			$data[$j]['description'] = $_POST['description'][$j];
			$data[$j]['link'] = $_POST['link'][$j];
			$data[$j]['thumbnail'] = $_POST['filename'][$j];

			if($_POST['del_file'] && in_array($j,$_POST['del_file'])){
				@unlink('../../data/m/app2/'.$_POST['filename'][$j]);
				$data[$j]['thumbnail'] = "";
			}
		}

		if($_FILES['thumbnail']){
			$upload = new upload_file;

			$dir = "../../data/m/app2";
			if (!is_dir($dir)) {
				@mkdir($dir, 0707);
				@chmod($dir, 0707);
			}

			$file_array = reverse_file_array($_FILES['thumbnail']);
			for($i=0;$i<count($_FILES['thumbnail']['tmp_name']);$i++){
				if($_FILES[thumbnail][tmp_name][$i]){
					$filename = $_FILES[thumbnail][name][$i];
					$upload->upload_file($file_array[$i],$dir.'/'.$i.'_'.$filename,'image');
					if(!$upload->upload())msg('업로드 파일이 올바르지 않습니다.',-1);
					else $data[$i]['thumbnail'] = $i.'_'.$filename;
				}
			}
		}

		$data_apppremium = array(
			'app_premium2'=>serialize($data),
		);

		$config->save('shoppingApp',$data_apppremium);

		break;


	case "del_upload_img":
		if($_POST['img_name']) {
			@unlink('../../data/m/upload_img/'.$_POST['img_name']);
		}

		echo 'OK';
		exit;

		break;

	case "temp_design_insert" :

		$tmp_arr = Array();

		$tmp_arr['page_type'] = 'main';

		$tmp_ins_query = $db->_query_print('INSERT INTO '.GD_MOBILE_DESIGN.' SET [cv]', $tmp_arr);
		$db->query($tmp_ins_query);

		$ret_arr = Array();

		$ret_arr['mdesign_no'] = $db->_last_insert_id();
		$json = new Services_JSON(16);

		echo $json->encode($ret_arr);
		exit;

		break;

	case "design_delete" :

		$tmp_arr = Array();

		$mdesign_no = $_POST['mdesign_no'];

		$del_query = $db->_query_print('DELETE FROM '.GD_MOBILE_DESIGN.' WHERE mdesign_no=[i]', $mdesign_no);
		$ret = $db->query($del_query);

		if($ret) {
			$ret_arr['result'] = 'OK';
			$json = new Services_JSON(16);
			echo $json->encode($ret_arr);
			exit;
		}
		else {
			$ret_arr['result'] = 'FAIL';
			$json = new Services_JSON(16);
			echo $json->encode($ret_arr);
			exit;
		}

		break;

	case "disp_category":

		$json = new Services_JSON(16);
		$upload = new upload_file;

		# 상품분류 HIDDEN 처리
		setGoodslinkHide($_POST['category'], $_POST['hidden_mobile'],'mobile');

		### 하위 분류 설정 적용하기
		if($_POST[chkdesign]){
			$child_cate_query = $db->_query_print('SELECT * FROM '.GD_CATEGORY.' WHERE category LIKE [s]', $_POST['category'].'%');
			$res_child_cate = $db->_select($child_cate_query);
		}
		else {
			$res_child_cate[0]['category'] = $_POST['category'];
		}

		if(is_array($res_child_cate) && !empty($res_child_cate)) {
			foreach($res_child_cate as $row_child_cate) {

				$data_arr = Array();
				$data_arr['page_type'] = 'cate';
				$data_arr['title'] = $row_child_cate['category'];
				$data_arr['tpl'] = $_POST['tpl'];
				$data_arr['temp1'] ='top';
				$data_arr['text_temp1'] = addslashes($_POST['mobile_body']);
				$chk_query = $db->_query_print('SELECT mdesign_no, temp1, tpl, tpl_opt FROM '.GD_MOBILE_DESIGN.' WHERE page_type=[s] AND title=[s]', 'cate', $row_child_cate['category']);
				$chk_res = $db->_select($chk_query);

				$mdesign_no = Array();

				if(is_array($chk_res) && !empty($chk_res)) {
					foreach($chk_res as $row_chk) {
						$mdesign_no[$row_chk['temp1']] = $row_chk['mdesign_no'];

						if($row_chk['tpl'] == 'tpl_07' && $data_arr['tpl'] != 'tpl_07') {
							$del_img = $json->decode($row_chk['tpl_opt']);

							foreach($del_img['banner_img'] as $del_img_val) {
								if($del_img_val) {
									@unlink('../../data/m/upload_img/'.$del_img_val);
								}
							}

							unset($del_img);
						}
					}
				}

				switch($data_arr['tpl']) {

					case 'tpl_05' :
						$data_arr['line_cnt'] = $_POST['line_cnt'];
						$data_arr['disp_cnt'] = $_POST['disp_cnt'];
						$data_arr['banner_width'] = 0;
						$data_arr['banner_height'] = 0;
						$data_arr['display_type'] = '5';

						$tpl_opt_arr = Array();

						$tpl_opt_arr['tab_num'] = $_POST['tab_num'];
						if(!empty($_POST['tab_name']) && is_array($_POST['tab_name'])) {
							$i = 0;
							foreach($_POST['tab_name'] as $val_tab_name) {
								$i ++;
								$tpl_opt_arr['tab_name'][$i] = $val_tab_name;
							}
						}

						$data_arr['tpl_opt'] = $json->encode($tpl_opt_arr);
						unset($tpl_opt_arr);
						break;

					case 'tpl_07' :
						$data_arr['line_cnt'] = 0;
						$data_arr['disp_cnt'] = 0;
						$data_arr['banner_width'] = $_POST['banner_width'];
						$data_arr['banner_height'] = $_POST['banner_height'];
						$data_arr['display_type'] = '7';

						$tpl_opt_arr = Array();

						$tpl_opt_arr['banner_num'] = $_POST['banner_num'];

						if(!empty($_POST['del_banner_img'])) {
							foreach($_POST['del_banner_img'] as $key_del => $val_del) {
								if($_POST['banner_img_hidden'][$key_del]) {
									@unlink('../../data/m/upload_img/'.$_POST['banner_img_hidden'][$key_del]);
									unset($_POST['banner_img_hidden'][$key_del]);
								}
							}
						}

						if(!empty($_FILES['banner_img'])) {
							$dir = "../../data/m/upload_img";
							if (!is_dir($dir)) {
								@mkdir($dir, 0707);
								@chmod($dir, 0707);
							}
							$banner_img_arr = reverse_file_array($_FILES['banner_img']);
							$i = 0;
							$banner_img_name = Array();
							foreach($banner_img_arr as $row_banner_img) {
								$i++;
								if($row_banner_img['tmp_name']) {
									$tmp = explode('.', $row_banner_img['name']);
									$ext = strtolower($tmp[count($tmp)-1]);
									$filename = 'banner_img_'.$mdesign_no.'_'.$i.'.'.$ext;
									$upload->upload_file($row_banner_img, $dir.'/'.$filename);
									if(!$upload->upload())msg('배너 이미지 업로드 파일이 올바르지 않습니다.', 'disp_main.php', 'parent');
								}
								else {
									$filename = '';
									if($_POST['banner_img_hidden'][$i]) $filename = $_POST['banner_img_hidden'][$i];
								}
								$banner_img_name[$i] = $filename;
							}
						}

						$tpl_opt_arr['banner_img'] = $banner_img_name;
						$data_arr['tpl_opt'] = $json->encode($tpl_opt_arr);
						unset($tpl_opt_arr);
						break;
					default :
						$data_arr['line_cnt'] = $_POST['line_cnt'];
						$data_arr['disp_cnt'] = $_POST['disp_cnt'];
						$data_arr['banner_width'] = 0;
						$data_arr['banner_height'] = 0;
						$data_arr['display_type'] = $_POST['display_type'];
						$data_arr['tpl_opt'] = '';
						break;

				}

				if($mdesign_no['top']) {
					$top_query = $db->_query_print('UPDATE '.GD_MOBILE_DESIGN.' SET [cv] WHERE mdesign_no=[i]', $data_arr, $mdesign_no['top']);
				}
				else {
					$top_query = $db->_query_print('INSERT INTO '.GD_MOBILE_DESIGN.' SET [cv]', $data_arr);
				}

				$db->query($top_query);

				if(!$mdesign_no['top']) $mdesign_no['top'] = $db->_last_insert_id();

				if($_POST['category'] == $row_child_cate['category']) {
					$display_ins_query = Array();

					## 카테고리 대표 이미지 더미 이미지 삭제 ##
					if($data_arr['display_type'] != '3') {
						$img_chk_query = $db->_query_print('SELECT display_type, temp2 FROM '.GD_MOBIEL_DISPLAY.' WHERE mdesign_no=[i]', $mdesign_no);
						$res_img_chk = $db->_select($img_chk_query);

						if(is_array($res_img_chk) && !empty($res_img_chk)) {
							foreach($res_img_chk as $row_img_chk) {
								if($row_img_chk['display_type'] == '3' && $row_img_chk['temp2']) {
									@unlink('../../data/m/upload_img/'.$row_img_chk['temp2']);
								}
							}
						}
					}

					switch($data_arr['display_type']) {
						case '1' :
							if(!empty($_POST['e_step']) && is_array($_POST['e_step'])) {
								$sort = 1;
								foreach($_POST['e_step'] as $goodsno) {
									$display_arr = Array();
									$display_arr['mdesign_no'] = $mdesign_no['top'];
									$display_arr['display_type'] = $data_arr['display_type'];
									$display_arr['goodsno'] = $goodsno;
									$display_arr['sort'] = $sort;
									$display_ins_query[] = $db->_query_print('INSERT INTO '.GD_MOBILE_DISPLAY.' SET [cv]', $display_arr);

									$sort ++;
								}
							}
							break;
						case '2' :
							if(!empty($_POST['categoods']) && is_array($_POST['categoods'])) {
								$sort = 1;
								foreach($_POST['categoods'] as $category) {
									$display_arr = Array();
									$display_arr['mdesign_no'] = $mdesign_no['top'];
									$display_arr['display_type'] = $data_arr['display_type'];
									$display_arr['category'] = $category;
									$display_arr['sort'] = $sort;
									$display_ins_query[] = $db->_query_print('INSERT INTO '.GD_MOBILE_DISPLAY.' SET [cv]', $display_arr);

									$sort ++;
								}
							}
							break;
						case '3' :
							if(!empty($_POST['catelist']) && is_array($_POST['catelist'])) {
								$dir = "../../data/m/upload_img";
								if (!is_dir($dir)) {
									@mkdir($dir, 0707);
									@chmod($dir, 0707);
								}

								$cate_img_arr = reverse_file_array($_FILES['cate_img']);

								$sort = 1;
								$i = 0;

								foreach($_POST['catelist'] as $category) {

									if($_POST['del_cate_img'][$i + 1]) {
										if($_POST['cate_img_hidden'][$i + 1]) {
											@unlink('../../data/m/upload_img/'.$_POST['cate_img_hidden'][$i + 1]);
											unset($_POST['cate_img_hidden'][$i + 1]);
										}
									}

									if($cate_img_arr[$i]['tmp_name']) {
										$tmp = explode('.', $cate_img_arr[$i]['name']);
										$ext = strtolower($tmp[count($tmp)-1]);
										$filename = 'cate_img_'.$mdesign_no['top'].'_'.$i.'.'.$ext;
										$upload->upload_file($cate_img_arr[$i], $dir.'/'.$filename);
										if(!$upload->upload())msg('카테고리 대표 이미지 업로드 파일이 올바르지 않습니다.', 'disp_main.php', 'parent');
									}
									else {
										$filename = '';
										if($_POST['cate_img_hidden'][$i + 1]) $filename = $_POST['cate_img_hidden'][$i + 1];
									}

									$display_arr = Array();
									$display_arr['mdesign_no'] = $mdesign_no['top'];
									$display_arr['display_type'] = $data_arr['display_type'];
									$display_arr['category'] = $category;
									$display_arr['sort'] = $sort;
									$display_arr['temp2'] = $filename;
									$display_ins_query[] = $db->_query_print('INSERT INTO '.GD_MOBILE_DISPLAY.' SET [cv]', $display_arr);

									$sort ++;
									$i ++;
								}
							}
							break;
						case '5' :
							for($i = 1; $i<($_POST['tab_num'] + 1); $i++) {
								if(!empty($_POST['e_tab_step'.$i]) && is_array($_POST['e_tab_step'.$i])) {
									$sort = 1;
									foreach($_POST['e_tab_step'.$i] as $tab_goodsno) {
										$display_arr = Array();
										$display_arr['mdesign_no'] = $mdesign_no['top'];
										$display_arr['display_type'] = $data_arr['display_type'];
										$display_arr['tab_no'] = $i;
										$display_arr['goodsno'] = $tab_goodsno;
										$display_arr['sort'] = $sort;
										$display_ins_query[] = $db->_query_print('INSERT INTO '.GD_MOBILE_DISPLAY.' SET [cv]', $display_arr);

										$sort ++;
									}
								}
							}
							break;
						case '7' :
							if(!empty($_POST['banner_link'])) {
								$sort = 1;
								foreach($_POST['banner_link'] as $key_link => $val_link) {
									$display_arr = Array();
									$display_arr['mdesign_no'] = $mdesign_no['top'];
									$display_arr['display_type'] = $data_arr['display_type'];
									$display_arr['banner_no'] = $key_link;
									$display_arr['temp1'] = $val_link;
									$display_arr['sort'] = $sort;
									$display_ins_query[] = $db->_query_print('INSERT INTO '.GD_MOBILE_DISPLAY.' SET [cv]', $display_arr);

									$sort ++;
								}
							}
							break;
					}

					$display_del_query = $db->_query_print('DELETE FROM '.GD_MOBILE_DISPLAY.' WHERE mdesign_no=[i]', $mdesign_no['top']);
					$db->query($display_del_query);

					foreach($display_ins_query as $ins_query) {
						$db->query($ins_query);
					}
					unset($display_arr, $diplay_del_query, $display_ins_query);
				}
				unset($data_arr, $top_query);

				$bottom_data_arr = Array();
				$bottom_data_arr['page_type'] = 'cate';
				$bottom_data_arr['title'] = $row_child_cate['category'];
				$bottom_data_arr['tpl'] = $_POST['b_tpl'];
				$bottom_data_arr['temp1'] = 'bottom';
				$bottom_data_arr['line_cnt'] = $_POST['b_line_cnt'];
				$bottom_data_arr['disp_cnt'] = $_POST['b_disp_cnt'];
				$bottom_data_arr['banner_width'] = 0;
				$bottom_data_arr['banner_height'] = 0;
				$bottom_data_arr['display_type'] = '2';
				$bottom_data_arr['tpl_opt'] = '';

				if($mdesign_no['bottom']) {
					$bottom_query = $db->_query_print('UPDATE '.GD_MOBILE_DESIGN.' SET [cv] WHERE mdesign_no=[i]', $bottom_data_arr, $mdesign_no['bottom']);
				}
				else {
					$bottom_query = $db->_query_print('INSERT INTO '.GD_MOBILE_DESIGN.' SET [cv]', $bottom_data_arr);
				}

				if(!$mdesign_no['bottom']) $mdesign_no['bottom'] = $db->_last_insert_id();

				$db->query($bottom_query);

				$bottom_display_arr = Array();
				$bottom_display_arr['mdesign_no'] = $mdesign_no['bottom'];
				$bottom_display_arr['display_type'] = '2';
				$bottom_display_arr['category'] = $row_child_cate['category'];
				$bottom_display_arr['sort'] = 1;
				$bottom_display_ins_query = $db->_query_print('INSERT INTO '.GD_MOBILE_DISPLAY.' SET [cv]', $bottom_display_arr);

				$bottom_display_del_query = $db->_query_print('DELETE FROM '.GD_MOBILE_DISPLAY.' WHERE mdesign_no=[i]', $mdesign_no['bottom']);

				$db->query($bottom_display_del_query);
				$db->query($bottom_display_ins_query);

			}
		}

		unset($bottom_data_arr, $botoom_display_arr, $bottom_query, $bottom_display_del_query, $bottom_display_ins_query);
		break;

	case "disp_main":
		echo "처리중 .... ";
		$json = new Services_JSON(16);
		$upload = new upload_file;

		$upd_arr = Array();

		$mdesign_no = $_POST['mdesign_no'];
		$content_no = $_POST['content_no'];

		$upd_arr['page_type'] = 'main';
		$upd_arr['chk'] = $_POST['chk'];
		$upd_arr['title'] = $_POST['title'];
		$upd_arr['sort_type'] = $_POST['sort_type'];
		$upd_arr['select_date'] = $_POST['select_date'];
		$upd_arr['mobile_categoods'] = @implode(",",$_POST['mobile_categoods']);
		$upd_arr['price'] = @implode(",",$_POST['price']);
		$upd_arr['stock_type'] = $_POST['stock_type'];
		$upd_arr['stock_amount'] = @implode(",",$_POST['stock_amount']);
		$upd_arr['regdt'] = $_POST['regdt'];

		$upd_arr['tpl'] = $_POST['tpl'];

		## 배너 더미 이미지 삭제 ##
		$img_chk_query = $db->_query_print('SELECT tpl, tpl_opt FROM '.GD_MOBILE_DESIGN.' WHERE mdesign_no=[i]', $mdesign_no);
		$res_img_chk = $db->_select($img_chk_query);

		$row_img_chk = $res_img_chk[0];

		if($row_img_chk['tpl'] == 'tpl_07' && $upd_arr['tpl'] != 'tpl_07') {
			$del_img = $json->decode($row_img_chk['tpl_opt']);

			foreach($del_img['banner_img'] as $del_img_val) {
				if($del_img_val) {
					@unlink('../../data/m/upload_img/'.$del_img_val);
				}
			}

			unset($del_img);
		}
		unset($img_chk_query, $res_img_chk, $row_img_chk);

		## 2012-11-19 출력라인수 와 라인당 상품수의 설정값을 최소 1로 처리해준다.
		$line_cnt = (intval($_POST['line_cnt'])<=0)? 1:intval($_POST['line_cnt']);
		$disp_cnt = (intval($_POST['disp_cnt'])<=0)? 1:intval($_POST['disp_cnt']);
		switch($upd_arr['tpl']) {
			case 'tpl_05' :
				$upd_arr['line_cnt'] = $line_cnt;
				$upd_arr['disp_cnt'] = $disp_cnt;
				$upd_arr['banner_width'] = 0;
				$upd_arr['banner_height'] = 0;
				$upd_arr['display_type'] = '5';

				$tpl_opt_arr = Array();

				$tpl_opt_arr['tab_num'] = intval($_POST['tab_num']);
				if(!empty($_POST['tab_name']) && is_array($_POST['tab_name'])) {
					$i = 0;
					foreach($_POST['tab_name'] as $val_tab_name) {
						$i ++;
						$tpl_opt_arr['tab_name'][$i] = $val_tab_name;
					}
				}

				$upd_arr['tpl_opt'] = $json->encode($tpl_opt_arr);
				unset($tpl_opt_arr);
				break;
			case 'tpl_06' :
				$upd_arr['line_cnt'] = 0;
				$upd_arr['disp_cnt'] = $disp_cnt;
				$upd_arr['banner_width'] = $_POST['banner_width'];
				$upd_arr['banner_height'] = $_POST['banner_height'];
				$upd_arr['display_type'] = $_POST['display_type'];
				$upd_arr['tpl_opt'] = '';
				break;
			case 'tpl_07' :
				$upd_arr['line_cnt'] = 0;
				$upd_arr['disp_cnt'] = 0;
				$upd_arr['banner_width'] = $_POST['banner_width'];
				$upd_arr['banner_height'] = $_POST['banner_height'];
				$upd_arr['display_type'] = '7';

				$tpl_opt_arr = Array();

				$tpl_opt_arr['banner_num'] = $_POST['banner_num'];

				if(!empty($_POST['del_banner_img'])) {
					foreach($_POST['del_banner_img'] as $key_del => $val_del) {
						if($_POST['banner_img_hidden'][$key_del]) {
							@unlink('../../data/m/upload_img/'.$_POST['banner_img_hidden'][$key_del]);
							unset($_POST['banner_img_hidden'][$key_del]);
						}
					}
				}

				if(!empty($_FILES['banner_img'])) {
					$dir = "../../data/m/upload_img";
					if (!is_dir($dir)) {
						@mkdir($dir, 0707);
						@chmod($dir, 0707);
					}
					$banner_img_arr = reverse_file_array($_FILES['banner_img']);
					$i = 0;
					$banner_img_name = Array();
					foreach($banner_img_arr as $row_banner_img) {
						$i++;
						if($row_banner_img['tmp_name']) {
							$tmp = explode('.', $row_banner_img['name']);
							$ext = strtolower($tmp[count($tmp)-1]);
							$filename = 'banner_img_'.$mdesign_no.'_'.$i.'.'.$ext;
							$upload->upload_file($row_banner_img, $dir.'/'.$filename);
							if(!$upload->upload())msg('배너 이미지 업로드 파일이 올바르지 않습니다.', 'disp_main.php', 'top');
						}
						else {
							$filename = '';
							if($_POST['banner_img_hidden'][$i]) $filename = $_POST['banner_img_hidden'][$i];
						}
						$banner_img_name[$i] = $filename;
					}
				}

				$tpl_opt_arr['banner_img'] = $banner_img_name;
				$upd_arr['tpl_opt'] = $json->encode($tpl_opt_arr);
				unset($tpl_opt_arr);
				break;
			default :
				$upd_arr['line_cnt'] = $line_cnt;
				$upd_arr['disp_cnt'] = $disp_cnt;
				$upd_arr['banner_width'] = 0;
				$upd_arr['banner_height'] = 0;
				$upd_arr['display_type'] = $_POST['display_type'];
				$upd_arr['tpl_opt'] = '';
				break;
		}

		$upd_query = $db->_query_print('UPDATE '.GD_MOBILE_DESIGN.' SET [cv] WHERE mdesign_no=[i]', $upd_arr, $mdesign_no);

		$db->query($upd_query);

		$display_ins_query = Array();

		## 카테고리 대표 이미지 더미 이미지 삭제 ##
		if($upd_arr['display_type'] != '3') {
			$img_chk_query = $db->_query_print('SELECT display_type, temp2 FROM '.GD_MOBILE_DISPLAY.' WHERE mdesign_no=[i]', $mdesign_no);
			$res_img_chk = $db->_select($img_chk_query);

			if(is_array($res_img_chk) && !empty($res_img_chk)) {
				foreach($res_img_chk as $row_img_chk) {
					if($row_img_chk['display_type'] == '3' && $row_img_chk['temp2']) {
						@unlink('../../data/m/upload_img/'.$row_img_chk['temp2']);
					}
				}
			}
		}

		switch($upd_arr['display_type']) {
			case '1' :
				if(!empty($_POST['e_step']) && is_array($_POST['e_step'])) {
					$sort = 1;
					foreach($_POST['e_step'] as $goodsno) {
						$display_arr = Array();
						$display_arr['mdesign_no'] = $mdesign_no;
						$display_arr['display_type'] = $upd_arr['display_type'];
						$display_arr['goodsno'] = $goodsno;
						$display_arr['sort'] = $sort;
						$display_ins_query[] = $db->_query_print('INSERT INTO '.GD_MOBILE_DISPLAY.' SET [cv]', $display_arr);

						$sort ++;
					}
				}
				break;
			case '2' :
				if(!empty($_POST['categoods']) && is_array($_POST['categoods'])) {
					$sort = 1;
					foreach($_POST['categoods'] as $category) {
						$display_arr = Array();
						$display_arr['mdesign_no'] = $mdesign_no;
						$display_arr['display_type'] = $upd_arr['display_type'];
						$display_arr['category'] = $category;
						$display_arr['sort'] = $sort;
						$display_ins_query[] = $db->_query_print('INSERT INTO '.GD_MOBILE_DISPLAY.' SET [cv]', $display_arr);

						$sort ++;
					}
				}
				break;
			case '3' :
				if(!empty($_POST['catelist']) && is_array($_POST['catelist'])) {
					$dir = "../../data/m/upload_img";
					if (!is_dir($dir)) {
						@mkdir($dir, 0707);
						@chmod($dir, 0707);
					}

					$cate_img_arr = reverse_file_array($_FILES['cate_img']);

					$sort = 1;
					$i = 0;

					foreach($_POST['catelist'] as $category) {

						if($_POST['del_cate_img'][$i + 1]) {
							if($_POST['cate_img_hidden'][$i + 1]) {
								@unlink('../../data/m/upload_img/'.$_POST['cate_img_hidden'][$i + 1]);
								unset($_POST['cate_img_hidden'][$i + 1]);
							}
						}

						if($cate_img_arr[$i]['tmp_name']) {
							$tmp = explode('.', $cate_img_arr[$i]['name']);
							$ext = strtolower($tmp[count($tmp)-1]);
							$filename = 'cate_img_'.$mdesign_no.'_'.$i.'.'.$ext;
							$upload->upload_file($cate_img_arr[$i], $dir.'/'.$filename);
							
							if(!$upload->upload())msg('카테고리 대표 이미지 업로드 파일이 올바르지 않습니다.', 'disp_main.php', 'top');
						}
						else {
							$filename = '';
							if($_POST['cate_img_hidden'][$i + 1]) $filename = $_POST['cate_img_hidden'][$i + 1];
						}

						$display_arr = Array();
						$display_arr['mdesign_no'] = $mdesign_no;
						$display_arr['display_type'] = $upd_arr['display_type'];
						$display_arr['category'] = $category;
						$display_arr['sort'] = $sort;
						$display_arr['temp2'] = $filename;
						$display_ins_query[] = $db->_query_print('INSERT INTO '.GD_MOBILE_DISPLAY.' SET [cv]', $display_arr);

						$sort ++;
						$i ++;
					}
				}
				break;
			case '5' :
				for($i = 1; $i<($_POST['tab_num'] + 1); $i++) {
					if(!empty($_POST['e_tab_step'.$i]) && is_array($_POST['e_tab_step'.$i])) {
						$sort = 1;
						foreach($_POST['e_tab_step'.$i] as $tab_goodsno) {
							$display_arr = Array();
							$display_arr['mdesign_no'] = $mdesign_no;
							$display_arr['display_type'] = $upd_arr['display_type'];
							$display_arr['tab_no'] = $i;
							$display_arr['goodsno'] = $tab_goodsno;
							$display_arr['sort'] = $sort;
							$display_ins_query[] = $db->_query_print('INSERT INTO '.GD_MOBILE_DISPLAY.' SET [cv]', $display_arr);

							$sort ++;
						}
					}
				}
				break;
			case '7' :
				if(!empty($_POST['banner_link'])) {
					$sort = 1;
					foreach($_POST['banner_link'] as $key_link => $val_link) {

						$val_link = preg_replace('#^[^:/.]*[:/]+#i', '', $val_link);

						$display_arr = Array();
						$display_arr['mdesign_no'] = $mdesign_no;
						$display_arr['display_type'] = $upd_arr['display_type'];
						$display_arr['banner_no'] = $key_link;
						$display_arr['temp1'] = addslashes($val_link) ;
						$display_arr['sort'] = $sort;
						$display_ins_query[] = $db->_query_print('INSERT INTO '.GD_MOBILE_DISPLAY.' SET [cv]', $display_arr);

						$sort ++;
					}
				}
				break;
		}

		$display_del_query = $db->_query_print('DELETE FROM '.GD_MOBILE_DISPLAY.' WHERE mdesign_no=[i]', $mdesign_no);
		$db->query($display_del_query);

		foreach($display_ins_query as $ins_query) {
			$db->query($ins_query);
		}

		if ($upd_arr['sort_type'] != '1') {
			$mainAutoSort = Core::loader('mainAutoSort');
			$mobile_categoods = array_filter(explode(",",$upd_arr['mobile_categoods']));

			$mainAutoSort -> setMainAutoSort($upd_arr['sort_type'], $upd_arr['select_date'], $mobile_categoods);
		}
		
		if ($_SERVER['HTTP_REFERER']) {
			echo "<script>parent.location.href='".$_SERVER['HTTP_REFERER']."';</script>";
		} else {
			echo "<script>parent.location.href='disp_main_form.php?mdesign_no=".$mdesign_no."&content_no=".$content_n."';</script>";
		}
		exit;
		break;

	case 'popup_regist' :
		$upload = new upload_file;

		$mpopup_no = $_POST['mpopup_no'];

		$popup_data = Array();
		$popup_data['popup_title'] = $_POST['popup_title'];
		$popup_data['open_type'] = $_POST['open_type'];
		if($popup_data['open_type'] == '1') {
			$_POST['start_date'] = str_replace("-","",$_POST['start_date']);
			$_POST['end_date'] = str_replace("-","",$_POST['end_date']);
			$popup_data['start_date'] = date('Y-m-d', mktime(0, 0, 0, substr($_POST['start_date'], 4, 2), substr($_POST['start_date'], 6, 2), substr($_POST['start_date'], 0, 4)));
			$popup_data['end_date'] = date('Y-m-d', mktime(0, 0, 0, substr($_POST['end_date'], 4, 2), substr($_POST['end_date'], 6, 2), substr($_POST['end_date'], 0, 4)));
			$popup_data['start_time'] = $_POST['start_time'];
			$popup_data['end_time'] = $_POST['end_time'];
		} else {
			$popup_data['start_date'] = NULL;
			$popup_data['end_date'] = NULL;
			$popup_data['start_time'] = NULL;
			$popup_data['end_time'] = NULL;
		}
		$popup_data['popup_type'] = $_POST['popup_type'];
		$popup_data['popup_body'] = $_POST['popup_body'];
		$popup_data['link_url'] = $_POST['link_url'];

		if($mpopup_no) {
			$popup_query = $db->_query_print('UPDATE '.GD_MOBILEV2_POPUP.' SET [cv] WHERE mpopup_no=[i]', $popup_data, $mpopup_no);
			$db->query($popup_query);
		}
		else {
			$popup_query = $db->_query_print('INSERT INTO '.GD_MOBILEV2_POPUP.' SET [cv]', $popup_data);
			$db->query($popup_query);
			$mpopup_no = $db->_last_insert_id();
		}

		$img_data = array();

		if($_POST['del_popup_img']) {
			if($_POST['popup_img_hidden']) {
				@unlink('../../data/m/upload_img/'.$_POST['popup_img_hidden']);
				unset($_POST['popup_img_hidden']);
			}
		}

		$dir = "../../data/m/upload_img";
		if (!is_dir($dir)) {
			@mkdir($dir, 0707);
			@chmod($dir, 0707);
		}

		if($_FILES['popup_img']['tmp_name']) {
			$tmp = explode('.', $_FILES['popup_img']['name']);
			$ext = strtolower($tmp[count($tmp)-1]);
			$filename = 'popup_img_'.$mpopup_no.'.'.$ext;
			$upload->upload_file($_FILES['popup_img'], $dir.'/'.$filename);
			if(!$upload->upload())msg('팝업 이미지 업로드 파일이 올바르지 않습니다.', 'popup_list.php');
		}
		else {
			$filename = '';
			if($_POST['popup_img_hidden']) $filename = $_POST['popup_img_hidden'];
		}

		$img_data['popup_img'] = $filename;

		$update_img_query = $db->_query_print('UPDATE '.GD_MOBILEV2_POPUP.' SET [cv] WHERE mpopup_no=[i]', $img_data, $mpopup_no);
		$db->query($update_img_query);

		unset($upload, $popup_data, $popup_query);
		go('mobile_popup_list.php');
		break;
	case 'del_popup' :

		$mpopup_no = $_POST['mpopup_no'];

		## 팝업 이미지 삭제 ##
		$chk_img_query = $db->_query_print('SELECT popup_img FROM '.GD_MOBILEV2_POPUP.' WHERE mpopup_no=[i]', $mpopup_no);
		$res_chk_img = $db->_select($chk_img_query);
		$popup_img = $res_chk_img[0]['popup_img'];

		if($popup_img) {
			@unlink('../../data/m/upload_img/'.$popup_img);
		}

		$del_query = $db->_query_print('DELETE FROM '.GD_MOBILEV2_POPUP.' WHERE mpopup_no=[i]', $mpopup_no);
		$db->query($del_query);

		unset($chk_img_query, $res_chk_img, $popup_img, $del_query);
		break;
	case 'open_popup' :

		$mpopup_no = $_POST['mpopup_no'];

		$open_data[open] = $_POST['change_open'];

		$update_open_query = $db->_query_print('UPDATE '.GD_MOBILEV2_POPUP.' SET [cv] WHERE mpopup_no=[i]', $open_data, $mpopup_no);
		$db->query($update_open_query);

		unset($open_data, $update_open_query);

		break;

	case 'event_regist' :
		$json = new Services_JSON(16);
		$mevent_no = $_POST['mevent_no'];

		$event_data = array();
		$event_data['event_title'] = $_POST['event_title'];
		$event_data['start_date'] = date('Y-m-d 00:00:00', mktime(0, 0, 0, substr($_POST['start_date'], 4, 2), substr($_POST['start_date'], 6, 2), substr($_POST['start_date'], 0, 4)));
		$event_data['end_date'] = date('Y-m-d 23:59:59', mktime(0, 0, 0, substr($_POST['end_date'], 4, 2), substr($_POST['end_date'], 6, 2), substr($_POST['end_date'], 0, 4)));
		//$event_data['event_body'] = addslashes(stripcslashes($_POST['event_body']));
		$event_data['event_body'] = $_POST['event_body'];

		if( $_POST['catnm'] ){
			$next_cate_query = $db->_query_print('SELECT max(category) max_category FROM '.GD_CATEGORY.' WHERE LENGTH(category)=[i]', 3);
			$res_next_cate = $db->_select($next_cate_query);
			$max_category = $res_next_cate[0]['max_category'];
			$next_category = $max_category + 1;
			$next_category = sprintf('%03d', $next_category);

			if( !$_POST['category'] ){
				## catnm 은 있지만, category가 없으면,  gd_category 에 카테고리 레코드를 추가해 준다.
				$ins_cate_arr = array();
				$ins_cate_arr['category'] = $next_category;
				$ins_cate_arr['catnm'] = $_POST['catnm'];
				$ins_cate_arr['hidden'] = 1;
				$ins_cate_arr['hidden_mobile'] = 1;

				$cate_query = $db->_query_print('INSERT INTO '.GD_CATEGORY.' SET [cv], sort=unix_timestamp()', $ins_cate_arr);
				$db->query($cate_query);

				$arr = array(
					'rtpl' => 'tpl_01',
					'rpage_num' => '4',
					'rcols' => '4',
					'body' => '',
					'tpl' => 'tpl_01',
					'page_num' => '20',
					'cols' => '4'
				);

				$qfile->open("../../conf/category/$_POST[category].php");
				$qfile->write("<? \n");
				$qfile->write("\$lstcfg = array( \n");
				foreach ($arr as $k=>$v){
					$v = (!is_array($v)) ? "'$v'" : "array(".implode(",",$v).")";
					$qfile->write("'$k' => $v, \n");
				}
				$qfile->write("); \n");
				$qfile->write("?>");
				$qfile->close();
				@chmod("../../conf/category/$_POST[category].php", 0707);
				unset($ins_cate_arr, $arr, $cate_query);
			}
			else {
				$next_category = $_POST['category'];
				$upd_cate_arr = array();
				$upd_cate_arr['catnm'] = $_POST['catnm'];

				$cate_query = $db->_query_print('UPDATE '.GD_CATEGORY.' SET [cv] WHERE category=[s]', $upd_cate_arr, $next_category);
				$db->query($cate_query);
				unset($upd_cate_arr, $cate_query);
			}

			$hidden = getCateHideCnt($next_category) > 0 ? 1 : 0;


		}else{
			$next_category = "";
		}

		$event_data['category'] = $next_category;
		$event_data['tpl'] = $_POST['tpl'];

		switch($event_data['tpl']) {
			case 'tpl_05' :
				$event_data['line_cnt'] = $_POST['line_cnt'];
				$event_data['disp_cnt'] = $_POST['disp_cnt'];
				$display_type = '5';

				$tpl_opt_arr = Array();

				$tpl_opt_arr['tab_num'] = $_POST['tab_num'];
				if(!empty($_POST['tab_name']) && is_array($_POST['tab_name'])) {
					$i = 0;
					foreach($_POST['tab_name'] as $val_tab_name) {
						$i ++;
						$tpl_opt_arr['tab_name'][$i] = $val_tab_name;
					}
				}
//debug($tpl_opt_arr);
				$event_data['tpl_opt'] = $json->encode($tpl_opt_arr);
				//$event_data['tpl_opt'] = gd_json_encode($tpl_opt_arr);
				unset($tpl_opt_arr);
				break;
			default :
				$event_data['line_cnt'] = $_POST['line_cnt'];
				$event_data['disp_cnt'] = $_POST['disp_cnt'];
				$display_type = '1';
				$event_data['tpl_opt'] = '';
				break;
		}

		if($mevent_no) {
			$upd_query = $db->_query_print('UPDATE '.GD_MOBILE_EVENT.' SET [cv] WHERE mevent_no=[i]', $event_data, $mevent_no);
			$db->query($upd_query);
		}
		else {
			$ins_query = $db->_query_print('INSERT INTO '.GD_MOBILE_EVENT.' SET [cv]', $event_data);
			$db->query($ins_query);
			$mevent_no = $db->_last_insert_id();
		}

		$display_ins_query = Array();

		switch($display_type) {
			case '1' :
				if(!empty($_POST['e_step']) && is_array($_POST['e_step'])) {
					$sort = 1;
					foreach($_POST['e_step'] as $goodsno) {
						$display_arr = Array();
						$display_arr['mevent_no'] = $mevent_no;
						$display_arr['display_type'] = $display_type;
						$display_arr['goodsno'] = $goodsno;
						$display_arr['sort'] = $sort;
						$display_ins_query[] = $db->_query_print('INSERT INTO '.GD_MOBILE_DISPLAY.' SET [cv]', $display_arr);

						$sort ++;
					}
				}
				break;

			case '5' :
				for($i = 1; $i<($_POST['tab_num'] + 1); $i++) {
					if(!empty($_POST['e_tab_step'.$i]) && is_array($_POST['e_tab_step'.$i])) {
						$sort = 1;
						foreach($_POST['e_tab_step'.$i] as $tab_goodsno) {
							$display_arr = Array();
							$display_arr['mevent_no'] = $mevent_no;
							$display_arr['display_type'] = $display_type;
							$display_arr['tab_no'] = $i;
							$display_arr['goodsno'] = $tab_goodsno;
							$display_arr['sort'] = $sort;
							$display_ins_query[] = $db->_query_print('INSERT INTO '.GD_MOBILE_DISPLAY.' SET [cv]', $display_arr);

							$sort ++;
						}
					}
				}
				break;
		}

		## 이벤트에 등록된 상품을 등록하기 위해,  이전에 해당 이벤트에 매핑된 모든 상품을 지우고,  새로 인서트 한다.
		$display_del_query = $db->_query_print('DELETE FROM '.GD_MOBILE_DISPLAY.' WHERE mevent_no=[i]', $mevent_no);
		$db->query($display_del_query);

		foreach($display_ins_query as $ins_query) {
			$db->query($ins_query);
		}

		### 이벤트카테고리와 연결상품 싱크
		if ($event_data['category']) {
			$link_del_query = $db->_query_print('DELETE FROM '.GD_GOODS_LINK.' WHERE category=[s]', $event_data['category']);
			$db -> query($link_del_query);
			$display_get_query = $db->_query_print('SELECT mevent_no, goodsno from '.GD_MOBILE_DISPLAY.' WHERE mevent_no=[i]', $mevent_no);
			$res = $db->query($display_get_query);
			$i=0;
			while($tmp = $db -> fetch($res)){
				$timestamp = time()+$i;
				$link_arr['goodsno'] = $tmp['goodsno'];
				$link_arr['category'] = $event_data['category'];
				$link_arr['hidden'] = $hidden;
				$link_arr['sort'] = "-".$timestamp;
				$link_ins_query = $db->_query_print('INSERT INTO '.GD_GOODS_LINK.' SET [cv]', $link_arr);
				$db -> query($link_ins_query);
				$i=$i+10;
			}
		}
		unset($link_arr, $link_del_query, $link_ins_query);
		unset($event_data);
		unset($display_arr, $display_del_query, $ins_query);

		go('event_list.php');
		break;

	case 'del_event' :

		$mevent_no = $_POST['mevent_no'];

		$del_query = $db->_query_print('DELETE FROM '.GD_MOBILE_EVENT.' WHERE mevent_no=[i]', $mevent_no);
		$db->query($del_query);

		$display_del_query = $db->_query_print('DELETE FROM '.GD_MOBILE_DISPLAY.' WHERE mevent_no=[i]', $mevent_no);
		$db->query($display_del_query);

		unset($del_query, $display_del_query);
		break;
	case "convert":	// 모바일 V2 로 전환 : 파일 무브
		/*
		$sRefUrl = $_SERVER[HTTP_REFERER];
		$sNewUrl = str_replace("mobileShop", "mobileShop2", $sRefUrl);
		go($sNewUrl);
		*/
		## 현재 적용된 버전은 버전파일 존재 여부로 확인한다
		$version2_apply_file_name = ".htaccess";

		$version2_apply_file_path = dirname(__FILE__)."/../../../m/".$version2_apply_file_name;
		$version2_directory = dirname(__FILE__)."/../../../m2";

		$bCurrent_V2_htaccess = file_exists($version2_apply_file_path);
		$bCurrent_V2_applied = false;
		## 현재 적용버전을 확인하다
		if ( $bCurrent_V2_htaccess ) {
			$aFileContent = file(dirname(__FILE__)."/../../../m/".$version2_apply_file_name);
			for ($i=0; $i<count($aFileContent); $i++) {
				if (preg_match("/RewriteRule/i", $aFileContent[$i])) {
					break;
				}
			}
			if ($i == count($aFileContent)) {
				$bCurrent_V2_applied = false;
			} else {
				$bCurrent_V2_applied = true;
			}
		} else {
			$bCurrent_V2_applied = false;
		}

		// 검증
		if ( $bCurrent_V2_htaccess && $bCurrent_V2_applied ) {
			### 1단계  : .htaccess파일의 내용을 지운다.
			$fp = fopen($version2_apply_file_path, 'w');
			if (!fp) {
				msg("전환에 실패하였습니다. 확인 후 시도하세요.", -1);
			}
			if (!fwrite($fp, " \n")) {
				msg("전환에 실패하였습니다. 확인 후 시도하세요.", -1);
			}
			fclose($fp);
			### 전환 후,  모바일스킨 확인하기
			include dirname(__FILE__) . "/../../conf/config.php";
			if ($cfg['tplSkinMobile'] != 'default' || $cfg['tplSkinMobileWork'] != 'default' ) {
				$cfg['tplSkinMobile'] = 'default';
				$cfg['tplSkinMobileWork'] = 'default';

				$cfg = array_map("stripslashes",$cfg);
				$cfg = array_map("addslashes",$cfg);
				$qfile->open( $path = dirname(__FILE__) . "/../../conf/config.php");
				$qfile->write("<?\n" );
				$qfile->write("\$cfg = array(\n" );
				foreach ( $cfg as $k => $v ){
					if ( $v === true ) $qfile->write("'$k'\t\t\t=> true,\n" );
					else if ( $v === false ) $qfile->write("'$k'\t\t\t=> false,\n" );
					else $qfile->write("'$k'\t\t\t=> '$v',\n" );
				}

				$qfile->write(");\n\n" );
				$qfile->write("?>" );
				$qfile->close();
				@chMod( $path, 0757 );
			}

			### config.mobileShop.php 의 스킨(tplSkinMobile) 확인 및 Root 디렉토리변경 후, 파일에 WRITE
			@include dirname(__FILE__) . "/../../conf/config.mobileShop.php";

			$cfgMobileShop = (array)$cfgMobileShop;
			$cfgMobileShop = array_map("stripslashes",$cfgMobileShop);
			$cfgMobileShop = array_map("addslashes",$cfgMobileShop);

			$cfgMobileShop['tplSkinMobile'] = 'default';
			$cfgMobileShop['mobileShopRootDir'] = '/m';
			# 파일에 WRITE
			$qfile->open($path = dirname(__FILE__) . "/../../conf/config.mobileShop.php");
			$qfile->write("<? \n");
			$qfile->write("\$cfgMobileShop = array( \n");
			foreach ($cfgMobileShop as $k=>$v) $qfile->write("'$k' => '$v', \n");
			$qfile->write(") \n;");
			$qfile->write("?>");
			$qfile->close();
			@chMod( $path, 0757 );

			msg("전환 완료했습니다. 모바일샵 V1를 확인하세요.");
		}
		else {
			msg("전환대상인 모바일샵  V1 가 확인되지 않습니다.  확인 후 시도하세요.", -1);
		}

		$sRefUrl = $_SERVER[HTTP_REFERER];
		$sNewUrl = str_replace("mobileShop2", "mobileShop", $sRefUrl);
		go($sNewUrl);

	case "disp_category_set":

		$cfgMobileDispCategory = array_map("stripslashes",$cfgMobileDispCategory);
		$cfgMobileDispCategory = array_map("addslashes",$cfgMobileDispCategory);

		$cfgMobileDispCategory['disp_goods_count']		= $_POST['disp_goods_count'];

		$qfile->open("../../conf/config.mobileShop.category.php");
		$qfile->write("<? \n");
		$qfile->write("\$cfgMobileDispCategory = array( \n");
		foreach ($cfgMobileDispCategory as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();

		break;
}

go($_SERVER[HTTP_REFERER]);

?>
