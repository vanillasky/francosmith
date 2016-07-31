/*** INTERPARK REQUEST SEND (IRS) ***/
IRS = {

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
		fobj['shopName'].value = param['shopName'];
		fobj['compName'].value = param['shopName'];
		fobj['ceoName'].value = param['ceoName'];
		fobj['compSerial'].value = param['compSerial'];
		fobj['email'].value = param['email'];
		fobj['phone[]'][0].value = param['phone'][0] == null ? '' : param['phone'][0];
		fobj['phone[]'][1].value = param['phone'][1] == null ? '' : param['phone'][1];
		fobj['phone[]'][2].value = param['phone'][2] == null ? '' : param['phone'][2];
		fobj['fax[]'][0].value = param['fax'][0] == null ? '' : param['fax'][0];
		fobj['fax[]'][1].value = param['fax'][1] == null ? '' : param['fax'][1];
		fobj['fax[]'][2].value = param['fax'][2] == null ? '' : param['fax'][2];
		_ID('shopName0').innerHTML = param['shopName'];
		_ID('domain0').innerHTML = 'http://' + param['shopUrl'];
		_ID('ceoName0').innerHTML = param['ceoName'];
		this.isExists();
		this.getShopCategory(0);
	},

	ctrl_field : function (val)
	{
		if (val) fobj.compName.value = fobj.shopName.value;
		else fobj.compName.value = '';
	},

	isExists : function ()
	{
		var urlStr = "../interpark/ajaxSock.php?mode=isExists&godosno=" + fobj['godosno'].value + "&dummy=" + new Date().getTime();
		var ajax = new Ajax.Request( urlStr,
		{
			method: "get",
			onComplete: function ()
			{
				var req = ajax.transport;
				if ( req.status == 200 && req.responseText == 'true' )
						msgDiv.innerHTML = '[신청여부 검사] 이미 신청한 상점입니다.';
				else if ( req.status == 200 && req.responseText != 'true' )
				{
					sendDiv.style.display = 'block';
					msgDiv.style.display = 'none';
				}
				else {
					var msg = req.getResponseHeader("Status");
					if ( msg == null || msg.length == null || msg.length <= 0 ) msg = 'Error! Request status is ' + req.status;
					msgDiv.innerHTML = '[신청여부 검사 실패] ' + msg + '';
				}
			}
		} );
	},

	getShopCategory : function (step, callCate)
	{
		for ( i = step; i <= 1; i++ ) // select option 초기화
		{
			var stepOpt = document.getElementsByName("cate[]")[step];
			while ( 1 < stepOpt.options.length ) stepOpt.options[ (stepOpt.options.length - 1) ] = null;
		}

		if (step == 0) callCate = '';
		var urlStr = "../interpark/ajaxSock.php?mode=getShopCategory&callCate=" + callCate.replace(/&/,"%26") + "&dummy=" + new Date().getTime();
		var ajax = new Ajax.Request( urlStr,
		{
			method: "get",
			onComplete: function ()
			{
				var req = ajax.transport;
				if ( req.status == 200 ){
					var response = req.responseXML.documentElement;
					var stepOpt = document.getElementsByName("cate[]")[step];
					var cates = response.getElementsByTagName( "cate" );
					for ( i = 0; i < cates.length; i++ )
					{
						stepOpt.options[(i+1)]=new Option(cates[i].firstChild.data, cates[i].firstChild.data );
					}
				}
				else {
					var msg = req.getResponseHeader("Status");
					if ( msg == null || msg.length == null || msg.length <= 0 ) msg = 'Error! Request status is ' + req.status;
					alert(msg);
				}
			}
		} );
	},

	putMerchant : function ()
	{
		if ( chkForm(fobj) === false ) return;
		if ( $F(fobj.delvCostCondition) == '' ) { fobj.delvCostCondition.focus(); alert("배송비 측정가를 입력하셔야합니다."); return; }
		if ( $F(fobj.delvCostBasic) == '' ) { fobj.delvCostBasic.focus(); alert("배송비를 입력하셔야합니다."); return; }

		query = decodeURIComponent(Form.serialize(fobj).replace(/%26/,"&&")).replace(/&&/,"%26");
		var urlStr = "../interpark/ajaxSock.php?mode=putMerchant&" + query + "&dummy=" + new Date().getTime();
		var ajax = new Ajax.Request( urlStr,
		{
			method: "get",
			acynchronous: false,
			onLoading: function ()
			{
				if (document.getElementById('avoidSubmit') && !document.getElementById('avoidMsg') )
				{
					sendDiv = document.getElementById('avoidSubmit');
					msgDiv = sendDiv.parentNode.insertBefore( sendDiv.cloneNode(true), sendDiv.nextSibling );
					msgDiv.id = 'avoidMsg';
					msgDiv.style.letterSpacing = '0px';
					msgDiv.innerHTML = "--- 인터파크 샵플러스 입점 신청중입니다 ---";
				}

				sendDiv.style.display = 'none';
				msgDiv.style.display = 'block';
			},
			onComplete: function ()
			{
				var req = ajax.transport;
				if ( req.status == 200 )
				{
					if ( req.responseText == 'true' )
					{
						msgDiv.innerHTML = "인터파크 샵플러스 신청이 정상적으로 이루어졌습니다.";
						alert('인터파크 샵플러스 신청이 정상적으로 이루어졌습니다.');
						document.location.replace( '../interpark/progress.php' );
					}
				}
				else {
					sendDiv.style.display = 'block';
					msgDiv.style.display = 'none';

					var msg = req.getResponseHeader("Status");
					if ( msg == null || msg.length == null || msg.length <= 0 )
						alert( "Error! Request status is " + req.status );
					else
						alert( msg );
				}
			}
		} );
	}
}