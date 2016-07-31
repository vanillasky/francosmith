<?
/*********************************************************
* ���ϸ�     :  pOrderRefundItem.php
* ���α׷��� :	pad ȯ���ֹ� ������ API
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

if(!$pAPI->keyCheck($_POST['authentic'])) {
	$res_data['code'] = '302';
	$res_data['msg'] = '����Ű�� ���� �ʽ��ϴ�.';
	echo ($json->encode($res_data));
	exit;
}
unset($_POST['authentic']);
### ����Ű Check �� ###

$sno = $_POST['sno'];

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
		o.ncash_emoney as ncash_emoney,
		o.ncash_cash as ncash_cash,
		oc.rncash_emoney as rncash_emoney,
		oc.rncash_cash as rncash_cash,
		o.goodsprice as goodsprice,
		o.delivery as delivery,
		o.coupon as coupon,
		o.emoney as emoney,
		o.memberdc as memberdc,
		o.enuri as enuri,
		o.eggFee as eggFee,
		o.escrowyn as escrowyn,
		o.pgcancel as pgcancel,
		m.m_no as m_no,
		m.m_id as m_id
	FROM '.GD_ORDER_CANCEL.' as oc
	INNER JOIN '.GD_ORDER_ITEM.' as oi ON oc.sno = oi.cancel and oc.ordno = oi.ordno
	INNER join '.GD_ORDER.' as o ON oi.ordno=o.ordno
	LEFT JOIN '.GD_MEMBER.' as m ON o.m_no=m.m_no
	WHERE oc.sno=[i]
	GROUP BY oc.sno
	ORDER BY oc.sno DESC
', $sno);

$res_ord = $db->_select($order_query);
$row_ord = $res_ord[0];

$cnt_query = $db->_query_print('SELECT count(*) as ordCnt, IFNULL(SUM(CASE WHEN cancel != "" && cancel >=[i] THEN 1 END), "0") as cCnt FROM '.GD_ORDER_ITEM.' WHERE ordno=[i]', $sno, $row_ord['ordno']);
$res_cnt = $db->_select($cnt_query);

$row_ord['ordCnt'] = $res_cnt[0]['ordCnt'];
$row_ord['cCnt'] = $res_cnt[0]['cCnt'];

// ��ҵ� ���̹� ���ϸ����� ĳ���� �ִ°�� ȯ�ұݿ��� ����
if((int)$row_ord['rncash_emoney'] || (int)$row_ord['rncash_cash'])
{
	$row_ord['repay'] -= $row_ord['rncash_emoney'] + $row_ord['rncash_cash'];
}

// ������ ��ҵ� ���̹� ���ϸ���, ĳ�� ��ȸ
list($rncash_emoney, $rncash_cash) = $db->fetch("SELECT SUM(`rncash_emoney`), SUM(`rncash_cash`) FROM `gd_order_cancel` WHERE `ordno`=".$row_ord['ordno']);

// ������ ��ҵ� ���̹� ���ϸ����� ĳ���� �ִ°�� �����ݿ� �߰�
if((int)$rncash_emoney || (int)$rncash_cash)
{
	$row_ord['settleprice'] += ($rncash_emoney - $row_ord['rncash_emoney']) + ($rncash_cash - $row_ord['rncash_cash']);
}

$row_ord['ncash_emoney'] += $rncash_emoney;
$row_ord['ncash_cash'] += $rncash_cash;

if($row_ord['settleprice'] >= $row_ord['repay']) {
	$repay = $row_ord['repay'];
	$repaymsg = '��ǰ�����ܰ�';

	if($row_ord['cCnt'] == $row_ord['ordCnt']) {
		$repaymsg = "��ǰ�����ܰ� + ��۷� - ������ - ������ + �������������";
		$repay = $repay + $row_ord['delivery'] - $row_ord['enuri'] - $row_ord['emoney'] + $row_ord['eggFee'];
	}
	if((int)$row_ord['ncash_emoney']) $repaymsg .= " - ���̹����ϸ���";
	if((int)$row_ord['ncash_cash']) $repaymsg .= " - ���̹�ĳ��";
}
else {
	$repay = $row_ord['repay'];
}
if($row_ord['cancelCnt'] == $row_ord['ordCnt']) $repaymsg = '�� �����ݾ�';
if($repay < 0) $repay = 0;
$repayfee = getRepayFee($repay);
if($row_ord['settleprice'] < $row_ord['repay']) $remoney = $row_ord['repay'] - $repay;

$row_ord['repay'] = $repay;
$row_ord['repaymsg'] = $repaymsg;
$row_ord['repayfee'] = $repayfee;
$row_ord['remoney'] = $remoney;

$emoney_query = $db->_query_print('SELECT SUM(remoney) agoemoney FROM '.GD_ORDER_ITEM.' a LEFT JOIN '.GD_ORDER_CANCEL.' b on a.cancel = b.sno WHERE a.ordno=[i]', $row_ord['ordno']);
$res_emoney = $db->_select($emoney_query);
$row_ord['agoemoney'] = $res_emoney[0]['agoemoney'];

$ord_item_query = $db->_query_print('SELECT a.*, b.*, tg.tgsno FROM '.GD_ORDER_ITEM.' a LEFT JOIN '.GD_GOODS.' b ON a.goodsno=b.goodsno LEFT JOIN '.GD_TODAYSHOP_GOODS.' tg ON a.goodsno=tg.goodsno WHERE a.cancel=[i] AND a.ordno=[i]', $sno, $row_ord['ordno']);
$res_ord_item = $db->_select($ord_item_query);

$cancel_query = $db->_query_print('SELECT rprice, rfee, pgcancel FROM '.GD_ORDER_CANCEL.' WHERE sno=[i]', $sno);
$res_cancel = $db->_select($cancel_query);
$row_cancel = $res_cancel[0];

$res_data = Array();

$res_data['order'] = $row_ord;
$res_data['cancel_item'] = $res_ord_item;
$res_data['cancel'] = $row_cancel;

echo ($json->encode($res_data));
