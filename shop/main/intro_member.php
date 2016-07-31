<?
include "../_header.php";
include "../conf/fieldset.php";

### 회원인증여부
if( $sess ){
	msg("고객님은 로그인 중입니다.", -1 );
}

if (!$_GET['returnUrl']) $returnUrl = 'http://'.$_SERVER['HTTP_HOST'];
else $returnUrl = $_GET['returnUrl'];

$loginActionUrl = "../member/login_ok.php";

$tpl->assign($_POST);
$tpl->assign('realnameyn', (empty($realname[id]) ? 'n' : empty($realname[useyn])? 'n': $realname[useyn] ));
$tpl->assign('ipinyn', (empty($ipin[id]) ? 'n' : empty($ipin[useyn])? 'n': $ipin[useyn]));
$tpl->assign('shopName', $cfg['shopName']);

$tpl->print_('tpl');
?>