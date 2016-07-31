<?
require_once('../lib/todayshop_cache.class.php');
$cache = new todayshop_cache();

include "../_header.php";

### �����Ҵ�
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

// genarate file�� ���� ���
$strmonth = ($month > 10)? $month : '0'.$month;
$level = '_L'.(($sess['level'])?$sess['level']:0);

$todayshop = Core::loader('todayshop');

// �޷�����
$calendar = $todayshop->getCalendar($year, $month, 'cal');

// ��¥�� ��ǰ����
$dateData = $todayshop->getGoodsByMonth($year, $month);

// �޷� ����(����, �Ϳ�)
$monthnavi = $todayshop->getMonthNavi($year, $month);

// ���� ����
$subscribe = unserialize(stripslashes($todayShop->cfg['subscribe']));
$interest = unserialize(stripslashes($todayShop->cfg['interest']));

// ���ø� ���
$tpl->assign('monthnavi', $monthnavi);
$tpl->assign('calendar', $calendar);
$tpl->assign('dateData', $dateData);

$_html = $tpl->fetch('tpl');

$cache->setCache($_html);
?>
