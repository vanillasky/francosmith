/*** WebTax21 REQUEST SEND (WRS) ***/
WRS = {

	isNull : function (tVar)
	{
		if (tVar == null || tVar == '') return false;
		return true;
	},

	init_set : function ()
	{
		if (document.getElementById('avoidSubmit') && !document.getElementById('avoidMsg') )
		{
			sendDiv = document.getElementById('avoidSubmit');
			msgDiv = sendDiv.parentNode.insertBefore( sendDiv.cloneNode(true), sendDiv );
			msgDiv.id = 'avoidMsg';
			msgDiv.style.letterSpacing = '0px';
			msgDiv.innerHTML = '페이지 로딩 중입니다. 잠시만 기다려주세요.';
		}
		sendDiv.style.display = 'none';
		msgDiv.style.display = 'block';

		fobj = document.forms['form'];
		fobj['compName'].value = param['compName'];
		fobj['ceoName'].value = param['ceoName'];
		fobj['compSerial'].value = param['compSerial'];
		fobj['service'].value = param['service'];
		fobj['item'].value = param['item'];
		fobj['email'].value = param['email'];
		fobj['phone[]'][0].value = param['phone'][0] == null ? '' : param['phone'][0];
		fobj['phone[]'][1].value = param['phone'][1] == null ? '' : param['phone'][1];
		fobj['phone[]'][2].value = param['phone'][2] == null ? '' : param['phone'][2];
		fobj['address'].value = param['address'];
		this.isExists();
	},

	isExists : function ()
	{
		var urlStr = "../order/tax_indb.php?mode=isExists&godosno=" + fobj['godosno'].value + "&dummy=" + new Date().getTime();
		var ajax = new Ajax.Request( urlStr,
		{
			method: "get",
			onComplete: function ()
			{
				var req = ajax.transport;
				if ( req.status == 200 && req.responseText == 'true' )
						msgDiv.innerHTML = '[가입여부 검사] 이미 가입한 상점입니다.';
				else if ( req.status == 200 && req.responseText != 'true' )
				{
					sendDiv.style.display = 'block';
					msgDiv.style.display = 'none';
				}
				else {
					var msg = req.getResponseHeader("Status");
					if ( msg == null || msg.length == null || msg.length <= 0 ) msg = 'Error! Request status is ' + req.status;
					msgDiv.innerHTML = '[가입여부 검사 실패] ' + msg + '';
				}
			}
		} );
	},

	request : function ()
	{
		if (chkForm(fobj) === false) return false;
		if (document.forms['webtax21_form'] == null)
		{
			var dNode = document.createElement('div');
			document.body.appendChild(dNode);
			dNode.innerHTML = '\
			<form method="post" name="webtax21_form">\n\
			<input type="hidden" name="func" value="app_webhost">\n\
			<input type="hidden" name="sub" value="GODO">\n\
			<input type="hidden" name="userid">\n\
			<input type="hidden" name="password">\n\
			<input type="hidden" name="regno">\n\
			<input type="hidden" name="company">\n\
			<input type="hidden" name="name">\n\
			<input type="hidden" name="condition">\n\
			<input type="hidden" name="items">\n\
			<input type="hidden" name="email">\n\
			<input type="hidden" name="tell">\n\
			<input type="hidden" name="cell">\n\
			<input type="hidden" name="address">\n\
			<input type="hidden" name="return_url">\n\
			</form>\n\
			';
		}
		wtfobj = document.forms['webtax21_form'];
		wtfobj['userid'].value = fobj['userid'].value;
		wtfobj['password'].value = fobj['password'].value;
		wtfobj['regno'].value = fobj['compSerial'].value;
		wtfobj['company'].value = fobj['compName'].value;
		wtfobj['name'].value = fobj['ceoName'].value;
		wtfobj['condition'].value = fobj['service'].value;
		wtfobj['items'].value = fobj['item'].value;
		wtfobj['email'].value = fobj['email'].value;
		wtfobj['tell'].value = fobj['phone[]'][0].value + '-' + fobj['phone[]'][1].value + '-' + fobj['phone[]'][2].value;
		wtfobj['cell'].value = fobj['mobile[]'][0].value + '-' + fobj['mobile[]'][1].value + '-' + fobj['mobile[]'][2].value;
		wtfobj['address'].value = fobj['address'].value;
		wtfobj['return_url'].value = param['return_url'];
		wtfobj.target = "ifrmHidden";
		wtfobj.action = "http://www.webtax21.com/webtax21/webtax";
		wtfobj.submit();
		msgDiv.innerHTML = '전자세금계산서 가입 처리 중입니다';
		sendDiv.style.display = 'none';
		msgDiv.style.display = 'block';
		return false;
	},

	receive_err : function (msg)
	{
		msgDiv.innerHTML = '<font color="#bf0000"><b>[가입실패] ' + msg + '</b></font>';
		sendDiv.style.display = 'block';
		msgDiv.style.display = 'block';
	},

	receive : function (getParam)
	{
		var getParam = eval( '(' + getParam + ')' );
		if (this.isNull(getParam['userid']) === false) return this.receive_err('상점아이디가 정상적으로 반환되지 않았습니다.');
		if (this.isNull(getParam['regno']) === false) return this.receive_err('사업자번호가 정상적으로 반환되지 않았습니다.');
		if (getParam['userid'] != wtfobj['userid'].value) return this.receive_err('상점아이디가 반환된 상점아이디와 일치하지 않습니다.');
		if (getParam['regno'] != wtfobj['regno'].value) return this.receive_err('사업자번호가 반환된 사업자번호와 일치하지 않습니다.');
		if (getParam['result'] != 'succ'){
			result = getParam['result'].replace(/fail:/, '');
			var msg = '<span style="font-weight:normal;">고도 e나무의 LG데이콤webtax21 전자세금계산서를 사용하시기 위해서는<br>\
				기존의 회원정보를 없엔후 본 페이지에서 다시 가입해 주셔야 합니다.<br>\
				자세한 사항은 LG데이콤webtax21 고객센터(1644-7882)로 문의해 주시면 친절히 안내해 드리겠습니다.<br>\
				(LG데이콤webtax21 고객센터와 통화가 어려우시면 고도 02-567-3722로 연락해 주십시오)</span>';
			if (result == 'userid') return this.receive_err('"CGO_' + wtfobj['userid'].value + '" 상점아이디는 이미 사용중인 아이디입니다.');
			if (result == 'regnoU') return this.receive_err('사업자번호 "' + wtfobj['regno'].value + '" 은 LG데이콤webtax21의 준회원으로 이미 가입되어 있습니다.<br>' + msg);
			if (result == 'regnoT' || result == 'regnoTV' || result == 'regnoTW') return this.receive_err('사업자번호 "' + wtfobj['regno'].value + '" 은 LG데이콤webtax21의 정회원으로 이미 가입되어 있습니다.<br>' + msg);
		}
		this.putMerchant();
	},

	putMerchant : function ()
	{
		msgDiv.innerHTML = '전자세금계산서 가입정보 저장 중입니다. 잠시만 기다려주세요.';
		var urlStr = "../order/tax_indb.php?mode=putMerchant&" + decodeURIComponent( Form.serialize(fobj) ) + "&dummy=" + new Date().getTime();
		var ajax = new Ajax.Request( urlStr,
		{
			method: "get",
			onComplete: function ()
			{
				var req = ajax.transport;
				if ( req.status == 200 && req.responseText == 'true' )
				{
					msgDiv.innerHTML = "전자세금계산서 가입이 정상적으로 이루어졌습니다.";
					alert('전자세금계산서 가입이 정상적으로 이루어졌습니다.');
					document.location.replace( '../order/etax.pay.php' );
				}
				else {
					var msg = req.getResponseHeader("Status");
					if ( msg == null || msg.length == null || msg.length <= 0 ) msg = 'Error! Request status is ' + req.status + '{RESAVE}';
					msg = msg.replace(/{RESAVE}/, ' <a href="javascript:WRS.putMerchant()" style="text-decoration:underline; color:blue;">저장 재시도하기</a>');
					msgDiv.innerHTML = '[저장 실패] ' + msg;
				}
			}
		} );
	}
}





/*** TAX POINT RESERVE (TPR) ***/
TPR = {

	init_set : function ()
	{
		if (document.getElementById('avoidSubmit') && !document.getElementById('avoidMsg') )
		{
			sendDiv = document.getElementById('avoidSubmit');
			msgDiv = sendDiv.parentNode.insertBefore( sendDiv.cloneNode(true), sendDiv );
			msgDiv.id = 'avoidMsg';
			msgDiv.style.letterSpacing = '0px';
			msgDiv.innerHTML = '페이지 로딩 중입니다. 잠시만 기다려주세요.';
		}
		msgDiv.style.display = 'block';
	},

	popupPay : function (fobj)
	{
		this.init_set();

		var urlStr = "../order/tax_indb.php?mode=isExists&godosno=" + fobj['sno'].value + "&dummy=" + new Date().getTime();
		var ajax = new Ajax.Request( urlStr,
		{
			method: "get",
			onComplete: function ()
			{
				var req = ajax.transport;
				if ( req.status == 200 && req.responseText == 'true' )
				{
					msgDiv.style.display = 'none';
					window.open("","popupPay","width=500,height=450");
					fobj.action = "http://www.godo.co.kr/userinterface/_godoConn/vaspay.php";
					fobj.target = "popupPay";
					fobj.submit();
				}
				else if ( req.status == 200 && req.responseText != 'true' )
					msgDiv.innerHTML = '[가입여부 검사] 전자세금계산서(WebTax21) 가입을 먼저 하세요.';
				else {
					var msg = req.getResponseHeader("Status");
					if ( msg == null || msg.length == null || msg.length <= 0 ) msg = 'Error! Request status is ' + req.status;
					msgDiv.innerHTML = '[가입여부 검사 실패] ' + msg + '';
				}
			}
		} );
		return false;
	}
}





/*** TAX APPLICATION MANAGEMENT (TAM) ***/
TAM = {

	iciSelect : function (obj) // 라인 배경색
	{
		var row = obj.parentNode.parentNode;
		row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
		var row = row.parentNode.rows[(row.rowIndex + 1)];
		row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
	},

	act_allmodify : function () // 일괄수정
	{
		if ( fmList['chk[]'] == null || PubChkSelect( fmList['chk[]'] ) == false ){
			alert( "수정하실 내역을 선택하여 주십시요." );
			return;
		}

		fmList.action = "../order/tax_indb.php?mode=allmodify";
		fmList.submit() ;
	},

	act_delete : function () // 삭제
	{
		if ( fmList['chk[]'] == null || PubChkSelect( fmList['chk[]'] ) == false ){
			alert( "삭제하실 내역을 선택하여 주십시요." );
			return;
		}

		if ( confirm( "선택한 아이템을 정말 삭제하시겠습니까?\n삭제 후 복구할 수 없습니다." ) == false ) return;

		fmList.action = "../order/tax_indb.php?mode=delete" ;
		fmList.submit() ;
	},

	act_agree : function () // 승인
	{
		var chk = document.getElementsByName('chk[]');
		for (i = 0; i < chk.length; i++){
			if (chk[i].checked && chk[i].getAttribute('cashreceipt') != ''){
				alert( "현금영수증이 발행된 내역입니다.\n현금영수증과 세금계산서는 동시에 발급되어 질 수 없습니다." );
				chk[i].focus();
				return;
			}
		}

		if ( fmList['chk[]'] == null || PubChkSelect( fmList['chk[]'] ) == false ){
			alert( "승인하실 내역을 선택하여 주십시요." );
			return;
		}

		fmList.action = "../order/tax_indb.php?mode=agree" ;
		fmList.submit() ;
	},

	putTax : function( idx ) // 공급액,부가세 계산
	{
		var price = eval( fmList['price[' + idx + ']'].value );
		if( !price ) price = 0;

		var supply	= Math.round( price / 1.1 );
		var surtax	= price - supply;

		fmList["supply[" + idx + "]"].value	= supply;
		fmList["surtax[" + idx + "]"].value	= surtax;
	}
}





/*** TAX ISSUE MANAGEMENT (TIM) ***/
TIM = {

	iciSelect : function (obj) { TAM.iciSelect(obj); },  // 라인 배경색
	act_delete : function () { TAM.act_delete(); }, // 삭제

	dnXls : function () // 엑셀파일다운
	{
		var fm = document.frmDnXls;
		fm.target = "ifrmHidden";
		fm.action = "../order/tax_dnXls.php";
		fm.submit();
	}
}





/*** WebTax21 TAXBILL SEND (WTS) ***/
WTS =  {
	begin: function ()
	{
		AGM.act({'onStart' : this.startCallback, 'onRequest' : this.requestCallback, 'onComplete' : 0, 'onErrorCallback' : 0});
	},

	startCallback: function (grp)
	{
		grp.layoutTitle = "세금계산서 발행요청중 ...";
		grp.bMsg['chkEmpty'] = "발행요청할 내역이 없습니다.";
		grp.bMsg['chkCount'] = "총 __count__개의 내역을 발행요청하셨습니다.";
		grp.bMsg['start'] = "세금계산서 발행요청을 시작합니다.";
		grp.bMsg['end'] = "세금계산서 발행요청이 종료되었습니다.";

		grp.articles = new Array();
		grp.iobj = new Array();
		grp.iobj.push(document.getElementsByName('chk[]'));

		var count = grp.iobj[0].length;
		for (idx = 0; idx < count ; idx++)
			if (grp.iobj[0][idx].checked === true) grp.articles.push(idx);
	},

	requestCallback: function (grp, idx)
	{
		var query = '&' + grp.iobj[0][idx].name.replace(/\[\]/,'') + '=' + grp.iobj[0][idx].value;
		var urlStr = "../order/tax_indb.php?mode=putTaxbill" + query + "&dummy=" + new Date().getTime();
		var ajax = new Ajax.Request( urlStr,
		{
			method: "get",
			onComplete: function ()
			{
				var req = ajax.transport;
				if (req.status == 200)
				{
					var subObj = grp.iobj[0][idx];
					var rows = new Array();
					rows.push(subObj.parentNode.parentNode);
					rows.push(subObj.parentNode.parentNode.parentNode.rows[(subObj.parentNode.parentNode.rowIndex + 1)]);
					for (i = 0; i < rows.length; i++)
					{
						rows[i].style.backgroundColor = "#ffffff";
						var inputs = rows[i].getElementsByTagName('input');
						for (j = 0; j < inputs.length; j++)
						{
							if (inputs[j].type != "checkbox"){
								inputs[j].readOnly = true;
								inputs[j].style.backgroundColor = "#DDDDDD";
							}
							else {
								inputs[j].disabled = true;
								inputs[j].checked = false;
							}
						}
					}
					rows[0].cells[rows[0].cells.length - 1].innerHTML = '<font color=EA0095><b>전자발행</b></font>';
					grp.complete(req);
				}
				else grp.error(req);
			}
		} );
	}
}





/*** Taxbill 정보 출력 ***/
function getTaxbill( doc_number, idnm )
{
	var trObj1 = document.getElementById(idnm);
	var trObj2 = trObj1.parentNode.rows[(trObj1.rowIndex + 1)];
	var r1c1 = trObj1.cells[(trObj1.cells.length - 2)];
	var r1c2 = trObj1.cells[(trObj1.cells.length - 1)];
	var r2c1 = trObj2.cells[(trObj2.cells.length - 1)];

	var urlStr = "../order/tax_indb.php?mode=getTaxbill&doc_number=" + doc_number + "&dummy=" + new Date().getTime();
	var ajax = new Ajax.Request( urlStr,
	{
		method: "get",
		onComplete: function ()
		{
			var req = ajax.transport;
			if (req.status == 200)
			{
				var jsonData = eval( '(' + req.responseText + ')' );

				r1c1.innerHTML = (jsonData.mtsid != null ? '<font class=small color=444444>' + jsonData.mtsid + '</font>' : '―');
				if (jsonData.err_msg == null || jsonData.status != 'ERR')
				{
					r1c2.innerHTML = '<font color="#EA0095"><b>' + (jsonData.status_txt != null ? jsonData.status_txt : '') + '</b></font>';
					if (jsonData.status == 'RDY' || jsonData.status == 'SND' || jsonData.status == 'RCV' || jsonData.status == 'ACK'){
						r1c2.innerHTML += '<div><img src="../img/i_cancel.gif" style="cursor:pointer;" onclick="appCancel(this, \''+ jsonData.status +'\', \''+ jsonData.doc_number +'\')"></div>';
					}
				}
				else {
					r1c2.innerHTML = '<a href="javascript:alert(\'' + jsonData.err_msg + '\')"><font color="#EA0095"><b>' + (jsonData.status_txt != null ? jsonData.status_txt : '') + '</b></font></a>';
					r1c2.title = jsonData.err_msg;
				}
				r2c1.innerHTML = (jsonData.act_tm != null ? '<font class=small color=444444>' + jsonData.act_tm.replace(/ /,'<br>') + '</font>' : '―');
			}
			else {
				var msg = req.getResponseHeader("Status");
				r1c2.title = msg;
				r1c2.innerHTML ='<font class=small color=444444>로딩중에러</font>';
			}
		}
	} );
}






/** 발행취소 **/
function appCancel(obj, status, doc_number)
{
	if (status == 'SND' || status == 'RCV'){
		var msg = '발행을 취소하시겠습니까?';
	}
	else if (status == 'ACK'){
		var msg = '승인을 취소요청하시겠습니까?'+"\n"+'공급받는자가 취소 확인을 할때까지 시간이 소요됩니다.';
	}
	else {
		var msg = '취소하시겠습니까?';
	}
	if (confirm(msg) === false) return;

	var urlStr = "../order/tax_indb.php?mode=ccrTaxbill&doc_number=" + doc_number + "&dummy=" + new Date().getTime();
	var ajax = new Ajax.Request( urlStr,
	{
		method: "get",
		onComplete: function ()
		{
			var req = ajax.transport;
			if (req.status == 200)
			{
				obj.parentNode.parentNode.innerHTML = '<font color="#EA0095"><b>' + req.responseText.substr(4,2) + '</b></font>';
			}
			else {
				var msg = req.getResponseHeader("Status");
				alert(msg);
			}
		}
	} );
}