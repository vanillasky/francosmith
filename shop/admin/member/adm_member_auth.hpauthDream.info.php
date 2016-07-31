<?php

$location = '회원관리 > 본인확인 인증서비스 > 휴대폰본인확인서비스 안내';
include dirname(__FILE__).'/../_header.php';

$requestVar = array(
	'code' => 'service_dreamsecurity'
);

?>

<div class="title title_top">휴대폰본인확인서비스 안내</div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?php echo http_build_query($requestVar); ?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>

<?php

include dirname(__FILE__).'/../_footer.php';

?>