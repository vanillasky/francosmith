<?php
$scriptLoad='<link rel="styleSheet" href="./_inc/style.css">';
$location = "쇼플 > 쇼플 제휴판매 신청";

include "../_header.php";
require_once ('./_inc/config.inc.php');

// 쇼플 판매정보
$shople = Core::loader('shople');
$shopleCfg = $shople->cfg;

$request = $shople->subscribe->modify();
?>

<div class="title title_top">입점 정보 확인/수정</div>

<?=$request?>

<? include "../_footer.php"; ?>
