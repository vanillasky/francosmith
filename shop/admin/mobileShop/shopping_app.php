<?php

$location = "����ϼ����� > ���θ� ���� �ȳ�";
include "../_header.php";
$requestVar = array(
	'code'=>'mobileshop_app'
);
?>

<div class="title title_top">���θ� ���� �ȳ�</div>

<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>

<? include "../_footer.php"; ?>