<?php
$location = "ZUM 광고 > ZUM 쇼핑박스";
include "../_header.php";
$requestVar = array(
	'code'=>'marketing_zum_box'
);
?>
<div class="title title_top">ZUM 쇼핑박스</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<? include "../_footer.php"; ?>