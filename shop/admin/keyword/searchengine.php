<?php
$location = "검색 광고 > 검색엔진 등록";
include "../_header.php";
$requestVar = array(
	'code'=>'marketing_searchengine'
);
?>
<div class="title title_top">검색엔진 등록</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<? include "../_footer.php"; ?>