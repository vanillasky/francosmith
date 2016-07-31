<?php

// 설정 및 모듈로드
include dirname(__FILE__).'/../lib.php';
include dirname(__FILE__).'/../../lib/SocialMember/SocialMemberServiceLoader.php';

// 변수설정
$useyn = $_POST['useyn'];
$useAdvanced = $_POST['useAdvanced'];
$appID = $_POST['appID'];
$appSecretCode = $_POST['appSecretCode'];

// 사용 여부 확인
if (strlen(trim($useyn)) < 1) {
	$useyn = 'n';
}

// 설정 방법 확인
if (strlen(trim($useAdvanced)) < 1) {
	$useAdvanced = 'n';
}

if ($useAdvanced === 'y') {
	if (strlen(trim($appID)) < 1) {
		msg('앱(Client) ID를 입력해 주시기 바랍니다.', './adm_member_social_login.facebook.php');
	}
	if (strlen(trim($appSecretCode)) < 1) {
		msg('앱(Client) 시크릿 코드를 입력해 주시기 바랍니다.', './adm_member_social_login.facebook.php');
	}
}

// 설정 저장
$socialMemberService->saveFacebookConfig(array(
    'useyn' => $useyn,
    'useAdvanced' => $useAdvanced,
    'appID' => $appID,
    'appSecretCode' => $appSecretCode,
));

?>
<script type="text/javascript">
alert("정상적으로 저장되었습니다.");
<?php if ($useyn === 'y') { ?>
parent.window.enableService('facebook');
<?php } else { ?>
parent.window.disableService('facebook');
<?php } ?>
location.replace('./adm_member_social_login.facebook.php');
</script>