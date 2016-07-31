<?
/*********************************************************
* 파일명     :  pGoodsSearch.php
* 프로그램명 :	pad 상품검색 API
* 작성자     :  dn
* 생성일     :  2011.10.12
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

if(!empty($_POST)) {
	foreach($_POST as $key => $val) {
		if(strstr($key, 'arr_')) {
			${str_replace('arr_', '', $key)} = explode('|', $val);
		}
		else  {
			${$key} = $val;
		}
	}
}

if(!$from) $from = 0;
if(!$to) $to = 10;

$arr_where = Array();

$arr_where[] = $db->_query_print('g.todaygoods=[s]', 'n');	//투데이샵 상품은 제외
if($category) $arr_where[] = $db->_query_print('l.category like [s]', $category.'%');

if($sword) {
	if($skey == 'goodsnm') {
		$arr_where[] = $db->_query_print('g.'.$skey.' like [s]', '%'.$sword.'%');
	}
	else if($skey =='goodsno'){
		$arr_where[] = $db->_query_print('g.'.$skey.'=[i]', $sword);
	}
	else if($skey =='goodscd') {
		$arr_where[] = $db->_query_print('g.'.$skey.'=[s]', $sword);
	}
}

if($open != '') $arr_where[] = $db->_query_print('sg.open_shoptouch=[i]', $open);

if(!empty($arr_where)) {
	$where = ' WHERE '.implode(' AND ', $arr_where);
}
else {
	$where = ' WHERE 1=1';
}

$table = '
'.GD_GOODS.' g
JOIN '.GD_SHOPTOUCH_GOODS.' sg ON g.goodsno = sg.goodsno
LEFT JOIN '.GD_GOODS_OPTION.' o ON g.goodsno=o.goodsno AND o.link
';
if(!empty($category)) {
	$table .= '
	LEFT JOIN '.GD_GOODS_LINK.' l ON g.goodsno=l.goodsno';
}

$goods_query = $db->_query_print('SELECT distinct g.goodsno, sg.img_shoptouch, img_s, img_l, goodsnm, regdt, sg.open_shoptouch, g.open, price, totstock FROM '.$table.$where.' ORDER BY regdt DESC LIMIT [i], [i]', $from, ($to - $from) + 1);

$res_goods = $db->_select($goods_query);

if(!empty($res_goods)) {
	$i = 0;
	foreach($res_goods as $row_goods) {
		$tmp_goods[$i]['goodsno'] = $row_goods['goodsno'];	//상품번호
		
		$img_path = 'http://'.$_SERVER['HTTP_HOST'].$cfg[rootDir].'/data/goods/';
		
		$tmp_goods[$i]['thumbnail'] = '';

		if($row_goods['img_shoptouch']) {
			
			$arr_img_shopTouch = @explode('|', $row_goods['img_shoptouch']);
			
			if($arr_img_shopTouch[0]) $tmp_goods[$i]['thumbnail'] = $arr_img_shopTouch[0];

		}else if($row_goods['img_s']) {	//리스트 이미지가 있을 경우
			$tmp_goods[$i]['thumbnail'] = $img_path.$row_goods['img_s']; //썸네일
		}
		else if($row_goods['img_l']) {
			$tmp_goods[$i]['thumbnail'] = $img_path.$row_goods['img_l']; //썸네일
		}
		
		$tmp_goods[$i]['goodsnm'] = $row_goods['goodsnm'];	//상품명
		$tmp_goods[$i]['regdt'] = substr($row_goods['regdt'], 0, 10);	//등록일
		
		if(!$row_goods['open_shoptouch']) $row_goods['open_shoptouch'] = '0';
		$tmp_goods[$i]['open'] = $row_goods['open_shoptouch'];	//진열여부

		if(!$row_goods['price']) $row_goods['price'] = '0';	 //price 가 null 인경우( gd_goods_option 에 row가 없는 경우 ) 0 입력
		$tmp_goods[$i]['price'] = $row_goods['price'];	//가격
		
		if(!$row_goods['totstock']) $row_goods['totstock'] = '0';
		$tmp_goods[$i]['totstock'] = $row_goods['totstock'];	//재고

		$i++;
	}
}

$res_data['result']['code'] = '000';
$res_data['result']['msg'] = '성공';
if(!empty($tmp_goods)) {
	$res_data['data'] = $tmp_goods;
}

echo ($json->encode($res_data));

?>