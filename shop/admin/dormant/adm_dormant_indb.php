<?php
include '../lib.php';
$dormant = Core::loader("dormant");

//개인정보 유효기간제 설정 체크
if($dormant->checkDormantAgree() === false){
	msg("개인정보 유효기간제 설정 후 이용가능합니다.", "../basic/adm_basic_dormantConfig.php");
	exit;
}

$executeResult = false;
switch($_POST['mode']){
	//휴면회원 해제
	case 'dormantRestoreAdmin':
		register_shutdown_function(array($dormant, 'shutdownLog'), 'dormantRestoreAdmin');

		try {
			$executeResult = $dormant->executeMemberRestoreAdmin($_POST['chk']);
			if($executeResult === false){
				throw new Exception("휴면회원해제를 실패하였습니다.\n잠시 후 다시 한번 시도하여 주세요.");
			}

			msg("정상적으로 해제되었습니다.");
			popupReload();
		}
		catch(Exception $e){
			msg($e->getMessage(), -1);
		}
	break;

	//회원 삭제 - 휴면 회원리스트
	case 'dormantMemberDelete':
		register_shutdown_function(array($dormant, 'shutdownLog'), 'dormantMemberDelete');

		try {
			$executeResult = $dormant->executeMemberDelete('dormantMemberDelete', $_POST['chk']);
			if($executeResult === false){
				throw new Exception("회원삭제를 실패하였습니다.\n잠시 후 다시 한번 시도하여 주세요.");
			}

			msg("정상적으로 삭제되었습니다.");
			popupReload();
		}
		catch(Exception $e){
			msg($e->getMessage(), -1);
		}
	break;

	//회원 삭제 - 휴면 전환 예정 회원리스트
	case 'dormantMemberToBeDelete':
		register_shutdown_function(array($dormant, 'shutdownLog'), 'dormantMemberToBeDelete');

		try {
			$executeResult = $dormant->executeMemberDelete('dormantMemberToBeDelete', $_POST['chk']);
			if($executeResult === false){
				throw new Exception("회원삭제를 실패하였습니다.\n잠시 후 다시 한번 시도하여 주세요.");
			}

			msg("정상적으로 삭제되었습니다.");
			popupReload();
		}
		catch(Exception $e){
			msg($e->getMessage(), -1);
		}
	break;

	//회원정보 전체 삭제
	case 'dormantMemberDeleteAll':
		register_shutdown_function(array($dormant, 'shutdownLog'), 'dormantMemberDeleteAll');

		try {
			$executeResult = $dormant->executeMemberDelete('dormantMemberDeleteAll');
			if($executeResult === false){
				throw new Exception("회원전체삭제를 실패하였습니다.\n잠시 후 다시 한번 시도하여 주세요.");
			}

			msg("정상적으로 삭제되었습니다.");
			popupReload();
		}
		catch(Exception $e){
			msg($e->getMessage(), -1);
		}
	break;

	//휴면회원 전환
	case 'dormantAdmin':
		register_shutdown_function(array($dormant, 'shutdownLog'), 'dormantAdmin');

		try {
			$executeResult = $dormant->executeDormantAdmin($_POST['chk']);
			if($executeResult === false){
				throw new Exception("휴면회원 전환을 실패하였습니다.\n잠시 후 다시 한번 시도하여 주세요.");
			}

			msg("정상적으로 전환되었습니다.");
			popupReload();
		}
		catch(Exception $e){
			msg($e->getMessage(), -1);
		}
	break;
}
?>