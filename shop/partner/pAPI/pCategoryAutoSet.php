<?
/*********************************************************
* ���ϸ�     :  pCategoryAutoSet.php
* ���α׷��� :	ī�װ� �ڵ����� API
* �ۼ���     :  dn
* ������     :  2012.02.08
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

### �� ���� ȭ�� 
$file	= dirname(__FILE__)."/../../conf/godomall.cfg.php";
$file	= file($file);
$godo	= decode($file[1],1);

$select_query_1depth = $db->_query_print('SELECT category, catnm, sort, hidden FROM '.GD_CATEGORY.' WHERE length(category)=[i] ORDER BY sort ASC', 3);
$res_1depth = $db->_select($select_query_1depth);

if(!empty($res_1depth) && is_array($res_1depth)) {
	foreach($res_1depth as $row_1depth) {
		
		$arr['menu_name'] = $row_1depth['catnm'];
		$arr['parent_idx'] = '0';				
		$json_res = $pAPI->mainMenuAdd($godo['sno'], $arr);

		
		$res = $json->decode($json_res);
		unset($arr);
		if($res['result']['code'] != '000') {
			$res_data['code'] = '300';
			$res_data['msg'] = 'ī�װ� ������ ������ �߻� �Ͽ����ϴ�';
			echo ($json->encode($res_data));
			exit;
		}
		else {
			
			$parent_idx = $res['menu_idx'];

			$map_arr = Array();
			$tmp_arr = Array();
			$json_arr = Array();
			
			$tmp_arr['type'] = 'category';
			$tmp_arr['value'] = $row_1depth['category'];
			$json_arr['data'][] = $tmp_arr;	

			$map_arr['menu_idx'] = $parent_idx;	
			$map_arr['data'] = $json->encode($json_arr);
			
			$json_res = $pAPI->menuTemplateAdd($godo['sno'], $map_arr);
			
			unset($tmp_arr, $map_arr, $json_arr);
		}

		$category_1depth = $row_1depth['category'].'%';
		$select_query_2depth = $db->_query_print('SELECT category, catnm, sort, hidden FROM '.GD_CATEGORY.' WHERE length(category)=[i] AND category like [s] ORDER BY sort ASC', 6, $category_1depth);

		$res_2depth = $db->_select($select_query_2depth);

		if(!empty($res_2depth) && is_array($res_2depth)) {
			foreach($res_2depth as $row_2depth) {
				
				$arr['menu_name'] = $row_2depth['catnm'];
				$arr['parent_idx'] = $parent_idx;
				$json_sub_res = $pAPI->mainMenuAdd($godo['sno'], $arr);
				$sub_res = $json->decode($json_sub_res);
				unset($arr);
				if($sub_res['result']['code'] != '000') {
					$res_data['code'] = '300';
					$res_data['msg'] = 'ī�װ� ������ ������ �߻� �Ͽ����ϴ�';
					echo ($json->encode($res_data));
					exit;
				}
				else {
					$child_idx = $sub_res['menu_idx'];

					$map_arr = Array();
					$tmp_arr = Array();
					$json_arr = Array();
					
					$tmp_arr['type'] = 'category';
					$tmp_arr['value'] = $row_2depth['category'];
					$json_arr['data'][] = $tmp_arr;	

					$map_arr['menu_idx'] = $child_idx;	
					$map_arr['data'] = $json->encode($json_arr);

					$json_res = $pAPI->menuTemplateAdd($godo['sno'], $map_arr);
					unset($map_arr);

				}
			}
		}
	}

	$res_data['code'] = '000';
	$res_data['msg'] = 'SUCCESS';
	echo ($json->encode($res_data));
	exit;
}
else {
	$res_data['code'] = '306';
	$res_data['msg'] = 'e���� ī�װ��� �����ϴ�';
	echo ($json->encode($res_data));
	exit;
}



?>