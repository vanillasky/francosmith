<?php
$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';
$location = "���� > ���� �÷��� ��û";

include "../_header.php";
require_once ('./_inc/config.inc.php');

// ���� �Ǹ�����
$shople = Core::loader('shople');
$shopleCfg = $shople->cfg;

$request = $shople->subscribe->result();
?>

<div class="title title_top">���� �÷��� ������û</div>

<?=$request?>


<? include "../_footer.php"; ?>
