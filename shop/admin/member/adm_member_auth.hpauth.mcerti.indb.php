<?php

// C1. ���̺귯�� ��Ŭ���
include dirname(__FILE__).'/../lib.php';

// C2. ���� �� ���ε�
$hpauth = Core::loader('Hpauth');

// C3. ��������
$useyn		= $_POST['useyn'];
$modyn		= $_POST['modyn'];
$moduseyn	= $_POST['moduseyn'];
$minoryn	= $_POST['minoryn'];

// C4. ��뿩�� Ȯ��
if (strlen(trim($useyn)) < 1) {
	msg('��뿩�ΰ� ���õ��� �ʾҽ��ϴ�.', -1);
	exit;
}

// C5. �������� ��뿩�� Ȯ��
else if (strlen(trim($useyn)) < 1) {
	msg('�������� ��뿩�ΰ� ���õ��� �ʾҽ��ϴ�.', -1);
	exit;
}

// C6. ���� ����
else {
	$hpauth->saveServiceConfig('mcerti', array(
	    'useyn' => $useyn,
		'modyn' => $modyn,
		'moduseyn' => $moduseyn,
	    'minoryn' => $minoryn,
	));
}

?>
<script type="text/javascript">
alert("���������� ����Ǿ����ϴ�.");
<?php if ($useyn === 'y') { ?>
parent.window.setHpauth('mcerti');
<?php } ?>
location.replace('./adm_member_auth.hpauth.mcerti.php');
</script>