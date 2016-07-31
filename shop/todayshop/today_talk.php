<?
require_once('../lib/todayshop_cache.class.php');
$cache = new todayshop_cache();

### 변수할당
$tgsno = $_GET['tgsno'];

include "../_header.php";

$tgsno = $_GET['tgsno'];
$page = (is_numeric($_GET['page']))? $_GET['page'] : 1;

### 회원정보 가져오기
if ($sess){
	$query = "
	SELECT * FROM
		".GD_MEMBER." a
		LEFT JOIN ".GD_MEMBER_GRP." b ON a.level=b.level
	WHERE
		m_no='$sess[m_no]'
	";
	$member = $db->fetch($query,1);
}

// TodayShop class
$todayShop = Core::loader('todayshop');

// 상품토크
$talk = $todayShop->getTalk($member, $tgsno, $page, $todayShop->cfg['talkCnt']);
$talkpager = $todayShop->getTalkPager($tgsno, $page, $todayShop->cfg['talkCnt']);

### 템플릿 출력
$tpl->assign('tgsno', $tgsno);
$tpl->assign('member', $member);
$tpl->assign('talk', $talk);
$tpl->assign('talkpager', $talkpager);
$_html = $tpl->fetch('tpl');

$cache->setCache($_html);
?>
