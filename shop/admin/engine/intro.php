<?

$location = "���ݺ񱳻���Ʈ ���� > ���� �ȳ�";
include "../_header.php";
$requestVar = array(
	'code'=>'marketing_danawa_enuri'
);
?>


<div class="title title_top">���� �ȳ� <span>���ݺ񱳻���Ʈ�� ���� �Ұ� / ���� / ���� ���� �ȳ��� �帮�� ������ �������Դϴ�</span></div>
<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>
<? include "../_footer.php"; ?>