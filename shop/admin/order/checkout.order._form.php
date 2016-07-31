<?php
/**
 * 네이버체크아웃 주문 > 주문상세내역
 * @author sunny, oneorzero
 */
$naverCheckoutAPI = Core::loader('naverCheckoutAPI');

function dateOutput($var) {
	if($var=='0000-00-00 00:00:00') {
		return '';
	}
	return preg_replace('/^(\d{4})-(\d{2})-(\d{2}) (\d{2}):(\d{2}):(\d{2})$/','$1년 $2월 $3일 $4시 $5분',$var);
}

$orderNo = (int)$_GET['orderNo'];
$OrderID = (int)$_GET['OrderID'];

if($OrderID) {
	$query = $db->_query_print('select orderNo from gd_navercheckout_order where ORDER_OrderID=[s]',$OrderID);
	$result = $db->_select($query);
	$orderNo = $result[0]['orderNo'];
}

$naverCheckoutAPI->noEmoney = true;
$naverCheckoutAPI->SyncOrder($orderNo);

$query = $db->_query_print('select * from gd_navercheckout_order where orderNo=[s]',$orderNo);
$result = $db->_select($query);
$orderInfo = $result[0];


$query = $db->_query_print('select * from gd_navercheckout_order_product where orderNo=[s] order by seq asc',$orderNo);
$productList = $db->_select($query);

// 사용 가능한 액션
$isPlaceOrder=$isCancelSale=$isCancelOrder=$isShipOrder=$isCancelShipping=false;
// 주문접수가능 조건
if($orderInfo['ORDER_OrderStatusCode']=='OD0002') {
	$isPlaceOrder=true;
}
//판매취소가능 조건
if($orderInfo['ORDER_OrderStatusCode']=='OD0002') {
	$isCancelSale=true;
}
//주문취소가능 조건
if(in_array($orderInfo['ORDER_OrderStatusCode'],array('OD0007','OD0008','OD0009','OD0010','OD0011'))) {
	$isCancelOrder=true;
}
//발송처리가능 조건
if(in_array($orderInfo['ORDER_OrderStatusCode'],array('OD0007','OD0008'))) {
	$isShipOrder=true;
}
//발송취소가능 조건
if(in_array($orderInfo['ORDER_OrderStatusCode'],array('OD0009','OD0010','OD0011'))) {
	$isCancelShipping=true;
}
list($totalEmoney) = $db->fetch("SELECT SUM(emoney) FROM gd_navercheckout_order_product WHERE orderNo='$orderNo'");
?>
<? include('checkout.common.php'); ?>
<script type="text/javascript">
document.observe("dom:loaded", function() {
	<? if($isShipOrder): ?>
	Event.observe($("frmShipOrder").select(".selShippingCompany")[0], 'change', function(event) {
		var element = $(Event.element(event));
		if(element.value=='z_etc' || element.value=='z_quick' || element.value=='z_direct' || element.value=='z_visit' || element.value=='z_delegation') {
			$("frmShipOrder").select(".iptTrackingNumber")[0].value='';
			$("frmShipOrder").select(".iptTrackingNumber")[0].disabled=true;
			$("frmShipOrder").select(".iptTrackingNumber")[0].style.backgroundColor="#cccccc";
		}
		else {
			$("frmShipOrder").select(".iptTrackingNumber")[0].disabled=false;
			$("frmShipOrder").select(".iptTrackingNumber")[0].style.backgroundColor="#ffffff";
		}
	});
	<? endif; ?>
});
function syncOrder() {
	customPopupLayer('checkout.api.SyncOrder.php?orderNo=<?=$orderNo?>',600,400);
}

function callPlaceOrder() {
	customPopupLayer('about:blank',780,500);
	$('frmPlaceOrder').submit();
}

function callShipOrder() {
	var eleShippingCompleteDate = $("frmShipOrder").select(".iptShippingCompleteDate")[0];
	var eleShippingCompany = $("frmShipOrder").select(".selShippingCompany")[0];
	var eleTrackingNumber = $("frmShipOrder").select(".iptTrackingNumber")[0];

	if(eleShippingCompleteDate.value.length==0) {
		alert('선택하신 주문을 발송처리하기 위해서는 배송일을 입력하셔야 합니다');
		return;
	}
	if(eleShippingCompany.selectedIndex==0) {
		alert('선택하신 주문을 발송처리하기 위해서는 배송방법을 선택하셔야 합니다');
		return;
	}
	var tmp = eleShippingCompany.options[eleShippingCompany.selectedIndex].value;
	if(!(tmp=='z_etc' || tmp=='z_quick' || tmp=='z_direct' || tmp=='z_visit' || tmp=='z_delegation')) {
		if(eleTrackingNumber.value.length==0) {
			alert('선택하신 주문을 발송처리하기 위해서는 송장번호를 입력하셔야 합니다');
			return;
		}
	}

	customPopupLayer('about:blank',780,500);
	$('frmShipOrder').submit();
}

function callCancelSale() {
	if($("frmCancelSale").CancelReasonDetail.value.length==0) {
		alert('판매 취소 메세지를 적어주세요');
		return;
	}
	customPopupLayer('about:blank',780,500);
	$('frmCancelSale').submit();
}

function callCancelOrder() {
	if($("frmCancelOrder").CancelReasonDetail.value.length==0) {
		alert('주문 취소 메세지를 적어주세요');
		return;
	}
	customPopupLayer('about:blank',780,500);
	$('frmCancelOrder').submit();
}

function callCancelShipping() {
	customPopupLayer('about:blank',780,500);
	$('frmCancelShipping').submit();
}
</script>
<div class="title title_top">주문상세</div>

<table class="tb" cellpadding="4" cellspacing="0">
<tr height="25" bgcolor="#2E2B29" class="small4" style="padding-top:8px">
	<th><font color="white">번호</font></th>
	<th><font color="white">상품명</font></th>
	<th><font color="white">옵션</font></th>
	<th><font color="white">수량</font></th>
	<th><font color="white">상품가격</font></th>
	<th><font color="white">반품신청여부</font></th>
</tr>
<col align=center>
<col>
<col align=center span=4>
<? foreach($productList as $eachProduct): ?>
<tr>
	<td width=70 nowrap><?=$eachProduct['seq']?></td>
	<td width=100%><?=htmlspecialchars($eachProduct['ProductName'])?></td>
	<td width=200 nowrap><?=htmlspecialchars($eachProduct['ProductOption'])?></td>
	<td width=80 nowrap><?=number_format($eachProduct['Quantity'])?></td>
	<td width=150 nowrap><?=number_format($eachProduct['UnitPrice'])?>원</td>
	<td width=120 nowrap><?=$eachProduct['ReturnRequested']=='y'?'예':'아니요'?></td>
</tr>
<? endforeach; ?>
</table>
<br><br>

<? if($isPlaceOrder): ?>
<form id="frmPlaceOrder" action="checkout.api.PlaceOrder.php" target="processLayerForm" method="post">
<input type="hidden" name="orderNo[]" value="<?=$orderInfo['orderNo']?>">
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>주문접수처리</td>
	<td><input type="button" value="주문접수하기" onclick="callPlaceOrder()"></td>
</tr>
</table>
</form>
<br>
<? endif; ?>

<? if($isShipOrder): ?>
<form id="frmShipOrder" action="checkout.api.ShipOrder.php" target="processLayerForm" method="post">
<input type="hidden" name="request[0][orderNo]" value="<?=$orderInfo['orderNo']?>">
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>발송처리</td>
	<td>
		<table cellpadding="0">
		<tr>
		<td>배송일 :</td>
		<td>
		<input type="text" name="request[0][ShippingCompleteDate]" value="" onclick="calendar(event)" readonly style="width:100px" class="iptShippingCompleteDate">
		</td>
		<tr>
		<td>배송방법 :</td>
		<td>
		<select name="request[0][ShippingCompany]" style="width:95%;font-size:7pt;" class="selShippingCompany">
		<option value="">(선택)</option>
		<option value="korex">대한통운</option>
		<option value="cjgls">CJGLS</option>
		<option value="sagawa">SC 로지스</option>
		<option value="yellow">옐로우캡</option>
		<option value="kgb">로젠택배</option>
		<option value="dongbu">동부익스프레스택배</option>
		<option value="EPOST">우체국택배</option>
		<option value="hanjin">한진택배</option>
		<option value="hyundai">현대택배</option>
		<option value="kgbls">KGB 택배</option>
		<option value="z_etc">기타 택배</option>
		<option value="z_quick">퀵서비스</option>
		<option value="z_direct">직배송</option>
		<option value="z_visit">방문 수령</option>
		<option value="z_post">우편 등기</option>
		<option value="z_delegation">업체별 배송</option>
		<option value="kdexp">경동택배</option>
		</select>
		</td>
		</tr>
		<tr>
		<td>송장번호 :</td>
		<td><input type="text" name="request[0][TrackingNumber]" style="width:300px" class="iptTrackingNumber"></td>
		</tr>
		<tr>
		<td></td>
		<td> <input type="button" value="발송처리하기 " onclick="callShipOrder()"></td>
		</tr>
		</table>
	</td>
</tr>
</table></form>
<br>
<? endif; ?>

<? if($isCancelSale): ?>
<form id="frmCancelSale" action="checkout.api.CancelSale.php" target="processLayerForm" method="post">
<input type="hidden" name="orderNo[]" value="<?=$orderInfo['orderNo']?>">
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>판매취소처리</td>
	<td>
		<table cellpadding="0">
		<tr>
		<td>판매취소 사유 :</td>
		<td>
		<select name="CancelReason" style="width:100px">
		<option value="41"> 품절</option>
		<option value="42"> 제품 하자</option>
		<option value="43"> 허위 구매</option>
		<option value="44"> 기타</option>
		</select>
		</td>
		<tr>
		<td>판매취소 메세지 :</td>
		<td><input type="text" name="CancelReasonDetail" style="width:300px"></td>
		</tr>
		<tr>
		<td></td>
		<td> <input type="button" value="판매취소하기 " onclick="callCancelSale()"></td>
		</tr>
		</table>
	</td>
</tr>
</table></form>
<br>
<? endif; ?>

<? if($isCancelOrder): ?>
<form id="frmCancelOrder" action="checkout.api.CancelOrder.php" target="processLayerForm" method="post">
<input type="hidden" name="orderNo[]" value="<?=$orderInfo['orderNo']?>">
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>주문취소처리</td>
	<td>
		<table cellpadding="0">
		<tr>
		<td>주문취소 사유 :</td>
		<td>
		<select name="CancelReason" style="width:100px">
		<option value="31"> 필요성 상실</option>
		<option value="32"> 단순 변심</option>
		<option value="33"> 결제 수단 변경</option>
		<option value="34"> 서비스 불만</option>
		<option value="35"> 이미 구입했음(선물받음)</option>
		<option value="36"> 이중 주문</option>
		<option value="37"> 기타</option>
		</select>
		</td>
		<tr>
		<td>주문취소 메세지 :</td>
		<td><input type="text" name="CancelReasonDetail" style="width:300px"></td>
		</tr>
		<tr>
		<td></td>
		<td> <input type="button" value="주문취소하기 " onclick="callCancelOrder()"></td>
		</tr>
		</table>
	</td>
</tr>
</table></form>
<br>
<? endif; ?>

<? if($isCancelShipping): ?>
<form id="frmCancelShipping" action="checkout.api.CancelShipping.php" target="processLayerForm" method="post">
<input type="hidden" name="orderNo[]" value="<?=$orderInfo['orderNo']?>">
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>발송취소처리</td>
	<td><input type="button" value="발송취소하기" onclick="callCancelShipping()"></td>
</tr>
</table>
</form>
<br>
<? endif; ?>


<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>주문정보</b></font></div>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>주문 일시</td>
	<td><?=dateOutput($orderInfo['ORDER_OrderDateTime'])?></td>
</tr>
<tr>
	<td>주문 상태</td>
	<td><?=htmlspecialchars($orderInfo['ORDER_OrderStatus'])?></td>
</tr>
<tr>
	<td>재결제 여부</td>
	<td><?=htmlspecialchars($orderInfo['ORDER_Repayment'])?></td>
</tr>
<tr>
	<td>판매 완료 일시</td>
	<td><?=dateOutput($orderInfo['ORDER_SaleCompleteDate'])?></td>
</tr>
<tr>
	<td>결제 기한 일시</td>
	<td><?=dateOutput($orderInfo['ORDER_PaymentDueDate'])?></td>
</tr>
<tr>
	<td>결제 번호</td>
	<td><?=htmlspecialchars($orderInfo['ORDER_PaymentNumber'])?></td>
</tr>
<tr>
	<td>입금 은행</td>
	<td><?=htmlspecialchars($orderInfo['ORDER_PaymentBank'])?></td>
</tr>
<tr>
	<td>입금자 이름</td>
	<td><?=htmlspecialchars($orderInfo['ORDER_PaymentSender'])?></td>
</tr>
<tr>
	<td>매출 코드</td>
	<td><?=htmlspecialchars($orderInfo['ORDER_SellingCode'])?></td>
</tr>
<tr>
	<td>주문 추가 정보</td>
	<td><?=htmlspecialchars($orderInfo['ORDER_OrderExtraData'])?></td>
</tr>
</table>


<br><br>
<table border="0" width="100%">
<tr>
<td width="50%" valign="top">
	<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>최종결제정보</b></font></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>총 판매가</td>
		<td><?=number_format($orderInfo['ORDER_TotalProductAmount'])?>원</td>
	</tr>
	<tr>
		<td>배송비</td>
		<td><?=number_format($orderInfo['ORDER_ShippingFee'])?>원</td>
	</tr>
	<tr>
		<td>가맹점 총 주문 금액</td>
		<td><?=number_format($orderInfo['ORDER_MallOrderAmount'])?>원</td>
	</tr>
	<tr>
		<td>네이버 할인 금액</td>
		<td><?=number_format($orderInfo['ORDER_NaverDiscountAmount'])?>원</td>
	</tr>
	<tr>
		<td>총 주문 금액</td>
		<td><?=number_format($orderInfo['ORDER_TotalOrderAmount'])?>원</td>
	</tr>
	<tr>
		<td>적립금 사용 금액</td>
		<td><?=number_format($orderInfo['ORDER_CashbackDiscountAmount'])?>원</td>
	</tr>
	<tr>
		<td>결제 금액</td>
		<td><?=number_format($orderInfo['ORDER_PaymentAmount'])?>원</td>
	</tr>
	<tr>
		<td>결제 방법</td>
		<td><?=htmlspecialchars($orderInfo['ORDER_PaymentMethod'])?></td>
	</tr>
	<tr>
		<td>결제 일시</td>
		<td><?=dateOutput($orderInfo['ORDER_PaymentDate'])?></td>
	</tr>
	<tr>
		<td>에스크로 여부</td>
		<td><?=htmlspecialchars($orderInfo['ORDER_Escrow'])?></td>
	</tr>
	<tr>
		<td>배송비 종류</td>
		<td><?=htmlspecialchars($orderInfo['ORDER_ShippingFeeType'])?></td>
	</tr>
	<tr>
		<td>적립될 금액</td>
		<td><?=number_format($totalEmoney)?>원</td>
	</tr>
	</table>
</td>
<td width="50%" valign="top">
	<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>기존결제정보</b></font></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>총 판매가</td>
		<td><?=number_format($orderInfo['ORDER_OriginalTotalProductAmount'])?>원</td>
	</tr>
	<tr>
		<td>배송비</td>
		<td><?=number_format($orderInfo['ORDER_OriginalShippingFee'])?>원</td>
	</tr>
	<tr>
		<td>가맹점 총 주문 금액</td>
		<td><?=number_format($orderInfo['ORDER_OriginalMallOrderAmount'])?>원</td>
	</tr>
	<tr>
		<td>네이버 할인 금액</td>
		<td><?=number_format($orderInfo['ORDER_OriginalNaverDiscountAmount'])?>원</td>
	</tr>
	<tr>
		<td>총 주문 금액</td>
		<td><?=number_format($orderInfo['ORDER_OriginalTotalOrderAmount'])?>원</td>
	</tr>
	<tr>
		<td>적립금 사용 금액</td>
		<td><?=number_format($orderInfo['ORDER_OriginalCashbackDiscountAmount'])?>원</td>
	</tr>
	<tr>
		<td>결제 금액</td>
		<td><?=number_format($orderInfo['ORDER_OriginalPaymentAmount'])?>원</td>
	</tr>
	<tr>
		<td>결제 방법</td>
		<td><?=htmlspecialchars($orderInfo['ORDER_OriginalPaymentMethod'])?></td>
	</tr>
	<tr>
		<td>결제 일시</td>
		<td><?=dateOutput($orderInfo['ORDER_OriginalPaymentDate'])?></td>
	</tr>
	<tr>
		<td>에스크로 여부</td>
		<td><?=htmlspecialchars($orderInfo['ORDER_OriginalEscrow'])?></td>
	</tr>
	<tr>
		<td>배송비 종류</td>
		<td><?=htmlspecialchars($orderInfo['ORDER_OriginalShippingFeeType'])?></td>
	</tr>
	</table>
</td>
</table>


<br><br>
<table border="0" width="100%">
<tr>
<td width="50%"  valign="top">
	<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>주문자정보</b></font></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>이름</td>
		<td><?=htmlspecialchars($orderInfo['ORDER_OrdererName'])?></td>
	</tr>
	<tr>
		<td>아이디(네이버)</td>
		<td><?=htmlspecialchars($orderInfo['ORDER_OrdererID'])?></td>
	</tr>
	<tr>
		<td>연락처</td>
		<td><?=htmlspecialchars($orderInfo['ORDER_OrdererTel'])?></td>
	</tr>
	<tr>
		<td>메일 주소</td>
		<td><?=htmlspecialchars($orderInfo['ORDER_OrdererEmail'])?></td>
	</tr>
	</table>
</td>
<td width="50%" valign="top">
	<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>수령자 정보</b></font></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>이름</td>
		<td><?=htmlspecialchars($orderInfo['SHIPPING_Recipient'])?></td>
	</tr>
	<tr>
		<td>배송지 주소</td>
		<td>(<?=substr($orderInfo['SHIPPING_ZipCode'],0,3)?>-<?=substr($orderInfo['SHIPPING_ZipCode'],3,3)?>) <br>
		<?=$orderInfo['SHIPPING_ShippingAddress1']?> <?=htmlspecialchars($orderInfo['SHIPPING_ShippingAddress2'])?> </td>
	</tr>
	<tr>
		<td>연락처1</td>
		<td><?=htmlspecialchars($orderInfo['SHIPPING_RecipientTel1'])?></td>
	</tr>
	<tr>
		<td>연락처2</td>
		<td><?=htmlspecialchars($orderInfo['SHIPPING_RecipientTel2'])?></td>
	</tr>
	<tr>
		<td>배송 메세지</td>
		<td><?=htmlspecialchars($orderInfo['SHIPPING_ShippingMessage'])?></td>
	</tr>
	</table>
</td>
</table>


<br><br>
<table border="0" width="100%">
<tr>
<td width="50%"  valign="top">
	<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>배송정보</b></font></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>배송 일시</td>
		<td><?=dateOutput($orderInfo['DELIVERY_SendDate'])?></td>
	</tr>
	<tr>
		<td>집화 일시</td>
		<td><?=dateOutput($orderInfo['DELIVERY_PickupDate'])?></td>
	</tr>
	<tr>
		<td>배송 완료 일시</td>
		<td><?=dateOutput($orderInfo['DELIVERY_ShippingCompleteDate'])?></td>
	</tr>
	<tr>
		<td>배송 방법</td>
		<td><?=htmlspecialchars($orderInfo['DELIVERY_ShippingCompany'])?></td>
	</tr>
	<tr>
		<td>기타 배송</td>
		<td><?=htmlspecialchars($orderInfo['DELIVERY_EtcShipping'])?></td>
	</tr>
	<tr>
		<td>송장 번호</td>
		<td><?=htmlspecialchars($orderInfo['DELIVERY_TrackingNumber'])?></td>
	</tr>
	<tr>
		<td>상태 구분</td>
		<td><?=htmlspecialchars($orderInfo['DELIVERY_ShippingProcessStatus'])?></td>
	</tr>
	<tr>
		<td>배송 상태</td>
		<td><?=htmlspecialchars($orderInfo['DELIVERY_ShippingStatus'])?></td>
	</tr>
	</table>
</td>
<td width="50%" valign="top">
	<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>취소 정보</b></font></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>취소 사유</td>
		<td><?=htmlspecialchars($orderInfo['CANCEL_CancelReason'])?></td>
	</tr>
	<tr>
		<td>취소 요청 주체</td>
		<td><?=htmlspecialchars($orderInfo['CANCEL_CancelRequester'])?></td>
	</tr>
	<tr>
		<td>환불 대기 상태</td>
		<td><?=htmlspecialchars($orderInfo['CANCEL_RefundPended'])?></td>
	</tr>
	<tr>
		<td>환불 은행</td>
		<td><?=htmlspecialchars($orderInfo['CANCEL_RefundBank'])?></td>
	</tr>
	<tr>
		<td>환불 계좌주 이름</td>
		<td><?=htmlspecialchars($orderInfo['CANCEL_RefundAccountOwner'])?></td>
	</tr>
	<tr>
		<td>환불 계좌 번호</td>
		<td><?=htmlspecialchars($orderInfo['CANCEL_RefundAccountNumber'])?></td>
	</tr>
	<tr>
		<td>취소 신청 일시</td>
		<td><?=dateOutput($orderInfo['CANCEL_CancelRequestDate'])?></td>
	</tr>
	</table>
</td>
</table>


<br><br>
<table border="0" width="100%">
<tr>
<td width="50%"  valign="top">
	<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>반품정보</b></font></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
	<td>반품 사유</td>
	<td><?=htmlspecialchars($orderInfo['RETURN_ReturnReason'])?>
	</tr>
	<tr>
	<td>반품 진행 상태 코드</td>
	<td><?=htmlspecialchars($orderInfo['RETURN_ReturnStatusCode'])?>
	</tr>
	<tr>
	<td>반품 상태</td>
	<td><?=htmlspecialchars($orderInfo['RETURN_ReturnStatus'])?>
	</tr>
	<tr>
	<td>반송 일시</td>
	<td><?=dateOutput($orderInfo['RETURN_ReturnDate'])?>
	</tr>
	<tr>
	<td>반송 택배사</td>
	<td><?=$orderInfo['RETURN_ReturnShippingCompany']?>
	</tr>
	<tr>
	<td>반송 송장 번호</td>
	<td><?=htmlspecialchars($orderInfo['RETURN_ReturnTrackingNumber'])?>
	</tr>
	<tr>
	<td>반송 배송비 종류</td>
	<td><?=htmlspecialchars($orderInfo['RETURN_ReturnShippingFeeType'])?>
	</tr>
	<tr>
	<td>제품 입수 일시</td>
	<td><?=dateOutput($orderInfo['RETURN_ReceivedDate'])?>
	</tr>
	<tr>
	<td>환불 은행</td>
	<td><?=htmlspecialchars($orderInfo['RETURN_RefundBank'])?>
	</tr>
	<tr>
	<td>환불 계좌주 이름</td>
	<td><?=htmlspecialchars($orderInfo['RETURN_RefundAccountOwner'])?>
	</tr>
	<tr>
	<td>환불 계좌 번호</td>
	<td><?=htmlspecialchars($orderInfo['RETURN_RefundAccountNumber'])?>
	</tr>
	<tr>
	<td>이의 제기</td>
	<td><?=htmlspecialchars($orderInfo['RETURN_Protest'])?>
	</tr>
	<tr>
	<td>반품 신청 일시</td>
	<td><?=dateOutput($orderInfo['RETURN_ReturnRequestDate'])?>
	</tr>
	</table>
</td>
<td width="50%" valign="top">
	<div class="title2" style="margin:0px 0px 5px 0px">&nbsp;<img src="../img/icon_process.gif" align="absmiddle"><font color="508900"><b>교환정보</b></font></div>
	<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
	<td>교환 사유</td>
	<td><?=htmlspecialchars($orderInfo['EXCHANGE_ExchangeReason'])?></td>
	</tr>
	<tr>
	<td>교환 진행 상태 코드</td>
	<td><?=htmlspecialchars($orderInfo['EXCHANGE_ExchangeStatusCode'])?></td>
	</tr>
	<tr>
	<td>교환 상태</td>
	<td><?=htmlspecialchars($orderInfo['EXCHANGE_ExchangeStatus'])?></td>
	</tr>
	<tr>
	<td>반송 일시</td>
	<td><?=dateOutput($orderInfo['EXCHANGE_ReturnDate'])?></td>
	</tr>
	<tr>
	<td>반송 택배사</td>
	<td><?=htmlspecialchars($orderInfo['EXCHANGE_ReturnShippingCompany'])?></td>
	</tr>
	<tr>
	<td>반송 송장 번호</td>
	<td><?=htmlspecialchars($orderInfo['EXCHANGE_ReturnTrackingNumber'])?></td>
	</tr>
	<tr>
	<td>반송 배송비 종류</td>
	<td><?=htmlspecialchars($orderInfo['EXCHANGE_ReturnShippingFeeType'])?></td>
	</tr>
	<tr>
	<td>제품 입수 일시</td>
	<td><?=dateOutput($orderInfo['EXCHANGE_ReceivedDate'])?></td>
	</tr>
	<tr>
	<td>재배송 일시</td>
	<td><?=dateOutput($orderInfo['EXCHANGE_ResendDate'])?></td>
	</tr>
	<tr>
	<td>재배송 택배사</td>
	<td><?=htmlspecialchars($orderInfo['EXCHANGE_ResendShippingCompany'])?></td>
	</tr>
	<tr>
	<td>재배송 송장 번호</td>
	<td><?=htmlspecialchars($orderInfo['EXCHANGE_ResendTrackingNumber'])?></td>
	</tr>
	<tr>
	<td>이의 제기</td>
	<td><?=htmlspecialchars($orderInfo['EXCHANGE_Protest'])?></td>
	</tr>
	<tr>
	<td>교환 신청 일시</td>
	<td><?=dateOutput($orderInfo['EXCHANGE_ExchangeRequestDate'])?></td>
	</tr>
	<tr>
	<td>재배송 수취인 이름</td>
	<td><?=htmlspecialchars($orderInfo['EXCHANGE_ResendRecipient'])?></td>
	</tr>
	<tr>
	<td>재배송 연락처</td>
	<td><?=htmlspecialchars($orderInfo['EXCHANGE_ResendRecipientTel'])?></td>
	</tr>
	<tr>
	<td>재배송 주소</td>
	<td><?=htmlspecialchars($orderInfo['EXCHANGE_ResendShippingAddress'])?></td>
	</tr>
	</table>
</td>
</table>

<br><br>
<!--
<div style="text-align:center">
	<input type="button" value="주문정보 동기화" onclick="syncOrder()">
</div>-->
