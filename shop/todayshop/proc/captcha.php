<?

header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");

ini_set("session.save_path",$_SERVER['DOCUMENT_ROOT']."/session_tmp");

session_start();
@include dirname(__FILE__) . "/../../lib/captcha.class.php";
$captcha = new Captcha('output');
?>