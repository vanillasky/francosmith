/**
	2011-09-30 by x-ta-c

	페이지내 이미지 객체에 대한 contextmenu 방지
	페이지내 이미지에 대한 drag 방지
	페이지내 선택영역에 대한 drag 방지 및 복사 방지 (단 input, textarea 필드는 제외)
 */
function _preventContentsCopy(option) {

	function _getTagName(el) {
		return el.tagName;
	}

	function _getSelection() {
		return document.selection || document.getSelection();
	}

	function _image(e) {
		var el = e.srcElement || e.target;
		var cancel = false;

		if (_getTagName(el) == "IMG") cancel = true;

		if (cancel) {
			if (e.cancelable) {
				e.preventDefault();
				e.stopPropagation();
			}

			return false;
		}
	}


	function _copy(e) {

		var _code = e.keyCode;
		var _ctrl = e.ctrlKey || false;
		var cancel = false;

		if (_code == 67 && _ctrl) {

			var sel = _getSelection();
			if (navigator.appName=="Microsoft Internet Explorer") {
				sel.empty();
			} else {
				sel.removeAllRanges();
			}
			cancel = true;
		}

		if (cancel) {
			if (e.cancelable) {
				e.preventDefault();
				e.stopPropagation();
			}
			return false;
		}
	}

	function _selection(e) {
		var el = e.srcElement || e.target;
		if (_getTagName(el) != 'INPUT' && _getTagName(el) != 'TEXTAREA') {
			var sel = document.selection || document.getSelection();
			if (navigator.appName=="Microsoft Internet Explorer") {
				sel.empty();
			} else {
				sel.removeAllRanges();
			}
		}
	}

	function _foo(e) {
		var _code = e.keyCode;
		var _ctrl = e.ctrlKey || false;
		var cancel = false;

		if (_code == 44) cancel = true;

		if (cancel) {
			if (e.cancelable) {
				e.preventDefault();
				e.stopPropagation();
			}
			return false;
		}
	}


	var config = {};

	if (document.all) {
		document.attachEvent( "oncontextmenu", _image );
		document.attachEvent( "ondragstart",	_image );
		document.attachEvent( "onkeydown",	_copy );
		document.attachEvent( "onmousedown",	_selection );

		document.attachEvent( "onkeyup",	_foo );
		document.attachEvent( "onkeypress",	_foo );
	}
	else {
		document.addEventListener("contextmenu", _image );
		document.addEventListener("keydown", _copy );
		document.addEventListener("dragstart", _image );
		document.addEventListener("mousedown", _selection );

		document.addEventListener( "keyup",	_foo );
		document.addEventListener( "keypress",	_foo );
	}

	var el = document.createElement('META');
	el.httpEquiv = 'imagetoolbar';
	el.content = 'no';
	document.getElementsByTagName('head')[0].appendChild(el);
}

_preventContentsCopy();