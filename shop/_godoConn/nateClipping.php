<?php
require "../lib/library.php";
require_once "../lib/lib.enc.php";
require_once "../lib/load.class.php";
require_once "../lib/qfile.class.php";
require_once "../lib/nateClipping.class.php";

$file = file("../conf/godomall.cfg.php");
$godo = decode($file[1],1);
$key = unserialize(godoConnDecode($_GET['key']));
$arr = array('sid'=>$key[0],'status'=>$key[1],'sno'=>$key[2]);

if($godo['sno'] == $key[2]){
	$nate = new NateClipping();
	$nate -> config_write($arr);
}else echo "올바른 접속 경로가 아닙니다.";
?>