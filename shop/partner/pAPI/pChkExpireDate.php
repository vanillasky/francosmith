<?
/*********************************************************
* 파일명     :  pChkExpireDate.php
* 프로그램명 :	만료일자 Check API
* 작성자     :  dn
* 생성일     :  2012.01.12
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
		$res_data['result']['msg'] = '현재 쇼핑몰은 관리자에 의해 접근이 일시적으로 중단 된 상태입니다.';
	}
	else {
		
		$res_data['result']['code'] = '000';
		$res_data['result']['msg'] = '성공';
		$res_data['expire_dt'] = $expire_dt;
	}	
}
else {
	$res_data['result']['code'] = '302';
	$res_data['result']['msg'] = '현재 쇼핑몰은 관리자에 의해 접근이 일시적으로 중단 된 상태입니다.';
}
echo ($json->encode($res_data));

?>