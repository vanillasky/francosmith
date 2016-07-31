<?

include "../lib.php";
require_once("../../lib/qfile.class.php");
require_once("../../lib/upload.lib.php");
require_once("../../lib/todayshop_cache.class.php");
todayshop_cache::truncate();

$qfile = new qfile();

// config °æ·Î
$pathConf	= "../../conf/";

// skin_today °æ·Î
$pathSkin	= "../../data/skin_today/";

// ÀÓ½Ã Æú´õ °æ·Î ¹× Ã¼Å©
$pathTmp	= "../../data/tmp_skinTodayCopy/";
if ( !@file_exists( $pathTmp ) ) @mkdir( $pathTmp, 0757 );
@chMod( $pathTmp, 0757 );

// µğÀÚÀÎ ¼³Á¤ ÆÄÀÏ ¼³Á¤
$confDesignFile	= array("config.todayshop.banner_","design_skinToday_","design_basicToday_");
$confBaseSkin	= array( 'today' );

// ½ºÅ² ´Ù¿î·Îµå
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

// È®ÀåÀÚ Ã¼Å©
function chkExe( $fn ){

	$app_ext = array( 'gz' );

	$chks = explode( ";", $types );
	$mxs = sizeof( $chks );
	$extFn = strtoLower( strrChr( $fn, "." ) );
	for ( $i = 0; $i < $mxs; $i++ ) if ( trim( $chks[$i] ) && trim( $chks[$i] ) == $extFn ) return true;
	return false;
}

// ÇØ´ç Æú´õÀÇ ÆÄÀÏ ¹× Æú´õ °¡Áö°í ¿À±â
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

// ÇØ´ç Æú´õÀÇ ÇÏÀ§ Æú´õ ¹× ÆÄÀÏ ¸®½ºÆ® °¡Á®¿À±â
function getDirFile($pathDir){

	// ¹è¿­ ¼±¾ğ
	$tmp=$tmp1=$tmp2=$getArr=array();

	// ¹è¿­ÀÎ ¾Æ´Ñ°æ¿ì ¹è¿­ Ã³¸®
	if ( !is_array($pathDir) ){
		$pathDir = array($pathDir);
	}

	// °¢ Æú´õº° Æú´õ¿Í ÆÄÀÏ¸í ÃßÃâ
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

	// Æú´õ¿Í ÆÄÀÏ ¹è¿­ º´ÇÕ
	$tmp1 = array_merge($tmp['dir'],$tmp['file']);

	// Àç±Í
	if(is_array($tmp['dir'])){
		$tmp2 = getDirFile($tmp['dir']);
	}

	//Áßº¹µÈ °ªÀ» Á¦°Å
	$getArr = array_unique(array_merge($tmp1,$tmp2));
	unset($tmp1);
	unset($tmp2);

	return $getArr;
}
// getDirFile ÇÔ¼ö ÀÌÈÄÀÇ Á¤·Ä
function sortDirFile(&$arrDir){
	// ¹è¿­ ¼±¾ğ
	$getArr = array();
	if ( is_array($arrDir) ){
		// ¿ª¼ø Á¤·Ä (³ªÁß Æú´õ »èÁ¦¸¦ À§ÇØ¼­)
		rsort($arrDir);
		// ¹è¿­¿¡ °¢°¢ ´ã±â
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

		if($_GET['useSkinToday']){ // È¯°æÆÄÀÏ ÀúÀå

			# ½ºÅ²º° ±âº» Á¤º¸
			if(is_file(dirname(__FILE__) . "/../../conf/design_basicToday_".$_GET['useSkinToday'].".php")){
				// Åõµ¥ÀÌ¼¥ ½ºÅ² ¼³Á¤ÀÌ ±âº» ¼³Á¤À» º¯°æÇÏÁö ¸øÇÏµµ·Ï ¾Æ·¡ÀÇ ÆÄÀÏÀ» ºÒ·¯¿ÀÁö ¾ÊÀ½.
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

			msg($_GET['useSkinToday']." ½ºÅ²ÀÌ [»ç¿ë½ºÅ²]À¸·Î ¼³Á¤µÇ¾ú½À´Ï´Ù.\\n\\rÀÌÁ¦ Åõµ¥ÀÌ¼¥ È­¸éÀÌ ".$_GET['useSkinToday']." À¸·Î º¸¿©Áı´Ï´Ù.");
			echo("<script>parent.location.href=parent.location.href;</script>");
			exit;
		}

	break;

	case "skinChangeWork":

		include_once dirname(__FILE__) . "/../../conf/config.php";

		if($_GET['workSkinToday']){ // È¯°æÆÄÀÏ ÀúÀå

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

			msg($_GET['workSkinToday']." ½ºÅ²ÀÌ [ÀÛ¾÷½ºÅ²]À¸·Î ¼³Á¤µÇ¾ú½À´Ï´Ù.\\n\\rÀÌÁ¦ µğÀÚÀÎÀÛ¾÷Àº ".$_GET['workSkinToday']." À¸·Î ÇÏ½Ç ¼ö ÀÖ½À´Ï´Ù.");
			echo("<script>parent.location.href=parent.location.href;</script>");
			exit;
		}

	break;

	// ½ºÅ² ´Ù¿î·Îµå
	case "skinDown":

		// ½ºÅ²°ªÀÌ ¾ø´Â °æ¿ì
		if (!$_GET['tplSkinToday']){
			msg('½ºÅ²¸í ¿À·ùÀÔ´Ï´Ù.',-1);
			exit();
		}

		// Banner DB ÃßÃâ
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

		// µğÀÚÀÎ ¼³Á¤ ÆÄÀÏ
		for ($i = 0; $i < count($confDesignFile); $i++){
			if( is_file($pathConf.$confDesignFile[$i].$_GET['tplSkinToday'].".php") ){
				$tmpConf[]	= $pathConf.$confDesignFile[$i].$_GET['tplSkinToday'].".php";
			}
		}

		// ¸í·É¾î ÀÛ¼º
		$strSkinName	= $pathTmp.$_GET['tplSkinToday']."_backup.tar.gz";
		$strCommand = "tar -pzcf ".$strSkinName." ".$pathSkin.$_GET['tplSkinToday']."/* " . implode(" ",$tmpConf);


		// ¸í·É¾î Ã³¸®
		system($strCommand, $retval);

		// ´Ù¿î ·Îµå Ã³¸®
		if( $retval == 0 ){
			if( is_file($strSkinName) ){
				// ½ºÅ² ´Ù¿î
				skinDown($strSkinName);
				// ÇØ´ç ½ºÅ² »èÁ¦
				if ( !unlink( $strSkinName ) ) return false;
				// µğºñ ¹é¾÷ È­ÀÏ »èÁ¦
				if( is_file($pathTmp."bannerDB.php") ){
					if ( !unlink( $pathTmp."bannerDB.php" ) ) return false;
				}
			}else{
				msg('½ºÅ² ´Ù¿î·Îµå¿¡ ½ÇÆĞ ÇÏ¿´½À´Ï´Ù. [¾ĞÃà ´Ù¿î·Îµå ¿À·ù]',-1);
				exit();
			}
		}else{
			msg('½ºÅ² ´Ù¿î·Îµå¿¡ ½ÇÆĞ ÇÏ¿´½À´Ï´Ù. [¾ĞÃà ÆÄÀÏ »ı¼º ¿À·ù]',-1);
			exit();
		}

	break;

	// ½ºÅ² º¹»ç
	case "skinCopy":

		// ±âÁ¸¿¡ ÇØ´ç ½ºÅ²ÀÌ ÀÖ´ÂÁö¸¦ Ã¼Å©
		$strSkinName	= $_GET['tplSkinToday'] . "_C";	// º¹»çµÈ ½ºÅ²Àº ½ºÅ²¸í µÚ¿¡ _C ¸¦ ºÙÀÓ
		if( is_dir($pathSkin.$strSkinName) ){
			msg($strSkinName . ' ½ºÅ²°ú µ¿ÀÏÇÑ ½ºÅ²ÀÌ Á¸ÀçÇÕ´Ï´Ù. ´Ù½Ã È®ÀÎ ÇØÁÖ½Ê½Ã¿ä.',-1);
			exit();
		}

		// µğÀÚÀÎ ¼³Á¤ ÆÄÀÏ º¹»ç
		for ($i = 0; $i < count($confDesignFile); $i++){
			if( is_file($pathConf.$confDesignFile[$i].$_GET['tplSkinToday'].".php") ){
				$tmpConfFileS	= $pathConf.$confDesignFile[$i].$_GET['tplSkinToday'].".php";
				$tmpConfFileC	= $pathConf.$confDesignFile[$i].$strSkinName.".php";
				if (!copy($tmpConfFileS, $tmpConfFileC)) {
					msg( $confDesignFile[$i].$strSkinName.'.php È­ÀÏÀÌ »ı¼ºµÇÁö ¾Ê¾Ò½À´Ï´Ù.');
					$resultChk = false;
				}else{
					@chmod( $pathConf.$confDesignFile[$i].$strSkinName.".php", 0707 );
				}
			}
		}

		// ÆÄÀÏ ¹× Æú´õ ¿Å±â±â
		$strCommand1	= "cp ".$pathSkin.$_GET['tplSkinToday']." ".$pathSkin.$strSkinName." -Rf";
		$strCommand2	= "chmod 707 ".$pathSkin.$strSkinName." -Rf";
		system($strCommand1, $retval);
		system($strCommand2, $retval);

		if( $retval == 0 ){
			// DB Banner Table ÀÇ ÇØ´ç ½ºÅ² º¹»ç
			$strSQL = "SELECT * FROM ".GD_BANNER." WHERE tplSkin = '".$_GET['tplSkinToday']."'";
			$res = $db->query($strSQL);

			while ($data=$db->fetch($res)){
				$strSQL = "INSERT INTO ".GD_BANNER." (loccd,linkaddr,img,regdt,target,sort,tplSkin) VALUES ('".$data['loccd']."','".$data['linkaddr']."','".$data['img']."',now(),'".$data['target']."','".$data['sort']."','".$strSkinName."')";
				$db->query($strSQL);
			}
		}else{
			msg( $strSkinName.' ½ºÅ² º¹»ç¿¡ ÀÌ»óÀÌ ÀÖ½À´Ï´Ù. Á÷Á¢ FTP ¿¡¼­ È®ÀÎÇÏ½Ã±â ¹Ù¶ø´Ï´Ù.');
			$resultChk = false;
		}

		if( $resultChk = true ){
			msg($strSkinName.'½ºÅ²ÀÌ º¹»ç µÇ¾ú½À´Ï´Ù.');
		}

		echo("<script>parent.location.href=parent.location.href;</script>");
		exit;

	break;

	// ½ºÅ² »èÁ¦
	case "skinDel":

		// ÀÛ¾÷/»ç¿ë½ºÅ² Ã¼Å©
		if($_GET['tplSkinToday'] == $cfg['tplSkinToday'] || $_GET['tplSkinToday'] == $cfg['tplSkinTodayWork']){
			msg('ÀÛ¾÷½ºÅ²ÀÌ³ª »ç¿ë½ºÅ²Àº »èÁ¦ÇÒ ¼ö ¾ø½À´Ï´Ù.',-1);
			exit();
		}

		$resultChk = true;

		// µğÀÚÀÎ ¼³Á¤ ÆÄÀÏ »èÁ¦
		for ($i = 0; $i < count($confDesignFile); $i++){
			if( is_file($pathConf.$confDesignFile[$i].$_GET['tplSkinToday'].".php") ){
				$tmpConfFile	= $pathConf.$confDesignFile[$i].$_GET['tplSkinToday'].".php";
				if ( !unlink( $tmpConfFile ) ){
					msg( $pathConf.$confDesignFile[$i].$_GET['tplSkinToday'].'.php È­ÀÏÀÌ »èÁ¦µÇÁö ¾Ê¾Ò½À´Ï´Ù.');
					$resultChk = false;
				}
			}
		}

		// ½ºÅ² Æú´õ »èÁ¦
		$strCommand	= "rm -rf ".$pathSkin.$_GET['tplSkinToday'];
		system($strCommand, $retval);
		if( $retval != 0 ){
			msg( $_GET['tplSkinToday'].' ½ºÅ²ÀÌ Á¤»óÀûÀ¸·Î »èÁ¦µÇÁö ¾Ê¾Ò½À´Ï´Ù. FTP·Î Á÷Á¢ »èÁ¦ ÇÏ½Ê½Ã¿ä.');
			$resultChk = false;
		}

		// DB Banner Table ÀÇ ÇØ´ç »èÁ¦
		$strSQL = "DELETE FROM ".GD_BANNER." WHERE tplSkin = '".$_GET['tplSkinToday']."'";
		$db->query($strSQL);

		if( $resultChk = true ){
			msg($_GET['tplSkinToday'].'½ºÅ²ÀÌ »èÁ¦ µÇ¾ú½À´Ï´Ù.');
		}

		echo("<script>parent.location.href=parent.location.href;</script>");
		exit;

	break;

}

switch ($_POST['mode']){

	// ½ºÅ² ¾÷·Îµå
	case "skinUpload":

		// ½ºÅ²°ªÀÌ ¾ø´Â °æ¿ì
		if ( !$_POST['upload_skin_name'] ){
			msg('¾÷·Îµå ÇÒ ½ºÅ²¸íÀ» ³Ö¾îÁÖ¼¼¿ä.',-1);
			exit();
		}

		// ¾÷·Îµå °¡¿ë »çÀÌÁî
		if ($_FILES['upload_skin']['error'] == 1) { // UPLOAD_ERR_INI_SIZE
			msg( strtoupper(ini_get('upload_max_filesize')).'B ¸¦ ÃÊ°úÇÏ´Â ÆÄÀÏÀº ¾÷·Îµå ÇÏ½Ç ¼ö ¾ø½À´Ï´Ù.',-1);
			exit();
		}

		// ¾÷·Îµå ÆÄÀÏ Ã¼Å©
		if ( !$_FILES['upload_skin']['name'] ){
			msg('¾÷·Îµå ÇÒ ¾ĞÃà ÆÄÀÏÀ» ¿Ã·ÁÁÖ¼¼¿ä.',-1);
			exit();
		}
		if ( !chkExe( $_FILES['upload_skin']['name'] ) == false ){
			msg('¾ĞÃà ÆÄÀÏÀº tar.gz ¸¸ Çã¿ëµË´Ï´Ù.',-1);
			exit();
		}

		// ±âÁ¸¿¡ ÇØ´ç ½ºÅ²ÀÌ ÀÖ´ÂÁö¸¦ Ã¼Å©
		//$strSkinName	= $_POST['upload_skin_name'] . "_U";	// ¾÷·ÎµåµÈ ½ºÅ²Àº ½ºÅ²¸í µÚ¿¡ _U ¸¦ ºÙÀÓ
		$strSkinName	= $_POST['upload_skin_name'];			// ¾÷·ÎµåµÈ ½ºÅ²¸í
		if( is_dir($pathSkin.$strSkinName) ){
			msg('±âÁ¸¿¡ Á¸ÀçÇÏ´Â ½ºÅ²¸í ÀÔ´Ï´Ù. ´Ù½Ã È®ÀÎ ÇØÁÖ½Ê½Ã¿ä.',-1);
			exit();
		}

		// ÆÄÀÏ ¾÷·Îµå
		$upload = new upload_file($_FILES['upload_skin'],$pathTmp.$_FILES['upload_skin']['name']);
		if(!$upload -> upload()) msg('¾÷·ÎµåÆÄÀÏÀÌ ¿Ã¹Ù¸£Áö ¾Ê½À´Ï´Ù.',-1);

		if( is_file($pathTmp . $_FILES['upload_skin']['name']) ){

			$strCommand = "tar -C ".$pathTmp." -pzxf ".$pathTmp . $_FILES['upload_skin']['name'];

			// ¸í·É¾î Ã³¸®
			system($strCommand, $retval);

			// ÇØ´ç ½ºÅ² ¾ĞÃà ÆÄÀÏ »èÁ¦
			@unlink( $pathTmp . $_FILES['upload_skin']['name'] );

			// È¤½Ã ¸ô¶ó ÀüºÎ ¹Ù²Ş
			$strCommand	= "chmod 707 ".$pathTmp." -Rf";
			system($strCommand, $retval);

			$pathTmpConf	= $pathTmp."conf/";
			$pathTmpSkin	= $pathTmp."data/skin_today/";
			$pathTmpBnDB	= $pathTmp."data/tmp_skinTodayCopy/bannerDB.php";
			if( !is_dir($pathTmpConf) || !is_dir($pathTmpSkin) ){
				$strCommand	= "rm -rf ".$pathTmp."*";
				system($strCommand, $retval);
				msg('¾÷·ÎµåÇÑ ¾ĞÃà ÆÄÀÏÀÌ Àß¸ø µÈ°ÍÀ¸·Î ÆÇ´ÜµÇ¾î Áı´Ï´Ù. È®ÀÎ ÈÄ ¿Ã·ÁÁÖ¼¼¿ä.',-1);
				exit();
			}

			//if( $retval == 0 ){

				// µğÀÚÀÎ ¼³Á¤ ÆÄÀÏ º¯°æ
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

				// ½ºÅ² Æú´õ¸í º¯°æ
				$tmp = getDirList($pathTmpSkin,"dir");
				if( is_dir($tmp[0]) ){
					@rename($tmp[0], $pathTmpSkin.$strSkinName);
					@chmod( $pathTmpSkin.$strSkinName, 0707 );
				}

				// ÆÄÀÏ ¹× Æú´õ ¿Å±â±â
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

				// ³²Àº ÆÄÀÏ ¹× Æú´õ »èÁ¦
				$strCommand	= "rm -rf ".$pathTmp."*";
				system($strCommand, $retval);

				msg($strSkinName.'½ºÅ²ÀÌ ¾÷·Îµå µÇ¾ú½À´Ï´Ù. È­¸éº¸±â·Î È®ÀÎÇÏ½Ê½Ã¿ä.');
			//}
		}

		echo "<script>opener.location.reload();window.close();</script>";
	break;

	/*

	*/
	case "mod_default":

		include_once dirname(__FILE__) . "/../../conf/config.php";

		{ // È¯°æÆÄÀÏ ÀúÀå

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

			# ±âÁ¸ Á¤º¸ ºñ¿ì±â
			unset($cfg);

			# ½ºÅ²º° ±âº» Á¤º¸
			if(is_file(dirname(__FILE__) . "/../../conf/design_basicToday_".$_POST['tplSkinTodayWork'].".php")){
				include dirname(__FILE__) . "/../../conf/design_basicToday_".$_POST['tplSkinTodayWork'].".php";
			}

			# °¢ ½ºÅ²º° ±âº»Á¤º¸ ÀúÀå (±âº» »çÀÌÁî)
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

		{ // µğÀÚÀÎÄÚµğÆÄÀÏ ÀúÀå

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

		{ // µğÀÚÀÎÄÚµğÆÄÀÏ ÀúÀå

			$_POST['content'] = str_replace( "&#55203;", "ÆR", $_POST['content'] );

			$qfile->open( $path = dirname(__FILE__) . "/../../data/skin_today/" . $cfg['tplSkinTodayWork'] . "/common.js");
			if (ini_get('magic_quotes_gpc') == 1) $_POST['content'] = stripslashes( $_POST['content'] );
			$qfile->write($_POST['content'] );
			$qfile->close();
			@chMod( $path, 0757 );
		}

		break;

	case "mod_intro":

		$_SERVER[HTTP_REFERER] .= '?' . time();

		{ // È¯°æÆÄÀÏ ÀúÀå

			# ±âÁ¸ Á¤º¸ ºñ¿ì±â
			unset($cfg);

			# ½ºÅ²º° ±âº» Á¤º¸
			if(is_file(dirname(__FILE__) . "/../../conf/design_basicToday_".$_POST['tplSkinTodayWork'].".php")){
				include dirname(__FILE__) . "/../../conf/design_basicToday_".$_POST['tplSkinTodayWork'].".php";
			}

			# ÀÎÆ®·Î »ç¿ë¿©ºÎ
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


		{ // µğÀÚÀÎÄÚµğÆÄÀÏ ÀúÀå

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
						if(!$upload->upload())msg('¿Ã¹Ù¸£Áö¾ÊÀº ÆÄÀÏÀÔ´Ï´Ù.',-1);
					}
				}
			}
		}

		break;


}
?>