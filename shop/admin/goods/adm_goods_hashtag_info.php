<?php
$location = "��ǰ���� > �ؽ��±� ��?";
include '../_header.php';

$requestVar = array(
	'code'=>'function_hashtag'
);
?>
<div class="title title_top">�ؽ��±� ��?</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<?php include '../_footer.php'; ?>