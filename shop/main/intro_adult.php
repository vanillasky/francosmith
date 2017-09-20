<?
include "../_header.php";
include "../conf/fieldset.php";

$hpauth = Core::loader('Hpauth');
$hpauthRequestData = $hpauth->getAdultRequestData();

if( Clib_Application::session()->isAdult() ){
	msg("������ �̹� �������� �ϼ̽��ϴ�.", -1 );
}

if ($socialMemberService->isEnabled()) {
	$enabledSocialMemberServiceList = $socialMemberService->getEnabledServiceList();
	if (in_array(SocialMemberService::PAYCO, $enabledSocialMemberServiceList)) {
		$paycoMember = SocialMemberService::getMember(SocialMemberService::PAYCO);
		$tpl->assign('PaycoLoginURL', $paycoMember->getLoginURL());
	}
}

if (!$_GET['returnUrl']) $returnUrl = 'http://'.$_SERVER['HTTP_HOST'];
else $returnUrl = $_GET['returnUrl'];

$loginActionUrl = "../member/login_ok.php";

$tpl->assign($_POST);
$tpl->assign('realnameyn', (empty($realname[id]) ? 'n' : empty($realname[useyn])? 'n': $realname[useyn]));
$tpl->assign('ipinyn', (empty($ipin[id]) ? 'n' : empty($ipin[useyn])? 'n': $ipin[useyn]));
$tpl->assign('niceipinyn', ($ipin[nice_useyn] == 'y' && $ipin[nice_minoryn] == 'y') ? 'y' : 'n');
$tpl->assign('hpauthDreamyn', $hpauthRequestData['useyn']);
$tpl->assign('hpauthDreamcpid', $hpauthRequestData['cpid']);
$tpl->assign('shopName', $cfg['shopName']);
$tpl->define('intro_auth', 'proc/intro_auth.htm');
$tpl->define('intro_auth_login', 'proc/intro_auth_login.htm');

$tpl->print_('tpl');
?>
