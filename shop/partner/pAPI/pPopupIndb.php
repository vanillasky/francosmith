<?
/*********************************************************
* ���ϸ�     :  pPopupIndb.php
* ���α׷��� :	pad �˾�ó�� API
* �ۼ���     :  dn
* ������     :  2011.10.22
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

if(!($m_no = $pAPI->keyCheck($_POST['authentic'], 'm_no'))) {
	$res_data['code'] = '302';
	$res_data['msg'] = '����Ű�� ���� �ʽ��ϴ�.';
	echo ($json->encode($res_data));
	exit;
}

unset($_POST['authentic']);
### ����Ű Check �� ###

$mode = $_POST['mode'];
unset($_POST['mode']);
if(!$mode) {
	$res_data['code'] = '301';
	$res_data['msg'] = '�߸��� ���� �Դϴ�.';
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

$file	= dirname(__FILE__)."/../../conf/godomall.cfg.php";
if (!is_file($file)) {
	$res_data['result']['code'] = '300';
	$res_data['result']['msg'] = '�� ���������� �����ϴ�. ���������� ����ϼ���';
	echo ($json->encode($res_data));
	exit;
}

$file	= file($file);
$godo	= decode($file[1],1);

switch($mode) {

	case "del_main_popup" :
		$no = $_POST['no'];
		$query = $db->_query_print('DELETE FROM '.GD_SHOPTOUCH_DISPLAY.' WHERE no=[i] AND mode=[s]', $no, 'popup');
		$db->query($query);
		
		$res_data['code'] = '000';
		$res_data['msg'] = '����';		
		
		break;
	
	case "main_popup_use":

		$arr_no = $_POST['no'];
		$tmp_display = $_POST['use_display'];
		
		$select_display = $db->_query_print('SELECT no FROM '.GD_SHOPTOUCH_DISPLAY.' WHERE mode=[s]', 'popup');
		$res_display = $db->_select($select_display);

		if(!empty($res_display) && is_array($res_display)) {
			foreach($res_display as $row_display) {
				$use_display = ($_POST['no'] == $row_display['no']) ? '1' : '0';
				$query = $db->_query_print('UPDATE '.GD_SHOPTOUCH_DISPLAY.' SET use_display=[s] WHERE no=[i]', $use_display, $row_display['no']);
				$db->query($query);				

			}
		}

		if($_POST['no'] != '0') {
			$arr['notice'] = 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_disp/popup.php';
		}
		else {
			$arr['notice'] = '';

		}
		
		$arr['shop_nm'] = $cfg['shopName'];
		$arr['login'] = 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_mem/login.php';
		$arr['logout'] = 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_mem/logout.php';

		$arr['info'] = 'http://'.$_SERVER['HTTP_HOST'].'/shopTouch/shopTouch_disp/company_info.php';

		$json_data = $pAPI->setShopInfo($godo['sno'],$arr);

		$data = $json->decode($json_data);

		
		$res_data['code'] = '000';
		$res_data['msg'] = '����';		
		
		break;
}

echo ($json->encode($res_data));