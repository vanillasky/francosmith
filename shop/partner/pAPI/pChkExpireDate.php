<?
/*********************************************************
* ���ϸ�     :  pChkExpireDate.php
* ���α׷��� :	�������� Check API
* �ۼ���     :  dn
* ������     :  2012.01.12
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

if($_GET['debug']) {
	$_POST['os'] = 'apple';
}

if(!empty($_POST)) {
	foreach($_POST as $key => $val) {
		${$key} = $val;
	}
}

$category = 'shoptouch';
$chk_apple_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', $category, 'apple_expire_dt');
$apple_res = $db->_select($chk_apple_query);
$expire_dt_apple = $apple_res[0]['value'];

$chk_android_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', $category, 'android_expire_dt');
$android_res = $db->_select($chk_android_query);
$expire_dt_android = $android_res[0]['value'];

$expire_dt = '';

if($os == 'apple') {
	$expire_dt = $expire_dt_apple;
}
else if($os == 'android') {
	$expire_dt = $expire_dt_android;
}
else {
	
	if($expire_dt_apple > $expire_dt_android) {
		$expire_dt = $expire_dt_apple;
	}
	else {
		$expire_dt = $expire_dt_android;
	}
}

if($expire_dt > date('Y-m-d H:i:s')) {
	
	if($os == 'apple') {
		$chk_use_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', $category, 'use_apple');
	}
	
	if($os == 'android') {
		$chk_use_query = $db->_query_print('SELECT value FROM gd_env WHERE category=[s] AND name=[s]', $category, 'use_android');
	}

	$res_use = $db->_select($chk_use_query);

	$use_app = $res_use[0]['value'];

	if($use_app == '0') {
		$res_data['result']['code'] = '302';
		$res_data['result']['msg'] = '���� ���θ��� �����ڿ� ���� ������ �Ͻ������� �ߴ� �� �����Դϴ�.';
	}
	else {
		
		$res_data['result']['code'] = '000';
		$res_data['result']['msg'] = '����';
		$res_data['expire_dt'] = $expire_dt;
	}	
}
else {
	$res_data['result']['code'] = '302';
	$res_data['result']['msg'] = '���� ���θ��� �����ڿ� ���� ������ �Ͻ������� �ߴ� �� �����Դϴ�.';
}
echo ($json->encode($res_data));

?>