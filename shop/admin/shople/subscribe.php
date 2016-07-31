<?php
$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';
$location = "쇼플 > 쇼플 플러그 신청";

include "../_header.php";
require_once ('./_inc/config.inc.php');

// 쇼플 판매정보
$shople = Core::loader('shople');
$shopleCfg = $shople->cfg;

$request = $shople->subscribe->result();
?>

<div class="title title_top">쇼플 플러그 입점신청</div>

<?=$request?>


<? include "../_footer.php"; ?>
