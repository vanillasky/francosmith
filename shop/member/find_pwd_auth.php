<?
include "../_header.php";

if ($sess) {
	msg("고객님은 로그인 중입니다.", '../', 'parent');
}


error_reporting(0);

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

switch ((int) $_GET['type']) {
	case 1: // 이메일 인증
		$_tpl = 'auth_by_mb_email.htm';
		// 13번 템플릿 authNum
		break;
	case 2: // 휴대폰 인증
		$_tpl = 'auth_by_mb_mobile.htm';
		break;

	case 3: // 나신평 휴대폰 인증
		$_tpl = 'auth_by_mobile.htm';
		break;
	case 4: // 나신평 범용 공인인증서 인증
		$_tpl = 'auth_by_certificate.htm';
		break;
	default:
		echo '<script>top.location.reload();</script>';
		exit;
		break;
}

$tpl->define(array('tpl'=>'member/'.$_tpl));

$tpl->assign('token', $_POST['token']);
$tpl->assign('m_id', $_POST['m_id']);

### 템플릿 출력
$tpl->print_('tpl');

?>
