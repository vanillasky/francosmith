<?
/*------------------------------------------------------------------------------
ⓒ Copyright 2005, Flyfox All right reserved.
@파일내용: 디자인코디툴 > 레이아웃 > 독립파일
@수정내용/수정자/수정일:
------------------------------------------------------------------------------*/
?>

<div style="padding-top:10;"></div>

<form method="post" name="fm" action="../todayshop/codi/indb.php?mode=save&design_file=<?=$_GET['design_file']?>" onsubmit="return chkForm( this );" enctype="multipart/form-data">
<input type="hidden" name="linkurl" value="<?=$data_file['linkurl']?>">


<?
if ($todayShop->cfg['shopMode'] == "todayshop") {
@include_once dirname(__FILE__) . "/_codi_map.php";
?>
<div style="margin:17px 0;">
<table class=tb>
<tr>
	<td width=50>파일설명</td>
	<td colspan="3"><input type="text" name="text" value="<?=$data_file['text']?>" class="line"></td>
</tr>
<tr>
	<td>전체색상</td>
	<td>
		<div><font class=small1>전체배경색상</font><span style="padding-left:10px"> </span><input type="text" name="outbg_color" value="<?=$data_file['outbg_color']?>" maxlength="6" style="width:58;" class="line"> <a href="javascript:colortable();"><img src="../img/codi/btn_colortable.gif" border="0" alt="색상표 보기" align="absmiddle"></a>
		</div>
		<div><font class=small1>전체배경이미지</font> <input type="file" name="outbg_img_up" size="50" style="width:150" class="line"><input type="hidden" name="outbg_img" value="<?=$data_file['outbg_img']?>">
			<a href="javascript:webftpinfo( '<?=( $data_file['outbg_img'] != '' ? '/data/skin_today/' . $cfg['tplSkinTodayWork'] . '/img/codi/' . $data_file['outbg_img'] : '' )?>' );"><img src="../img/codi/icon_imgview.gif" border="0" alt="이미지 보기" align="absmiddle"></a>
			<? if ( $data_file['outbg_img'] != '' ){ ?>&nbsp;&nbsp;<span class="noline"><input type="checkbox" name="outbg_img_del" value="Y"><font class=small1>삭제</font></span><? } ?>
		</div>
	</td>
	<td>본문색상</td>
	<td>
		<div><font class=small1>배경색상</font><span style="padding-left:10px"> </span><input type="text" name="inbg_color" value="<?=$data_file['inbg_color']?>" maxlength="6" style="width:58;" class="line"> <a href="javascript:colortable();"><img src="../img/codi/btn_colortable.gif" border="0" alt="색상표 보기" align="absmiddle"></a>
		</div>
		<div><font class=small1>배경이미지</font>
			<input type="file" name="inbg_img_up" size="50" style="width:150" class="line"><input type="hidden" name="inbg_img" value="<?=$data_file['inbg_img']?>">
			<a href="javascript:webftpinfo( '<?=( $data_file['inbg_img'] != '' ? '/data/skin_today/' . $cfg['tplSkinTodayWork'] . '/img/codi/' . $data_file['inbg_img'] : '' )?>' );"><img src="../img/codi/icon_imgview.gif" border="0" alt="이미지 보기" align="absmiddle"></a>
			<? if ( $data_file['inbg_img'] != '' ){ ?>&nbsp;&nbsp;<span class="noline"><input type="checkbox" name="inbg_img_del" value="Y"><font class=small1>삭제</font></span><? } ?>
		</div>
	</td>
</tr>
</table>
</div>
<?
}
else {
?>
<div style="margin:17px 0;">
<table class=tb>
<tr>
	<td width=50>파일설명</td>
	<td colspan="3"><input type="text" name="text" value="<?=$data_file['text']?>" class="line"></td>
</tr>
</table>
</div>
<?
}

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