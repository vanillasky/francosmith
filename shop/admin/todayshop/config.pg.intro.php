<?php
$location = "�����̼� > �����̼� ���ڰ��� �ȳ�/��û";
include "../_header.php";

$todayShop = &load_class('todayshop', 'todayshop');
if (!$todayShop->auth()) {
	msg(' ���� ��û�ȳ��� ���� �����ͷ� �������ֽñ� �ٶ��ϴ�.', -1);
}

$requestVar = array(
	'code'=>'todayshop_pg_intro',
	'etc'=>array(
		'shopName'=>$cfg['shopName'],
		'shopUrl'=>$_SERVER['HTTP_HOST'],
	),
);
?>

<div class="title title_top">�����̼� ���ڰ��� �ȳ�</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="2000" scrolling="no"></iframe>
<? include "../_footer.php"; ?>