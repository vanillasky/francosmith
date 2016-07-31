<?php
$location = "배너광고 > 브랜드검색";
include "../_header.php";
$requestVar = array(
	'code'=>'marketing_advertise_brand'
);
?>
<div class="title title_top">브랜드검색</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<? include "../_footer.php"; ?>