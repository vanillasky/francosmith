<?php
$location = "어바웃 광고상품 > 어바웃 브랜드 쇼핑박스";
include "../_header.php";
$requestVar = array(
	'code'=>'marketing_about_brandbox'
);
?>
<div class="title title_top">어바웃 브랜드 쇼핑박스<span> </div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<? include "../_footer.php"; ?>