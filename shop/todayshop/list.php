<?
require_once('../lib/todayshop_cache.class.php');
$cache = new todayshop_cache();

include "../_header.php";
include "../lib/page.class.php";

$todayshop = Core::loader('todayshop');

switch ($todayshop->cfg['sortOrder']) {
	case 'open' : { $orderby = 'TG.startdt ASC, TG.tgsno ASC'; break; }
	case 'close' : { $orderby = 'TG.enddt DESC, TG.tgsno DESC'; break; }
	case 'admin' : { $orderby = 'TG.sort, TG.startdt ASC'; break; }
	case 'random' : { $orderby = 'rand()'; break; }

}





$_GET['page'] = isset($_GET['page']) ? $_GET['page'] : 1;
$_GET['page_num'] = isset($_GET['page_num']) ? $_GET['page_num'] : 10;


if ($_GET['keyword'] != '')
	$where[] .= '  TG.goodsnm like \'%'.$_GET['keyword'].'%\'';

if ($paging['category'] != '')
	$where[] .= '  LNK.category = \''.$_GET['category'].'\'';

// 판매중인 상품만.
	$where[] = "(
				(TG.startdt <= NOW() AND TG.enddt > NOW())
				OR
				(TG.startdt IS NULL AND TG.enddt IS NULL)
				)
				";

// 출력 상품만
	$where[] = " TG.visible = 1 ";

$db_table = "
			".GD_TODAYSHOP_GOODS_MERGED." AS TG

			LEFT JOIN ".GD_GOODS_OPTION." AS GO
			ON TG.goodsno = GO.goodsno AND GO.link = 1 and go_is_deleted <> '1' and go_is_display = '1'

			LEFT JOIN ".GD_TODAYSHOP_LINK." AS LNK
			ON TG.tgsno = LNK.tgsno

			LEFT JOIN ".GD_TODAYSHOP_CATEGORY." AS TC
			ON TC.category=LNK.category
";


$pg = new Page($_GET[page],$_GET[page_num]);

$pg->field = "
				DISTINCT TG.tgsno, TG.startdt, TG.enddt,TG.goodsnm, TG.img_i, TG.img_s, TG.sms, TG.limit_ea, TG.showtimer,

				TG.runout, (TG.fakestock + TG.buyercnt) AS buyercnt,

				GO.stock, GO.price, GO.consumer,
				COALESCE(TC.level,0) AS level
";
$pg->setQuery($db_table,$where,$orderby,'');
$pg->exec();

$res = $db->query($pg->query);


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

$sms = Core::loader('sms');

$curTm = time();

while ($data=$db->fetch($res,1)){

	if (empty($data['img_i'])) {
		$img_m = explode('|', $data['img_m']);
		$data['img_i'] = $img_m[0];
	}

	// SNS POST
	$args = array('shopnm'=>$cfg['shopName'],
					'goodsnm'=>$data['goodsnm'],
					'goodsurl'=>'http://'.$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/todayshop/today_goods.php?tgsno='.$data['tgsno'],
					'img'=>$data['img_s']);

	$data['snspost'] = $todayshop->getSnsPostBtn($args);

	$smsMsg = $todayshop->makeSmsMsg($data['sms']);
	$data['smsCnt'] = count($smsMsg);

	$startTm = $data['startdt'] ? strtotime($data['startdt']) : 0;
	$closeTm = $data['enddt'] ? strtotime($data['enddt']) : 0;

	$data['status'] = '';
	$data['remainTm'] = '';
	if ($startTm > 0 && $curTm < $startTm) $data['status'] = 'before'; // 시작시간이 있고, 시작 전이면 status = 'before'
	else if ($closeTm > 0) { // 종료시간이 있으면
		$data['remainTm'] = $closeTm - $curTm;
		if ($data['remainTm'] <= 0) $data['status'] = 'closed'; // 종료 후이면 status = 'closed'
	}
	else $data['status'] = 'noperiod'; // 진행시간이 안정해져있으면 status = 'noperiod'

	$data['status'] = ($data['status'])? $data['status'] : 'ing'; // 현재 판매중이면 status = 'ing'

	if ($data['runout'] == 'y') $data['status'] = 'closed';


	$arRow[] = $data;
}

// 구독 관련
$subscribe = unserialize(stripslashes($todayShop->cfg['subscribe']));
$interest = unserialize(stripslashes($todayShop->cfg['interest']));

$tpl->assign(array(
			'pg'		=> $pg,
			'data'	=> $arRow,
			'category' => $todayshop->getCategory(),
			));




$_html = $tpl->fetch('tpl');

// 캐시
$cache->setCache($_html);
?>
