<?php
include "../lib.php";
include "../../lib/lib.enc.php";

$id = godoConnEncode($godo[sno]);
$out = readurl("http://gongji.godo.co.kr/userinterface/freessl_request.php?id=$id");

if($out=='request')msg("무료 SSL 신청이 완료 되었습니다.");
else if($out=='duplication') msg("이미 신청되었습니다. 설치시간은 요청 후 최대 하루 입니다.");
else msg("서비스에 장애가 발생하였습니다. 잠시 후 이용해주시기 바랍니다.");
?>