<html>
<head>
<title>�ô�����Ʈ</title>
<style type="text/css">
<!--
body { font-family:"����"; font-size:9pt; color:#000000; font-weight:normal; letter-spacing:0pt; line-height:180%; }
td { font-family:"����"; font-size:9pt; color:#000000; font-weight:normal; letter-spacing:0pt; line-height:180%; }
.clsleft { padding:0 10px; text-align:left; }
-->
</style>
<script language=javascript>
<!--
function Request(form)
{
	////////////////////////////////////////////
	//  �Էµ� ����Ÿ�� ��ȿ���� �˻��մϴ�.  //
	////////////////////////////////////////////
	if(form.id_no.value == "")
	{
		alert("�ֹε�Ϲ�ȣ�� �Է��Ͻʽÿ�.");
		return;
	}
	form.submit();
}
-->
</script>
</head>
<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
<form name="frmAGS_escrow" method="post" action="./escrow_confirm_return.php">
<input type="hidden" name="ordno" value="<?php echo $_GET['ordno'];?>">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td><hr></td>
</tr>
<tr>
	<td align="center"><b>�ô�����Ʈ ����ũ�� �ŷ� ����Ȯ�� ��û</b></td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td align="center">
	<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td class="clsleft">�� �ֹε�Ϲ�ȣ (13)</td>
		<td><input type="text" style="width:150px" name="id_no" maxlength="13" value=""></td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td><hr></td>
</tr>
<tr>
	<td align="center">
	<input type="button" value="��û" onclick="javascript:Request(frmAGS_escrow);">
	</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
</table>
</form>
</body>
</html>