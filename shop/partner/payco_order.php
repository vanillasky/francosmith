<?
/*
페이코중계 > 쇼핑몰 주문정보 조회
*/
include "../lib/library.php";

if($_SERVER['REMOTE_ADDR'] != '211.233.51.165' && $_SERVER['REMOTE_ADDR'] != '211.233.51.166' && $_SERVER['REMOTE_ADDR'] != '211.233.51.250') exit;

function resposen_log($msg)
{
	global $paycoApi;
	if(!$paycoApi) $paycoApi = &load_class('paycoApi','paycoApi');
	$paycoApi->transmit_log($msg, 'payco_order');
	print_r(serialize($msg));
	exit;
}

function arr_data_iconv($b)
{
	$iconv_data = array();

	foreach($b as $k => $v) {
		if(is_array($v)) $iconv_data[$k] = arr_data_iconv($v);
		else $iconv_data[$k] = iconv('utf-8', 'euc-kr', $v);
	}
	return $iconv_data;
}

$arr_data = $_POST;

if(empty($arr_data)) resposen_log('전송된 데이터가 없습니다.');

//페이코 클래스
$paycoApi = &load_class('paycoApi','paycoApi');
$payco = &load_class('payco','payco');

// 수신데이터 로그 저장
$paycoApi->transmit_log($arr_data, 'payco_order');

// 수신 데이터 쇼핑몰 체크 및 데이터 복호화
$addr_data = $paycoApi->shop_check($arr_data);

if($addr_data === false) {
	resposen_log('쇼핑몰 고유값이 다릅니다.');
}

$param = arr_data_iconv($addr_data);

if($param['mode'] == 'order_status') {
	/*
	 * 주문상태 변경
	 * 결제시도 => 입금확인
	 * 결제시도 => 결제실패
	*/

	//주문상태 조회
	$query = $db->_query_print('SELECT step, step2 FROM '.GD_ORDER.' WHERE ordno=[s]', $param['ordno']);
	$res = $db->fetch($query, true);

	if($res['step'] == $param['step'] && $res['step2'] == $param['step2']) resposen_log('0');
	else if($res['step'] > 0) resposen_log('1');

	$query = $db->_query_print('UPDATE '.GD_ORDER.' SET step=[i], step2=[i] WHERE ordno=[s]', $param['step'], $param['step2'], $param['ordno']);
	$db->_query($query);

	$query2 = $db->_query_print('UPDATE '.GD_ORDER_ITEM.' SET istep=[i] WHERE ordno=[s]', $param['step2'], $param['ordno']);
	$db->_query($query2);

	// 주문로그 저장
	orderLog($param['ordno'], $r_step[$param['step']]." > ".$r_step2[$param['step2']]);

	resposen_log('0');
}
else if($param['mode'] == 'order_search') {
	//주문상태 조회
	$query = $db->_query_print('SELECT step, step2 FROM '.GD_ORDER.' WHERE ordno=[s]', $param['ordno']);
	$res = $db->fetch($query, true);
	resposen_log($res);
}

?>