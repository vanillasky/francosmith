<?
/*********************************************************
* 파일명     :  pOrderList.php
* 프로그램명 :	pad 주문리스트 API
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
		${$tmp_key} = $val;
	}
}

if(!$list) $list = 20;
if(!$page) $page = 1;

$arr_where = Array();

if($skey == 'all' && $sword) {
	$tmp_where = Array();

	$tmp_where[] = $db->_query_print('o.ordno like "%'.$sword.'%"');
	$tmp_where[] = $db->_query_print('o.nameOrder like "%'.$sword.'%"');
	$tmp_where[] = $db->_query_print('o.nameReceiver like "%'.$sword.'%"');
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
			case 'nameReceiver' : $alias = 'o.'; break;
			case 'bankSender' : $alias = 'o.'; break;
			case 'm_id' : $alias = 'm.'; break;
		}
		$arr_where[] = $db->_query_print($alias.$skey.' like "%'.$sword.'%"'); 
	}
}

if($step != '') {
	if(strlen($step) > 1) {
		$arr_where[] = $db->_query_print('o.step2=[i]', $step);
	}
	else {
		$arr_where[] = $db->_query_print('o.step=[i]', $step);
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
		o.ordno as ordno,
		o.nameOrder as nameOrder,
		o.nameReceiver as nameReceiver,
		o.settlekind as settlekind,
		o.step as step,
		o.step2 as step2,
		o.orddt as orddt,
		o.prn_settleprice as prn_settleprice,
		'.$count_item.',
		oi.goodsnm as goodsnm,
		m.m_id as m_id,
		m.m_no as m_no
	FROM '.GD_ORDER.' as o
	LEFT JOIN '.GD_ORDER_ITEM.' as oi ON o.ordno=oi.ordno
	LEFT JOIN '.GD_MEMBER.' as m ON o.m_no=m.m_no
	'.$where.'
	GROUP BY o.ordno
	ORDER BY o.orddt DESC
');
//debug($order_query);


$res_order = $db->_select_page($list, $page, $order_query);

$order_data = $res_order['record'];

$arr_settle_type = Array();
$res_data = Array();
if(!empty($order_data)) {
	foreach($order_data as $row_order) {
		if($row_order['count_item'] > 1) {
			$row_order['goodsnm'] .= ' 외 '.(string)($row_order['count_item'] - 1).'건';
		}

		$row_order['settlekind'] = $r_settlekind[$row_order['settlekind']];
		$row_order['stepmsg'] = getStepMsg($row_order['step'], $row_order['step2'], $row_order['ordno']);//$r_step[$row_order['step']];
		$row_order['step2'] = $r_step2[$row_order['step2']];
				
		$sdate = $row_order['orddt'];
		$edate = date('Y-m-d');

		$tmp_sdate = explode(' ', $sdate);
		$tmp_edate = explode(' ', $edate);

		$arr_sdate = explode('-', $tmp_sdate[0]);
		$arr_edate = explode('-', $tmp_edate[0]);

		$ts_sdate = mktime(0,0,0, $arr_sdate[1], $arr_sdate[2], $arr_sdate[0]);
		$ts_edate = mktime(0,0,0, $arr_edate[1], $arr_edate[2], $arr_edate[0]);

		$gap_day = floor(($ts_edate - $ts_sdate +1)/60/60/24);
		$str_gap_day = '';
		if($gap_day == 0) {
			$str_gap_day = '-';
		}
		else {
			$str_gap_day = $gap_day;
		}

		$row_order['flowdt'] = $str_gap_day;
		$res_data[] = $row_order;
	}
}


echo ($json->encode($res_data));

?>