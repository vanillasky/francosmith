<?

@include "../../conf/partner.php";

$location = "�÷���ġ�� > �÷���ġ�� �ȳ�/��û";
include "../_header.php";

$requestVar = array(
	'code'=>'pluscheese_intro',
	'etc'=>array(
		'shopName'=>$cfg['shopName'],
		'shopUrl'=>$_SERVER['HTTP_HOST'],
	),
);

?>

<div class="title title_top">�÷���ġ�� �Ҽȼ��� �ȳ�/��û</div>

<iframe name='innaver' src='../proc/remote_godopage.php?<?=http_build_query($requestVar)?>' frameborder='0' marginwidth='0' marginheight='0' width='100%' height='2100'></iframe>

<?include "../_footer.php"; ?>