<?

ob_start();
@include_once dirname(__FILE__) . "/../../lib.php";
@include_once dirname(__FILE__) . "/../../../conf/config.php";
@include_once dirname(__FILE__) . "/../../lib.skin.php";
@include_once dirname(__FILE__) . "/code.class.php";
$codi = new codi;
ob_end_clean();

$mode = ( $_POST['mode'] ) ? $_POST['mode'] : $_GET['mode'];

switch ( $mode ){

	case "getTextarea": # Textarea

		ob_start();
		### Textarea ���� ��
		if ( $_GET['body'] == 'user_body' && $cfg['tplSkinTodayWork'] != '' && $_GET['tplFile'] != '' && file_exists( $tmp = dirname(__FILE__) . "/../../../data/skin_today/" . $cfg['tplSkinTodayWork'] . $_GET['tplFile'] ) ){
			$file = @file( $tmp );
			$output = implode("",$file);
		}

		### Textarea ���� ��
		if ( $_GET['body'] == 'base_body' &&  $cfg['tplSkinTodayWork'] != '' && $_GET['tplFile'] != '' && file_exists( $tmp = dirname(__FILE__) . "/../../../skin_today_ori/" . $cfg['tplSkinTodayWork'] . $_GET['tplFile'] ) ){
			$file = file( $tmp );
			$output = implode("",$file);
		}
		ob_end_clean();

		header("Content-type: text/html; charset=euc-kr");

		echo $output;
		exit;

		break;

	case "getReplacecode": # replacecode

		ob_start();

		### xml Ȯ���ڷ� ������
		$_GET[design_file] = str_replace(array(".htm", ".txt"), ".xml", $_GET[design_file]);

		### ���� �������� �ʴ� ��� ���ϰ�� ������
		if (file_exists(dirname(__FILE__) . "/replacecode/".$_GET[design_file]) === false)
		{
			## �Խ��ǰ��
			if (preg_match("/^board\//", $_GET[design_file])) $_GET[design_file] = preg_replace("/^board.*\//", "board/default/", $_GET[design_file]);
			## �˾����
			else if (preg_match("/^popup\//", $_GET[design_file])) $_GET[design_file] = "popup/standard.xml";
			## ��ܵ����ΰ��
			else if (preg_match("/^outline\/header\//", $_GET[design_file])) $_GET[design_file] = "outline/header/standard.xml";
			## ��������ΰ��
			else if (preg_match("/^outline\/side\//", $_GET[design_file])) $_GET[design_file] = "outline/side/standard.xml";
			## �ϴܵ����ΰ��
			else if (preg_match("/^outline\/footer\//", $_GET[design_file])) $_GET[design_file] = "outline/footer/standard.xml";
		}

		### dirdepth ����
		$dirdepth = '';
		$dirnm = basename(dirname(dirname($_GET[design_file])));
		if ($dirnm !='' && $dirnm !='.') $dirdepth .= $dirnm . '/';
		$dirnm = basename(dirname($_GET[design_file]));
		if ($dirnm !='' && $dirnm !='.') $dirdepth .= $dirnm . '/';
		$dirdepth .= basename($_GET[design_file]);

		### ��ȿ�� üũ
		if ($_GET[design_file] == '') $errMsg = "���ϸ� ����";
		else if ($_GET[design_file] != $dirdepth) $errMsg = "���ϸ� ����ġ";
		else if (strpos($_GET[design_file], '../') !== false) $errMsg = "���ٺҰ� ��� ����";
		else if (substr($_GET[design_file], -4) != '.xml') $errMsg = "xml ���ϸ� �ƴ�";
		else if (file_exists(dirname(__FILE__) . "/replacecode/".$_GET[design_file]) === false) $errMsg = "{$_GET[design_file]}���� �������� ����";
		else $clean[design_file] = $_GET[design_file];

		if ($errMsg != ''){
			header("Status: {$errMsg}", true, 400);
			echo "";
			exit;
		}

		header("Content-type: text/xml; charset=euc-kr");

		$output = implode("", file(dirname(__FILE__) . "/replacecode/".$clean[design_file]));
		ob_end_clean();

		echo $output;
		exit;

		break;

	case "getDir":

		$json_var = array();

		### ���丮/������ �ܰ躰 ����
		$dirfiles = array();
		if ( $_GET['val'] )
		{
			unset($tmpjoin);
			if ( substr( $_GET['val'], 0, 1 ) != '/' ) $dirfiles[] = '';
			$tmp = explode( "/", $_GET['val'] );
			foreach ( $tmp as $v ){
				if ( substr( $v, -4 ) != '.htm' ) $tmpjoin = $dirfiles[] = $tmpjoin . $v . '/';
			}
		}
		else $dirfiles[] = $_GET['dirfiles'];

		if ( $_GET['idx'] ) $dirfiles = array_notnull( $dirfiles );

		foreach ( $dirfiles as $path )
		{
			unset($ret);
			foreach( $codi->get_dirList( $path ) as $arr ){ // SELECT OPTION ����
				# main�� html ���丮 ���� (2006-08-31 sunny)
				if ( $arr[type] == 'dir' && !( $path == '' && in_array( $arr[name], array('main','html') ) ) ) $ret[] = "[" . $arr[text] . "] .. " . $arr[name] . "|" . $path . $arr[name] . "/";
			}
			if ( $ret ) $ret = implode( "||", $ret );
			$json_var[] = array('ret' => $ret, 'dirfiles' => $path, 'val' => $_GET['val']);
		}

		include dirname(__FILE__)."/../../../lib/json.class.php";
		$json = new Services_JSON();
		$output = $json->encode($json_var);

		echo $output;
		exit;

		break;

	case "chkFile":

		$file_unexist = 'Y';

		foreach( $codi->get_dirList( $_GET['dir_name'] ) as $arr ){
			if ( $arr['name'] == $_GET['file_name'] . $_GET['file_ext'] ){
				$file_unexist = 'N';
				break;
			}
		}

		echo $file_unexist;
		exit;

		break;


	case "getCodiTree":

		$codiTree = new codiTree;
		$codiTree->getTree($_GET['dirfiles']);
		if ($codiTree->obOut != '') echo $codiTree->obOut;
		else echo $codiTree->output;
		exit;

		break;
}

?>