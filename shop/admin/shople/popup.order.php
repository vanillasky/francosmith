<?
$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';
include "../_header.popup.php";
require_once ('./_inc/config.inc.php');

$mode = $_GET['m'];

$shople = Core::loader('shople');
?>
<script type="text/javascript" src="./_inc/common.js"></script>
<!-- * -->
<script type="text/javascript">
function fnPutReason() {

	var data = $('frmClaim').serialize().toQueryParams();
	opener.nsShople.order.<?=$mode?>( data );
	self.close();
	return false;
}
</script>

<div class="title title_top" style="margin-top:10px;">판매불가 처리<span>&nbsp;</span></div>

<p class="gd_notice">
<span>구매자의 취소요청시 ‘고객변심’ 사유를 선택하면 신용점수 차감 없이 직접 취소처리를 할 수 있습니다.</span>
<span>‘배송지연예상, 품절 등’에 의한 판매자귀책 사유의 판매불가시 신용점수가 -1점 처리 됩니다.</span>
<span>구매자와 협의없이 악의적인 목적으로 취소처리가 되면 신용점수 -5점 처리되고, 고객센터로부터 제재를 받을 수 있습니다.</span>
<span>주문번호별로 처리하시려는 상품(옵션)의 수량 선택 후 판매불가처리 완료를 하시면 구매자에게 판매자대행의 처리결과가 e메일과 SMS로 즉시 안내됩니다.</span>
<span class="red">취소완료시 구매자가 주문 시에 사용하신 제휴포인트는 포인트로 환불되고, 신용카드로 주문/결제하신 경우 카드사에 따라 취소 상품의 부분취소가 불가능할 수 있으니 이 경우에는 잔여상품에 대해 재결제를 하도록 반드시 안내해주시기 바랍니다.</span>
</p>

<form name="frmClaim" id="frmClaim" method="post" action="" onSubmit="return fnPutReason();">
<table class="tb">
<col class="cellC"><col class="cellL">
<? if ($mode == 'delivery') { ?>
<tr>
	<td>발송일</td>
	<td><input type=text name="sendDt" value="<?=date('Ymd')?>" onclick="calendar(event)" onkeydown="onlynumber()"></td>
</tr>
<tr>
	<td>배송방법</td>
	<td>
		<select name="dlvMthdCd">
		<? foreach ($_spt_ar_dlv_type as $k => $v) { ?>
		<option value="<?=$k?>" <?=('01' == $k ? 'selected' : '')?>><?=$v?></option>
		<? } ?>
		</select>
	</td>
</tr>
<tr>
	<td>택배사선택</td>
	<td>
		<select name="dlvEtprsCd">
		<option value="">선택</option>
		<? foreach ($_spt_ar_dlv_company as $k => $v) { ?>
		<option value="<?=$k?>" <?=($shople->cfg['dlv_company'] == $k ? 'selected' : '')?>><?=$v?></option>
		<? } ?>
		</select>
	</td>
</tr>
<tr>
	<td>송장번호입력</td>
	<td><input type=text name="invcNo" value="" onkeydown="onlynumber()"></td>
</tr>
<? } else if ($mode == 'reject') { ?>
<tr>
	<td>불가사유</td>
	<td>
		<select name="ordCnRsnCd">
		<option value="">선택</option>
		<? foreach ($_spt_ar_ord_reject_type as $k => $v) { ?>
		<option value="<?=$k?>"><?=$v?></option>
		<? } ?>
		</select>
	</td>
</tr>
<tr>
	<td>상세</td>
	<td><input type=text name="ordCnDtlsRsn" value=""></td>
</tr>
<? } ?>
</table>

<div class="button">
<input type="image" src="../img/btn_save.gif">
<img src="../img/btn_cancel.gif" class="hand" onClick="self.close();">

</div>
</form>

<!-- eof * -->
<script type="text/javascript">
linecss();
table_design_load();
</script>
</body>
</html>
