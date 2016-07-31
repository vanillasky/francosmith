<?php
$location = "택배연동 서비스 > 굿스플로 서비스 신청안내";
include "../_header.php";

$requestVar = array(
	'code'=>'goodsflow_intro',
	'etc'=>array(
		'shopName'=>$cfg['shopName'],
		'shopUrl'=>$_SERVER['HTTP_HOST'],
	),
);
?>
<div class="title title_top">굿스플로 서비스 신청안내<span> </div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<? include "../_footer.php"; ?>