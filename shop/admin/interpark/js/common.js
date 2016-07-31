/*** 전시카테고리명 출력 ***/
function getDispNm( dispno, idnm )
{
	var urlStr = "../interpark/ajaxSock.php?mode=getDispNm&dispno=" + dispno + "&dummy=" + new Date().getTime();
	var ajax = new Ajax.Request( urlStr,
	{
		method: "get",
		onComplete:  function ()
		{
			var req = ajax.transport;
			if ( req.status == 200 )
			{
				var response = req.responseText;
				if (_ID(idnm).tagName == 'INPUT') _ID(idnm).value = response;
				else _ID(idnm).innerHTML = response;
			}
		}
	} );
}