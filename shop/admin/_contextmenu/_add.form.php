<?
include "../lib.php";

$sno = isset($_REQUEST['sno']) ? $_REQUEST['sno'] : '';
$mode = 'add';
if ($sno) {
	$menu = $db->fetch("SELECT * FROM ".GD_CONTEXTMENU." WHERE sno = '".$sno."' AND m_no = '".$sess['m_no']."'",1);
	@extract($menu);
	$mode = 'mod';
}
else {
	$url = isset($_REQUEST['url']) ? $_REQUEST['url'] : '';
	$name = '';
	$target = '';
}
?>

<div class="title title_top" style="padding-bottom:7px;border-bottom:2px solid #D0D0D0;">���� �������� �߰�</div>

<form name="frmContextMenuForm">
<input type="hidden" name="mode" value="<?=$mode?>">
<input type="hidden" name="sno" value="<?=$sno?>">
<table width="100%" cellpadding="0" cellspacing="1" bgcolor="#E6E6E6" class="context_menu_form">
<col width="80">
<col width="*">
<tr>
	<th>URL</th>
	<td><input type="text" name="url" value="<?=$url?>" style="width:100%"></td>
</tr>
<tr>
	<th>�̵�</th>
	<td><select name="target">
	<option value="_self" <?=$target == '_self' ? 'selected' : ''?>>����â</option>
	<option value="_blank" <?=$target == '_blank' ? 'selected' : ''?>>��â����</option>
	</select></td>
</tr>
<tr>
	<th>�޴���</th>
	<td><input type="text" name="name" value="<?=$name?>" style="width:100px;"> <span class="extext">�����Ͻ� �̸��� �Է��ϼ���.</span></td>
</tr>

</table>
</form>

<div class="context_menu_form_button-wrap">
	<a href="javascript:void(0);" onClick="nsGodoContextMenu.setup.save(document.frmContextMenuForm)"><img src="../img/btn_<?=$mode == 'mod' ? 'save' : 'register' ?>.gif"></a>
	<a href="javascript:void(0);" onClick="nsGodoContextMenu.setup.close()"><img src="../img/btn_cancel.gif"></a>
</div>
