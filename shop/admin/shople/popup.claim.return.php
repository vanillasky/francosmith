<?
$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';
include "../_header.popup.php";
require_once ('./_inc/config.inc.php');

$mode = $_GET['m'];

$shople = Core::loader('shople');
?>
<script type="text/javascript" src="./_inc/common.js"></script>
<!-- * -->
<script type="text/javascript">
function fnPutReason() {

	var data = $('frmClaim').serialize().toQueryParams();
	opener.nsShople.claim.return_.<?=$mode?>( data );
	self.close();
	return false;
}
</script>

<div class="title title_top" style="margin-top:10px;">��ǰ���� ó��<span>&nbsp;</span></div>

<p class="gd_notice">
<? if ($mode == 'hold') { ?>
<span>���������� �Է��Ͻð� [Ȯ��]�� Ŭ���Ͻø�, ������ �ֹ��� ���� �ϰ� ����˴ϴ�.</span>
<span>��ǰ���� �� �����ڿ� �����Ͻþ� ��ǰ�ϷḦ �� �� �ֵ��� �����ٶ��ϴ�.</span>
<span>���� �Ŀ��� [��������/��ǰ�Ϸ�]�� �����ϸ� �ź��Ͻ� �� ������ �����Ͻñ� �ٶ��ϴ�.</span>
<span>���� �� �����ڿ��� ���ǰ� �﷣�Ͻ� ��� 11���� �ǸŰ����͸� �����Ͻñ� �ٶ��ϴ�.</span>
<? } else if ($mode == 'reject') { ?>
<span>������ �ֹ��� ���� ��ǰ�ź� ������ �ϰ� �ݿ��˴ϴ�.</span>
<span>���� �Է� �� [Ȯ��]��ư�� �����ø� �ź�ó���� �ǰ� �Է� ������ ������ SMS, �̸��Ϸ� �߼۵˴ϴ�.</span>
<? } else if ($mode == 'accepthold') { ?>
<span>��ǰ�ϷẸ���� �Ͻø� �ڵ����� ��ǰ�Ϸ�ó�� ���� �ʽ��ϴ�.</span>
<span>��ǰ�ϷẸ�� ó�� �� �ݵ�� ��ǰ�Ϸ� ó���� ���ֽñ� �ٶ��ϴ�.</span>
<span>��ǰ�Ϸᰡ ��Ⱓ ��ó�� �Ǹ� ������ Ȯ�� �� ���� ȯ�� ó���� �� �ֽ��ϴ�.</span>
<? } else { }?>
</p>

<form name="frmClaim" id="frmClaim" method="post" action="" onSubmit="return fnPutReason();">
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>����</td>
	<td>
		<select name="reasonCD">
		<option value="">����</option>
		<? foreach (${'_spt_ar_clm_return_'.$mode.'_type'} as $k => $v) { ?>
		<option value="<?=$k?>"><?=$v?></option>
		<? } ?>
		</select>
	</td>
</tr>
<tr>
	<td>�󼼳���</td>
	<td><textarea name="reasonCont"></textarea></td>
</tr>
</table>

<div class="button">
<input type="image" src="../img/btn_save.gif">
<img src="../img/btn_cancel.gif" class="hand" onClick="self.close();">

</div>
</form>

<!-- eof * -->
<script type="text/javascript">
linecss();
table_design_load();
</script>
</body>
</html>
