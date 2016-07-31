<?
include dirname(__FILE__) . "/../_shopTouch_header.php"; 
@include $shopRootDir . "/lib/page.class.php";
@include $shopRootDir . "/conf/config.pay.php";

### 배송안내 ###
$delivery_info_query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s] AND name=[s]', 'shoptouch', 'delivery_info');
$res_delivery_info = $db->_select($delivery_info_query);
$delivery_info = $res_delivery_info[0]['value'];

### 반품안내 ###
$return_info_query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s] AND name=[s]', 'shoptouch', 'return_info');
$res_return_info = $db->_select($return_info_query);
$return_info = $res_return_info[0]['value'];

if(!$delivery_info) $delivery_info = "본 상품의 평균 배송일은 일입니다.(입금 확인 후) 설치 상품의 경우 다소 늦어질수 있습니다.[배송예정일은 주문시점(주문순서)에 따른 유동성이 발생하므로 평균 배송일과는 차이가 발생할 수 있습니다.] 
본 상품의 배송 가능일은 일 입니다.
배송 가능일이란 본 상품을 주문 하신 고객님들께 상품 배송이 가능한 기간을 의미합니다. (단, 연휴 및 공휴일은 기간 계산시 제외하며 현금 주문일 경우 입금일 기준 입니다.)";


if(!$return_info) $return_info = "상품 청약철회 가능기간은 상품 수령일로 부터 일 이내 입니다.
상품 택(tag)제거 또는 개봉으로 상품 가치 훼손 시에는 일 이내라도 교환 및 반품이 불가능합니다.
저단가 상품, 일부 특가 상품은 고객 변심에 의한 교환, 반품은 고객께서 배송비를 부담하셔야 합니다(제품의 하자,배송오류는 제외)
일부 상품은 신모델 출시, 부품가격 변동 등 제조사 사정으로 가격이 변동될 수 있습니다.
신발의 경우, 실외에서 착화하였거나 사용흔적이 있는 경우에는 교환/반품 기간내라도 교환 및 반품이 불가능 합니다.
수제화 중 개별 주문제작상품(굽높이,발볼,사이즈 변경)의 경우에는 제작완료, 인수 후에는 교환/반품기간내라도 교환 및 반품이 불가능 합니다.
수입,명품 제품의 경우, 제품 및 본 상품의 박스 훼손, 분실 등으로 인한 상품 가치 훼손 시 교환 및 반품이 불가능 하오니, 양해 바랍니다.
일부 특가 상품의 경우, 인수 후에는 제품 하자나 오배송의 경우를 제외한 고객님의 단순변심에 의한 교환, 반품이 불가능할 수 있사오니, 각 상품의 상품상세정보를 꼭 참조하십시오.";

$delivery_info = str_replace("\n", "<br />", $delivery_info);
$return_info = str_replace("\n", "<br />", $return_info);


$tpl->assign( '_set', $set);
$tpl->assign( 'delivery_info', $delivery_info );
$tpl->assign( 'return_info', $return_info );
$tpl->assign( 'pg', $pg );

### 템플릿 출력
$tpl->print_('tpl');

?>