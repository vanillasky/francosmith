<?php
$location = "투데이샵 > 투데이샵 전자결제 안내/신청";
include "../_header.php";

$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg(' 서비스 신청안내는 고도몰 고객센터로 문의해주시기 바랍니다.', -1);
}

$requestVar = array(
	'code'=>'todayshop_pg_intro',
	'etc'=>array(
		'shopName'=>$cfg['shopName'],
		'shopUrl'=>$_SERVER['HTTP_HOST'],
	),
);
?>

<div class="title title_top">투데이샵 전자결제 안내</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="2000" scrolling="no"></iframe>
<? include "../_footer.php"; ?>