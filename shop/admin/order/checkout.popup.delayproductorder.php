<?php
include '../_header.popup.php';
$checkout_message_schema = include "./_cfg.checkout.php";

?>
<script type="text/javascript">
function fnCheckForm(f) {

	if (f.DispatchDueDate.value == '') {
		alert('�߼� ������ �Է��� �ּ���.');
		return false;
	}

	var  today   = new Date();
	var _dueDate = f.DispatchDueDate.value.replace(/[^0-9]/g,'');
	var  dueDate = new Date(_dueDate.substring(0,4), parseInt(_dueDate.substring(4,6)) - 1,  _dueDate.substring(6,8));

	if (today >= dueDate) {
		alert('�߼� ������ ����(������) ���� �����մϴ�.');
		return false;
	}

	if (f.DispatchDelayReasonCode.value == '') {
		alert('�߼� ���� ������ ������ �ּ���.');
		return false;
	}

	return true;
}
</script>

<div class="title title_top">�߼����� ó��</div>

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
	<td>�߼� ����</td>
	<td>
		<input type="text" name="DispatchDueDate" value="<?=date('Ymd', strtotime('+1 day'))?>" onclick="calendar(event)" size=12 class=line readonly>
	</td>
</tr>
<tr>
	<td>�߼� ���� ����</td>
	<td>
		<select name="DispatchDelayReasonCode">
		<option value=""> == ���� == </option>
		<? foreach ($checkout_message_schema['delayedDispatchReasonType'] as $code => $name) { ?>
		<option value="<?=$code?>"><?=$name?></option>
		<? } ?>
		</select>
	</td>
</tr>
<tr>
	<td>�� ����</td>
	<td>
		<textarea name="DispatchDelayDetailReason" style="width:100%;height:100px;"></textarea>
	</td>
</tr>
</table>

<div class="button">
	<input type="image" src="../img/btn_confirm.gif">
	<a href="javascript:void(0)" onClick="parent.closeLayer();"><img src="../img/btn_cancel.gif"></a>

</div>
</form>

<script>table_design_load();</script>