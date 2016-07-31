<?
include "lib.php";
require_once("../lib/qfile.class.php");
$qfile = new qfile();

$url = "http://www.godo.co.kr/userinterface/_license.agree.php?license=".urlencode(base64_encode($godo[sno]));
$tmp = readurl($url);
if($tmp == 'ok'){
	$file = "../conf/license.cfg.php";
	$qfile->open($file);
	$qfile->write("agree");
	$qfile->close();
	chmod($file,0707);
}
?>