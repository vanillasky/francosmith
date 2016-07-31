<?
/*********************************************************
* ���ϸ�     :  pMemberQnaItem.php
* ���α׷��� :	pad 1:1���� ������ API
* �ۼ���     :  dn
* ������     :  2011.11.01
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

$sno = $_POST['sno'];

$qna_query = $db->_query_print('SELECT mq.*, m.m_id FROM '.GD_MEMBER_QNA.' mq LEFT JOIN '.GD_MEMBER.' m ON mq.m_no=m.m_no WHERE mq.sno=[i]', $sno);
$res_qna = $db->_select($qna_query);
$row_qna = $res_qna[0];

$row_qna['contents'] = strip_tags($row_qna['contents'], "<(/)?([a-zA-Z]*)(\\s[a-zA-Z]*=[^>]*)?(\\s)*(/)?>");
$qna = $row_qna;

if($row_qna['parent'] != $row_qna['sno']) {
	$r_qna_query = $db->_query_print('SELECT mq.*, m.m_id FROM '.GD_MEMBER_QNA.' mq LEFT JOIN '.GD_MEMBER.' m ON mq.m_no=m.m_no WHERE mq.sno=[i]', $row_qna['parent']);
	$res_r_qna = $db->_select($r_qna_query);
	$row_r_qna = $res_r_qna[0];

	
	$row_r_qna['contents'] = strip_tags($row_r_qna['contents'], "<(/)?([a-zA-Z]*)(\\s[a-zA-Z]*=[^>]*)?(\\s)*(/)?>");

	$reply = $row_qna;
	$qna = $row_r_qna;
}

$res_data['qna'] = $qna;
$res_data['reply'] = $reply;

echo ($json->encode($res_data));
