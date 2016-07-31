<?php
$location = "¹è³Ê±¤°í > Æ÷ÅÐ ¹è³Ê±¤°í";
include "../_header.php";
$requestVar = array(
	'code'=>'marketing_advertise_portal'
);
?>
<div class="title title_top">Æ÷ÅÐ ¹è³Ê±¤°í</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<? include "../_footer.php"; ?>