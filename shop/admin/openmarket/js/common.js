
var Cate = new Object();

/*--------------------------------------------------------------------------*/

/*** Ajax ���â ***/
Cate.ajaxWarning = function(req)
{
	var msg = req.getResponseHeader("Status");
	if ( msg == null || msg.length == null || msg.length <= 0 )
		alert( "Error! Request status is " + req.status );
	else
		alert( msg );
}

/*--------------------------------------------------------------------------*/

/*** ���¸��� �з��˻� ***/
Cate.Srch = {
	parent: Cate,

	/* �˻���û */
	begin: function ()
	{
		var self = this;
		if (chkForm(_ID("srchName").form) === false) return;
		var urlStr = "../openmarket/indb.php?mode=srchCategory&srchName=" + _ID("srchName").value + "&dummy+" + new Date().getTime();
		var ajax = new Ajax.Request( urlStr,
		{
			method: "get",
			onLoading: this.loading,
			onComplete: function ()
			{
				var req = ajax.transport;
				if ( req.status == 200 ) self.complete(req.responseXML.documentElement);
				else self.parent.ajaxWarning(req);
			}
		} );
	},

	/* �ε��� �޽��� */
	loading: function ()
	{
		var tblObj = _ID('srchCatePrint').getElementsByTagName('table')[0];
		while ( tblObj.rows.length > 0 ) tblObj.deleteRow( tblObj.rows.length - 1); // ��� rows �ʱ�ȭ

		newTr = tblObj.insertRow(-1);
		newTr.style.background='#FFFFFF';
		newTr.style.height='130';

		newTd = newTr.insertCell(-1);
		newTd.colSpan = 2;
		newTd.style.textAlign ='center';
		newTd.innerHTML = '<font color=6d6d6d>�з��� �˻��ϰ� �ֽ��ϴ�. ��ø� ��ٷ� �ּ���.</font>';
	},

	/* �˻������� */
	complete: function (response)
	{
		var tblObj = _ID('srchCatePrint').getElementsByTagName('table')[0];
		while ( tblObj.rows.length > 0 ) tblObj.deleteRow( tblObj.rows.length - 1); // ��� rows �ʱ�ȭ

		var cates = response.getElementsByTagName( "cate" );
		for ( i = 0; i < cates.length; i++ )
		{
			newTr = tblObj.insertRow(-1);
			newTr.style.background='#FFFFFF';
			newTr.style.height='20';

			newTd = newTr.insertCell(-1);
			newTd.style.textAlign ='center';
			newTd.innerHTML = '<a href="javascript:Cate.stepCate.begin(1, \'' + cates[i].getElementsByTagName('code')[0].firstChild.data  +'\');"><img src="../img/btn_openmarket_cateselect.gif"></a>';

			newTd = newTr.insertCell(-1);
			newTd.innerHTML = cates[i].getElementsByTagName('name')[0].firstChild.data; // cates[i].childNodes[0].firstChild.data <= ���ϰ�ü ����Ŵ
		}

		if (cates.length == 0){
			newTr = tblObj.insertRow(-1);
			newTr.style.background='#FFFFFF';
			newTr.style.height='130';

			newTd = newTr.insertCell(-1);
			newTd.colSpan = 2;
			newTd.style.textAlign ='center';
			newTd.innerHTML = '<font color=6d6d6d>�˻��� ����� �����ϴ�. �ٽ� �˻��� �ּ���.</font>';
		}
	}
}

var callSrch = function() { Cate.Srch.begin(); return false; };

/*--------------------------------------------------------------------------*/

/*** ���¸��� �з����� ***/
Cate.stepCate = {
	parent: Cate,

	/* �з���û */
	begin: function (step)
	{
		var self = this;

		var defaultOpt = new Array;
		if ( arguments[1] != null ) // �ʱ⼱�ð�
		{
			if ( typeof(arguments[1]) != 'object' ) defaultOpt = arguments[1].toString().split("|");
			else defaultOpt = arguments[1];
		}

		idx = (_ID('stepCate') != null ? 0 : 1);
		for ( i = step; i <= 4; i++ ) // select option �ʱ�ȭ
		{
			var stepOpt = _ID("cat_opt" + i);
			if ( stepOpt == null ) // select tag ����
			{
				var stepOpt=document.createElement('select');
				_ID("cat_div" + i).appendChild(stepOpt);
				stepOpt.setAttribute('id', "cat_opt" + i );
				stepOpt.setAttribute('name', "cate[]");
				if (_ID('stepCate') == null) stepOpt.options[0]=new Option('= '+ i +'�� �з� =', '' );
			}
			if ( stepOpt.options.length == 0 ) break;
			while ( idx < stepOpt.options.length ) stepOpt.options[ (stepOpt.options.length - 1) ] = null;
		}

		var tmp = new Array();
		for ( i = 1; i < step; i++ ) // �з�ȣ�� ��û ����Ÿ ����
		{
			var stepOpt = _ID("cat_opt" + i);
			if ( stepOpt == null ) break;
			if ( stepOpt.value == '' && (i+1) == step ) return;
			tmp.push(stepOpt.value);
		}
		var callCate = tmp.join('|');

		var urlStr = "../openmarket/indb.php?mode=stepCategory&callCate=" + callCate + "&dummy+" + new Date().getTime();
		var ajax = new Ajax.Request( urlStr,
		{
			method: "get",
			onComplete: function ()
			{
				var req = ajax.transport;
				if ( req.status == 200 ) self.complete(req.responseXML.documentElement, step, defaultOpt);
				else self.parent.ajaxWarning(req);
			}
		} );
	},

	/* �з���� */
	complete: function (response, step, defaultOpt)
	{
		var stepOpt = _ID("cat_opt" + step);
		var cates = response.getElementsByTagName( "cate" );
		for ( i = 0; i < cates.length; i++ )
		{
			idx = (_ID('stepCate') != null ? i : (i + 1));
			stepOpt.options[idx]=new Option(cates[i].getElementsByTagName('name')[0].firstChild.data, cates[i].getElementsByTagName('code')[0].firstChild.data );
			if ( defaultOpt[ (step-1) ] != '' && defaultOpt[ (step-1) ] == cates[i].getElementsByTagName('code')[0].firstChild.data ) stepOpt.selectedIndex = idx;
		}

		if (_ID('stepCate') != null) stepOpt.setAttribute('size', 10 );
		if( stepOpt.length == 0 ) stepOpt.options[idx]=new Option('', '' ); // IE(emtpy option->size=1 ����)

		if ( step < 4 )
		{
			stepOpt.onchange = function() { Cate.stepCate.begin( (step + 1), defaultOpt ); };
			Cate.stepCate.begin( (step + 1), defaultOpt );
		}
	}
}

var callStepCate = function(catno) { Cate.stepCate.begin(1, catno); };

/*--------------------------------------------------------------------------*/

/*** ���¸��� �з����� ***/
Cate.applyCate = {
	parent: Cate,
	catno: '',
	catnm: '',

	begin: function ()
	{
		var tmp_catno = new Array();
		var tmp_catnm = new Array();

		for ( i = 1; i <= 4; i++ )
		{
			var stepOpt = _ID("cat_opt" + i);
			if (stepOpt.length > 0 && stepOpt.value == '' && stepOpt.options[0].value != ''){
				alert("������ �з����� �����ϼž� �մϴ�.");
				return;
			}
			if ( stepOpt.value != '' )
			{
				tmp_catno.push( stepOpt.value );
				tmp_catnm.push( stepOpt.options[stepOpt.selectedIndex].text );
			}
		}

		this.catno = tmp_catno.join('|');
		this.catnm = tmp_catnm.join(' > ');

		if (idnm) this.select();
		else if (category && rowIdx) this.save();
	},

	/* �з����� */
	select: function ()
	{
		parent._ID(idnm).value = this.catno;
		var html = '<a href="javascript:popupLayer(\'../openmarket/popup.category.php?defaultOpt='+ this.catno +'&idnm='+ idnm +'&\'+new Date().getTime(),650,550);">';
		parent._ID(idnm + '_text').innerHTML = html + "<u>"+ this.catnm +"</u></a>";
		parent.closeLayer();
	},

	/* �з����� */
	save: function ()
	{
		var self = this;
		var samelow = (_ID('samelow').checked ? 'Y' : '');
		var urlStr = "../openmarket/indb.php?mode=saveCategory&catno=" + this.catno + "&category=" + category + "&samelow=" + samelow + "&dummy+" + new Date().getTime();
		var ajax = new Ajax.Request( urlStr,
		{
			method: "get",
			onComplete: function ()
			{
				var req = ajax.transport;
				if ( req.status == 200 ) self.complete(req.responseText, samelow);
				else self.parent.ajaxWarning(req);
			}
		} );
	},

	/* �з���� */
	complete: function (response, samelow)
	{
		if ( response == 'OK' && this.catnm != '' )
		{
			if (samelow == '')
			{
				var opentr = parent._ID('cateMatchList').rows[rowIdx];
				opentr.cells[1].innerHTML = this.catnm;
				opentr.cells[2].innerHTML = '<img src="../img/btn_openmarket_cateedit.gif" style="cursor:pointer;" onclick="popupLayer(\'../openmarket/popup.category.php?category=' + category + '&defaultOpt=' + this.catno + '&rowIdx=\'+this.parentNode.parentNode.rowIndex+\'&\'+new Date().getTime(),650,550);">';
			}
			else {
				catno_obj = parent.document.getElementsByTagName('catno');
				for (i=0; i < catno_obj.length; i++)
				{
					if ( catno_obj[i].getAttribute('category').match( eval("/^"+category+"/") ) )
					{
						var opentr = catno_obj[i].parentNode.parentNode;
						opentr.cells[1].innerHTML = this.catnm;
						opentr.cells[2].innerHTML = '<img src="../img/btn_openmarket_cateedit.gif" style="cursor:pointer;" onclick="popupLayer(\'../openmarket/popup.category.php?category=' + category + '&defaultOpt=' + this.catno + '&rowIdx=\'+this.parentNode.parentNode.rowIndex+\'&\'+new Date().getTime(),650,550);">';
					}
				}
			}
		}
		parent.closeLayer();
	}
}

var callApply = function() { Cate.applyCate.begin(); };

/*--------------------------------------------------------------------------*/

/*** ���¸��� �з��� ��� ***/
Cate.printCateNm = {
	parent: Cate,

	begin: function (catno, idnm, mode)
	{
		var self = this;
		var urlStr = "../openmarket/indb.php?mode=getCategoryName&catno=" + catno + "&dummy+" + new Date().getTime();
		var ajax = new Ajax.Request( urlStr,
		{
			method: "get",
			onComplete: function ()
			{
				var req = ajax.transport;
				if ( req.status == 200 ) self.complete(req.responseText, catno, idnm, mode);
			}
		} );
	},

	/* �з������ */
	complete: function (response, catno, idnm, mode)
	{
		if (mode == 'link'){
			var html = '<a href="javascript:popupLayer(\'../openmarket/popup.category.php?defaultOpt='+ catno +'&idnm='+ idnm +'&\'+new Date().getTime(),650,550);">';
			_ID(idnm + '_text').innerHTML = html + "<u>"+ (response == '' ? '�̰��� Ŭ���ؼ� ī�װ��� �Է����ּ���.' : response) +"</u></a>";
		}
		else {
			_ID(idnm).innerHTML = response;
		}
	}
}

var callCateNm = function(catno, idnm, mode) { Cate.printCateNm.begin(catno, idnm, mode); };

/*--------------------------------------------------------------------------*/

/*** ���¸��� ������ǰ���� ***/
quickRegister =  {
	begin: function ()
	{
		AGM.act({'onStart' : this.startCallback, 'onRequest' : this.requestCallback, 'onCloseBtn' : this.closeBtnCallback, 'onErrorCallback' : 0});
	},

	startCallback: function (grp)
	{
		grp.layoutTitle = "���¸��� �ǸŰ����� ������ ...";
		grp.bMsg['chkEmpty'] = "���õ� ��ǰ�� �����ϴ�. �����Ͻ� ��ǰ�� üũ�ڽ��� üũ�� �ּ���.";
		grp.bMsg['chkCount'] = "�� __count__���� ��ǰ ������ ��û�ϼ̽��ϴ�.";
		grp.bMsg['start'] = "��ǰ������ �����մϴ�.";
		grp.bMsg['end'] = "��ǰ������ ����Ǿ����ϴ�.";

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
		var tmp = new Array();
		var fieldnm = new Array('goodsnm', 'category', 'origin', 'price', 'stock', 'maker', 'brandnm', 'goodscd', 'shortdesc');
		for (i = 0; i < fieldnm.length; i++){
			tmp.push(fieldnm[i] + "=" + document.getElementsByName( fieldnm[i] )[idx].value.replace(/\+/gi, '%2B').replace(/ /gi, '+').replace(/&/gi, '%26'));
		}
		var query = tmp.join("&");
		var urlStr = "../openmarket/indb.php?mode=quickRegister&goodsno="+ grp.iobj[0][idx].value +"&"+ query +"&dummy=" + new Date().getTime();
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

var callQuickRegister = function() { quickRegister.begin(); };

/*--------------------------------------------------------------------------*/

/*** ���¸��� ������ǰ���� ***/
quickModify =  {
	begin: function ()
	{
		AGM.act({'onStart' : this.startCallback, 'onRequest' : this.requestCallback, 'onCloseBtn' : this.closeBtnCallback, 'onErrorCallback' : 0});
	},

	startCallback: function (grp)
	{
		grp.layoutTitle = "��ǰ������ ...";
		grp.bMsg['chkEmpty'] = "���õ� ��ǰ�� �����ϴ�. �����Ͻ� ��ǰ�� üũ�ڽ��� üũ�� �ּ���.";
		grp.bMsg['chkCount'] = "�� __count__���� ��ǰ ������ ��û�ϼ̽��ϴ�.";
		grp.bMsg['start'] = "��ǰ������ �����մϴ�.";
		grp.bMsg['end'] = "��ǰ������ ����Ǿ����ϴ�.";

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
		var tmp = new Array();
		var fieldnm = new Array('goodsnm', 'category', 'origin', 'price', 'stock', 'maker', 'brandnm', 'goodscd', 'shortdesc');
		for (i = 0; i < fieldnm.length; i++){
			tmp.push(fieldnm[i] + "=" + document.getElementsByName( fieldnm[i] )[idx].value.replace(/\+/gi, '%2B').replace(/ /gi, '+').replace(/&/gi, '%26'));
		}
		var query = tmp.join("&");
		var urlStr = "../openmarket/indb.php?mode=quickModify&goodsno="+ grp.iobj[0][idx].value +"&"+ query +"&dummy=" + new Date().getTime();
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

var callQuickModify = function() { quickModify.begin(); };

/*--------------------------------------------------------------------------*/

/*** ���¸��� ������ üũ ***/
Useable = {
	begin: function (idnm)
	{
		var self = this;
		var urlStr = "../openmarket/indb.php?mode=getUseable&dummy+" + new Date().getTime();
		var ajax = new Ajax.Request( urlStr,
		{
			method: "get",
			onComplete: function ()
			{
				var req = ajax.transport;
				if ( req.status == 200 ) self.complete(req.responseText, idnm);
			}
		} );
	},

	/* ���޽������ */
	complete: function (response, idnm)
	{
		var html = '<div style="border:solid 2px #5D644A; background:#F5FF9F; padding:5px 10px; margin:10px 0 20px 0;"><img src="../img/ico_warning.gif" align="absmiddle" style="margin-right:10px;">{msg}</div>';
		if (response == 'false-need:join'){
			_ID(idnm).innerHTML = html.replace(/{msg}/, '�Ŀ����¸����� ��û�ϼž� ����� �����մϴ�. <a href="../openmarket/request.php"><b>[��û�ϱ�]</b></a>');
		}
		else if (response == 'false-need:extension'){
			_ID(idnm).innerHTML = html.replace(/{msg}/, '�Ŀ����¸����� �����ϼž� ����� �����մϴ�. <a href="../openmarket/request.php"><b>[�����ϱ�]</b></a>');
		}
	}
}

var callUseable= function(idnm) { Useable.begin(idnm); };

/*--------------------------------------------------------------------------*/

function chkLen(obj, len, id)
{
	str = obj.value;
	if (str.length > len){
		alert(len +"�ڱ����� �Է��� �����մϴ�");
		obj.value = str.substring(0, len);
	}
	_ID(id).innerHTML = obj.value.length;
}

/*--------------------------------------------------------------------------*/