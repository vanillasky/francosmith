<?
$location = "����Ʈ + ���ļ��� > ����Ʈ ���ιڽ� 1��";
include "../_header.php";
$requestVar = array(
	'code'=>'marketing_nate_tab1'
);
?>
<div class="title title_top">����Ʈ ���ιڽ� 1��</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<? include "../_footer.php"; ?>