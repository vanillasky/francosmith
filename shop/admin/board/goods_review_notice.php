<?
include "../_header.popup.php";
if($_GET['mode'] == 'noticeModify' && $_GET['sno']){
	$query = "select * from ".GD_GOODS_REVIEW." where sno='{$_GET['sno']}'";
	$data = $db->fetch($query);
}
?>
<form name=form method=post action="goods_review_indb.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="<?=$_GET[mode]?>">
<input type=hidden name=sno value="<?=$_GET[sno]?>">
<input type=hidden name='page' value="<?=$_GET[page]?>">
<div class="title title_top">��ǰ�ı� �Խ��� ����<span></span></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>����</td>
	<td><input type="text" name="subject" value="<?=$data['subject']?>" style="width:90%;" class=line required fld_esssential label="����"></td>
</tr>
<tr>
	<td>����</td>
	<td>
	<textarea name="contents" id="contents" style="width:550px;height:350px" required fld_esssential label="����"><?=$data['contents']?></textarea>
	</td>
</tr>
</table>

<div class="button_popup">
<input type=image src="../img/btn_confirm_s.gif">
<a href="javascript:window.close()"><img src="../img/btn_cancel_s.gif"></a>
</div>

</form>

<script>
linecss();
table_design_load();
</script>