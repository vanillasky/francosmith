<?php
// üũ�ƿ� 4.0 ����
	$checkout_message_schema = include "./_cfg.checkout.php";

// �ֹ���ȣ, ��ǰ�ֹ���ȣ
	$OrderID			= $_GET['OrderID'];

// 3.0 �ֹ����� üũ
	list($_3_cnt) = $db->fetch("SELECT count(orderNo) FROM gd_navercheckout_order WHERE ORDER_OrderID = '$OrderID'");
	if ($_3_cnt > 0) {
		msg('���̹� üũ�ƿ� 3.0 �ֹ����� ���̱׷����̼� �� �����մϴ�.',-1);
		exit;
	}

// �ֹ�����
	$query = "
		SELECT

			O.*, PO.*,

			G.img_s,

			MB.m_id,MB.m_no,

			D.DeliveryStatus,
			D.DeliveryMethod,
			D.DeliveryCompany,
			D.TrackingNumber,
			D.SendDate,
			D.PickupDate,
			D.DeliveredDate,
			D.IsWrongTrackingNumber,
			D.WrongTrackingNumberRegisteredDate,
			D.WrongTrackingNumberType,

			false AS claimInfo

		FROM ".GD_NAVERCHECKOUT_ORDERINFO."						AS O
		INNER JOIN ".GD_NAVERCHECKOUT_PRODUCTORDERINFO."		AS PO	ON PO.OrderID = O.OrderID
		LEFT JOIN ".GD_GOODS."								AS G ON PO.ProductID = G.goodsno
		LEFT JOIN ".GD_MEMBER."								AS MB	ON PO.MallMemberID=MB.m_id
		LEFT JOIN ".GD_NAVERCHECKOUT_DELIVERYINFO."				AS D	ON PO.ProductOrderID = D.ProductOrderID

		WHERE
			O.OrderID = '$OrderID'
		ORDER BY PO.PackageNumber
	";
	$rs = $db->query($query);

	// �� ���� �ݾ�, ��ǰ�� �����ݾ�, ���ξ� ���, �ֹ�����, �κй�� ��� üũ �� �迭ȭ
	// Ŭ���� ������ �ִٸ� ������ (�Ź� join ���� ����)
		$orderInfo = array();
		$partial = array();
		$has = array();
		$rowspan = array();
		$_rowspan = array();
		$orderStatus = array();
		$refundAmount = array();
		$deliveryInfo = array();

		while ($row = $db->fetch($rs,1)) {


			$row['calculated_payAmount'] = $row['TotalPaymentAmount'];
			$row['calculated_ordAmount'] = $row['TotalProductAmount'];

			// �κй߼� ���� üũ
			$_tmp = array($row['DeliveryMethod'],$row['DeliveryCompany'],$row['TrackingNumber']);
			if (!in_array($_tmp,(array)$partial['delivery'])) $partial['delivery'][] = $_tmp;

			// Ŭ���� ����
			switch($row['ClaimType']) {
				case 'RETURN':
					$claim_table = GD_NAVERCHECKOUT_RETURNINFO;
					break;
				case 'CANCEL':
				case 'ADMIN_CANCEL':
					$claim_table = GD_NAVERCHECKOUT_CANCELINFO;
					break;
				case 'EXCHANGE':
					$claim_table = GD_NAVERCHECKOUT_EXCHANGEINFO;
					break;
				case 'PURCHASE_DECISION_HOLDBACK':
					$claim_table = GD_NAVERCHECKOUT_DECISIONHOLDBACKINFO;
					break;
				default :
					$claim_table = "";
					break;
			}

			if ($claim_table) {
				// �κ�Ŭ����(���/��ǰ/��ȯ) ���� üũ
				$_tmp = array($row['ClaimType'],$row['ClaimStatus']);
				if (!in_array($_tmp,(array)$partial['claim'])) $partial['claim'][] = $_tmp;

				$row['claimInfo'] = $db->fetch("SELECT * FROM ".$claim_table." WHERE ProductOrderID = '".$row['ProductOrderID']."'",1);
				$has['claim'] = true;

				if ($row['claimInfo']['RefundStandbyStatus'] == 'ȯ��ó���Ϸ�') $refundAmount[$row['ProductOrderID']] = $row['calculated_payAmount'];

			}

			$_delivery = array(
				'DeliveryMethod' => $row['DeliveryMethod'],
				'DeliveryStatus' => $row['DeliveryStatus'],
				'DeliveryCompany' => $row['DeliveryCompany'],
				'TrackingNumber' => $row['TrackingNumber'],
				'SendDate' => $row['SendDate'],
				'PickupDate' => $row['PickupDate'],
				'DeliveredDate' => $row['DeliveredDate']
			);

			if (!empty($_delivery['DeliveryMethod']) && !in_array($_delivery,$deliveryInfo)) {
				$deliveryInfo[] = $_delivery;
			}

			// ó������
			$orderStatus[] = getCheckoutOrderStatus($row,true);

			$orderInfo[] = $row;

			$_rowspan[$row['PackageNumber']]++;

		}

		// ���� ��� �ڵ庰 ��ħ
		foreach($_rowspan as $k => $v) {
			$rowspan[] = $v;
			if ($v-1 > 0) $rowspan = array_pad ( $rowspan, sizeof($rowspan) + $v - 1, 0);
		}

		foreach($partial as $k => $v) $partial[$k] = (sizeof($v) > 1) ? true : false;
		unset($_tmp);

		// ���� ������(or ���ݵ� ������)
		$query = "
			SELECT
				SUM(eNamooEmoney)
			FROM ".GD_NAVERCHECKOUT_PRODUCTORDERINFO."
			WHERE OrderID = '$OrderID'
			";
		list($totalEmoney) = $db->fetch($query);

// �ֹ� ����
$Order = $orderInfo[0];
$orderStatus = array_unique($orderStatus);
?>
<style>
.title2 {
	font-weight:bold;
	padding-bottom:5px;
}
</style>

<script type="text/javascript" src="./checkout.js"></script>
<script type="text/javascript">
function fnDeliveryTrace(channel, code, dlvno) {
	var url = './popup.delivery.php?channel='+channel+'&code='+code+'&dlvno='+dlvno;
	popup(url,800,500);
}
</script>

<div class="title title_top">�ֹ���</div>

<!-- �ֹ� ��ǰ -->
<form name="frmNaverCheckout" method="post" target="processLayerForm">
	<input type="hidden" name="mode" value="">

	<table class=tb cellpadding=4 cellspacing=0>
	<col width="35">
	<col width="120">
	<col width="40">
	<col>
	<col width="50">
	<col width="50">
	<col width="50">
	<col width="50">
	<col width="50">
	<col width="60">
	<? if ($partial['delivery']) { ?>
	<col width="100">
	<? } ?>

	<tr height=25 bgcolor=#2E2B29 class="small"4 style="padding-top:8px">
		<th><font color="white"><a href="javascript:void(0)" onClick="chkBoxAll();fnSetAvailableOperationButton();" class=white>����</a></font></th>
		<th><font color="white">��ǰ�ֹ���ȣ</font></th>
		<th colspan=2><font color="white">��ǰ��</th>
		<th><font color="white">����</font></th>
		<th><font color="white">��ǰ����</font></th>
		<th><font color="white">����</font></th>
		<th><font color="white">����</font></th>
		<th><font color="white">��ۺ�</font></th>
		<th><font color="white">ó������</font></th>
		<? if ($partial['delivery']) { ?>
		<th><font color="white">�ù��/�����ȣ</font></th>
		<? } ?>
	</tr>
	<col align=center span=3><col>
	<col align=center span=10>
	<?
	$idx = 0;

	foreach($orderInfo as $p_order) {
		$idx++;
		$disabled = in_array($p_order['ProductOrderStatus'],array('CANCELED','RETURNED','CANCELED_BY_NOPAYMENT','EXCHANGED','PURCHASE_DECIDED')) ? 'disabled' : '';
	?>
	<tr bgcolor="<?=$bgcolor?>" bg="<?=$bgcolor?>" align="center">
		<td class="noline">
		<input type="checkbox" name="OrderID[<?=$idx?>]" value="<?=$p_order['OrderID']?>" class="el-OrderID {ProductOrderStatus:'<?=$p_order['ProductOrderStatus']?>',ClaimType:'<?=$p_order['ClaimType']?>',ClaimStatus:'<?=$p_order['ClaimStatus']?>',PlaceOrderStatus:'<?=$p_order['PlaceOrderStatus']?>',HoldbackStatus:'<?=$p_order['claimInfo']['HoldbackStatus']?>',HoldbackReason:'<?=$p_order['claimInfo']['HoldbackReason']?>'}" onclick="iciSelect(this);fnSetAvailableOperationButton();" <?=$disabled?> />
		<input type="hidden" name="ProductOrderIDList[<?=$idx?>]" value="<?=$p_order['ProductOrderID']?>" />
		</td>
		<td><font class=ver8 color="#444444"><?=$p_order['ProductOrderID']?></td>
		<td><a href="../../goods/goods_view.php?goodsno=<?=$p_order['ProductID']?>" target=_blank><?=goodsimg($p_order['img_s'],30,"style='border:1 solid #cccccc'",1)?></a></td>
		<td align="left">
			<a href="javascript:popup('../goods/popup.register.php?mode=modify&goodsno=<?=$p_order['ProductID']?>',825,600)">
			<?=$p_order['ProductName']?>
			<?=($p_order['ProductOption']) ? '['.$p_order['ProductOption'].']' : '' ?>
			</a>

			<div style="margin-top:3px;">
				<table class="tb">
				<col class="cellC"><col class="cellL">
				<? if ($p_order['ShippingMemo']) { ?>
				<tr>
					<td class="small">��� �޸�</td>
					<td style="padding:3px 10px" class="small"><?=nl2br($p_order['ShippingMemo'])?></td>
				</tr>
				<? }
				if ($p_order['SellingCode']) { ?>
				<tr>
					<td class="small">���� �ڵ�</td>
					<td style="padding:3px 10px"><?=$p_order['SellingCode']?></td>
				</tr>
				<? }
				if ($p_order['MallManageCode']) { ?>
				<tr>
					<td class="small">�� ���� �ڵ�</td>
					<td style="padding:3px 10px"><?=$p_order['MallManageCode']?></td>
				</tr>
				<? }
				if ($p_order['OrderExtraData']) {
					$_OrderExtraDatas = explode('///',$p_order['OrderExtraData']);
					foreach($_OrderExtraDatas as $_OrderExtraData) {
					if (!$_OrderExtraData) continue;
					$_OrderExtraData = explode(':::',$_OrderExtraData);
				?>
				<tr>
					<td class="small"><?=$_OrderExtraData[0]?></td>
					<td style="padding:3px 10px"><?=$_OrderExtraData[1]?></td>
				</tr>
				<? } } ?>
				</table>
			</div>
		</td>
		<td><?=number_format($p_order['Quantity'])?></td>
		<td><?=number_format($p_order['UnitPrice'])?></td>
		<td><?=number_format($p_order['ProductDiscountAmount'])?></td>
		<td><?=number_format($p_order['calculated_payAmount'])?></td>
		<? if ($rowspan[$idx - 1] > 0) { ?>
		<td class="small" rowspan="<?=$rowspan[$idx - 1]?>"><?=$p_order['ShippingFeeType']?><?=$p_order['ShippingFeeType'] != '����' ? '<br>('.number_format($p_order['DeliveryFeeAmount']).')' : '' ?></td>
		<? } ?>
		<td class="small4"><?=getCheckoutOrderStatus($p_order)?></td>
		<? if ($partial['delivery']) { ?>
		<td>
			<? if ($p_order['DeliveryMethod'] == 'DELIVERY') { ?>
			<div nowrap class="small" color="#555555">
			<?=$checkout_message_schema['deliveryCompanyType'][$p_order['DeliveryCompany']]?>
			</div>
			<div nowrap class="small" color="#555555">
			<?=$p_order['TrackingNumber']?>
			</div>
			<? } else { ?>
			<?=$checkout_message_schema['deliveryMethodType'][$p_order['DeliveryMethod']]?>
			<? } ?>
		</td>
		<? } ?>
	</tr>
	<? } ?>
	</table>

	<div style="margin:5px 0 15px 0;_border:1px solid #fff">

		<div class="fl">

			<button id="el-fnPlaceProductOrder" disabled class="default-btn-off el-product-order" onClick="fnPlaceProductOrder();return false;">����Ȯ��</button>
			<button id="el-fnDelayProductOrder" disabled class="default-btn-off el-product-order" onClick="fnDelayProductOrder();return false;">�߼�����</button>
			<button id="el-fnShipProductOrder" disabled class="default-btn-off el-product-order" onClick="fnShipProductOrder();return false;">�߼�ó��</button>

			<button id="el-fnCancelSale" disabled class="default-btn-off el-product-order" onClick="fnCancelSale();return false;">�Ǹ����</button>

			<button id="el-fnApproveCancelApplication" disabled class="default-btn-off el-product-order" onClick="fnApproveCancelApplication();return false;">��ҿ�û����</button>

			<button id="el-fnRequestReturn" disabled class="default-btn-off el-product-order" onClick="fnRequestReturn();return false;">��ǰ��û</button>
			<button id="el-fnApproveReturnApplication" disabled class="default-btn-off el-product-order" onClick="fnApproveReturnApplication();return false;">��ǰ��û����</button>

			<button id="el-fnApproveExchangeApplication" disabled class="default-btn-off el-product-order" onClick="fnApproveExchangeApplication();return false;">��ȯ��û����</button>
			<button id="el-fnApproveCollectedExchange" disabled class="default-btn-off el-product-order" onClick="fnApproveCollectedExchange();return false;">��ȯ���ſϷ�</button>
			<button id="el-fnReDeliveryExchange" disabled class="default-btn-off el-product-order" onClick="fnReDeliveryExchange();return false;">��ȯ����</button>

		</div>

		<div class="cb"></div>

	</div>

</form>

<!-- �ֹ����� -->

	<div class=title2>&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="#508900">���ֹ�����</font></div>

	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>�ֹ�����</td>
		<td style="padding:2px 10px">
		<?=(sizeof($orderStatus) === 1) ? array_shift($orderStatus) : '�κ�ó�� �� �ֹ����Դϴ�. ��� ����Ʈ�� ó�����¸� Ȯ���ϼ���.' ?>
	</tr>
	</table><p>

<!-- �������� -->
	<div class=title2>&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="#508900">�����ݾ�����</font></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>�ֹ��ݾ�</td>
		<td width=110 align=right><font class=ver8><?=number_format(gd_array_sum($orderInfo, 'calculated_ordAmount'))?></font>��</td>
		<td></td>
	</tr>
	<tr>
		<td>��ۺ�</td>
		<td width=110 align=right><font class=ver8><?=number_format($Order['DeliveryFeeAmount'])?></font>��</td>
		<td>
			<img src="../img/arrow_gray.gif" align="absmiddle">
			<font class="small" color="#444444"><?=$Order['ShippingFeeType']?></font>
		</td>
	</tr>
	<tr>
		<td>���ξ�</td>
		<?
		$_dc_per_product = gd_array_sum($orderInfo, 'ProductDiscountAmount');
		$_dc_per_order = $Order['OrderDiscountAmount'];
		?>
		<td align=right><font class=ver8>- <?=number_format($_dc_per_product + $_dc_per_order)?></font>��</td>
		<td>
			<img src="../img/arrow_gray.gif" align="absmiddle">
			<font class="small" color="#444444">
			��ǰ�� ���� (<font color=0074BA class=ver81><?=number_format($_dc_per_product)?></font>��)
			+ �ֹ� ���� (<font color=0074BA class=ver81><?=number_format($_dc_per_order)?></font>��)
			</font>

		</td>
	</tr>
	<? if (sizeof($refundAmount) > 0) { ?>
	<tr>
		<td>ȯ�ұݾ�</td>
		<td align=right><font class=ver8>- <?=number_format( array_sum($refundAmount) )?></font>��</td>
		<td>
			<img src="../img/arrow_gray.gif" align="absmiddle">
			<font class="small" color="#444444">
			��ǰ�ֹ���ȣ (<?=implode(', ',array_keys($refundAmount))?>)
			</font>

		</td>
	</tr>
	<? } ?>
	<tr>
		<td>�����ݾ�</td>
		<td align=right><font color=0074BA class=ver8><b><?=number_format( $Order['GeneralPaymentAmount'] + $Order['NaverMileagePaymentAmount'] + $Order['ChargeAmountPaymentAmount'] + $Order['CheckoutAccumulationPaymentAmount'] );?></b></font>��</td>
		<td>
			<img src="../img/arrow_gray.gif" align="absmiddle">
			<font class="small" color="#444444">
			�Ϲݰ��� (<font color=0074BA class=ver81><?=number_format($Order['GeneralPaymentAmount'])?></font>��)
			<? if ($Order['NaverMileagePaymentAmount'] > 0) { ?>+ ���̹� ���ϸ��� (<font color=0074BA class=ver81><?=number_format($Order['NaverMileagePaymentAmount'])?></font>��)<? } ?>
			<? if ($Order['ChargeAmountPaymentAmount'] > 0) { ?>+ ���̹� ������ (<font color=0074BA class=ver81><?=number_format($Order['ChargeAmountPaymentAmount'])?></font>��)<? } ?>
			<? if ($Order['CheckoutAccumulationPaymentAmount'] > 0) { ?>+ üũ�ƿ� ������ (<font color=0074BA class=ver81><?=number_format($Order['CheckoutAccumulationPaymentAmount'])?></font>��)<? } ?>
			</font>
		</td>
	</tr>
	<tr>
		<td>������ �ݾ�</td>
		<td align=right><font class=ver8><?=number_format($totalEmoney);?></font>��</td>
		<td></td>
	</tr>
	</table><p>

<table width=100% cellpadding=0 cellspacing=0>
	<col span=3 valign=top>
	<tr>
		<td width=50%>

		<!-- �ֹ������� -->
		<div class=title2>&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="#508900">�ֹ�������</font></div>
		<table class="tb">
		<col class="cellC"><col class="cellL">
		<tr>
			<td>�̸�/ID</td>
			<td><? if ($Order[m_id]) { ?><span id="navig" name="navig" m_id="<?=$Order[m_id]?>" m_no="<?=$Order[m_no]?>" popup="<?=$popup?>"><? } ?><font color=0074BA><b>
			<?=$Order[OrdererName]?>
			<? if ($Order[m_id]){ ?>/ <?=$Order[m_id]?></b></font></span>
			<? } ?>
			</td>
		</tr>
		<tr>
			<td>����ó</td>
			<td class=ver8><?=$Order[OrdererTel1]?> / <?=$Order[OrdererTel2]?></td>
		</tr>
		<tr>
			<td>�ֹ���</td>
			<td><font class=ver8><?=$Order[OrderDate]?></td>
		</tr>
		</table>

		</td>
		<td width=10 nowrap></td>
		<td width=50%>

		<!-- ���������� -->
		<div class=title2>&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="#508900">����������</font></div>
		<table class="tb">
		<col class="cellC"><col class="cellL">
		<tr>
			<td>������</td>
			<td><?=$Order[ShippingAddressName]?></td>
		</tr>
		<tr>
			<td>����ó</td>
			<td><?=$Order[ShippingAddressTel1]?> / <?=$Order[ShippingAddressTel2]?></td>
		</tr>
		<tr>
			<td>�ּ�</td>
			<td>
				<font color="#444444"><?=$Order[ShippingAddressZipCode]?></font><br>
				<?=$Order['ShippingAddressBaseAddress']?> <?=$Order['ShippingAddressDetailedAddress']?>
			</td>
		</tr>
		</table>

		</td>
	</tr>
	</tr><tr><td height=15></td></tr>
	<tr>
		<td width=50%>

		<!-- �������� -->
		<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>��������</div>
		<table class=tb>
		<col class=cellC><col class=cellL>
		<tr>
			<td>���� �Ͻ�(���� ����)</td>
			<td><?=$Order['PaymentDate']?></td>
		</tr>
		<tr>
			<td>���� ����</td>
			<td><?=$checkout_message_schema['payMeansClassType'][$Order['PaymentMeans']]?></td>
		</tr>
		<tr>
			<td>�Ա� ����</td>
			<td><?=$Order['PaymentDueDate']?></td>
		</tr>
		<tr>
			<td>���� ��ȣ</td>
			<td><?=$Order['PaymentNumber']?></td>
		</tr>
		</table>

		</td>
		<td width=10 nowrap></td>
		<td width=50%>

		<!-- ������� -->
		<div class=title2>&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="#508900">�������</font></div>

		<? foreach($deliveryInfo as $_delivery) { ?>

		<table class="tb" style="margin-bottom:15px;">
		<col class="cellC"><col class="cellL">
		<tr>
			<td>��۹��</td>
			<td><?=$checkout_message_schema['deliveryMethodType'][$_delivery['DeliveryMethod']]?></td>
		</tr>
		<tr>
			<td>��ۻ���</td>
			<td><?=$_delivery['DeliveryStatus']?></td>
		</tr>

		<tr>
			<td>�����ȣ</td>
			<td>
				<?=$checkout_message_schema['deliveryCompanyType'][$_delivery['DeliveryCompany']]?> / <?=$_delivery['TrackingNumber']?>
				<a href="javascript:void(0);" onClick="fnDeliveryTrace('checkout','<?=$_delivery['DeliveryCompany']?>','<?=$_delivery['TrackingNumber']?>');"><img src="../img/btn_delifind.gif" border=0></a>
			</td>
		</tr>
		<tr>
			<td>�߼� �Ͻ�</td>
			<td><font class=ver8><?=$_delivery[SendDate]?></td>
		</tr>
		<tr>
			<td>��ȭ �Ͻ�</td>
			<td><font class=ver8><?=$_delivery[PickupDate]?></td>
		</tr>
		<tr>
			<td>��� �Ϸ� �Ͻ�</td>
			<td><font class=ver8><?=$_delivery[DeliveredDate]?></td>
		</tr>
		</table>

		<? } ?>

		</td>
	</tr>
	</tr><tr><td height=15></td></tr>
	<tr>
		<td colspan="3">
		<?
		if ($has['claim']) {
			$claimInfo = array();

			foreach($orderInfo as $_Order) {

				if (empty($_Order['claimInfo'])) continue;

				// �߰� ������ ����
				$_Order['claimInfo']['ClaimType'] = $_Order['ClaimType'];

				$claimInfo[] = $_Order['claimInfo'];
				if ($partial['claim'] == false) break;
			}
		?>
		<!-- Ŭ����(���/��ǰ/��ȯ) -->
		<div class=title2>&nbsp;<img src="../img/icon_process.gif" align=absmiddle><font color=508900>Ŭ����(���/��ǰ/��ȯ) ����</div>

		<?
			foreach ($claimInfo as $claim) {

				$schema = $checkout_message_schema['claim'.$claim['ClaimType']];

		?>
				<table class=tb style="margin-bottom:15px;">
				<col class=cellC><col class=cellL>
				<tr>
					<td>��ǰ�ֹ���ȣ</td>
					<td><?=$claim['ProductOrderID']?></td>
				</tr>
				<? foreach ($schema as $field => $name) { ?>
				<?
					$_field = is_array($name) ? $name['name'] : $name;
					$_value = is_array($name) ? $checkout_message_schema[$name['schema']][$claim[$field]] : $claim[$field];

					if (!$_value) continue;
				?>
				<tr>
					<td><?=$_field?></td>
					<td><?=nl2br($_value)?></td>
				</tr>
				<? } ?>
				</table>
		<?
			}

		}
		?>
		</td>

	</tr>
</table>