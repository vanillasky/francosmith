<?

$scriptLoad='<link rel="styleSheet" href="../../style.css">
<script src="../../common.js"></script>
<script src="../../prototype.js"></script>
<script src="../../prototype_ext.js"></script>
<script src="./_codi.js"></script>';
include "../../_header.popup.php";

?>

<div class="title title_top">���ο� ������ �߰��ϱ�<span>���Ӱ� �ʿ��� �������� �߰��մϴ�.</span></div>

<form method="post" name="create" action="./indb.php" onsubmit="return DCCM.chk( this );">
<input type="hidden" name="mode" value="create">
<input type="hidden" name="design_file">
<input type="hidden" name="file_result">

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>������</td>
	<td style="padding-top:8px"><script>DCDM.write();</script> <div style="padding-top:4px"><font class=extext>�� �������� ������ ������ ���ϼ���. �������� �� ����ϼ���.</font></div></td>
</tr>
<tr>
	<td>���ϸ�</td>
	<td>
	<input type="text" name="file_name" size="20">
	<select name="file_ext">
	<option value=".htm">.htm</option>
	<option value=".txt">.txt</option>
	</select>
	&nbsp; <a href="javascript:DCCM.file_check()"><img src="../../img/btn_overlap.gif" border=0 align=absmiddle></a>
	</td>
</tr>
<tr>
	<td>����</td>
	<td><input type="text" name="file_desc" size="35"></td>
</tr>
</table>

<center style="margin:5px 0px;" class="noline"><input type="image" src="../../img/btn_save_s.gif" alt="����"></center>
</form>

<script>table_design_load();</script>