<?

$location = "��ǰ �ΰ���� ���� > ��ǰ �������� ����";
include "../_header.php";

$query = "select * from ".GD_COMMON_INFO." where 1=1 order by idx";
$res = $db->query($query);

$arr_info=array();
while ($data=$db->fetch($res)) {
	$idx = $data[idx];
	$arr_info[$idx][title] = $data[title];
	$arr_info[$idx][info] = $data[info];
	$arr_info[$idx][open] = $data[open];
}
?>
<script>
function text_disabled(obj, num) {

	if(obj.value == '1') {
		document.getElementById('title_'+num).required = "required";
	} else {
		document.getElementById('title_'+num).required = "";
	}

}
</script>

<div class="title title_top">��ǰ �������� ����<span>��ǰ �ϴܿ� �������� ��µǴ� ������ �����ϴ� ������ �Դϴ�. <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=43')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>

<form method=post name="form" action="indb.php" onsubmit="return chkForm(this)">
<input type="hidden" name="mode" value="common_info">

<table class=tb>
<col class=cellC><col class=cellL>
<? 
	for($i=1; $i<=10; $i++) { 
		$checked_1 = $checked_0 = "";

		$checked_1 = ($arr_info[$i][open] == 1)	? "checked" : "";
		$checked_0 = ($arr_info[$i][open] == 0)	? "checked" : "";

?>
<tr>
	<td>
		<input type=text name="title[<?=$i?>]" id="title_<?=$i?>" value="<?=$arr_info[$i][title]?>"   class="line" <?=($checked_1 == "checked") ? "required='true'" : ""?>>
	</td>
	<td>
		<div>
			<div style="float:left;">
				<label class="noline"><input type="radio" name="open[<?=$i?>]" value="1" onClick="text_disabled(this, <?=$i?>)" <?=$checked_1?> /> ���</label>
				<label class="noline"><input type="radio" name="open[<?=$i?>]" value="0" onClick="text_disabled(this, <?=$i?>)" <?=$checked_0?> /> �̻��</label>
			</div>
			<!--
			<div style="padding-left:20px; float:left;">
				<a href="javascript:void(0)" onClick="fnDelDisplayForm(<?=$i?>);"><img src="../img/i_del.gif" align="absmiddle"></a>
			</div>
			-->
		</div>
		<textarea name="info[<?=$i?>]" style="width:100%;height:100px" type=editor><?=$arr_info[$i][info]?></textarea>
	</td>
</tr>
<? } ?>
</table>

<div class=button>
	<input type=image src="../img/btn_save.gif">
</div>

<script src="../../lib/meditor/mini_editor.js"></script>
<script>mini_editor("../../lib/meditor/");</script>

<div style="padding-top:10px"></div>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td>
<img src="../img/icon_list.gif" align="absmiddle"> ����� ��ǰ �������� ������ ���θ� ��ǰ�������� �ϴܿ� �Էµ� ���� ������ ��µ˴ϴ�.<br />
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>


</form>

<? include "../_footer.php"; ?>
