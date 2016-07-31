<?php
$location = "배너광고 > 리얼클릭";
include "../_header.php";
$requestVar = array(
	'code'=>'marketing_advertise_realc'
);
?>
<div class="title title_top">리얼클릭</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<? include "../_footer.php"; ?>