/*** Image Host Replace (IHR) ***/
imgHost = {
	items : new Array(),
	num : 1,
	total : 0,
	point : 1,
	loadObj : null,
	ftp : null,

	submit : function ()
	{
		this.items = new Array();
		this.total = 0;
		this.point = 1;
		if (isChked(document.getElementsByName('chk[]')) === false){
			if (document.getElementsByName('chk[]').length) document.getElementsByName('chk[]')[0].focus();
			return;
		}
		for (i = 0; i < document.getElementsByName('chk[]').length; i++){
			if (document.getElementsByName('chk[]')[i].checked) this.items.push( document.getElementsByName('chk[]')[i].value );
		}
		this.total = Math.ceil(this.items.length / this.num);
		if (this.ftp == null){
			this.ftpDisplayForm();
			return;
		}
		this.send();
	},

	/* 전송 */
	send : function ()
	{
		clsThis = this;
		var urlStr = "../goods/imgHostReplace.indb.php?mode=putReplace&dummy=" + new Date().getTime();
		var parameters = "";
		var tmp = new Array();
		var startNo = this.point - 1;
		var endNo = this.point * this.num;
		if (endNo > this.items.length) endNo = this.items.length
		for (i = startNo; i < endNo; i++) tmp.push( this.items[i] );
		parameters += "&goods=" + tmp.join(',');
		var ajax = new Ajax.Request( urlStr,
		{
			method: "post",
			parameters: parameters,
			onLoading: function ()
			{
				if (clsThis.point == 1 && clsThis.loadObj == null)
				{
					popupLayer('', 500, 250);
					_ID('objPopupLayer').getElementsByTagName('div')[0].style.display = 'none';
					var doc = _ID('objPopupLayer').getElementsByTagName('iframe')[0].contentWindow.document;
					doc.open();
					doc.write(processHtml);
					doc.close();
				}
				else {
					var doc = _ID('objPopupLayer').getElementsByTagName('iframe')[0].contentWindow.document;
				}
				doc.getElementById('message').innerHTML = '<b style="font-size:16pt;">' + endNo + ' / ' + clsThis.items.length + ' 처리중입니다.</b> 잠시만 기다려주세요.';
			},
			onComplete:  function ()
			{
				var req = ajax.transport;
				if ( req.status == 200 )
				{
					var jsonData = eval( '(' + req.responseText + ')' );
					for (i=0; i < jsonData.length; i++)
					{
						tmp2 = jsonData[i].split(":");
						_ID('in_' + tmp2[0]).innerHTML = tmp2[1];
					}
				}
				else {
					_ID('objPopupLayer').getElementsByTagName('div')[0].style.display = 'block';
					var doc = _ID('objPopupLayer').getElementsByTagName('iframe')[0].contentWindow.document;
					doc.getElementById('loading').style.display = 'none';

					var msg = req.getResponseHeader("Status");
					if ( msg == null || msg.length == null || msg.length <= 0 ) msg = "Error! Request status is " + req.status;
					doc.getElementById('message').innerHTML = '<div style="margin-top:80px;"><b style="font-size:16pt;">에러가 발생하여 중단합니다.</b><br>(' + msg + ')</div>';
					return;
				}

				if (clsThis.point == clsThis.total){
					_ID('objPopupLayer').getElementsByTagName('div')[0].style.display = 'block';
					var doc = _ID('objPopupLayer').getElementsByTagName('iframe')[0].contentWindow.document;
					doc.getElementById('loading').style.display = 'none';
					doc.getElementById('message').innerHTML = '<div style="margin-top:80px;"><b style="font-size:16pt;">' + endNo + ' / ' + clsThis.items.length + ' 처리완료되었습니다.</b></div>';
				}

				if (clsThis.point < clsThis.total){
					clsThis.point++;
					clsThis.send();
				}
			}
		} );
	},

	/* FTP 폼 출력 */
	ftpDisplayForm : function ()
	{
		popupLayer('', 500, 230);
		doc = _ID('objPopupLayer').getElementsByTagName('iframe')[0].contentWindow.document;
		doc.open();
		doc.write(ftpHtml);
		doc.close();
	},

	/* FTP 인증 실행 */
	ftpVerify : function (fObj)
	{
		clsThis = this;
		if (chkForm(fObj) === false) return;
		var urlStr = "../goods/imgHostReplace.indb.php?mode=ftpVerify&dummy=" + new Date().getTime();
		var parameters = decodeURIComponent(clsThis.formSerialize(fObj).replace(/%26/,"&&")).replace(/&&/,"%26");
		var ajax = new Ajax.Request( urlStr,
		{
			method: "post",
			parameters: parameters,
			onLoading: function ()
			{
				_ID('objPopupLayer').getElementsByTagName('iframe')[0].contentWindow.document.getElementById('confirm').style.display = 'none';
				_ID('objPopupLayer').getElementsByTagName('iframe')[0].contentWindow.document.getElementById('wait').style.display = 'block';
			},
			onComplete:  function ()
			{
				var req = ajax.transport;
				if ( req.status == 200 )
				{
					var response = req.responseText;
					if (response == 'true')
					{
						clsThis.ftp = true;
						closeLayer();
						clsThis.send();
					}
					else {
						fObj.pass.value = '';
						alert("접속이 실패되었습니다.\nFTP 정보를 확인한 후 다시 시도하세요.");
					}
				}
				else {
					var msg = req.getResponseHeader("Status");
					if ( msg == null || msg.length == null || msg.length <= 0 ) msg = "Error! Request status is " + req.status;
					alert( msg );
				}
				_ID('objPopupLayer').getElementsByTagName('iframe')[0].contentWindow.document.getElementById('confirm').style.display = 'block';
				_ID('objPopupLayer').getElementsByTagName('iframe')[0].contentWindow.document.getElementById('wait').style.display = 'none';
			}
		} );
	},

	/* form테이다 Serialize 합니다. IE10 Serialize 에러가 있어서 추가*/
	formSerialize : function (chkfrm){
		if(chkfrm==null) return "";
		var values="";
		chkFormElment = new Array();
		for(var i=0;i <chkfrm.length;i++){
			chkFormElment[chkFormElment.length]=chkfrm[i].name+"="+chkfrm[i].value;
		}
		values=chkFormElment.join('&');

		return values;
	}
}




/* 진행상황 HTML */
var processHtml = '\
<html>\
<head>\
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">\
<link rel="styleSheet" href="../style.css">\
</head>\
<body style="margin:0">\
<div style="text-align:center; margin-top:40px;" id="loading"><img src="../img/loading.gif"></div>\
<div style="text-align:center; margin-top:10px;" id="message"></div>\
</body>\
</html>\
';




/* FTP 폼 HTML */
var ftpHtml = '\
<html>\
<head>\
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">\
<link rel="styleSheet" href="../style.css">\
<script language="javascript">\
function echoDomain(obj){\
	document.getElementById("domain").innerHTML = obj.value;\
}\
</script>\
</head>\
<body style="margin:0" onload="frm1.userid.focus();">\
<form name=frm1 method="post" onSubmit="return (parent.imgHost.ftpVerify(this) ? false : false)">\
<table cellpadding=0 cellspacing=0 border=0 width=100%>\
<tr><td height=22></td></tr>\
<tr><td align=center><div style="font-family: 돋움; font-size: 8pt; letter-spacing:-1;"><font color="#777777"><b>고도의 이미지호스팅 신청고객은 고객님의 FTP 계정정보를 입력하세요.</b></div>\
<tr><td height=12></td></tr></table>\
\
<table cellpadding=5 cellspacing=0 border=0 align=center>\
<col><col style="padding-left:10px">\
<tr>\
	<td height=28 align=right style="font-family: verdana; font-size: 8pt; letter-spacing:-1;"><font color="#777777"><b>FTP 주소</td>\
	<td bgColor="#eeeeee"><b>ftp://ftp.<span id="domain"></span>.godohosting.com</b></td>\
</tr>\
<tr>\
	<td height=28 align=right style="font-family: verdana; font-size: 8pt; letter-spacing:-1;"><font color="#777777"><b>FTP ID</td>\
	<td background="../img/login_linebg.gif">\
	<input name="userid" type="text" style="width:284px;background:transparent;border:0px;font:8pt verdana;color:333333" required label="아이디" value="" onkeyup="echoDomain(this)">\
	</td>\
</tr>\
<tr>\
	<td height=28 align=right style="font-family: verdana; font-size: 8pt; letter-spacing:-1;"><font color="#777777"><b>FTP Password</td>\
	<td background="../img/login_linebg.gif">\
	<input name="pass" type="password" style="width:284px;background:transparent;border:0px;font:8pt verdana;color:333333" required label="비밀번호">\
	</td>\
</tr>\
<tr><td height=5></td></tr>\
<tr>\
	<td colspan=2 align=center id="confirm" class=noline><input type="image" src="../img/btn_confirm_s.gif" border=0></td>\
</tr>\
<tr>\
	<td colspan=2 align=center id="wait" style="font-family: verdana; font-size: 8pt; color:#777777; display:none;"><b>처리중입니다. 잠시만 기다려주세요.</b></td>\
</tr>\
</table>\
<div style="font-family: 돋움; color:#777777; letter-spacing:-1; padding-top:12px; text-align:center;">※ <span style="font-size: 8pt;">FTP 주소는 FTP ID를 입력하면 자동으로 구성됩니다.</span></div>\
</form>\
</body>\
</html>\
';