<?

include "../_header.popup.php";

$idx = $_GET[idx];
?>
<div class="title title_top">�����å���</div>
<form name="frm" style="margin:0px;">
<input type="hidden" name="idx" value="<?=$idx?>">
<table class=tb>
<col class=cellC width=20%><col class=cellL width=80%>
<tr>
	<td>�����å��</td>
	<td>
	<input type=text name="deliveryTitle">
	</td>
</tr>
<tr>
	<td>��å</td>
	<td>
	�� ���ž��� <input type=text name="price">�� �̻��� �� ��ۺ� ����, �̸��� �� 
	<select name="methodtype">
	<option value="1">����</option>
	<option value="2">����</option>
	<select> <input type=text name="delivery">�� ��ۺ� �ΰ�
	</td>
</tr>
</table>
<div style='font:0;padding-top:5'></div>
<div align="center"><input type="image" src="../img/btn_register.gif" class="null">
<a href="javascript:parent.closeLayer();"><img src="../img/btn_cancel.gif"></a></div>
</form>