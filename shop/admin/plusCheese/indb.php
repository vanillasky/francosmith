<?php
@require "../lib.php";
@require "../../lib/lib.enc.php";
@require "../../lib/load.class.php";
@require "../../lib/qfile.class.php";
@require "../../lib/upload.lib.php";
@include "../../conf/config.plusCheeseCfg.php";
include "../../lib/plusCheese.class.php";

$qfile = new qfile;

if($_GET['mode'] != ""){
	$_POST['mode'] = $_GET['mode'];
	$_POST['key'] = $_GET['key'];
}
switch($_POST['mode']){
	case "sno":
		$plusCheeseCfg['key'] = $_POST['key'];

		$qfile->open("../../conf/config.plusCheeseCfg.php");
		$qfile->write("<? \n");
		$qfile->write("\$plusCheeseCfg = array( \n");
		foreach ($plusCheeseCfg as $k=>$v){
			if(is_array($v)):
				$qfile->write("'$k' => array(");
				foreach ($v as $k1=>$v1) $qfile->write("'$v1',");
				$qfile->write("), \n");
			else:
				$qfile->write("'$k' => '$v', \n");
			endif;
		}
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();
		@chmod("../../conf/config.plusCheeseCfg.php",0707);
		echo "A";
		break;
	case "set": //설정 저장
		$qfile->open("../../conf/config.plusCheeseCfg.php");
		$qfile->write("<? \n");
		$qfile->write("\$plusCheeseCfg = array( \n");
		foreach ($_POST as $k=>$v){
			if($k == "mode") continue;

			if(is_array($v)):
				$qfile->write("'$k' => array(");
				foreach ($v as $k1=>$v1) $qfile->write("'$v1',");
				$qfile->write("), \n");
			else:
				$qfile->write("'$k' => '$v', \n");
			endif;
		}
		$plusCheese = new plusCheese($godo['sno']);
		$qfile->write("'key' => '".$plusCheese->getRelayKey()."', \n");
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();
		@chmod("../../conf/config.plusCheeseCfg.php",0707);
		msg("설정이 저장 되었습니다",-1);
		break;
}?>