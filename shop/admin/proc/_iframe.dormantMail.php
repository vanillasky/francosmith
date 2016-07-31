<?php
include '../lib.php';

ignore_user_abort(true);
set_time_limit(0);
ini_set("memory_limit", -1);

$dormant = Core::loader('dormant');

register_shutdown_function(array($dormant, 'shutdownLog'), 'dormantAuto');

//�޸�ȸ�� ��ȯ 30���� ����� ���� �߼�
$dormantMeilCount = 0;
$dormantMeilCount = $dormant->getDormantMemberCount('dormantMemberAutoMail');
if($dormantMeilCount > 0) {
	$dormant->executeDormantMail();
}
?>