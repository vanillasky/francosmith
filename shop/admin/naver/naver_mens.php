<?
$location = "네이버 지식쇼핑 광고 상품 > 네이버 MEN";
include "../_header.php";
$requestVar = array(
	'code'=>'marketing_naver_mens'
);
?>
<div class="title title_top">네이버 MEN</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<? include "../_footer.php"; ?>