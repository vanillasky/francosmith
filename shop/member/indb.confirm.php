<?
include "../lib/library.php";
include "../conf/config.php";

switch ($_POST['mode']) {
	case 'confirmPassword':
		if ($_POST['confirm_password']) {
			$checkQuery = "SELECT COUNT(m_id) FROM ".GD_MEMBER." WHERE m_id = '".$sess['m_id']."' AND password in (PASSWORD('".$_POST['confirm_password']."'),OLD_PASSWORD('".$_POST['confirm_password']."'),MD5('".$_POST['confirm_password']."'))";
			list($_SESSION['sess']['confirm_password']) = $db->fetch($checkQuery);
		}

		if (!$_SESSION['sess']['confirm_password'])
			msg('비밀번호를 정확하게 입력해 주세요.');
		break;
}

go($_SERVER['HTTP_REFERER']);
?>
