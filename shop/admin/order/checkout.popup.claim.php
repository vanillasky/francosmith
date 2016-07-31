<?php
include '../_header.popup.php';
$checkout_message_schema = include "./_cfg.checkout.php";

$_title = array(
	'ApproveCancelApplication' => '취소 요청 승인',
	'ApproveReturnApplication' => '반품 요청 승인',
	'ApproveExchangeApplication' => '교환 요청 승인',
);
?>
<script type="text/javascript">
function fnCheckForm(f) {

	f.EtcFeeDemandAmount.value = f.EtcFeeDemandAmount.value.replace(/[^0-9]/g,'');

	if (f.EtcFeeDemandAmount.value == '') {
		alert('기타 비용 청구액을 입력해 주세요.');
		return false;
	}

	return true;

}
</script>

<div class="title title_top"><?=$_title[$_POST['mode']]?></div>

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
	<td>기타 비용 청구액</td>
	<td>
		<input type="text" name="EtcFeeDemandAmount" value="0"  size=12 class=line> <span class="extext">청구액이 없을 경우 0 을 입력해 주세요.</span>
	</td>
</tr>
<tr>
	<td>구매자 전달 메시지</td>
	<td>
		<textarea name="Memo" style="width:100%;height:100px;"></textarea>
	</td>
</tr>
</table>

<div class="button">
	<input type="image" src="../img/btn_confirm.gif">
	<a href="javascript:void(0)" onClick="parent.closeLayer();"><img src="../img/btn_cancel.gif"></a>

</div>
</form>

<script>table_design_load();</script>