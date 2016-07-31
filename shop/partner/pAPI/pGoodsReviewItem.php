<?
/*********************************************************
* 파일명     :  pGoodsReviewItem.php
* 프로그램명 :	pad 상품후기 아이템 API
* 작성자     :  dn
* 생성일     :  2011.11.01
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

$sno = $_POST['sno'];

$review_query = $db->_query_print('SELECT gr.*, if(gr.sno = gr.parent and m.m_no > 0 , "Y" , "N") as apply, if(gr.sno = gr.parent and m.m_no > 0 and gr.emoney = 0 , "Y" , "N") as apply2, g.goodsnm, g.goodsno, g.img_s, g.img_l, m.m_id, m.name as m_name FROM '.GD_GOODS_REVIEW.' gr LEFT JOIN '.GD_GOODS.' g ON gr.goodsno=g.goodsno LEFT JOIN '.GD_MEMBER.' m ON gr.m_no=m.m_no WHERE gr.sno=[i]', $sno);
$res_review = $db->_select($review_query);
$row_review = $res_review[0];

$row_review['contents'] = strip_tags($row_review['contents'], "<(/)?([a-zA-Z]*)(\\s[a-zA-Z]*=[^>]*)?(\\s)*(/)?>");
$row_review['img_url'] = '';

if($row_review['attach']) {
	$row_review['attach_url'] = 'http://'.$_SERVER['HTTP_HOST'].$GLOBALS[cfg][rootDir].'/data/review/RV'.sprintf("%010s", $row_review['sno']);
}

$row_review['img_url'] = '';
if($row_review['img_s']) {
	$row_review['img_url'] = "http://".$_SERVER['HTTP_HOST'].$GLOBALS[cfg][rootDir]."/data/goods/".$row_review['img_s'];
}
else if($row_review['img_l']) {
	$row_review['img_url'] = "http://".$_SERVER['HTTP_HOST'].$GLOBALS[cfg][rootDir]."/data/goods/".$row_review['img_l'];
}


$review = $row_review;

if($row_review['parent'] != $row_review['sno']) {
	$r_review_query = $db->_query_print('SELECT gr.*, if(gr.sno = gr.parent and m.m_no > 0 , "Y" , "N") as apply, if(gr.sno = gr.parent and m.m_no > 0 and gr.emoney = 0 , "Y" , "N") as apply2, g.goodsnm, g.goodsno, g.img_s, g.img_l, m.m_id, m.name as m_name FROM '.GD_GOODS_REVIEW.' gr LEFT JOIN '.GD_GOODS.' g ON gr.goodsno=g.goodsno LEFT JOIN '.GD_MEMBER.' m ON gr.m_no=m.m_no WHERE gr.sno=[i]', $row_review['parent']);
	$res_r_review = $db->_select($r_review_query);
	$row_r_review = $res_r_review[0];

	if($row_r_review['attach']) {
		$row_r_review['attach_url'] = 'http://'.$_SERVER['HTTP_HOST'].$GLOBALS[cfg][rootDir].'/data/review/RV'.sprintf("%010s", $row_r_review['sno']);
	}
	
	$row_r_review['img_url'] = '';
	if($row_r_review['img_s']) {
		$row_r_review['img_url'] = "http://".$_SERVER['HTTP_HOST'].$GLOBALS[cfg][rootDir]."/data/goods/".$row_r_review['img_s'];
	}
	else if($row_review['img_l']) {
		$row_r_review['img_url'] = "http://".$_SERVER['HTTP_HOST'].$GLOBALS[cfg][rootDir]."/data/goods/".$row_r_review['img_l'];
	}

	$row_r_review['contents'] = strip_tags($row_r_review['contents'], "<(/)?([a-zA-Z]*)(\\s[a-zA-Z]*=[^>]*)?(\\s)*(/)?>");

	$reply = $row_review;
	$review = $row_r_review;
}

$res_data['review'] = $review;
$res_data['reply'] = $reply;

echo ($json->encode($res_data));
