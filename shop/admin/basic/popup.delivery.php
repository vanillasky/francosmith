<?

include "../lib.php";

$title = ($_GET[mode]=="registerDelivery") ? "�ù�� �߰�" : "������� ����";

if ($_GET[mode]=="modifyDelivery"){
	$query = "select * from ".GD_LIST_DELIVERY." where deliveryno='$_GET[no]'";
	$data = $db->fetch($query);
}

?>

<script src="../common.js"></script>
<script src="../admin.js"></script>
<link rel="styleSheet" href="../style.css">

<form method=post action="indb.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="<?=$_GET[mode]?>">
<input type=hidden name=deliveryno value="<?=$_GET[no]?>">

<div class="title title_top"><?=$title?></div>
<table class=tb>
<col class=cellC><col class=cellL>
<? if ($_GET[mode]=="modifyDelivery"){ ?>
<tr>
	<td width=100 nowrap>������ȣ</td>
	<td width=100%><b><?=$_GET[no]?></b></td>
</tr>
<? } ?>
<tr>
	<td>�ù���</td>
	<td><input type=text name=deliverycomp value="<?=$data[deliverycomp]?>" required label="�ù���" class="line"></td>
</tr>
<tr>
	<td>��������ּ�</td>
	<td><textarea name=deliveryurl style="width:100%;height:50;word-break:break-all;" class="tline"><?=$data[deliveryurl]?></textarea>
	<div style="padding-top:4px"></div>
	<font class=extext>�ù�翡�� �����ϴ� ��������ּҸ� ������ �˴ϴ�.<br>
	�⺻������ �ּҴ� �ԷµǾ� �ֽ��ϴ�.<br>�ش� �ù�� Ȩ�������� ���ø� Ȯ���Ͻ� �� �ֽ��ϴ�.</font>
	<div style="padding-top:4px"></div>
	</td>
</tr>
</table>

<div class="button_popup">
<input type=image src="../img/btn_regist.gif">
<a href="javascript:parent.closeLayer()"><img src="../img/btn_cancel_s.gif"></a>
</div>

</form>

<script>table_design_load();
linecss(document.form);
</script>