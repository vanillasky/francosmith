var nsAdminForm = function()
{
	return {
		originalDatas : [],
		forms : [],
		init : function()
		{
			var self = this;
			var form;
			try {

				for (var i = 0, m = arguments.length; i < m; i++)
				{
					form = arguments[i];

					self.toggle.auto.exec(form, form);
					self.fieldHighlighter(form);
				}

			}
			catch (e) {
			}
		},
		htmlspecialchars : function(str)
		{
			return str.replace(/&/g, "&amp;").replace(/\"/g, '&quot;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
		},
		fieldHighlighter : function(form)
		{
			var _type;
			var _bind;

			form.select('input, textarea').each(function(el)
			{

				_bind = false;

				if (el.tagName == 'INPUT') {
					_type = el.type;
					if (_type == 'text' || _type == 'password' || _type == 'file') {
						_bind = true;
					}
				}
				else {
					_bind = true;
				}

				if (_bind) {
					el.observe('focus', function(e)
					{
						if (!this.hasClassName('highlight')) {
							this.addClassName('highlight');
						}
					}).observe('blur', function(e)
					{
						if (this.hasClassName('highlight')) {
							this.removeClassName('highlight');
						}
					});
				}
			});

		},
		anchorHelper : {
			element : null,
			isQuirksModeOnIE : false,
			elementWidth : 0,
			init : function(option)
			{
				var defaultOption = {
					id : 'enamoo-anchor-helper',
					link : 'auto',
					save : function()
					{
						document.getElementsByTagName('form')[0].submit();
					},
					cancel : function()
					{
						window.history.back();
					}
				};

				var options = Object.extend(defaultOption, option || {});

				var self = this;

				// set anchor helper element.
				self.element = $(options.id);

				// IE Quirks Mode
				if (Prototype.Browser.IE) {
					self.isQuirksModeOnIE = !(document.compatMode == "CSS1Compat");
				}

				// event bind on scroll.
				Event.observe(window, 'scroll', function(event)
				{
					var Y = document.viewport.getScrollOffsets().top;
					self.moveVertical(Y);

					if (Y > 110) {// top menu's height.
						self.show();
					}
					else {
						self.hide();
					}
				});

				// event bind on resize when IE quirks mode
				if (self.isQuirksModeOnIE) {
					Event.observe(window, 'resize', function(event)
					{
						self.elementWidth = document.body.clientWidth;
					});

					// adjust anchor helper element's width
					self.elementWidth = document.body.clientWidth;
				}

				// draw.
				self._draw(options.link, options.save, options.cancel);

				// fire scroll event;
				if (document.createEvent) {
					var e = document.createEvent('HTMLEvents');
					e.initEvent('scroll', true, true);

					window.dispatchEvent(e);
				}
				else if (window.fireEvent) {
					window.fireEvent('onscroll');
				}
			},
			_draw : function(link, save, cancel)
			{
				var self = this;

				self.element.update('');

				// jumper
				var jumper = new Element('ul', {
					'class' : 'anchor-links'
				});

				if (link == 'auto') {

					var _el, _id, _name;
					var _time = Math.round(new Date().getTime() / 1000);

					$$('h2.title').each(function(el)
					{

						_el = el.firstChild;

						while (_el.nodeType != 3) {
							_el = _el.nextSibling;
						}

						_name = _el.nodeValue;
						_id = 'anchor-' + _time++;

						el.insert({
							before : '<a id="' + _id + '"></a>'
						});

						jumper.insert({
							bottom : '<li><a href="javascript:void(0);" onclick="nsAdminForm.anchorHelper.scroll(\'' + _id + '\')">' + _name + '</a></li>'
						});
					});

				}
				else {

				}

				// buttons
				var buttons = new Element('ul', {
					'class' : 'buttons'
				});

				buttons.insert({
					bottom : '<li><button type="button" class="default-btn" onclick="' + save + '">저장</button></li>'
				});
				buttons.insert({
					bottom : '<li><button type="button" class="default-btn" onclick="' + cancel + '">취소</button></li>'
				});
				buttons.insert({
					bottom : '<li><button type="button" class="default-btn" onclick="window.scrollTo();">탑</button></li>'
				});

				//self.element.insert({bottom:jumper});
				self.element.insert({
					bottom : buttons
				});

			},
			scroll : function(id)
			{
				var y = $(id).cumulativeOffset().top - 50;
				window.scrollTo(0, y);
			},
			moveVertical : function(pos)
			{
				if (this.isQuirksModeOnIE) {
					this.element.setStyle({
						position : 'absolute',
						width : this.elementWidth + 'px',
						top : pos + 'px'
					});
				}
				else {
					// position is fixed.
				}
			},
			scriptaclousEffectOption : {
				duration : 0.3,
				beforeStart : function()
				{
					nsAdminForm.anchorHelper.scriptaclousEffectRunning = true;
				},
				afterFinish : function()
				{
					nsAdminForm.anchorHelper.scriptaclousEffectRunning = false;
				}
			},
			scriptaclousEffectRunning : false,
			show : function()
			{
				var self = this;

				if (self.element.getStyle('display') == 'block') {
					return;
				}

				// scriptaclous
				if ( typeof Effect != 'undefined') {
					if (self.scriptaclousEffectRunning) {
						return;
					}
					self.element.appear(self.scriptaclousEffectOption);
				}
				else {
					self.element.setStyle({
						display : 'block'
					});
				}
			},
			hide : function()
			{
				var self = this;

				if (self.element.getStyle('display') != 'block') {
					return;
				}

				// scriptaclous
				if ( typeof Effect != 'undefined') {
					var self = this;

					if (self.scriptaclousEffectRunning) {
						return;
					}
					self.element.fade(self.scriptaclousEffectOption);
				}
				else {
					self.element.setStyle({
						display : 'none'
					});
				}
			}
		},
		inputSizeIndicator : {
			init : function()
			{
				var self = this;

				var pattern = /inputSize:({.+?})/;
				var clas, tmp;

				$$('span').each(function(el)
				{
					clas = el.readAttribute('class');

					if (clas) {
						tmp = clas.match(pattern);

						if (tmp) {

							var opt = eval('(' + tmp[1] + ')');
							var fld = $(opt.target);
							var max = opt.max;

							self.check(fld, max, el);

							fld.observe('keyup', function(e)
							{
								self.check(this, max, el);
							});

						}
					}
				});
			},
			check : function(field, max, span)
			{
				if (field.value.length > max) {
					alert('최대 ' + max + '자 까지 입력 가능합니다.');
					field.value = field.value.substring(0, max);
					return false;
				}
				var str = field.value.length + ' / ' + max + ' 자';
				span.update(str);
			}
		},
		getRadioValue : function(els)
		{
			for (var i = 0, m = els.length; i < m; i++) {
				if (els[i].checked) {
					return els[i].value;
				}
			}
			return false;
		},
		addSelectOption : function(id, value, label)
		{
			try {
				// 등록여부 체크
				$(id).childElements().each(function(el)
				{
					if (el.value == value) {
						el.selected = true;
						throw {};
					}
				});

				$(id).insert(new Element('option', {
					value : value,
					selected : true
				}).update(label));

			}
			catch (e) {
				return false;
			}

			return true;

		},
		dialog : {
			scrollStatus : true,
			toggleScrollBar : function(fl)// true : view, false : hide
			{
				if (fl) {
					this.scrollStatus = !fl;
				}
				if (this.scrollStatus) {
					document.getElementsByTagName("html")[0].style.overflow = "hidden";
				}
				else {
					document.getElementsByTagName("html")[0].style.overflow = 'auto';
				}
				this.scrollStatus = !this.scrollStatus;

			},
			drawBackground : function()
			{
				bg = new Element('div', {
					id : 'godo_layer_background'
				});

				bg.setStyle({
					position : 'absolute',
					width : '100%',
					backgroundColor : '#000000',
					zIndex : 9999999,
					filter : 'Alpha(Opacity=80)',
					opacity : 0.5,
					display : 'none'
				});

				document.body.appendChild(bg);

				return bg;

			},
			drawLayer : function()
			{
				layer = new Element('div', {
					id : 'godo_layer'
				});

				layer.setStyle({
					position : 'absolute',
					backgroundColor : '#fff',
					border : '2px solid #3babee',
					zIndex : 99999999,
					display : 'none',
					padding : '0'
				});

				document.body.appendChild(layer);

				return layer;
			},
			open : function(option)
			{
				var defaultOption = {
					title : '',
					type : 'url',
					contents : 'about:blank',
					width : 500,
					height : 500
				};

				var options = Object.extend(defaultOption, option || {});

				this.toggleScrollBar(false);

				// bg
				var bg = $('godo_layer_background');

				if (!bg) {
					bg = this.drawBackground();
				}
				// layer
				var layer = $('godo_layer');

				if (!layer) {
					layer = this.drawLayer();
				}
				// positioning and display.

				bg.setStyle({
					top : document.viewport.getScrollOffsets().top + 'px',
					height : document.viewport.getHeight() + 'px',
					left : 0,
					display : 'block'
				});

				var _w, _h;
				_h = (document.viewport.getHeight() - options.height) / 2 + document.viewport.getScrollOffsets().top;
				_w = (document.viewport.getWidth() - options.width) / 2 + document.viewport.getScrollOffsets().left;

				layer.setStyle({
					height : options.height + 'px',
					width : options.width + 'px',
					top : _h + 'px',
					left : _w + 'px',
					display : 'block'
				});

				var ht = '';
				ht += '<div class="dialog">';
				ht += '	<div class="head">';
				ht += '		<span>' + (options.title ? options.title : '') + '</span>';
				ht += '		<button onClick="nsAdminForm.dialog.close();return false;">x</button>';
				ht += '	</div>';
				ht += '	<div class="contents"><div class="wrapper">';

				if (options.type == 'url') {
					ht += '<iframe src="' + options.contents + '" id="godo_layer_iframe"></iframe>';
				}
				else {
					ht += options.contents;
				}
				ht += '</div></div></div>';

				layer.update(ht);

			},
			close : function()
			{
				this.toggleScrollBar(true);

				try {
					$('godo_layer_background').setStyle({
						display : 'none'
					});
					$('godo_layer').setStyle({
						display : 'none'
					});
				}
				catch (e) {
				}
			}
		},
		toggle : {
			auto : {
				pattern : /IF_([a-zA-Z0-9_\[\]]+)_IS_([0-9a-zA-Z]+)/,
				classes : [],
				hasChildren : function(el)
				{
					return el.childElements().size() > 0 ? true : false;
				},
				getClasses : function(form, elements)
				{
					var self = this;
					var clas, tmp;

					elements.childElements().each(function(el)
					{
						clas = el.readAttribute('class');

						if (clas) {

							tmp = [];

							clas.scan(self.pattern, function(m)
							{
								tmp.push(m);
							});

							if (tmp.length > 0) {
								self.classes.push(tmp);
							}

						}

						if (self.hasChildren(el)) {
							self.getClasses(form, el);
						}

					});
				},
				exec : function(form, elements)
				{
					var self = this;
					var tmp, v, displ, els, els_;
					var field, value;

					self.getClasses(form, elements);

					// 정렬;
					self.classes.sort(function(a, b)
					{
						return a.length - b.length;
					});

					for (var i = 0, m = self.classes.length; i < m; i++) {

						tmp = self.classes[i];

						displ = '';
						els = null;

						for (var j = 0, n = tmp.length; j < n; j++) {

							field = tmp[j][1];
							value = tmp[j][2];

							if (!form[field])
								continue;

							if (form[field].length > 1) {
								// radio
								v = nsAdminForm.getRadioValue(form[field]);
							}
							else {
								v = $F(form[field]);
							}

							if (v != value) {
								displ = 'none';
							}

							// select target elements;

							// some_array[0] support;
							if (/\[[0-9]+\]/.test(tmp[j][0])) {
								els_ = nsAdminForm.toggle._cssSelector(tmp[j][0]);
							}
							else {
								els_ = $$('.' + tmp[j][0]);
							}

							if (els == null) {
								els = els_;
							}
							else {
								els = els.intersect(els_);
							}
						}

						try {
							// toggle;
							els.each(function(el)
							{
								el.setStyle({
									display : displ
								});
							});
						}
						catch (e) {
						}

					}

				}
			},
			_is : function(target, val)
			{
				var form = target.up('form');

				switch (target.tagName.toUpperCase()) {
					case 'INPUT':
						if (target.type.toUpperCase() == 'RADIO') {
							var _val = nsAdminForm.getRadioValue(form[target.name]);
						}
						else {
							var _val = $F(form[target.name]);
						}
						break;
					default:
						return false;
						break;

				}

				var classname = 'IF_' + target.name + '_IS_' + val;

				// some_array[0] support;
				if (/\[[0-9]+\]/.test(classname)) {
					var els = this._cssSelector(classname);
				}
				else {
					var els = $$('.' + classname);
				}

				els.each(function(el)
				{
					el.setStyle({
						display : (_val == val) ? '' : 'none'
					});
				});

			},
			is : function(event)// click event
			{
				var target = Event.element(event);

				for (var i = 1, m = arguments.length; i < m; i++) {
					this._is(target, arguments[i]);
				}
			},
			_cssSelector : function(classname)
			{
				var els = [];
				var tmp = document.getElementsByTagName("*"), classes;
				var i, j, m, n;
				for ( i = 0, m = tmp.length; i < m; i++) {
					classes = tmp[i].className.split(/\s+/);
					for ( j = 0, n = classes.length; j < n; j++) {
						if (classes[j] == classname) {
							els.push(tmp[i]);
						}
					}
				}

				return els;
			}
		},
		disable : {
			_is : function(target, val)
			{
				var form = target.up('form');

				switch (target.tagName.toUpperCase()) {
					case 'INPUT':
						if (target.type.toUpperCase() == 'RADIO') {
							var _val = nsAdminForm.getRadioValue(form[target.name]);
						}
						else {
							var _val = $F(form[target.name]);
						}
						break;
					default:
						return false;
						break;

				}
				$$('.IF_' + target.name + '_IS_' + val).each(function(el)
				{
					el.setStyle({
						display : (_val == val) ? '' : 'none'
					});
				});

			},
			is : function(event)// click event
			{
				var target = Event.element(event);

				for (var i = 1, m = arguments.length; i < m; i++) {
					this._is(target, arguments[i]);
				}
			}
		},
		ajax : function(url, param, callback, create, complete)
		{

			if ( typeof callback == "undefined")
				callback = function()
				{
				};
			if ( typeof create == "undefined")
				create = function()
				{
					nsAdminForm.loading.open();
				};
			if ( typeof complete == "undefined")
				complete = function()
				{
					nsAdminForm.loading.close();
				};

			return new Ajax.Request(url, {
				method : "post",
				parameters : param,
				onSuccess : callback,
				onCreate : create,
				onComplete : complete
			});
		},
		loading : {
			initialized : false,
			opened : false,
			init : function()
			{
				if ( typeof nsGodoLoadingIndicator == 'object') {
					nsGodoLoadingIndicator.init({
						elWidth : 280,
						elHeight : 80,
						elMsg : '<img src="../img/progress_bar.gif">'
					});
					this.initialized = true;
				}
				else {
					this.initialized = false;
				}
			},
			close : function()
			{
				if (!this.initialized)
					this.init();
				if (this.opened == true) {
					if ( typeof nsGodoLoadingIndicator == 'object') {
						nsGodoLoadingIndicator.hide();
					}
				}

				this.opened = false;
			},
			open : function()
			{
				if (!this.initialized)
					this.init();

				if (this.opened == false) {
					if ( typeof nsGodoLoadingIndicator == 'object') {
						nsGodoLoadingIndicator.show();
					}
				}

				this.opened = true;
			}
		},
		openWindow : function(url, width, height)
		{

			var x = (screen.width - width) / 2;
			var y = (screen.height - height) / 2;

			return window.open(url, "", "width=" + width + ",height=" + height + ",top=" + y + ",left=" + x + ",scrollbars=yes,resizable=yes");
		}
	};
}();
