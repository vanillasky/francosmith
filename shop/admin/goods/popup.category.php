<?
include "../_header.popup.php";
?>

<script>
function chkForm2(obj){
	return chkForm(obj);
	parent.saveHistory(parent.form);
}
</script>

<form name=form method=post action="indb.php" onsubmit="return chkForm2(this)">
<input type=hidden name=mode value="root_category">

<div class="title title_top">최상위분류생성<span>최상위분류명을 추가로 생성하실수 있습니다</span></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr height=26>
	<td>분류명</td>
	<td><input type=text name=rootCatnm class=lline required></td>
</tr>
</table>

<div class=title>하위분류생성</div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr height=26>
	<td>상위분류명</td>
	<td><?=currPosition($_GET[category],1)?></td>
</td>
</tr>
<tr height=26>
	<td>하위분류명</td>
	<td><input type=text name=rootCatnm class=lline required></td>
</tr>
</table>

<div class="button_popup">
<input type=image src="../img/btn_regist.gif">
<a href="javascript:parent.closeLayer()"><img src="../img/btn_cancel_s.gif"></a>
</div>

</form>

<script>table_design_load();</script>