/*** 오픈마켓 분류검색 요청 ***/
function srchOMCategory()
{
	var urlStr = "../open/om.ajax.php?mode=srchOMCategory&srchName=" + document.getElementById("srchName").value + "&dummy+" + new Date().getTime();
	var ajax = new Ajax.Request( urlStr,
	{
		method: "get",
		onComplete: srchOMCategoryPrint
	} );
}

/*** 오픈마켓 분류검색 응답 ***/
function srchOMCategoryPrint( req )
{
	if ( req.status == 200 )
	{
		var response = req.responseXML.documentElement;

		var tblObj = document.getElementById('om_srchOMCategoryPrint').getElementsByTagName('table')[0];
		while ( tblObj.rows.length > 0 ) tblObj.deleteRow( tblObj.rows.length - 1); // 결과 rows 초기화

		var cates = response.getElementsByTagName( "cate" );
		for ( i = 0; i < cates.length; i++ )
		{
			newTr = tblObj.insertRow(-1);
			newTr.style.background='#FFFFFF';
			newTr.style.height='20';

			newTd = newTr.insertCell(-1);
			newTd.style.textAlign ='center';
			newTd.innerHTML = '<a href="javascript:;" onclick="optOMCategory(1, \'' + cates[i].getElementsByTagName('code')[0].firstChild.data  +'\');"><img src="../img/btn_open_cateselect.gif"></a>';

			newTd = newTr.insertCell(-1);
			newTd.innerHTML = cates[i].getElementsByTagName('name')[0].firstChild.data; // cates[i].childNodes[0].firstChild.data <= 동일개체 가리킴
		}
	}
	else {
		var msg = req.getResponseHeader("Status");
		if ( msg == null || msg.length == null || msg.length <= 0 )
			alert( "Error! Request status is " + req.status );
		else
			alert( msg );
	}
}

/*** 오픈마켓 분류 요청 ***/
function optOMCategory( step )
{
	var defaultOpt = new Array;
	if ( optOMCategory.arguments[1] != null ) // 초기선택값
	{
		if ( typeof(optOMCategory.arguments[1]) != 'object' )
		{
			var defaultOpt = new Array();
			defaultOpt.push(  optOMCategory.arguments[1].substr(0,8)  );
			defaultOpt.push(  optOMCategory.arguments[1].substr(8,8)  );
			defaultOpt.push(  optOMCategory.arguments[1].substr(16,8)  );
			defaultOpt.push(  optOMCategory.arguments[1].substr(24,8)  );
		}
		else defaultOpt = optOMCategory.arguments[1];
	}

	for ( i = step; i <= 4; i++ ) // select option 초기화
	{
		var stepOpt = document.getElementById("cat_opt" + i);
		if ( stepOpt == null ) break;
		if ( stepOpt.options.length == 0 ) break;
		while ( 0 < stepOpt.options.length )
			stepOpt.options[ (stepOpt.options.length - 1) ] = null;
	}

	var callCate = '';
	for ( i = 1; i < step; i++ ) // 분류호출 요청 데이타 정의
	{
		var stepOpt = document.getElementById("cat_opt" + i);
		if ( stepOpt == null ) break;
		var tmp = stepOpt.value;
		if ( tmp != '' ) callCate += tmp;
		else {
			callCate = null;
			break;
		}
	}

	var urlStr = "../open/om.ajax.php?mode=optOMCategory&callCate=" + callCate + "&dummy+" + new Date().getTime();
	var ajax = new Ajax.Request( urlStr,
	{
		method: "get",
		onComplete: optOMCategoryPrint,
		pars: {step:step,defaultOpt:defaultOpt}
	} );
}

/*** 오픈마켓 분류 응답 ***/
function optOMCategoryPrint( req ){
	if ( req.status == 200 )
	{
		var response = req.responseXML.documentElement;
		var step = optOMCategoryPrint.arguments[2].step;
		var defaultOpt = optOMCategoryPrint.arguments[2].defaultOpt;

		var stepOpt = document.getElementById("cat_opt" + step);

		if ( stepOpt == null ) // select tag 생성
		{
			var stepOpt=document.createElement('select');
			document.getElementById("cat_div" + step).appendChild(stepOpt);
			stepOpt.setAttribute('id', "cat_opt" + step );
		}

		var cates = response.getElementsByTagName( "cate" );
		for ( i = 0; i < cates.length; i++ )
		{
			stepOpt.options[i]=new Option(cates[i].getElementsByTagName('name')[0].firstChild.data, cates[i].getElementsByTagName('code')[0].firstChild.data );
			if ( defaultOpt[ (step-1) ] != '' && defaultOpt[ (step-1) ] == cates[i].getElementsByTagName('code')[0].firstChild.data ) stepOpt.selectedIndex = i;
		}

		stepOpt.setAttribute('size', 10 );
		if( stepOpt.length == 0 ) stepOpt.options[i]=new Option('', '' ); // IE(emtpy option->size=1 방지)

		if ( step < 4 )
		{
			stepOpt.onchange = function() { optOMCategory( (step + 1), defaultOpt ); };
			optOMCategory( (step + 1), defaultOpt );
		}
	}
	else {
		var msg = req.getResponseHeader("Status");
		if ( msg == null || msg.length == null || msg.length <= 0 )
			alert( "Error! Request status is " + req.status );
		else
			alert( msg );
	}
}

/*** 오픈마켓 분류저장 요청 ***/
function saveOMCategory()
{
	var tmp_code = new Array();
	var tmp_name = new Array();

	for ( i = 1; i <= 4; i++ )
	{
		var stepOpt = document.getElementById("cat_opt" + i);
		if ( stepOpt.value != '' )
		{
			tmp_code.push( stepOpt.value );
			tmp_name.push( stepOpt.options[stepOpt.selectedIndex].text );
		}
	}

	var om_category = tmp_code.join('');
	var om_name = tmp_name.join(' > ');

	var urlStr = "../open/om.ajax.php?mode=saveOMCategory&om_category=" + om_category + "&category=" + category + "&dummy+" + new Date().getTime();
	var ajax = new Ajax.Request( urlStr,
	{
		method: "get",
		onComplete: saveOMCategoryPrint,
		pars: {om_category:om_category,om_name:om_name}
	} );
}

/*** 오픈마켓 분류저장 응답 ***/
function saveOMCategoryPrint( req )
{
	if ( req.status == 200 )
	{
		var response = req.responseText;
		var om_category = saveOMCategoryPrint.arguments[2].om_category;
		var om_name = saveOMCategoryPrint.arguments[2].om_name;

		if ( window.opener && !window.opener.closed && response == 'OK' && om_name != '' )
		{
			var opentr = opener.document.getElementById('om_Category').rows[rowIdx];
			opentr.cells[1].innerHTML = om_name;
			opentr.cells[2].innerHTML = '<a href="javascript:;" onclick="popup(\'../open/om.popup.category.php?category=' + category + '&defaultOpt=' + om_category + '&rowIdx=\' + this.parentNode.parentNode.rowIndex,650,500);"><img src="../img/btn_open_cateedit.gif"></a>';
		}
		opener.focus();
		top.window.close();
	}
	else {
		var msg = req.getResponseHeader("Status");
		if ( msg == null || msg.length == null || msg.length <= 0 )
			alert( "Error! Request status is " + req.status );
		else
			alert( msg );
	}
}

/*** 오픈마켓 분류명 출력 ***/
function getOMCategoryName( code, idnm )
{
	var urlStr = "./om.ajax.php?mode=getOMCategoryName&code=" + code + "&dummy+" + new Date().getTime();
	var ajax = new Ajax.Updater( idnm, urlStr,
	{
		method: "get"
	} );
}

/*** 오픈마켓 상품처리 클래스 ***/
registerOMGood =  {
	begin: function ()
	{
		AGM.act({'onStart' : this.startCallback, 'onRequest' : this.requestCallback, 'onCloseBtn' : this.closeBtnCallback, 'onErrorCallback' : 0});
	},

	startCallback: function (grp)
	{
		grp.layoutTitle = "오픈마켓 등록중 ...";
		grp.bMsg['chkEmpty'] = "선택된 상품이 없습니다. 등록하실 상품을 체크박스에 체크해 주세요.";
		grp.bMsg['chkCount'] = "총 __count__개의 상품 등록을 요청하셨습니다.";
		grp.bMsg['start'] = "상품등록을 시작합니다.";
		grp.bMsg['end'] = "상품등록이 종료되었습니다.";

		grp.articles = new Array();
		grp.iobj = new Array();
		grp.iobj.push(document.getElementsByName('chk[]'));

		if (grp.iobj[0][0].type == 'hidden')
			grp.articles.push(0);
		else {
			var count = grp.iobj[0].length;
			for (idx = 0; idx < count ; idx++)
				if (grp.iobj[0][idx].checked === true) grp.articles.push(idx);
		}
	},

	requestCallback: function (grp, idx)
	{
		var urlStr = "../open/om.ajax.php?mode=registerOMGood&goodsno=" + grp.iobj[0][idx].value + "&dummy=" + new Date().getTime();
		var ajax = new Ajax.Request( urlStr,
		{
			method: "get",
			onComplete: function ()
			{
				var req = ajax.transport;
				if (req.status == 200) grp.complete(req);
				else grp.error(req);
			}
		} );
	},

	closeBtnCallback: function (grp, btnDiv)
	{
		for ( i = 0; i < grp.running.length; i++ )
			if ( grp.running[i][1] == true )
			{
				btnDiv.childNodes[0].href = "javascript:closeLayer(); document.location.reload();";
				break;
			}
	}
}

/*** 오픈마켓 시스템 사용제어 ***/
function om_useControl()
{
	var layoutBody = document.documentElement.getElementsByTagName('table')[0].rows[1].cells[1].getElementsByTagName('table')[0].rows[0].cells[0];

	var warning = "\
		<div style='border:solid 1px #bf0000; background-color:#FEE8EC; padding:10px; margin: 10px 0;'>\
		<h1 style='font-size:14px; font-weight:bold; color:#bf0000; margin:0px;'>서비스 기간이 만료되어 본 서비스를 이용할 수 없습니다.</h1>\
		<?=$dot?>남은기간: <font class=ver81><b style='color:#FF6600'><?=betweenDate(date('Ymd'),$godo[edate])?></b> 일\
		<a href='http://www.godo.co.kr/mygodo/login.php' target=_new><img src='../img/btn_addperiod.gif' border=0 align=absmiddle hspace=2></a>\
		</div>\
		";

	var msgDiv = document.createElement('div');
	msgDiv.innerHTML = warning;
	layoutBody.insertBefore(msgDiv, layoutBody.getElementsByTagName('div')[0].nextSibling);

	var aTag = layoutBody.getElementsByTagName('a');
	for ( i = 0; i < aTag.length; i++ )
		if ( aTag[i].childNodes[0].src != null && aTag[i].childNodes[0].src.match(/\/img\/(btn_q.gif|btn_addperiod.gif)$/));
		else
		{
			aTag[i].href = "javascript:alert('서비스 기간이 만료되어 본 서비스를 이용할 수 없습니다.');";
			aTag[i].target = "_self";
			aTag[i].onclick = "";
		}

	var formTag = layoutBody.getElementsByTagName('input');
	for ( i = 0; i < formTag.length; i++ )
		if ( formTag[i].type == 'image' )
			formTag[i].disabled = true;
}