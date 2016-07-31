var mini_path = '';
var upload_chk = 1;
var r_miniEditorTextarea = new Array();
var mini_color_mode = new Array();
var r_selection = new Array();
var browser = navigator.userAgent;
var docMode = (parent.document.compatMode=='CSS1Compat'?'Standards':'Quirks'); // CSS1Compat:표준(DTD), BackCompat:쿼크

var e = document.getElementsByTagName('script')
for(var i=0; i<e.length; i++) {
	if(e[i].src && /(^|\/)mini_editor\.js([?#].*)?$/i.test(e[i].src)) {
		var thisScriptDir = e[i].src.replace(/[^\/]+$/, '');
		break;
	}
}

/**
 * AJAX 연결
 * @return Object AJAX 연결객체
 */
function getXMLHttpRequest()
{
	if (window.XMLHttpRequest) {
		return new window.XMLHttpRequest;
	}
	else {
		try {
			return new ActiveXObject('MSXML2.XMLHTTP.3.0');
		}
		catch(ex) {
			return null;
		}
	}
}

/**
 * 에디터 실행
 * @param String path 에디터실행경로
 * @param Int chk 그림삽입 사용여부
 */
function mini_editor(path,chk)
{
	mini_path = path;

	var css_url = mini_path + 'mini_editor.css';
	if (document.createStyleSheet) { // IE < 11
		document.createStyleSheet(css_url);
	}
	else {
		var link = document.createElement('link');
		link.rel = 'stylesheet';
		link.type = 'text/css';
		link.href = css_url;
		document.getElementsByTagName('head')[0].appendChild(link);
	}

	if(chk == 0) upload_chk = chk;
	var textareas = document.getElementsByTagName('textarea');
	for (var i=0;i<textareas.length;i++){
		if (textareas[i].getAttribute('type')=='editor'){
			r_miniEditorTextarea.push(textareas[i]);
		}
	}

	for (var i=0;i<r_miniEditorTextarea.length;i++){
		miniEditorFrame(r_miniEditorTextarea[i],i);
	}
}

/**
 * 커멘드
 * @param Int idx 에디터인덱스
 * @param String str 커멘드 코드
 * @param String value 커멘드 값
 */
function mini_command(idx,str,value)
{
	// selection 유지
	r_selection[idx] = null;
	if (docMode == 'Standards' && (browser.indexOf('MSIE 8.0') > 0 || browser.indexOf('MSIE 7.0') > 0 || browser.indexOf('MSIE 6.0') > 0)) { // IE8/IE7/IE6 Standards
		if (str == 'ForeColor' || str == 'BackColor') {
			r_selection[idx] = saveSelection(idx);
		}
	} else if (docMode == 'Standards' || typeof window.getSelection != 'undefined') { // Standards, IE >= 10, non-IE
		r_selection[idx] = saveSelection(idx);
	} else if (str != 'InsertTable' && str != 'InsertImage') { // IE < 9 Quirks
		r_selection[idx] = saveSelection(idx);
	}

	var mode = false;
	if (!value) value = null;
	switch (str){
		case 'FontName':
			if (value.substr(0,4) == 'web_') { // 웹폰트
				var miniEditorName = r_miniEditorTextarea[idx].getAttribute('name');
				var miniEditorId = 'miniEditorIframe_' + miniEditorName;
				var miniEditorIframe = document.getElementById(miniEditorId);
				var miniEditorContent = miniEditorIframe.contentWindow.document;

				miniEditorIframe.contentWindow.focus();
				if (typeof miniEditorIframe.contentWindow.window.getSelection != 'undefined') { // IE9 Standards, IE >= 10, non-IE
					sel = miniEditorIframe.contentWindow.window.getSelection();
					if (sel.getRangeAt && sel.rangeCount) {
						range = miniEditorIframe.contentWindow.window.getSelection().getRangeAt(0);
						node = miniEditorContent.createElement('span');
						node.setAttribute('class', value.substr(4));
						documentFragment = range.cloneContents();
						node.appendChild(documentFragment);
						range.deleteContents();
						range.insertNode(node);
						return;
					}
				} else if (miniEditorContent.selection && miniEditorContent.selection.type != 'Control') { // IE9 Quirks, IE < 9
					range = miniEditorContent.selection.createRange();
					range.pasteHTML('<span class="'+value.substr(4)+'">'+range.htmlText+'</span>');
					return;
				}
			}
			break;
		case 'ForeColor': case 'BackColor':
			var ready = true;
			if (document.getElementById('mini_color_box'+idx).style.display=='block' && str!=mini_color_mode[idx]) mini_vlayer(idx,'mini_color_box');
			break;
		case 'CreateLink':
			mode = true;
			break;
		case 'InsertTable':
			mini_vlayer(idx,'mini_table_box');
			return; break;
		case 'InsertImage':
			window.open(mini_path + 'popup.image.php?idx=' + idx,'','width=400,height=510');
			return; break;
	}

	mini_command_exec(idx,str,mode,value,ready);
}

/**
 * 커멘드 실행
 * @param Int idx 에디터인덱스
 * @param String str 커멘드 코드
 * @param Bool mode 커멘드 취소여부
 * @param String value 커멘드 값
 * @param Bool ready 커멘드 실행여부
 */
function mini_command_exec(idx,str,mode,value,ready)
{
	if (r_selection[idx] != 'undefined') { // selection 유지
		restoreSelection(idx, r_selection[idx]);
	}

	var miniEditorName = r_miniEditorTextarea[idx].getAttribute('name');
	var miniEditorId = 'miniEditorIframe_' + miniEditorName;
	var miniEditorIframe = document.getElementById(miniEditorId);
	var miniEditorContent = miniEditorIframe.contentWindow.document;

	if (typeof(ready)=='undefined'){
		if (str == 'CreateLink' && typeof miniEditorIframe.contentWindow.window.getSelection != 'undefined'){ // IE9 Standards, IE >= 10, non-IE
			mode = false;
			value = prompt('URL : ','http://');
			if ( value == 'http://' ) value = '';
			if ( value == '' ) miniEditorContent.execCommand('UnLink', mode, '');
		}
		if (str == 'CreateLink' && mode == false && value == '');
		else miniEditorContent.execCommand(str, mode, value);
	}
	mini_reset(idx,str);
	miniEditorIframe.contentWindow.focus();

	if (str=='ForeColor' || str=='BackColor'){
		mini_color_mode[idx] = str;
		mini_vlayer(idx,'mini_color_box');
	}
}

/**
 * 폰트 글꼴 커멘드 구성
 * @param Int idx 에디터인덱스
 * @return String Tag
 */
function mini_set_font(idx)
{
	var oReq = getXMLHttpRequest();
	if (oReq != null) {
		oReq.open('GET',thisScriptDir+'/webfont.php?r='+Math.floor(Math.random()*99999), true);
		oReq.onreadystatechange = function(){
			if (oReq.readyState == 4 && oReq.status == 200 ) {
				var result = eval(oReq.responseText);
				if(!result.length) return;
				var selBox = document.getElementById('mini_btn' + idx + 'FontName');
				for(i=0;i<result.length;i++) {
					var tmp = document.createElement('option');
					tmp.setAttribute('value',result[i].code);
					tmp.innerText=result[i].name;
					selBox.appendChild(tmp);
				}
				selBox.style.width=100;
			}
		};
		oReq.send();
	}
	else {
		window.alert('AJAX (XMLHTTP) not supported.');
	}

	var name = new Array('굴림','돋움','바탕','궁서','굴림체','Arial','Courier','Tahoma');
	var ret = "<select id=mini_btn" + idx + "FontName style='font:9pt tahoma' onchange=\"mini_command(" + idx + ",'FontName',this[this.selectedIndex].value)\"><option>Font";
	for (var i=0;i<name.length;i++){
		ret += "<option value='" + name[i] + "'>" + name[i];
	}
	ret += '</select>';
	return ret;
}

/**
 * 폰트 사이즈 커멘드 구성
 * @param Int idx 에디터인덱스
 * @return String Tag
 */
function mini_set_size(idx)
{
	var ret = "<select id=mini_btn" + idx + "FontSize style='font:9pt tahoma' onchange=\"mini_command(" + idx + ",'FontSize',this[this.selectedIndex].value)\"><option>Size";
	for (var i=1;i<=7;i++){
		ret += "<option value='" + i + "'>" + i;
	}
	ret += '</select>';
	return ret;
}

/**
 * 커멘드 버튼 구성
 * @param Int idx 에디터인덱스
 * @param Int chk 그림삽입 사용여부
 * @return String Tag
 */
function mini_set_btn(idx,mode)
{
	if(mode == 'InsertImage' && upload_chk == 0) return '';
	return "<img id=mini_btn" + idx + mode + " src='" + mini_path + "img/btn_" + mode + ".gif' onClick=\"mini_command(" + idx + ",'" + mode + "')\" onmouseover=mini_btn_over(this) onmousedown=mini_btn_down(this) onmouseup=mini_btn_up(this) onmouseout=mini_btn_out2(this) style='border:1px solid #ffffff;cursor:pointer'>";
}

/**
 * 커멘드 버튼 활성화
 * @param Object obj 버튼객체
 * @param Variant ret 활성화여부
 */
function mini_btn_onoff(obj, ret)
{
	obj = document.getElementById(obj);
	if (!obj.disabled){
		if (ret) mini_btn_down(obj);
		else mini_btn_out(obj);
	}
}

/**
 * 커멘드 버튼 마우스다운
 * @param Object obj 버튼객체
 */
function mini_btn_down(obj)
{
	with (obj.style){
		borderBottom	= 'buttonhighlight 1px solid';
		borderLeft		= 'buttonshadow 1px solid';
		borderRight		= 'buttonhighlight 1px solid';
		borderTop		= 'buttonshadow 1px solid';
	}
}

/**
 * 커멘드 버튼 마우스업
 * @param Object obj 버튼객체
 */
function mini_btn_up(obj)
{
	with (obj.style){
		borderBottom	= 'buttonshadow 1px solid';
		borderLeft		= 'buttonhighlight 1px solid';
		borderRight		= 'buttonshadow 1px solid';
		borderTop		= 'buttonhighlight 1px solid';
	}
}

/**
 * 커멘드 버튼 마우스오버
 * @param Object obj 버튼객체
 */
function mini_btn_over(obj)
{
	if (obj.style.borderBottom != 'buttonhighlight 1px solid') mini_btn_up(obj);
}

/**
 * 커멘드 버튼 마우스아웃
 * @param Object obj 버튼객체
 */
function mini_btn_out(obj)
{
	obj.style.borderColor = '#ffffff';
}

/**
 * 커멘드 버튼 마우스아웃
 * @param Object obj 버튼객체
 */
function mini_btn_out2(obj)
{
	if (obj.style.borderBottom != 'buttonhighlight 1px solid') obj.style.borderColor = '#ffffff';
}

/**
 * 폰트 색상 커멘드박스 구성
 * @param Int idx 에디터인덱스
 * @return String Tag
 */
function mini_color_box(idx)
{
	var ret = '';
	var arr = new Array(
			'#FFC0C0','#FFF000','#FFFFE0','#E0FFE0','#C0E0FF','#30C0FF','#F0C0FF','#FFFFFF',
			'#FF8080','#FFC000','#FFFF80','#80FFC0','#C0E0FF','#2080D0','#FF80FF','#C0C0C0',
			'#FF0000','#FF8000','#FFFF00','#00FF00','#00FFFF','#0000FF','#FF00FF','#808080',
			'#800000','#604800','#808000','#008000','#008080','#000080','#800080','#000000'
			);
	for (var i=0;i<arr.length;i++){
		if (i && i%8==0) ret += '</tr><tr>';
		ret += "<td style='width:15px;height:15px;border-top:1px solid #000000;border-left:1px solid #000000;background:" + arr[i] + "' onClick=\"mini_command_exec(" + idx + ",mini_color_mode[" + idx + "], false, '" + arr[i] + "')\"><div style='width:100%;height:100%;cursor:pointer;'></div></td>";
	}
	ret = "<div id=mini_color_box" + idx + " style='position:absolute;display:none;border:2px solid #efefef;padding:3px;background:#f7f7f7'><table><tr>" + ret + "</tr></table></div>";
	return ret;
}

/**
 * 테이블 커멘드박스 구성
 * @param Int idx 에디터인덱스
 * @return String Tag
 */
function mini_table_box(idx)
{
	var ret = '<table class="mini-editor-table-drawer" id="mini_table_inner' + idx + '" border="1" bordercolor="#cccccc" style="border-collapse:collapse;">';
	for (var i=0;i<10;i++){
		ret += "<tr>";
		for (var j=0;j<7;j++) ret += "<td style='width:20px;height:15px;font-size:0;' onmouseover='mini_chk_table(" + idx + "," + j + "," + i + ")' ><a href='javascript:mini_set_table(" + idx + "," + j + "," + i + ")' style='width:100%;height:100%;display:block'></a></td>";
		ret += "</tr>";
	}
	ret += "</table><div id=mini_table_status" + idx + " style='font:8pt tahoma;border:1px solid #cccccc;width:100%;height:20px;margin-top:3px;padding-top:2px;background:#f7f7f7' align=center></div>";
	ret = "<div id=mini_table_box" + idx + " style='position:absolute;display:none;border:2px solid #efefef;padding:3px;background:#ffffff'>" + ret + "</div>";
	return ret;
}

/**
 * 툴바 구성
 * @param Int idx 에디터인덱스
 * @return String Tag
 */
function mini_set_toolbar(idx)
{
	var ret = '\
	<table class="mini-editor-toolbar" id=frame_toolbar' + idx + ' cellpadding=0 cellspacing=0>\
	<tr>\
		<td bgcolor=#f7f7f7 style="padding:4px 5px 0 10px;border:1px solid #cccccc">\
		<table width=100% cellpadding=0 cellspacing=0>\
		<tr>\
			<td style="padding-right:10px" height=28 nowrap>\
			<a href="javascript:miniEditorMode(' + idx + ',\'editor\')"><img id=btn_editor' + idx + ' src="' + mini_path + 'img/btn_editor.gif" border=0 align=absmiddle style="display:none"></a>\
			<a href="javascript:miniEditorMode(' + idx + ',\'source\')"><img id=btn_source' + idx + ' src="' + mini_path + 'img/btn_source.gif" border=0 align=absmiddle></a>\
			</td>\
			<td id=toolbar' + idx + ' width=100%>\
			<table cellpadding=2 cellspacing=0>\
			<tr>\
				<td>' + mini_set_font(idx) + '</td>\
				<td>' + mini_set_size(idx) + '</td>\
				<td>' + mini_set_btn(idx,"Bold") + mini_set_btn(idx,"Italic") + mini_set_btn(idx,"Underline") + mini_set_btn(idx,"StrikeThrough") + '</td>\
				<td><img src="' + mini_path + 'img/seperator.gif"></td>\
				<td>' + mini_set_btn(idx,"ForeColor") + mini_set_btn(idx,"BackColor") + '</td>\
				<td><img src="' + mini_path + 'img/seperator.gif"></td>\
				<td>' + mini_set_btn(idx,"JustifyLeft") + mini_set_btn(idx,"JustifyCenter") + mini_set_btn(idx,"JustifyRight") + '</td>\
				<td><img src="' + mini_path + 'img/seperator.gif"></td>\
				<td>' + mini_set_btn(idx,"InsertTable") + mini_set_btn(idx,"CreateLink") + mini_set_btn(idx,"InsertImage") + '</td>\
			</tr>\
			<tr>\
				<td colspan=3></td>\
				<td>' + mini_color_box(idx) + '</td>\
				<td colspan=3></td>\
				<td>' + mini_table_box(idx) + '</td>\
			</tr>\
			</table>\
			</td>\
		</tr>\
		</table>\
		</td>\
	</tr>\
	</table>\
	';
	return ret;
}

var mini_bHeader;

/**
 * 에디터 프레임 구성
 * @param Object obj 에디터 Textarea 객체
 * @param Int idx 에디터인덱스
 */
function miniEditorFrame(obj,idx)
{
	var miniEditorContent;
	var miniEditorTextarea = obj;

	var html_tag = "<html>";
	var meta_tag = "";
	if (docMode == "Standards") {
		html_tag = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"><html xmlns="http://www.w3.org/1999/xhtml" lang="ko" xml:lang="ko">';
		meta_tag = '<meta http-equiv="X-UA-Compatible" content="IE=edge" />';
		if (browser.indexOf('MSIE 8.0') > 0 && docMode == 'Standards') {
			meta_tag = '<meta http-equiv="X-UA-Compatible" content="IE=EmulateIE7">';
		}
	}

	mini_bHeader = html_tag + "\
	<html>\
	<head>\
	" + meta_tag + "\
		<meta http-equiv='content-type' content='text/html; charset=euc-kr'>\
	";

	if (
		browser.indexOf('Gecko') > 0
		 || browser.indexOf('MSIE 10.0') > 0
		 || (browser.indexOf('MSIE 9.0') > 0 && docMode == 'Standards')
		 || (browser.indexOf('MSIE 8.0') > 0 && docMode == 'Standards')
		 || (browser.indexOf('MSIE 7.0') > 0 && docMode == 'Standards')
		 || (browser.indexOf('MSIE 6.0') > 0 && docMode == 'Standards')
		 || browser.indexOf('Chrome') > 0
		 || browser.indexOf('Firefox') > 0
	) {
		mini_bHeader += "\
		<style>body {margin:10px;} body,table {font:small 돋움} p {margin:2px 0} td,th {border:1px #bfbfbf dotted}</style>\
		";
	} else {
		mini_bHeader += "\
		<style>body {margin:10px;} body,table {font:x-small 돋움} p {margin:2px 0} td,th {border:1px #bfbfbf dotted}</style>\
		";
	}

	mini_bHeader += "\
		<link rel='styleSheet' href='"+thisScriptDir+"webfont_css.php'>\
	</head>\
	<body>\
	</body>\
	</html>\
	";

	with (miniEditorTextarea.style){
		padding = '10px';
		backgroundColor = '#414141';
		color = '#FFFFE0';
		display = 'none';
		border = '1px solid #cccccc';
	}

	var miniEditorName = miniEditorTextarea.getAttribute('name');
	var miniEditorId = 'miniEditorIframe_' + miniEditorName;
	var miniEditorIframe = document.createElement('iframe');
	miniEditorIframe.setAttribute('scrolling','yes');
	miniEditorIframe.setAttribute('wrap','virtual');
	miniEditorIframe.setAttribute('frameBorder',0);
	miniEditorIframe.setAttribute('id',miniEditorId);
	miniEditorIframe.style.marginTop = '1px';
	miniEditorIframe.style.border = '1px solid #cccccc';
	miniEditorIframe.style.width = miniEditorTextarea.style.width;
	miniEditorIframe.style.height = (miniEditorTextarea.style.height.replace('px','') - 2).toString() + 'px';

	miniEditorTextarea.parentNode.insertBefore(miniEditorIframe,miniEditorTextarea);

	var objToolbar = document.createElement('div');
	objToolbar.innerHTML = mini_set_toolbar(idx);
	miniEditorIframe.parentNode.insertBefore(objToolbar,miniEditorIframe);
	document.getElementById('frame_toolbar'+idx).style.width = miniEditorTextarea.style.width;

	miniEditorContent = document.getElementById(miniEditorId).contentWindow.document;
	miniEditorContent.designMode = 'on';
	miniEditorContent.open();
	miniEditorContent.write(mini_bHeader);

	if (document.attachEvent){ // IE < 11
		miniEditorContent.body.innerHTML = '';
		miniEditorContent.write(miniEditorTextarea.value);
		miniEditorContent.close();
		miniEditorContent.body.attachEvent('onclick',function(){mini_reset(idx)},false);
		document.getElementById(miniEditorId).attachEvent('onblur',function(){var rng = miniEditorContent.body.createTextRange(); miniEditorTextarea.value = rng.htmlText; if (miniEditorTextarea.value=='<P>&nbsp;</P>') miniEditorTextarea.value = '';},false);
	} else if (document.addEventListener){ // IE >= 11, non-IE
		miniEditorContent.close();
		miniEditorContent.body.innerHTML = miniEditorTextarea.value;
		miniEditorContent.addEventListener('click',function(){mini_reset(idx)},false);
		miniEditorIframe.contentWindow.addEventListener('blur',miniCopyToHtml,false);
	}

	var _form = miniEditorTextarea;
	var _find = false;

	try
	{
		do
		{
			_form = _form.parentNode;
			if (_form.tagName == 'FORM') {
				_find = true;
			}
		}
		while (_find == false);
	}
	catch (e) {
		_form = null;
	}
	finally {
		if (_form != null)
		{
			_form.onreset = function() {
				miniEditorContent.body.innerHTML = '';
				return;
			}
		}
	}
}

/**
 * 에디터 Textarea 값 재정의
 */
function miniCopyToHtml() // IE >= 11, non-IE
{
	for (var i=0;i<r_miniEditorTextarea.length;i++){
		var miniEditorName = r_miniEditorTextarea[i].getAttribute('name');
		var miniEditorId = 'miniEditorIframe_' + miniEditorName;
		var miniEditorIframe = document.getElementById(miniEditorId);
		var miniEditorContent = miniEditorIframe.contentWindow.document;
		r_miniEditorTextarea[i].value = miniEditorContent.body.innerHTML;
		if (r_miniEditorTextarea[i].value=='<br>') r_miniEditorTextarea[i].value = '';
	}
}

/**
 * 에디터 모드 전환
 * @param Int idx 에디터인덱스
 * @param String mode 모드값(editor|source)
 */
function miniEditorMode(idx,mode)
{
	var miniEditorName = r_miniEditorTextarea[idx].getAttribute('name');
	var miniEditorId = 'miniEditorIframe_' + miniEditorName;
	var miniEditorIframe = document.getElementById(miniEditorId);
	var miniEditorContent = miniEditorIframe.contentWindow.document;

	switch (mode){
		case 'editor':
			r_miniEditorTextarea[idx].style.display = 'none';
			miniEditorIframe.style.display = 'block';
			if (document.attachEvent){ // IE < 11
				miniEditorContent.open();
				miniEditorContent.write(mini_bHeader);
				miniEditorContent.body.innerHTML = '';
				miniEditorContent.write(r_miniEditorTextarea[idx].value);
				miniEditorContent.close();
				miniEditorContent.body.attachEvent('onclick',function(){mini_reset(idx)},false);
				document.getElementById(miniEditorId).attachEvent('onblur',function(){var rng = miniEditorContent.body.createTextRange(); r_miniEditorTextarea[idx].value = rng.htmlText; if (r_miniEditorTextarea[idx].value=='<P>&nbsp;</P>') r_miniEditorTextarea[idx].value = '';},false);
			} else if (document.addEventListener){ // IE >= 11, non-IE
				miniEditorContent.body.innerHTML = r_miniEditorTextarea[idx].value;
			}
			document.getElementById('btn_editor'+idx).style.display = 'none';
			document.getElementById('btn_source'+idx).style.display = 'block';
			document.getElementById('toolbar'+idx).style.display = 'block';
			break;
		case 'source':
			r_miniEditorTextarea[idx].style.display = 'block';
			miniEditorIframe.style.display = 'none';
			document.getElementById('btn_editor'+idx).style.display = 'block';
			document.getElementById('btn_source'+idx).style.display = 'none';
			document.getElementById('toolbar'+idx).style.display = 'none';
			r_miniEditorTextarea[idx].focus();
			break;
	}
}

/**
 * 배열값 검색
 * @param String obj 검색어
 * @param Array arr 배열
 * @return Bool 검색결과
 */
function mini_in_array(el,arr)
{
	var ret = false;
	for (var i=0;i<arr.length;i++){
		if (el==arr[i]){
			ret = true;
			break;
		}
	}
	return ret;
}

/**
 * 에디터 커멘드 리셋
 * @param Int idx 에디터인덱스
 * @param String obj 커멘드 코드
 */
function mini_reset(idx,obj)
{
	var r_obj = new Array();
	var arr = new Array('Bold','Italic','Underline','StrikeThrough','FontName','FontSize');

	var miniEditorName = r_miniEditorTextarea[idx].getAttribute('name');
	var miniEditorId = 'miniEditorIframe_' + miniEditorName;
	var miniEditorIframe = document.getElementById(miniEditorId);
	var miniEditorContent = miniEditorIframe.contentWindow.document;

	miniEditorContent.body.focus();

	if (!obj) r_obj = arr;
	else if (mini_in_array(obj,arr)) r_obj[0] = obj;

	for (var i=0;i<r_obj.length;i++){
		switch (r_obj[i]){
			case 'FontName': case 'FontSize':
				mini_set_select('mini_btn' + idx + r_obj[i], miniEditorContent.queryCommandValue(r_obj[i]));
				break;
			default:
				mini_btn_onoff('mini_btn' + idx + r_obj[i], miniEditorContent.queryCommandValue(r_obj[i]));
				break;
		}
	}

	if (!obj){
		if (document.getElementById('mini_table_box'+idx).style.display=='block') mini_vlayer(idx,'mini_table_box');
		if (document.getElementById('mini_color_box'+idx).style.display=='block') mini_vlayer(idx,'mini_color_box');
	}
}

/**
 * Selected 처리
 * @param Object obj Select Element
 * @param String ret Select value
 */
function mini_set_select(obj,ret)
{
	obj = document.getElementById(obj);
	for (var i=0;i<obj.length;i++){
		if (obj.options[i].value==ret){
			obj.selectedIndex = i;
			break;
		}
	}
}

/**
 * 에디터 커멘드박스 활성화
 * @param Int idx 에디터인덱스
 * @param String obj 커멘드박스명
 */
function mini_vlayer(idx,obj)
{
	obj = document.getElementById(obj+idx);
	obj.style.display = (obj.style.display!='block') ? 'block' : 'none';
	if (obj.id=='mini_color_box'){
		var value = (obj.style.display=='block') ? true : false;
		mini_btn_onoff('mini_btn' + mini_color_mode, value);
	}
}

/**
 * HTML 삽입
 * @param Int idx 에디터인덱스
 * @param String str HTML
 */
function mini_set_html(idx,str)
{
	if (r_selection[idx] != 'undefined') { // selection 유지
		restoreSelection(idx, r_selection[idx]);
	}

	var miniEditorName = r_miniEditorTextarea[idx].getAttribute('name');
	var miniEditorId = 'miniEditorIframe_' + miniEditorName;
	var miniEditorIframe = document.getElementById(miniEditorId);
	var miniEditorContent = miniEditorIframe.contentWindow.document;

	miniEditorIframe.contentWindow.focus();
	if (typeof miniEditorIframe.contentWindow.window.getSelection != 'undefined') { // IE9 Standards, IE >= 10, non-IE
		sel = miniEditorIframe.contentWindow.window.getSelection();
		if (sel.getRangeAt && sel.rangeCount) {
			range = miniEditorIframe.contentWindow.window.getSelection().getRangeAt(0);
			range.deleteContents();
			if (range.createContextualFragment) { // IE >= 10, non-IE
				node = range.createContextualFragment(str);
			} else { // IE9 Standards
				var div = miniEditorContent.createElement('div'), child;
				div.innerHTML = str;
				node = miniEditorContent.createDocumentFragment();
				while ( (child = div.firstChild) ) {
					node.appendChild(child);
				}
			}
			var oLastNode = node.lastChild;
			range.insertNode(node);
			range.setEndAfter(oLastNode);
			range.setStartAfter(oLastNode);
			range.collapse(false);
		}
	} else if (miniEditorContent.selection && miniEditorContent.selection.type != 'Control') { // IE9 Quirks, IE < 9
		var range = miniEditorContent.selection.createRange();
		range.pasteHTML(str);
	}
}

/**
 * 테이블 열/행 표기
 * @param Int idx 에디터인덱스
 * @param Int x 열
 * @param Int y 행
 */
function mini_chk_table(idx,x,y)
{
	var obj = document.getElementById('mini_table_inner'+idx);
	for (var i=0;i<10;i++){
		for (var j=0;j<7;j++){
			obj.rows[i].cells[j].style.background = (j<=x && i<=y) ? '#316AC5' : '#ffffff';
		}
	}
	document.getElementById('mini_table_status'+idx).innerHTML = '<b>' + (x+1) + '</b> cells X <b>' + (y+1) + '</b> rows Table';
}

/**
 * 테이블 열/행 구성
 * @param Int idx 에디터인덱스
 * @param Int x 열
 * @param Int y 행
 * @param Int ff 실행여부
 */
function mini_set_table(idx,x,y,ff)
{
	if (document.all && ff) return;
	var ret = '<table width=99%>';
	for (var i=0;i<=y;i++){
		ret += '<tr>';
		for (var j=0;j<=x;j++) ret += '<td></td>';
		ret += '</tr>';
	}
	ret += '</table>';
	mini_vlayer(idx,'mini_table_box');
	mini_set_html(idx,ret);
}

/**
 * selection 저장
 * @param Int idx 에디터인덱스
 * @return Object Range
 */
function saveSelection(idx)
{
	var miniEditorName = r_miniEditorTextarea[idx].getAttribute('name');
	var miniEditorId = 'miniEditorIframe_' + miniEditorName;
	var miniEditorIframe = document.getElementById(miniEditorId);
	var miniEditorContent = miniEditorIframe.contentWindow.document;

	if (miniEditorIframe.contentWindow.window.getSelection) { // IE9 Standards, IE >= 10, non-IE
		sel = miniEditorIframe.contentWindow.window.getSelection();
		if (sel.getRangeAt && sel.rangeCount) {
			var ranges = [];
			for (var i = 0, len = sel.rangeCount; i < len; ++i) {
				ranges.push(sel.getRangeAt(i));
			}
			return ranges;
		}
	} else if (miniEditorIframe.contentWindow.document.selection && miniEditorIframe.contentWindow.document.selection.createRange) { // IE9 Quirks, IE < 9
		return miniEditorIframe.contentWindow.document.selection.createRange();
	}
	return null;
}

/**
 * selection 재정의
 * @param Int idx 에디터인덱스
 * @param Object Range
 */
function restoreSelection(idx, savedSel)
{
	var miniEditorName = r_miniEditorTextarea[idx].getAttribute('name');
	var miniEditorId = 'miniEditorIframe_' + miniEditorName;
	var miniEditorIframe = document.getElementById(miniEditorId);
	var miniEditorContent = miniEditorIframe.contentWindow.document;

	if (savedSel) {
		if (miniEditorIframe.contentWindow.window.getSelection) { // IE9 Standards, IE >= 10, non-IE
			sel = miniEditorIframe.contentWindow.window.getSelection();
			sel.removeAllRanges();
			for (var i = 0, len = savedSel.length; i < len; ++i) {
				sel.addRange(savedSel[i]);
			}
		} else if (miniEditorIframe.contentWindow.document.selection && savedSel.select) { // IE9 Quirks, IE < 9
			savedSel.select();
		}
	}
}