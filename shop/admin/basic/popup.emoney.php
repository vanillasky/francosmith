<?
include "../_header.popup.php";
?>
<div class=title>ȯ�Ҽ����� ����<span>ȯ�Ҽ������� �⺻���� �����մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<form action='indb.php'  method=post onsubmit='return chkForm(this)'>
<input type=hidden name=mode value="cfgemoney">
<table class=tb width=100%>
<col class=cellC><col class=cellL>
<tr><td colspan=20 height=1 bgcolor=DDDDDD></td></tr>
<tr>
	<td>ȯ�Ҽ�����</td>
	<td>
		<input type=hidden name=repayfee size=3 maxlength=3 value="" onkeydown="onlynumber()" class=line style='text-align=right'>
		<!--<div>�ֹ��ݾ��� <input type=text name=repayfee size=3 maxlength=3 value="<?=$cfg[repayfee]?>" onkeydown="onlynumber()" class=line style='text-align=right'> %&nbsp;<span class=small1><font color=#5B5B5B>(�������� �θ� �Ʒ� �⺻�����Ḹ ����˴ϴ�)</font></span></div>-->
		<div style="padding-top:2">�⺻������ <input type=text name=minrepayfee size=8 maxlength=8 value="<?=$cfg[minrepayfee]?>" class=line onkeydown="onlynumber()" style='text-align=right'> ��&nbsp;<span class=small1><font color=#5B5B5B>(�������� �θ� �⺻������� 0���� �˴ϴ�)</font></span></div>
		<input type=hidden name=minpos size=1 maxlength=1 value="" class=line onkeydown="onlynumber()" style='text-align=right'>
		<!--
		<div style="padding-top:2"><input type=text name=minpos size=1 maxlength=1 value="<?=$cfg[minpos]?>" class=line onkeydown="onlynumber()" style='text-align=right'> �ڸ� ���ϴ� ���� ó���մϴ�.</div>
		<div style="padding-top:2"><span class=small><font color=#5B5B5B>ex) 1�ڸ�: 1������, 2�ڸ�: 10������, 3�ڸ�: 100������ (���ڸ� �Է�) </font></span></div>-->
	</td>
</tr>
<tr><td colspan=20 height=1 bgcolor=DDDDDD></td></tr>
<!--
<tr>
	<td>������ֹ����</td>
	<td class=noline>
		<input type='radio'  name='userCancel' value='1' <?=$checked[userCancel][1]?>> ��� <input type='radio'  name='userCancel' value='0' <?=$checked[userCancel][0]?>> ����
		&nbsp;<span class=small><font color=#5B5B5B>��뿡 üũ�� �ֹ������ܰ迡�� �����ڰ� �ֹ���� �����մϴ�.</font></span>
	</td>
</tr>
-->
</table>
<div class="button">
<input type=image src="../img/btn_save.gif">

</div>

</form>
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_tip>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><font color=0074BA>ȯ�Ҽ�����</font> : ȯ�ҽ� �߻��Ǵ� �ݼۺ�� �� ��Ÿ ������ ���� ���Ͻ� �� �ֽ��ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01','#F7F7F7')</script>