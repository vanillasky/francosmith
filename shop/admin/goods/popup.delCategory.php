<?

include "../_header.popup.php";

// ��ǰ�з� ������ ��ȯ ���ο� ���� ó��
$whereArr		= getCategoryLinkQuery('category', $_GET['category']);
list($cntGoods) = $db->fetch("select count(".$whereArr['distinct']." goodsno) from ".GD_GOODS_LINK." where ".$whereArr['where']);
?>

<script>
function chkForm2(obj){
	return chkForm(obj);
	parent.saveHistory(parent.form);
}
</script>

<form name=form method=post action="indb.php" onsubmit="return chkForm2(this)">
<input type=hidden name=mode value="del_category">
<input type=hidden name=category value="<?=$_GET[category]?>">

<div class="title title_top">ī�װ� ����<span>����ī�װ��� �ڵ� �����˴ϴ�</span></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr height=26>
	<td>���� ī�װ�</td>
	<td><?=currPosition($_GET[category],1)?></td>
</tr>
<tr>
	<td>�����ǰ��</td>
	<td><b><?=$cntGoods?></b>��</td>
</tr>
<tr>
	<td>���ǻ���</td>
	<td class=small1 style="color:#5B5B5B;padding:5px;">
		��ܲٹ̱⿡ ���� �̹����� �ٸ� �������� ����ϰ� ���� �� �����Ƿ� �ڵ� �������� �ʽ��ϴ�.<br>
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