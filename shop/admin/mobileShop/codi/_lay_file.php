<?
/*------------------------------------------------------------------------------
ⓒ Copyright 2005, Flyfox All right reserved.
@파일내용: 디자인코디툴 > 레이아웃 > 독립파일
@수정내용/수정자/수정일:
------------------------------------------------------------------------------*/
?>

<div style="padding-top:10;"></div>

<form method="post" name="fm" action="../mobileShop/codi/indb.php?mode=save&design_file=<?=$_GET['design_file']?>" onsubmit="return chkForm( this );" enctype="multipart/form-data">
<input type="hidden" name="linkurl" value="<?=$data_file['linkurl']?>">


<?
@include_once dirname(__FILE__) . "/_codi_map.php";
?>


<div style="margin:17px 0;">
<table class=tb>
<tr>
	<td width=50>파일설명</td>
	<td><input type="text" name="text" value="<?=$data_file['text']?>" class="line"></td>
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
<? if ($isPreview == true){ ?>
<a onclick="preview()"><img src="../img/codi/btn_editview.gif" border=0 align=absmiddle hspace=3></a>
<? } ?>
<input type=image src="../img/btn_save.gif" alt="저장하기">
<? if ( $_GET['design_file'] != 'main/index.htm' ){ ?>
<a href="javascript:;" onclick="DCSM.call( 'on' );"><img src="../img/btn_saveas.gif" border=0></a>
<? } ?>
<a href="javascript:file_del('<?=$_GET['design_file']?>');"><img src="../img/btn_del.gif" border=0></a>
</div>


</form>
