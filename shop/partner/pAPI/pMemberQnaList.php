<?
/*********************************************************
* ���ϸ�     :  pMemberQnaList.php
* ���α׷��� :	1:1���� ����Ʈ API
* �ۼ���     :  dn
* ������     :  2011.10.31
**********************************************************/
include "../../lib/library.php";
include "../../conf/config.php";
require_once "../../lib/pAPI.class.php";
require_once "../../lib/json.class.php";

$pAPI = new pAPI();
$json = new Services_JSON(16);

### ����Ű Check (�����δ� ���̵�� ��� ��) ���� ###
if(!$_POST['authentic']) {
	$res_data['code'] = '302';
	$res_data['msg'] = '����Ű�� �����ϴ�.';
	echo ($json->encode($res_data));
	exit;
}

if(!$pAPI->keyCheck($_POST['authentic'])) {
	$res_data['code'] = '302';
	$res_data['msg'] = '����Ű�� ���� �ʽ��ϴ�.';
	echo ($json->encode($res_data));
	exit;
}
unset($_POST['authentic']);
### ����Ű Check �� ###

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
$arr_where[] = $db->_query_print('mq.parent = mq.sno');

if($search['skey'] && $search['sword']) {
	
	if ($search['skey']== 'all') {
		$tmp_where[] = $db->_query_print('mq.subject like "%'.$search['sword'].'%"');
		$tmp_where[] = $db->_query_print('mq.contents like "%'.$search['sword'].'%"');
		$tmp_where[] = $db->_query_print('m.m_id like "%'.$search['sword'].'%" OR mq.name like "%'.$search['sword'].'%"');
			$arr_where[] = '('.implode(' OR ', $tmp_where).')';
	}
	else {
		switch($search['skey']){
			case 'subject' : $arr_where[] = $db->_query_print('mq.subject like "%'.$search['sword'].'%"'); break;
			case 'contents' : $arr_where[] = $db->_query_print('mq.contents like "%'.$search['sword'].'%"'); break;
			case 'm_id' : $arr_where[] = $db->_query_print('m.m_id like "%'.$search['sword'].'%" OR mq.name like "%'.$search['sword'].'%"'); break;
		}
	}
}


if ($search['s_date'] && $search['e_date']) $arr_where[] = $db->_query_print('mq.regdt >=[s] AND mq.regdt <=[s]', $search['s_date'].' 00:00:00', $search['e_date'].' 23:59:59');

$table = '
'.GD_MEMBER_QNA.' mq
LEFT JOIN '.GD_MEMBER.' m ON mq.m_no=m.m_no';

$where = implode(' AND ', $arr_where);

$qna_query = $db->_query_print('
	SELECT DISTINCT
		mq.sno as sno,
		mq.parent as parent,
		mq.subject as subject,
		mq.itemcd as itemcd,
		mq.regdt as regdt,
		mq.name as name,
		m.m_id as m_id
	FROM '.$table.'
	WHERE '.$where.'
	ORDER BY regdt DESC'
	);

$res_qna = $db->_select_page($search['list'], $search['page'], $qna_query);

$qna_data = $res_qna['record'];

$arr_question = codeitem('question');
if(!empty($qna_data) && is_array($qna_data)) {
	foreach($qna_data as $row_qna) {
		
		$row_qna['str_itemcd'] = $arr_question[$row_qna['itemcd']];

		$reply_query = $db->_query_print('SELECT mq.sno, mq.parent, mq.subject, mq.itemcd, mq.regdt, mq.name, m.m_id FROM '.GD_MEMBER_QNA.' mq LEFT JOIN '.GD_MEMBER.' m ON mq.m_no=m.m_no WHERE mq.parent=[i] AND mq.parent != sno', $row_qna['sno']);
		$res_reply = $db->_select($reply_query);
		$row_qna['reply'] = $res_reply;
		
		$res_data[] = $row_qna;
	}
}

echo ($json->encode($res_data));
?>