<?php
$location = "��ٿ� �����ǰ > ��ٿ� �귣�� ���ιڽ�";
include "../_header.php";
$requestVar = array(
	'code'=>'marketing_about_brandbox'
);
?>
<div class="title title_top">��ٿ� �귣�� ���ιڽ�<span> </div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<? include "../_footer.php"; ?>