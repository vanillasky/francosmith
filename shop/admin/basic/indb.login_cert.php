<?
include '../lib.php';

$mode = ($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];
unset($_POST['mode']); unset($_POST['x']); unset($_POST['y']);

$alCert = Core::loader('adminLoginCert');

switch ($mode) {
	case 'setAdminLoginCert': // ���� ����
		$alCert->setAdminLoginCert($_POST);
		break;

	case 'delContact': // OTP ����ó ����
		$alCert->delContact($_POST['chk']);
		msg('������ �Ϸ�Ǿ����ϴ�.', $_SERVER[HTTP_REFERER]);
		break;

	case 'regitContact':  // OTP ����ó ���
		$alCert->regitContact($_POST);
		echo "<script>parent.location.reload();</script>";
		exit;
		break;

	case 'sendRegitOtp': // Regit OTP ����
		$res = $alCert->sendRegitOtp($_POST['mobile'], $_POST['token']);
		exit($res);
		break;

	case 'compareRegitOtp' : // Regit OTP Ȯ��
		$res = $alCert->compareRegitOtp($_POST['otp'], $_POST['token']);
		exit($res);
		break;
}

go($_SERVER[HTTP_REFERER]);

?>