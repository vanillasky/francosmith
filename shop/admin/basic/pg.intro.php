<?

$location = "결제모듈연동 > 전자결제서비스 안내【필독】";
include "../_header.php";
$requestVar = array(
	'code' => 'service_pg_info'
);

?>
<div class="title title_top">전자지불서비스 안내 [필독]<span>LG U+, 삼성올앳, KCP, 이니시스와 제휴를 맺고 전자결제서비스를 제공해 드립니다.</span></div>


<iframe name="inguide" src="../proc/remote_godopage.php?<?php echo http_build_query($requestVar); ?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>


<? include "../_footer.php"; ?>