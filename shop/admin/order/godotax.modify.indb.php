<?php
include "../lib.php";
$godotax = Core::loader('godotax');

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

$arTaxsno = (array)$_POST['chk'];

foreach($arTaxsno as $eachSno) {
	$eachData = $_POST['modify'][$eachSno];
	$arUpdate = array(
		'busino'=>$eachData['busino'],
		'company'=>$eachData['company'],
		'name'=>$eachData['name'],
		'service'=>$eachData['service'],
		'item'=>$eachData['item'],
		'address'=>$eachData['address'],
		'price'=>$eachData['price'],
		'supply'=>$eachData['supply'],
		'surtax'=>$eachData['surtax'],
		'issuedate'=>$eachData['issuedate'],
	);
	$query = $db->_query_print('update gd_tax set [cv] where sno=[s]',$arUpdate,$eachSno);

	$db->query($query);
}
?>
<script>
alert("수정되었습니다");
parent.location.href=parent.location.href;
</script>
