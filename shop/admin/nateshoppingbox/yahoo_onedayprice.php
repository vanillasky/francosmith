<?
$location = "����Ʈ + ���ļ��� > ���� �Ϸ�Ư�� ��Ű��";
include "../_header.php";
$requestVar = array(
	'code'=>'marketing_yahoo_onedayprice'
);
?>
<div class="title title_top">���� �Ϸ�Ư�� ��Ű��</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<? include "../_footer.php"; ?>