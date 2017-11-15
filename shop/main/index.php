<?

$mainpage = true;
include "../_header.php";
@include "../conf/config.pay.php";
@include "../conf/design.search.php";
@include "../conf/config.soldout.php";

$todayshop = Core::loader('todayshop');
if ($todayshop->cfg['shopMode'] == 'todayshop') header('location:'.dirname($_SERVER['SCRIPT_NAME']).'/../todayshop/today_goods.php?'.$_SERVER['QUERY_STRING']);

include "../conf/design.main.php";
@include "../conf/design_main.$cfg[tplSkin].php";

$hashtag = Core::loader('hashtag');

function dataDisplayTabGoods( $mode, $img='img_s', $limit=0 ){

	global $db, $cfg, $hashtag;
	include dirname(__FILE__) . "/../conf/config.pay.php";

	if (is_file(dirname(__FILE__) . "/../conf/config.soldout.php"))
		include dirname(__FILE__) . "/../conf/config.soldout.php";
	
	@include dirname(__FILE__) . "/../conf/config.display.php";
	
	$goods = array();

	if ($GLOBALS['tpl']->var_['']['connInterpark']) $where .= "and b.inpk_prdno!=''";

	// 메인페이지에서만 사용하는 데이터 함수이므로, 스킨별 처리 및 품절 상품 설정별 처리를 한다.
	$orderby = 'order by a.sort';

	if( $cfg['shopMainGoodsConf'] == "E" ){
		$where .= " and tplSkin = '".$cfg['tplSkin']."'";
	}else{
		$where .= " and (tplSkin = '' OR tplSkin IS NULL)";
	}

	// 품절 상품 제외
	if ($cfg_soldout['exclude_main']) {
		$where .= " AND !( b.runout = 1 OR (b.usestock = 'o' AND b.usestock IS NOT NULL AND b.totstock < 1) ) ";
	}
	// 제외시키지 않는 다면, 맨 뒤로 보낼지를 결정
	else if ($cfg_soldout['back_main']) {
		$orderby = "order by `soldout` ASC, a.sort";
		$_add_field = ",IF (b.runout = 1 , 1, IF (b.usestock = 'o' AND b.totstock = 0, 1, 0)) as `soldout`";
	}

	$query = "
	select
		*,b.$img img_s
		$_add_field
	from
		".GD_GOODS_DISPLAY." a
		left join ".GD_GOODS." b on a.goodsno=b.goodsno
		left join ".GD_GOODS_OPTION." c on a.goodsno=c.goodsno and link and go_is_deleted <> '1' and go_is_display = '1'
		left join ".GD_GOODS_BRAND." d on b.brandno=d.sno
	where
		a.mode = '$mode'
		and b.open
		{$where}
	{$orderby}
	";
	if ( $limit > 0 ) $query .= " limit " . $limit;
	$res = $db->query($query);
	while ( $data = $db->fetch( $res, 1 ) ){

		### 실재고에 따른 자동 품절 처리
		$data['stock'] = $data['totstock'];
		if ($data[usestock] && $data[stock]==0) $data[runout] = 1;

		### 쿠폰
		list($data['coupon'],$data['coupon_emoney']) = getCouponInfo($data['goodsno'],$data['price']);

		### 적립금 셋팅
		if(!$data['use_emoney']){
			if( !$set['emoney']['chk_goods_emoney'] ){
				if( $set['emoney']['goods_emoney'] ) $tmp['reserve'] = getDcprice($data['price'],$set['emoney']['goods_emoney'].'%');
			}else{
				$tmp['reserve'] = $set['emoney']['goods_emoney'];
			}
			$data['reserve'] = $tmp['reserve'];
		}

		$data['reserve'] += $data['coupon_emoney'];

		### 아이콘
		$data[icon] = setIcon($data[icon],$data[regdt]);
		
		// 상품할인 가격 표시
		if ($displayCfg['displayType'] === 'discount') {
			$discountModel = '';
			$goodsDiscount = '';
			if ($data['use_goods_discount'] === '1') {
				$discountModel = Clib_Application::getModelClass('Goods_Discount');
				$goodsDiscount = $discountModel->getDiscountAmountSearch($data);
			}
			if ($goodsDiscount) {
				$data['oriPrice'] = $data['price'];
				$data['goodsDiscountPrice'] = $data['price'] - $goodsDiscount;
			}
			else {
				$data['oriPrice'] = '0';
				$data['goodsDiscountPrice'] = $data['price'];
			}
		}
		
		$data['hashtag'] = $hashtag->getHashtagList('goodsList', array('goodsno'=>$data['goodsno']));
		
		// 출력 제어
		$goods[] = setGoodsOuputVar($data);
	}

	return $goods;
}

// 메인 상품 리스트 화면 설정
$cfg_step_keys = array_keys($cfg_step);

for($i = 0, $imax = count($cfg_step_keys); $i < $imax; $i++) {

	$_cfg = $cfg_step[ $cfg_step_keys[$i] ];

	if ($_cfg[chk] == 'on') {
		if (empty($_cfg[tpl])) $_cfg[tpl] = 'tpl_01';
		if (empty($_cfg[img])) $_cfg[img] = 'img_s';
		if (empty($_cfg[page_num])) $_cfg[page_num] = 5;
		if (empty($_cfg[cols])) $_cfg[cols] = 5;
	}

	$_cfg['idx'] = $cfg_step_keys[$i]; // 인덱스 값 저장

	if($_cfg['tpl'] == "tpl_07") { // 탭방식일 경우 상품 리스트 추가 읽기

		for($j = 1; $j <= $_cfg['tabNum']; $j++) {
			$_cfg['tabLoop'][$j] = dataDisplayTabGoods($j."_".$cfg_step_keys[$i], $_cfg['img'], $_cfg['page_num']);
		}
	}
	$cfg_step[ $cfg_step_keys[$i] ] = $_cfg;
}

for($i=0; $i<count($cfg_search); $i++){
	foreach($cfg_search[$i] as $key=>$val){
		if( strstr($val, ',') ) $val = explode(',', $val);
		$s_type[$key] = $val;

		switch($key){
			case 'keyword':
				if( !is_array($val) ) {
					if( $val ) $s_type[$key] =  '<a href="javascript: add_param_submit(\'sword\', \''.$val.'\');">'.$val.'</a>';
					continue;
				}
				foreach($val as $k=>$v){
					$s_type[$key][$k] = '<a href="javascript: add_param_submit(\'sword\', \''.$v.'\');">'.$v.'</a>';
				}
				$s_type['keyword'] = implode(', ', $s_type['keyword']);
				break;
			case 'detail_type':
			case 'detail_add_type':
				if(!is_array($val)) $s_type[$key] = array($val);
				else $s_type[$key] = $val;
				break;
			default:
				$s_type[$key] = $val;
				break;
		}
	}
}
if(count($s_type['pr_text']) > 1) {
	$randcnt = rand(0, count($s_type['pr_text'])-1);
	$s_type['pr_text'] = $s_type['pr_text'][$randcnt];
	$s_type['link_url'] = $s_type['link_url'][$randcnt];
}

if(!$_GET['disp_type']) {
	if( is_array($s_type['disp_type']) ) $_GET['disp_type'] = 'list';
	else $_GET['disp_type'] = $s_type['disp_type'];
}
$s_type['disp_type'] = (is_array($s_type['disp_type']) ? 'Y' : 'N');

####크리테오#####
$criteo = new Criteo();
if($criteo->begin()) {
	$criteo->get_main();
	$systemHeadTagEnd .= $criteo->scripts;
	$tpl->assign('systemHeadTagEnd',$systemHeadTagEnd);
}
#################

$hashtagHtml = '';
$hashtagHtml = $hashtag->getHashtagList('main');
$tpl->assign('hashtagHtml', $hashtagHtml);


$tpl->print_('tpl');


//$db->viewLog();

?>
