<?
require_once('../lib/todayshop_cache.class.php');
$cache = new todayshop_cache();

### 변수할당
$tgsno = $_GET['tgsno'];

include "../_header.php";

require "../lib/load.class.php";

// TodayShop class
$todayShop = Core::loader('todayshop');

### 상품 데이타
// 오늘의 상품(들)
$todayThumb = $todayShop->getGoodsByDate(); // 현재 판매중인 상품만 가져옴.

### 템플릿 출력
$tpl->assign('todayThumb', $todayThumb);
$tpl->assign('customHeader', $customHeader);
$_html = $tpl->fetch('tpl');

$cache->setCache($_html);
?>
