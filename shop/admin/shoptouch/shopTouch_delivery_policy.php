<?php
$location = "쇼핑몰 App관리 > 배송정책안내 설정";
include "../_header.php";
@include_once "../../lib/pAPI.class.php";
@include_once "../../lib/json.class.php";
$pAPI = new pAPI();
$json = new Services_JSON(16);

$expire_dt = $pAPI->getExpireDate();
if(!$expire_dt) {
	msg('서비스 신청후에 사용가능한 메뉴입니다.', -1);
}

$now_date = date('Y-m-d 23:59:59');
$tmp_now_date = date('Y-m-d 23:59:59', mktime(0,0,0, substr($now_date, 5, 2), substr($now_date, 8, 2) - 30, substr($now_date, 0, 4)));
if($expire_dt < $tmp_now_date) {
	msg('서비스 사용기간 만료후 30일이 지나 서비스가 삭제 되었습니다.\n서비스를 다시 신청해 주시기 바랍니다.', -1);
}


$d_info_query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s] AND name=[s]', 'shoptouch', 'delivery_info');
$d_res = $db->_select($d_info_query);
$delivery_info = $d_res[0]['value'];

$r_info_query = $db->_query_print('SELECT * FROM gd_env WHERE category=[s] AND menu=[s]', 'shoptouch', 'return_info');
$r_res = $db->_select($r_info_query);
$return_info = $r_res[0]['value'];

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
?>
<?
if($expire_dt < $now_date) {
	@include('shopTouch_expire_msg.php');
}
?>
<form name=form method=post action="indb.php" enctype="multipart/form-data">
<input type=hidden name=mode value="delivery_policy_set">

<div class="title title_top">배송정책 안내 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=shoppingapp&no=17')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<div class="title_sub" style="margin:0">배송 안내<span>쇼핑몰 App 상품 상세 페이지에서 보여질 배송정책 안내 입니다. <font class=extext>(치환코드의 내용은 디자인관리 상품 상세화면 관리 에서 확인 하실 수 있습니다.)</font></span></div>
<div style="width:100%;padding:10px;">
	<textarea name="delivery_fix" style="width:100%;scroll:auto;height:50px;" readonly>배송비 : 기본배송료는 {?_set.delivery.default}{=number_format(_set.delivery.default)}원{:}무료{/} 입니다. (도서,산간,오지 일부지역은 배송비가 추가될 수 있습니다) {?_set.delivery.free}&nbsp;{=number_format(_set.delivery.free)}원 이상 구매시 무료배송입니다.{/}</textarea>
	<textarea name="delivery_info" style="width:100%;scroll:auto;height:150px;"><?=$delivery_info?>
	</textarea>
</div>
<div style="width:100%;height:20px;"></div>
<div class="title_sub" style="margin:0">교환및반품 안내</div>
<div style="width:100%;padding:10px;">
	<textarea name="return_info" style="width:100%;scroll:auto;height:150px;"><?=$return_info?></textarea>
</div>
<div class="button">
<input type=image src="../img/btn_modify.gif">
</div>
</form>

<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">쇼핑몰 App의 상품상세 화면중 이용안내 탭에 보여질 배송정책에 대해 설정 하실 수 있습니다.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

<? include "../_footer.php"; ?>