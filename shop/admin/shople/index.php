<?php
$location = "쇼플 > 쇼플 플러그 안내";

include "../_header.php";
require_once ('./_inc/config.inc.php');

$requestVar = array(
	'code'=>'shoppingtong_info',
	'etc'=>array(
		'shopName'=>$cfg['shopName'],
		'shopUrl'=>$_SERVER['HTTP_HOST'],
	),
);
?>
<script type="text/javascript">
function gd_IframeResize(el) {
	el.style.height = el.contentWindow.document.body.scrollHeight + "px";
}
</script>
<div class="title title_top">쇼플 플러그 안내</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no" onLoad="gd_IframeResize(this);"></iframe>
<? include "../_footer.php"; ?>