<?

include "../_header.popup.php";
$data = $db->fetch("select *, sno as brand from ".GD_GOODS_BRAND." where sno='$_GET[brand]'",1);
list($cntGoods) = $db->fetch("select count(distinct goodsno) from ".GD_GOODS." where brandno = '$_GET[brand]'");

?>

<script>
function chkForm2(obj){
	return chkForm(obj);
	parent.saveHistory(parent.form);
}
</script>

<form name=form method=post action="indb.php" onsubmit="return chkForm2(this)">
<input type=hidden name=mode value="del_brand">
<input type=hidden name=brand value="<?=$_GET[brand]?>">

<div class="title title_top">�귣�� ����</div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr height=26>
	<td>���� �귣��</td>
	<td>TOP > <?=$data[brandnm]?></td>
</tr>
<tr>
	<td>�����ǰ��</td>
	<td><b><?=$cntGoods?></b>��</td>
</tr>
<tr>
	<td>���ǻ���</td>
	<td class=small1 style="color:#5B5B5B;padding:5px;">
		���HTML�� ���� �̹����� �ٸ� �������� ����ϰ� ���� �� �����Ƿ� �ڵ� �������� �ʽ��ϴ�.<br>
		'�����ΰ��� > webFTP�̹������� > data > editor'���� �̹���üũ �� ���������ϼ���.
	</td>
</tr>
</table>

<div class="button_popup">
<input type=image src="../img/btn_confirm_s.gif">
<a href="javascript:parent.closeLayer()"><img src="../img/btn_cancel_s.gif"></a>
</div>

</form>

<script>table_design_load();</script>