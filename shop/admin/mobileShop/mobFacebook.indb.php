<?
include "../lib.php";
require_once("../../lib/qfile.class.php");
require_once("../../lib/upload.lib.php");
require_once("../../lib/facebook.class.php");
$qfile = new qfile();
$fb = new Facebook();
$upload = new upload_file;

$dir = "../../data/sns";
if (!is_dir($dir)) {
	@mkdir($dir, 0707);
	@chmod($dir, 0707);
}

$file_array = array();
$file_array = reverse_file_array($_FILES['facebook_btn']);

for($i=0;$i<count($_FILES[facebook_btn][tmp_name]);$i++){
	if($_FILES[facebook_btn][tmp_name][$i]){
		$tmp = explode('.',$_FILES[facebook_btn][name][$i]);
		$ext = strtolower($tmp[count($tmp) - 1]);
		$filename = "mbfacebook_btn_".time().$i.".".$ext;
		$upload->upload_file($file_array[$i],$dir.'/'.$filename,'image');
		$ret = $upload->upload();
		//if($r_myicon[$i]) @unlink($dir.'/'. $r_myicon[$i]);
		//$r_myicon[$i] = $filename;

		$_POST['page']['mbfacebookBtn']=$filename;
	}else{
		$r_myicon[$i] = ($r_myicon[$i] == '') ? "" : $r_myicon[$i];
	}
}


$_POST['page']['useYn'] = ($_POST['useYn']!='')?$_POST['useYn'] : $fb->defaultUseYn ;
$_POST['page']['addr'] =  ($_POST['addr']!='')?$_POST['addr'] : $fb->defaultAddr ;

$facebook = array();
$facebook = array_map("addslashes",array_map("stripslashes",$facebook));
$facebook = array_merge($facebook,$_POST[page]);
$qfile->open("../../conf/mfbPage.cfg.php");
$qfile->write("<? \n");
$qfile->write("\$mfbPageCfg = array( \n");
foreach ($facebook as $k=>$v) $qfile->write("'$k' => '$v', \n");
$qfile->write(") \n;");
$qfile->write("?>");
$qfile->close();
@chmod("../../conf/mfbPage.cfg.php",0707);

go($_SERVER[HTTP_REFERER]);
?>