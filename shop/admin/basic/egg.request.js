/*** ���ں������񽺽�û ��û ***/
function usafeRequest( fobj )
{
	if ( chkForm(fobj) === false ) return;

	var tmp = decodeURIComponent( Form.serialize(fobj) );
	tmp = tmp.replace(/\r/ig, "%0D" );
	tmp = tmp.replace(/\n/ig, "%0A" );

	var urlStr = "../basic/egg.ajaxSock.php?mode=usafeRequest&" + tmp + "&dummy=" + new Date().getTime();
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
				msgDiv.innerHTML = "--- ���ں������� ��û���Դϴ� ---";
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
					msgDiv.innerHTML = "���ں������� ��û�� ���������� �̷�������ϴ�.";
					alert('���ں������� ��û�� ���������� �̷�������ϴ�.');
					document.location.replace( '../basic/egg.progress.php' );
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