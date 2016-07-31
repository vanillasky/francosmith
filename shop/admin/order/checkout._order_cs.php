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
if($checkoutapi['cryptkey'] && $checkoutapi['integrateOrder']=='y') {
	$isEnableAdminCheckoutOrder = true;
}
else {
	$isEnableAdminCheckoutOrder = false;
}

/*
 * CheckoutAPI 주문 연동 통합관리를 사용하는 경우
*/
if($isEnableAdminCheckoutOrder) {
	$checkout_arWhere=array();
	if(count($search['type'])) {
		$subWhere = array();
		foreach($search['type'] as $v) {
			switch($v) {
				case '1':
					$subWhere['OD0003']=true;$subWhere['OD0004']=true;$subWhere['OD0005']=true;
					break;
				case '2':
					$subWhere['OD0038']=true;$subWhere['OD0039']=true;$subWhere['OD0040']=true;
					$subWhere['OD0041']=true;$subWhere['OD0042']=true;
					break;
				case '3':
					$subWhere['OD0038']=true;$subWhere['OD0039']=true;$subWhere['OD0040']=true;
					$subWhere['OD0041']=true;$subWhere['OD0042']=true;
					break;
				case '4':
					$subWhere['OD0026']=true;$subWhere['OD0027']=true;$subWhere['OD0028']=true;
					$subWhere['OD0029']=true;$subWhere['OD0030']=true;$subWhere['OD0031']=true;
					$subWhere['OD0034']=true;$subWhere['OD0035']=true;
					break;
				//case '5': $subWhere[] = '(oi.dyn="r" and oi.cyn="y")'; break;
				case '6':
					$subWhere['OD0032']=true;$subWhere['OD0033']=true;
					break;
				case '7':
					$subWhere['OD0024']=true;$subWhere['OD0025']=true;
					break;
			}
		}
		if(count($subWhere)) {
			$subWhere = array_keys($subWhere);
			$subWhere = preg_replace('/^.+$/','"$0"',$subWhere);
			$checkout_arWhere[] = 'o.ORDER_OrderStatusCode in ('.implode(',',$subWhere).')';
		}
	}
	else {
		$checkout_arWhere[] =
			'o.ORDER_OrderStatusCode in ('.
				'"OD0014","OD0015","OD0016","OD0017","OD0018","OD0019",'.
				'"OD0020","OD0021","OD0022","OD0023","OD0024","OD0025",'.
				'"OD0026","OD0027","OD0028","OD0029","OD0030","OD0031",'.
				'"OD0032","OD0033","OD0034","OD0035","OD0038","OD0039",'.
				'"OD0040","OD0041","OD0042"'.
			')';
	}

	if($search['sword'] && $search['skey']) {
		$es_sword = $db->_escape($search['sword']);
		switch($search['skey']) {
			case 'all':
				$arWhere[] = "(
					o.ORDER_OrderID = '{$es_sword}' or
					o.ORDER_OrdererName like '%{$es_sword}%' or
					o.ORDER_PaymentSender like '%{$es_sword}%' or
					o.ORDER_OrdererID = '{$es_sword}'
				)"; break;
			case 'ordno': $checkout_arWhere[] = "o.ORDER_OrderID = '{$es_sword}'"; break;
			case 'nameOrder': $checkout_arWhere[] = "o.ORDER_OrdererName like '%{$es_sword}%'"; break;
			case 'bankSender': $checkout_arWhere[] = "o.ORDER_PaymentSender like '%{$es_sword}%'"; break;
			case 'm_id': $checkout_arWhere[] = "o.ORDER_OrdererID = '{$es_sword}'"; break;
		}
	}
	if($search['regdt_start']) {
		if(!$search['regdt_end']) $search['regdt_end'] = date('Ymd');
		$tmp_start = substr($search['regdt_start'],0,4).'-'.substr($search['regdt_start'],4,2).'-'.substr($search['regdt_start'],6,2).' 00:00:00';
		$tmp_end = substr($search['regdt_end'],0,4).'-'.substr($search['regdt_end'],4,2).'-'.substr($search['regdt_end'],6,2).' 23:59:59';
		$checkout_arWhere[] = $db->_query_print('o.ORDER_OrderDateTime between [s] and [s]',$tmp_start,$tmp_end);
	}
	if($search['settlekind']) {
		$tmpMap = array('a'=>'무통장입금','c'=>'신용카드','o'=>'계좌이체','v'=>'가상계좌','h'=>'휴대폰');
		$checkout_arWhere[] = $db->_query_print('o.ORDER_PaymentMethod = [s]',$tmpMap[$search['settlekind']]);
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
				oi.sno as itemsno
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
				o.ORDER_OrderDateTime as orddt,
				if(o.CANCEL_CancelRequestDate<>"0000-00-00 00:00:00",o.CANCEL_CancelRequestDate,
					if(o.RETURN_ReturnRequestDate<>"0000-00-00 00:00:00",o.RETURN_ReturnRequestDate,
					if(o.EXCHANGE_ExchangeRequestDate<>"0000-00-00 00:00:00",o.EXCHANGE_ExchangeRequestDate,
					""))) as canceldt,
				if(o.CANCEL_CancelRequestDate<>"0000-00-00 00:00:00",o.CANCEL_CancelReason,
					if(o.RETURN_ReturnRequestDate<>"0000-00-00 00:00:00",o.RETURN_ReturnReason,
					if(o.EXCHANGE_ExchangeRequestDate<>"0000-00-00 00:00:00",o.EXCHANGE_ExchangeReason,
					""))) as code,
				o.ORDER_OrderID as ordno,
				o.ORDER_OrdererName as nameOrder,
				o.ORDER_PaymentMethod as settlekind,
				o.ORDER_OrdererID as m_id,
				"" as dormant_regDate,
				"" as m_no,
				op.ProductName as goodsnm,
				"" as goodsno,
				count(op.orderNo) as count_goods,
				sum(op.Quantity) as sea,
				o.ORDER_MallOrderAmount as pay,
				o.ORDER_OrderStatus as step,
				"" as istep,
				"" as itemsno
			from
				gd_navercheckout_order as o
				inner join gd_navercheckout_order_product as op on o.orderNo=op.orderNo
			'.$checkout_strWhere.'
			group by
				o.orderNo
		)
		order by
			orddt desc
	';

	$cancelResult = $db->_select_page(20,$page,$query);
}
?>
