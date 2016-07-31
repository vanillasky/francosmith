<?

$location = "자동입금확인 서비스 > 자동입금확인 서비스 안내";
include "../_header.php";
$requestVar = array(
	'code' => 'service_ebank'
);
?>

<div class="title title_top">자동입금확인 서비스 안내 <span>자동입금확인 서비스에 대한 소개 / 특징 / 신청 등을 안내해 드리는 컨텐츠 페이지입니다</span></div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?php echo http_build_query($requestVar); ?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>

<? include "../_footer.php"; ?>