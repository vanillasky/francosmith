<?php
include '../_header.popup.php';
$checkout_message_schema = include "./_cfg.checkout.php";

?>
<script type="text/javascript">
function fnCheckForm(f) {
	if (f.CancelReasonCode.value == '')
	{
		alert('��� ������ ������ �ּ���.');
		return false;
	}

	return true;
}
</script>

<div class="title title_top">�Ǹ� ��� ó��</div>

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
	<td>��� ����</td>
	<td>
		<select name="CancelReasonCode">
		<option value=""> == ���� == </option>
		<? foreach ($checkout_message_schema['claimRequestReasonType'] as $code => $name) { ?>
		<? if (!in_array($code, array('PRODUCT_UNSATISFIED','DELAYED_DELIVERY','SOLD_OUT'))) continue;?>
		<? ?>
		<option value="<?=$code?>"><?=$name?></option>
		<? } ?>
		</select>
	</td>
</tr>
</table>

<div class="button">
	<input type="image" src="../img/btn_confirm.gif">
	<a href="javascript:void(0)" onClick="parent.closeLayer();"><img src="../img/btn_cancel.gif"></a>

</div>
</form>

<script>table_design_load();</script>