{ // ���� ����
	var isDOM = ( document.getElementById ? true : false );	// ������ ���� üũ
	var isIE4 = ( navigator.userAgent.indexOf("MSIE") >= 0 || navigator.userAgent.indexOf("Trident") >= 0 ? true : false );
	var isNS4 = ( navigator.userAgent.indexOf("MSIE") < 0 && navigator.userAgent.indexOf("Trident") < 0 ? true : false );
}

/*-------------------------------------
�ϰ� ����
-------------------------------------*/
function file_batch()
{
	if ( !confirm( "��� ���Ͽ� �ϰ� �����Ͻðڽ��ϱ�?\n�����ϸ� �Ʒ��� ���� �����˴ϴ�.\n-----------------------------\n\n* ���Ÿ�� : �⺻�ٹ̱�\n* �ϴ�Ÿ�� : �⺻�ٹ̱�\n* ����Ÿ�� : �⺻�ٹ̱�\n* ������ġ : �⺻�ٹ̱�\n* ������ : Emtpy\n* ����̹��� : Emtpy" ) ) return;

	var fobj = document.createElement("form");
	document.getElementById('jsmotion').appendChild(fobj);

	fobj.method = "post";
	fobj.action = "../design/codi/indb.php?mode=batch";
	fobj.submit();
}

/*-------------------------------------
 DESIGN_CODI MOVE
-------------------------------------*/
function designcodeMove(sobj)
{
	var text = sobj.options[sobj.selectedIndex].text;
	var path = sobj.options[sobj.selectedIndex].getAttribute('path');
	if (path != 'noprint' && path != ''){
		document.location.href='../design/iframe.codi.php?design_file=' + path;
	}
	else if (path == 'noprint' && sobj.value == 'default'){
		alert("�⺻Ÿ���� �����Դϴ�. �����̸� �ҽ��� ������ �� �����ϴ�.");
	}
	else if (sobj.value == 'noprint'){
		alert(text + "�̸� �ҽ��� ������ �� �����ϴ�.");
	}
	else {
		alert("������ �����ϼž� �մϴ�.");
	}
}

/*** DESIGN_CODI DIR METHOD (DCDM) ***/
DCDM = {
	oDirfiles : null,

	/*-------------------------------------
	 ���
	-------------------------------------*/
	write: function ()
	{
		for (i = 0; i < 2; i++){ // HTML DISPLAY
			document.write("<select style='margin-right:2px;' idx=" + i + " name='dirfiles[]' onchange='DCDM.request(this)'></select>");
		}

		this.oDirfiles = document.getElementsByName('dirfiles[]');

		this.build();
		if (this.oDirfiles != null && this.oDirfiles[0] != null) this.request(this.oDirfiles[0]);
	},

	/*-------------------------------------
	 �ɼ� �ʱ�ȭ
	-------------------------------------*/
	build: function ()
	{
		if (this.oDirfiles == null) return;

		for (i = 0; i < this.oDirfiles.length; i++)
		{
			this.oDirfiles[i].options[0] = new Option( "= " + ( i + 1 ) + "�� ���� =", "" );
			this.oDirfiles[i].options[0].style.backgroundColor = "#eeeeee";
		}
	},

	/*-------------------------------------
	 �ش� �������丮 ȣ��
	-------------------------------------*/
	request: function (obj, val)
	{
		var cThis = this;
		if ( !val ) val = "";
		var idx = obj.getAttribute( 'idx' );

		for (i = parseInt(idx + 1); i < this.oDirfiles.length; i++ ){
			for ( j = this.oDirfiles[i].options.length; j > 0; j-- ) this.oDirfiles[i].remove(j);
			this.oDirfiles[i].options.selectedIndex = 0;
		}

		var urlStr = "./_ajax.php?mode=getDir&idx=" + idx + "&val=" + val + "&dirfiles=" + obj.value + "&dummy=" + new Date().getTime();
		var ajax = new Ajax.Request( urlStr,
		{
			method: "get",
			onComplete: function ()
			{
				var req = ajax.transport;
				if ( req.status != 200 ) return;
				act = eval( '(' + req.responseText + ')' );
				if (act.length){
					for (i = 0; i < act.length; i++){
						cThis.update (act[i]['ret'], act[i]['dirfiles'], act[i]['val']);
					}
				}
			}
		} );
	},

	/*-------------------------------------
	 �ش� �������丮 ����
	-------------------------------------*/
	update: function (ret, dirfiles, val)
	{
		var idx = 0;

		if (dirfiles != '')
		{
			var tmp = dirfiles.split( "/" );
			idx = tmp.length - 1;
		}

		if (this.oDirfiles == null || this.oDirfiles[idx] == null) return;
		var sobj = this.oDirfiles[idx];

		if (typeof( sobj ) == "object" && ret)
		{
			div = ret.split( "||" );

			for (i = sobj.options.length; i > 0; i--) sobj.remove(1);

			for (i = 0; i < div.length; i++)
			{
				div2 = div[i].split( "|" );
				sobj.options[ i + 1 ] = new Option( div2[0], div2[1] );

				var tmp = div2[1].replace( /\//gi, "\\/" );
				var reg = eval( "/^" + tmp + "/g" );
				if ( val.match(reg) ) sobj.selectedIndex = i + 1;
			}
		}
	}
}

/*** DESIGN_CODI CREATE METHOD (DCCM) ***/
DCCM = {
	/*-----------------------------
	���� �����ϱ� üũ&����
	-----------------------------*/
	chk: function (fobj)
	{
		var dir_name = '';

		for (i = 1; i >= 0; i--)
		{
			if (fobj['dirfiles[]'][i].value != '')
			{
				dir_name = fobj['dirfiles[]'][i].value;
				break;
			}
		}

		if (dir_name == '')
		{
			alert("��Ƴ��� ������ ���� �����ϼ���.");
			fobj['dirfiles[]'][0].focus();
			return false;
		}

		if (fobj.file_name.value == '')
		{
			alert("���ϸ��� �Է��ϼ���.");
			fobj.file_name.focus();
			return false;
		}

		if (fobj.file_result .value == "" || fobj.file_result.value == "N")
		{
			alert("���ϸ� �ߺ����θ� Ȯ������ �ʾҽ��ϴ�.");
			fobj.file_name.focus();
			return false;
		}

		if (fobj.file_desc.value == '')
		{
			alert("������ �Է��ϼ���.");
			fobj.file_desc.focus();
			return false;
		}

		fobj.design_file.value = dir_name + fobj.file_name.value + fobj.file_ext.value;

		return true;
	},

	/*-----------------------------
	��밡�� ���ϸ� ���θ� üũ�Ѵ�.
	-----------------------------*/
	file_check: function ()
	{
		var fobj = document.create;
		var dir_name = '';

		for (i = 1; i >= 0; i--)
		{
			if (fobj['dirfiles[]'][i].value != '')
			{
				dir_name = fobj['dirfiles[]'][i].value;
				break;
			}
		}

		if (dir_name == '')
		{
			alert("��Ƴ��� ������ ���� �����ϼ���.");
			fobj['dirfiles[]'][0].focus();
			return;
		}

		if (fobj.file_name.value == '')
		{
			alert("���ϸ��� �Է��ϼ���.");
			fobj.file_name.focus();
			return;
		}

		fobj.file_name.value = fobj.file_name.value.replace(/(<|>|:|\"|\'|\/|\\|\||\?|\*)/g,'_');

		var urlStr = "./_ajax.php?mode=chkFile&dir_name=" + dir_name + "&file_name=" + fobj.file_name.value + "&file_ext=" + fobj.file_ext.value + "&dummy=" + new Date().getTime();
		var ajax = new Ajax.Request( urlStr,
		{
			method: "get",
			onComplete: function ()
			{
				var req = ajax.transport;
				if ( req.status != 200 ) return;
				file_unexist = req.responseText;
				if (file_unexist == 'Y'){
					alert(fobj.file_name.value + fobj.file_ext.value + " �� ��밡���� ���ϸ��Դϴ�");
					fobj.file_result.value = file_unexist;
				} else {
					alert(fobj.file_name.value + fobj.file_ext.value + " �� �̹� ���� �ִ� ���ϸ��Դϴ�.\n�ٸ� ���ϸ����� �˻����ּ���.");
				}
			}
		} );
	}
}

/*** DESIGN_CODI SAVEAS METHOD (DCSM) ***/
DCSM = {
	/*-----------------------------
	���̸����� ���� üũ&����
	-----------------------------*/
	chk: function (fobj)
	{
		if (!chkForm( document.fm )) return false;

		if (fobj.file_name.value == '')
		{
			alert("���ϸ��� �Է��ϼ���.");
			fobj.file_name.focus();
			return false;
		}

		if (fobj.file_result .value == "" || fobj.file_result.value == "N")
		{
			alert("���ϸ� �ߺ����θ� Ȯ���ϼ���.");
			fobj.file_name.focus();
			return false;
		}

		if (fobj.file_desc.value == '')
		{
			alert("������ �Է��ϼ���.");
			fobj.file_desc.focus();
			return false;
		}

		document.fm.text.value = fobj.file_desc.value;
		document.fm.action = '../design/codi/indb.php?mode=saveas&design_file=' + fobj.dir_name.value + fobj.file_name.value + fobj.file_ext.value;
		document.fm.submit();

		return false;
	},

	/*-----------------------------
	��밡�� ���ϸ� ���θ� üũ�Ѵ�.
	-----------------------------*/
	file_check: function ()
	{
		var fobj = document.save;

		if (fobj.file_name.value == '')
		{
			alert("���ϸ��� �Է��ϼ���.");
			fobj.file_name.focus();
			return;
		}

		var urlStr = "../design/codi/_ajax.php?mode=chkFile&dir_name=" + fobj.dir_name.value + "&file_name=" + fobj.file_name.value + "&file_ext=" + fobj.file_ext.value + "&dummy=" + new Date().getTime();
		var ajax = new Ajax.Request( urlStr,
		{
			method: "get",
			onComplete: function ()
			{
				var req = ajax.transport;
				if ( req.status != 200 ) return;
				file_unexist = req.responseText;
				if (file_unexist == 'Y'){
					alert(fobj.file_name.value + fobj.file_ext.value + " �� ��밡���� ���ϸ��Դϴ�");
					fobj.file_result.value = file_unexist;
				} else {
					alert(fobj.file_name.value + fobj.file_ext.value + " �� �̹� ���� �ִ� ���ϸ��Դϴ�.\n�ٸ� ���ϸ����� �˻����ּ���.");
				}
			}
		} );
	},

	/*-----------------------------
	'���ϻ���' ���̾� ��Ʈ��
	-----------------------------*/
	call: function (flg)
	{
		if ( flg == 'on' ) document.getElementById('div_saveas').style.display = 'block';
		else document.getElementById('div_saveas').style.display = 'none';
		setHeight_ifrmCodi();
	}
}

/*-----------------------------
�����ϱ�
-----------------------------*/
function file_del(design_file)
{
	if (!confirm( "��" + design_file + "�������� �����Ͻðڽ��ϱ�?" )) return;

	var fobj = document.createElement("form");
	document.getElementById('jsmotion').appendChild(fobj);

	fobj.method = "post";
	fobj.action = "../design/codi/indb.php?mode=del&design_file=" + design_file;
	fobj.submit();
}

/*** DESIGN_CODI TEXTAREA METHOD (DCTM) ***/
DCTM = {
	textarea_copy_body: 'copy_body',
	textarea_user_body: 'user_body',
	textarea_base_body: 'base_body',
	textarea_user_view: 'user_view',
	textarea_base_view: 'base_view',
	textarea_view_id: 'user_body',

	/*-------------------------------------
	 ��� (Textarea �̸�, ����, �ټ�, �Ӽ�, ����)
	-------------------------------------*/
	write: function (t_name, t_width, t_rows, t_property, tplFile)
	{
		document.write('\
		<style type="text/css">\
		#textarea { width:' + t_width + '; margin:0; }\
		#textarea .head { padding:5; background:#ECE9D8; }\
		#textarea .icon { border-style: none; width: 15px; height: 15px; }\
		#textarea .body { padding-bottom:5; background:#ECE9D8; }\
		#textarea #base_body { display:none; color:#00EC37; background:#000000; }\
		#textarea #copy_body { display:none; }\
		#textarea .tail { padding:0 5 5 5; background:#7F7F7F; }\
		#user_view, #base_view { font:9pt tahoma; border-style:solid; border-width:0; margin:0; }\
		#user_view { color:#222222; background:#ECE9D8; }\
		#base_view { color:#FFFFFF; background:#7F7F7F; }\
		.txt_do8	 {font-size:8pt; font-family:"����"; color:#646464; letter-spacing:-1;}\
		</style>\
		\
		<div id="textarea">\
		\
		<div class="head">\
		<input type="button" class="icon" style="background:url(../img/btn_ae_webftp.gif);width:97px; no-repeat 0 bottom;" title="WebFTP �̹��� ����(ActiveX)" onclick="javascript:popup2(\'popup.webftp_activex.php\',760,610,0)">\
		<input type="button" class="icon" style="background:url(../img/codi/btn_webftp.gif);width:87px;" title="WebFTP �̹��� ����" onclick="DCTM.webftp();">\
		<input type="button" class="icon" style="background:url(../img/codi/btn_stylesheet.gif);width:63px;" title="��Ÿ�Ͻ�Ʈ����" onclick="DCTM.stylesheet();">\
		<input type="button" class="icon" style="background:url(../img/codi/btn_colortable1.gif);width:29px;" title="����ǥ ����" onclick="DCTM.colortable();">\
		<input type="button" class="icon" style="background:url(../img/codi/btn_pageurl.gif);width:64px;" title="��������ũ" onclick="DCTM.pagelink();">\
		<input type="button" class="icon" style="background:url(../img/codi/btn_templetip.gif);width:73px;" title="���ø� Ȱ�����" onclick="DCTM.template();">\
		<input type="button" class="icon" style="background:url(../img/codi/btn_designtip.gif);width:40px;" title="������Ȱ����" onclick="DCTM.manual();">\
		<input type="button" class="icon" style="background:url(../img/codi/btn_codeinput.gif);width:40px;" title="�ڵ�ҽ��Է�" onclick="DCTM.put_codi();">\
		<input type="button" class="icon" style="background:url(../img/codi/bu_01.gif);width:43px;" title="�ٹٲ� ����/����" onclick="DCTM.textarea_wrap();">\
		<input type="button" class="icon" style="background:url(../img/codi/bu_04.gif);width:38px;" title="��ĭ���ø����" onmousedown="DCTM.row_start();DCTM.row_control( \'+\' );" onmouseup="DCTM.row_stop();" onmouseout="DCTM.row_stop();">\
		<input type="button" class="icon" style="background:url(../img/codi/bu_03.gif);width:38px;" title="��ĭ�����̱�" onmousedown="DCTM.row_start();DCTM.row_control( \'-\' );" onmouseup="DCTM.row_stop();" onmouseout="DCTM.row_stop();">\
		<input type="button" class="icon" style="background:url(../img/codi/bu_05.gif);width:54px;" title="ũ�Դø���" onclick="DCTM.row_direct( 50 );">\
		<input type="button" class="icon" style="background:url(../img/codi/bu_06.gif);width:32px;" title="�⺻" onclick="DCTM.row_direct( ' + t_rows + ' );">\
		<input type="button" class="icon" style="background:url(../img/codi/bu_07.gif);width:58px;" title="�൵�����" onclick="javascript:alert(\'���� �൵����� API ���񽺰� ����Ǿ����ϴ�. ���������� �൵����� ���񽺸� �̿��Ͽ� �츮ȸ���� �൵�� �־��ּ���\');window.open(\'http://blog.daum.net/daummaps/482\');">\
		<br>\
		<div id="resetting" class=noline><label for="codeact"><input type="checkbox" name="codeact" id="codeact" onclick="DCTM.codeBaseInput( this )"> <font class="txt_do8">�������� (�����ҽ��� �����˴ϴ�)</font></label></div>\
		</div>\
		\
		<div class="body">\
		<textarea id="user_body" class="tline" name="' + t_name + '" style="width:100%" rows="' + t_rows + '" onkeydown="DCTM.textarea_useTab( this, event );" wrap="off" ' + t_property + '></textarea>\
		<textarea id="base_body" name="base_' + t_name + '" style="width:100%" rows="' + t_rows + '" onkeydown="DCTM.textarea_useTab( this, event );" wrap="off">�����ҽ�</textarea><textarea id="copy_body"></textarea>\
		</div>\
		\
		<div class="tail"><input type="button" ID="user_view" value="�����ҽ�" onclick="DCTM.textarea_view( this )"><input type="button" ID="base_view" value="�����ҽ�����" onclick="DCTM.textarea_view( this )"></div>\
		\
		</div>\
		');

		this.source(tplFile, 'user_body');
		this.source(tplFile, 'base_body');
	},

	/*-------------------------------------
	 �ҽ����� �ε�
	-------------------------------------*/
	source: function (tplFile, body)
	{
		if (body != 'user_body' && body != 'base_body') return;

		var urlStr = "../design/codi/_ajax.php?mode=getTextarea&body=" + body + "&tplFile=" + tplFile + "&dummy=" + new Date().getTime();
		var ajax = new Ajax.Request( urlStr,
		{
			method: "get",
			onComplete: function ()
			{
				var req = ajax.transport;
				if ( req.status != 200 ) return;
				document.getElementById( body ).value = req.responseText;
			}
		} );
	},

	/*-------------------------------------
	 �ҽ����� ����ó��
	-------------------------------------*/
	textarea_view: function ( obj )
	{
		if ( obj.id == this.textarea_base_view )
		{
			this.textarea_view_id = this.textarea_base_body;

			document.getElementById( this.textarea_user_body ).style.display = 'none';
			document.getElementById( this.textarea_base_body ).style.display = 'block';

			document.getElementById( this.textarea_user_view ).style.color = '#FFFFFF';
			document.getElementById( this.textarea_user_view ).style.background = '#7F7F7F';

			document.getElementById( this.textarea_base_view ).style.color = '#222222';
			document.getElementById( this.textarea_base_view ).style.background = '#ECE9D8';
		}
		else
		{
			this.textarea_view_id = this.textarea_user_body;

			document.getElementById( this.textarea_user_body ).style.display = 'block';
			document.getElementById( this.textarea_base_body ).style.display = 'none';

			document.getElementById( this.textarea_user_view ).style.color = '#222222';
			document.getElementById( this.textarea_user_view ).style.background = '#ECE9D8';

			document.getElementById( this.textarea_base_view ).style.color = '#FFFFFF';
			document.getElementById( this.textarea_base_view ).style.background = '#7F7F7F';
		}
	},

	/*-------------------------------------
	 �����ҽ� �Է�
	-------------------------------------*/
	codeBaseInput: function ( CObj, auto )
	{
		var idObj = document.getElementById('resetting');

		var codyObj = document.getElementById( this.textarea_copy_body );
		var userObj = document.getElementById( this.textarea_user_body );
		var baseObj = document.getElementById( this.textarea_base_body );

		if ( CObj.checked )
		{
			if ( baseObj.value == '' )
			{
				if ( auto != true ) alert( "�� ��ġ�� �����ҽ��� �������� �ʽ��ϴ�." );
				CObj.checked = false;
				idObj.style.color = '#000000';
				idObj.style.fontWeight = 'normal';
			}
			else
			{
				codyObj.value = userObj.value;
				userObj.value = baseObj.value;
				idObj.style.color = '#bf0000';
				idObj.style.fontWeight = 'bold';
			}
		}
		else
		{
			userObj.value = codyObj.value;
			idObj.style.fontColor = '#000000';
			idObj.style.color = '#000000';
			idObj.style.fontWeight = 'normal';
		}

		this.textarea_view( userObj );
	},

	/*-------------------------------------
	 TEXTAREA �ټ� ���� ����
	-------------------------------------*/

	control_stop: 1,

	row_start: function ()
	{
		this.control_stop = 0;
	},

	/*-------------------------------------
	 TEXTAREA �ټ� ���� ����
	-------------------------------------*/
	row_stop: function ()
	{
		this.control_stop = 1;
	},

	/*-------------------------------------
	 TEXTAREA �ټ� ����
	-------------------------------------*/
	row_control: function ( plug )
	{
		var TObj = eval( "document.getElementById( '" + this.textarea_view_id + "' )" );

		if ( this.control_stop != 1 && ( plug == '+' || plug == '-' ) )
		{
			if ( plug == '+' && TObj.rows >= 50 )
			{
				alert( "50���� ������ ������ �� �ֽ��ϴ�." );
				this.row_stop();
				return;
			}
			else if ( plug == '-' && TObj.rows <= 1 )
			{
				alert( "1���� ������ ������ �� �ֽ��ϴ�." );
				this.row_stop();
				return;
			}

			TObj.rows = eval( "TObj.rows " + plug + " 1" );
			setHeight_ifrmCodi();
			setTimeout( "DCTM.row_control( '"  + plug + "' )", 100 );
		}
		else
		{
			this.row_stop();
			return;
		}
	},

	/*-------------------------------------
	 TEXTAREA �ټ� ����
	-------------------------------------*/
	row_direct: function ( num )
	{
		var TObj = eval( "document.getElementById( '" + this.textarea_view_id + "' )" );
		TObj.rows = num;
		setHeight_ifrmCodi();
	},

	/*-------------------------------------
	 TEXTAREA �ٹٲ� ����/����
	-------------------------------------*/
	textarea_wrap: function ()
	{
		if ( isNS4 == true ) alert( '�ͽ��÷η������� �����˴ϴ�.' );
		else
		{
			var TObj = eval( "document.getElementById( '" + this.textarea_view_id + "' )" );

			if ( TObj.wrap == 'off' ) TObj.wrap = 'soft';
			else TObj.wrap = 'off';
		}
	},

	/*-------------------------------------
	 TEXTAREA ��Ű ��밡��
	-------------------------------------*/
	textarea_useTab: function ( el, e )
	{
		e = (e) ? e : ((event) ? event : null );

		if ( isNS4 == true );
		else
		{
			if ( event.keyCode == 9 )
			{
				if (window.getSelection) {
					var selectionStart = el.selectionStart;
					var selectionEnd = el.selectionEnd;
				} else {
					var t = ( el.selection = document.selection.createRange() );
				}

				var seletText = (window.getSelection) ? el.value.substring(selectionStart, selectionEnd) : t.text;

				if (event.shiftKey == false) {
					if(seletText == '') seletText = "\t";
					else  seletText = "\t" + seletText.replace( /\n/gi, '\n\t' );
				} else {
					if(seletText != '') seletText = seletText.replace( /^\t/gi, '' ).replace( /\n\t/gi, '\n' );
				}

				if(window.getSelection){
					el.value = 	el.value.substring(0,selectionStart) + seletText + el.value.substring(selectionEnd);
					el.selectionStart = el.selectionEnd = selectionStart + 1;
				} else {
					t.text = seletText;
				}

				e.preventDefault ? e.preventDefault() : event.returnValue = false;
			}
		}
	},

	/*-------------------------------------
	 �ڵ�ҽ��Է�
	-------------------------------------*/
	put_codi: function ()
	{
		var userObj = document.getElementById( this.textarea_user_body );
		userObj.value = "{ # header }" + "\n\n" + "{ # footer }" + "\n" + userObj.value;
	},

	/*-------------------------------------
	 ����ǥ ����
	-------------------------------------*/
	colortable: function ()
	{
		var hrefStr = '../proc/help_colortable.php';
		var win = popup_return( hrefStr, 'colortable', 400, 400, 200, 200, 0 );
		win.focus();
	},

	/*-------------------------------------
	 ������ ��ũ ����
	-------------------------------------*/
	pagelink: function ()
	{
		var hrefStr = './popup.link.php';
		var win = popup_return( hrefStr, 'pagelink', 700, 700, 100, 50, 1 );
		win.focus();
	},

	/*-------------------------------------
	 WebFTP
	-------------------------------------*/
	webftp: function ()
	{
		var hrefStr = '../design/popup.webftp.php';
		var win = popup_return( hrefStr, 'webftp', 900, 800, 50, 50, 1 );
		win.focus();
	},

	/*-------------------------------------
	 Stylesheet
	-------------------------------------*/
	stylesheet: function ()
	{
		var hrefStr = '../design/iframe.css.php';
		var win = popup_return( hrefStr, 'stylesheet', 900, 650, 100, 100, 1 );
		win.focus();
	},

	/*-------------------------------------
	 manual
	-------------------------------------*/
	manual: function ()
	{
		var hrefStr = 'http://www.godo.co.kr/edu/edu_board_list.html?cate=design#Go_view';
		var win = window.open( hrefStr, 'manual' );
		win.focus();
	},

	/*-------------------------------------
	 Template_
	-------------------------------------*/
	template: function ()
	{
		var hrefStr = 'http://gongji.godo.co.kr/userinterface/help_template.php';
		var win = popup_return( hrefStr, 'template', 900, 800, 50, 50, 1 );
		win.focus();
	}
}

/*** DESIGN_CODI MAP METHOD (DCMAPM) ***/
DCMAPM = {

	file_outline: function (key)
	{
		if (key == '') return;
		if (this.point == null || this.point[ key ] == null) return;

		var key_prop = this.point[ key ]; // �ܰ� ��ġ�� �Ӽ��ε�

		if ( document.getElementById( key ).value == 'default' ) var val = key_prop['default_val']; // �⺻Ÿ���� ���
		else var val = document.getElementById( key ).value; // �׿�

		if ( val == 'noprint' ) // ������ ���
			document.getElementById( key_prop['map_point'] ).style.background = 'url(../img/codi/' + key_prop['map_point'] + '_off.gif) no-repeat';
		else if ( val != '' ) // �׿�
		{
			var list = key_prop['img_list'];
			if ( list[ val ] != '' ) document.getElementById( key_prop['map_point'] ).style.background = 'url(' + list[ val ] + ') no-repeat'; // �����̹��� �ִ� ���
			else document.getElementById( key_prop['map_point'] ).style.background = 'url(../img/codi/' + key_prop['map_point'] + '_on.gif) no-repeat'; // �����̹��� ���� ���
		}

		if ( key_prop['map_point'] == 'map_footer' ) document.getElementById( key_prop['map_point'] ).style.backgroundPosition  = 'bottom right';
	},

	file_float: function (float_type)
	{
		if (typeof(float_type) == 'object'){
			var ele = document.getElementsByName(float_type.name);
			for ( i = 0; i < ele.length; i++ ){
				if ( ele[i].checked ) float_type = ele[i].getAttribute('float');
			}
		}

		if ( navigator.userAgent.indexOf("MSIE") >= 0 || navigator.userAgent.indexOf("Trident") >= 0 ){
			_ID('frame_side').style.styleFloat  = float_type;
		}
		else{
			_ID('frame_side').style.cssFloat  = float_type;
			if (float_type == 'left'){
				_ID('frame_body').getElementsByTagName('div')[0].style.marginLeft = '3px';
				_ID('frame_body').getElementsByTagName('div')[0].style.marginRight = '0';
			}
			else if (float_type == 'right'){
				_ID('frame_body').getElementsByTagName('div')[0].style.marginLeft = '0';
				_ID('frame_body').getElementsByTagName('div')[0].style.marginRight = '3px';
			}
		}
	}

}

/*** DESIGN_CODI REPLACECODE METHOD (DCRM) ***/
DCRM = {
	write: function (design_file)
	{
		var lay = document.getElementById('codi_replacecode');

		var dNode = lay.appendChild( document.createElement('div') );
		dNode.style.backgroundImage = "url(../img/codi/tab_bg.gif)";
		dNode.innerHTML = '<a HREF="javascript:;" onclick="DCRM.onoff(\'' + design_file + '\', this)"><img src="../img/codi/tab_codeview.gif"></a><a HREF="javascript:;" onclick="DCRM.onoff(\'public.xml\', this)"><img src="../img/codi/tab_commoncode.gif"></a><a href="javascript:DCTM.template();"><img src="../img/codi/tab_templetip.gif"></a>';

		var dNode = lay.appendChild( document.createElement('div') );
		dNode.setAttribute('id', 'codi_public1');

		var dNode = lay.appendChild( document.createElement('div') );
		dNode.setAttribute('id', 'codi_recode1');

		var dNode = lay.appendChild( document.createElement('div') );
		dNode.setAttribute('id', 'MSG10');
		dNode.innerHTML = '<table cellpadding=1 cellspacing=0 border=0 class=small_ex>\
		<tr><td><img src="../img/icon_list.gif" align="absmiddle"><b>ġȯ�ڵ��?</b> ���α׷����� ��Ҹ� �ڵ�� ��ȯ�Ͽ� Ȱ���ϴ� ����Դϴ�.</td></tr>\
		<tr><td><img src="../img/icon_list.gif" align="absmiddle">[�ڵ庹��] ��ư�� Ŭ���ϸ� ġȯ�ڵ尡 ����˴ϴ�. ���ϴ� ��ġ�� \'�ٿ��ֱ�(Ctrl+V)\'�Ͽ� ����Ͻø� ���մϴ�.</td></tr>\
		</table>';
		cssRound('MSG10');
		setHeight_ifrmCodi();
	},

	onoff: function (design_file, aObj)
	{
		if (design_file == 'public.xml') var lay = document.getElementById('codi_public1');
		else var lay = document.getElementById('codi_recode1');
		lay.style.display = (lay.style.display == 'block' ? 'none' : 'block');

		if (design_file != 'public.xml' && lay.style.display == 'block') aObj.innerHTML = '<img src="../img/codi/tab_codeview1.gif">';
		if (design_file != 'public.xml' && lay.style.display == 'none') aObj.innerHTML = '<img src="../img/codi/tab_codeview.gif">';
		if (design_file == 'public.xml' && lay.style.display == 'block') aObj.innerHTML = '<img src="../img/codi/tab_commoncode1.gif">';
		if (design_file == 'public.xml' && lay.style.display == 'none') aObj.innerHTML = '<img src="../img/codi/tab_commoncode.gif">';

		if (lay.style.display == 'block' && lay.innerHTML == '') this.load(design_file);
		else {
			document.location.href = "#" + lay.id;
			setHeight_ifrmCodi();
		}
	},

	load: function (design_file, aObj)
	{
		var urlStr = "../design/codi/_ajax.php?mode=getReplacecode&design_file=" + design_file;
		var ajax = new Ajax.Request( urlStr,
		{
			method: "get",
			onComplete: function ()
			{
				var req = ajax.transport;
				if ( req.status != 200 ){
					var msg = req.getResponseHeader("Status");
					if ( msg == null || msg.length == null || msg.length <= 0 ) msg = 'Error! Request status is ' + req.status;
					alert(msg);
					return;
				}

				DCRM.display(req.responseXML, design_file, aObj);
				setHeight_ifrmCodi();
			}
		} );
	},

	display: function (xml, design_file, aObj)
	{
		if (aObj != null)
		{
			var divid = aObj.parentNode.parentNode.parentNode.parentNode.parentNode.getAttribute('id');
			var idstr = divid.substr(0,11);
			var anchorNm = nextid = idstr.toString() + (eval(divid.replace(idstr, '')) + 1);

			var lay = document.getElementById(divid);
			if (document.getElementById(nextid)) document.getElementById(nextid).parentNode.removeChild(document.getElementById(nextid));
			var lay = lay.appendChild( document.createElement('div') );
			lay.setAttribute('id', nextid);

			var dNode = lay.appendChild( document.createElement('div') );
			dNode.className = "title_sub";
			dNode.innerHTML = aObj.parentNode.parentNode.cells[1].innerHTML.anchor(anchorNm) + " <span>" + aObj.parentNode.parentNode.cells[2].innerHTML + "</span>";
		}
		else if (design_file == 'public.xml')
		{
			var lay = document.getElementById('codi_public1');
			var anchorNm = 'codi_public1';

			var dNode = lay.appendChild( document.createElement('div') );
			dNode.className = "title";
			dNode.innerHTML = "����ġȯ�ڵ� ����Ʈ".anchor(anchorNm) + " <span>��� ���Ͽ��� ��밡���� �ڵ��Դϴ�.</span>";
		}
		else
		{
			var lay = document.getElementById('codi_recode1');
			var anchorNm = 'codi_recode1';

			var dNode = lay.appendChild( document.createElement('div') );
			dNode.className = "title";
			dNode.innerHTML = "ġȯ�ڵ� ����Ʈ".anchor(anchorNm) + " <span>�ش� ���ϳ������� ��밡���� �ڵ��Դϴ�.</span>";
		}

		var tblObj = lay.appendChild( document.createElement('table') );
		with (tblObj)
		{
			border = 1;
			borderColor = "#EBEBEB";
			cellPadding = "4";
		}
		with (tblObj.style)
		{
			width = "100%";
			borderCollapse = "collapse";
		}

		// Ÿ��Ʋ ���� ����
		newTr = tblObj.insertRow(-1);
		{
			newTd = newTr.insertCell(-1);
			with (newTd.style)
			{
				width = '40px';
				textAlign = "center";
				fontWeight = "bold";
				color = '#FFFFFF';
				background = "#4A3F38";
			}
			newTd.innerHTML = '';

			newTd = newTr.insertBefore( newTr.childNodes[0].cloneNode(true), newTd.nextSibling );
			newTd.style.width = "250px";
			newTd.innerHTML = 'ġȯ�ڵ�';

			newTd = newTr.insertBefore( newTr.childNodes[0].cloneNode(true), newTd.nextSibling );
			newTd.style.width = "";
			newTd.innerHTML = '����';

			newTd = newTr.insertBefore( newTr.childNodes[0].cloneNode(true), newTd.nextSibling );
			newTd.style.width = "200px";
			newTd.innerHTML = '����';

			newTd = newTr.insertBefore( newTr.childNodes[0].cloneNode(true), newTd.nextSibling );
			newTd.style.width = "50px";
			newTd.innerHTML = '���';
		}

		var lists = xml.getElementsByTagName( "list" );
		for ( i = 0; i < lists.length; i++ )
		{
			clipCode = lists[i].getElementsByTagName('code')[0].firstChild.data.replace(/'/gi,"\\'").replace(/"/gi, "&quot;");
			newTr = tblObj.insertRow(-1);

			// ������
			newTd = newTr.insertCell(-1);
			with (newTd.style)
			{
				width = '40px';
				textAlign = "center";
			}
			if (lists[i].getElementsByTagName('code')[0].getAttribute('power') != null) newTd.innerHTML += '<img src="../img/icon_power.gif" align="absmiddle">';
			if (lists[i].getElementsByTagName('code')[0].getAttribute('new') != null) newTd.innerHTML += '<img src="../img/icon_new.gif" align="absmiddle">';

			// ġȯ�ڵ�
			newTd = newTr.insertCell(-1);
			with (newTd.style)
			{
				width = '250px';
				paddingLeft = "10px";
				background = "#f7f7f7";
			}
			code = lists[i].getElementsByTagName('code')[0].firstChild.data.replace(/</gi, "&lt;").replace(/>/gi, "&gt;");
			newTd.innerHTML = '<A HREF="javascript:;" onclick="DCRM.clip(\'' + clipCode + '\');">' + code + '</A>';

			// ����
			newTd = newTr.insertCell(-1);
			newTd.style.paddingLeft = "10px";
			newTd.innerHTML = (lists[i].getElementsByTagName('desc')[0].firstChild != null ? lists[i].getElementsByTagName('desc')[0].firstChild.data : '');

			// ����
			newTd = newTr.insertCell(-1);
			with (newTd.style)
			{
				paddingLeft = "10px";
				width = "200px";
			}
			if (lists[i].getElementsByTagName('exam')[0].firstChild == null) exam = '';
			else exam = lists[i].getElementsByTagName('exam')[0].firstChild.data.replace(/</gi, "&lt;").replace(/>/gi, "&gt;");
			if (lists[i].getElementsByTagName('exam')[0].getAttribute('more')) exam += '<A HREF="javascript:;" onclick="DCRM.load(\'' + lists[i].getElementsByTagName('exam')[0].getAttribute('more') + '\', this);" style="color:red;"><img src="../img/btn_detailsview.gif"></A>';
			newTd.innerHTML = exam;

			// ���
			newTd = newTr.insertCell(-1);
			with (newTd.style)
			{
				width = "50px";
				textAlign = "center";
			}
			newTd.innerHTML = '<A HREF="javascript:;" onclick="DCRM.clip(\'' + clipCode + '\');"><img src="../img/btn_codecopy.gif"></A>';
		}

		if (anchorNm != null) document.location.href = "#" + anchorNm;
	},

	clip: function (code)
	{
		if (window.clipboardData) {
			alert("�ڵ带 �����Ͽ����ϴ�. \n���ϴ� ���� �ٿ��ֱ�(Ctrl+V)�� �Ͻø� �˴ϴ�~");
			window.clipboardData.setData("Text", code);
		} else {
			temp = prompt("�ڵ带 Ŭ������� ����(Ctrl+C) �Ͻð�. \n���ϴ� ���� �ٿ��ֱ�(Ctrl+V)�� �Ͻø� �˴ϴ�~", code);
		}
	}
}

document.write("<script src='../design/codi/_codi_ex.js'></script>");
