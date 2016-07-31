<?
$integrate_cfg = array();

$integrate_cfg['channels'] = array(
				'enamoo' => !empty($cfg['shopName']) ? $cfg['shopName'] : '이나무',
				'payco' => '페이코',
				'checkout' => '네이버체크아웃',
				'shople' => '쇼플',
				'ipay' => '옥션iPay',
				'mobile' => '모바일샵',
				'todayshop' => '투데이샵',
				'pluscheese' => '플러스치즈',
				);

$integrate_cfg['inflows'] = array(
				"naverCheckout" => "네이버체크아웃",
				"naver" => "네이버지식쇼핑",
				"naver_price" => "네이버가격비교",
				"danawa" => "다나와",
				"mm" => "마이마진",
				"bb" => "베스트바이어",
				"omi" => "오미",
				"enuri" => "에누리",
				//"yahooysp" => "야후전문몰",
				"yahoo_fss" => "야후패션소호",
				"yahoo" => "야후가격비교",
				//"interpark" => "인터파크샵플러스",
				"openstyle" => "인터파크오픈스타일",
				"openstyleOutlink" => "인터파크오픈스타일아웃링크",
				"naver_pchs_040901" => "네이버지식쇼핑추천광고",
				"auctionos" => "옥션어바웃",
				"daumCpc" => "다음쇼핑하우",
				"cywordScrap" => "싸이월드스크랩",
				);

$integrate_cfg['step'] = array(
				0	=> '주문접수',
				1	=> '입금확인',
				2	=> '배송준비중',
				3	=> '배송중',
				4	=> '배송완료',

				10	=> '취소접수',
				11	=> '취소완료',

				20	=> '환불접수',
				21	=> '환불완료',

				30	=> '반품접수',
				31	=> '반품완료',

				40	=> '교환접수',
				41	=> '교환완료',

				50	=> '결제시도',
				51	=> 'PG확인요망',
				54	=> '결제실패',
				91  => '재주문',

				99 => '알수없음',	// 처리 단계가 명확하지 않아, 각각의 판매관리에서 조회해봐야 함
);

// 배송사 설정

// 이나무
$query = "SELECT deliveryno AS code, deliverycomp AS name, useyn  FROM ".GD_LIST_DELIVERY." WHERE useyn = 'y' ORDER BY deliverycomp";
$res = $db->query($query);
while ($data=$db->fetch($res)){
	$integrate_cfg['dlv_company']['enamoo'][$data['code']] = $data['name'];
}

// 쇼플
$integrate_cfg['dlv_company']['shople'] = array(
	'00001' => '동부익스프레스',
	'00002' => '로젠택배',
	'00003' => '사가와익스프레',
	'00006' => '옐로우캡',
	'00007' => '우체국택배',
	'00008' => '우편등기',
	'00010' => '하나로택배',
	'00011' => '한진택배',
	'00012' => '현대택배',
	'00013' => 'CJ-GLS',
	'00014' => 'KGB택배',
	'00017' => '대한통운',
	'00019' => '이노지스택배',
	'00021' => '대신택배',
	'00022' => '일양로지스',
	'00023' => 'ACI',
	'00025' => 'WIZWA',
	'00026' => '경동택배',
	'00027' => '천일택배',
	'00099' => '기타'
	);

// 체크아웃
$integrate_cfg['dlv_company']['checkout'] = array(
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

// ipay
$integrate_cfg['dlv_company']['ipay'] = array(
	'korex' => '대한통운택배',
	'hyundai' => '현대택배',
	'epost' => '우체국택배',
	'dongbu' => '동부익스프레스택배',
	'ajutb' => '동원로엑스택배',
	'cjgls' => 'CJ GLS택배',
	'hth' => 'CJ GLS택배',
	'kgb' => '로젠택배',
	'yellow' => '옐로우캡',
	'hanjin' => '한진택배',
	'kgbls' => 'KGB택배',
	'hanaro' => '하나로로지스',
	'sagawa' => 'SC로지스(사가와)',
	'ktlogistics' => 'KT로지스',
	'sedex' => '한진택배',
	'nedex' => '한진택배',
	'innogis' => '이노지스택배',
	'gmgls' => '굿모닝택배',
	'daesin' => '대신택배',
	'ilyang' => '일양로지스',
	'kyungdong' => '경동택배',
	'chonil' => '천일택배',
	'gtx' => 'GTX택배',
	'etc' => '기타',
);


// 택배사 매칭 맵
// 키는 이나무 택배사 idx
$integrate_cfg['dlv_company']['map'] = array(
	'checkout' => array(
		'KGBLS' => 1,
		'KOREX' => 4,
		'KGB' => 5,
		'YELLOW' => 8,
		'EPOST' => 9,
		'REGISTPOST' => 18,
		'HANJIN' => 12,
		'HYUNDAI' => 13,
		'CJGLS' => 15,
		'SAGAWA' => 17,
		'DONGBU' => 21,
		'KDEXP' => 39,
		'HANARO' => '20',
		'INNOGIS' => '32',
		'DAESIN' => '33',
		'ILYANG' => '22',
		'CHUNIL' => '19',
	),
	'ipay' => array(
		'kgbls' =>  1,
		'korex' => 4,
		'kgb'  => 5,
		'yellow' =>  8,
		'epost' => 9,
		'hanjin' => 12,
		'hyundai' => 13,
		'cjgls'  =>15,
		'sagawa' => 17,
		'dongbu' => 21,
		'kyungdong' => 39,
	),
	'shople' => array(
		'00014' => 1,
		'00017' => 4,
		'00002' => 5,
		'00006' => 8,
		'00007' => 9,
		'00011' => 12,
		'00012' => 13,
		'00013' => 15,
		'00003' => 17,
		'00001' => 21,
		'00026' => 39,
	)
);

// 클레임 사유 코드
$integrate_cfg['claim_code'] = array(
	'enamoo' => codeitem("cancel"),
	'shople' => array(
		'06' => '배송 지연 예상',
		'07' => '상품/가격 정보 잘못 입력',
		'08' => '상품 품절(전체옵션)',
		'09' => '옵션 품절(해당옵션)',
		'10' => '고객변심',
		'99' => '기타',
		'101' => '고객변심',
		'102' => '포장불량',
		'103' => '서비스 및 상품불만족',
		'104' => '상품하자',
		'105' => '상품정보상이',
		'106' => '상품파손',
		'107' => '수취거부',
		'108' => '오배송',
		'109' => '반송',
		'110' => '사이즈, 색상 등을 잘못 선택함',
		'111' => '배송된 상품의 파손/하자/포장 불량',
		'112' => '상품이 도착하고 있지 않음',
		'113' => '기타',
		'119' => '고객센터 구매확정후 취소',
		'201' => '고객변심',
		'202' => '포장불량',
		'203' => '서비스 및 상품불만족',
		'204' => '상품하자',
		'205' => '상품파손',
		'206' => '사이즈 또는 색상 등을 잘못 선택함',
		'207' => '배송된 상품의 파손/하자/포장 불량',
		'208' => '다른 상품이 잘못 배송됨',
		'209' => '품절 등의 사유로 판매자 협의 후 교환',
		'210' => '상품이 상품상세 정보와 틀림',
		'211' => '기타',
		'301' => '배송누락',
		'302' => '상품분실',
		'303' => '수취거부',
	),
	'ipay' => array(
		'LowerThanWishPrice' => '낙찰가격이 희망판매가격에 미치지 못함',
		'ManufacturingDefect' => '제품에 하자가 생겨서 판매불가',
		'RunOutOfStock' => '재고부족(품절)',
		'SellToOtherDitstributionChannel' => '다른 경로로 판매하고자 함',
		'SoldToOtherBuyer' => '구매자의 입금지연으로 인하여 다른 구매자에게 판매',
		'UnreliableBuyer' => '장난/허위 입찰로 보여서 판매거부',
		'OtherReason' => '기타 사유',
	),
);

// 결제 방법
$integrate_cfg['pay_method'] = $r_settlekind;
$integrate_cfg['pay_method']['o'] = '실시간계좌이체';
$integrate_cfg['pay_method']['NAVER_CASH'] = '네이버 캐쉬';


// 통합검색
$integrate_cfg['skey'] = array(
	array(
		'field'=>'o.ordno',
		'condition'=>'equal',
		'pattern'=>'/^[0-9]+$/',
	),
	array(
		'field'=>'o.ord_name',
		'condition'=>'like',
		'pattern'=>'/.{4,}/',
	),
	array(
		'field'=>'o.rcv_name',
		'condition'=>'like',
		'pattern'=>'/.{4,}/',
	),
	array(
		'field'=>'o.pay_bank_name',
		'condition'=>'like',
		'pattern'=>'/.{4,}/',
	),
	array(
		'field'=>'m.m_id',
		'condition'=>'equal',
		'pattern'=>'/^[\xa1-\xfea-zA-Z0-9_-]{4,20}$/',
	),
	array(
		'field'=>'o.ord_phone',
		'condition'=>'like',
		'pattern'=>'/[0-9]{4,}/',
	),
	array(
		'field'=>'o.ord_mobile',
		'condition'=>'like',
		'pattern'=>'/[0-9]{4,}/',
	),
	array(
		'field'=>'o.rcv_phone',
		'condition'=>'like',
		'pattern'=>'/[0-9]{4,}/',
	),
	array(
		'field'=>'o.rcv_mobile',
		'condition'=>'like',
		'pattern'=>'/[0-9]{4,}/',
	),
	array(
		'field'=>'o.rcv_address',
		'condition'=>'like',
		'pattern'=>'/.{4,}/',
	),
	array(
		'field'=>'o.dlv_no',
		'condition'=>'like',
		'pattern'=>'/^[0-9]+$/',
	)
);
?>