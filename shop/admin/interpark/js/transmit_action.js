/*** INTERPARK : TRANSMIT GOODS (ITG) ***/
ITG = {
	query : null,
	sections : null,
	total : '',
	point : 1,
	loadObj : null,

	send : function ()
	{
		clsThis = this;
		var urlStr = "../interpark/ajaxSock.php?mode=putTransmitGoods&dummy=" + new Date().getTime();
		parameters = {
			"point" : this.point
		}
		if (this.query == '') parameters["section"] = this.sections[ (this.point - 1) ];
		else parameters["query"]=this.query;

		var ajax = new Ajax.Request( urlStr,
		{
			method: "post",
			parameters: parameters,
			onLoading: function ()
			{
				if (clsThis.point == 1 && clsThis.loadObj == null)
				{
					clsThis.loadObj = _ID('result').parentNode.insertBefore(document.createElement('DIV'), _ID('result'));
					clsThis.loadObj.style.position = 'relative';
					var cDiv = clsThis.loadObj.appendChild(document.createElement('DIV'));
					var cImg = cDiv.appendChild(document.createElement('IMG'));
					cImg.src = '../img/loading.gif';
					with (cDiv.style) {
						position = 'absolute';
						backgroundColor = '#FFFFFF';
						border = 'solid 1px #dddddd';
						filter = "Alpha(Opacity=90)";
						opacity = "0.9";
						padding = 50;
						left = 200;
						top = 10;
					}
				}
			},
			onComplete:  function ()
			{
				var req = ajax.transport;
				if ( req.status == 200 )
				{
					var response = req.responseXML.documentElement;
					var tblObj = document.getElementById('result');
					tblObj.style.backgroundColor = '#E8F1FC';
					var items = response.getElementsByTagName( "success" );
					for ( i = 0; i < items.length; i++ )
					{
						newTr = tblObj.insertRow(-1);

						newTd = newTr.insertCell(-1);
						newTd.style.textAlign ='center';
						newTd.innerHTML = newTr.rowIndex;

						newTd = newTr.insertCell(-1);
						newTd.style.textAlign ='center';
						newTd.innerHTML = items[i].getElementsByTagName('originPrdNo')[0].firstChild.data;

						newTd = newTr.insertCell(-1);
						newTd.style.textAlign ='center';
						if (items[i].getElementsByTagName('prdNo')[0].firstChild != null){
							newTd.innerHTML = '<a target="_blank" href="http://www.interpark.com/product/MallDisplay.do?_method=detail&sc.shopNo=0000100000&sc.prdNo=' + items[i].getElementsByTagName('prdNo')[0].firstChild.data + '">' + items[i].getElementsByTagName('prdNo')[0].firstChild.data + '</a>';
						}

						newTd = newTr.insertCell(-1);
						newTd.innerHTML = '<a href="javascript:popup(\'../goods/popup.register.php?mode=modify&goodsno='
							 + items[i].getElementsByTagName('originPrdNo')[0].firstChild.data + '\',825,600)">'
							 + items[i].getElementsByTagName('prdNm')[0].firstChild.data + '</a>';

						newTd = newTr.insertCell(-1);
						//newTd.innerHTML = items[i].getElementsByTagName('ecode')[0].firstChild.data + ':' + items[i].getElementsByTagName('resultMessage')[0].firstChild.data;
						description = response.getElementsByTagName( "description" )[0].firstChild.data;
						newTd.innerHTML =description;// items[i].getElementsByTagName('description')[0].firstChild.data;
					}

					var items = response.getElementsByTagName( "error" );
					for ( i = 0; i < items.length; i++ )
					{
						newTr = tblObj.insertRow(-1);

						newTd = newTr.insertCell(-1);
						newTd.style.textAlign ='center';
						newTd.innerHTML = newTr.rowIndex;

						newTd = newTr.insertCell(-1);
						newTd.style.textAlign ='center';
						newTd.innerHTML = response.getElementsByTagName( "code" )[i].firstChild.data;

						newTd = newTr.insertCell(-1);
						newTd.style.textAlign ='center';
						description = response.getElementsByTagName( "description" )[0].firstChild.data;

						newTd = newTr.insertCell(-1);
						newTd.style.textAlign ='center';
						newTd.innerHTML=response.getElementsByTagName( "message" )[i].firstChild.data;

						newTd = newTr.insertCell(-1);
						newTd.style.textAlign ='center';
						newTd.innerHTML=response.getElementsByTagName( "explanation" )[i].firstChild.data;
					}
				}
				else {
					var msg = req.getResponseHeader("Status");
					if ( msg == null || msg.length == null || msg.length <= 0 )
						alert( "Error! Request status is " + req.status );
					else
						alert( msg );
				}

				if (clsThis.point == clsThis.total && clsThis.loadObj != null){
					clsThis.loadObj.parentNode.removeChild(clsThis.loadObj);
					tblObj.style.backgroundColor = '';
				}

				if (clsThis.point < clsThis.total){
					clsThis.point++;
					clsThis.send();
				}
			}
		} );
	}
}