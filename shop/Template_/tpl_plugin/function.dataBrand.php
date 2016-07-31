<?php

/* Return Brand Data Function */

function dataBrand( $limit=0 ){

	global $db;

	$brand = array();

	$query = "select sno, brandnm from ".GD_GOODS_BRAND." order by sort";
	if ( $limit > 0 ) $query .= " limit " . $limit;
	$res = $db->query($query);
	while ( $data = $db->fetch( $res, 1 ) ){
		$brand[] = array( 'brand' => $data[sno], 'name' => $data[brandnm] );
	}

	return $brand;
}
?>