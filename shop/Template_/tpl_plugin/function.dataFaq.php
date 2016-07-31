<?php

/* Return FaQ Article Data Function */

function dataFaq($limit=5)
{
	global $db;

	$query = "
	select * from 
		".GD_FAQ." 
	order by sno desc
	limit $limit
	";
	$res = $db->query($query);
	while ($data=$db->fetch($res)){
		$loop[] = $data;
	}
	return $loop;
}

?>