<?
$location = "하이! eBay > 번역 서비스 안내";
include "../_header.php";
include_once "./checker.php";

$requestVar = array(
	'code'=>'forseller_translate_intro',
	'etc'=>array(
		'shopName'=>$cfg['shopName'],
		'shopUrl'=>$_SERVER['HTTP_HOST'],
	),
);
?>
<div class="title title_top">번역 서비스 안내</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<?include "../_footer.php"; ?>
