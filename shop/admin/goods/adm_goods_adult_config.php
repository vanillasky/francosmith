<?
$location = "��ǰ �ΰ���� ���� > �������� ��ǰ ������� ����";
include "../_header.php";

$config = Core::loader('config')->load('goods_adult_auth');
?>
<h2 class="title">�������� ��ǰ ������� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=product&no=45');"><img src="../img/btn_q.gif" border="0" align="absmiddle" hspace="2" /></a></h2>

<form method="post" class="admin-form" name="form" action="indb_adm_goods_adult_config.php">
<table class="admin-form-table">
<tr>
	<th>������� ����</th>
	<td>
		<label><input type="radio" name="allow_guest_auth" value="1" <?=$config['allow_guest_auth'] != '0' ? 'checked ' : ''?>/> ��ü(��ȸ��+ȸ��)</label>
		<label><input type="radio" name="allow_guest_auth" value="0" <?=$config['allow_guest_auth'] == '0' ? 'checked ' : ''?>/> ȸ������(��ȸ������)</label>

		<dl class="help">
			<dt>��ü�� ������</dt>
			<dd>��ȸ���� ��� ����������ǰ�� ���ٽ� ���� ���������� �ʿ�� �ϱ� ������ ���񽺺���� �߰��� �ΰ� �˴ϴ�. ���� ������ �ּ���.</dd>
			<dt>ȸ������ ���� ������</dt>
			<dd>���θ��� ���Ե� ȸ���� ���Ͽ� �������� ������ ����˴ϴ�. ��ȸ�� �� ��� ȸ������ �Ϸ�� ���������� �� �� �ֽ��ϴ�.</dd>
			<dd>�������� ��ǰ ������ �� ��ǰ�� [ ��ǰ���/�����ϱ� > ��ǰ�߰��������� > �������� ] ���� �Ͻ� �� �ֽ��ϴ�.</dd>
			<dd>�������� ����� ������ ���� ���� ��û�Ϸ� �� �̿� �����մϴ�. <a href="../member/realname_info.php" target="_blank"><img src="../img/buttons/btn_confirmation.gif" /></a></dd>
		</dl>
	</td>
</tr>
</table>

<div class="button-container">
	<input type="image" src="../img/btn_save.gif" />
</div>
</form>

<? include "../_footer.php"; ?>
