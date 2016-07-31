<?php

/* Return Review Article Data Function */
// $limit = 상품후기 노출 수
// $new = true 일 때 상품의 외부호스팅 이미지 적용을 위한 값 - 스킨 쪽 이미지 경로 수정 필요
function dataReview($limit=5,$new=0)
{
	global $db;

	$query = "
	select *, a.regdt, a.name, a.point from
		".GD_GOODS_REVIEW." a
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
		if($new){
			if(preg_match('/^http(s)?:\/\//',$data[img_s])){
			} else if ($data[img_s]) {
				$data[img_s] = '../data/goods/'.$data[img_s];
			}
		}
		$loop[] = $data;
	}
	return $loop;
}

?>