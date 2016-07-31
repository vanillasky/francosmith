<?php
/**
 * �ֹ�����Ʈ���� ���̹�üũ�ƿ� �ֹ�������
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
	$checkout_isOrderItemSearch = false;
	$checkout_arWhere = array();
	$checkout_isUnableCondition=false;

	if($search['regdt_start']) {
		if(!$search['regdt_end']) $search['regdt_end'] = date('Ymd');
		$tmp_start = substr($search['regdt_start'],0,4).'-'.substr($search['regdt_start'],4,2).'-'.substr($search['regdt_start'],6,2).' 00:00:00';
		$tmp_end = substr($search['regdt_end'],0,4).'-'.substr($search['regdt_end'],4,2).'-'.substr($search['regdt_end'],6,2).' 23:59:59';
		switch($search['dtkind']) {
			case 'orddt': $checkout_arWhere[] = $db->_query_print('o.ORDER_OrderDateTime between [s] and [s]',$tmp_start,$tmp_end); break;
			case 'cdt': $checkout_arWhere[] = $db->_query_print('o.ORDER_PaymentDate between [s] and [s]',$tmp_start,$tmp_end); break;
			case 'ddt': $checkout_arWhere[] = $db->_query_print('o.DELIVERY_SendDate between [s] and [s]',$tmp_start,$tmp_end); break;
			case 'confirmdt': $checkout_arWhere[] = $db->_query_print('o.DELIVERY_ShippingCompleteDate between [s] and [s]',$tmp_start,$tmp_end); break;
		}
	}

	if($search['settlekind']) {
		$tmpMap = array('a'=>'�������Ա�','c'=>'�ſ�ī��','o'=>'������ü');
		if(array_key_exists($search['settlekind'],$tmpMap)) {
			$checkout_arWhere[] = $db->_query_print('o.ORDER_PaymentMethod  = [s]',$tmpMap[$search['settlekind']]);
		}
		else {
			$checkout_isUnableCondition=true;
		}
	}

	$checkout_arStatus=array();
	if(count($search['step'])) {
		if(in_array(0,$search['step'])) { $checkout_arStatus[] = 'OD0001'; }
		if(in_array(1,$search['step'])) { $checkout_arStatus[] = 'OD0002'; }
		if(in_array(2,$search['step'])) { $checkout_arStatus[] = 'OD0007'; $checkout_arStatus[] = 'OD0009'; }
		if(in_array(3,$search['step'])) { $checkout_arStatus[] = 'OD0012'; }
		if(in_array(4,$search['step'])) { $checkout_arStatus[] = 'OD0013'; }
	}
	if(count($search['step2'])) {
		if(in_array(1,$search['step2'])) {
			$checkout_arStatus[] = 'OD0003'; $checkout_arStatus[] = 'OD0004';
			$checkout_arStatus[] = 'OD0005'; $checkout_arStatus[] = 'OD0006';
		}
		if(in_array(2,$search['step2'])) {
			$checkout_arStatus[] = 'OD0038'; $checkout_arStatus[] = 'OD0039';
			$checkout_arStatus[] = 'OD0040'; $checkout_arStatus[] = 'OD0041'; $checkout_arStatus[] = 'OD0042';
		}
		if(in_array(3,$search['step2'])) {
			$checkout_arStatus[] = 'OD0026'; $checkout_arStatus[] = 'OD0027'; $checkout_arStatus[] = 'OD0028'; $checkout_arStatus[] = 'OD0029';
			$checkout_arStatus[] = 'OD0030'; $checkout_arStatus[] = 'OD0031'; $checkout_arStatus[] = 'OD0032';
			$checkout_arStatus[] = 'OD0033'; $checkout_arStatus[] = 'OD0034'; $checkout_arStatus[] = 'OD0035';
		}
	}
	if(count($search['step']) || count($search['step2'])) {
		if(count($checkout_arStatus)) {
			$tmp_arStatus = preg_replace('/.+/','"$0"',$checkout_arStatus);
			$checkout_arWhere[] = 'o.ORDER_OrderStatusCode in ('.implode(',',$tmp_arStatus).')';
		}
		else {
			$checkout_isUnableCondition=true;
		}
	}
	if($search['sword'] && $search['skey']) {
		$es_sword = $db->_escape($search['sword']);
		switch($search['skey']) {
			case 'all':
				$checkout_arWhere[] = "(
					o.ORDER_OrderID = '{$es_sword}' or
					o.ORDER_OrdererName like '%{$es_sword}%' or
					o.SHIPPING_Recipient like '%{$es_sword}%' or
					o.ORDER_PaymentSender like '%{$es_sword}%' or
					o.ORDER_OrdererID = '{$es_sword}'
				)"; break;
			case 'ordno': $checkout_arWhere[] = "o.ORDER_OrderID = '{$es_sword}'"; break;
			case 'nameOrder': $checkout_arWhere[] = "o.ORDER_OrdererName like '%{$es_sword}%'"; break;
			case 'nameReceiver': $checkout_arWhere[] = "o.SHIPPING_Recipient like '%{$es_sword}%'"; break;
			case 'bankSender': $checkout_arWhere[] = "o.ORDER_PaymentSender like '%{$es_sword}%'"; break;
			case 'm_id': $checkout_arWhere[] = "o.ORDER_OrdererID = '{$es_sword}'"; break;
		}
	}
	if($search['sgword'] && $search['sgkey']) {
		$es_sgword = $db->_escape($search['sgword']);
		switch($search['sgkey']) {
			case 'goodsnm': $checkout_arWhere[] = "op.ProductName like '%{$es_sgword}%'"; break;
			default: $checkout_isUnableCondition=true;
		}
		$checkout_isOrderItemSearch=true;
	}

	if($checkout_isUnableCondition) {
		$checkout_arWhere[] = '0';
	}

	if(count($checkout_arWhere)) {
		$checkout_strWhere = 'where '.implode(' and ',$checkout_arWhere);
	}
	if($checkout_isOrderItemSearch) {
		$checkout_select_count_item = '(select count(*) from gd_navercheckout_order_product as s_op where s_op.orderNo=o.orderNo) as count_item';
	}
	else {
		$checkout_select_count_item = 'count(op.orderNo) as count_item';
	}

	if($search['mode']=='group') {
		$SQL_CALC_FOUND_ROWS='';
	}
	else {
		$SQL_CALC_FOUND_ROWS='SQL_CALC_FOUND_ROWS';
	}

	$query = '
		(
			select '.$SQL_CALC_FOUND_ROWS.'
				"godo" as _order_type,
				o.ordno as ordno,
				o.nameOrder as nameOrder,
				o.nameReceiver as nameReceiver,
				o.settlekind as settlekind,
				o.step as step,
				o.step2 as step2,
				o.orddt as orddt,
				o.dyn as dyn,
				o.escrowyn as escrowyn,
				o.eggyn as eggyn,
				o.inflow as inflow,
				o.deliverycode as deliverycode,
				o.cashreceipt as cashreceipt,
				o.cbyn as cbyn,
				o.oldordno as oldordno,
				o.prn_settleprice as prn_settleprice,
				m.m_id as m_id,
				m.m_no as m_no,
				'.$select_count_item.',
				'.$select_count_dv_item.',
				oi.goodsnm as goodsnm,
				"" as stepMsg
			from
				'.GD_ORDER.' as o
				left join '.GD_ORDER_ITEM.' as oi on o.ordno=oi.ordno
				left join '.GD_MEMBER.' as m on o.m_no = m.m_no
				'.$join_GD_COUPON_ORDER.'
			'.$strWhere.'
			group by o.ordno
		)
		union
		(
			select
				"checkout" as _order_type,
				o.ORDER_OrderID as ordno,
				o.ORDER_OrdererName as nameOrder,
				o.SHIPPING_Recipient as nameReceiver,
				o.ORDER_PaymentMethod as settlekind,
				o.ORDER_OrderStatusCode as step,
				"" as step2,
				o.ORDER_OrderDateTime as orddt,
				"n" as dyn,
				o.ORDER_Escrow as escrowyn,
				"" as eggyn,
				"" as inflow,
				"" as deliverycode,
				"" as cashreceipt,
				"" as cbyn,
				"" as oldordno,
				o.ORDER_MallOrderAmount as prn_settleprice,
				o.ORDER_OrdererID as m_id,
				"" as m_no,
				count(op.orderNo) as count_item,
				"" as count_dv_item,
				op.ProductName as goodsnm,
				o.ORDER_OrderStatus as stepMsg
			from
				gd_navercheckout_order as o
				left join gd_navercheckout_order_product as op on o.orderNo = op.orderNo
			'.$checkout_strWhere.'
			group by
				o.orderNo
		)
	';

	if($search['mode']=='group') {
		$result = $db->_select($query);

		// �׷캰�� �ֹ��� �Ҵ�
		foreach($result as $v) {
			if($v['_order_type']=='godo') {
				$orderGroupKey = $v['step2']*10+($v['step'] === '1' || ($v['step'] === '2' && $v['step2'] > 40) ? 1 : $v['step']);
				$orderGroupNameMap[$orderGroupKey] = getStepMsg($v['step'],$v['step2']);

				$orderList[$orderGroupKey][] = $v;
			}
			elseif($v['_order_type']=='checkout') {
				$tmp_StepMap = array(
					'OD0001'=>0,'OD0002'=>1,'OD0007'=>2,'OD0008'=>2,'OD00012'=>3,'OD0010'=>3,'OD0013'=>4,'OD0036'=>4,'OD0037'=>4
				);
				if(array_key_exists($v['step'],$tmp_StepMap)) {
					$orderGroupKey = $tmp_StepMap[$v['step']];
					$orderGroupNameMap[$orderGroupKey] = getStepMsg($tmp_StepMap[$v['step']],0);
					$orderList[$orderGroupKey][] = $v;
				}
			}
		}
		ksort($orderList);
		foreach($orderList as $orderGroupKey=>$eachOrderGroup) {
			$sortAssistDyn=$sortAssistOrddt=array();
			foreach ($eachOrderGroup as $k => $v) {
				$sortAssistDyn[$k]  = $v['dyn'];
				$sortAssistOrddt[$k] = $v['orddt'];
				if($v['_order_type']=='godo') {
					$orderList[$orderGroupKey][$k]['stepMsg'] = getStepMsg($v['step'],$v['step2'],$v['ordno']);
				}
			}
			array_multisort($sortAssistDyn,SORT_ASC,$sortAssistOrddt,SORT_DESC,$orderList[$orderGroupKey]);

			$i=0;
			foreach ($eachOrderGroup as $k => $v) {
				$orderList[$orderGroupKey][$k]['_rno'] = count($eachOrderGroup)-($i++);
			}
		}
	}
	else {
		if(!$cfg['orderPageNum']) $cfg['orderPageNum'] = 15;

		$query = $query.' order by ordno desc';
		$result = $db->_select_page($cfg['orderPageNum'],$page,$query);

		$orderList[9999]=array();
		foreach($result['record'] as $v) {
			if($v['_order_type']=='godo') {
				$v['stepMsg']=getStepMsg($v['step'],$v['step2'],$v['ordno']);
			}
			$orderList[9999][] = $v;
		}
		$pageNavi = $result['page'];
	}
?>

<script type="text/javascript">
document.observe("dom:loaded", function() {
	$$(".selShippingCompany").each(function(item){
		Event.observe(item, 'change', function(event) {
			var element = $(Event.element(event));
			if(element.value=='z_etc' || element.value=='z_quick' || element.value=='z_direct' || element.value=='z_visit' || element.value=='z_delegation') {
				element.up(1).select(".iptTrackingNumber")[0].value='';
				element.up(1).select(".iptTrackingNumber")[0].disabled=true;
				element.up(1).select(".iptTrackingNumber")[0].style.backgroundColor="#cccccc";
			}
			else {
				element.up(1).select(".iptTrackingNumber")[0].disabled=false;
				element.up(1).select(".iptTrackingNumber")[0].style.backgroundColor="#ffffff";
			}
		});
	});
});

/**
* üũ�ƿ��� �ֹ����º������
*/
function processCheckoutOrder(f, selCase, isGodoChk) {
	var isCheckoutOrder=false;
	var valid = true;
	var stop = false;
	f.select("input[type=checkbox]").each(function(item){
		if(stop) return;
		var re = new RegExp('^checkoutPlaceOrder');
		if(re.test(item.name) && item.checked) {
			isCheckoutOrder=true;
			if(selCase.value!="2") {
				alert("�Ա�Ȯ�δܰ��� üũ�ƿ��ֹ��� ����غ������θ� ���� �����մϴ�");
				stop=true;valid=false;
				return;
			}
		}
		var re = new RegExp('^checkoutShipOrder');
		if(re.test(item.name) && item.checked) {
			var ShippingCompleteDate = document.getElementsByName('ShippingCompleteDate['+item.value+']')[0];
			var ShippingCompany = document.getElementsByName('ShippingCompany['+item.value+']')[0];
			var TrackingNumber = document.getElementsByName('TrackingNumber['+item.value+']')[0];

			if(ShippingCompleteDate.value.length==0) {
				alert('�����Ͻ� �ֹ��� �߼�ó���ϱ� ���ؼ��� ������� �Է��ϼž� �մϴ�');
				stop=true;valid=false;
				return;
			}
			if(ShippingCompany.value.length==0) {
				alert('�����Ͻ� �ֹ��� �߼�ó���ϱ� ���ؼ��� ��۹���� �����ϼž� �մϴ�');
				stop=true;valid=false;
				return;
			}
			if(!(ShippingCompany.value=='z_etc' || ShippingCompany.value=='z_quick'
				|| ShippingCompany.value=='z_direct' || ShippingCompany.value=='z_visit' || ShippingCompany.value=='z_delegation')) {
				if(TrackingNumber.value.length==0) {
					alert('�����Ͻ� �ֹ��� �߼�ó���ϱ� ���ؼ��� �����ȣ�� �Է��ϼž� �մϴ�');
					stop=true;valid=false;
					return;
				}
			}

			if(selCase.value!="3") {
				alert("����غ��ߴܰ��� üũ�ƿ��ֹ��� ��������θ� ���� �����մϴ�");
				stop=true;valid=false;
				return;
			}
			isCheckoutOrder=true;
		}
	});

	if(valid==false) {
		return false;
	}

	if(isCheckoutOrder) {
		var url = "./indb.checkout.ax.php";
		var myAjax = new Ajax.Request(url,{
			"method":"post",
			"parameters":f.serialize(true),
			"onComplete":function(transport){
				alert(transport.responseText);
				if(isGodoChk) {f.submit();}
				else {self.location.href=self.location.href}
			}
		});
		return false;
	}
	return true;
}
</script>

<?php
}
?>
