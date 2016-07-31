<?
include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.$cfg[settlePg].php";
include "../../../conf/pg.escrow.php";

if ($escrow['type'] == 'INI') {
	include "./ini_escrow_cancel.php";
	exit();
}
$ordno = $_GET[ordno];

$query = "
select
	escrowno,deliverycomp,deliverycode,delivery,ddt
from
	".GD_ORDER." a
	left join ".GD_LIST_DELIVERY." b on a.deliveryno = b.deliveryno
where
	a.ordno = '$ordno'
";
$data = $db->fetch($query);
?>

<html>
<head>
<title>�ϳ����� �Ÿź�ȣ ���� ��ǰ���/����</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">

<style type="text/css">
	BODY{font-size:9pt; line-height:160%}
	TD{font-size:9pt; line-height:160%}
	A {color:blue;line-height:160%; background-color:#E0EFFE}
	INPUT{font-size:9pt;}
	SELECT{font-size:9pt;}
	.emp{background-color:#FDEAFE;}
</style>

<script language="Javascript">

function checkGLSHost(obj, obj0, obj1)
	{
		if (obj.value!="OTHEREXPRX") {
			if (obj.value=="choose") {
				alert ("�̸��� �������� ������ �ּ���. \n������ ����Ʈ�� ���� ��� ���� �Է��� ������ �ּ���.");
				obj0.value="";
				obj0.readOnly=true;
				obj0.style.background = "#DDDDDD";
				return false;
			}
			else {
				if(obj.value == "CJGLSLOGIS") {
				obj1.value="CJ GLS";
				}else if (obj.value == "HYUNDAIEXP"){
				obj1.value="�����ù�";
				}else if (obj.value == "HANJINLOGI"){
				obj1.value="�����ù�";
				}else if (obj.value == "KOREXEXPR"){
				obj1.value="�������";
				}else if (obj.value == "KGBLOGISTI"){
				obj1.value="KGB";
				}
				obj0.value=obj.value;
				obj1.readOnly=true;
				obj1.style.background = "#DDDDDD";
			}
		} else {
			obj0.value=obj.value;
			obj1.value="";
			obj1.readOnly=false;
			obj1.style.background = "white";
			obj1.focus();
		}
	}


function f_check(){
	if(document.ini.hanatid.value == ""){
		alert("�ŷ���ȣ�� �������ϴ�.")
		return;
	}
	else if(document.ini.mid.value == ""){
		alert("���� ���̵� �������ϴ�.")
		return;
	}
	else if(document.ini.EscrowType.value == ""){
		alert("����ũ�� �۾��� �����Ͻʽÿ�.")
		return;
	}
	else if(document.ini.invno.value == ""){
		alert("������ȣ�� �������ϴ�.")
		return;
	}
	else if(document.ini.transtype.value == ""){
		alert("��������� �������ϴ�.")
		return;
	}
	document.ini.submit();

}

</script>

</head>

<body>
<form name=ini method=post action="sample/INIescrow.php">
<input type=hidden name=ordno value="<?=$ordno?>">

<table border=0 width=500>
	<tr>
	<td>
	<hr noshade size=1>
	<b>�ϳ����� �Ÿź�ȣ ���� ��ǰ���</b>
	<hr noshade size=1>
	</td>
	</tr>
</table>
</table>
<br>

<table border=0 width=500 style="display:none">
<tr>
<td align=center>
<table width=500 cellspacing=0 cellpadding=0 border=0 bgcolor=#6699CC>
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
		<td width=30%>�ŷ���ȣ : </td>
		<td width=70%><input type=text name=hanatid size=45 maxlength=40 value="<?=$data[escrowno]?>"></td>
	</tr>
	<tr>
		<td>���� ���̵� : </td>
		<td><input type=text name=mid size=15 maxlength=10 value="<?=$escrow[id]?>"</td>
	</tr>
	<tr>
		<td>����ũ�� Type : </td>
		<td>	<select name=EscrowType >
			<option value="">�����Ͻʽÿ�
			<option value="rr" selected>��ǰ���</option>
			<option value="ru">��ǰ����</option>
			</select>
		</td>
	</tr>

	<tr>
		<td>����� ��ȣ : </td>
		<td><input type=text name=invno size=45 maxlength=40 value="<?=$data[delivery]?>"></td>
	</tr>

	<tr>
		<td>����� ID : </td>
		<td><input type=text name=adminID size=17 maxlength=12 value="<?=$sess[m_id]?>"></td>
	</tr>

	<tr>
		<td>����ڼ��� : </td>
		<td><input type=text name=adminName size=25 maxlength=20 value="<?=$member[name]?>"></td>
	</tr>

	<tr>
		<td>���ȸ�� �� : </td>
		<td>	<input type=text name=compName size=40 maxlength=40 readOnly style="background-color:#DDDDDD"></td>
	</tr>

	<tr>
		<td>���ȸ�� ID : </td>
		<td>
		<select name="glsid_temp" onchange="javascript:checkGLSHost(this.form.glsid_temp, this.form.compID,this.form.compName)">
			 <option value=''>�����Ͻʽÿ�</option>
			<option value='CJGLSLOGIS'>CJ GLS:CJGLSLOGIS</option>
			<option value='HYUNDAIEXP'>�����ù�:HYUNDAIEXP</option>
			<option value='HANJINLOGI'>�����ù�:HANJINLOGI</option>
			<option value='KOREXEXPR'>�������:KOREXEXPR</option>
			<option value='KGBLOGISTI'>KGB:KGBLOGISTI</option>
			<option value='OTHEREXPRX'>��Ÿ:OTHEREXPRX</option>
		</select>
		<input type="text" name="compID" size="15" readOnly style="background-color:#DDDDDD">
	</td>
	</tr>

	<tr>
		<td>������� : </td>
		<td>	<select name=transtype>
			<option value="" selected>�����Ͻʽÿ�
			<option value="S0" selected>1.�Ϲݹ�� - S0
			<option value="S1">2.�ɾ߹�� - S1
			<option value="S2">3.�ָ���� - S2
			<option value="S3">4.Ư�޹�� - S3
			<option value="S4">5.������� - S4
			<option value="S5">6.����������� - S5
			</select>
		</td>
	</tr>

	<tr>
		<td>��ۼ��� : </td>
		<td>	<select name=transport >
			<option value="" selected>�����Ͻʽÿ�
			<option value="T0">1.ȭ���� - T0
			<option value="T1">2.�̷��� - T1
			<option value="T2">3.ȭ������ - T2
			</select>
		</td>
	</tr>

	<tr>
		<td>��ۺ� : </td>
		<td>
		<input type=text name=transfee size=15 maxlength=10 value="">
		</td>
	</tr>

	<tr>
		<td>��ۺ� ���޹�� : </td>
		<td>	<select name=paymeth >
			<option value="" selected>�����Ͻʽÿ�
			<option value="F0">1.���� - F0
			<option value="F1">2.���� - F1
			<option value="F2">3.���� - F2
			</select>
		</td>
	</tr>

	<tr>
		<td>��� ���ǻ��� : </td>
		<td>	<select name=notice >
			<option value="" selected>�����Ͻʽÿ�
			<option value="C0">1.��ǰ - C0
			<option value="C1">2.����(����) - C1
			<option value="C2">3.�ļ� - C2
			<option value="C3">4.���� - C3
			<option value="C4">5.��Ÿ������� - C4
			</select>
		</td>
	</tr>
	<tr>
		<td>��ۿ�û���� (From) : </td>
		<td>	<input type=text name=transdate1 size=13 maxlength=8 value=""></td>
	</tr>

	<tr>
		<td>��ۿ�û���� (To) : </td>
		<td>	<input type=text name=transdate2 size=13 maxlength=8 value=""></td>
	</tr>

	<tr>
		<td>��ǰ���� : </td>
		<td>
		<select name="returntype">
		<option value="">-�����Ͻʽÿ�-</option>
		<option value="0" selected>��ǰ����</option>
		<option value="1">��ǰ����</option>
		</select>
	</td>
	</tr>
	<tr>
		<td>��ǰ���� : </td>
		<td>
		<select name="returncode">
		<option value="">-�����Ͻʽÿ�-</option>
		<option value="R0">���ۿϷ�</option>
		<option value="R1">�⼭�� ����</option>
		<option value="R2">��ǰ��ǰ �̼���</option>
		<option value="R3">��ǰ��ǰ�� ����ǰ�� �ٸ�</option>
		<option value="R4">�δ籸�� öȸ</option>
		<option value="R5" selected>��Ÿ</option>
		</select>
		</td>
	</tr>
	<tr>
		<td>��Ÿ���� : </td>
		<td>
		<input name="reMsg" type="text" size="76" value="">
		</td>
	</tr>
	<tr>
	<td colspan=2 align=center>
	<br>
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
<input type="button" value="Ȯ ��" onClick=f_check()>
</form>

<script>f_check();</script>

</body>
</html>