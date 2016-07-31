/*** 입금내역 요청 ***/
function accountList( query )
{
	// 커버 활성화
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

	// 예외처리 : 모질라
	if ( !document.all ) listcoverObj.parentNode.style.height = '';

	// 리스트 초기화 함수
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
			while ( listingObj.rows.length > 3 ) listingObj.deleteRow( listingObj.rows.length - 1); // 결과 rows 초기화

		for ( var n in pageObj )
			if ( pageObj[n] && n == 'navi' ) pageObj[n].innerHTML =' ';
			else if ( pageObj[n] ) pageObj[n].innerHTML = '0';
	}

	// 리스팅 함수
	var func_listing = function( lists )
	{
		var gdstatusNm = new Array();
		gdstatusNm['T'] = '매칭성공 (by시스템)';
		gdstatusNm['B'] = '매칭성공 (by관리자)';
		gdstatusNm['F'] = '매칭실패 (불일치)';
		gdstatusNm['S'] = '매칭실패 (동명이인)';
		gdstatusNm['A'] = '관리자입금확인완료';
		gdstatusNm['U'] = '관리자미확인';

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
				<option value="F"' + ( l_row.gdstatus == 'F' ? ' selected' : '' ) + '>매칭실패 (불일치)</option>\
				<option value="S"' + ( l_row.gdstatus == 'S' ? ' selected' : '' ) +'>매칭실패 (동명이인)</option>\
				<option value="A"' + ( l_row.gdstatus == 'A' ? ' selected' : '' ) +'>관리자입금확인완료</option>\
				<option value="U"' + ( l_row.gdstatus == 'U' ? ' selected' : '' ) +'>관리자미확인</option>\
				</select>';
			else
				newTd.innerHTML = '<font color=EA0095><b>' + ( gdstatusNm[l_row.gdstatus] != undefined ? gdstatusNm[l_row.gdstatus] : '확인전' ) + '</b></font>';

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
				newTd.innerHTML = '―';

			newTd = newTr.insertCell(-1);
			if ( ( l_row.gdstatus == 'T' || l_row.gdstatus == 'B' ) && l_row.bkmemo4 !='' )
				newTd.innerHTML = '<a href="javascript:popup(\'popup.order.php?ordno=' + l_row.bkmemo4 + '\',800,600)"><font class=ver81 color=0074BA><b>' + l_row.bkmemo4 + '</b></font></a>';
			else if ( l_row.gdstatus == 'F' || l_row.gdstatus == 'S' || l_row.gdstatus == 'A' || l_row.gdstatus == 'U' )
				newTd.innerHTML = '<input type=text name=bkmemo4[] value="' + l_row.bkmemo4 + '" valued="' + l_row.bkmemo4 + '" style="width:90%" onblur="barColorChg(this);">';
			else
				newTd.innerHTML = '―';

			// 라인
			newTr = listingObj.insertRow(-1);
			newTd = newTr.insertCell(-1);
			newTd.className ='rndline';
			newTd.colSpan = 12;
		}
	}

	// 리스팅 메시지출력 함수
	var func_list_msg = function( msg )
	{
		if ( listingObj == undefined ) return;

		newTr = listingObj.insertRow(-1);
		newTr.align='center';

		newTd = newTr.insertCell(-1);
		newTd.style.padding='20px 0 20px 0';
		newTd.colSpan = 12;
		newTd.innerHTML = msg;

		// 라인
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

	// AJAX 실행
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

				// 리스팅 실행
				try{
					func_listing( jsonData.lists );
				}
				catch(err)
				{
					func_list_msg( '<span style="color:#FF6600; font-weight:bold; font-size:10pt;">요청하신 목록이 정상적으로 출력되지 않았습니다. 다시 시도하세요.</span>' );
					listcoverObj.style.display = "none";
					return;
				}

				// 페이징정보 출력
				try{
					for ( var n in pageObj )
						if ( pageObj[n] && n == 'navi' )
						{
							var navi = jsonData.page[n];
							var len = navi[0].length;
							var pageHtml = new Array();

							for ( i = 0; i < len; i++ )
								if ( navi[0][i] == '' ) // 현재 페이지번호
									pageHtml.push( '<b>' + navi[1][i] + '</b>' );
								else  // 이동할 페이지번호
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

/*** 비교(Matching) 요청 ***/
function bankMatching()
{
	var closedList = false; // 입금내역 요청 호출여부

	popupLayer('',550,300); // 레이어 팝업창 띄우기
	document.getElementById('objPopupLayer').innerHTML = "\
		<div id=bank_report>\
			<h1>실시간입금확인중 ...</h1>\
			<table>\
			<tr>\
				<th>전송상태</th>\
				<td>\
					<div id=briefing>\
					<ul>\
						<li>브리핑 메시지 샘플.</li>\
					</ul>\
					</div>\
				</td>\
			</tr>\
			</table>\
			<p><!--점선--></p>\
			<div id=bank_report_btn><a href='javascript:;'><img src='../img/btn_confirm_s.gif' alt=닫기></a></div>\
		</div>\
		";
	document.getElementById('briefing').style.height = 220;

	var briefing = function( str, emtpy, color ) // 브리핑 함수
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

	var closeBtn = function() // '확인' 버튼 활성화 함수
	{
		var btnDiv = document.getElementById('bank_report_btn');
		if ( closedList == true ) // 입금내역 요청 호출
		{
			// 금일을 최종종료일 필드에 입력
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

	// 실행시작 메시지
	briefing( '실시간입금확인을 시작합니다.', true );
	briefing( '비교(Matching) 작업 진행 중입니다. <font color="#DF6600">(진행 중에는 창을 닫지 마세요.)</font>' );

	// AJAX 실행
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
				var msg = response.split( "^" ); // [0] 결과 메시지, [1] 데이터

				// 결과 메시지 브리핑
				var remsg = msg[0];
				try{
					var datas = msg[1].split( "|" ); // [..] 매칭된 주문번호들
					remsg += '<div style="padding-left:10px;">총 ' + datas.length + ' 건의 주문이 입금확인 되었습니다.</div>';

					for ( i = 0; i < datas.length; i++ )
					{
						if ( i == 0 ) remsg += '<ol type="1" style="margin-top:5px;margin-bottom:10px;">';
						remsg += '<li>주문번호 : ' + datas[i] + '</li>';
						if ( i > 0 && (i+1) == datas.length ) remsg += '</ol>';
					}

					closedList = true;
				}
				catch(err)
				{
					remsg += '<div style="padding-left:10px; color:red;">입금확인된 주문건이 없습니다.</div>';
				}

				briefing( remsg );
			}
			else {
				var msg = req.getResponseHeader("Status");
				if ( msg == null || msg.length == null || msg.length <= 0 )
					briefing( "Error! Request status is " + req.status, false, 'red' );
				else
				{
					// 결과 메시지 브리핑
					var remsg = '';
					var tmp = msg.split( "^" ); // [0] 결과 메시지, [..] 오류 메시지
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

			// 실행종료 메시지
			briefing( '실시간입금확인이 종료되었습니다.' );
			closeBtn();
		}
	} );

}

/*** 오늘날짜 입력(yy-mm-dd hh:ii) ***/
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

/*** 최종 매칭일 형식 검증 ***/
function chkDateFormat( tObj )
{
	if ( tObj.value == '' ) return;
	if( tObj.value.match(/^([0-9]{2})-[0-9]{2}-[0-9]{2} [0-9]{2}:([0-9]{2})$/) != null ) return;
	alert("날짜는 YY-MM-DD HH:SS 형식으로 입력하셔야 합니다.");
	tObj.value = tObj.getAttribute('valued');
}

/*** 입금내역 변경된 레코드 배경색상 변경 ***/
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

/*** 입금내역 일괄수정 클래스 ***/
batchUpdate =  {
	begin: function ()
	{
		AGM.act({'onStart' : this.startCallback, 'onRequest' : this.requestCallback, 'onCloseBtn' : 0, 'onErrorCallback' : 0});
	},

	startCallback: function (grp)
	{
		grp.layoutTitle = "입금내역 일괄수정중 ...";
		grp.bMsg['chkEmpty'] = "수정할 입금내역이 없습니다.";
		grp.bMsg['chkCount'] = "총 __count__개의 입금내역 수정을 요청하셨습니다.";
		grp.bMsg['start'] = "입금내역 수정을 시작합니다.";
		grp.bMsg['end'] = "입금내역 수정이 종료되었습니다.";

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