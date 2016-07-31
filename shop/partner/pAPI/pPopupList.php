<?
/*********************************************************
* 파일명     :  pPopupList.php
* 프로그램명 :	pad 팝업리스트 API
* 작성자     :  dn
* 생성일     :  2011.10.27
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


$file	= dirname(__FILE__)."/../../conf/godomall.cfg.php";
if (!is_file($file)) {
	$res_data['result']['code'] = '300';
	$res_data['result']['msg'] = '고도 설정파일이 없습니다. 설정파일을 등록하세요';
	echo ($json->encode($res_data));
	exit;
}

$file	= file($file);
$godo	= decode($file[1],1);

if(!empty($_POST)) {
	foreach($_POST as $key => $val) {

		$tmp_key = str_replace('search_', '', $key);
		if(strstr($tmp_key, 'arr_')) {
			$n_tmp_key = str_replace('arr_', '', $tmp_key);
			${$n_tmp_key} = explode('|', $val);
		}
		else  {
			${$tmp_key} = $val;
		}
	}
}

if(!$list) $list = 20;
if(!$page) $page = 1;

$display_query = $db->_query_print('SELECT * FROM '.GD_SHOPTOUCH_DISPLAY.' WHERE mode=[s] ORDER BY sort ASC, no ASC', 'popup');
$res_display = $db->_select_page($list, $page, $display_query);
//debug($order_query);

$display_data = $res_display['record'];

$arr_link_type = array(1 => '분류');
$arr_link_type[] = '상품';
$arr_link_type[] = 'URL';

$res_data = Array();
if(!empty($display_data)) {
	foreach($display_data as $row_display) {
		
		$row_display['str_link_type'] = $arr_link_type[$row_display['link_type']];

		if($row_display['link_type'] == '1') {
			$tmp_data = $pAPI->getMainMenuItem($godo['sno'], $row_display['category']);		
			$cate_data = $json->decode($tmp_data);
			
			$row_display['link_path'] = $cate_data['name'].'('.$row_display['category'].')';
		}
		else if($row_display['link_type'] == '2') {
			$field = 'goodsnm';
			$table = GD_GOODS;
			$where = $db->_query_print('goodsno = [i]', $row_display['goodsno']);

			$row_query = $db->_query_print('SELECT '.$field.' from '.$table.' WHERE '.$where);
			$row_result = $db->_select($row_query);
			$row_result = $row_result[0];

			$row_display['link_path'] = $row_result['goodsnm'].'('.$row_display['goodsno'].')';


		}		
		else {
			$row_display['link_path'] = $row_display['link_url'];
		}

		$res_data[] = $row_display;
	}
}


echo ($json->encode($res_data));

?>