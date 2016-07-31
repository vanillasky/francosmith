<?php

$location = "QR 코드 관리 > QR 코드 안내";
include "../_header.php";
$requestVar = array(
 'code'=>'service_qrcode'
);
?>

<div class="title title_top">QR 코드 안내</div>

<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>

<? include "../_footer.php"; ?>