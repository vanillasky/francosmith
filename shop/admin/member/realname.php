<?

$location = "ȸ������ > �Ǹ�Ȯ�ΰ���";
include "../_header.php";
include "../../conf/fieldset.php";

$checked[realname][useyn][$realname[useyn]] = "checked";
$checked[realname][minoryn][$realname[minoryn]] = "checked";

?>

<form name=frmField method=post action="indb.php">
<input type=hidden name=mode value=realname>

<div class="title title_top">�Ǹ�Ȯ�ΰ���<span>�Ǹ�Ȯ�ο� �ʿ��� �׸��� �����մϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=13')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>


<table border=4 bordercolor=#dce1e1 style="border-collapse:collapse" width=700>
<tr><td style="padding:7 0 10 10">
<div style="padding-top:5"><b>�� �Ǹ�Ȯ�μ��񽺿� ���� �ȳ��Դϴ�. �� �о����.</b></div>
<div style="padding-top:7"><font class=g9 color=666666>�� �Ǹ�Ȯ�μ��񽺸� �����ϴ� NICE�ſ��������� ��� �������� �ۼ��մϴ�. <a href="http://www.godo.co.kr/service/nice_godo.zip"><font class=extext_l>[������ �ٿ�ε�ޱ�]</font></a></div>
<div style="padding-top:5"><font class=g9 color=666666>�� �ۼ��� �������� ���⼭��(����ڵ����)�� NICE�ſ��������� �ѽ�(2122-4579)�� �����ּ���.</div>
<div style="padding-top:5"><font class=g9 color=666666>�� NICE�ſ��������� �ּҴ� "����� �������� ���ǵ��� 14-33 NICE�ſ������� CB������� e-����������<br />�� �谡���븮 ��" �Դϴ�.</div>
<div style="padding-top:5"><font class=g9 color=666666>�� NICE�ſ��������� ����ڷκ��� ȸ���� ID�� �߱� �����ð� �˴ϴ�.</div>
<div style="padding-top:5"><font class=g9 color=666666>�� �߱� ������ ȸ���� ID�� �Ʒ� ���Զ��� �Է��ϰ� ��Ϲ�ư�� �����ϴ�.</div>
<div style="padding-top:5"><font class=g9 color=666666>�� ���� ���θ�ȭ�鿡�� ȸ������ �����߿� �Ǹ�Ȯ�� ���񽺰� ���������� ���۵Ǵ��� Ȯ���ϼ���.</div></td></tr>
</table>


<div style="padding-top:10"></div>


<table class=tb>
<col class=cellC><col class=cellL>
<tr height=28>
	<td>ȸ���� ID</td>
	<td><input type=text name="realname[id]" class=line value="<?=$realname[id]?>"> <font class=extext>NICE�ſ��������� ����� �߱� ���� ID�� �Է��ϼ���</font></td>
</tr>
<tr height=28>
	<td>�Ǹ�Ȯ�ο���</td>
	<td class="noline">
	<input type="radio" name="realname[useyn]" value="y" <?=$checked[realname][useyn][y]?> onclick="setDisabled()"> ���
	<input type="radio" name="realname[useyn]" value="n" <?=$checked[realname][useyn][n]?> onclick="setDisabled()"> ������
	</td>
</tr>
<tr height=28>
	<td>������������</td>
	<td class="noline">
	<input type="radio" name="realname[minoryn]" value="y" <?=$checked[realname][minoryn][y]?>> ��� <font class=extext>(19�� �̸� ȸ������ �Ұ�)</font>
	&nbsp;&nbsp;<input type="radio" name="realname[minoryn]" value="n" <?=$checked[realname][minoryn][n]?>> ������ <font class=extext>(���� ���ο� ������� �Ǹ�Ȯ�θ� ��)</font>
	</td>
</tr>
</table>

<table style="margin-top:20px;">
<tr>
	<td colspan="2" style="font-weight:bold;text-align:center;height:25px;">�Ǹ�Ȯ���� Ȱ��Ǵ� ������</td>
</tr>
<tr align="center">
	<td style="line-height:180%;">
	��Ʈ�� ���������� �Ǹ�Ȯ��<br>
	<img src="../img/img_realname_intro.gif">
	</td>
	<td style="line-height:180%;">
	ȸ������ ���������� �Ǹ�Ȯ��<br>

	<img src="../img/img_realname_member.gif">
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
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�Ǹ�Ȯ�μ���(NICE�ſ�������)�� �� �� �ֵ���
���α׷��� �⺻ž�� �Ǿ� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�Ǹ�Ȯ�μ��񽺸� �ϱ� ���ؼ��� NICE�ſ��������� ��ุ �����Ͻø� �˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�Ǹ�Ȯ�μ��� ������ü: <a href="http://www.idcheck.co.kr/" target="_new"><font color="white">NICE�ſ������� <font class="ver7">(http://www.idcheck.co.kr)</font></a>
</font></td></tr>
</table>

<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td height=20></td></tr>

<tr><td><font class=def1 color=white>[�ʵ�] �Ǹ�Ȯ�μ��� ����</b></font></td></tr>
<tr><td><font class=def1 color=white>��</font> NICE�ſ��������� ��� �������� �ۼ��Ͽ� �߼��ϼ���.</td></tr>
<tr><td><font class=def1 color=white>��</font> NICE�ſ��������� ����ڷκ��� ȸ����ID�� �߱� �����ð� �˴ϴ�.</td></tr>
<tr><td><font class=def1 color=white>��</font> �߱� ������ ȸ����ID�� �� �������� �Է��ϼ���.</td></tr>
<tr><td><font class=def1 color=white>��</font> �Ǹ�Ȯ�� ��� ���θ� �����մϴ�.</td></tr>
<tr><td><font class=def1 color=white>��</font> �������� ���θ� �����մϴ�.</td></tr>
<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;* ���� �������δ� ȸ������ �������� �����Դϴ�. </td></tr>
<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;* ���� ���� ����Ʈ �湮�� �������� �����ϴ� ��ɰ��� �ٸ� ��������� ���� �����ñ� �ٶ��ϴ�.</td></tr>
<tr><td><font class=def1 color=white>��</font> ���θ� ȸ������ ���� �߿� �Ǹ�Ȯ�� ���񽺰� ������ �ϴ��� Ȯ���ϼ���.</td></tr>

<tr><td height=8></td></tr>

<tr><td><font class=def1 color=white>[�ܺ�ȣ����] �Ǹ�Ȯ�� ���񽺸� ���� ȯ�漳�� ���ǻ���</b></font></td></tr>
<tr><td><font class=def1 color=white>��</font> PHP 4.30 ���� ������ ��� iconv �Լ��� �⺻������ �������� �ʽ��ϴ�.<br>
&nbsp;&nbsp;&nbsp;http://kr.php.net/iconv ����Ʈ�� ���� �Ͻþ� �ش缭���� �´� dll�� ��ġ �Ͻñ� �ٶ��ϴ�.</td></tr>
<tr><td><font class=def1 color=white>��</font> ���νǸ�Ȯ�� ���񽺴� ��ġ�Ͻô� �������� NICE�ſ������� ������ 80��Ʈ�� �̿��� ����� �̷�� ���ϴ�.<br>
&nbsp;&nbsp;&nbsp;��ȭ���� secure.nuguya.com(�ݵ�� DNS���񽺿��� �մϴ�.) 80 outer ��Ʈ�� ���� �ִ��� Ȯ���� �ֽñ� �ٶ��ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<script>
function setDisabled(){
	var fm = document.frmField;
	fm['realname[minoryn]'][0].disabled = (fm['realname[useyn]'][0].checked ? false : true);
	fm['realname[minoryn]'][1].disabled = (fm['realname[useyn]'][0].checked ? false : true);
}
setDisabled()
</script>


<? include "../_footer.php"; ?>