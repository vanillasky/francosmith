<?php
include "../lib.php";

if ($_POST['action'] == 'checkUniqueValue') {

	$_POST['value'] = iconv('utf-8','euc-kr',$_POST['value']);

	$query = sprintf("select count(*) from gd_goods where %s = '%s'", $_POST['column'], $_POST['value']);

	if ($_POST['goodsno']) {
		$query .= ' and goodsno != '.$_POST['goodsno'];
	}

	list($cnt) = $db->fetch($query);

	if ($cnt > 0) {
		echo gd_json_encode(false);
	}
	else {
		echo gd_json_encode(true);
	}
}
