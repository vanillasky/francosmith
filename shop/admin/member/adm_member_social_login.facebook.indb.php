<?php

// ���� �� ���ε�
include dirname(__FILE__).'/../lib.php';
include dirname(__FILE__).'/../../lib/SocialMember/SocialMemberServiceLoader.php';

// ��������
$useyn = $_POST['useyn'];
$useAdvanced = $_POST['useAdvanced'];
$appID = $_POST['appID'];
$appSecretCode = $_POST['appSecretCode'];

// ��� ���� Ȯ��
if (strlen(trim($useyn)) < 1) {
	$useyn = 'n';
}

// ���� ��� Ȯ��
if (strlen(trim($useAdvanced)) < 1) {
	$useAdvanced = 'n';
}

if ($useAdvanced === 'y') {
	if (strlen(trim($appID)) < 1) {
		msg('��(Client) ID�� �Է��� �ֽñ� �ٶ��ϴ�.', './adm_member_social_login.facebook.php');
	}
	if (strlen(trim($appSecretCode)) < 1) {
		msg('��(Client) ��ũ�� �ڵ带 �Է��� �ֽñ� �ٶ��ϴ�.', './adm_member_social_login.facebook.php');
	}
}

// ���� ����
$socialMemberService->saveFacebookConfig(array(
    'useyn' => $useyn,
    'useAdvanced' => $useAdvanced,
    'appID' => $appID,
    'appSecretCode' => $appSecretCode,
));

?>
<script type="text/javascript">
alert("���������� ����Ǿ����ϴ�.");
<?php if ($useyn === 'y') { ?>
parent.window.enableService('facebook');
<?php } else { ?>
parent.window.disableService('facebook');
<?php } ?>
location.replace('./adm_member_social_login.facebook.php');
</script>