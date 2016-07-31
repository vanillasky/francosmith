<?
include "../_header.php";
include "../lib/cart.class.php";
include "../conf/config.pay.php";
@include "../conf/pg.$cfg[settlePg].php";
include "../conf/pg.escrow.php";
include '../lib/lib.func.egg.php';
$egg = getEggConf();
@include "../conf/auctionIpay.pg.cfg.php";

if(class_exists('validation') && method_exists('validation','xssCleanArray')){
	$_POST = validation::xssCleanArray($_POST, array(
		validation::DEFAULT_KEY => 'text',
	));
}

// getordno 함수는 /shop/lib/lib.func.php 파일로 이동

// 윈도우8 Metro IE로 결제페이지 접속시 Desktop IE로 전환 유도 메시지 출력
if (isset($pg) === true) {
	header( 'X-UA-Compatible: requiresActiveX=true');
}

### ok캐쉬백결제
@include "../conf/pg.cashbag.php";
if( $cashbag['usesettle'] == "on" && $cashbag['code'] && $cashbag['key'] ) $set['use']['p'] = "on";

### 외부 유입의 경우(야후패션소호) 에스크로 자동실행
if ($_COOKIE[cc_inflow] == 'yahoo_fss'){
	$escrow['use'] = 'Y';
	$escrow['min'] = '0';
}

### 회원정보 가져오기
if ($sess){
	$query = "
	select * from
		".GD_MEMBER." a
		left join ".GD_MEMBER_GRP." b on a.level=b.level
	where
		m_no='$sess[m_no]'
	";
	$member = $db->fetch($query,1);
	$style_member = "readonly style='border:0'";
}

### 장바구니 쿠키 설정
if ($_POST[mode]=="addItem" && !$_COOKIE[gd_isDirect]) setcookie('gd_isDirect',1,0,'/');
$isDirect = ($_POST[mode]=="addItem" || $_COOKIE[gd_isDirect]) ? 1 : 0;

$cart = Core::loader('Cart',$isDirect);
$mobilians = Core::loader('Mobilians');
$danal = Core::loader('Danal');

if ($_REQUEST[preview]=='y' && $_REQUEST[cody]!='y' ) {	//미리보기팝업 진열상품처리
	chkOpenYn($_POST[goodsno],"D",'parentClose');
		echo "
			<script>
				parent.frmView.preview.value='n';
				parent.act('../order/order','opener');
			</script>
		";
		exit;
}

if(is_array($_POST[goodsno])){	//배코에서 넘어온 세트상품
	chkOpenYn($_POST[goodsno],"D",-2);	//진열여부 체크
	for($i=0;$i<sizeof($_POST[goodsno]);$i++){
		if($_POST[goodsno][$i]){
			$cart->addCart($_POST[goodsno][$i],array_notnull($_POST[opt][$i]),$_POST[addopt][$i],$_POST[addopt_inputable][$i],$_POST[ea][$i],$_POST[goodsCoupon][$i]);
		}
	}
}
else{
	if ($_POST[mode]=="addItem"){
		chkOpenYn($_POST[goodsno],"D",-1);	//진열여부 체크
		if ($_POST[multi_ea]) {
			$_keys = array_keys($_POST[multi_ea]);
			for ($i=0, $m=sizeof($_keys);$i<$m;$i++) {
				$_opt = $_POST[multi_opt][ $_keys[$i] ];
				$_ea = $_POST[multi_ea][ $_keys[$i] ];
				$_addopt = $_POST[multi_addopt][ $_keys[$i] ];
				$_addopt_inputable = $_POST[multi_addopt_inputable][ $_keys[$i] ];

				$cart->addCart($_POST[goodsno],$_opt,$_addopt,$_addopt_inputable,$_ea,$_POST[goodsCoupon]);
			}
		}
		else {
			$cart->addCart($_POST[goodsno],$_POST[opt],$_POST[addopt],$_POST[_addopt_inputable],$_POST[ea],$_POST[goodsCoupon]);
		}
	}
}

if (!isset($_POST[idxs])) $_POST[idxs] = $_COOKIE['_posted_idxs'] ? unserialize( get_magic_quotes_gpc() ? stripslashes($_COOKIE['_posted_idxs']) : $_COOKIE['_posted_idxs']) : 'all';
//성인인증 상품 청소년보호법 적용 (성인인증을 안한 상태에서 성인인증 상품 주문시 성인인증 진행)
if($_POST[idxs] == 'all'){
	if ($cart->item) {
		foreach ($cart->item as $v){
			if ($v['use_only_adult'] == '1' && !Clib_Application::session()->canAccessAdult()){
				msg("성인인증이 필요한 상품/컨텐츠 가 포함되어 있습니다.\\n\\n성인(본인) 인증 후 주문하기를 진행해 주세요.","../main/intro_adult.php?returnUrl=../goods/goods_cart.php");
				exit;
			}
		}
	}
} else {
	if (is_array($_POST[idxs]) && !empty($_POST[idxs])) foreach($_POST[idxs] as $idx) {
		$item = $cart->item[$idx];
		if ($item['use_only_adult'] == '1' && !Clib_Application::session()->canAccessAdult()){
			msg("성인인증이 필요한 상품/컨텐츠 가 포함되어 있습니다.\\n\\n성인(본인) 인증 후 주문하기를 진행해 주세요.","../main/intro_adult.php?returnUrl=../goods/goods_cart.php");
			exit;
		}
	}
}
$cart->setOrder($_POST[idxs]);	// $_POST[idxs] 는 , 로 구분된 0 이상의 정수 또는 'all'

if($_POST['mode'] != 'addItem' && is_array($_POST['goodsno']) == false){	//장바구니 주문하기에서 진열여부,판매종료 체크
	chkOpenYn($cart,"D",-1);
}

if($member){
	$cart->excep = $member['excep'];
	$cart->excate = $member['excate'];
	$cart->dc = $member[dc]."%";
}
$cart -> coupon = $_POST['coupon'];
$cart -> coupon_emoney = $_POST['coupon_emoney'];
$cart->calcu();
$orderitem_rowspan = get_items_rowspan($cart->item);

### s1스킨들을 위해 기본 배송비 가져오기
$param = array(
	'mode' => '0',
	'zipcode' => $member[zipcode],
	'emoney' => 0,
	'deliPoli' => 0,
	'coupon' => 0,
	'road_address' => $member['road_address'],
	'address' => $member['address'],
	'address_sub' => $member['address_sub'],
);

$delivery = getDeliveryMode($param);
$cart -> delivery = $delivery['price'];
$cart -> totalprice += $delivery['price'];

### 잔여 재고 체크........2007-07-18 modify
if ($cart->item) {
	foreach ($cart->item as $v){
		$cart->chkStock($v[goodsno],$v[opt][0],$v[opt][1],$v[ea]);
	}
}

### 비회원일 경우 로그인창으로 이동
if ($_GET[guest]) setCookie('guest',1,0,'/');
if (!$sess && !$_GET[guest] && !$_COOKIE[guest]){
	setCookie('_posted_idxs', serialize($_POST[idxs]) ,0,'/');
	go("../member/login.php?guest=1&returnUrl=$_SERVER[PHP_SELF]");
}
else {
	setCookie('_posted_idxs', false , time() - 86400 ,'/');
}

### 주문번호 생성
$ordno = getordno();

$set['emoney']['base'] = pow(10,$set['emoney']['cut']);

### 적립금 사용범위
if(!$set['emoney']['emoney_use_range'])$tmp = $cart->goodsprice;
else $tmp = $cart->totalprice;
$tmp = $tmp - getDcPrice($cart->goodsprice,$cart->dc) - getDcPrice($cart->goodsprice,$cart->special_discount_amount);
$emoney_max = getDcprice($tmp,$set[emoney][max])+0;

$r_deli = explode('|',$set['r_delivery']['title']);

if ($member){
	$member[zipcode] = explode("-",$member[zipcode]);
	$member[phone] = explode("-",$member[phone]);
	$member[mobile] = explode("-",$member[mobile]);
	$tpl->assign($member);
}

### 오픈스타일 헤더 노출
if($_COOKIE['cc_inflow']=="openstyleOutlink"){
	echo "<script src='http://www.interpark.com/malls/openstyle/OpenStyleEntrTop.js'></script>";
}

### 어바웃 쿠폰
if($about_coupon->use && $_COOKIE['about_cp']=='1'){
	$tpl->assign('view_aboutdc', 1);
	$tpl->assign('about_coupon', (int) $cart->tot_about_dc_price);
}

### 네이버 마일리지
$load_config_ncash = $config->load('ncash');
$naverNcash = Core::loader('naverNcash');
if(!$naverNcash->realyn()){ $load_config_ncash['useyn'] = "N"; $naverNcash->useyn = "N"; }
if($load_config_ncash['api_id'] == '' || $load_config_ncash['api_key'] == '') $load_config_ncash['useyn'] = "N";
if($load_config_ncash['useyn'] == 'Y'){

	$exceptionYN = "";

	$naverNcash = Core::loader('naverNcash');

	if($naverNcash->useyn == 'Y'){
		$tpl->assign('ncash_yn','Y');
		if($naverNcash->baseAccumRate)
		{
			$load_config_ncash['N_ba'] = preg_replace('/\.0$/', '', $naverNcash->get_base_accum_rate());
			$load_config_ncash['N_aa'] = preg_replace('/\.0$/', '', $naverNcash->get_add_accum_rate());
		}
	}
	else {
		$load_config_ncash['useyn'] = 'N';
	}

	### 예외상품 체크
	$exceptionYN = $naverNcash->exception_goods($cart->item);
	$load_config_ncash['exception_price'] = $naverNcash->exception_price($cart->item);

	if( $exceptionYN == "N" ){
		$load_config_ncash['doneUrl'] = urlencode("http://".$_SERVER['HTTP_HOST']."/shop/proc/naverNcash_bridge.php");
		$tpl->assign('ncash',$load_config_ncash);
	}

	foreach($cart->item as $k => $v){
		$exception_goods = $exception_category = "N";

		// 예외상품 체크
		if(@in_array($v['goodsno'],$naverNcash->e_exceptions))	$exception_goods = "Y";

		// 예외카테고리 체크
		$res = $db->query("select category from `gd_goods_link` where `goodsno` = ".$v['goodsno']);
		while ($data=$db->fetch($res)){
			if(@in_array($data['category'],$naverNcash->e_category))	$exception_category = "Y";
		}

		// 예외 상태 값 적용
		if(!($exception_goods == 'N' && $exception_category == 'N')){
			$cart->item[$k]['exceptionYN'] = 'Y';
		}else{
			$cart->item[$k]['exceptionYN'] = 'N';
		}
	}
}

// 아이페이 전용결제
if(isset($auctionIpayPgCfg))
{
	if($auctionIpayPgCfg['testYn']==='y')
	{
		if(isset($sess) && (int)$sess['level']===100) $useIpayPg = true;
		else $useIpayPg = false;
	}
	else
	{
		if($auctionIpayPgCfg['useYn']==='y') $useIpayPg = true;
		else $useIpayPg = false;
	}
}

//페이코
if(is_file('../lib/payco.class.php')){
	$Payco = Core::loader('payco')->getButtonHtmlCode('EASYPAY', false, '');
	if($Payco) $tpl->assign('Payco', $Payco);
}

$tpl->assign('useIpayPg', $useIpayPg);

$tpl->assign('orderitem_rowspan',$orderitem_rowspan);
$tpl->assign('cart',$cart);
$tpl->assign('ordno',$ordno);

### 주문처리url
if($cfg['ssl_type'] == "free") { //무료
	$tpl->assign('orderActionUrl',$sitelink->link('order/settle.php','regular'));
} else { //유료 혹은 보안서버안씀
	$tpl->assign('orderActionUrl',$sitelink->link('order/settle.php','ssl'));
}

//nscreen 결제일때, 결제수단 제외 예외 처리 2013-10-31 dn
@include '../conf/config.nscreenPayment.php';
$nScreenPayment = Core::Loader('nScreenPayment');

if($config_nscreen_payment['use'] && ($nScreenPayment->getScreenType() == 'MOBILE' && $cfg[settlePg] != 'lgdacom')) {
	$set['use']['o'] = ''; //계좌이체
	$set['use']['u'] = ''; //중국카드
	$set['use']['y'] = ''; //옐로페이
	$escrow['use'] = 'N';  //에스크로
	$tpl->assign('useIpayPg', false); //아이페이
}

// 모빌리언스 서비스가 활성화 되어있을 시 모바일 결제 추가 셋팅
if ($cfg['settleCellPg'] === 'mobilians' && $mobilians->isEnabled()) {
	$set['use']['h'] = 'on';
}
// 다날 서비스가 활성화 되어있을 시 모바일 결제 추가 셋팅
else if ($cfg['settleCellPg'] === 'danal' && $danal->isEnabled()) {
	$set['use']['h'] = 'on';
}

$termsPolicyCollection3 = getTermsGuideContents('terms', 'termsPolicyCollection3');
$tpl->assign('termsPolicyCollection3', $termsPolicyCollection3);

$tpl->print_('tpl');

?>
