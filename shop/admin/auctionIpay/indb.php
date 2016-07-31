<?php
@require "../lib.php";
@require "../../lib/lib.enc.php";
@require "../../lib/load.class.php";
@require "../../lib/qfile.class.php";
@require "../../lib/upload.lib.php";
@require "../../conf/config.php";

$qfile = new qfile;

unset($_POST['x'],$_POST['y'],$_POST['exceptions'],$_POST['search_exceptions'],$_POST['cate']);
foreach($_POST as $k=>$v)
{
	if(is_array($v)):
		foreach ($v as $k1=>$v1) $auctionIpayCfg[$k][] = addslashes($v1);
	else:
		$auctionIpayCfg[$k] = addslashes($v);
	endif;
}

$qfile->open("../../conf/auctionIpay.cfg.php");
$qfile->write("<? \n");
$qfile->write("\$auctionIpayCfg = array( \n");
foreach ($auctionIpayCfg as $k=>$v)
{
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
@chmod("../../conf/auctionIpay.cfg.php",0707);

$tmp = readurl('http://gongji.godo.co.kr/userinterface/auctionIpay/banWords.php');
$out = godoConnDecode($tmp);
$tmp = explode(',',$out);

$qfile->open("../../conf/auctionIpay.banWords.php");
$qfile->write("<?\n");
$qfile->write("\$checkoutBan = array(\n");
if (empty($tmp) === false && is_array($tmp)) {
	foreach($tmp as $v)
	{
		$qfile->write("'".$v."',");
	}
}
$qfile->write(");\n");
$qfile->write("?>");
$qfile->close();
@chmod("../../conf/auctionIpay.banWords.php",0707);

msg('설정이 저장되었습니다.');
?>