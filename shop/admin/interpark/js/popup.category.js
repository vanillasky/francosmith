/*** 전시카테고리검색 요청 ***/
function getDispSrch(fObj)
{
	var loadObj = null;
	if (fObj.srchName.value.trim() == '' || fObj.srchName.value.trim() == '<'){
		alert("검색어를 입력하셔야 합니다.");
		fObj.srchName.focus();
		return false;
	}
	var urlStr = "../interpark/ajaxSock.php?mode=getDispSrch&" + decodeURI(Form.serialize(fObj)) + "&dummy=" + new Date().getTime();
	var ajax = new Ajax.Request( urlStr,
	{
		method: "get",
		onLoading: function ()
		{
			if (loadObj == null){
				loadObj = _ID('p_disp').parentNode.insertBefore(document.createElement('DIV'), _ID('p_disp'));
				loadObj.style.position = 'relative';
				var cDiv = loadObj.appendChild(document.createElement('DIV'));
				var cImg = cDiv.appendChild(document.createElement('IMG'));
				cImg.src = '../img/loading.gif';
				with (cDiv.style) {
					position = 'absolute';
					backgroundColor = '#FFFFFF';
					border = 'solid 1px #dddddd';
					filter = "Alpha(Opacity=90)";
					opacity = "0.9";
					padding = 50;
					left = 100;
					top = 50;
				}
			}
			loadObj.style.display='block';
		},
		onComplete:  function ()
		{
			var req = ajax.transport;
			if ( req.status == 200 )
			{
				var response = req.responseXML.documentElement;

				var tblObj = document.getElementById('p_disp').getElementsByTagName('table')[0];
				while ( tblObj.rows.length > 0 ) tblObj.deleteRow( tblObj.rows.length - 1); // 결과 rows 초기화

				var cates = response.getElementsByTagName( "cate" );
				for ( i = 0; i < cates.length; i++ )
				{
					newTr = tblObj.insertRow(-1);
					newTr.style.background='#FFFFFF';
					newTr.style.height='20';

					newTd = newTr.insertCell(-1);
					newTd.style.textAlign ='center';
					newTd.innerHTML = '<a href="javascript:;" onclick="assignDisp(\'' + cates[i].getElementsByTagName('dispno')[0].firstChild.data  +'\', \'' + cates[i].getElementsByTagName('dispnm')[0].firstChild.data.replace(/\"/g,'&quot;').replace(/\'/g,"\\'")  +'\');"><img src="../img/btn_open_cateselect.gif"></a>';

					newTd = newTr.insertCell(-1);
					newTd.innerHTML = cates[i].getElementsByTagName('dispnm')[0].firstChild.data;
				}
			}
			else {
				var msg = req.getResponseHeader("Status");
				if ( msg == null || msg.length == null || msg.length <= 0 )
					alert( "Error! Request status is " + req.status );
				else
					alert( msg );
			}
			if (loadObj != null) loadObj.style.display='none';
		}
	} );
}

/*** 전시카테고리 대입 ***/
function assignDisp(dispno, dispnm)
{
	if (parent.InterparkProductRequireInfo) {
		var inpkPrdReqInfo = new parent.InterparkProductRequireInfo();
		inpkPrdReqInfo.setProductGroup(dispno.substring(0, 9));
	}

	if (spot == null) return;
	parent.document.getElementsByName(spot)[0].value = dispno;

	spot = spot.replace("dispno", "dispnm");
	if (parent.document.getElementById(spot).tagName == 'INPUT')
		parent.document.getElementById(spot).value = dispnm;
	else
		parent.document.getElementById(spot).innerHTML = dispnm;
	parent.closeLayer();
}