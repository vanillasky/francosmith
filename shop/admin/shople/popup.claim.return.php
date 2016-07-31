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
	opener.nsShople.claim.return_.<?=$mode?>( data );
	self.close();
	return false;
}
</script>

<div class="title title_top" style="margin-top:10px;">반품보류 처리<span>&nbsp;</span></div>

<p class="gd_notice">
<? if ($mode == 'hold') { ?>
<span>보류사유를 입력하시고 [확인]을 클릭하시면, 선택한 주문에 대해 일괄 적용됩니다.</span>
<span>반품보류 후 구매자와 협의하시어 반품완료를 할 수 있도록 협조바랍니다.</span>
<span>보류 후에는 [보류해제/반품완료]만 가능하며 거부하실 수 없으니 유의하시기 바랍니다.</span>
<span>보류 후 구매자와의 협의가 곤랜하신 경우 11번가 판매고객센터르 문의하시기 바랍니다.</span>
<? } else if ($mode == 'reject') { ?>
<span>선택한 주문에 대해 반품거부 사유가 일괄 반영됩니다.</span>
<span>내용 입력 후 [확인]버튼을 누르시면 거부처리가 되고 입력 내용은 고객에게 SMS, 이메일로 발송됩니다.</span>
<? } else if ($mode == 'accepthold') { ?>
<span>반품완료보류를 하시면 자동으로 반품완료처리 되지 않습니다.</span>
<span>반품완료보류 처리 후 반드시 반품완료 처리를 해주시기 바랍니다.</span>
<span>반품완료가 장기간 미처리 되면 고객센터 확인 후 강제 환불 처리될 수 있습니다.</span>
<? } else { }?>
</p>

<form name="frmClaim" id="frmClaim" method="post" action="" onSubmit="return fnPutReason();">
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>사유</td>
	<td>
		<select name="reasonCD">
		<option value="">선택</option>
		<? foreach (${'_spt_ar_clm_return_'.$mode.'_type'} as $k => $v) { ?>
		<option value="<?=$k?>"><?=$v?></option>
		<? } ?>
		</select>
	</td>
</tr>
<tr>
	<td>상세내용</td>
	<td><textarea name="reasonCont"></textarea></td>
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
