<?
/*********************************************************
* 파일명     :  pNoticeSet.php
* 프로그램명 :	push 설정
* 작성자     :  dn
* 생성일     :  2012.03.03
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

if(!($m_no = $pAPI->keyCheck($_POST['authentic'], 'm_no'))) {
	$res_data['code'] = '302';
	$res_data['msg'] = '인증키가 맞지 않습니다.';
	echo ($json->encode($res_data));
	exit;
}

unset($_POST['authentic']);
### 인증키 Check 끝 ###

$mode = $_POST['mode'];
unset($_POST['mode']);
if(!$mode) {
	$res_data['code'] = '301';
	$res_data['msg'] = '잘못된 접근 입니다.';
	echo ($json->encode($res_data));
	exit;
}

foreach($_POST as $key=>$val) {
	if(strstr($key, 'arr_')) {
		$tmp_arr[str_replace('arr_', '', $key)] = explode('|', $val);
	}
	else  {
		$tmp_arr[$key] = $val;
	}
}
unset($_POST);
$_POST = $tmp_arr;

switch($mode) {

	case "notice_set" :

		$arr = Array();
		$chk_query = $db->_query_print('SELECT count(*) chk_cnt FROM gd_env WHERE category=[s] AND name=[s]', 'shoptouch', $_POST['notice_type']);
		$res_chk = $db->_select($chk_query);
		$chk_cnt = $res_chk[0]['chk_cnt'];

		if($chk_cnt) {
			$arr['value'] = $_POST['value'];
			$notice_query = $db->_query_print('UPDATE gd_env SET [cv] WHERE category=[s] AND name=[s]', $arr, 'shoptouch', $_POST['notice_type']);
		}
		else {
			$arr['category'] = 'shoptouch';
			$arr['name'] = $_POST['notice_type'];
			$arr['value'] = $_POST['value'];
			$notice_query = $db->_query_print('INSERT INTO gd_env SET [cv]', $arr);

		}

		$db->query($notice_query);
	
		$res_data['code'] = '000';
		$res_data['msg'] = '성공';		
		
		break;
}

echo ($json->encode($res_data));