<?
require_once('../lib/todayshop_cache.class.php');
$cache = new todayshop_cache();

### �����Ҵ�
$tgsno = $_GET['tgsno'];

include "../_header.php";

require "../lib/load.class.php";

// TodayShop class
$todayShop = Core::loader('todayshop');

### ��ǰ ����Ÿ
// ������ ��ǰ(��)
$todayThumb = $todayShop->getGoodsByDate(); // ���� �Ǹ����� ��ǰ�� ������.

### ���ø� ���
$tpl->assign('todayThumb', $todayThumb);
$tpl->assign('customHeader', $customHeader);
$_html = $tpl->fetch('tpl');

$cache->setCache($_html);
?>
