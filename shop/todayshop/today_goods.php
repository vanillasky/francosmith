<?
require_once('../lib/todayshop_cache.class.php');
$cache = new todayshop_cache();

### �����Ҵ�
$tgsno = $_GET['tgsno'];

include "../_header.php";

include "../conf/config.pay.php";
@include "../conf/coupon.php";
require "../lib/load.class.php";

function getTimeArray($str) {
	$rtn['YY'] = substr($str, 0, 4);
	$rtn['MM'] = substr($str, 5, 2);
	$rtn['DD'] = substr($str, 8, 2);
	$rtn['hh'] = substr($str, 11, 2);
	$rtn['mm'] = substr($str, 14, 2);
	$rtn['ss'] = substr($str, 17, 2);

	return $rtn;
}

// TodayShop class
$todayShop = Core::loader('todayshop');
$tsCfg = $todayShop->cfg;

### ��ǰ ����Ÿ
if (!$tgsno) {
	// ������ ��ǰ(��)
	$todayGoods = $todayShop->getGoodsByDate(); // ���� �Ǹ����� ��ǰ�� ������.
	if (!is_array($todayGoods) || empty($todayGoods)) $tgsno = -1; // �Ǹ����� ��ǰ�� ���� ���
	else {
		header('location:today_goods.php?'.$_SERVER['QUERY_STRING'].'&tgsno='.$todayGoods[0]['tgsno']);
		exit;
	}

	unset($todayGoods);
}

if ($tgsno == -1) { // ��ǰ�� ���� ���.
	$data['tgsno'] = -1;
	$data['r_img'][] = '../skin_today/'.$cfg['tplSkinToday'].'/img/main_closed.gif';
	$data['showpercent'] = 'y';
	$data['smsCnt'] = 0;
}
else { // ��ǰ�� ���� ���.
	$data = $todayShop->getGoods($tgsno);
	
	if (!is_array($data) || empty($data)) {
		msg("�߸��� ��ǰ��ȣ�Դϴ�.",-1);
		exit;
	}

	$goodsno = $data['goodsno'];
	$stime = getTimeArray($data['startdt']);
	$etime = getTimeArray($data['enddt']);
	$start_time = mktime($stime['hh'], $stime['mm'], $stime['ss'], $stime['MM'], $stime['DD'], $stime['YY']);
	$end_time = mktime($etime['hh'], $etime['mm'], $etime['ss'], $etime['MM'], $etime['DD'], $etime['YY']);
	unset($stime, $etime);

	### ��ǰ ���� ���� üũ
	if (!$data['visible']) {
		msg("�ش��ǰ�� ������ ���� ��ǰ�� �ƴմϴ�",-1);
		exit;
	}

	### ���̹� ���ļ��� ������ ��Ű����
	if($_GET[nv_pchs]){
		SetCookie("nv_pchs",$_GET[nv_pchs],time()+86400*30,"/" );	# ��ȿ�Ⱓ 30��
	}

	###  ���� ���¼��� ������ ��Ű����
	if($_GET['clickid']){
		SetCookie("aos_clickid",$_GET['clickid'],time()+86400,"/" ); # ��ȿ�Ⱓ 1��
	}

	list( $data[brand] ) = $db->fetch("select brandnm from ".GD_GOODS_BRAND." where sno='$data[brandno]'");

	### �߰����� ����
	$data[ex_title] = explode("|",$data[ex_title]);
	foreach ($data[ex_title] as $k=>$v) $data[ex][$v] = $data["ex".($k+1)];
	$data[ex] = array_notnull($data[ex]);

	### ������
	$data[icon] = setIcon($data[icon],$data[regdt]);

	### �̹��� �迭
	$data[r_img] = explode("|",$data[img_m]);
	$data[t_img] = array_map("toThumb",$data[r_img]);

	if ($start_time > time()) {
		msg("�ش� ��ǰ�� ���� �ǸűⰣ�� �ƴմϴ�.", -1);
		exit;
	}

	// �⺻ ���ż���
	$data['default_ea'] = ($data['min_ea'] != 0)? $data['min_ea'] : 1;

	// ���� ��¥
	$today_dt = date('Ymd');
	for($i = 0; $i < strlen($today_dt); $i++) {
		$data['today_dt'][] = substr($today_dt, $i, 1);
	}

	// ��¥ ����
	$data['opendt'] = (string)$data['startdt']; // ����Ⱓ ��¿� ���� ���۽ð�
	if (substr($data['startdt'], 0, 10) != substr($data['enddt'], 0, 10)) $data['closedt'] = (string)$data['enddt']; // ����Ⱓ ��¿� ���� ����ð�
	$data['startdt'] = explode(' ', $data['startdt']); // Ÿ�̸ӿ� ���� ���۽ð�
	$data['enddt'] = explode(' ', $data['enddt']); // Ÿ�̸ӿ� ���� ����ð�

	// ���� ��ǰ�� ī�װ�
	$data['category'] = $todayShop->getCurCategory($tgsno);

	### ace ī����
	$Acecounter->goods_view($goodsno,$data[goodsnm],$data[price],$_GET[category]);
	if($Acecounter->scripts){
		$systemHeadTagEnd .= $Acecounter->scripts;
		$tpl->assign('systemHeadTagEnd',$systemHeadTagEnd);
	}

	### �߰��ɼ�
	$r_addoptnm = explode("|",$data[addoptnm]);
	for ($i=0;$i<count($r_addoptnm);$i++) list ($addoptnm[],$addoptreq[]) = explode("^",$r_addoptnm[$i]);
	$query = "select * from ".GD_GOODS_ADD." where goodsno='$goodsno' order by sno";
	$res = $db->query($query);
	while ($tmp=$db->fetch($res,1)) $addopt[$addoptnm[$tmp[step]]][] = $tmp;

	### ��Ÿ����
	if($data[meta_title]) $meta_title = $cfg[shopName] ." ". strip_tags($data[goodsnm]);
	$meta_keywords = $data[keyword];

	// SMS �߼۽� �ʿ��� ����Ʈ ����
	$smsMsg = $todayShop->makeSmsMsg($data['sms']);
	$data['smsCnt'] = count($smsMsg);
	unset($smsMsg);

	/*
	ĳ�ñ���� �������� ���� �Ʒ��� ������ �ҷ� ���̴� indb.pageinit.php ������ ���ʿ� �ϳ�,
	�� ��Ų���� ����ϹǷ� �������� �ƴ� ��
	[optno] => 673
	[opt1] => 240
	[opt2] =>
	[price] => 2000
	[stock] => 11
	*/
	$ext_data = $todayShop->getGoodsSummary($tgsno);

	$data[totstock] = 0;

	if (is_array($ext_data) && empty($ext_data) === false) {
		$option = array();

		foreach($ext_data as $val) {
			$option[] = $val;

			if (!isset($data[price])) $data[price] = $val[price];
			if (!isset($data[consumer])) $data[consumer] = $val[consumer];

			$data[totstock] = (int)$data[totstock] + (int)$val[stock];

		}

		$sms = Core::loader('sms');
		$smsCnt = preg_replace('/[^0-9-]*/', '', $sms->smsPt);
		unset($sms);

		$data['smsCnt'] = $smsCnt;
		$data['useSMS'] = $todayShop->cfg['useSMS'];
		$data['useEncor'] = $todayShop->cfg['useEncor'];
		$data['useGoodsTalk'] = $todayShop->cfg['useGoodsTalk'];
	}
	else {
		$data = array_merge($data,$result);
		$data['smsCnt'] = 0;
		$data['useSMS'] = 'n';
		$data['useEncor'] = 'n';
		$data['useGoodsTalk'] = 'n';
	}
}

### �����ڼ�
$data[buyercnt] = $data[buyercnt] + $data[fakestock];

### ������
$data['dc_rate'] = 100 - ceil($data['price'] * 100 / $data['consumer']);

// SNS POST
$args = array('shopnm'=>$cfg['shopName'],
				'goodsnm'=>$data['goodsnm'],
				'goodsurl'=>'http://'.$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/todayshop/today_goods.php?tgsno='.$tgsno,
				'img'=>$data['img_s']);
$data['snspost'] = $todayShop->getSnsPostBtn($args);
$customHeader .= $data['snspost']['meta']; // ���̽��Ͽ� ���� meta tag

// ��ٱ��� ��� (�ǹ� & ��ù߼� ��ǰ �϶���
$data['use_cart'] = ($data['goodstype'] == 'goods' && $data['processtype'] == 'i' ) ? 'y' : 'n';

// ���� ����
$subscribe = unserialize(stripslashes($todayShop->cfg['subscribe']));
$interest = unserialize(stripslashes($todayShop->cfg['interest']));

### ���ø� ���
$tpl->assign($data);
$tpl->assign('customHeader', $customHeader);
$tpl->assign('ts_category_all', $ts_category_all);	// ���� _header.php ���� �ҷ��ɴϴ�.

$_html = $tpl->fetch('tpl');

$cache->setCache($_html);
?>
