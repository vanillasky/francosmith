<?

include "../_header.popup.php";

$query = "select a.*,b.step,`b`.`pg` from ".GD_ORDER_ITEM." a left join ".GD_ORDER." b on a.ordno=b.ordno where a.sno in ($_GET[chk])";
$res = $db->query($query);

if(!$_GET[m]) $tmsg = " ���ó���ϱ� / ��ǰó��";
else $tmsg = "�±�ȯ";
?>
<body style="margin:0" scroll=no>

<form method=post action="indb.php" onsubmit="return chkForm2(this)">
<input type=hidden name=mode value="chkCancel">
<input type=hidden name=ordno value="<?=$_GET[ordno]?>">

<div style="padding-bottom:5px">&nbsp;<img src="../img/icon_process.gif" align=absmiddle><b style="color:494949">�ֹ���ǰ<?=$tmsg?>�ϱ�</b></div>

<table border=4 bordercolor=#000000 style="border-collapse:collapse" width=100%>
<tr><td>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<tr>
	<td><font class=small1 color=434343><b>��ǰ����</td>
	<td style="padding:0" colspan=3>

	<table width=100% cellpadding=0 cellspacing=0>
	<tr bgcolor=#f7f7f7 height=22>
		<th width=80><font class=small1 color=434343><b>�ֹ�����</th>
		<th><font class=small1 color=434343><b>��ǰ��</th>
		<th width=150><font class=small1 color=434343><b>�ɼ�</th>
		<th width=150><font class=small1 color=434343><b>����</th>
	</tr>
	<? 
	while ($data=$db->fetch($res)){ 
		$step = $data[step];
		$pg = $data['pg'];
	?>
	<input type=hidden name=sno[] value="<?=$data[sno]?>">
	<tr>
		<td align=center><font class=small1 color=ED00A2><b><?=$r_istep[$data[istep]]?></b></font></td>
		<td style="padding-left:10px"><font class=small1><?=$data[goodsnm]?></td>
		<td></td>
		<td align=center><input type=text name=ea[] value="<?=$data[ea]?>" size=3 class="rline"<?php if($data['pg']==='ipay') echo ' readonly style="background:#e3e3e3;"'; ?>><font class=small1>��</td>
	</tr>
	<? } ?>
	</table>

	</td>
</tr>
<tr>
	<td width=130 nowrap><font class=small1 color=434343><b>ó�������</td>
	<td width=50%><input type=text name=name value="<?=$_COOKIE[member][name]?>" required class="line"></td>
	<td width=130 nowrap><font class=small1 color=434343><b>����</td>
	<td width=50%>
	<select name=code required>
	<option value="">= �����ϼ��� =
	<? foreach ( codeitem("cancel") as $k=>$v){ ?>
	<option value="<?=$k?>"><?=$v?>
	<? } ?>
	</select>
	</td>
</tr>
<tr>
	<td><font class=small1 color=434343><b>�󼼻���</td>
	<td colspan=3>
	<textarea name=memo style="width:100%;height:65px" required  class="tline"></textarea>
	</td>
</tr>
<?
if($step >= 1 && !$_GET[m]){
?>
<tr>
	<td height=26><font class=small1 color=434343><b>ȯ�Ұ�������</td>
	<td colspan=3>
		<div>
			<font class=small1 color=434343>���� <select name=bankcode >
			<option value="" style="font: 8pt ����;">= �����ϼ��� =
			<? foreach ( codeitem("bank") as $k=>$v){ ?>
			<option value="<?=$k?>"><?=$v?>
			<? } ?>
			</select>&nbsp;&nbsp;<font class=small1>���¹�ȣ <input type=text name=bankaccount value=''  class="line">&nbsp;&nbsp;
		<font class=small1>������ <input type=text name=bankuser value=''  class="line"></div>
	</td>
</tr>
<?
	}			
?>
<tr>
	<td colspan=4 class=noline align=left>
	<div align=center><input type=image src="../img/btn_confirm_o.gif"></div>
	<div style="padding:8 0 6 67"><font color=black><b>- ��ǰ����� ��� -</b></font> &nbsp;&nbsp;<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=3')"><img src="../img/btn_cancel_manual.gif" border=0 align=absmiddle></a></div>
	<div style="padding:3 0 0 67"><font class=small1 color=444444>�� �ֹ����°� <font color=ED00A2>�ֹ�����</font>�� ��� �ٷ� <font color=ED00A2>�ֹ����</font>ó���˴ϴ�. �Ա��� �ݾ��� ���� �����Դϴ�.</font></div>
	<div style="padding:3 0 0 67"><font class=small1 color=444444>�� �ֹ����°� <font color=ED00A2>�Ա�Ȯ��</font>�� ��� �ٷ� <font color=ED00A2>ȯ����������Ʈ</font>�� �����Ǿ� <font color=ED00A2>ȯ�ҿϷ�</font>�� ó���Ͽ��� �մϴ�.</font></div>
	<div style="padding:3 0 0 67"><font class=small1 color=444444>�� �ֹ����°� <font color=ED00A2>����� �Ǵ� ��ۿϷ�</font>�� ��� <font color=ED00A2>��ǰ/��ȯ��������Ʈ</font>���� �Ϸ�ó�� �� <font color=ED00A2>ȯ����������Ʈ</font>���� ���� <font color=ED00A2>ȯ�ҿϷ�</font>�� ó���Ͽ��� �մϴ�.</font></div>

    <div style="padding:10 0 0 67"><font class=small1 color=444444><b>1) ������, ������ü, ������·� ������ �ֹ��� ����ϴ� ���</b></div>
    <div style="padding:3 0 0 82">ȯ������ �����°� �ʿ��ϹǷ�, �����κ��� ���� ȯ�Ұ��������� �Է��ϼ���.</div>
	<div style="padding:10 0 0 67"><b>2) ī��� ������ �ֹ��� ����ϴ� ���</b></div>
	<div style="padding:3 0 0 67"><font class=def color=444444>��</font> ī�������Ҹ� �ؾ��ϹǷ� ȯ�Ұ��������� �Է����� �ʽ��ϴ�.</div>
	<div style="padding:3 0 0 82">�׷��� '�󼼻���'������ 'ī���������Ұ�'���� ���Բ��� ������ �� �ִ� �޸�� ���ܵμ���.</div>
	<div style="padding:3 0 0 67"><font class=def color=444444>��</font> ī�������Ҵ� �ش� PG��(�������� ī�������)�� ȸ���� �������������� �����Ͽ� ī�������Ҹ� �ؾ� �մϴ�.</div>
	<div style="padding:3 0 0 67"><font class=def color=444444>��</font> ī����� �ֹ����� �ݵ�� �ֹ�����Ʈ������ ���ó���ϰ�, PG�� ���������������� �����Ͽ� �������ó���� �ؾ� �մϴ�.</div>
	<div style="padding:3 0 10 67"><font class=def color=444444>��</font> PG���� ���������������� ī����� ��Ҹ� �Ϸ��� ��, 'ȯ����������Ʈ'���� ȯ�ҿϷ�ó���� �մϴ�.</div>
	<div style="padding:3 0 0 67"><b>3) �޴������� ������ �ֹ��� ����ϴ� ���</b></div>
	<div style="padding:3 0 0 67"><font class=def color=444444>��</font> �޴��� ���� ��Ҹ� �ؾ��ϹǷ� ȯ�Ұ��������� �Է����� �ʽ��ϴ�.</div>
	<div style="padding:3 0 0 82">�׷��� '�󼼻���'������ 'ī���������Ұ�'���� ���Բ��� ������ �� �ִ� �޸�� ���ܵμ���.</div>
	<div style="padding:3 0 0 67"><font class=def color=444444>��</font> �޴��� ���� ��Ҵ� ȯ�� ���� ����Ʈ���� ��Ҹ� ó���ؾ� �մϴ�.</div>
	<div style="padding:3 0 0 82"><font class=def color=444444>��</font> �޴��� ���� �ֹ����� �ݵ�� �ֹ�����Ʈ���� ���ó�� �ؾ� ȯ�� ���� ����Ʈ ���� ��� �����մϴ�.</div>

   <table cellpadding=0 cellspacing=0 width=88% align=center>
   <tr><td bgcolor=cccccc width=100% height=1></td></tr></table>

	<div style="padding:13 0 6 67"><font color=black><b>- ��ǰ��ȯ�� ��� -</b></font> &nbsp;&nbsp;<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=3')"><img src="../img/btn_cancel_manual.gif" border=0 align=absmiddle></a></div>
	<div style="padding:3 0 0 67"><font class=small1 color=444444>�� �ֹ����°� <font color=ED00A2>����� �Ǵ� ��ۿϷ�</font>�϶��� <font color=ED00A2>��ǰ��ȯó���� ����</font>�մϴ�.</font></div>
	<div style="padding:3 0 0 67"><font class=small1 color=444444><font color=ED00A2>�ֹ�����, �Ա�Ȯ�� ����</font>������ <font color=ED00A2>��۵��� ���� ����</font>�̱� ������ ��ȯó���� �ƴ� <font color=ED00A2>�ٷ�  �ֹ����</font>�� �˴ϴ�.</font></div>
	<div style="padding:3 0 0 67"><font class=small1 color=444444>�����, ��ۿϷ� ���¿��� ��ȯ��û�� �� ���, �̰����� ��ȯ���� �� <font color=ED00A2>��ǰ/��ȯ��������Ʈ</font>���� <font color=ED00A2>��ȯ�Ϸ��� ���ֹ��ֱ�</font>�� ó���Ͽ��� �մϴ�.</font></div>
	<div style="padding:3 0 5 67"><font class=small1 color=444444><font color=ED00A2>���� ��ǰ������ ��ȯ</font>�� �����ϸ� <font color=ED00A2>(�±�ȯ)</font>, ���ֹ��� �ڵ������Ͽ� ������ �ٽ� ����ؾ��ϱ� �����Դϴ�.</font></div>
	</td>
</tr>
</table>

</td></tr></table>

</form>

<script>
function chkForm2(f)
{
	var
	step = <?php echo $step; ?>,
	isIpay = <?php echo $pg=='ipay'?'true':'false'; ?>;
	if(isIpay && (step==1 || step==2))
	{
		if(chkForm(f)) return confirm("iPay PG �ֹ����� �Ա�Ȯ��, ����غ��� �ܰ��� �ֹ����� ��ҽ�\r\n������ ȯ��ó�� ó������ �ٷ� ��ҿϷ�� ó���˴ϴ�.\r\n����Ͻðڽ��ϱ�?");
		else return false;
	}
	else
	{
		return chkForm(f);
	}
}

table_design_load();
window.onload = function(){
	parent.document.getElementById('ifrmCancel').style.height = document.body.scrollHeight + "px";
}
</script>