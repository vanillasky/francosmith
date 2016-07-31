<?php
@include "../../conf/partner.php";
$location = "장바구니 알림 > 장바구니 알림 안내";
include "../_header.php";
$requestVar = array('code' => 'service_basket');
?>
<div class="title title_top">장바구니 알림 안내</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?php echo http_build_query($requestVar); ?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<? include "../_footer.php"; ?>
