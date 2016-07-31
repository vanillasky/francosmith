<?

include "../lib/library.php";
include '../lib/SocialMember/SocialMemberServiceLoader.php';

$referer = ($_POST[referer]) ? $_POST[referer] : $_GET[referer];

if (SocialMemberService::getPersistentData('social_code')) {
	$session = Core::loader('session');
	$session->socialLogout(SocialMemberService::getPersistentData('social_code'));
}

$_SESSION = array();

session_destroy();
setCookie('Xtime','',0,'/');
setcookie('gd_cart','',time() - 3600,'/');
setcookie('gd_cart_direct','',time() - 3600,'/');

if (!$referer) $referer = ($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : "../main/index.php";
if (strpos($referer,"member/")!==false) $referer = "../main/index.php";
if (strpos($referer,"mypage/")!==false) $referer = "../main/index.php";
if (strpos($referer,"order/")!==false) $referer = "../main/index.php";
go($referer);

?>