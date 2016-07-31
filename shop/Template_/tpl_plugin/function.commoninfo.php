<?php

/* Return FaQ Article Data Function */

function commoninfo()
{
	global $db;

	$commoninfo = array();

	$query = "select * from ".GD_COMMON_INFO." where open order by idx";
	$res = $db->query($query);
	
	while ( $data = $db->fetch( $res, 1 ) ) $commoninfo[] = $data;

	return $commoninfo;

}

/*
function commoninfo()
{
	global $db;

	$commoninfo = array();

	$query = "select * from ".GD_COMMON_INFO." where open order by idx";
	$res = $db->query($query);
	
	$html = "";
	$html .= "<div style=\"margin:15px;\"></div>\n";
	while ( $data = $db->fetch( $res, 1 ) ){
		$html .= "<div class=\"line\" style=\"height:25px; background:#e0e0e0;color:#515151; line-height:25px; padding-left:10px; font-weight:bold;\">".$data[title]."</div>\n";
		$html .= "<div style=\"padding:10px; background:#ffffff;\">".$data[info]."</div>\n";
		$html .= "<div style=\"margin:5px;\"></div>\n";
	}
	$html .= "<div style=\"margin:15px;\"></div>\n";

	return $html;

}
*/

?>