<?php
$location = "옥션 iPay 결제 > 옥션 iPay 결제 서비스 안내";
include "../_header.php";
$requestVar = array(
	'code'=>'marketing_auction_ipay'
);
?>
<div class="title title_top">옥션 iPay 결제 서비스 안내</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<? include "../_footer.php"; ?>