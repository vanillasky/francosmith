<?

$location = "���̹� ���ϸ��� > ���̹� ���ϸ��� �ȳ�/��û";
include "../_header.php";
$requestVar = array(
	'code'=>'marketing_naver_point'
);
?>

<div class="title title_top">���̹� ���ϸ��� �ȳ�/��û</div>

<iframe name="inguide" src="../proc/remote_godopage.php?<?=http_build_query($requestVar)?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="500" scrolling="no"></iframe>

<?include "../_footer.php"; ?>