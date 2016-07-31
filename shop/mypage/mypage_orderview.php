<?

include "../_header.php";

if (!$sess && !$_COOKIE[guest_ordno]) go("../member/login.php?returnUrl=$_SERVER[PHP_SELF]");

@include dirname(__FILE__)."/../conf/pg.cashbag.php";
@include dirname(__FILE__)."/../conf/auctionIpay.cfg.php";

if(!is_object($order)){
	$order = Core::loader('order');
	$order->load($_GET['ordno']);
}
if(!is_object($mypage_paymentDetails)) $mypage_paymentDetails = Core::loader('mypage_paymentDetails', $_GET['ordno']);
if(!is_object($cashreceipt)) $cashreceipt = new cashreceipt();
$prnSettleEtcMsg = '';

$r_exc = $r_kind = array();
$cashbagprice = 0;
if($cashbag['paykind'])$r_kind = unserialize($cashbag['paykind']);
if($cashbag['e_refer'])$r_exc = unserialize($cashbag['e_refer']);

$query = "
SELECT
	a.*, b.*, c.*,
	d.cp_num,
	d.cp_publish,
	d.cp_sms_cnt,
	d.cp_ea,
	TG.usestartdt, TG.useenddt
FROM
	".GD_ORDER." as a
	LEFT JOIN ".GD_ORDER_ITEM." AS OI ON a.ordno = OI.ordno
	LEFT JOIN ".GD_TODAYSHOP_GOODS." AS TG ON TG.goodsno = OI.goodsno
	left join ".GD_LIST_BANK." as b ON a.bankAccount=b.sno
	left join ".GD_LIST_DELIVERY." as c ON a.deliveryno=c.deliveryno
	LEFT JOIN ".GD_TODAYSHOP_ORDER_COUPON." as d on a.ordno = d.ordno
where a.ordno = '$_GET[ordno]'
";
$data = $db->fetch($query,1);

if (!$data) {
	### 네이버 체크아웃 주문서 검색
	$query = "
	SELECT
		O.*, PO.*,

		D.DeliveryStatus, D.DeliveryMethod, D.DeliveryCompany, D.TrackingNumber,
		D.SendDate, D.PickupDate, D.DeliveredDate, D.IsWrongTrackingNumber, D.WrongTrackingNumberRegisteredDate,
		D.WrongTrackingNumberType
	FROM
		".GD_NAVERCHECKOUT_ORDERINFO." AS O

		INNER JOIN ".GD_NAVERCHECKOUT_PRODUCTORDERINFO." AS PO
			ON PO.OrderID = O.OrderID

		LEFT JOIN ".GD_MEMBER." AS MB
			ON PO.MallMemberID=MB.m_id

		LEFT JOIN ".GD_NAVERCHECKOUT_DELIVERYINFO." AS D
			ON PO.ProductOrderID = D.ProductOrderID

		LEFT JOIN ".GD_NAVERCHECKOUT_CANCELINFO." AS C
			ON PO.ProductOrderID = C.ProductOrderID

		LEFT JOIN ".GD_NAVERCHECKOUT_RETURNINFO." AS R
			ON PO.ProductOrderID = R.ProductOrderID

		LEFT JOIN ".GD_NAVERCHECKOUT_EXCHANGEINFO." AS E
			ON PO.ProductOrderID = E.ProductOrderID

		LEFT JOIN ".GD_NAVERCHECKOUT_DECISIONHOLDBACKINFO." AS DH
			ON PO.ProductOrderID = DH.ProductOrderID

	WHERE O.OrderID = '".$_GET['ordno']."'

	GROUP BY O.OrderID
	";
	$data = $db->fetch($query,1);
	if($data) {
		$orderType = "naverCheckout";
		list($data['m_no']) = $db->fetch("SELECT m_no FROM ".GD_MEMBER." WHERE m_id = '".$data['MallMemberID']."'");
		$data['ordno'] = $data['OrderID']; // 주문번호
		$data['nameOrder'] = $data['OrdererName']; // 주문자명
		$data['phoneOrder'] = $data['OrdererTel1']; // 주문자 전화번호
		$data['mobileOrder'] = $data['OrdererTel2']; // 주문자 휴대전화
		$data['email'] = ''; // 주문자 이메일
		$data['step'] = 0; // 하단에 영수증 신청 부분을 가리기 위해 0으로 선언
		$data['nameReceiver'] = $data['ShippingAddressName']; // 수령자명
		$data['phoneReceiver'] = $data['ShippingAddressTel1']; // 수령자 전화번호
		$data['mobileReceiver'] = $data['ShippingAddressTel2']; // 수령자 휴대전화
		$data['zipcode'] = $data['ShippingAddressZipCode']; // 수령지 우편번호
		$data['address'] = $data['ShippingAddressBaseAddress']; // 수령지 주소
		$data['address_sub'] = $data['ShippingAddressDetailedAddress']; // 수령지 세부주소
		$data['memo'] = $data['ShippingMemo']; // 배송메세지
		if($data['TrackingNumber']) { // 배송정보

			$_checkout_delivery_company = array(
				'KOREX' => '대한통운','CJGLS' => 'CJGLS','SAGAWA' => 'SC 로지스(사가와익스프레스택배)','YELLOW' => '옐로우캡','KGB' => '로젠택배','DONGBU' => '동부익스프레스택배','EPOST' => '우체국택배',
				'REGISTPOST' => '우편등기','HANJIN' => '한진택배','HYUNDAI' => '현대택배','KGBLS' => 'KGB 택배','HANARO' => '하나로택배','INNOGIS' => '이노지스택배','DAESIN' => '대신택배',
				'ILYANG' => '일양로지스','KDEXP' => '경동택배','CHUNIL' => '천일택배','CH1' => '기타 택배',
			);

			$data['deliverycomp'] = $_checkout_delivery_company[$data['DeliveryCompany']]; // 배송사
			if($data['DeliveryMethod'] != "DELIVERY" && $data['DeliveryMethod'] != "GDFW_ISSUE_SVC") $data['deliverycode'] = "(기타배송)"; // 기타 배송일 경우 송장번호가 없음
			else $data['deliverycode'] = $data['TrackingNumber']; // 송장번호
		}
		// 금액정보는 하단에서 처리
	}

	if (!$data) msg("해당 주문이 존재하지 않습니다",-1);
}
else {
	$goodsprice = $mypage_paymentDetails->getGoodsPrice(); //총 주문금액
	$emoney = $mypage_paymentDetails->getUseEmoney(); //적립금 사용
	$prnSettlePrice = $mypage_paymentDetails->getRealPrnSettlePrice(); //결제금액 - 취소완료된 결제금액
}

### 권한 체크
if ($sess[m_no]){
	if ($data[m_no]!=$sess[m_no]) msg("접근권한이 없습니다",-1);
} else {
	if ($data[nameOrder]!=$_COOKIE[guest_nameOrder] || $data[m_no]) msg("접근권한이 없습니다",-1);
}

$query = "
select count(*) from
	".GD_ORDER_ITEM."
where
	ordno = '$_GET[ordno]' and istep < 40
	";
list($icnt) = $db->fetch($query);

$isSocial = false;	// 쿠폰 상품인지 체크키 위한 변수

$query = "
SELECT
	b.*, a.*,
	C.goodstype,

	IF (C.processtype = 'i',
	4,
		IF (
			NOW() < C.startdt,
			1,	/* 판매대기 */
			IF (
				(NOW() <= C.enddt OR C.enddt IS NULL) AND b.runout = 0,
				2,	/* 판매중 */
				IF (
					C.fakestock2real = 1,
						IF (C.limit_ea <> 0 AND (C.buyercnt + C.fakestock) < C.limit_ea,
						3,	/* 판매실패 */
						4	/* 판매완료 = 판매종료 */
						)
						,
						IF (C.limit_ea <> 0 AND C.buyercnt < C.limit_ea,
						3,	/* 판매실패 */
						4	/* 판매완료 = 판매종료 */
						)
				)
			)
		)
	) AS stats

FROM ".GD_ORDER_ITEM." AS a
INNER join ".GD_GOODS." AS b on a.goodsno=b.goodsno
LEFT JOIN ".GD_TODAYSHOP_GOODS." AS C ON a.goodsno = C.goodsno
where
	a.ordno = '$_GET[ordno]'
";
$res = $db->query($query);
while ($sub=$db->fetch($res)){

	if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
		$sub = validation::xssCleanArray($sub, array(
			validation::DEFAULT_KEY	=> 'text'
		));
	}

	$item[] = $sub;

	if(substr($sub[coupon_emoney],-1) == '%') $sub[coupon_emoney] = getDcprice($sub[price],$sub[coupon_emoney]);

	if($icnt == 0){ //모든 주문상품이 취소,환불일 경우
		$coupon_emoney += $sub[coupon_emoney] * $sub[ea];

	}else if ($sub[istep]<40){
		$coupon_emoney += $sub[coupon_emoney] * $sub[ea];

		if( in_array($sub['goodsno'],$r_exc) ) $minus += $sub[price] * $sub[ea];
		$cashbagprice += $sub[price] * $sub[ea];
	}
	/**
		2011-01-27 by x-ta-c
		구매 상품중 쿠폰상품(티켓)이 존재하는지 체크 (실제로 티켓을 구매했다면 레코드는 1개임)
	*/
	if ($sub['goodstype']) {
		$isSocial = true;		// 상세보기 템플릿 치환
		$goodstype = $sub['goodstype'];
		$socialStatus = $sub[stats];
	}
}
$cashbagprice -= $minus;

// 체크아웃일 경우 상품 데이터 재설정
if($orderType == "naverCheckout") {
	$query = "
	SELECT COUNT(*) FROM
		".GD_NAVERCHECKOUT_PRODUCTORDERINFO."
	WHERE
		OrderID  = '".$_GET['ordno']."' and (ClaimType IS NULL OR ClaimType = '')
		";
	list($icnt) = $db->fetch($query); // 취소하지 않은 상품

	$query = "
	SELECT
		a.*, b.*
	FROM ".GD_NAVERCHECKOUT_PRODUCTORDERINFO." AS a
	LEFT join ".GD_GOODS." AS b on a.ProductID=b.goodsno
	where
		a.OrderID = '".$_GET['ordno']."'
	";
	$res = $db->query($query);
	while ($sub=$db->fetch($res)){

		if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
			$sub = validation::xssCleanArray($sub, array(
				validation::DEFAULT_KEY	=> 'text'
			));
		}

		// 결제, 할인 금액
		$data['TotalOrderAmount'] = (int)$data['TotalOrderAmount'] + (int)$sub['UnitPrice'] * (int)$sub['Quantity'];
		$data['ProductDiscountAmount'] = (int)$data['ProductDiscountAmount'] + (int)$sub['ProductDiscountAmount'];

		// 옵션 추출
			$tmpOption = explode('/', $sub['ProductOption']);
			$tmp = explode(':', $tmpOption[0]); $sub['opt1'] = $tmp[1];
			$tmp = explode(':', $tmpOption[1]); $sub['opt2'] = $tmp[1];

		$sub['price'] = $sub['UnitPrice']; // 가격
		$sub['ea'] = $sub['Quantity']; // 수량
		$sub['istep'] = getCheckoutOrderStatus($sub);
		$r_istep[$sub['istep']] = $sub['istep']; // 존재하지 않는 주문상태일 경우 새로 정의

		$item[] = $sub;

	}

	// 네이버 체크아웃 결제정보
	$goodsprice = $data['TotalOrderAmount']; // 총 주문 금액
	$emoney = $data['ProductDiscountAmount']; // 적립금 사용
	$prnSettlePrice = $data['TotalOrderAmount'] - $data['ProductDiscountAmount']; // 결제 금액

	switch($data['PaymentMeans']) {
		case "신용카드" : $data['settlekind'] = "c"; break;
		case "무통장입금" : $data['settlekind'] = "v"; $data['vAccount'] = "계좌정보는 <a href=\"http://checkout.naver.com/customer/orderList.nhn\" target=\"_blank\" style=\"text-decoration:underline;\">[네이버 체크아웃 MY페이지]</a>에서 확인해주세요."; break;
	}
}

include "../conf/config.pay.php";
@include "../conf/pg.$cfg[settlePg].php";
include "../conf/pg.escrow.php";
@include "../conf/egg.usafe.php";

// 투데이샵 주문인경우 경우 PG 설정 교체
if ($isSocial && function_exists('resetPaymentGateway')) resetPaymentGateway(true);

if($set['delivery']['deliverynm'] == '')$set['delivery']['deliverynm'] = '기본배송';

if($data[step2] == '50' || $data[step2] == '54'){

	$r_deli = explode('|',$set['r_delivery']['title']);

}

### 현금영수증
$cashReceipt = array();
$cashReceipt = $cashreceipt->getCashReceiptCalCulate($_GET['ordno']);
$r_type = array(
		"a"	=> "NBANK",
		"o"	=> "ABANK",
		"v"	=> "VBANK",
		);
$cashReceipt['type'] = $r_type[$data['settlekind']];

if($data['settleInflow'] == 'payco'){
	$pg['receipt'] = 'N';
}
else {
	if ($data['cashreceipt_ectway'] == 'Y')
	{
		$data['cashreceipt'] = '-';
		$tpl->define('cash_receipt',"proc/_cashreceipt.htm");
	}
	else if ($set['receipt']['publisher'] != 'seller'){
		$query = "select certno from gd_cashreceipt where ordno='$_GET[ordno]' order by crno desc limit 0,1";
		$cash = $db->fetch($query,1);
		if( $data[settlekind] != 'o' ){
			$tpl->assign('certno',$cash[certno]);
		}else{
			$tpl->assign('certno',$data[mobileOrder]);
		}
		$tpl->define('cash_receipt',"order/cash_receipt/{$cfg[settlePg]}.htm");
	}
	else if ($set['receipt']['publisher'] == 'seller'){
		if (is_object($cashreceipt))
		{
			$cashreceipt->prnUserReceipt($_GET['ordno']);
			$tpl->assign('receipt',$cashreceipt);
		}
		$tpl->define('cash_receipt',"proc/_cashreceipt.htm");
	}
}

### 세금계산서
if ( $set[tax][useyn] == 'y' ){
	$tmp = 0;
	if ( $set[tax][ "use_{$data[settlekind]}" ] == 'on' ) $tmp++;
	if ( in_array($set[tax][step], array('1', '2', '3', '4')) && $data[step] >= $set[tax][step] && !$data[step2] ) $tmp++;
	list($cnt) = $db->fetch("select count(*) from ".GD_ORDER_ITEM." where ordno='$_GET[ordno]' and tax='1'");
	if ( $cnt >= 1 ) $tmp++;
	if ( $tmp == 3 ) $data[taxapp] = 'Y';
	if (is_object($cashreceipt) && $cashreceipt->writeable != 'true' && $set['receipt']['publisher'] == 'seller') $data['taxapp'] = 'N'; // 현금영수증 발행중이면 세금계산서 신청불가
}

$data[taxmode] = '';
$query = "select name, company, service, item, busino, address, regdt, agreedt, printdt, price, step, doc_number from ".GD_TAX." where ordno='$_GET[ordno]' order by sno desc limit 1";
$res = $db->query($query);
if ( $db->count_($res) ){
	$data[taxmode] = 'taxview';
	$taxed = $db->fetch($res);
}
else if ( $data[taxapp] == 'Y' ) $data[taxmode] = 'taxapp';

if($data[phoneReceiver])$data['phone'] = explode('-',$data['phoneReceiver']);
if($data[mobileReceiver])$data['mobile'] = explode('-',$data['mobileReceiver']);
if($data[zipcode])$data['postcode'] = explode('-',$data['zipcode']);

if($cfg[settlePg]){
	$tmp = preg_split("/[\n]+/", $data[settlelog]);
	foreach($tmp as $v)if(preg_match('/KCP 거래번호 : /',$v))$data[tno] = str_replace('KCP 거래번호 : ','',$v);

	if($cfg['settlePg'] == 'allat' && preg_match('/거래번호 : (.*)/', $data['settlelog'], $matched)){
		$data['tno'] = $matched[1];
	}
}

/*if($data['step2'] == 50 || $data['step2'] == 54)*/ $resettleAble = true;
/*
if($cfg['autoCancelFail'] > 0){
	$ltm = toTimeStamp($data['orddt']) + 3600 * $cfg['autoCancelFail'] ;
	if($ltm < time()){
		$resettleAble = false;
	}
}else{
	$resettleAble = false;
}
*/

### PG 결제실패사유
if($data['step'] == 0 && $data['step2'] == 54 && $data['settlelog']){
	if(preg_match('/결과내용 : (.*)\n/',$data['settlelog'], $matched)){
		$data['pgfailreason'] = $matched[1];
	}
}

### 캐쉬백 적립
if(
	$cashbag['use'] == "on" &&
	$cashbag['code'] != null &&
	$data['cbyn'] == 'N' &&
	$data['step'] == '4' &&
	$data['step2'] == '0' &&
	in_array($data['settlekind'],$r_kind) &&
	$cashbagprice > 0

) $ableCashbag = 1;

$r_savetype = array(
	'ord' => 'orddt',
	'inc' => 'cdt',
	'deli' => 'ddt'
);
$r_savepriod = array(
	'mon' => 'month',
	'day' => 'day'
);
if($cashbag['savetype'] && $cashbag['savepriodtype'] && $cashbag['savepriod'] && $ableCashbag){
	$tmp = $data[$r_savetype[$cashbag['savetype']]];
	$tmp = strtotime($tmp);
	$tmp = strtotime("+".$cashbag['savepriod']." ".$r_savepriod[$cashbag['savepriodtype']],$tmp);
	if($tmp < time()){
		$ableCashbag = 0;
	}
}
if( $data[orddt] && $cashbag[savedt] && $ableCashbag ){
	if( $cashbag[savedt] > str_replace('-','',substr($data[orddt],0,10)) ){
		$ableCashbag = 0;
	}
}

if($data[cbyn] == 'Y'){
	$query = "select tno, add_pnt, pnt_app_time from " . GD_ORDER_OKCASHBAG . " where ordno='".$_GET['ordno']."' limit 1";
	list($data[oktno], $data[add_pnt], $data[pnt_app_time]) = $db->fetch($query);
}
$authdata = md5($pg[id].$data[cardtno].$pg[mertkey]); // dacom 다이렉트 매출전표 출력 인증문자열 생성

/**
	본 상품이 쿠폰이면 출력 템플릿 교체
*/
if ($isSocial === true && (is_file($tpl->template_dir.'/'.'mypage/mypage_orderview_social.htm'))) {


	$tpl->define('tpl','mypage/mypage_orderview_social.htm');

	// 값
	$tpl->assign('goodstype', $goodstype); // 상품유형

	$tpl->assign('socialStatus',$socialStatus);	// ()

	$tpl->assign('couponNumber',$data[cp_num]);	// 쿠폰번호
	$tpl->assign('couponEA',$data[cp_ea]);	// 쿠폰장수

	$tpl->assign('couponUseStartDate',$data[usestartdt]);	// 유효기간  시작
	$tpl->assign('couponUseEndDate',$data[useenddt]);	// 유효기간 끝

}
//

//마이페이지 결제금액 재설정
$data['emoney'] = $emoney;
$data['prn_settleprice'] = $prnSettlePrice;
$data['goodsprice'] = $goodsprice; //상품금액
$data['diffPrice'] = $mypage_paymentDetails->getDiffPrice(); //상품조정금액
$data['delivery'] = $mypage_paymentDetails->getDelivery(); //배송비
list($data['goodsDc'], $data['memberdc'], $data['coupon'], $data['enuri']) = $mypage_paymentDetails->getDiscount(); //상품할인 회원할인 쿠폰할인, 에누리
list($data['canceled_price'], $data['canceling_price'], $data['canceling_RealPrnSettlePrice']) = $mypage_paymentDetails->getCancelMultiPrice(); //취소금액, 취소접수금액, 취소시 결제금액
if($order->getRefundedFeeAmount() > 0) $prnSettleEtcMsg .= '(환불수수료 : '.number_format($order->getRefundedFeeAmount()).'원)';

if($data['settleInflow'] == 'payco'){
	$resettleAble = false;
	$payco = Core::loader('payco');
	$data['paycoSettleType'] = $payco->getPaycoSettleType($data['payco_settle_type']); //페이코 결제타입 한글명 반환
}

if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
	$data	= validation::xssCleanArray($data,	array( validation::DEFAULT_KEY	=> 'text' ));
	$taxed	= validation::xssCleanArray($taxed, array( validation::DEFAULT_KEY	=> 'text' ));
}

$tpl->assign('authdata',$authdata);  // dacom 다이렉트 매출전표 출력 인증문자열 생성
$tpl->assign($data);
$tpl->assign('item',$item);
$tpl->assign('taxed',$taxed);
$tpl->assign('ipay',$auctionIpayCfg);
$tpl->print_('tpl');

?>