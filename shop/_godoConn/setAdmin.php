<?
include "../lib/library.php";

$shopid = base64_decode($_GET['shopid']);
$shoppw = base64_decode($_GET['shoppw']);

if($shopid && $shoppw){
	$query = "select count(*) from ".GD_MEMBER." where m_no=1";
	list($cnt) = $db->fetch($query);
	if(!$cnt){
		$query = "INSERT INTO `".GD_MEMBER."` (`m_no`, `m_id`, `level`, `name`, `password`, `status`, `sex`, `birth_year`, `birth`, `calendar`, `email`, `zipcode`, `address`, `address_sub`, `phone`, `mobile`, `fax`, `company`, `service`, `item`, `busino`, `emoney`, `mailling`, `sms`, `marriyn`, `marridate`, `job`, `interest`, `regdt`, `last_login`, `cnt_login`, `cnt_sale`, `sum_sale`, `memo`, `recommid`, `ex1`, `ex2`, `ex3`, `ex4`, `ex5`, `ex6`) VALUES (1, '".$shopid."', 100, '°ü¸®ÀÚ', password('".$shoppw."'), 1, 'm', '', '0000', 's', '', '', '', '', '', '', '', '', '', '', '0000000000', 0,  'y', 'y', 'n', '00000000', '', '', '', '','' , 0, 0, '', '', '', '', '', '', '', '')";
		$res = $db->query($query);
	}
}
if($res){
	echo "true";
}else{
	echo "false";
}
?>
