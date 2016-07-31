<?
$location = "컴백 쿠폰/SMS > 컴백 쿠폰/SMS 안내";
include "../_header.php";
$requestVar = array('code' => 'service_comeback');
?>
<div class="title title_top">컴백 쿠폰/SMS 안내</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?php echo http_build_query($requestVar); ?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<? include "../_footer.php"; ?>