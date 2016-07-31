<?
include "./lib/library.php";

$file	= "./conf/godomall.cfg.php";
$file	= @file($file);
$godo	= decode($file[1],1);
echo $godo[sno];
?>