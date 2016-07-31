<?
$location = "어바웃 > 어바웃 안내/입점";
include "../_header.php";
$requestVar = array(
	'code'=>'marketing_about_reg'
);
?>
<div class="title title_top">어바웃 안내/입점<span> </div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<? include "../_footer.php"; ?>