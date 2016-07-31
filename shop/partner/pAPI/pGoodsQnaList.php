<?
/*********************************************************
* 파일명     :  pGoodsQnaList.php
* 프로그램명 :	상품문의 리스트 API
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
$arr_where[] = $db->_query_print('gq.parent = gq.sno');

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

		if(count($tmp)) $goodnm_where = 'gq.goodsno IN('.implode( ',', $tmp).')';
		else $goodnm_where = "0";
	}

	if ($search['skey']== 'all') {
		$tmp_where[] = $db->_query_print('gq.subject like "%'.$search['sword'].'%"');
		$tmp_where[] = $db->_query_print('gq.contents like "%'.$search['sword'].'%"');
		$tmp_where[] = $db->_query_print('m.m_id like "%'.$search['sword'].'%" OR gq.name like "%'.$search['sword'].'%"');
		$tmp_where[] = $goodnm_where;

		$arr_where[] = '('.implode(' OR ', $tmp_where).')';
	}
	else {
		switch($search['skey']){
			case 'goodsnm' : $arr_where[] = $goodnm_where; break;
			case 'subject' : $arr_where[] = $db->_query_print('gq.subject like "%'.$search['sword'].'%"'); break;
			case 'contents' : $arr_where[] = $db->_query_print('gq.contents like "%'.$search['sword'].'%"'); break;
			case 'm_id' : $arr_where[] = $db->_query_print('m.m_id like "%'.$search['sword'].'%" OR gq.name like "%'.$search['sword'].'%"'); break;
		}
	}
}


if ($search['s_date'] && $search['e_date']) $arr_where[] = $db->_query_print('gq.regdt >=[s] AND gq.regdt <=[s]', $search['s_date'].' 00:00:00', $search['e_date'].' 23:59:59');

$table = '
'.GD_GOODS_QNA.' gq
LEFT JOIN '.GD_MEMBER.' m ON gq.m_no=m.m_no 
LEFT JOIN '.GD_GOODS.' g ON gq.goodsno = g.goodsno';

if($category) {
	$table .= '
	LEFT JOIN '.GD_GOODS_LINK.' gl ON gq.goodsno=gl.goodsno';
}

$where = implode(' AND ', $arr_where);

$qna_query = $db->_query_print('
	SELECT DISTINCT
		gq.sno as sno,
		gq.parent as parent,
		gq.goodsno as goodsno,
		gq.subject as subject,
		gq.regdt as regdt,
		gq.name as name,
		gq.secret as secret,
		gq.notice as notice,
		g.goodsnm as goodsnm,
		g.totstock as totstock,
		g.open as open,
		g.img_s as img_s,
		g.img_l as img_l,
		m.m_id as m_id
	FROM '.$table.'
	WHERE '.$where.'
	ORDER BY notice DESC, regdt DESC'
	);

$res_qna = $db->_select_page($search['list'], $search['page'], $qna_query);

$qna_data = $res_qna['record'];

if(!empty($qna_data) && is_array($qna_data)) {
	foreach($qna_data as $row_qna) {
		
		$row_qna['img_url'] = '';
		
		if($row_qna['img_s']) {
			$row_qna['img_url'] = "http://".$_SERVER['HTTP_HOST'].$GLOBALS[cfg][rootDir]."/data/goods/".$row_qna['img_s'];
		}
		else if($row_qna['img_l']) {
			$row_qna['img_url'] = "http://".$_SERVER['HTTP_HOST'].$GLOBALS[cfg][rootDir]."/data/goods/".$row_qna['img_l'];
		}

		$reply_query = $db->_query_print('SELECT gq.sno, gq.parent, gq.goodsno, gq.subject, gq.regdt, gq.name, gq.secret, gq.notice, m.m_id FROM '.GD_GOODS_QNA.' gq LEFT JOIN '.GD_MEMBER.' m ON gq.m_no=m.m_no WHERE gq.parent=[i] AND gq.parent != sno', $row_qna['sno']);
		$res_reply = $db->_select($reply_query);
		$row_qna['reply'] = $res_reply;
		
		$res_data[] = $row_qna;
	}
}

echo ($json->encode($res_data));
?>