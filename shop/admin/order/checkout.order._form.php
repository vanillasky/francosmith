<?php
/**
 * ���̹�üũ�ƿ� �ֹ� > �ֹ��󼼳���
 * @author sunny, oneorzero
 */
$naverCheckoutAPI = Core::loader('naverCheckoutAPI');

function dateOutput($var) {
	if($var=='0000-00-00 00:00:00') {
		return '';
	}
	return preg_replace('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/','$1�� $2�� $3�� $4�� $5��',$var);
}

$orderNo = (int)$_GET['orderNo'];
$OrderID = (int)$_GET['OrderID'];

if($OrderID) {
	$query = $db->_query_print('select orderNo from gd_navercheckout_order where ORDER_OrderID=[s]',$OrderID);
	$result = $db->_select($query);
	$orderNo = $result[0]['orderNo'];
}

$naverCheckoutAPI->noEmoney = true;
$naverCheckoutAPI->SyncOrder($orderNo);

$query = $db->_query_print('select * from gd_navercheckout_order where orderNo=[s]',$orderNo);
$result = $db->_select($query);
$orderInfo = $result[0];


$query = $db->_query_print('select * from gd_navercheckout_order_product where orderNo=[s] order by seq asc',$orderNo);
$productList = $db->_select($query);

// ��� ������ �׼�
$isPlaceOrder=$isCancelSale=$isCancelOrder=$isShipOrder=$isCancelShipping=false;
// �ֹ��������� ����
if($orderInfo['ORDER_OrderStatusCode']=='OD0002') {
	$isPlaceOrder=true;
}
//�Ǹ���Ұ��� ����
if($orderInfo['ORDER_OrderStatusCode']=='OD0002') {
	$isCancelSale=true;
}
//�ֹ���Ұ��� ����
if(in_array($orderInfo['ORDER_OrderStatusCode'],array('OD0007','OD0008','OD0009','OD0010','OD0011'))) {
	$isCancelOrder=true;
}
//�߼�ó������ ����
if(in_array($orderInfo['ORDER_OrderStatusCode'],array('OD0007','OD0008'))) {
	$isShipOrder=true;
}
//�߼���Ұ��� ����
if(in_array($orderInfo['ORDER_OrderStatusCode'],array('OD0009','OD0010','OD0011'))) {
	$isCancelShipping=true;
}
list($totalEmoney) = $db->fetch("SELECT SUM(emoney) FROM gd_navercheckout_order_product WHERE orderNo='$orderNo'");
?>
<? include('checkout.common.php'); ?>
<script type="text/javascript">
document.observe("dom:loaded", function() {
	<? if($isShipOrder): ?>
	Event.observe($("frmShipOrder").select(".selShippingCompany")[0], 'change', function(event) {
		var element = $(Event.element(event));
		if(element.value=='z_etc' || element.value=='z_quick' || element.value=='z_direct' || element.value=='z_visit' || element.value=='z_delegation') {
			$("frmShipOrder").select(".iptTrackingNumber")[0].value='';
			$("frmShipOrder").select(".iptTrackingNumber")[0].disabled=true;
			$("frmShipOrder").select(".iptTrackingNumber")[0].style.backgroundColor="#cccccc";
		}
		else {
			$("frmShipOrder").select(".iptTrackingNumber")[0].disabled=false;
			$("frmShipOrder").select(".iptTrackingNumber")[0].style.backgroundColor="#ffffff";
		}
	});
	<? endif; ?>
});
function syncOrder() {
	customPopupLayer('checkout.api.SyncOrder.php?orderNo=<?=$orderNo?>',600,400);
}

function callPlaceOrder() {
	customPopupLayer('about:blank',780,500);
	$('frmPlaceOrder').submit();
}

function callShipOrder() {
	var eleShippingCompleteDate = $("frmShipOrder").select(".iptShippingCompleteDate")[0];
	var eleShippingCompany = $("frmShipOrder").select(".selShippingCompany")[0];
	var eleTrackingNumber = $("frmShipOrder").select(".iptTrackingNumber")[0];

	if(eleShippingCompleteDate.value.length==0) {
		alert('�����Ͻ� �ֹ��� �߼�ó���ϱ� ���ؼ��� ������� �Է��ϼž� �մϴ�');
		return;
	}
	if(eleShippingCompany.selectedIndex==0) {
		alert('�����Ͻ� �ֹ��� �߼�ó���ϱ� ���ؼ��� ��۹���� �����ϼž� �մϴ�');
		return;
	}
	var tmp = eleShippingCompany.options[eleShippingCompany.selectedIndex].value;
	if(!(tmp=='z_etc' || tmp=='z_quick' || tmp=='z_direct' || tmp=='z_visit' || tmp=='z_delegation')) {
		if(eleTrackingNumber.value.length==0) {
			alert('�����Ͻ� �ֹ��� �߼�ó���ϱ� ���ؼ��� �����ȣ�� �Է��ϼž� �մϴ�');
			return;
		}
	}

	customPopupLayer('about:blank',780,500);
	$('frmShipOrder').submit();
}

function callCancelSale() {
	if($("frmCancelSale").CancelReasonDetail.value.length==0) {
		alert('�Ǹ� ��� �޼����� �����ּ���');
		return;
	}
	customPopupLayer('about:blank',780,500);
	$('frmCancelSale').submit();
}

function callCancelOrder() {
	if($("frmCancelOrder").CancelReasonDetail.value.length==0) {
		alert('�ֹ� ��� �޼����� �����ּ���');
		return;
	}
	customPopupLayer('about:blank',780,500);
	$('frmCancelOrder').submit();
}

function callCancelShipping() {
	customPopupLayer('about:blank',780,500);
	$('frmCancelShipping').submit();
}
</script>
<div class="title title_top">�ֹ���</div>

<table class="tb" cellpadding="4" cellspacing="0">
<tr height="25" bgcolor="#2E2B29" class="small4" style="padding-top:8px">
	<th><font color="white">��ȣ</font></th>
	<th><font color="white">��ǰ��</font></th>
	<th><font color="white">�ɼ�</font></th>
	<th><font color="white">����</font></th>
	<th><font color="white">��ǰ����</font></th>
	<th><font color="white">��ǰ��û����</font></th>
</tr>
<col align=center>
<col>
<col align=center span=4>
<? foreach($productList as $eachProduct): ?>
<tr>
	<td width=70 nowrap><?=$eachProduct['seq']?></td>
	<td width=100%><?=htmlspecialchars($eachProduct['ProductName'])?></td>
	<td width=200 nowrap><?=htmlspecialchars($eachProduct['ProductOption'])?></td>
	<td width=80 nowrap><?=number_format($eachProduct['Quantity'])?></td>
	<td width=150 nowrap><?=number_format($eachProduct['UnitPrice'])?>��</td>
	<td width=120 nowrap><?=$eachProduct['ReturnRequested']=='y'?'��':'�ƴϿ�'?></td>
</tr>
<? endforeach; ?>
</table>
<br><br>

<? if($isPlaceOrder): ?>
<form id="frmPlaceOrder" action="checkout.api.PlaceOrder.php" target="processLayerForm" method="post">
<input type="hidden" name="orderNo[]" value="<?=$orderInfo['orderNo']?>">
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�ֹ�����ó��</td>
	<td><input type="button" value="�ֹ������ϱ�" onclick="callPlaceOrder()"></td>
</tr>
</table>
</form>
<br>
<? endif; ?>

<? if($isShipOrder): ?>
<form id="frmShipOrder" action="checkout.api.ShipOrder.php" target="processLayerForm" method="post">
<input type="hidden" name="request[0][orderNo]" value="<?=$orderInfo['orderNo']?>">
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�߼�ó��</td>
	<td>
		<table cellpadding="0">
		<tr>
		<td>����� :</td>
		<td>
		<input type="text" name="request[0][ShippingCompleteDate]" value="" onclick="calendar(event)" readonly style="width:100px" class="iptShippingCompleteDate">
		</td>
		<tr>
		<td>��۹�� :</td>
		<td>
		<select name="request[0][ShippingCompany]" style="width:95%;font-size:7pt;" class="selShippingCompany">
		<option value="">(����)</option>
		<option value="korex">�������</option>
		<option value="cjgls">CJGLS</option>
		<option value="sagawa">SC ������</option>
		<option value="yellow">���ο�ĸ</option>
		<option value="kgb">�����ù�</option>
		<option value="dongbu">�����ͽ��������ù�</option>
		<option value="EPOST">��ü���ù�</option>
		<option value="hanjin">�����ù�</option>
		<option value="hyundai">�����ù�</option>
		<option value="kgbls">KGB �ù�</option>
		<option value="z_etc">��Ÿ �ù�</option>
		<option value="z_quick">������</option>
		<option value="z_direct">�����</option>
		<option value="z_visit">�湮 ����</option>
		<option value="z_post">���� ���</option>
		<option value="z_delegation">��ü�� ���</option>
		<option value="kdexp">�浿�ù�</option>
		</select>
		</td>
		</tr>
		<tr>
		<td>�����ȣ :</td>
		<td><input type="text" name="request[0][TrackingNumber]" style="width:300px" class="iptTrackingNumber"></td>
		</tr>
		<tr>
		<td></td>
		<td> <input type="button" value="�߼�ó���ϱ� " onclick="callShipOrder()"></td>
		</tr>
		</table>
	</td>
</tr>
</table></form>
<br>
<? endif; ?>

<? if($isCancelSale): ?>
<form id="frmCancelSale" action="checkout.api.CancelSale.php" target="processLayerForm" method="post">
<input type="hidden" name="orderNo[]" value="<?=$orderInfo['orderNo']?>">
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�Ǹ����ó��</td>
	<td>
		<table cellpadding="0">
		<tr>
		<td>�Ǹ���� ���� :</td>
		<td>
		<select name="CancelReason" style="width:100px">
		<option value="41"> ǰ��</option>
		<option value="42"> ��ǰ ����</option>
		<option value="43"> ���� ����</option>
		<option value="44"> ��Ÿ</option>
		</select>
		</td>
		<tr>
		<td>�Ǹ���� �޼��� :</td>
		<td><input type="text" name="CancelReasonDetail" style="width:300px"></td>
		</tr>
		<tr>
		<td></td>
		<td> <input type="button" value="�Ǹ�����ϱ� " onclick="callCancelSale()"></td>
		</tr>
		</table>
	</td>
</tr>
</table></form>
<br>
<? endif; ?>

<? if($isCancelOrder): ?>
<form id="frmCancelOrder" action="checkout.api.CancelOrder.php" target="processLayerForm" method="post">
<input type="hidden" name="orderNo[]" value="<?=$orderInfo['orderNo']?>">
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�ֹ����ó��</td>
	<td>
		<table cellpadding="0">
		<tr>
		<td>�ֹ���� ���� :</td>
		<td>
		<select name="CancelReason" style="width:100px">
		<option value="31"> �ʿ伺 ���</option>
		<option value="32"> �ܼ� ����</option>
		<option value="33"> ���� ���� ����</option>
		<option value="34"> ���� �Ҹ�</option>
		<option value="35"> �̹� ��������(��������)</option>
		<option value="36"> ���� �ֹ�</option>
		<option value="37"> ��Ÿ</option>
		</select>
		</td>
		<tr>
		<td>�ֹ���� �޼��� :</td>
		<td><input type="text" name="CancelReasonDetail" style="width:300px"></td>
		</tr>
		<tr>
		<td></td>
		<td> <input type="button" value="�ֹ�����ϱ� " onclick="callCancelOrder()"></td>
		</tr>
		</table>
	</td>
</tr>
</table></form>
<br>
<? endif; ?>

<? if($isCancelShipping): ?>
<form id="frmCancelShipping" action="checkout.api.CancelShipping.php" target="processLayerForm" method="post">
<input type="hidden" name="orderNo[]" value="<?=$orderInfo['orderNo']?>">
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�߼����ó��</td>
	<td><input type="button" value="�߼�����ϱ�" onclick="callCancelShipping()"></td>
</tr>
</table>
</form>
<br>
<? endif; ?>


<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>�ֹ�����</b></font></div>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�ֹ� �Ͻ�</td>
	<td><?=dateOutput($orderInfo['ORDER_OrderDateTime'])?></td>
</tr>
<tr>
	<td>�ֹ� ����</td>
	<td><?=htmlspecialchars($orderInfo['ORDER_OrderStatus'])?></td>
</tr>
<tr>
	<td>����� ����</td>
	<td><?=htmlspecialchars($orderInfo['ORDER_Repayment'])?></td>
</tr>
<tr>
	<td>�Ǹ� �Ϸ� �Ͻ�</td>
	<td><?=dateOutput($orderInfo['ORDER_SaleCompleteDate'])?></td>
</tr>
<tr>
	<td>���� ���� �Ͻ�</td>
	<td><?=dateOutput($orderInfo['ORDER_PaymentDueDate'])?></td>
</tr>
<tr>
	<td>���� ��ȣ</td>
	<td><?=htmlspecialchars($orderInfo['ORDER_PaymentNumber'])?></td>
</tr>
<tr>
	<td>�Ա� ����</td>
	<td><?=htmlspecialchars($orderInfo['ORDER_PaymentBank'])?></td>
</tr>
<tr>
	<td>�Ա��� �̸�</td>
	<td><?=htmlspecialchars($orderInfo['ORDER_PaymentSender'])?></td>
</tr>
<tr>
	<td>���� �ڵ�</td>
	<td><?=htmlspecialchars($orderInfo['ORDER_SellingCode'])?></td>
</tr>
<tr>
	<td>�ֹ� �߰� ����</td>
	<td><?=htmlspecialchars($orderInfo['ORDER_OrderExtraData'])?></td>
</tr>
</table>


<br><br>
<table border="0" width="100%">
<tr>
<td width="50%" valign="top">
	<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>������������</b></font></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>�� �ǸŰ�</td>
		<td><?=number_format($orderInfo['ORDER_TotalProductAmount'])?>��</td>
	</tr>
	<tr>
		<td>��ۺ�</td>
		<td><?=number_format($orderInfo['ORDER_ShippingFee'])?>��</td>
	</tr>
	<tr>
		<td>������ �� �ֹ� �ݾ�</td>
		<td><?=number_format($orderInfo['ORDER_MallOrderAmount'])?>��</td>
	</tr>
	<tr>
		<td>���̹� ���� �ݾ�</td>
		<td><?=number_format($orderInfo['ORDER_NaverDiscountAmount'])?>��</td>
	</tr>
	<tr>
		<td>�� �ֹ� �ݾ�</td>
		<td><?=number_format($orderInfo['ORDER_TotalOrderAmount'])?>��</td>
	</tr>
	<tr>
		<td>������ ��� �ݾ�</td>
		<td><?=number_format($orderInfo['ORDER_CashbackDiscountAmount'])?>��</td>
	</tr>
	<tr>
		<td>���� �ݾ�</td>
		<td><?=number_format($orderInfo['ORDER_PaymentAmount'])?>��</td>
	</tr>
	<tr>
		<td>���� ���</td>
		<td><?=htmlspecialchars($orderInfo['ORDER_PaymentMethod'])?></td>
	</tr>
	<tr>
		<td>���� �Ͻ�</td>
		<td><?=dateOutput($orderInfo['ORDER_PaymentDate'])?></td>
	</tr>
	<tr>
		<td>����ũ�� ����</td>
		<td><?=htmlspecialchars($orderInfo['ORDER_Escrow'])?></td>
	</tr>
	<tr>
		<td>��ۺ� ����</td>
		<td><?=htmlspecialchars($orderInfo['ORDER_ShippingFeeType'])?></td>
	</tr>
	<tr>
		<td>������ �ݾ�</td>
		<td><?=number_format($totalEmoney)?>��</td>
	</tr>
	</table>
</td>
<td width="50%" valign="top">
	<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>������������</b></font></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>�� �ǸŰ�</td>
		<td><?=number_format($orderInfo['ORDER_OriginalTotalProductAmount'])?>��</td>
	</tr>
	<tr>
		<td>��ۺ�</td>
		<td><?=number_format($orderInfo['ORDER_OriginalShippingFee'])?>��</td>
	</tr>
	<tr>
		<td>������ �� �ֹ� �ݾ�</td>
		<td><?=number_format($orderInfo['ORDER_OriginalMallOrderAmount'])?>��</td>
	</tr>
	<tr>
		<td>���̹� ���� �ݾ�</td>
		<td><?=number_format($orderInfo['ORDER_OriginalNaverDiscountAmount'])?>��</td>
	</tr>
	<tr>
		<td>�� �ֹ� �ݾ�</td>
		<td><?=number_format($orderInfo['ORDER_OriginalTotalOrderAmount'])?>��</td>
	</tr>
	<tr>
		<td>������ ��� �ݾ�</td>
		<td><?=number_format($orderInfo['ORDER_OriginalCashbackDiscountAmount'])?>��</td>
	</tr>
	<tr>
		<td>���� �ݾ�</td>
		<td><?=number_format($orderInfo['ORDER_OriginalPaymentAmount'])?>��</td>
	</tr>
	<tr>
		<td>���� ���</td>
		<td><?=htmlspecialchars($orderInfo['ORDER_OriginalPaymentMethod'])?></td>
	</tr>
	<tr>
		<td>���� �Ͻ�</td>
		<td><?=dateOutput($orderInfo['ORDER_OriginalPaymentDate'])?></td>
	</tr>
	<tr>
		<td>����ũ�� ����</td>
		<td><?=htmlspecialchars($orderInfo['ORDER_OriginalEscrow'])?></td>
	</tr>
	<tr>
		<td>��ۺ� ����</td>
		<td><?=htmlspecialchars($orderInfo['ORDER_OriginalShippingFeeType'])?></td>
	</tr>
	</table>
</td>
</table>


<br><br>
<table border="0" width="100%">
<tr>
<td width="50%"  valign="top">
	<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>�ֹ�������</b></font></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>�̸�</td>
		<td><?=htmlspecialchars($orderInfo['ORDER_OrdererName'])?></td>
	</tr>
	<tr>
		<td>���̵�(���̹�)</td>
		<td><?=htmlspecialchars($orderInfo['ORDER_OrdererID'])?></td>
	</tr>
	<tr>
		<td>����ó</td>
		<td><?=htmlspecialchars($orderInfo['ORDER_OrdererTel'])?></td>
	</tr>
	<tr>
		<td>���� �ּ�</td>
		<td><?=htmlspecialchars($orderInfo['ORDER_OrdererEmail'])?></td>
	</tr>
	</table>
</td>
<td width="50%" valign="top">
	<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>������ ����</b></font></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>�̸�</td>
		<td><?=htmlspecialchars($orderInfo['SHIPPING_Recipient'])?></td>
	</tr>
	<tr>
		<td>����� �ּ�</td>
		<td>(<?=substr($orderInfo['SHIPPING_ZipCode'],0,3)?>-<?=substr($orderInfo['SHIPPING_ZipCode'],3,3)?>) <br>
		<?=$orderInfo['SHIPPING_ShippingAddress1']?> <?=htmlspecialchars($orderInfo['SHIPPING_ShippingAddress2'])?> </td>
	</tr>
	<tr>
		<td>����ó1</td>
		<td><?=htmlspecialchars($orderInfo['SHIPPING_RecipientTel1'])?></td>
	</tr>
	<tr>
		<td>����ó2</td>
		<td><?=htmlspecialchars($orderInfo['SHIPPING_RecipientTel2'])?></td>
	</tr>
	<tr>
		<td>��� �޼���</td>
		<td><?=htmlspecialchars($orderInfo['SHIPPING_ShippingMessage'])?></td>
	</tr>
	</table>
</td>
</table>


<br><br>
<table border="0" width="100%">
<tr>
<td width="50%"  valign="top">
	<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>�������</b></font></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>��� �Ͻ�</td>
		<td><?=dateOutput($orderInfo['DELIVERY_SendDate'])?></td>
	</tr>
	<tr>
		<td>��ȭ �Ͻ�</td>
		<td><?=dateOutput($orderInfo['DELIVERY_PickupDate'])?></td>
	</tr>
	<tr>
		<td>��� �Ϸ� �Ͻ�</td>
		<td><?=dateOutput($orderInfo['DELIVERY_ShippingCompleteDate'])?></td>
	</tr>
	<tr>
		<td>��� ���</td>
		<td><?=htmlspecialchars($orderInfo['DELIVERY_ShippingCompany'])?></td>
	</tr>
	<tr>
		<td>��Ÿ ���</td>
		<td><?=htmlspecialchars($orderInfo['DELIVERY_EtcShipping'])?></td>
	</tr>
	<tr>
		<td>���� ��ȣ</td>
		<td><?=htmlspecialchars($orderInfo['DELIVERY_TrackingNumber'])?></td>
	</tr>
	<tr>
		<td>���� ����</td>
		<td><?=htmlspecialchars($orderInfo['DELIVERY_ShippingProcessStatus'])?></td>
	</tr>
	<tr>
		<td>��� ����</td>
		<td><?=htmlspecialchars($orderInfo['DELIVERY_ShippingStatus'])?></td>
	</tr>
	</table>
</td>
<td width="50%" valign="top">
	<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>��� ����</b></font></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>��� ����</td>
		<td><?=htmlspecialchars($orderInfo['CANCEL_CancelReason'])?></td>
	</tr>
	<tr>
		<td>��� ��û ��ü</td>
		<td><?=htmlspecialchars($orderInfo['CANCEL_CancelRequester'])?></td>
	</tr>
	<tr>
		<td>ȯ�� ��� ����</td>
		<td><?=htmlspecialchars($orderInfo['CANCEL_RefundPended'])?></td>
	</tr>
	<tr>
		<td>ȯ�� ����</td>
		<td><?=htmlspecialchars($orderInfo['CANCEL_RefundBank'])?></td>
	</tr>
	<tr>
		<td>ȯ�� ������ �̸�</td>
		<td><?=htmlspecialchars($orderInfo['CANCEL_RefundAccountOwner'])?></td>
	</tr>
	<tr>
		<td>ȯ�� ���� ��ȣ</td>
		<td><?=htmlspecialchars($orderInfo['CANCEL_RefundAccountNumber'])?></td>
	</tr>
	<tr>
		<td>��� ��û �Ͻ�</td>
		<td><?=dateOutput($orderInfo['CANCEL_CancelRequestDate'])?></td>
	</tr>
	</table>
</td>
</table>


<br><br>
<table border="0" width="100%">
<tr>
<td width="50%"  valign="top">
	<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>��ǰ����</b></font></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
	<td>��ǰ ����</td>
	<td><?=htmlspecialchars($orderInfo['RETURN_ReturnReason'])?>
	</tr>
	<tr>
	<td>��ǰ ���� ���� �ڵ�</td>
	<td><?=htmlspecialchars($orderInfo['RETURN_ReturnStatusCode'])?>
	</tr>
	<tr>
	<td>��ǰ ����</td>
	<td><?=htmlspecialchars($orderInfo['RETURN_ReturnStatus'])?>
	</tr>
	<tr>
	<td>�ݼ� �Ͻ�</td>
	<td><?=dateOutput($orderInfo['RETURN_ReturnDate'])?>
	</tr>
	<tr>
	<td>�ݼ� �ù��</td>
	<td><?=$orderInfo['RETURN_ReturnShippingCompany']?>
	</tr>
	<tr>
	<td>�ݼ� ���� ��ȣ</td>
	<td><?=htmlspecialchars($orderInfo['RETURN_ReturnTrackingNumber'])?>
	</tr>
	<tr>
	<td>�ݼ� ��ۺ� ����</td>
	<td><?=htmlspecialchars($orderInfo['RETURN_ReturnShippingFeeType'])?>
	</tr>
	<tr>
	<td>��ǰ �Լ� �Ͻ�</td>
	<td><?=dateOutput($orderInfo['RETURN_ReceivedDate'])?>
	</tr>
	<tr>
	<td>ȯ�� ����</td>
	<td><?=htmlspecialchars($orderInfo['RETURN_RefundBank'])?>
	</tr>
	<tr>
	<td>ȯ�� ������ �̸�</td>
	<td><?=htmlspecialchars($orderInfo['RETURN_RefundAccountOwner'])?>
	</tr>
	<tr>
	<td>ȯ�� ���� ��ȣ</td>
	<td><?=htmlspecialchars($orderInfo['RETURN_RefundAccountNumber'])?>
	</tr>
	<tr>
	<td>���� ����</td>
	<td><?=htmlspecialchars($orderInfo['RETURN_Protest'])?>
	</tr>
	<tr>
	<td>��ǰ ��û �Ͻ�</td>
	<td><?=dateOutput($orderInfo['RETURN_ReturnRequestDate'])?>
	</tr>
	</table>
</td>
<td width="50%" valign="top">
	<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>��ȯ����</b></font></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
	<td>��ȯ ����</td>
	<td><?=htmlspecialchars($orderInfo['EXCHANGE_ExchangeReason'])?></td>
	</tr>
	<tr>
	<td>��ȯ ���� ���� �ڵ�</td>
	<td><?=htmlspecialchars($orderInfo['EXCHANGE_ExchangeStatusCode'])?></td>
	</tr>
	<tr>
	<td>��ȯ ����</td>
	<td><?=htmlspecialchars($orderInfo['EXCHANGE_ExchangeStatus'])?></td>
	</tr>
	<tr>
	<td>�ݼ� �Ͻ�</td>
	<td><?=dateOutput($orderInfo['EXCHANGE_ReturnDate'])?></td>
	</tr>
	<tr>
	<td>�ݼ� �ù��</td>
	<td><?=htmlspecialchars($orderInfo['EXCHANGE_ReturnShippingCompany'])?></td>
	</tr>
	<tr>
	<td>�ݼ� ���� ��ȣ</td>
	<td><?=htmlspecialchars($orderInfo['EXCHANGE_ReturnTrackingNumber'])?></td>
	</tr>
	<tr>
	<td>�ݼ� ��ۺ� ����</td>
	<td><?=htmlspecialchars($orderInfo['EXCHANGE_ReturnShippingFeeType'])?></td>
	</tr>
	<tr>
	<td>��ǰ �Լ� �Ͻ�</td>
	<td><?=dateOutput($orderInfo['EXCHANGE_ReceivedDate'])?></td>
	</tr>
	<tr>
	<td>���� �Ͻ�</td>
	<td><?=dateOutput($orderInfo['EXCHANGE_ResendDate'])?></td>
	</tr>
	<tr>
	<td>���� �ù��</td>
	<td><?=htmlspecialchars($orderInfo['EXCHANGE_ResendShippingCompany'])?></td>
	</tr>
	<tr>
	<td>���� ���� ��ȣ</td>
	<td><?=htmlspecialchars($orderInfo['EXCHANGE_ResendTrackingNumber'])?></td>
	</tr>
	<tr>
	<td>���� ����</td>
	<td><?=htmlspecialchars($orderInfo['EXCHANGE_Protest'])?></td>
	</tr>
	<tr>
	<td>��ȯ ��û �Ͻ�</td>
	<td><?=dateOutput($orderInfo['EXCHANGE_ExchangeRequestDate'])?></td>
	</tr>
	<tr>
	<td>���� ������ �̸�</td>
	<td><?=htmlspecialchars($orderInfo['EXCHANGE_ResendRecipient'])?></td>
	</tr>
	<tr>
	<td>���� ����ó</td>
	<td><?=htmlspecialchars($orderInfo['EXCHANGE_ResendRecipientTel'])?></td>
	</tr>
	<tr>
	<td>���� �ּ�</td>
	<td><?=htmlspecialchars($orderInfo['EXCHANGE_ResendShippingAddress'])?></td>
	</tr>
	</table>
</td>
</table>

<br><br>
<!--
<div style="text-align:center">
	<input type="button" value="�ֹ����� ����ȭ" onclick="syncOrder()">
</div>-->
