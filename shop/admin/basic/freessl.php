<?php
include "../lib.php";
include "../../lib/lib.enc.php";

$id = godoConnEncode($godo[sno]);
$out = readurl("http://gongji.godo.co.kr/userinterface/freessl_request.php?id=$id");

if($out=='request')msg("���� SSL ��û�� �Ϸ� �Ǿ����ϴ�.");
else if($out=='duplication') msg("�̹� ��û�Ǿ����ϴ�. ��ġ�ð��� ��û �� �ִ� �Ϸ� �Դϴ�.");
else msg("���񽺿� ��ְ� �߻��Ͽ����ϴ�. ��� �� �̿����ֽñ� �ٶ��ϴ�.");
?>