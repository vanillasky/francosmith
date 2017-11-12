<?php
set_time_limit(0);

include("../dbconn.php");
include("../lib/lib.func.php");
@include dirname(__FILE__).'/../conf/config.mobileShop.php';
include '../conf/config.pay.php';
@include "../conf/config.php";
@include_once '../conf/coupon.php';
@include_once '../conf/fieldset.php';
@include "../lib/naverPartner.class.php";

$naver = new naverPartner();

header("Cache-Control: no-cache, must-revalidate");
header("Content-Type: text/plain; charset=euc-kr");

$naver->summaryEp();

?>
