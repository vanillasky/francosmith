<?php

/* Return Bank Data Function */

function dataBank( $limit=0 ){

	global $db;

	$bank = array();

	$query = "select bank, account, name from ".GD_LIST_BANK." where useyn='y'";
	if ( $limit > 0 ) $query .= " limit " . $limit;
	$res = $db->query($query);
	while ( $data = $db->fetch( $res, 1 ) ) $bank[] = $data;

	return $bank;
}
?>
