<?php

// 설정 및 모듈로드
include dirname(__FILE__).'/../lib.php';
include dirname(__FILE__).'/../../lib/SocialMember/SocialMemberServiceLoader.php';

switch($_POST['mode']) {
	case "getServiceCode":
		$socialMember = SocialMemberService::getMember('PAYCO');
		$responseData = $socialMember->getServiceCode($_POST);

		if ($responseData['status'] == 'FAIL') {
			msg('페이코 로그인 신청을 완료하지 못 하였습니다.\n잠시 후 다시 시도하여 주시기바랍니다.',-1);
			exit;
		} else if ($responseData['status'] == 'REQUIRED NOT') {
			 msg('페이코 로그인 신청을 완료하지 못 하였습니다.\n잠시 후 다시 시도하여 주시기바랍니다.',-1);
			exit;
		} else {
			$useyn = 'y';

			$socialMemberService->savePaycoConfig(array(
				'useyn' => $useyn,
			));
		}
		break;
	case "modifyAppID":
		// 변수설정
		$useyn = $_POST['useyn'];

		// 사용 여부 확인
		if (strlen(trim($useyn)) < 1) {
			$useyn = 'n';
		}

		// 설정 저장
		$socialMember = SocialMemberService::getMember('PAYCO');
		$socialMember->modifyServiceCode($_POST);
		$socialMemberService->savePaycoConfig(array(
			'useyn' => $useyn,
		));
		break;
}
?>
<script type="text/javascript">
alert("정상적으로 저장되었습니다.");

<?php if ($_POST['mode'] == 'getServiceCode') { ?>
parent.location.reload();
<?php } else { ?>
location.replace('./adm_member_social_login.payco.php');
<?php } ?>
</script>