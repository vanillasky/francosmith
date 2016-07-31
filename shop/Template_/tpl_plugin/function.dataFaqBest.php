<?php

/* Return FAQ Best Data Function */

function dataFaqBest( $limit=0, $Strlen=0 ){

	global $db;

	$faq = array();

	$query = "select sno, question from ".GD_FAQ." where best='Y' order by bestsort asc, regdt desc";
	if ( $limit > 0 ) $query .= " limit " . $limit;
	$res = $db->query($query);
	while ( $data = $db->fetch( $res, 1 ) ){
		if ( $Strlen > 0 ) $data[question] = strcut( $data[question],$Strlen );
		$faq[] = $data;
	}

	return $faq;
}
?>