<?php
$noleft=true;
$location = "운영지원서비스";
include "../_header.php";
include "../../lib/lib.enc.php";
$blogshop = new blogshop();
$callHeight = 'http://'.$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/admin/proc/_iframeresize.php';
?>
<div style="width:100%;">
<iframe name="wsos" src="http://gongji.godo.co.kr/userinterface/etc/index.php?callHeight=<?=urlencode($callHeight)?>&sno=<?=godoConnEncode($godo['sno'])?>" width="100%" height="2000" frameborder="0"></iframe>
</div>
<? include "../blog/_blog_footer.php"; ?>