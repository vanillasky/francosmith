<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
require_once("../../lib/upload.lib.php");
$qfile = new qfile();

// config ���
$pathConf	= "../../conf/";

// skin ���
$pathSkin	= "../../data/skin_mobileV2/";

// �ӽ� ���� ��� �� üũ
$pathTmp	= "../../data/tmp_skinCopy/";
if ( !@file_exists( $pathTmp ) ) @mkdir( $pathTmp, 0757 );
@chMod( $pathTmp, 0757 );

// ������ ���� ���� ����
$confDesignFile	= array("design_skinMobileV2_", "design_itMobileV2_", "config.mobileAnimationBanner_");
$confBaseSkin	= array("default");

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

switch ($_GET['mode']){

	case "skinChange":

		include dirname(__FILE__) . "/../../conf/config.php";

		if($_GET['useSkin']){ // ȯ������ ����

			$cfg['tplSkinMobile']			= $_GET['useSkin'];

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

			@include dirname(__FILE__) . "/../../conf/config.mobileShop.php";
			$cfgMobileShop = (array)$cfgMobileShop;
			$cfgMobileShop = array_map("stripslashes",$cfgMobileShop);
			$cfgMobileShop = array_map("addslashes",$cfgMobileShop);

			$cfgMobileShop['tplSkinMobile'] = $_GET['useSkin'];

			$qfile->open($path = dirname(__FILE__) . "/../../conf/config.mobileShop.php");
			$qfile->write("<? \n");
			$qfile->write("\$cfgMobileShop = array( \n");
			foreach ($cfgMobileShop as $k=>$v) $qfile->write("'$k' => '$v', \n");
			$qfile->write(") \n;");
			$qfile->write("?>");
			$qfile->close();
			@chMod( $path, 0757 );

			$templateCache = Core::loader('TemplateCache');
			$templateCache->clearCache();

			msg($_GET['useSkin']." ��Ų�� [��뽺Ų]���� �����Ǿ����ϴ�.\\n\\r���� ���θ�ȭ���� ".$_GET['useSkin']." ���� �������ϴ�.");
			echo("<script>parent.location.href=parent.location.href;</script>");
			exit;
		}

	break;

	case "skinChangeWork":

		include_once dirname(__FILE__) . "/../../conf/config.php";

		if($_GET['workSkin']){ // ȯ������ ����

			$cfg['tplSkinMobileWork']			= $_GET['workSkin'];

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

			msg($_GET['workSkin']." ��Ų�� [�۾���Ų]���� �����Ǿ����ϴ�.\\n\\r���� �������۾��� ".$_GET['workSkin']." ���� �Ͻ� �� �ֽ��ϴ�.");
			echo("<script>parent.location.href=parent.location.href;</script>");
			exit;
		}

	break;

	// ��Ų �ٿ�ε�
	case "skinDown":

		// ��Ų���� ���� ���
		if (!$_GET['tplSkin']){
			msg('��Ų�� �����Դϴ�.',-1);
			exit();
		}

		// ������ ���� ����
		for ($i = 0; $i < count($confDesignFile); $i++){
			if( is_file($pathConf.$confDesignFile[$i].$_GET['tplSkin'].".php") ){
				$tmpConf[]	= $pathConf.$confDesignFile[$i].$_GET['tplSkin'].".php";
			}
		}		

		// ��ɾ� �ۼ�
		if (!$_SERVER[SystemRoot]) {
			$strSkinName	= $pathTmp.$_GET['tplSkin']."_backup.tar.gz";
			$strCommand = "tar -pzcf ".$strSkinName." ".$pathSkin.$_GET['tplSkin']."/* " . implode(" ",$tmpConf);
		} else { 
			echo "<script>alert('�������� ȯ�濡���� ������� �ʽ��ϴ�.');</script>";
			echo("<script>parent.location.href=parent.location.href;</script>");
			exit; 
		}
		// ��ɾ� ó��
		system($strCommand, $retval);

		// �ٿ� �ε� ó��
		if( $retval == 0 ){
			if( is_file($strSkinName) ){
				// ��Ų �ٿ�
				skinDown($strSkinName);
				// �ش� ��Ų ����
				if ( !unlink( $strSkinName ) ) return false;
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
		$strSkinName	= $_GET['tplSkin'] . "_C";	// ����� ��Ų�� ��Ų�� �ڿ� _C �� ����
		if( is_dir($pathSkin.$strSkinName) ){
			msg($strSkinName . ' ��Ų�� ������ ��Ų�� �����մϴ�. �ٽ� Ȯ�� ���ֽʽÿ�.',-1);
			exit();
		}

		// ������ ���� ���� ����
		for ($i = 0; $i < count($confDesignFile); $i++){
			if( is_file($pathConf.$confDesignFile[$i].$_GET['tplSkin'].".php") ){
				$tmpConfFileS	= $pathConf.$confDesignFile[$i].$_GET['tplSkin'].".php";
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
		if (!$_SERVER[SystemRoot]) {
			$strCommand1	= "cp ".$pathSkin.$_GET['tplSkin']." ".$pathSkin.$strSkinName." -Rf";
			$strCommand2	= "chmod 707 ".$pathSkin.$strSkinName." -Rf";
			system($strCommand1, $retval);
			system($strCommand2, $retval);
		} else {
			# �������� ȯ�� 
			$windowsPathSkins = str_replace("/", "\\", $pathSkin); 
			$strCommand1	= "xcopy /e /h /k /y /i /q ".$windowsPathSkins.$_GET['tplSkin']." ".$windowsPathSkins.$strSkinName." ";
			system($strCommand1, $retval);
		}

		if( !$retval == 0 ){
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
		if($_GET['tplSkin'] == $cfg['tplSkinMobile'] || $_GET['tplSkin'] == $cfg['tplSkinMobileWork']){
			msg('�۾���Ų�̳� ��뽺Ų�� ������ �� �����ϴ�.',-1);
			exit();
		}

		$resultChk = true;

		// ������ ���� ���� ����
		for ($i = 0; $i < count($confDesignFile); $i++){
			if( is_file($pathConf.$confDesignFile[$i].$_GET['tplSkin'].".php") ){
				$tmpConfFile	= $pathConf.$confDesignFile[$i].$_GET['tplSkin'].".php";
				if ( !unlink( $tmpConfFile ) ){
					msg( $pathConf.$confDesignFile[$i].$_GET['tplSkin'].'.php ȭ���� �������� �ʾҽ��ϴ�.');
					$resultChk = false;
				}
			}
		}

		// ��Ų ���� ����
		if (!$_SERVER[SystemRoot]) {
			$strCommand	= "rm -rf ".$pathSkin.$_GET['tplSkin'];
		} else {
			$windowsPathSkins = str_replace("/", "\\", $pathSkin); 
			$strCommand	= "rmDir /s /q ".$windowsPathSkins.$_GET['tplSkin'];
			//debug($strCommand); exit; 
		}
		system($strCommand, $retval);
		if( $retval != 0 ){
			msg( $_GET['tplSkin'].' ��Ų�� ���������� �������� �ʾҽ��ϴ�. FTP�� ���� ���� �Ͻʽÿ�.');
			$resultChk = false;
		}

		if( $resultChk = true ){
			msg($_GET['tplSkin'].'��Ų�� ���� �Ǿ����ϴ�.');
		}

		echo("<script>parent.location.href=parent.location.href;</script>");
		exit;

	break;

}

switch ($_POST['mode']){

	// ��Ų ���ε�
	case "skinUpload":
		if ($_SERVER[SystemRoot]) {
			echo "<script>alert('�������� ȯ�濡���� ������� �ʽ��ϴ�.');</script>";
			echo "<script>opener.location.reload();window.close();</script>";
			exit; 
		}
		// ��Ų���� ���� ���
		if ( !$_POST['upload_skin_name'] ){
			msg('���ε� �� ��Ų���� �־��ּ���.',-1);
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
			$pathTmpSkin	= $pathTmp."data/skin_mobileV2/";
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

				// ���� ���� �� ���� ����
				$strCommand	= "rm -rf ".$pathTmp."*";
				system($strCommand, $retval);

				msg($strSkinName.'��Ų�� ���ε� �Ǿ����ϴ�. ȭ�麸��� Ȯ���Ͻʽÿ�.');
			//}
		}

		echo "<script>opener.location.reload();window.close();</script>";
	break;
}
?>