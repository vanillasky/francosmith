<?

$location = "�ڵ��Ա�Ȯ�� ���� > �ڵ��Ա�Ȯ�� ���� �ȳ�";
include "../_header.php";
$requestVar = array(
	'code' => 'service_ebank'
);
?>

<div class="title title_top">�ڵ��Ա�Ȯ�� ���� �ȳ� <span>�ڵ��Ա�Ȯ�� ���񽺿� ���� �Ұ� / Ư¡ / ��û ���� �ȳ��� �帮�� ������ �������Դϴ�</span></div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?php echo http_build_query($requestVar); ?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>

<? include "../_footer.php"; ?>