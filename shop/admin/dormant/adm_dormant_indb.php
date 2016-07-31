<?php
include '../lib.php';
$dormant = Core::loader("dormant");

//�������� ��ȿ�Ⱓ�� ���� üũ
if($dormant->checkDormantAgree() === false){
	msg("�������� ��ȿ�Ⱓ�� ���� �� �̿밡���մϴ�.", "../basic/adm_basic_dormantConfig.php");
	exit;
}

$executeResult = false;
switch($_POST['mode']){
	//�޸�ȸ�� ����
	case 'dormantRestoreAdmin':
		register_shutdown_function(array($dormant, 'shutdownLog'), 'dormantRestoreAdmin');

		try {
			$executeResult = $dormant->executeMemberRestoreAdmin($_POST['chk']);
			if($executeResult === false){
				throw new Exception("�޸�ȸ�������� �����Ͽ����ϴ�.\n��� �� �ٽ� �ѹ� �õ��Ͽ� �ּ���.");
			}

			msg("���������� �����Ǿ����ϴ�.");
			popupReload();
		}
		catch(Exception $e){
			msg($e->getMessage(), -1);
		}
	break;

	//ȸ�� ���� - �޸� ȸ������Ʈ
	case 'dormantMemberDelete':
		register_shutdown_function(array($dormant, 'shutdownLog'), 'dormantMemberDelete');

		try {
			$executeResult = $dormant->executeMemberDelete('dormantMemberDelete', $_POST['chk']);
			if($executeResult === false){
				throw new Exception("ȸ�������� �����Ͽ����ϴ�.\n��� �� �ٽ� �ѹ� �õ��Ͽ� �ּ���.");
			}

			msg("���������� �����Ǿ����ϴ�.");
			popupReload();
		}
		catch(Exception $e){
			msg($e->getMessage(), -1);
		}
	break;

	//ȸ�� ���� - �޸� ��ȯ ���� ȸ������Ʈ
	case 'dormantMemberToBeDelete':
		register_shutdown_function(array($dormant, 'shutdownLog'), 'dormantMemberToBeDelete');

		try {
			$executeResult = $dormant->executeMemberDelete('dormantMemberToBeDelete', $_POST['chk']);
			if($executeResult === false){
				throw new Exception("ȸ�������� �����Ͽ����ϴ�.\n��� �� �ٽ� �ѹ� �õ��Ͽ� �ּ���.");
			}

			msg("���������� �����Ǿ����ϴ�.");
			popupReload();
		}
		catch(Exception $e){
			msg($e->getMessage(), -1);
		}
	break;

	//ȸ������ ��ü ����
	case 'dormantMemberDeleteAll':
		register_shutdown_function(array($dormant, 'shutdownLog'), 'dormantMemberDeleteAll');

		try {
			$executeResult = $dormant->executeMemberDelete('dormantMemberDeleteAll');
			if($executeResult === false){
				throw new Exception("ȸ����ü������ �����Ͽ����ϴ�.\n��� �� �ٽ� �ѹ� �õ��Ͽ� �ּ���.");
			}

			msg("���������� �����Ǿ����ϴ�.");
			popupReload();
		}
		catch(Exception $e){
			msg($e->getMessage(), -1);
		}
	break;

	//�޸�ȸ�� ��ȯ
	case 'dormantAdmin':
		register_shutdown_function(array($dormant, 'shutdownLog'), 'dormantAdmin');

		try {
			$executeResult = $dormant->executeDormantAdmin($_POST['chk']);
			if($executeResult === false){
				throw new Exception("�޸�ȸ�� ��ȯ�� �����Ͽ����ϴ�.\n��� �� �ٽ� �ѹ� �õ��Ͽ� �ּ���.");
			}

			msg("���������� ��ȯ�Ǿ����ϴ�.");
			popupReload();
		}
		catch(Exception $e){
			msg($e->getMessage(), -1);
		}
	break;
}
?>