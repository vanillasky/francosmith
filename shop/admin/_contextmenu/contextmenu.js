
var nsGodoContextMenu = function() {

	var bg, div;

	function create(id, tag, opt) {

		if (Object.isElement($(id))) {
			var el = $(id);
		}
		else {
			var el = new Element(tag, opt || {});
			document.body.appendChild(el);
		}

		return el;
	}

	function setCookie( name, value, expires, path, domain, secure ){

		var curCookie = name + "=" + escape( value ) +
			( ( expires ) ? "; expires=" + expires.toGMTString() : "" ) +
			( ( path ) ? "; path=" + path : "" ) +
			( ( domain ) ? "; domain=" + domain : "" ) +
			( ( secure ) ? "; secure" : "" );

		document.cookie = curCookie;
	}

	function dialog_close() {
		var id = '_godo_contextmenu_dialog';

		var $bg = $(id + '_bg');
		var $div = $(id);

		$div.style.display = 'none';
		$bg.style.display = 'none';
	}

	function dialog_open(url, width, height, data) {

		var id = '_godo_contextmenu_dialog';
		var px = 'px';

		var doc_size = {
			width : document.body.scrollWidth || document.documentElement.scrollWidth,
			height: document.body.scrollHeight || document.documentElement.scrollHeight
		}

		var win_size = {
			width : window.innerWidth	|| (window.document.documentElement.clientWidth	|| window.document.body.clientWidth),
			height: window.innerHeight	|| (window.document.documentElement.clientHeight|| window.document.body.clientHeight)
		}


		// ���
		var $bg = $(id + '_bg');
		if ($bg == null) {

			$bg = new Element('div',{id : id + '_bg'});

			document.body.appendChild($bg);
		}

		with ($bg){
			style.position = "absolute";
			style.left = 0;
			style.top = 0;
			style.zIndex = 1000 - 10;
			style.width = doc_size.width;
			style.height = doc_size.height;
			style.backgroundColor = "#000000";
			style.filter = "alpha(opacity=60)";
			style.opacity = "0.6";
			style.display = "none";
		}

		var scTop = document.body.scrollTop;

		// ���̾�α�
		var $div = $(id);
		if ($div == null) {

			$div = new Element('div',{id : id} );

			document.body.appendChild($div);

		}

		with ($div){
			id = id;
			style.position = "absolute";
			style.left = (win_size.width + width) / 2 - width + px;
			style.top = ((win_size.height + height) / 2 - height) + scTop + px;
			style.zIndex = 1000;
			style.width = width + px;
			style.height = height + px;
			style.backgroundColor = "#ffffff";
			style.display = "none";
			style.padding = "10px";
			style.overflowY = "scroll";

		}

		var ajax = new Ajax.Request(url, {
			method: "post",
			parameters: Object.toQueryString(data) || '',
			asynchronous: true,
			onComplete: function(response) {
				if (response.status == 200) {

					$bg.setStyle({display:'block'});
					$div.update( response.responseText ).setStyle({display:'block'});

				}

			}
		});

		nsGodoContextMenu._hide();
	}

	return {
		_status : true,
		_isView : false,
		_context : null,
		_contextid : 'godo_context_menu',
		_options : {},
		_menus : 0,
		toggle : function() {
			if ($('el-use-context-menu').checked == true) {
				this._status = true;
				setCookie( '_TOGGLE_CONTEXT_MENU_', 1, 0, '/');
			}
			else {
				this._status = false;
				setCookie( '_TOGGLE_CONTEXT_MENU_', 0, 0, '/');
			}
		},
		init : function(options) {

			var self = this;

			self._context = new Element('div',{'id': self._contextid});

			self._options = Object.extend({
				option  : {
							contextWidth : 200,
							zIndex		 : 999999
				},
				menu	: [
							{
								//type : 'seprator',	// seprator �� ��� �Ʒ� name, url, target ��µ��� ����, hr �� ��ü��.
								name : '(��)������Ʈ',
								url : 'http://www.godo.co.kr',
								target: '_blank'
							}
							,
							{
								type : 'seprator'
							}
							,
							{
								name : '�޴��� �߰��� �ּ���.'
							}
				]
			}, options || { });

			self._draw(self._options);
			self._event.bind();
			document.body.appendChild( self._context );

			if ($('el-use-context-menu').checked == false) {
				this._status = false;
			}
		}
		,
		_draw : function (options) {

			var self = this;

			var ul = new Element('ul');

			// ����� ���� �޴�
			options.menu.each(function(item){

				if (item.type == 'seprator') {
					ul.insert({
						bottom: new Element('li',{'class': 'seprator'}).update('<hr/>')
					});
				}
				else {
					ul.insert({
						bottom: new Element('li').update('<a href="'+(item.url || '#')+'" target="'+(item.target || '_self')+'">'+(item.name || 'untitled')+'</a>')
					});
				}
			});

			// �⺻ ���� ��ƿ �޴�
			ul.insert({
				bottom: new Element('li',{'class': 'seprator'}).update('<hr>')
			});

			ul.insert({
				bottom: new Element('li').update('<a href="javascript:nsGodoContextMenu.setup.add()">���� �������� �߰�</a>')
			});

			ul.insert({
				bottom: new Element('li').update('<a href="javascript:nsGodoContextMenu.setup.config()">�޴� ����</a>')
			});

			// ��;
			self._context.update(ul);

			self._context.setStyle({
				width : self._options.option.contextWidth + 'px',
				zIndex : self._options.option.zIndex
			});

		}
		,
		_event : {

			bind : function() {
				//

				// ���콺
				Event.observe(document, 'contextmenu',	nsGodoContextMenu._event.onContextMenu);
				Event.observe(document, 'click',		nsGodoContextMenu._event.onClick);

				// Ű���� �̺�Ʈ
				if (Prototype.Browser.Gecko || Prototype.Browser.Opera ) {
					Event.observe(document, 'keypress',	nsGodoContextMenu._event.onKeyPress);
				} else {
					Event.observe(document, 'keydown',	nsGodoContextMenu._event.onKeyPress);
				}

			}
			,
			// .........
			onKeyPress : function(e) {

				if (nsGodoContextMenu._isView == false) return;

				if (! nsGodoContextMenu._isBindedKey(e.keyCode)) nsGodoContextMenu._hide();

				e.preventDefault();
			}
			,
			onClick : function(e) {

				if (nsGodoContextMenu._isView == false) return;

				var el = Event.element(e);

				if (el.descendantOf(nsGodoContextMenu._context) == false) {
					nsGodoContextMenu._hide(e);
				}

			}
			,
			onContextMenu : function(e) {

				if (nsGodoContextMenu._status == false)
				{
					return;
				}

				var el = Event.element(e);

				var preventTags = 'INPUT|TEXTAREA';

				/*
				if (e.ctrlKey == false) {
					self._hide(e);
					return;
				}
				*/

				// �� �ʵ� (input, textarea)
				if (preventTags.indexOf(el.tagName) > -1) {
					nsGodoContextMenu._hide();
					return;
				}

				nsGodoContextMenu._show(e);

				// ��Ű �̺�Ʈ

				// �ݱ� (���ѵ� Ŭ�� or esc Ű)


				e.preventDefault();

			}
		}
		,
		_show : function(e) {

			var self = this;

			var win = self._getWindowSize();
			var clk = e.pointer();

			// ��ġ..
			var pos = {
						left: ((clk.x + self._options.option.contextWidth) > win.width) ? clk.x - self._options.option.contextWidth : clk.x ,
						top:  ((clk.y + self._context.getHeight()) > win.height) ? clk.y - self._context.getHeight() : clk.y
						};

			self._context.setStyle({
				top : pos.top + 'px',
				left : pos.left + 'px',
				display: 'block'
			});

			self._isView = true;
		}
		,
		_hide : function(e) {

			var self = this;

			self._context.setStyle({
				display:'none'
			});

			self._isView = false;

		}
		,
		_getWindowSize : function() {
			return {
				width : window.innerWidth	|| (window.document.documentElement.clientWidth	|| window.document.body.clientWidth),
				height: window.innerHeight	|| (window.document.documentElement.clientHeight|| window.document.body.clientHeight)
			}
		}
		,
		_getDocumentSize : function() {
			return {
				width : document.documentElement.scrollWidth || document.body.scrollWidth,
				height: document.documentElement.scrollHeight || document.body.scrollHeight
			}
		}
		,
		setup : {

			add : function(url, name) {

				var self = this;

				nsGodoContextMenu._hide();

				if (!url) url = window.location.href;

				var data = {
					url : url
				};

				dialog_open('../_contextmenu/_add.form.php',450,230, data);
			}
			,
			mod : function(sno) {

				var self = this;

				nsGodoContextMenu._hide();

				dialog_open('../_contextmenu/_add.form.php?sno='+sno,450,230);
			}
			,
			config : function() {
				dialog_open('../_contextmenu/_config.form.php',600,500);
			}
			,
			close : function() {
				dialog_close();
			}
			,
			save : function(form) {

				if (form.name.value == '') {
					alert('�����Ͻ� �̸��� �Է��ϼ���.');
					return false;
				}

				var ajax = new Ajax.Request('../_contextmenu/indb.php', {
					method: "post",
					parameters: Form.serialize(form),
					asynchronous: true,
					onComplete: function(response) {

						if (response.status == 200 && response.responseText != '' ) {

							var menu = response.responseText.evalJSON(true);

							nsGodoContextMenu.init({
								option  : nsGodoContextMenu._options.option,
								menu	: menu
							});

							nsGodoContextMenu.setup.close();

						}
						else {
							alert('������ �߻��߽��ϴ�. �ٽ� �õ��� �ּ���.');
							nsGodoContextMenu.setup.close();
						}
					}
				});
			}
			,
			del : function(sno) { if (confirm('�����Ͻðڽ��ϱ�?')) {

				var ajax = new Ajax.Request('../_contextmenu/indb.php', {
					method: "post",
					parameters: 'mode=del&sno='+sno,
					asynchronous: true,
					onComplete: function(response) {

						if (response.status == 200 && response.responseText != '' ) {

							var menu = response.responseText.evalJSON(true);

							nsGodoContextMenu.init({
								option  : nsGodoContextMenu._options.option,
								menu	: menu
							});

							$('el-contextmenu-config-row-'+sno).remove();

						}
						else {
							alert('������ �߻��߽��ϴ�. �ٽ� �õ��� �ּ���.');
							nsGodoContextMenu.setup.close();
						}
					}
				});

			}}
		}
		,
		_isBindedKey : function(k) {
			/*
			����Ű ������ ������ �ش� �׼�;

			*/


			return false;
		}
	}
}();