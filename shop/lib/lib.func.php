<?

/*****************************
 * 환경 변수 정의
 *****************************/

### 테이블 디파인
$r_table = array(
	'gd_banner',
	'gd_board',
	'gd_board_category',
	'gd_board_inf',
	'gd_board_memo',
	'gd_bd_',
	'gd_cashreceipt',
	'gd_category',
	'gd_code',
	'gd_cooperation',
	'gd_coupon',
	'gd_coupon_apply',
	'gd_coupon_applymember',
	'gd_coupon_category',
	'gd_coupon_goodsno',
	'gd_coupon_member',
	'gd_coupon_mgrp',
	'gd_coupon_order',
	'gd_diaryAlarm',
	'gd_diaryContent',
	'gd_dopt',
	'gd_event',
	'gd_faq',
	'gd_favorite_address',
	'gd_goods',
	'gd_goods_add',
	'gd_goods_brand',
	'gd_goods_display',
	'gd_goods_display_mobile',
	'gd_goods_favorite_reply',
	'gd_goods_link',
	'gd_goods_option',
	'gd_goods_smart_search',
	'gd_goods_update_naver',
	'gd_goods_qna',
	'gd_goods_review',
	'gd_goods_stocked_noti',
	'gd_goods_related',
	'gd_list_bank',
	'gd_list_delivery',
	'gd_log_cancel',
	'gd_log_email',
	'gd_log_emoney',
	'gd_log_hack',
	'gd_member',
	'gd_member_crm',
	'gd_member_grp',
	'gd_member_grp_ruleset',
	'gd_member_grp_schedule',
	'gd_member_grp_changed_log',
	'gd_member_qna',
	'gd_member_wishlist',
	'gd_openmarket_goods',
	'gd_openmarket_goods_option',
	'gd_attendance',
	'gd_order',
	'gd_order_cancel',
	'gd_order_del',
	'gd_order_item',
	'gd_order_item_log',
	'gd_order_log',
	'gd_order_temp',
	'gd_search',
	'gd_sms_log',
	'gd_sms_sample',
	'gd_sms_address',
	'gd_tax',
	'gd_yahoofss',
	'inpk_claim',
	'inpk_claim_item',
	'inpk_claim_item_log',
	'linkprice_order',
	'mini_counter',
	'mini_ip_browser',
	'mini_ip_os',
	'mini_referer',
	'mini_ip',
	'gd_navercheckout',
	'gd_navercheckout_agreement',
	'gd_navercheckout_item',
	'gd_dopt_extend',
	'gd_todayshop_goods',
	'gd_todayshop_goods_review',
	'gd_todayshop_goods_merged',
	'gd_todayshop_talk',
	'gd_todayshop_encor',
	'gd_todayshop_company',
	'gd_todayshop_category',
	'gd_todayshop_link',
	'gd_todayshop_order_coupon',
	'gd_todayshop_subscribe',
	'gd_qrcode',
	'gd_ghostbanker',
	'gd_cart',
	/* 셀리 */
	'gd_goods_stlog',
	/* 쇼플 */
	'gd_shople_goods_map',
	'gd_shople_category_map',
	'gd_shople_category',
	'gd_shople_goods',
	'gd_shople_goods_option',
	'gd_shople_origin_code',
	/* 사입처 */
	'gd_purchase',
	'gd_purchase_goods',
	'gd_purchase_smslog',
	/* 관리자 퀵 메뉴 */
	'gd_contextmenu',
	/* 오프라인 쿠폰 */
	'gd_offline_download',
	'gd_offline_coupon',
	'gd_offline_goods',
	/* 오픈마켓관리 - 포셀러 */
	'gd_goods_openmarket',
	/* 상품 페이지 뷰 */
	'gd_goods_pageview',
	'gd_navercheckout_order',
	'gd_navercheckout_order_product',

	/* 주문 통합 */
	'gd_integrate_order',
	'gd_integrate_order_item',
	'gd_auctionipay',
	'gd_auctionipay_item',

	/* 쿠폰 팝업알림 */
	'gd_coupon_check_member',

	/* 샵터치 */
	'gd_shoptouch_goods',
	'gd_shoptouch_display',

	/* 굿스플로 */
	'gd_goodsflow',
	'gd_goodsflow_order_map',

	/* 네이버 체크아웃 4.0 */
	'gd_navercheckout_CancelInfo',
	'gd_navercheckout_DecisionHoldbackInfo',
	'gd_navercheckout_DeliveryInfo',
	'gd_navercheckout_ExchangeInfo',
	'gd_navercheckout_OrderInfo',
	'gd_navercheckout_ProductOrderInfo',
	'gd_navercheckout_ReturnInfo',
	'gd_navercheckout_PurchaseReview',

	/* SELLY */
	'gd_market_goods',
	'gd_market_order',
	'gd_market_order_item',

	/* 비번 찾기용 OTP */
	'gd_otp',

	/* 우체국 택배 */
	'gd_godopost_reserved',

	/* 모바일샵 */
	'gd_mobile_design',
	'gd_mobile_display',
	'gd_mobile_event',
	'gd_mobile_popup',
	'gd_mobile_visit',
	'gd_mobile_analysis',

	'gd_env',
	'gd_attendance_check',
	'gd_attendance_comment',
	'gd_log_aboutcoupon',
	'gd_navercheckout_inquiry',
	'gd_navercheckout_inquiry_item',
	'gd_offline_paper',
	'gd_order_okcashbag',
	'gd_webfont',

	/* 코디 상품 */
	'gd_set_cody',
	'gd_set_comment',
	'gd_set_image',
	'gd_set_template',

	/* 관리자로그 */
	'gd_admin_log',

	/* 모바일샵 팝업 */
	'gd_mobilev2_popup',

	/* 상품공통정보관리 */
	'gd_common_info',

	/*config*/
	'gd_goods_discount',

	/* 아이룩상품관리 */
	'gd_eyelook',

	/* 지역별 배송비 */
	'gd_area_delivery',

	/* SMS 발송내역 */
	'gd_sms_sendlist',
	'gd_sms_faillist',

	/* 주문배송비 내역/변경이력 */
	'gd_order_item_delivery',
	'gd_order_item_delivery_log',

	/* 페이코 */
	'gd_payco_transmit_log',
	'gd_payco_receive_log',

	/* 장바구니 리마인드 */
	'gd_cart_reminder',

	/* 추가 동의 항목 */
	'gd_consent',
	'gd_member_consent',
	
	// 다음 쇼핑하우 요약 EP
	'gd_goods_update_daum',

	// 다음 쇼핑하우 상품평 EP
	'gd_goods_update_review_daum',
	
	// 컴백쿠폰/SMS
	'gd_comeback_coupon',

);

foreach($r_table as $v)	define(strtoupper($v),$v);
unset($r_table);

$r_step			= array(
				0	=> "주문접수",
				1	=> "입금확인",
				2	=> "배송준비중",
				3	=> "배송중",
				4	=> "배송완료"
				);
$r_step2		= array(
				0	=> "",
				40	=> "취소요청",
				41	=> "취소접수",
				42	=> "취소진행",
				44	=> "취소완료",
				50	=> "결제시도",
				51	=> "PG확인요망",
				54	=> "결제실패",
				);

$r_stepi		= array(
				0	=> array(
						0	=> "주문접수",
						40	=> "취소요청",
						41	=> "취소접수",
						42	=> "취소진행",
						44	=> "취소완료",
						50	=> "결제시도",
						51	=> "PG확인요망",
						54	=> "결제실패",
						),
				1	=> array(
						0	=> "입금확인",
						40	=> "환불요청",
						41	=> "환불접수",
						44	=> "환불완료",
						),
				2	=> array(
						0	=> "배송준비중",
						40	=> "환불요청",
						41	=> "환불접수",
						44	=> "환불완료",
						),
				3	=> array(
						0	=> "배송중",
						40	=> "반품요청",
						41	=> "반품접수",
						42	=> "환불접수",
						44	=> "환불완료",
						),
				4	=> array(
						0	=> "배송완료",
						40	=> "반품요청",
						41	=> "반품접수",
						42	=> "환불접수",
						44	=> "환불완료",
						),
				);

$r_istep		= array(
				0	=> "주문접수",
				1	=> "입금확인",
				2	=> "배송준비중",
				3	=> "배송중",
				4	=> "배송완료",
				41	=> "취소접수",
				42	=> "취소진행",
				44	=> "취소완료",
				50	=> "결제시도",
				51	=> "PG확인요망",
				54	=> "결제실패",
				);

$r_settlekind	= array(
				"a"	=> "무통장",
				"c"	=> "신용카드",
				"o"	=> "계좌이체",
				"v"	=> "가상계좌",
				"d"	=> "전액할인",
				"h"	=> "핸드폰",
				"p"	=> "포인트",
				"u" => "신용카드 (중국)",
				"y" => "옐로페이",
				"e" => "페이코 포인트",
				);
$r_cyn			= array(
				"y"	=> "결제확인",
				"n"	=> "결제미확인",
				"r"	=> "환불",
				);

$r_inflow			= array(
				"naver"		=> "네이버지식쇼핑",
				"naver_elec"	=> "네이버가격비교",
				"naver_bea"	=> "네이버가격비교",
				"naver_milk"	=> "네이버가격비교",
				"danawa"		=> "다나와",
				"mm"		=> "마이마진",
				"bb"			=> "베스트바이어",
				"omi"		=> "오미",
				"enuri"		=> "에누리",
				"yahoo_fss"	=> "야후패션소호",
				"yahoo"		=> "야후가격비교",
				"interpark"	=> "인터파크샵플러스",
				"openstyle"	=> "인터파크오픈스타일",
				"openstyleOutlink"	=> "오픈스타일아웃링크",
				"naver_pchs_040901"	=> "네이버지식쇼핑추천광고",
				);

$default[orderXls] = array(
	array('번호','no','순번 표시','checked'),
	array('주문번호','ordno','주문번호 표시','checked'),
	array('주문자명','nameOrder','주문자명 표시','checked'),
	array('주문자아이디','m_id','주문자아이디 표시','checked'),
	array('상품명','goodsnm','2개이상인경우 외 갯수로 표시됨','checked'),
	array('이메일','email','이메일 표시','checked'),
	array('주문자전화번호','phoneOrder','주문자 전화번호 표시','checked'),
	array('주문자핸드폰','mobileOrder','주문자 핸드폰 표시','checked'),
	array('받는분이름','nameReceiver','받는분 이름 표시','checked'),
	array('받는분전화번호','phoneReceiver','받는분 전화번호 표시','checked'),
	array('받는분핸드폰','mobileReceiver','받는분 핸드폰번호 표시','checked'),
	array('우편번호','zipcode_','우편번호 표시(새 우편번호 우선 표시)','checked'),
	array('(구)우편번호','zipcode','하이픈(-) 를 포함한 7자리의 우편번호 표시','checked'),
	array('(새)우편번호','zonecode','5자리의 새 우편번호(국가기초구역번호) 표시','checked'),
	array('주소','address_','주소 표시 (도로명주소 우선 표시)','checked'),
	array('(구)지번주소','address','(구)지번주소 표시','checked'),
	array('(신)도로명주소','road_address','(신)도로명주소 표시','checked'),
	array('배송메세지','order_memo','배송메세지 표시','checked'),
	array('결제수단','settlekind','결제방법 표시','checked'),
	array('결제금액','settleprice','결제금액 표시','checked'),
	array('주문일자','orddt','주문한 일자를 표시 예)2007-10-10','checked'),
	array('주문상태','step','주문상태 표시','checked'),
	array('배송코드','deliveryno','배송사코드표시','checked'),
	array('송장번호','deliverycode','송장번호표시','checked'),
	array('착불여부','deli_type','착불여부표시','checked'),
	array('배송일','ddt','배송일자 표시 예)2007-10-10 18:00:00','checked'),
	array('어바웃쿠폰','about_dc_sum','어바웃쿠폰할인금액','checked'),
	array('배송완료일','confirmdt','배송완료일자 표시 예)2011-11-30 10:00:00','checked'),
	array('입금자','bankSender','입금자명 표시','checked'),
);

$default[orderGoodsXls] = array(
	array('번호','no','순번 표시','checked'),
	array('일련번호','sno','주문상품의 일련번호','checked'),
	array('주문번호','ordno','주문번호 표시','checked'),
	array('주문자명','nameOrder','주문자명 표시','checked'),
	array('주문자아이디','m_id','주문자아이디 표시','checked'),
	array('이메일','email','이메일 표시','checked'),
	array('주문자전화번호','phoneOrder','주문자 전화번호 표시','checked'),
	array('주문자핸드폰','mobileOrder','주문자 핸드폰 표시','checked'),
	array('받는분이름','nameReceiver','받는분 이름 표시','checked'),
	array('받는분전화번호','phoneReceiver','받는분 전화번호 표시','checked'),
	array('받는분핸드폰','mobileReceiver','받는분 핸드폰번호 표시','checked'),
	array('우편번호','zipcode_','우편번호 표시(새 우편번호 우선 표시)','checked'),
	array('(구)우편번호','zipcode','하이픈(-) 를 포함한 7자리의 우편번호 표시','checked'),
	array('(새)우편번호','zonecode','5자리의 새 우편번호(국가기초구역번호) 표시','checked'),
	array('주소','address_','주소 표시 (도로명주소 우선 표시)','checked'),
	array('(구)지번주소','address','(구)지번주소 표시','checked'),
	array('(신)도로명주소','road_address','(신)도로명주소 표시','checked'),
	array('배송메세지','order_memo','배송메세지 표시','checked'),
	array('결제수단','settlekind','결제방법 표시','checked'),
	array('주문일자','orddt','주문한 일자를 표시 예)2007-10-10','checked'),
	array('주문상태','step','주문상태 표시','checked'),
	array('상품명','goodsnm','주문 상품명 표시','checked'),
	array('상품코드','goodscd','주문 상품코드 표시','checked'),
	array('옵션','opt','주문 상품옵션 표시','checked'),
	array('제조사','maker','주문 상품 제조사 표시','checked'),
	array('원산지','origin','주문 상품 원산지 표시','checked'),
	array('브랜드','brandnm','주문 상품 브랜드 표시','checked'),
	array('수량','ea','주문 상품 수량 표시','checked'),
	array('매입가','supply','주문 상품 매입가 표시','checked'),
	array('상품가격','price','주문 상품가격 표시','checked'),
	array('결제가격','sprice','주문 상품 결제가격 표시','checked'),
	array('어바웃쿠폰할인단가','about_dc_price','어바웃쿠폰할인단가','checked'),
	array('배송코드','deliveryno','배송사표시','checked'),
	array('송장번호','deliverycode','송장번호표시','checked'),
	array('착불여부','deli_type','착불여부표시','checked'),
	array('배송일','ddt','배송일자 표시 예)2007-10-10 18:00:00','checked'),
	array('배송완료일','confirmdt','배송완료일자 표시 예)2011-11-30 10:00:00','checked'),
	array('입금자','bankSender','입금자명 표시','checked'),
);

$default[orderTodayGoodsXls] = array(
	array('번호','no','순번 표시','checked'),
	array('주문번호','ordno','주문번호 표시','checked'),
	array('주문자명','nameOrder','주문자명 표시','checked'),
	array('주문자아이디','m_id','주문자아이디 표시','checked'),
	array('상품명','goodsnm','2개이상인경우 외 갯수로 표시됨','checked'),
	array('이메일','email','이메일 표시','checked'),
	array('주문자전화번호','phoneOrder','주문자 전화번호 표시','checked'),
	array('주문자핸드폰','mobileOrder','주문자 핸드폰 표시','checked'),
	array('받는분이름','nameReceiver','받는분 이름 표시','checked'),
	array('받는분전화번호','phoneReceiver','받는분 전화번호 표시','checked'),
	array('받는분핸드폰','mobileReceiver','받는분 핸드폰번호 표시','checked'),
	array('우편번호','zipcode','우편번호 표시','checked'),
	array('주소','address','주소 표시','checked'),
	array('배송메세지','order_memo','배송메세지 표시','checked'),
	array('결제수단','settlekind','결제방법 표시','checked'),
	array('결제금액','settleprice','결제금액 표시','checked'),
	array('주문일자','orddt','주문한 일자를 표시 예)2007-10-10','checked'),
	array('주문상태','step','주문상태 표시','checked'),
	array('배송코드','deliveryno','배송사코드표시','checked'),
	array('송장번호','deliverycode','송장번호표시','checked'),
	array('착불여부','deli_type','착불여부표시','checked'),
	array('배송일','ddt','배송일자 표시 예)2007-10-10 18:00:00','checked'),
	array('배송완료일','confirmdt','배송완료일자 표시 예)2011-11-30 10:00:00','checked'),
);

$default[orderTodayCouponXls] = array(
	array('번호','no','순번 표시','checked'),
	array('주문번호','ordno','주문번호 표시','checked'),
	array('주문자명','nameOrder','주문자명 표시','checked'),
	array('주문자아이디','m_id','주문자아이디 표시','checked'),
	array('상품명','goodsnm','2개이상인경우 외 갯수로 표시됨','checked'),
	array('이메일','email','이메일 표시','checked'),
	array('주문자전화번호','phoneOrder','주문자 전화번호 표시','checked'),
	array('주문자핸드폰','mobileOrder','주문자 핸드폰 표시','checked'),
	array('받는분이름','nameReceiver','받는분 이름 표시','checked'),
	array('받는분핸드폰','mobileReceiver','받는분 핸드폰번호 표시','checked'),
	array('배송메세지','order_memo','배송메세지 표시','checked'),
	array('결제수단','settlekind','결제방법 표시','checked'),
	array('결제금액','settleprice','결제금액 표시','checked'),
	array('주문일자','orddt','주문한 일자를 표시 예)2007-10-10','checked'),
	array('주문상태','step','주문상태 표시','checked'),
	array('쿠폰번호','cp_num','쿠폰번호 표시','checked'),
	array('쿠폰수량','ea','쿠폰수량 표시','checked'),
	array('유효기간','usedt','유효기간 표시','checked'),
);


$r_couponType = array('운영자발급','회원직접다운로드','회원가입자동발급','구매후 자동발급');
$r_couponAbility = array( 0 => '할인', 1 => '적립');
$r_couponMemberType = array( 0 => '전체회원발급', 1 => '그룹별발급', 2 => '회원개별발급');
$r_smsType = array(
					0=>'자동발송',
					1=>'개별발송',
					2=>'회원 주소록 검색 목록',
					3=>'회원 주소록 선택 목록',
					4=>'일반 주소록 검색 목록',
					5=>'일반 주소록 선택 목록',6=>'회원 주소록 전체 목록',7=>'일반 주소록 전체 목록',
					14=>'검색한 투데이샵 구독자 목록',
					15=>'선택한 투데이샵 구독자 목록',
					20=>'상품 재입고 신청자 목록'
					);

// SMS / Mail의 발송 대상 코드, 기본 기간 설정, 기본값 처리
$r_sendDateCode		= array(
		'sms'	=> array('order','incash','account','delivery','dcode','cancel','repay'),
		'mail'	=> array('0','1','3'),
);
$r_sendDatePeriod		= array(
		'sms'	=> array(3, 7, 15, 30, 90),
		'mail'	=> array(3, 7, 15, 30, 90),
);
$r_sendDateDefault		= array(
		'sms'	=> 15,
		'mail'	=> 15,
);

/*****************************
 * 함수 라이브러리
 *****************************/

function byte2str($k)
{
	$x = ($k>1024) ? round($k/1024) : round($k/1024,2);		// K단위
	$y = round($x / 1024);		// M단위

	if ($y>999){
		$x = round($y / 1024,2);
		$s = "G";
	}
	else if ($x>999){
		$x = round($x / 1024,2);
		$s = "M";
	} else $s = "K";
	return $x.$s;
}

function mb2byte($x)
{
	$x *= 1024 * 1024;
	return $x.$s;
}

function betweenDate($sdate,$edate)
{
	$s = strtotime($sdate);
	$e = strtotime($edate);
	return ($e - $s) / (24*60*60);
}

### 계정용량 계산
function setDu($key='')
{
	$duPath = dirname(__FILE__) . "/../conf/du.php";
	$shopPath = dirname(__FILE__) . "/../";

	if (file_exists($duPath) === false) $mode = 'all';
	else if (date('Ymd', filectime($duPath)) != date('Ymd')) $mode = 'all';
	else if (date('Ymd', filectime($duPath)) == date('Ymd') && $key == '') return;

	@include $duPath;
	if ($mode != 'all' && $key) $dutmp = $du;

	# data 폴더용량 계산
	if ($mode == 'all'){
		$ret = explode(chr(10), `du -hb --max-depth=1 {$shopPath}/data/`); # data
		foreach ($ret as $v){
			$div = explode("\t", $v);
			$div[1] = str_replace("{$shopPath}/data/", "", $div[1]);
			if (strpos($div[1],'/') === false && $div[1]) $du[disks][$div[1]] = $div[0];
		}
	}
	else if ($key != '' && is_dir("{$shopPath}/data/{$key}")) {
		$du[disks][$key] = intval(`du -sb {$shopPath}/data/{$key}/`);
	}
	else if (($stat = @stat("{$shopPath}/data/{$key}")) !== false) {
		$user = posix_getpwuid($stat['uid']);
		if ($user['name'] == 'nobody') $du[disks][$key] = intval(`du -sb {$shopPath}/data/{$key}/`);
	}

	# 폴더용량 저장
	if(is_file($duPath)) unlink($duPath);
	$fp = fopen($duPath,"w");
	fwrite($fp,"<? \n");
	fwrite($fp,"\$du = array( \n");
	fwrite($fp,"'disk' => '" . array_sum($du[disks]) . "', \n");
	fwrite($fp,"'disks' => array( \n");
	foreach ($du[disks] as $k => $v) fwrite($fp,"\t'{$k}' => '{$v}', \n");
	fwrite($fp,"\t), \n");
	fwrite($fp,") \n;");
	fwrite($fp,"?>");
	fclose($fp);
	@chmod($duPath,0707);
}

### 계정용량 리턴
function getDu($key='')
{
	setDu();
	include dirname(__FILE__) . "/../conf/du.php";
	return ($key ? $du[$key] : $du);
}

### 날짜 출력형식
function toDate($date,$div)
{
	return sprintf("%04d{$div}%02d{$div}%02d",substr($date,0,4),substr($date,4,2),substr($date,6,2));
}

### 주문 로그
function orderLog($ordno,$memo)
{
	global $db;
	$db->query("insert into ".GD_ORDER_LOG." set ordno='$ordno',memo='$memo',regdt=now()");
}

### 썸네일 이미지 배열 만들기 (array_map)
function toThumb($src)
{
	if (is_file(dirname(__FILE__)."/../data/goods/t/$src")) $dir = "t/";
	return $dir.$src;
}

function slashes($var)
{
	if (get_magic_quotes_gpc()) $var = stripslashes($var);
	return htmlspecialchars($var);
}

### 상품구매적립금
function setGoodsEmoney($ordno,$mode)
{
	global $db,$set;
	if(!$set[emoney])@include dirname(__FILE__)."/../conf/config.pay.php";

	$query = "select * from ".GD_ORDER." where ordno='$ordno'";
	$data = $db->fetch($query);

	if($set['emoney']['useyn'] == 'n') return;
	if($set[emoney][limit] == 1 && $data[emoney]) return;	//적립금 결제시 상품 적립금 미지급
	if (!$data[m_no]) return;

	// 네이버 마일리지를 사용했거나, 둘다 적립안함 선택시 적립금 미지급
	if($data['ncash_save_yn']=='y' || $data['ncash_save_yn']=='u') return;

	### 취소상품 적립금 제외
	$query = "select sum( reserve * ea ) from ".GD_ORDER_ITEM." where ordno='$ordno' and istep>40";
	list($gap) = $db->fetch($query);

	$reserve = ($data[reserve] - $gap) * $mode;
	if (!$reserve) return;
	$msg = ($mode>0) ? "구매완료로 인해 구매적립금 적립" : "구매취소로 인해 구매적립금 환원";

	// 적립금 지급/차감
	setEmoney($data['m_no'],$reserve,$msg,$ordno);

}

### 일반 적립금
function setEmoney($m_no,$emoney,$msg,$ordno='')
{
	global $db;

	if ($ordno) {

		// 차감/적립 카운트가 없거나, 쌍을 이룰 경우에만 지급
		// 차감/적립 카운트중 적립 카운트가 더 클때에만 차감

		if ($msg == '구매취소로 인해 구매적립금 환원') {
			$_msg = "구매완료로 인해 구매적립금 적립";
		}
		elseif ($msg == '구매완료로 인해 구매적립금 적립') {
			$_msg = "구매취소로 인해 구매적립금 환원";
		}
		elseif ($msg == '쿠폰 적립금 환원') {
			$_msg = "쿠폰 적립금 적립";
		}
		elseif ($msg == '쿠폰 적립금 적립') {
			$_msg = "쿠폰 적립금 환원";
		}
		else {
			$_msg = "";
		}

		if ($_msg != "") {

			$query = "
			SELECT
				COUNT( IF(emoney > 0, 1, null) ) AS add_cnt,
				COUNT( IF(emoney < 0, 1, null) ) AS sub_cnt

			FROM gd_log_emoney

			WHERE m_no = '".$m_no."' AND ABS(emoney) = '".abs($emoney)."' AND ordno = '".$ordno."'
			AND (memo = '".$_msg."' OR memo = '".$msg."')
			";

			$tmp = $db->fetch($query,1);

			if ($emoney > 0) {
				$chk = ($tmp['add_cnt'] == $tmp['sub_cnt']) ? true : false;
			}
			else {
				$chk = ($tmp['add_cnt'] > $tmp['sub_cnt']) ? true : false;
			}

		}
		else {
			$chk = true;
		}

	}
	else {
		$chk = true;
	}

	if ($chk === true) {
		$dormantMember = false;
		$dormant = Core::loader('dormant');
		$dormantMember = $dormant->checkDormantMember(array('m_no'=>$m_no), 'm_no');

		if($dormantMember === true){
			$dormantEmoneyQuery = $dormant->getEmoneyUpdateQuery($m_no, $emoney);
			$db->query($dormantEmoneyQuery);
		}
		else {
			$db->query("update ".GD_MEMBER." set emoney=emoney+$emoney where m_no='$m_no'");
		}

		$query = "
		insert into ".GD_LOG_EMONEY." set
			m_no	= '$m_no',
			ordno	= '$ordno',
			emoney	= '$emoney',
			memo	= '$msg',
			regdt	= now()
		";
		$db->query($query);
	}


}

### 디버그 함수
function debug($data)
{
	print "<xmp style=\"display:block;font:9pt 'Bitstream Vera Sans Mono, Courier New';background:#202020;color:#D2FFD2;padding:10px;margin:5px;\">";
	print_r($data);
	print "</xmp>";
}

function drawTable($data)
{
	$keys = array_keys($data[0]);
	$ret  = "<table border=1 bordercolor=#cccccc style='border-collapse:collapse' style='font:8pt tahoma'>";
	$ret .= "<tr bgcolor=#f7f7f7><th>".implode("</th><th>",$keys)."</th></tr>";
	foreach ($data as $v) $ret .= "<tr><td>".implode("</td><td>",$v)."</td></tr>";
	$ret .= "</table>";
	echo $ret;
}

### 쿼리 출력문 확인
function drawQuery($query,$mode=0)
{
	global $db;
	if ($mode) $query = "explain ".$query;
	$res = $db->query($query);
	while ($data=$db->fetch($res,1)) $loop[] = $data;
	drawTable($loop);
}

### GET/POST변수 자동 병합
function getVars($except='', $request='')
{
	if ($except) $exc = explode(",",$except);
	if ( is_array( $request ) == false ) $request = $_REQUEST;
	foreach ($request as $k=>$v){
		if (isset($_COOKIE[$k])) continue; # 쿠키 제외(..sunny)
		if (!@in_array($k,$exc) && $v!=''){
			if (!is_array($v)) $ret[] = "$k=".urlencode(stripslashes($v));
			else {
				$tmp = getVarsSub($k,$v);
				if ($tmp) $ret[] = $tmp;
			}
		}
	}
	if ($ret) return implode("&",$ret);
}

function getVarsSub($key,$value)
{
	foreach ($value as $k2=>$v2){
		if ($v2!='') $ret2[] = $key."[".$k2."]=".urlencode(stripslashes($v2));
	}
	if ($ret2) return implode("&",$ret2);
}

### 문자열 자르기 함수
function strcut($str,$len)
{
	if (strlen($str) > $len){
		$len = $len-2;
		for ($pos=$len;$pos>0 && ord($str[$pos-1])>=127;$pos--);
		if (($len-$pos)%2 == 0) $str = substr($str, 0, $len) . "..";
		else $str = substr($str, 0, $len+1) . "..";
	}
	return $str;
}

function strcut_by_char($instr,$len,$r_pad='')
{
	$output = '';

	if (!empty($r_pad)) {

		for ($i=0,$m=strlen($r_pad);$i<$m;$i++) {
			$char = $r_pad[$i];
			$len = $len - ((ord($char) >= 127) ? 0.5 : 1);	// 2byte or 1byte.
		}

	}

	for ($i=0,$m=strlen($instr);$i<$m && $len > 0;$i++) {
		$char = $instr[$i];
		$len = $len - ((ord($char) >= 127) ? 0.5 : 1);	// 2byte or 1byte.

		$output .= $char;
	}

	return $output.$r_pad;

}

### 예외 처리
function msg($msg,$code=null,$target='')
{
	echo '<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">';
	echo "<script>alert('$msg')</script>";
	switch (getType($code)){
		case "null":
			return;
		case "integer":
			if ($code) echo "<script>history.go($code)</script>";
			exit;
		case "string":
			if ($code=="close") echo "<script>window.close()</script>";
			else if($code=='parentClose') {
				echo "<script>parent.window.close()</script>";
			}
			else go($code,$target);
			exit;
	}
}

### 페이지 이동
function go($url,$target='')
{
	if ($target) $target .= ".";
	//echo '<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">';
	echo "<script>{$target}location.replace('$url')</script>";
	exit;
}

### 시간 측정
function get_microtime($old,$new)
{
	$old = explode(" ", $old);
	$new = explode(" ", $new);
	$time[msec] = $new[0] - $old[0];
	$time[sec]  = $new[1] - $old[1];
	if($time[msec] < 0) {
		$time[msec] = 1.0 + $time[msec];
		$time[sec]--;
	}
	$ret = $time[sec] + $time[msec];
	return $ret;
}

function viewTime($rtime)
{
	$cnt = count($rtime);
	for ($i=1;$i<$cnt;$i++) $ret[] = get_microtime($rtime[$i-1],$rtime[$i]);
	echo "
	<table width=150 border=1 bordercolor='#cccccc' style='border-collapse:collapse' style='font:8pt tahoma'>
	<tr bgcolor='#f7f7f7'>
		<th width=40 nowrap>no</th>
		<th width=100%>time</th>
	</tr>
	<col align=center span=2>
	";
	foreach ($ret as $k=>$v) echo "<tr><td>".($k+1)."</td><td>$v</td></tr>";
	if ($cnt>2){
		$total = get_microtime($rtime[0],$rtime[$cnt-1]);
		echo "<tr bgcolor='#f7f7f7'><td>total</td><td>$total</td></tr>";
	}
	echo "</table>";
}


### 배열 null 제거 함수
function array_notnull($arr)
{
	if (!is_array($arr)) return;
	foreach ($arr as $k=>$v) if ($v=="") unset($arr[$k]);
	return $arr;
}

### 이미지 사이즈 조절
function ImgSizeSet($ImgName,$getWSize="",$getHSize="",$oriWSize="",$oriHSize=""){

	if($oriWSize != "" && $oriHSize != ""){
		$ImgSize[0]	= $oriWSize;
		$ImgSize[1]	= $oriHSize;
	}else{
		$ImgSize	= @getimagesize($ImgName);
	}

	if($getWSize&&$getHSize){
		$PreWidth	= $getWSize;
		$PreHeight	= $getHSize;
	}
	else {
		$PreWidth	=$ImgSize[0];
		$PreHeight	=$ImgSize[1];
	}

	if ($ImgSize[0] >= $PreWidth && $ImgSize[1] >= $PreHeight)
	{
		$height = $PreWidth * $ImgSize[1] / $ImgSize[0];
		$width = $PreHeight * $ImgSize[0] / $ImgSize[1];

		if($width >= $PreWidth && $height <= $PreHeight){
			$width = $PreWidth;
			$height = $width * $ImgSize[1] / $ImgSize[0];
		}
		if($width <= $PreWidth && $height >= $PreHeight){
			$height = $PreHeight;
			$width = $height * $ImgSize[0] / $ImgSize[1];
		}
	}else if ($ImgSize[0] >= $PreWidth || $ImgSize[1] >= $PreHeight){
		if($ImgSize[0] >= $PreWidth){
			$width = $PreWidth;
			$height = $width * $ImgSize[1] / $ImgSize[0];
		}
		if($ImgSize[1] >= $PreHeight){
			$height = $PreHeight;
			$width = $height * $ImgSize[0] / $ImgSize[1];
		}
	}else{
		$width = $ImgSize[0];
		$height = $ImgSize[1];
	}

	if(!$width || !$height){
		$width = $PreWidth;
		$height = $PreHeight;
	}

	$ReSizeImg = array($width,$height);

	return $ReSizeImg;
}

### 썸네일 함수
function thumbnail($src,$folder,$sizeX=100,$sizeY=100,$fix=0,$quality=100)
{
	if ( !eregi('http://',$src) ){
		if(!is_file($src)) return;
	}else{
		$result = imgage_check($src);
		if(!$result) return;
	}
	$size	= getimagesize($src);

	//일반적으로 보는데 문제 없으나 gd 에서 오류 발생하여 이미지생성이 안된다.
	//jpeg 파일이 일반적으로 보는데 문제 없으나 손상된 파일이거나 CYMK 일 경우 오류 발생하여 오류 무시 처리 해준다.
	if($size[2] == 2)	@ini_set('gd.jpeg_ignore_warning', 1);

	switch ($size[2]){
		case 1:	$image	= @ImageCreatefromGif($src); break;
		case 2:	$image	= ImageCreatefromJpeg($src); break;
		case 3:	$image	= ImageCreatefromPng($src);  break;
	}

	if ($fix){
		$gap = abs($size[0]-$size[1]);
		switch ($fix){
			case 1:		# 설정된 크기에 따라 비율을 조정
				$reSize		= ImgSizeSet($src,$sizeX,$sizeY,$size[0],$size[1]);
				$g_width	= 0;
				$g_height	= 0;
				$newSizeX	= $reSize[0];
				$newSizeY	= $reSize[1];
				break;
			case 2:		# 사용되지 않음
				if ($size[0]>$size[1]) $g_width  = $gap / 2;
				else $g_height = $gap / 2;
				$newSizeX	= $sizeX;
				$newSizeY	= $sizeX;
				if ($size[0]>$size[1]) $size[0] = $size[1];
				else $size[1] = $size[0];
				break;
			case 3:		# 사용되지 않음
				if ($size[0]>$size[1]) $g_width  = $gap;
				else $g_height = $gap;
				$newSizeX	= $sizeX;
				$newSizeY	= $sizeX;
				if ($size[0]>$size[1]) $size[0] = $size[1];
				else $size[1] = $size[0];
				break;
			case 4:
				$newSizeX	= $sizeX;
				$newSizeY	= $sizeY;
				break;
		}

		$dst	= ImageCreateTruecolor($newSizeX,$newSizeY);
		Imagecopyresampled($dst,$image,0,0,$g_width,$g_height,$newSizeX,$newSizeY,$size[0],$size[1]);
	} else {
		$width	= $sizeX;
		$height = $size[1] / $size[0] * $sizeX;
		$dst	= ImageCreateTruecolor($width,$height);
		Imagecopyresampled($dst,$image,0,0,0,0,$width,$height,$size[0],$size[1]);
	}
	ImageJpeg($dst,$folder,$quality);
	ImageDestroy($dst);
	@chmod($folder,0707); // 업로드된 파일 권한 변경
}

### 외부 호스팅 이미지 유효성 체크
function imgage_check($src){
	$url = parse_url($src);

	$fp = fsockopen($url[host],80,$errno,$errstr,10);

	if($fp){
		socket_set_timeout($fp, 3);
		if(fputs($fp,"POST ".$url[path]." HTTP/1.0\r\n"."Host: ".$url[host]."\r\n"."User-Agent: Web 0.1\r\n"."\r\n")){
			while(!feof($fp)){
				$data .= fread($fp,1024);
			}
			if(stristr($data,"Content-Type: image")){
				return true;
			}
		}
		fclose($fp);
	}
	return false;
}

### 구분자로 구분되어진 문자열에서 특정 문자열이 존재하는지 체크
function find_in_set($str,$strlist,$division=",")
{
	return (in_array($str,explode($division,$strlist))) ? true : false;
}

### 문자열에서 특정 문자 사이에 있는 문자열 배열로 뽑아오기
function split_betweenStr($str,$keyS,$keyE)
{
	for ($i=0;$i<strlen($str);$i++){
		$target = substr($str,$i);
		$start_pre = strpos($target,$keyS);
		$start = $start_pre + strlen($keyS);
		$end = strpos(substr($target,$start),$keyE);
		if ($start_pre===false || $end===false) break;
		$ret[] = substr($target,$start,$end);
		$i += $start + $end + strlen($keyE) - 1;
	}
	return $ret;
}

### 진행상황별 처리
function ctlStep($ordno,$step,$stock='')
{
	global $db, $r_step, $order_confirm;

	$todayshop_noti = Core::loader('todayshop_noti');
	$ts_orderdata = $todayshop_noti->getorderinfo($ordno);
	if (!$ts_orderdata) {
		unset($todayshop_noti, $ts_orderdata);
		$todayshop_noti = false;
	}

	$pre = $db->fetch("select * from ".GD_ORDER." where ordno='$ordno'");


	if(isset($pre['settleInflow']))
	{
		switch($pre['settleInflow']) {
			case 'payco' : {	// 페이코 주문상태 변경
				global $paycoCfg, $r_istep;

				if($pre['settlekind'] == 'v' && $pre['step'] < 1 && $step > 0) {
					msg('페이코 가상계좌 주문은 주문접수 상태에서 '.$r_istep[$step].' 상태로 변경할 수 없습니다.', -1);
					exit;
				}

				if(!$paycoCfg && is_file(dirname(__FILE__) . '/../conf/payco.cfg.php')){
					include dirname(__FILE__) . '/../conf/payco.cfg.php';
				}
				$payco_item = $db->query("SELECT * FROM `".GD_ORDER_ITEM."` WHERE `ordno`=".$ordno." AND `istep`<=4");

				while($preItem = $db->fetch($payco_item, 1)) {
					$arr_item_sno[] = $preItem['sno'];
				}

				$request['seller_key'] = $paycoCfg['paycoSellerKey'];
				$request['ordno'] = $ordno;
				$request['sno'] = implode('|', $arr_item_sno);
				$request['step'] = $step;
				$request['step2'] = '0';
				$request['payco_settle_type'] = $pre['payco_settle_type'];

				if($step > 1) {
					if(!$paycoApi) $paycoApi = &load_class('paycoApi','paycoApi');
					$response = $paycoApi->request('order_status', $request);
					$response = json_decode($response, true);

					if($response['code'] != '000') {
						msg('페이코 주문상태 변경을 실패했습니다.['.iconv('utf-8', 'euc-kr', $response['msg']).']', -1);
						exit;
					}
				}
				else if($step == 0){
					msg('페이코 주문은 주문접수 상태로 변경할 수 없습니다.', -1);
					exit;
				}
				break;
			}
		}
	}

	// 아이패이PG 주문건의 경우 API호출
	if(strlen($pre['ipay_cartno'])>0 && strlen($pre['ipay_payno'])>0)
	{
		include dirname(__FILE__).'/integrate_order_processor.model.ipay.class.php';
		$res = $db->query("SELECT * FROM `".GD_ORDER_ITEM."` WHERE `ordno`=".$ordno." AND `istep`<=4");
		while($preItem = $db->fetch($res, 1))
		{
			switch($step)
			{
				// 발주확인
				case '2':
					$auctionIpay = new integrate_order_processor_ipay();
					$auctionIpay->IpayConfirmReceivingOrder($preItem['ipay_ordno']);
					break;
				// 배송중
				case '3': case '4':
					if(isset($set)===false) include dirname(__FILE__).'/../conf/config.pay.php';
					$auctionIpay = new integrate_order_processor_ipay();
					// 주문건별 송장등록
					if(strlen(trim($_POST['deliveryno']))>0 && strlen(trim($_POST['deliverycode']))>0 && (int)$set['delivery']['basis']===0)
					{
						$deliveryNo = trim($_POST['deliveryno']);
						$deliveryCode = trim($_POST['deliverycode']);
					}
					// 통합주문
					else if(isset($_POST['dlv_company']['enamoo'][$ordno]) && isset($_POST['dlv_no']['enamoo'][$ordno]))
					{
						$deliveryNo = trim($_POST['dlv_company']['enamoo'][$ordno]);
						$deliveryCode = trim($_POST['dlv_no']['enamoo'][$ordno]);
					}
					// 개별 송장등록
					else
					{
						$deliveryNo = $preItem['dvno'];
						$deliveryCode = $preItem['dvcode'];
					}

					if(strlen($deliveryNo)>0 && strlen($deliveryCode)>0)
					{
						if((int)$preItem['istep']<=4)
						{
							if((int)$preItem['istep']<2) $auctionIpay->IpayConfirmReceivingOrder($preItem['ipay_ordno']);
							$result = $auctionIpay->DoIpayShippingGeneral($preItem['ipay_ordno'], $deliveryNo, $deliveryCode);
							if($result!=='Success') $auctionIpay->IpayChangeShippingType($preItem['ipay_ordno'], $deliveryNo, $deliveryCode);
							$db->query("UPDATE `gd_order_item` SET `istep`=3, `dyn`='y' WHERE `sno`=".$preItem['sno']);
						}
					}
					else
					{
						msg('송장번호를 입력하여 주세요.', -1);
						exit;
					}
					break;
			}
		}
	}

	$naverNcash = Core::loader('naverNcash', true);

	if ($pre[step]!=$step){

		## 배송중처리시
		if ($pre['inflow'] == 'interpark' && $step == "3")
		{
			if ($pre['deliveryno'] > 0 && $pre['deliverycode'] !='');
			else {
				list($cnt) = $db->fetch("select count(sno) from ".GD_ORDER_ITEM." where ordno='{$ordno}' and istep='{$pre['step']}' and (dvno=0 or dvcode='')");
				if ($cnt){
					msg("인터파크샵플러스 주문은 택배사 및 송장번호를 먼저 입력하신 후 배송중으로 전환하세요.",-1);
				}
			}
		}

		## 오픈스타일 배송중처리시
		if ($pre['inflow'] == 'openstyle' && $step == "3")
		{
			if ($pre['deliveryno'] > 0 && $pre['deliverycode'] !='');
			else {
				list($cnt) = $db->fetch("select count(sno) from ".GD_ORDER_ITEM." where ordno='{$ordno}' and istep='{$pre['step']}' and (dvno=0 or dvcode='')");
				if ($cnt){
					msg("인터파크오픈스타일 주문은 택배사 및 송장번호를 먼저 입력하신 후 배송중으로 전환하세요.",-1);
				}
			}
		}


		if (!$order_confirm) $order_confirm = "admin";

		switch ($step){

			case "0":				// 주문접수

				$db->query("update ".GD_ORDER." set cyn='n',cdt=null,dyn='n',ddt=null where ordno=$ordno");
				$db->query("update ".GD_ORDER_ITEM." set cyn='n',dyn='n' where ordno=$ordno and istep='$pre[step]'");
				if ($pre[confirm])  $exec_confirm_n = true;

				if($pre[coupon_emoney]) setEmoney($pre[m_no],$pre[coupon_emoney] * -1,'쿠폰 적립금 환원',$ordno);

				break;

			case "1": case "2":		// 입금확인, 배송준비중

				if ($pre[cyn]=="n") $exec_cyn_y = true;
				if ($pre[dyn]=="y") $exec_dyn_n = true;
				if ($pre[confirm])  $exec_confirm_n = true;

				if($pre[coupon_emoney]) setEmoney($pre[m_no],$pre[coupon_emoney] * -1,'쿠폰 적립금 환원',$ordno);
				break;

			case "3":				// 배송중

				if ($pre[cyn]=="n") $exec_cyn_y = true;
				if ($pre[dyn]=="n") $exec_dyn_y = true;
				if ($pre[confirm])  $exec_confirm_n = true;

				if($pre[coupon_emoney]) setEmoney($pre[m_no],$pre[coupon_emoney] * -1,'쿠폰 적립금 환원',$ordno);
				break;

			case "4":				// 배송완료

				if ($pre[cyn]=="n") $exec_cyn_y = true;
				if ($pre[dyn]=="n") $exec_dyn_y = true;
				if (!$pre[confirm]) $exec_confirm_y = true;

				### 주문 적림금 적립
				setGoodsEmoney($ordno,1);

				if($pre[coupon_emoney]){
					### 적립쿠폰 적립금 적립
					setEmoney($pre[m_no],$pre[coupon_emoney],'쿠폰 적립금 적립',$ordno);
				}

				### 구매완료 쿠폰 발급
				setGoodsCoupon($ordno);

				break;
		}

		### 실행코드
		if ($exec_cyn_y){
			$db->query("update ".GD_ORDER." set cyn='y',cdt=now() where ordno=$ordno");
			$db->query("update ".GD_ORDER_ITEM." set cyn='y' where ordno=$ordno and istep='$pre[step]'");

			if ($pre[inflow] == 'interpark');
			else if ($pre[inflow] == 'openstyle');
			else {

				// 알림
				if ($todayshop_noti === false) {
					### 입금확인SMS
					$GLOBALS[dataSms] = $pre;
					sendSmsCase('incash',$pre[mobileOrder]);

					### 입금확인메일
					sendMailCase($pre[email],1,$pre);
				}

				### 현금영수증(자동발급-데이터취합)
				if (is_object($GLOBALS['cashreceipt'])){
					$GLOBALS['cashreceipt']->autoApproval($ordno);
				}
			}
		}
		if ($exec_dyn_n){
			$db->query("update ".GD_ORDER." set dyn='n',ddt=null where ordno=$ordno");
			$db->query("update ".GD_ORDER_ITEM." set dyn='n' where ordno=$ordno and istep='$pre[step]'");
		}

		if ($exec_dyn_y){
			$db->query("update ".GD_ORDER." set dyn='y',ddt=now() where ordno=$ordno");
			$db->query("update ".GD_ORDER_ITEM." set dyn='y' where ordno=$ordno and istep='$pre[step]'");

			if ($pre[inflow] == 'interpark');
			else if ($pre[inflow] == 'openstyle');
			else {
				### 배송업체 정보
				list ($pre[deliverycomp]) = $db->fetch("select deliverycomp from ".GD_LIST_DELIVERY." where deliveryno='$pre[deliveryno]'");

				// 알림
				if ($todayshop_noti !== false) {
					if ($ts_orderdata['goodstype'] == 'coupon') {
						$todayshop_noti->set($ordno,'sale');
						$todayshop_noti->send();
					}
					elseif ($pre['deliverycomp']) { // 배송관련 정보가 있을 경우만 발송안내 메세지 안보냄.
						$todayshop_noti->set($ordno, 'delivery');
						$todayshop_noti->send();
					}
				}
				else {

					### 발송확인SMS
					$GLOBALS[dataSms] = $pre;
					sendSmsCase('delivery',$pre[mobileOrder]);

					### 송장번호SMS
					if($pre[deliverycomp]) sendSmsCase('dcode',$pre[mobileOrder]);

					### 발송확인메일
					sendMailCase($pre[email],3,$pre);

				}
			}
		}

		if ($exec_confirm_n){
			$addQr = ",confirm='',confirmdt=''";
		}
		if ($exec_confirm_y){
			$addQr = ",confirm='$order_confirm',confirmdt=IF(confirmdt > '' , confirmdt , now() )";
		}

		$db->query("update ".GD_ORDER." set step='$step' $addQr where ordno=$ordno");
		$db->query("update ".GD_ORDER_ITEM." set istep='$step' where ordno=$ordno and istep='$pre[step]'");
		orderLog($ordno,$r_step[$pre[step]]." > ".$r_step[$step]);

		if ($stock) setStock($ordno);

		/*
		// @삭제 : 삭제된 서비스
		if ($step == 4 && $pre[inflow] == 'yahoo_fss' )
		{
			ob_start();
			@include dirname(__FILE__)."/yahoofss.class.php";
			$yfss = new Yfss( 'sendOrder', $ordno ); # 주문내역 전송
			ob_end_clean();
		}
		*/

		if ($pre[inflow] == 'interpark' && $exec_dyn_y)
		{
			@include_once dirname(__FILE__) . "/interpark.e2i_order.class.php";
			$e2i_order_api = new e2i_order_api('delvCompForComm', array('ordno' => $ordno, 'preStep' => $pre[step], 'stock' => $stock));
		}

		if ($pre[inflow] == 'openstyle' && $exec_dyn_y)
		{
			//배송시작
			@include_once dirname(__FILE__) . "/interpark.e2i_openstyle_order.class.php";
			$e2i_order_api = new e2i_order_api('delvCompForComm', array('ordno' => $ordno, 'preStep' => $pre[step], 'stock' => $stock));
		}


		if ($todayshop_noti  !== false) {

			$query = "
				select
				TG.tgsno from ".GD_ORDER_ITEM." AS O
				INNER JOIN ".GD_TODAYSHOP_GOODS." AS TG
				ON O.goodsno = TG.goodsno
				where O.ordno='$ordno'
			";
			$res = $db->query($query);
			while($tmp = $db->fetch($res)) {

				$query = "
					SELECT

						IFNULL(SUM(OI.ea), 0) AS cnt

					FROM ".GD_ORDER." AS O
					INNER JOIN ".GD_ORDER_ITEM." AS OI
						ON O.ordno=OI.ordno
					INNER JOIN ".GD_TODAYSHOP_GOODS_MERGED." AS TG
						ON OI.goodsno = TG.goodsno

					WHERE
							O.step > 0
						AND O.step2 < 40
						AND TG.tgsno='".$tmp['tgsno']."'

				";

				$_res = $db->query($query);

				while ($_tmp = $db->fetch($_res)) {

					$query = "
					UPDATE
						".GD_TODAYSHOP_GOODS_MERGED."		AS TGM
						INNER JOIN ".GD_TODAYSHOP_GOODS."	AS TG	ON TGM.goodsno = TG.goodsno
					SET
						TGM.buyercnt = ".$_tmp['cnt'].",
						TG.buyercnt = ".$_tmp['cnt']."
					WHERE
						TG.tgsno = ".$tmp['tgsno']."
					";
					$db->query($query);

				}

			}

		}

		if ($exec_cyn_y && $naverNcash->useyn == 'Y'){
			if ($pre[inflow] == 'interpark');
			else if ($pre[inflow] == 'openstyle');
			else {
				if ($todayshop_noti === false) {
					### Ncash 거래 확정
					if($pre['settlekind']=='a' || $pre['settlekind']=='v') $naverNcash->deal_done($ordno);
				}
			}
		}
		if($pre[step]!=$step && $step == '0' && $pre[step] != '4'){
			### Ncash 거래 확정 취소 API
			if($pre['settlekind']=='a' || $pre['settlekind']=='v') $naverNcash->deal_done_cancel($ordno);
		}

		//플러스치즈 주문 상태 변경 처리
		if(!empty($pre['pCheeseOrdNo'])){
			$plusCheese = Core::loader('plusCheese', -1);
			$res = $plusCheese->sendStatus($ordno, $step);
		}
	}
	// 네이버 배송정보 등록
	if(($step=='3' || $step=='4') && $naverNcash->useyn == 'Y')
	{
		if(isset($_POST['dlv_company']['enamoo'][$ordno]) && isset($_POST['dlv_no']['enamoo'][$ordno])) $naverNcash->delivery_invoice($ordno, trim($_POST['dlv_company']['enamoo'][$ordno]), trim($_POST['dlv_no']['enamoo'][$ordno]));
		else $naverNcash->delivery_invoice($ordno);
	}
}

### 재고
function setStock($ordno)
{
	global $db;

	// 재고가 차감 되는 동안에 다른 쓰레드에서 read 가 되어 구매가 되면 안되므로, write lock 을 걸어줌.
	$db->query("LOCK TABLES
				".GD_GOODS." write,
				".GD_GOODS." AS b write,
				".GD_GOODS." AS G write,
				".GD_GOODS_OPTION." write,
				".GD_GOODS_OPTION." AS go write,
				".GD_ORDER_ITEM." write,
				".GD_ORDER_ITEM." AS a write,
				".GD_ORDER." write,
				".GD_TODAYSHOP_GOODS." write,
				".GD_TODAYSHOP_GOODS." AS c write,
				".GD_TODAYSHOP_GOODS." AS TG write,
				".GD_GOODS_UPDATE_NAVER." write,
				".GD_GOODS_UPDATE_DAUM." write,
				".GD_TODAYSHOP_GOODS_MERGED." AS TGM write
				");

	include dirname(__FILE__)."/../conf/config.php";
	include dirname(__FILE__)."/todayshop_cache.class.php";

	$x_istep = ($cfg[stepStock]) ? "0,44" : "44";

	$query = "
	SELECT
		a.*,
		b.usestock, b.inpk_prdno, b.totstock,
		c.tgsno, IF ((c.enddt IS NOT NULL AND c.enddt < now()) OR b.runout=1, 'y', 'n') AS tgout,
		go.stock
	FROM ".GD_ORDER_ITEM." AS a
	INNER join ".GD_GOODS." AS b
	ON a.goodsno=b.goodsno
	LEFT JOIN ".GD_GOODS_OPTION." AS go
	ON go.goodsno = a.goodsno AND go.opt1 = a.opt1 AND go.opt2 = a.opt2 and go_is_deleted <> '1'

	LEFT JOIN ".GD_TODAYSHOP_GOODS." AS c
	ON a.goodsno = c.goodsno

	where
		a.ordno='$ordno'
	";

	$res = $db->query($query);
	while ($data=$db->fetch($res)) {

		if ($data['stockable'] == 'y') {

			$mode = 0;
			if ($data[stockyn]=="y" && find_in_set($data[istep],$x_istep)) $mode = 1;
			else if ($data[stockyn]=="n" && !find_in_set($data[istep],$x_istep)) $mode = -1;

			$cstock = $data['stock'];
			$ostock = $cstock;
			$cstock = $cstock +( $mode * $data[ea] );
			if($cstock < 0) $cstock = 0;

			### 전체 재고 수정
			//list($data['totstock']) = $db->fetch("select totstock from ".GD_GOODS." where goodsno='".$data['goodsno']."' limit 1");	// join 으로 처리
			$totstock = $data['totstock']+( $mode * $data['ea'] );
			if($data['totstock'] != $totstock) $db->query("update ".GD_GOODS." set totstock = '".$totstock."' where goodsno='".$data['goodsno']."'");

			if($ostock != $cstock) {
				$query = "
				update ".GD_GOODS_OPTION." set
					stock = '".$cstock."'
				where
					goodsno = '$data[goodsno]'
					and opt1 = '$data[opt1]'
					and opt2 = '$data[opt2]'
				";
				$db->query($query);

				if ($data['inpk_prdno'] != '' && $mode!=0) $inpkGoods[] = $data[goodsno];
			}

			if ($mode!=0){
				$stockyn = ($mode>0) ? "n" : "y";
				$db->query("update ".GD_ORDER_ITEM." set stockyn='$stockyn' where sno='$data[sno]'");
			}

			// 투데이샵
			if ($data['tgsno']) {

				// 재고 0일때
				if($totstock == 0 && $data['stockyn'] == 'n') {
					$db->query("UPDATE ".GD_GOODS." SET runout=1 WHERE goodsno='".$data['goodsno']."'");
					$data['tgout'] = 'y';

					// 캐시 삭제
					todayshop_cache::remove($data['tgsno'],'*');
				}

				// 상품 재고 판매 수량 조정
				$query = "
				UPDATE
					".GD_TODAYSHOP_GOODS_MERGED."		AS TGM
					INNER JOIN ".GD_TODAYSHOP_GOODS."	AS TG	ON TGM.goodsno = TG.goodsno
					INNER JOIN ".GD_GOODS."				AS G	ON TG.goodsno = G.goodsno
				SET
					TGM.usestock = G.usestock,
					TGM.totstock = G.totstock,
					TGM.runout = G.runout
				WHERE
					TG.tgsno = ".$data['tgsno']."
				";
				$db->query($query);

				todayshop_cache::remove($data['tgsno'],'indbpageinit');

			} // 투데이샵

			// 일반상품
			else {

				// 재고 0 일때
				if($cstock == 0 && $data['stockyn'] == 'n') {
					$pre = $db->fetch("select * from ".GD_ORDER." where ordno='$ordno'");
					$GLOBALS['dataSms'] = $pre;
					$GLOBALS['dataSms']['goodsnm'] = $data['goodsnm'];
					sendSmsCase('runout',$GLOBALS['cfg']['smsRecall']);
				}

				// 네이버 지식쇼핑 & 다음 쇼핑하우 상품엔진
				if($data['totstock']>0 && $totstock<=0)
				{
					naver_goods_runout($data[goodsno]);
					daum_goods_runout($data[goodsno]);
				}
				else if($data['totstock']==0 && $totstock>0)
				{
					naver_goods_runout_recovery($data[goodsno]);
					daum_goods_runout_recovery($data[goodsno]);
				}

				if (is_array($inpkGoods) === true)
				{
					@include_once dirname(__FILE__) . "/interpark.class.php";
					@include_once dirname(__FILE__) . "/interpark.e2i_openstyle_goods.class.php";
					$e2i_goods_api = new e2i_goods_openstyle_api($inpkGoods, 'stock');
				}

			} // 일반상품

		}

	}

	// 페이지캐시 초기화
	$templateCache = Core::loader('TemplateCache');
	$templateCache->clearCacheByClass('goods');

	$db->query('unlock tables');

}

### 카테고리 단계
function cateStep()
{
	global $db;
	list($len) = $db->fetch("select max(length(category)) from ".GD_CATEGORY."");
	$catestep = $len / 3;
	return $catestep;
}

### 카테고리 정렬 재정의
function resort($arr)
{
	if (!is_array($arr)) return;
	ksort($arr);
	foreach ($arr as $v) foreach ($v as $v2) $tmp[] = $v2;
	return $tmp;
}

### 현재위치 정보 출력
function currPosition($category,$mode=0)
{
	global $db;
	$query = "
	select * from
		".GD_CATEGORY."
	where
		category in (left('$category',3),left('$category',6),left('$category',9),'$category')
	order by category
	";
	$res = $db->query($query);
	while ($data=$db->fetch($res)) $pos[] = "<a href='../goods/goods_list.php?category=$data[category]'>$data[catnm]</a>";
	$ret = @implode(" > ",$pos);
	if ($mode) $ret = strip_tags($ret);
	return $ret;
}

### 관리자페이지에서 업로드한 이미지 (사용안함)
function uploaded($key,$idx=0)
{
	global $conf;
	$v = &$conf[$key];
	if (!$v[img][$idx]) return;
	$ret = "<img src='../img/upload/{$v[img][$idx]}' usemap=#>";
	if ($v[url][$idx]) $ret = "<a href='{$v[url][$idx]}' target='{$v[target][$idx]}'>$ret</a>";
	return $ret;
}

### 외부 파일 읽기
function readurl($str,$port=80)
{
	$url = parse_url($str);

	$fp = @fsockopen($url[host], $port,$errno,$errstr,10);
	if ($fp){
		fwrite($fp, "GET $url[path]?$url[query] HTTP/1.0\r\nHost: $url[host]\r\n\r\n");
		while (!feof($fp)) $out .= fread($fp, 1024);
		fclose($fp);

		$out = explode("\r\n\r\n",$out);
		array_shift($out);
		$out = implode("",$out);
	}

	return $out;
}

### 외부 파일 읽기(POST)
function readpost($str, $data, $port=80)
{
	$url = parse_url($str); # parsing the given URL
	$referrer = $_SERVER['SCRIPT_URL']; #  Building referrer
	if (!isset($url[port])) $url[port] = $port; # find out which port is needed(=80)
	$data_string = getVars('', $data); # making string from $data

	### building POST-request:
	$request .= "POST {$url[path]} HTTP/1.1\r\nHost: {$url[host]}\r\nReferer: {$referrer}\r\n";
	$request .= "Content-type: application/x-www-form-urlencoded\r\n";
	$request .= "Content-length: " . strlen($data_string) . "\r\n";
	$request .= "Connection: close\r\n\r\n";
	$request .= "{$data_string}\r\n";

	### connect socket
	$fp = @fsockopen($url[host], $url[port], $errno, $errstr, 10);
	if ($fp){
		fwrite($fp, $request);
		do $header .= fread($fp, 1); while (!preg_match('/\\r\\n\\r\\n$/', $header)); # get header

		### get content
		if (preg_match('/Transfer\\-Encoding:\\s+chunked\\r\\n/', $header, $matches)) # check for chunked encoding
			do {
				$byte = $chunk_size = "";
				do { $chunk_size .= $byte; $byte = fread($fp, 1); } while ($byte != "\r"); # till we match the CR
				fread($fp, 1); # also drop off the LF
				$chunk_size = hexdec($chunk_size); # convert to real number
				if ($chunk_size) $out .= fread($fp, $chunk_size);
				fread($fp, 2); # ditch the CRLF that trails the chunk
			} while ($chunk_size); # till we reach the 0 length chunk (end marker)
		else if (preg_match('/Content\\-Length:\\s+([0-9]*)\\r\\n/', $header, $matches)) $out = fread($fp,$matches[1]); # check for specified content length
		else while (!feof($fp)) $out .= fread($fp, 4096); # not a nice way to do it

		fclose($fp); # close connection
	}

	return $out;
}

### 외부 파일 읽기(POST)
function readpostssl($str, $data, $port=80)
{
	$url = parse_url($str); # parsing the given URL
	$referrer = $_SERVER['SCRIPT_URL']; #  Building referrer
	if (!isset($url[port])) $url[port] = $port; # find out which port is needed(=80)
	switch ($url['scheme']) {
		case 'https':
			$scheme = 'ssl://';
			break;
		case 'http':
		default:
			$scheme = '';
	}
	$data_string = getVars('', $data); # making string from $data

	### building POST-request: post 주소를 $url[host] 가 아닌 $str로 get 변수 포함 전송
	$request .= "POST {$str} HTTP/1.1\r\nHost: {$url[host]}\r\nReferer: {$referrer}\r\n";
	$request .= "Content-type: application/x-www-form-urlencoded\r\n";
	$request .= "Content-length: " . strlen($data_string) . "\r\n";
	$request .= "Connection: close\r\n\r\n";
	$request .= "{$data_string}\r\n";

	### connect socket
	$fp = @fsockopen($scheme.$url[host], $url[port], $errno, $errstr, 10);
	if ($fp){
		fwrite($fp, $request);
		do $header .= fread($fp, 1); while (!preg_match('/\\r\\n\\r\\n$/', $header)); # get header

		### get content
		if (preg_match('/Transfer\\-Encoding:\\s+chunked\\r\\n/', $header, $matches)) # check for chunked encoding
			do {
				$byte = $chunk_size = "";
				do { $chunk_size .= $byte; $byte = fread($fp, 1); } while ($byte != "\r"); # till we match the CR
				fread($fp, 1); # also drop off the LF
				$chunk_size = hexdec($chunk_size); # convert to real number
				if ($chunk_size) $out .= fread($fp, $chunk_size);
				fread($fp, 2); # ditch the CRLF that trails the chunk
			} while ($chunk_size); # till we reach the 0 length chunk (end marker)
		else if (preg_match('/Content\\-Length:\\s+([0-9]*)\\r\\n/', $header, $matches)) $out = fread($fp,$matches[1]); # check for specified content length
		else while (!feof($fp)) $out .= fread($fp, 4096); # not a nice way to do it

		fclose($fp); # close connection
	}

	return $out;
}

### 상품이미지(드래그&드롭 장바구니용)
function Movegoodsimg($src,$size='',$goodsno,$type)
{
	$tmp=''; $hidden='';
	if(!preg_match('/http:\/\//',$src)){
		if ($hidden) $path = "../";
		$path .= "../data/goods/";
		if ($hidden==3) $path = "http://".$GLOBALS[cfg][shopUrl].$GLOBALS[cfg][rootDir]."/data/goods/";
	}
	if ($size){
		$size = explode(",",$size);
		$vsize = " width=$size[0]";
		if ($size[1]) $vsize .= " height=$size[1]";
	}
	if ($tmp) $tmp = " ".$tmp;

	if ($size[0]>300) $nosize = 500;
	else if ($size[0]>130) $nosize = 300;
	else if ($size[0]>100) $nosize = 130;
	else $nosize = 100;
	$onerror = ($hidden<2) ? "onerror=this.src='".$GLOBALS[cfg][rootDir]."/data/skin/".$GLOBALS[cfg][tplSkin]."/img/common/noimg_$nosize.gif'" : "onerror=this.style.display='none'";
	if( $type == "wishlist" ) return "<img src='$path{$src}'{$vsize}{$tmp} $onerror onmouseover=\"Div_type('wishlist');Div_clone(event);\" onmouseup='move_stop(event);' name='".$goodsno."'>";
	else return "<img src='$path{$src}'{$vsize}{$tmp} $onerror onmouseover=\"Div_type('list');Div_clone(event);\" name='".$goodsno."'>";
}

### 상품이미지
/*
$hidden		0	일반 사용자 페이지
			1	관리자 페이지
			2	관리자 페이지 (onerror시 hidden)
			3	절대웹경로
*/
function goodsimg($src,$size='',$tmp='',$hidden='', $viewerid='')
{
	if(!preg_match('/http:\/\//',$src)){
		if ($hidden) $path = "../";
		$path .= "../data/goods/";
		if ($hidden==3) $path = "http://".$GLOBALS[cfg][shopUrl].$GLOBALS[cfg][rootDir]."/data/goods/";
		if ($hidden==4) $path = "http://".$_SERVER['HTTP_HOST'].$GLOBALS[cfg][rootDir]."/data/goods/";
	}
	if ($size){
		$size = explode(",",$size);
		$vsize = " width=$size[0]";
		if ($size[1]) $vsize .= " height=$size[1]";
	}
	if ($tmp) $tmp = " ".$tmp;

	if ($size[0]>300) $nosize = 500;
	else if ($size[0]>130) $nosize = 300;
	else if ($size[0]>100) $nosize = 130;
	else $nosize = 100;

	$onerror = ($hidden<2) ? "onerror=this.src='".$GLOBALS[cfg][rootDir]."/data/skin/".$GLOBALS[cfg][tplSkin]."/img/common/noimg_$nosize.gif'" : "onerror=this.style.display='none'";

	$rtn = "<img src='$path{$src}'{$vsize}{$tmp} $onerror ";
	if ($viewerid) $rtn .= ' viewerid="'.$viewerid.'" ';
	$rtn .= '/>';
	return $rtn;
}

### 오늘본상품
function todayGoods($arr)
{
	$max = 10;	// 리스트 저장 개수
	$goodsno = $arr[goodsno];
	$div = explode(",",$_COOKIE[todayGoodsIdx]);
	$todayG = unserialize(stripslashes($_COOKIE[todayGoods]));
	if (!is_array($todayG)) $todayG = array();
	if (in_array($goodsno,$div)){
		$key = array_search($goodsno,$div);
		array_splice($div,$key,1);
		array_splice($todayG,$key,1);
	}
	array_unshift($div,$goodsno); array_unshift($todayG,$arr);
	array_splice($todayG,$max); //array_splice($div,$max);
	setcookie('todayGoodsIdx',implode(",",$div),time()+3600*24,'/');
	setcookie('todayGoods',serialize($todayG),time()+3600*24,'/');
}

### 배송비 구하기
function getDelivery($zipCode)
{
	$zipCode = str_replace("-","",$zipCode);
	include dirname(__FILE__)."/../conf/config.pay.php";
	$set = $set['delivery'];
	$delivery = $set['default'];
	if (!$zipCode) return $delivery;
	$tmp = explode('|',trim($set[overZipcode]));
	foreach($tmp as $k => $v){
		$idx = count($rzip);
		$arr = array_unique(explode(',',$v));
		foreach($arr as $v2)if($v2)$rzip[$idx][] = $v2;
	}
	$over = explode("|",$set[over]);
	$head = substr($zipCode,0,3);
	for ($i=0;$i<count($rzip);$i++)if(in_array($head,$rzip[$i])){
		$key=$i;
		$delivery = $over[$key];
	}
	return $delivery + 0;
}

function getDeliveryMode($param){

	include_once dirname(__FILE__)."/../lib/cart.class.php";
	include_once dirname(__FILE__) . '/../lib/areaDelivery.class.php';
	include dirname(__FILE__)."/../conf/config.pay.php";
	global $sess,$db;

	// 변수 초기화 & 설정
	settype($param['deliPoli'], "integer");
	settype($param['delivery_type'], "integer");
	settype($param['mode'], "integer");

	// 무료배송에 따른 지역별 추가 배송비 설정값 세팅
	if (isset($set['delivery']['add_extra_fee']) === true) {
		$tmp_add_extra_fee			= $set['delivery']['add_extra_fee'];		// 기존 레거시 보장, 해당 값은 더이상 사용 안함
	} else {
		$tmp_add_extra_fee			= 1;										// 기본 값은 지역별 추가 배송비 받음으로 처리
	}
	if (isset($set['delivery']['add_extra_fee_basic']) === false) {				// "기본 배송정책에 의한 조건부 무료인 경우"에서 기본값 (기존 레거시 또는 지역별 추가 배송비 받음)
		$set['delivery']['add_extra_fee_basic']			= $tmp_add_extra_fee;
	}
	if (isset($set['delivery']['add_extra_fee_free']) === false) {				// "무료배송 상품 주문시"인 경우 기본값 (기존 레거시 또는 지역별 추가 배송비 받음)
		$set['delivery']['add_extra_fee_free']			= $tmp_add_extra_fee;
	}
	if (isset($set['delivery']['add_extra_fee_memberGroup']) === false) {		// "회원 그룹 혜택에 의한 배송비 무료인 경우"에서 기본값 (기존 레거시 또는 지역별 추가 배송비 받음)
		$set['delivery']['add_extra_fee_memberGroup']	= $tmp_add_extra_fee;
	}
	unset($tmp_add_extra_fee);

	// 지역별 추가 배송비 다중 부과 기본값 세팅
	if (isset($set['delivery']['add_extra_fee_duplicate_each']) === false) {
		$set['delivery']['add_extra_fee_duplicate_each']		= 1;			// 개별배송상품 주문시 기본값은 "항목별 중복 부과" 로 처리 (더이상 사용하지 않음)
	}
	if (isset($set['delivery']['add_extra_fee_duplicate_free']) === false) {
		$set['delivery']['add_extra_fee_duplicate_free']		= 1;			// 무료배송 상품 주문시 기본값은 "항목별 중복 부과" 로 처리
	}
	if (isset($set['delivery']['add_extra_fee_duplicate_fixEach']) === false) {
		$set['delivery']['add_extra_fee_duplicate_fixEach']		= 1;			// 고정 배송비 상품 주문시 기본값은 "항목별 중복 부과" 로 처리
	}

	$items = array();
	$_extra_fee	= 0;

	// 배송비 설정
	$tmp = array();
	$tmp[] = array($set['delivery']['free'],$set['delivery']['deliveryType'],$set['delivery']['default'],$set['delivery']['default_msg'],$set['delivery']['deliverynm']);
	$arr = explode('|',$set['r_delivery']['title']);
	foreach($arr as $v) {
		$tmp[] = array($set[$v]['r_free'],$set[$v]['r_deliType'],$set[$v]['r_default'],$set[$v]['r_default_msg'],$v);
	}
	$default_delivery_policy = $tmp[$param['deliPoli']];	// 선택한 기본 배송비
	unset($tmp);

	/*
	 * mode
	 * 0 : 장바구니내 상품 기준
	 * 1 : param 변수에 담긴 정보 기준 (단일 상품)
	 */
	if($param['mode'] === 0) {

		// 장바구니
		$cart = Core::loader('Cart', $_COOKIE[gd_isDirect]);

		// 회원 배송비 할인
		if ($sess['level'] > 0) {
			if (($member_group = $db->fetch("SELECT excep, excate, free_deliveryfee, free_deliveryfee_std_amt, free_deliveryfee, dc FROM ".GD_MEMBER_GRP." WHERE LEVEL='".$sess['level']."' LIMIT 1")) !== false) {
				if ($member_group['excep'])		$cart->excep	= $member_group['excep'];
				if ($member_group['excate'])	$cart->excate	= $member_group['excate'];

				$cart->dc = ((int)$member_group[dc]) . '%';
			}
		}

		// 쿠폰
		$cart->coupon = $param['coupon'];

		// 할인등 연산 -> 이때, 쿠폰 할인, 회원 할인 중복 여부 등이 모두 체크 되므로 본 함수에서는 삭제 함
		$cart->calcu();

		// 배송비 적용 기준
		if($set['delivery']['deliveryOrder']) {
			// 정상가
			$delivery_criteria = $cart->goodsprice;
		}
		else {
			// 결제가 기준
			// (상품가 - 회원할인 - 쿠폰할인 - 상품할인(단, 체크아웃, 아이페이의 경우 제외) - 적립금(단, 적립금으로 주문시 결제금액에 적립금 주문금액 비포함 설정일때만 적립금을 제함)
			$delivery_criteria = $cart->goodsprice - $cart->dcprice - $cart->coupon - ($param['marketingType'] ? 0 : $cart->special_discount_amount) - ($set['emoney']['emoney_delivery'] == 1 ? $param['emoney'] : 0);
		}

		// 배송비 계산할 상품
		$items = $cart->item;

	}else{

		// 배송비 적용 기준
		if($set['delivery']['deliveryOrder']) {
			// 정상가
			$delivery_criteria = $param['price'];
		}
		else {
			// 결제가 기준 (상동. 단, 본 루틴에서는 회원 할인액, 적립금 사용액이 존재할 수 없음)
			$delivery_criteria = $param['price'] - $param['coupon'];
		}

		// 배송비 계산할 상품
		$items[] = $param;

	}

	/*
	 * 지역별 배송비
	 * assign to $_extra_fee
	 */
	if($param['zipcode']){
		$areaDelivery = Core::loader('areaDelivery');
		$_extra_fee = $areaDelivery->getPay($param);
	}

	/*
	 * 상품별 배송비 계산
	 *
	 * 0 : 기본 배송
	 * 1 : 무료배송
	 * 2 : 상품별 배송비 (더이상 사용하지 않음)
	 * 3 : 착불배송비
	 * 4 : 고정 배송비
	 * 5 : 수량별 배송비
	 */
	$r_goods = array();
	$extra_fee	= array();	// 각 배송방법별 지역별 추가 배송비
	$seperate_delivery = array();
	$seperate_delivery['prepay_total'] = 0;		// 개별 배송비 총 합 (추가 배송비 포함안됨)
	$seperate_delivery['prepay_max'] = 0;		// 개별 배송비 中 가장 큰 배송비

	for ($i=0,$item_cnt = sizeof($items);$i<$item_cnt;$i++) {

		$_item = $items[$i];
		$_item['ea'] = (int)$_item['ea'] > 0 ? $_item['ea'] : 1;	// 수량

		$_delivery_price = $_item['goods_delivery'];	// 상품 배송비

		switch((int)$_item['delivery_type']) {
			case 0:	## 기본배송상품
				$r_goods['basic'][] = $_item['goodsno'];
				$r_goods['basic_each'][$_item['goodsno']] = '0';

				// 지역별 추가 배송비
				$extra_fee['basic']	= $_extra_fee;								// 기본 배송 정책은 한주문에 하나 이므로 지역별 추가 배송비 한번만 부과
				break;
			case 1:	## 무료배송상품
				$r_goods['free'][] = $_item['goodsno'];
				$r_goods['free_each'][$_item['goodsno']][$_item['optno']] = '0';

				// 지역별 추가 배송비
				if ($set['delivery']['add_extra_fee_duplicate_free'] == '1') {
					$extra_fee['free']	= $extra_fee['free'] + $_extra_fee;		// 항목별 중복 부과
				} else {
					$extra_fee['free']	= $_extra_fee;							// 한번만 부과
				}
				break;
			case 2:	## 개별배송상품 (더이상 사용하지 않음)
				$r_goods['each'][] =  $_item['goodsno'];
				$seperate_delivery['prepay_total'] += $_delivery_price;
				$seperate_delivery['extra_fee'] += $_extra_fee;
				if($seperate_delivery['prepay_max'] < $_delivery_price){
					$seperate_delivery['prepay_max'] = $_delivery_price;
				}

				// 지역별 추가 배송비
				if ($set['delivery']['add_extra_fee_duplicate_each'] == '1') {
					$extra_fee['each']	= $extra_fee['each'] + $_extra_fee;		// 항목별 중복 부과
				} else {
					$extra_fee['each']	= $_extra_fee;							// 한번만 부과
				}
				break;
			case 3:	## 착불배송상품
				$r_goods['after'][] =  $_item['goodsno'];
				$seperate_delivery['ondelivery_total'] += $_delivery_price;
				if($seperate_delivery['ondelivery_max'] < $_delivery_price){
					$seperate_delivery['ondelivery_max'] = $_delivery_price;
				}
				$r_goods['after_each'][$_item['goodsno']] = '0';

				// 지역별 추가 배송비
				$extra_fee['after']		= 0;									// 착불 배송은 지역별 추가 배송비 0원
				break;
			case 4:	## 고정 배송비 (같은 상품 이면 옵션이 달라도 수량 관계 없이 배송비 1회 부과)
				if (@in_array($_item['goodsno'], $r_goods['each'])) continue;
				$r_goods['each'][] =  $_item['goodsno'];
				$r_goods['fix_each'][$_item['goodsno']] = $_delivery_price;
				$seperate_delivery['prepay_total'] += $_delivery_price;
				if($seperate_delivery['prepay_max'] < $_delivery_price){
					$seperate_delivery['prepay_max'] = $_delivery_price;
				}

				// 지역별 추가 배송비
				if ($set['delivery']['add_extra_fee_duplicate_fixEach'] == '1') {
					$extra_fee['fix_each']	= $extra_fee['fix_each'] + $_extra_fee;		// 항목별 중복 부과
				} else {
					$extra_fee['fix_each']	= $_extra_fee;								// 한번만 부과
				}
				break;
			case 5:	## 수량별 배송비 (배송비가 수량에 비례하여 커짐)
				$r_goods['each'][] =  $_item['goodsno'];
				$r_goods['cnt_each'][$_item['goodsno']] += ($_delivery_price * $_item['ea']);
				$r_goods['cnt_each_one_delivery_ptice'][$_item['goodsno']] = $_delivery_price;
				$seperate_delivery['prepay_total'] += ($_delivery_price * $_item['ea']);
				if($seperate_delivery['prepay_max'] < $_delivery_price){
					$seperate_delivery['prepay_max'] = $_delivery_price;
				}

				// 지역별 추가 배송비
				$extra_fee['cnt_each']	= $extra_fee['cnt_each'] + ($_extra_fee * $_item['ea']);	// 수량별 배송비 상품은 수량과 상품마다 중복 부과
				break;
		}

	}

	// 무료배송상품의 경우 지역별 추가 배송비 부가 여부 (여기시 미리 계산을 해서 아래에서 사용함)
	if ($set['delivery']['add_extra_fee_free'] == '1') {
		$extra_fee['free']	= $extra_fee['free'];		// 무료배송 시 지역별 추가 배송비 받음
	} else {
		$extra_fee['free']	= 0;						// 무료배송 시 지역별 추가 배송비 받지 않음
	}

	/*
	 * 회원 그룹별 혜택 (무료배송)
	 */
	if($param['mode'] === 0 && !empty($member_group)) {
		switch($member_group['free_deliveryfee']){
			case 'Y': $tmp_deliveryfee_chk = true; break;
			case 'N': $tmp_deliveryfee_chk = false; break;
			case 'goods':
			case 'settle_amt':

				if ($member_group['free_deliveryfee'] == 'settle_amt')
					$delivery_criteria = $cart->goodsprice - $cart->dcprice - $cart->coupon - ($param['marketingType'] ? 0 : $cart->special_discount_amount) - ($set['emoney']['emoney_delivery'] == 1 ? $param['emoney'] : 0);
				else
					$delivery_criteria = $cart->goodsprice;

				if( $delivery_criteria >= $member_group['free_deliveryfee_std_amt'] ) $tmp_deliveryfee_chk = true;
				break;
		}

		if( $tmp_deliveryfee_chk === true) {
			// 지역별 추가 배송비 : 회원 그룹별 무료배송 혜택의 경우는 기본 배송정책에 의한 배송비가 0원 이므로 지역별 추가 배송비는 "기본 배송정책에 의한 조건부 무료인 경우"에 의해 부가 여부가 결정됨
			if ($set['delivery']['add_extra_fee_basic'] == '1') {
				$extra_fee['basic']	= $extra_fee['basic'];		// 무료배송 시 지역별 추가 배송비 받음
			} else {
				$extra_fee['basic']	= 0;						// 무료배송 시 지역별 추가 배송비 받지 않음
			}

			$delivery['type'] = '무료';
			$delivery['extra_price'] = $delivery['price'] = ($set['delivery']['add_extra_fee_memberGroup'] == '1') ? array_sum($extra_fee) : 0;
			$delivery['unableMenu'] = 1;	// 배송 수단 선택 불가
			$delivery['msg'] = number_format($delivery['price']).' 원 (회원등급)';

			foreach($items as $_item) $delivery['order_delivery_item'][1][$_item['goodsno']][$_item['optno']] = 0;//orderDeliveryItem.class에서 사용될 값 선언

			return $delivery;
		}
	}


	/*
	선불, 무료 -> 결제금액 있음
	선불, 착불 -> 결제금액 있음
	무료, 착불 -> 결제금액 없음
	*/
	$delivery['type'] = '';
	$delivery['price'] = 0;
	$delivery['extra_price'] = 0;	// $delivery['price'] 中 지역별 추가 배송비


	// 기본 배송비 처리
	$ctype_cnt = sizeof($r_goods['basic']);
	if($ctype_cnt > 0) {

		### 기본 배송비 처리
		$delivery['type'] = $default_delivery_policy[1];	// 선불 or 후불
		$delivery['price'] = $default_delivery_policy[2];
		$basic_delivery_price = $default_delivery_policy[2];

		$delivery['default_name'] = $default_delivery_policy[4];
		$delivery['free'] = (int)$default_delivery_policy[0];	// 기본 배송시, 무료배송 기준 금액 (연산에는 사용되지 않음)
		$delivery['free_criteria'] = $set['delivery']['deliveryOrder'] ? 'order' : 'pay';

		if($delivery['type'] == '후불'){
			// 기본 배송비 정책 상품만 있는 경우
			if ($ctype_cnt === $item_cnt) {
				$delivery['msg']	= $default_delivery_policy[3];
			}
			$delivery['price']		= 0;
			$extra_fee['basic']		= 0;						// 후불 이므로 배송시 지역별 추가 배송비 받지 않음
		}

		// 조건부 무료(~이상 무료배송. 기본 배송 상품에 한함)
		if ((int)$default_delivery_policy[0] > 0 && (int)$default_delivery_policy[0] <= $delivery_criteria) {
			$delivery['type'] = '무료';
			$delivery['freeDelivery'] = 1;
			$delivery['price']		= 0;						// 조건부 무료 이므로 기본 배송비는 0원처리

			// 지역별 추가 배송비
			if ($set['delivery']['add_extra_fee_basic'] == '1') {
				$extra_fee['basic']	= $extra_fee['basic'];		// 무료배송 시 지역별 추가 배송비 받음
			} else {
				$extra_fee['basic']	= 0;						// 무료배송 시 지역별 추가 배송비 받지 않음
			}

			$delivery['default_type_conditional_free'] = 1;	// 조건부 무료
			$basic_delivery_price = 0;
		}

		$delivery['default_type'] = $delivery['type'];
		$delivery['default_price']	= $basic_delivery_price;	// 기본 배송 정책 상품의 기본 배송정책 배송비용

		foreach($r_goods['basic_each'] as $basic_goods_no => $null) $r_goods['basic_each'][$basic_goods_no] = $basic_delivery_price;

		$delivery['order_delivery_item'][0] = $r_goods['basic_each'];

		// 기본 배송비 정책 상품만 있는 경우
		if ($ctype_cnt === $item_cnt) {
			// 조건부 무료인 경우 메시지
			if ($delivery['default_type_conditional_free'] === 1) {
				$delivery['msg']		= number_format($delivery['price']).' 원 (조건부 무료)';
			}
			$delivery['extra_price']	= $extra_fee['basic'];
			$delivery['price']			= $delivery['price'] + $delivery['extra_price'];
			return $delivery;
		}
	}




	// 선결제 개별 배송비(고정배송비, 수량별 배송비)
	$ctype_cnt = sizeof($r_goods['each']);
	if ($ctype_cnt > 0 && ($seperate_delivery['prepay_total'] > 0 || $seperate_delivery['prepay_max'] > 0)) {

		// 선결제 이므로, 착불 금액은 포함시키지 아니함.
		if ($delivery['type'] == '후불') $delivery['price'] = 0;

		$delivery['type'] = '선불';

		switch ((int)$set['delivery']['goodsDelivery']) {
			case 0:			// 상품별 배송비와 기본배송비를 합산한 금액을 배송비로 합니다.
				$delivery['price'] = $delivery['price'] + $seperate_delivery['prepay_total'];

				/* 배송비 정보 별도저장시 필요정보 가공 */
				if(isset($r_goods['basic_each'])) $delivery['order_delivery_item'][0] = $r_goods['basic_each'];//기본배송
				if(isset($r_goods['fix_each'])) $delivery['order_delivery_item'][4] = $r_goods['fix_each'];//고정배송
				if(isset($r_goods['cnt_each'])) $delivery['order_delivery_item'][5] = $r_goods['cnt_each'];//수량별배송
				break;
			case 1:			// 상품별 배송비와 기본배송비 중 더 큰 배송비로 합니다.

				/* 배송비 정보 별도저장시 필요정보 가공 */
				if(isset($r_goods['basic_each'])) $tmp_delivery[0] = $r_goods['basic_each'];//기본배송비
				if(isset($r_goods['fix_each'])) $tmp_delivery[4] = $r_goods['fix_each'];//고정배송비
				if(isset($r_goods['cnt_each'])) $tmp_delivery[5] = $r_goods['cnt_each'];//수량별배송비
				/*
					$tmp_delivery
					Array
					(
						[0] => Array
							(
								[상품번호] => 값없음
							)

						[4] => Array
							(
								[상품번호] => 고정배송비 금액
							)

						[5] => Array
							(
								[상품번호] => 수량별 배송비 합계금액
							)
					)
				*/

				if(isset($tmp_delivery) && is_array($tmp_delivery) && !empty($tmp_delivery)) {
					$max_delivery = 0;
					// 상품별 배송비와 기본배송비 중 큰 배송타입을 구한다.
					foreach($tmp_delivery as $deli_type => $deli_data) {
						if($deli_type == '5') $deli_data = $r_goods['cnt_each_one_delivery_ptice'];//수량별 배송비는 1개당 배송비로 계산한다.
						arsort($deli_data);
						foreach($deli_data as $goodsno => $deli_price) {
							if($max_delivery < $deli_price) {
								$max_delivery = $deli_price;
								unset($max_delivery_type);
								$max_delivery_type[$deli_type][$goodsno] = $deli_price;
								$delivery['price'] = $deli_price;
							}
							break;
						}
					}

					// 위에서 설정된 배송타입을 제외한 나머지 배송타입은 0원으로 변경
					foreach($tmp_delivery as $deli_type => $deli_data) {
						foreach($deli_data as $goodsno => $deli_price) {
							if(isset($max_delivery_type[$deli_type][$goodsno]) === false) $max_delivery_type[$deli_type][$goodsno] = 0;
						}
					}

					$delivery['order_delivery_item'] = $max_delivery_type;
				}

				break;
			case 2:			// 상품별 "배송비의 합"과 기본배송비 중 더 큰 배송비로 합니다.
				$delivery['price'] = ($delivery['price'] < $seperate_delivery['prepay_total']) ? $seperate_delivery['prepay_total'] : $delivery['price'];

				/* 배송비 정보 별도저장시 필요정보 가공 */
				if(isset($r_goods['basic_each'])) $tmp_delivery[0] = $r_goods['basic_each'];//기본배송비
				if(isset($r_goods['fix_each'])) $tmp_delivery[4] = $r_goods['fix_each'];//고정배송비
				if(isset($r_goods['cnt_each'])) $tmp_delivery[5] = $r_goods['cnt_each'];//수량별배송비

				$tmp_basic_delivery_price = 0;//기본배송 배송비
				$tmp_goods_delivery_price = 0;//상품별 배송비

				foreach($tmp_delivery as $tmp_delivery_type => $tmp_detail_data) {
					switch($tmp_delivery_type) {//무료배송, 착불배송은 배송비가 0원으로 제외
						case '0' : //기본배송비
							foreach($tmp_detail_data as $goodsno => $tmp_basic_delivery_price) {//기본배송 배송비 설정
								break;
							}
							break;
						case '4' : //고정배송비
						case '5' : //수량별 배송비
							foreach($tmp_detail_data as $goodsno => $set_goods_delivery_price) {
								$tmp_goods_delivery_price += $set_goods_delivery_price;
							}
							break;
					}
				}

				if($tmp_basic_delivery_price >= $tmp_goods_delivery_price) {//기본배송비가 상품별 배송비보다 큰 경우
					foreach($tmp_delivery as $tmp_delivery_type => $tmp_detail_data) {
						if($tmp_delivery_type != '0') {
							foreach($tmp_detail_data as $goodsno => $drop_data) {
								$delivery['order_delivery_item'][$tmp_delivery_type][$goodsno] = 0;
							}
						}
						else $delivery['order_delivery_item'][$tmp_delivery_type] = $tmp_detail_data;
					}
				}
				else if($tmp_basic_delivery_price < $tmp_goods_delivery_price) {//기본배송비가 상품별 배송비보다 작은 경우
					foreach($tmp_delivery as $tmp_delivery_type => $tmp_detail_data) {
						if($tmp_delivery_type == '0') {
							foreach($tmp_detail_data as $goodsno => $drop_data) {
								$delivery['order_delivery_item'][$tmp_delivery_type][$goodsno] = 0;
							}
						}
						else $delivery['order_delivery_item'][$tmp_delivery_type] = $tmp_detail_data;
					}
				}
				break;
		}

	}

	// 개별 착불
	$ctype_cnt = sizeof($r_goods['after']);
	if ($ctype_cnt > 0) {

		$delivery['on_delivery_each_goods'] = 1;

		// 구매상품이 모두 개별 착불 상품일때
		if ($ctype_cnt === $item_cnt) {
			$delivery['type'] = '후불';
			$delivery['msg'] = '개별 착불 배송비';
			$delivery['unableMenu'] = 1;	// 배송 수단 선택 불가
			$delivery['price'] = 0;
			$delivery['_price'] = $seperate_delivery['ondelivery_total'];	// 체크아웃 착불 배송비 계산
			$delivery['order_delivery_item'][3] = $r_goods['after_each'];
			return $delivery;
		}
		else {
			if ($delivery['type'] == '후불') {
				//$delivery['price'] = $delivery['price'] + $seperate_delivery['ondelivery_total'];
				$delivery['price'] = 0;
				$delivery['order_delivery_item'][3] = $r_goods['after_each'];
			}
			elseif (empty($delivery['type'])) {
				$delivery['type'] = '후불';
				$delivery['price'] = 0;
			}

			$delivery['order_delivery_item'][3] = $r_goods['after_each'];
		}
	}

	// 개별 무료배송
	$ctype_cnt = sizeof($r_goods['free']);
	if($ctype_cnt > 0) {

		$delivery['free_each_goods'] = 1;

		// "무료배송 상품을 같이 주문했을 경우, 배송비를 무조건 무료로 합니다." 설정이 적용되었거나, 구매상품이 모두 무료배송 상품일때.
		if ($ctype_cnt === $item_cnt || $set['delivery']['freeDelivery'] == 1) {
			$delivery['price']			= 0;						// 무료배송이므로 배송비는 0원
			$delivery['freeDelivery']	= 1;
			$delivery['type']			= '무료';
			$tmp_extra_price			= array_sum($extra_fee);					// 무료 배송이라도 지역별 추가 배송비는 설정에 따라 부과
			$tmp_delivery_price			= $delivery['price'] + $tmp_extra_price;	// 지역별 추가 배송비를 더한 금액이 총 배송비
			$delivery['msg']			= number_format($tmp_delivery_price).' 원';	// 화면 출력용 배송비 내역 (실제 배송비는 아래에서 처리함)
			if (sizeof($r_goods['basic']) > 0) $delivery['default_type_conditional_free'] = 1;

			if(isset($delivery['order_delivery_item'])) {
				$free_set_data = $delivery['order_delivery_item'];
				$free_set_data[1] = $r_goods['free_each'];

				unset($delivery['order_delivery_item']);
				foreach($free_set_data as $free_set_delivery_type => $tmp_delivery) {
					foreach($tmp_delivery as $tmp_goodsno => $arr_optno) {
						if(is_array($arr_optno)) {
							foreach($arr_optno as $optno => $null) $delivery['order_delivery_item'][$free_set_delivery_type][$tmp_goodsno][$optno] = '0';
						}
						else $delivery['order_delivery_item'][$free_set_delivery_type][$tmp_goodsno] = '0';
					}
				}
			}
			else {
				$delivery['order_delivery_item'][1] = $r_goods['free_each'];
			}
		}
		else {
			if ($delivery['type'] == '후불') {
				$delivery['price'] = 0;
				$extra_fee['free']	= 0;							// 후불 이므로 배송시 지역별 추가 배송비 받지 않음
			}
			elseif (empty($delivery['type'])) {
				$delivery['type'] = '무료';
				$delivery['price']		= 0;						// 무료배송이므로 배송비는 0원
			}

			$delivery['order_delivery_item'][1] = $r_goods['free_each'];
		}

	}

	// 지역별 추가 배송비 총 금액 및 총 배송비를 처리를 함
	$delivery['extra_price']	= array_sum($extra_fee);							// 지역별 추가 배송비 총 금액
	$delivery['price']			= $delivery['price'] + $delivery['extra_price'];	// 총 배송비

	return $delivery;
}

### 할인액 계산
function getDcprice($price,$dc)
{
	global $set;
	if (!$dc) return 0;
	if ($set['emoney']['cut']>0) $po = pow(10,$set['emoney']['cut']);
	else if($set['emoney']['cut'] == '0') $po = 1;
	else $po = 100;
	$price = (substr($dc,-1)=="%") ? $price * substr($dc,0,-1) / 100 : $dc;
	return floor($price / $po) * $po;
}

### 적립금 유효성 체크
function chkEmoney($set,$emoney)
{
	if ($emoney<0) msg("결제 적립금은 0원 이상이어야 합니다",-1);
	if ($emoney && $set['hold'] > $GLOBALS[member][emoney])msg("보유하신 적립금이 ".$set['hold']."보다 적습니다.",-1);

	if($set['emoney_use_range'])$erange = $GLOBALS[cart]->totalprice;
	else $erange = $GLOBALS[cart]->goodsprice;
	$max_emoney = getDcprice($erange,$set[max]);

	if ($emoney && $GLOBALS[member][emoney]<$emoney) msg("결제 적립금이 보유하신 적립금보다 많습니다",-1);
	if ($set[min] && $emoney && $emoney<$set[min]) msg("결제 적립금이 최저 사용 적립금 ".number_format($set[min])."원보다 적습니다",-1);
	if ($max_emoney && $emoney>$max_emoney) msg("결제 적립금이 최대 사용 적립금 ".number_format($max_emoney)."원보다 많습니다",-1);
}

### 회원인증여부
function chkMember()
{
	if (!$GLOBALS[sess]) msg("로그인하셔야 본 서비스를 이용하실 수 있습니다","../member/login.php");
}
function chkMemberPopup()
{
	if (!$GLOBALS[sess]){
		echo("
		<script>
			alert('로그인하셔야 본 서비스를 이용하실 수 있습니다');
			opener.location.href='../member/login.php';
			self.close();
		</script>");
		exit;
	}
}
function chkMemberPopupConfirm()
{
	if (!$GLOBALS[sess]){
		echo("
		<script>
			if(confirm('회원전용 서비스 입니다. 로그인/회원가입 페이지로 이동하시겠습니까?')){
				opener.location.href='../member/login.php';
			}
			self.close();
		</script>");
		exit;
	}
}

function checkCoupon($orderitems=array(),$coupon=0,$coupon_emoney=0,$apply_coupon=array(),$settlekind='') {

	global $sess, $db, $set;

	$_now = time();

	$goods = Core::loader('Goods');

	// 금액 단위 절삭 (getDcprice 함수 인용)
	if ($set['emoney']['cut']>0) $po = pow(10,$set['emoney']['cut']);
	else if($set['emoney']['cut'] == '0') $po = 1;
	else $po = 100;

	// 변수 초기화
	$total['saving'] = $total['discount'] = 0;	// 적립, 할인 = 0

	if (((int)$coupon > 0 || (int)$coupon_emoney > 0) && sizeof($apply_coupon) > 0) {

		// 전체 상품가격을 구함
		$total['price'] = 0;
		foreach ($orderitems as $item) $total['price'] = $total['price'] + ($item['price'] * $item['ea']);

		foreach ($orderitems as $item) {

			$ar_category = $goods->get_goods_category($item['goodsno']);
			if (!empty($ar_category)) foreach($ar_category as $k=>$v) $ar_category[$k]="'".$v."'";

			foreach($apply_coupon as $_couponcd) {

				$_ea = $item['ea'];
				$_coupon = array();

				// 오프라인 쿠폰
				if (preg_match('/^off_([0-9]+)$/',$_couponcd,$_match)) {

					$today = date("YmdH");

					$query = "
					SELECT
						CP.*,
						CP_G.goodsno,
						CP_G.category

					FROM ".GD_OFFLINE_DOWNLOAD." AS CP_DN

					INNER JOIN ".GD_OFFLINE_COUPON." AS CP
					ON CP_DN.coupon_sno = CP.sno

					LEFT JOIN ".GD_OFFLINE_GOODS." AS CP_G
					ON CP_DN.coupon_sno = CP_G.coupon_sno

					WHERE
						CP_DN.m_no = '".$sess['m_no']."'
					AND CP_DN.coupon_sno = '".$_match[1]."'
					AND (
							CP.goods_apply='all'
						OR	(CP.goods_apply='limited' AND CP_G.goodsno = '".$item['goodsno']."')
						OR  (CP.goods_apply='limited' AND CP_G.category IN ( ".implode(',',$ar_category)." ))
						)

					AND CONCAT(CP.start_year,CP.start_mon,CP.start_day,CP.start_time) <= '$today'
					AND CONCAT(CP.end_year,CP.end_mon,CP.end_day,CP.end_time) >= '$today'
					AND	CP.`status`!='disuse'
					";

					if ($settlekind != 'a') {
						$query .= " AND CP.pay_method = 'unlimited' ";
					}

					if (($_cp = $db->fetch($query,1)) == false) continue;

					// 데이터 정리
					$_coupon['type'] = $_cp[coupon_type] == 'sale' ? 'dc' : 'save';	// 할인 or 적립
					$_coupon['price'] = $_cp[coupon_price].($_cp[currency] == '%' ? '%' : '');
					$_coupon['limit'] = $_cp[limit_amount];

				}
				// 그냥 쿠폰
				else {

					$today = date("Y-m-d H:i:s");

					$query = "
					SELECT
						coupon.*
					FROM ".GD_COUPON." AS coupon
					INNER JOIN ".GD_COUPON_APPLY." AS apply
						ON apply.couponcd=coupon.couponcd
					LEFT JOIN ".GD_COUPON_CATEGORY." AS cate
						ON coupon.couponcd = cate.couponcd
					LEFT JOIN ".GD_COUPON_GOODSNO." AS goods
						ON coupon.couponcd = goods.couponcd
					LEFT JOIN ".GD_COUPON_APPLYMEMBER." AS mem
						ON apply.sno=mem.applysno
					WHERE
						coupon.couponcd = '".$_couponcd."'
					AND (
								(coupon.sdate <= '$today' AND coupon.edate >= '$today' AND coupon.priodtype='0')
							OR
								(
										coupon.priodtype='1'
									AND (coupon.edate >= '$today' OR  coupon.edate = '')
									AND ADDDATE(apply.regdt,INTERVAL coupon.sdate DAY) >= '".date("Y-m-d")." 00:00:00'
								)
						)
					AND ((mem.m_no='".$sess['m_no']."') OR (membertype='0') OR (apply.membertype='1' AND apply.member_grp_sno='".$sess['groupsno']."'))
					AND ((apply.goodsno='".$item['goodsno']."' AND coupon.coupontype='1') OR (apply.goodsno='0' AND coupon.coupontype!='1'))
					AND apply.status='0'
					";

					if($ar_category && $item['goodsno']){
						$query .= "AND (((cate.category in(".implode(',',$ar_category).") OR goods.goodsno = '".$item['goodsno']."') AND coupon.goodstype='1') OR coupon.goodstype='0')";
					}else if($item['goodsno']){
						$query .= "AND ((goods.goodsno = '".$item['goodsno']."' AND coupon.goodstype='1') OR coupon.goodstype='0')";
					}

					if ($settlekind != 'a') {
						$query .= " AND coupon.payMethod = '0' ";
					}

					if (($_cp = $db->fetch($query,1)) == false) continue;

					// 데이터 정리
					$_coupon['type'] = $_cp[ability] == 0 ? 'dc' : 'save';	// 할인 or 적립
					$_coupon['price'] = $_cp[price];
					$_coupon['limit'] = $_cp[excPrice];

					// 회원 직접 다운로드 쿠폰인 경우 쿠폰 적용 수량 제어
					if ($_cp['coupontype'] == '1' && $_cp['eactl'] != '1') $_ea = 1;

				}

				if (preg_match('/^([0-9]+)\%$/',$_coupon['price'],$_match)) {
					//$_amount = $item[price] * $_ea * $_coupon[price] / 100;
					$_amount = getDcprice($item[price], $_coupon[price]);
				}
				else {
					//$_amount = $_coupon['price'] * ($_cp['coupontype'] == '1' ? $_ea : 1);
					$_amount = getDcprice($item[price], $_coupon['price']);
					if ($_cp['coupontype'] != '1') $_ea = 1;
				}

				$_amount = $_amount * $_ea;

				// 쿠폰 사용 제한금액
				if ($_coupon['limit'] > 0 && $total['price'] < $_coupon['limit']) continue;

				if ($_coupon['type'] == 'dc') $total['discount'] = $total['discount'] + $_amount;
				else $total['saving'] = $total['saving'] + $_amount;

			}

		}

	}

	// 쿠폰 사용액이 올바른가?
	if ($coupon > $total['discount']) {
		msg('쿠폰 할인액이 올바르지 않습니다.',-1);
		exit;
	}

	if ($coupon_emoney > $total['saving']) {
		msg('쿠폰 적립액이 올바르지 않습니다.',-1);
		exit;
	}

}

### 주문정보 유효성 체크
function chkCart(&$cart,$arr='')
{
	global $db,$sess,$ableDc,$ableCoupon;

	if( !$ableDc )$memberdc = 0;
	else $memberdc = $cart->dc;

	foreach ($cart->item as $k=>$item){

		$query = "
		select
			a.goodsno,a.goodsnm,a.open,a.todaygoods,b.*
		from
			".GD_GOODS." a
			left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno and go_is_deleted <> '1' and go_is_display = '1'
		where
			a.goodsno = '$item[goodsno]'
			and b.opt1 = '".mysql_real_escape_string($item[opt][0])."'
			and b.opt2 = '".mysql_real_escape_string($item[opt][1])."'
		";
		$data = $db->fetch($query);

		if (!$data['open']) {
			// 투데이샵 상품은 field 가 다름
			if ($data['todaygoods'] == 'y') {
				$t_data = $db->fetch("SELECT visible FROM ".GD_TODAYSHOP_GOODS_MERGED." WHERE goodsno = '$data[goodsno]'",1);
				if ($t_data['visible'] == 0) msg($data['goodsnm']." 상품은 현재 진열중인 상품이 아닙니다.", -1);
			}
			else {
				msg($data['goodsnm']." 상품은 현재 진열중인 상품이 아닙니다.", -1);
			}
		}

		if ($data[price]!=$item[price]) msg("상품가격이 일치하지 않습니다",-1);
		if ($data[goodsnm]!=$item[goodsnm]) msg("상품명이 일치하지 않습니다",-1);

		$item_price = $data[price];
		if($memberdc) $item_price -= getDcPrice($data[price],$memberdc);


		### 추가옵션 체크
		if (sizeof((array)$item['addopt']) !== sizeof(array_notnull(explode(',',$item['addno'])))) {
			msg($data['goodsnm']." 추가옵션정보를 확인해 주세요.", -1);
		}

		if ($item[addopt]){
			foreach ($item[addopt] as $v){
				$query = "
				select * from
					".GD_GOODS_ADD."
				where
					goodsno = '$item[goodsno]'
					and sno = '$v[sno]'
				";
				$dataAdd = $db->fetch($query);

				if ($dataAdd[addprice]!=$v[price]) msg("옵션추가가격이 일치하지 않습니다",-1);
				if ($dataAdd[opt]!=$v[opt]) {
					if ($dataAdd['type'] == 'I') {
						$dataAdd[opt] = intval($dataAdd[opt]);
						if ($dataAdd[opt] === 0 || $dataAdd[opt] >= mb_strlen($v[opt], Clib_Application::getConfig('global', 'charset'))) {
							continue;
						}
					}

					msg("추가옵션이 일치하지 않습니다",-1);

				}

			}
		}
	}
}

### 배열값에서 정규식 검색
function array_ereg( $pattern, $haystack ){

	$tmp = array();

	foreach ( $haystack as $v ){
		if ( preg_match( $pattern, $v ) ) $tmp[] = $v;
	}

	return $tmp;
}

### 코드반환
function codeitem( $groupcd ){

	global $db;

	$tmp = array();
	$res = $db->query("select itemcd, itemnm from ".GD_CODE." where groupcd!='' and groupcd='$groupcd' order by sort");
	while( $row = $db->fetch($res) ) $tmp[ $row['itemcd'] ] = $row['itemnm'];

	return $tmp;
}

### 회원그룹반환
function member_grp( $level='' ){

	global $db;

	$tmp = array();

	if ( $level == '' ){
		$res = $db->query("select * from ".GD_MEMBER_GRP." order by level");
		while( $row = $db->fetch($res) ) $tmp[] = $row;
	}
	else {
		list( $tmp['grpnm'], $tmp['level'], $tmp['dc'] ) = $db->fetch("select grpnm,level,dc from ".GD_MEMBER_GRP." where level='$level'");
	}

	return $tmp;
}

### Block용 태그 존재여부 리턴
### 참고) Block용 태그 : <P>, <OL>, <UL>, <TABLE>, <DIV>, <H1>~<H6>
function blocktag_exists( $string ){

	preg_match_all( "'<(p|ol|ul|table|div|h1|h2|h3|h4|h5|h6)'si", $string, $matches );

	if ( count( $matches[0] ) > 0 ) return true;
	else return false;
}

### 회원로그인 로그 남기기
function member_log( $m_id ){

	$log_msg = "";
	$log_msg .= date('Y-m-d H:i:s') . "\t";
	$log_msg .= $_SERVER['REMOTE_ADDR'] . "\t";
	$log_msg .= $m_id . "\n";

	error_log($log_msg, 3, $tmp = dirname(__FILE__) . "/../log/login_" . date('Ym') . ".log");
	@chmod( $tmp, 0707 );
}

### SMS 90byte 이상 일때 메세지 전송 방법에 따른 전송 (90byte만, 분할발송)
function sendSmsLenType($msg,$mobile,$case,$maxLen=90){
	include_once dirname(__FILE__)."/sms.class.php";
	$sms = new Sms();
	$sms_sendlist = $sms->loadSendlist();

	$msg_len = strlen($msg); // 메세지 길이
	if ($msg_len > $maxLen){
		if ($GLOBALS['cfg']['smsAutoSendType'] == 'MULTI'){ // 분할발송
			$msg = gd_str_split($msg,$maxLen);
			$msg_size = sizeof($msg);
			// 분할 발송일 경우 순서대로 받기위해 예약발송으로 발송
			$reserve_time = time() + 15;
			for ($i=0;$i<$msg_size;$i++) {
				$reserve_time += 1;
				$sms->regdt = date('Y-m-d H:i:s', $reserve_time);
				$sms->log($msg[$i],$mobile,$case,1);
				$sms_sendlist->setSimpleInsert($mobile, $sms->smsLogInsertId, '');	 //sendlist
				$sms->send($msg[$i], $mobile, $GLOBALS['cfg']['smsRecall'], $sms->regdt, '', 'send');
				$sms->update_ok_eNamoo = true;
				$sms->update();
			}
		} else { // 90byte까지만 발송
			$sms->log($msg,$mobile,$case,1);
			$sms_sendlist->setSimpleInsert($mobile, $sms->smsLogInsertId, '');	 //sendlist
			$sms->send($msg,$mobile,$GLOBALS['cfg']['smsRecall']);
			$sms->update_ok_eNamoo = true;
			$sms->update();
		}
	} else {
		$sms->log($msg,$mobile,$case,1);
		$sms_sendlist->setSimpleInsert($mobile, $sms->smsLogInsertId, '');	 //sendlist
		$sms->send($msg,$mobile,$GLOBALS['cfg']['smsRecall']);
		$sms->update_ok_eNamoo = true;
		$sms->update();
	}
}

### 상황별 SMS 발송
function sendSmsCase($case,$mobile)
{
	global $r_sendDateCode, $r_sendDateDefault;

	$maxLen = 90;
	@include dirname(__FILE__)."/../conf/sms/$case.php";
	if (is_array($sms_auto)) extract($sms_auto);

	// 발송대상 체크
	if (in_array($case, $r_sendDateCode['sms'])) {
		// 체크여부
		$_sendCheck	= true;

		// 주문일 및 주문번호 여부를 확인 (Unix timestamp 기준으로 체크)
		if (empty($GLOBALS['dataSms']['orddt']) === false) {
			$ordnoCompare	= strtotime($GLOBALS['dataSms']['orddt']);
		} else if (empty($GLOBALS['dataSms']['ordno']) === false) {
			$ordnoCompare	= substr($GLOBALS['dataSms']['ordno'], 0, 10);	// 주문번호에서 timestamp 인 10자리만 추출
		} else {
			$_sendCheck		= false;
		}

		// 체크 대상인 경우
		if ($_sendCheck === true) {
			// 기준일을 추출 (Unix timestamp 기준으로 체크)
			if (empty($sendDate)) {
				$ordnoCheck	= strtotime(($r_sendDateDefault['sms'] * -1).' day');
			} else {
				$ordnoCheck	= strtotime(($sendDate * -1).' day');
			}

			// 설정된 발송기간이 지난 경우 return
			if ($ordnoCompare < $ordnoCheck) {
				return;
			}
		}
	}

	#사용자에게 전송
	if ($send_c && $mobile){
		$msg_c = parseCode($msg_c);
		sendSmsLenType($msg_c,$mobile,$case,$maxLen);
	}

	#관리자에게 전송
	if ($send_a && $GLOBALS['cfg']['smsAdmin']){
		$msg_a = parseCode($msg_a);
		sendSmsLenType($msg_a,$GLOBALS['cfg']['smsAdmin'],$case,$maxLen);
	}

	#추가관리자에게 전송
	if ($send_m && $GLOBALS['cfg']['smsAddAdmin']){
		$msg_a = parseCode($msg_a);
		$smsAddAdmin = explode("|",$GLOBALS['cfg']['smsAddAdmin']);
		foreach ($smsAddAdmin as $key=>$val){
			sendSmsLenType($msg_a,$val,$case,$maxLen);
		}
	}
}

### 재고검사 후 알림 SMS 발송
function sendSmsStock($ordno) {
	global $db;

	@include dirname(__FILE__)."/../conf/config.purchase.php";
	if($purchaseSet['smsYn'] != "1" || !$purchaseSet['smsStock'] || !$purchaseSet['cp1'] || !$purchaseSet['cp2'] || !$purchaseSet['cp3']) return false;
	$rPhone = $purchaseSet['cp1'].$purchaseSet['cp2'].$purchaseSet['cp3'];

	include_once dirname(__FILE__)."/sms.class.php";
	$sms = new Sms();
	$sms_sendlist = $sms->loadSendlist();

	$sql = "SELECT * FROM ".GD_ORDER_ITEM." WHERE ordno = '$ordno'";
	$res = $db->query($sql);

	for($i = 0; $data = $db->fetch($res); $i++) {
		// 재고 검사
		list($tmpStock, $pchsno) = $db->fetch("SELECT stock, pchsno FROM ".GD_GOODS_OPTION." WHERE goodsno = '".$data['goodsno']."' AND opt1 = '".$data['opt1']."' AND opt2 = '".$data['opt2']."' and go_is_deleted <> '1' and go_is_display = '1'");
		list($checkStockSmsLog) = $db->fetch("SELECT COUNT(pslno) FROM ".GD_PURCHASE_SMSLOG." WHERE goodsno = '".$data['goodsno']."' AND opt1 = '".$data['opt1']."' AND opt2 = '".$data['opt2']."'");

		if($purchaseSet['smsStock'] > $tmpStock && $checkStockSmsLog == 0) {
			// 상품정보
			list($comnm, $phone1, $phone2) = $db->fetch("SELECT comnm, phone1, phone2 FROM ".GD_PURCHASE." WHERE pchsno = '$pchsno' LIMIT 1");
			$sPhone = ($phone1 && $phone1 != "--") ? $phone1 : $phone2;
			$msg_p = "[매진예정]
".$data['goodsnm']."
$comnm
남은수량 {$tmpStock}개";

			$sms->log($msg_p,$rPhone,"purchase",1);
			$sms_sendlist->setSimpleInsert($rPhone, $sms->smsLogInsertId, '');

			if($sms->send($msg_p,$rPhone,$sPhone)){
				list($smsLogSno) = $db->fetch("SELECT sno FROM ".GD_SMS_LOG." WHERE msg = '$msg_p' AND type = 'purchase' AND to_tran = '$rPhone' AND cnt = '1' ORDER BY regdt DESC LIMIT 1");
				$db->query("INSERT INTO ".GD_PURCHASE_SMSLOG." SET sno = '$smsLogSno', goodsno = '".$data['goodsno']."', opt1 = '".$data['opt1']."', opt2 = '".$data['opt2']."', regdt= NOW()");
				$sms->update_ok_eNamoo = true;
				$sms->update();
			}
		}
	}
}

### 상황별 이메일 발송
function sendMailCase($email,$modeMail,$data='')
{
	global $r_sendDateCode, $r_sendDateDefault;

	include dirname(__FILE__)."/../conf/config.php";
	include_once dirname(__FILE__)."/../lib/automail.class.php";

	if ($email && $cfg["mailyn_$modeMail"]=="y"){

		// 발송대상 체크
		if (in_array($modeMail, $r_sendDateCode['mail']) && empty($data) === false) {
			// 체크여부
			$_sendCheck	= true;

			// 주문일 및 주문번호 여부를 확인 (Unix timestamp 기준으로 체크)
			if (empty($data['orddt']) === false) {
				$ordnoCompare	= strtotime($data['orddt']);
			} else if (empty($data['ordno']) === false) {
				$ordnoCompare	= substr($data['ordno'], 0, 10);	// 주문번호에서 timestamp 인 10자리만 추출
			} else {
				$_sendCheck		= false;
			}

			// 체크 대상인 경우
			if ($_sendCheck === true) {
				// 기준일을 추출 (Unix timestamp 기준으로 체크)
				if (empty($cfg['mailSendDate_'.$modeMail])) {
					$ordnoCheck	= strtotime(($r_sendDateDefault['mail'] * -1).' day');
				} else {
					$ordnoCheck	= strtotime(($cfg['mailSendDate_'.$modeMail] * -1).' day');
				}

				// 설정된 발송기간이 지난 경우 return
				if ($ordnoCompare < $ordnoCheck) {
					return;
				}
			}
		}

		if($modeMail==3) {
			$automail = new automail();
			$automail->_set($modeMail,$email,$cfg);
			$automail->_assign_tpl($data[ordno]);
			$automail->_send();
		}
		else{
			$automail = new automail();
			$automail->_set($modeMail,$email,$cfg);
			$automail->_assign($data);
			$automail->_send();
		}
	}
}

### 치환코드 파싱
function parseCode($str)
{
	@extract($GLOBALS[cfg]); @extract($GLOBALS[dataSms]); @extract($_REQUEST);

	$orderhp = '';
	if(count($mobileOrder) > 1)$orderhp = implode('-',$mobileOrder);
	else $orderhp = $mobileOrder;

	$str = preg_replace("/{([a-zA-Z]+)}/","{\$$1}",$str);
	eval("\$str = \"$str\";");
	return $str;
}

### 문자열 길이만큼 자르기
function gd_str_split($msg, $len=90)
{
	$buf = '';
	$str = '';
	for($i = 0; $i < strlen($msg); $i++) {
		$buf = substr($msg, $i, 1);
		if (ord($buf) > 127) {
			if (strlen($str) == ($len-1)) {
				$rtn[] = $str;
				$str = '';
			}

			$buf = substr($msg, $i, 2);
			$i++;
		}
		$str .= $buf;
		if (strlen($str) == $len || $i == strlen($msg) - 1) {
			$rtn[] = $str;
			$str = '';
		}
	}
	return $rtn;
}

### 계정사용 체크
function disk()
{
	# 고도몰환경
	$file = dirname(__FILE__)."/../conf/godomall.cfg.php";
	if (is_file($file)){
		$file = file($file);
		$godo = decode($file[1],1);
	}

	if ( preg_match( "/^self_enamoo/i", $godo[ecCode] ) ) return; # 독립형 경우
	if ( getDu('disk') >= mb2byte($godo[maxDisk] + $godo[diskGoods]) ) return array( '001', '용량초과' ); # 용량초과
	if ( intval($godo[diskEdate]) > 1 && strlen($godo[diskEdate]) == 8 && betweenDate(date('Ymd'),$godo[diskEdate]) < 0 ) return array( '002', '기간경과' ); # 기간경과
}

### 상품아이콘 이미지 병합
function setIcon($icon,$regdt,$path="")
{
	@include dirname(__FILE__)."/../conf/my_icon.php";
	$tplSkin = $GLOBALS[cfg][tplSkin];

	/// 아이콘 갯수
	$r_myicon = isset($r_myicon) ? (array)$r_myicon : array();
	for ($i=0;$i<=7;$i++) if (!isset($r_myicon[$i])) $r_myicon[$i] = '';
	$cnt_myicon = sizeof($r_myicon);

	$arr = array('good_icon_new.gif','good_icon_recomm.gif','good_icon_special.gif','good_icon_popular.gif','good_icon_event.gif','good_icon_reserve.gif','good_icon_best.gif','good_icon_sale.gif');

	for($i=0;$i<$cnt_myicon;$i++){
		$ti_date = substr($regdt,0,10);
		$r_date = explode('-',$ti_date);

		if($r_myicon[$i])$img = "<img src='{$path}../data/my_icon/".$r_myicon[$i]."'>";
		else $img = "<img src='{$path}../data/skin/".$tplSkin."/img/icon/".$arr[$i]."'>";

		if($r_myicondt[$i]){
			$date = date('Ymd',mktime(0, 0, 0, $r_date[1], $r_date[2]+$r_myicondt[$i], $r_date[0]));
			if($date < date('Ymd',time())) $img = "";
		}
		$f = pow(2, $i);
		if ($icon&$f && $img) $tmp[] = $img;
	}
	return @implode(" ",$tmp);
}

### 결제금액(출력/최종) 계산
function set_prn_settleprice($ordno)
{
	global $db;

	$order = new order;
	$order->load($ordno);

	$csettleprice = $order->getRealSettleAmount();	// 셜결제 금액(최초 결제 금액 - 취소금액)으로 갱신한다.

	$query = "update ".GD_ORDER." set prn_settleprice = '$csettleprice' where ordno='$ordno'";
	$db->query($query);

	if($order['m_no']){ //회원일경우 회원 구매 금액 업데이트
		$query = "select ifnull(sum(prn_settleprice),'0'),ifnull(count(*),'0') from ".GD_ORDER." where step='4' and (step2 is null or step2='0') and m_no='".$order['m_no']."'";
		list($member_sum,$member_cnt) = $db->fetch($query);

		$dormantMember = false;
		$dormant = Core::loader('dormant');
		$dormantMember = $dormant->checkDormantMember(array('m_no'=>$order['m_no']), 'm_no');

		if($dormantMember === true){
			$query = $dormant->getSumSaleUpdateQuery($order['m_no'], $member_sum, $member_cnt);
		}
		else {
			$query = "update ".GD_MEMBER." set sum_sale='$member_sum',cnt_sale='$member_cnt',last_sale=now() where m_no='".$order['m_no']."'";
		}

		$db->query($query);
	}
}

function getCatename($category) {
	global $db;

	$sql = "select catnm from ".GD_CATEGORY." where category='$category'";
	list($catename) = $db->fetch($sql);

	return $catename;
}

function get_delivery($data,$set){
	//배송료
	if( $data[price] < $set['delivery']['free'] ) $d_price = $set['delivery']['default'];
	else if( $data[price] >= $set['delivery']['free'] ) $d_price = 0;

	if($data[delivery_type]) $d_price = 0;

	if($d_price)$delivery = "유료".$d_price."원 " .$set['delivery']['free']."이상 무료";
	else $delivery="무료";

	return $delivery;
}

### 컨텐츠복사방지
function copyProtect($bodyTag=false)
{
	global $cfg;
	if(!$cfg)include dirname(__FILE__)."/../conf/config.php";
	if ($cfg["copyProtect"] != "1") return;

	if ( $bodyTag === true ) echo 'oncontextmenu="return false" ondragstart="return false" onselectstart="return false"';
	else echo '<META http-equiv="imagetoolbar" CONTENT="no">';
}

### 분류 HIDDEN 갯수
function getCateHideCnt($category,$mobile=0)
{
	global $db;

	$hiddenFieldName = $mobile ? 'hidden_mobile':'hidden';

	$cates = array();
	$repeat = strlen($category) / 3 ;
	for ($i = 1; $i <= $repeat; $i++) $cates[] = substr($category, 0, $i * 3);
	list($hCnt) = $db->fetch("select count(*) from ".GD_CATEGORY." where category != '' and category in ('" . implode("','", $cates) . "') and {$hiddenFieldName}=1");

	return $hCnt;
}

### 상품분류 HIDDEN 처리
function setGoodslinkHide($category, $hidden,$mobile=0)
{
	global $db;
	global $cfgMobileShop; //모바일샵 설정정보
	if (empty($cfgMobileShop)) {
		@include(dirname(__FILE__).'/../conf/config.mobileShop.php');
	}

	// 분류코드($category) 유효성 체크
	if ($category == '') return;

//	$hiddenFieldName = $mobile ? 'hidden_mobile':'hidden';

	// 분류감추기 타겟 정의
	if ($mobile === 'mobile') { // 모바일샵 분류감추기
		$linkHiddenFieldName = 'hidden_mobile';

		if ($cfgMobileShop['vtype_category'] == 0) {
			// 모바일샵 카테고리 노출 설정이 '온라인 쇼핑몰(PC버전)과 노출설정 동일하게 적용'인 경우
			$cateHiddenFieldName = 'hidden';
			$cateHideCnt = getCateHideCnt($category);
		}
		else {
			// 모바일샵 카테고리 노출 설정이 '모바일샵 별도 노출설정 적용'인 경우
			$cateHiddenFieldName = 'hidden_mobile';
			$cateHideCnt = getCateHideCnt($category, 'mobile');
		}
	}
	else { // PC 분류감추기
		$linkHiddenFieldName = 'hidden';
		$cateHiddenFieldName = 'hidden';
		$cateHideCnt = getCateHideCnt($category);
	}

	if ($hidden == 1 || $cateHideCnt > 0) {
		$db->query("update ".GD_GOODS_LINK." set {$linkHiddenFieldName}='1' where category like '$category%'");
	}
	else {
		$res = $db->query("select category, {$cateHiddenFieldName} from ".GD_CATEGORY." where category like '$category%' order by category");
		while ($data=$db->fetch($res)){
			if ( $cateHidden[ substr($data[category],0,-3) ] == 1 ) $data[$cateHiddenFieldName] = 1;
			$cateHidden[$data[category]] = $data[$cateHiddenFieldName];
			$db->query("update ".GD_GOODS_LINK." set {$linkHiddenFieldName}='".$data[$cateHiddenFieldName]."' where category='$data[category]'");
		}
	}
}

### 위시리스트 정보 불러오기!(드래그&드롭 장바구니용)
function get_wishlist($m_no){
	global $db;

	$db_table = "
	select
		w.*,a.goodsnm,a.img_s,b.price,b.reserve
	from
	".GD_MEMBER_WISHLIST." as w
	left join ".GD_GOODS." as a on w.goodsno=a.goodsno
	left join ".GD_GOODS_OPTION." as b on w.goodsno=b.goodsno and w.opt1=b.opt1 and w.opt2=b.opt2 and go_is_deleted <> '1' and go_is_display = '1'
	where w.m_no = '$m_no' and a.open = '1'
	order by sno desc
	";

	$res = $db->query($db_table);
	while ($data=$db->fetch($res,1)){

		### 필수옵션
		$data[opt]	= array_notnull(array(
					$data[opt1],
					$data[opt2],
					));
		### 선택옵션
		$addopt = array_notnull(explode("|",$data[addopt]));
		if ($addopt){
			$data[r_addopt] = $addopt;
			unset($r_addopt); $addprice = 0;
			foreach ($addopt as $v){
				list ($tmp[sno],$tmp[optnm],$tmp[opt],$tmp[price]) = explode("^",$v);
				$r_addopt[] = $tmp;
				$addprice += $tmp[price];
			}
			$data[addopt] = $r_addopt;
			$data[addprice] = $addprice;
		}
		$loop[] = $data;
	}

	return $loop;
}

### 주문 취소
function chkCancel($ordno,$arr){
	global $db, $cfg;

	$todayshop_noti = Core::loader('todayshop_noti');
	$ts_orderdata = $todayshop_noti->getorderinfo($ordno);
	if (!$ts_orderdata) {
		unset($todayshop_noti, $ts_orderdata);
		$todayshop_noti = false;
	}

	if( $arr['no_cancel'] != "" && !is_null($arr['no_cancel']) ){
		$no_cancel = $arr['no_cancel'];
	}else{
		### 주문취소 정보 저장
		$query = "
		insert into ".GD_ORDER_CANCEL." set
			ordno	= '$ordno',
			name	= '$arr[name]',
			code	= '$arr[code]',
			memo	= '$arr[memo]',
			bankcode 	= '$arr[bankcode]',
			bankaccount = HEX(AES_ENCRYPT('".$arr['bankaccount']."', '".$ordno."')),
			bankuser  	= '$arr[bankuser]',
			regdt	= now()
		";
		$db->query($query);
		$no_cancel = $db->lastID();

		### 취소번호 재정의
		list($max_cancel) = $db->fetch("select max(cancel)+1 from gd_order_item where cancel>0");
		if ($max_cancel > $no_cancel) {
			$db->query("update ".GD_ORDER_CANCEL." set sno='$max_cancel' where sno='$no_cancel'");
			$no_cancel = $max_cancel;
		}
	}
	for ($i=0;$i<count($arr[sno]);$i++){
		$data = $db->fetch("select oi.*, tg.goodstype from ".GD_ORDER_ITEM." AS oi LEFT JOIN ".GD_TODAYSHOP_GOODS." AS tg ON oi.goodsno=tg.goodsno where oi.sno='{$arr[sno][$i]}'", 1);

		$istep = ($data[cyn]=="n" && $data[dyn]=="n") ? 44 : 41;

		$data[goodsnm] = addslashes($data[goodsnm]);

		### 취소 로그 저장
		$query = "
		insert into ".GD_LOG_CANCEL." set
			ordno	= '$data[ordno]',
			itemno	= '{$arr[sno][$i]}',
			cancel	= '$no_cancel',
			`prev`	= '$data[istep]',
			`next`	= '$istep',
			goodsnm	= '$data[goodsnm]',
			ea		= '{$arr[ea][$i]}'
		";
		$db->query($query);

		### 주문수량과 취소수량이 불일치할 경우 주문서 분리
		$gap = $data[ea] - $arr[ea][$i];
		if ($gap){
			unset($tmp);
			foreach ($data as $k=>$v)
			{
				if ($k == 'sno');
				else if ($k == 'ea') $tmp[] = "`$k`='{$arr[ea][$i]}'";
				else if ($k == 'istep') $tmp[] = "`$k`='$istep'";
				else if ($k == 'cancel') $tmp[] = "`$k`='$no_cancel'";
				else if($k == 'goodstype') continue;
				else $tmp[] = "`$k`='".addslashes($v)."'";
			}
			$tmp = implode(",",$tmp);
			$query = "insert into ".GD_ORDER_ITEM." set $tmp";
			$db->query($query);
			$new_cancel_sno = $db->lastID();
			$db->query("update ".GD_ORDER_ITEM." set ea=$gap where sno='{$arr[sno][$i]}'");
			$arr[sno][$i] = $new_cancel_sno;
		} else $db->query("update ".GD_ORDER_ITEM." set istep='$istep',cancel='$no_cancel' where sno='{$arr[sno][$i]}'");
	}

	$pre = $db->fetch("select * from ".GD_ORDER." where ordno='$ordno'");

	### 주문취소 여부 체크
	$cnt = 0;
	$query = "select * from ".GD_ORDER_ITEM." where ordno='$ordno'";
	$res = $db->query($query);
	while ($data=$db->fetch($res))	if ($data[istep]>40) $cnt++;

	if ($db->count_($res)==$cnt || $cnt == 0){
		list($step2) = $db->fetch("select min(istep) from ".GD_ORDER_ITEM." where ordno='$ordno'");
		if(!$step2)$step2 = 44;
		$db->query("update ".GD_ORDER." set step2=$step2 where ordno='$ordno'");
		### 적립금 환원
		if($pre[m_no] && $pre[emoney] && $pre[step] < 1 && ($cfg[autoCancelRecoverReserve] != 'n' || $arr['memo'] != '자동주문취소')) setEmoney($pre[m_no],$pre[emoney],'주문취소로 인해 사용 적립금 환원',$ordno);

		### 쿠폰 사용내역이 있을 경우 쿠폰 복원
		list($cnt) = $db->fetch("select count(*) from ".GD_COUPON_ORDER." where ordno='$ordno'");
		if($cnt > 0 && ($cfg[autoCancelRecoverCoupon] != 'n' || $arr['memo'] != '자동주문취소')) {
			$db->query("delete from ".GD_COUPON_ORDER." where ordno='$ordno'");
		}

		### 주문취소 SMS
		if ($step2 == '44'){
			$GLOBALS['dataSms'] = $pre;
			$today_failed = false;
			if ($todayshop_noti !== false && $ts_orderdata['processtype'] == 'b') { // 투데이샵 일괄발송 상품.
				if ($ts_orderdata['tgout'] == 'y' && ($ts_orderdata['limit_ea'] > ($ts_orderdata['buyercnt']+$ts_orderdata['fakestock']))) { // 진행기간이 종료되고 거래미성사시.
					$today_failed = true;
				}
			}

			// 알림
			if ($today_failed) { // 투데이샵 거래미성사일 경우.
				$todayshop_noti->set($ordno,'cancel');
				$todayshop_noti->send();
			}
			else {
				sendSmsCase('cancel',$pre['mobileOrder']);
			}
		}

		### 현금영수증(자동취소-데이터취합)
		if ($step2 == '44' && is_object($GLOBALS['cashreceipt'])){
			$GLOBALS['cashreceipt']->autoCancel($ordno);
		}
	}

	if($pre[step] > 3 && $pre[m_no]){

		### 취소상품 구매적립금 환원 (지급 내역에서 금액을 가져옴)
		$query = "select OI.reserve, OI.extra_reserve, OI.ea ,LE.memo from ".GD_ORDER_ITEM." AS OI INNER JOIN ".GD_LOG_EMONEY." AS LE ON OI.ordno = LE.ordno where OI.ordno='$ordno' and OI.sno in (".implode(',',$arr[sno]).") and OI.reserve_status='NORMAL'";
		$rs = $db->query($query);
		$reserve = 0;
		while ($row = $db->fetch($rs,1)) {
			if ($row['memo'] == '구매완료로 인해 구매적립금 적립') {
				$reserve += ($row['reserve'] + $row['extra_reserve']) * $row['ea'];
			}
		}

		if ($reserve > 0) {

			$msg = "구매취소로 인해 구매적립금 환원";

			// 적립금 환원 기록이 없으므로 환원하고 기록함.
			$dormantMember = false;
			$dormant = Core::loader('dormant');
			$dormantMember = $dormant->checkDormantMember(array('m_no'=>$pre[m_no]), 'm_no');

			if($dormantMember === true){
				$query = $dormant->getEmoneyUpdateQuery($pre[m_no], $reserve, '-');
			}
			else {
				$query = "update ".GD_MEMBER." set emoney = emoney - $reserve where m_no='$pre[m_no]'";
			}

			$db->query($query);
			$query = "
			insert into ".GD_LOG_EMONEY." set
				m_no	= '$pre[m_no]',
				ordno	= '$ordno',
				emoney	= '-".$reserve."',
				memo	= '$msg',
				regdt	= now()
			";
			$db->query($query);

			$query = 'UPDATE '.GD_ORDER_ITEM.' SET reserve_status="CANCEL" WHERE sno IN('.implode(',', $arr['sno']).')';
			$db->query($query);
		}

	}

	if($pre['pg']=='ipay')
	{
		if(class_exists('integrate_order_processor_ipay', false)===false) include dirname(__FILE__).'/integrate_order_processor.model.ipay.class.php';
		$auctionIpay = new integrate_order_processor_ipay();

		$CURRENT_DATETIME = Core::helper('Date')->format(G_CONST_NOW);

		$res = $db->query("
		SELECT `oi`.`sno`, `oi`.`ordno`, `oi`.`cancel`, `oi`.`ipay_itemno`, `oi`.`ipay_ordno`, `oi`.`cyn`, `oi`.`dyn`, `oc`.`code`
		FROM `gd_order_item` AS `oi`
		INNER JOIN `gd_order_cancel` AS `oc`
		ON `oi`.`cancel`=`oc`.`sno`
		WHERE `oi`.`ordno`=".$ordno." AND `oc`.`sno`=".$no_cancel
		);

		while($row = $db->fetch($res, 1))
		{
			switch($row['cyn'].$row['dyn'])
			{
				// 주문접수상태였던 주문건
				case 'nn':
					$auctionIpay->IpayDenySell($row['ipay_itemno'], $row['ipay_ordno'], $arr['code']);
					break;
				// 입금확인, 배송준비중 상태였던 주문건
				case 'yn':
					$auctionIpay->IpayDenySell($row['ipay_itemno'], $row['ipay_ordno'], $row['code']);
					$db->query("UPDATE `gd_order_item` SET `istep`=44, `cyn`='r' WHERE `sno`=".$row['sno']);
					$db->query("UPDATE `gd_order_cancel` SET `pgcancel`='r', `ccdt`='".$CURRENT_DATETIME."' WHERE `sno`=".$row['cancel']);
					$db->query("UPDATE `gd_order` SET `sync_`=0, `uptdt_`='".$CURRENT_DATETIME."' WHERE `ordno`=".$row['ordno']);

					$log = PHP_EOL.'----------------------------------------'.PHP_EOL
						 . '['.date('Y-m-d H:i:s').']IPay결제 취소완료'.PHP_EOL
						 . '----------------------------------------'.PHP_EOL;
					$orderSql = "
					UPDATE `gd_order` AS `o`
					LEFT JOIN (SELECT `soi`.`ordno`, COUNT(`soi`.`sno`) AS `count_all`, COUNT(IF(`soi`.`cyn`='r',1,NULL)) AS `count_paid` FROM `gd_order_item` AS `soi` GROUP BY `soi`.`ordno`) AS `oi`
					ON `o`.`ordno`=`oi`.`ordno`
					SET `o`.`step2`=44, `o`.`cyn`='r',
					`o`.`settlelog`=CONCAT(IFNULL(`settlelog`,''),'".$log."'),
					`o`.`pgcancel`='r'
					WHERE `o`.`ordno`=".$row['ordno']." AND `oi`.`count_all`=`oi`.`count_paid` AND `o`.`step2` IN(41,42)";
					$db->query($orderSql);
					break;
				// 배송중, 배송완료 상태였던 주문건
				case 'yy':
					$auctionIpay->DoIpayReturnRequestBySeller($row['ipay_ordno'], $arr['code'], $arr['memo']);
					break;
			}
		}
	}

	$naverNcash = Core::loader('naverNcash', true);
	if($naverNcash->useyn == 'Y' && ($pre['ncash_save_yn']=='y' || $pre['ncash_save_yn']=='b')){

		// 차감될 마일리지, 캐쉬금액
		$cancel_mileage = 0;
		$cancel_cash = 0;

		// 취소된 상품가격의 합계조회
		list($cancel_price) = $db->fetch("SELECT SUM((`price`-(`coupon`+`memberdc`))*`ea`) FROM `".GD_ORDER_ITEM."` WHERE `sno` IN(".implode(',', $arr['sno']).")");

		// 네이버 캐쉬가 사용됨(취소 우선순위에따라 캐쉬를 먼저 차감)
		if($pre['ncash_cash']>0 && $cancel_price>0)
		{
			// 취소될 금액이 사용캐쉬보다 큼
			if($pre['ncash_cash']<$cancel_price)
			{
				$cancel_price -= $pre['ncash_cash'];
				$cancel_cash = $pre['ncash_cash'];
			}
			// 취소될 금액이 사용캐쉬와 같거나 작음
			else
			{
				$cancel_cash = $cancel_price;
				$cancel_price = 0;
			}
		}

		// 네이버 마일리지가 사용됨
		if($pre['ncash_emoney']>0 && $cancel_price>0)
		{
			// 차감할 금액이 사용마일리지보다 큼
			if($pre['ncash_emoney']<$cancel_price) $cancel_mileage = $pre['ncash_emoney'];
			// 차감할 금액이 사용마일리지와 같거나 작음
			else $cancel_mileage = $cancel_price;
		}

		// 차감된 마일리지 및 캐쉬 기록
		if($cancel_mileage || $cancel_cash)
		{
			$db->query("UPDATE `".GD_ORDER_CANCEL."` SET `rncash_emoney`=".$cancel_mileage.", `rncash_cash`=".$cancel_cash." WHERE `sno`=".$no_cancel);
			$db->query("UPDATE `".GD_ORDER."` SET `ncash_emoney`=`ncash_emoney`-".$cancel_mileage.", `ncash_cash`=`ncash_cash`-".$cancel_cash." WHERE `ordno`=".$ordno);
		}

		if($pre[step] < '1' || ($pre['step']<3 && $pre['pg']=='ipay')){
			//네이버 포인트 결제 취소
			$naverNcash->deal_cancel($ordno, $no_cancel);
		}
	}
}
### 재주문
function reorder($ordno,$cancel){
			global $db;

	### 주문번호 생성
	$newOrdno = getordno();

			$query = "select * from ".GD_ORDER." where ordno='$ordno'";
			$order = $db->fetch($query);

			if(strlen(trim($order['ncash_tx_id']))>0) return false;

			$query = "select count(*) from ".GD_ORDER_ITEM." where ordno='$ordno'";
			list($tcnt)=$db->fetch($query);

			$query = "select * from ".GD_ORDER_ITEM." where cancel='$cancel' and ordno='$ordno'";
			$res = $db->query($query);

			### 주문상품 저장
			while($item = $db->fetch($res)){
				$i++;
				$item= array_map("addslashes",$item);
				$query = "
				insert into ".GD_ORDER_ITEM." set
					ordno			= '$newOrdno',
					goodsno			= '$item[goodsno]',
					goodsnm			= '$item[goodsnm]',
					opt1			= '$item[opt1]',
					opt2			= '$item[opt2]',
					addopt			= '$item[addopt]',
					price			= '$item[price]',
					supply			= '$item[supply]',
					reserve			= '$item[reserve]',
					coupon			= '$item[coupon]',
					memberdc		= '$item[memberdc]',
					ea				= '$item[ea]',
					maker			= '$item[maker]',
					brandnm			= '$item[brandnm]',
					istep			= '0',
					cyn				= 'y',
					dyn				= 'n',
					about_coupon_flag = '$item[about_coupon_flag]',
					about_dc_price = '$item[about_dc_price]'
				";

				$reserve += $item[reserve] * $item[ea];
				$goodsprice += $item[price] * $item[ea];
				$memberdc += $item[memberdc] * $item[ea];
				$coupon += ($item[coupon] + $item[about_dc_price]) * $item[ea];

				$db->query($query);
			}

			$delivery = 0;
			if($i == $tcnt)$delivery = $order[delivery];

			$settleprice = $goodsprice + $delivery - $memberdc - $coupon;

			$query = "
			insert into ".GD_ORDER." set
				step			= '0',
				step2			= '0',
				ordno			= '$newOrdno',
				nameOrder		= '$order[nameOrder]',
				email			= '$order[email]',
				phoneOrder		= '$order[phoneOrder]',
				mobileOrder		= '$order[mobileOrder]',
				nameReceiver	= '$order[nameReceiver]',
				phoneReceiver	= '$order[phoneReceiver]',
				mobileReceiver	= '$order[mobileReceiver]',
				zipcode			= '$order[zipcode]',
				address			= '$order[address]',
				settlekind		= 'a',
				settleprice		= '$settleprice',
				prn_settleprice		= '$settleprice',
				goodsprice		= '$goodsprice',
				coupon			= '$coupon',
				delivery		= '$delivery',
				memberdc		= '$memberdc',
				reserve			= '$reserve',
				bankAccount		= '$order[bankAccount]',
				bankSender		= '$order[bankSender]',
				m_no			= '$order[m_no]',
				ip				= '$order[ip]',
				referer			= '$order[referer]',
				memo			= '$order[memo]',
				adminmemo		= '주문번호({$ordno})의 교환으로 생성된 재주문 입니다.',
				inflow			= '',
				orddt			= now(),
				cdt				= now(),
				cyn				= 'y',
				oldordno =	'$ordno',
				about_coupon_flag = '$order[about_coupon_flag]',
				about_dc_sum = '$order[about_dc_sum]'
			";

			$db->query($query);

			return $newOrdno;

}
### 환불수수료
function getRepayFee($repay){
	global $cfg;
	if($cfg[repayfee]) $repayfee = $cfg[repayfee] * $repay / 100;
	if( $cfg[minrepayfee] && $repayfee < $cfg[minrepayfee] ) $repayfee = $cfg[minrepayfee];
	if(!$cfg[minpos])$cfg[minpos] = 1;
	if($repayfee){
		$repayfee = floor($repayfee / pow(10,($cfg[minpos]-1)))*pow(10,($cfg[minpos]-1));
	}
	if(!$repayfee)$repayfee=0;
	return $repayfee;
}

### 스텝별 주문 단계 가져오기
function getStepMsg($step,$step2,$ordno='',$itemsno=''){
	global $r_stepi,$db;
	$stepMsg = $r_stepi[$step][$step2];

	if($step == 0  && $step2 > 40 && $step2 < 50){
		 $stepMsg = '주문접수⇒'.$stepMsg;
	}
	if($step >= 1 && $step <= 2 && $step2 > 40){
		 $stepMsg = '입금확인⇒'.$stepMsg;
	}
	if($step > 2){
		if($step2 == '42') $stepMsg = '반품완료⇒환불접수';
		if($step2 == '44'){
			$stepMsg = '반품완료⇒환불완료';
			if($ordno){
				$query = "select dyn from ".GD_ORDER_ITEM." where ordno='$ordno'";
				if($itemsno)$query .= " AND sno='$itemsno'";
				$res = $db->query($query);
				$dyn = "e";
				while($data = $db->fetch($res)) if($data[dyn] != "e") $dyn = $data[dyn];
				if($dyn == 'e') $stepMsg = '반품완료⇒교환완료';
			}

		}
	}
	return $stepMsg;
}



## 배송정보 출력
function displayDelivery($deliveryno,$deliverycode){
	global $db;
	$port = 80;
	$tail = trim($deliverycode);
	switch ($deliveryno){
		case "16" : ### 신세계
			$port = 8080;
			break;
		case "21" : ### 동부익스프레스
			$tail .= '&search_type=1&mode=SEARCH';
			break;
		case "27" : ### 네덱스
			$port = 8080;
			break;
		case "29" : ### 대한통운(미국상사)
			$port = 7004;
			break;
		case "33" : ### 대신택배
			$tmp[0] = substr($deliverycode, 0, 4);
			$tmp[1] = substr($deliverycode, 4, 3);
			$tmp[2] = substr($deliverycode, 7);
			$tail = '?billno1='.$tmp[0].'&billno2='.$tmp[1].'&billno3='.$tmp[2];
			break;
		case "40" : ### 현대국제택배
			$tail = '';//post로 송장번호를 넘겨야 해서 tail 빈값처리
			break;
		case "8" : ### 앨로우캡택배
			$port = 443;
			$tail = array();
			$tail['delivery'] = $deliverycode;
			break;
		default :
			break;
	}
	list($deliverycomp,$deliveryurl) = $db -> fetch("select deliverycomp,deliveryurl from gd_list_delivery where deliveryno='$deliveryno'");
	if($deliveryurl){
		if($deliveryno == "8"){
			$url = $deliveryurl;
			$out = readpostssl($url,$tail,$port);
		} else {
			$url = $deliveryurl.$tail;
			$out = readurl($url,$port);
		}
	}

	echo "<div class=\"title title_top\">배송추적</div>
	<b>$deliverycomp : $deliverycode</b><br>";
	include dirname(__FILE__) . "/../proc/delivery/$deliveryno.php";
}

### 에디터 이미지 삭제
function delEditorImg($contents){
	preg_match_all("/<IMG[^>]*(src *= *['|\"]*([^'|\"| |>]*)['|\"]*)[^>]*>/i", $contents, $matches);
	foreach ($matches[2] as $file){
		$url = parse_url($file);
		if ($url[query]) continue;
		if ($url[host] && $url[host] != $_SERVER['HTTP_HOST']) continue;
		if (false === strpos($url[path], 'data/editor/')) continue;
		if ('' == ($realpath = realpath($_SERVER['DOCUMENT_ROOT'] . '/' . $url[path]))) continue;
		//debug( '====> ' . $realpath);
		@unlink($realpath);
	}
}

function setGoodsCoupon($ordno){
	global $db;

	$query = "select m_no from ".GD_ORDER." where ordno='$ordno'";
	list($m_no) = $db->fetch($query);
	if($m_no){

		$query = "select goodsno from ".GD_ORDER_ITEM." where ordno='$ordno'";
		$res = $db->query($query);
		while($tmp = $db->fetch($res)) $arr_goodsno[] = $tmp[goodsno];

		$query = "select category,char_length(category) clen from ".GD_GOODS_LINK." where hidden = 0 and goodsno in (".implode(',',$arr_goodsno).")";
		$res = $db->query($query);
		while($tmp = $db->fetch($res)) for($i=3;$i<=$tmp[clen];$i+=3) $arrCategory[] = "'".substr($tmp[category],0,$i)."'";
		if(count($arrCategory) > 0)$arrCategory = array_unique($arrCategory);
		else $arrCategory = array();

		$query	=	"SELECT a.*
					FROM
						".GD_COUPON." a
						LEFT JOIN ".GD_COUPON_CATEGORY." b ON a.couponcd = b.couponcd
						LEFT JOIN ".GD_COUPON_GOODSNO." c ON a.couponcd = c.couponcd
					WHERE a.coupontype = 3
						AND ((a.sdate <= '".date("Y-m-d H:i:s")."' AND a.edate >= '".date("Y-m-d H:i:s")."' AND a.priodtype='0') OR a.priodtype='1')
						AND (((b.category in(".implode(',',$arrCategory).") OR c.goodsno in (".implode(',',$arr_goodsno).")) AND a.goodstype='1') OR a.goodstype='0')";

		$res = $db->query($query);
		$i=0;

		while($data = $db->fetch($res)){
			$query = "select a.sno from ".GD_COUPON_APPLY." a left join ".GD_COUPON_APPLYMEMBER." b on a.sno=b.applysno where a.couponcd='$data[couponcd]' and b.m_no = '$m_no' order by a.regdt desc limit 1";
			list($applysno) = $db->fetch($query);
			$query = "select count(*) from ".GD_COUPON_ORDER." where applysno='$applysno' and m_no = '$m_no'";
			list($cnt) = $db->fetch($query);

			if(!$applysno){
				$newapplysno = new_uniq_id('sno',GD_COUPON_APPLY);
				$query = "INSERT INTO ".GD_COUPON_APPLY." SET
							sno				= '$newapplysno',
							couponcd		= '$data[couponcd]',
							membertype		= '2',
							member_grp_sno  = '',
							regdt			= now()";
				$db->query($query);
				$query = "insert into ".GD_COUPON_APPLYMEMBER." set m_no='$m_no', applysno ='$newapplysno'";
				$db->query($query);
			}else if($cnt == 0){
				$query = "update ".GD_COUPON_APPLY." set regdt=now() where sno='$applysno'";
				$db->query($query);
			}
		}
	}
}

### 쿠폰정보
function getCouponInfo($goodsno,$goodsprice='',$mode='',$daumCpc=''){
	$Goods = Core::loader('Goods');
	$arCategory = $Goods->get_goods_category($goodsno);

	if(!$goodsprice){
		$goodsprice = $Goods->get_goods_price($goodsno);
	}

	$coupon_price = Core::loader('coupon_price');
	$cfgCoupon = & $GLOBALS['cfgCoupon'];
	if(!$cfgCoupon) @include SHOPROOT."/conf/coupon.php";

	if($mode && $mode!='list'){
		$mode = "view";
	} else {
		$mode = "list";
	}

	$coupon_price->reset_item(); // 쿠폰 적용 상품 아이템 정보 리셋
	$coupon_price->set_config($cfgCoupon); // 쿠폰 설정
	$coupon_price->set_item($goodsno,$goodsprice,1,$arCategory); //적용상품
	$coupon_price->get_goods_coupon($mode);

	if($mode&& $mode!='list')return $coupon_price->arCoupon;
	if($coupon_price->arCoupon)foreach($coupon_price->arCoupon as $arr){

		if($mode=="list"){
			$sale = (int) $arr['sale'][$goodsno];
			$reserve = (int) $arr['reserve'][$goodsno];
		}else{
			$sale = (int) $arr['sale'][$goodsno] + (int) $arr['sale']['order'];
			$reserve = (int) $arr['reserve'][$goodsno] + (int) $arr['reserve']['order'];
		}

		// 사용 제한 금액이 적용 할인/적립
		$_sale = ($arr['excPrice'] > 0 && $goodsprice < $arr['excPrice']) ? 0 : $sale;
		$_reserve = ($arr['excPrice'] > 0 && $goodsprice < $arr['excPrice']) ? 0 : $reserve;

		if($cfgCoupon['double'] == 1){
			$result[0] += $sale;
			$result[1] += $reserve;

			$result[10] += $_sale;
			$result[11] += $_reserve;

		}else{
			if($result[0] < $sale) $result[0] = $sale;
			if($result[1] < $reserve) $result[1] = $reserve;

			if($result[10] < $_sale) $result[00] = $_sale;
			if($result[11] < $_reserve) $result[11] = $_reserve;
		}

		if ($daumCpc === 'Y' && $arr['sale']) {
			$result[2][] = $arr['price'];
		}
	}
	return $result;
}

function new_uniq_id($field,$table){
	global $db;
	$query = "select max($field) from $table";
	list($newid) = $db->fetch($query);
	return $newid + 1;
}

### Anti-Spam 검증
function antiSpam($switch, $filenm='', $method='')
{
	global $cfg;

	$ori_url_diff = (!eregi(preg_replace('/:[0-9]*$/','',getenv("HTTP_HOST")), getenv("HTTP_REFERER"))) && (!eregi(preg_replace('/:[0-9]*$/','',getenv("HTTP_HOST")), $cfg['ssl_freedomain']));

	if ($switch[0] == '1' && $ori_url_diff) return array('code'=>'1001', 'msg'=>'Fail to verify HTTP_HOST');
	if ($switch[1] == '2' && $filenm != '' && !eregi($filenm, getenv("HTTP_REFERER"))) return array('code'=>'2001', 'msg'=>'Fail to verify File');
	if ($switch[2] == '3' && $method != '' && strcasecmp(strtoupper($method), getenv("REQUEST_METHOD"))) return array('code'=>'3001', 'msg'=>'Fail to verify Method');
	if ($switch[3] == '4')
	{
		@include dirname(__FILE__) . "/captcha.class.php";
		$captcha = new Captcha();
		$rst = $captcha->verify();
		if ($rst[code] <> '0000') return $rst;
	}
	return array('code'=>'0000', 'msg'=>'Succeed in verifing');
}

### URL Query 재구성
function getReUrlQuery($except, $url)
{
	$info = parse_url($url);
	parse_str($info[query], $output);
	return getVars($except, $output);
}

### POST 페이지 이동
function goPost($url, $element, $target='')
{
	if ($target) $target .= ".";
	echo "
	<script>
	ce = {$target}document.createElement('form');
	ce.setAttribute('method', 'post');
	ce.setAttribute('action', '{$url}');
	var fm = {$target}document.getElementById('jsmotion').appendChild(ce);
	";
	if (is_array($element)){
		foreach($element as $k => $v){
			echo "
			ce = {$target}document.createElement('input');
			ce.setAttribute('type', 'hidden');
			ce.setAttribute('name', '{$k}');
			ce.setAttribute('value', '{$v}');
			fm.appendChild(ce);
			";
		}
	}
	echo "
	fm.submit();
	history.back();
	</script>
	";
	exit;
}

### 레퍼러 정의
function getReferer(){
	if(!$_SERVER['HTTP_REFERER']) $referer = '/';
	else $referer = $_SERVER['HTTP_REFERER'];
	return $referer;
}

### 상품분류이미지
function getCategoryImg($category=''){
	$dir = dirname(__FILE__) . "/../data/category";
	$tail = array('_basic','_over');
	if ($handle = opendir($dir)) {
		while ($file = readdir($handle)) {
			for($i=0;$i<2;$i++){
				if ( $category ) {
					if(preg_match('/^'.$category.$tail[$i].'/',$file)) $imgName[$category][$i] = $file;
				}else{
					$tmp = explode('.',$file);
					$category = str_replace($tail[$i],'',$tmp[0]);
					if(is_numeric($category)){
						$imgName[$category][$i] = $file;
					}
				}
			}
		}
	}
	return $imgName;
}

### datetime -> timestamp
function toTimeStamp($datetime){
	$y = substr($datetime,0,4);	$m = substr($datetime,5,2);	$d = substr($datetime,8,2);
	$h = substr($datetime,11,2);	$i = substr($datetime,14,2);	$s = substr($datetime,17,2);
	return mktime($h, $i, $s, $m, $d, $y);
}

## htmspecialchars함수와 비슷한데 한글은 통과하는 함수
function htmlchars_ech($str) {
	$str = preg_replace_callback("/&[^&]{0,7}/","amphtml",$str);
	$patterns=array("\"","'","<",">");
	$replacements=array("&quot;","&#039;","&lt;","&gt;");
	return str_replace($patterns,$replacements,$str);
}
function amphtml($matches)
{
	if(preg_match("/&#[0-9]{4,5};/",$matches[0]))
		return $matches[0];
	else
		return str_replace("&","&amp;",$matches[0]);
}

function naver_goods_diff_check() {
	global $db;
	$tmp = date("Y-m-d 00:00:00");
	$db->query("delete from ".GD_GOODS_UPDATE_NAVER." where utime < '$tmp'");
}

function naver_goods_runout($goodsno) {
	global $db;
	$str_date = date("Y-m-d H:i:s");
	$db->query("insert into ".GD_GOODS_UPDATE_NAVER." set mapid='$goodsno',class='D',utime='$str_date'");
}

function naver_goods_runout_recovery($goodsno) {
	global $db;
	$str_date = date("Y-m-d H:i:s");
	$db->query("insert into ".GD_GOODS_UPDATE_NAVER." set mapid='$goodsno',class='U',utime='$str_date'");
}

### 회원할인 제외상품 체크
function chk_memberdc_exc($member,$goodsno) {
	global $db;
	if($member['excep']){
		$arr_excep = @explode(',',$member['excep']);
		if(in_array($goodsno,$arr_excep)) return  true;
	}
	if($member['excate']){
		$arr = explode(',',$member['excate']);
		foreach($arr as $cate){
			list($excnt) = $db->fetch( "select count(*) from ".GD_GOODS." a left join ".GD_GOODS_LINK." b on a.goodsno=b.goodsno where a.goodsno='$goodsno' and ".getCategoryLinkQuery('b.category', $cate, 'where'));
			$tcnt += $excnt;
		}
		if($tcnt > 0) return true;
	}
	return false;
}

function url($url) {
	$sitelink = Core::loader('sitelink');
	$result = $sitelink->link($url);
	if(!strpos($result,'?')) {
		$result=$result.'?';
	}
	return $result;
}

function auctionIpayLogo() {
	@include "../conf/auctionIpay.cfg.php";
	@include "../conf/auctionIpay.pg.cfg.php";

	// iPay 안전결제나 전용결제 둘중 하나를 사용하고 로고타입이 지정된 경우에 리턴
	if(((isset($auctionIpayCfg['useYn']) && $auctionIpayCfg['useYn']=='y') || (isset($auctionIpayPgCfg['useYn']) && $auctionIpayPgCfg['useYn']=='y')) && $auctionIpayCfg['logoType']) {
		return '<img src="'.$auctionIpayCfg['logoType'].'" />';
	}
}

function restore_coupon($ordno){
	global $db, $sess, $member;
	//쿠폰 복원

	//--- 로그 생성
	$settlelog	= '';
	$settlelog	.= '===================================================='.PHP_EOL;
	$settlelog	.= (($sess[level] >= 80) ? '관리자' : '사용자').' 쿠폰 복원'.PHP_EOL;
	$settlelog	.= '처리일시 : '.date('Y-m-d H:i:s').PHP_EOL;
	$settlelog	.= '처리자 : '.$member[name].'('.$sess[m_id].')'.PHP_EOL;

	$query = "select applysno, coupon, downloadsno from ".GD_COUPON_ORDER." where ordno = ".$ordno;
	$res = $db->query($query);
	$cnt_data = $db->count_($res);

	if ($cnt_data > 0 ) {

		while ($data=$db->fetch($res)) {
			$applysno = $data['applysno'];
			$coupon = $data['coupon'];
			$downloadsno = $data['downloadsno'];

			if($applysno || $downloadsno){

				if($applysno) {

					$db->query("update ".GD_COUPON_APPLY." set status = '0' where sno='$applysno'");
				}

				$settlelog	.= '복원쿠폰 : '.strip_tags($coupon).PHP_EOL;

				// 복원된 쿠폰은 gd_coupon_order 테이블에서 삭제한다.
				$db->query("delete from ".GD_COUPON_ORDER." where ordno='$ordno'");
			}
		}

		// 결제시도나 결제실패 주문상태의 주문건 중 쿠폰복원 된 주문건의 쿠폰복원상태(recovery_coupon)를 'y'로 변경
		$query = "UPDATE `".GD_ORDER."` SET `recovery_coupon`='y', settlelog=concat(ifnull(settlelog,''),'$settlelog')  WHERE `ordno`='".$ordno."'";
		$db->query($query);

		msg("미사용 쿠폰으로 변경하였습니다.");
	}
}

function restore_emoney($ordno){
	global $db;
	//적립금 복원
	list($emoney, $m_no) = $db->fetch("select emoney, m_no  from ".GD_LOG_EMONEY." where ordno='$ordno' AND memo = '쿠폰 적립금 적립'");

	// 환원 기록
	list($history) = $db->fetch("select count(sno) from ".GD_LOG_EMONEY." where ordno='$ordno' AND memo = '쿠폰 적립금 환원'");
	if($emoney && !$history) {
		$dormantMember = false;
		$dormant = Core::loader('dormant');
		$dormantMember = $dormant->checkDormantMember(array('m_no'=>$m_no), 'm_no');

		if($dormantMember === true){
			$dormantEmoneyQuery = $dormant->getEmoneyUpdateQuery($m_no, $emoney, '-');
			$db->query($dormantEmoneyQuery);
		}
		else {
			$db->query("update ".GD_MEMBER." set emoney=emoney - $emoney where m_no='$m_no'");
		}

		$query = "
		insert into ".GD_LOG_EMONEY." set
			m_no	= '$m_no',
			ordno	= '$ordno',
			emoney	= '-".$emoney."',
			memo	= '쿠폰 적립금 환원',
			regdt	= now()
		";
		$db->query($query);
	}
}

// TS 함수는 todayshop
### 분류 HIDDEN 갯수
function getCateHideCntTS($category,$mobile=0)
{
	global $db;

	$hiddenFieldName = $mobile ? 'hidden_mobile':'hidden';

	$cates = array();
	$repeat = strlen($category) / 3 ;
	for ($i = 1; $i <= $repeat; $i++) $cates[] = substr($category, 0, $i * 3);
	list($hCnt) = $db->fetch("select count(*) from ".GD_TODAYSHOP_CATEGORY." where category != '' and category in ('" . implode("','", $cates) . "') and {$hiddenFieldName}=1");

	return $hCnt;
}

### 상품분류 HIDDEN 처리
function setGoodslinkHideTS($category, $hidden,$mobile=0)
{
	global $db;

	$hiddenFieldName = $mobile ? 'hidden_mobile':'hidden';

	if ($hidden == 1 || getCateHideCntTS($category) > 0) $db->query("update ".GD_TODAYSHOP_LINK." set {$hiddenFieldName}='1' where category like '$category%'");
	else {
		$res = $db->query("select category, {$hiddenFieldName} from ".GD_TODAYSHOP_CATEGORY." where category like '$category%' order by category");
		while ($data=$db->fetch($res)){
			if ( $cateHidden[ substr($data[category],0,-3) ] == 1 ) $data[$hiddenFieldName] = 1;
			$cateHidden[$data[category]] = $data[$hiddenFieldName];
			$db->query("update ".GD_TODAYSHOP_LINK." set hidden='".$data[$hiddenFieldName]."' where category='$data[category]'");
		}
	}
}

### 상품분류이미지
function getCategoryImgTS($category=''){
	$dir = dirname(__FILE__) . "/../data/category";
	$tail = array('_basic','_over');
	if ($handle = opendir($dir)) {
		while ($file = readdir($handle)) {
			for($i=0;$i<2;$i++){
				if ( $category ) {
					if(preg_match('/^TS'.$category.$tail[$i].'/',$file)) $imgName[$category][$i] = $file;
				}else{
					$tmp = explode('.',$file);
					$category = str_replace(array('TS',$tail[$i]),'',$tmp[0]);
					if(is_numeric($category)){
						$imgName[$category][$i] = $file;
					}
				}
			}
		}
	}
	return $imgName;
}

// 카테고리 위치.
function currPositionTS($category) {
	global $db;
	$query = "
	select * from
		".GD_TODAYSHOP_CATEGORY."
	where
		category in (left('".$category."',3),left('".$category."',6),left('".$category."',9),'".$category."')
	order by category
	";
	$res = $db->query($query);
	while ($tmpData=$db->fetch($res)) $pos[] = "<a href='../goods/goods_list.php?category=$tmpData[category]'>$tmpData[catnm]</a>";
	$ret = @implode(" > ",$pos);
	if ($mode) $ret = strip_tags($ret);
	$curPos = $ret;
	unset($res, $tmpData);
	return $curPos;
}

### 투데이샵 사용자용 상품이미지
/*
$hidden		0	일반 사용자 페이지
			1	관리자 페이지
			2	관리자 페이지 (onerror시 hidden)
			3	절대웹경로
*/
function goodsimgTS($src,$size='',$tmp='',$hidden='', $viewerid='')
{
	if(!preg_match('/http:\/\//',$src)){
		$path = $GLOBALS[cfg][rootDir]."/data/goods/"; // 경로를 절대경로로 설정.
		if ($hidden==3) $path = "http://".$GLOBALS[cfg][shopUrl].$GLOBALS[cfg][rootDir]."/data/goods/";
	}
	if ($size){
		$size = explode(",",$size);
		$vsize = " width=$size[0]";
		if ($size[1]) $vsize .= " height=$size[1]";
	}
	if ($tmp) $tmp = " ".$tmp;

	if ($size[0]>300) $nosize = 500;
	else if ($size[0]>130) $nosize = 300;
	else if ($size[0]>100) $nosize = 130;
	else $nosize = 100;

	$onerror = ($hidden<2) ? "onerror=this.src='".$GLOBALS[cfg][rootDir]."/data/skin/".$GLOBALS[cfg][tplSkin]."/img/common/noimg_$nosize.gif'" : "onerror=this.style.display='none'";

	$rtn = "<img src='$path{$src}'{$vsize}{$tmp} $onerror ";
	if ($viewerid) $rtn .= ' viewerid="'.$viewerid.'" ';
	$rtn .= '/>';
	return $rtn;
}

function gd_json_decode($json='') {

	if (!class_exists('Services_JSON', false))
		include_once dirname(__FILE__).'/json.class.php';

	$o = new Services_JSON( SERVICES_JSON_LOOSE_TYPE );

	return $o->decode($json);

}

function gd_json_encode($array=false) {

	if (!class_exists('Services_JSON', false))
		include_once dirname(__FILE__).'/json.class.php';

	$o = new Services_JSON( SERVICES_JSON_LOOSE_TYPE );

	return $o->encode($array);

}

/**
	2011-02-07 by x-ta-c
	투데이샵 PG 설정값을 불러와 global 변수에 덮어 씌움
 */
function resetPaymentGateway($forced = false) {

	//*/
	if (! preg_match('/\/todayshop(\/|$)/',dirname($_SERVER['PHP_SELF'])) && ! $forced) return;
	/*/
	if ($tsCfg['shopMode'] != 'todayshop') return;
	/**/

	$todayShop = Core::loader('todayshop');
	$tsPG = $todayShop->getPginfo();

	if (!empty($tsPG)) {
		foreach($tsPG as $k => $v) {
			if ($k == 'mode') continue;
			global $$k;
			$$k = array_merge((array)$$k,(array)$v);
			//$$k = $v;
		}
	}

	return $tsPG;
}

function setGoodsOuputVar($data=array()) {

	global $sess, $cfg_soldout;

	$_goodsinfo = array();

	// 상품 URL
	$_goodsinfo['goods_view_url'] = '../goods/goods_view.php?goodsno='.$data['goodsno'].'&category='.($_GET['category'] ? $_GET['category'] : $data['category']);

	// 성인 전용 상품일때 이미지 교체
	if ($data['use_only_adult'] && ! Clib_Application::session()->canAccessAdult()) {
		$_goodsinfo['img_i'] = 'http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['cfg']['rootDir'] . "/data/skin/" . $GLOBALS['cfg']['tplSkin'] . '/img/common/19.gif';
		$_goodsinfo['img_s'] = 'http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['cfg']['rootDir'] . "/data/skin/" . $GLOBALS['cfg']['tplSkin'] . '/img/common/19.gif';
		$_goodsinfo['img_m'] = 'http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['cfg']['rootDir'] . "/data/skin/" . $GLOBALS['cfg']['tplSkin'] . '/img/common/19.gif';
		$_goodsinfo['img_l'] = 'http://' . $_SERVER['HTTP_HOST'] . $GLOBALS['cfg']['rootDir'] . "/data/skin/" . $GLOBALS['cfg']['tplSkin'] . '/img/common/19.gif';
	}

	if($sess['level'] < $data['level'] && $data['level_auth'] == 3)
		$_goodsinfo['goods_view_url'] = 'javascript: msg_back();';

	// 가격, 상품명, 기타정보(쿠폰, 아이콘, 적립금 등) 출력 여부
	$_goodsinfo['is_open_price'] = (($data['level'] == 0 || $sess['level'] >= $data['level']) || $data['auth_step'][2] == 'Y') ? true : false;
	$_goodsinfo['is_open_extra'] = ($data['level'] == 0 || $sess['level'] >= $data['level']) ? true : false;
	$_goodsinfo['is_open_name'] = (($data['level'] == 0 || $sess['level'] >= $data['level']) || $data['auth_step'][1] == 'Y') ? true : false;

	// 각 출력 여부에 따른 처리
	if ($_goodsinfo['is_open_extra'] === false) {
		$_goodsinfo['icon'] = '';
		$_goodsinfo['coupon'] = '';
	}
	else {
		if ($data['runout'] && $cfg_soldout['icon'] == '0') $_goodsinfo['icon'] = '';
		if ($data['runout'] && $cfg_soldout['coupon'] == '0') $_goodsinfo['coupon'] = '';
	}

	// 품절상품 제어
	if ($data['runout']) {

		if ($cfg_soldout['display'] == 'icon')
			$_goodsinfo['soldout_icon'] = ($cfg_soldout['display_icon'] == 'custom') ? 'custom' : 'skin';
		elseif ($cfg_soldout['display'] == 'overlay') {
			$_goodsinfo['soldout_overlay'] = ($cfg_soldout['display_overlay'] == 'custom') ? '../data/goods/icon/custom/soldout_overlay' : '../data/goods/icon/icon_soldout'.$cfg_soldout['display_overlay'];
			$_goodsinfo['css_selector'] = 'el-goods-soldout-image';
		}
		else
			$_goodsinfo['soldout_icon'] = 'skin';

		if($cfg_soldout['display'] == 'none') {
			$_goodsinfo['soldout_icon'] = $_goodsinfo['css_selector'] = "";
		}

		if ($cfg_soldout['goodsnm'] == '0')
			$_goodsinfo['goodsnm'] = '';

		$_goodsinfo['price'] = '';

		if ($cfg_soldout['price'] == '0') {

		}
		elseif ($cfg_soldout['price'] == 'string') {	// 대체문구
			$_goodsinfo['soldout_price_string'] = $cfg_soldout['price_string'];
		}
		elseif ($cfg_soldout['price'] == 'image') {	// 대체문구
			$_goodsinfo['soldout_price_image'] = '<img src="../data/goods/icon/custom/soldout_price">';
		}
		else {
			$_goodsinfo['price'] = $data['price'];
		}

	}

	if ($_goodsinfo['is_open_name'] === false || ($data['runout'] && $cfg_soldout['goodsnm'] == '0')) {
		$_goodsinfo['goodsnm'] = '';
	}

	if ($_goodsinfo['is_open_price'] === false) {
		$_goodsinfo['price'] = '';
	}

	$data = array_merge($data, $_goodsinfo);

	return $data;
}

/**
 * 구분자로 된 이루어진 문자열의 item 추가/제거 함수
 * $outlink = 회원 레코드의 outlink 필드의 값
 * $addlink = 추가할 목록 ex) "|naver|daum|" or "naver|daum"
 * $dellink = 삭제할 목록 ex) "|naver|daum|" or "naver|daum"
 * 참고 : 만약 추가와 삭제를 동시에 할 경우 삭제부분이 먼저 진행됨
 */
function modOutlink($outlink="", $addlink="", $dellink="", $delimiter="|") {
	// 제거
	if($dellink) {
		$ar_dellink = explode($delimiter, $dellink);

		for($i = 0, $imax = count($ar_dellink); $i < $imax; $i++) {
			if($ar_dellink[$i]) $outlink = str_replace($ar_dellink[$i], "", $outlink);
		}
	}

	// 추가
	if($addlink) $outlink = $outlink.$delimiter.$addlink;

	// 가공
	if($outlink) {
		$ar_outlink = explode($delimiter, $outlink);
		$ar_outlink = array_unique($ar_outlink);
		$outlink = "";

		foreach($ar_outlink as $k => $v) {
			if($outlink) $outlink .= $delimiter;
			if($v) $outlink .= $v;
		}

		if($outlink) $outlink = $delimiter.$outlink.$delimiter;
	}

	return $outlink;
}

/**
 * 네이버체크아웃 회원 철회
 *
 * @param string $m_no 가맹점 회원 고유 번호
 */
function naverCheckoutHack($m_no) {
	global $db, $naverCheckoutAPI;
	list($MallUserID, $MallUserNo, $inflow, $outlink) = $db->fetch("select m_id, m_no, inflow, outlink from ".GD_MEMBER." where m_no='" . $m_no . "'");

	if (is_a($naverCheckoutAPI, 'naverCheckoutAPI') === false) {
		$naverCheckoutAPI = Core::loader('naverCheckoutAPI');
	}

	// 가맹점 회원 정보 제공 동의 철회(이용자가 동의를 철회하거나 탈퇴시)
	if (preg_match("/\|naverCheckout\|/", $outlink) == 1) {
		$res = $naverCheckoutAPI->CancelMallUserAgreement($MallUserID, $MallUserNo);
		if ($res === true || ($res === false && $naverCheckoutAPI->errorCode == 'ERR-NC-102001')) {
			$outlink = modOutlink($outlink, "", "naverCheckout");
			$db->query("UPDATE ".GD_MEMBER." SET outlink = '$outlink' WHERE m_no = '".$m_no."'");
			$db->query("DELETE FROM ".GD_NAVERCHECKOUT_AGREEMENT." WHERE m_no = '".$m_no."'");
			$res = true;
		}
		return array('result' => ($res ? true : false), 'error' => $naverCheckoutAPI->error);
	}
	// 가맹점 회원 탈퇴 완료 알림(탈퇴시)
	else if ($inflow == 'naverCheckout') {
		$res = $naverCheckoutAPI->LeaveMallUser($MallUserID, $MallUserNo);
		return array('result' => ($res ? true : false), 'error' => $naverCheckoutAPI->error);
	}
	return true;
}

/**
 * 해당 주문의 저장 여부 및 위변조 체크
 *
 * @param string $ordno 주문 번호
 * @param string $settleprice 결제금액
 * @return boolean 결과 출력
 */
function forge_order_check($ordno, $settleprice = 0)
{
	global $db, $_POST;

	// 주문 체크
	list($chk_order, $chk_price, $chk_recoupon)	= $db->fetch("SELECT ordno,settleprice,recovery_coupon FROM ".GD_ORDER." WHERE ordno='".$ordno."'");

	// 주문취소 여부 체크
	list($chk_item)		= $db->fetch("SELECT COUNT(sno) FROM ".GD_ORDER_ITEM." WHERE ordno='".$ordno."'");

	if ((empty($chk_order) === false && $chk_order == $ordno) && $chk_item > 0) {
		// 결제 금액 비교
		if ($settleprice > 0 && $settleprice != $chk_price) {
			$chk_result	= "price";
		} else {
			$chk_result	= "ok";
		}
	} else {
		$chk_result	= "error";
	}

	//쿠폰복원여부 150129 추가
	if($chk_recoupon == 'y') {
		$chk_result	= "error";
	}

	if ($chk_result == 'ok') {
		return true;
	} else {
		$logMsg		= array();
		foreach ($_POST as $key => $val) {
			$logMsg[]	= $key.' : '.$val;
		}
		$logInfo	 = 'INFO	['.date('Y-m-d H:i:s').']	START PG Order log'.chr(10);
		$logInfo	.= 'DEBUG	['.date('Y-m-d H:i:s').']	Connect IP	: '.$_SERVER['REMOTE_ADDR'].chr(10);
		$logInfo	.= 'DEBUG	['.date('Y-m-d H:i:s').']	Request URL	: '.$_SERVER['REQUEST_URI'].chr(10);
		$logInfo	.= 'DEBUG	['.date('Y-m-d H:i:s').']	User Agent	: '.$_SERVER['HTTP_USER_AGENT'].chr(10);
		$logInfo	.= 'Error Type : '.$chk_result.chr(10);
		$logInfo	.= implode(chr(10),$logMsg);
		$logInfo	.= 'INFO	['.date('Y-m-d H:i:s').']	END PG Order log'.chr(10);
		$logInfo	.= '------------------------------------------------------------------------------'.chr(10).chr(10);

		error_log($logInfo, 3, SHOPROOT.'/log/pg_forge_log_'.date('Ymd').'.log');
		return false;
	}
}


if (!function_exists('http_build_query')) {
	function http_build_query($formdata=null, $numeric_prefix='', $arg_separator='&',$tag='') {

		$str = "";

		if (is_array($formdata)) {
			foreach($formdata as $k => $v) {
				if (is_array($v)) {
					$str .= ($str != "" ? $arg_separator : '') . http_build_query($v, $numeric_prefix, $arg_separator, $k);
				}
				else {
					$_key = ($tag) ? $tag.'['.$k.']' : $k;
					$str .= ($str != "" ? $arg_separator : '').urlencode($_key).'='.urlencode($v);
				}
			}
		}

		return ($str);

	}
}

function getordno() {
	global $db;

	list($max) = $db->fetch("SELECT max(ordno) FROM ".GD_ORDER_TEMP);

	if (!empty($max)) {
		if (($time = (int)substr($max,0,10)) < G_CONST_NOW) {
			$time = G_CONST_NOW;
		}
	}
	else {
		$time = G_CONST_NOW;
	}

	$suffix = rand(0,999);
	$find = $ordno = false;

	do {

		$chker++;

		$ordno = $time.sprintf("%03d",$suffix);

		$query = "insert into ".GD_ORDER_TEMP." (ordno) values ('$ordno')";
		$db->query($query);

		if($db->affected() > 0) {
			$find = true;
			$old = ($time - 3600) .'999';
			$db->query("delete from ".GD_ORDER_TEMP. " where ordno < '$old'");
		}
		else {
			if ($suffix < 999) {
				$suffix++;
			}
			else {
				$time++;
				$suffix = 0;
			}
		}

	} while (! $find);

	return $ordno;
}

/**
 * 슬래시로 인용 문자열
 * @param array|string $arr_r 배열 또는 값
 */
function add_slashes($arr_r)
{
	if (is_array($arr_r) === true) {
		foreach ($arr_r as $k => $val) {
			$arr_r[$k] = is_array($val) ? add_slashes($val) : addslashes($val);
		}
	}
	else {
		$arr_r = addslashes($arr_r);
	}
	return $arr_r;
}

/**
 * 취소 따옴표 인용 문자열
 * @param array|string $arr_r 배열 또는 값
 */
function strip_slashes($arr_r)
{
	if (is_array($arr_r) === true) {
		foreach ($arr_r as $k => $val) {
			$arr_r[$k] = is_array($val) ? strip_slashes($val) : stripslashes($val);
		}
	}
	else {
		$arr_r = stripslashes($arr_r);
	}
	return $arr_r;
}

function getCheckoutOrderStatus($order,$full=false) {

	global $checkout_message_schema;

	if (empty($checkout_message_schema)) {
		$checkout_message_schema = include dirname(__FILE__)."/../admin/order/_cfg.checkout.php";
	}

	$status = $checkout_message_schema['productOrderStatusType'][$order['ProductOrderStatus']];

	if ($order['PlaceOrderStatus'] == 'OK' && $order['ProductOrderStatus'] == 'PAYED') {
		$status = '배송준비중';
	}

	// 철회 상태가 아닌 클레임.
	if ($order['ClaimType'] && !preg_match('/_REJECT$/',$order['ClaimStatus'])) {

		// 전체 메시지 출력 & 완료상태 아닐때
		if ($full && !preg_match('/_DONE$/',$order['ClaimStatus'])) {
			$status  = '<span style="text-decoration:line-through;">'.$status.'</span>';
			$status .= '⇒';
		}
		else {
			$status = '';
		}

		$status .= $checkout_message_schema['claimStatusType'][$order['ClaimStatus']];

		if (preg_match('/_DONE$/',$order['ClaimStatus']) && ($order['HoldbackStatus'] == 'HOLDBACK' || $order['claimInfo']['HoldbackStatus'] == 'HOLDBACK')) {

			if ($order['HoldbackReason'] == 'PURCHASER_CONFIRM_NEED' || $order['claimInfo']['HoldbackReason'] == 'PURCHASER_CONFIRM_NEED') {
				// 구매자의 승인(재결재 혹은 확인)이 필요한 클레임 건
				//$status .= '<br>구매자 승인 대기';
			}
		}
		else if ($order['ClaimType'] == 'EXCHANGE' && $order['ClaimStatus'] == 'COLLECT_DONE' && ($order['HoldbackStatus'] == 'RELEASED' || $order['claimInfo']['HoldbackStatus'] == 'RELEASED')) {
			$status = '교환배송준비중';
		}

	}

	return $status;
}

/**
 * PG 특수문자 제거
 *
 * @param string $textMsg 내용
 */
function pg_text_replace($textMsg)
{
	$arrReplace	= array(',', '&', ';', chr(10), '\\', '|', '\'', '`', '"', '%',);
	foreach ($arrReplace as $val) {
		$textMsg	= str_replace($val, '', $textMsg);
	}

	return $textMsg;
}

function get_items_rowspan(&$items) {

	$span = array();

	foreach ($items as $key => $row) $delivery_type[$key]  = $row['delivery_type'];

	if (!empty($delivery_type)) {
		$_span = array_count_values($delivery_type);

		foreach($_span as $k => $v) {
			$fill = $k !== 0 ? 1 : 0;
			$span[] = $fill == 1 ? $fill : $v;
			if ($v-1 > 0) {
				$span = array_pad ( $span, sizeof($span) + $v - 1, $fill);
			}
		}

	}

	return $span;

}

function get_js_compatible_key($str) {

	$str = html_entity_decode($str); $str = htmlspecialchars($str);

	$r = "";

	for ($i=0,$m=strlen($str);$i<$m;$i++) {
		$c = $str[$i];
		if (ord($c) >= 127) {
			$i++;
			$c .= $str[$i];
		}

		if (($h = js_ord( $c )) !== false) {
			$r .= $h;
		}
	}

	return strtoupper($r);

}

function js_ord($c) {	// = js's charCodeAt

	$c = iconv("euc-kr","utf-8",$c);

	$h = ord($c{0});
	if ($h <= 0x7F) {
		return $h;
	} else if ($h < 0xC2) {
		return false;
	} else if ($h <= 0xDF) {
		return ($h & 0x1F) << 6 | (ord($c{1}) & 0x3F);
	} else if ($h <= 0xEF) {
		return ($h & 0x0F) << 12 | (ord($c{1}) & 0x3F) << 6
								 | (ord($c{2}) & 0x3F);
	} else if ($h <= 0xF4) {
		return ($h & 0x0F) << 18 | (ord($c{1}) & 0x3F) << 12
								 | (ord($c{2}) & 0x3F) << 6
								 | (ord($c{3}) & 0x3F);
	} else {
		return false;
	}
}

function chkOpenYn($items,$msg="C",$action,$reOrder_chk='')
{
	switch($msg) {
		// 상품의 판매 중지 설정여부를 체크 하기위한 D 타입 추가
		// extacy @ 2013-04-05
		case "D":
			$_msg=' 상품은 현재 판매중인 상품이 아닙니다.';
			break;
		case "A":
			$_msg=' 상품은 현재 진열중인 상품이 아닙니다. 삭제 후 진행해 주세요.';
			break;
		case "B":
			$_msg=' 상품은 현재 진열중인 상품이 아닙니다. 상품보관함에 담기지 않습니다.';
			break;
		case "C":
			$_msg=' 상품은 현재 진열중인 상품이 아닙니다.';
			break;
	}
	$chk=rtnOpenYn($items, $msg);
	foreach($chk as $val) {
		if(!$reOrder_chk) msg($val.$_msg, $action);
		else return 1;
	}
}
function setNotAvailbleGoodsName($goodsno, $msg = null, &$goodsnames) {

	// 상품 모델
	$goods = Clib_Application::getModelClass('goods');
	$goods->load($goodsno, array('goodsnm','open','sales_range_start','sales_range_end','buyable_member_group','buyable'));
	
	// 모바일 접속시 모바일로 세팅
	if ($isMobile == 'Y') Clib_Application::setMobile();

	$add = false;

	// 구매가능 회원그룹 설정
	switch ($goods['buyable']) {
		case '2':	// 회원전용
			if ( ! Clib_Application::session()->isLogged()) {
				$add = true;
			}
			break;
		case '3':	// 특정회원그룹
			// 특정 회원 그룹이 아닐때나 회원이 아닐때(비회원일때) => $add = true; 처리
			if (substr($goods->getBuyableMemberGroup(), Clib_Application::session()->getMemberLevel() - 1, 1) !== '1' || ! Clib_Application::session()->isLogged()) {
				$add = true;
			}
			break;
		case '1':	// 전체
		default:
			break;
	}

	// 진열여부
	if (Clib_Application::isMobile()) {

		global $cfgMobileShop;

		if (empty($cfgMobileShop)) {
			@include(dirname(__FILE__).'/../conf/config.mobileShop.php');
		}

		$open = $cfgMobileShop['vtype_goods'] ? $goods['open_mobile'] : $goods['open'];
	}
	else {
		$open = $goods['open'];
	}

	if ($add === false && !$open) {
		$add = true;
	}

	// 판매여부
	if ($add === false && ($msg == 'D' || $msg == 'C' ||  $msg == 'A') && ! $goods->canSales()) {
		$add = true;
	}

	if ($add) {
		array_push($goodsnames, htmlspecialchars($goods['goodsnm']));
	}

}

function rtnOpenYn($items, $msg = null, $isMobile = '') {


	// 상품 모델
	$goods = Clib_Application::getModelClass('goods');

	$goodsname=array();

	if(is_object($items)) {	// cart object
		foreach ($items->item as $k=>$item){
			setNotAvailbleGoodsName($item[goodsno], $msg, $goodsname, $isMobile);
		}
	}
	else if(is_array($items)){	//세트상품일경우(배열로넘어옴)
		foreach($items as $val) {
			if(array_key_exists('goodsno', $val) && $val[goodsno]){
				setNotAvailbleGoodsName($val[goodsno], $msg, $goodsname, $isMobile);
			}
			else {
				setNotAvailbleGoodsName($val, $msg, $goodsname, $isMobile);
			}
		}
	}
	else{
		setNotAvailbleGoodsName($items, $msg, $goodsname, $isMobile);
	}

	return $goodsname;
}

function snsPosts($sno) {
	$sns = new SNS();
	echo $sns->get_post_listbox($sno);
}

// @see : https://developers.google.com/youtube/player_parameters
function youtubePlayer($url, $sizeType = 0, $width = 640, $height = 360) {

	if (preg_match('/^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=)([^#\&\?]*).*/', $url, $matches)) {

		$videoId = $matches[2];

		if (! $sizeType || (int) $width === 0) {
			$width = 640;
		}

		if (! $sizeType || (int) $height === 0) {
			$height = 360;
		}

		$player = '<iframe width="%d" height="%d" src="http://www.youtube.com/embed/%s" frameborder="0" allowfullscreen></iframe>';
		$player = sprintf($player, $width, $height, $videoId);
		return $player;

	}
	else {
		return '';
	}

}

function storeCookieMemberInfo($memberId, $memberPw)
{
	$sessionMemberInfo = array($memberId, $memberPw);
	$serializedMemberInfo = serialize($sessionMemberInfo);
	$encodedMemberInfo = base64_encode(base64_encode($serializedMemberInfo).base64_encode('godomall'));
	setCookie('stored_member_info', $encodedMemberInfo, time() + (60 * 60 * 24 * 30), '/');
}

function accessCookieMemberInfo()
{
	if (isset($_COOKIE['stored_member_info']) === false) {
		return false;
	}
	else {
		$encodedMemberInfo = $_COOKIE['stored_member_info'];
		$decodedMemberInfo = base64_decode(preg_replace('/'.base64_encode('godomall').'$/', '', base64_decode($encodedMemberInfo)));
		return unserialize($decodedMemberInfo);
	}
}

function expireCookieMemberInfo()
{
	if (accessCookieMemberInfo()) {
		setCookie('stored_member_info', '', 1, '/');
	}
}

// 쿠폰정보 모바일샵
function getCouponInfoMobile($goodsno,$goodsprice='',$mode='',$daumCpc=''){
	global $set;
	if (!$set) $set = Core::config('configpay');

	$Goods = Core::loader('Goods');
	$arCategory = $Goods->get_goods_category($goodsno);

	if(!$goodsprice){
		$goodsprice = $Goods->get_goods_price($goodsno);
	}

	$coupon_price = Core::loader('coupon_price');
	$cfgCoupon = & $GLOBALS['cfgCoupon'];
	if(!$cfgCoupon) @include SHOPROOT."/conf/coupon.php";

	if($mode && $mode!='list'){
		$mode = "view";
	} else {
		$mode = "list";
	}

	$coupon_price->reset_item(); // 쿠폰 적용 상품 아이템 정보 리셋
	$coupon_price->set_config($cfgCoupon); // 쿠폰 설정
	$coupon_price->set_item($goodsno,$goodsprice,1,$arCategory); //적용상품
	$coupon_price->get_goods_coupon_mobile($mode);

	if($mode&& $mode!='list')return $coupon_price->arCoupon;
	if($coupon_price->arCoupon)foreach($coupon_price->arCoupon as $arr){

		if($mode=="list"){
			$sale = (int) $arr['sale'][$goodsno];
			$reserve = (int) $arr['reserve'][$goodsno];
		}else{
			$sale = (int) $arr['sale'][$goodsno] + (int) $arr['sale']['order'];
			$reserve = (int) $arr['reserve'][$goodsno] + (int) $arr['reserve']['order'];
		}

		// 사용 제한 금액이 적용 할인/적립
		$_sale = ($arr['excPrice'] > 0 && $goodsprice < $arr['excPrice']) ? 0 : $sale;
		$_reserve = ($arr['excPrice'] > 0 && $goodsprice < $arr['excPrice']) ? 0 : $reserve;

		if($cfgCoupon['double'] == 1){
			$result[0] += $sale;
			$result[1] += $reserve;

			$result[10] += $_sale;
			$result[11] += $_reserve;

		}else{
			if($result[0] < $sale) $result[0] = $sale;
			if($result[1] < $reserve) $result[1] = $reserve;

			if($result[10] < $_sale) $result[00] = $_sale;
			if($result[11] < $_reserve) $result[11] = $_reserve;
		}

		if ($daumCpc === 'Y' && $arr['sale'] && $arr['c_screen'] === 'm') {
			$result[2][] = $arr['price'];
		}
	}
	return $result;
}

// 구매 가능 상태 체크
function getGoodsBuyable($goodsno)
{
	$result = true; // true : 구매가능, buyable2 : 회원전용 로그인 전임, buyable3 : 특정회원그룹 소속 안됨.

	// 상품 모델
	$goods = Clib_Application::getModelClass('goods');
	$goods->load($goodsno, array('buyable_member_group','buyable'));

	// 구매가능 회원그룹 설정
	switch ($goods['buyable']) {
		case '2':	// 회원전용
			if ( ! Clib_Application::session()->isLogged()) {
				$result = 'buyable2';
			}
			break;
		case '3':	// 특정회원그룹
			// 특정 회원 그룹이 아닐때나 회원이 아닐때(비회원일때)
			if ( ! Clib_Application::session()->isLogged()) {
				$result = 'buyable2';
				break;
			}
			if (substr($goods->getBuyableMemberGroup(), Clib_Application::session()->getMemberLevel() - 1, 1) !== '1') {
				$result = 'buyable3';
			}
			break;
		case '1':	// 전체
		default:
			break;
	}

	return $result;
}

### 오늘본상품 - 모바일샵
function todayGoodsMobile($arr, $date=1)
{

	$max = 30;	// 리스트 저장 개수
	$goodsno = $arr[goodsno];
	$div = explode(",",$_COOKIE[todayGoodsMobileIdx]);
	$todayG = unserialize(stripslashes($_COOKIE[todayGoodsMobile]));
	if (!is_array($todayG)) $todayG = array();
	if (in_array($goodsno,$div)){
		$key = array_search($goodsno,$div);
		array_splice($div,$key,1);
		array_splice($todayG,$key,1);
	}
	array_unshift($div,$goodsno); array_unshift($todayG,$arr);
	array_splice($todayG,$max); //array_splice($div,$max);

	setcookie('todayGoodsMobileIdx',implode(",",$div),time()+3600*24*$date,'/');
	setcookie('todayGoodsMobile',serialize($todayG),time()+3600*24*$date,'/');
}

### 모바일 회원로그인 로그 남기기
function mobile_member_log( $m_id ){

	$log_msg = "";
	$log_msg .= date('Y-m-d H:i:s') . "\t";
	$log_msg .= $_SERVER['REMOTE_ADDR'] . "\t";
	$log_msg .= $m_id . "\n";

	error_log($log_msg, 3, $tmp = dirname(__FILE__) . "/../log/mobile_login_" . date('Ym') . ".log");
	@chmod( $tmp, 0707 );
}

// 프로토콜, 포트포함 도메인 2013-05-15 추가
function ProtocolPortDomain() {
	if($_SERVER['SERVER_PORT'] == 80) {
		$Port = "";
	} elseif($_SERVER['SERVER_PORT'] == 443) {
		$Port = "";
	} else {
		$Port = $_SERVER['SERVER_PORT'];
	}
	if (strlen($Port) > 0) $Port = ":".$Port;
	$Protocol = ($_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';

	$host = parse_url($_SERVER['HTTP_HOST']);
	if ($host['path']) {
		$Host = $host['path'];
	} else {
		$Host = $host['host'];
	}
	$ProtocolPortDomain = $Protocol.$Host.$Port;

	return $ProtocolPortDomain;
}

### 주문정보 유효성 체크 모바일샵
function chkCartMobile($cart,$vtype_goods)
{
	global $db,$sess,$ableDc,$ableCoupon;

	if( !$ableDc )$memberdc = 0;
	else $memberdc = $cart->dc;

	foreach ($cart->item as $k=>$item){

		$query = "
		select
			a.goodsno,a.goodsnm,a.open, a.open_mobile, a.todaygoods,b.*
		from
			".GD_GOODS." a
			left join ".GD_GOODS_OPTION." b on a.goodsno=b.goodsno and go_is_deleted <> '1' and go_is_display = '1'
		where
			a.goodsno = '$item[goodsno]'
			and b.opt1 = '".mysql_real_escape_string($item[opt][0])."'
			and b.opt2 = '".mysql_real_escape_string($item[opt][1])."'
		";
		$data = $db->fetch($query);

		$open = $data['open'];
		if($vtype_goods) {
			$open = $data['open_mobile'];
		}

		if (!$open) {
			// 투데이샵 상품은 field 가 다름
			if ($data['todaygoods'] == 'y') {
				$t_data = $db->fetch("SELECT visible FROM ".GD_TODAYSHOP_GOODS_MERGED." WHERE goodsno = '$data[goodsno]'",1);
				if ($t_data['visible'] == 0) msg($data['goodsnm']." 상품은 현재 진열중인 상품이 아닙니다.", -1);
			}
			else {
				msg($data['goodsnm']." 상품은 현재 진열중인 상품이 아닙니다.", -1);
			}
		}

		if ($data[price]!=$item[price]) msg("상품가격이 일치하지 않습니다",-1);
		if ($data[goodsnm]!=$item[goodsnm]) msg("상품명이 일치하지 않습니다",-1);

		$item_price = $data[price];
		if($memberdc) $item_price -= getDcPrice($data[price],$memberdc);


		### 추가옵션 체크
		if (sizeof((array)$item['addopt']) !== sizeof(array_notnull(explode(',',$item['addno'])))) {
			msg($data['goodsnm']." 추가옵션정보를 확인해 주세요.", -1);
		}

		if ($item[addopt]){
			foreach ($item[addopt] as $v){
				$query = "
				select * from
					".GD_GOODS_ADD."
				where
					goodsno = '$item[goodsno]'
					and sno = '$v[sno]'
				";
				$dataAdd = $db->fetch($query);

				if ($dataAdd[addprice]!=$v[price]) msg("옵션추가가격이 일치하지 않습니다",-1);
				if ($dataAdd[opt]!=$v[opt]) {
					if ($dataAdd['type'] == 'I') {
						$dataAdd[opt] = intval($dataAdd[opt]);
						if ($dataAdd[opt] === 0 || $dataAdd[opt] >= mb_strlen($v[opt], Clib_Application::getConfig('global', 'charset'))) {
							continue;
						}
					}

					msg("추가옵션이 일치하지 않습니다",-1);

				}

			}
		}
	}
}

function getReserve($goods) {

	global $set;

	$reserve = 0;

	// 상품별 설정이 아닐때.
	if(!$goods['use_emoney']){
		if( !$set['emoney']['chk_goods_emoney'] ){
			if( $set['emoney']['goods_emoney'] ) $reserve = getDcprice($goods['price'],$set['emoney']['goods_emoney'].'%');
		}else{
			$reserve = $set['emoney']['goods_emoney'];
		}
	}
	// 상품별 설정일때
	else {
		$reserve = $goods['reserve'];
	}

	return $reserve;

}

/**
 * 회원 추가적립금 계산
 * @param integer $amount 지급할 적립금율
 * @param string $type	적립금 기준금액 구분 (goods, settle_amt)
 * @param integer $min	최소 기준금액 (기준 금액이 해당 값 이상이어야 함)
 * @param integer $price	기준금액
 * @param Cart $cart	장바구니
 * @return integer 계산된 적립금
 */
function getExtraReserve($amount, $type, $min, $price, $cart)
{
	if (!$amount || !$price) return 0;

	if (get_class($cart) == 'Cart') {
		foreach($cart->item as $item) {
			if ($item['exclude_member_reserve']) {

				$minus = ($item['price'] * $item['ea']);

				if ($type == 'settle_amt') {
					// @TODO : 쿠폰, 상품할인 등등 제외
					$minus -= 0;
				}
			}
			else {
				$minus = 0;
			}

			$price = $price - $minus;
		}

		if ($price >= $min) {
			$reserve = calculateExtraReserveEachItem($amount, $type, $price, $cart);
		}
		else {
			$reserve = 0;
		}
	}
	else {
		$reserve = ($price >= $min) ? getDcprice($price, $amount.'%') : 0;
	}

	return (int)$reserve;
}

/**
 * 회원 추가적립금 계산하여 $cart->item[n]['extra_reserve']에 넣어준 뒤 총 추가적립금을 반환한다
 * @param integer $amount 지급할 적립금율
 * @param string $type	적립금 기준금액 구분 (goods, settle_amt)
 * @param integer $price 기준금액
 * @param Cart $cart 장바구니
 * @return integer 총 추가적립금
 */
function calculateExtraReserveEachItem($amount, $type, $price, $cart)
{
	$totalReserve = 0;
	if ($type == 'settle_amt') {
		foreach ($cart->item as $key => $item) {
			$percentage = (float)(((int)$item['price'] / $cart->goodsprice) * 100);
			$reservePrice = ((int)$price / 100) * $percentage;
			$reserve = getDcprice($reservePrice, $amount.'%');
			$cart->item[$key]['extra_reserve'] = $reserve;
			$totalReserve += $reserve * $item['ea'];
		}
	}
	else {
		foreach ($cart->item as $key => $item) {
			$reserve = getDcprice((int)$item['price'], $amount.'%');
			$cart->item[$key]['extra_reserve'] = $reserve;
			$totalReserve += $reserve * $item['ea'];
		}
	}
	return $totalReserve;
}

/**
 * 주문접수 메일 발송에 필요한 주문데이터
 * @param  bigInt ordno 주문번호
 * @return object result 주문데이터
 */
function getMailOrderData($ordno) {
	global $db;
	global $r_settlekind;
	$orderRow = array();
	$item = array();
	$items = array();

	$queryOrder="SELECT a.ordno,a.nameOrder,a.phoneOrder,a.mobileOrder,a.nameOrder,a.email,a.goodsprice,a.settleprice,a.settlekind,a.zipcode,a.zonecode,a.address,a.nameReceiver,a.phoneReceiver,a.mobileReceiver,a.deliverycode,a.delivery,b.deliveryno,b.deliveryurl,a.memo,a.deli_msg
	FROM ".GD_ORDER." a LEFT OUTER JOIN ".GD_LIST_DELIVERY." b on a.deliveryno=b.deliveryno WHERE a.ordno='".$ordno."' ";
	$orderRow  = $db->fetch($queryOrder);

	$queryItem = "
		SELECT a.goodsno,a.goodsnm,a.opt1,a.opt2,a.addopt,a.reserve,a.price,a.ea,b.img_s
		FROM ".GD_ORDER_ITEM." AS a
		INNER join ".GD_GOODS." AS b on a.goodsno=b.goodsno
		where	a.ordno = '".$ordno."'";
	$orderItemList = $db->query($queryItem);

	while($itemRow = $db->fetch($orderItemList)) {
		$item['img']=$itemRow['img_s'];
		$item['goodsnm'] = $itemRow['goodsnm'];
		$item['goodsimg'] = $itemRow['img'];
		$item['reserve'] = $itemRow['reserve']*$itemRow['ea'] ;	//적립금
		$item['price'] = $itemRow['price'];	//단일상품가
		$item['ea']= $itemRow['ea'];	//갯수
		$item['sumprice'] = $itemRow['price']*$itemRow['ea'];	//상품합계

		$item['opt'] = array();
		if($itemRow['opt1']){
			$item['opt'][] = $itemRow['opt1'];
		}
		if($itemRow['opt2']){
			$item['opt'][] = $itemRow['opt2'];
		}

		$item['addopt'] = array();
		if($itemRow['addopt']){
			$_addOpt = explode('^',$itemRow['addopt']);
			for($i=0 ; $i < count($_addOpt) ; $i++) {
				list($optnm, $opt) = explode(':',$_addOpt[$i]);
				$item['addopt'][$i]['optnm'] =  $optnm;
				$item['addopt'][$i]['opt'] =  $opt;
			}
		}

		$item['goodsimg'] = $itemRow['goodsnm'];
		$items[] = $item;
	}

	$data = $orderRow ;
	$data['str_settlekind'] = $r_settlekind[$orderRow['settlekind']];
	$data['zipcode'] = ($data['zonecode']) ? $data['zonecode'] : $data['zipcode'];
	$data['cart'] = new stdClass;
	$data['cart']->item = $items;
	$data['cart']->goodsprice = $orderRow['goodsprice'];
	$data['cart']->delivery = $orderRow['delivery'];
	$data['cart']->totalprice = $orderRow['goodsprice']+$orderRow['delivery'];

	return $data;
}

/**
 * 패스워드 사용 가능 여부
 * 10자이상 ~ 16자이하 && 영문소문자, 영문대문자, 숫자, 특수문자32가지중 두가지포함
 * @param  string 패스워드
 * @return boolean 패스워드 사용 가능여부
 */
function passwordPatternCheck($password){
	$passwordCount = 0;
	if (preg_match('/[a-z]/',$password)) $passwordCount++;
	if (preg_match('/[A-Z]/',$password)) $passwordCount++;
	if (preg_match('/[0-9]/',$password)) $passwordCount++;
	if (preg_match('/[~`!>@?\/<#\"\'$;:\]%.^,&[*()_+\-=|\\\{}]/',$password)) $passwordCount++;
	if (!preg_match('/^[\x21-\x7E]{10,16}$/',$password) || $passwordCount < 2){
		return false;
	}

	return true;
}

/**
 * 약관, 개인정보, 이용안내, 탈퇴안내 내용
 * @param1  string $type => 'terms' or 'guide' get종류
 * @param2  string $fileName 파일명
 * @param3  string $textArea 'Y' or 'N' textarea 노출여부
 * @return  string $result 치환된 내용
 */
function getTermsGuideContents($type, $fileName, $textArea = 'N')
{
	global $tpl;

	$template_dir		= $tpl->template_dir;				//기존 template_dir 백업

	$tpl->template_dir	= dirname(__FILE__) . '/../';		//template_dir 재지정
	$filePath = 'conf/' . $type . '/' . $fileName . '.txt';	//파일경로
	$absolutePath = $tpl->template_dir . $filePath;			//파일 절대경로
	$relativePath = $filePath;								//파일 상대경로

	if(is_file($absolutePath)){
		$tpl->define('result', $relativePath);

		if ($textArea === 'Y') {
			$result = $tpl->fetch('result');
		} else {
			$result = nl2br($tpl->fetch('result'));
		}
	}

	$tpl->template_dir = $template_dir;

	if($result) return $result;

	return false;
}

/**
 * 카테고리 코드를 토대로 상위 카테고리 코드를 가지고 옴
 * @param  sting cateString 카테고리코드 (여러개 인경우 '|' 로 구분)
 * @return array 카테고리 코드
 */
function getHighCategoryCode($cateString){

	// 카테고리 자리수
	$cateLength		= 3;

	// 구분자
	$strDivision	= '|';

	// 받아온 카테고리를 구분자에 의해 자른뒤 배열
	$arrCate		= explode($strDivision, $cateString);

	// 상위 카테고리 배열
	$arrCateHigh	= array();

	// 카테고리 처리
	foreach($arrCate as $key => $val){

		// 공백 처리
		$val		= trim($val);

		// 카테고리 자리수에 못미치는 경우 앞에 0 처리 (ex : 23 -> 023)
		$arrCate[$key]	= str_pad($val, ceil(strlen($val) / $cateLength ) * $cateLength, '0', STR_PAD_LEFT);

		//카테고리 차수 계산
		if (_CATEGORY_NEW_METHOD_ === true) {
			for ($i = 1; $i <= (strlen($arrCate[$key]) / $cateLength); $i++){
				// 상위카테고리를 배열 처리
				$arrCateHigh[]	= substr($arrCate[$key], 0, ($i * $cateLength));
			}
		}
	}

	// 카테고리 전부 배열화
	if (_CATEGORY_NEW_METHOD_ === true) {
		$arrCate	= array_merge($arrCate, $arrCateHigh);
	}

	// 중복 제거
	$arrCate	= array_unique($arrCate);

	// 소트
	sort($arrCate);

	return $arrCate;
}

/**
 * 상품분류 연결방식 전환 여부에 따른 gd_goods_link 테이블의 검색 조건
 * @param1  string $fieldName 사용한 필드 이름 (gd_goods_link 의 category 필드네임)
 * @param2  string $category 카테고리
 * @param3  string $resultCode 리턴될 배열명, 없는 경우 전부 배열로, 있는 경우 내용으로 출력
 * @param2  string $groupby GROUP BY 처리한 필드 이름
 * @return  array/string $result query
 */
function getCategoryLinkQuery($fieldName, $category = null, $resultCode = null, $groupby = null)
{
	// 상품분류 연결방식 전환 여부에 따른 처리
	if (_CATEGORY_NEW_METHOD_ === true) {
		$result['where']		= " " . $fieldName . " = '" . $category . "' ";
		$result['distinct']		= "";
		$result['group']		= "";
		$result['max']			= "MAX(" . $fieldName . ") AS category";
	}
	else {
		$result['where']		= " " . $fieldName . " LIKE '" . $category . "%' ";
		$result['distinct']		= " DISTINCT ";
		if (is_null($groupby) === false) {
			$result['group']		= " GROUP BY ".$groupby;
		}
		$result['max']			= $fieldName;
	}

	if (is_null($resultCode)) {
		return $result;
	}
	else {
		return $result[$resultCode];
	}
}

/*
 * kb, mb, gb 를 byte로 변환
 */
function str_to_byte($str)
{
    if (preg_match('/([0-9\.]+)\s?([A-Za-z]+)/', $str, $matches)) {

        /* @formatter:off */
        $exp = array(
			'B' => 0,
            'K' => 1,
            'KB' => 1,
            'M' => 2,
            'MB' => 2,
            'G' => 3,
            'GB' => 3,
        );
        /* @formatter:on */

        $size = $matches[1];
        $unit = strtoupper($matches[2]);

        return ceil($size * pow(1024, $exp[$unit]));
    }
    else {
        return $str;
    }
}

/**
 * PG 데이타 로그 저장
 * @param array $pgData PG DATA 내용 (1차원 배열)
 * @param string $pgName PG사 이름 (영문명으로만)
 * @param string $savePath PG DATA 저장 경로
 */
function pg_data_log_write($pgArrData, $pgName, $savePath)
{
	// 저장할 로그 화일
	$saveFile	= $savePath . $pgName.'_log_'.date('Ymd').'.log';

	// 저장할 폴더가 없는 경우 생성 및 권한 설정
	if (!is_dir($savePath)) {
		@mkdir($savePath, 0707);
		@chmod($savePath, 0707);
	}

	// PG 데이타 가공
	foreach ($pgArrData as $key => $val) {
		$logData[]	= 'DEBUG	['.date('Y-m-d H:i:s').']	'.$key.' : '.$val;
	}

	// 저장될 로그 내용
	$pgLogData	 = 'INFO	['.date('Y-m-d H:i:s').']	START '.$pgName.' PG DATA log'.chr(10);
	$pgLogData	.= 'INFO	['.date('Y-m-d H:i:s').']	Connect IP	: '.$_SERVER['REMOTE_ADDR'].chr(10);
	$pgLogData	.= 'INFO	['.date('Y-m-d H:i:s').']	Referer URL : '.$_SERVER['HTTP_REFERER'].chr(10);
	$pgLogData	.= 'INFO	['.date('Y-m-d H:i:s').']	Request URL	: '.$_SERVER['REQUEST_URI'].chr(10);
	$pgLogData	.= 'INFO	['.date('Y-m-d H:i:s').']	User Agent	: '.$_SERVER['HTTP_USER_AGENT'].chr(10);
	$pgLogData	.= implode(chr(10),$logData).chr(10);
	$pgLogData	.= 'INFO	['.date('Y-m-d H:i:s').']	END '.$pgName.' PG DATA log'.chr(10);
	$pgLogData	.= '------------------------------------------------------------------------------'.chr(10).chr(10);

	//로그 저장
	@error_log($pgLogData, 3, $saveFile);

	// 권한 설정
	@chmod( $saveFile, 0707 );
}

function settleIcon($flow)
{
	global $cfg;

	if(!$cfg){
		$cfg = Core::loader('config')->load('config');
	}

	switch($flow) {
		case 'payco' :
			return '<img src="'.$cfg['rootDir'].'/admin/img/icon_payco.gif">';
			break;
	}
}

/**
 * 수신동의설정 메일발송
 * @author working by
 * @param string $mail   발송될 메일주소
 * @param string $mailling 메일수신여부
 * @param string $sms sms 수신여부
 */
function sendAcceptAgreeMail($email, $mailling='', $sms='')
{
	global $db;

	$acceptAgreeData = array();
	$acceptAgreeData = setAcceptAgreeData($mailling, $sms);

	if(function_exists('sendMailCase')){
		sendMailCase($email, 31, $acceptAgreeData);
	}
}

/**
 * 수신동의 치환 데이타
 * @author working by
 * @param string $mailling 메일수신여부
 * @param string $sms sms 수신여부
 */
function setAcceptAgreeData($mailling, $sms)
{
	$acceptAgreeData = array(
		'agreeDate_year'	=> date('Y'),
		'agreeDate_month'	=> date('m'),
		'agreeDate_day'	=> date('d'),
		'emailAgree' => '',
		'smsAgree' => '',
	);

	$acceptAgreeData['emailAgree'] = ($mailling == 'y') ? '수신동의' : '수신거부';
	$acceptAgreeData['smsAgree'] = ($sms == 'y') ? '수신동의' : '수신거부';

	return $acceptAgreeData;
}

### 성인인증 일자
function getAdultAuthDate($m_id)
{
	global $db,$set;

	$query = "select * from ".GD_MEMBER." where m_id='$m_id'";
	$data = $db->fetch($query);

	return $data;
}

### 성인인증 일자 갱신
function setAdultAuthDate($m_id)
{
	global $db;

	$currentDate = date("Y-m-d");
	$db->query("update ".GD_MEMBER." set auth_date='$currentDate' where m_id='$m_id'");
}

// 다음 쇼핑하우 요약 EP DB 체크
function daum_goods_diff_check() {
	global $db;
	$tmp = date("Y-m-d 00:00:00");
	$db->query("delete from ".GD_GOODS_UPDATE_DAUM." where utime < '$tmp'");
}

// 다음 쇼핑하우 요약 EP 품절 체크
function daum_goods_runout($goodsno) {
	global $db;
	$str_date = date("Y-m-d H:i:s");
	$db->query("insert into ".GD_GOODS_UPDATE_DAUM." set mapid='$goodsno',class='D',utime='$str_date'");
}

// 다음 쇼핑하우 요약 EP 품절->구매가능
function daum_goods_runout_recovery($goodsno) {
	global $db;
	$str_date = date("Y-m-d H:i:s");
	$db->query("insert into ".GD_GOODS_UPDATE_DAUM." set mapid='$goodsno',class='U',utime='$str_date'");
}

// 다음 쇼핑하우 상품평 EP 체크
function daum_goods_review_check() {
	global $db;
	$tmp = date("Y-m-d 00:00:00",strtotime("-1 days"));
	$db->query("delete from ".GD_GOODS_UPDATE_REVIEW_DAUM." where deldt < '$tmp'");
}

// 다음 쇼핑하우 상품평 EP DB 저장
function daum_goods_review($sno) {
	global $db;
	daum_goods_review_check();

	$str_date = date("Y-m-d H:i:s");
	$query = "select sno,goodsno,subject,contents,point,regdt,name,parent from ".GD_GOODS_REVIEW." where sno='$sno'";
	$data = $db->fetch($query);

	if ($data['sno'] === $data['parent'] && $data['goodsno'] > 0) {
		$db->query("
		insert into ".GD_GOODS_UPDATE_REVIEW_DAUM." set
			sno='$data[sno]',
			goodsno='$data[goodsno]',
			subject='$data[subject]',
			contents='$data[contents]',
			point='$data[point]',
			regdt='$data[regdt]',
			name='$data[name]',
			deldt='$str_date'
			");
	}
	else
		return;
}
?>
