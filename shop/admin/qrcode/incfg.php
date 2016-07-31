<?
@require "../lib.php";
@require "../../lib/lib.enc.php";
@require "../../lib/load.class.php";
@require "../../lib/qfile.class.php";
@require "../../lib/upload.lib.php";
@require "../../conf/config.php";
@require "../../conf/qr.cfg.php";

if($_POST['useLogo'] == 'y' && !empty($_FILES['logoImg'])){
	$LoadClass = new LoadClass();
	$target = dirname(__FILE__)."/../../data/skin/".$cfg['tplSkin']."/img/";
	$tmpData = dirname(__FILE__)."/../../data/skin/";

	$LoadClass->class_load('upload','upload_file');
	if($_FILES['logoImg'][tmp_name]){
		$tmp = explode('.',$_FILES['logoImg'][name]);
		$ext = $tmp[count($tmp)-1];
		$filename = "qr_Logo.$ext";
		$LoadClass->class['upload']->upload_set($_FILES['logoImg'],$target.$filename,'image');
		$LoadClass->class['upload']->upload();
	}
}

if(!$filename)$filename = $qrCfg['logoImg'];
$arr = array( 
'useGoods'=>$_POST['useGoods'],
'useEvent'=>$_POST['useEvent'],
'useLogo'=>$_POST['useLogo'],
'logoImg'=>$filename,
'degree'=>$_POST['degree'],
'logoLocation'=>$_POST['logoLocation'],
'qr_style'=>$_POST['qr_style'],
); 

$qfile = new qfile();

foreach($arr as $k=>$v)
{
	if(is_array($v)):
		foreach ($v as $k1=>$v1)$qrCfg[$k][] = addslashes($v1);
	else:
		$qrCfg[$k] = addslashes($v);
	endif;
}

$qfile->open("../../conf/qr.cfg.php");

$qfile->write("<? \n");
$qfile->write("\$qrCfg = array( \n");

foreach ($qrCfg as $k=>$v)
{
	if(is_array($v)):
		$qfile->write("'$k' => array(");
		foreach ($v as $k1=>$v1) $qfile->write("'$v1',");
		$qfile->write("), \n");
	else:
		$qfile->write("'$k' => '$v', \n");
	endif;
	$str .= "ok - ".$k."=>".$v."<br>";
}

$qfile->write(") \n;");
$qfile->write("?>");

$qfile->close();

@chmod("../../conf/qr.cfg.php",0707);

go($_POST[returnUrl]);
?>