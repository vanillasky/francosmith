<?

include "../_header.popup.php";
@include "../../conf/captcha.php";

if (is_array($captcha)) $captcha = array_map("slashes",$captcha);

?>

<div class="title title_top">�ڵ���Ϲ������� �̹��� ����<span>�ڵ���Ϲ������� ���û����� �����ϼ���</span></div>

<form method=post action="indb.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="captcha">

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>�̹��� ����</td><td><input type=text name=captcha[bgcolor] value="<?=$captcha['bgcolor']?>" maxlength="6" style="width:100;"> <a href="javascript:colortable();"><img src="../img/codi/btn_colortable.gif" border="0" alt="����ǥ ����" align="absmiddle"></a> &nbsp;<font class=extext>�⺻����<font class=small>(FFFFFF)</font>�� ����Ϸ��� �������� �μ���</td>
</tr>
<tr>
	<td>�̹��� ���ڻ�</td><td><input type=text name=captcha[color] value="<?=$captcha['color']?>" maxlength="6" style="width:100;"> <a href="javascript:colortable();"><img src="../img/codi/btn_colortable.gif" border="0" alt="����ǥ ����" align="absmiddle"></a> &nbsp;<font class=extext>�⺻����<font class=small>(262626)</font>�� ����Ϸ��� �������� �μ���</td>
</tr>
<tr>
	<td height=50>���� �����<br>��Ϲ��� �̹���</td><td><IMG src="../../proc/captcha.php" align="absmiddle">&nbsp;&nbsp;<font class=small1 color=666666>�Ʒ� Ȯ���� ������ ����� �̹����� ���Դϴ�</font></td>
</tr>
</table>

<div style="margin-bottom:10px;padding-top:10;" class=noline align=center>
<input type="image" src="../img/btn_confirm_s.gif">
</div>

</form>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�̰����� �����ϸ� ����ϴ� ��� �Խ����� �ڵ���Ϲ������ڿ� ����˴ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<script>table_design_load();</script>