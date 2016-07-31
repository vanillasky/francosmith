<?
include "../lib.php";
include "../../lib/lib.enc.php";
include "../../conf/config.php";

$baseurl=$ftpuser=$baseurl="";

if($_GET['mode'] != 'imagehosting'){
$tmp ='self';
if(preg_match('/rental/',$godo['ecCode'])):
	$baseurl=$cfg['rootDir']."/data/";
	$tmp ='rental';
endif;
setcookie("awfi",'',time()+2592000,'/');
if(!$_COOKIE['awfi'] && !preg_match('/webhost_outside/',$godo['webCode'])):
	$str = godoConnEncode(serialize(array($godo[sno],$tmp)));
	$url = 'http://gongji.godo.co.kr/userinterface/webftp/get_awfi.php?shopinfo='.$str;
	$ftpuser = readurl($url);
elseif($_COOKIE['awfi']):
	$ftpuser = $_COOKIE['awfi'];
endif;

if($ftpuser!="err"&&$ftpuser) setcookie("awfi",$ftpuser,time()+2592000,'/');
else $ftpuser='';

$ftpaddress = $_SERVER['HTTP_HOST'];
}
?>
<body leftmargin="0" topmargin="0" marginwidth="0" marginheight="0">
<object id="genshortcut" width="743" height="608" classid="CLSID:BFAB1224-7252-4DBB-A53C-8FE81E9EBDE1" CODEBASE="http://webftp.godo.co.kr/WebFTPClient01.cab#version=1,0,0,7">
<PARAM NAME="baseurl" VALUE="<?php echo $baseurl;?>">
<PARAM NAME="ftpuser" VALUE="<?php echo $ftpuser;?>">
<PARAM NAME="ftpaddress" VALUE="<?php echo $ftpaddress;?>">
</object>
</body>
</html>