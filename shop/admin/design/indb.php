<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
require_once("../../lib/upload.lib.php");
$qfile = new qfile();

$mode = ($_POST[mode]) ? $_POST[mode] : $_GET[mode];
unset($_POST[mode]); unset($_POST[x]); unset($_POST[y]);

switch ($mode){

	case "mod_default":

		include_once dirname(__FILE__) . "/../../conf/config.php";

		{ // 환경파일 저장

			$cfg['tplSkin']				= $_POST['tplSkin'];
			$cfg['tplSkinWork']			= $_POST['tplSkinWork'];

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

			# 기존 정보 비우기
			unset($cfg);

			# 스킨별 기본 정보
			if(is_file(dirname(__FILE__) . "/../../conf/design_basic_".$_POST['tplSkinWork'].".php")){
				include dirname(__FILE__) . "/../../conf/design_basic_".$_POST['tplSkinWork'].".php";
			}

			# 각 스킨별 기본정보 저장 (기본 사이즈)
			$cfg['shopAlign']			= $_POST['shopAlign'];
			$cfg['shopOuterSize']		= $_POST['shopOuterSize'];
			$cfg['shopSideSize']		= $_POST['shopSideSize'];
			$cfg['shopLineColorL']		= $_POST['shopLineColorL'];
			$cfg['shopLineColorC']		= $_POST['shopLineColorC'];
			$cfg['shopLineColorR']		= $_POST['shopLineColorR'];
			$cfg['shopMainGoodsConf']	= $_POST['shopMainGoodsConf'];
			$cfg['copyProtect']			= $_POST['copyProtect'];
			$cfg['subCategory']			= $_POST['subCategory'];

			$cfg = array_map("stripslashes",$cfg);
			$cfg = array_map("addslashes",$cfg);

			$qfile->open( $path = dirname(__FILE__) . "/../../conf/design_basic_".$_POST['tplSkinWork'].".php");
			$qfile->write("<?\n" );

			foreach ( $cfg as $k => $v ){
				$qfile->write("\$cfg['".$k."']\t\t\t=\"".$v."\";\n" );
			}
			$qfile->write("?>" );
			$qfile->close();
			@chMod( $path, 0757 );

			$templateCache = Core::loader('TemplateCache');
			$templateCache->clearCache();

			echo("<script>parent.location.href=parent.location.href;</script>");
			exit;
		}

		break;

	case "mod_css":
	case "mod_js":

		$_SERVER[HTTP_REFERER] .= '?' . time();
		include_once dirname(__FILE__) . "/../../conf/config.php";
		include_once dirname(__FILE__) . "/../lib.skin.php";

		{ // 디자인코디파일 저장

			switch($mode) {
				case "mod_css" : {
					$file_nm = "/style.css";
					break;
				}
				case "mod_js" : {
					$file_nm = "/common.js";
					$_POST['content'] = str_replace( "&#55203;", "?", $_POST['content'] );
					break;
				}

			}

			$qfile->open( $path = dirname(__FILE__) . "/../../data/skin/" . $cfg['tplSkinWork'] . $file_nm);
			if (ini_get('magic_quotes_gpc') == 1) $_POST['content'] = stripslashes( $_POST['content'] );
			$qfile->write($_POST['content'] );
			$qfile->close();
			@chMod( $path, 0757 );

			// 2013-10-13 slowj 히스토리관리
			save_design_history_file('skin', $cfg['tplSkinWork'], $file_nm);
			// 2013-10-13 slowj 히스토리관리
		}


		break;

	case "mod_intro":

		$_SERVER[HTTP_REFERER] .= '?' . time();

		{ // 환경파일 저장

			# 기존 정보 비우기
			unset($cfg);

			# 스킨별 기본 정보
			if(is_file(dirname(__FILE__) . "/../../conf/design_basic_".$_POST['tplSkinWork'].".php")){
				include dirname(__FILE__) . "/../../conf/design_basic_".$_POST['tplSkinWork'].".php";
			}

			# 인트로 사용여부
			$cfg['introUseYN']			= $_POST['introUseYN'];

			# 인트로 종류
			$cfg['custom_landingpage']			= ($cfg['introUseYN'] == 'Y' && !isset($_POST['custom_landingpage'])) ? 1 : $_POST['custom_landingpage'];

			$cfg = array_map("stripslashes",$cfg);
			$cfg = array_map("addslashes",$cfg);

			$qfile->open( $path = dirname(__FILE__) . "/../../conf/design_basic_".$_POST['tplSkinWork'].".php");
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
			$root_dir = str_replace( $_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME'] ) . $GLOBALS['cfg']['rootDir'];
			$file_name = basename($_POST['skin_file']);
			if ($_POST['gd_preview'] == '1') {
				$path = "/data/_skin_preview/skin/" . $_POST['tplSkinWork'] . $_POST['skin_file'];	
			}
			else {
				$path = "/data/skin/" . $_POST['tplSkinWork'] . $_POST['skin_file'];
			}
			$tmp = explode('/', dirname($path));
			$path = $root_dir;
			for ( $i = 0; $i < count($tmp); $i++ )
			{
				$path .= $tmp[$i] . '/';
				if (!@file_exists($path)) @mkdir($path, 0757, true);
				@chMod($path, 0757);
			}
			$path .= $file_name;

			$qfile->open( $path );
			if (ini_get('magic_quotes_gpc') == 1) $_POST['content'] = stripslashes( $_POST['content'] );
			$qfile->write($_POST['content'] );
			$qfile->close();
			@chMod( $path, 0757 );

			// 2013-10-13 slowj 히스토리관리
			if ($_POST['gd_preview'] !== '1') {
				save_design_history_file('skin', $_POST['tplSkinWork'], $_POST['skin_file']);
			}
			else {
				echo '<script>parent.preview_popup();</script>';
			}
			// 2013-10-13 slowj 히스토리관리
		}

		break;

	case "checkprivacy":

		include_once dirname(__FILE__) . "/../../conf/config.php";

		$cfg['private2YN']			= $_POST['private2YN'];
		$cfg['private3YN']			= $_POST['private3YN'];

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

		break;

}

go($_SERVER[HTTP_REFERER]);

?>
