<?php
$location = "���θ� App���� > ���þ۽����";
include "../_header.php";

@include_once "../../lib/pAPI.class.php";
$pAPI = new pAPI();

if (!$pAPI->chkExpireDate('apple')) {
	msg('���� ��û�Ŀ� ��밡���� �޴��Դϴ�.', -1);
}

?>
<iframe name="inguide" src="http://www.godo.co.kr/userinterface/_shoptouch/service.php?shopsno=<?=$godo['sno']?>&menu=apple" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="970px;" scrolling="no"></iframe>
<?include "../_footer.php"; ?>
