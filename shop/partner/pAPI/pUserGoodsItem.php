<?
/*********************************************************
* 파일명     :  pUserGoodsItem.php
* 프로그램명 :	pad 사용자 App.용 상품아이템 API
* 작성자     :  dn
* 생성일     :  2011.12.28
**********************************************************/
include "../../lib/library.php";
include "../../conf/config.php";
require_once "../../lib/pAPI.class.php";
require_once "../../lib/json.class.php";

$pAPI = new pAPI();
$json = new Services_JSON(16);

### 인증키 Check (실제로는 아이디와 비번 임) 시작 ###
if(!$_POST['authentic']) {
	$res_data['result']['code'] = '302';
	$res_data['result']['msg'] = '인증키가 없습니다.';
	echo ($json->encode($res_data));
	exit;
}

if(!$pAPI->keyCheck($_POST['authentic'])) {
	$res_data['result']['code'] = '302';
	$res_data['result']['msg'] = '인증키가 맞지 않습니다.';
	echo ($json->encode($res_data));
	exit;
}
unset($_POST['authentic']);
### 인증키 Check 끝 ###

$goodsno = $_POST['goodsno'];

if(!$goodsno) {
	$res_data['result']['code']='301';
	$res_data['result']['msg']='상품번호가 없습니다.';
	echo ($json->encode($res_data));
	exit;
}

$goods_query = $db->_query_print('SELECT * FROM '.GD_GOODS.' g JOIN '.GD_SHOPTOUCH_GOODS.' sg ON g.goodsno = sg.goodsno WHERE g.goodsno=[i]', $goodsno);
$link_query = $db->_query_print('SELECT * FROM '.GD_GOODS_LINK.' WHERE goodsno=[i] ORDER BY category', $goodsno);
$option_query = $db->_query_print('SELECT * FROM '.GD_GOODS_OPTION.' WHERE goodsno=[i]', $goodsno);
$add_query = $db->_query_print('SELECT * FROM '.GD_GOODS_ADD.' WHERE goodsno=[i] AND stats=1', $goodsno);

$res_goods = $db->_select($goods_query);
$row_goods = $res_goods[0];

$res_link = $db->_select($link_query);
$res_option = $db->_select($option_query);
$res_add = $db->_select($add_query);

$res_data['result']['code'] = '000';
$res_data['result']['msg'] = '성공';

$res_data['data']['goodsno'] = $row_goods['goodsno'];

foreach($res_link as $row_link) {
	$res_data['data']['category'][] = (string)$row_link['category'];
	$res_data['data']['catnm'][] = currPosition($row_link['category'], 1);
}

### 브랜드명 검색 ###
$row_goods['brandnm'] = '';
if($row_goods['brandno']) {
	$brand_query = $db->_query_print('SELECT * FROM '.GD_GOODS_BRAND.' WHERE sno=[i]', $row_goods['brandno']);
	$brand_res = $db->_select($brand_query);

	$row_goods['brandnm'] = $brand_res[0]['brandnm'];
}

$res_data['data']['goodsnm'] = (string)$row_goods['goodsnm'];
$res_data['data']['delivery_type'] = (string)$row_goods['delivery_type'];
$res_data['data']['goods_delivery'] = (string)$row_goods['goods_delivery'];
$res_data['data']['usestock'] = (string)$row_goods['usestock'];
$res_data['data']['runout'] = (string)$row_goods['runout'];
$res_data['data']['min_ea'] = (string)$row_goods['min_ea'];
$res_data['data']['max_ea'] = (string)$row_goods['max_ea'];
$res_data['data']['tax'] = (string)$row_goods['tax'];
$res_data['data']['strprice'] = (string)$row_goods['strprice'];

if($row_goods['optnm']) {
	$arr_optnm = explode('|', $row_goods['optnm']);
}

$res_data['data']['opt1kind'] = (string)$arr_optnm[0];
$res_data['data']['opt2kind'] = (string)$arr_optnm[1];

$arr_option = array();
if(!empty($res_option) && is_array($res_option)) {
	foreach($res_option as $row_option) {
		$arr_option['opt1'][] = (string)$row_option['opt1'];
		$arr_option['opt2'][] = (string)$row_option['opt2'];

		if(!$row_option['stock']) $row_option['stock'] = '0';
		$arr_option['stock'][] = (string)$row_option['stock'];
		
		if(!$row_option['price']) $row_option['price'] = '0';
		$arr_option['price'][] = (string)$row_option['price'];
		
		if(!$row_option['consumer']) $row_option['consumer'] = '0';
		$arr_option['consumer'][] = (string)$row_option['consumer'];

		if(!$row_option['supply']) $row_option['supply'] = '0';
		$arr_option['supply'][] = (string)$row_option['supply'];

		if(!$row_option['reserve']) $row_option['reserve'] = '0';
		$arr_option['reserve'][] = (string)$row_option['reserve'];
	}
}

if(empty($arr_option)) {
	$arr_option['opt1'][] = '';
	$arr_option['opt2'][] = '';
	$arr_option['stock'][] = '0';
	$arr_option['price'][] = '0';
	$arr_option['consumer'][] = '0';
	$arr_option['supply'][] = '0';
	$arr_option['reserve'][] = '0';
}

if(!empty($arr_option)) {
	$res_data['data']['option'] = $arr_option;
}

$row_goods['useAdd'] = '0';
if(count($res_add) > 0) {
	$row_goods['useAdd'] = '1';
}

$res_data['data']['useAdd'] = $row_goods['useAdd'];

if($row_goods['useAdd'] == '1') {
	$tmp_arr_add = @explode('|', $row_goods['addoptnm']);
	$arr_addoptnm = array();
	$arr_addoptreq = array();
	if(!empty($tmp_arr_add) && is_array($tmp_arr_add)) {
		foreach($tmp_arr_add as $tmp_add) {
			$_tmp_add = explode('^', $tmp_add);
			$arr_addoptnm[] = $_tmp_add[0];
			$arr_addoptreq[] = $_tmp_add[1];
		}
	}


	if(!empty($arr_addoptnm)) {
		$res_data['data']['addoptnm'] = $arr_addoptnm;
	}

	$arr_add = array();
	if(!empty($res_add) && is_array($res_add)) {
		foreach($res_add as $row_add) {
			$arr_add['opt'][$row_add['step']][] = (string)$row_add['opt'];
			$arr_add['sno'][$row_add['step']][] = (string)$row_add['sno'];
			$arr_add['addprice'][$row_add['step']][] = (string)$row_add['addprice'];
		}
	}

	if(!empty($arr_add)) {
		$res_data['data']['addopt'] = $arr_add;
	}

	if(!empty($arr_addoptreq)) {
		$res_data['data']['addoptreq'] = $arr_addoptreq;
	}
}
$res_data['data']['img_shoptouch'] = Array();

if($row_goods['img_shoptouch']) {
	$res_data['data']['img_shoptouch'] = explode('|', $row_goods['img_shoptouch']);
}

$res_data['data']['open'] = $row_goods['open'];
$res_data['data']['open_shoptouch'] = $row_goods['open_shoptouch'];

$res_data['data']['thumbnail'] = $res_data['data']['img_shoptouch'][0];
$res_data['data']['longdesc'] = str_replace("\r", "", str_replace("\n", "", $row_goods['slongdesc']));


$link_pregOld = array('/(href=.|src=.)(\/)/i'); 
$link_pregNew = array('$1http://'.$_SERVER['HTTP_HOST'].'/'); 

$result = preg_replace($link_pregOld, $link_pregNew, $res_data['data']['longdesc'] ); 
$res_data['data']['longdesc'] = $result;


/* 필요 url 추가 2011.12.28 dn */
$res_data['data']['qna_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_myp/qna_goods.php?goodsno='.$goodsno;
$res_data['data']['review_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_myp/review.php?goodsno='.$goodsno;
$res_data['data']['order_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_ord/order.php';
$res_data['data']['cart_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_goods/ajaxAction.php';
$res_data['data']['info_url'] = 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_disp/info_goods.php';

$res_data['data']['goods_url'] = 'http://'.$_SERVER['HTTP_HOST'].''.$cfg['rootDir'].'/goods/goods_view.php?&goodsno='.$res_data['data']['goodsno'];

/* brand명 추가 2011.12.30 dn */
$res_data['data']['brandnm'] = $row_goods['brandnm'];

echo ($json->encode($res_data));

?>