<?
	include "../lib.php";
	include "../../lib/qfile.class.php";
	@include "../../conf/egg.usafe.php";
	/*
		�����
		1 : ����
		2 : ���� ������ ����
		3 : ���� ������ ����
		4 : GET����� ���� ����
	*/

	$qfile = new qfile();

	if(!file_exists("../../conf/egg.usafe.php")) exit("2");
	if(!isset($egg)) exit("3");
	if(!count($_GET)) exit("4");

	$qfile->open("../../conf/egg.usafe.php");
	$qfile->write("<? \n");
	$qfile->write("\$egg = array( \n");

	foreach($egg as $k => $v) {
		$qfile->write("'$k' => '".((!$_GET[$k]) ? $egg[$k] : $_GET[$k] )."', \n");
	}

	$qfile->write(") \n;");
	$qfile->write("?>");
	$qfile->close();
	@chmod("../../conf/egg.usafe.php",0707);

	exit("1");
?>