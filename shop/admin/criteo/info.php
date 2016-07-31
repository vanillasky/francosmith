<?
$location = "리타게팅 광고 > 리타게팅 광고 안내";
include "../_header.php";
$requestVar = array(
	'code'=>'marketing_criteo'
);
?>
<div class="title title_top">리타게팅 광고 안내</div>

<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>

<?include "../_footer.php"; ?>