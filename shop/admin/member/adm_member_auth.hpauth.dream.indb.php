<?php

// C1. ���̺귯�� ��Ŭ���
include dirname(__FILE__).'/../lib.php';

// C2. ���� �� ���ε�
$hpauth = Core::loader('Hpauth');
$dreamsecurity = Core::loader('Dreamsecurity');

// C3. ��������
$cpid		= $_POST['cpid'];
$useyn		= $_POST['useyn'];
$modyn		= $_POST['modyn'];
$moduseyn	= $_POST['moduseyn'];
$minoryn	= $_POST['minoryn'];

// C4. ȸ���� cpid Ȯ��
if (strlen(trim($cpid)) < 1) {
	msg('ȸ���� Code�� �Էµ��� �ʾҽ��ϴ�.', -1);
	exit;
}

// C5. ��뿩�� Ȯ��
else if (strlen(trim($useyn)) < 1) {
	msg('��뿩�ΰ� ���õ��� �ʾҽ��ϴ�.', -1);
	exit;
}

// C6. �������� ��뿩�� Ȯ��
else if (strlen(trim($useyn)) < 1) {
	msg('�������� ��뿩�ΰ� ���õ��� �ʾҽ��ϴ�.', -1);
	exit;
}

// C7. ���񽺾��̵� prefix Ȯ��
else if ($dreamsecurity->checkPrefix($cpid) !== true) {
	msg('ȸ���� �ڵ尡 ��Ȯ���� �ʽ��ϴ�. �߱޹��� �ڵ带 Ȯ���ϼ���.', -1);
	exit;
}

// C8. ���� ����
else {
	$hpauth->saveServiceConfig('dream', array(
	    'cpid' => $cpid,
	    'useyn' => $useyn,
		'modyn' => $modyn,
		'moduseyn' => $moduseyn,
	    'minoryn' => $minoryn
	));
	$dreamsecurity->saveConfig($cpid);
}

?>
<script type="text/javascript">
alert("���������� ����Ǿ����ϴ�.");
<?php if ($useyn === 'y') { ?>
parent.window.setHpauth('dream');
<?php } ?>
location.replace('./adm_member_auth.hpauth.dream.php');
</script>