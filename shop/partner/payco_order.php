<?
/*
�������߰� > ���θ� �ֹ����� ��ȸ
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

if(empty($arr_data)) resposen_log('���۵� �����Ͱ� �����ϴ�.');

//������ Ŭ����
$paycoApi = &load_class('paycoApi','paycoApi');
$payco = &load_class('payco','payco');

// ���ŵ����� �α� ����
$paycoApi->transmit_log($arr_data, 'payco_order');

// ���� ������ ���θ� üũ �� ������ ��ȣȭ
$addr_data = $paycoApi->shop_check($arr_data);

if($addr_data === false) {
	resposen_log('���θ� �������� �ٸ��ϴ�.');
}

$param = arr_data_iconv($addr_data);

if($param['mode'] == 'order_status') {
	/*
	 * �ֹ����� ����
	 * �����õ� => �Ա�Ȯ��
	 * �����õ� => ��������
	*/

	//�ֹ����� ��ȸ
	$query = $db->_query_print('SELECT step, step2 FROM '.GD_ORDER.' WHERE ordno=[s]', $param['ordno']);
	$res = $db->fetch($query, true);

	if($res['step'] == $param['step'] && $res['step2'] == $param['step2']) resposen_log('0');
	else if($res['step'] > 0) resposen_log('1');

	$query = $db->_query_print('UPDATE '.GD_ORDER.' SET step=[i], step2=[i] WHERE ordno=[s]', $param['step'], $param['step2'], $param['ordno']);
	$db->_query($query);

	$query2 = $db->_query_print('UPDATE '.GD_ORDER_ITEM.' SET istep=[i] WHERE ordno=[s]', $param['step2'], $param['ordno']);
	$db->_query($query2);

	// �ֹ��α� ����
	orderLog($param['ordno'], $r_step[$param['step']]." > ".$r_step2[$param['step2']]);

	resposen_log('0');
}
else if($param['mode'] == 'order_search') {
	//�ֹ����� ��ȸ
	$query = $db->_query_print('SELECT step, step2 FROM '.GD_ORDER.' WHERE ordno=[s]', $param['ordno']);
	$res = $db->fetch($query, true);
	resposen_log($res);
}

?>