<?
/*********************************************************
* 파일명     :  pNotification.php
* 프로그램명 :	알림 정보 전달
* 작성자     :  dn
* 생성일     :  2011.11.07
**********************************************************/
include "../../lib/library.php";
include "../../conf/config.php";
require_once "../../lib/pAPI.class.php";
require_once "../../lib/qfile.class.php";
require_once "../../lib/json.class.php";

$pAPI = new pAPI();
$json = new Services_JSON(16);
$qfile = new qfile();

$cfgShopTouch['order_noti_last'] = '';
$cfgShopTouch['pay_noti_last'] = '';
$cfgShopTouch['qa_noti_last'] = '';

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

if(!$_POST['order'] && !$_POST['pay'] && !$_POST['qa']) {
	$_POST['order'] = 'true';
	$_POST['pay'] = 'true';
	$_POST['qa'] = 'true';
}

$shopName = $cfg['shopName'];

$env_category = 'shoptouch';

if($_POST['order'] == 'true') {
	
	$env_name='order_noti_last';

	$date_chk_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', $env_category, $env_name);
	$res_date_chk = $db->_select($date_chk_query);
	$order_noti_last = $res_date_chk[0]['value'];

	if(!$order_noti_last) {
		$s_date = '0000-00-00 00:00:00';
	}
	else {
		$s_date = $order_noti_last;
	}

	$e_date = date('Y-m-d H:i:s');

	$select_query = $db->_query_print('SELECT count(*) cnt FROM '.GD_ORDER.' WHERE step=0 AND orddt > [s] AND orddt <=[s]', $s_date, $e_date);
	$res_data = $db->_select($select_query);
	$cnt = $res_data[0]['cnt'];
	
	if($cnt) {
		$msg_order = '['.$shopName.'] 신규 주문 내역이 '.$cnt.'건 있습니다.';
	}

	if(!$res_date_chk[0]['value']) {
		$tmp_arr['category'] = $env_category;
		$tmp_arr['name'] = $env_name;
		$tmp_arr['value'] = $e_date;

		$date_upd_query = $db->_query_print('INSERT INTO gd_env SET [cv]', $tmp_arr);
	}
	else {
		
		$date_upd_query = $db->_query_print('UPDATE gd_env SET value=[s] WHERE category=[s] AND name=[s]', $e_date, $env_category, $env_name);
	}

	$db->query($date_upd_query);
}
unset($cnt, $last_date, $s_date, $e_date, $select_query, $res_data);

if($_POST['pay'] == 'true') {
	
	$env_name='pay_noti_last';

	$date_chk_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', $env_category, $env_name);
	$res_date_chk = $db->_select($date_chk_query);
	$pay_noti_last = $res_date_chk[0]['value'];
		
	if(!$pay_noti_last) {
		$s_date = '0000-00-00 00:00:00';
	}
	else {
		$s_date = $pay_noti_last;
	}

	$e_date = date('Y-m-d H:i:s');

	$select_query = $db->_query_print('SELECT count(*) cnt FROM '.GD_ORDER.' WHERE step=1 AND cdt > [s] AND cdt <=[s]', $s_date, $e_date);
	$res_data = $db->_select($select_query);
	$cnt = $res_data[0]['cnt'];

	if($cnt) {
		$msg_pay = '['.$shopName.'] 입금확인 내역이 '.$cnt.'건 있습니다.';
	}
	
	if(!$res_date_chk[0]['value']) {
		$tmp_arr['category'] = $env_category;
		$tmp_arr['name'] = $env_name;
		$tmp_arr['value'] = $e_date;

		$date_upd_query = $db->_query_print('INSERT INTO gd_env SET [cv]', $tmp_arr);
	}
	else {
		
		$date_upd_query = $db->_query_print('UPDATE gd_env SET value=[s] WHERE category=[s] AND name=[s]', $e_date, $env_category, $env_name);
	}

	$db->query($date_upd_query);
}
unset($cnt, $last_date, $s_date, $e_date, $select_query, $res_data);

if($_POST['qa'] == 'true') {
	
	$env_name='qa_noti_last';

	$date_chk_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', $env_category, $env_name);
	$res_date_chk = $db->_select($date_chk_query);
	$qa_noti_last = $res_date_chk[0]['value'];
		
	if(!$qa_noti_last) {
		$s_date = '0000-00-00 00:00:00';
	}
	else {
		$s_date = $qa_noti_last;
	}

	$e_date = date('Y-m-d H:i:s');

	$select_query = $db->_query_print('SELECT count(*) cnt FROM '.GD_GOODS_QNA.' WHERE parent=sno AND regdt > [s] AND regdt <=[s]', $s_date, $e_date);
	$res_data = $db->_select($select_query);
	$cnt = $res_data[0]['cnt'];

	if($cnt) {
		$msg_qa = '['.$shopName.'] 상품문의가 '.$cnt.'건 있습니다.';
	}
	
	if(!$res_date_chk[0]['value']) {
		$tmp_arr['category'] = $env_category;
		$tmp_arr['name'] = $env_name;
		$tmp_arr['value'] = $e_date;

		$date_upd_query = $db->_query_print('INSERT INTO gd_env SET [cv]', $tmp_arr);
	}
	else {
		
		$date_upd_query = $db->_query_print('UPDATE gd_env SET value=[s] WHERE category=[s] AND name=[s]', $e_date, $env_category, $env_name);
	}

	$db->query($date_upd_query);
}
unset($cnt, $last_date, $s_date, $e_date, $select_query, $res_data);

$res_data['result']['code'] = '000';
$res_data['result']['msg'] = 'SUCCESS';

if($msg_order) {
	$res_data['order']['msg'] = $msg_order;
	$res_data['order']['url'] = '';
}

if($msg_pay) {
	$res_data['pay']['msg'] = $msg_pay;
	$res_data['pay']['url'] = '';
}

if($msg_qa) {
	$res_data['qa']['msg'] = $msg_qa;
	$res_data['qa']['url'] = '';
}

echo ($json->encode($res_data));
?>