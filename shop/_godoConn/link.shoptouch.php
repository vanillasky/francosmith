<?php
include("../lib/library.php");
$godo = $config->load('godo');

$mode = $_POST['mode'];

// 체크용 통신
if($mode=='check') {
	echo $godo['sno'];
	exit;
}

$category = 'shoptouch';

switch ($mode) {
	case 'expire' :
		$expire_dt = $_POST['expire_dt'];
		
		$os = $_POST['os'];
			
		if($os == 'android') {
			$name = 'android_expire_dt';
		}
		else {
			$name = 'apple_expire_dt';
		}
		
		$chk_query = $db->_query_print('SELECT count(*) as cnt_chk FROM gd_env WHERE category=[s] AND name=[s]', $category, $name);

		$res_chk = $db->_select($chk_query);
		$chk_cnt = $res_chk[0]['cnt_chk'];
		
		if($chk_cnt) {
			$req_query = $db->_query_print('UPDATE gd_env SET value=[s] WHERE category=[s] AND name=[s]', $expire_dt, $category, $name);
		}
		else {
			$ins_arr = Array();
			$ins_arr['category'] = 'shoptouch';
			$ins_arr['name'] = $name;
			$ins_arr['value'] = $expire_dt;
			$req_query = $db->_query_print('INSERT INTO gd_env SET [cv]', $ins_arr);

		}
		$db->query($req_query);
		exit('DONE');
		break;
		
	case 'request' :
		$arr['category'] = $category;
		$arr['name'] = $_POST['service_name'];
		$arr['value'] = $_POST['service_status'];

		$chk_query = $db->_query_print('SELECT count(*) as cnt_chk FROM gd_env WHERE category=[s] AND name=[s]', $category, $arr['name']);
		$res_chk = $db->_select($chk_query);
		$chk_cnt = $res_chk[0]['cnt_chk'];

		if($chk_cnt) {
			$req_query = $db->_query_print('UPDATE gd_env SET value=[s] WHERE category=[s] AND name=[s]', $arr['value'], $category, $arr['name']);
		}
		else {
			$req_query = $db->_query_print('INSERT INTO gd_env SET [cv]', $arr);
		}
		$db->query($req_query);
		exit('DONE');
		break;

	case 'step_change' :
		$arr['category'] = $category;
		$arr['name'] = $_POST['market_name'];
		$arr['value'] = $_POST['market_step'];

		$chk_query = $db->_query_print('SELECT count(*) as cnt_chk FROM gd_env WHERE category=[s] AND name=[s]', $category, $arr['name']);
		$res_chk = $db->_select($chk_query);
		$chk_cnt = $res_chk[0]['cnt_chk'];

		if($chk_cnt) {
			$req_query = $db->_query_print('UPDATE gd_env SET value=[s] WHERE category=[s] AND name=[s]', $arr['value'], $category, $arr['name']);
		}
		else {
			$req_query = $db->_query_print('INSERT INTO gd_env SET [cv]', $arr);
		}
		$db->query($req_query);
		exit('DONE');
		break;
	
	case 'status' :
		$arr['category'] = $category;
		$arr['name'] = $_POST['os'].'_status';
		$arr['value'] = $_POST['status'];

		$chk_query = $db->_query_print('SELECT count(*) as cnt_chk FROM gd_env WHERE category=[s] AND name=[s]', $category, $arr['name']);
		$res_chk = $db->_select($chk_query);
		$chk_cnt = $res_chk[0]['cnt_chk'];

		if($chk_cnt) {
			$req_query = $db->_query_print('UPDATE gd_env SET value=[s] WHERE category=[s] AND name=[s]', $arr['value'], $category, $arr['name']);
		}
		else {
			$req_query = $db->_query_print('INSERT INTO gd_env SET [cv]', $arr);
		}
		$db->query($req_query);
		exit('DONE');
		break;

	case 'my_menu_set' :
		
		@include "../lib/pAPI.class.php";
		@include "../lib/json.class.php";
		$pAPI = new pAPI();
		$json = new Services_JSON(16);

		$tmp_mymenu = $pAPI->getMyMenu($godo['sno']);
		
		$arr_mymenu = $json->decode($tmp_mymenu);

		$basic_mymenu = Array();

		$basic_mymenu = Array(
			'로그인' => array('menu_name' => '로그인','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_mem/login.php','visibility' => 'true'),
			'장바구니' => array('menu_name' => '장바구니','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_goods/cart.php','visibility' => 'true'),
			'주문/배송' => array('menu_name' => '주문/배송','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_myp/orderlist.php','visibility' => 'true'),
			'1:1문의' => array('menu_name' => '1:1문의','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_myp/qna.php','visibility' => 'true'),
			'할인쿠폰' => array('menu_name' => '할인쿠폰','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_myp/couponlist.php','visibility' => 'true'),
			'적립금내역' => array('menu_name' => '적립금내역','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_myp/emoneylist.php','visibility' => 'true'),
			'나의 상품후기' => array('menu_name' => '나의 상품후기','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_myp/review.php','visibility' => 'true'),
			'나의 상품문의' => array('menu_name' => '나의 상품문의','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_myp/qna_goods.php','visibility' => 'true'),
			'FAQ' => array('menu_name' => 'FAQ','menu_web_url' => 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_myp/faq.php','visibility' => 'true')
		);

		if(!$arr_mymenu['code'] && !empty($arr_mymenu) && is_array($arr_mymenu)) {
			foreach($arr_mymenu as $row_mymenu) {
				if(!empty($basic_mymenu[$row_mymenu['menu_name']])) {
					$basic_mymenu[$row_mymenu['menu_name']]['menu_idx'] = $row_mymenu['menu_idx'];
					$basic_mymenu[$row_mymenu['menu_name']]['visibility'] = $row_mymenu['visibility'];
				}
				else {
					$del_mymenu[$row_mymenu['menu_name']] = $row_mymenu['menu_idx'];
				}
			}

		}

		foreach($basic_mymenu as $row_basic) {
			if(!$row_basic['menu_idx']) {
				$menu_idx = 0;
				
				$menu_idx = $json->decode($pAPI->myMenuAdd($godo['sno'], $row_basic));

				if($menu_idx) $basic_mymenu[$row_basic['menu_name']]['menu_idx'] = $menu_idx['menu_idx'];
			}

		}

		if(!empty($del_mymenu) && is_array($del_mymenu)) {
			foreach($del_mymenu as $row_del) {
				$tmp_del['menu_idx'] = $row_del;
				$ret = $pAPI->myMenuDelete($godo['sno'], $tmp_del);
			}
		}
		
		exit('DONE');
		break;
}





?>