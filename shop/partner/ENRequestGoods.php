<?
include "../lib/library.php";
include "../lib/selly.class.php";
include "../lib/parsexmlstruc.class.php";
include "../conf/config.php";

$xml = new StrucXMLParser();
$xml->parse(stripslashes($_POST['xml_data']));
$reqXml = $xml->parseOut();
$reqData = array();

$stRec = new sellyRec();
$stRec->makeXml($reqXml);
?>