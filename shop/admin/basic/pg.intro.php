<?

$location = "������⿬�� > ���ڰ������� �ȳ����ʵ���";
include "../_header.php";
$requestVar = array(
	'code' => 'service_pg_info'
);

?>
<div class="title title_top">�������Ҽ��� �ȳ� [�ʵ�]<span>LG U+, �Ｚ�þ�, KCP, �̴Ͻý��� ���޸� �ΰ� ���ڰ������񽺸� ������ �帳�ϴ�.</span></div>


<iframe name="inguide" src="../proc/remote_godopage.php?<?php echo http_build_query($requestVar); ?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>


<? include "../_footer.php"; ?>