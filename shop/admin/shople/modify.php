<?php
$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';
$location = "���� > ���� �����Ǹ� ��û";

include "../_header.php";
require_once ('./_inc/config.inc.php');

// ���� �Ǹ�����
$shople = Core::loader('shople');
$shopleCfg = $shople->cfg;

$request = $shople->subscribe->modify();
?>

<div class="title title_top">���� ���� Ȯ��/����</div>

<?=$request?>

<? include "../_footer.php"; ?>
