<?php
$location = '페이코 > 페이코 서비스 안내';
include '../_header.php';
$requestVar = array(
	'code'=>'service_payco'
);
?>
<div class="title title_top">페이코 서비스 안내 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=33')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<?php include '../_footer.php'; ?>