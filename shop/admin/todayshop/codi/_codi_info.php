<?
/*------------------------------------------------------------------------------
ⓒ Copyright 2005, Flyfox All right reserved.
@파일내용: 디자인코디툴 > 파일정보
@수정내용/수정자/수정일:
------------------------------------------------------------------------------*/

if( $data_dir['name'] != '' && $data_dir['name'] != '.' ){
	$fileText = "{$data_dir['text']}&nbsp;▶&nbsp;{$data_file['text']}&nbsp;&nbsp;I&nbsp;&nbsp;{$_GET['design_file']}";
} else {
	$fileText = $data_file['text'];
}

### 화면보기
$isPreview = false;
if ($form_type == 'file' || $form_type == 'inc')
{
	$tmp = dirname(__FILE__) . "/../../../" . str_replace(".htm", ".php", $_GET['design_file']);
	if (preg_match("/(board\/|goods\/list\/|goods\/goods_event\.htm|member\/_form\.htm|order\/|_myBox\.htm)/i", $_GET['design_file']));
	else if ( file_exists($tmp) === true ){
		$isPreview = true;
		$real_linkurl = "./../../" . str_replace(".htm", ".php", $_GET['design_file'])."?tplSkinToday=".$cfg['tplSkinTodayWork'];
		if (preg_match("/goods\/goods_view\.htm/i", $_GET['design_file'])){
			list($goodsno) = $db->fetch("select goodsno from ".GD_GOODS." where open=1 order by -goodsno limit 1");
			$real_linkurl .= "&goodsno=" . $goodsno;
		}
	}
	else if (preg_match("/(\.htm|\.txt)$/i", $_GET['design_file'])){
		$isPreview = true;
		$real_linkurl = "./../../main/html.php?htmid={$_GET['design_file']}&tplSkinToday=".$cfg['tplSkinTodayWork'];
	}
}

?>
<a name="codi_info"></a>
<div style="margin-bottom:5px;">
<input type="text" readonly class="small" style="border:solid 5px #6b86d5; padding:5px 0 5px 13px; width:80%; height:37px; font-size:16pt; font-weight:bold;" value="<?=$fileText?>" />
<? if ($isPreview == true){ ?>
<a href="<?=$real_linkurl;?>" target="_blank"><img src="../img/btn_html_page_view.gif" border=0 align=absmiddle hspace=3></a>
<? } ?>
</div>