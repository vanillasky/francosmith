<?
include '../../lib/library.php';

error_reporting(0);

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

$alCert = Core::loader('adminLoginCert');

if ($alCert->useLoginCert !== true) { // 관리자보안 인증여부 확인
	exit('9999');
}

switch ($_POST['mode']) {
	case 'sendLoginOtp': // Login OTP 전송
		$res = $alCert->sendLoginOtp($_POST['mobileAocSno'], $_POST['token']);
		exit($res);
		break;

	case 'compareLoginOtp' : // Login OTP 확인
		$res = $alCert->compareLoginOtp($_POST['otp'], $_POST['mobileAocSno'], $_POST['token']);
		exit($res);
		break;
}

?>
9999