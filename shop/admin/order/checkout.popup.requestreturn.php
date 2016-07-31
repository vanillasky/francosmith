<?php
include '../_header.popup.php';
$checkout_message_schema = include "./_cfg.checkout.php";

?>
<script type="text/javascript">
function fnCheckForm(f) {

	f.CollectTrackingNumber.value = f.CollectTrackingNumber.value.trim();

	if (f.ReturnReasonCode.value == '') {
		alert('��ǰ ������ ������ �ּ���.');
		return false;
	}

	if (f.CollectDeliveryMethodCode.value == '') {
		alert('���� ��� ����� ������ �ּ���.');
		return false;
	}

	if (f.CollectDeliveryCompanyCode.value != '' || f.CollectTrackingNumber.value != '') {

		if (f.CollectDeliveryCompanyCode.value == '') {
			alert('���� �ù�縦 ������ �ּ���.');
			return false;
		}

		if (f.CollectTrackingNumber.value == '') {
			alert('���� ���� ��ȣ�� �Է��� �ּ���.');
			return false;
		}

	}

	return true;
}
</script>

<div class="title title_top">��ǰ��û</div>

<form name="frmNaverCheckout" method="post" action="./checkout.api.process.php" target="_self" onSubmit="return fnCheckForm(this);">
<input type="hidden" name="mode" value="<?=$_POST['mode']?>" />
<? foreach ($_POST['OrderID'] as $k => $OrderID) { ?>
<input type="hidden" name="OrderID[<?=$k?>]" value="<?=$OrderID?>" />
<? if (isset($_POST['ProductOrderIDList'][$k])) { ?>
<input type="hidden" name="ProductOrderIDList[<?=$k?>]" value="<?=$_POST['ProductOrderIDList'][$k]?>" />
<? } ?>
<? } ?>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>��ǰ ����</td>
	<td>
		<select name="ReturnReasonCode">
		<option value=""> == ���� == </option>
		<? foreach ($checkout_message_schema['claimRequestReasonType'] as $code => $name) { ?>
		<option value="<?=$code?>"><?=$name?></option>
		<? } ?>
		</select>
	</td>
</tr>
<tr>
	<td>���� ��� ���</td>
	<td>
		<select name="CollectDeliveryMethodCode">
		<option value=""> == ���� == </option>
		<? foreach ($checkout_message_schema['deliveryMethodType'] as $code => $name) { ?>
		<? if ($code != 'RETURN_INDIVIDUAL') continue;?>
		<option value="<?=$code?>"><?=$name?></option>
		<? } ?>
		</select>
	</td>
</tr>
<tr>
	<td>���� �ù��</td>
	<td>
		<select name="CollectDeliveryCompanyCode">
		<option value=""> == ���� == </option>
		<? foreach ($checkout_message_schema['selectDeliveryCompanyType'] as $code => $name) { ?>
		<option value="<?=$code?>"><?=$name?></option>
		<? } ?>
		</select>
	</td>
</tr>
<tr>
	<td>���� ���� ��ȣ</td>
	<td>
		<input type="text" name="CollectTrackingNumber" class="lline">
	</td>
</tr>
</table>

<div class="button">
	<input type="image" src="../img/btn_confirm.gif">
	<a href="javascript:void(0)" onClick="parent.closeLayer();"><img src="../img/btn_cancel.gif"></a>

</div>
</form>

<script>table_design_load();</script>