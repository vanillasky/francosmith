<?php
$location = "SNS ���� > SNS �����ϱ� �ȳ�";
include "../_header.php";
$requestVar = array(
	'code'=>'sns_service_info'
);
?>
<div class="title title_top">SNS �����ϱ� �ȳ�</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="2000" scrolling="no"></iframe>
<? include "../_footer.php"; ?>