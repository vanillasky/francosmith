<?php

$location = "QR �ڵ� ���� > QR �ڵ� �ȳ�";
include "../_header.php";
$requestVar = array(
 'code'=>'service_qrcode'
);
?>

<div class="title title_top">QR �ڵ� �ȳ�</div>

<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>

<? include "../_footer.php"; ?>