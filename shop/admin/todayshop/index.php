<?php
$location = "투데이샵 > 투데이샵 안내";
include "../_header.php";
$requestVar = array(
	'code'=>'todayshop_intro',
	'etc'=>array(
		'shopName'=>$cfg['shopName'],
		'shopUrl'=>$_SERVER['HTTP_HOST'],
	),
);
?>
<div class="title title_top">투데이샵 안내<span> </div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<? include "../_footer.php"; ?>