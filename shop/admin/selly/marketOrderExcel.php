<?
/*********************************************************
* 파일명     :  marketOrderExcel.php
* 프로그램명 :  마켓주문엑셀리스트
* 작성자     :  dn
* 생성일     :  2012.05.26
**********************************************************/
$location = "셀리 > 마켓주문관리";
header('Content-type: application/vnd.ms-excel; charset=euc-kr');
header('Content-Disposition: attachment; filename=MarketOrder_'.date('Y-m-d H:i:s',time()).'.xls');
include dirname(__FILE__)."/../lib.php";

include "../../lib/sAPI.class.php";

$sAPI = new sAPI();

$search = $_GET;
$page = (int)$_GET['page'] ? (int)$_GET['page'] : 1;
unset($_GET);

$code_arr['grp_cd'] = 'order_status';
$tmp_order_status = $sAPI->getcode($code_arr, 'hash');
unset($code_arr);
if(is_array($tmp_order_status) && !empty($tmp_order_status)) {
	foreach($tmp_order_status as $key_order_status => $val_order_status) {
		$order_status[$key_order_status]['code'] = $key_order_status;
		$order_status[$key_order_status]['code_nm'] = $val_order_status;

		if($key_order_status == '0020' || $key_order_status == '0030' ||$key_order_status == '0022' || $key_order_status == '0032' || $key_order_status == '0042' || $key_order_status == '0043' || $key_order_status == '0044') {
			$send_order_status[$key_order_status]['code'] = $key_order_status;
			$send_order_status[$key_order_status]['code_nm'] = $val_order_status;
		}
		
	}
}

foreach($order_status as $row_order_status){
	$nowsts[$row_order_status['code']] = ($row_order_status['code'] == $search['status'])? 'on_sts' : 'sts';
}

$code_arr['grp_cd'] = 'mall_cd';
$mall_cd = $sAPI->getcode($code_arr, 'hash');
unset($code_arr);

if($search['search_date'][0]) $search['search_date_start'] = $search['search_date'][0];
if($search['search_date'][1]) $search['search_date_end'] = $search['search_date'][1];

$arr_where = array();

if($search['search_date_start']) {
	if(!$search['search_date_end']) $search['search_date_end'] = date('Ymd');

	$tmp_start = date('Y-m-d 00:00:00', mktime(0, 0, 0, substr($search['search_date_start'],4,2), substr($search['search_date_start'],6,2), substr($search['search_date_start'],0,4)));
	$tmp_end = date('Y-m-d 23:59:59', mktime(0, 0, 0, substr($search['search_date_end'],4,2), substr($search['search_date_end'],6,2), substr($search['search_date_end'],0,4)));
	
	$arr_where[] = $db->_query_print($search['search_date_type']. ' >=[s] AND '.$search['search_date_type'].' <=[s]', $tmp_start, $tmp_end);
}

if($search['sword']) {
	$tmp_sword = '%'.$search['sword'].'%';
	$arr_where[] = $db->_query_print($search['search_key_type']. ' LIKE [s] ', $tmp_sword);
}

if(empty($search['mall_cd'])) {
	$search['mall_cd'][0] = 'all';
}

if($search['mall_cd'][0] == 'all') {
	$checked['mall_cd']['all'] = 'checked';
	foreach($mall_cd as $key_mall_cd => $val_mall_cd) {
		$checked['mall_cd'][$key_mall_cd] = 'checked';
	}
}
else {
	$tmp_mall_cd = array();
	foreach($search['mall_cd'] as $val_mall_cd) {
		$checked['mall_cd'][$val_mall_cd] = 'checked';
		$tmp_mall_cd[] = $val_mall_cd;
	}

	$arr_where[] = $db->_query_print('om.mall_cd IN [v]', $tmp_mall_cd);

}

if(empty($search['send_yn'])) {
	$search['send_yn'][0] = 'all';
}

if($search['send_yn'][0] == 'all') {
	$checked['send_yn']['all'] = 'checked';
	$checked['send_yn']['none'] = 'checked';
	$checked['send_yn']['N'] = 'checked';
	$checked['send_yn']['Y'] = 'checked';
}
else {
	$tmp_send_yn = array();
	foreach($search['send_yn'] as $val_send_yn) {
		$checked['send_yn'][$val_send_yn] = 'checked';
		if($val_send_yn == 'none') {
			$tmp_arr_where[] = $db->_query_print('om.send_yn IS NULL');
		}
		else {
			$tmp_send_yn[] = $val_send_yn;
		}		
	}
	
	if(!empty($tmp_send_yn)) $tmp_arr_where[] = $db->_query_print('om.send_yn IN [v]', $tmp_send_yn);
	
	$arr_where[] = '('.implode(' OR ', $tmp_arr_where).')';
}

if($search['status']) $arr_where[] = $db->_query_print('om.status=[s]', $search['status']);

if(empty($arr_where)) $arr_where[] = '1=1';
$where = implode(' AND ', $arr_where);


$order_query = $db->_query_print('SELECT om.order_no, om.reg_date, om.order_idx, om.mall_cd, om.mall_login_id, om.mall_goods_cd, om.mall_order_no, om.mall_goods_nm, om.order_nm, om.receive_nm, om.settle_price, om.status, om.delivery_cd, om.delivery_no, om.exchange_delivery_cd, om.exchange_delivery_no FROM '.GD_MARKET_ORDER.' om WHERE '.$where.' ORDER BY om.reg_date DESC');

$result = $db->_select($order_query);
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr" />
<style>
th { background-color:#C7C7C7; }
th, td { padding:3px 7px; text-align:center; mso-number-format:'@'; border:solid 1px #F7F7F7;}
td { height:60px; }
.num, .date { font-size:11px; font-family:verdana; }
.num { text-align:right; }
</style>
</head>

<body>
<table>
<thead>
<tr>
	<th>주문번호</th>
	<th>마켓</th>
	<th>마켓주문번호</th>
	<th>마켓상품명</th>
	<th>주문자</th>
	<th>받는분</th>
	<th>배송사코드</th>
	<th>송장번호</th>
	<th>교환배송사코드</th>
	<th>교환송장번호</th>
</tr>
</thead>
<? if(empty($result) || !is_array($result)) { ?>
<tbody id="tbody_list">
<tr><td align="center" colspan="10"><!--데이터가 없습니다.-->데이터가 없습니다.</td></tr>
</tbody>
<?
	}
	else {
		echo '<tbody id="tbody_list">';
		foreach($result as $data) {
?>
<tr>
	<td style="text-align:left;"><?=$data['order_no']?></td>
	<td><?=$mall_cd[$data['mall_cd']]?></td>
	<td style="text-align:left;"><?=$data['mall_order_no']?></td>
	<td style="text-align:left;"><?=$data['mall_goods_nm']?></td>
	<td style="text-align:left;"><?=$data['order_nm']?></td>
	<td style="text-align:left;"><?=$data['receive_nm']?></td>
	<td style="text-align:left;"><?=$data['delivery_cd']?></td>
	<td style="text-align:left;"><?=$data['delivery_no']?></td>
	<td style="text-align:left;"><?=$data['exchange_delivery_cd']?></td>
	<td style="text-align:left;"><?=$data['exchange_delivery_no']?></td>
</tr>
<?
		}
		echo '</tbody>'.chr(10);
	}
?>
</tbody>
</table>
</body>
</html>