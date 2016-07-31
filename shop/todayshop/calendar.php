<?
require_once('../lib/todayshop_cache.class.php');
$cache = new todayshop_cache();

include "../_header.php";

### 변수할당
$year = (int)$_GET['year'];
$month = (int)$_GET['month'];

$now = getdate();
$curyear = $now['year'];
$curmonth = $now['mon'];
unset($now);
if (!$year || !$month) {
	$year = $curyear;
	$month = $curmonth;
}

// genarate file이 있을 경우
$strmonth = ($month > 10)? $month : '0'.$month;
$level = '_L'.(($sess['level'])?$sess['level']:0);

$todayshop = Core::loader('todayshop');

// 달력정보
$calendar = $todayshop->getCalendar($year, $month, 'cal');

// 날짜별 상품정보
$dateData = $todayshop->getGoodsByMonth($year, $month);

// 달력 설정(전월, 익월)
$monthnavi = $todayshop->getMonthNavi($year, $month);

// 구독 관련
$subscribe = unserialize(stripslashes($todayShop->cfg['subscribe']));
$interest = unserialize(stripslashes($todayShop->cfg['interest']));

// 템플릿 출력
$tpl->assign('monthnavi', $monthnavi);
$tpl->assign('calendar', $calendar);
$tpl->assign('dateData', $dateData);

$_html = $tpl->fetch('tpl');

$cache->setCache($_html);
?>
