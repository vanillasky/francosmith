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
		$filename = "facebook_btn_".time().$i.".".$ext;
		$upload->upload_file($file_array[$i],$dir.'/'.$filename,'image');
		$ret = $upload->upload();
		//if($r_myicon[$i]) @unlink($dir.'/'. $r_myicon[$i]);
		//$r_myicon[$i] = $filename;

		$_POST['page']['facebookBtn']=$filename;
	}else{
		$r_myicon[$i] = ($r_myicon[$i] == '') ? "" : $r_myicon[$i];
	}
}


if($_POST['mode']=='page') {
	$_POST['page']['useYn'] = ($_POST['useYn']!='')?$_POST['useYn'] : $fb->defaultUseYn ;
	$_POST['page']['addr'] =   ($_POST['addr']!='')?$_POST['addr'] : $fb->defaultAddr ;
	$_POST['page']['url'] =  ($_POST['addr']!='')? urlencode("http://facebook.com/".$_POST['addr']) : $fb->defaultUrl ;  
	$_POST['page']['width']	 = ($_POST['width']!='')?$_POST['width'] : $fb->defaultWidth ;
	$_POST['page']['height']	 = ($_POST['height']!='')?$_POST['height'] : $fb->defaultHeight ;
	$_POST['page']['bordercolor']	 = ($_POST['bordercolor']!='')?$_POST['bordercolor'] : $fb->defaultBordercolor ;
	$_POST['page']['streamYn']	 = ($_POST['streamYn']!='')?$_POST['streamYn'] : 'false'  ;
	$_POST['page']['facesYn']	 = ($_POST['facesYn']!='')?$_POST['facesYn'] : 'false'  ;	

	$facebook = array();
	$facebook = array_map("addslashes",array_map("stripslashes",$facebook));
	$facebook = array_merge($facebook,$_POST[page]);
	$qfile->open("../../conf/fbPage.cfg.php");
	$qfile->write("<? \n");
	$qfile->write("\$fbPageCfg = array( \n");
	foreach ($facebook as $k=>$v) $qfile->write("'$k' => '$v', \n");
	$qfile->write(") \n;");
	$qfile->write("?>");
	$qfile->close();
	@chmod("../../conf/fbPage.cfg.php",0707);
	go($_SERVER[HTTP_REFERER]);
}
else if($_POST['mode']=='cmt'){
	$_POST['cmt']['useYn'] =  ($_POST['useYn']!='')?$_POST['useYn'] : $fb->defaultUseYn ;
	$_POST['cmt']['count'] =  ($_POST['count']!='')?$_POST['count'] : $fb->defaultCount  ;
	$_POST['cmt']['width'] =   ($_POST['width']!='')?$_POST['width'] : $fb->defaultCmtWidth  ;

	$facebook = array();
	$facebook = array_map("addslashes",array_map("stripslashes",$facebook));
	$facebook = array_merge($facebook,$_POST[cmt]);
	$qfile->open("../../conf/fbCmt.cfg.php");
	$qfile->write("<? \n");
	$qfile->write("\$fbCmtCfg = array( \n");
	foreach ($facebook as $k=>$v) $qfile->write("'$k' => '$v', \n");
	$qfile->write(") \n;");
	$qfile->write("?>");
	$qfile->close();
	@chmod("../../conf/fbCmt.cfg.php",0707);
	go($_SERVER[HTTP_REFERER]."#cmt");
}
?>