<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
require_once("../../lib/upload.lib.php");
include_once dirname(__FILE__) . "/webftp/webftp.class_outcall.php";

include "../../conf/config.php";
include "../../conf/config.mobileShop.php";
$cfgMobileShop = (array)$cfgMobileShop;

$qfile = new qfile();

// ���� ��� ���� ����� ��Ų�� ���� �� �⺻ ���� ��Ų�� ����.
if(empty($cfg['tplSkinMobile']) === true){

	$cfg['tplSkinMobile'] = $cfg['tplSkinMobileWork'] = "default";

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
	$cfgMobileShop['tplSkinMobile'] = "default";

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

		# ����ϼ� ��Ʈ���
		if(!$cfgMobileShop['mobileShopRootDir']) $cfgMobileShop['mobileShopRootDir'] = '/m';

		# �ΰ��̹���
		if (isset($_FILES['mobileShopLogo_up'])){
			$_BGFILES = array( 'mobileShopLogo_up' => $_FILES['mobileShopLogo_up'] );
			$userori = array( 'mobileShopLogo' => 'mobileShopLogo' . strrChr( $_FILES['mobileShopLogo_up']['name'], "." ) );

			outcallUpload( $_BGFILES, '/../../../data/skin_mobile/'.$_POST['tplSkinMobile'].'/', $userori );
			unset($_POST[mobileShopLogo_del]);
		}
		else $_POST[mobileShopLogo] = $cfgMobileShop[mobileShopLogo];

		# �������̹���
		if (isset($_FILES['mobileShopIcon_up'])){
			$_BGFILES = array( 'mobileShopIcon_up' => $_FILES['mobileShopIcon_up'] );
			$userori = array( 'mobileShopIcon' => 'mobileShopIcon' . strrChr( $_FILES['mobileShopIcon_up']['name'], "." ) );

			outcallUpload( $_BGFILES, '/../../../data/skin_mobile/'.$_POST['tplSkinMobile'].'/', $userori );
			unset($_POST[mobileShopIcon_del]);
		}
		else $_POST[mobileShopIcon] = $cfgMobileShop[mobileShopIcon];

		# ���ι���̹���
		if (isset($_FILES['mobileShopMainBanner_up'])){
			$_BGFILES = array( 'mobileShopMainBanner_up' => $_FILES['mobileShopMainBanner_up'] );
			$userori = array( 'mobileShopMainBanner' => 'mobileShopMainBanner' . strrChr( $_FILES['mobileShopMainBanner_up']['name'], "." ) );

			outcallUpload( $_BGFILES, '/../../../data/skin_mobile/'.$_POST['tplSkinMobile'].'/', $userori );
			unset($_POST[mobileShopMainBanner_del]);
		}
		else $_POST[mobileShopMainBanner] = $cfgMobileShop[mobileShopMainBanner];

		$cfgMobileShop['mobileShopLogo']	= $_POST['mobileShopLogo'];
		$cfgMobileShop['mobileShopIcon']	= $_POST['mobileShopIcon'];
		$cfgMobileShop['mobileShopMainBanner']	= $_POST['mobileShopMainBanner'];
		$cfgMobileShop['useMobileShop']		= $_POST['useMobileShop'];
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

		# ��뿩�� �αױ��
		@readurl("http://gongji.godo.co.kr/userinterface/mobileshop_log.php?use=".$cfgMobileShop['useMobileShop']."&shopSno=".$godo['sno']."&shopHost=".$_SERVER['HTTP_HOST']."&ecCode=".$godo['ecCode']);

		break;
		
	case "mod_intro":

		$_SERVER[HTTP_REFERER] .= '?' . time();

		{ // ȯ������ ����

			# ���� ���� ����
			unset($cfg);

			# ��Ų�� �⺻ ���� design_basicMobile_default.php / 
			if(is_file(dirname(__FILE__) . "/../../conf/design_basicMobile_".$_POST['tplSkinMobileWork'].".php")){
				include dirname(__FILE__) . "/../../conf/design_basicMobile_".$_POST['tplSkinMobileWork'].".php";
			}
			
			# ��Ʈ�� ��뿩��
			$cfg['introUseYNMobile']			= $_POST['introUseYNMobile'];

			# ��Ʈ�� ����
			$cfg['custom_landingpageMobile']			= ($cfg['introUseYNMobile'] == 'Y' && !isset($_POST['custom_landingpageMobile'])) ? 1 : $_POST['custom_landingpageMobile'];

			$cfg = array_map("stripslashes",$cfg);
			$cfg = array_map("addslashes",$cfg);

			$qfile->open( $path = dirname(__FILE__) . "/../../conf/design_basicMobile_".$_POST['tplSkinMobileWork'].".php");
			$qfile->write("<?\n" );

			foreach ( $cfg as $k => $v ){

				$qfile->write("\$cfg['".$k."']\t\t\t=\"".$v."\";\n" );
			}

			$qfile->write("?>" );
			$qfile->close();
			@chMod( $path, 0757 );
		}

		break;

	case "intro_save" :	// ��Ʈ�� ������ ����

		{ // �������ڵ����� ����
			$path = dirname(__FILE__) . "/../../data/skin/" . $_POST['tplSkinMobileWork'] . $_POST['skin_file'];
			if (!file_exists(dirname($path))) mkdir(dirname($path), 0757, true);
			$qfile->open( $path );
			if (ini_get('magic_quotes_gpc') == 1) $_POST['content'] = stripslashes( $_POST['content'] );
			$qfile->write($_POST['content'] );
			$qfile->close();
			@chMod( $path, 0757 );

			// 2013-10-13 slowj �����丮����
			if ($_POST['gd_preview'] !== '1') {
				save_design_history_file('skin', $_POST['tplSkinMobileWork'], $_POST['skin_file']);
			}
			else {
				echo '<script>parent.preview_popup();</script>';
			}
			// 2013-10-13 slowj �����丮����
		}

		break;
		
	case "config_view_set":

		$cfgMobileShop = array_map("stripslashes",$cfgMobileShop);
		$cfgMobileShop = array_map("addslashes",$cfgMobileShop);

		# ��ǰ���⼳�� ����� db update
		if($_POST['vtype_goods']=='0' && $cfgMobileShop['vtype_goods']!=$_POST['vtype_goods']){
			$query = "update gd_goods set `open_mobile`=`open`;";
			$db->query($query);
		}

		# ī�װ����⼳�� ����� db update
		if($_POST['vtype_category']=='0' && $cfgMobileShop['vtype_category']!=$_POST['vtype_category']){
			$query = "update gd_category set `hidden_mobile`=`hidden`;";
			$db->query($query);
			$query_link = "update gd_goods_link set `hidden_mobile`=`hidden`;";
			$db->query($query_link);
		}

		# ����ϼ� ��Ʈ���
		if(!$cfgMobileShop['mobileShopRootDir']) $cfgMobileShop['mobileShopRootDir'] = '/m';

		$cfgMobileShop['vtype_goods']		= $_POST['vtype_goods'];
		$cfgMobileShop['vtype_category']	= $_POST['vtype_category'];

		$qfile->open("../../conf/config.mobileShop.php");
		$qfile->write("<? \n");
		$qfile->write("\$cfgMobileShop = array( \n");
		foreach ($cfgMobileShop as $k=>$v) $qfile->write("'$k' => '$v', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();

		break;

	case "disp_main":

		$file = "../../conf/config.mobileShop.main.php";

		$qfile->open($file);
		$qfile->write("<? \n");
		foreach ($_POST['page_num'] as $k=>$v){
			$qfile->write("\$cfg_mobile_step[$k] = array( \n");
			$qfile->write("'chk' => '{$_POST[chk][$k]}', \n");
			$qfile->write("'title' => '{$_POST[title][$k]}', \n");
			$qfile->write("'tpl' => '{$_POST[tpl][$k]}', \n");
			$qfile->write("'size' => '{$_POST[size][$k]}', \n");
			$qfile->write("'page_num' => '{$_POST[page_num][$k]}', \n");
			$qfile->write("); \n");
		}
		$qfile->write("?>");
		$qfile->close();

		foreach ($_POST['page_num'] as $k=>$v){
			$sort = 0;
			$key = "e_step".$k;
			$_POST[$key] = @array_unique($_POST[$key]);
			$strSQL = "delete from ".GD_GOODS_DISPLAY_MOBILE." where mode='$k'";
			$db->query($strSQL);
			if ($_POST[$key]){
				foreach ($_POST[$key] as $v){
					$strSQL = "insert into ".GD_GOODS_DISPLAY_MOBILE." set goodsno='$v',mode='$k',sort='".$sort++."'";
					$db->query($strSQL);
				}
			}
		}
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

	case "mod_css":
	case "mod_js":
	case "mod_goods_list_js":

		$_SERVER[HTTP_REFERER] .= '?' . time();
		include_once dirname(__FILE__) . "/../../conf/config.php";
		include_once dirname(__FILE__) . "/../lib.skin.php";

		{ // �������ڵ����� ����

			switch($mode) {
				case "mod_css" : {
					$file_nm = "/common/css/style.css";
					break;
				}
				case "mod_js" : {
					$file_nm = "/common/js/common.js";
					$_POST['content'] = str_replace( "&#55203;", "�R", $_POST['content'] );
					break;
				}
				case "mod_goods_list_js" : {
					$file_nm = "/common/js/goods_list_action.js";
					$_POST['content'] = str_replace( "�R", "�R", $_POST['content'] );
					break;
				}
			}

			$qfile->open( $path = dirname(__FILE__) . "/../../data/skin_mobile/" . $cfg['tplSkinMobileWork'] . $file_nm);
			if (ini_get('magic_quotes_gpc') == 1) $_POST['content'] = stripslashes( $_POST['content'] );

			if ($mode == "mod_js") {
				//���Խ� �ҽ� ���� ó��
				$_POST['content'] = str_replace('/^(http://)*[.a-zA-Z0-9-]+.[a-zA-Z]+$/', '/^(http\:\/\/)*[.a-zA-Z0-9-]+\.[a-zA-Z]+$/', $_POST['content'] );
				$_POST['content'] = str_replace('/[uAC00-uD7A3]/', '/[\uAC00-\uD7A3]/', $_POST['content'] );
				$_POST['content'] = str_replace('/[uAC00-uD7A3a-zA-Z]/', '/[\uAC00-\uD7A3a-zA-Z]/', $_POST['content'] );
				$_POST['content'] = str_replace('/^[uAC00-uD7A3]*$/', '/^[\uAC00-\uD7A3]*$/', $_POST['content'] );
			}

			$qfile->write($_POST['content'] );
			$qfile->close();
			@chMod( $path, 0757 );

			// 2013-10-13 slowj �����丮����
			save_design_history_file('skin_mobile', $cfg['tplSkinMobileWork'], $file_nm);
			// 2013-10-13 slowj �����丮����
		}


		break;

	case "AppSet":

		$load_config_shoppingApp = $config->load('shoppingApp');

		$e_exceptions = unserialize($load_config_shoppingApp['e_exceptions']);

		switch($_POST['useYN']){
			// ��� ��ǰ�� ������ ���·� ����
			case "all":
				$e_exceptions = array();
				break;
			// ������ ��ǰ�� ���� ���·� ����
			case "Y":
				foreach($_POST['goodsno'] as $v){
					$e_exceptions[] = $v;
				}
				$e_exceptions = array_unique($e_exceptions);
				break;
			// ������ ��ǰ�� ������ ���·� ����
			case "N":
				$key = array();
				if(count($e_exceptions) > 0){ ## ������ ��ǰ�� ������ ���
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

	case "AppPremium":	// ���θ� ���� ���������� ����κ�

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
					if(!$upload->upload())msg('���ε� ������ �ùٸ��� �ʽ��ϴ�.',-1);
					else $data[$i]['thumbnail'] = $i.'_'.$filename;
				}
			}
		}

		$data_apppremium = array(
			'app_premium'=>serialize($data),
		);

		$config->save('shoppingApp',$data_apppremium);

		break;

	case "AppPremium2":	// ���θ� ���� ���������� ����κ�

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
					if(!$upload->upload())msg('���ε� ������ �ùٸ��� �ʽ��ϴ�.',-1);
					else $data[$i]['thumbnail'] = $i.'_'.$filename;
				}
			}
		}

		$data_apppremium = array(
			'app_premium2'=>serialize($data),
		);

		$config->save('shoppingApp',$data_apppremium);

		break;
	case "convert":	// ����� V2 �� ��ȯ : ���� ����
		## ���� ����� ������ �������� ���� ���η� Ȯ���Ѵ� 
		$version2_apply_file_name = ".htaccess";
		
		$version2_apply_file_path = dirname(__FILE__)."/../../../m/".$version2_apply_file_name; 
		$version2_directory = dirname(__FILE__)."/../../../m2"; 
		
		$bCurrent_V2_htaccess = file_exists($version2_apply_file_path);
		$bCurrent_V2_applied = false; 
		 ## ���� ��������� Ȯ���ϴ� 
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
		
		$bExist_V2 = file_exists($version2_directory);

		// ����
		if ( $bCurrent_V2_htaccess && !$bCurrent_V2_applied && $bExist_V2) {
			// 1�ܰ� 
			$fp = fopen($version2_apply_file_path, 'w');
			if (!fp) {
				msg("��ȯ�� �����Ͽ����ϴ�. Ȯ�� �� �õ��ϼ���.", -1);
			}
			if (!fwrite($fp, "RewriteEngine On\n")) {
				msg("��ȯ�� �����Ͽ����ϴ�. Ȯ�� �� �õ��ϼ���.", -1);		
			} 
			if (!fwrite($fp, "RewriteBase /\n")) {
				msg("��ȯ�� �����Ͽ����ϴ�. Ȯ�� �� �õ��ϼ���.", -1);		
			} 
			if (!fwrite($fp, "RewriteCond %{REQUEST_URI} ^(.*)$ [NC]\n")) {
				msg("��ȯ�� �����Ͽ����ϴ�. Ȯ�� �� �õ��ϼ���.", -1);		
			} 
			if (!fwrite($fp, "RewriteRule ^(.*)$ /m2/$1 [R,L,NE]\n")) {
				msg("��ȯ�� �����Ͽ����ϴ�. Ȯ�� �� �õ��ϼ���.", -1);		
			} 
			fclose($fp);
			### ��ȯ ��,  ����Ͻ�Ų Ȯ���ϱ� 
			include dirname(__FILE__) . "/../../conf/config.php";
			if ($cfg['tplSkinMobile'] != 'light' || $cfg['tplSkinMobileWork'] != 'light' ) {
				$cfg['tplSkinMobile'] = 'light'; 
				$cfg['tplSkinMobileWork'] = 'light'; 
				
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
			### config.mobileShop.php �� ��Ų(tplSkinMobile) Ȯ�� �� Root ���丮���� ��, ���Ͽ� WRITE 
			@include dirname(__FILE__) . "/../../conf/config.mobileShop.php";
	
			$cfgMobileShop = (array)$cfgMobileShop;
			$cfgMobileShop = array_map("stripslashes",$cfgMobileShop);
			$cfgMobileShop = array_map("addslashes",$cfgMobileShop);

			$cfgMobileShop['tplSkinMobile'] = 'light';
			$cfgMobileShop['mobileShopRootDir'] = '/m2'; 

			$qfile->open($path = dirname(__FILE__) . "/../../conf/config.mobileShop.php");
			$qfile->write("<? \n");
			$qfile->write("\$cfgMobileShop = array( \n");
			foreach ($cfgMobileShop as $k=>$v) $qfile->write("'$k' => '$v', \n");
			$qfile->write(") \n;");
			$qfile->write("?>");
			$qfile->close();
			@chMod( $path, 0757 );					
			
			msg("��ȯ �Ϸ��߽��ϴ�. ����ϼ� ����2 �� Ȯ���ϼ���.");
		}
		else {
			msg("��ȯ����� ����ϼ� ���� V2 �� Ȯ�ε��� �ʽ��ϴ�.  Ȯ�� �� �õ��ϼ���.", -1);
		}

		$sRefUrl = $_SERVER[HTTP_REFERER];
		$sNewUrl = str_replace("mobileShop", "mobileShop2", $sRefUrl);
		go($sNewUrl);
		break; 
}

go($_SERVER[HTTP_REFERER]);

?>
