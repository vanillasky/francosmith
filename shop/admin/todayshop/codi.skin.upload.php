<?
include "../_header.popup.php";
?>
<div class="title title_top">��Ų ���ε�</div>
<form method="post" action="indb.skin.php" enctype="multipart/form-data">
<input type="hidden" name="mode" value="skinUpload">
<table border=1 bordercolor=#cccccc style="border-collapse:collapse">
<col class="cellC"><col class="cellL">
<tr>
	<td>��Ų��</td>
	<td><input type="text" name="upload_skin_name" class="line" value="" maxlength="20" style="width:130px;ime-mode:disabled;"></td>
</tr>
<tr>
	<td>���ε�</td>
	<td><input type="file" name="upload_skin" class="line" style="width:230px;"></td>
</tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td height="10"></td></tr>
<tr>
	<td align="center" class="noline"><input type="image" src="../img/btn_register.gif"></td>
</tr>
<tr><td height="10"></td></tr>
</table>
<div style="padding:6px 0px 0px 25px"><font class="extext">�� �ش� �������� ������ ������ ���� ������ �Դϴ�.</font></div>
<div style="padding:6px 0px 0px 25px"><font class="extext">�� �ݵ�� �ٿ� ���� tar.gz ���� ȭ�ϸ� �����մϴ�.</font></div>
<div style="padding:6px 0px 0px 25px"><font class="extext">�� �ٸ� ������ �÷� ����� ������ ���ؼ��� å���� ���� �ʽ��ϴ�.</font></div>
<div style="padding:6px 0px 0px 25px"><font class="extext">�� ȣ���� ��ü�� ���� ���ε尡 ���� ���� �� �ֽ��ϴ�.</font></div>
<div style="padding:6px 0px 0px 25px"><font class="extext">�� ���뷮�� ����� ȭ���� 2�� �̻��� ���� �־�� ���ε尡 �����մϴ�.</font></div>
<div style="padding:6px 0px 0px 25px"><font class="extext">�� ��Ų���� ������ �����մϴ�.</font></div>
</form>
</body>
</html>