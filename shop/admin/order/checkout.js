/*** 레이어 팝업창 띄우기 ***/
function customPopupLayer(s,w,h)
{
	if (!w) w = 600;
	if (!h) h = 400;

	var pixelBorder = 3;
	var titleHeight = 12;
	w += pixelBorder * 2;
	h += pixelBorder * 2 + titleHeight;

	var bodyW = document.body.clientWidth;
	var bodyH = document.body.clientHeight;

	var posX = (bodyW - w) / 2;
	var posY = (bodyH - h) / 2;

	hiddenSelectBox('hidden');

	/*** 백그라운드 레이어 ***/
	var obj = document.createElement("div");
	with (obj.style){
		position = "absolute";
		left = 0;
		top = 0;
		width = "100%";
		height = document.body.scrollHeight;
		backgroundColor = "#000000";
		filter = "Alpha(Opacity=80)";
		opacity = "0.5";
	}
	obj.id = "objPopupLayerBg";
	document.body.appendChild(obj);

	/*** 내용프레임 레이어 ***/
	var obj = document.createElement("div");
	with (obj.style){
		position = "absolute";
		left = posX + document.body.scrollLeft;
		top = posY + document.body.scrollTop;
		width = w;
		height = h;
		backgroundColor = "#ffffff";
		border = "3px solid #000000";
	}
	obj.id = "objPopupLayer";
	document.body.appendChild(obj);

	/*** 타이틀바 레이어 ***/
	var bottom = document.createElement("div");
	with (bottom.style){
		position = "absolute";
		width = w - pixelBorder * 2;
		height = titleHeight;
		left = 0;
		top = h - titleHeight - pixelBorder * 3;
		padding = "4px 0 0 0";
		textAlign = "center";
		backgroundColor = "#000000";
		color = "#ffffff";
		font = "bold 8pt tahoma; letter-spacing:0px";

	}
	bottom.innerHTML = "<a href='javascript:closeLayer()' class='white'>X close</a>";
	obj.appendChild(bottom);

	/*** 아이프레임 ***/
	try {
		var ifrm = document.createElement("<iframe name='processLayerForm' scrolling=\"no\"></iframe>");
	}
	catch (e1) {
		obj.innerHTML += '<iframe name="processLayerForm" scrolling="no"></iframe>';
		var ifrm = obj.childNodes[obj.childNodes.length-1];
	}
	with (ifrm.style){
		width = w - 6;
		height = h - pixelBorder * 2 - titleHeight - 3;
		//border = "3 solid #000000";
	}
	ifrm.frameBorder = 0;
	ifrm.src = s;
	//ifrm.className = "scroll";
	obj.appendChild(ifrm);
}

var chkAllOrderNOFlag=true;
function chkAllOrderNO() {
	$$('input[name^="ProductOrderID["]').each(function(item){
		item.checked=chkAllOrderNOFlag;
	});
	chkAllOrderNOFlag=!chkAllOrderNOFlag;
}

/**
* 라인색상 활성화
*/
function iciSelect(obj) {
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F9FFA1" : row.getAttribute('bg');
	if($('tr_'+obj.value)) {
		$('tr_'+obj.value).style.background=row.style.background;
	}
}

/**
* 전체선택
*/
var chkBoxAll_flag=true;
function chkBoxAll() {
	$$(".el-OrderID").each(function(item){
		if(item.disabled==true) return;
		item.checked=chkBoxAll_flag;
		iciSelect(item);
	});
	chkBoxAll_flag=!chkBoxAll_flag;
}

/*
 * ======================================================================================
 * ======================================================================================
 * ======================================================================================
 * ======================================================================================
 * ======================================================================================
 * ======================================================================================
 */

function fnSetAvailableOperationButton(el) {

	var _data;

	var buttons = {
	'el-fnPlaceProductOrder':false,
	'el-fnDelayProductOrder':false,
	'el-fnShipProductOrder':false,
	'el-fnCancelSale':false,
	'el-fnApproveCancelApplication':false,
	'el-fnRequestReturn':false,
	'el-fnApproveReturnApplication':false,
	'el-fnApproveExchangeApplication':false,
	'el-fnApproveCollectedExchange':false,
	'el-fnReDeliveryExchange':false
	};

	var reject_pattern = /_REJECT$/;

	$$('input[name^="OrderID["]:checked').each(function(el) {

		_data = fnGetOrderStatus(el);

		// 발주확인
		if ((_data.ClaimType == '' || reject_pattern.test(_data.ClaimStatus) == true) && _data.ProductOrderStatus== 'PAYED' && _data.PlaceOrderStatus != 'OK') {
			buttons['el-fnPlaceProductOrder'] = true;
		}

		// 발송지연, 판매취소 (클레임 없는 결제 완료건 중, 발주확인이 안된건)
		if ((_data.ClaimType == '' || reject_pattern.test(_data.ClaimStatus) == true) && _data.ProductOrderStatus== 'PAYED') {
			buttons['el-fnDelayProductOrder'] = true;
			buttons['el-fnCancelSale'] = true;
		}

		// 발송처리 (클레임 없는 결제 완료건 중, 발주확인 된건)
		if ((_data.ClaimType == '' || reject_pattern.test(_data.ClaimStatus) == true) && _data.ProductOrderStatus== 'PAYED' && _data.PlaceOrderStatus == 'OK') {
			buttons['el-fnShipProductOrder'] = true;
		}

		// 취소요청승인 (취소요청 상태가 아닌경우)
		if (_data.ClaimType == 'CANCEL' && _data.ClaimStatus == 'CANCEL_REQUEST') {
			buttons['el-fnApproveCancelApplication'] = true;
		}

		// 반품신청 (클레임이 없는, 배송/배송완료 상태가 아닌 경우)
		if (_data.ClaimType == '' && (_data.ProductOrderStatus== 'DELIVERING' || _data.ProductOrderStatus== 'DELIVERED')) {
			buttons['el-fnRequestReturn'] = true;
		}

		// 반품요청승인 (반품요청 상태가 아닌경우)
		if (_data.ClaimType == 'RETURN' && reject_pattern.test(_data.ClaimStatus) == false && _data.HoldbackReason == 'SELLER_CONFIRM_NEED') {
			buttons['el-fnApproveReturnApplication'] = true;
		}

		// 교환요청승인 (보류상태가 HOLDBACK 이며, 보류 사유가 SELLER_CONFIRM_NEED 일때)
		if (_data.ClaimType == 'EXCHANGE' && _data.HoldbackStatus == 'HOLDBACK' && _data.HoldbackReason == 'SELLER_CONFIRM_NEED') {
			buttons['el-fnApproveExchangeApplication'] = true;
		}

		// 교환수거완료 (수거중 이거나, 교환 요청 상태일때)
		if (_data.ClaimType == 'EXCHANGE' && (_data.ClaimStatus == 'COLLECTING' || _data.ClaimStatus == 'EXCHANGE_REQUEST')) {
			buttons['el-fnApproveCollectedExchange'] = true;
		}

		// 교환재배송 (수거 완료이며, 보류 상태가 아닌 경우)
		if (_data.ClaimType == 'EXCHANGE' && _data.ClaimStatus == 'COLLECT_DONE' && _data.HoldbackStatus != 'HOLDBACK') {
			buttons['el-fnReDeliveryExchange'] = true;
		}

	});

	var btn;

	$H(buttons).each(function(pair){

		btn = $(pair.key);

		if (pair.value)
		{
			btn.removeClassName('default-btn-off');	btn.removeClassName('default-btn');	btn.addClassName('default-btn');
			btn.disabled = false;
		}
		else {
			btn.removeClassName('default-btn-off');	btn.removeClassName('default-btn');	btn.addClassName('default-btn-off');
			btn.disabled = true;
		}
	});

}

function fnCheckedOrder() {
	var chks = 	$$('input[name^="OrderID["]:checked');
	return chks.size() > 0 ? true : false;
}

function fnGetOrderStatus($el) {

	var data_pattern = /({.+?})/;
	var data_value = {};

	try
	{
		data_value = $el.classNames().toString().match(data_pattern).pop().evalJSON();
	}
	catch (e)
	{
		data_value = {};
	}

	return data_value;
}

function fnPlaceProductOrder() {

	if (! fnCheckedOrder()) {
		alert('발주 처리할 주문건을 선택해 주세요.');
		return false;
	}

	customPopupLayer('about:blank',500,400);

	var f = document.frmNaverCheckout;

	f.mode.value = 'PlaceProductOrder';
	f.action = './checkout.api.process.php';
	f.submit();

}

function fnShipProductOrder() {
	if (! fnCheckedOrder()) {
		alert('발송 처리할 주문건을 선택해 주세요.');
		return false;
	}

	// 리스트 내에 배송일, 배송방법, 택배사 , 송장번호 필드가 있을때
	if (
		$$('input[name^="DispatchDate["]').size() > 0
		/* && $$('select[name^="DeliveryMethodCode["]').size() > 0
		&& $$('select[name^="DeliveryCompanyCode["]').size() > 0
		&& $$('input[name^="TrackingNumber["]').size() > 0*/ ) {

		try
		{
			var idx = 0;
			$$('input[name^="OrderID["]').each(function(el) {
				idx++;
				if (el.checked) {
					if ($$('input[name="DispatchDate['+idx+']"]').pop().value == '') throw '배송일을 입력해 주세요.';
					if ($$('select[name="DeliveryMethodCode['+idx+']"]').pop().value == '') throw '배송방법을 선택해 주세요.';

					if ($$('select[name="DeliveryMethodCode['+idx+']"]').pop().value == 'DELIVERY') {
						if ($$('select[name="DeliveryCompanyCode['+idx+']"]').pop().value == '') throw '택배사를 선택해 주세요.';
						if ($$('input[name="TrackingNumber['+idx+']"]').pop().value == '') throw '송장번호를 입력해 주세요.';
					}
				}
			});
		}
		catch (e) {
			if (typeof e == 'string') alert(e);
			return false;
		}

		customPopupLayer('about:blank',500,400);

		var f = document.frmNaverCheckout;

		f.mode.value = 'ShipProductOrder';
		f.action = './checkout.api.process.php';
		f.submit();

	}
	else {
	// 없을때 (팝업을 띄움)
		customPopupLayer('about:blank',500,400);

		var f = document.frmNaverCheckout;

		f.mode.value = 'ShipProductOrder';
		f.action = './checkout.popup.shipproductorder.php';
		f.submit();
	}

	return;

}

function fnCancelSale() {
	if (! fnCheckedOrder()) {
		alert('판매취소 처리할 주문건을 선택해 주세요.');
		return false;
	}

	customPopupLayer('about:blank',500,400);

	var f = document.frmNaverCheckout;

	f.mode.value = 'CancelSale';
	f.action = './checkout.popup.cancelsale.php';
	f.submit();

}

function fnDelayProductOrder() {
	if (! fnCheckedOrder()) {
		alert('발송지연 처리할 주문건을 선택해 주세요.');
		return false;
	}

	customPopupLayer('about:blank',500,400);

	var f = document.frmNaverCheckout;

	f.mode.value = 'DelayProductOrder';
	f.action = './checkout.popup.delayproductorder.php';
	f.submit();

}

function fnRequestReturn() {
	if (! fnCheckedOrder()) {
		alert('반품접수 처리할 주문건을 선택해 주세요.');
		return false;
	}

	if (! confirm('반품신청은 전화통화를 통해 구매자의 동의를 얻은 후 진행해주세요.\n구매자의 동의 없이 반품신청처리를 하여 발생하는 구매자 불만사항에 대해서는 판매자께서 책임지셔야 합니다.\n지금 구매자를 대신하여 반품신청을 하시겠습니까?')) return false;

	customPopupLayer('about:blank',500,250);

	var f = document.frmNaverCheckout;

	f.mode.value = 'RequestReturn';
	f.action = './checkout.popup.requestreturn.php';
	f.submit();

}

function fnApproveCancelApplication() {
	if (! fnCheckedOrder()) {
		alert('취소 요청 승인 처리할 주문건을 선택해 주세요.');
		return false;
	}

	customPopupLayer('about:blank',500,400);

	var f = document.frmNaverCheckout;

	f.mode.value = 'ApproveCancelApplication';
	f.action = './checkout.popup.claim.php';
	f.submit();

}

function fnApproveExchangeApplication() {
	if (! fnCheckedOrder()) {
		alert('교환 요청 승인 처리할 주문건을 선택해 주세요.');
		return false;
	}

	// 처리 불가능한 건이 포함되어 있는지
	var _data;

	try
	{
		$$('input[name^="OrderID["]:checked').each(function(el) {
			_data = fnGetOrderStatus(el);
			if (!(_data.ClaimType == 'EXCHANGE' && _data.HoldbackStatus == 'HOLDBACK' && _data.HoldbackReason == 'SELLER_CONFIRM_NEED')) {
				throw '교환 요청 승인 처리할 수 없는 주문건이 포함되어 있습니다.';
			}

		});
	}
	catch (e) {
		if (typeof e == 'string') alert(e);
		return false;
	}

	customPopupLayer('about:blank',500,400);

	var f = document.frmNaverCheckout;

	f.mode.value = 'ApproveExchangeApplication';
	f.action = './checkout.popup.claim.php';
	f.submit();

}

function fnApproveCollectedExchange() {
	if (! fnCheckedOrder()) {
		alert('교환 수거 완료 처리할 주문건을 선택해 주세요.');
		return false;
	}

	// 처리 불가능한 건이 포함되어 있는지
	var _data;

	try
	{
		$$('input[name^="OrderID["]:checked').each(function(el) {

			_data = fnGetOrderStatus(el);

			if (!(_data.ClaimType == 'EXCHANGE' && (_data.ClaimStatus == 'COLLECTING' || _data.ClaimStatus == 'EXCHANGE_REQUEST'))) {
				throw '교환 수거 완료 처리할 수 없는 주문건이 포함되어 있습니다.';
			}
		});
	}
	catch (e) {
		if (typeof e == 'string') alert(e);
		return false;
	}

	customPopupLayer('about:blank',500,400);

	var f = document.frmNaverCheckout;

	f.mode.value = 'ApproveCollectedExchange';
	f.action = './checkout.api.process.php';
	f.submit();

}

function fnReDeliveryExchange() {
	if (! fnCheckedOrder()) {
		alert('교환 재배송 처리할 주문건을 선택해 주세요.');
		return false;
	}

	// 처리 불가능한 건이 포함되어 있는지
	var _data;

	try
	{
		$$('input[name^="OrderID["]:checked').each(function(el) {

			_data = fnGetOrderStatus(el);

			if (!(_data.ClaimType == 'EXCHANGE' && _data.ClaimStatus == 'COLLECT_DONE' && _data.HoldbackStatus != 'HOLDBACK')) {
				throw '교환 재배송 처리할 수 없는 주문건이 포함되어 있습니다.';
			}

			if (_data.ClaimType == 'EXCHANGE' && _data.HoldbackStatus == 'HOLDBACK' && _data.HoldbackReason == 'PURCHASER_CONFIRM_NEED') {
				throw '구매자에게 청구된 기타비용이 결제되지 않아, 재배송 할 수 없는 주문건이 포함되어 있습니다.';
			}
		});
	}
	catch (e) {
		if (typeof e == 'string') alert(e);
		return false;
	}

	customPopupLayer('about:blank',500,400);

	var f = document.frmNaverCheckout;

	f.mode.value = 'ReDeliveryExchange';
	f.action = './checkout.popup.redelivery.php';
	f.submit();

}

function fnApproveReturnApplication() {
	if (! fnCheckedOrder()) {
		alert('반품 요청 승인 처리할 주문건을 선택해 주세요.');
		return false;
	}

	customPopupLayer('about:blank',500,400);

	var f = document.frmNaverCheckout;

	f.mode.value = 'ApproveReturnApplication';
	f.action = './checkout.popup.claim.php';
	f.submit();

}

function fnChangedDeliveryMethodCode() {
	if ($('el-DeliveryMethodCode').value == 'DELIVERY') {
		$('el-DeliveryCompanyCode').disabled = false;

	}
	else {
		$('el-DeliveryCompanyCode').disabled = true;
	}
}

function fnApplyDeliveryCodes() {

	if (! fnCheckedOrder()) {
		alert('배송방법을 적용할 주문건을 선택해 선택해 주세요.');
		return false;
	}

	var m_si = $('el-DeliveryMethodCode').selectedIndex;	// Method selectedIndex
	var c_si = $('el-DeliveryCompanyCode').selectedIndex;	// Code selectedIndex;

	var idx = 0;

	$$('input[name^="OrderID["]').each(function(el){
		idx++;

		if (el.checked) {
			$$('select[name="DeliveryMethodCode['+idx+']"]').pop().selectedIndex = m_si;

			if (m_si === 1)
				$$('select[name="DeliveryCompanyCode['+idx+']"]').pop().selectedIndex = c_si;
			else
				$$('select[name="DeliveryCompanyCode['+idx+']"]').pop().selectedIndex = 0;

		}

	});

}