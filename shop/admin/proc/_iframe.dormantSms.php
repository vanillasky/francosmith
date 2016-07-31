<?php
include '../lib.php';

ignore_user_abort(true);
set_time_limit(0);
ini_set("memory_limit", -1);

$dormant = Core::loader('dormant');

register_shutdown_function(array($dormant, 'shutdownLog'), 'dormantAutoSms');

//�޸�ȸ�� ��ȯ SMS �߼�
$dormantSmsCount = 0;
$dormantSmsCount = $dormant->getDormantMemberCount('dormantMemberAutoSms_30');
if($dormantSmsCount < 1){
	$dormantSmsCount = $dormant->getDormantMemberCount('dormantMemberAutoSms_7');
}

if($dormantSmsCount > 0) {
	$dormant->executeDormantSms();
}
?>