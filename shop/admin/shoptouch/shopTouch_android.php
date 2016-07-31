<?php

$location = "쇼핑몰 App관리 > 안드로이드 마켓";
include "../_header.php";

@include_once "../../lib/pAPI.class.php";
$pAPI = new pAPI();

if (!$pAPI->chkExpireDate('android')) {
	msg('서비스 신청후에 사용가능한 메뉴입니다.', -1);
}

?>
<iframe name="inguide" src="http://www.godo.co.kr/userinterface/_shoptouch/service.php?shopsno=<?=$godo['sno']?>&menu=android" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="970px;" scrolling="no"></iframe>
<?include "../_footer.php"; ?>
