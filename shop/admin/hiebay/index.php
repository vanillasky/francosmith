<?
$location = "하이! eBay > 서비스 안내";
include "../_header.php";
$ignoreToken = true;
include_once "./checker.php";

if(!$fsConfig['token']) go("./info.php");
else go("./loglist.php");
?>
