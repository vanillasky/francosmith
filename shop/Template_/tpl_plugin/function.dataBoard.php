<?php

/* Return Board Data Function */

function dataBoard( $limit=0 ){

	global $db;

	$board = array();

	$query = "select id from ".GD_BOARD." order by sno";
	if ( $limit > 0 ) $query .= " limit " . $limit;
	$res = $db->query($query);
	while ( $data = $db->fetch( $res, 1 ) ){
		@include dirname(__file__) . "/../../conf/bd_$data[id].php";
		if ($bdName) $board[] = array( 'id' => $data[id], 'name' => $bdName );
	}

	return $board;
}
?>