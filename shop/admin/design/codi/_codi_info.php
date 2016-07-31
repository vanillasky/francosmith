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
		$real_linkurl = "./../../" . str_replace(".htm", ".php", $_GET['design_file'])."?tplSkin=".$cfg['tplSkinWork'];
		if (preg_match("/goods\/goods_view\.htm/i", $_GET['design_file'])){
			list($goodsno) = $db->fetch("select goodsno from ".GD_GOODS." where open=1 order by -goodsno limit 1");
			$real_linkurl .= "&goodsno=" . $goodsno;
		}
	}
	else if (preg_match("/(\.htm|\.txt)$/i", $_GET['design_file'])){
		$isPreview = true;
		$real_linkurl = "./../../main/html.php?htmid={$_GET['design_file']}&tplSkin=".$cfg['tplSkinWork'];
	}
}
else if ($form_type == 'outSection') {
	@include dirname(__FILE__).'/../../../conf/design_skin_'.$cfg['tplSkinWork'].'.php';
	foreach($design_skin as $key => $val) {
		if ($key == 'default' || preg_match('/^outline\//', $key) || !$val['linkurl']) continue;
		if (!$val['outline_header']) $val['outline_header'] = $design_skin['default']['outline_header'];
		if (!$val['outline_side']) $val['outline_side'] = $design_skin['default']['outline_side'];
		if (!$val['outline_footer']) $val['outline_footer'] = $design_skin['default']['outline_footer'];
		foreach($val as $key2 => $val2) {
			if (!preg_match('/^outline_/', $key2)) continue;
			if ($val2 == $_GET['design_file']) {
				$isPreview = true;
				$real_linkurl = "./../../".$val['linkurl']."?tplSkin=".$cfg['tplSkinWork'];
				break 2;
			}
		}
	}
	unset($design_skin);
}
else if ($form_type == 'outline') {
	@include dirname(__FILE__).'/../../../conf/design_skin_'.$cfg['tplSkinWork'].'.php';
	if ($design_skin[$_GET['design_file']]['linkurl']) {
		$isPreview = true;
		$real_linkurl = "./../../".$design_skin[$_GET['design_file']]['linkurl']."?tplSkin=".$cfg['tplSkinWork'];
	}
	unset($design_skin);
}

?>
<a name="codi_info"></a>
<div style="margin-bottom:5px;">
<input type="text" readonly class="small" style="border:solid 5px #6b86d5; padding:5px 0 5px 13px; width:80%; height:37px; font-size:16pt; font-weight:bold;" value="<?=$fileText?>" />
<? if ($isPreview == true){ ?>
<a href="<?=$real_linkurl;?>" target="_blank"><img src="../img/btn_html_page_view.gif" border=0 align=absmiddle hspace=3></a>
<? } ?>
</div>

<?=gen_design_history_tag('skin', $cfg['tplSkinWork'], $_GET['design_file']); ?>

<script>
// 미리보기
function preview() {
	DCPV.design_preview = window.open('about:blank');
	var fobj = document.fm;
	var ori_action = fobj.action;
	var ori_target = fobj.target;

	try {
		if (DCTM.editor_type == "codemirror" && DCTM.textarea_view_id == DCTM.textarea_merge_body) {
			DCTC.ed1.setValue(DCTC.merge_ed.editor().getValue());
		}
	}
	catch(e) {}

	fobj.action = ori_action + "&gd_preview=1";
	fobj.target = "ifrmHidden";
	fobj.submit();

	fobj.action = ori_action;
	fobj.target = ori_target;
}

// 미리보기 팝업
function preview_popup() {
	var url = "../<?=$real_linkurl?>";
	DCPV.preview_popup(url, "<?=$_GET['design_file']?>");
}

</script>
