<?

include "../_header.php";

### 회원인증여부
if( $sess ){
	msg("고객님은 로그인 중입니다.",$code=-1 );
}

if (!$_GET['returnUrl']) $returnUrl = $_SERVER['HTTP_REFERER'];
else $returnUrl = $_GET['returnUrl'];

### 투데이샵 전용샵 비회원 처리 시작
require "../lib/load.class.php";
// TodayShop class
$todayShop = Core::loader('todayshop');
if ($todayShop->cfg['shopMode'] == 'todayshop') {
	$tpl->assign('guest_disabled', 'y');
}
unset($todayShop);
### 투데이샵 전용샵 비회원 처리 끝

$tpl->print_('tpl');

?>
