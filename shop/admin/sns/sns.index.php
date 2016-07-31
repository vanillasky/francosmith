<?php
$location = "SNS 서비스 > SNS 공유하기 안내";
include "../_header.php";
$requestVar = array(
	'code'=>'sns_service_info'
);
?>
<div class="title title_top">SNS 공유하기 안내</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="2000" scrolling="no"></iframe>
<? include "../_footer.php"; ?>