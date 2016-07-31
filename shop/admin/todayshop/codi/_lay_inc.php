<?
/*------------------------------------------------------------------------------
ⓒ Copyright 2005, Flyfox All right reserved.
@파일내용: 디자인코디툴 > 레이아웃 > 인클루드
@수정내용/수정자/수정일:
------------------------------------------------------------------------------*/
?>

<form method="post" name="fm" action="../todayshop/codi/indb.php?mode=save&design_file=<?=$_GET['design_file']?>" onsubmit="return chkForm( this );" enctype="multipart/form-data">
<input type="hidden" name="linkurl" value="<?=$data_file['linkurl']?>">


<div style=" margin:17px 0;">
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>파일설명</td>
	<td><input type="text" name="text" value="<?=$data_file['text']?>" size="50" class="line"></td>
</tr>
</table>
</div>


<?
@include_once dirname(__FILE__) . "/_codi_info.php"; # 파일정보
?>


<?
{ // 디자인코디툴

	$tmp = array();
	$tmp['t_name']		= 'content';
	$tmp['t_width']		= '100%';
	$tmp['t_rows']		= 40;
	$tmp['t_property']	= ' required label="HTML 소스"';
	$tmp['tplFile']		= "/" . $_GET['design_file'];

	echo "<script>DCTM.write('{$tmp['t_name']}', '{$tmp['t_width']}', '{$tmp['t_rows']}', '{$tmp['t_property']}', '{$tmp['tplFile']}');</script>";
}
?>


<div style="padding:20px" align=center class=noline>
<input type=image src="../img/btn_save.gif" alt="저장하기">
<!--
<a href="javascript:;" onclick="DCSM.call( 'on' );"><img src="../img/btn_saveas.gif" border=0></a>
<a href="javascript:file_del('<?=$_GET['design_file']?>');"><img src="../img/btn_del.gif" border=0></a>
-->
</div>


</form>