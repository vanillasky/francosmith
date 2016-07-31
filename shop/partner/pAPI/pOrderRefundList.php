<?
/*********************************************************
* 파일명     :  pOrderRefundList.php
* 프로그램명 :	pad 환불리스트 API
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
$arr_where[] = $db->_query_print('oi.istep > [i] AND oi.cyn=[s] AND (oi.dyn=[s] OR oi.dyn=[s])', 40, 'y', 'n', 'r');

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
		unset($alias);
	}
}
if($settlekind) $arr_where[] = $db->_query_print('o.settlekind=[s]', $settlekind);
if($bankcode) $arr_where[] = $db->_query_print('oc.bankcode=[i]', $bankcode);
if($s_date && $e_date){
	switch($dtkind) {
		case 'orddt' : $alias = 'o.'; break;
		case 'cdt' : $alias = 'oc.'; break;
	}
	$arr_where[] = $db->_query_print('('.$alias.$dtkind.' >= "'.$s_date.' 00:00:00" AND '.$alias.$dtkind.' <= "'.$e_date.' 23:59:59")');
	unset($alias);
}

$where = implode(' AND ', $arr_where);
if(!$where) $where = '1=1';

$where = ' WHERE '.$where;

$order_query = $db->_query_print('
	SELECT 
		oc.sno as sno,
		oc.regdt as canceldt,
		oc.name as nameCancel,
		oc.bankcode as bankcode,
		oc.bankaccount as bankaccount,
		oc.bankuser as bankuser,
		sum((oi.price-oi.memberdc-oi.coupon) * oi.ea) as repay,
		count(*) cancelCnt,
		oc.code as code,
		o.ordno as ordno,
		o.orddt as orddt,
		o.nameOrder as nameOrder,
		o.settlekind as settlekind,
		o.settleprice as settleprice,
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
		
		$item_query = $db->_query_print('SELECT count(*) as ordCnt FROM '.GD_ORDER_ITEM.' WHERE ordno=[i]', $row_order['ordno']);
		$res_item = $db->_select($item_query);
		
		$row_order['ordCnt'] = $res_item[0]['ordCnt'];		
			
		$r_cancel = codeitem('cancel');
		$row_order['r_cancel'] = $r_cancel[$row_order['code']];

		$row_order['r_settlekind'] = $r_settlekind[$row_order['settlekind']];
		$res_data[] = $row_order;
	}
}


echo ($json->encode($res_data));

?>