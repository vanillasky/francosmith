<?php

/* Return QnA Article Data Function */

function dataQna($limit=5)
{
	global $db;

	$query = "
	select *, a.regdt as regdt , a.name from
		".GD_GOODS_QNA." a
		left join ".GD_MEMBER." b on a.m_no=b.m_no
		left join ".GD_GOODS." c on a.goodsno=c.goodsno
	where
		sno = parent
	order by sno desc
	limit $limit
	";
	$res = $db->query($query);
	while ($data=$db->fetch($res)){
		if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
			$data = validation::xssCleanArray($data, array(
				validation::DEFAULT_KEY => 'text',
				'contents' => array('html', 'ent_noquotes'),
				'subject' =>  array('html', 'ent_noquotes'),
			));
		}
		$loop[] = $data;
	}
	return $loop;
}

?>