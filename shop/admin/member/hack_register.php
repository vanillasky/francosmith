<?

include "../_header.popup.php";

if ($_GET[mode]=="modify"){
	$data = $db->fetch("select * from ".GD_LOG_HACK." where sno='" . $_GET['sno'] . "'",1);

	$checked = array();
	$checked[itemcd] = array();
	foreach( codeitem('hack') as $k => $v ){
		if ($data[itemcd]&pow(2,$k)) $checked[itemcd][] = $v;
	}
}
?>

<form name=form method=post action="hack_indb.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="<?=$_GET[mode]?>">
<input type=hidden name=sno value="<?=$_GET[sno]?>">

<div class="title title_top">ȸ��Ż�𳻿� �󼼳���<span></span></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>���̵�</td>
	<td><font class=ver8><?=$data[m_id]?></font></td>
</tr>
<tr>
	<td>�̸�</td>
	<td><font class=ver8><?=$data[name]?></font></td>
</tr>
<tr>
	<td>ó������</td>
	<td><font class=extext><b><?=($data[actor] == '1' ? '����Ż��' : '��������')?></b></font></td>
</tr>
<tr>
	<td>Ż����</td>
	<td><font class=ver8><?=$data[regdt]?> &nbsp;&nbsp;(<?=$data[ip]?>)</td>
</tr>
<tr>
	<td>�������</td>
	<td>
	<font class=small>
	<ol style="margin-left:23;margin-bottom:5;margin-top:10;">
	<? foreach( $checked[itemcd] as $k => $v ){?>
		<li><?=$v?></li>
	<? } ?>
	</ol>
	</td>
</tr>
<tr>
	<td>�����</td>
	<td><textarea name="reason" cols=60 rows=6 style="width:90%;" class=tline><?=$data['reason']?></textarea></td>
</tr>
<tr>
	<td>�����޸�</td>
	<td><textarea name="adminMemo" cols=60 rows=5 style="width:90%;" class=tline><?=$data['adminMemo']?></textarea></td>
</tr>
</table>

<div class="button_popup">
<input type=image src="../img/btn_confirm_s.gif">
<a href="javascript:parent.closeLayer()"><img src="../img/btn_cancel_s.gif"></a>
</div>

</form>

<script>table_design_load();</script>