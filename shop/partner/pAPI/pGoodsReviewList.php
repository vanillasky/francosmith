<?
/*********************************************************
* 파일명     :  pGoodsReviewList.php
* 프로그램명 :	상품후기 리스트 API
* 작성자     :  dn
* 생성일     :  2011.10.31
**********************************************************/
include "../../lib/library.php";
include "../../conf/config.php";
require_once "../../lib/pAPI.class.php";
require_once "../../lib/json.class.php";

$pAPI = new pAPI();
$json = new Services_JSON(16);

### 인증키 Check (실제로는 아이디와 비번 임) 시작 ###
if(!$_POST['authentic']) {
	$res_data['code'] = '302';
	$res_data['msg'] = '인증키가 없습니다.';
	echo ($json->encode($res_data));
	exit;
}

if(!$pAPI->keyCheck($_POST['authentic'])) {
	$res_data['code'] = '302';
	$res_data['msg'] = '인증키가 맞지 않습니다.';
	echo ($json->encode($res_data));
	exit;
}
unset($_POST['authentic']);
### 인증키 Check 끝 ###

$search = array();

if(!empty($_POST)) {
	foreach($_POST as $key => $val) {
		$tmp_key = str_replace('search_', '', $key);

		if(strstr($key, 'arr_')) {
			$search[str_replace('arr_', '', $tmp_key)] = explode('|', $val);
		}
		else  {
			$search[$tmp_key] = $val;
		}
	}
}

$arr_where = Array();
$arr_where[] = $db->_query_print('gr.parent = gr.sno');

$arr_category = array_notnull($search['category']);
$category = $arr_category[count($arr_category)-1];

if($category) {
	$arr_where[] = "gl.category like '$category%'";
}

if($search['skey'] && $search['sword']) {
	if($search['skey'] == 'goodsnm' || $search['skey'] == 'all') { 
		$tmp = Array();
		
		$goods_query = $db->_query_print('SELECT goodsno FROM '.GD_GOODS.' WHERE goodsnm LIKE "%'.$search['sword'].'%"');
		$res_goods = $db->_select($goods_query);

		if(!empty($res_goods) && is_array($res_goods)) {
			foreach ($res_goods as $row_goods) {
				$tmp[] = $row_goods['goodsno'];
			}
		}

		if(count($tmp)) $goodnm_where = 'gr.goodsno IN('.implode( ',', $tmp).')';
		else $goodnm_where = "0";
	}

	if ($search['skey']== 'all') {
		$tmp_where[] = $db->_query_print('gr.subject like "%'.$search['sword'].'%"');
		$tmp_where[] = $db->_query_print('gr.contents like "%'.$search['sword'].'%"');
		$tmp_where[] = $db->_query_print('m.m_id like "%'.$search['sword'].'%" OR gr.name like "%'.$search['sword'].'%"');
		$tmp_where[] = $goodnm_where;

		$arr_where[] = '('.implode(' OR ', $tmp_where).')';
	}
	else {
		switch($search['skey']){
			case 'goodsnm' : $arr_where[] = $goodnm_where; break;
			case 'subject' : $arr_where[] = $db->_query_print('gr.subject like "%'.$search['sword'].'%"'); break;
			case 'contents' : $arr_where[] = $db->_query_print('gr.contents like "%'.$search['sword'].'%"'); break;
			case 'm_id' : $arr_where[] = $db->_query_print('m.m_id like "%'.$search['sword'].'%" OR gr.name like "%'.$search['sword'].'%"'); break;
		}
	}
}


if ($search['s_date'] && $search['e_date']) $arr_where[] = $db->_query_print('gr.regdt >=[s] AND gr.regdt <=[s]', $search['s_date'].' 00:00:00', $search['e_date'].' 23:59:59');

$table = '
'.GD_GOODS_REVIEW.' gr
LEFT JOIN '.GD_MEMBER.' m ON gr.m_no=m.m_no 
LEFT JOIN '.GD_GOODS.' g ON gr.goodsno = g.goodsno';

if($category) {
	$table .= '
	LEFT JOIN '.GD_GOODS_LINK.' gl ON gr.goodsno=gl.goodsno';
}

$where = implode(' AND ', $arr_where);

$review_query = $db->_query_print('
	SELECT DISTINCT
		gr.sno as sno,
		gr.parent as parent,
		gr.goodsno as goodsno,
		gr.subject as subject,
		gr.regdt as regdt,
		gr.name as name,
		gr.point as point,
		gr.emoney as emoney,
		gr.attach as attach,
		g.goodsnm as goodsnm,
		g.totstock as totstock,
		g.open as open,
		g.img_s as img_s,
		g.img_l as img_l,
		m.m_id as m_id
	FROM '.$table.'
	WHERE '.$where.'
	ORDER BY regdt DESC'
	);

$res_review = $db->_select_page($search['list'], $search['page'], $review_query);

$review_data = $res_review['record'];

if(!empty($review_data) && is_array($review_data)) {
	foreach($review_data as $row_review) {
		
		if($row_review['img_s']) {
			$row_review['img_url'] = "http://".$_SERVER['HTTP_HOST'].$GLOBALS[cfg][rootDir]."/data/goods/".$row_review['img_s'];
		}
		else if($row_qna['img_l']) {
			$row_review['img_url'] = "http://".$_SERVER['HTTP_HOST'].$GLOBALS[cfg][rootDir]."/data/goods/".$row_review['img_l'];
		}

		$reply_query = $db->_query_print('SELECT gr.sno, gr.parent, gr.goodsno, gr.subject, gr.regdt, gr.name, gr.point, gr.emoney, gr.attach, m.m_id FROM '.GD_GOODS_REVIEW.' gr LEFT JOIN '.GD_MEMBER.' m ON gr.m_no=m.m_no WHERE gr.parent=[i] AND gr.parent != sno', $row_review['sno']);
		$res_reply = $db->_select($reply_query);
		$row_review['reply'] = $res_reply;

		$res_data[] = $row_review;
	}
}

echo ($json->encode($res_data));
?>