<?php
/**
 * ��ǰ/��ȯ��������Ʈ���� ���̹�üũ�ƿ� ��ǰ/��ȯ�ֹ�������
 * @author sunny, oneorzero
 */

/*
 * CheckoutAPI ȯ�溯��
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
 * CheckoutAPI �ֹ� ���� ���հ����� ����ϴ� ���
*/
if($isEnableAdminCheckoutOrder) {
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
				o.pg,
				o.ncash_tx_id,
				m.m_no as m_no,
				m.m_id as m_id,
				m.dormant_regDate as dormant_regDate
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
				orderNo as sno,
				if(RETURN_ReturnRequestDate<>"0000-00-00 00:00:00",RETURN_ReturnRequestDate,
					if(EXCHANGE_ExchangeRequestDate<>"0000-00-00 00:00:00",EXCHANGE_ExchangeRequestDate,
					"")) as canceldt,
				"" as nameCancel,
				if(RETURN_ReturnRequestDate<>"0000-00-00 00:00:00",RETURN_ReturnReason,
					if(EXCHANGE_ExchangeRequestDate<>"0000-00-00 00:00:00",EXCHANGE_ExchangeReason,
					"")) as code,
				ORDER_OrderID as ordno,
				ORDER_OrderDateTime as orddt,
				ORDER_OrdererName as nameOrder,
				ORDER_PaymentMethod as settlekind,
				"" as pg,
				"" as ncash_tx_id,
				"" as m_no,
				ORDER_OrdererID as m_id,
				"" as dormant_regDate
			from
				gd_navercheckout_order
			where
				ORDER_OrderStatusCode in (
					"OD0014","OD0015","OD0016","OD0017","OD0018","OD0019",
					"OD0020","OD0021","OD0022","OD0023","OD0024","OD0025",
					"OD0026","OD0027","OD0028","OD0029","OD0030","OD0031",
					"OD0031","OD0032","OD0033","OD0034","OD0035") and
				( confirmReturn = "n" and confirmExchange = "n" )
		)
		order by
			canceldt desc
	';
	$regoodsResult = $db->_select_page(10,$page,$query);
?>

<script type="text/javascript">
/**
* ��ǰ�Ϸ�ó���� üũ�ƿ� �ֹ��ǵ� ����
*/
function indbCheckoutReturn() {
	var arChk=document.getElementsByName('checkoutNo[]');
	var length=arChk.length;
	for(i=0;i<length;i++) {
		if(arChk[i].checked) {return true;}
	}
	return false;
}
/**
* ��ȯ�Ϸ�ó���� üũ�ƿ� �ֹ����� ����
*/
function indbCheckoutExchange() {
	var arChk=document.getElementsByName('checkoutNo[]');
	var length=arChk.length;
	var checked=false;
	for(i=0;i<length;i++) {
		if(arChk[i].checked) {checked=true;break;}
	}
	if(checked) {
		alert("üũ�ƿ��ֹ��� ��ȯó���� �ƴ� ��ǰ�Ϸ�ó���� ���ּ���");
		return false;
	}
	return true;
}
</script>

<?php
}
?>
