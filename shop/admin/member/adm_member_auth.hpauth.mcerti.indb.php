<?php

// C1. 라이브러리 인클루드
include dirname(__FILE__).'/../lib.php';

// C2. 설정 및 모듈로드
$hpauth = Core::loader('Hpauth');

// C3. 변수설정
$useyn		= $_POST['useyn'];
$modyn		= $_POST['modyn'];
$moduseyn	= $_POST['moduseyn'];
$minoryn	= $_POST['minoryn'];

// C4. 사용여부 확인
if (strlen(trim($useyn)) < 1) {
	msg('사용여부가 선택되지 않았습니다.', -1);
	exit;
}

// C5. 성인인증 사용여부 확인
else if (strlen(trim($useyn)) < 1) {
	msg('성인인증 사용여부가 선택되지 않았습니다.', -1);
	exit;
}

// C6. 설정 저장
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
alert("정상적으로 저장되었습니다.");
<?php if ($useyn === 'y') { ?>
parent.window.setHpauth('mcerti');
<?php } ?>
location.replace('./adm_member_auth.hpauth.mcerti.php');
</script>