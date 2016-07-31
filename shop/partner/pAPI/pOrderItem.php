<?
/*********************************************************
* 파일명     :  pOrderItem.php
* 프로그램명 :	pad 주문아이템 API
* 작성자     :  dn
* 생성일     :  2011.10.22
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


$ordno = $_POST['ordno'];

$ord_query = $db->_query_print('SELECT a.*, b.m_id FROM '.GD_ORDER.' a LEFT JOIN '.GD_MEMBER.' b ON a.m_no=b.m_no WHERE ordno=[i]', $ordno);
$res_ord = $db->_select($ord_query);
$row_ord = $res_ord[0];

$row_ord['stepmsg'] = getStepMsg($row_ord['step'],$row_ord['step2'],$row_ord['ordno']);

$new_ord_query = $db->_query_print('SELECT ordno FROM '.GD_ORDER.' WHERE oldordno=[i]', $ordno);
$res_new_ord = $db->_select($new_ord_query);
$row_ord['n_ordno'] = $res_new_ord[0]['ordno'];

$ord_item_query = $db->_query_print('SELECT a.*, b.*, tg.tgsno FROM '.GD_ORDER_ITEM.' a LEFT JOIN '.GD_GOODS.' b ON a.goodsno=b.goodsno LEFT JOIN '.GD_TODAYSHOP_GOODS.' tg ON a.goodsno=tg.goodsno WHERE a.ordno=[i] ORDER BY a.sno', $ordno);
$res_ord_item = $db->_select($ord_item_query);

if(!empty($res_ord_item) && is_array($res_ord_item)) {
	foreach($res_ord_item as $row_ord_item) {
		$row_ord_item['img_html'] = goodsimg($row_ord_item['img_s'],30,"style='border:solid 1px #CCCCCC'",4);

		$tmp_ord_item[$row_ord_item['sno']] = $row_ord_item;
	}
}

$ord_coupon_query = $db->_query_print('SELECT a.*, c.couponcd, c.goodsnm FROM '.GD_COUPON_ORDER.' a LEFT JOIN '.GD_ORDER_ITEM.' b ON a.goodsno=b.goodsno AND a.ordno=b.ordno LEFT JOIN '.GD_COUPON_APPLY.' c ON a.applysno=c.sno WHERE a.ordno=[i]', $ordno);
$res_ord_coupon = $db->_select($ord_coupon_query);

if(!empty($res_ord_coupon) && is_array($res_ord_coupon)) {
	foreach($res_ord_coupon as $row_ord_coupon) {
		
		if($row_ord_coupon['downloadsno']) {
			$off_cp_query = $db->_query_print('SELECT p.number FROM '.GD_OFFLINE_DOWNLOAD.' d LEFT OUTER JOIN '.GD_OFFLINE_PAPER.' p ON d.paper_sno=p.sno WHERE d.sno=[i]', $row_ord_coupon['donwloadsno']);
			$res_cp = $db->_select($off_cp_query);
			$row_cp = $res_cp[0];
			$row_ord_coupon['couponcd'] = $row_cp['number'];
		}	
		$tmp_ord_coupon[] = $row_ord_coupon;
	}
}

if($row_ord['cbyn'] == 'Y') {
	$ord_cashbag_query = $db->_query_print('SELECT * FROM gd_order_okcashbag WHERE ordno=[i]', $ordno);
	$res_ord_cashbag = $db->_select($ord_cashbag_query);
}

$ord_refund_query = $db->_query_print('SELECT DISTINCT a.cancel, b.* FROM '.GD_ORDER_ITEM.' a LEFT JOIN '.GD_ORDER_CANCEL.' b ON a.cancel=b.sno WHERE a.istep=[i] AND a.cyn IN ([s],[s]) AND a.ordno=[i] AND (b.rprice OR b.remoney OR b.rfee)', 44, 'r', 'y', $ordno);
$res_ord_refund = $db->_select($ord_refund_query);
if(!empty($res_ord_refund) && is_array($res_ord_refund)) {
	foreach($res_ord_refund as $row_ord_refund) {
		
		$refund_item_query = $db->_query_print('SELECT * FROM '.GD_ORDER_ITEM.' WHERE cancel=[i]', $row_ord_refund['cancel']);
		$res_refund_item = $db->_select($refund_item_query);
		
		$row_ord_refund['item_res'] = $res_refund_item;
		
		$tmp_ord_refund[] = $row_ord_refund;
	}
}

$ord_tax_query = $db->_query_print('SELECT regdt, agreedt, printdt, price, step, doc_number FROM '.GD_TAX.' WHERE ordno=[i] ORDER BY sno DESC  LIMIT 1', $ordno);
$res_ord_tax = $db->_select($ord_tax_query);
$row_ord_tax = $res_ord_tax[0];

$res_data = Array();

$res_data['order'] = $row_ord;
$res_data['order_item'] = $tmp_ord_item;
$res_data['order_tax'] = $row_ord_tax;
$res_data['order_coupon'] = $tmp_ord_coupon;
$res_data['order_cashbag'] = $res_ord_cashbag;
$res_data['order_refund'] = $tmp_ord_refund;

echo ($json->encode($res_data));
