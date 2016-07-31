<?php
$location = 'SNS 서비스 > 인스고위젯 안내';
include '../_header.php';
$requestVar = array(
	'code'=>'function_instar'
);
?>
<div class="title title_top">인스고위젯 안내</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>

<?php include '../_footer.php'; ?>