<?php
@include "../../conf/partner.php";
$location = "��ٱ��� �˸� > ��ٱ��� �˸� �ȳ�";
include "../_header.php";
$requestVar = array('code' => 'service_basket');
?>
<div class="title title_top">��ٱ��� �˸� �ȳ�</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?php echo http_build_query($requestVar); ?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<? include "../_footer.php"; ?>
