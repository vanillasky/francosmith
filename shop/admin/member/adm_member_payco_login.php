<?php

$location = 'ȸ������ > ������ ���̵� �α��� �ȳ�';

include '../_header.php';
$requestVar = array(
	'code'=>'service_payco_login'
);
?>

<div class="title title_top">������ ���̵� �α��� �ȳ�</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>

<?php include '../_footer.php'; ?>