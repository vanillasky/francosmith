<?
$location = "���ϰ��� > �Ŀ����� ����";
include "../_header.php";
	
@include "../../conf/amail.set.php";
$set = $set['amail'];
$mode = "amailsetting";

if(!file_exists('../../conf/amail.set.php')){	
	$cell = explode('|',$cfg['smsAddAdmin']);
	if(!$cell[0]) $cell[0] = $cfg['compPhone'];
	$set['user_name'] = $cfg['ceoName'];
	$set['user_email'] = $cfg['adminEmail'];
	$set['user_tel'] = $cfg['compPhone'];
	$set['user_cell'] = $cell[0];
	$set['user_id'] = $godo['sno'];	
}
?>
<div class="title title_top">�Ŀ����� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=12')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<div style="font-weight:bold">�Ŀ����ϼ��� ���񽺰� �������� ������ �������� �����Ͻʽÿ�!</div>
<div style="font:0;height:5"></div>
<form method="post" action="indb.php" onsubmit="return chkForm(this)">
<input type="hidden" name="mode" value="<?=$mode?>">
<input type="hidden" name="user_id" value="<?=$set[user_id]?>">
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>�̸�</td>
	<td>
	<input type="text" name="user_name" value="<?=$set['user_name']?>" class="line" size="40" required>	
	</td>
</tr>
<tr>
	<td>�̸���</td>
	<td>
	<input type="text" name="user_email" value="<?=$set['user_email']?>" class="line" size="40" required>
	</td>
</tr>
<?$tel = @explode('-',$set[user_tel]);?>
<tr>
	<td>��ȭ��ȣ</td>
	<td>
	<input type="text" name="tel[]" value="<?=$tel[0]?>" class="line" size='4' maxlength='4' onkeydown="onlynumber()" label="��ȭ��ȣ" required>
	-
	<input type="text" name="tel[]" value="<?=$tel[1]?>" class="line" size='4' maxlength='4' onkeydown="onlynumber()" label="��ȭ��ȣ" required>
	-
	<input type="text" name="tel[]" value="<?=$tel[2]?>" class="line" size='4' maxlength='4' onkeydown="onlynumber()" label="��ȭ��ȣ" required>

	<span class=small><font class=extext>(�ݵ�� '-'���� ���ڷθ� �Է�)</font></span>
	</td>
</tr>
<?$cell = @explode('-',$set[user_cell]);?>
<tr>
	<td>�޴���</td>
	<td>
	<input type="text" name="cell[]" value="<?=$cell[0]?>" class="line" size='4' maxlength='4' onkeydown="onlynumber()" label="�޴���" required>
	-
	<input type="text" name="cell[]" value="<?=$cell[1]?>" class="line" size='4' maxlength='4' onkeydown="onlynumber()" label="�޴���" required>
	-
	<input type="text" name="cell[]" value="<?=$cell[2]?>" class="line" size='4' maxlength='4' onkeydown="onlynumber()" label="�޴���" required>

	<span class=small><font class=extext>(�ݵ�� '-'���� ���ڷθ� �Է�)</font></span>
	</td>
</tr>

</table>
<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>
<div style="font:0;height:5"></div>
<div class="button"><input type="image" src="../img/btn_register.gif" />
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a></div>
</form>
<? include "../_footer.php"; ?>
