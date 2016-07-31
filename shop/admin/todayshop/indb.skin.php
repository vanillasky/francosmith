<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
require_once("../../lib/upload.lib.php");
require_once("../../lib/todayshop_cache.class.php");
todayshop_cache::truncate();

$qfile = new qfile();

// config ���
$pathConf	= "../../conf/";

// skin_today ���
$pathSkin	= "../../data/skin_today/";

// �ӽ� ���� ��� �� üũ
$pathTmp	= "../../data/tmp_skinTodayCopy/";
if ( !@file_exists( $pathTmp ) ) @mkdir( $pathTmp, 0757 );
@chMod( $pathTmp, 0757 );

// ������ ���� ���� ����
$confDesignFile	= array("config.todayshop.banner_","design_skinToday_","design_basicToday_");
$confBaseSkin	= array( 'today' );

// ��Ų �ٿ�ε�
function skinDown($skinName){

	$skinName = trim($skinName);

	Header("Content-type: application/octet-stream");
	Header("Content-disposition:attachment;filename=" . str_replace( dirname( $skinName ) . '/', "", $skinName ) );
	Header("Content-length:" . fileSize( $skinName ) );
	Header("Content-Transfer-Encoding: binary");
	Header("Pragma: no-cache");
	Header("Expires: 0");

	readFile( $skinName );
}

// Ȯ���� üũ
function chkExe( $fn ){

	$app_ext = array( 'gz' );

	$chks = explode( ";", $types );
	$mxs = sizeof( $chks );
	$extFn = strtoLower( strrChr( $fn, "." ) );
	for ( $i = 0; $i < $mxs; $i++ ) if ( trim( $chks[$i] ) && trim( $chks[$i] ) == $extFn ) return true;
	return false;
}

// �ش� ������ ���� �� ���� ������ ����
function getDirList($pathDir,$mode="all"){
	if( is_dir($pathDir) ){
		if ( $handle = opendir($pathDir) ) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					if( $mode == "dir" ){
						if( is_dir($pathDir.$file) ){
							$getList[]	= $pathDir.$file;
						}
					}
					if( $mode == "file" ){
						if( !is_dir($pathDir.$file) ){
							$getList[]	= $pathDir.$file;
						}
					}
					if( $mode == "all" ){
						$getList[]	= $pathDir.$file;
					}
				}
			}
			closedir($handle);
		}
		return $getList;
	}
}

// �ش� ������ ���� ���� �� ���� ����Ʈ ��������
function getDirFile($pathDir){

	// �迭 ����
	$tmp=$tmp1=$tmp2=$getArr=array();

	// �迭�� �ƴѰ�� �迭 ó��
	if ( !is_array($pathDir) ){
		$pathDir = array($pathDir);
	}

	// �� ������ ������ ���ϸ� ����
	for ($i = 0; $i < count($pathDir); $i++){
		if ( $handle = opendir($pathDir[$i]) ) {
			while (false !== ($file = readdir($handle))) {
				if ($file != "." && $file != "..") {
					if( is_dir($pathDir[$i].$file) ){
						$tmp['dir'][]	= $pathDir[$i].$file."/";
					}else{
						$tmp['file'][]	= $pathDir[$i].$file;
					}
				}
			}
			closedir($handle);
		}
	}

	// ������ ���� �迭 ����
	$tmp1 = array_merge($tmp['dir'],$tmp['file']);

	// ���
	if(is_array($tmp['dir'])){
		$tmp2 = getDirFile($tmp['dir']);
	}

	//�ߺ��� ���� ����
	$getArr = array_unique(array_merge($tmp1,$tmp2));
	unset($tmp1);
	unset($tmp2);

	return $getArr;
}
// getDirFile �Լ� ������ ����
function sortDirFile(&$arrDir){
	// �迭 ����
	$getArr = array();
	if ( is_array($arrDir) ){
		// ���� ���� (���� ���� ������ ���ؼ�)
		rsort($arrDir);
		// �迭�� ���� ���
		foreach( $arrDir as $val){
			if( substr($val , -1) == "/" ){
				$getArr['dir'][] = $val;
			}else{
				$getArr['file'][] = $val;
			}
		}
		return $getArr;
	}
}
//$skinDirTmp = "../../data/skin_today/";
//$tmp1 = sortDirFile(getDirFile($skinDirTmp."season2/"));

switch ($_GET['mode']){

	case "skinChange":

		include dirname(__FILE__) . "/../../conf/config.php";

		if($_GET['useSkinToday']){ // ȯ������ ����

			# ��Ų�� �⺻ ����
			if(is_file(dirname(__FILE__) . "/../../conf/design_basicToday_".$_GET['useSkinToday'].".php")){
				// �����̼� ��Ų ������ �⺻ ������ �������� ���ϵ��� �Ʒ��� ������ �ҷ����� ����.
				//include dirname(__FILE__) . "/../../conf/design_basicToday_".$_GET['useSkinToday'].".php";
			}

			$cfg['tplSkinToday']			= $_GET['useSkinToday'];

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

			msg($_GET['useSkinToday']." ��Ų�� [��뽺Ų]���� �����Ǿ����ϴ�.\\n\\r���� �����̼� ȭ���� ".$_GET['useSkinToday']." ���� �������ϴ�.");
			echo("<script>parent.location.href=parent.location.href;</script>");
			exit;
		}

	break;

	case "skinChangeWork":

		include_once dirname(__FILE__) . "/../../conf/config.php";

		if($_GET['workSkinToday']){ // ȯ������ ����

			$cfg['tplSkinTodayWork']			= $_GET['workSkinToday'];

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

			msg($_GET['workSkinToday']." ��Ų�� [�۾���Ų]���� �����Ǿ����ϴ�.\\n\\r���� �������۾��� ".$_GET['workSkinToday']." ���� �Ͻ� �� �ֽ��ϴ�.");
			echo("<script>parent.location.href=parent.location.href;</script>");
			exit;
		}

	break;

	// ��Ų �ٿ�ε�
	case "skinDown":

		// ��Ų���� ���� ���
		if (!$_GET['tplSkinToday']){
			msg('��Ų�� �����Դϴ�.',-1);
			exit();
		}

		// Banner DB ����
		$strSQL = "SELECT * FROM ".GD_BANNER." WHERE tplSkin = '".$_GET['tplSkinToday']."' ORDER BY loccd ASC";
		$res = $db->query($strSQL);

		$qfile->open( $path = $pathTmp . "bannerDB.php");
		$qfile->write("<?\n\n" );
		while ($data=$db->fetch($res)){
			$strSQL = "INSERT INTO ".GD_BANNER." (loccd,linkaddr,img,regdt,target,sort,tplSkin) VALUES ('".$data['loccd']."','".$data['linkaddr']."','".$data['img']."',now(),'".$data['target']."','".$data['sort']."','__backupDB__')";
			$qfile->write("\$arrBannerDB[]\t\t\t=\"".$strSQL."\";\n" );
		}
		$qfile->write("\n\n" );
		$qfile->write("?>" );
		$qfile->close();
		@chMod( $path, 0757 );
		if( is_file($pathTmp."bannerDB.php") ){
			$tmpConf[]	= $pathTmp."bannerDB.php";
		}

		// ������ ���� ����
		for ($i = 0; $i < count($confDesignFile); $i++){
			if( is_file($pathConf.$confDesignFile[$i].$_GET['tplSkinToday'].".php") ){
				$tmpConf[]	= $pathConf.$confDesignFile[$i].$_GET['tplSkinToday'].".php";
			}
		}

		// ��ɾ� �ۼ�
		$strSkinName	= $pathTmp.$_GET['tplSkinToday']."_backup.tar.gz";
		$strCommand = "tar -pzcf ".$strSkinName." ".$pathSkin.$_GET['tplSkinToday']."/* " . implode(" ",$tmpConf);


		// ��ɾ� ó��
		system($strCommand, $retval);

		// �ٿ� �ε� ó��
		if( $retval == 0 ){
			if( is_file($strSkinName) ){
				// ��Ų �ٿ�
				skinDown($strSkinName);
				// �ش� ��Ų ����
				if ( !unlink( $strSkinName ) ) return false;
				// ��� ��� ȭ�� ����
				if( is_file($pathTmp."bannerDB.php") ){
					if ( !unlink( $pathTmp."bannerDB.php" ) ) return false;
				}
			}else{
				msg('��Ų �ٿ�ε忡 ���� �Ͽ����ϴ�. [���� �ٿ�ε� ����]',-1);
				exit();
			}
		}else{
			msg('��Ų �ٿ�ε忡 ���� �Ͽ����ϴ�. [���� ���� ���� ����]',-1);
			exit();
		}

	break;

	// ��Ų ����
	case "skinCopy":

		// ������ �ش� ��Ų�� �ִ����� üũ
		$strSkinName	= $_GET['tplSkinToday'] . "_C";	// ����� ��Ų�� ��Ų�� �ڿ� _C �� ����
		if( is_dir($pathSkin.$strSkinName) ){
			msg($strSkinName . ' ��Ų�� ������ ��Ų�� �����մϴ�. �ٽ� Ȯ�� ���ֽʽÿ�.',-1);
			exit();
		}

		// ������ ���� ���� ����
		for ($i = 0; $i < count($confDesignFile); $i++){
			if( is_file($pathConf.$confDesignFile[$i].$_GET['tplSkinToday'].".php") ){
				$tmpConfFileS	= $pathConf.$confDesignFile[$i].$_GET['tplSkinToday'].".php";
				$tmpConfFileC	= $pathConf.$confDesignFile[$i].$strSkinName.".php";
				if (!copy($tmpConfFileS, $tmpConfFileC)) {
					msg( $confDesignFile[$i].$strSkinName.'.php ȭ���� �������� �ʾҽ��ϴ�.');
					$resultChk = false;
				}else{
					@chmod( $pathConf.$confDesignFile[$i].$strSkinName.".php", 0707 );
				}
			}
		}

		// ���� �� ���� �ű��
		$strCommand1	= "cp ".$pathSkin.$_GET['tplSkinToday']." ".$pathSkin.$strSkinName." -Rf";
		$strCommand2	= "chmod 707 ".$pathSkin.$strSkinName." -Rf";
		system($strCommand1, $retval);
		system($strCommand2, $retval);

		if( $retval == 0 ){
			// DB Banner Table �� �ش� ��Ų ����
			$strSQL = "SELECT * FROM ".GD_BANNER." WHERE tplSkin = '".$_GET['tplSkinToday']."'";
			$res = $db->query($strSQL);

			while ($data=$db->fetch($res)){
				$strSQL = "INSERT INTO ".GD_BANNER." (loccd,linkaddr,img,regdt,target,sort,tplSkin) VALUES ('".$data['loccd']."','".$data['linkaddr']."','".$data['img']."',now(),'".$data['target']."','".$data['sort']."','".$strSkinName."')";
				$db->query($strSQL);
			}
		}else{
			msg( $strSkinName.' ��Ų ���翡 �̻��� �ֽ��ϴ�. ���� FTP ���� Ȯ���Ͻñ� �ٶ��ϴ�.');
			$resultChk = false;
		}

		if( $resultChk = true ){
			msg($strSkinName.'��Ų�� ���� �Ǿ����ϴ�.');
		}

		echo("<script>parent.location.href=parent.location.href;</script>");
		exit;

	break;

	// ��Ų ����
	case "skinDel":

		// �۾�/��뽺Ų üũ
		if($_GET['tplSkinToday'] == $cfg['tplSkinToday'] || $_GET['tplSkinToday'] == $cfg['tplSkinTodayWork']){
			msg('�۾���Ų�̳� ��뽺Ų�� ������ �� �����ϴ�.',-1);
			exit();
		}

		$resultChk = true;

		// ������ ���� ���� ����
		for ($i = 0; $i < count($confDesignFile); $i++){
			if( is_file($pathConf.$confDesignFile[$i].$_GET['tplSkinToday'].".php") ){
				$tmpConfFile	= $pathConf.$confDesignFile[$i].$_GET['tplSkinToday'].".php";
				if ( !unlink( $tmpConfFile ) ){
					msg( $pathConf.$confDesignFile[$i].$_GET['tplSkinToday'].'.php ȭ���� �������� �ʾҽ��ϴ�.');
					$resultChk = false;
				}
			}
		}

		// ��Ų ���� ����
		$strCommand	= "rm -rf ".$pathSkin.$_GET['tplSkinToday'];
		system($strCommand, $retval);
		if( $retval != 0 ){
			msg( $_GET['tplSkinToday'].' ��Ų�� ���������� �������� �ʾҽ��ϴ�. FTP�� ���� ���� �Ͻʽÿ�.');
			$resultChk = false;
		}

		// DB Banner Table �� �ش� ����
		$strSQL = "DELETE FROM ".GD_BANNER." WHERE tplSkin = '".$_GET['tplSkinToday']."'";
		$db->query($strSQL);

		if( $resultChk = true ){
			msg($_GET['tplSkinToday'].'��Ų�� ���� �Ǿ����ϴ�.');
		}

		echo("<script>parent.location.href=parent.location.href;</script>");
		exit;

	break;

}

switch ($_POST['mode']){

	// ��Ų ���ε�
	case "skinUpload":

		// ��Ų���� ���� ���
		if ( !$_POST['upload_skin_name'] ){
			msg('���ε� �� ��Ų���� �־��ּ���.',-1);
			exit();
		}

		// ���ε� ���� ������
		if ($_FILES['upload_skin']['error'] == 1) { // UPLOAD_ERR_INI_SIZE
			msg( strtoupper(ini_get('upload_max_filesize')).'B �� �ʰ��ϴ� ������ ���ε� �Ͻ� �� �����ϴ�.',-1);
			exit();
		}

		// ���ε� ���� üũ
		if ( !$_FILES['upload_skin']['name'] ){
			msg('���ε� �� ���� ������ �÷��ּ���.',-1);
			exit();
		}
		if ( !chkExe( $_FILES['upload_skin']['name'] ) == false ){
			msg('���� ������ tar.gz �� ���˴ϴ�.',-1);
			exit();
		}

		// ������ �ش� ��Ų�� �ִ����� üũ
		//$strSkinName	= $_POST['upload_skin_name'] . "_U";	// ���ε�� ��Ų�� ��Ų�� �ڿ� _U �� ����
		$strSkinName	= $_POST['upload_skin_name'];			// ���ε�� ��Ų��
		if( is_dir($pathSkin.$strSkinName) ){
			msg('������ �����ϴ� ��Ų�� �Դϴ�. �ٽ� Ȯ�� ���ֽʽÿ�.',-1);
			exit();
		}

		// ���� ���ε�
		$upload = new upload_file($_FILES['upload_skin'],$pathTmp.$_FILES['upload_skin']['name']);
		if(!$upload -> upload()) msg('���ε������� �ùٸ��� �ʽ��ϴ�.',-1);

		if( is_file($pathTmp . $_FILES['upload_skin']['name']) ){

			$strCommand = "tar -C ".$pathTmp." -pzxf ".$pathTmp . $_FILES['upload_skin']['name'];

			// ��ɾ� ó��
			system($strCommand, $retval);

			// �ش� ��Ų ���� ���� ����
			@unlink( $pathTmp . $_FILES['upload_skin']['name'] );

			// Ȥ�� ���� ���� �ٲ�
			$strCommand	= "chmod 707 ".$pathTmp." -Rf";
			system($strCommand, $retval);

			$pathTmpConf	= $pathTmp."conf/";
			$pathTmpSkin	= $pathTmp."data/skin_today/";
			$pathTmpBnDB	= $pathTmp."data/tmp_skinTodayCopy/bannerDB.php";
			if( !is_dir($pathTmpConf) || !is_dir($pathTmpSkin) ){
				$strCommand	= "rm -rf ".$pathTmp."*";
				system($strCommand, $retval);
				msg('���ε��� ���� ������ �߸� �Ȱ����� �ǴܵǾ� ���ϴ�. Ȯ�� �� �÷��ּ���.',-1);
				exit();
			}

			//if( $retval == 0 ){

				// ������ ���� ���� ����
				$tmp = getDirList($pathTmpConf,"file");
				foreach ($tmp as $tVal){
					for ($i = 0; $i < count($confDesignFile); $i++){
						if( eregi($confDesignFile[$i],$tVal) ){
							@rename($tVal, $pathTmpConf.$confDesignFile[$i].$strSkinName.".php");
							@chmod( $pathTmpConf.$confDesignFile[$i].$strSkinName.".php", 0707 );
						}
					}
				}
				unset($tmp);

				// ��Ų ������ ����
				$tmp = getDirList($pathTmpSkin,"dir");
				if( is_dir($tmp[0]) ){
					@rename($tmp[0], $pathTmpSkin.$strSkinName);
					@chmod( $pathTmpSkin.$strSkinName, 0707 );
				}

				// ���� �� ���� �ű��
				$strCommand1	= "mv ".$pathTmpConf."*.php ".$pathConf;
				$strCommand2	= "mv ".$pathTmpSkin.$strSkinName." ".$pathSkin;
				system($strCommand1, $retval);
				system($strCommand2, $retval);

				// Banner DB Update
				if( is_file($pathTmpBnDB) ){
					include $pathTmpBnDB;
					foreach ($arrBannerDB AS $key){
						@$db->query($key);
					}
					$strSQL = "UPDATE ".GD_BANNER." SET tplSkin = '".$strSkinName."' WHERE tplSkinToday = '__backupDB__'";
					$db->query($strSQL);
				}

				// ���� ���� �� ���� ����
				$strCommand	= "rm -rf ".$pathTmp."*";
				system($strCommand, $retval);

				msg($strSkinName.'��Ų�� ���ε� �Ǿ����ϴ�. ȭ�麸��� Ȯ���Ͻʽÿ�.');
			//}
		}

		echo "<script>opener.location.reload();window.close();</script>";
	break;

	/*

	*/
	case "mod_default":

		include_once dirname(__FILE__) . "/../../conf/config.php";

		{ // ȯ������ ����

			$cfg['tplSkinToday']				= $_POST['tplSkinToday'];
			$cfg['tplSkinTodayWork']			= $_POST['tplSkinTodayWork'];

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

			# ���� ���� ����
			unset($cfg);

			# ��Ų�� �⺻ ����
			if(is_file(dirname(__FILE__) . "/../../conf/design_basicToday_".$_POST['tplSkinTodayWork'].".php")){
				include dirname(__FILE__) . "/../../conf/design_basicToday_".$_POST['tplSkinTodayWork'].".php";
			}

			# �� ��Ų�� �⺻���� ���� (�⺻ ������)
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

			$qfile->open( $path = dirname(__FILE__) . "/../../conf/design_basicToday_".$_POST['tplSkinTodayWork'].".php");
			$qfile->write("<?\n" );

			foreach ( $cfg as $k => $v ){
				$qfile->write("\$cfg['".$k."']\t\t\t=\"".$v."\";\n" );
			}
			$qfile->write("?>" );
			$qfile->close();
			@chMod( $path, 0757 );

			echo("<script>parent.location.href=parent.location.href;</script>");
			exit;
		}

		break;

	case "mod_css":

		$_SERVER[HTTP_REFERER] .= '?' . time();
		include_once dirname(__FILE__) . "/../../conf/config.php";
		include_once dirname(__FILE__) . "/../lib.skin.php";

		{ // �������ڵ����� ����

			$qfile->open( $path = dirname(__FILE__) . "/../../data/skin_today/" . $cfg['tplSkinTodayWork'] . "/style.css");
			if (ini_get('magic_quotes_gpc') == 1) $_POST['content'] = stripslashes( $_POST['content'] );
			$qfile->write($_POST['content'] );
			$qfile->close();
			@chMod( $path, 0757 );
		}


		break;

	case "mod_js":

		$_SERVER[HTTP_REFERER] .= '?' . time();
		include_once dirname(__FILE__) . "/../../conf/config.php";
		include_once dirname(__FILE__) . "/../lib.skin.php";

		{ // �������ڵ����� ����

			$_POST['content'] = str_replace( "&#55203;", "�R", $_POST['content'] );

			$qfile->open( $path = dirname(__FILE__) . "/../../data/skin_today/" . $cfg['tplSkinTodayWork'] . "/common.js");
			if (ini_get('magic_quotes_gpc') == 1) $_POST['content'] = stripslashes( $_POST['content'] );
			$qfile->write($_POST['content'] );
			$qfile->close();
			@chMod( $path, 0757 );
		}

		break;

	case "mod_intro":

		$_SERVER[HTTP_REFERER] .= '?' . time();

		{ // ȯ������ ����

			# ���� ���� ����
			unset($cfg);

			# ��Ų�� �⺻ ����
			if(is_file(dirname(__FILE__) . "/../../conf/design_basicToday_".$_POST['tplSkinTodayWork'].".php")){
				include dirname(__FILE__) . "/../../conf/design_basicToday_".$_POST['tplSkinTodayWork'].".php";
			}

			# ��Ʈ�� ��뿩��
			$cfg['introUseYN']			= $_POST['introUseYN'];

			$cfg = array_map("stripslashes",$cfg);
			$cfg = array_map("addslashes",$cfg);

			$qfile->open( $path = dirname(__FILE__) . "/../../conf/design_basicToday_".$_POST['tplSkinTodayWork'].".php");
			$qfile->write("<?\n" );

			foreach ( $cfg as $k => $v ){

				$qfile->write("\$cfg['".$k."']\t\t\t=\"".$v."\";\n" );
			}

			$qfile->write("?>" );
			$qfile->close();
			@chMod( $path, 0757 );
		}


		{ // �������ڵ����� ����

			$qfile->open( $path = dirname(__FILE__) . "/../../data/skin_today/" . $_POST['tplSkinTodayWork'] . "/main/intro.htm");
			if (ini_get('magic_quotes_gpc') == 1) $_POST['content'] = stripslashes( $_POST['content'] );
			$qfile->write($_POST['content'] );
			$qfile->close();
			@chMod( $path, 0757 );
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

		$dir = str_replace( $_SERVER['SCRIPT_NAME'], "", $_SERVER['SCRIPT_FILENAME'] ) . '/w3c/';
		if (is_dir($dir)){
			if ($_FILES[w3c]){
				$upload = new upload_file;
				$file_array = reverse_file_array($_FILES['w3c']);
				foreach ($_FILES[w3c][tmp_name] as $k=>$v){
					if (is_uploaded_file($v) || $_POST[w3cDel][$k] == 'Y') @unlink($dir.$_POST[w3cOld][$k]);
					if (is_uploaded_file($v)){
						$newfile = $dir.$_FILES[w3c][name][$k];
						$upload->upload_file($file_array[$k],$newfile);
						if(!$upload->upload())msg('�ùٸ������� �����Դϴ�.',-1);
					}
				}
			}
		}

		break;


}
?>