<?

include "../_header.popup.php";

if ($_GET['mode']=="modify"){
	$data = $db->fetch("select * from ".GD_CODE." where sno='" . $_GET['sno'] . "'",1);
}
else {
	$data['groupcd'] = $_GET['groupcd'];
}

list( $groupnm ) = $db->fetch("SELECT itemnm FROM ".GD_CODE." WHERE groupcd='' and itemcd='" . $data['groupcd'] . "'"); # �ڵ�з���
?>

<form name="form" method="post" action="data_code_indb.php" onsubmit="return chkForm(this);">
<input type="hidden" name="mode" value="<?=$_GET['mode']?>" />
<input type="hidden" name="sno" value="<?=$_GET['sno']?>" />
<input type="hidden" name="groupcd" value="<?=$data['groupcd']?>" />

<div class="title title_top">�ڵ� <?=( $_GET['mode'] == "modify" ? '����' : '���' )?><span></span></div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr height="26">
	<td>�з�</td>
	<td><?=$groupnm?></td>
</tr>
<tr>
	<td>�ڵ��ȣ</td>
	<td><input type="text" name="itemcd" size="5"  maxlength="2" value="<?echo( $data['itemcd'] )?>" onKeyDown="onlynumber();"> 2�ڸ�</td>
</tr>
<tr>
	<td>�ڵ��</td>
	<td><input type="text" name="itemnm" size="60"  maxlength="30" value="<?echo( $data['itemnm'] )?>"> 30�ڸ�</td>
</tr>
</table>

<div class="button_popup">
<input type="image" src="../img/btn_confirm_s.gif" />
<a href="javascript:parent.closeLayer()"><img src="../img/btn_cancel_s.gif" /></a>
</div>

</form>

<script>table_design_load();</script>