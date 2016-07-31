<?
/*********************************************************
* 파일명     :  pOrderReturnList.php
* 프로그램명 :	pad 반품리스트 API
* 작성자     :  dn
* 생성일     :  2011.10.27
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


if(!empty($_POST)) {
	foreach($_POST as $key => $val) {

		$tmp_key = str_replace('search_', '', $key);
		if(strstr($tmp_key, 'arr_')) {
			$n_tmp_key = str_replace('arr_', '', $tmp_key);
			${$n_tmp_key} = explode('|', $val);
		}
		else  {
			${$tmp_key} = $val;
		}
	}
}

if(!$list) $list = 20;
if(!$page) $page = 1;

$arr_where = Array();
$arr_where[] = $db->_query_print('oi.istep >= [i] AND oi.cyn=[s] AND oi.dyn=[s]', 40, 'y', 'y');

if($skey == 'all' && $sword) {
	$tmp_where = Array();

	$tmp_where[] = $db->_query_print('o.ordno like "%'.$sword.'%"');
	$tmp_where[] = $db->_query_print('o.nameOrder like "%'.$sword.'%"');
	$tmp_where[] = $db->_query_print('oi.goodsnm like "%'.$sword.'%"');
	$tmp_where[] = $db->_query_print('oc.name like "%'.$sword.'%"');

	$arr_where[] = '('.implode(' OR ', $tmp_where).')';
	unset($tmp_where);
}
else {
	if($skey && $sword) {

		switch($skey) {
			case 'ordno' : $alias = 'o.'; break;
			case 'nameOrder' : $alias = 'o.'; break;
			case 'goodsnm' : $alias = 'oi.'; break;
			case 'name' : $alias = 'oc.'; break;
		}
		$arr_where[] = $db->_query_print($alias.$skey.' like "%'.$sword.'%"'); 
	}
}

if($cancel_key) $arr_where[] = $db->_query_print('oc.code=[s]', $cancel_key);
if($s_date && $e_date) $arr_where[] = $db->_query_print('(oc.regdt >= "'.$s_date.' 00:00:00" AND oc.regdt <= "'.$e_date.' 23:59:59")');

$where = implode(' AND ', $arr_where);
if(!$where) $where = '1=1';

$where = ' WHERE '.$where;

$order_query = $db->_query_print('
	SELECT SQL_CALC_FOUND_ROWS
		oc.sno as sno,
		oc.regdt as canceldt,
		oc.name as nameCancel,
		oc.code as code,
		o.ordno as ordno,
		o.orddt as orddt,
		o.nameOrder as nameOrder,
		o.settlekind as settlekind,
		m.m_no as m_no,
		m.m_id as m_id
	FROM '.GD_ORDER_CANCEL.' as oc
	INNER JOIN '.GD_ORDER_ITEM.' as oi ON oc.sno = oi.cancel and oc.ordno = oi.ordno
	INNER join '.GD_ORDER.' as o ON oi.ordno=o.ordno
	LEFT JOIN '.GD_MEMBER.' as m ON o.m_no=m.m_no
	'.$where.'
	GROUP BY oc.sno
	ORDER BY oc.sno DESC
');
//debug($order_query);


$res_order = $db->_select_page($list, $page, $order_query);

$order_data = $res_order['record'];

$arr_settle_type = Array();
$res_data = Array();
if(!empty($order_data)) {
	foreach($order_data as $row_order) {
		
		$item_query = $db->_query_print('SELECT b.*, a.* FROM '.GD_ORDER_ITEM.' a LEFT JOIN '.GD_GOODS.' b ON a.goodsno=b.goodsno WHERE a.cancel=[i] AND a.ordno=[i]', $row_order['sno'], $row_order['ordno']);
		$res_item = $db->_select($item_query);
		
		$row_order['cancel_item'] = Array();
		
		$ii = 0;
		foreach($res_item as $row_item) {
			$row_roder['cancel_item'][$ii]['i_goodsno'] = $row_item['goodsno'];
			$row_roder['cancel_item'][$ii]['i_goodsnm'] = $row_item['goodsnm'];
			$goodsnm_opt = "";
			$goodsnm_opt .= $row_item['goodsnm'];
			if($row_item['opt1']) $goodsnm_opt .= '['.$row_item['goodsnm'];
			if($row_item['opt2']) $goodsnm_opt .= '/'.$row_item['goodsnm'];
			if($row_item['opt1'] || $row_item['opt2']) $goodsnm_opt .= ']';
			if($row_item['addopt']) $goodsnm_opt .= '<br />['.str_replace('^', '] [', $row_item['addopt']).']';

			$row_order['cancel_item'][$ii]['i_goodsnm_opt'] = $goodsnm_opt;
			$row_order['cancel_item'][$ii]['i_price'] = $row_item['price'];
			$row_order['cancel_item'][$ii]['i_memberdc'] = $row_item['memberdc'];
			$row_order['cancel_item'][$ii]['i_coupon'] = $row_item['coupon'];
			$row_order['cancel_item'][$ii]['i_settleprice'] = $row_item['price'] - $row_item['memberdc'] - $row_item['coupon'];
			$row_order['cancel_item'][$ii]['i_ea'] = $row_item['ea'];
		}
		
		$r_cancel = codeitem('cancel');
		$row_order['r_cancel'] = $r_cancel[$row_order['code']];

		$res_data[] = $row_order;
	}
}


echo ($json->encode($res_data));

?>