<?php
set_time_limit(0);
@include "../lib/library.php";
@include "../conf/config.php";
@include "../conf/config.pay.php";
@include "../lib/naverPartner.class.php";
@include "../conf/coupon.php";
@include "../conf/config.mobileShop.php";

$naver = new naverPartner();

if($naver->partner['useYn'] != 'y') exit;

header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: text/plain; charset=euc-kr");

// 수집된 파일이 있고 자동생성 사용 설정중이면 수집된 파일 보여주고 파일명 old로 변경
if ($naver->epFileChk($naver->new_filename) === true && $naver->epAutoUseChk() === true) {
	$naver->epPrint();
}
else {
	$naver->epCreatePrint();
}
?>