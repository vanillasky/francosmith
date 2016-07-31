<?
/*------------------------------------------------------------------------------
ⓒ Copyright 2005, Flyfox All right reserved.
@파일내용: 디자인코디툴 > 새이름으로 저장하기 & 삭제하기
@수정내용/수정자/수정일:
------------------------------------------------------------------------------*/
?>


<div style="position:relative;display:none;" id="div_saveas">
<div style="position:absolute;top:-200;left:200;border:2px #000000 solid;background:#ffffff;width:350;">
<table width="100%" border="0" cellspacing="0" cellpadding="2" style="margin-bottom:5px;">
<tr style="background:#000000;color:#ffffff;">
	<td>새이름으로 저장하기</td>
	<td align="center" style="cursor:pointer;width:20;" onclick="DCSM.call();"><strong>ⓧ<strong></td>
</tr>
</table>

<form method="post" name="save" onsubmit="return DCSM.chk( this );">
<input type="hidden" name="dir_name" value="<?=dirname($_GET['design_file']) . '/';?>">
<input type="hidden" name="file_result">
<table width="90%" border="0" cellspacing="0" cellpadding="1" align="center">
<tr>
	<td>디렉토리</td>
	<td><?=dirname($_GET['design_file']) . '/';?> .. [<?=$data_dir['text'];?>]</td>
</tr>
<tr>
	<td>파일명</td>
	<td>
	<input type="text" name="file_name" size="20">
	<select name="file_ext">
	<option value=".htm">.htm</option>
	<option value=".txt">.txt</option>
	</select>
	&nbsp; <a href="javascript:DCSM.file_check()"><img src="../img/btn_overlap.gif" align=absmiddle></a>
	</td>
</tr>
<tr>
	<td>설명</td>
	<td><input type="text" name="file_desc" size="35"></td>
</tr>
</table>

<center style="margin:5px 0px;" class="noline"><input type="image" src="../img/btn_save_s.gif" alt="저장"> <a href="javascript:DCSM.call();"><img src="../img/btn_cancel_s.gif" border=0></a></center>
</form>
</div>
</div>