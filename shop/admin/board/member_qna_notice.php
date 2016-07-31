<?
include "../_header.popup.php";
if($_GET['mode'] == 'noticeModify' && $_GET['sno']){
	$query = "select * from ".GD_GOODS_REVIEW." where sno='{$_GET['sno']}'";
	$data = $db->fetch($query);
}
?>
<form name=form method=post action="member_qna_indb.php" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="<?=$_GET[mode]?>">
<input type=hidden name=sno value="<?=$_GET[sno]?>">
<input type=hidden name='page' value="<?=$_GET[page]?>">
<div class="title title_top">1:1문의 공지 등록<span></span></div>

<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>제목</td>
	<td><input type="text" name="subject" value="<?=$data['subject']?>" style="width:90%;" required fld_esssential label="제목" class=line></td>
</tr>
<tr>
	<td>내용</td>
	<td>
	<textarea name="contents" id="contents" style="width:550px;height:350px" required fld_esssential label="내용"><?=$data['contents']?></textarea>
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