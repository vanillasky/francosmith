<HTML>
<HEAD>
	<TITLE>�ϳ����� �Ÿź�ȣ ���� ����Ȯ��/���� ����</TITLE>
	<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
	<style type="text/css">
		BODY{font-size:9pt; line-height:160%}
		TD{font-size:9pt; line-height:160%}
		A {color:blue;line-height:160%; background-color:#E0EFFE}
		INPUT{font-size:9pt;}
		SELECT{font-size:9pt;}
		.emp{background-color:#FDEAFE;}
	</style>
	
	<!-- �ϳ����� ����ũ�� �׽�Ʈ��  �÷������� ���� JS ���� -->
	<!--<SCRIPT language=javascript src="http://211.32.31.131:7001/js/cpconfirm.js" ></SCRIPT>-->
	
	<!-- �ϳ����� ����ũ�� �ǻ�����  �÷������� ���� JS ���� -->
	<!-- �׽�Ʈ�� �Ϸ�ǽ��� ���� �׽�Ʈ���� �����Ͻð� �Ʒ� �κ� �ּ��� �����ϼż� ����Ͻñ� �ٶ��ϴ�. -->
	<SCRIPT language=javascript src="http://www.hanaescrow.com/js/cpconfirm.js"></SCRIPT>


	<!-- �÷����� ���� �� ȣ��Ǵ� �Լ� : �÷������� ����� ȹ���ϴ� �ڵ� ���� -->
	<SCRIPT language="javascript">
	function UserDefine()
	{
		if(status_cd == "SUCCESS")
		{
			document.cporder.result_value.value= "SUCCESS";
			alert("����Ȯ��/������ ���������� �Ϸ�Ǿ����ϴ�.");
			document.cporder.submit();
		}
		else
		if (status_cd == "CANCEL")
		{
			document.cporder.result_value.value= "CANCEL";
			alert("����Ȯ��/������ ��ҵǾ����ϴ�.");
			document.cporder.submit();
		}
		else
	 	{
			alert(status_cd);
		}
	}
	</SCRIPT>
</HEAD>
<BODY>
	<!-- ����Ȯ���� ���� �� : �̸� ���� �Ұ� -->
	<form name=cporder action="escrow_confirm.php">
	<table border=0 width=500>
		
		
	<tr>
	<td>
	<hr noshade size=1>
	<b>�ϳ����� �Ÿź�ȣ ���� ����Ȯ��/����</b>
	<hr noshade size=1>
	</td>
	</tr>
	
	<tr>
	<td>
	<font color=gray>
	�� �������� ������ �ϳ����� ����ũ�� ����Ȯ��/������ �� �� �ֵ��� ������ �����Դϴ�.
	�ͻ��� �䱸�� �°� ������ �����Ͽ� ����Ͻʽÿ�.
	</font>
	</td>
	</tr>	
	
	<tr>
        <td>
        <br>�ݵ�� �÷������� ��ġ�� �Ϸ��� �Ŀ� "Ȯ��"�� �����ʽÿ�.<br> �÷������� �ڵ����� �ٿ�ε�Ǿ� ��ġ�˴ϴ�.<br> 
�ٿ�ε忡 �ټ� �ð��� �ɸ��� ���� ������ ���Ȱ��â�� ��Ÿ�� ������ ��� ��ٷ� �ֽñ� �ٶ��ϴ�.<br>

�÷������� ���������� ��ġ�������� �ÿ��� <b>�ϳ����� �ݼ���</b>�� ���ǹٶ��ϴ�.
        </td>
        </tr>
        
	<tr>
	<td>
	<br>���ҽÿ� ��� �Ǵ� �ŷ���ȣ(TID)�� �Է��Ͽ� ������ ����Ȯ��/���� 
	 �� �� �ֵ��� �����Ͻʽÿ�. 
	</td>
	</tr>
	</table>
	<br>
	
	
	
	<table border=0 width=500>
<tr>
<td align=center>
<table width=400 cellspacing=0 cellpadding=0 border=0 bgcolor=#6699CC>
<tr>
<td>
<table width=100% cellspacing=1 cellpadding=2 border=0>
<tr bgcolor=#BBCCDD height=25>
<td align=center>
������ �����Ͻ� �� Ȯ�ι�ư�� �����ֽʽÿ�
</td>
</tr>
<tr bgcolor=#FFFFFF>
<td valign=top>
<table width=100% cellspacing=0 cellpadding=2 border=0>
<tr>
<td align=center>
<br>
<table>

	<tr>
	<td>�ŷ���ȣ : </td>
	<td><input type=text name=tid size=45 value="<?=$_GET[tid]?>"></td>
	</tr>
	
	<!--
	Ȯ�� ������ ���� 
		- ����Ȯ�� : CFRM
		- ���Ű��� : CNCL
		- �̿����� ���� : NULL
	-->
	<tr>
	<td>Ȯ������ : </td>
	<td>
	<select name=ctype>
	<option value="" selected>�����Ͻʽÿ�
	<option value="CFRM">����Ȯ��
	<option value="CNCL">���Ű���
	</select>
	</td>
	</tr>
	
	<tr>
	<td colspan=2 align=center>
	<br>
	<input type="button" value=" Ȯ �� " onClick=javascript:approve()>
	<br><br>
	</td>
	</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
</td>
</tr>
</table>
<br>

<table border=0 width=500>
	<tr>
	<td><hr noshade size=1></td>
	</tr>
</table>

<!-- rfnd_amt�� �κа����̶�� Ư���� ȯ��ó���� ���� �ɼ��̱� ������, �׻� value�� NULL �� ���� -->
<input type="hidden" name="rfnd_amt" value="">
<input type="hidden" name="result_value" value="">		
</form>
</BODY>
</HTML>