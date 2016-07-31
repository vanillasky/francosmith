<?
$location = "네이트 + 야후쇼핑 > 야후 쇼핑박스 4탭";
include "../_header.php";
$requestVar = array(
	'code'=>'marketing_yahoo_fashionsoho'
);
?>
<div class="title title_top">야후 쇼핑박스 4탭</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<? include "../_footer.php"; ?>