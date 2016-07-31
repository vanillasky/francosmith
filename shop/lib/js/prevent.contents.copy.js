/**
 2011-09-30 by x-ta-c
 
 페이지내 이미지 객체에 대한 contextmenu 방지
 페이지내 이미지에 대한 drag 방지
 페이지내 선택영역에 대한 drag 방지 및 복사 방지 (단 input, textarea 필드는 제외)
 */
function _preventContentsCopy(e) {
	function b(j) {
		return j.tagName
	}
	function d() {
		return document.selection || document.getSelection()
	}
	function f(l) {
		var j = l.srcElement || l.target;
		var k = false;
		if (b(j) == "IMG") {
			k = true
		}
		if (k) {
			if (l.cancelable) {
				l.preventDefault();
				l.stopPropagation()
			}
			return false
		}
	}
	function h(n) {
		var j = n.keyCode;
		var k = n.ctrlKey || false;
		var l = false;
		if (j == 67 && k) {
			var m = d();
			if (navigator.appName=="Microsoft Internet Explorer") {
				m.empty();
			} else {
				m.removeAllRanges();
			}
			l = true
		}
		if (l) {
			if (n.cancelable) {
				n.preventDefault();
				n.stopPropagation()
			}
			return false
		}
	}
	function g(l) {
		var j = l.srcElement || l.target;
		if (b(j) != "INPUT" && b(j) != "TEXTAREA") {
			var k = document.selection || document.getSelection();
			if (navigator.appName=="Microsoft Internet Explorer") {
				k.empty();
			} else {
				k.removeAllRanges();
			}
		}
	}
	function i(m) {
		var j = m.keyCode;
		var k = m.ctrlKey || false;
		var l = false;
		if (j == 44) {
			l = true
		}
		if (l) {
			if (m.cancelable) {
				m.preventDefault();
				m.stopPropagation()
			}
			return false
		}
	}
	var c = {};
	if (document.all) {
		document.attachEvent("oncontextmenu", f);
		document.attachEvent("ondragstart", f);
		document.attachEvent("onkeydown", h);
		document.attachEvent("onmousedown", g);
		document.attachEvent("onkeyup", i);
		document.attachEvent("onkeypress", i)
	} else {
		document.addEventListener("contextmenu", f);
		document.addEventListener("keydown", h);
		document.addEventListener("dragstart", f);
		document.addEventListener("mousedown", g);
		document.addEventListener("keyup", i);
		document.addEventListener("keypress", i)
	}
	var a = document.createElement("META");
	a.httpEquiv = "imagetoolbar";
	a.content = "no";
	document.getElementsByTagName("head")[0].appendChild(a)
}
_preventContentsCopy();