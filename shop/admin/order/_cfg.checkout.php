<?
/*
 * 네이버 체크아웃 API 4.0 설정
 */

$cfg['orderPeriod'] = $cfg['orderPeriod'] ? $cfg['orderPeriod'] : 30;

$__navercheckout_message_schema = array();

// 결제수단
$__navercheckout_message_schema['payMeansClassType'] = array(
	'신용카드' => '신용카드',
	'실시간계좌이체' => '실시간계좌이체',
	'무통장입금' => '무통장입금',
	'휴대폰' => '휴대폰',
	'포인트결제' => '포인트결제',
	'네이버 캐쉬' => '네이버 캐쉬',
	'신용카드 간편결제' => '신용카드 간편결제',
	'휴대폰 간편결제' => '휴대폰 간편결제',
	'계좌 간편결제' => '계좌 간편결제'
);

// 취소/반품 사유 코드
$__navercheckout_message_schema['claimRequestReasonType'] = array(
	'INTENT_CHANGED' => '구매 의사 취소',
	'COLOR_AND_SIZE' => '색상 및 사이즈 변경',
	'WRONG_ORDER' => '다른 상품 잘못 주문',
	'PRODUCT_UNSATISFIED' => '서비스 및 상품 불만족',	// 판매 취소시 사용 (반품에도 사용가능)
	'DELAYED_DELIVERY' => '배송 지연',					// 판매 취소시 사용 (반품에도 사용가능)
	'SOLD_OUT' => '상품 품절',							// 판매 취소시 사용 (반품에도 사용가능)
	'DROPPED_DELIVERY' => '배송 누락',
	'BROKEN' => '상품 파손',
	'INCORRECT_INFO' => '상품 정보 상이',
	'WRONG_DELIVERY' => '오배송',
	'WRONG_OPTION' => '색상 등 옵션이 다른 상품 잘못 배송',
	'ETC' => '기타',
);

// 배송방법 코드
$__navercheckout_message_schema['deliveryMethodType'] = array(
	/* 원배송/재배송 */
	'DELIVERY' => '일반 택배',
	'GDFW_ISSUE_SVC' => '굿스플로 송장 출력',
	'VISIT_RECEIPT' => '방문 수령',
	'DIRECT_DELIVERY' => '직접 전달',
	'QUICK_SVC' => '퀵서비스',
	'NOTHING' => '배송 없음',

	/* 반송일때만 */
	'RETURN_DESIGNATED' => '지정 반품 택배',
	'RETURN_DELIVERY' => '일반 반품 택배',
	'RETURN_INDIVIDUAL' => '직접 반송',
);

// 택배사 코드 (wdsl 상 code 타입 아님, 네이버측 확인 결과 계속 추가될 우려가 있어 string 으로 변경해두었다고 함)
$__navercheckout_message_schema['deliveryCompanyType'] = array(
	'KOREX' => '대한통운',
	'CJGLS' => 'CJ대한통운',
	'KOREXG' => 'CJ대한통운(국제택배)',
	'SAGAWA' => 'SC 로지스(사가와익스프레스택배)',
	'YELLOW' => '옐로우캡(종료)',
	'DHLDE' => 'DHL(독일)',
	'KGB' => '로젠택배',
	'DONGBU' => 'KG로지스',
	'EPOST' => '우체국택배',
	'REGISTPOST' => '우편등기',
	'HANJIN' => '한진택배',
	'HYUNDAI' => '현대택배',
	'KGBLS' => 'KGB 택배',
	'HANARO' => '하나로택배',
	'INNOGIS' => 'GTX로지스',
	'DAESIN' => '대신택배',
	'ILYANG' => '일양로지스',
	'KDEXP' => '경동택배',
	'CHUNIL' => '천일택배',
	'DHL' => 'DHL',
	'FEDEX' => 'FEDEX',
	'GSMNTON' => 'GSMNTON',
	'WARPEX' => 'WarpEx',
	'WIZWA' => 'WIZWA',
	'EMS' => 'EMS',
	'ACIEXPRESS' => 'ACI',
	'EZUSA' => 'EZUSA',
	'PANTOS' => '범한판토스',
	'SWGEXP' => '성원글로벌',
	'TNT' => 'TNT',
	'UPS' => 'UPS',
	'CVSNET' => '편의점택배',
	'HDEXP' => '합동택배',
	'CH1' => '기타 택배',
);
$__navercheckout_message_schema['selectDeliveryCompanyType'] = array(
	'CJGLS' => 'CJ대한통운',
	'KOREXG' => 'CJ대한통운(국제택배)',
	'DHLDE' => 'DHL(독일)',
	'KGB' => '로젠택배',
	'DONGBU' => 'KG로지스',
	'EPOST' => '우체국택배',
	'REGISTPOST' => '우편등기',
	'HANJIN' => '한진택배',
	'HYUNDAI' => '현대택배',
	'KGBLS' => 'KGB 택배',
	'INNOGIS' => 'GTX로지스',
	'DAESIN' => '대신택배',
	'ILYANG' => '일양로지스',
	'KDEXP' => '경동택배',
	'CHUNIL' => '천일택배',
	'DHL' => 'DHL',
	'FEDEX' => 'FEDEX',
	'GSMNTON' => 'GSMNTON',
	'WARPEX' => 'WarpEx',
	'WIZWA' => 'WIZWA',
	'EMS' => 'EMS',
	'ACIEXPRESS' => 'ACI',
	'EZUSA' => 'EZUSA',
	'PANTOS' => '범한판토스',
	'SWGEXP' => '성원글로벌',
	'TNT' => 'TNT',
	'UPS' => 'UPS',
	'CVSNET' => '편의점택배',
	'HDEXP' => '합동택배',
	'CH1' => '기타 택배',
);

$__navercheckout_message_schema['claimStatusType'] = array(
	'CANCEL_REQUEST' => '취소요청',
	'CANCELING' => '취소처리중',
	'CANCEL_DONE' => '취소처리완료',	// 이때, 상품 주문상태가 '취소' 로 변경됨
	'CANCEL_REJECT' => '취소철회',
	'RETURN_REQUEST' => '반품요청',
	'EXCHANGE_REQUEST' => '교환요청',
	'COLLECTING' => '수거처리중',
	'COLLECT_DONE' => '수거완료',
	'EXCHANGE_REDELIVERING' => '교환재배송중',
	'RETURN_DONE' => '반품완료',		// 이때, 상품 주문상태가 '반품' 로 변경됨
	'EXCHANGE_DONE' => '교환완료',		// 이때, 상품 주문상태가 '교환' 로 변경됨
	'RETURN_REJECT' => '반품철회',
	'EXCHANGE_REJECT' => '교환거부',
	'PURCHASE_DECISION_HOLDBACK' => '구매확정보류',
	'PURCHASE_DECISION_HOLDBACK_REDELIVERING' => '구매확정보류 재배송중',
	'PURCHASE_DECISION_REQUEST' => '구매확정요청',
	'PURCHASE_DECISION_HOLDBACK_RELEASE' => '구매확정보류해제',
	'ADMIN_CANCELING' => '직권취소중',
	'ADMIN_CANCEL_DONE' => '직권취소완료',	// 이때, 상품 주문상태가 '취소' 로 변경됨
);


$__navercheckout_message_schema['holdbackClassType'] = array(
	'RETURN_DELIVERYFEE' => 'RETURN_DELIVERYFEE',
	'EXTRAFEEE' => 'EXTRAFEEE',
	'RETURN_DELIVERYFEE_AND_EXTRAFEEE' => 'RETURN_DELIVERYFEE_AND_EXTRAFEEE',
	'RETURN_PRODUCT_NOT_DELIVERED' => 'RETURN_PRODUCT_NOT_DELIVERED',
	'ETC' => 'ETC',
	'EXCHANGE_DELIVERYFEE' => 'EXCHANGE_DELIVERYFEE',
	'EXCHANGE_EXTRAFEE' => 'EXCHANGE_EXTRAFEE',
	'EXCHANGE_PRODUCT_READY' => 'EXCHANGE_PRODUCT_READY',
	'EXCHANGE_PRODUCT_NOT_DELIVERED' => 'EXCHANGE_PRODUCT_NOT_DELIVERED',
	'SELLER_CONFIRM_NEED' => '판매자확인필요',
	'PURCHASER_CONFIRM_NEED' => '구매자확인필요',
	'SELLER_REMIT' => '판매자 직접 송금',
	'ETC2' => 'ETC2',
	'EXCHANGE_HOLDBACK' => '교환 구매확정보류',
);

$__navercheckout_message_schema['holdbackStatusType'] = array(
	'NOT_YET' => '미보류',
	'HOLDBACK' => '보류중',
	'RELEASED' => '보류해제',
);



$__navercheckout_message_schema['addressType'] = array(
	'DOMESTIC' => '국내',
	'FOREIGN' => '해외',
);

$__navercheckout_message_schema['claimType'] = array(
	'CANCEL' => '취소',
	'RETURN' => '반품',
	'EXCHANGE' => '교환',
	'PURCHASE_DECISION_HOLDBACK' => '구매확정보류',
	'ADMIN_CANCEL' => '직권 취소',
);

// 발송지연사유
$__navercheckout_message_schema['delayedDispatchReasonType'] = array(
	'PRODUCT_PREPARE' => '상품 준비 중',
	'CUSTOMER_REQUEST' => '고객 요청',
	'CUSTOM_BUILD' => '주문 제작',
	'RESERVED_DISPATCH' => '예약 발송',
	'ETC' => '기타',
);

$__navercheckout_message_schema['placeOrderStatusType'] = array(
	'NOT_YET' => '발주 미확인',
	'OK' => '발주 확인',
	'CANCEL' => '발주 확인해제',
);

$__navercheckout_message_schema['productOrderStatusType'] = array(
	'PAYMENT_WAITING' => '입금대기',
	'CANCELED_BY_NOPAYMENT' => '미입금취소',
	'PAYED' => '결제완료',
	'DELIVERING' => '배송중',
	'DELIVERED' => '배송완료',
	'PURCHASE_DECIDED' => '구매확정',
	'CANCELED' => '취소',
	'RETURNED' => '반품',
	'EXCHANGED' => '교환',
);



$__navercheckout_message_schema['productOrderChangeType'] = array(
	'PAY_WAITING' => '입금 대기',
	'PAYED' => '결제 완료',
	'DISPATCHED' => '발송 처리',
	'CANCEL_REQUESTED' => '취소 요청',
	'RETURN_REQUESTED' => '반품 요청',
	'EXCHANGE_REQUESTED' => '교환 요청',
	'HOLDBACK_REQUESTED' => '구매 확정 보류 요청',
	'CANCELED' => '취소',
	'RETURNED' => '반품',
	'EXCHANGED' => '교환',
	'PURCHASE_DECIDED' => '미입금취소'
);

$__navercheckout_message_schema['PurchaseReviewScore'] = array(
	'0' => '불만족',
	'1' => '보통',
	'2' => '만족',
);

// 이상 wdsl 스키마 설정

// 이하 클레임 테이블 필드네임 정의
$__navercheckout_message_schema['claimRETURN'] = array(
	'ClaimStatus' => array('name'=>'클레임 상태','schema'=>'claimStatusType'),
	'ClaimRequestDate' => '클레임 요청일',
	'RequestChannel' => '접수 채널',
	'ReturnReason' => array('name'=>'반품 사유','schema'=>'claimRequestReasonType'),
	'ReturnDetailedReason' => '반품 상세 사유',
	'HoldbackStatus' => array('name'=>'보류 상태','schema'=>'holdbackStatusType'),
	'HoldbackReason' => array('name'=>'보류 사유','schema'=>'claimStatusType'),
	'HoldbackDetailedReason' => '보류 상세 사유',
	'CollectAddressAddressType' => array('name'=>'수거지(from) 주소 구분(해외/국내)','schema'=>'addressType'),
	'CollectAddressZipCode' => '수거지(from) 우편번호',
	'CollectAddressBaseAddress' => '수거지(from) 기본 주소',
	'CollectAddressDetailedAddress' => '수거지(from) 상세 주소',
	'CollectAddressCity' => '수거지(from) 도시',
	'CollectAddressState' => '수거지(from) 주(state)',
	'CollectAddressCountry' => '수거지(from) 국가',
	'CollectAddressTel1' => '수거지(from) 연락처 1',
	'CollectAddressTel2' => '수거지(from) 연락처 2',
	'CollectAddressName' => '수거지(from) 이름',
	'ReturnReceiveAddressAddressType' => array('name'=>'수취지(to) 주소 구분(해외/국내)','schema'=>'addressType'),
	'ReturnReceiveAddressZipCode' => '수취지(to) 우편번호',
	'ReturnReceiveAddressBaseAddress' => '수취지(to) 기본 주소',
	'ReturnReceiveAddressDetailedAddress' => '수취지(to) 상세 주소',
	'ReturnReceiveAddressCity' => '수취지(to) 도시',
	'ReturnReceiveAddressState' => '수취지(to) 주(state)',
	'ReturnReceiveAddressCountry' => '수취지(to) 국가',
	'ReturnReceiveAddressTel1' => '수취지(to) 연락처 1',
	'ReturnReceiveAddressTel2' => '수취지(to) 연락처 2',
	'ReturnReceiveAddressName' => '수취지(to) 이름',
	'CollectStatus' => '수거 상태',
	'CollectDeliveryMethod' => array('name'=>'수거 방법','schema'=>'deliveryMethodType'),
	'CollectDeliveryCompany' => '수거 택배사',
	'CollectTrackingNumber' => '수거 송장 번호',
	'CollectCompletedDate' => '수거 완료일',
	'EtcFeeDemandAmount' => '기타 비용 청구액',
	'EtcFeePayMethod' => '기타 비용 결제 방법',
	'EtcFeePayMeans' => '기타 비용 결제 수단',
	'RefundStandbyStatus' => '환불 대기 상태',
	'RefundStandbyReason' => '환불 대기 사유',
	'RefundRequestDate' => '환불 요청일'
);

$__navercheckout_message_schema['claimCANCEL'] = array(
	'ClaimStatus' => array('name'=>'클레임 상태','schema'=>'claimStatusType'),
	'ClaimRequestDate' => '클레임 요청일',
	'RequestChannel' => '접수 채널',
	'CancelReason' => array('name'=>'취소 사유','schema'=>'claimRequestReasonType'),
	'CancelDetailedReason' => '취소 상세 사유',
	'CancelCompletedDate' => '취소 완료일',
	'CancelApprovalDate' => '취소 승인일',
	'HoldbackStatus' => array('name'=>'보류 상태','schema'=>'holdbackStatusType'),
	'HoldbackReason' => array('name'=>'보류 사유','schema'=>'claimStatusType'),
	'HoldbackDetailedReason' => '보류 상세 사유',
	'EtcFeeDemandAmount' => '기타 비용 청구액',
	'EtcFeePayMethod' =>'기타 비용 결제 방법',
	'EtcFeePayMeans' => '기타 비용 결제 수단',
	'RefundStandbyStatus' => '환불 대기 상태',
	'RefundStandbyReason' => '환불 대기 사유',
	'RefundRequestDate' => '환불 요청일'
);

$__navercheckout_message_schema['claimADMIN_CANCEL'] = array(
	'ClaimStatus' => array('name'=>'클레임 상태','schema'=>'claimStatusType'),
	'ClaimRequestDate' => '클레임 요청일',
	'RequestChannel' => '접수 채널',
	'CancelReason' => array('name'=>'취소 사유','schema'=>'claimRequestReasonType'),
	'CancelDetailedReason' => '취소 상세 사유',
	'CancelCompletedDate' => '취소 완료일',
	'CancelApprovalDate' => '취소 승인일',
	'HoldbackStatus' => array('name'=>'보류 상태','schema'=>'holdbackStatusType'),
	'HoldbackReason' => array('name'=>'보류 사유','schema'=>'claimStatusType'),
	'HoldbackDetailedReason' => '보류 상세 사유',
	'EtcFeeDemandAmount' => '기타 비용 청구액',
	'EtcFeePayMethod' =>'기타 비용 결제 방법',
	'EtcFeePayMeans' => '기타 비용 결제 수단',
	'RefundStandbyStatus' => '환불 대기 상태',
	'RefundStandbyReason' => '환불 대기 사유',
	'RefundRequestDate' => '환불 요청일'
);

$__navercheckout_message_schema['claimEXCHANGE'] = array(
	'ClaimStatus' => array('name'=>'클레임 상태','schema'=>'claimStatusType'),
	'ClaimRequestDate' => '클레임 요청일',
	'RequestChannel' => '접수 채널',
	'ExchangeReason' => array('name'=>'교환 사유','schema'=>'claimRequestReasonType'),
	'ExchangeDetailedReason' => '교환 상세 사유',
	'HoldbackStatus' => array('name'=>'보류 상태','schema'=>'holdbackStatusType'),
	'HoldbackReason' => array('name'=>'보류 사유','schema'=>'claimStatusType'),
	'HoldbackDetailedReason' => '보류 상세 사유',
	'CollectAddressAddressType' => array('name'=>'수거지(from) 주소 구분(해외/국내)','schema'=>'addressType'),
	'CollectAddressZipCode' => '수거지(from) 우편번호',
	'CollectAddressBaseAddress' => '수거지(from) 기본 주소',
	'CollectAddressDetailedAddress' => '수거지(from) 상세 주소',
	'CollectAddressCity' => '수거지(from) 도시',
	'CollectAddressState' => '수거지(from) 주(state)',
	'CollectAddressCountry' => '수거지(from) 국가',
	'CollectAddressTel1' => '수거지(from) 연락처 1',
	'CollectAddressTel2' => '수거지(from) 연락처 2',
	'CollectAddressName' => '수거지(from) 이름',
	'ReturnReceiveAddressAddressType' => array('name'=>'수취지(to) 주소 구분(해외/국내)','schema'=>'addressType'),
	'ReturnReceiveAddressZipCode' => '수취지(to) 우편번호',
	'ReturnReceiveAddressBaseAddress' => '수취지(to) 기본 주소',
	'ReturnReceiveAddressDetailedAddress' => '수취지(to) 상세 주소',
	'ReturnReceiveAddressCity' => '수취지(to) 도시',
	'ReturnReceiveAddressState' => '수취지(to) 주(state)',
	'ReturnReceiveAddressCountry' => '수취지(to) 국가',
	'ReturnReceiveAddressTel1' => '수취지(to) 연락처 1',
	'ReturnReceiveAddressTel2' => '수취지(to) 연락처 2',
	'ReturnReceiveAddressName' => '수취지(to) 이름',
	'CollectStatus' => '수거 상태',
	'CollectDeliveryMethod' => array('name'=>'수거 방법','schema'=>'deliveryMethodType'),
	'CollectDeliveryCompany' => '수거 택배사',
	'CollectTrackingNumber' => '수거 송장 번호',
	'CollectCompletedDate' => '수거 완료일',
	'ReDeliveryStatus' => '재배송 상태',
	'ReDeliveryMethod' => array('name'=>'재배송 방법','schema'=>'deliveryMethodType'),
	'ReDeliveryCompany' => '재배송 택배사',
	'ReDeliveryTrackingNumber' => '재배송 송장 번호',
	'EtcFeeDemandAmount' => '기타 비용 청구액',
	'EtcFeePayMethod' =>'기타 비용 결제 방법',
	'EtcFeePayMeans' => '기타 비용 결제 수단',
);

$__navercheckout_message_schema['claimPURCHASE_DECISION_HOLDBACK'] = array(
	'ClaimStatus' => array('name'=>'클레임 상태','schema'=>'claimStatusType'),
	'ClaimRequestDate' => '클레임 요청일',
	'DecisionHoldbackReason' => '구매 확정 보류 사유',
	'DecisionHoldbackDetailedReason' => '구매 확정 보류 상세 사유',
	'DecisionHoldbackTreatMemo' => '구매 확정 보류 처리 메모',
	'ReDeliveryExpectedDate' => '도착 예정일',
	'ReDeliveryMethod' => array('name'=>'재배송 방법','schema'=>'deliveryMethodType'),
	'ReDeliveryCompany' => '재배송 택배사',
	'ReDeliveryTrackingNumber' => '재배송 송장 번호'
);

// 상태별 검색을 위한 값이며, wdsl 정의와는 무관함
$__navercheckout_message_schema['extra_productOrderStatusType'] = array(
	'입금대기'	=> "(PO.ProductOrderStatus = 'PAYMENT_WAITING' AND PO.ClaimStatus IN ('','CANCEL_REJECT','RETURN_REJECT','EXCHANGE_REJECT'))",
	'입금확인'	=> "(PO.ProductOrderStatus = 'PAYED' AND PO.PlaceOrderStatus = 'NOT_YET' AND PO.ClaimStatus IN ('','CANCEL_REJECT','RETURN_REJECT','EXCHANGE_REJECT'))",
	'배송준비중'=> "(PO.ProductOrderStatus = 'PAYED' AND PO.PlaceOrderStatus = 'OK' AND PO.ClaimStatus IN ('','CANCEL_REJECT','RETURN_REJECT','EXCHANGE_REJECT'))",
	'배송중'	=> "(PO.ProductOrderStatus = 'DELIVERING' AND PO.ClaimStatus IN ('','CANCEL_REJECT','RETURN_REJECT','EXCHANGE_REJECT'))",
	'배송완료'	=> "(PO.ProductOrderStatus = 'DELIVERED' AND PO.ClaimStatus IN ('','CANCEL_REJECT','RETURN_REJECT','EXCHANGE_REJECT'))",
	'구매확정'	=> "(PO.ProductOrderStatus = 'PURCHASE_DECIDED' AND PO.ClaimStatus IN ('','CANCEL_REJECT','RETURN_REJECT','EXCHANGE_REJECT','PURCHASE_DECISION_HOLDBACK_RELEASE'))",

	'취소전체'	=> "((PO.ClaimType = 'CANCEL' AND PO.ClaimStatus <> 'CANCEL_REJECT') OR PO.ClaimType = 'ADMIN_CANCEL')",
	'취소요청'	=> "(PO.ProductOrderStatus = 'PAYED' AND PO.ClaimType = 'CANCEL' AND PO.ClaimStatus = 'CANCEL_REQUEST' AND C.HoldbackStatus = 'HOLDBACK' AND C.HoldbackReason = 'SELLER_CONFIRM_NEED')",
	'취소처리중'=> "((PO.ProductOrderStatus = 'PAYED' AND PO.ClaimType = 'CANCEL' AND PO.ClaimStatus = 'CANCELING' AND C.HoldbackStatus = 'HOLDBACK' AND C.HoldbackReason = 'PURCHASER_CONFIRM_NEED') OR (PO.ClaimType = 'ADMIN_CANCEL' AND PO.ClaimStatus = 'ADMIN_CANCELING'))",
	'취소완료'	=> "(PO.ProductOrderStatus IN ('CANCELED' , 'CANCELED_BY_NOPAYMENT'))",

	'반품전체'		=> "(PO.ClaimType = 'RETURN' AND PO.ClaimStatus <> 'RETURN_REJECT')",
	'반품요청'		=> "(PO.ProductOrderStatus IN ('DELIVERING','DELIVERED') AND PO.ClaimType = 'RETURN' AND PO.ClaimStatus = 'RETURN_REQUEST' AND R.HoldbackStatus = 'HOLDBACK' AND R.HoldbackReason = 'SELLER_CONFIRM_NEED')",
	'반품수거중'	=> "(PO.ProductOrderStatus IN ('DELIVERING','DELIVERED') AND PO.ClaimType = 'RETURN' AND PO.ClaimStatus = 'COLLECTING' AND R.HoldbackStatus = 'HOLDBACK' AND R.HoldbackReason = 'SELLER_CONFIRM_NEED')",
	'반품수거완료'	=> "(PO.ProductOrderStatus IN ('DELIVERING','DELIVERED') AND PO.ClaimType = 'RETURN' AND PO.ClaimStatus = 'COLLECT_DONE' AND R.HoldbackStatus = 'HOLDBACK' AND R.HoldbackReason = 'PURCHASER_CONFIRM_NEED')",
	'반품완료'		=> "(PO.ProductOrderStatus = 'RETURNED')",

	'교환전체'		=> "(PO.ClaimType = 'EXCHANGE' AND PO.ClaimStatus <> 'EXCHANGE_REJECT')",
	'교환요청'		=> "(PO.ProductOrderStatus IN ('DELIVERING','DELIVERED') AND PO.ClaimType = 'EXCHANGE' AND PO.ClaimStatus = 'EXCHANGE_REQUEST' AND E.HoldbackStatus = 'HOLDBACK' AND E.HoldbackReason = 'SELLER_CONFIRM_NEED')",
	'교환수거중'	=> "(PO.ProductOrderStatus IN ('DELIVERING','DELIVERED') AND PO.ClaimType = 'EXCHANGE' AND PO.ClaimStatus = 'COLLECTING' AND E.HoldbackStatus = 'HOLDBACK' AND E.HoldbackReason = 'SELLER_CONFIRM_NEED')",
	'교환수거완료'=> "(PO.ProductOrderStatus IN ('DELIVERING','DELIVERED') AND PO.ClaimType = 'EXCHANGE' AND PO.ClaimStatus = 'COLLECT_DONE' AND E.HoldbackStatus = 'HOLDBACK' AND E.HoldbackReason = 'SELLER_CONFIRM_NEED')",
	'교환배송준비중'=> "(PO.ProductOrderStatus IN ('DELIVERING','DELIVERED') AND PO.ClaimType = 'EXCHANGE' AND PO.ClaimStatus = 'COLLECT_DONE' AND E.HoldbackStatus = 'RELEASED')",
	'교환재배송중'	=> "(PO.ProductOrderStatus IN ('DELIVERING','DELIVERED') AND PO.ClaimType = 'EXCHANGE' AND PO.ClaimStatus = 'EXCHANGE_REDELIVERING' AND E.HoldbackStatus = 'RELEASED')",
	'교환완료'		=> "(PO.ProductOrderStatus = 'EXCHANGED')",

);

return $__navercheckout_message_schema;
?>