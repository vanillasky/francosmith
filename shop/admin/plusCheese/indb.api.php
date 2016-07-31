<?
$_url = "http://pluscheese.godo.co.kr/listen.shop.php?mode=key&shopsno=".$godo['sno'];
$fp = fopen($_url, 'r');
if ($fp) {
	$plusCheeseResult = fgets($fp, 4096);
}
?>