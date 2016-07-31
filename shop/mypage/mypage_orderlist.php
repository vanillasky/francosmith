<?
header("Cache-Control: no-store, no-cache, must-revalidate");
header("Pragma: no-cache");

include "../_header.php";
include "../lib/page.class.php";
@include "../conf/naverCheckout.cfg.php";


if (!$sess && !$_COOKIE[guest_ordno]) go("../member/login.php?returnUrl=$_SERVER[PHP_SELF]");
$db_table = "".GD_ORDER."";
if($checkoutCfg['ncMemberYn'] == "y") {
	$db_table = "(
	SELECT
		'gd_order' AS oriTable,
		IFNULL( o1.m_no, '".$sess['m_no']."' ) AS m_no,
		o1.orddt AS orddt,
		o1.ordno AS ordno,
		o1.settlekind AS settlekind,
		o1.settleprice AS settleprice,
		o1.step AS step,
		o1.step2 AS step2,o1.nameOrder,o1.inflow
	FROM
		".GD_ORDER." o1
	WHERE
		o1.m_no ='".$sess['m_no']."'

	UNION

	SELECT
		'gd_navercheckout_order',
		'".$sess['m_no']."',
		NO.OrderDate,
		NO.OrderID,
		NO.PaymentMeans,
		SUM(NPO.Quantity * NPO.UnitPrice - NPO.ProductDiscountAmount) AS calculated_payAmount,
		NPO.ProductOrderStatus,
		NPO.ClaimStatus ,NO.OrdererName,''
	FROM
		".GD_NAVERCHECKOUT_ORDERINFO." AS NO
	INNER JOIN ".GD_NAVERCHECKOUT_PRODUCTORDERINFO." AS NPO
	ON NO.OrderID = NPO.OrderID
	WHERE
		NPO.MallMemberID = '".$sess['m_id']."'
	GROUP BY NO.OrderID, NPO.ProductOrderStatus, NPO.ClaimStatus
) AS gd_order_temp";
}

if ($sess[m_no]) $where[] = "m_no = '$sess[m_no]'";
else {
	$where[] = "ordno = '$_COOKIE[guest_ordno]'";
	$where[] = "nameOrder = '$_COOKIE[guest_nameOrder]'";
	$where[] = "m_no = '0'";
}

$pg = new Page($_GET[page],10);
$pg->setQuery($db_table,$where,"orddt desc");
$pg->exec();

$res = $db->query($pg->query);

while ($data=$db->fetch($res)){
	if($data['oriTable'] == "gd_navercheckout_order") {
		$data['ordertypestr'] = "체크아웃";
		$data['ProductOrderStatus'] = $data['step'];
		$data['ClaimStatus'] = $data['step2'];
		$data['str_step'] = getCheckoutOrderStatus($data);
		$data['str_settlekind'] = $data['settlekind'];
	}
	else {
		$data[str_step] = (!$data[step2]) ? $r_step[$data[step]] : $r_step2[$data[step2]];
		$data[str_settlekind] = $r_settlekind[$data[settlekind]];

		// 최초 결제 금액은 변경하지 않음.
		// 취소 금액은 (최초결제 금액 - 실결제금액)
		//if($data[prn_settleprice]) $data[settleprice] = $data[prn_settleprice];
		$order = Core::loader('order');
		$order->load($data['ordno']);

		$data['settleprice'] = $order->getRealPrnSettleAmount();
		$data['canceled_price'] = $order->getCancelCompletedAmount();

		// 투데이샵 주문수정 : 주문구분 및 주문상태
		$query2 = $db->_query_print("SELECT g.todaygoods, tg.goodstype FROM ".GD_ORDER_ITEM." AS oi JOIN ".GD_GOODS." AS g ON oi.goodsno=g.goodsno LEFT JOIN ".GD_TODAYSHOP_GOODS." AS tg ON g.goodsno=tg.goodsno WHERE oi.ordno=[i] LIMIT 1", $data['ordno']);
		$res2 = $db->query($query2);
		$data2 = $db->fetch($res2, 1);
		if($data2['todaygoods']=='y') {
			$data['ordertypestr'] = '소셜';
			if ($data2['goodstype'] == 'coupon') {
				$data['ordertypestr'] .= '(쿠폰)';
				$data[str_step] = str_replace("배송","발급",$data[str_step]);

			}
			else $data['ordertypestr'] .= '(실물)';
		}
		else {
			$data['ordertypestr'] = '일반';
			if($data['inflow'] == "sugi") $data['ordertypestr'] .= '(수기)';
			$data['goodstype'] = '';
		}
		unset($data2, $res2, $query2);
		// 투데이샵 주문수정 : 주문구분 및 주문상태
	}

	if(function_exists('settleIcon')){
		$data['str_settlekind'] = (settleIcon($data['settleInflow'])) ? settleIcon($data['settleInflow']) . ' ' . $data['str_settlekind'] : $data['str_settlekind'];
	}

	$loop[] = $data;
}

$tpl->assign(array(
			'loop'	=> $loop,
			'pg'	=> $pg,
			));
$tpl->print_('tpl');

?>