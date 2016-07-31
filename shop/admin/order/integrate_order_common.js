/**
* 반품완료처리
*/
function indbReturn() {

	var f = document.frmList;

	// 처리할 주문건 체크.
	if ($$('input[name^="chk["]:checked').size() < 1) {
		alert('처리할 주문건을 선택해 주세요.');
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
		if(confirm("선택하신 주문건중에 iPay PG결제 주문건이 포함되어있습니다.\r\niPay PG결제 주문건은 반품완료 처리시 바로 환불처리 됩니다.\r\n계속 진행하시겠습니까?")) {
			f.ord_status.value="31";
			f.submit();
		}
	}
	else
	{
		if(confirm("정말로 반품처리를 하시겠습니까?")) {
			f.ord_status.value="31";
			f.submit();
		}
	}
}

/**
* 교환완료 후 재주문 넣기
*/
function indbExchange() {

	var f = document.frmList;

	// 처리할 주문건 체크.
	var _tmp = true;
	if ($$('input[name^="chk["]:checked').size() < 1) {
		alert('처리할 주문건을 선택해 주세요.');
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
		alert('네이버체크아웃, 쇼플, 옥션iPay 주문건은 일괄 처리가 불가능 하므로, 각각의 판매관리로 이동하셔서 처리해 주세요.');

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
		alert("선택하신 주문건들중 iPay PG결제 주문건이 있습니다.\r\niPay PG결제로 결제된 주문건은 교환처리가 불가능 하오니\r\n해당건들은 체크를 해제하여주시기 바랍니다.\r\n(iPay PG결제 주문건은 주문번호뒤에 iPay아이콘이 있습니다.)");
		return;
	}
	else if($$('input[name^="chk["][data-naver-mileage="true"]:checked').size()>0)
	{
		alert("선택하신 주문건중에 네이버 마일리지가 사용된 주문건이 있습니다.\r\n네이버 마일리지가 사용된 주문건은 교환처리가 불가능 하오니\r\n해당주문건의 체크를 해제하여주시기 바랍니다.");
		return;
	}
	else
	{
		if(confirm("정말로 교환처리를 하시겠습니까?")) {
			f.ord_status.value="41";
			f.submit();
		}
	}

}

function fnSetOrder(st) {

	var f = document.frmList;
	f.ord_status.value = st;

	// 처리할 주문건 체크.
	if ($$('input[name^="chk["]:checked').size() < 1) {
		alert('처리할 주문건을 선택해 주세요.');
		return;
	}

	// 처리 단계별 포함되지 말아야할 채널의 주문이 있는지 체크
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
		alert('네이버체크아웃, 쇼플, 옥션iPay 주문건은 해당 상태로 변경이 불가능합니다.');

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

	// 처리할 주문건 체크.
	if ($$('input[name^="chk["]:checked').size() < 1) {
		alert('처리할 주문건을 선택해 주세요.');
		return;
	}

	// 처리 단계별 포함되지 말아야할 채널의 주문이 있는지 체크
	f.mode.value = 'requestSMS';
	f.submit();

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
	$$(".chk_ordno").each(function(item){
		if(item.disabled==true) return;
		item.checked=chkBoxAll_flag;
		iciSelect(item);
	});
	chkBoxAll_flag=!chkBoxAll_flag;
}
/**
* 그룹선택
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
* 엑셀파일 다운로드
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
			alert('선택한 내역이 없습니다.');
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

