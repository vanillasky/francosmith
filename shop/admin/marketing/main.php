<?
$location = "마케팅 센터 > 마케팅 메인페이지";
include "../_header.php";
$requestVar = array(
	'code'=>'marketing_main'
);
?>
<div class="title title_top">마케팅 메인페이지</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<? include "../_footer.php"; ?>