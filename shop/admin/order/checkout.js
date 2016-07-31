/*** ���̾� �˾�â ���� ***/
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

	/*** ��׶��� ���̾� ***/
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

	/*** ���������� ���̾� ***/
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

	/*** Ÿ��Ʋ�� ���̾� ***/
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

	/*** ���������� ***/
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
* ���λ��� Ȱ��ȭ
*/
function iciSelect(obj) {
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F9FFA1" : row.getAttribute('bg');
	if($('tr_'+obj.value)) {
		$('tr_'+obj.value).style.background=row.style.background;
	}
}

/**
* ��ü����
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

		// ����Ȯ��
		if ((_data.ClaimType == '' || reject_pattern.test(_data.ClaimStatus) == true) && _data.ProductOrderStatus== 'PAYED' && _data.PlaceOrderStatus != 'OK') {
			buttons['el-fnPlaceProductOrder'] = true;
		}

		// �߼�����, �Ǹ���� (Ŭ���� ���� ���� �Ϸ�� ��, ����Ȯ���� �ȵȰ�)
		if ((_data.ClaimType == '' || reject_pattern.test(_data.ClaimStatus) == true) && _data.ProductOrderStatus== 'PAYED') {
			buttons['el-fnDelayProductOrder'] = true;
			buttons['el-fnCancelSale'] = true;
		}

		// �߼�ó�� (Ŭ���� ���� ���� �Ϸ�� ��, ����Ȯ�� �Ȱ�)
		if ((_data.ClaimType == '' || reject_pattern.test(_data.ClaimStatus) == true) && _data.ProductOrderStatus== 'PAYED' && _data.PlaceOrderStatus == 'OK') {
			buttons['el-fnShipProductOrder'] = true;
		}

		// ��ҿ�û���� (��ҿ�û ���°� �ƴѰ��)
		if (_data.ClaimType == 'CANCEL' && _data.ClaimStatus == 'CANCEL_REQUEST') {
			buttons['el-fnApproveCancelApplication'] = true;
		}

		// ��ǰ��û (Ŭ������ ����, ���/��ۿϷ� ���°� �ƴ� ���)
		if (_data.ClaimType == '' && (_data.ProductOrderStatus== 'DELIVERING' || _data.ProductOrderStatus== 'DELIVERED')) {
			buttons['el-fnRequestReturn'] = true;
		}

		// ��ǰ��û���� (��ǰ��û ���°� �ƴѰ��)
		if (_data.ClaimType == 'RETURN' && reject_pattern.test(_data.ClaimStatus) == false && _data.HoldbackReason == 'SELLER_CONFIRM_NEED') {
			buttons['el-fnApproveReturnApplication'] = true;
		}

		// ��ȯ��û���� (�������°� HOLDBACK �̸�, ���� ������ SELLER_CONFIRM_NEED �϶�)
		if (_data.ClaimType == 'EXCHANGE' && _data.HoldbackStatus == 'HOLDBACK' && _data.HoldbackReason == 'SELLER_CONFIRM_NEED') {
			buttons['el-fnApproveExchangeApplication'] = true;
		}

		// ��ȯ���ſϷ� (������ �̰ų�, ��ȯ ��û �����϶�)
		if (_data.ClaimType == 'EXCHANGE' && (_data.ClaimStatus == 'COLLECTING' || _data.ClaimStatus == 'EXCHANGE_REQUEST')) {
			buttons['el-fnApproveCollectedExchange'] = true;
		}

		// ��ȯ���� (���� �Ϸ��̸�, ���� ���°� �ƴ� ���)
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
		alert('���� ó���� �ֹ����� ������ �ּ���.');
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
		alert('�߼� ó���� �ֹ����� ������ �ּ���.');
		return false;
	}

	// ����Ʈ ���� �����, ��۹��, �ù�� , �����ȣ �ʵ尡 ������
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
					if ($$('input[name="DispatchDate['+idx+']"]').pop().value == '') throw '������� �Է��� �ּ���.';
					if ($$('select[name="DeliveryMethodCode['+idx+']"]').pop().value == '') throw '��۹���� ������ �ּ���.';

					if ($$('select[name="DeliveryMethodCode['+idx+']"]').pop().value == 'DELIVERY') {
						if ($$('select[name="DeliveryCompanyCode['+idx+']"]').pop().value == '') throw '�ù�縦 ������ �ּ���.';
						if ($$('input[name="TrackingNumber['+idx+']"]').pop().value == '') throw '�����ȣ�� �Է��� �ּ���.';
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
	// ������ (�˾��� ���)
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
		alert('�Ǹ���� ó���� �ֹ����� ������ �ּ���.');
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
		alert('�߼����� ó���� �ֹ����� ������ �ּ���.');
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
		alert('��ǰ���� ó���� �ֹ����� ������ �ּ���.');
		return false;
	}

	if (! confirm('��ǰ��û�� ��ȭ��ȭ�� ���� �������� ���Ǹ� ���� �� �������ּ���.\n�������� ���� ���� ��ǰ��ûó���� �Ͽ� �߻��ϴ� ������ �Ҹ����׿� ���ؼ��� �Ǹ��ڲ��� å�����ž� �մϴ�.\n���� �����ڸ� ����Ͽ� ��ǰ��û�� �Ͻðڽ��ϱ�?')) return false;

	customPopupLayer('about:blank',500,250);

	var f = document.frmNaverCheckout;

	f.mode.value = 'RequestReturn';
	f.action = './checkout.popup.requestreturn.php';
	f.submit();

}

function fnApproveCancelApplication() {
	if (! fnCheckedOrder()) {
		alert('��� ��û ���� ó���� �ֹ����� ������ �ּ���.');
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
		alert('��ȯ ��û ���� ó���� �ֹ����� ������ �ּ���.');
		return false;
	}

	// ó�� �Ұ����� ���� ���ԵǾ� �ִ���
	var _data;

	try
	{
		$$('input[name^="OrderID["]:checked').each(function(el) {
			_data = fnGetOrderStatus(el);
			if (!(_data.ClaimType == 'EXCHANGE' && _data.HoldbackStatus == 'HOLDBACK' && _data.HoldbackReason == 'SELLER_CONFIRM_NEED')) {
				throw '��ȯ ��û ���� ó���� �� ���� �ֹ����� ���ԵǾ� �ֽ��ϴ�.';
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
		alert('��ȯ ���� �Ϸ� ó���� �ֹ����� ������ �ּ���.');
		return false;
	}

	// ó�� �Ұ����� ���� ���ԵǾ� �ִ���
	var _data;

	try
	{
		$$('input[name^="OrderID["]:checked').each(function(el) {

			_data = fnGetOrderStatus(el);

			if (!(_data.ClaimType == 'EXCHANGE' && (_data.ClaimStatus == 'COLLECTING' || _data.ClaimStatus == 'EXCHANGE_REQUEST'))) {
				throw '��ȯ ���� �Ϸ� ó���� �� ���� �ֹ����� ���ԵǾ� �ֽ��ϴ�.';
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
		alert('��ȯ ���� ó���� �ֹ����� ������ �ּ���.');
		return false;
	}

	// ó�� �Ұ����� ���� ���ԵǾ� �ִ���
	var _data;

	try
	{
		$$('input[name^="OrderID["]:checked').each(function(el) {

			_data = fnGetOrderStatus(el);

			if (!(_data.ClaimType == 'EXCHANGE' && _data.ClaimStatus == 'COLLECT_DONE' && _data.HoldbackStatus != 'HOLDBACK')) {
				throw '��ȯ ���� ó���� �� ���� �ֹ����� ���ԵǾ� �ֽ��ϴ�.';
			}

			if (_data.ClaimType == 'EXCHANGE' && _data.HoldbackStatus == 'HOLDBACK' && _data.HoldbackReason == 'PURCHASER_CONFIRM_NEED') {
				throw '�����ڿ��� û���� ��Ÿ����� �������� �ʾ�, ���� �� �� ���� �ֹ����� ���ԵǾ� �ֽ��ϴ�.';
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
		alert('��ǰ ��û ���� ó���� �ֹ����� ������ �ּ���.');
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
		alert('��۹���� ������ �ֹ����� ������ ������ �ּ���.');
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