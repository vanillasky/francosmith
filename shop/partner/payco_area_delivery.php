<?
/*
������ > ���θ� �߰���ۺ�(������ ��ۺ�) ��ȸ
*/
include "../lib/library.php";

//if($_SERVER['REMOTE_ADDR'] != '211.233.51.165' && $_SERVER['REMOTE_ADDR'] != '211.233.51.166' && $_SERVER['REMOTE_ADDR'] != '211.233.51.250') exit;

function resposen_log($msg)
{
	global $paycoApi;
	if(!$paycoApi) $paycoApi = &load_class('paycoApi','paycoApi');
	$paycoApi->receive_log($msg, 'area_delivery');
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
$paycoApi->receive_log($arr_data, 'area_delivery');

// ���� ������ ���θ� üũ �� ������ ��ȣȭ
$addr_data = $paycoApi->shop_check($arr_data);

if($addr_data === false) {
	resposen_log('���θ� �������� �ٸ��ϴ�.');
}

$param = arr_data_iconv($addr_data);
$items = $param['sno'];
$param['road_address'] = $param['address'];

$area = &load_class('areaDelivery','areaDelivery');
$_extra_fee = $area->getPay();

if($_extra_fee == '') $_extra_fee = '0';

include dirname(__FILE__).'/../conf/config.pay.php';
$conf_delivery = $set['delivery'];

if (isset($conf_delivery['add_extra_fee']) === true) {
	$tmp_add_extra_fee			= $conf_delivery['add_extra_fee'];		// ���� ���Ž� ����, �ش� ���� ���̻� ��� ����
}
else {
	$tmp_add_extra_fee			= 1;										// �⺻ ���� ������ �߰� ��ۺ� �������� ó��
}

if (isset($conf_delivery['add_extra_fee_basic']) === false) {				// "�⺻ �����å�� ���� ���Ǻ� ������ ���"���� �⺻�� (���� ���Ž� �Ǵ� ������ �߰� ��ۺ� ����)
	$conf_delivery['add_extra_fee_basic']			= $tmp_add_extra_fee;
}

if (isset($conf_delivery['add_extra_fee_free']) === false) {				// "������ ��ǰ �ֹ���"�� ��� �⺻�� (���� ���Ž� �Ǵ� ������ �߰� ��ۺ� ����)
	$conf_delivery['add_extra_fee_free']			= $tmp_add_extra_fee;
}

if (isset($conf_delivery['add_extra_fee_memberGroup']) === false) {		// "ȸ�� �׷� ���ÿ� ���� ��ۺ� ������ ���"���� �⺻�� (���� ���Ž� �Ǵ� ������ �߰� ��ۺ� ����)
	$conf_delivery['add_extra_fee_memberGroup']	= $tmp_add_extra_fee;
	/* ������ �����Ŵ� ��ȸ���� ������� �ϰ� �־� ȸ�� �׷� ���ÿ� ���� ó���� ���� ���� */
}
unset($tmp_add_extra_fee);

// ������ �߰� ��ۺ� ���� �ΰ� �⺻�� ����
if (isset($conf_delivery['add_extra_fee_duplicate_each']) === false) {
	$conf_delivery['add_extra_fee_duplicate_each']		= 1;			// ������ۻ�ǰ �ֹ��� �⺻���� "�׸� �ߺ� �ΰ�" �� ó�� (���̻� ������� ����)
}

if (isset($conf_delivery['add_extra_fee_duplicate_free']) === false) {
	$conf_delivery['add_extra_fee_duplicate_free']		= 1;			// ������ ��ǰ �ֹ��� �⺻���� "�׸� �ߺ� �ΰ�" �� ó��
}

if (isset($conf_delivery['add_extra_fee_duplicate_fixEach']) === false) {
	$conf_delivery['add_extra_fee_duplicate_fixEach']		= 1;			// ���� ��ۺ� ��ǰ �ֹ��� �⺻���� "�׸� �ߺ� �ΰ�" �� ó��
}

/* �⺻��ۺ�� ����� �߰���ۺ� �ȹ��� */

/*
������ ��ۺ� �ΰ�
 - �⺻��ۺ� : ����, ������ ������� 1ȸ�ΰ�
 - ��ǰ��ۺ�(����, ����) : �� ��ǰ�� �ΰ� (�����ǰ 2�� - ��ǰ�� �ٸ��ų� �ɼ��� �ٸ����, ����3���� ��� 5�� �ΰ�)
 - ��ǰ��ۺ�(������) : ������ �ΰ�
*/


/*
	$conf_delivery['freeDelivery']
	- 1 = ������ ��ǰ�� �ִ� ��� ��� �ֹ� ������
		�����۽� ������ ��ۺ� �̺ΰ�
	- 0 = ������ ��ǰ�� ����
		������ ��ǰ���� ������ ��ۺ� �ΰ�
*/


/*
	$conf_delivery['add_extra_fee']
	- 1 = �����۽� �߰���ۺ� ����
	- 0 = �����ۺ� �߰���ۺ� ���� ����
		�⺻��ۺ�� ����ÿ��� ���� ����
		freeDelivery�� 0�� ��� ������ ��ǰ���� ������ ��ۺ� �ΰ�
*/

foreach($param['sno'] as $idxs) {
	if(strstr($idxs, 'dv_')) {
		$item_delivery_idxs = str_replace('dv_', '', $idxs);
		break;
	}
}

$item_delivery = $db->_select('SELECT * from '.GD_ORDER_ITEM_DELIVERY.' WHERE ordno='.$param['ordno'].' AND ordno='.$item_delivery_idxs.' ORDER BY delivery_type asc');

$free_fee = false;
$free_pay = false;
$fix_fee = false;
$fix_pay = false;
$arr_area['area_delivery'] = 0;
$arr_area['oi_delivery_idx'] = 0;

foreach($item_delivery as $delivery) {
	switch($delivery['delivery_type']) {
		case '0' ://�⺻��ۺ�
			if(isset($arr_area[0]) === false) {
				//�⺻��ۺ� �����̰�, �⺻��ۺ� �����϶� ������ ��ۺ� �ʹ��� ������ ���
				if($delivery['prn_delivery_price'] < 1 && $conf_delivery['add_extra_fee_basic'] == '0') break;;

				$arr_area['area_delivery'] += $_extra_fee;
			}
			break;
		case '1' ://�����ۺ�	(���θ����� $conf_delivery['add_extra_fee']���� ������� �߰���ۺ� �ΰ���)
			// �������� ��� ������ ��ۺ� �ߺ��ΰ� ����
			if($conf_delivery['add_extra_fee_duplicate_free'] == '1') $free_fee = true;
			else {
				if($free_pay === false) $free_fee = true;
				else $free_fee = false;
			}

			if($free_fee === true && $conf_delivery['add_extra_fee_free'] == '1') {
				$arr_area['area_delivery'] += $_extra_fee;
				$free_pay = true;
			}

			break;
		case '4' ://������ۺ�	(���θ����� $conf_delivery['add_extra_fee']���� ������� �߰���ۺ� �ΰ���)
			// ������ۺ��� ��� ������ ��ۺ� �ߺ��ΰ� ����
			if($conf_delivery['add_extra_fee_duplicate_fixEach'] == '1') $fix_fee = true;
			else {
				if($fix_pay === false) $fix_fee = true;
				else $fix_fee = false;
			}

			if($fix_fee === true) {
				$arr_area['area_delivery'] += $_extra_fee;
				$fix_pay = true;
			}

			break;
		case '5' ://������ ��ۺ�
			$item_res = $db->_select('SELECT sum(ea) as ea FROM '.GD_ORDER_ITEM.' WHERE oi_delivery_idx='.$delivery['oi_delivery_idx']);

			$arr_area['area_delivery'] += $item_res[0]['ea'] * $_extra_fee;
			break;
		case '100' : //�̹� ��ϵ� ������ ��ۺ�� ����
				$db->_query('DELETE FROM '.GD_ORDER_ITEM_DELIVERY.' WHERE ordno='.$param['ordno'].' AND oi_delivery_idx='.$delivery['oi_delivery_idx']);
			break;
	}
}

if(isset($arr_area)) {
	$orderDeliveryItem = &load_class('orderDeliveryItem','orderDeliveryItem');
	$extra_data_idx = $orderDeliveryItem->extra_delivery($param['ordno'], $arr_area['area_delivery'], $arr_area['area_delivery']);

	$upd_query = $db->_query_print('UPDATE '.GD_ORDER_ITEM.' SET oi_area_idx=[i] WHERE ordno=[i]', $extra_data_idx, $param['ordno']);
	$area_rtn = $db->_query($upd_query);
}

if(!isset($arr_area)) {
	$arr_area = '0';
}

resposen_log($arr_area);
?>