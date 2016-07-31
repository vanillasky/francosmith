<?
include "../lib/library.php";
@include_once "../lib/sms.class.php";
$smscl = new Sms();
$smscl -> update();

echo("OK");
?>