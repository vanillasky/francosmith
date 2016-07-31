<?php
include "../lib.php";
$godotax = Core::loader('godotax');

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

$arTaxsno = (array)$_POST['chk'];

foreach($arTaxsno as $eachSno) {
	$query = $db->_query_print('delete from gd_tax where sno=[s]',$eachSno);
	$db->query($query);
}
?>
<script>
alert("삭제되었습니다.");
parent.location.href=parent.location.href;
</script>
