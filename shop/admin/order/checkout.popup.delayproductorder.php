<?php
include '../_header.popup.php';
$checkout_message_schema = include "./_cfg.checkout.php";

?>
<script type="text/javascript">
function fnCheckForm(f) {

	if (f.DispatchDueDate.value == '') {
		alert('발송 기한을 입력해 주세요.');
		return false;
	}

	var  today   = new Date();
	var _dueDate = f.DispatchDueDate.value.replace(/[^0-9]/g,'');
	var  dueDate = new Date(_dueDate.substring(0,4), parseInt(_dueDate.substring(4,6)) - 1,  _dueDate.substring(6,8));

	if (today >= dueDate) {
		alert('발송 기한은 익일(다음날) 부터 가능합니다.');
		return false;
	}

	if (f.DispatchDelayReasonCode.value == '') {
		alert('발송 지연 사유를 선택해 주세요.');
		return false;
	}

	return true;
}
</script>

<div class="title title_top">발송지연 처리</div>

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
	<td>발송 기한</td>
	<td>
		<input type="text" name="DispatchDueDate" value="<?=date('Ymd', strtotime('+1 day'))?>" onclick="calendar(event)" size=12 class=line readonly>
	</td>
</tr>
<tr>
	<td>발송 지연 사유</td>
	<td>
		<select name="DispatchDelayReasonCode">
		<option value=""> == 선택 == </option>
		<? foreach ($checkout_message_schema['delayedDispatchReasonType'] as $code => $name) { ?>
		<option value="<?=$code?>"><?=$name?></option>
		<? } ?>
		</select>
	</td>
</tr>
<tr>
	<td>상세 사유</td>
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