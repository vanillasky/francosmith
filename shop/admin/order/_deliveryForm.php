<?
switch (basename($_SERVER['PHP_SELF'])) {
	case 'list.integrate.php':
		$_manual_url = $guideUrl.'board/view.php?id=order&no=24';
		break;
	case 'list.step2.php':
		$_manual_url = $guideUrl.'board/view.php?id=order&no=27';
		break;
	case 'list.step3.php':
		$_manual_url = $guideUrl.'board/view.php?id=order&no=28';
		break;
	default :
		$_manual_url = $guideUrl.'board/view.php?id=order&no=2';
		break;
}
?>
<div>
<div style="padding-top:15px"></div>
<div class="title title_top">��ۿϷ�ó�� �� ���� �ϰ����<span>�뷮�� �����ȣ ��� �� ���ó���� �ϰ��� ����Ͻ� �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$_manual_url?>')"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle"/></a></div>

<div style="padding-top:15px"></div>

<form name=deliveryfm method=post action="../order/data_delivery_indb.php" target='ifrmHidden'  enctype="multipart/form-data" onsubmit="return chkForm(this)">

<div style="padding-top:5px;padding-left:10px;"><font class=extext>* �ۼ��Ϸ�� ����CSV������ �ø�����.</div>


<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td width=240 height=35>���� CSV ���� �ø���</td>
	<td><input type="file" name="file_excel" size="45" required label="CSV ����"> &nbsp;&nbsp; <span class="noline"><input type=image src="../img/btn_regist_s.gif" align="absmiddle"></span></td>
</tr>
</table>
</form>


<div style="padding-top:15px"></div>

<div id=MSG02>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><b>[��ۿϷ� ó�� �ϱ�]</b></td></tr>
<tr><td>&nbsp;- ��ۿϷ� ó���� �������Ϸ� ���ε��Ͽ� �ϰ������� ����� �� �ֽ��ϴ�.</td></tr>
<tr><td>&nbsp;- �ֹ����� ����Ʈ���� �ٿ���� �������� ��Ĵ�� ��ۿϷ��� �׸� ��ۿϷ����ڸ� �Է�, ������(CSV ����) [����ϱ�] ��ư�� Ŭ���Ͽ� ������ ���ε� �մϴ�.</td></tr>
<tr><td>&nbsp;- ��ۿϷ��� ǥ��� �ݵ�� "YYYY-MM-DD HH:mm:ss" �������� �Է��ϼž� �մϴ�. ��) 2012-12-30 13:00:00</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td><b>[�������� �̷��� �ϼ���.]</b></td></tr>
<tr><td>�� �����Է¹�� ����</td></tr>
<tr><td>&nbsp;- �ù������ ����Ͻ÷��� ���� ������������ > ���θ� �⺻���� > �ֹ� �������� "�����Է¹�� ����" �� ���ּž��մϴ�. <a href="../basic/order_set.php"><font color="#ffffff"><b>[�ֹ����� �ٷΰ���]</b></font></a></td></tr>
<tr><td>&nbsp;- �����Է¹�� ���� </td></tr>
<tr><td>&nbsp;&nbsp;&nbsp;  �ֹ��� ��ǰ�� ���� ���� ��� �ϳ��� �����ȣ�θ� �����ϰų�, ������ ��ǰ���� �����ȣ�� �Է��ϰ� �ϴ� ����Դϴ�</td></tr>
<tr><td>&nbsp;&nbsp;&nbsp;  a. �� ���� �����ȣ�� �Է� - �ֹ����� �����ȣ�� �Է��� �� ����</td></tr>
<tr><td>&nbsp;&nbsp;&nbsp;  b. ��ǰ���� �����ȣ�� �Է� - ��ǰ���� �����ȣ�� ���� �Է��� �� ����</td></tr>
<tr><td>�� �����׸���</td></tr>
<tr><td>&nbsp;- �����Է¹���� "�� ���� �����ȣ�� �Է�"�̸� "�ֹ��� �׸���"�� �����ϰ� "��ǰ���� �����ȣ�� �Է�"�̸� "��ǰ�� �׸���"�� ���� �����մϴ�.</td></tr>
<tr><td>&nbsp;- �ֹ��� �׸� ������ �ֹ���ȣ, �����ȣ, ��ۻ��ڵ�� �ʼ� �׸��Դϴ�.</td></tr>
<tr><td>&nbsp;- ��ǰ�� �׸� ������ �Ϸù�ȣ, �ֹ���ȣ, �����ȣ, ��ۻ��ڵ�� �ʼ� �׸��Դϴ�.</td></tr>
<tr><td>�� �����ٿ�ε�</td></tr>
<tr><td>&nbsp;- �����Է¹���� "�� ���� �����ȣ�� �Է�"�̸� "�ֹ��� ��������" �� �����ð� "��ǰ���� �����ȣ�� �Է�"�̸� "��ǰ�� ��������" �� �ٿ�ε� �޽��ϴ�.</td></tr>
<tr><td>�� ������</td></tr>
<tr><td>&nbsp;- �ٿ�ε� ������ �������Ͽ��� ����ڵ�� �����ȣ�� �Է��մϴ�.</td></tr>
<tr><td>&nbsp;- ��ۻ��ڵ�� ���θ��⺻���� > ���/�ù���å���� �ش� �ù�縦 ���� �� ������ư�� Ŭ���ϸ� ������ȣ�� ǥ�õǴ� ��ȣ�Դϴ� <a href="../basic/delivery.php"><font color="#ffffff"><b>[���/�ù�� ���� �ٷΰ���]</b></font></td></tr>
<tr><td>&nbsp;-�����ǡ��������Ͽ� �ִ� �ʵ������� ������ ������ ������ �����׸�� �ݵ�� �����ؾ� �մϴ�. <br/>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;�ٸ� �������� ������ ��� ������ �߻��ϰ� �˴ϴ�.</td></tr>
<tr><td>&nbsp;-�����ǡ�ó�� �̿��Ͻô� ���� �ݵ�� �ֹ� 1�Ǹ��� ������� �׽�Ʈ�� ������ ���ñ� �ٶ��ϴ�.<br/>
<tr><td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;�ùٸ��� ���ε�Ǹ� ��ü���� �ֹ����� ������� �������ֽñ� �ٶ��ϴ�.</td></tr>
<tr><td>&nbsp;- �Է��� �Ϸ�� ���������� CSV �������� ������ ��  ���� CSV ���� �ø��⸦ �̿��� ���������� �ø��ϴ�.</td></tr>
<tr><td>&nbsp;- �ֹ�����(�ֹ��󼼳���)�� �̿��ؼ� ���������� ��Ȯ�� �ԷµǾ����� Ȯ���Ͻ� �� �ֽ��ϴ�.</td></tr>
<tr><td>�� �ֹ���, �޴��� ������ �������Ͽ��� �����Ͽ� ����� �� �ֽ��ϴ�. (��, ���������� ������ ��ɿ��� �����Ͻ� �� �����ϴ�.)</td></tr>
<tr><td>&nbsp;- �������� ���� : �ֹ��ڸ�, �̸���, �ֹ�����ȭ��ȣ, �ֹ����ڵ���, �޴º��̸�, �޴º���ȭ��ȣ, �޴º��ڵ���, (��)�����ȣ, (��)�����ȣ, (��)�����ּ�, (��)���θ��ּ�</td></tr>
</table>
</div>
</div>
<script>cssRound('MSG02')</script>