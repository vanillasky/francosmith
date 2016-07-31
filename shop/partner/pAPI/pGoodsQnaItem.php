<?
/*********************************************************
* ���ϸ�     :  pGoodsQnaItem.php
* ���α׷��� :	pad ��ǰ���� ������ API
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
	echo ($pAPI->returnData($res_data));
	exit;
}

if(!$pAPI->keyCheck($_POST['authentic'])) {
	$res_data['code'] = '302';
	$res_data['msg'] = '����Ű�� ���� �ʽ��ϴ�.';
	echo ($pAPI->returnData($res_data));
	exit;
}
unset($_POST['authentic']);
### ����Ű Check �� ###

$sno = $_POST['sno'];

$qna_query = $db->_query_print('SELECT gq.*, g.goodsnm, g.goodsno, g.img_s, g.img_l, m.m_id FROM '.GD_GOODS_QNA.' gq LEFT JOIN '.GD_GOODS.' g ON gq.goodsno=g.goodsno LEFT JOIN '.GD_MEMBER.' m ON gq.m_no=m.m_no WHERE gq.sno=[i]', $sno);
$res_qna = $db->_select($qna_query);
$row_qna = $res_qna[0];

$row_qna['contents'] = strip_tags($row_qna['contents'], "<(/)?([a-zA-Z]*)(\\s[a-zA-Z]*=[^>]*)?(\\s)*(/)?>");

$row_qna['img_url'] = '';
if($row_qna['img_s']) {
	$row_qna['img_url'] = "http://".$_SERVER['HTTP_HOST'].$GLOBALS[cfg][rootDir]."/data/goods/".$row_qna['img_s'];
}
else if($row_qna['img_l']) {
	$row_qna['img_url'] = "http://".$_SERVER['HTTP_HOST'].$GLOBALS[cfg][rootDir]."/data/goods/".$row_qna['img_l'];
}

$qna = $row_qna;

if($row_qna['parent'] != $row_qna['sno']) {
	$r_qna_query = $db->_query_print('SELECT gq.*, g.goodsnm, g.goodsno, g.img_s, g.img_l, m.m_id FROM '.GD_GOODS_QNA.' gq LEFT JOIN '.GD_GOODS.' g ON gq.goodsno=g.goodsno LEFT JOIN '.GD_MEMBER.' m ON gq.m_no=m.m_no WHERE gq.sno=[i]', $row_qna['parent']);
	$res_r_qna = $db->_select($r_qna_query);
	$row_r_qna = $res_r_qna[0];
	
	$row_r_qna['img_url'] = '';
	if($row_r_qna['img_s']) {
		$row_r_qna['img_url'] = "http://".$_SERVER['HTTP_HOST'].$GLOBALS[cfg][rootDir]."/data/goods/".$row_r_qna['img_l'];
	}
	else if($row_r_qna['img_l']) {
		$row_r_qna['img_url'] = "http://".$_SERVER['HTTP_HOST'].$GLOBALS[cfg][rootDir]."/data/goods/".$row_r_qna['img_s'];
	}
	
	$row_r_qna['contents'] = strip_tags($row_r_qna['contents'], "<(/)?([a-zA-Z]*)(\\s[a-zA-Z]*=[^>]*)?(\\s)*(/)?>");

	$reply = $row_qna;
	$qna = $row_r_qna;
}

$res_data['qna'] = $qna;
$res_data['reply'] = $reply;

echo ($json->encode($res_data));
