<?

include "../_shopTouch_header.php";

### 회원인증여부
if( $sess ){
	$mem_query = $db->_query_print('SELECT name FROM '.GD_MEMBER.' WHERE m_id=[s]', $sess['m_id']);
	$mem_res = $db->_select($mem_query);
	$mem_name = $mem_res[0]['name'];
	msg("고객님은 로그인 중입니다.", "vumall://vercoop.com/login_success?close=true&usr_nm=".urlencode(iconv('euc-kr', 'utf-8', $mem_name)) );
}

if (!$_GET['returnUrl']) $returnUrl = $_SERVER['HTTP_REFERER'];
else $returnUrl = $_GET['returnUrl'];

//if(!$returnUrl) $returnUrl = $mobileRootDir;
$url['join'] = 'http://'.$_SERVER['HTTP_HOST'].'/shop/'.'member/join.php';
$url['id'] = 'http://'.$_SERVER['HTTP_HOST'].'/shop/'.'member/find_id.php';
$url['pwd'] = 'http://'.$_SERVER['HTTP_HOST'].'/shop/'.'member/find_pwd.php';

$tpl->assign('url',$url);
$tpl->print_('tpl');

?>