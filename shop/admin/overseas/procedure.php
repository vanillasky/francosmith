<?
$location = "�ؿ����� > �ؿܱ��Ŵ��� > ���� �̿� �� ����";
include "../_header.php";

$requestVar = array(
	'code'=>'overseas_procedure',
	'etc'=>array(
		'shopName'=>$cfg['shopName'],
		'shopUrl'=>$_SERVER['HTTP_HOST'],
	),
);

?>

<div class="title title_top">���� �̿� �� ����</div>

<iframe name='innaver' src='../proc/remote_godopage.php?<?=http_build_query($requestVar)?>' frameborder='0' marginwidth='0' marginheight='0' width='100%' height='2100'></iframe>

<?include "../_footer.php"; ?>