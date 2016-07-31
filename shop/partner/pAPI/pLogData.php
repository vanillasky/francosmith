<?
/*********************************************************
* 파일명     :  pLogData.php
* 프로그램명 :	방문자 분석 data
* 작성자     :  dn
* 생성일     :  2012.01.25
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

### data 출력 ###
$log_date = $_POST['s_date'];
$mode = $_POST['mode'];

$log_date = substr($log_date, 0, 4).substr($log_date, 5, 2).substr($log_date, 8, 2);

switch ($mode){
	case 'visit_day': 
		$query = $db->_query_print('SELECT * FROM '.MINI_COUNTER.' WHERE day=[s]', $log_date);
		break;

	case 'visit_month':
		$tmp_date = substr($log_date, 0, 6).'%';
		$query = $db->_query_print('SELECT RIGHT(day, 2)+0 k, uniques v FROM '.MINI_COUNTER.' WHERE day like [s]', $tmp_date);
		break;

	case 'pv_month':
		$tmp_date = substr($log_date, 0, 6).'%';
		$query = $db->_query_print('SELECT RIGHT(day, 2)+0 k, pageviews v FROM '.MINI_COUNTER.' WHERE day like [s]', $tmp_date);
		break;

	case 'visit_time':
		$query = $db->_query_print('SELECT * FROM '.MINI_COUNTER.' WHERE day=[i]', 0);
		break;
}

$res_data = $db->_select($query);

switch ($mode){
	case 'visit_day': case 'visit_time': 
		$ret = array_slice($res_data[0],3,24); break;
	case 'visit_month': case 'pv_month':
		for ($i=1;$i<=date("t",strtotime($log_date));$i++) $ret[$i] = 0;
		foreach ($res_data as $v) $ret[$v[k]] = $v[v];
		break;
}


$max = 0;
foreach ($ret as $k=>$v) $max = ($max<=$v) ? $v : $max;
$total = array_sum($ret);

$ret_data['total'] = $total;
$ret_data['max'] = $max;
$ret_data['data'] = $ret; 

echo $json->encode($ret_data);
exit;
?>