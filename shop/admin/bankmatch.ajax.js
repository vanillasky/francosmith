/*** �Աݳ��� ��û ***/
function accountList( query )
{
	// Ŀ�� Ȱ��ȭ
	var listcoverObj = document.getElementById('listcover');
	with (listcoverObj.style)
	{
		display = "block";
		border = "solid 1px blue";
		backgroundColor = "#000000";
		filter = "Alpha(Opacity=20)";
		opacity = "0.2";
		textAlign = "center";
	}

	// ����ó�� : ������
	if ( !document.all ) listcoverObj.parentNode.style.height = '';

	// ����Ʈ �ʱ�ȭ �Լ�
	var listingObj		= document.getElementById('listing');
	var pageObj		= new Array();
	pageObj['rtotal']	= document.getElementById('page_rtotal');
	pageObj['recode']	= document.getElementById('page_recode');
	pageObj['now']	= document.getElementById('page_now');
	pageObj['total']	= document.getElementById('page_total');
	pageObj['navi']	= document.getElementById('page_navi');

	var func_list_init = function()
	{
		if ( listingObj )
			while ( listingObj.rows.length > 3 ) listingObj.deleteRow( listingObj.rows.length - 1); // ��� rows �ʱ�ȭ

		for ( var n in pageObj )
			if ( pageObj[n] && n == 'navi' ) pageObj[n].innerHTML =' ';
			else if ( pageObj[n] ) pageObj[n].innerHTML = '0';
	}

	// ������ �Լ�
	var func_listing = function( lists )
	{
		var gdstatusNm = new Array();
		gdstatusNm['T'] = '��Ī���� (by�ý���)';
		gdstatusNm['B'] = '��Ī���� (by������)';
		gdstatusNm['F'] = '��Ī���� (����ġ)';
		gdstatusNm['S'] = '��Ī���� (��������)';
		gdstatusNm['A'] = '�������Ա�Ȯ�οϷ�';
		gdstatusNm['U'] = '�����ڹ�Ȯ��';

		var len = lists.length;
		for ( n = 0; n < len; n++ )
		{
			l_row = lists[n];

			newTr = listingObj.insertRow(-1);
			newTr.height='25';
			newTr.align='center';
			newTr.bgcolor='#ffffff';
			newTr.bg='#ffffff';
			newTr.setAttribute('updateItems', '');

			newTd = newTr.insertCell(-1);
			newTd.innerHTML = '<font class=ver81 color=333333>' + l_row.no;
			if ( l_row.gdstatus == 'F' || l_row.gdstatus == 'S' || l_row.gdstatus == 'A' || l_row.gdstatus == 'U' )
				newTd.innerHTML += '<input type=hidden name=bkcode[] value="' + l_row.bkcode + '" subject="' + l_row.bkjukyo + ' (' + comma(l_row.bkinput) + ')">';

			newTd = newTr.insertCell(-1);
			newTd.innerHTML = '<font class=ver81 color=333333>' + l_row.bkdate.substr(2,2) + '-' + l_row.bkdate.substr(4,2) + '-' + l_row.bkdate.substr(6,2) + '</font>';

			newTd = newTr.insertCell(-1);
			newTd.innerHTML = '<font class=ver81 color=0074BA>' + l_row.bkacctno + '</font>';

			newTd = newTr.insertCell(-1);
			newTd.innerHTML = l_row.bkname;

			newTd = newTr.insertCell(-1);
			newTd.innerHTML = '<font class=ver8><b>' + comma(l_row.bkinput) + '</b></font>';

			newTd = newTr.insertCell(-1);
			newTd.innerHTML = l_row.bkjukyo;

			newTd = newTr.insertCell(-1);
			if ( l_row.gdstatus == 'F' || l_row.gdstatus == 'S' || l_row.gdstatus == 'A' || l_row.gdstatus == 'U' )
				newTd.innerHTML = '\
				<select name=gdstatus[] valued="' + l_row.gdstatus + '" onchange="barColorChg(this);">\
				<option value="F"' + ( l_row.gdstatus == 'F' ? ' selected' : '' ) + '>��Ī���� (����ġ)</option>\
				<option value="S"' + ( l_row.gdstatus == 'S' ? ' selected' : '' ) +'>��Ī���� (��������)</option>\
				<option value="A"' + ( l_row.gdstatus == 'A' ? ' selected' : '' ) +'>�������Ա�Ȯ�οϷ�</option>\
				<option value="U"' + ( l_row.gdstatus == 'U' ? ' selected' : '' ) +'>�����ڹ�Ȯ��</option>\
				</select>';
			else
				newTd.innerHTML = '<font color=EA0095><b>' + ( gdstatusNm[l_row.gdstatus] != undefined ? gdstatusNm[l_row.gdstatus] : 'Ȯ����' ) + '</b></font>';

			newTd = newTr.insertCell(-1);
			if ( l_row.gddatetime.substr(0,8) != '' )
				dt = l_row.gddatetime.substr(2,2) + '-' + l_row.gddatetime.substr(4,2) + '-' + l_row.gddatetime.substr(6,2) + ' ' + l_row.gddatetime.substr(8,2) + ':' + l_row.gddatetime.substr(10,2);
			else
				dt = '';

			if ( l_row.gdstatus == 'T' && l_row.gddatetime.substr(0,8) != '' )
				newTd.innerHTML = '<font class=ver81 color=333333>' + dt;
			else if ( l_row.gdstatus == 'F' || l_row.gdstatus == 'S' || l_row.gdstatus == 'A' || l_row.gdstatus == 'U' )
				newTd.innerHTML = '<input type=text name=gddatetime[] value="' + dt + '" valued="' + dt + '" style="width:90%" onblur="chkDateFormat(this); barColorChg(this);" ondblclick="setToday(this)">';
			else
				newTd.innerHTML = '��';

			newTd = newTr.insertCell(-1);
			if ( ( l_row.gdstatus == 'T' || l_row.gdstatus == 'B' ) && l_row.bkmemo4 !='' )
				newTd.innerHTML = '<a href="javascript:popup(\'popup.order.php?ordno=' + l_row.bkmemo4 + '\',800,600)"><font class=ver81 color=0074BA><b>' + l_row.bkmemo4 + '</b></font></a>';
			else if ( l_row.gdstatus == 'F' || l_row.gdstatus == 'S' || l_row.gdstatus == 'A' || l_row.gdstatus == 'U' )
				newTd.innerHTML = '<input type=text name=bkmemo4[] value="' + l_row.bkmemo4 + '" valued="' + l_row.bkmemo4 + '" style="width:90%" onblur="barColorChg(this);">';
			else
				newTd.innerHTML = '��';

			// ����
			newTr = listingObj.insertRow(-1);
			newTd = newTr.insertCell(-1);
			newTd.className ='rndline';
			newTd.colSpan = 12;
		}
	}

	// ������ �޽������ �Լ�
	var func_list_msg = function( msg )
	{
		if ( listingObj == undefined ) return;

		newTr = listingObj.insertRow(-1);
		newTr.align='center';

		newTd = newTr.insertCell(-1);
		newTd.style.padding='20px 0 20px 0';
		newTd.colSpan = 12;
		newTd.innerHTML = msg;

		// ����
		newTr = listingObj.insertRow(-1);
		newTd = newTr.insertCell(-1);
		newTd.className ='rndline';
		newTd.colSpan = 12;
	}

	// Create Query
	if ( query == undefined )
	{
		var tmp = new Array();
		var fObj = document.frmList;
		var eleLen = fObj.length;
		for ( i = 0; i < eleLen; i++ )
			if ( fObj[i].value != '' )
				tmp.push( fObj[i].name + "=" + fObj[i].value );
		var query = tmp.join("&");
	}

	// AJAX ����
	var urlStr = "../order/bankmatch.ajax.php?mode=accountList&" + query + "&dummy=" + new Date().getTime();
	var ajax = new Ajax.Request( urlStr,
	{
		method: "get",
		onComplete: function()
		{
			var req = ajax.transport;
			if ( req.status == 200 )
			{
				var response = req.responseText;
				var jsonData = eval( '(' + response + ')' );
				func_list_init();

				// ������ ����
				try{
					func_listing( jsonData.lists );
				}
				catch(err)
				{
					func_list_msg( '<span style="color:#FF6600; font-weight:bold; font-size:10pt;">��û�Ͻ� ����� ���������� ��µ��� �ʾҽ��ϴ�. �ٽ� �õ��ϼ���.</span>' );
					listcoverObj.style.display = "none";
					return;
				}

				// ����¡���� ���
				try{
					for ( var n in pageObj )
						if ( pageObj[n] && n == 'navi' )
						{
							var navi = jsonData.page[n];
							var len = navi[0].length;
							var pageHtml = new Array();

							for ( i = 0; i < len; i++ )
								if ( navi[0][i] == '' ) // ���� ��������ȣ
									pageHtml.push( '<b>' + navi[1][i] + '</b>' );
								else  // �̵��� ��������ȣ
									pageHtml.push( '<a href="javascript:accountList(\'' + navi[0][i] + '\');">' + navi[1][i] + '</a>' );
							pageObj[n].innerHTML = pageHtml.join('&nbsp;');
						}
						else if ( pageObj[n] ) pageObj[n].innerHTML = comma(jsonData.page[n]);
				}
				catch(err){
					listcoverObj.style.display = "none";
					return;
				}
			}
			else {
				var msg = req.getResponseHeader("Status");
				if ( msg == null || msg.length == null || msg.length <= 0 )
					alert( "Error! Request status is " + req.status );
				else
					alert( msg );

				func_list_init();
			}

			listcoverObj.style.display = "none";
		}
	} );
}

/*** ��(Matching) ��û ***/
function bankMatching()
{
	var closedList = false; // �Աݳ��� ��û ȣ�⿩��

	popupLayer('',550,300); // ���̾� �˾�â ����
	document.getElementById('objPopupLayer').innerHTML = "\
		<div id=bank_report>\
			<h1>�ǽð��Ա�Ȯ���� ...</h1>\
			<table>\
			<tr>\
				<th>���ۻ���</th>\
				<td>\
					<div id=briefing>\
					<ul>\
						<li>�긮�� �޽��� ����.</li>\
					</ul>\
					</div>\
				</td>\
			</tr>\
			</table>\
			<p><!--����--></p>\
			<div id=bank_report_btn><a href='javascript:;'><img src='../img/btn_confirm_s.gif' alt=�ݱ�></a></div>\
		</div>\
		";
	document.getElementById('briefing').style.height = 220;

	var briefing = function( str, emtpy, color ) // �긮�� �Լ�
	{
		if ( document.getElementById('briefing').childNodes[0].nodeType == 1 )
			var briefing = document.getElementById('briefing').childNodes[0];
		else
			var briefing = document.getElementById('briefing').childNodes[1];

		if ( emtpy == true )
			while ( briefing.childNodes.length > 0 ) briefing.removeChild( briefing.lastChild );

		var liNode = document.createElement('LI');
		briefing.appendChild(liNode);
		liNode.innerHTML = str;

		if ( color != '' )
			liNode.style.color = color;
	}

	var closeBtn = function() // 'Ȯ��' ��ư Ȱ��ȭ �Լ�
	{
		var btnDiv = document.getElementById('bank_report_btn');
		if ( closedList == true ) // �Աݳ��� ��û ȣ��
		{
			// ������ ���������� �ʵ忡 �Է�
			var dt = new Date();
			var toDay = dt.getMonth() + 1;
			if ( toDay.toString().length < 2 ) toDay = '0' + toDay;
			if ( dt.getDate().toString().length < 2 ) toDay += '0';
			toDay += dt.getDate();
			toDay = dt.getYear().toString() + toDay;
			document.frmList['gddate[]'][0].value = toDay;
			document.frmList['gddate[]'][1].value = toDay;
		}

		btnDiv.childNodes[0].href = "javascript:setTimeout('accountList()', 100); closeLayer();";
		btnDiv.style.display = "block";
	}

	// ������� �޽���
	briefing( '�ǽð��Ա�Ȯ���� �����մϴ�.', true );
	briefing( '��(Matching) �۾� ���� ���Դϴ�. <font color="#DF6600">(���� �߿��� â�� ���� ������.)</font>' );

	// AJAX ����
	var fObj = document.frmList;
	var urlStr = "../order/bankmatch.ajax.php?mode=bankMatching&bkdate[]=" + fObj['bkdate[]'][0].value + "&bkdate[]=" + fObj['bkdate[]'][1].value + "&dummy=" + new Date().getTime();
	var ajax = new Ajax.Request( urlStr,
	{
		method: "get",
		onComplete: function()
		{
			var req = ajax.transport;
			if ( req.status == 200 )
			{
				var response = req.responseText;
				var msg = response.split( "^" ); // [0] ��� �޽���, [1] ������

				// ��� �޽��� �긮��
				var remsg = msg[0];
				try{
					var datas = msg[1].split( "|" ); // [..] ��Ī�� �ֹ���ȣ��
					remsg += '<div style="padding-left:10px;">�� ' + datas.length + ' ���� �ֹ��� �Ա�Ȯ�� �Ǿ����ϴ�.</div>';

					for ( i = 0; i < datas.length; i++ )
					{
						if ( i == 0 ) remsg += '<ol type="1" style="margin-top:5px;margin-bottom:10px;">';
						remsg += '<li>�ֹ���ȣ : ' + datas[i] + '</li>';
						if ( i > 0 && (i+1) == datas.length ) remsg += '</ol>';
					}

					closedList = true;
				}
				catch(err)
				{
					remsg += '<div style="padding-left:10px; color:red;">�Ա�Ȯ�ε� �ֹ����� �����ϴ�.</div>';
				}

				briefing( remsg );
			}
			else {
				var msg = req.getResponseHeader("Status");
				if ( msg == null || msg.length == null || msg.length <= 0 )
					briefing( "Error! Request status is " + req.status, false, 'red' );
				else
				{
					// ��� �޽��� �긮��
					var remsg = '';
					var tmp = msg.split( "^" ); // [0] ��� �޽���, [..] ���� �޽���
					for ( i = 0; i < tmp.length; i++ )
					{
						if ( i == 1 )
							remsg += '<ol type="1" style="margin-bottom:10px;">';
						if ( i == 0 )
							remsg += tmp[i];
						else
							remsg += '<li>' + tmp[i] + '</li>';
						if ( i > 0 && (i+1) == tmp.length )
							remsg += '</ol>';
					}
					briefing( remsg, false, 'red' );
				}
			}

			// �������� �޽���
			briefing( '�ǽð��Ա�Ȯ���� ����Ǿ����ϴ�.' );
			closeBtn();
		}
	} );

}

/*** ���ó�¥ �Է�(yy-mm-dd hh:ii) ***/
function setToday( tObj )
{
	var now = new Date();
	var year = now.getFullYear().toString().substring(2,4);

	var month = now.getMonth() + 1;
	if ( month.toString().length == 1 ) month = '0' + month.toString();

	var day = now.getDate();
	if ( day.toString().length == 1 ) day = '0' + day.toString();

	var hours = now.getHours();
	if ( hours.toString().length == 1 ) hours = '0' + hours.toString();

	var minutes = now.getMinutes();
	if ( minutes.toString().length == 1 ) minutes = '0' + minutes.toString();

	tObj.value = year + '-' + month + '-' + day + ' ' + hours + ':' + minutes;
}

/*** ���� ��Ī�� ���� ���� ***/
function chkDateFormat( tObj )
{
	if ( tObj.value == '' ) return;
	if( tObj.value.match(/^([0-9]{2})-[0-9]{2}-[0-9]{2} [0-9]{2}:([0-9]{2})$/) != null ) return;
	alert("��¥�� YY-MM-DD HH:SS �������� �Է��ϼž� �մϴ�.");
	tObj.value = tObj.getAttribute('valued');
}

/*** �Աݳ��� ����� ���ڵ� ������ ���� ***/
function barColorChg( Obj )
{
	var trObj = Obj.parentNode.parentNode;
	if ( Obj.value != Obj.getAttribute('valued') )
		trObj.setAttribute('updateItems', trObj.getAttribute('updateItems') + Obj.name);
	else
		trObj.setAttribute('updateItems', trObj.getAttribute('updateItems').replace(Obj.name, '') );
	if ( trObj.getAttribute('updateItems') != '') trObj.style.backgroundColor = "#dddddd";
	else trObj.style.backgroundColor = "#ffffff";
}

/*** �Աݳ��� �ϰ����� Ŭ���� ***/
batchUpdate =  {
	begin: function ()
	{
		AGM.act({'onStart' : this.startCallback, 'onRequest' : this.requestCallback, 'onCloseBtn' : 0, 'onErrorCallback' : 0});
	},

	startCallback: function (grp)
	{
		grp.layoutTitle = "�Աݳ��� �ϰ������� ...";
		grp.bMsg['chkEmpty'] = "������ �Աݳ����� �����ϴ�.";
		grp.bMsg['chkCount'] = "�� __count__���� �Աݳ��� ������ ��û�ϼ̽��ϴ�.";
		grp.bMsg['start'] = "�Աݳ��� ������ �����մϴ�.";
		grp.bMsg['end'] = "�Աݳ��� ������ ����Ǿ����ϴ�.";

		grp.articles = new Array();
		grp.iobj = new Array();
		grp.iobj.push(document.getElementsByName('bkcode[]'));
		grp.iobj.push(document.getElementsByName('gdstatus[]'));
		grp.iobj.push(document.getElementsByName('gddatetime[]'));
		grp.iobj.push(document.getElementsByName('bkmemo4[]'));

		var count = grp.iobj[0].length;
		for ( idx = 0; idx < count ; idx++ )
		{
			var able = false;
			for ( n = 0; n < grp.iobj.length; n++ )
			{
				if ( grp.iobj[n][idx].name !='bkcode[]' &&  grp.iobj[n][idx].value != grp.iobj[n][idx].getAttribute('valued') )
				{
					able = true;
					break;
				}
			}

			if ( able == true ) grp.articles.push(idx);
		}
	},

	requestCallback: function (grp, idx)
	{
		var query = '';
		for ( n = 0; n < grp.iobj.length; n++ )
			query += '&' + grp.iobj[n][idx].name.replace(/\[\]/,'') + '=' + grp.iobj[n][idx].value;

		var urlStr = "../order/bankmatch.ajax.php?mode=bankUpdate" + query + "&dummy=" + new Date().getTime();
		var ajax = new Ajax.Request( urlStr,
		{
			method: "get",
			onComplete: function ()
			{
				var req = ajax.transport;
				if (req.status == 200){
					grp.iobj[0][idx].parentNode.parentNode.style.backgroundColor = "#ffffff";
					grp.complete(req);
				}
				else grp.error(req);
			}
		} );
	}
}