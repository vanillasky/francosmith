<?php
include "../_header.popup.php";

// 다중 선택이 가능하므로, 시작일, 종료일은 알아서 구해온다.

if (!is_array($_GET[chk])) echo '<script>alert("기간 설정할 관련상품을 선택해 주세요.");parent.closeLayer();</script>';

$query = "SELECT min(r_start) as r_start, max(r_end) as r_end FROM ".GD_GOODS_RELATED." WHERE goodsno = '$_GET[goodsno]' AND r_goodsno in (".implode(',',$_GET[chk]).")";
$data = $db->fetch($query,1);

$data[r_start] = !empty($data[r_start]) ? explode('-',$data[r_start]) : explode('-',date('Y-m-d',G_CONST_NOW));
$data[r_end] = !empty($data[r_end]) ? explode('-',$data[r_end]) : explode('-',date('Y-m-d',G_CONST_NOW));

?>
<script>
function _chkForm(f) {



	return true;
}
function fnChangeForm(v) {
	if (v == 1)
	{
		$('el-range').setStyle({display:'block'});
	}
	else {
		$('el-range').setStyle({display:'none'});
	}
}

function fnSetDate(from,to) {
	var f = document.frmRelatedGoods;

	var from = from.split('-');
	var to = to.split('-');

	f.r_start_y.value = from[0];
	f.r_start_m.value = from[1];
	f.r_start_d.value = from[2];

	f.r_end_y.value = to[0];
	f.r_end_m.value = to[1];
	f.r_end_d.value = to[2];
}
</script>

<div class="title title_top">기간설정</div>



<form name="frmRelatedGoods" method="post" action="indb.related.php" target="ifrmHidden" onsubmit="return _chkForm(this);">
<input type="hidden" name="mode" value="range">
<input type="hidden" name="goodsno" value="<?=$_GET[goodsno]?>">
<? foreach($_GET[chk] as $r_goodsno) { ?>
<input type="hidden" name="chk[]" value="<?=$r_goodsno?>">
<? } ?>

<table class="tb">
<col class="cellC" width="100"><col class="cellL">
<tr>
	<td>노출 기간 선택</td>
	<td class="noline">
		<label><input type="radio" name="range_type" value="0" onClick="fnChangeForm(0);">지속노출</label>
		<label><input type="radio" name="range_type" value="1" onClick="fnChangeForm(1);">기간노출</label>
	</td>
</tr>
</table>



<fieldset id="el-range" style="display:none;margin-top:5px;padding:10px 5px 5px 5px;"><legend class="extext">노출 기간</legend>

	<input type="text" name="r_start_y" size="4" value="<?=$data[r_start][0]?>" maxlength="4" onkeydown="onlynumber();" class="cline">년
	<input type="text" name="r_start_m" size="2" value="<?=$data[r_start][1]?>" maxlength="2" onkeydown="onlynumber();" class="cline">월
	<input type="text" name="r_start_d" size="2" value="<?=$data[r_start][2]?>" maxlength="2" onkeydown="onlynumber();" class="cline">일
	~
	<input type="text" name="r_end_y" size="4" value="<?=$data[r_end][0]?>" maxlength="4" onkeydown="onlynumber();" class="cline">년
	<input type="text" name="r_end_m" size="2" value="<?=$data[r_end][1]?>" maxlength="2" onkeydown="onlynumber();" class="cline">월
	<input type="text" name="r_end_d" size="2" value="<?=$data[r_end][2]?>" maxlength="2" onkeydown="onlynumber();" class="cline">일

	<div style="margin-top:10px;text-align:right;">
	<a href="javascript:fnSetDate('<?=date("Y-m-d",G_CONST_NOW)?>','<?=date("Y-m-d",G_CONST_NOW)?>')"><img src="../img/sicon_today.gif" align=absmiddle></a>
	<a href="javascript:fnSetDate('<?=date("Y-m-d",G_CONST_NOW)?>','<?=date("Y-m-d",strtotime("+7 day",G_CONST_NOW))?>')"><img src="../img/sicon_week.gif" align=absmiddle></a>
	<a href="javascript:fnSetDate('<?=date("Y-m-d",G_CONST_NOW)?>','<?=date("Y-m-d",strtotime("+15 day",G_CONST_NOW))?>')"><img src="../img/sicon_twoweek.gif" align=absmiddle></a>
	<a href="javascript:fnSetDate('<?=date("Y-m-d",G_CONST_NOW)?>','<?=date("Y-m-d",strtotime("+1 month",G_CONST_NOW))?>')"><img src="../img/sicon_month.gif" align=absmiddle></a>
	<a href="javascript:fnSetDate('<?=date("Y-m-d",G_CONST_NOW)?>','<?=date("Y-m-d",strtotime("+2 month",G_CONST_NOW))?>')"><img src="../img/sicon_twomonth.gif" align=absmiddle></a>
	</div>

</fieldset>

<div class="button_top">
<input type="image" src="../img/btn_confirm_s.gif">
</div>

</form>

<script>
linecss();
table_design_load();
</script>
