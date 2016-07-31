<?
include "../lib.php";
require_once("../../lib/qfile.class.php");

$qfile = new qfile();

$postData = $_POST;
$postData = array_map('strip_slashes',$postData);
$postData = array_map('add_slashes',$postData);

$displayType = $postData['displayType'];

$qfile->open("../../conf/config.display.php");
$qfile->write("<? \n");
$qfile->write("\$displayCfg = array( \n");
$qfile->write("'displayType' => '".$displayType."', \n");
$qfile->write(") \n;");
$qfile->write("?>");
$qfile->close();

msg('저장되었습니다.',-1);

?>