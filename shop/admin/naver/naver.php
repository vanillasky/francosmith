<?
$location = "���̹� ���ļ��� > ���ļ��� �ȳ�/����";
include "../_header.php";
$requestVar = array(
	'code'=>'marketing_naver_kin'
);
?>
<div class="title title_top">���ļ��� �ȳ�/����<span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<? include "../_footer.php"; ?>