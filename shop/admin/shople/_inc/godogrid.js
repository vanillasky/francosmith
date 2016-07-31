var nsGodogrid = function() {

	return {
		_data : {},			// data obj.
		_table : null,
		_rows : 0,
		_cols : 0,
		_focused : null,
		_focused_old : null,
		_cursor : {x:0,y:0},
		_id_prefix : '_gd_grid_cell_',
		_options : {},
		_id : null,
		init : function(id, options) {

			var self = this;

			self._id = id;
			self._options = options;

			self._table = $(id);
			if (self._table.tagName != 'TABLE') return;


			Event.observe( self._table.up('form') , 'submit',	function(event) { event.preventDefault(); });

			self._data[id] = Object.extend( {dom : {cells:{}}}, options || {} );

			self._keyhook = false;
			self._rows = self._table.tBodies[0].rows.length;
			self._cols = self._table.rows[0].cells.length;

			// 셀마다 id 부여
			var i, j, k = 0;
			for (i=1;i<=self._rows;i++) { k=0; for (j=0;j<self._cols ;j++ ) {

				if (typeof self._table.rows[i] != 'undefined') {

					k = k + self._table.rows[i].cells[j].getAttribute('colspan');
					self._table.rows[i].cells[j].id = self._id_prefix + i + '_' + j;	// y, x

				}

				if (k > self._cols) break;

			}}

			self.evt._bind();

		}	// init
		,
		refresh : function() {

			var self = this;

			self.evt._unbind();
			self.init( self._id, self._options );

		}
		,
		getFormData : function() {

			var self = this;

			var _data, k, d;
			var _data = self._data[ self._table.id ].dom.cells;

			var _formdata = {};

			for(k in _data) {

				d = _data[k];

				if (d.changed == true)
				{
					if ( _formdata[ d.formatter.seq ] == null) _formdata[ d.formatter.seq ] = {};
					_formdata[ d.formatter.seq ][ d.formatter.name ] = d.formatter.value;
				}
			}

			return _formdata;

		}
		,
		getCellDomData : function(cell) {

			var self = this;

			if(!self._data[ self._table.id ].dom.cells[ cell.id ]) {

				// 포맷팅 옵션 불러다 저장
				var fopt = self.formatter._getOption(cell);
				fopt.value = cell.innerText.stripTags().strip();

				self._data[ self._table.id ].dom.cells[ cell.id ] = {
																		origin : cell.innerHTML,
																		active : false,
																		changed : false,
																		formatter : fopt
																	};
			}

			return self._data[ self._table.id ].dom.cells[ cell.id ];

		}
		,
		selectTableCell : function(e) {

			var el = Event.element(e);

			if (el.tagName == 'TD') {
				nsGodogrid.evt._onFocus(el);
			}
			else if (el.tagName == 'INPUT') {

			}
			else if (el.up('td').tagName == 'TD') {
				nsGodogrid.evt._onFocus(el.up('td'));
			}
			else { return; }

		}
		,
		selectTableCellByCursor : function(cur) {
			var _id = this._id_prefix + cur.y + '_' + cur.x;
			var cell = $(_id);
			if (cell) {
				nsGodogrid.evt._onFocus(cell);
			}
		}
		,
		editTableCell : function(cell,e) {

			var self = this;

			self._keyhook = false;

			var data = self.getCellDomData(cell);

			data.active = true;

			var field = new Element(	  (data.formatter.type ? data.formatter.type : 'input'), {
								'name'	: (data.formatter.name ? data.formatter.name : cell.id),
								'value' : (data.formatter.method != null ? data.formatter.method.get(data.formatter.value) : data.formatter.value)
								});

			field.onchange = self.evt._onChange.bindAsEventListener(this);
			field.onkeypress = self.evt._onChange.bindAsEventListener(this);

			cell.update(field);
			field.focus();
		}
		,
		formatter : {
			_cell_option_pattern : /({.+?})/,
			_getOption : function(cell) {

				var self	= this;
				var parent	= nsGodogrid;

				var fopt = cell.readAttribute('class').match(self._cell_option_pattern);
				if (fopt != null)
				{
					fopt = eval('(' + fopt.shift() + ')');

					for(var key in fopt) {
						if (key == 'seq' || key == 'name' || key == 'type') continue;
						if (self[key] && self[key] instanceof Object) fopt.method =  self[key];
					}
				}
				else {
					fopt = {type:'input',seq: null, name: cell.id, method:null};
				}

				return fopt;

			}
			,
			numeric : {
				set:function(str) {
					str = parseInt(str);
					return comma(str);
				},
				get:function(str) {
					var pattern = /[^0-9]/;
					return str.replace(pattern, '');
				}
			}

		}
		,
		evt : {
			_unbind : function() {

				var self	= this;
				var parent	= nsGodogrid;


				Event.stopObserving(parent._table.up('form') , 'submit');
				Event.stopObserving(parent._table.tBodies[0], 'click');
				Event.stopObserving(document,				  'click');

				if (Prototype.Browser.Gecko || Prototype.Browser.Opera ) {
					Event.stopObserving(document, 'keypress');
				} else {
					Event.stopObserving(document, 'keydown');
				}

				parent._data = {};

			}
			,
			_bind : function() {

				var self	= this;
				var parent	= nsGodogrid;

				// 키보드 이벤트
				if (Prototype.Browser.Gecko || Prototype.Browser.Opera ) {
					Event.observe(document, 'keypress',	function(event) {
						var result = self._onKeyPress(event);
						if (!result) event.preventDefault();
					});
				} else {
					Event.observe(document, 'keydown',	function(event) {
						var result = self._onKeyPress(event);
						if (!result) event.preventDefault();
					});
				}

				// 마우스 클릭 이벤트(테이블 찍으면 셀 선택, 테이블 밖에는 셀 선택 해제)
				Event.observe(parent._table.tBodies[0], 'click', parent.selectTableCell);
				Event.observe(document,					'click', function(event) {
					if (!parent._focused) return;

					var el = Event.element(event);

					if (el.descendantOf(parent._table) == false) {

						if (el.tagName != 'TD' && el.tagName != 'INPUT') {return;}

						self._onBlur(parent._focused);
						parent._keyhook = false;
						parent._focused_old = null;
					}
				});
			}
			,
			_onFocus : function(cell) {

				var self	= this;
				var parent	= nsGodogrid;

				parent._keyhook = true;

				if (parent._focused != null) self._onBlur(parent._focused);

				parent._focused_old = parent._focused;
				parent._focused = cell;

				var xy = cell.id.substring(parent._id_prefix.length , cell.id.length).split('_');

				parent._cursor.x = parseInt(xy[1]);
				parent._cursor.y = parseInt(xy[0]);

				cell.addClassName('selected');

				// 가시영역을 벗어난다면 스크롤
					var sc_top		= window.pageYOffset || document.documentElement.scrollTop  || document.body.scrollTop;
					var wd_height	= window.innerHeight || (window.document.documentElement.clientHeight || window.document.body.clientHeight);
					var sel_top		= cell.cumulativeOffset()[1];
					var gap = ( sc_top + wd_height ) - sel_top;

					if ((gap - 50) < 0)
						window.scrollTo(0, sc_top + Math.abs(gap) + 50 );
					else if (gap > wd_height)
						window.scrollTo(0, sc_top - Math.abs(gap-wd_height) - 50 );

			}
			,
			_onBlur : function(cell) {

				var self	= this;
				var parent	= nsGodogrid;

				if (!cell) return;
				self._focused = null;

				var data = parent.getCellDomData(cell);

				if (data.active == true) {
					cell.innerHTML = data.formatter.value;
					data.active = false;
				}

				cell.removeClassName('selected');
			}
			,
			_onKeyPress : function(e) {

				var self	= this;
				var parent	= nsGodogrid;

				if (!parent._keyhook) return true;

				//var keyCode = (e.keyCode == 9 && e.shiftKey) ? -1 : e.keyCode;

				switch (e.keyCode) {

					case Event.KEY_LEFT:	// 좌

						if (parent._cursor.x > 0) {
							parent._cursor.x = parent._cursor.x - 1;
						}
						else {
							return;
						}
						break;

					case Event.KEY_RIGHT:	// 우

						if ((parent._cols - 1) > parent._cursor.x) {
							parent._cursor.x = parent._cursor.x + 1;
						}
						else {
							return;
						}
						break;

					case Event.KEY_UP:		// 위
						if ((parent._cursor.y - 1) > 0) {
							parent._cursor.y = parent._cursor.y - 1;
						}
						else {
							return;
						}
						break;

					case Event.KEY_DOWN:	// 아래
						if (parent._rows > parent._cursor.y) {
							parent._cursor.y = parent._cursor.y + 1;
						}
						else {
							return;
						}
						break;
					case Event.KEY_TAB:			// 다음으로 그냥 이동, 끝까지 가면 다음 행 첫번째 껄로 이동
						if ((parent._cols - 1) > parent._cursor.x) {
							parent._cursor.x = parent._cursor.x + 1;
						}
						else if (parent._rows > parent._cursor.y) {
							parent._cursor.y = parent._cursor.y + 1;
							parent._cursor.x = 0;
						}
						else {
							return;
						}
						break;
					case 33:					// page up
						if ((parent._cursor.y - 1) > 0) {
							parent._cursor.y = 1;
						}
						else {
							return;
						}
						break;
					case 34:					// page down
						if (parent._rows > parent._cursor.y) {
							parent._cursor.y = parent._rows;
						}
						else {
							return;
						}
						break;
					case 35:					// end
						if ((parent._cols - 1) > parent._cursor.x) {
							parent._cursor.x = parent._cols-1;
						}
						else {
							return;
						}
						break;
					case 36:					// home
						if (parent._cursor.x > 0) {
							parent._cursor.x = 0;
						}
						else {
							return;
						}
						break;

					default :

						// 일반적 글자들.
						if ((e.keyCode == 13) || (e.keyCode >= 48 && e.keyCode <= 90) || (e.keyCode >= 96 && e.keyCode <= 105) || (e.keyCode >= 186 && e.keyCode <= 222) ) {
							if (parent._focused.hasClassName('editable')) {
								// 셀 수정.
								parent.editTableCell(parent._focused , e);
							}
						}

						return;
				}



				// 커서를 기준으로 셀 선택.
				parent.selectTableCellByCursor(parent._cursor);
				return false;
			}
			,
			_onChange : function(e) {

				var self	= this;
				var parent	= nsGodogrid;

				if (e.type == 'keypress')
					if (e.keyCode != 13) return;

				var cell = Event.findElement(e,'td');
				var field = Event.findElement(e,'input');

				var data = parent.getCellDomData(cell);

				data.formatter.value = (data.formatter.method != null) ? data.formatter.method.set(field.value) : field.value;
				cell.innerHTML = data.formatter.value;
				data.active = false;

				cell.removeClassName('changed');

				if (data.origin == data.formatter.value)
				{
					data.changed = false;
				}
				else {
					data.changed = true;
					cell.addClassName('changed');
				}

				parent.evt._onFocus(cell);
			}
			,
			_onCancel : function(cell) {

				var self	= this;
				var parent	= nsGodogrid;

				var data = parent.getCellDomData(cell);
				cell.innerHTML = data.formatter.value;
				data.active = false;

				parent.evt._onFocus(cell);

			}

		}

	}
}();