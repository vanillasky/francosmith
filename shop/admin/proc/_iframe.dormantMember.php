<?php
include '../lib.php';
include '../../lib/dormant.class.php';

ignore_user_abort(true);
set_time_limit(0);
ini_set("memory_limit", -1);

$dormant = new dormant();

register_shutdown_function(array($dormant, 'shutdownLog'), 'dormantAuto');

//�޸�ȸ�� ��ȯ
$dormantCount = 0;
$dormantCount = $dormant->getDormantMemberCount('dormantMemberAuto');
if($dormantCount > 0) {
	$dormant->executeDormant();
}
?>