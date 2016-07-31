<?
/*
페이코 > 쇼핑몰 추가배송비(지역별 배송비) 조회
*/
include "../lib/library.php";

//if($_SERVER['REMOTE_ADDR'] != '211.233.51.165' && $_SERVER['REMOTE_ADDR'] != '211.233.51.166' && $_SERVER['REMOTE_ADDR'] != '211.233.51.250') exit;

function resposen_log($msg)
{
	global $paycoApi;
	if(!$paycoApi) $paycoApi = &load_class('paycoApi','paycoApi');
	$paycoApi->receive_log($msg, 'area_delivery');
	print_r(serialize($msg));
	exit;
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

$arr_data = $_POST;

if(empty($arr_data)) resposen_log('전송된 데이터가 없습니다.');

//페이코 클래스
$paycoApi = &load_class('paycoApi','paycoApi');
$payco = &load_class('payco','payco');

// 수신데이터 로그 저장
$paycoApi->receive_log($arr_data, 'area_delivery');

// 수신 데이터 쇼핑몰 체크 및 데이터 복호화
$addr_data = $paycoApi->shop_check($arr_data);

if($addr_data === false) {
	resposen_log('쇼핑몰 고유값이 다릅니다.');
}

$param = arr_data_iconv($addr_data);
$items = $param['sno'];
$param['road_address'] = $param['address'];

$area = &load_class('areaDelivery','areaDelivery');
$_extra_fee = $area->getPay();

if($_extra_fee == '') $_extra_fee = '0';

include dirname(__FILE__).'/../conf/config.pay.php';
$conf_delivery = $set['delivery'];

if (isset($conf_delivery['add_extra_fee']) === true) {
	$tmp_add_extra_fee			= $conf_delivery['add_extra_fee'];		// 기존 레거시 보장, 해당 값은 더이상 사용 안함
}
else {
	$tmp_add_extra_fee			= 1;										// 기본 값은 지역별 추가 배송비 받음으로 처리
}

if (isset($conf_delivery['add_extra_fee_basic']) === false) {				// "기본 배송정책에 의한 조건부 무료인 경우"에서 기본값 (기존 레거시 또는 지역별 추가 배송비 받음)
	$conf_delivery['add_extra_fee_basic']			= $tmp_add_extra_fee;
}

if (isset($conf_delivery['add_extra_fee_free']) === false) {				// "무료배송 상품 주문시"인 경우 기본값 (기존 레거시 또는 지역별 추가 배송비 받음)
	$conf_delivery['add_extra_fee_free']			= $tmp_add_extra_fee;
}

if (isset($conf_delivery['add_extra_fee_memberGroup']) === false) {		// "회원 그룹 혜택에 의한 배송비 무료인 경우"에서 기본값 (기존 레거시 또는 지역별 추가 배송비 받음)
	$conf_delivery['add_extra_fee_memberGroup']	= $tmp_add_extra_fee;
	/* 페이코 간편구매는 비회원을 대상으로 하고 있어 회원 그룹 혜택에 대한 처리를 하지 않음 */
}
unset($tmp_add_extra_fee);

// 지역별 추가 배송비 다중 부과 기본값 세팅
if (isset($conf_delivery['add_extra_fee_duplicate_each']) === false) {
	$conf_delivery['add_extra_fee_duplicate_each']		= 1;			// 개별배송상품 주문시 기본값은 "항목별 중복 부과" 로 처리 (더이상 사용하지 않음)
}

if (isset($conf_delivery['add_extra_fee_duplicate_free']) === false) {
	$conf_delivery['add_extra_fee_duplicate_free']		= 1;			// 무료배송 상품 주문시 기본값은 "항목별 중복 부과" 로 처리
}

if (isset($conf_delivery['add_extra_fee_duplicate_fixEach']) === false) {
	$conf_delivery['add_extra_fee_duplicate_fixEach']		= 1;			// 고정 배송비 상품 주문시 기본값은 "항목별 중복 부과" 로 처리
}

/* 기본배송비로 무료시 추가배송비 안받음 */

/*
지역별 배송비 부과
 - 기본배송비 : 종류, 수량에 상관없이 1회부과
 - 상품배송비(무료, 고정) : 각 상품별 부과 (무료상품 2개 - 상품이 다르거나 옵션이 다른경우, 고정3개인 경우 5번 부과)
 - 상품배송비(수량별) : 수량별 부과
*/


/*
	$conf_delivery['freeDelivery']
	- 1 = 무료배송 상품이 있는 경우 모든 주문 무료배송
		무료배송시 지역별 배송비 미부과
	- 0 = 무료배송 상품만 무료
		무료배송 상품에도 지역별 배송비 부과
*/


/*
	$conf_delivery['add_extra_fee']
	- 1 = 무료배송시 추가배송비 받음
	- 0 = 무료배송비 추가배송비 받지 않음
		기본배송비로 무료시에도 받지 않음
		freeDelivery가 0인 경우 무료배송 상품에도 지역별 배송비 부과
*/

foreach($param['sno'] as $idxs) {
	if(strstr($idxs, 'dv_')) {
		$item_delivery_idxs = str_replace('dv_', '', $idxs);
		break;
	}
}

$item_delivery = $db->_select('SELECT * from '.GD_ORDER_ITEM_DELIVERY.' WHERE ordno='.$param['ordno'].' AND ordno='.$item_delivery_idxs.' ORDER BY delivery_type asc');

$free_fee = false;
$free_pay = false;
$fix_fee = false;
$fix_pay = false;
$arr_area['area_delivery'] = 0;
$arr_area['oi_delivery_idx'] = 0;

foreach($item_delivery as $delivery) {
	switch($delivery['delivery_type']) {
		case '0' ://기본배송비
			if(isset($arr_area[0]) === false) {
				//기본배송비가 무료이고, 기본배송비가 무료일때 지역별 배송비 않받음 설정인 경우
				if($delivery['prn_delivery_price'] < 1 && $conf_delivery['add_extra_fee_basic'] == '0') break;;

				$arr_area['area_delivery'] += $_extra_fee;
			}
			break;
		case '1' ://무료배송비	(쇼핑몰에서 $conf_delivery['add_extra_fee']값에 상관없이 추가배송비 부과함)
			// 무료배송인 경우 지역별 배송비 중복부과 설정
			if($conf_delivery['add_extra_fee_duplicate_free'] == '1') $free_fee = true;
			else {
				if($free_pay === false) $free_fee = true;
				else $free_fee = false;
			}

			if($free_fee === true && $conf_delivery['add_extra_fee_free'] == '1') {
				$arr_area['area_delivery'] += $_extra_fee;
				$free_pay = true;
			}

			break;
		case '4' ://고정배송비	(쇼핑몰에서 $conf_delivery['add_extra_fee']값에 상관없이 추가배송비 부과함)
			// 고정배송비인 경우 지역별 배송비 중복부과 설정
			if($conf_delivery['add_extra_fee_duplicate_fixEach'] == '1') $fix_fee = true;
			else {
				if($fix_pay === false) $fix_fee = true;
				else $fix_fee = false;
			}

			if($fix_fee === true) {
				$arr_area['area_delivery'] += $_extra_fee;
				$fix_pay = true;
			}

			break;
		case '5' ://수량별 배송비
			$item_res = $db->_select('SELECT sum(ea) as ea FROM '.GD_ORDER_ITEM.' WHERE oi_delivery_idx='.$delivery['oi_delivery_idx']);

			$arr_area['area_delivery'] += $item_res[0]['ea'] * $_extra_fee;
			break;
		case '100' : //이미 등록된 지역별 배송비는 삭제
				$db->_query('DELETE FROM '.GD_ORDER_ITEM_DELIVERY.' WHERE ordno='.$param['ordno'].' AND oi_delivery_idx='.$delivery['oi_delivery_idx']);
			break;
	}
}

if(isset($arr_area)) {
	$orderDeliveryItem = &load_class('orderDeliveryItem','orderDeliveryItem');
	$extra_data_idx = $orderDeliveryItem->extra_delivery($param['ordno'], $arr_area['area_delivery'], $arr_area['area_delivery']);

	$upd_query = $db->_query_print('UPDATE '.GD_ORDER_ITEM.' SET oi_area_idx=[i] WHERE ordno=[i]', $extra_data_idx, $param['ordno']);
	$area_rtn = $db->_query($upd_query);
}

if(!isset($arr_area)) {
	$arr_area = '0';
}

resposen_log($arr_area);
?>