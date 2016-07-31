<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
require_once("../../lib/upload.lib.php");
$qfile = new qfile();

// config 경로
$pathConf	= "../../conf/";

// skin 경로
$pathSkin	= "../../data/skin_mobileV2/";

// 임시 폴더 경로 및 체크
$pathTmp	= "../../data/tmp_skinCopy/";
if ( !@file_exists( $pathTmp ) ) @mkdir( $pathTmp, 0757 );
@chMod( $pathTmp, 0757 );

// 디자인 설정 파일 설정
$confDesignFile	= array("design_skinMobileV2_", "design_itMobileV2_", "config.mobileAnimationBanner_");
$confBaseSkin	= array("default");

// 스킨 다운로드
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

// 확장자 체크
function chkExe( $fn ){

	$app_ext = array( 'gz' );

	$chks = explode( ";", $types );
	$mxs = sizeof( $chks );
	$extFn = strtoLower( strrChr( $fn, "." ) );
	for ( $i = 0; $i < $mxs; $i++ ) if ( trim( $chks[$i] ) && trim( $chks[$i] ) == $extFn ) return true;
	return false;
}

// 해당 폴더의 파일 및 폴더 가지고 오기
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

// 해당 폴더의 하위 폴더 및 파일 리스트 가져오기
function getDirFile($pathDir){

	// 배열 선언
	$tmp=$tmp1=$tmp2=$getArr=array();

	// 배열인 아닌경우 배열 처리
	if ( !is_array($pathDir) ){
		$pathDir = array($pathDir);
	}

	// 각 폴더별 폴더와 파일명 추출
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

	// 폴더와 파일 배열 병합
	$tmp1 = array_merge($tmp['dir'],$tmp['file']);

	// 재귀
	if(is_array($tmp['dir'])){
		$tmp2 = getDirFile($tmp['dir']);
	}

	//중복된 값을 제거
	$getArr = array_unique(array_merge($tmp1,$tmp2));
	unset($tmp1);
	unset($tmp2);

	return $getArr;
}
// getDirFile 함수 이후의 정렬
function sortDirFile(&$arrDir){
	// 배열 선언
	$getArr = array();
	if ( is_array($arrDir) ){
		// 역순 정렬 (나중 폴더 삭제를 위해서)
		rsort($arrDir);
		// 배열에 각각 담기
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

		if($_GET['useSkin']){ // 환경파일 저장

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

			msg($_GET['useSkin']." 스킨이 [사용스킨]으로 설정되었습니다.\\n\\r이제 쇼핑몰화면이 ".$_GET['useSkin']." 으로 보여집니다.");
			echo("<script>parent.location.href=parent.location.href;</script>");
			exit;
		}

	break;

	case "skinChangeWork":

		include_once dirname(__FILE__) . "/../../conf/config.php";

		if($_GET['workSkin']){ // 환경파일 저장

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

			msg($_GET['workSkin']." 스킨이 [작업스킨]으로 설정되었습니다.\\n\\r이제 디자인작업은 ".$_GET['workSkin']." 으로 하실 수 있습니다.");
			echo("<script>parent.location.href=parent.location.href;</script>");
			exit;
		}

	break;

	// 스킨 다운로드
	case "skinDown":

		// 스킨값이 없는 경우
		if (!$_GET['tplSkin']){
			msg('스킨명 오류입니다.',-1);
			exit();
		}

		// 디자인 설정 파일
		for ($i = 0; $i < count($confDesignFile); $i++){
			if( is_file($pathConf.$confDesignFile[$i].$_GET['tplSkin'].".php") ){
				$tmpConf[]	= $pathConf.$confDesignFile[$i].$_GET['tplSkin'].".php";
			}
		}		

		// 명령어 작성
		if (!$_SERVER[SystemRoot]) {
			$strSkinName	= $pathTmp.$_GET['tplSkin']."_backup.tar.gz";
			$strCommand = "tar -pzcf ".$strSkinName." ".$pathSkin.$_GET['tplSkin']."/* " . implode(" ",$tmpConf);
		} else { 
			echo "<script>alert('윈도우즈 환경에서는 실행되지 않습니다.');</script>";
			echo("<script>parent.location.href=parent.location.href;</script>");
			exit; 
		}
		// 명령어 처리
		system($strCommand, $retval);

		// 다운 로드 처리
		if( $retval == 0 ){
			if( is_file($strSkinName) ){
				// 스킨 다운
				skinDown($strSkinName);
				// 해당 스킨 삭제
				if ( !unlink( $strSkinName ) ) return false;
			}else{
				msg('스킨 다운로드에 실패 하였습니다. [압축 다운로드 오류]',-1);
				exit();
			}
		}else{
			msg('스킨 다운로드에 실패 하였습니다. [압축 파일 생성 오류]',-1);
			exit();
		}

	break;

	// 스킨 복사
	case "skinCopy":

		// 기존에 해당 스킨이 있는지를 체크
		$strSkinName	= $_GET['tplSkin'] . "_C";	// 복사된 스킨은 스킨명 뒤에 _C 를 붙임
		if( is_dir($pathSkin.$strSkinName) ){
			msg($strSkinName . ' 스킨과 동일한 스킨이 존재합니다. 다시 확인 해주십시요.',-1);
			exit();
		}

		// 디자인 설정 파일 복사
		for ($i = 0; $i < count($confDesignFile); $i++){
			if( is_file($pathConf.$confDesignFile[$i].$_GET['tplSkin'].".php") ){
				$tmpConfFileS	= $pathConf.$confDesignFile[$i].$_GET['tplSkin'].".php";
				$tmpConfFileC	= $pathConf.$confDesignFile[$i].$strSkinName.".php";
				if (!copy($tmpConfFileS, $tmpConfFileC)) {
					msg( $confDesignFile[$i].$strSkinName.'.php 화일이 생성되지 않았습니다.');
					$resultChk = false;
				}else{
					@chmod( $pathConf.$confDesignFile[$i].$strSkinName.".php", 0707 );
				}
			}
		}

		// 파일 및 폴더 옮기기
		if (!$_SERVER[SystemRoot]) {
			$strCommand1	= "cp ".$pathSkin.$_GET['tplSkin']." ".$pathSkin.$strSkinName." -Rf";
			$strCommand2	= "chmod 707 ".$pathSkin.$strSkinName." -Rf";
			system($strCommand1, $retval);
			system($strCommand2, $retval);
		} else {
			# 윈도우즈 환경 
			$windowsPathSkins = str_replace("/", "\\", $pathSkin); 
			$strCommand1	= "xcopy /e /h /k /y /i /q ".$windowsPathSkins.$_GET['tplSkin']." ".$windowsPathSkins.$strSkinName." ";
			system($strCommand1, $retval);
		}

		if( !$retval == 0 ){
			msg( $strSkinName.' 스킨 복사에 이상이 있습니다. 직접 FTP 에서 확인하시기 바랍니다.');
			$resultChk = false;
		}

		if( $resultChk = true ){
			msg($strSkinName.'스킨이 복사 되었습니다.');
		}

		echo("<script>parent.location.href=parent.location.href;</script>");
		exit;

	break;

	// 스킨 삭제
	case "skinDel":

		// 작업/사용스킨 체크
		if($_GET['tplSkin'] == $cfg['tplSkinMobile'] || $_GET['tplSkin'] == $cfg['tplSkinMobileWork']){
			msg('작업스킨이나 사용스킨은 삭제할 수 없습니다.',-1);
			exit();
		}

		$resultChk = true;

		// 디자인 설정 파일 삭제
		for ($i = 0; $i < count($confDesignFile); $i++){
			if( is_file($pathConf.$confDesignFile[$i].$_GET['tplSkin'].".php") ){
				$tmpConfFile	= $pathConf.$confDesignFile[$i].$_GET['tplSkin'].".php";
				if ( !unlink( $tmpConfFile ) ){
					msg( $pathConf.$confDesignFile[$i].$_GET['tplSkin'].'.php 화일이 삭제되지 않았습니다.');
					$resultChk = false;
				}
			}
		}

		// 스킨 폴더 삭제
		if (!$_SERVER[SystemRoot]) {
			$strCommand	= "rm -rf ".$pathSkin.$_GET['tplSkin'];
		} else {
			$windowsPathSkins = str_replace("/", "\\", $pathSkin); 
			$strCommand	= "rmDir /s /q ".$windowsPathSkins.$_GET['tplSkin'];
			//debug($strCommand); exit; 
		}
		system($strCommand, $retval);
		if( $retval != 0 ){
			msg( $_GET['tplSkin'].' 스킨이 정상적으로 삭제되지 않았습니다. FTP로 직접 삭제 하십시요.');
			$resultChk = false;
		}

		if( $resultChk = true ){
			msg($_GET['tplSkin'].'스킨이 삭제 되었습니다.');
		}

		echo("<script>parent.location.href=parent.location.href;</script>");
		exit;

	break;

}

switch ($_POST['mode']){

	// 스킨 업로드
	case "skinUpload":
		if ($_SERVER[SystemRoot]) {
			echo "<script>alert('윈도우즈 환경에서는 실행되지 않습니다.');</script>";
			echo "<script>opener.location.reload();window.close();</script>";
			exit; 
		}
		// 스킨값이 없는 경우
		if ( !$_POST['upload_skin_name'] ){
			msg('업로드 할 스킨명을 넣어주세요.',-1);
			exit();
		}

		// 업로드 파일 체크
		if ( !$_FILES['upload_skin']['name'] ){
			msg('업로드 할 압축 파일을 올려주세요.',-1);
			exit();
		}
		if ( !chkExe( $_FILES['upload_skin']['name'] ) == false ){
			msg('압축 파일은 tar.gz 만 허용됩니다.',-1);
			exit();
		}

		$strSkinName	= $_POST['upload_skin_name'];			// 업로드된 스킨명
		if( is_dir($pathSkin.$strSkinName) ){
			msg('기존에 존재하는 스킨명 입니다. 다시 확인 해주십시요.',-1);
			exit();
		}

		// 파일 업로드
		$upload = new upload_file($_FILES['upload_skin'],$pathTmp.$_FILES['upload_skin']['name']);
		if(!$upload -> upload()) msg('업로드파일이 올바르지 않습니다.',-1);

		if( is_file($pathTmp . $_FILES['upload_skin']['name']) ){

			$strCommand = "tar -C ".$pathTmp." -pzxf ".$pathTmp . $_FILES['upload_skin']['name'];

			// 명령어 처리
			system($strCommand, $retval);

			// 해당 스킨 압축 파일 삭제
			@unlink( $pathTmp . $_FILES['upload_skin']['name'] );

			// 혹시 몰라 전부 바꿈
			$strCommand	= "chmod 707 ".$pathTmp." -Rf";
			system($strCommand, $retval);

			$pathTmpConf	= $pathTmp."conf/";
			$pathTmpSkin	= $pathTmp."data/skin_mobileV2/";
			if( !is_dir($pathTmpConf) || !is_dir($pathTmpSkin) ){
				$strCommand	= "rm -rf ".$pathTmp."*";
				system($strCommand, $retval);
				msg('업로드한 압축 파일이 잘못 된것으로 판단되어 집니다. 확인 후 올려주세요.',-1);
				exit();
			}

			//if( $retval == 0 ){

				// 디자인 설정 파일 변경
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

				// 스킨 폴더명 변경
				$tmp = getDirList($pathTmpSkin,"dir");
				if( is_dir($tmp[0]) ){
					@rename($tmp[0], $pathTmpSkin.$strSkinName);
					@chmod( $pathTmpSkin.$strSkinName, 0707 );
				}

				// 파일 및 폴더 옮기기
				$strCommand1	= "mv ".$pathTmpConf."*.php ".$pathConf;
				$strCommand2	= "mv ".$pathTmpSkin.$strSkinName." ".$pathSkin;
				system($strCommand1, $retval);
				system($strCommand2, $retval);

				// 남은 파일 및 폴더 삭제
				$strCommand	= "rm -rf ".$pathTmp."*";
				system($strCommand, $retval);

				msg($strSkinName.'스킨이 업로드 되었습니다. 화면보기로 확인하십시요.');
			//}
		}

		echo "<script>opener.location.reload();window.close();</script>";
	break;
}
?>