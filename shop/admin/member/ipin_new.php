<?

$location = 'ȸ������ > �����ɰ���';
include '../_header.php';
include '../../conf/fieldset.php';

$checked['ipin']['nice_useyn'][$ipin['nice_useyn']] = 'checked';
$checked['ipin']['nice_minoryn'][$ipin['nice_minoryn']] = 'checked';

?>

<form name="frmField" method="post" action="indb.php" onsubmit="return checkForm()">
<input type="hidden" name="mode" value="ipin">

<div class="title title_top">�����ɰ���<span>������ ���񽺿� �ʿ��� ������ �����մϴ�. </span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=14')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>


<table border="4" bordercolor="#dce1e1" style="border-collapse:collapse" width="700">
<tr><td style="padding:7px 0px 10px 10px">
<div style="padding-top:5px; color:#666666; font-weight:bold;" class="g9"><b>�� �����ɼ��񽺿� ���� �ȳ��Դϴ�. �� �о����.</b></div>
<div style="padding-top:10px; color:#666666;" class="g9">�� ���������� ���񽺸� �����ϴ� NICE�ſ��������� ��� �������� �ۼ��մϴ�.</div>
<div style="padding-top:3px; color:#666666;" class="g9">�� �ۼ��� �������� ���⼭��(����ڵ����)�� NICE�ſ��������� �ѽ�(2122-4579)�� �����ּ���.</div>
<div style="padding-top:3px; color:#666666;" class="g9">�� NICE�ſ��������� �ּҴ� "����� �������� ���ǵ��� 14-33 NICE�ſ������� CB������� e-����������<br />�� �谡���븮 ��" �Դϴ�.</div>
<div style="padding-top:3px; color:#666666;" class="g9">�� NICE�ſ��������� ����ڷκ��� ȸ����Code, ȸ����Password�� �߱� �����ð� �˴ϴ�.</div>
<div style="padding-top:3px; color:#666666;" class="g9">�� �߱� ������ ȸ����Code, ȸ����Password�� �Ʒ� ���Զ��� �Է��ϰ� ��Ϲ�ư�� �����ϴ�.</div>
<div style="padding-top:3px; color:#666666;" class="g9">�� ���� ���θ�ȭ�鿡�� ȸ������ �����߿� ���������� ���񽺰� ���������� ���۵Ǵ��� Ȯ���ϼ���.</div>
</td></tr>
</table>


<div style="padding-top:10"></div>

<div class="extext">
�� �������� �ű� �����ɼ��� ��� �� ��뼳���� �ϴ� ������ �Դϴ�.<br />
(��)�ѱ��ſ����� �����ɼ����� ID, SIkey, Ű��Ʈ���� �����ϰ� ��� ȸ������ �������ɼ���(�� �ѱ��ſ�����)�� �������������� ���� �� ������ �Ͽ� �ּ���.<br />
<a href="../member/ipin.php" class="extext"><strong>[ �����ɼ���(�� �ѱ��ſ�����) �ٷΰ��� ]</strong></a>
</div>

<div style="padding-top:10"></div>


<table class="tb">
<col class="cellC"><col class="cellL">
<tr height="28">
	<td>ȸ���� Code</td>
	<td><input type="text" name="ipin[code]" id="code" class="line" value="<?=$ipin['code']?>"> <font class="extext">NICE�ſ��������� ��� �� �߱� ���� ����Ʈ CODE�� �Է��ϼ���.</font></td>
</tr>
<tr height="28">
	<td>ȸ���� Password</td>
	<td><input type=text name="ipin[password]" id="password" class="line" value="<?=$ipin['password']?>"> <font class="extext">NICE�ſ��������� ��� �� �߱� ���� ����Ʈ Password�� �Է��ϼ���.</font></td>
</tr>
<tr height="28">
	<td>�����ɻ�뿩��</td>
	<td class="noline">
		<input type="radio" name="ipin[nice_useyn]" id="nice_usey" value="y" <?=$checked['ipin']['nice_useyn']['y']?> onclick="setDisabled()"> ���
		<input type="radio" name="ipin[nice_useyn]" id="nice_usen" value="n" <?=$checked['ipin']['nice_useyn']['n']?> onclick="setDisabled()"> ������
		<?=($ipin['useyn'] == 'y') ? " <font class=\"extext\" style=\"color:#FF0000\">'���'���� �����Ͻø� '�� �ѱ��ſ�����'�� ��� ������ '������'���� �ڵ� ���� �˴ϴ�.</font>" : ""?>
	</td>
</tr>
<tr height="28">
	<td>������������</td>
	<td class="noline">
	<input type="radio" name="ipin[nice_minoryn]" value="y" <?=$checked['ipin']['nice_minoryn']['y']?>> ��� <font class="extext">(19�� �̸� ȸ������ �Ұ�)</font>
	&nbsp;&nbsp;<input type="radio" name="ipin[nice_minoryn]" value="n" <?=$checked['ipin']['nice_minoryn']['n']?>> ������
	</td>
</tr>
</table>

<div class="button"><input type="image" src="../img/btn_register.gif"> <a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a></div>

</form>


<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��������������(NICE�ſ�������)�� �� �� �ֵ���
���α׷��� �⺻ž�� �Ǿ� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�������������񽺸� �ϱ� ���ؼ��� NICE�ſ��������� ��ุ �����Ͻø� �˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�������������� ������ü: <a href="http://www.idcheck.co.kr/" target="_new"><font color="white">NICE�ſ������� <font class="ver7">(http://www.idcheck.co.kr)</font></a>
</font></td></tr>
</table>

<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td height="20"></td></tr>
<tr><td><font class="def1" color="white">[�ʵ�] �������������� ����</b></font></td></tr>
<tr><td><font class="def1" color="white">��</font> NICE�ſ��������� ��� �������� �ۼ��Ͽ� �߼��ϼ���.</td></tr>
<tr><td><font class="def1" color="white">��</font> NICE�ſ��������� ����ڷκ��� ȸ����Code, ȸ����Password�� �߱� �����ð� �˴ϴ�.</td></tr>
<tr><td><font class="def1" color="white">��</font> �߱� ������ ȸ����Code, ȸ����Password�� �� �������� �Է��ϼ���.</td></tr>
<tr><td><font class="def1" color="white">��</font> ���θ� ȸ�����������߿� �Ǹ�Ȯ�� ���񽺰� �������ϴ��� Ȯ���ϼ���.</td></tr>

<tr><td height="8"></td></tr>
<tr><td><font class="def1" color="white">[�ܺ�ȣ����] ���������� ���񽺸� ���� ȯ�漳�� ���ǻ���</b></font></td></tr>
<tr><td><font class="def1" color="white">��</font> PHP 4.30 ���� ������ ��� iconv �Լ��� �⺻������ �������� �ʽ��ϴ�.<br />
&nbsp;&nbsp;&nbsp;http://kr.php.net/iconv ����Ʈ�� ���� �Ͻþ� �ش缭���� �´� dll�� ��ġ �Ͻñ� �ٶ��ϴ�.</td></tr>
<tr><td><font class="def1" color="white">��</font> ���������� ���񽺴� ��ġ�Ͻô� �������� NICE�ſ������� ������ 80��Ʈ�� �̿��� ����� �̷�� ���ϴ�.<br /> &nbsp;&nbsp;&nbsp;��ȭ���� secure.nuguya.com(�ݵ�� DNS���񽺿��� �մϴ�.) 80 outer ��Ʈ�� ���� �ִ��� Ȯ���� �ֽñ� �ٶ��ϴ�.</td></tr>
</table>
</div>

<script>
	function setDisabled() {
		var fm = document.frmField;
		fm['ipin[nice_minoryn]'][0].disabled = (fm['ipin[nice_useyn]'][0].checked ? false : true);
		fm['ipin[nice_minoryn]'][1].disabled = (fm['ipin[nice_useyn]'][0].checked ? false : true);
	}

	function checkForm(f) {
		if($('nice_usey').checked) {
			if(!$('code').value || !$('password').value) {
				alert("ȸ���� Code�� ȸ���� Password��\n��� �Է��ϼž� ��� ������ �����Դϴ�.");
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