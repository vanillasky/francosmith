/**
* ��ǰ�Ϸ�ó��
*/
function indbReturn() {

	var f = document.frmList;

	// ó���� �ֹ��� üũ.
	if ($$('input[name^="chk["]:checked').size() < 1) {
		alert('ó���� �ֹ����� ������ �ּ���.');
		return;
	}
	else {
		/*$$('input[name^="chk["]:checked').each(function(el){
			if (el.name != 'chk[enamoo]') {

			}
		});*/
	}

	if($$('input[name^="chk["][data-ipay-pg="true"]:checked').size()>0)
	{
		if(confirm("�����Ͻ� �ֹ����߿� iPay PG���� �ֹ����� ���ԵǾ��ֽ��ϴ�.\r\niPay PG���� �ֹ����� ��ǰ�Ϸ� ó���� �ٷ� ȯ��ó�� �˴ϴ�.\r\n��� �����Ͻðڽ��ϱ�?")) {
			f.ord_status.value="31";
			f.submit();
		}
	}
	else
	{
		if(confirm("������ ��ǰó���� �Ͻðڽ��ϱ�?")) {
			f.ord_status.value="31";
			f.submit();
		}
	}
}

/**
* ��ȯ�Ϸ� �� ���ֹ� �ֱ�
*/
function indbExchange() {

	var f = document.frmList;

	// ó���� �ֹ��� üũ.
	var _tmp = true;
	if ($$('input[name^="chk["]:checked').size() < 1) {
		alert('ó���� �ֹ����� ������ �ּ���.');
		return;
	}
	else {
		$$('input[name^="chk["]:checked').each(function(el){
			if (el.name != 'chk[enamoo][]') {
				_tmp = false;
			}

		});
	}

	if (_tmp == false) {
		alert('���̹�üũ�ƿ�, ����, ����iPay �ֹ����� �ϰ� ó���� �Ұ��� �ϹǷ�, ������ �ǸŰ����� �̵��ϼż� ó���� �ּ���.');

		$$('input[name^="chk["]:checked').each(function(el){
			if (el.name != 'chk[enamoo][]') {
				el.checked = false;
				iciSelect(el);
			}

		});
		return false;
	}

	if($$('input[name^="chk["][data-ipay-pg="true"]:checked').size()>0)
	{
		alert("�����Ͻ� �ֹ��ǵ��� iPay PG���� �ֹ����� �ֽ��ϴ�.\r\niPay PG������ ������ �ֹ����� ��ȯó���� �Ұ��� �Ͽ���\r\n�ش�ǵ��� üũ�� �����Ͽ��ֽñ� �ٶ��ϴ�.\r\n(iPay PG���� �ֹ����� �ֹ���ȣ�ڿ� iPay�������� �ֽ��ϴ�.)");
		return;
	}
	else if($$('input[name^="chk["][data-naver-mileage="true"]:checked').size()>0)
	{
		alert("�����Ͻ� �ֹ����߿� ���̹� ���ϸ����� ���� �ֹ����� �ֽ��ϴ�.\r\n���̹� ���ϸ����� ���� �ֹ����� ��ȯó���� �Ұ��� �Ͽ���\r\n�ش��ֹ����� üũ�� �����Ͽ��ֽñ� �ٶ��ϴ�.");
		return;
	}
	else
	{
		if(confirm("������ ��ȯó���� �Ͻðڽ��ϱ�?")) {
			f.ord_status.value="41";
			f.submit();
		}
	}

}

function fnSetOrder(st) {

	var f = document.frmList;
	f.ord_status.value = st;

	// ó���� �ֹ��� üũ.
	if ($$('input[name^="chk["]:checked').size() < 1) {
		alert('ó���� �ֹ����� ������ �ּ���.');
		return;
	}

	// ó�� �ܰ躰 ���Ե��� ���ƾ��� ä���� �ֹ��� �ִ��� üũ
	var _tmp = true;
	switch (st)
	{
		case 3:
		case 2:
			break;
		default:
			$$('input[name^="chk["]:checked').each(function(el){
				if (el.name != 'chk[enamoo][]') {
					_tmp = false;
				}
			});
			break;
	}

	if (_tmp == false) {
		alert('���̹�üũ�ƿ�, ����, ����iPay �ֹ����� �ش� ���·� ������ �Ұ����մϴ�.');

		$$('input[name^="chk["]:checked').each(function(el){
			if (el.name != 'chk[enamoo][]') {
				el.checked = false;
				iciSelect(el);
			}

		});
		return false;
	}

	f.submit();
}


function fnDeliveryTrace(channel, code, dlvno) {
	var url = './popup.delivery.php?channel='+channel+'&code='+code+'&dlvno='+dlvno;
	popup(url,800,500);
}

function fnRequestSMS() {

	var f = document.frmList;

	// ó���� �ֹ��� üũ.
	if ($$('input[name^="chk["]:checked').size() < 1) {
		alert('ó���� �ֹ����� ������ �ּ���.');
		return;
	}

	// ó�� �ܰ躰 ���Ե��� ���ƾ��� ä���� �ֹ��� �ִ��� üũ
	f.mode.value = 'requestSMS';
	f.submit();

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
	$$(".chk_ordno").each(function(item){
		if(item.disabled==true) return;
		item.checked=chkBoxAll_flag;
		iciSelect(item);
	});
	chkBoxAll_flag=!chkBoxAll_flag;
}
/**
* �׷켱��
*/
var chkBoxGroup_flag=true;
function chkBoxGroup(k) {
	$$(".chk_ordno_"+k).each(function(item){
		if(item.disabled==true) return;
		item.checked=chkBoxGroup_flag;
		iciSelect(item);
	});
	chkBoxGroup_flag=!chkBoxGroup_flag;
}

/**
* �������� �ٿ�ε�
*/
function fnExcelDownload(mode)
{
	var fm = document.frmDnXls;
	fm.mode.value = mode;
	fm.target = "ifrmHidden";
	fm.action = "dnXls_integrate.php";
	fm.submit();
}

function fnOrderPrint(frmp_nm, frml_nm)
{
	var frmp = document.forms[frmp_nm];
	var frml = document.forms[frml_nm];
	if ( frmp['list_type'][0].checked != true && frmp['list_type'][1].checked != true ) return;

	if ( frmp['list_type'][0].checked == true && frmp['list_type'][0].value == 'list' ){
		if ($$('input[name="chk[enamoo][]"]:checked').size() < 1) {
			alert('������ ������ �����ϴ�.');
			return;
		}

		var ordnos = new Array();

		$$('input[name="chk[enamoo][]"]:checked').each(function(el, idx){
			ordnos[idx] = el.value;
		});

		frmp['ordnos'].value = ordnos.join(";");

		/*var cds = new Array();
		var idx = 0;
		var count=frml['chk[]'].length;

		if ( count == undefined ){
			if ( frml['chk[]'].ordno != null ) cds[ idx++ ] = frml['chk[]'].ordno;
			else cds[ idx++ ] = frml['chk[]'].value;
		}
		else
			for ( i = 0; i < count ; i++ )
				if ( frml['chk[]'][i].checked )
					if ( frml['chk[]'][i].ordno != null ) cds[ idx++ ] = frml['chk[]'][i].ordno;
					else cds[ idx++ ] = frml['chk[]'][i].value;

		frmp['ordnos'].value = cds.join( ";" );*/
	}

	var orderPrint = window.open("","orderPrint","width=750,height=600,menubar=yes,scrollbars=yes" );
	frmp.target='orderPrint';
	frmp.action='../order/_paper.php';
	frmp.submit();
	orderPrint.focus();
}

