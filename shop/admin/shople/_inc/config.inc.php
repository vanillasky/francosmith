<?php
$_spt_ar_dlv_company = array(
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

$_spt_ar_origin_type = array('01'=>'국내','02'=>'해외','03'=>'기타');

$_spt_ar_country = array(
'01'=>'국내',
'02'=>'해외',
);

$_spt_ar_dlv_type = array(
'01' => '택배',
'02' => '우편(소포/등기)',
'03' => '직접(화물배달)',
'04' => '퀵서비스',
'05' => '배송없음'
);


$_spt_ar_clm_return_hold_type = $_spt_ar_clm_return_accepthold_type = array(
'101' => '반품 상품 미입고',
'102' => '반품 배송비 미동봉',
'103' => '반품 상품 훼손',
'104' => '구매자 연락 두절',
'105' => '기타'
);



$_spt_ar_clm_return_reject_type = array(
'101' => '반품 상품 미입고',
'102' => '고객 반품신청 철회 대행',
'103' => '반품 불가 상품',
'104' => '기타'
);


$_spt_ar_ord_reject_type = array(
'06' => '배송 지연 예상',
'07' => '상품/가격 정보 잘못 입력',
'08' => '상품 품절(전체옵션)',
'09' => '옵션 품절(해당옵션)',
'10' => '고객변심',
'99' => '기타'
);




function _spt_get_gpc(&$var) {

	$_magic_quotes_gpc = get_magic_quotes_gpc();

    if(is_array($var)) {
        array_walk($var, '_spt_get_gpc');
    }
	else if ($var != '') {

		$var = ($_magic_quotes_gpc) ? stripslashes($var) : $var;

    }

}

$today = date('Ymd');

if (isset($_POST))	array_walk($_POST,	'_spt_get_gpc');
if (isset($_GET))	array_walk($_GET,	'_spt_get_gpc');
?>