<?php

/* Return Category Data Function */

function dataCategorySub( $category ){

	global $db, $sess;

	if ($category) $where = "category like '$category%' and";
	$length = strlen($category) + 3;

	### 1/2차 카테고리 정보
	if ($GLOBALS[ici_admin] === false) $where .= " hidden=0 and ";
	$query = "
	select category,catnm,sort,level,level_auth from
		".GD_CATEGORY."
	where $where
		length(category)=$length
	order by sort
	";
	$res = $db->query($query);
	$i=0;
	while ( $data = $db->fetch( $res, 1 ) ){
		$member_auth = false;
		if($data['level']){//권한 설정되어 있는지 체크
			switch($data['level_auth']){//권한체크
				case '1': //모두숨김
					if( (!$sess['level'] ? 0 : $sess['level']) >= $data['level'] ) $member_auth = true;
					break;
				default: $member_auth = true; break;
			}
		}
		else $member_auth = true;

		if($member_auth){
			$cate[$i][catnm] = $data[catnm];
			$cate[$i][category] = $data[category];
			$cate[$i][sort] = $data[sort];

			$i++;
		}
	}

	return $cate;
}
?>