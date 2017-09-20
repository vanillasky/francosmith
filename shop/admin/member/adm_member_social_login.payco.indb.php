<?php

// ���� �� ���ε�
include dirname(__FILE__).'/../lib.php';
include dirname(__FILE__).'/../../lib/SocialMember/SocialMemberServiceLoader.php';

switch($_POST['mode']) {
	case "getServiceCode":
		$socialMember = SocialMemberService::getMember('PAYCO');
		$responseData = $socialMember->getServiceCode($_POST);

		if ($responseData['status'] == 'FAIL') {
			msg('������ �α��� ��û�� �Ϸ����� �� �Ͽ����ϴ�.\n��� �� �ٽ� �õ��Ͽ� �ֽñ�ٶ��ϴ�.',-1);
			exit;
		} else if ($responseData['status'] == 'REQUIRED NOT') {
			 msg('������ �α��� ��û�� �Ϸ����� �� �Ͽ����ϴ�.\n��� �� �ٽ� �õ��Ͽ� �ֽñ�ٶ��ϴ�.',-1);
			exit;
		} else {
			$useyn = 'y';

			$socialMemberService->savePaycoConfig(array(
				'useyn' => $useyn,
			));
		}
		break;
	case "modifyAppID":
		// ��������
		$useyn = $_POST['useyn'];

		// ��� ���� Ȯ��
		if (strlen(trim($useyn)) < 1) {
			$useyn = 'n';
		}

		// ���� ����
		$socialMember = SocialMemberService::getMember('PAYCO');
		$socialMember->modifyServiceCode($_POST);
		$socialMemberService->savePaycoConfig(array(
			'useyn' => $useyn,
		));
		break;
}
?>
<script type="text/javascript">
alert("���������� ����Ǿ����ϴ�.");

<?php if ($_POST['mode'] == 'getServiceCode') { ?>
parent.location.reload();
<?php } else { ?>
location.replace('./adm_member_social_login.payco.php');
<?php } ?>
</script>