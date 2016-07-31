<?
/*********************************************************
* 파일명     :  pCode.php
* 프로그램명 :	코드값 API
* 작성자     :  dn
* 생성일     :  2011.10.17
**********************************************************/
include "../../lib/library.php";
include "../../conf/config.php";
include "../../conf/config.pay.php";
require_once "../../lib/pAPI.class.php";
require_once "../../lib/json.class.php";

$pAPI = new pAPI();
$json = new Services_JSON(16);

$code_name = $_POST['code_name'];
if($code_name != 'shop_name') {
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
}

if(!empty($_POST)) {
	foreach($_POST as $key => $val) {
		${$key} = $val;
	}
}

switch($code_name) {
	case 'step' :
		$i = 0;
		foreach($r_step as $k => $v){
			if($v) {
				$res_data[$i]['code'] = $k;
				$res_data[$i]['nm'] = $v;
				$i++;
			}
		}
		break;

	case 'step2' :
		$i = 0;
		foreach($r_step2 as $k => $v){
			if($v) {
				$res_data[$i]['code'] = $k;
				$res_data[$i]['nm'] = $v;
				$i++;
			}
		}
		break;
	
	case 'settlekind' :
		$i = 0;
		foreach($r_settlekind as $k => $v){
			if($v) {
				$res_data[$i]['code'] = $k;
				$res_data[$i]['nm'] = $v;
				$i++;
			}
		}
		break;

	case 'bank' :
		$bank_query = $db->_query_print('SELECT * FROM '.GD_LIST_BANK.' ORDER BY useyn asc, sno');
		$res_bank = $db->_select($bank_query);
		
		if(!empty($res_bank) && is_array($res_bank)) {
			$i = 0;
			foreach ($res_bank as $row_bank) {
				$res_data[$i]['code'] = $row_bank['sno'];
				$res_data[$i]['nm'] = $row_bank['account'].' '.$row_bank['name'];
				$i++;
			}
		}
		break;

	case 'istep' :
		$i = 0;
		foreach($r_istep as $k => $v){
			if($v) {
				$res_data[$i]['code'] = $k;
				$res_data[$i]['nm'] = $v;
				$i++;
			}
		}
		break;
	
	case 'delivery' :
		$delivery_query = $db->_query_print('SELECT * FROM '.GD_LIST_DELIVERY.' WHERE useyn=[s] ORDER BY deliverycomp', 'y');
		$res_delivery = $db->_select($delivery_query);
		
		if(!empty($res_delivery) && is_array($res_delivery)) {
			$i = 0;
			foreach ($res_delivery as $row_delivery) {
				$res_data[$i]['code'] = $row_delivery['deliveryno'];
				$res_data[$i]['nm'] = $row_delivery['deliverycomp'];
				$i++;
			}
		}
		break;

	case 'inflow' :
		$i = 0;
		foreach($r_inflow as $k => $v){
			if($v) {
				$res_data[$i]['code'] = $k;
				$res_data[$i]['nm'] = $v;
				$i++;
			}
		}
		break;
	
	case 'cancel_reason' :
		$i = 0;
		foreach(codeitem('cancel') as $k=>$v) {
			if($v) {
				$res_data[$i]['code'] = $k;
				$res_data[$i]['nm'] = $v;
				$i++;
			}
		}
		break;

	case 'cancel_bank' :
		$i = 0;
		foreach(codeitem('bank') as $k=>$v) {
			if($v) {
				$res_data[$i]['code'] = $k;
				$res_data[$i]['nm'] = $v;
				$i++;
			}
		}
		break;
	
	case 'cfg' :
		foreach($cfg as $k=>$v) {
			if($v) {
				$res_data[$i]['code'] = $k;
				$res_data[$i]['nm'] = $v;
				$i++;
			}
		}
		break;
	
	case 'set' :
		foreach($set as $k=>$v) {
			if($v) {
				$res_data[$i]['code'] = $k;
				$res_data[$i]['nm'] = $v;
				$i++;
			}
		}
		break;

	case 'default_delivery' :
		$delivery_query = $db->_query_print('SELECT * FROM '.GD_LIST_DELIVERY.' WHERE useyn=[s] AND deliveryno <> [i] ORDER BY deliverycomp', 'y', 100);
		$res_delivery = $db->_select($delivery_query);
		
		if(!empty($res_delivery) && is_array($res_delivery)) {
			$i = 0;
			foreach ($res_delivery as $row_delivery) {
				$res_data[$i]['code'] = $row_delivery['deliveryno'];
				$res_data[$i]['nm'] = $row_delivery['deliverycomp'];
				$i++;
			}
		}
		break;
	
	case 'point' :
		$i = 0;
		foreach(codeitem('point') as $k=>$v) {
			if($v) {
				$res_data[$i]['code'] = $k;
				$res_data[$i]['nm'] = $v;
				$i++;
			}
		}
		break;

	case 'question' :
		$i = 0;
		foreach(codeitem('question') as $k=>$v) {
			if($v) {
				$res_data[$i]['code'] = $k;
				$res_data[$i]['nm'] = $v;
				$i++;
			}
		}
		break;

	case 'shop_name' :
		$i = 0;
		$res_data[$i]['code'] = 'shopName';
		$res_data[$i]['nm'] = $cfg['shopName'];
		break;
}

echo ($json->encode($res_data));

?>