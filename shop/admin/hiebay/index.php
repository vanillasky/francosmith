<?
$location = "����! eBay > ���� �ȳ�";
include "../_header.php";
$ignoreToken = true;
include_once "./checker.php";

if(!$fsConfig['token']) go("./info.php");
else go("./loglist.php");
?>
