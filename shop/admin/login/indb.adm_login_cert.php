<?
include '../../lib/library.php';

error_reporting(0);

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

$alCert = Core::loader('adminLoginCert');

if ($alCert->useLoginCert !== true) { // �����ں��� �������� Ȯ��
	exit('9999');
}

switch ($_POST['mode']) {
	case 'sendLoginOtp': // Login OTP ����
		$res = $alCert->sendLoginOtp($_POST['mobileAocSno'], $_POST['token']);
		exit($res);
		break;

	case 'compareLoginOtp' : // Login OTP Ȯ��
		$res = $alCert->compareLoginOtp($_POST['otp'], $_POST['mobileAocSno'], $_POST['token']);
		exit($res);
		break;
}

?>
9999