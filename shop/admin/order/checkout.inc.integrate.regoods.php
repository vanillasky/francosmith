<?php
/**
 * 반품/교환접수리스트에서 네이버체크아웃 반품/교환주문데이터
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

$query = '
	(
		select SQL_CALC_FOUND_ROWS
			"godo" as _order_type,
			oc.sno as sno,
			oc.regdt as canceldt,
			oc.name as nameCancel,
			oc.code as code,
			o.ordno as ordno,
			o.orddt as orddt,
			o.nameOrder as nameOrder,
			o.settlekind as settlekind,
			m.m_no as m_no,
			m.m_id as m_id,
			m.dormant_regDate as dormant_regDate,
			null AS PlaceOrderStatus,
			null AS ProductOrderStatus,
			null AS ClaimType,
			null AS ClaimStatus,
			null AS ProductOrderIDList
		from
			gd_order_cancel as oc
			inner join gd_order_item as oi on oc.sno=oi.cancel and oc.ordno = oi.ordno
			inner join gd_order as o on oi.ordno=o.ordno
			left join gd_member as m on o.m_no=m.m_no
		where
			oi.istep> 40 and oi.cyn = "y" and oi.dyn = "y"
		group by
			oc.sno
	)
	union
	(
		select
			"checkout" as _order_type,
			"" as sno,
			if(R.ClaimRequestDate<>"0000-00-00 00:00:00",R.ClaimRequestDate,
				if(E.ClaimRequestDate<>"0000-00-00 00:00:00",E.ClaimRequestDate,
				"")) as canceldt,
			"" as nameCancel,
			if(R.ClaimRequestDate<>"0000-00-00 00:00:00",R.ReturnReason,
				if(E.ClaimRequestDate<>"0000-00-00 00:00:00",E.ExchangeReason,
				"")) as code,
			O.OrderID as ordno,
			O.OrderDate as orddt,
			O.OrdererName as nameOrder,
			O.PaymentMeans as settlekind,
			MB.m_no,
			MB.m_id,
			MB.dormant_regDate as dormant_regDate,
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

		WHERE PO.ClaimType > "" AND PO.ClaimStatus IN ("RETURN_REQUEST","EXCHANGE_REQUEST")

		GROUP BY PO.OrderID, PO.ProductOrderStatus, PO.ClaimStatus

	)
	order by
		canceldt desc
';

$regoodsResult = $db->_select_page(10,$page,$query);
?>

<script type="text/javascript">
/**
* 반품완료처리시 체크아웃 주문건도 포함
*/
function indbCheckoutReturn() {
	var arChk=document.getElementsByName('checkoutNo[]');
	var length=arChk.length;
	for(i=0;i<length;i++) {
		if(arChk[i].checked) {
			alert("체크아웃주문은 처리할 수 없습니다.");
			return false;

		}
	}
	return false;
}
/**
* 교환완료처리시 체크아웃 주문건은 제외
*/
function indbCheckoutExchange() {
	var arChk=document.getElementsByName('checkoutNo[]');
	var length=arChk.length;
	var checked=false;
	for(i=0;i<length;i++) {
		if(arChk[i].checked) {
			alert("체크아웃주문은 처리할 수 없습니다.");
			return false;
		}
	}
	return false;
}
</script>

<?php

return true;
?>
