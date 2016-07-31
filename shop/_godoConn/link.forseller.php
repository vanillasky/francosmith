<?
include("../lib/library.php");

$mode	= ($_POST['mode'])		? trim($_POST['mode'])		: "";
$token	= ($_POST['token'])		? trim($_POST['token'])		: "";

if($mode == "test") {
	echo "DONE";
	exit();
}

if(strlen($token) == 32) {
	list($oldToken) = $db->fetch("SELECT value FROM gd_env WHERE category = 'forseller' AND name='token'");
	if(!$oldToken) $db->query("INSERT INTO gd_env SET category = 'forseller', name='token', value='$token'");
	else {
		$db->query("INSERT INTO gd_env SET category = 'forseller', name='oldToken', value='$oldToken'");
		$db->query("UPDATE gd_env SET value='$token' WHERE category = 'forseller' AND name='token'");
	}

	list($myToken) = $db->fetch("SELECT value FROM gd_env WHERE category = 'forseller' AND name='token'");
	if($myToken) echo "DONE";
	else echo "ERRO";
}
else {
	echo "ERRO";
}
?>