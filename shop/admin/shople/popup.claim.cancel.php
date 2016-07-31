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
	opener.nsShople.claim.cancel.<?=$mode?>( data );
	self.close();
	return false;
}
</script>

<div class="title title_top" style="margin-top:10px;">주문취소 거부처리<span>&nbsp;</span></div>

<p class="gd_notice">
<span>이미 발송된 상품에 대해 송장번호를 입력하시면 취소요청이 겁되고 구매자에게 자동으로 취소불가에 대한 안내 메일이 발송됩니다.</span>
<span class="red">취소요청한 상품이 묶음배송 상품이라면 [취소불가]처리시, 묶음배송상품까지 모두 [발송처리]됩니다.</span>
</p>

<form name="frmClaim" id="frmClaim" method="post" action="" onSubmit="return fnPutReason();">
<div class="title title_top" style="margin-top:10px;">발송 정보를 입력하시면 취소요청 거부 처리 됩니다.</div>
<table class="tb">
<col class="cellC"><col class="cellL">
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
