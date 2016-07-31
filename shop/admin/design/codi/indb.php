<?

include "../../lib.php";
require_once("../../../lib/qfile.class.php");
$qfile = new qfile();

@include_once dirname(__FILE__) . "/../../../conf/config.php";
@include_once dirname(__FILE__) . "/../../lib.skin.php";
@include_once dirname(__FILE__) . "/../../../conf/design_dir.php";
@include_once dirname(__FILE__) . "/../../../conf/design_skin_" . $cfg['tplSkinWork'] . ".php";

$mode = ($_POST[mode]) ? $_POST[mode] : $_GET[mode];
unset($_POST[mode]); unset($_POST[x]); unset($_POST[y]);

foreach($design_skin as $k => $v){
	$v = array_map("stripslashes",$v);
	$v = array_map("addslashes",$v);
	$design_skin[$k] = $v;
}

$templateCache = Core::loader('TemplateCache');
$templateCache->clearCache();

switch ($mode){

	case "create": // ���ο� ������ �߰��ϱ�

		### �������ڵ����� ����
		## ���������� ����&����
		$tmp = explode( "/", '/' . $_POST['design_file'] );
		$dir = str_replace( $_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME'] ) . $cfg['rootDir'] . '/data/skin/' . $cfg['tplSkinWork'];

		for ( $i = 0; $i < ( count( $tmp ) - 1 ); $i++ ){

			$dir .= $tmp[ $i ] . '/';
			if ( !@file_exists( $dir ) ) @mkdir( $dir, 0757 );
			@chMod( $dir, 0757 );
		}

		$filenm = get_magic_quotes_gpc() ? stripslashes(array_pop($tmp)) : array_pop($tmp);
		$nowPath = $dir . str_replace(array('<','>',':','"','\'','/','\\','|','?','*'),'_',$filenm); # ���ε��� ������

		## ����
		$design_skin[ $_POST['design_file'] ]['text'] = $_POST['file_desc'];
		$design_skin[ $_POST['design_file'] ]['linkurl'] = "main/html.php?htmid={$_POST['design_file']}";
		$content = "{*** " . $_POST['file_desc'] . " | " . $design_skin[ $_POST['design_file'] ]['linkurl'] . " ***}" . "\n";
		if ( ereg("popup/",$_POST['design_file']) === false && ereg("outline/",$_POST['design_file']) === false ) $content .= "{ # header }\n\n{ # footer }";

		## ����
		$qfile->open( $path = $nowPath);
		if (ini_get('magic_quotes_gpc') == 1) $content = stripslashes( $content );
		$qfile->write($content );
		$qfile->close();
		@chMod( $path, 0757 );


		### �����ν�Ų���� ����
		$qfile->open( dirname(__FILE__) . "/../../../conf/design_skin_" . $cfg['tplSkinWork'] . ".php");
		$qfile->write("<?\n" );
		$qfile->write("\$design_skin = array();\n\n" );

		foreach ( $design_skin as $filekey => $property ){

			$qfile->write("\$design_skin['" . $filekey . "'] = array(\n" );
			foreach ( $property as $k => $v ) $qfile->write("'$k'\t\t\t=> '$v',\n" );
			$qfile->write(");\n\n" );
		}

		$qfile->write("?>" );
		$qfile->close();


		### ����
		echo "<script>parent.location.href='../codi.php?design_file={$_POST['design_file']}';</script>";
		exit;

		break;

	case "save": // ������ �����ϱ�
	case "saveas": // ���̸����� �����ϱ�
	case "popupConf": // �����˾�â �����ϱ�

		### �˾��ΰ�� ����
		if($mode == "popupConf"){
			if(!$_GET['design_file']){
				$_GET['design_file']	= "popup/".$_POST['name'].".htm";

				# �˾�â ������� ��� ���� �ߺ� üũ
				$tmp	= array_keys( $design_skin );
				$keys	= array_ereg( "'^popup/[^/]*$'si", $tmp );
				if(in_array($_GET['design_file'],$keys)){
					msg("������ �˾�â ���ϸ��� �����մϴ�. �ٽ� Ȯ�����ֽʽÿ�!",-1);
					exit();
				}
			}else{
				$_POST['linkurl']		= "main/html.php?htmid=popup/".$_POST['name'].".htm";
			}
			unset($_POST['name']);

			if($_POST['popup_dt2tm'] == "Y"){
				$_POST['popup_sdt']		= $_POST['popup_sdt_tg'];
				$_POST['popup_edt']		= $_POST['popup_edt_tg'];
				$_POST['popup_stime']	= $_POST['popup_stime_tg'];
				$_POST['popup_etime']	= $_POST['popup_etime_tg'];
			}
			unset($_POST['popup_sdt_tg']);
			unset($_POST['popup_edt_tg']);
			unset($_POST['popup_stime_tg']);
			unset($_POST['popup_etime_tg']);
		}


		### �������ڵ����� ����
		if ( isset( $_POST['content'] ) )
		{
			## ���������� ����&����
			$tmp = explode( "/", '/' . $_GET['design_file'] );
			if ($_GET['gd_preview'] == '1') {
				$dir = str_replace( $_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME'] ) . $cfg['rootDir'] . '/data/_skin_preview/skin/' . $cfg['tplSkinWork'];
			}
			else {
				$dir = str_replace( $_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME'] ) . $cfg['rootDir'] . '/data/skin/' . $cfg['tplSkinWork'];
			}

			for ( $i = 0; $i < ( count( $tmp ) - 1 ); $i++ )
			{
				$dir .= $tmp[ $i ] . '/';
				if ( !@file_exists( $dir ) ) @mkdir( $dir, 0757, true );
				@chMod( $dir, 0757 );
			}

			$nowPath = $dir . $tmp[ ( count( $tmp ) - 1 ) ]; # ���ε��� ������
			$ext = array_pop(explode('.',$nowPath));	// ���� Ȯ����

			## ��������
			if ( $_GET['design_file'] != 'proc/_agreement.txt' && !in_array($ext,array('js','css')))
			{
				preg_match("/\{\*\*\*( .*)\*\*\*\}/i", $_POST['content'], $matches);

				if ( $matches[1] ){ // ���ϳ��� �������� �ִ� ���

					$tmp = explode( "|", $matches[1] );
					$tmp[0] = trim( $tmp[0] );
					$tmp[1] = trim( $tmp[1] );

					if ( ($_POST['linkurl'] == '' && ereg("popup/",$_GET['design_file'])) || $mode == 'saveas' ) $_POST['linkurl'] = "main/html.php?htmid={$_GET['design_file']}";
					if ( $tmp[1] == '' && $_POST['linkurl'] == '' ) $_POST['linkurl'] = str_replace( array('.htm', '.txt'), '.php', $_GET['design_file'] );

					if ( $_POST['text'] != '' ) $tmp[0] = $_POST['text'];
					if ( $_POST['linkurl'] != '' ) $tmp[1] = $_POST['linkurl'];

					$matches[1] = "{*** " . implode( " | ", $tmp ) . " ***}";

					$_POST['content'] = str_replace( $matches[0], $matches[1], $_POST['content'] );
				}
				else { // ���ϳ��� �������� ���� ���

					if ( ($_POST['linkurl'] == '' && ereg("popup/",$_GET['design_file'])) || $mode == 'saveas' )
						$_POST['linkurl'] = "main/html.php?htmid={$_GET['design_file']}";
					else
						$_POST['linkurl'] = str_replace( array('.htm', '.txt'), '.php', $_GET['design_file'] );
					$_POST['content'] = "{*** " . $_POST['text'] . " | " . $_POST['linkurl'] . " ***}" . "\n" . $_POST['content'];
				}
			}

			## ����
			$qfile->open( $path = $nowPath);
			if (ini_get('magic_quotes_gpc') == 1) $_POST['content'] = stripslashes( $_POST['content'] );
			$qfile->write($_POST['content'] );
			$qfile->close();
			@chMod( $path, 0757 );
		}


		### ���̸����� �����϶� ��ó��
		if ( $mode == 'saveas' )
		{
			$design_skin[ $_GET['design_file'] ]['text'] = $_POST['text'];

			if ( isset( $_POST['outbg_img'] ) ) unset( $_POST['outbg_img'] );
			if ( isset( $_POST['outbg_img_del'] ) ) unset( $_POST['outbg_img_del'] );
			if ( isset( $_POST['inbg_img'] ) ) unset( $_POST['inbg_img'] );
			if ( isset( $_POST['inbg_img_del'] ) ) unset( $_POST['inbg_img_del'] );
			if ( isset( $_POST['spimg'] ) ) unset( $_POST['spimg'] );
			if ( isset( $_POST['spimg_del'] ) ) unset( $_POST['spimg_del'] );
		}


		### ����̹���
		$_BGFILES = array();
		$_BGFILES['outbg_img_up'] = $_FILES['outbg_img_up'];
		$_BGFILES['inbg_img_up'] = $_FILES['inbg_img_up'];

		$filenm = preg_replace( array( "'.htm$'si", "'/'si" ), array( "", "." ), $_GET['design_file'] );

		$userori = array();
		$userori['outbg_img'] = $filenm . '_outbg' . strrChr( $_FILES['outbg_img_up']['name'], "." );
		$userori['inbg_img'] = $filenm . '_inbg' . strrChr( $_FILES['inbg_img_up']['name'], "." );

		@include_once dirname(__FILE__) . "/../webftp/webftp.class_outcall.php";
		outcallUpload( $_BGFILES, '/img/codi/', $userori );

		if ( $_POST['outbg_img'] == '' ) unset( $_POST['outbg_img'] );
		if ( $_POST['inbg_img'] == '' ) unset( $_POST['inbg_img'] );


		### �����ν�Ų���� ����
		if ( count( $_POST ) > 0 && $_GET['gd_preview'] !== '1'){

			$notPostField = array( 'x', 'y', 'codeact', 'content', 'base_content', 'outbg_img_del', 'inbg_img_del', 'spimg_del' );
			foreach ( $_POST as $k => $v ){ // �ʵ� ����
				if ( in_array( $k, $notPostField ) ) unset( $_POST[$k] );
			}

			$design_skin[ $_GET['design_file'] ] = array();

			foreach ( $_POST as $k => $v ){
				if ( $v == '' ) continue;
				if ( $k == 'outline_header' && $v == 'default' ) continue;
				if ( $k == 'outline_footer' && $v == 'default' ) continue;
				if ( $k == 'outline_side' && $v == 'default' ) continue;
				if ( $k == 'outline_sidefloat' && $v == 'default' ) continue;

				if ( $v != '' && $v != '' ) $design_skin[ $_GET['design_file'] ][ $k ] = $v;
			}

			$qfile->open( $path = dirname(__FILE__) . "/../../../conf/design_skin_" . $cfg['tplSkinWork'] . ".php");
			$qfile->write("<?\n" );
			$qfile->write("\$design_skin = array();\n\n" );

			foreach ( $design_skin as $filekey => $property ){

				$qfile->write("\$design_skin['" . $filekey . "'] = array(\n" );
				foreach ( $property as $k => $v ) $qfile->write("'$k'\t\t\t=> '$v',\n" );
				$qfile->write(");\n\n" );
			}

			$qfile->write("?>" );
			$qfile->close();
			@chMod( $path, 0757 );
		}

		// 2013-08-05 slowj �����丮����
		if ($_GET['gd_preview'] !== '1') {
			save_design_history_file('skin', $cfg['tplSkinWork'], $_GET['design_file']);
		}
		// 2013-08-05 slowj �����丮����

		### ����
		// 2013-10-11 slowj �̸�����
		if ($_GET['gd_preview'] === '1') {
			echo '<script>parent.preview_popup();</script>';
		}
		else {
			if ( ereg("popup/",$_GET['design_file'] ) ){
				//go( '../iframe.popup_list.php' );
				go( $_SERVER[HTTP_REFERER] );
			}else{
				go( preg_replace( "'design_file=[^&].*\.(htm|txt)'si", "design_file=" . $_GET['design_file'], $_SERVER['HTTP_REFERER'] ) . '&' . time() );
			}
		}
		exit;

		break;

	case "del": // ������ �����ϱ�

		@include dirname(__FILE__) . "/code.class.php";
		$codi = new codi;
		$data_file		= $codi->get_fileinfo( $_GET['design_file'] );						# File Data


		### ����̹��� ����
		$_BGFILES = array();
		$_BGFILES['outbg_img'] = $data_file['outbg_img'];
		$_BGFILES['inbg_img'] = $data_file['inbg_img'];

		if ( count( $_BGFILES ) ){ // Webftp Ŭ���� ����

			include_once dirname(__FILE__) . "/../webftp/webftp.class.php";

			$webftp = new webftp;
			$webftp->ftp_path = str_replace( $_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME'] ) . $cfg['rootDir'] . '/data/skin/' . $cfg['tplSkinWork']; # ��Ų���
		}

		foreach ( $_BGFILES as $key => $file ){ // ����ó��
			if ( trim( $file ) != '' ) @unlink( $webftp->ftp_path . '/img/codi/' . $file );
		}


		### �����ν�Ų���� ����
		unset( $design_skin[ $_GET['design_file'] ] ); # �迭����

		$qfile->open( dirname(__FILE__) . "/../../../conf/design_skin_" . $cfg['tplSkinWork'] . ".php");
		$qfile->write("<?\n" );
		$qfile->write("\$design_skin = array();\n\n" );

		foreach ( $design_skin as $filekey => $property ){

			$qfile->write("\$design_skin['" . $filekey . "'] = array(\n" );
			foreach ( $property as $k => $v ) $qfile->write("'$k'\t\t\t=> '$v',\n" );
			$qfile->write(");\n\n" );
		}

		$qfile->write("?>" );
		$qfile->close();


		### �������ڵ����� ����
		$dir = str_replace( $_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME'] ) . $cfg['rootDir'] . '/data/skin/' . $cfg['tplSkinWork'];
		@unlink( $dir . '/' . $_GET['design_file'] ); # ���ϻ���


		### ����
		if ( ereg("popup/",$_GET['design_file'] ) ){
			go( '../../design/codi.php?design_file=popup/', 'parent' );
		}else{
			go( '../../design/codi.php', 'parent' );
		}
		exit;

		break;

	case "batch": // ��������� �ϰ�����

		### Webftp Ŭ���� ����
		include_once dirname(__FILE__) . "/../webftp/webftp.class.php";
		$webftp = new webftp;
		$webftp->ftp_path = str_replace( $_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME'] ) . $cfg['rootDir'] . '/data/skin/' . $cfg['tplSkinWork']; # ��Ų���


		### �ϰ� ����
		foreach ( $design_skin as $filekey => $property ){

			if ( $filekey == 'default' ) continue;

			if ( isset( $property['outline_header'] ) ) unset( $design_skin[ $filekey ]['outline_header'] );
			if ( isset( $property['outline_footer'] ) ) unset( $design_skin[ $filekey ]['outline_footer'] );
			if ( isset( $property['outline_side'] ) ) unset( $design_skin[ $filekey ]['outline_side'] );
			if ( isset( $property['outline_sidefloat'] ) ) unset( $design_skin[ $filekey ]['outline_sidefloat'] );
			if ( isset( $property['outbg_color'] ) ) unset( $design_skin[ $filekey ]['outbg_color'] );

			if ( isset( $property['outbg_img'] ) ){
				if ( trim( $property['outbg_img'] ) != '' ) @unlink( $webftp->ftp_path . '/img/codi/' . $property['outbg_img'] );
				unset( $design_skin[ $filekey ]['outbg_img'] );
			}
		}


		### �����ν�Ų���� ����
		$qfile->open( dirname(__FILE__) . "/../../../conf/design_skin_" . $cfg['tplSkinWork'] . ".php");
		$qfile->write("<?\n" );
		$qfile->write("\$design_skin = array();\n\n" );

		foreach ( $design_skin as $filekey => $property ){

			$qfile->write("\$design_skin['" . $filekey . "'] = array(\n" );
			foreach ( $property as $k => $v ) $qfile->write("'$k'\t\t\t=> '$v',\n" );
			$qfile->write(");\n\n" );
		}

		$qfile->write("?>" );
		$qfile->close();

		break;

}

go($_SERVER['HTTP_REFERER']);

?>
