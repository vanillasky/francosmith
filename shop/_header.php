<?

@include dirname(__FILE__) . "/lib/library.php";
@include dirname(__FILE__) . "/Template_/Template_.class.php";
@include dirname(__FILE__) . "/conf/config.php";
@include dirname(__FILE__) . "/lib/tplSkinView.php";
@include dirname(__FILE__) . "/conf/design_dir.php";
@include dirname(__FILE__) . "/conf/design_skin_" . $cfg['tplSkin'] . ".php";
@include dirname(__FILE__) . "/conf/design_skinToday_" . $cfg['tplSkinToday'] . ".php";
@include dirname(__FILE__) . "/lib/acecounter.class.php";
@include dirname(__FILE__) . "/lib/sns.class.php";
@include dirname(__FILE__) . "/lib/criteo.class.php";
@include dirname(__FILE__) . "/lib/facebook.class.php";
@include dirname(__FILE__) . "/setGoods/data/config/setGoodsConfig.php";

##### 2011-09-02 kmn 쿠폰팝업알림 추가
@include dirname(__FILE__) . "/lib/coupon_check.class.php";

/*
 * 투데이샵 스킨 적용 수정 2010.12.29 by slowj
 * url에 todayshop 포함인 경우 design_skin 대신 design_skinToday 적용
 *
 * 적용 방법 변경 2011-06-15 by x-ta-c
 *
 */
$todayShop = Core::loader('todayshop');

// 페이지가 투데이샵 페이지인지 확인.
//if (($isTodayShopPage = $todayShop->getCheckTodayShopPage($design_skinToday)) === true) {		// 2011-07-11 이 줄을 아래 2줄(24, 25라인)로 수정합니다.
$isTodayShopPage = $todayShop->getCheckTodayShopPage($design_skinToday);
if ($todayShop->cfg['shopMode'] == 'todayshop' || $isTodayShopPage) {

	$curPageSkinPath = 'skin_today/'.$cfg['tplSkinToday'];

	if (is_array($design_skinToday) && empty($design_skinToday)===false) {
		foreach($design_skinToday as $key => $val) {
			foreach($val as $key2 => $val2) {
				if ($key != 'default') $_key = '../../skin_today/'.$cfg['tplSkinToday'].'/'.$key;
				else $_key = $key;
				if (preg_match('/^outline_/', $key2) && $val2 != 'noprint') {
					$val2 = '../../skin_today/'.$cfg['tplSkinToday'].'/'.$val2;
				}
				$d_skinToday[$_key][$key2] = $val2;
			}
		}
		$design_skinToday = $d_skinToday;
	}

	// 샵 레이아웃 (일반몰 or 투데이샵 전용)
	if ($todayShop->cfg['shopMode'] == 'todayshop') {
		$headerSkinPath = 'skin_today/'.$cfg['tplSkinToday'];
		// 일반몰 스킨의 아웃라인 정보 삭제
		foreach($design_skin as $key => $val) {
			if ($key == 'default') continue;
			foreach($val as $key2 => $val2) {
				if ($key2 == 'outline_header' || $key2 == 'outline_footer') unset($design_skin[$key][$key2]);
			}
		}
		if (is_array($design_skinToday) && empty($design_skinToday)===false) {
			foreach($design_skinToday as $key => $val) {
				if ($key == 'default') {
					foreach($val as $key2 => $val2) {
						if (!preg_match('/^outline_side/', $key2)) $design_skin[$key][$key2] = $design_skinToday[$key][$key2];
					}
				}
				else {
					$design_skin[$key] = $design_skinToday[$key];
				}
			}
		}

		$_tmp['folder']	= explode("/",str_replace($_SERVER['DOCUMENT_ROOT'],"", $_SERVER['SCRIPT_FILENAME']));
		$_tmp['defaultFolder'] = $_SERVER['DOCUMENT_ROOT']."/".$_tmp['folder'][1];

		/*
			/lib/tplSkinView.php 에서 교체하는게 맞으나, 위치가 애매하여 여기에서 치환.
		*/
		$_design_basic_file = $_tmp['defaultFolder'] . "/conf/design_basicToday_".$cfg['tplSkinToday'].".php";
		if(is_file( $_design_basic_file)){
			include $_design_basic_file;
		}

	}
	else {
		$headerSkinPath = 'skin/'.$cfg['tplSkin'];
		// 투데이샵 전용몰 스킨의 아웃라인 정보 삭제 및 투데이샵 스킨 정보를 일반몰 스킨 정보에 추가($design_skinToday->$design_skin)
		if (is_array($design_skinToday) && empty($design_skinToday) === false) {
			foreach($design_skinToday as $key => $val) {
				if ($key == 'default') continue;
				foreach($val as $key2 => $val2) {
					if ($key2 == 'outline_header' || $key2 == 'outline_footer') unset($design_skinToday[$key][$key2]);
				}
				if (!preg_match('/^outline\/(header|footer)/', $key)) {
					$design_skin[$key] = $design_skinToday[$key];
				}
			}
		}
	}
}
else {
	$headerSkinPath = $curPageSkinPath = 'skin/'.$cfg['tplSkin'];
}

$referer_url = parse_url($_SERVER['HTTP_REFERER']);
$host_url = explode(":",$_SERVER['HTTP_HOST']);

$referer_domain = str_replace('www.','',$referer_url['host']);
$shop_domain = str_replace('www.','',$host_url[0]);

$cookie_domain = str_replace('www','',$host_url[0]);
if(substr($cookie_domain,0,1) != '.') $cookie_domain = ".".$cookie_domain;

function _destroyNcachCookie() {

	global $cookie_domain;

	$expire = time() - 3600;

	foreach (array('Ncisy','N_t','N_e','N_ba','N_aa') as $v) {
	setCookie($v, "", $expire ,"/",$cookie_domain);
	$_COOKIE[$v] = "";
	}
	setCookie("cookie_check",0,0,'/',$cookie_domain); # 유효기간 24시간

}

if(!$referer_domain){
	setCookie("cookie_check",1,0,'/',$cookie_domain);
	$_COOKIE['cookie_check'] = "1";
}

if (preg_match('/(\.naver\.com|godo\.co\.kr)/',$referer_domain) && $_GET['Ncisy']) {

	$Ncisy = $A_Ncisy = array();
	$Ncisy = explode("|",urldecode($_GET['Ncisy']));

	foreach($Ncisy as $v){
		$tmp = explode("=",$v);
		$A_Ncisy[$tmp[0]] = $tmp[1];
	}

	setCookie("Ncisy",$_GET['Ncisy'],0,'/',$cookie_domain); # 유효기간 24시간
	setCookie("N_t",$A_Ncisy['t'],0,'/',$cookie_domain); # 유효기간 24시간
	setCookie("N_e",$A_Ncisy['e'],0,'/',$cookie_domain); # 유효기간 24시간
	setCookie("N_ba",$A_Ncisy['ba'],0,'/',$cookie_domain); # 유효기간 24시간
	setCookie("N_aa",$A_Ncisy['aa'],0,'/',$cookie_domain); # 유효기간 24시간

	$_COOKIE['Ncisy'] = $_GET['Ncisy'];
	$_COOKIE['N_t'] = $A_Ncisy['t'];
	$_COOKIE['N_e'] = $A_Ncisy['e'];
	$_COOKIE['N_ba'] = $A_Ncisy['ba'];
	$_COOKIE['N_aa'] = $A_Ncisy['aa'];

	setCookie("cookie_check",1,0,'/',$cookie_domain);

}
elseif ($_COOKIE['N_e'] < time()) {
	_destroyNcachCookie();
}
elseif ($_COOKIE['cookie_check'] != 1) {
	_destroyNcachCookie();
}
elseif ($referer_domain && ($shop_domain != $referer_domain) && (!preg_match('/(naver\.com|godo\.co\.kr)/',$referer_url['host']))){
	_destroyNcachCookie();
}

### ssl 리다이렉트
$sitelink->ready_refresh();

$cfg = array_map("slashes",$cfg);
if($cfg['customerHour']){
	$cfg['customerHour'] = preg_replace("/&lt;br \/&gt;/","<br />",$cfg['customerHour']);
}

### 카운터정보획득
include dirname(__FILE__)."/lib/_log.php";

### 데모경고메시지
if (!$noDemoMsg) @include dirname(__FILE__) . '/proc/demo_warning_msg.php';

### 메타태그 변수 할당
$meta_title = $cfg[title];
$meta_keywords = $cfg[keywords];

### 회원인증 유지시간 체크
if ($sess && $cfg[sessTime]){
	if(!preg_match('/logout.php/',$_SERVER[PHP_SELF])){
		if ($cfg[sessTime]*60<time()-$_COOKIE[Xtime]) msg("오랜 시간동안 응답이 없어서 자동로그아웃 됩니다",$cfg['rootDir']."/member/logout.php?referer=$_SERVER[REQUEST_URI]");
	}
}
setCookie('Xtime',time(),0,'/');

//if ($sess && $cfg[sessTime]){
//	if ($cfg[sessTime]*60<time()-$_COOKIE[Xtime]) msg("오랜 시간동안 응답이 없어서 자동로그아웃 됩니다","../member/logout.php");
//	setCookie('Xtime',time(),0,'/');
//}

$tpl = new Template_;
$tpl->template_dir	= dirname(__FILE__)."/data/skin/".$cfg['tplSkin'];
$tpl->compile_dir	= dirname(__FILE__)."/Template_/_compiles/".$cfg['tplSkin'];
$tpl->prefilter		= "adjustPath|include_file|capture_print|sitelinkConvert|systemHeadTag";

#### 코디상품 베너 설정
$tpl->assign('Banner', "<a href='../setGoods/'><img src='../setGoods/data/banner/".$setGoodsConfig['setGoodsBanner']."'></a>");
$tpl->assign('setState', $setGoodsConfig['state']);

{ // File Key

	$key_file = preg_replace( "'^.*$cfg[rootDir]/'si", "", $_SERVER['SCRIPT_NAME'] );
	$key_file = preg_replace( "'\.php$'si", ".htm", $key_file );

	if ( ( $key_file == 'main/html.htm' || $key_file == 'todayshop/html.htm' ) && $_GET['htmid'] != '' ) $key_file = $_GET['htmid'];

	if ( preg_match( "/^member\/attendance/i", $key_file ) ){
		$attd = Core::loader('attendance');
		$attendance_no = (int)$_GET['attendance_no'];
		$query = "select * from gd_attendance where attendance_no='{$attendance_no}'";
		$result = $db->_select($query);
		$attd_info = $result[0];
		$key_file = $attd->design_body[$attd_info['design_body']];
	}

	if ( preg_match( "/^board\//i", $key_file ) && $_GET[id]){
		if(!preg_match('/^[a-zA-Z0-9_]*$/',$_GET['id'])) exit;
		include dirname(__FILE__) . "/conf/bd_$_GET[id].php";
		$key_file = str_replace( "board/", "board/$bdSkin/", $key_file );
	}

	if ($isTodayShopPage) $key_file = '../../skin_today/'.$cfg['tplSkinToday'].'/'.$key_file;

	$data_file		= $design_skin[ $key_file ];		# File Data
	
	/* 마이페이지 사이드 메뉴 */
	if($key_file == 'mypage/mypage.htm' && $_GET['htmid'] != 'mypage/mypage.htm' && isset($design_skin['mypage/mypage.htm']) === false) {
		$data_file['outline_side'] = 'outline/side/mypage.htm';
	}

}


if (strpos($_SERVER[HTTP_HOST], ".godo.interpark.com") !== false){ // 인터파크
	$data_file['outline_header'] = 'noprint';
	$tpl->assign('connInterpark', 'ok' );
}

### 성인인증 인트로
if($cfg[custom_landingpage] > 1 && !preg_match( "/main\/intro*/", $key_file) && !preg_match( "/member\/find*/", $key_file) && !preg_match( "/member\/join*/", $key_file) && !preg_match( "/member\/login*/", $key_file) && !preg_match( "/proc\/popup_zipcode*/", $key_file) && !preg_match( "/member\/change*/", $key_file) && !preg_match( "/proc\/popup_address*/", $key_file)){
	$returnUrl = urlencode($_SERVER['REQUEST_URI']);
	$auth_date = getAdultAuthDate($session->m_id);
	$auth_date = $auth_date['auth_date'];
	$current_date = date("Y-m-d");
	$auth_period = strtotime("+1 years", strtotime($auth_date)); 
	$auth_period = date("Y-m-d", $auth_period);
	
	if ($cfg[custom_landingpage] == 2 && !Clib_Application::session()->isAdult() && !$sess) {	// 성인 or 회원
		header('location:'.$cfg[rootDir].'/main/intro_adult.php?returnUrl=' . $returnUrl . ($_SERVER['QUERY_STRING'] ? '&'.$_SERVER['QUERY_STRING'] : ''));
	}
	else if ($cfg[custom_landingpage] == 2 && $sess && ($auth_date == '0000-00-00' || $current_date > $auth_period) && ((int)($session->level) < 80)) {	// 회원 성인인증기간(adult_date) 경과 검증
		header('location:'.$cfg[rootDir].'/main/intro_adult_login.php?returnUrl=' . $returnUrl . ($_SERVER['QUERY_STRING'] ? '&'.$_SERVER['QUERY_STRING'] : '')); 
	}
	elseif ($cfg[custom_landingpage] == 3 && !$sess) {	// 회원
		header('location:'.$cfg[rootDir].'/main/intro_member.php?returnUrl=' . $returnUrl . ($_SERVER['QUERY_STRING'] ? '&'.$_SERVER['QUERY_STRING'] : ''));
	}
}

{ // Screen Set

	# 상단타입
	if ( in_array( $data_file['outline_header'], array( '', 'default' ) ) ) $data_file['outline_header'] = $design_skin['default']['outline_header'];
	if ( $data_file['outline_header'] == 'noprint' ) $cfg['outline_header'] = ''; else $cfg['outline_header'] = $data_file['outline_header'];

	# 하단타입
	if ( in_array( $data_file['outline_footer'], array( '', 'default' ) ) ) $data_file['outline_footer'] = $design_skin['default']['outline_footer'];
	if ( $data_file['outline_footer'] == 'noprint' ) $cfg['outline_footer'] = ''; else $cfg['outline_footer'] = $data_file['outline_footer'];

	# 측면타입
	if ( in_array( $data_file['outline_side'], array( '', 'default' ) ) ) $data_file['outline_side'] = $design_skin['default']['outline_side'];
	if ( $data_file['outline_side'] == 'noprint' ) $cfg['outline_side'] = ''; else $cfg['outline_side'] = $data_file['outline_side'];

	# 측면위치
	if ( in_array( $data_file['outline_sidefloat'], array( '', 'default' ) ) ) $data_file['outline_sidefloat'] = $design_skin['default']['outline_sidefloat'];
	if ( $data_file['outline_sidefloat'] == '' ) $cfg['outline_sidefloat'] = 'left'; else $cfg['outline_sidefloat'] = $data_file['outline_sidefloat'];

	# 전체 배경이미지
	if ( $data_file['outbg_img'] == '' ) $cfg['outbg_img'] = !empty($design_skin['default']['outbg_img']) ? $cfg[rootDir] . '/data/' . $headerSkinPath . '/img/codi/' . $design_skin['default']['outbg_img'] : '';
	else { // 투데이샵 스킨 적용 2010.12.29 by slowj
		$cfg['outbg_img'] = $cfg[rootDir] . '/data/' . $curPageSkinPath . '/img/codi/' . $data_file['outbg_img'];
	}
	// 투데이샵 스킨 적용

	# 전체 배경색상
	if ( $data_file['outbg_color'] == '' ) $data_file['outbg_color'] = $design_skin['default']['outbg_color'];
	if ( $data_file['outbg_color'] != '' ) $cfg['outbg_color'] = $data_file['outbg_color'];

	# 본문 배경이미지
	if ( $data_file['inbg_img'] == '' ) $cfg['inbg_img'] = !empty($design_skin['default']['inbg_img']) ? $cfg[rootDir] . '/data/' . $headerSkinPath . '/img/codi/' . $design_skin['default']['inbg_img'] : '';
	else { // 투데이샵 스킨 적용 2010.12.29 by slowj
		$cfg['inbg_img'] = $cfg[rootDir] . '/data/' . $curPageSkinPath . '/img/codi/' . $data_file['inbg_img'];
	}
	// 투데이샵 스킨 적용

	# 본문 배경색상
	if ( $data_file['inbg_color'] == '' ) $data_file['inbg_color'] = $design_skin['default']['inbg_color'];
	if ( $data_file['inbg_color'] != '' ) $cfg['inbg_color'] = $data_file['inbg_color'];

	# 상단 배경이미지
	// 투데이샵 스킨 적용 2010.12.29 by slowj
	if ( $cfg['outline_header'] != '' ) {
		$data_file['topbg_img'] = $design_skin[ $cfg['outline_header'] ]['inbg_img'];
	}
	if ( $data_file['topbg_img'] != '' ) {
		$cfg['topbg_img'] = $cfg[rootDir] . '/data/' . $headerSkinPath . '/img/codi/' . $data_file['topbg_img'];
	}
	// 투데이샵 스킨 적용

	$cfg['shopLineWidthL'] = ( $cfg['shopLineColorL'] != '' ? 1 : 0 ); # 좌측라인크기
	$cfg['shopLineWidthC'] = ( $cfg['shopLineColorC'] != '' ? 1 : 0 ); # 중앙라인크기
	$cfg['shopLineWidthR'] = ( $cfg['shopLineColorR'] != '' ? 1 : 0 ); # 우측라인크기
	$cfg['shopLineColorL'] = ( $cfg['shopLineColorL'] != '' ? $cfg['shopLineColorL'] : '#FFFFFF' ); # 좌측라인색상
	$cfg['shopLineColorC'] = ( $cfg['shopLineColorC'] != '' ? $cfg['shopLineColorC'] : '#FFFFFF' ); # 중앙라인색상
	$cfg['shopLineColorR'] = ( $cfg['shopLineColorR'] != '' ? $cfg['shopLineColorR'] : '#FFFFFF' ); # 우측라인색상
	$cfg['shopSize'] = $cfg['shopOuterSize'] + $cfg['shopLineWidthL'] + $cfg['shopLineWidthC'] + $cfg['shopLineWidthR']; # 전체사이즈
}


{ // 템플릿 파일 정의

	$tpl->define( array(
				'tpl'			=> $key_file,
				'header'		=> 'outline/_header.htm',
				'footer'		=> 'outline/_footer.htm',
				'overture_cc'	=> 'proc/overture_cc.htm',
				'menuCategory'	=> 'proc/menuCategory.htm',
				'>myBox'		=> $_SERVER['DOCUMENT_ROOT']."/$cfg[rootDir]/mypage/_myBox.php",
				'>myBoxLayer'	=> $_SERVER['DOCUMENT_ROOT']."/$cfg[rootDir]/mypage/_myBoxLayer.php",
				'>myLevelLayer'	=> $_SERVER['DOCUMENT_ROOT']."/$cfg[rootDir]/mypage/_myLevelLayer.php",
				'>myCouponLayer'=> $_SERVER['DOCUMENT_ROOT']."/$cfg[rootDir]/mypage/_myCouponLayer.php", ## 2011/09/05 kmn 쿠폰여부 팝업
				'ccsms'			=> 'proc/ccsms.htm',
				'main_header'		=> 'outline/main_header.htm', ## shkim 메인용 헤더
				) );

	// 페이지캐쉬
	$templateCache = Core::loader('TemplateCache', $_SERVER['SCRIPT_NAME']);
	if (!isset($_SESSION['tplSkin']) && $templateCache->isEnabled() && $templateCache->checkCacingPage() && $templateCache->checkCondition()) {
		$templateCache->setCache($tpl);
	}
	if ( $cfg['outline_header'] != '' ) $tpl->define( 'header_inc', $cfg['outline_header'] );
	if ( $cfg['outline_footer'] != '' ) $tpl->define( 'footer_inc', $cfg['outline_footer'] );
	if ( $cfg['outline_side'] != '' && !$isTodayShopPage ) $tpl->define( 'side_inc', $cfg['outline_side'] );
	// 투데이샵 카테고리 템플릿
	if (is_file($_SERVER['DOCUMENT_ROOT'] . $cfg[rootDir] . '/data/skin_today/'.$cfg['tplSkinToday'] . '/proc/tsCategory.htm')) {
		$tpl->define('tsCategory' , '../../skin_today/'.$cfg['tplSkinToday'] . '/proc/tsCategory.htm');
	}

	$tpl->assign( array(
				pfile	=> basename($_SERVER[PHP_SELF]),
				pdir	=> basename(dirname($_SERVER[PHP_SELF])),
				) );
}

### 오늘본상품 // 성인 상품 - 인증 전에는 19 이미지 보여줌 - 인증 후 상품 이미지 보여줌
$todayGoodsList = unserialize(stripslashes($_COOKIE[todayGoods]));
$todayGoodsList_num = count($todayGoodsList);
for($ti=0;$ti<$todayGoodsList_num;$ti++){
	$query = " select use_only_adult from ".GD_GOODS." where goodsno='".$todayGoodsList[$ti]['goodsno']."' ";
	$res = $db->query($query);
	$row = $db->fetch($res,1);
	if($row['use_only_adult'] == '1' && !Clib_Application::session()->canAccessAdult()){
		$todayGoodsList[$ti]['img'] = 'http://' . $_SERVER['HTTP_HOST'] . $cfg['rootDir'] . "/data/skin/" . $cfg['tplSkin'] . '/img/common/19.gif';
	}
}

### 외부 유입의 경우(omi,네이버지식쇼핑,yahooFasionStreet 등) 쿠키생성
if($_GET['inflow']){
	if(strlen($_GET['inflow'])<=15){
		if($_GET['pchsFlag'] == '040901') $_GET['inflow'] = "naver_pchs_040901";
		setcookie("cc_inflow",$_GET['inflow'],0,'/');
		$_COOKIE['cc_inflow'] = $_GET['inflow'];
	}
}

### 외부 유입의 경우(야후패션소호) 쿠키생성
/*
// @삭제 : 삭제된 서비스
if($_GET[ref] == 'yahoo_fss'){
	setcookie("cc_inflow",$_GET[ref],0,'/');

	ob_start();
	@include dirname(__FILE__)."/lib/yahoofss.class.php";
	$yfss = new Yfss( 'sendCounter' ); # 접속카운터 전송
	ob_end_clean();
}
*/

//인터파크에서 넘어 왔을때 쿠키 생성
if($_COOKIE['cc_inflow']!="openstyleOutlink" && $_GET['inpkflow']=="connInterpark"){
	setcookie("cc_inflow","openstyleOutlink",0,'/');
	$_COOKIE['cc_inflow'] = "openstyleOutlink";
}

### 오픈스타일 헤더 호출
if(($_COOKIE['cc_inflow']=="openstyleOutlink" || $_GET['inpkflow']=="connInterpark") && (!preg_match('/goods_qna_list|goods_review_list|goods_view|goods_cart|order.php|order_end.php|mypage_wishlist|popup/',$_SERVER[PHP_SELF]))){
	$systemHeadTagStart .=  "<script src='http://www.interpark.com/malls/openstyle/OpenStyleEntrTop.js'></script>";
}

if(($_COOKIE['cc_inflow']=="auctionos" || $_GET['inflow']=="auctionos")
	&&(!preg_match('/goods_qna_list|goods_review_list|popup/',$_SERVER[PHP_SELF])) ){
	$systemHeadTagStart .= "<script type=\"text/javascript\" src=\"http://www.about.co.kr/Network/CooperBar.aspx?en=euc-kr\"></script>";
}

## 고도웹폰트서비스
$result = $db->_select('select font_code,`use` from gd_webfont where now() between expire_start and expire_end and LENGTH(`use`)');
$webFontCss = '';
foreach((array)$result as $each_font) {
	foreach(explode(',',$each_font['use']) as $each_size) {
		$fontCode = $each_font['font_code'].'_'.sprintf('%02d',$each_size);
		$webFontCss .= "
@font-face {
	font-family: {$fontCode};
	src: url(../proc/fonteot.php?name={$fontCode});
}
.{$fontCode} {font-family:{$fontCode} !important;font-size:{$each_size}pt !important}
		";
	}
}

$godofont = $config->load('godofont');
if($godofont['major_font']) {
	preg_match('/_([0-9]+)$/',$godofont['major_font'],$matches);
	$size = (int)$matches[1];
	$webFontCss .= "
@font-face {
	font-family: {$godofont['major_font']};
	font-style:  normal;
	font-weight: normal;
	src: url(../proc/fonteot.php?name={$godofont['major_font']});
}
* {font-family:{$godofont['major_font']} !important;font-size:{$size}pt !important}
	";
}
if($webFontCss) {
	$systemHeadTagEnd .= "\n<style type='text/css'>\n{$webFontCss}\n</style>\n";
}

### ace카운터
$Acecounter = new Acecounter();
$Acecounter->get_common_script();
if(!preg_match('/today_goods|today_cart|goods_search|goods_view|goods_cart|order_end.php/',$_SERVER[PHP_SELF]) && $Acecounter->scripts ){
	$systemHeadTagEnd .= $Acecounter->scripts;
}

### 어바웃 쿠폰 출력
$about_coupon = Core::loader('about_coupon');
if(!$_COOKIE['about_cp']) $systemHeadTagEnd .= $about_coupon -> about_coupon_popup();

### systemHeadTag
$tpl->assign('systemHeadTagStart',$systemHeadTagStart);
$tpl->assign('systemHeadTagEnd',$systemHeadTagEnd);

### 보안서버용 로긴url
$tpl->assign('loginActionUrl',$sitelink->link('member/login_ok.php','ssl'));

### 보안서버용 제휴안내
$tpl->assign('cooActionUrl',$sitelink->link('service/cooperation.php','ssl'));

// 커스텀 헤더 적용.
$customHeader = '';

if ($cfg['ssl'] == 1 && $cfg['ssl_type'] == "godo" && $cfg['ssl_seal'] == "c" && $cfg['comodo'] != 'b') { //보안서버작동중일때만 사용
	// 보안서버인증씰_COMODO
	$customHeader .= '<script language="javascript" type="text/javascript">
	//<![CDATA[
	var tl_loc0=(window.location.protocol == "https:")? "https://secure.comodo.net/trustlogo/javascript/trustlogo.js" : "http://www.trustlogo.com/trustlogo/javascript/trustlogo.js";
	document.writeln(\'<scr\' + \'ipt language="JavaScript" src="\'+tl_loc0+\'" type="text\/javascript">\' + \'<\/scr\' + \'ipt>\');
	//]]>
	</script>';
}

// 투데이샵 카테고리 정보
if ($todayShop->auth() && $todayShop->cfg['useTodayShop'] == 'y') {
	$arrTsCate = $todayShop->getCategory();
	$ts_category_all = $todayShop->getCategory(true);
	$tpl->assign('ts_category', $arrTsCate);
	if ($_GET['category'] && is_array($arrTsCate) && empty($arrTsCate)===false) {
		foreach($arrTsCate as $key => $val) {
			if ($val['category'] == $_GET['category']) {
				$tpl->assign('ts_curcate', $val);
				break;
			}
		}
		unset($arrTsCate, $key, $val);
	}
}

// 네이버 공통유입 스크립트
@include dirname(__FILE__).'/lib/naverCommonInflowScript.class.php';
$naverCommonInflowScript = new NaverCommonInflowScript();
if ($templateCache->isCached()) {
	if ($naverCommonInflowScript->useNaverService) {
		$customHeader .= '<script type="text/javascript" src="'.($_SERVER['HTTPS']?'https':'http').'://wcs.naver.net/wcslog.js"></script>';
	}
	$customHeader .= $templateCache->getPageUpdateScript();
}
else {
	$customHeader .= $naverCommonInflowScript->getCommonInflowScript();
}
$tpl->assign('naverCommonInflowScript', $naverCommonInflowScript);

$tpl->assign('customHeader', $customHeader);
$tpl->assign('todayshop_cfg', $todayShop->cfg);

// 미확인 입금자 배너 처리
$ghost_cfg = $config->load('ghostbanker');
if (!empty($ghost_cfg)) {

	$ghostBankerBanner = '';

	if ($ghost_cfg['use'] == 1) {

		$ghostBankerBanner .= '<a href="javascript:popup(\''.($cfg['rootDir'].'/service/ghostbanker.php').'\',640,600);">';

		if ( $ghost_cfg['banner_skin_type'] == 'direct' )
			$ghostBankerBanner .= '<img src="'.($cfg['rootDir'] . '/data/ghostbanker/' . $ghost_cfg['banner_file']).'">';
		else
			$ghostBankerBanner .= '<img src="'.($cfg['rootDir'] . '/data/ghostbanker/tpl/_banner_' . $ghost_cfg['banner_skin'].'.jpg').'">';

		$ghostBankerBanner .= '</a>';
	}

	$tpl->assign('ghostBankerBanner', $ghostBankerBanner);
}

// 회원 등급
$member_grp = Core::loader('member_grp');
$member_grp_ruleset = $member_grp->ruleset;
$member_grp->checkUpdate();

$tpl->assign('useMypageLayerBox', $member_grp_ruleset['useMypageLayerBox']);

// 2011/09/02 쿠폰팝업알림 kmn
$coupon_check = Core::loader('coupon_check');
$hasCoupon = $coupon_check->exists_alertcoupon();
$tpl->assign('alertCoupon', $hasCoupon);

// 인기상품
$populate = Core::loader('populate');
$tpl->define('populate','proc/'.($populate->cfg['design'] != 'rollover' ? 'populate_1.htm' : 'populate_2.htm'));
$tpl->setScope('populate');
$tpl->assign('populate_list', $populate->getData());
$tpl->assign('populate_cfg', $populate->cfg);
$tpl->setScope();

// 스마트검색
if(preg_match('/goods_list\.php/', $_SERVER['PHP_SELF']) && $cfg['smartSearch_useyn'] == 'y') {
	$smartSearch_useyn = 'y';
	$tpl->assign('smartSearch_useyn', $smartSearch_useyn);

	$smartSearch = Core::loader('smartSearch');
	$tpl->define('smartSearch','proc/smartSearch.htm');
	$tpl->assign('searchList', $smartSearch->loadTheme());
	$ssState = $smartSearch->setState();
	$tpl->assign('ssState', $ssState);
}
else {
	$smartSearch_useyn = 'n';
	$tpl->assign('smartSearch_useyn', $smartSearch_useyn);
}

//페이스북 연동 치환코드
$fb = new Facebook();
$tpl->assign('facecmt', $fb->comment());
$tpl->assign('facepage', $fb->likebox());
$tpl->assign('fbbnr', $fb->fbButton());
$tpl->assign('fb', $fb);

//하단주소
if($cfg['road_address']) {
  	$cfg['old_address'] = $cfg['address'];
  	$cfg['address']		= $cfg['road_address'];
} else {
  	$cfg['old_address'] = $cfg['address'];
  	$cfg['address']		= $cfg['address'];
}

$tpl->assign('$cfg[address]', $cfg[address]); //주소 (도로명 우선)
$tpl->assign('$cfg[old_address]', $cfg[address]); //(구)지번주소
$tpl->assign('$cfg[road_address]', $cfg[road_address]); //(신)도로명주소

include dirname(__FILE__).'/lib/SocialMember/SocialMemberServiceLoader.php';
if ($socialMemberService->isEnabled()) {
	$tpl->assign('SocialMemberEnabled', true);

	$scriptFile = array_pop(explode('/', $_SERVER['SCRIPT_NAME']));
	$returnURL = null;
	if ($scriptFile === 'login.php') {
		$returnURL = urlencode($_SERVER['HTTP_REFERER']);
	}

	$socialMemberServiceList = $socialMemberService->getEnabledServiceList();
	if ($scriptFile === 'login.php' || $scriptFile === 'intro_member.php') {
		if (in_array(SocialMemberService::FACEBOOK, $socialMemberServiceList)) {
			$facebookMember = SocialMemberService::getMember(SocialMemberService::FACEBOOK);
			$tpl->assign('FacebookLoginURL', $facebookMember->getLoginURL($returnURL));
		}
	}
}

$tpl->assign('jQueryPath', $cfg['rootDir'].'/lib/js/jquery-1.10.2.min.js');

?>