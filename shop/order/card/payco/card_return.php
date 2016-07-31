<?
/*
페이코 결제완료 통보
*/
include "../../../lib/library.php";

function response_log($msg)
{
	global $paycoApi;

	$paycoApi->receive_log($msg, 'card_return');
	exit($msg);
}

function arr_data_iconv($b)
{
	$iconv_data = array();

	foreach($b as $k => $v) {
		if(is_array($v)) $iconv_data[$k] = arr_data_iconv($v);
		else $iconv_data[$k] = iconv('utf-8', 'euc-kr', $v);
	}
	return $iconv_data;
}

/*
	gd_order, gd_order_item 데이터 복원
*/
function order_data_restoration()
{
	global $bankup_gd_order_data, $backup_gd_order_item_data, $gd_order_ins, $arr_gd_order_item, $db;

	foreach($gd_order_ins as $key => $val) {
		$upd_data[$key] = $bankup_gd_order_data[$key];
	}

	$query = $db->_query_print('UPDATE '.GD_ORDER.' SET [cv] WHERE ordno=[i]', $upd_data, $backup_gd_order_data['ordno']);
	$rtns[] = $db->_query($query);

	foreach($backup_gd_order_item_data as $b_item) {
		$item_upd_data['istep'] = $b_item['istep'];
		$item_upd_data['cyn'] = $b_item['cyn'];
		$item_query = $db->_query_print('UPDATE '.GD_ORDER_ITEM.' SET [cv] WHERE sno=[i]', $item_upd_data, $b_item['sno']);
		$rtns[] = $db->_query($item_query);
	}

	foreach($rtns as $rtn) if(!$rtn) return false;
	return true;
}


$arr_data = $_POST;

//response data 유효성 체크
if(empty($arr_data)) response_log('전송된 데이터가 없습니다.');

//페이코 클래스
$paycoApi = &load_class('paycoApi','paycoApi');
$payco = &load_class('payco','payco');

// 수신데이터 로그 저장
$paycoApi->receive_log($arr_data, 'card_return');

// 수신 데이터 쇼핑몰 체크 및 데이터 복호화
$settle_data = $paycoApi->shop_check($arr_data);
if($settle_data === false) {
	response_log('쇼핑몰 고유값이 다릅니다.');
}

// 수신데이터 iconv
$settle_data = arr_data_iconv($settle_data);

// 데이터 분할
$item_data = $settle_data['orderProducts'];
$delivery_data = $settle_data['deliveryPlace'];
$payment_data = $settle_data['paymentDetails'];

unset($settle_data['orderProducts'], $settle_data['deliveryPlace'], $settle_data['paymentDetails'], $settle_data['paymentAdmission']);
/*
	2015-01-14 $settle_data['paymentAdmission'] 는 페이코에서 데이터 전달시 삭제예정
*/

$order_data = $settle_data;

/*	$order_data Array
	sellerOrderReferenceKey			쇼핑몰 주문번호
	reserveOrderNo					페이코 주문예약 번호
	orderNo							페이코 주문번호
	memberName						구매자명
	memberEmail						구매자 이메일
	orderChannel					주문채널 (PC or MOBILE)
	totalOrderAmt					총 주문금액
	totalDeliveryFeeAmt				총 배송비
	totalRemoteAreaDeliveryFeeAmt	총 도서산간비
	totalPaymentAmt					총 결제금액
	serviceUrlParam					미사용
	paymentCompletionYn				결제완료여부 (Y or N)
	orderMethod						주문유형(CHECKOUT or EASYPAY_F or EASYPAY)
*/

/*	$item_data Array	2차배열
	orderProductNo					페이코 주문상품번호
	sellerOrderProductReferenceKey	쇼핑몰 item sno
	orderProductStatusCode			주문상품상태코드 (OPSPAED
	orderProductStatusName			주문상품상태명 (결제완료
	cpId							상점ID
	productId						상품ID
*/

/*	$delivery_data Array
	recipient				수취인명
	englishReceipent		
	address1				수취인 주소1
	address2				수취인 주소2
	zipcode					우편번호
	deliveryMemo			배송요청사항
	telephone				수취인 핸드폰번호
	individualCustomUniqNo	개인통관번호
*/

/*	$payment_data
	paymentTradeNo		결제번호
	paymentMethodCode	결제수단코드 (01)
	paymentMethodName	결제수단명 (신용카드)
	paymentAmt			결제금액
	tradeYmdt			결제일시 (20150113201530)
	pgAdmissionNo		PG승인번호 (20150113950567)
	pgAdmissionYmdt		PG승인일시 (20150113201530)
	easyPaymentYn		간편결제여부 (Y or N)

	결제수단에 따라 아래값이 추가됨

	신용카드 (통신시 실제 수신 데이터)
	cardSettleInfo	Array
		cardCompanyName				카드사명 (신한카드)
		cardNo						카드번호 (************6336)
		cardInstallmentMonthNumber	00

	휴대폰 (스펙문서상 표기되어 있는 값)
	cellphoneSettleInfo	Array
		companyName				통신사명
		celphoneNo				휴대폰번호

	실시간 계좌이체 (스펙문서상 표기되어 있는 값)
	realtimeAccountTransferSettleInfo	Array
		bankName				은행명
		bankCode				은행코드

	가상계좌 (스펙문서상 표기되어 있는 값)
	nonBankbookSettleInfo	Array
		bankName				은행명
		bankCode				은행코드
		accountNo				계좌번호
		paymentExpirationYmd	입금만료일

*/



$query = $db->_query_print('SELECT * FROM '.GD_ORDER.' WHERE ordno=[i]', $order_data['sellerOrderReferenceKey']);
$gd_order_data = $db->_select($query);

// gd_order 데이터 백업
$bankup_gd_order_data = $gd_order_data[0];

// 주문유효성 체크
if(count($gd_order_data) < 1) response_log('등록된 주문이 없습니다.');

// 적립금 사용 여부 및 유효성 체크 (true - 사용가능, false - 사용불가)
$useEmoney = false;
if ($order_data['orderMethod'] != 'CHECKOUT' && $bankup_gd_order_data['emoney'] > 0 && $bankup_gd_order_data['m_no']){
	$useEmoney = true;

	//가상계좌 입금확인은 미사용 및 체크X
	foreach($payment_data as $payment) {
		if($payment['paymentMethodCode'] == '02' && $order_data['paymentCompletionYn'] == 'Y') {
			$useEmoney = false;
			break;
		}
	}
}

if ($useEmoney === true){
	//적립금 유효성 체크
	if($payco->checkEmoney($bankup_gd_order_data) == false){
		response_log('사용할 적립금이 보유한 적립금보다 많습니다.');
	}
}

if($order_data['orderMethod'] == '') $order_data['orderMethod'] = strtoupper($bankup_gd_order_data['payco_settle_type']);

if($bankup_gd_order_data['step'] > 0) response_log('이미 입금확인된 주문입니다');

### 지역별 배송비
if($settle_data['totalRemoteAreaDeliveryFeeAmt'] > 0) $add_delivery_bool = true;
$total_area_delivery = 0;

### item별 유효성 체크
foreach($item_data as $item) {
	$item_query = $db->_query_print('SELECT * from '.GD_ORDER_ITEM.' WHERE sno=[i]', $item['sellerOrderProductReferenceKey']);
	$item_res = $db->_select($item_query);

	if(!empty($item_res)) {
		$order_item = $item_res[0];

		// gd_order_item 데이터 백업
		$backup_gd_order_item_data[] = $order_item;

		($order_data['orderChannel'] == 'PC') ? $isMobile = false : $isMobile = true;

		if(!$cfg) include "../../../conf/config.php";

		/*
		 * 결제완료인 경우
		 * A. 재고삭감단계가 [주문접수시]인 경우 주문가능여부 체크 안함
		 * B. 재고삭감간계가 [입금확인시]인 경우 조건부 주문가능여부 체크 진행
		 *  B-1. 가상계좌 주문인 경우 주문가능여부 체크 안함(가상계좌 + 페이코 포인트 포함)
		 *  B-2. 가상계좌가 아닌 주문인 경우 주문가능여부 체크 진행
		*/
		if($order_data['paymentCompletionYn'] === "Y") {
			if($cfg['stepStock'] === "0") $order_check_bool = false;//A.
			else {//B.
				//결제수단 확인
				foreach($payment_data as $tmp_payment) {
					if($tmp_payment['paymentMethodCode'] === "02") {//B-1.
						$order_check_bool = false;
						break;
					}
					else $order_check_bool = true;//B-2.
				}
			}
		}
		else $order_check_bool = false;


		// 주문가능여부 체크
		if($order_check_bool === true) {
			$order_check = $payco->check_paycoOrderAbleComplet($order_data['orderMethod'], $order_item['goodsno'], $isMobile);

			if($order_check !== false) {
				exit($order_check.' goodsno('.$order_item['goodsno'].')');
			}
		}

		// gd_order_item update 데이터 정의
		if($order_data['paymentCompletionYn'] == 'Y') {
			$tmp_order_item['sno'] = $item['sellerOrderProductReferenceKey'];
			$tmp_order_item['istep'] = '1';
			$tmp_order_item['cyn'] = 'y';
		}
		else {
			$tmp_order_item['sno'] = $item['sellerOrderProductReferenceKey'];
			$tmp_order_item['istep'] = '0';
		}
		$arr_gd_order_item[] = $tmp_order_item;
	}
	else {
		$total_area_delivery = $item['productPaymentAmt'] - $item['originalProductPaymentAmt'];
	}
}

// 총 지역별 배송비와 배송비item에 포함된 지역별 배송비 금액이 다른 경우 실패처리
if($settle_data['totalRemoteAreaDeliveryFeeAmt'] != $total_area_delivery) {
	response_log('총 지역별 배송비와 배송비ITEM에 포함된 지역별 배송비가 다릅니다.[total:'.$settle_data['totalRemoteAreaDeliveryFeeAmt'].'/item'.$total_area_delivery.']');
}

// 지역별 배송비가 있는 경우 결제금액 및 배송비에 포함
if($add_delivery_bool === true && $total_area_delivery > 0) {
	$arr_settle_log[] = $settle_data['sellerOrderReferenceKey'].' ('.date('Y-m-d H:i:s').')';
	$arr_settle_log[] = '-----------------------------------';
	$arr_settle_log[] = '지역별 배송비 추가 : '.number_format($total_area_delivery).'원';
	$arr_settle_log[] = '-----------------------------------';
	$arr_settle_log[] = '';

	if($order_data['totalPaymentAmt'] != $bankup_gd_order_data['settleprice']) {
		$add_query = $db->_query_print('UPDATE '.GD_ORDER.' SET settleprice=(settleprice + [i]), prn_settleprice=(prn_settleprice + [i]), delivery=(delivery + [i]) WHERE ordno=[i]', $total_area_delivery, $total_area_delivery, $total_area_delivery, $order_data['sellerOrderReferenceKey']);
		$rtn = $db->_query($add_query);
	}
}

// 가격변조체크
if(forge_order_check($order_data['sellerOrderReferenceKey'], $order_data['totalPaymentAmt']) !== true) response_log('결제금액이 잘못되었습니다. order_price('.$bankup_gd_order_data['settleprice'].') and settle_price('.$order_data['totalPaymentAmt'].')');


// 배송지정보 세팅
if($order_data['orderMethod'] == 'CHECKOUT') {
	/* 주석항목은 데이터 없음 */
	$gd_order_ins['nameOrder'] = $order_data['memberName'];	//주문자명
	$gd_order_ins['email'] = $order_data['memberEmail'];	//주문자 이메일
//	$gd_order_ins['phoneOrder'] = '';	//주문자 유선전화번호
//	$gd_order_ins['mobileOrder'] = '';	//주문자 휴대폰 번호

	$gd_order_ins['nameReceiver'] = $delivery_data['recipient'];	//추취자명
	$gd_order_ins['phoneReceiver'] = $delivery_data['telephone'];	//수취자 유선전화번호
	$gd_order_ins['mobileReceiver'] = $delivery_data['telephone'];	//수취자 휴대폰번호

	$gd_order_ins['zipcode'] = $delivery_data['zipcode'];	//우편번호
	$gd_order_ins['address'] = $delivery_data['address1'].' '.$delivery_data['address2'];	//배송지주소
//	$gd_order_ins['road_address'] = '';	//도로명주소
	### address1에 지번주소 또는 도로명주소가 전달되며 지번과 도로명을 구분하는 값은 없음

	$gd_order_ins['memo'] = $delivery_data['deliveryMemo'];	//배송요청사항
}
else {
	//간편결제시 gd_order_item update 데이터 정의
	if($order_data['paymentCompletionYn'] == 'Y') {
		$tmp_order_item['sno'] = $item['sellerOrderProductReferenceKey'];
		$tmp_order_item['istep'] = '1';
		$tmp_order_item['cyn'] = 'y';
	}
	else {
		$tmp_order_item['sno'] = $item['sellerOrderProductReferenceKey'];
		$tmp_order_item['istep'] = '0';
	}
	$arr_gd_order_item[] = $tmp_order_item;
}

$arr_settle_log[] = '-----------------------------------';
$arr_settle_log[] = '페이코 주문번호 : '.$order_data['orderNo'];
$arr_settle_log[] = '-----------------------------------';
$arr_settle_log[] = '';

foreach($payment_data as $payment) {
	$arr_settle_log[] = $order_data['sellerOrderReferenceKey'].' ('.date('Y-m-d H:i:s').')';
	$arr_settle_log[] = '-----------------------------------';
	$arr_settle_log[] = '결제번호 : '.$payment['paymentTradeNo'];
	$arr_settle_log[] = '결제수단코드 : '.$payment['paymentMethodCode'];
	$arr_settle_log[] = '결제수단명 : '.$payment['paymentMethodName'];
	$arr_settle_log[] = '결제금액 : '.$payment['paymentAmt'];
	$arr_settle_log[] = '결제일시 : '.$payment['tradeYmdt'];
	$arr_settle_log[] = 'PG승인번호 : '.$payment['pgAdmissionNo'];
	$arr_settle_log[] = 'PG승인일시 : '.$payment['pgAdmissionYmdt'];
	$arr_settle_log[] = '간편결제여부 : '.$payment['easyPaymentYn'];
	$arr_settle_log[] = '-------------상세로그--------------';

	$cdt_ymd[] = substr($payment['tradeYmdt'], 0, 4);
	$cdt_ymd[] = substr($payment['tradeYmdt'], 4, 2);
	$cdt_ymd[] = substr($payment['tradeYmdt'], 6, 2);
	$cdt_time[] = substr($payment['tradeYmdt'], 8, 2);
	$cdt_time[] = substr($payment['tradeYmdt'], 10, 2);
	$cdt_time[] = substr($payment['tradeYmdt'], 12, 2);

	$gd_order_ins['cdt'] = implode('-', $cdt_ymd).' '.implode(':', $cdt_time);
	unset($cdt_ymd, $cdt_time);


/*
	쇼핑몰 결제수단 코드
		a	무통장
		c	신용카드
		o	계좌이체
		v	가상계좌
		d	전액할인
		h	핸드폰
		p	포인트
		u	신용카드 (중국)
		y	옐로페이

		e	페이코 포인트

	페이코 결제수단 코드
		01	신용카드
		02	가상계좌
		04	실시간 계좌이체
		05	휴대폰
		31	신용카드 간편결제
		60	휴대폰 간편결제
		98	페이코 포인트
*/
	### 결제수단별 추가정보 등록
	switch($payment['paymentMethodCode']) {
		case '01' ://신용카드
		case '31' ://신용카드 간편결제
			$settle_method_data = $payment['cardSettleInfo'];
			$arr_settle_log[] = '카드사명 : '.$settle_method_data['cardCompanyName'];
			$arr_settle_log[] = '카드사코드 : '.$settle_method_data['cardCompanyCode'];
			$arr_settle_log[] = '카드번호 : '.$settle_method_data['cardNo'];
			$arr_settle_log[] = '할부개월 : '.$settle_method_data['cardInstallmentMonthNumber'];
			$arr_settle_log[] = '카드사 승인번호 : '.$settle_method_data['cardAdmissionNo'];

			$gd_order_ins['settlekind'] = 'c';
			break;
		
		case '02' ://가상계좌
			$settle_method_data = $payment['nonBankbookSettleInfo'];
			$arr_settle_log[] = '은행명 : '.$settle_method_data['bankName'];
			$arr_settle_log[] = '은행코드 : '.$settle_method_data['bankCode'];
			$arr_settle_log[] = '계좌번호 : '.$settle_method_data['accountNo'];
			$arr_settle_log[] = '입금만료일 : '.substr($settle_method_data['paymentExpirationYmd'], 0, 4).'-'.substr($settle_method_data['paymentExpirationYmd'], 4,2).'-'.substr($settle_method_data['paymentExpirationYmd'], 6,2);

			$gd_order_ins['settlekind'] = 'v';
			$gd_order_ins['vAccount'] = $settle_method_data['bankName'].' '.$settle_method_data['accountNo'];
			$gd_order_ins['vAccount'] .= ' '.substr($settle_method_data['paymentExpirationYmd'], 0, 4).'-'.substr($settle_method_data['paymentExpirationYmd'], 4,2).'-'.substr($settle_method_data['paymentExpirationYmd'], 6,2);
			break;

		case '35' ://바로이체
			$settle_method_data = $payment['realtimeAccountTransferSettleInfo'];
			$arr_settle_log[] = '은행명 : '.$settle_method_data['bankName'];
			$arr_settle_log[] = '은행코드 : '.$settle_method_data['bankCode'];

			$gd_order_ins['settlekind'] = 'o';
			break;

		case '05' ://휴대폰
		case '60' ://휴대폰 간편결제
			$settle_method_data = $payment['cellphoneSettleInfo'];
			$arr_settle_log[] = '통신사명 : '.$settle_method_data['companyName'];
			$arr_settle_log[] = '휴대폰번호 : '.$settle_method_data['cellphoneNo'];

			$gd_order_ins['settlekind'] = 'h';
			break;

		case '98' ://페이코 포인트
			if(!isset($gd_order_ins['settlekind'])) $gd_order_ins['settlekind'] = 'e';
			$gd_order_ins['payco_use_point'] = $payment['paymentAmt'];//페이코 포인트 사용 금액

			$arr_settle_log[] = '사용한 페이코 포인트 : '.$payment['paymentAmt'];

			// 다른 결제수단과 같이 사용하는 경우 해당 결제수단으로 정보저장하기 위함
			if(empty($gd_order_ins['settlekind'])) $gd_order_ins['settlekind'] = 'e';
			break;
		case '75' ://페이코 쿠폰(자유이용쿠폰)
		case '76' ://카드 쿠폰
		case '77' ://가맹점 쿠폰
			$gd_order_ins['payco_coupon_use_yn'] = 'Y';
			$gd_order_ins['payco_coupon_price'] = $payment['paymentAmt'];
			$arr_settle_log[] = '사용한 쿠폰금액 : '.$payment['paymentAmt'];
			break;
		default :
			break;
	}
	$arr_settle_log[] = '결제완료여부 : '.$order_data['paymentCompletionYn'];
	$arr_settle_log[] = '-----------------------------------';
}

if($order_data['paymentCompletionYn'] == 'Y') {
	$gd_order_ins['cyn'] = 'y';
	$gd_order_ins['step'] = '1';
	$gd_order_ins['step2'] = '0';
}
else {
	$gd_order_ins['step'] = '0';
	$gd_order_ins['step2'] = '0';
}

$gd_order_ins['settlelog'] = implode("\n", $arr_settle_log);
$gd_order_ins['sync_'] = '0';
$gd_order_ins['payco_order_no'] = $order_data['orderNo'];//페이코 주문번호

// 결제정보 저장 및 주문상태 변경
if(empty($gd_order_ins) === false && empty($arr_gd_order_item) === false) {
	$order_query = $db->_query_print('UPDATE '.GD_ORDER.' SET [cv] WHERE ordno=[i]', $gd_order_ins, $order_data['sellerOrderReferenceKey']);

	$order_rtn = $db->_query($order_query);
	if(!$order_rtn) {
		response_log('결제정보 DB저장 실패');
	}

	foreach($arr_gd_order_item as $gd_order_item) {
		if($gd_order_item['sno'] == 'item_'.$order_data['sellerOrderReferenceKey']) {
			$query = $db->_query_print('SELECT sno FROM '.GD_ORDER_ITEM.' WHERE ordno=[i]', $order_data['sellerOrderReferenceKey']);
			$arr_items = $db->_select($query);
		}
		else {
			$arr_items = $arr_gd_order_item;
		}

		foreach($arr_items as $_item) {
			$upd_item['istep'] = $gd_order_item['istep'];
			if(isset($gd_order_item['cyn'])) $upd_item['cyn'] = $gd_order_item['cyn'];

			$item_query = $db->_query_print('UPDATE '.GD_ORDER_ITEM.' SET [cv] WHERE sno=[i]', $upd_item, $_item['sno']);
			$item_rtn = $db->_query($item_query);

			if(!$item_rtn) {
				if(order_data_restoration()) response_log('결제정보 DB저장 실패');
				else response_log('결제정보 DB저장 실패 (데이터 복원실패)');
			}
		}
	}
}
else {
	response_log('결제정보 저장데이터 구성 실패');
}

$query = "
SELECT * from
	".GD_ORDER." a
	LEFT JOIN ".GD_LIST_BANK." b on a.bankAccount = b.sno
WHERE
	a.ordno='".$order_data['sellerOrderReferenceKey']."'
";
$data = $db->fetch($query);

// 상품재고 차감
$payco->adjustStock($order_data['sellerOrderReferenceKey']);

// 주문로그 저장
if($gd_order_ins['step'] > 0) orderLog($order_data['sellerOrderReferenceKey'], $r_step[$bankup_gd_order_data['step']]." > ".$r_step[$gd_order_ins['step']]);

// 상품구입시 적립금 사용
if ($useEmoney === true){
	setEmoney($data[m_no],-$data[emoney],"상품구입시 적립금 결제 사용", $order_data['sellerOrderReferenceKey']);
}

### 주문확인메일
if(function_exists('getMailOrderData')) {
	sendMailCase($order_data['memberEmail'],0,getMailOrderData($order_data['sellerOrderReferenceKey']));
}

// SMS 변수 설정
$dataSms = $data;
if ($gd_order_ins['settlekind'] != "v") {
	sendMailCase($data[email],1,$data);			### 입금확인메일
	sendSmsCase('incash',$data[mobileOrder]);	### 입금확인SMS
} else {
	sendSmsCase('order',$data[mobileOrder]);	### 주문확인SMS
}


// 수신결과 로그/출력
response_log('ok');
?>