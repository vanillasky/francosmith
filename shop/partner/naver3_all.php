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

// ������ ������ �ְ� �ڵ����� ��� �������̸� ������ ���� �����ְ� ���ϸ� old�� ����
if ($naver->epFileChk($naver->new_filename) === true && $naver->epAutoUseChk() === true) {
	$naver->epPrint();
}
else {
	$naver->epCreatePrint();
}
?>