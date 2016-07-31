<?
include "../_header.php";
include "../../lib/graph.class.php";
include "../../lib/parsexml.class.php";

$file = "http://".$cfg[shopUrl]."/cband-status-me?xml";
$buffer = file_get_contents($file);
$xml = new XMLParser(); 
$xml->parse($buffer); 
$data = $xml->parseOut(); 
debug($data);
?>