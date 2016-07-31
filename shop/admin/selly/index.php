<?
$location = "셀리 > 서비스 안내";
include "../_header.php";

$chkWord = array("rental", "free", "self");
foreach($chkWord as $v) {
	if(preg_match("/$v/", $godo['ecCode'])) {
		$mall_type = $v;
		break;
	}
}

$requestVar = array(
	'code'=>'selly_intro',
	'etc'=>array(
		'shopName'=>$cfg['shopName'],
		'shopUrl'=>$_SERVER['HTTP_HOST'],
	),
	'mall_type'=>$mall_type,
);
?>
<div class="title title_top">서비스 안내</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<?include "../_footer.php"; ?>
