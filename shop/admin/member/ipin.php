<?

$location = "ȸ������ > �����ɼ���(�� �ѱ��ſ�����)����";
include "../_header.php";
include "../../conf/fieldset.php";

$checked[ipin][useyn][$ipin[useyn]] = "checked";
$checked[ipin][minoryn][$ipin[minoryn]] = "checked";

?>

<form name=frmField method=post action="indb.php" onsubmit="return checkForm()">
<input type=hidden name=mode value=ipin>

<div class="title title_top">�����ɼ���(�� �ѱ��ſ�����)����<span>������ ���񽺿� �ʿ��� ������ �����մϴ�. </span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=14')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>


<table border=4 bordercolor=#dce1e1 style="border-collapse:collapse" width=700>
<tr><td style="padding:7px 0px 10px 10px">
<div style="padding-top:5px; color:#666666; font-weight:bold;" class="g9"><b>* �� �������� ���� �ѱ��ſ������� ���� �����ɼ��񽺸� ���� �ް� �ִ� ȸ���� ���� ������ �������Դϴ�.</b></div>
<div style="padding-top:10px; color:#666666;" class="g9"><b>����1.</b> NICE�ſ��������� � ȸ���ΰ���?<br />���� �ִ� �ſ����� ȸ���� �ѱ��ſ������� �ѱ��ſ��򰡻��� �պ�����<br />����� NICE�ſ��������� ��ȣ���� ���� �Ǿ����ϴ�.</div>
<div style="padding-top:10px; color:#666666;" class="g9"><b>����2.</b> ������ �޶��� �κ��� �ֳ���?<br />���� �� ��������� �޶��� ���� �����ϴ�.<br />�ٸ�, �����ɼ����� ������Ʈ�� ���� �űԸ���� �����Ͽ� ������ �ξ����ϴ�.</div>
<div style="padding-top:10px; color:#666666;" class="g9"><b>����3.</b> �����ɼ���(�� �ѱ��ſ�����) �������������� �뵵�� �����ΰ���?<br />�� �������� ���� ȸ������ �����ɼ��� �߰���� �� ��뿩�θ� �����Ͻ� �� �ֽ��ϴ�.</div>
<div style="padding-top:10px; color:#666666;" class="g9">��Ÿ �ñ��� ������ NICE�ſ������� e-���������� �谡������ڿ��� �������� �����ּ���.<br />(��ȭ��ȣ : 02-2122-4548 , �̸��� : gastar1@nice.co.kr)</div>
</td></tr>
</table>


<div style="padding-top:10"></div>

<div class="extext">
�� �������� (��)�ѱ��ſ������� �����ɼ��񽺸� ��� �� ��뼳���� �ϴ� ������ �Դϴ�.<br />
�ű� �����ɼ��� ��� �� ��뼳���� �Ͻ� ȸ������ �������ɰ����� �������������� ���� �� ������ �Ͽ� �ּ���. <a href="../member/ipin_new.php" class="extext"><strong>[ �����ɰ��� �ٷΰ��� ]</strong></a>
</div>

<div style="padding-top:10"></div>


<table class=tb>
<col class=cellC><col class=cellL>
<tr height=28>
	<td>ȸ���� ID</td>
	<td><input type=text name="ipin[id]" class=line value="<?=$ipin[id]?>"> <font class=extext>NICE�ſ��������� ����� �߱� ���� ID�� �Է��ϼ���</font></td>
</tr>
<tr height=28>
	<td>����Ʈ�ĺ����� SIKey</td>
	<td><input type=text name="ipin[SIKey]" class=line value="<?=$ipin[SIKey]?>" > <font class=extext>NICE�ſ��������� ����� �߱� ���� ����Ʈ�ĺ����� SIKey���� �Է��ϼ���</font></td>
</tr>
<tr height=28>
	<td>Ű��Ʈ�� 80�ڸ�</td>
	<td><input type=text name="ipin[athKeyStr]" class=lline value="<?=$ipin[athKeyStr]?>" style="width:600px;"> <br><font class=extext>NICE�ſ��������� ����� �߱� ���� Ű��Ʈ�� 80�ڸ��� �Է��ϼ���</font></td>
</tr>
<tr height=28>
	<td>�����ɻ�뿩��</td>
	<td class="noline">
	<input type="radio" name="ipin[useyn]" value="y" <?=$checked[ipin][useyn][y]?> onclick="setDisabled()"> ���
	<input type="radio" name="ipin[useyn]" value="n" <?=$checked[ipin][useyn][n]?> onclick="setDisabled()"> ������
	<?=($ipin['nice_useyn'] == 'y') ? " <font class=\"extext\" style=\"color:#FF0000\">'���'���� �����Ͻø� '�� �ѱ��ſ�����'�� ��� ������ '������'���� �ڵ� ���� �˴ϴ�.</font>" : ""?>
	</td>
</tr>
<tr height=28>
	<td>������������</td>
	<td class="noline">
	<input type="radio" name="ipin[minoryn]" value="y" <?=$checked[ipin][minoryn][y]?>> ��� <font class=extext>(19�� �̸� ȸ������ �Ұ�)</font>
	&nbsp;&nbsp;<input type="radio" name="ipin[minoryn]" value="n" <?=$checked[ipin][minoryn][n]?>> ������
	</td>
</tr>
</table>

<div class="button">
<input type=image src="../img/btn_register.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>


<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��������������(NICE�ſ�������)�� �� �� �ֵ���
���α׷��� �⺻ž�� �Ǿ� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�������������񽺸� �ϱ� ���ؼ��� NICE�ſ��������� ��ุ �����Ͻø� �˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�������������� ������ü: <a href="https://www.nuguya.com/" target=_new><font color=white>NICE�ſ������� <font class=ver7>(https://www.nuguya.com)</font></a>
</font></td></tr>
</table>

<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td height=20></td></tr>

<tr><td><font class=def1 color=white>[�ʵ�] �������������� ����</b></font></td></tr>
<tr><td><font class=def1 color=white>��</font> NICE�ſ��������� ��� �������� �ۼ��Ͽ� �߼��ϼ���.</td></tr>
<tr><td><font class=def1 color=white>��</font> NICE�ſ��������� ����ڷκ��� ȸ����ID�� �߱� �����ð� �˴ϴ�.</td></tr>
<tr><td><font class=def1 color=white>��</font> �߱� ������ ȸ����ID,����Ʈ�ĺ�����, Ű��Ʈ���� �� �������� �Է��ϼ���.</td></tr>
<tr><td><font class=def1 color=white>��</font> ���θ� ȸ�����������߿� �Ǹ�Ȯ�� ���񽺰� �������ϴ��� Ȯ���ϼ���.</td></tr>

<tr><td height=8></td></tr>

<tr><td><font class=def1 color=white>[�ܺ�ȣ����] ���������� ���񽺸� ���� ȯ�漳�� ���ǻ���</b></font></td></tr>
<tr><td><font class=def1 color=white>��</font> PHP 4.30 ���� ������ ��� iconv �Լ��� �⺻������ �������� �ʽ��ϴ�.<br>
&nbsp;&nbsp;&nbsp;http://kr.php.net/iconv ����Ʈ�� ���� �Ͻþ� �ش缭���� �´� dll�� ��ġ �Ͻñ� �ٶ��ϴ�.</td></tr>
<tr><td><font class=def1 color=white>��</font> ���������� ���񽺴� ��ġ�Ͻô� �������� NICE�ſ������� ������ 80��Ʈ�� �̿��� ����� �̷�� ���ϴ�.<br>
&nbsp;&nbsp;&nbsp;��ȭ���� secure.nuguya.com(�ݵ�� DNS���񽺿��� �մϴ�.) 80 outer ��Ʈ�� ���� �ִ��� Ȯ���� �ֽñ� �ٶ��ϴ�.</td></tr>
</table>
</div>
<script>
function setDisabled(){
	var fm = document.frmField;
	fm['ipin[minoryn]'][0].disabled = (fm['ipin[useyn]'][0].checked ? false : true);
	fm['ipin[minoryn]'][1].disabled = (fm['ipin[useyn]'][0].checked ? false : true);
}

function checkForm() {
	var ipin_id = document.getElementsByName('ipin[id]'); // ȸ���� ID
	var ipin_SIKey = document.getElementsByName('ipin[SIKey]'); // ����Ʈ�ĺ����� SIKey
	var ipin_athKeyStr = document.getElementsByName('ipin[athKeyStr]'); // Ű��Ʈ�� 80�ڸ�
	var ipin_useyn = document.getElementsByName('ipin[useyn]'); // �����ɻ�뿩��

	if(ipin_useyn[0].checked) {
		if(!ipin_id[0].value || !ipin_SIKey[0].value || !ipin_athKeyStr[0].value) {
			alert("ȸ���� ID, ����Ʈ�ĺ����� SIKey, Ű��Ʈ����\n��� �Է��ϼž� ��� ������ �����Դϴ�.");
			return false;
		}
	}

	return true;
}

window.onload = function() {
	cssRound('MSG01');
	setDisabled();
}
</script>


<? include "../_footer.php"; ?>