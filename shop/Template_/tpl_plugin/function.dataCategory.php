<?php

/* Return Category Data Function */

function dataCategory( $subcategory=0, $step=1, $mobile=0 ){

	global $db, $sess;
	$length = ( $subcategory ) ? $step * 3 + 3 : $step * 3;

	if ($step>1){
		$category = substr($_GET[category],0,($step-1)*3);
		if ($category) $where = "category like '$category%' and";
		else {
			$step = 1;
			$length = ( $subcategory ) ? $step * 3 + 3 : $step * 3;
		}
	}

	### 1/2차 카테고리 정보
	if ($GLOBALS[ici_admin] === false) {
		if($mobile) $where .= " hidden_mobile=0 and ";
		else $where .= " hidden=0 and ";
	}
	$query = "
	select category,catnm,sort,useimg,level,level_auth,auth_step from
		".GD_CATEGORY."
	where $where
		length(category)<=$length
	order by category
	";
	$res = $db->query($query);
	while ( $data = $db->fetch( $res, 1 ) ){
		$member_auth = false;
		//카테고리 권한 설정
		if($data['level']){
			switch($data['level_auth']){
				case '1':
					if( (!$sess['level'] ? 0 : $sess['level']) >= $data['level'] ) $member_auth = true;
					break;
				default: $member_auth = true; break;
			}
		}
		else $member_auth = true;

		if( $member_auth ){
		
			### 분류이미지
			if(!$mobile){
				if($data[useimg]) $tmp = getCategoryImg($data[category]);
				if($data[useimg] == '1' && $tmp[$data[category]][0]){
					$data[catnm] = "<img src='../data/category/".$tmp[$data[category]][0]."'>";
				}else if($data[useimg] == '2' && $tmp[$data[category]][0] && $tmp[$data[category]][1]){
					$data[catnm] = "<img src='../data/category/".$tmp[$data[category]][0]."' onmouseout=\"this.src='../data/category/".$tmp[$data[category]][0]."';\" onmouseover=\"this.src='../data/category/".$tmp[$data[category]][1]."';\">";
				}
			}

			if ( strlen( $data[category] ) == $step * 3 ){
				$cate[$data[sort]][] = $data;
				$spot = $data[category];
			} else if ( strlen( $data[category] ) == $step * 3 + 3 ){
				if($spot == substr( $data[category],0,($step * 3) )){
					$sub[$spot][$data[sort]][] = $data;
				}
			}
		}
	}

	### 배열 순서 재정의
	$cate = resort($cate);
	if ( $sub ) foreach ( $sub as $k => $v ) $sub[$k] = resort($v);

	### cate[][sub] 처리
	if ($cate) foreach ( $cate as $k => $v ) $cate[$k]['sub'] = $sub[ $v[category] ];

	return $cate;
}
?>