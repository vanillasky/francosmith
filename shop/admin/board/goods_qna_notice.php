<?
include "../_header.popup.php";
if($_GET['mode'] == 'noticeModify' && $_GET['sno']){
	$query = "select * from ".GD_GOODS_QNA." where sno='{$_GET['sno']}'";
	$data = $db->fetch($query);
}
?>
<form name=form method=post action="goods_qna_indb.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="<?=$_GET[mode]?>">
<input type=hidden name=sno value="<?=$_GET[sno]?>">
<input type=hidden name='page' value="<?=$_GET[page]?>">
<div class="title title_top">상품문의에 대한 공지<span></span></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>제목</td>
	<td><input type="text" name="subject" value="<?=$data['subject']?>" required fld_esssential label="제목" style="width:90%;" class=line></td>
</tr>
<tr>
	<td>내용</td>
	<td>
	<textarea name=contents style="width:550px;height:350px" type=editor fld_esssential label="내용"><?=$data['contents']?></textarea>
	<script src="../../lib/meditor/mini_editor.js"></script>
	<script>mini_editor("../../lib/meditor/")</script>
	</td>
</tr>
</table>

<div class="button_popup">
<input type=image src="../img/btn_confirm_s.gif">
<a href="javascript:self.close()"><img src="../img/btn_cancel_s.gif"></a>
</div>

</form>

<script>
linecss();
table_design_load();
</script>