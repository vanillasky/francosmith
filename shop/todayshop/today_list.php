<?
require_once('../lib/todayshop_cache.class.php');
$cache = new todayshop_cache();

include "../_header.php";

if (!$_GET['category']) {
	msg('카테고리 정보가 없습니다.', -1);
	exit;
}

### 변수할당
$now = getdate();

if ($_GET['year']) $now['year'] = (int)$_GET['year'];
if ($_GET['month']) $now['mon'] = (int)$_GET['month'];
if ($_GET['day']) $now['mday'] = (int)$_GET['day'];

if ($now['mon'] < 10) $now['mon'] = '0'.$now['mon'];
if ($now['mday'] < 10) $now['mday'] = '0'.$now['mday'];


$dt = $now['year'].'-'.$now['mon'].'-'.$now['mday'];

$todayshop = Core::loader('todayshop');

// 오늘의 상품정보
$data = $todayshop->getGoodsByDate($dt, $_GET['category']);

/**
	카테고리내, 상품 갯수
	0 : 메인으로 이동
	1 : 해당 상품 페이지로 이동
	2~: 리스트 출력
 */
$data_size = sizeof($data);
if ($data_size == 0) {
	ob_start();
	echo "
	<script>
		alert('상품 준비중입니다.');
		location.href='../todayshop/today_goods.php';
	</script>
	";
	$_html = ob_get_contents();
	ob_end_clean();
}
elseif ($data_size == 1) {
	ob_start();
	echo "<script>location.href='../todayshop/today_goods.php?tgsno=".$data[0]['tgsno']."&category=".$_GET['category']."';</script>";
	$_html = ob_get_contents();
	ob_end_clean();
}
else {

	if (is_array($data) && empty($data)===false) {
		foreach($data as $key => $val) {
			if (empty($val['img_i'])) {
				$img_m = explode('|', $val['img_m']);
				$data[$key]['img_i'] = $img_m[0];
			}

			// SNS POST
			$args = array('shopnm'=>$cfg['shopName'],
							'goodsnm'=>$val['goodsnm'],
							'goodsurl'=>'http://'.$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/todayshop/today_goods.php?tgsno='.$val['tgsno'],
							'img'=>$val['img_s']);
			$data[$key]['snspost'] = $todayshop->getSnsPostBtn($args);

			$sms = Core::loader('sms');
			$smsMsg = $todayshop->makeSmsMsg($val['sms']);
			$data[$key]['smsCnt'] = count($smsMsg);
			unset($sms, $smsMsg);
		}
	}

	// 날짜 네비게이션
	$datenavi = $todayshop->getDateNavi($now['year'], $now['mon'], $now['mday']);

	// 구독 관련
	$subscribe = unserialize(stripslashes($todayShop->cfg['subscribe']));
	$interest = unserialize(stripslashes($todayShop->cfg['interest']));

	// 템플릿 출력
	$tpl->assign('datenavi', $datenavi);
	$tpl->assign('date', $now);
	$tpl->assign('data', $data);

	$_html = $tpl->fetch('tpl');

}

$cache->setCache($_html);
?>
