<?

$location = '���ݿ����� ���� > ���ݿ����� �����߱�';
include '../_header.php';

?>

<div class="title title_top">���ݿ����� �����߱� <span>���ݿ������� �ֹ����� �ƴ� ���������� �߱޿�û�� �����մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=order&no=19')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>

<?
// ���հ��� ����� LG+ �� ���ݿ����� �߱� (��ǰ�� �������η� ó��)
if ($cfg['settlePg'] =='lgdacom' || $cfg['settlePg'] =='inicis' || $cfg['settlePg'] =='inipay'){
	include './cashreceipt._form_multitax.singly.php';
} else {
	include './cashreceipt._form.php';
}
?>

<div id="MSG01">
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">������ �� ������������ ������ �߰ų� ��Ÿ �������� �Ǹſ� ���ؼ� ���ݿ����� �߱��� �����մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ݿ����� �߱޽� ����û�� �뺸�Ǳ� ������ ��Ȯ�� �ڷḦ �Է��մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�߱޿�û �� <a href="../order/cashreceipt.list.php">[���ݿ����� �߱�/��ȸ]</a> ���� �߱��Ͽ��� �մϴ�.</font></td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle"><span style="font-size:9pt;">��</font> ����) ���ݿ����� �����߱� �Ϸ�� ���� �ֹ��ǰ� ��Ī���� �ʱ� ������, �ֹ�����Ʈ>�ش��ֹ��� �󼼳���>��������>���ݿ����� �κ���<br>
&nbsp;&nbsp;&nbsp;<b>[���ݿ����� �����߱� �� �������� �Ǿ���]</b>�� �� üũ�ϼž� �ֹ��ڰ� �ߺ����� ��û�� �� �� ���� ó�� �˴ϴ�.
</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>