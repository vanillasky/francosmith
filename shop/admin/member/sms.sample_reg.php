<?

include "../_header.popup.php";

if (!$_GET[mode]) $_GET[mode] = "sms_sample_reg";
if ($_GET[mode]=="sms_sample_mod") $data = $db->fetch("select * from ".GD_SMS_SAMPLE." where sno='$_GET[sno]'");

$selected[category][$data[category]] = "selected";

?>

<div class="title title_top"><font  face=굴림 color=black><b>SMS</b></font> 예제등록</div>

<form method=post action="indb.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="<?=$_GET[mode]?>">
<input type=hidden name=sno value="<?=$data[sno]?>">

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>분류</td>
	<td>
	<select name=category required>
	<option value="">== 선택해주세요 ==
	<? foreach ($r_sms_category as $v){ ?>
	<option value="<?=$v?>" <?=$selected[category][$v]?>><?=$v?>
	<? } ?>
	</select>
	</td>
</tr>
<tr>
	<td>제목</td>
	<td><input type=text name=subject value="<?=$data[subject]?>" required class="line"></td>
</tr>
<tr>
	<td>메세지</td>
	<td>
	
	<table cellpadding=0 cellspacing=0>
	<tr><td><img src="../img/sms_top.gif"></td></tr>
	<tr>
		<td background="../img/sms_bg.gif" align=center height="81"><textarea name=msg cols=16 rows=5 style="font:9pt 굴림체;overflow:hidden;border:0;background-color:transparent;" onkeydown="return chkLength(this)"><?=$data[msg]?> </textarea></td>
	</tr>
	<tr><td><img src="../img/sms_bottom.gif"></td></tr>
	</table>
	
	</td>
</tr>
</table>

<div class="button_popup">
<input type=image src="../img/btn_regist.gif">
<a href="javascript:parent.closeLayer()"><img src="../img/btn_cancel_s.gif"></a>
</div>

</form>

<script>table_design_load();</script>