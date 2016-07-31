<?
include '../lib.php';

$mode = ($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];
unset($_POST['mode']); unset($_POST['x']); unset($_POST['y']);

$alCert = Core::loader('adminLoginCert');

switch ($mode) {
	case 'setAdminLoginCert': // 설정 저장
		$alCert->setAdminLoginCert($_POST);
		break;

	case 'delContact': // OTP 수신처 삭제
		$alCert->delContact($_POST['chk']);
		msg('삭제가 완료되었습니다.', $_SERVER[HTTP_REFERER]);
		break;

	case 'regitContact':  // OTP 수신처 등록
		$alCert->regitContact($_POST);
		echo "<script>parent.location.reload();</script>";
		exit;
		break;

	case 'sendRegitOtp': // Regit OTP 전송
		$res = $alCert->sendRegitOtp($_POST['mobile'], $_POST['token']);
		exit($res);
		break;

	case 'compareRegitOtp' : // Regit OTP 확인
		$res = $alCert->compareRegitOtp($_POST['otp'], $_POST['token']);
		exit($res);
		break;
}

go($_SERVER[HTTP_REFERER]);

?>