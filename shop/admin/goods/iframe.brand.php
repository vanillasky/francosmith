<?
include "../_header.popup.php";
if ($data = $db->fetch("select *, sno as brand from ".GD_GOODS_BRAND." where sno='$_GET[brand]'",1)) {
	list($cntGoods) = $db->fetch("select count(distinct goodsno) from ".GD_GOODS." where brandno = '$data[brand]'");
}
else {
	list($cntGoods) = $db->fetch("select count(distinct goodsno) from ".GD_GOODS." where brandno > 0");
}
@include "../../conf/brand/$data[brand].php";

$checked[tpl][$lstcfg[tpl]] = "checked";
$checked[rtpl][$lstcfg[rtpl]] = "checked";

?>

<style>
body {margin:0}
</style>
<script type="text/javascript">
function fnDeleteBrand() {

	var brand = document.form.brand.value;
	var cntGoods = <?=(int)$cntGoods?>;

	if (brand) {

		if (cntGoods > 0) {
			alert('��ϵ� ��ǰ�� �ִ� �귣��� ������ �� �����ϴ�.');
		}
		else {
			popupLayer('popup.delBrand.php?brand='+brand);
		}
	}
	else {
		alert('��ü�귣��� ��������� �ƴմϴ�.');
	}

}
</script>
<form name=form method=post action="indb.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="mod_brand">
<input type=hidden name=brand value="<?=$_GET[brand]?>">

<div class="title_sub" style="margin:0">�귣��� ����/����/����<span>�귣����� �߰��ϰ� �����մϴ�</span></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tbody style="height:26px">
<tr>
	<td>����귣��</td>
	<td>
	<?=($_GET[brand])?'TOP > ' . $data[brandnm]:"��ü�귣��";?>
<? if ($_GET[brand]){ ?>
	<a href="../../goods/goods_brand.php?brand=<?=$data[brand]?>" target=_blank><img src="../img/i_nowview.gif" border=0 align=absmiddle hspace=10></a>
<? } ?>
	</td>
</tr>
<tr>
	<td>�� �귣���� ��ǰ��</td>
	<td><b><?=number_format($cntGoods)?></b>��</td>
</tr>
<? if ($_GET[brand]){ ?>
<tr>
	<td>���� �귣���</td>
	<td>
	<input type=text name=brandnm class=lline required value="<?=$data[brandnm]?>">
	&nbsp; �귣���ڵ� : <b><?=$data[brand]?></b>
	</td>
</tr>
<? } ?>
<? if ($_GET[brand] == ''){ ?>
<tr>
	<td>�����귣�� ����</td>
	<td><input type=text name=sub class=lline></td>
</tr>
<? } ?>
<? if ($_GET[brand]){ ?>
<tr>
	<td>�귣�� ����</td>
	<td><a href="javascript:void(0);" onClick="fnDeleteBrand();"><img src="../img/i_del.gif" border=0 align=absmiddle></a></td>
</tr>
<? } ?>
</table>

<? if ($_GET[brand]){ ?>
<div class="title_sub">�귣�������� ��ܺκ� �ٹ̱�<span>�귣�������� ��ܺκ��� �������մϴ�</span></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>���HTML</td>
	<td>
	<textarea name=lstcfg[body] style="width:100%;height:300px" type=editor><?=stripslashes($lstcfg[body])?></textarea>
	<script src="../../lib/meditor/mini_editor.js"></script>
	<script>mini_editor("../../lib/meditor/");</script>
	</td>
</tr>
</table>

<div class="title_sub">�귣�������� ����Ʈ�κ� �ٹ̱�<span>�귣�������� �ϴ��� ����Ʈ�κ��� �������մϴ�</span></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>���÷�������</td>
	<td>

	<table>
	<col align=center span=3>
	<tr>
		<td><img src="../img/goodalign_style_01.gif"></td>
		<td><img src="../img/goodalign_style_02.gif"></td>
		<td><img src="../img/goodalign_style_03.gif"></td>
	</tr>
	<tr class=noline>
		<td><input type=radio name=lstcfg[tpl] value="tpl_01" <?=$checked[tpl][tpl_01]?>></td>
		<td><input type=radio name=lstcfg[tpl] value="tpl_02" <?=$checked[tpl][tpl_02]?>></td>
		<td><input type=radio name=lstcfg[tpl] value="tpl_03" <?=$checked[tpl][tpl_03]?>></td>
	</tr>
	</table>

	</td>
</tr>
<!-- <tr>
	<td>�̹���������</td>
	<td><input type=text name=lstcfg[size] value="<?=$lstcfg[size]?>"> <font class=ver8>pixel</font></td>
</tr> -->
<tr>
	<td>��ǰ����</td>
	<td><input type=text name=lstcfg[page_num] value="<?=@implode(",",$lstcfg[page_num])?>" class=lline></td>
</tr>
<tr>
	<td>���δ� ��ǰ��</td>
	<td><input type=text name=lstcfg[cols] value="<?=$lstcfg[cols]?>"> ��</td>
</tr>
</table>
<? } ?>

<div class="button"><input type=image src="../img/btn_modify.gif"></div>

</form>

<div id=MSG01 >
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>��ǰ�귣��Ž���⿡�� TOP (�ֻ���)�� ������ �귣�带 ���� �� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>�귣����������ܿ� Ư���� �´� �̺�Ʈ�� ��ʸ� ��ġ�Ͽ� ����ȭ�� �� �ְ� ������ �غ�����.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>�귣�� ���������� �ش�귣�带 ������ Ű������ �����̵�Ű���� �����ϰ� ������ ���� �����մϴ�.</td></tr></table>
</div>
<script>cssRound('MSG01')</script>

<script>
table_design_load();
window.onload = function(){
	parent.document.getElementById('ifrmbrand').style.height = document.body.scrollHeight;
}
<? if ($_GET[brand]=='' && $_GET[focus]=="sub"){ ?>
document.form.sub.focus();
<? } ?>
</script>