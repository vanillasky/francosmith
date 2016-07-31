<?
/*********************************************************
* ���ϸ�     :  pCategory.php
* ���α׷��� :	ī�װ� API
* �ۼ���     :  dn
* ������     :  2011.10.17
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

if(!$_POST['depth']) $_POST['depth']= '1';

if(!empty($_POST)) {
	foreach($_POST as $key => $val) {
		${$key} = $val;
	}
}

$length = (int)$depth * 3;

$arr_where[] = $db->_query_print('LENGTH(category)=[i]', $length);
if($category) $arr_where[] = $db->_query_print('category like "'.$category.'%"');

$where = implode(' AND ', $arr_where);

$category_query = $db->_query_print('SELECT category, catnm FROM '.GD_CATEGORY.' WHERE '.$where.' ORDER BY sort');

$res_category = $db->_select($category_query);

$res_data = $res_category;
echo ($json->encode($res_data));

?>