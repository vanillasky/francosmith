<?
require_once('../lib/todayshop_cache.class.php');
$cache = new todayshop_cache();

### �����Ҵ�
$tgsno = $_GET['tgsno'];

include "../_header.php";

$tgsno = $_GET['tgsno'];
$page = (is_numeric($_GET['page']))? $_GET['page'] : 1;

### ȸ������ ��������
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

// ��ǰ��ũ
$talk = $todayShop->getTalk($member, $tgsno, $page, $todayShop->cfg['talkCnt']);
$talkpager = $todayShop->getTalkPager($tgsno, $page, $todayShop->cfg['talkCnt']);

### ���ø� ���
$tpl->assign('tgsno', $tgsno);
$tpl->assign('member', $member);
$tpl->assign('talk', $talk);
$tpl->assign('talkpager', $talkpager);
$_html = $tpl->fetch('tpl');

$cache->setCache($_html);
?>
