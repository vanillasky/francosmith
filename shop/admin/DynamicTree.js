// +----------------------------------------------------------------+
// | DO NOT REMOVE THIS												|
// +---------------------------------------------------------------+
// | DynamicTree 1.5.2												|
// | Author: Cezary Tomczak [www.gosu.pl]							|
// | Free for any use as long as all copyright messages are intact	|
// +----------------------------------------------------------------+

function DynamicTree(id) {
	this.path = "../img/";
	this.img = {
		"branch": "tree-branch.gif",
		//(del_type) "doc": "tree-doc.gif",
		//(del_type) "folder": "tree-folder.gif",
		//(del_type) "folderOpen": "tree-folder-open.gif",
		"leaf": "tree-leaf.gif",
		"leafEnd": "tree-leaf-end.gif",
		"node": "tree-node.gif",
		"nodeEnd": "tree-node-end.gif",
		"nodeOpen": "tree-node-open.gif",
		"nodeOpenEnd": "tree-node-open-end.gif" };
	this.cookiePath = "";
	this.cookieDomain = "";
	this.init = function(type) {
		var p, img;
		for (p in this.img) {
			this.img[p] = this.path + this.img[p];
		}
		for (p in this.img) {
			this.imgObjects.push(new Image());
			this.imgObjects.getLast().src = this.img[p];
			this.img[p] = this.imgObjects.getLast().src;
		}

		if (this.Hidding == undefined) this.useHidding = false; // 분류감춤기능
		if (this.useHidding) this.Hidding();

		if (this.Shifting == undefined) this.useShifting = false; // 분류이동기능
		if (this.useShifting) this.Shifting();

		if (_ID('treeCodi')) this.local = 'codi'; // 디자인코디관리
		else if (_ID('treeCodiToday')) this.local = 'codiToday'; // 디자인코디관리(투데이샵)
		else if (type == 'local') this.local = 'local'; // 분류관리
		else if (document.getElementsByName('cate[]').length) this.local = 'category'; // 분류관리
		else if (type == 'shoptouch') this.local = 'shoptouch'; // 샵터치분류관리
		else if (_ID('treeCodiMobile')) this.local = 'codiMobile'; // 디자인코디관리(모바일샵V1)
		else if (_ID('treeCodiMobile2')) this.local = 'codiMobile2'; // 디자인코디관리(모바일샵V2)
		else this.local = 'brand'; // 브랜드관리

		if (this.local == 'brand'){ // DIV 데이타 처리 (브랜드관리 경우)
	        this.parse(document.getElementById(this.id).childNodes, this.tree, 1);
	        this.loadState();
	        this.updateHtml();
		}
		else { // JSON 데이타 처리 (그외 경우)
			this.readData();
		}

		if (window.addEventListener) { window.addEventListener("unload", function(e) { self.saveState(); }, false); }
		else if (window.attachEvent) { window.attachEvent("onunload", function(e) { self.saveState(); }); }
	};
	this.readData = function(id, el) {
		var query = "";
		switch(this.local) {
			case 'category' : { // (분류관리 경우)
				query += "../goods/indb.php?mode=getCategory";
				if (id) query += "&category=" + id;
				break;
			}
			case 'codi' : { // (디자인코디관리 경우)
				query += "../design/codi/_ajax.php?mode=getCodiTree";
				if (id) query += "&dirfiles=" + id;
				break;
			}
			case 'codiToday' : { // (디자인코디관리(투데이샵) 경우)
				query += "../todayshop/codi/_ajax.php?mode=getCodiTree";
				if (id) query += "&dirfiles=" + id;
				break;
			}
			case 'local' : { // (분류관리 경우)
				query += "../todayshop/indb.category.php?mode=getCategory";
				if (id) query += "&category=" + id;
				break;
			}
			case 'codiMobile' : { // (디자인코디관리(모바일샵V1) 경우)
				query += "../mobileShop/codi/_ajax.php?mode=getCodiTree";
				if (id) query += "&dirfiles=" + id;
				break;
			}
			case 'codiMobile2' : { // (디자인코디관리(모바일샵V2) 경우)
				query += "../mobileShop2/codi/_ajax.php?mode=getCodiTree";
				if (id) query += "&dirfiles=" + id;
				break;
			}
			case 'shoptouch' : {	// (샵터치 분류관리 경우)
				query += "../shoptouch/indb.php?mode=getCategory";
				if (id) query += "&category=" + id;
				break;
			}
		}

		var urlStr = query + "&dummy=" + new Date().getTime();
		var ajax = new Ajax.Request( urlStr,
		{
			method: "get",
			onComplete: function () {
				var req = ajax.transport;
				if ( req.status == 200 ) {
					if ((response = req.responseText) == '') return;
					self.jsonData = eval('(' + response + ')');
					if (id == null) {
						self.parseData(self.jsonData, self.tree, 1);
						self.loadState();
						self.updateHtml();
						if (self.category) loadHistory(self.category);
						else openTree(_ID('node_top'));
					}
					else if (id) {
						self.parseData(self.jsonData, self.allNodes[id], 1);
						el.innerHTML = self.toHtml(self.allNodes[id].childNodes);
					}

				} else {
					var msg = req.getResponseHeader("Status");
					if ( msg == null || msg.length == null || msg.length <= 0 )
						alert( "Error! Request status is " + req.status );
					else
						alert( msg );
				}
			}
		} );
	};
	this.parseData = function(nodes, tree) {
		for (var i = 0; i < nodes.length; i++) {
			if (!nodes[i].id) {
				nodes[i].id = this.id + "-" + (++this.count);
			}
			var node = new Node();
			node.id = nodes[i].id;
			node.text = nodes[i].catnm;
			node.etcPara = new Array();
			node.etcPara['hidden'] = nodes[i].hidden;
			node.etcPara['depth'] = nodes[i].depth;	//2012-01-18 dn 샵터치 분류 때문에 추가함

			node.parentNode = tree;
			node.childNodes = (nodes[i].folder == "folder" ? new Array() : null);
			node.isDoc = (nodes[i].folder == "doc");
			node.isFolder = (nodes[i].folder == "folder");
			tree.childNodes.push(node);
			this.allNodes[node.id] = node;

			if (nodes[i].childNodes) {
				this.parseData(nodes[i].childNodes, tree.childNodes.getLast());
			}
		}
	};
    this.parse = function(nodes, tree) {
        for (var i = 0; i < nodes.length; i++) {
            if (nodes[i].nodeType == 1) {
                if (!nodes[i].className) { continue; }
                if (!nodes[i].id) {
                    nodes[i].id = this.id + "-" + (++this.count);
                }
                var node = new Node();
                node.id = nodes[i].id;
                if (nodes[i].firstChild) {
                    if (nodes[i].firstChild.tagName == "A") {
                        var a = nodes[i].firstChild;
                        if (a.firstChild) {
                            node.text = a.firstChild.nodeValue.trim();
                        }
                        if (a.href) {
                            node.href = a.href;
                        }
                        if (a.title) {
                            node.title = a.title;
                        }
                        if (a.target) {
                            node.target = a.target;
                        }
                        node.text = a.outerHTML;
                    } else if (nodes[i].firstChild.nodeName == "#text") {
                        node.text = nodes[i].firstChild.nodeValue.trim();
                    } else {
                        node.text = nodes[i].firstChild.innerHTML.trim();
                        node.etcPara = new Array();
                        node.etcPara['hidden'] = nodes[i].firstChild.getAttribute('hidden');
                    }
                }
                node.parentNode = tree;
                node.childNodes = (nodes[i].className == "folder" ? new Array() : null);
                node.isDoc      = (nodes[i].className == "doc");
                node.isFolder   = (nodes[i].className == "folder");
                tree.childNodes.push(node);
                this.allNodes[node.id] = node;
            }
            if (nodes[i].nodeType == 1 && nodes[i].childNodes) {
                this.parse(nodes[i].childNodes, tree.childNodes.getLast());
            }
        }
    };
	this.nodeClick = function(id) {
		var el = document.getElementById(id+"-section");
		var node = document.getElementById(id+"-node");
		//(del_type) var icon = document.getElementById(id+"-icon");
		if (el.style.display == "block") {
			el.style.display = "none";
			if (this.allNodes[id].isLast()) { node.src = this.img.nodeEnd; }
			else { node.src = this.img.node; }
			//(del_type) icon.src = this.img.folder;
			this.opened.removeByValue(id);
		} else {
			el.style.display = "block";
			if (this.allNodes[id].isLast()) { node.src = this.img.nodeOpenEnd; }
			else { node.src = this.img.nodeOpen; }
			//(del_type) icon.src = this.img.folderOpen;
			this.opened.push(id);
		}
		if (el.style.display == "block" && el.innerHTML == '') {
			this.allNodes[id].childNodes = new Array();
			this.readData(id, el);
		}
	};
	this.toHtml = function(nodes) {
		var s = "";
		for (var i = 0; i < nodes.length; i++) {
			s += nodes[i].toHtml();
		}
		return s;
	};
	this.updateHtml = function() {
		document.getElementById(this.id).innerHTML = this.toHtml(this.tree.childNodes);
	};
	this.loadState = function() {
		var opened = this.cookie.get("opened");
		if (opened) {
			this.opened = opened.split("|");
			this.opened.filter(function(id) { return self.allNodes[id] && self.allNodes[id].isFolder && self.allNodes[id].childNodes.length; });
		}
	};
	this.saveState = function() {
		if (this.opened.length) {
			this.cookie.set("opened", this.opened.join("|"), 3600*24*30, this.cookiePath, this.cookieDomain);
		} else {
			this.clearState();
		}
	};
	this.clearState = function() {
		this.cookie.del("opened");
	};
	function Node(id, text, parentNode, childNodes, isDoc, isFolder) {
		this.id = id;
		this.text = text;
		this.parentNode = parentNode;
		this.childNodes = childNodes;
		this.isDoc = isDoc;
		this.isFolder = isFolder;
		this.href = "";
		this.title = "";
		this.target = "";
		this.isLast = function() {
			if (this.parentNode) {
				return this.parentNode.childNodes.getLast().id == this.id;
			}
			throw "DynamicTree.Node.isLast() failed, this func cannot be called for the root element";
		};
		this.toHtml = function() {
			var s = '<div class="?" id="?">'.format((this.isFolder ? "folder" : "doc"), this.id);
			if (self.useShifting) { // 분류이동기능
				var onevent = '';
				onevent += ' onmouseup="?.shifting.nodeUp(event)"'.format(self.id);
				onevent += ' onmouseout="?.shifting.nodeUp(event)"'.format(self.id);
				onevent += ' onmousedown="?.shifting.nodeDown(event)"'.format(self.id);
			}
			if (this.isFolder) {
				var nodeIcon = (self.opened.contains(this.id) ? (this.isLast() ? self.img.nodeOpenEnd : self.img.nodeOpen) : (this.isLast() ? self.img.nodeEnd : self.img.node));
				//(del_type) var icon = ((self.opened.contains(this.id)) ? self.img.folderOpen : self.img.folder);
				s += '<a onclick="?.nodeClick(\'?\')">'.format(self.id, this.id);
				s += '<img id="?-node" src="?" width="18" height="18" alt="" />'.format(this.id, nodeIcon);
				s += '</a>';
				if (self.local == 'codi' || self.local == 'codiMobile' || self.local == 'codiMobile2' || self.local == 'codiToday') { // (디자인코디관리 경우)

					if (this.id.match(this.text) == null && this.id.match(/^bundle/) == null){
						text = '?<span class="ta7" style="margin:0; padding:0 0 0 5px;"><font color="#6ba900"><b>/?</b></font></span>'.format(this.text, this.id.replace(/\/$/, ''));
					}
					else {
						text = this.text;
					}
					if (this.id == 'popup/') { // 메인팝업창 목록 호출코드
						s += '<a onclick="?.nodeClick(\'?\'); openTree(this)" onfocus=blur() ? title="?">'.format(self.id, this.id, onevent, this.id);
						//(del_type) s += '<img id="?-icon" src="?" width="18" height="18" alt="" />'.format(this.id, icon);
						if (self.local == 'codiToday') s += '?<input type=hidden value="?"></a>'.format(text, '../todayshop/iframe.popup_list.php');
						else s += '?<input type=hidden value="?"></a>'.format(text, '../design/iframe.popup_list.php');
					}
					else {
						s += '<a onclick="?.nodeClick(\'?\');" onfocus=blur() ? title="?">'.format(self.id, this.id, onevent, this.id);
						//(del_type) s += '<img id="?-icon" src="?" width="18" height="18" alt="" />'.format(this.id, icon);
						s += '?<input type=hidden value="?"></a>'.format(text, this.id);
					}
				}
				else if(self.local == 'shoptouch') {	// (샵터치의 경우)

					s += '<a onclick="?.nodeClick(\'?\'); openTree(this)" onfocus=blur() ?>'.format(self.id, this.id, onevent);
					//(del_type) s += '<img id="?-icon" src="?" width="18" height="18" alt="" />'.format(this.id, icon);
					s += '?<input type=hidden name=cate?[] value="?"></a>'.format(this.text, this.etcPara.depth, this.id);
				}
				else { // (분류관리 경우)
					s += '<a onclick="?.nodeClick(\'?\'); openTree(this)" onfocus=blur() ?>'.format(self.id, this.id, onevent);
					//(del_type) s += '<img id="?-icon" src="?" width="18" height="18" alt="" />'.format(this.id, icon);
					s += '?<input type=hidden name=cate?[] value="?"></a>'.format(this.text, (this.id.length / 3), this.id);
				}
				if (self.useHidding) s += self.hidding.eyeHtml(this); // 분류감춤기능
				s += '<div class="section?" id="?-section"'.format((this.isLast() ? " last" : ""), this.id);
				if (self.opened.contains(this.id)) {
					s += ' style="display: block;"'; }
				s += '>';
				if (this.childNodes.length) {
					for (var i = 0; i < this.childNodes.length; i++) {
						s += this.childNodes[i].toHtml();
					}
				}
				s += '</div>';
			}
			if (this.isDoc) {
				s += '<span><img src="?" width="18" height="18" alt="" /></span>'.format((this.isLast() ? self.img.leafEnd : self.img.leaf));
				if (self.local == 'codi' || self.local == 'codiToday') { // (디자인코디관리 경우)

					if (this.id.match(this.text) == null && this.id != 'default'){
						if (this.isLast()) text = '?<div class="filenm">?</div>'.format(this.text, this.id);
						else text = '?<div class="filenm" style="background:url(?) no-repeat;">?</div>'.format(this.text, self.img.branch, this.id);
					}
					else {
						text = this.text;
					}
					s += '<a onclick="openTree(this)" onfocus=blur() ? style="position:relative;" title="?">'.format(onevent, this.id);
					//s += '<img src="?" width="18" height="18" alt="" />'.format(self.img.doc);
					if (this.id.match(/^popup\//)){
						if (self.local == 'codiToday') s += '?<input type=hidden value="?"></a>'.format(text, '../todayshop/iframe.popup_register.php?file=' + this.id.replace(/^popup\//, ''));
						else s += '?<input type=hidden value="?"></a>'.format(text, '../design/iframe.popup_register.php?file=' + this.id.replace(/^popup\//, ''));
					}
					else if (this.id == 'style.css') s += '?<input type=hidden value="?"></a>'.format(text, '../design/iframe.css.php');
					else if (this.id == 'common.js') s += '?<input type=hidden value="?"></a>'.format(text, '../design/iframe.js.php');
					else {
						s += '?<input type=hidden value="?"></a>'.format(text, this.id);
					}
				}else if (self.local == 'codiMobile'){
					if (this.id.match(this.text) == null && this.id != 'default'){
						if (this.isLast()) text = '?<div class="filenm">?</div>'.format(this.text, this.id);
						else text = '?<div class="filenm" style="background:url(?) no-repeat;">?</div>'.format(this.text, self.img.branch, this.id);
					}
					else {
						text = this.text;
					}
					s += '<a onclick="openTree(this)" onfocus=blur() ? style="position:relative;" title="?">'.format(onevent, this.id);
					if (this.id == 'style.css') s += '?<input type=hidden value="?"></a>'.format(text, '../mobileShop/iframe.css.php');
					else if (this.id == 'common.js') s += '?<input type=hidden value="?"></a>'.format(text, '../mobileShop/iframe.js.php');
					else if (this.id == 'goods_list_action.js') s += '?<input type=hidden value="?"></a>'.format(text, '../mobileShop/iframe.goods_list_js.php');
					else {
						s += '?<input type=hidden value="?"></a>'.format(text, this.id);
					}
				}else if (self.local == 'codiMobile2'){
					if (this.id.match(this.text) == null && this.id != 'default'){
						if (this.isLast()) text = '?<div class="filenm">?</div>'.format(this.text, this.id);
						else text = '?<div class="filenm" style="background:url(?) no-repeat;">?</div>'.format(this.text, self.img.branch, this.id);
					}
					else {
						text = this.text;
					}
					s += '<a onclick="openTree(this)" onfocus=blur() ? style="position:relative;" title="?">'.format(onevent, this.id);
					if (this.id == 'style.css') s += '?<input type=hidden value="?"></a>'.format(text, '../mobileShop2/iframe.css.php');
					else if (this.id == 'common.js') s += '?<input type=hidden value="?"></a>'.format(text, '../mobileShop2/iframe.js.php');
					else if (this.id == 'goods_list_action.js') s += '?<input type=hidden value="?"></a>'.format(text, '../mobileShop2/iframe.goods_list_js.php');
					else {
						s += '?<input type=hidden value="?"></a>'.format(text, this.id);
					}
				}
				else if(self.local == 'shoptouch') {

					s += '<a onclick="openTree(this)" onfocus=blur() ? style="position:relative;">'.format(onevent);
					s += '?<input type=hidden name=cate?[] value="?"></a>'.format(this.text, this.etcPara.depth, this.id);

				}
				else { // (분류관리 경우)
					s += '<a onclick="openTree(this)" onfocus=blur() ? style="position:relative;">'.format(onevent);
					//s += '<img src="?" width="18" height="18" alt="" />'.format(self.img.doc);
					s += '?<input type=hidden name=cate?[] value="?"></a>'.format(this.text, (this.id.length / 3), this.id);
				}
				if (self.useHidding) s += self.hidding.eyeHtml(this); // 분류감춤기능
			}
			s += '</div>';
			return s;
		};
	}
	function Cookie() {
		this.get = function(name) {
			var cookies = document.cookie.split(";");
			for (var i = 0; i < cookies.length; ++i) {
				var a = cookies[i].split("=");
				if (a.length == 2) {
					a[0] = a[0].trim();
					a[1] = a[1].trim();
					if (a[0] == name) {
						return unescape(a[1]);
					}
				}
			}
			return "";
		};
		this.set = function(name, value, seconds, path, domain, secure) {
			var cookie = (name + "=" + escape(value));
			if (seconds) {
				var date = new Date(new Date().getTime()+seconds*1000);
				cookie += ("; expires="+date.toGMTString());
			}
			cookie += (path	? "; path="+path : "");
			cookie += (domain ? "; domain="+domain : "");
			cookie += (secure ? "; secure" : "");
			document.cookie = cookie;
		};
		this.del = function(name) {
			document.cookie = name + "=; expires=Thu, 01-Jan-70 00:00:01 GMT";
		};
	}
	var self = this;
	this.id = id;
	this.tree = new Node("tree", "", null, new Array(), false, true);
	this.allNodes = {}; // id => object
	this.opened = []; // opened folders
	this.active = ""; // active node, text clicked
	this.cookie = new Cookie();
	this.imgObjects = [];
	this.count = 0;
	this.local = '';
	this.category = '';
	this.useHidding = true;
	this.useShifting = true;
}

DynamicTree.prototype.loading = function(evt) {
	var oDiv = document.createElement('DIV');
	var cDiv = document.body.appendChild(oDiv);
	var oImg = document.createElement('IMG');
	var cImg = cDiv.appendChild(oImg);
	cImg.src = '../img/loading.gif';
	with (cDiv.style) {
		position = 'absolute';
		border = 'solid 1px #dddddd';
		filter = "Alpha(Opacity=90)";
		opacity = "0.9";
	}
	cDiv.style.left = evt.clientX + document.body.scrollLeft - 10;
	cDiv.style.top = evt.clientY + document.body.scrollTop - (cDiv.offsetHeight / 2);
	return cDiv;
}

DynamicTree.prototype.debug = function(arr) {
	var s = '<div style="margin:10px 30px; border:solid 1px #dddddd;">';
	for (k in arr) {
		if (typeof(arr[k]) == 'object' && k == 'parentNode' && arr[k] != null)
			s += '[?] = ?<br>'.format(k, this.allNodes[ arr['id'] ].parentNode.id);
		else if (typeof(arr[k]) == 'object' && arr[k] != null)
			s += '[?] ?<br>'.format(k, this.debug(arr[k]));
		else if (typeof(arr[k]) == 'object' && arr[k] == null)
			s += '[?] = null<br>'.format(k);
		else if (arr[k].toString().match(/function/) != null);
			//s += '[?] = ?<br>'.format(k, arr[k].toString().substring(0, 20));
		else
			s += '[?] = ?<br>'.format(k, arr[k]);
	}
	return s += '</div>';
}

/* Check whether array contains given string */
if (!Array.prototype.contains) {
	Array.prototype.contains = function(s) {
		for (var i = 0; i < this.length; ++i) {
			if (this[i] === s) { return true; }
		}
		return false;
	};
}

/* Remove elements with such value (mutates) */
if (!Array.prototype.removeByValue) {
	Array.prototype.removeByValue = function(value) {
		var i, indexes = [];
		for (i = 0; i < this.length; ++i) {
			if (this[i] === value) { indexes.push(i); }
		}
		for (i = indexes.length - 1; i >= 0; --i) {
			this.splice(indexes[i], 1);
		}
	};
}

/* Remove elements judged 'false' by the passed function (mutates) */
if (!Array.prototype.filter) {
	Array.prototype.filter = function(func) {
		var i, indexes = [];
		for (i = 0; i < this.length; ++i) {
			if (!func(this[i])) { indexes.push(i); }
		}
		for (i = indexes.length - 1; i >= 0; --i) {
			this.splice(indexes[i], 1);
		}
	};
}

/* Get the last element from the array */
if (!Array.prototype.getLast) {
	Array.prototype.getLast = function() {
		return this[this.length-1];
	};
}

/* Strip whitespace from the beginning and end of a string */
if (!String.prototype.trim) {
	String.prototype.trim = function() {
		return this.replace(/^\s*|\s*$/g, "");
	};
}

/* Replace ? tokens with variables passed as arguments in a string */
String.prototype.format = function() {
	if (!arguments.length) { throw "String.format() failed, no arguments passed, this = "+this; }
	var tokens = this.split("?");
	if (arguments.length != (tokens.length - 1)) { throw "String.format() failed, tokens != arguments, this = "+this; }
	var s = tokens[0];
	for (var i = 0; i < arguments.length; ++i) {
		s += (arguments[i] + tokens[i + 1]);
	}
	return s;
};

/* Check whether array contains given string */
if (!Array.prototype.arraySearch) {
	Array.prototype.arraySearch = function(s) {
		for (var i = 0; i < this.length; ++i) {
			if (this[i] === s) { return i; }
		}
		return false;
	};
}
