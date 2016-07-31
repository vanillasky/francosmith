<?
require_once('../lib/todayshop_cache.class.php');
$cache = new todayshop_cache();

### 변수할당
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

### 상품 데이타
if (!$tgsno) {
	// 오늘의 상품(들)
	$todayGoods = $todayShop->getGoodsByDate(); // 현재 판매중인 상품만 가져옴.
	if (!is_array($todayGoods) || empty($todayGoods)) $tgsno = -1; // 판매중인 상품이 없을 경우
	else {
		header('location:today_goods.php?'.$_SERVER['QUERY_STRING'].'&tgsno='.$todayGoods[0]['tgsno']);
		exit;
	}

	unset($todayGoods);
}

if ($tgsno == -1) { // 상품이 없을 경우.
	$data['tgsno'] = -1;
	$data['r_img'][] = '../skin_today/'.$cfg['tplSkinToday'].'/img/main_closed.gif';
	$data['showpercent'] = 'y';
	$data['smsCnt'] = 0;
}
else { // 상품이 있을 경우.
	$data = $todayShop->getGoods($tgsno);
	
	if (!is_array($data) || empty($data)) {
		msg("잘못된 상품번호입니다.",-1);
		exit;
	}

	$goodsno = $data['goodsno'];
	$stime = getTimeArray($data['startdt']);
	$etime = getTimeArray($data['enddt']);
	$start_time = mktime($stime['hh'], $stime['mm'], $stime['ss'], $stime['MM'], $stime['DD'], $stime['YY']);
	$end_time = mktime($etime['hh'], $etime['mm'], $etime['ss'], $etime['MM'], $etime['DD'], $etime['YY']);
	unset($stime, $etime);

	### 상품 진열 여부 체크
	if (!$data['visible']) {
		msg("해당상품은 진열이 허용된 상품이 아닙니다",-1);
		exit;
	}

	### 네이버 지식쇼핑 구매율 쿠키생성
	if($_GET[nv_pchs]){
		SetCookie("nv_pchs",$_GET[nv_pchs],time()+86400*30,"/" );	# 유효기간 30일
	}

	###  옥션 오픈쇼핑 구매율 쿠키생성
	if($_GET['clickid']){
		SetCookie("aos_clickid",$_GET['clickid'],time()+86400,"/" ); # 유효기간 1일
	}

	list( $data[brand] ) = $db->fetch("select brandnm from ".GD_GOODS_BRAND." where sno='$data[brandno]'");

	### 추가스펙 세팅
	$data[ex_title] = explode("|",$data[ex_title]);
	foreach ($data[ex_title] as $k=>$v) $data[ex][$v] = $data["ex".($k+1)];
	$data[ex] = array_notnull($data[ex]);

	### 아이콘
	$data[icon] = setIcon($data[icon],$data[regdt]);

	### 이미지 배열
	$data[r_img] = explode("|",$data[img_m]);
	$data[t_img] = array_map("toThumb",$data[r_img]);

	if ($start_time > time()) {
		msg("해당 상품은 현재 판매기간이 아닙니다.", -1);
		exit;
	}

	// 기본 구매수량
	$data['default_ea'] = ($data['min_ea'] != 0)? $data['min_ea'] : 1;

	// 오늘 날짜
	$today_dt = date('Ymd');
	for($i = 0; $i < strlen($today_dt); $i++) {
		$data['today_dt'][] = substr($today_dt, $i, 1);
	}

	// 날짜 설정
	$data['opendt'] = (string)$data['startdt']; // 진행기간 출력에 사용될 시작시간
	if (substr($data['startdt'], 0, 10) != substr($data['enddt'], 0, 10)) $data['closedt'] = (string)$data['enddt']; // 진행기간 출력에 사용될 종료시간
	$data['startdt'] = explode(' ', $data['startdt']); // 타이머에 사용될 시작시간
	$data['enddt'] = explode(' ', $data['enddt']); // 타이머에 사용될 종료시간

	// 현재 상품의 카테고리
	$data['category'] = $todayShop->getCurCategory($tgsno);

	### ace 카운터
	$Acecounter->goods_view($goodsno,$data[goodsnm],$data[price],$_GET[category]);
	if($Acecounter->scripts){
		$systemHeadTagEnd .= $Acecounter->scripts;
		$tpl->assign('systemHeadTagEnd',$systemHeadTagEnd);
	}

	### 추가옵션
	$r_addoptnm = explode("|",$data[addoptnm]);
	for ($i=0;$i<count($r_addoptnm);$i++) list ($addoptnm[],$addoptreq[]) = explode("^",$r_addoptnm[$i]);
	$query = "select * from ".GD_GOODS_ADD." where goodsno='$goodsno' order by sno";
	$res = $db->query($query);
	while ($tmp=$db->fetch($res,1)) $addopt[$addoptnm[$tmp[step]]][] = $tmp;

	### 메타정보
	if($data[meta_title]) $meta_title = $cfg[shopName] ." ". strip_tags($data[goodsnm]);
	$meta_keywords = $data[keyword];

	// SMS 발송시 필요한 포인트 갯수
	$smsMsg = $todayShop->makeSmsMsg($data['sms']);
	$data['smsCnt'] = count($smsMsg);
	unset($smsMsg);

	/*
	캐시기능의 수정으로 인해 아래의 내용을 불러 들이는 indb.pageinit.php 파일은 불필요 하나,
	구 스킨에서 사용하므로 삭제하지 아니 함
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

### 구매자수
$data[buyercnt] = $data[buyercnt] + $data[fakestock];

### 할인율
$data['dc_rate'] = 100 - ceil($data['price'] * 100 / $data['consumer']);

// SNS POST
$args = array('shopnm'=>$cfg['shopName'],
				'goodsnm'=>$data['goodsnm'],
				'goodsurl'=>'http://'.$_SERVER['HTTP_HOST'].$cfg['rootDir'].'/todayshop/today_goods.php?tgsno='.$tgsno,
				'img'=>$data['img_s']);
$data['snspost'] = $todayShop->getSnsPostBtn($args);
$customHeader .= $data['snspost']['meta']; // 페이스북에 사용될 meta tag

// 장바구니 사용 (실물 & 즉시발송 상품 일때만
$data['use_cart'] = ($data['goodstype'] == 'goods' && $data['processtype'] == 'i' ) ? 'y' : 'n';

// 구독 관련
$subscribe = unserialize(stripslashes($todayShop->cfg['subscribe']));
$interest = unserialize(stripslashes($todayShop->cfg['interest']));

### 템플릿 출력
$tpl->assign($data);
$tpl->assign('customHeader', $customHeader);
$tpl->assign('ts_category_all', $ts_category_all);	// 값은 _header.php 에서 불러옵니다.

$_html = $tpl->fetch('tpl');

$cache->setCache($_html);
?>
