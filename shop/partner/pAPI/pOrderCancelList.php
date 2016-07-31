<?
/*********************************************************
* 파일명     :  pOrderCancelList.php
* 프로그램명 :	pad 주문취소리스트 API
* 작성자     :  dn
* 생성일     :  2011.10.18
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
$arr_where[] = $db->_query_print('oi.istep >= [i] AND oi.istep <=[i]', 40, 49);

if(!empty($type) && is_array($type)) {
	$tmp_where = Array();
	foreach ($type as $v) {
		switch($v) {
			case '1': $tmp_where[] = $db->_query_print('(oi.cyn=[s] and oi.dyn=[s])', 'n', 'n'); break;
			case '2': $tmp_where[] = $db->_query_print('oi.cyn=[s]', 'y'); break;
			case '3': $tmp_where[] = $db->_query_print('oi.cyn=[s]', 'r'); break;
			case '4': $tmp_where[] = $db->_query_print('oi.dyn=[s]', 'y'); break;
			case '5': $tmp_where[] = $db->_query_print('(oi.dyn=[s] and oi.cyn=[s])', 'r', 'y'); break;
			case '6': $tmp_where[] = $db->_query_print('(oi.dyn=[s] and oi.cyn=[s])', 'r', 'r'); break;
			case '7': $tmp_where[] = $db->_query_print('(oi.dyn=[s] and oi.cyn=[s])', 'e', 'e'); break;
		}
	}
	$arr_where[] = '('.implode(' OR ', $tmp_where).')';
	unset($tmp_where);
}

if($skey == 'all' && $sword) {
	$tmp_where = Array();

	$tmp_where[] = $db->_query_print('o.ordno like "%'.$sword.'%"');
	$tmp_where[] = $db->_query_print('o.nameOrder like "%'.$sword.'%"');
	$tmp_where[] = $db->_query_print('o.bankSender like "%'.$sword.'%"');
	$tmp_where[] = $db->_query_print('m.m_id like "%'.$sword.'%"');

	$arr_where[] = '('.implode(' OR ', $tmp_where).')';
	unset($tmp_where);
}
else {
	if($skey && $sword) {

		switch($skey) {
			case 'ordno' : $alias = 'o.'; break;
			case 'nameOrder' : $alias = 'o.'; break;
			case 'bankSender' : $alias = 'o.'; break;
			case 'm_id' : $alias = 'm.'; break;
		}
		$arr_where[] = $db->_query_print($alias.$skey.' like "%'.$sword.'%"'); 
	}
}

if($g_skey && $g_sword) $arr_where[] = $db->_query_print('oi.'.$g_skey.' like "%'.$g_sword.'%"');
if($dtkind && $s_date && $e_date) $arr_where[] = $db->_query_print('(o.'.$dtkind.' >= "'.$s_date.' 00:00:00" AND o.'.$dtkind.' <= "'.$e_date.' 23:59:59")');

$where = implode(' AND ', $arr_where);
if(!$where) $where = '1=1';

$where = ' WHERE '.$where;

$count_item = '(select count(*) from '.GD_ORDER_ITEM.' as s_oi where s_oi.ordno=o.ordno) as count_item';
$order_query = $db->_query_print('
	SELECT
		o.orddt as orddt,
		oc.regdt as canceldt,
		oc.code as code,
		o.ordno as ordno,
		o.nameOrder as nameOrder,
		o.settlekind as settlekind,
		m.m_id as m_id,
		o.m_no as m_no,
		oi.goodsnm as goodsnm,
		oi.goodsno as goodsno,
		count(oi.goodsno) as count_goods,
		sum(oi.ea) as sea,
		sum((oi.price-oi.memberdc-oi.coupon)*oi.ea) as pay,
		o.step as step,
		oi.istep as istep,
		oi.sno as itemsno
	FROM '.GD_ORDER_CANCEL.' as oc
	INNER JOIN '.GD_ORDER_ITEM.' as oi ON oc.sno = oi.cancel and oc.ordno = oi.ordno
	INNER join '.GD_ORDER.' as o ON oi.ordno=o.ordno
	LEFT JOIN '.GD_MEMBER.' as m ON o.m_no=m.m_no
	'.$where.'
	GROUP BY oc.sno
	ORDER BY oc.regdt DESC
');
//debug($order_query);


$res_order = $db->_select_page($list, $page, $order_query);

$order_data = $res_order['record'];

$arr_settle_type = Array();
$res_data = Array();
if(!empty($order_data)) {
	foreach($order_data as $row_order) {
		if($row_order['count_goods'] > 1) {
			$row_order['goodsnm'] .= ' 외 '.(string)($row_order['count_goods'] - 1).'건';
		}

		$row_order['settlekind'] = $r_settlekind[$row_order['settlekind']];
		$row_order['stepmsg'] = getStepMsg($row_order['step'], $row_order['istep'], $row_order['ordno'], $row_order['itemsno']);//$r_step[$row_order['step']];
		$row_order['step2'] = $r_step2[$row_order['step2']];
		
		$r_cancel = codeitem('cancel');
		$row_order['r_cancel'] = $r_cancel[$row_order['code']];
		$res_data[] = $row_order;
	}
}


echo ($json->encode($res_data));

?>