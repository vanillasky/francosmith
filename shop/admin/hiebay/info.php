<?
$location = "����! eBay > ���� �ȳ�";
include "../_header.php";

msg("���񽺰� ����Ǿ����ϴ�.", -1);
exit;

$requestVar = array(
	'code'=>'forseller_intro',
	'etc'=>array(
		'shopName'=>$cfg['shopName'],
		'shopUrl'=>$_SERVER['HTTP_HOST'],
	),
);
?>
<div class="title title_top">���� �ȳ�</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<?include "../_footer.php"; ?>
