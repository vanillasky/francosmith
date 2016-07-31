<?php
$location = "입점대행 서비스 > 마케팅 입점대행 안내/신청";
include "../_header.php";
$requestVar = array(
	'code'=>'marketing_naver_pass'
);
?>
<div class="title title_top">마케팅 입점대행 안내/신청</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<? include "../_footer.php"; ?>