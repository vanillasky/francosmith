<?php
/**
 * 주문취소리스트에서 네이버체크아웃 취소주문데이터
 * @author sunny, oneorzero
 */

/*
 * CheckoutAPI 환경변수
*/
$config = Core::loader('config');
$checkoutapi = $config->load('checkoutapi');
/*
 * CheckoutAPI 주문 연동 통합관리를 사용하는 경우
*/
if(!($checkoutapi['cryptkey'] && $checkoutapi['integrateOrder']=='y')) return false;	// include 이므로 return 해도 됨..

// 체크아웃 4.0 설정
$checkout_message_schema = include "./_cfg.checkout.php";

$checkout_arWhere=array();
if(count($search['type'])) {
	$subWhere = array();
	foreach($search['type'] as $v) {
		switch($v) {
			case '1':	// 취소완료
				$subWhere[] = $checkout_message_schema['extra_productOrderStatusType']['취소완료'];

				break;
			case '2':	// 환불접수

				break;
			case '3':	// 환불완료

				break;
			case '4':	// 반품접수
				$subWhere[] = $checkout_message_schema['extra_productOrderStatusType']['반품요청'];

				break;
			//case '5': $subWhere[] = '(oi.dyn="r" and oi.cyn="y")'; break;
			case '6':	// 반품완료
				$subWhere[] = $checkout_message_schema['extra_productOrderStatusType']['반품완료'];
				break;
			case '7':	// 교환완료
				$subWhere[] = $checkout_message_schema['extra_productOrderStatusType']['교환완료'];
				break;
		}
	}
	if(sizeof($subWhere) > 0) {
		$checkout_arWhere[] = implode(' OR ',$subWhere);
	}
}
else {
	$checkout_arWhere[] = " PO.ClaimType > '' ";
}

if($search['sword'] && $search['skey']) {
	$es_sword = $db->_escape($search['sword']);
	switch($search['skey']) {
		case 'all':
			$arWhere[] = "(
				O.OrderID = '{$es_sword}' or
				O.OrdererName like '%{$es_sword}%' or
				O.OrdererID = '{$es_sword}'
			)"; break;
		case 'ordno': $checkout_arWhere[] = "O.OrderID = '{$es_sword}'"; break;
		case 'nameOrder': $checkout_arWhere[] = "O.OrdererName like '%{$es_sword}%'"; break;
		case 'm_id': $checkout_arWhere[] = "O.OrdererID = '{$es_sword}'"; break;
	}
}
if($search['regdt_start']) {
	if(!$search['regdt_end']) $search['regdt_end'] = date('Ymd');
	$tmp_start = substr($search['regdt_start'],0,4).'-'.substr($search['regdt_start'],4,2).'-'.substr($search['regdt_start'],6,2).' 00:00:00';
	$tmp_end = substr($search['regdt_end'],0,4).'-'.substr($search['regdt_end'],4,2).'-'.substr($search['regdt_end'],6,2).' 23:59:59';
	$checkout_arWhere[] = $db->_query_print('O.OrderDate between [s] and [s]',$tmp_start,$tmp_end);
}
if($search['settlekind']) {
	$tmpMap = array('a'=>'무통장입금','c'=>'신용카드','o'=>'계좌이체','v'=>'가상계좌');
	$checkout_arWhere[] = $db->_query_print('o.settlekind = [s]',$tmpMap[$search['settlekind']]);
}
if(count($checkout_arWhere)) {
	$checkout_strWhere = 'where '.implode(' and ',$checkout_arWhere);
}

$query = '
	(
		select SQL_CALC_FOUND_ROWS
			"godo" as _order_type,
			o.orddt as orddt,
			oc.regdt as canceldt,
			oc.code as code,
			o.ordno as ordno,
			o.nameOrder as nameOrder,
			o.settlekind as settlekind,
			m.m_id as m_id,
			m.dormant_regDate as dormant_regDate,
			o.m_no as m_no,
			oi.goodsnm as goodsnm,
			oi.goodsno as goodsno,
			count(oi.goodsno) as count_goods,
			sum(oi.ea) as sea,
			sum((oi.price-oi.memberdc-oi.coupon)*oi.ea) as pay,
			o.step as step,
			oi.istep as istep,
			oi.sno as itemsno,
			null AS PlaceOrderStatus,
			null AS ProductOrderStatus,
			null AS ClaimType,
			null AS ClaimStatus,
			null AS ProductOrderIDList

		from
			gd_order_cancel as oc
			inner join gd_order_item as oi on oc.sno = oi.cancel and oc.ordno = oi.ordno
			inner join gd_order as o on oi.ordno=o.ordno
			left join gd_member as m on o.m_no=m.m_no
		'.$strWhere.'
		group by
			oc.sno
	)
	union
	(
		select
			"checkout" as _order_type,
			O.OrderDate as orddt,
			if(C.ClaimRequestDate<>"0000-00-00 00:00:00",C.ClaimRequestDate,
				if(R.ClaimRequestDate<>"0000-00-00 00:00:00",R.ClaimRequestDate,
				if(E.ClaimRequestDate<>"0000-00-00 00:00:00",E.ClaimRequestDate,
				""))) as canceldt,

			if(C.ClaimRequestDate<>"0000-00-00 00:00:00",C.CancelReason,
				if(R.ClaimRequestDate<>"0000-00-00 00:00:00",R.ReturnReason,
				if(E.ClaimRequestDate<>"0000-00-00 00:00:00",E.ExchangeReason,
				""))) as code,

			O.OrderID as ordno,
			O.OrdererName as nameOrder,
			O.PaymentMeans as settlekind,
			O.OrdererID as m_id,
			MB.dormant_regDate as dormant_regDate,
			"" as m_no,
			PO.ProductName as goodsnm,
			"" as goodsno,
			count(PO.ProductOrderID) as count_goods,
			sum(PO.Quantity) as sea,
			SUM(PO.Quantity * PO.UnitPrice - ProductDiscountAmount) AS pay,
			PO.ProductOrderStatus as step,
			"" as istep,
			"" as itemsno,
			PO.PlaceOrderStatus,
			PO.ProductOrderStatus,
			PO.ClaimType,
			PO.ClaimStatus,
			GROUP_CONCAT(PO.ProductOrderID SEPARATOR ",") AS ProductOrderIDList

		FROM '.GD_NAVERCHECKOUT_ORDERINFO.' AS O

		INNER JOIN '.GD_NAVERCHECKOUT_PRODUCTORDERINFO.' AS PO
			ON PO.OrderID = O.OrderID

		LEFT JOIN '.GD_MEMBER.' AS MB
			ON PO.MallMemberID=MB.m_id

		LEFT JOIN '.GD_NAVERCHECKOUT_DELIVERYINFO.' AS D
			ON PO.ProductOrderID = D.ProductOrderID

		LEFT JOIN '.GD_NAVERCHECKOUT_CANCELINFO.' AS C
			ON PO.ProductOrderID = C.ProductOrderID

		LEFT JOIN '.GD_NAVERCHECKOUT_RETURNINFO.' AS R
			ON PO.ProductOrderID = R.ProductOrderID

		LEFT JOIN '.GD_NAVERCHECKOUT_EXCHANGEINFO.' AS E
			ON PO.ProductOrderID = E.ProductOrderID

		LEFT JOIN '.GD_NAVERCHECKOUT_DECISIONHOLDBACKINFO.' AS DH
			ON PO.ProductOrderID = DH.ProductOrderID

		'.$checkout_strWhere.'

		GROUP BY PO.OrderID, PO.ProductOrderStatus, PO.ClaimStatus
	)
	order by
		orddt desc
';

$cancelResult = $db->_select_page(20,$page,$query);

return true;
?>
