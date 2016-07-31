<?php
include '../_header.popup.php';
$checkout_message_schema = include "./_cfg.checkout.php";

?>
<script type="text/javascript">
function fnCheckForm(f) {

	if (f.ReDeliveryMethodCode.value == '') {
		alert('배송 방법을 선택해 주세요.');
		return false;
	}

	if (f.ReDeliveryMethodCode.value == 'DELIVERY') {

		f.ReDeliveryTrackingNumber.value = f.ReDeliveryTrackingNumber.value.trim();

		if (f.ReDeliveryCompanyCode.value == '') {
			alert('택배사를 선택해 주세요.');
			return false;
		}

		if (f.ReDeliveryTrackingNumber.value == '') {
			alert('송장 번호를 입력해 주세요.');
			return false;
		}

	}

	return true;
}

function fnSetDeliveryFields(v) {

	var f = document.frmNaverCheckout;

	switch (v) {

		case 'DELIVERY':
			f.ReDeliveryCompanyCode.disabled = false;
			f.ReDeliveryTrackingNumber.disabled = false;
			break;
		case 'GDFW_ISSUE_SVC' :
		case 'VISIT_RECEIPT' :
		case 'DIRECT_DELIVERY' :
		case 'QUICK_SVC' :
		case 'NOTHING' :
		default :
			f.ReDeliveryCompanyCode.disabled = true;
			f.ReDeliveryTrackingNumber.disabled = true;
			break;
	}

}
</script>

<div class="title title_top">교환 재배송 처리</div>

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
	<td>배송 방법</td>
	<td>
		<select name="ReDeliveryMethodCode" onChange="fnSetDeliveryFields(this.value);">
		<option value=""> == 선택 == </option>
		<? foreach ($checkout_message_schema['deliveryMethodType'] as $code => $name) { ?>
		<? if (strpos($code,'RETURN_') === 0) continue;?>
		<option value="<?=$code?>"><?=$name?></option>
		<? } ?>
		</select>
	</td>
</tr>
<tr>
	<td>택배사</td>
	<td>
		<select name="ReDeliveryCompanyCode" disabled>
		<option value=""> == 선택 == </option>
		<? foreach ($checkout_message_schema['selectDeliveryCompanyType'] as $code => $name) { ?>
		<option value="<?=$code?>"><?=$name?></option>
		<? } ?>
		</select>
	</td>
</tr>
<tr>
	<td>송장 번호</td>
	<td>
		<input type="text" name="ReDeliveryTrackingNumber" class="lline" disabled>
	</td>
</tr>
</table>

<div class="button">
	<input type="image" src="../img/btn_confirm.gif">
	<a href="javascript:void(0)" onClick="parent.closeLayer();"><img src="../img/btn_cancel.gif"></a>

</div>
</form>

<script>table_design_load();</script>