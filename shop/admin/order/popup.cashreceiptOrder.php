<?
include '../_header.popup.php';
list($cnt) = $db->fetch("select count(*) from ".GD_CASHRECEIPT." where ordno='{$_GET['ordno']}' and status in ('RDY', 'ACK')");
?>

<div class="title title_top">���ݿ����� ��û</div>

<? if ($cnt){ ?>
<div style="border:solid 1px #BDBDBD; padding:1px;">
	<div style="border:solid 1px #DBDBDB; padding:20px; background-color:#F9F9F9;">
		�̹� ���ݿ����� �߱��� ��û�ϼ̽��ϴ�.
	</div>
</div>
<? } else { ?>
<?
// ���հ��� ����� LG+ �� ���ݿ����� �߱� (��ǰ�� �������η� ó��)
if ($cfg['settlePg'] =='lgdacom' || $cfg['settlePg'] =='inicis' || $cfg['settlePg'] =='inipay'){
	include './cashreceipt._form_multitax.php';
} else {
	include './cashreceipt._form.php';
}
?>
<? } ?>

<div id="MSG01">
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ݿ����� �߱޽� ����û�� �뺸�Ǳ� ������ ��Ȯ�� �ڷḦ �Է��մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�߱޿�û �� <a onclick="parent.location.href='../order/cashreceipt.list.php';" style="cursor:pointer;">[���ݿ����� �߱�/��ȸ]</a> ���� �߱��Ͽ��� �մϴ�.</font></td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">[����]</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�鼼��ǰ�� ���� ���ݿ����� �߱��� �鼼�����(�Ǵ� �鼼+���� ���ջ����)�� PG�翡 ���� ��û�ϼž� �մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�̽�û ���¿��� �߱��� ��� PG�� ��å�� ���� �߱� �ݾ��� ������ �� �ֽ��ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<script>table_design_load();</script>