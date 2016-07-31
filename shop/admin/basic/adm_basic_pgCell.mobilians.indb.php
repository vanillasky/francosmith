<?php

// C1. 라이브러리 인클루드
include dirname(__FILE__).'/../lib.php';

// C2. 설정 및 모듈로드
@include dirname(__FILE__).'/../../conf/config.mobileShop.php';
$mobilians = Core::loader('Mobilians');
$danal = Core::loader('Danal');
$shopConfig = Core::loader('config')->load('config');
$paymentConfig = Core::loader('config')->load('configpay');

// C3. 변수설정
$serviceId = $_POST['serviceId'];
$serviceType = $_POST['serviceType'];
$pg_centersetting = $_POST['pg_centersetting'];

// C4. 일반쇼핑몰 휴대폰결제 사용확인
if ($paymentConfig['use']['h'] === 'on') {
	exit('
	<script type="text/javascript">
	var isConfirm = confirm("통합 전자결제 설정의 휴대폰 결제와 모빌리언스 결제를 동시에 사용할 수 없습니다.\r\n모빌리언스 서비스를 이용하시려면 먼저 통합 전자결제 설정 페이지에서 휴대폰 결제를 사용하지 않도록 변경하여주시기 바랍니다.\r\n통합 전자결제 설정 페이지로 이동 하시겠습니까?");
	if (isConfirm) {
		parent.location.replace("'.$shopConfig['rootDir'].'/admin/basic/pg.php");
	}
	</script>
	');
}

// C5. 모바일샵 휴대폰결제 사용확인
if ($paymentConfig['use_mobile']['h'] === 'on') {
	if ($cfgMobileShop['mobileShopRootDir'] !== '/m2') {
		exit('
		<script type="text/javascript">
		var isConfirm = confirm("모바일샵 전자결제 설정의 휴대폰 결제와 모빌리언스 결제를 동시에 사용할 수 없습니다.\r\n모빌리언스 서비스를 이용하시려면 먼저 모바일샵 전자결제 설정 페이지에서 휴대폰 결제를 사용하지 않도록 변경하여주시기 바랍니다.\r\n모바일샵 전자결제 설정 페이지로 이동 하시겠습니까?");
		if (isConfirm) {
			parent.location.replace("'.$shopConfig['rootDir'].'/admin/mobileShop/mobile_pg.php");
		}
		</script>
		');
	}
	else {
		exit('
		<script type="text/javascript">
		var isConfirm = confirm("모바일샵 전자결제 설정의 휴대폰 결제와 모빌리언스 결제를 동시에 사용할 수 없습니다.\r\n모빌리언스 서비스를 이용하시려면 먼저 모바일샵 전자결제 설정 페이지에서 휴대폰 결제를 사용하지 않도록 변경하여주시기 바랍니다.\r\n모바일샵 전자결제 설정 페이지로 이동 하시겠습니까?");
		if (isConfirm) {
			parent.location.replace("'.$shopConfig['rootDir'].'/admin/mobileShop2/mobile_pg.php");
		}
		</script>
		');
	}
}

// C5_1 휴대폰결제 사용확인
if ($danal->isEnabled() === true) {
	msg('현재 다날 휴대폰 결제 서비스를 사용중입니다.\r\n사용중인 휴대폰 결제 서비스를 해제 한 후 다시 설정 해주세요.',-1);
	exit;
}

// C6. 서비스ID 확인
if (strlen(trim($serviceId)) < 1) {
	msg('서비스ID가 입력되지 않았습니다.', -1);
	exit;
}
// C7. 서비스상태 확인
else if (strlen(trim($serviceType)) < 1) {
	msg('서비스환경이 선택되지 않았습니다.', -1);
	exit;
}
// C8. 서비스아이디 prefix 확인
else if ($mobilians->checkPrefix($serviceId) !== true) {
	msg('Prefix 인증에 실패하였습니다.', -1);
	exit;
}
// C9. 설정 저장
else {
	$mobilians->saveConfig($serviceId, $serviceType, $pg_centersetting);
}

?>
<script type="text/javascript">
alert("정상적으로 저장되었습니다.");
parent.location.reload();
</script>