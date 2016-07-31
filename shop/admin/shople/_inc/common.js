if (typeof Effect == 'undefined') {

	// script.aculo.us effects.js v1.9.0, Thu Dec 23 16:54:48 -0500 2010

	// Copyright (c) 2005-2010 Thomas Fuchs (http://script.aculo.us, http://mir.aculo.us)
	// Contributors:
	//  Justin Palmer (http://encytemedia.com/)
	//  Mark Pilgrim (http://diveintomark.org/)
	//  Martin Bialasinki
	//
	// script.aculo.us is freely distributable under the terms of an MIT-style license.
	// For details, see the script.aculo.us web site: http://script.aculo.us/

	// converts rgb() and #xxx to #xxxxxx format,
	// returns self (or first argument) if not convertable
	/*--------------------------------------------------------------------------*/

	Element.getInlineOpacity = function(element){
	  return $(element).style.opacity || '';
	};

	Element.forceRerendering = function(element) {
	  try {
		element = $(element);
		var n = document.createTextNode(' ');
		element.appendChild(n);
		element.removeChild(n);
	  } catch(e) { }
	};

	/*--------------------------------------------------------------------------*/

	var Effect = {
	  _elementDoesNotExistError: {
		name: 'ElementDoesNotExistError',
		message: 'The specified DOM element does not exist, but is required for this effect to operate'
	  },
	  Transitions: {
		linear: Prototype.K,
		sinoidal: function(pos) {
		  return (-Math.cos(pos*Math.PI)/2) + .5;
		},
		reverse: function(pos) {
		  return 1-pos;
		},
		flicker: function(pos) {
		  var pos = ((-Math.cos(pos*Math.PI)/4) + .75) + Math.random()/4;
		  return pos > 1 ? 1 : pos;
		},
		wobble: function(pos) {
		  return (-Math.cos(pos*Math.PI*(9*pos))/2) + .5;
		},
		pulse: function(pos, pulses) {
		  return (-Math.cos((pos*((pulses||5)-.5)*2)*Math.PI)/2) + .5;
		},
		spring: function(pos) {
		  return 1 - (Math.cos(pos * 4.5 * Math.PI) * Math.exp(-pos * 6));
		},
		none: function(pos) {
		  return 0;
		},
		full: function(pos) {
		  return 1;
		}
	  },
	  DefaultOptions: {
		duration:   1.0,   // seconds
		fps:        100,   // 100= assume 66fps max.
		sync:       false, // true for combining
		from:       0.0,
		to:         1.0,
		delay:      0.0,
		queue:      'parallel',
		callback:	null
	  },
	  tagifyText: function(element) {
		var tagifyStyle = 'position:relative';
		if (Prototype.Browser.IE) tagifyStyle += ';zoom:1';

		element = $(element);
		$A(element.childNodes).each( function(child) {
		  if (child.nodeType==3) {
			child.nodeValue.toArray().each( function(character) {
			  element.insertBefore(
				new Element('span', {style: tagifyStyle}).update(
				  character == ' ' ? String.fromCharCode(160) : character),
				  child);
			});
			Element.remove(child);
		  }
		});
	  },
	  multiple: function(element, effect) {
		var elements;
		if (((typeof element == 'object') ||
			Object.isFunction(element)) &&
		   (element.length))
		  elements = element;
		else
		  elements = $(element).childNodes;

		var options = Object.extend({
		  speed: 0.1,
		  delay: 0.0
		}, arguments[2] || { });
		var masterDelay = options.delay;

		$A(elements).each( function(element, index) {
		  new effect(element, Object.extend(options, { delay: index * options.speed + masterDelay }));
		});
	  },
	  PAIRS: {
		'appear': ['Appear','Fade']
	  },
	  toggle: function(element, effect, options) {
		element = $(element);
		effect  = (effect || 'appear').toLowerCase();

		return Effect[ Effect.PAIRS[ effect ][ element.visible() ? 1 : 0 ] ](element, Object.extend({
		  queue: { position:'end', scope:(element.id || 'global'), limit: 1 }
		}, options || {}));
	  }
	};

	Effect.DefaultOptions.transition = Effect.Transitions.sinoidal;

	/* ------------- core effects ------------- */

	Effect.ScopedQueue = Class.create(Enumerable, {
	  initialize: function() {
		this.effects  = [];
		this.interval = null;
	  },
	  _each: function(iterator) {
		this.effects._each(iterator);
	  },
	  add: function(effect) {
		var timestamp = new Date().getTime();

		var position = Object.isString(effect.options.queue) ?
		  effect.options.queue : effect.options.queue.position;

		switch(position) {
		  case 'front':
			// move unstarted effects after this effect
			this.effects.findAll(function(e){ return e.state=='idle' }).each( function(e) {
				e.startOn  += effect.finishOn;
				e.finishOn += effect.finishOn;
			  });
			break;
		  case 'with-last':
			timestamp = this.effects.pluck('startOn').max() || timestamp;
			break;
		  case 'end':
			// start effect after last queued effect has finished
			timestamp = this.effects.pluck('finishOn').max() || timestamp;
			break;
		}

		effect.startOn  += timestamp;
		effect.finishOn += timestamp;

		if (!effect.options.queue.limit || (this.effects.length < effect.options.queue.limit))
		  this.effects.push(effect);

		if (!this.interval)
		  this.interval = setInterval(this.loop.bind(this), 15);
	  },
	  remove: function(effect) {
		this.effects = this.effects.reject(function(e) { return e==effect });
		if (this.effects.length == 0) {
		  clearInterval(this.interval);
		  this.interval = null;
		}
	  },
	  loop: function() {
		var timePos = new Date().getTime();
		for(var i=0, len=this.effects.length;i<len;i++)
		  this.effects[i] && this.effects[i].loop(timePos);
	  }
	});

	Effect.Queues = {
	  instances: $H(),
	  get: function(queueName) {
		if (!Object.isString(queueName)) return queueName;

		return this.instances.get(queueName) ||
		  this.instances.set(queueName, new Effect.ScopedQueue());
	  }
	};
	Effect.Queue = Effect.Queues.get('global');

	Effect.Base = Class.create({
	  position: null,
	  start: function(options) {
		if (options && options.transition === false) options.transition = Effect.Transitions.linear;
		this.options      = Object.extend(Object.extend({ },Effect.DefaultOptions), options || { });
		this.currentFrame = 0;
		this.state        = 'idle';
		this.startOn      = this.options.delay*1000;
		this.finishOn     = this.startOn+(this.options.duration*1000);
		this.fromToDelta  = this.options.to-this.options.from;
		this.totalTime    = this.finishOn-this.startOn;
		this.totalFrames  = this.options.fps*this.options.duration;

		this.render = (function() {
		  function dispatch(effect, eventName) {
			if (effect.options[eventName + 'Internal'])
			  effect.options[eventName + 'Internal'](effect);
			if (effect.options[eventName])
			  effect.options[eventName](effect);
		  }

		  return function(pos) {
			if (this.state === "idle") {
			  this.state = "running";
			  dispatch(this, 'beforeSetup');
			  if (this.setup) this.setup();
			  dispatch(this, 'afterSetup');
			}
			if (this.state === "running") {
			  pos = (this.options.transition(pos) * this.fromToDelta) + this.options.from;
			  this.position = pos;
			  dispatch(this, 'beforeUpdate');
			  if (this.update) this.update(pos);
			  dispatch(this, 'afterUpdate');
			}
		  };
		})();

		this.event('beforeStart');
		if (!this.options.sync)
		  Effect.Queues.get(Object.isString(this.options.queue) ?
			'global' : this.options.queue.scope).add(this);
	  },
	  loop: function(timePos) {
		if (timePos >= this.startOn) {
		  if (timePos >= this.finishOn) {
			this.render(1.0);
			this.cancel();
			this.event('beforeFinish');
			if (this.finish) this.finish();
			this.event('afterFinish');
			return;
		  }
		  var pos   = (timePos - this.startOn) / this.totalTime,
			  frame = (pos * this.totalFrames).round();
		  if (frame > this.currentFrame) {
			this.render(pos);
			this.currentFrame = frame;
		  }
		}
	  },
	  cancel: function() {
		if (!this.options.sync)
		  Effect.Queues.get(Object.isString(this.options.queue) ?
			'global' : this.options.queue.scope).remove(this);
		this.state = 'finished';
	  },
	  event: function(eventName) {
		if (this.options[eventName + 'Internal']) this.options[eventName + 'Internal'](this);
		if (this.options[eventName]) this.options[eventName](this);
	  },
	  inspect: function() {
		var data = $H();
		for(property in this)
		  if (!Object.isFunction(this[property])) data.set(property, this[property]);
		return '#<Effect:' + data.inspect() + ',options:' + $H(this.options).inspect() + '>';
	  }
	});

	Effect.Opacity = Class.create(Effect.Base, {
	  initialize: function(element) {
		this.element = $(element);
		if (!this.element) throw(Effect._elementDoesNotExistError);
		// make this work on IE on elements without 'layout'
		if (Prototype.Browser.IE && (!this.element.currentStyle.hasLayout))
		  this.element.setStyle({zoom: 1});
		var options = Object.extend({
		  from: this.element.getOpacity() || 0.0,
		  to:   1.0
		}, arguments[1] || { });
		this.start(options);
	  },
	  update: function(position) {
		this.element.setOpacity(position);
	  }
	});

	Effect.Faderemove = function(element) {
	  element = $(element);
	  var oldOpacity = element.getInlineOpacity();
	  var options = Object.extend({
		from: element.getOpacity() || 1.0,
		to:   0.0,
		afterFinishInternal: function(effect) {

		  if (effect.options.to!=0) return;
		  effect.element.remove();

		}
	  }, arguments[1] || { });
	  return new Effect.Opacity(element,options);
	};

	Effect.Fade = function(element) {
	  element = $(element);
	  var oldOpacity = element.getInlineOpacity();
	  var options = Object.extend({
		from: element.getOpacity() || 1.0,
		to:   0.0,
		afterFinishInternal: function(effect) {

		  if (effect.options.to!=0) return;
		  effect.element.hide().setStyle({opacity: oldOpacity});

		}
	  }, arguments[1] || { });
	  return new Effect.Opacity(element,options);
	};

	Effect.Appear = function(element) {
	  element = $(element);
	  var options = Object.extend({
	  from: (element.getStyle('display') == 'none' ? 0.0 : element.getOpacity() || 0.0),
	  to:   1.0,
	  // force Safari to render floated elements properly
	  afterFinishInternal: function(effect) {
		effect.element.forceRerendering();
	  },
	  beforeSetup: function(effect) {
		effect.element.setOpacity(effect.options.from).show();
	  }}, arguments[1] || { });
	  return new Effect.Opacity(element,options);
	};

	Effect.Pulsate = function(element) {
	  element = $(element);
	  var options    = arguments[1] || { },
		oldOpacity = element.getInlineOpacity(),
		transition = options.transition || Effect.Transitions.linear,
		reverser   = function(pos){
		  return 1 - transition((-Math.cos((pos*(options.pulses||5)*2)*Math.PI)/2) + .5);
		};

	  return new Effect.Opacity(element,
		Object.extend(Object.extend({  duration: 2.0, from: 0,
		  afterFinishInternal: function(effect) { effect.element.setStyle({opacity: oldOpacity}); }
		}, options), {transition: reverser}));
	};

	Element.CSS_PROPERTIES = $w(
	  'backgroundColor backgroundPosition borderBottomColor borderBottomStyle ' +
	  'borderBottomWidth borderLeftColor borderLeftStyle borderLeftWidth ' +
	  'borderRightColor borderRightStyle borderRightWidth borderSpacing ' +
	  'borderTopColor borderTopStyle borderTopWidth bottom clip color ' +
	  'fontSize fontWeight height left letterSpacing lineHeight ' +
	  'marginBottom marginLeft marginRight marginTop markerOffset maxHeight '+
	  'maxWidth minHeight minWidth opacity outlineColor outlineOffset ' +
	  'outlineWidth paddingBottom paddingLeft paddingRight paddingTop ' +
	  'right textIndent top width wordSpacing zIndex');

	Element.CSS_LENGTH = /^(([\+\-]?[0-9\.]+)(em|ex|px|in|cm|mm|pt|pc|\%))|0$/;

	String.__parseStyleElement = document.createElement('div');
	String.prototype.parseStyle = function(){
	  var style, styleRules = $H();
	  if (Prototype.Browser.WebKit)
		style = new Element('div',{style:this}).style;
	  else {
		String.__parseStyleElement.innerHTML = '<div style="' + this + '"></div>';
		style = String.__parseStyleElement.childNodes[0].style;
	  }

	  Element.CSS_PROPERTIES.each(function(property){
		if (style[property]) styleRules.set(property, style[property]);
	  });

	  if (Prototype.Browser.IE && this.include('opacity'))
		styleRules.set('opacity', this.match(/opacity:\s*((?:0|1)?(?:\.\d*)?)/)[1]);

	  return styleRules;
	};

	if (document.defaultView && document.defaultView.getComputedStyle) {
	  Element.getStyles = function(element) {
		var css = document.defaultView.getComputedStyle($(element), null);
		return Element.CSS_PROPERTIES.inject({ }, function(styles, property) {
		  styles[property] = css[property];
		  return styles;
		});
	  };
	} else {
	  Element.getStyles = function(element) {
		element = $(element);
		var css = element.currentStyle, styles;
		styles = Element.CSS_PROPERTIES.inject({ }, function(results, property) {
		  results[property] = css[property];
		  return results;
		});
		if (!styles.opacity) styles.opacity = element.getOpacity();
		return styles;
	  };
	}

	Effect.Methods = {
	  morph: function(element, style) {
		element = $(element);
		new Effect.Morph(element, Object.extend({ style: style }, arguments[2] || { }));
		return element;
	  },
	  visualEffect: function(element, effect, options) {
		element = $(element);
		var s = effect.dasherize().camelize(), klass = s.charAt(0).toUpperCase() + s.substring(1);
		new Effect[klass](element, options);
		return element;
	  },
	  highlight: function(element, options) {
		element = $(element);
		new Effect.Highlight(element, options);
		return element;
	  }
	};

	$w('fade appear pulsate faderemove').each(
	  function(effect) {
		Effect.Methods[effect] = function(element, options){
		  element = $(element);
		  Effect[effect.charAt(0).toUpperCase() + effect.substring(1)](element, options);
		  return element;
		};
	  }
	);

	$w('getInlineOpacity forceRerendering getStyles').each(
	  function(f) { Effect.Methods[f] = Element[f]; }
	);

	Element.addMethods(Effect.Methods);

}

var nsShople = function() {

	function cl(v) {
		if (console != undefined)
			console.log(v);
		else
			alert(v);

	}

	function post(url, param, cb) {
		if (cb == undefined) cb = function(){};
		return new Ajax.Request( url,
		{
			method: "post",
			parameters: param,
			onComplete: cb
		});
	}

	function popup(url,w_width,w_height,scroll) {

		var x = (screen.availWidth - w_width) / 2;
		var y = (screen.availHeight - w_height) / 2;
		var sc = "scrollbars=yes";
		return window.open(url,"","width="+w_width+",height="+w_height+",top="+y+",left="+x+","+sc);

	}

	function $RF(el, radioGroup) {

		if($(el).type && $(el).type.toLowerCase() == 'radio') {
			var radioGroup = $(el).name;
			var el = $(el).form;
		}
		else if ($(el).tagName.toLowerCase() != 'form') {
			return false;
		}

		var checked = $(el).getInputs('radio', radioGroup).find(function(re) {return re.checked;});

		return (checked) ? $F(checked) : null;
	}

	function getPaging(page,total) {

		var navi = '';

		var start		= (Math.ceil(page/10)-1)*10;

		if(page>10){
			navi += "<a href=\"javascript:nsShople.page(1);\" class=navi>[1]</a>";
			navi += "<a href=\"javascript:nsShople.page("+start+");\" class=navi>��</a>";
		}

		var i = 0;
		var move, next;

		while(i+start < total && i<10){
			i++;
			move = i+start;
			navi += (page==move) ? " <b>"+move+"</b> " : " <a href=\"javascript:nsShople.page("+move+");\" class=navi>["+move+"]</a> ";
		}

		if(total>move){
			next = move+1;
			navi += "<a href=\"javascript:nsShople.page("+next+");\" class=navi>��</a>";
			navi += "<a href=\"javascript:nsShople.page("+total+");\" class=navi>["+total+"]</a>";
		}

		return navi;

	}


	function serialize(v) {

		return v;

	}

	return {

		page : function (n) {
			$('frmListOption').fire("shople:submit", {page : n });
		}
		,
		osd : {
			make : function(obj) {

				var p = obj.up('tr');

				var osd = new Element('span', {'class': 'osd'});
				var _pos = p.cumulativeOffset();
				osd.setStyle({top : (_pos[1]+1) + 'px',left : (_pos[0]+1) + 'px',width:(p.getWidth() - 2 )+ 'px', height:(p.getHeight()-2) + 'px', lineHeight:(p.getHeight()-2) + 'px'});
				osd.removeClassName('status-error').removeClassName('status-success').removeClassName('status-sending');

				document.body.appendChild(osd);

				return osd;
			}

		}
		,

	 // ī�װ�
		category : {

			selects	: $$('.el-shople-category'),
			catename: ['��з�','�ߺз�','�Һз�','���з�'],
			init	: function(full_dispno) {

						var self = this;

						self.selects.each(function(item) {
							item.onchange = function() { self.set(this) };
						});

						if (full_dispno) self.preset(full_dispno);
			}
			,
			preset	: function(full_dispno) {
						var tmp = full_dispno.split('|');
						var select = this.selects[0];

						select.setValue( tmp[0] );

						for (var i=0;i<select.options.length ;i++)
						{
							if (select.options[i].value == tmp[0])
							{
								select.selectedIndex = i;
								break;
							}

						}
						this.set(1,full_dispno);
						return;
			}
			,
			set		: function (o/* or depth*/, full_dispno) {

						var self = this;

						if (typeof(o) != 'object') {						// depth
							var id			= 'el-shople-category-'+o;
							var depth		= parseInt(o);
							var depth_next	= depth+1;
							o				= $(id);
						}
						else {												// object
							var id			= o.getAttribute('id');
							var depth		= parseInt(id.replace('el-shople-category-',''));
							var depth_next	= depth+1;
						}

						if (o.value == 'null')
						{
							return;
						}

						var ar_dispno = false;
						if (full_dispno != undefined) ar_dispno = full_dispno.split('|');
						else full_dispno = '';

						var select, row;
						var selects_size = self.selects.size();

						// ���� ����Ʈ �ڽ� �ɼ� �ʱ�ȭ.
						for (var i=depth_next; i<= selects_size; i++ ) {
							select = self.selects[i-1];
							while (select.options.length > 0) select.options[ (select.options.length-1) ] = null;
							select.options[0]=new Option(self.catename[i-1] + ' ����', 'null');
						}

						// ����ĭ �ɼ�
						var ajax = post(
							'../shople/ax.indb.category.php',
							'mode=get&depth=' + depth_next + '&dispno=' + o.value,
							function(){
								if (ajax.transport.status == 200) {

									var json = ajax.transport.responseText.evalJSON(true);

									select = self.selects[depth_next-1];

									if (json.length < 1){
										select.options[0]=new Option(self.catename[depth_next-1] + '�� �����ϴ�.', 'null');
										return;
									}

									for (var i=0;i< json.length ;i++ ) {
										row = json[i];
										select.options[i]=new Option(row.name, row.dispno );
									}

									select.setValue( ar_dispno[ depth_next - 1 ] );

									if (selects_size > depth && ar_dispno[depth_next] != undefined)
									{
										depth++;
										self.set(depth, full_dispno);
									}

								}
						});
			}
			,
			search	: function() {

						var tmp_row;

						if ($('srchName').value == '')
						{
							alert('�˻�� �Է��ϼž� �մϴ�');
							return false;
						}

						$$('#srchCatePrint li').each(function(item) {
							item.remove();
						});

						var ajax = post(
							'../shople/ax.indb.category.php',
							'mode=search&keyword='+ $('srchName').value ,
							function(){
								if (ajax.transport.status == 200) {

									if (ajax.transport.responseText != 'null') {

										var json = ajax.transport.responseText.evalJSON(true);

										for (var i=0;i<json.length ;i++) {
											tmp_row = json[i];
											$('srchCatePrint').insert('<li><img src="../img/btn_openmarket_cateselect.gif" align="absmiddle" onClick="nsShople.category.preset(\''+tmp_row.full_dispno +'\');"> '+ tmp_row.full_name +'</li>');
										}
									}
									else { $('srchCatePrint').insert('<li class="notice">�˻� ����� �����ϴ�.</li>'); }
								}
						});
						return false;
			}
			,
			apply	: function() {

				var self = this;

				var select, row;
				var selects_size = self.selects.size();

				var full_dispno = '';
				var full_name = '';

				var samelow = ($('samelow').checked ? 'Y' : '');

				for (var i = 1;i<=selects_size ;i++)
				{
					select = self.selects[i-1];

					if (select.length > 0 && select.value == '' && select.options[0].value != 'null') {
						alert("���� �з��� ������ �ּ���.");
						return false;
					}
					else if (select.value) {
						full_dispno += (full_dispno == '') ? select.value : '|' + select.value;
						full_name += (full_name == '') ? select.options[select.selectedIndex].text : ' > ' + select.options[select.selectedIndex].text;
					}
				}

				if (idnm) {
					parent.$(idnm).value = full_dispno;
					parent.$(idnm + '_text').update( '<a href="javascript:popupLayer(\'../shople/popup.config.category.php?full_dispno='+full_dispno+'&idnm='+idnm+'\',750,550);">'+full_name+'</a>' );
					parent.closeLayer();
					return;
				}

				// ���� or �׳� �Է�
				var ajax = post(
					'../shople/ax.indb.category.php',
					'mode=save&catno='+full_dispno+'&category='+category+'&samelow='+samelow,
					function(){
						if (ajax.transport.status == 200) {

							if (samelow == '')
							{
								var opentr = parent.$('cateMatchList').rows[rowIdx];
								opentr.cells[1].innerHTML = full_name;
								opentr.cells[2].innerHTML = '<img src="../img/btn_openmarket_cateedit.gif" style="cursor:pointer;" onclick="popupLayer(\'../shople/popup.config.category.php?category=' + category + '&full_dispno=' + full_dispno + '&rowIdx=\'+this.parentNode.parentNode.rowIndex,750,550);">';
							}
							else {
								var catno_obj = parent.document.getElementsByTagName('catno');
								for (i=0; i < catno_obj.length; i++)
								{
									if ( catno_obj[i].getAttribute('category').match( eval("/^"+category+"/") ) )
									{
										var opentr = catno_obj[i].parentNode.parentNode;
										opentr.cells[1].innerHTML = full_name;
										opentr.cells[2].innerHTML = '<img src="../img/btn_openmarket_cateedit.gif" style="cursor:pointer;" onclick="popupLayer(\'../shople/popup.config.category.php?category=' + category + '&full_dispno=' + full_dispno + '&rowIdx=\'+this.parentNode.parentNode.rowIndex,750,550);">';
									}
								}
							}

							parent.closeLayer();

						}
				});
			}

		}
	 // eof ī�װ�
		,
	 // ��ǰ
		goods : {
			_form : null,
			_list : null,
			_queue: [],
			_queueProcess : function() {
				if (this._queue.length > 0) this._send( this._queue.shift() );
			}
			,
			_layout : {
				head : '\
					<tr>\
						<th style="width:30px;"><input type="checkbox" onclick="chkBoxAll(document.getElementsByName(\'chk[]\'),\'rev\')" class="null"></th>\
						<th style="width:70px;">��ǰ��ȣ</th>\
						<th>��ǰ��</th>\
						<th style="width:75px;">�ǸŻ���</th>\
						<th style="width:150px;">�����</th>\
						<th style="width:70px;">�ǸŰ�</th>\
						<th style="width:60px;">���</th>\
						<th style="width:50px;">�ɼ�</th>\
						<th style="width:50px;">�󼼼���</th>\
						<th style="width:50px;">�̹���</th>\
						<th style="width:70px;">����</th>\
					</tr>'
				,
				row : '\
					<tr>\
						<td class="noedit noline"><input type="checkbox" name="chk[]" value="#{prdNo}" onclick="iciSelect(this)"></td>\
						<td class="noedit"><a href="http://www.11st.co.kr/product/SellerProductDetail.tmall?method=getSellerProductDetail&prdNo=#{prdNo}" target="_blank">#{prdNo}</a></td>\
						<td class="editable al {type:\'input\', seq:\'#{prdNo}\',name:\'prdNm\'}">#{prdNm}</td>\
						<td class="noedit"><span id="selStatNm-#{prdNo}">#{selStatNm}</span></td>\
						<td class="noedit">#{regdt}</td>\
						<td class="editable {type:\'input\', seq:\'#{prdNo}\',name:\'selPrc\',numeric:true}">#{selPrc}</td>\
						<td>#{prdSelQty}</td>\
						<td class="noedit"><a href="javascript:nsShople.edit.option(#{prdNo},\'11st\');"><img src="../img/i_edit.gif" alt="����"></a></td>\
						<td class="noedit"><a href="javascript:nsShople.edit.descript(#{prdNo},\'11st\');"><img src="../img/i_edit.gif" alt="����"></a></td>\
						<td class="noedit"><a href="javascript:nsShople.edit.image(#{prdNo},\'11st\');"><img src="../img/i_edit.gif" alt="����"></a></td>\
						<td class="noedit"><a href="javascript:nsShople.edit.goods(#{prdNo},\'11st\');"><img src="../img/btn_product_edit.gif" alt="��ǰ����"></a></td>\
					</tr>'
			}
			,
			_send : function(data) {

				var self = this;
				var seq;

				seq = (data.seq != null) ? data.seq : data.object.getValue();

				var ex_param = (data.param) ? '&' + data.param : '';

				if (data.object == undefined) $$('input[name="chk[]"]').each(function(item) { if (item.value == seq) data.object = item; });

				var osd = nsShople.osd.make( data.object );

				var ajax = new Ajax.Request('../shople/ax.indb.goods.php', {
					method: "post",
					parameters: 'mode='+data.mode+'&seq='+seq + ex_param,
					asynchronous: true,
					onComplete: function(response) {
						if (response.status == 200) {

							var json = response.responseText.evalJSON(true);

							if (json.result == true) {

								osd.removeClassName('status-sending').addClassName('status-success').update( data.message.end );
								osd.fade({duration: 0.5,delay:2});

								if (data.mode == 'register') $('prdno-'+seq).update('Y');
								else if (data.mode == 'stopdisplay') $('selStatNm-'+seq).update('�Ǹ�����');
								else if (data.mode == 'startdisplay') $('selStatNm-'+seq).update('�Ǹ���');

							}
							else {
								osd.removeClassName('status-sending').addClassName('status-error').update( json.body );
								osd.onclick = function() { this.fade({duration: 0.5});};
							}

							if (data.object) data.object.writeAttribute('checked',false);

						}

						self._queueProcess();

					},
					onCreate : function(){
						osd.appear({duration: 0.2});
						osd.addClassName('status-sending').update(data.message.start);
					}
				});	// ajax
			}
			,
			register : function() {

				var self = this;

				var data = {};

				$$('input[name="chk[]"]:checked').each(function(item){
					data = {
						object : item,
						mode : 'register',
						message : {
							start : '������',
							end : '��ǰ������ ���������� �Ϸ�Ǿ����ϴ�.'
						}
					}
					self._queue.push(data);

				});

				self._queueProcess();

			}
			,
			stopdisplay : function() {

				var self = this;

				var data = {};

				$$('input[name="chk[]"]:checked').each(function(item){
					data = {
						object : item,
						mode : 'stopdisplay',
						message : {
							start : '�Ǹ����� ���� ó�����Դϴ�.',
							end : '�Ǹ����� ���·� �����Ǿ����ϴ�.'
						}
					}
					self._queue.push(data);

				});

				self._queueProcess();
			}
			,
			startdisplay : function() {

				var self = this;

				var data = {};

				$$('input[name="chk[]"]:checked').each(function(item){
					data = {
						object : item,
						mode : 'startdisplay',
						message : {
							start : '�Ǹ����� ���� ó�����Դϴ�.',
							end : '�Ǹ����� ���°� ���� �Ǿ����ϴ�.'
						}
					}
					self._queue.push(data);

				});

				self._queueProcess();

			}
			,
			save : function(datas) {

				var self = this;

				var data = {};

				Object.keys(datas).sort(function(a,b){return a-b;}).each(function(seq) {

					data = {
						seq : seq,
						//object : item,
						param : Object.toQueryString(datas[seq]),
						mode : 'save',
						message : {
							start : '������',
							end : '��ǰ������ ���������� �Ϸ�Ǿ����ϴ�.'
						}
					}
					self._queue.unshift(data);

				});

				self._queueProcess();

			}
			,
			init : function() {

				var self = this;

				self._list = $('oGoodslist');

				self._form = $('frmListOption');
				self._form.onsubmit = self.reload.bindAsEventListener(this);
				self._form.observe("shople:submit", function(event) {

					if (self._form.page == undefined) {
						self._form.insert({
							top : '<input type="hidden" name="page" value="" />'
						});
					}
					self._form.page.value = (event.memo != undefined) ? event.memo.page : 3;
					self.reload();
				});
				self.load();

			}
			,
			_remove : function() {

				var self = this;

				try{ Element.remove(self._list.down('thead').rows[0]); }
				catch (e) { }

				$A(self._list.down('tbody').rows).each(function(tr){
					Element.remove(tr);
				});

			}
			,
			reload : function() {

				var self = this;

				self._remove();
				self.load();

				return false;
			}
			,
			load : function() {

				var self = this;

				var ajax = new Ajax.Request('../shople/ax.indb.goods.php', {
					method: "post",
					parameters: '&mode=list&'+self._form.serialize(),
					asynchronous: true,
					onComplete: function(response) { if (response.status == 200) {

						self._list.down('tbody').down('tr').remove();

						var json = response.responseText.evalJSON(true);

						if (json.result == true) {

							// preparing draw works.
								g_jsonData = json.body;

								var i,row,html,len, no;
								var _tpl_row = new Template( self._layout.row );
								var _tpl_head = new Template( self._layout.head );

							// thead draw.
								self._list.down('thead').insert({ bottom: _tpl_head.evaluate({}) });

							// tbody draw.
								len = g_jsonData.length;

								for (i=0; i<len ;i++) {
									row = g_jsonData[i];

									row.optionAllQty = comma(row.optionAllQty);
									row.selPrc = comma(row.selPrc);
									self._list.down('tbody').insert({ bottom: _tpl_row.evaluate(row) });
								}

							// paging draw.
								var pg = getPaging(json.page, json.pages);
								$('pageNavi').update(pg);

							// grid refresh.
								nsGodogrid.refresh();

						}
						else {
							self._list.down('tbody').insert({ bottom: '<tr><td colspan="20" class="nodata">��ȸ����� �������� �ʽ��ϴ�.</td></tr>' });
						}

					}},
					onCreate : function(){
						self._list.down('tbody').insert({ bottom: '<tr><td colspan="20" class="loading"><img src="../img/loading.gif"></td></tr>' });
					}
				});

			}
		}
	 // eof ��ǰ
		,
	 // �ı�/����
		review: {
			_form : null,
			_list : null,
			init : function() {

				var self = this;

				self._list = $('oReviewList');
				self._form = $('frmListOption');
				self._form.onsubmit = self.reload.bindAsEventListener(this);
				self._form.observe("shople:submit", function(event) {

					if (self._form.page == undefined) {
						self._form.insert({
							top : '<input type="hidden" name="page" value="" />'
						});
					}
					self._form.page.value = (event.memo != undefined) ? event.memo.page : 3;
					self.reload();
				});
				self.load();

			}
			,
			_layout : {
				LIST : {
					head : '\
						<tr>\
							<th style="width:70px;">��ȣ</th>\
							<th style="width:70px;">��ǰ��ȣ</th>\
							<th>��ǰ��</th>\
							<th style="width:70px;">��������</th>\
							<th>����</th>\
							<th style="width:60px;">���ſ���</th>\
							<th style="width:70px;">�ۼ���</th>\
							<th style="width:120px;">�����Ͻ�</th>\
							<th style="width:70px;">ó���Ͻ�</th>\
							<th style="width:60px;">ó������</th>\
						</tr>'
					,
					row : '\
						<tr id="#{id}">\
							<td>#{brdInfoNo}</td>\
							<td>#{brdInfoClfNo}</td>\
							<td class="al">#{prdNm}</td>\
							<td>#{qnaDtlsCdNm}</td><!-- (qnaDtlsCd)-->\
							<td class="al">#{brdInfoSbjct}</td>\
							<td>#{buyYn}</td>\
							<td>#{memID}<br>(#{memNM})</td>\
							<td>#{createDt}</td>\
							<td>#{answerDt}</td>\
							<td>#{answerYn}</td>\
						</tr>'
				}
			}
			,
			reload : function() {

				var self = this;

				self._remove();
				self.load();

				return false;
			}
			,
			_remove : function() {

				var self = this;

				Element.remove(self._list.down('thead').rows[0]);

				$A(self._list.down('tbody').rows).each(function(tr){
					Element.remove(tr);
				});

			}
			,
			load : function() {
				var self = this;

				var ajax = new Ajax.Request('../shople/ax.indb.cs.php', {
					method: "post",
					parameters: '&mode=reviewlist&'+self._form.serialize(),
					asynchronous: true,
					onComplete: function(response) { if (response.status == 200) {

						self._list.down('tbody').down('tr').remove();

						var json = response.responseText.evalJSON(true);

						if (json.result == true) {

							// ������ ������
								g_jsonData = json.body;

								var i,row, html,len, no, tr;
								var _tpl_row	= new Template( self._layout.LIST.row );
								var _tpl_head	= new Template( self._layout.LIST.head );

							// thead
								self._list.down('thead').insert({ bottom: _tpl_head.evaluate({}) });

							// tbody
								$H(g_jsonData).each(function(pair){
									pair.value.id = pair.key;
									tr = _tpl_row.evaluate(pair.value);
									self._list.down('tbody').insert({ bottom: tr });
								});

						}
						else {
							self._list.down('tbody').insert({ bottom: '<tr><td colspan="20" class="nodata">��ȸ����� �������� �ʽ��ϴ�.</td></tr>' });
						}

					}},
					onCreate : function(){
						self._list.down('tbody').insert({ bottom: '<tr><td colspan="20" class="loading"><img src="../img/loading.gif"></td></tr>' });
					}
				});
			}

		}
		,

	 // Q&A
		qna : {
			_form : null,
			_form : null,
			_list : null,
			init : function() {

				var self = this;

				self._list = $('oQnaList');
				self._form = $('frmListOption');
				self._form.onsubmit = self.reload.bindAsEventListener(this);
				self._form.observe("shople:submit", function(event) {

					if (self._form.page == undefined) {
						self._form.insert({
							top : '<input type="hidden" name="page" value="" />'
						});
					}
					self._form.page.value = (event.memo != undefined) ? event.memo.page : 3;
					self.reload();
				});
				self.load();

			}
			,
			_layout : {
				LIST : {
					head : '\
						<tr>\
							<th style="width:70px;">��ȣ</th>\
							<th style="width:70px;">��ǰ��ȣ</th>\
							<th>��ǰ��</th>\
							<th style="width:70px;">��������</th>\
							<th>����</th>\
							<th style="width:60px;">���ſ���</th>\
							<th style="width:70px;">�ۼ���</th>\
							<th style="width:120px;">�����Ͻ�</th>\
							<th style="width:70px;">ó���Ͻ�</th>\
							<th style="width:60px;">ó������</th>\
						</tr>'
					,
					row : '\
						<tr id="#{id}" onClick="nsShople.qna.fill(\'SEQ#{brdInfoNo}\');">\
							<td>#{brdInfoNo}</td>\
							<td>#{brdInfoClfNo}</td>\
							<td class="al">#{prdNm}</td>\
							<td>#{qnaDtlsCdNm}</td><!-- (qnaDtlsCd)-->\
							<td class="al">#{brdInfoSbjct}</td>\
							<td>#{buyYn}</td>\
							<td>#{memID}<br>(#{memNM})</td>\
							<td>#{createDt}</td>\
							<td>#{answerDt}</td>\
							<td>#{answerYn}</td>\
						</tr>'
				}
			}
			,
			reload : function() {

				var self = this;

				self._remove();
				self.load();

				return false;
			}
			,
			_remove : function() {

				var self = this;

				try {
					Element.remove(self._list.down('thead').rows[0]);
				}
				catch (e) { }

				$A(self._list.down('tbody').rows).each(function(tr){
					Element.remove(tr);
				});

			}
			,
			load : function() {
				var self = this;

				var ajax = new Ajax.Request('../shople/ax.indb.cs.php', {
					method: "post",
					parameters: '&mode=qnalist&'+self._form.serialize(),
					asynchronous: true,
					onComplete: function(response) { if (response.status == 200) {

						self._list.down('tbody').down('tr').remove();

						var json = response.responseText.evalJSON(true);

						if (json.result == true) {

							// ������ ������
								g_jsonData = json.body;

								var i,row, html,len, no, tr;
								var _tpl_row	= new Template( self._layout.LIST.row );
								var _tpl_head	= new Template( self._layout.LIST.head );

							// thead
								self._list.down('thead').insert({ bottom: _tpl_head.evaluate({}) });

							// tbody
								$H(g_jsonData).each(function(pair){
									pair.value.id = pair.key;
									tr = _tpl_row.evaluate(pair.value);
									self._list.down('tbody').insert({ bottom: tr });
								});

							// paging draw.
								var pg = getPaging(json.page,json.pages);
								$('pageNavi').update(pg);

						}
						else {
							self._list.down('tbody').insert({ bottom: '<tr><td colspan="20" class="nodata">��ȸ����� �������� �ʽ��ϴ�.</td></tr>' });
						}

					}},
					onCreate : function(){
						self._list.down('tbody').insert({ bottom: '<tr><td colspan="20" class="loading"><img src="../img/loading.gif"></td></tr>' });
					}
				});
			}
			,
			fill : function(seq) {



				var data = $H(eval('g_jsonData.'+seq));

				var frmid = seq+'_FORM';

				if ($(frmid) != null) {
					$(frmid).remove();
				}
				else {

					var _tpl = new Template(  $('answer_FORM').innerHTML );
					data.set( 'buttonDisplay', (data.get('answerYn') == 'N' ? 'block' : 'none') );

					var _form = _tpl.evaluate( data );
					$(seq).insert({after: '<tr id="'+ frmid +'"><td colspan="20" class="answer-form-wrap">' + _form + '</td></tr>' });

				}

			}
			,
			answer : function(seq) {

				var form = $$('form[name="frmAnswer['+seq+']"]').shift();

				if ($(form['answerCont']).getValue().strip() == '') {
					alert('�亯������ �Է��� �ּ���.');
					return false;
				}

				if ($(form['brdInfoNo']).getValue().strip() == '' || $(form['brdInfoClfNo']).getValue().strip() == '') {
					alert('�亯ó���� ������ ������ �ּ���.');
					return false;
				}

				var ajax = new Ajax.Request('../shople/ax.indb.cs.php', {
					method: "post",
					parameters: 'mode=qna&' + form.serialize(),
					asynchronous: true,
					onComplete: function(response) { if (response.status == 200) {

						var json = response.responseText.evalJSON(true);

						if (json.result == true) {
							alert(json.body);

						}
						else {
							alert(json.body);
						}

					}}
				});


			}

		}
	 // eof Q&A
		,
	 // ������
		origin : {

			_contry : null,
			init : function() {

				var self = this;

				$$('#origin_area, #origin_value').each(function(item) {
					item.onchange = function() {
						var depth = (item.id == 'origin_area') ? 2 : 0;
						self.set(depth,	this.value);
					};
				});
			}
			,
			toggle : function() {

				var self = this;

				$$('#origin_area, #origin_value').each(function(item){
					if ($('origin_select').checked == true) {
						item.enable();
						if (item.options.length == 1) {
							self.set(1, $RF('origin_type') );

						}
					}
					else item.disable();
				});
			}
			,
			change : function(v) {

				$('origin_select_wrap').hide();
				$('origin_input_wrap').hide();

				switch (v) {
					case '02':	// �ؿ�
						$('origin_select_wrap').show();
						$('origin_select').checked = true;
						this.set(1,v);
						break;
					case '01':	// ����
						$('origin_select_wrap').show();
						$('origin_select').checked = false;
						this.set(1,v);
						break;
					case '03':	// ��Ÿ
						$('origin_input_wrap').show();
						break;

				}

				this.toggle();
			}
			,
			set		: function (depth,v) {

				if (depth < 1)
				{
					return;
				}

				var self = this;

				var el = (depth == 1) ? $('origin_area') :  $('origin_value');
				var el_next	= (depth == 1) ? $('origin_value') :  null;

				var ajax = post(
					'../shople/ax.indb.origin.php',
					'mode=get&depth='+depth+'&value=' + v,
					function(){
						if (ajax.transport.status == 200) {

							var json = ajax.transport.responseText.evalJSON(true);

							if (el_next != null)
							{
								while (el_next.options.length > 0) el_next.options[ (el_next.options.length-1) ] = null;
								el_next.options[0]=new Option('�����ϼ���', 'null');
							}

							for (var i=0;i< json.length ;i++ ) {
								row = json[i];
								el.options[i]=new Option(row.name, row.value );
							}

						}
					}
				);
			}

		}
	 // eof ������
		,
	 // ��ǰ����
		edit : {
			goods : function(seq,t) {

				if (t == '11st')
				{
					popup('../shople/popup.goods.register.php?goodsno11st='+seq ,825 ,700);
				}
				else {
					popup('../shople/popup.goods.register.php?goodsno='+seq ,825 ,700);

				}
			}
			,
			option : function(seq,t) {

				if (t == '11st')
				{
					popup('../shople/popup.goods.edit.php?mode=option&goodsno11st='+seq ,825 ,700);
				}
				else {
					popup('../shople/popup.goods.edit.php?mode=option&goodsno='+seq ,825 ,700);

				}
			}
			,
			descript : function(seq,t) {

				if (t == '11st')
				{
					popup('../shople/popup.goods.edit.php?mode=descript&goodsno11st='+seq ,825 ,700);
				}
				else {
					popup('../shople/popup.goods.edit.php?mode=descript&goodsno='+seq ,825 ,700);

				}
			}
			,
			image : function(seq,t) {

				if (t == '11st')
				{
					popup('../shople/popup.goods.edit.php?mode=image&goodsno11st='+seq ,825 ,700);
				}
				else {
					popup('../shople/popup.goods.edit.php?mode=image&goodsno='+seq ,825 ,700);

				}
			}
		}
	 // eof ��ǰ����
		,
	 // �ֹ�����
		order : {
			_form : null,
			_list : null,
			_queue: [],
			_queueProcess : function() {
				if (this._queue.length > 0) this._send( this._queue.shift() );
			},
			_layout : {
				GET_ORDER_CONFIRM_LIST : {
					head : '\
						<tr>\
							<th style="width:30px;"><input type="checkbox" onclick="chkBoxAll(document.getElementsByName(\'chk[]\'),\'rev\')" class="null"></th>\
							<th style="width:50px;">��ȣ</th>\
							<th style="width:120px;">�ֹ��Ͻ�</th>\
							<th style="width:60px;">�ֹ�����</th>\
							<th style="width:100px;">�ֹ���ȣ</th>\
							<th>��ǰ��</th>\
							<th style="width:50px;">���ŷ�</th>\
							<th style="width:60px;">�����ݾ�</th>\
							<th style="width:60px;">������</th>\
							<th style="width:60px;">������</th>\
						</tr>'
					,
					row : '\
						<tr>\
							<td class="noline"><input type="checkbox" name="chk[]" value="#{ordNo}" data="{ordNo : \'#{ordNo}\',ordPrdSeq : \'#{ordPrdSeq}\',addPrdYn : \'#{addPrdYn}\',addPrdNo : \'null\',dlvNo : \'#{dlvNo}\', ordPrdSeq : \'#{ordPrdSeq}\'}" onclick="iciSelect(this)"></td>\
							<td>#{rowNo}</td>\
							<td>#{ordDt}</td>\
							<td>#{stats}</td>\
							<td>#{ordNo}</td>\
							<td class="goodsnm osd-#{ordNo}">#{prdNm}\
							<p class="opt">#{slctPrdOptNm}</p>\
							</td>\
							<td>#{ordQty}</td>\
							<td>#{ordPayAmt}��</td>\
							<td>#{ordNm}</td>\
							<td>#{rcvrNm}</td>\
					</tr>'
					,
					buttons : 'CONFIRM'
				}
				,
				GET_ORDER_DELIVERY_LIST : {
					head : '\
						<tr>\
							<th style="width:30px;"><input type="checkbox" onclick="chkBoxAll(document.getElementsByName(\'chk[]\'),\'rev\')" class="null"></th>\
							<th style="width:50px;">��ȣ</th>\
							<th style="width:120px;">�ֹ��Ͻ�</th>\
							<th style="width:60px;">�ֹ�����</th>\
							<th style="width:100px;">�ֹ���ȣ</th>\
							<th style="min-width:400px;">��ǰ��</th>\
							<th style="width:50px;">���ŷ�</th>\
							<th style="width:80px;">�����ȣ</th>\
							<th style="width:60px;">�����ݾ�</th>\
							<th style="width:60px;">������</th>\
							<th style="width:60px;">������</th>\
						</tr>'
					,
					row : '\
						<tr>\
							<td class="noline"><input type="checkbox" name="chk[]" value="#{dlvNo}" data="{ordNo : \'#{ordNo}\',ordPrdSeq : \'#{ordPrdSeq}\',addPrdYn : \'#{addPrdYn}\',addPrdNo : \'null\',dlvNo : \'#{dlvNo}\', ordPrdSeq : \'#{ordPrdSeq}\'}" onclick="iciSelect(this)"></td>\
							<td>#{rowNo}</td>\
							<td>#{ordDt}</td>\
							<td>����غ���</td>\
							<td>#{ordNo}</td>\
							<td class="goodsnm osd-#{ordNo}">#{prdNm}\
							<p class="opt">#{slctPrdOptNm}</p>\
							</td>\
							<td>#{ordQty}</td>\
							<td class="editable {type:\'input\', seq:\'#{dlvNo}\',name:\'invcNo\'}">&nbsp;</td>\
							<td>#{ordPayAmt}��</td>\
							<td>#{ordNm}</td>\
							<td>#{rcvrNm}</td>\
					</tr>'
					,
					buttons : 'DELIVERY'
				}
				,
				GET_ORDER_DELIVERING_LIST : {
					head : '\
						<tr>\
							<th style="width:30px;"><input type="checkbox" onclick="chkBoxAll(document.getElementsByName(\'chk[]\'),\'rev\')" class="null"></th>\
							<th style="width:50px;">��ȣ</th>\
							<th style="width:120px;">�ֹ��Ͻ�</th>\
							<th style="width:60px;">�ֹ�����</th>\
							<th style="width:100px;">�ֹ���ȣ</th>\
							<th style="min-width:400px;">��ǰ��</th>\
							<th style="width:50px;">���ŷ�</th>\
							<th style="width:80px;">�����ȣ</th>\
							<th style="width:60px;">�����ݾ�</th>\
							<th style="width:60px;">������</th>\
							<th style="width:60px;">������</th>\
						</tr>'
					,
					row : '\
						<tr>\
							<td class="noline"><input type="checkbox" name="chk[]" value="#{dlvNo}" data="{ordNo : \'#{ordNo}\',ordPrdSeq : \'#{ordPrdSeq}\',addPrdYn : \'#{addPrdYn}\',addPrdNo : \'null\',dlvNo : \'#{dlvNo}\', ordPrdSeq : \'#{ordPrdSeq}\'}" onclick="iciSelect(this)"></td>\
							<td>#{rowNo}</td>\
							<td>#{ordDt}</td>\
							<td>#{stats}</td>\
							<td>#{ordNo}</td>\
							<td>#{prdNm}\
							<p class="opt">#{slctPrdOptNm}</p>\
							</td>\
							<td>#{ordQty}</td>\
							<td>#{invcNo}</td>\
							<td>#{ordPayAmt}��</td>\
							<td>#{ordNm}</td>\
							<td>#{rcvrNm}</td>\
					</tr>'
				}
				,
				GET_ORDER_COMPLETE_LIST : {
					head : '\
						<tr>\
							<th style="width:30px;"><input type="checkbox" onclick="chkBoxAll(document.getElementsByName(\'chk[]\'),\'rev\')" class="null"></th>\
							<th style="width:50px;">��ȣ</th>\
							<th style="width:120px;">�ֹ��Ͻ�</th>\
							<th style="width:60px;">�ֹ�����</th>\
							<th style="width:100px;">�ֹ���ȣ</th>\
							<th style="min-width:400px;">��ǰ��</th>\
							<th style="width:50px;">���ŷ�</th>\
							<th style="width:80px;">�����ȣ</th>\
							<th style="width:60px;">�����ݾ�</th>\
							<th style="width:60px;">������</th>\
							<th style="width:60px;">������</th>\
						</tr>',
					row : '\
						<tr>\
							<td class="noline"><input type="checkbox" name="chk[]" value="#{ordNo}" onclick="iciSelect(this)"></td>\
							<td>#{rowNo}</td>\
							<td>#{ordDt}</td>\
							<td>#{stats}</td>\
							<td>#{ordNo}</td>\
							<td class="goodsnm osd-#{ordNo}">#{prdNm}\
							<p class="opt">#{slctPrdOptNm}</p>\
							</td>\
							<td>#{ordQty}</td>\
							<td>-</td>\
							<td>#{ordPayAmt}��</td>\
							<td>#{ordNm}</td>\
							<td>#{rcvrNm}</td>\
					</tr>'
				}
			},
			init : function() {

				var self = this;

				self._list = $('oOrderlist');

				self._form = $('frmListOption');

				self._form.onsubmit = self.reload.bindAsEventListener(this);

				self._form.observe("shople:submit", function(event) {

					if (self._form.page == undefined) {
						self._form.insert({
							top : '<input type="hidden" name="page" value="" />'
						});
					}
					self._form.page.value = (event.memo != undefined) ? event.memo.page : 3;
					self.reload();
				});

				self.load();

			}
			,
			_remove : function() {

				var self = this;

				try {
					Element.remove(self._list.down('thead').rows[0]);
				}
				catch (e) { }

				$A(self._list.down('tbody').rows).each(function(tr){
					Element.remove(tr);
				});

			}
			,
			reload : function() {

				var self = this;

				self._remove();
				self.load();

				return false;
			}
			,
			load : function() {


				var self = this;

				var ajax = new Ajax.Request('../shople/ax.indb.order.php', {
					method: "post",
					parameters: '&mode=list&'+self._form.serialize(),
					asynchronous: true,
					onComplete: function(response) { if (response.status == 200) {

						self._list.down('tbody').down('tr').remove();

						var json = response.responseText.evalJSON(true);

						if (json.result == true) {

							// preparing draw works.
								g_jsonData = json.body;

								var i,row,html,len, no;
								var _tpl_row = new Template( self._layout[ self._form['method'].value ].row );
								var _tpl_head = new Template( self._layout[ self._form['method'].value ].head );
								var _tpl_buttons	= self._layout[ self._form['method'].value ].buttons;
								var _tpl_width	= self._layout[ self._form['method'].value ].width;

							// thead draw.
								self._list.down('thead').insert({ bottom: _tpl_head.evaluate({}) });

							// tbody draw.
								len = g_jsonData.length;

								for (i=0; i<len ;i++) {
									row = g_jsonData[i];
									row.ordPayAmt = comma(row.ordPayAmt);
									self._list.down('tbody').insert({ bottom: _tpl_row.evaluate(row) });
								}


							// paging draw.
								var pg = getPaging(json.page,json.pages);
								$('pageNavi').update(pg);

							// ��ư
								if (_tpl_buttons != undefined && _tpl_buttons != '') {

									$$('div.button-group').each(function(it){
										if (it.hasClassName( _tpl_buttons ))
											it.show();
										else
											it.hide();
									});
								}


							// grid refresh.
								nsGodogrid.refresh();

						}
						else {
							//self._list.down('tbody').insert({ bottom: '<tr><td colspan="20" class="nodata">' + json.body.strip() + '</td></tr>' });
							self._list.down('tbody').insert({ bottom: '<tr><td colspan="20" class="nodata">��ȸ����� �������� �ʽ��ϴ�.</td></tr>' });
						}

					}},
					onCreate : function(){
						self._list.down('tbody').insert({ bottom: '<tr><td colspan="20" class="loading"><img src="../img/loading.gif"></td></tr>' });
					}
				});

			}
			,
			download : function() {

				var self = this;

				var inputs = '<input type="hidden" name="mode" value="download" />';

				$A(self._form.serialize().split('&')).each(function(it){
					it = decodeURIComponent(it);
					var pair = it.split('=');
					inputs+='<input type="hidden" name="'+ pair[0] +'" value="'+ pair[1] +'" />';

				});
				var f = new Element('form', {'method': 'post','action' : '../shople/ax.indb.order.php','target':'ifrmHidden'}).update(inputs);
				document.body.appendChild(f);
				f.submit();
			}
			,
			excel : function() {
				var self = this;
				var _url = '../shople/popup.order.excel.php';
				popup(_url,700,600,0);
			}
			,
			getReason : function(m) {

				if ($$('input[name="chk[]"]:checked').length < 1) {
					alert('ó�� ����� ������ �ּ���.');
					return;
				}
				else {
					var _url = '../shople/popup.order.php?m='+m;
					popup(_url,600,420,0);
				}
			}
			,
			reject : function(reason /* json */) {
				var self = this;

				var data = {};

				$$('input[name="chk[]"]:checked').each(function(item){

					var row = item.readAttribute('data').evalJSON();

					row.ordCnRsnCd		= reason.ordCnRsnCd;
					row.ordCnDtlsRsn	= encodeURIComponent(decodeURIComponent(reason.ordCnDtlsRsn));	// url ���ڵ�

					data = {
						object : item,
						mode : 'reject',
						param : Object.toQueryString(  row  ) ,
						message : {
							start : '�ǸŰź� ó�����Դϴ�.',
							end : '�ǸŰź� ó�� �Ǿ����ϴ�.'
						}
					}
					self._queue.push(data);
				});

				self._queueProcess();

			}
			,
			confirm : function() {
				var self = this;

				var data = {};

				var datas = $$('input[name="chk[]"]:checked');


				if (datas.length < 1) {
					alert('����Ȯ���� �ֹ����� ������ �ּ���.');
					return;
				}

				datas.each(function(item){

					data = {
						object : item,
						mode : 'confirm',
						param : Object.toQueryString(  item.readAttribute('data').evalJSON()  ) ,
						message : {
							start : '����Ȯ�� ó�����Դϴ�.',
							end : '����Ȯ�� ó�� �Ǿ����ϴ�.'
						}
					}
					self._queue.push(data);
				});

				self._queueProcess();

			}
			,
			delivery : function() {

				var self = this;

				var datas = nsGodogrid.getFormData();

				if (Object.keys(datas).length < 1) {
					alert('�߼�ó���� �ֹ����� �����ȣ�� �Է��� �ּ���.');
					return;
				}

				Object.keys(datas).sort(function(a,b){return a-b;}).each(function(seq) {

					data = {
						seq : seq,
						param : Object.toQueryString(datas[seq]),
						mode : 'delivery',
						message : {
							start : '�߼� ó�����Դϴ�.',
							end : '�߼� ó�� �Ǿ����ϴ�.'
						}
					}
					self._queue.unshift(data);

				});

				self._queueProcess();

			}
			,
			_send : function(data) {

				var self = this;
				var seq;

				seq = (data.seq != null) ? data.seq : data.object.getValue();

				var ex_param = (data.param) ? '&' + data.param : '';

				if (data.object == undefined) $$('input[name="chk[]"]').each(function(item) { if (item.value == seq) data.object = item; });

				var osd = nsShople.osd.make( data.object );

				var ajax = new Ajax.Request('../shople/ax.indb.order.php', {
					method: "post",
					parameters: 'mode='+data.mode+'&seq='+seq + ex_param,
					asynchronous: true,
					onComplete: function(response) {

						if (response.status == 200) {

							var json = response.responseText.evalJSON(true);

							if (json.result == true) {
								osd.removeClassName('status-sending').addClassName('status-success').update( data.message.end );
								osd.fade({duration: 0.5,delay:2});

								if (data.mode == 'register') $('prdno-'+seq).update('Y');
								else if (data.mode == 'register') $('prdno-'+seq).update('Y');


							}
							else {
								osd.removeClassName('status-sending').addClassName('status-error').update( json.body );
								osd.onclick = function() { this.fade({duration: 0.5});};
							}

							if (data.object) data.object.writeAttribute('checked',false);
						}

						// ť�� ���� �ִٸ� ����.
						self._queueProcess();
					},
					onCreate : function(){
						osd.appear({duration: 0.2});
						osd.addClassName('status-sending').update(data.message.start);
					}
				});	// ajax
			}
		}
	 // eof �ֹ�����
		,
	 // Ŭ���Ӱ���
		claim : {
			_form : null,
			_list : null,
			_ordCnRsnCdStr : $H({'06' : '��� ���� ����','07' : '��ǰ/���� ���� �߸� �Է�','08' : '��ǰ ǰ��(��ü�ɼ�)','09' : '�ɼ� ǰ��(�ش�ɼ�)','10' : '������','99' : '��Ÿ'}),
			_ordCnStatCdStr : $H({'01' : '��ҿ�û','02' : '��ҿϷ�'}),
			_affliateBndlDlvSeqStr : $H({0:'���ᱳȯ',1:'���ᱳȯ'}),
			_clmReqRsnStr : $H({'101' : '������','102' : '����ҷ�','103' : '���� �� ��ǰ�Ҹ���','104' : '��ǰ����','105' : '��ǰ��������','106' : '��ǰ�ļ�','107' : '����ź�','108' : '�����','109' : '�ݼ�','110' : '������, ���� ���� �߸� ������','111' : '��۵� ��ǰ�� �ļ�/����/���� �ҷ�','112' : '��ǰ�� �����ϰ� ���� ����','113' : '��Ÿ','119' : '������ ����Ȯ���� ���','201' : '������','202' : '����ҷ�','203' : '���� �� ��ǰ�Ҹ���','204' : '��ǰ����','205' : '��ǰ�ļ�','206' : '������ �Ǵ� ���� ���� �߸� ������','207' : '��۵� ��ǰ�� �ļ�/����/���� �ҷ�','208' : '�ٸ� ��ǰ�� �߸� ��۵�','209' : 'ǰ�� ���� ������ �Ǹ��� ���� �� ��ȯ','210' : '��ǰ�� ��ǰ�� ������ Ʋ��','211' : '��Ÿ','301' : '��۴���','302' : '��ǰ�н�','303' : '����ź�'}),
			_clmStatStr : $H({'101' : '��ǰ����','102' : '��ǰ�����ź�','103' : '����������','104' : '��ǰ����','105' : '��ǰ��û','106' : '��ǰ�Ϸ�','107' : '��ǰ�ź�','108' : '��ǰöȸ','109' : '��ǰ�ϷẸ��','201' : '��ȯ��û','211' : '��ȯ����','212' : '��ȯ�߼ۿϷ�','213' : '��ȯ����','214' : '��ȯ����','221' : '��ȯ�Ϸ�','231' : '��ȯ�����ź�','232' : '��ȯ�ź�','233' : '��ȯöȸ','301' : '��������','302' : '���ۿϷ�'}),
			_layout : {
				GET_CLAIMCANCEL_REQUEST_LIST : {
					head : '\
						<tr>\
							<th style="width:30px;"><input type="checkbox" onclick="chkBoxAll(document.getElementsByName(\'chk[]\'),\'rev\')" class="null"></th>\
							<th style="width:60px;">��ȣ</th>\
							<th style="width:100px;">��ҿ�û��</th>\
							<th>����</th>\
							<th>����</th>\
							<th style="width:80px;">�����ڵ�</th>\
							<th style="width:60px;">����</th>\
							<th style="width:100px;">�ֹ���ȣ</th>\
							<th style="width:60px;">�ܺθ� Ŭ���� ��ȣ</th>\
							<th style="width:60px;">�ֹ�����</th>\
							<th style="width:60px;">��ǰ��ȣ</th>\
							<th style="width:60px;">Ŭ���� �ɼǸ�</th>\
						</tr>'
					,
					row : '\
						<tr>\
							<td class="noline"><input type="checkbox" name="chk[]" value="#{ordNo}" data="{ordNo : \'#{ordNo}\',ordPrdSeq : \'#{ordPrdSeq}\',ordPrdCnSeq : \'#{ordPrdCnSeq}\'}" onclick="iciSelect(this)"></td>\
							<td>#{rowNo}</td>\
							<td>#{createDt}</td>\
							<td class="osd-#{ordNo}">#{ordCnDtlsRsn}</td>\
							<td>#{ordCnQty}</td>\
							<td>#{ordCnRsnCd}</td>\
							<td>#{ordCnStatCd}</td>\
							<td>#{ordNo}</td>\
							<td>#{ordPrdCnSeq}</td>\
							<td>#{ordPrdSeq}</td>\
							<td>#{prdNo}</td>\
							<td>#{slctPrdOptNm}</td>\
					</tr>'
					,
					buttons : 'CLAIMCANCEL'
				}
				,
				GET_CLAIMCANCEL_COMPLETE_LIST : {
					head : '\
						<tr>\
							<th style="width:30px;"><input type="checkbox" onclick="chkBoxAll(document.getElementsByName(\'chk[]\'),\'rev\')" class="null"></th>\
							<th style="width:60px;">��ȣ</th>\
							<th style="width:100px;">��ҿ�û��</th>\
							<th>����</th>\
							<th>����</th>\
							<th style="width:80px;">�����ڵ�</th>\
							<th style="width:60px;">����</th>\
							<th style="width:100px;">�ֹ���ȣ</th>\
							<th style="width:60px;">�ܺθ� Ŭ���� ��ȣ</th>\
							<th style="width:60px;">�ֹ�����</th>\
							<th style="width:60px;">��ǰ��ȣ</th>\
							<th style="width:60px;">Ŭ���� �ɼǸ�</th>\
						</tr>'
					,
					row : '\
						<tr>\
							<td class="noline"><input type="checkbox" name="chk[]" value="#{ordNo}" data="{ordNo : \'#{ordNo}\',ordPrdSeq : \'#{ordPrdSeq}\',addPrdYn : \'#{addPrdYn}\',addPrdNo : \'null\',dlvNo : \'#{dlvNo}\', ordPrdSeq : \'#{ordPrdSeq}\'}" onclick="iciSelect(this)"></td>\
							<td>#{rowNo}</td>\
							<td>#{createDt}</td>\
							<td>#{ordCnDtlsRsn}</td>\
							<td>#{ordCnQty}</td>\
							<td>#{ordCnRsnCd}</td>\
							<td>#{ordCnStatCd}</td>\
							<td>#{ordNo}</td>\
							<td>#{ordPrdCnSeq}</td>\
							<td>#{ordPrdSeq}</td>\
							<td>#{prdNo}</td>\
							<td>#{slctPrdOptNm}</td>\
					</tr>'
				}
				,
				GET_CLAIMRETURN_REQUEST_LIST : {
					head : '\
						<tr>\
							<th style="width:30px;"><input type="checkbox" onclick="chkBoxAll(document.getElementsByName(\'chk[]\'),\'rev\')" class="null"></th>\
							<th style="width:60px;">��ȣ</th>\
							<th style="width:60px;">Ŭ���� ����</th>\
							<th style="width:60px;">�����ǰ ����</th>\
							<th>��ǰ �����ڵ忡 ���� �󼼳���</th>\
							<th style="width:60px;">��ǰ ����</th>\
							<th style="width:60px;">��ǰ �����ڵ�</th>\
							<th style="width:60px;">�ܺθ� Ŭ���� ��ȣ</th>\
							<th style="width:60px;">�ɼǸ�</th>\
							<th style="width:80px;">11���� �ֹ���ȣ</th>\
							<th style="width:60px;">�ֹ�����</th>\
							<th style="width:80px;">��ǰ��ȣ</th>\
							<th style="width:100px;">Ŭ���� ��û �Ͻ�</th>\
						</tr>'
					,//
					row : '\
						<tr>\
							<td class="noline"><input type="checkbox" name="chk[]" value="#{ordNo}" data="{ordNo : \'#{ordNo}\',ordPrdSeq : \'#{ordPrdSeq}\',clmReqSeq : \'#{clmReqSeq}\'}" onclick="iciSelect(this)"></td>\
							<td>#{rowNo}</td>\
							<td>#{clmStat}</td>\
							<td>#{affliateBndlDlvSeq}</td>\
							<td>#{clmReqCont}</td>\
							<td>#{clmReqQty}</td>\
							<td>#{clmReqRsn}</td>\
							<td>#{clmReqSeq}</td>\
							<td>#{optName}</td>\
							<td>#{ordNo}</td>\
							<td>#{ordPrdSeq}</td>\
							<td>#{prdNo}</td>\
							<td>#{reqDt}</td>\
					</tr>'
					,
					buttons : 'CLAIMRETURN'
				}
				,
				GET_CLAIMRETURN_COMPLETE_LIST : {
					head : '\
						<tr>\
							<th style="width:30px;"><input type="checkbox" onclick="chkBoxAll(document.getElementsByName(\'chk[]\'),\'rev\')" class="null"></th>\
							<th style="width:60px;">��ȣ</th>\
							<th style="width:60px;">Ŭ���� ����</th>\
							<th style="width:60px;">�����ǰ ����</th>\
							<th>��ǰ �����ڵ忡 ���� �󼼳���</th>\
							<th style="width:60px;">��ǰ ����</th>\
							<th style="width:60px;">��ǰ �����ڵ�</th>\
							<th style="width:60px;">�ܺθ� Ŭ���� ��ȣ</th>\
							<th style="width:60px;">�ɼǸ�</th>\
							<th style="width:80px;">11���� �ֹ���ȣ</th>\
							<th style="width:60px;">�ֹ�����</th>\
							<th style="width:80px;">��ǰ��ȣ</th>\
							<th style="width:100px;">Ŭ���� ��û �Ͻ�</th>\
						</tr>'
					,
					row : '\
						<tr>\
							<td class="noline"><input type="checkbox" name="chk[]" value="#{ordNo}" data="{ordNo : \'#{ordNo}\',ordPrdSeq : \'#{ordPrdSeq}\',addPrdYn : \'#{addPrdYn}\',addPrdNo : \'null\',dlvNo : \'#{dlvNo}\', ordPrdSeq : \'#{ordPrdSeq}\'}" onclick="iciSelect(this)"></td>\
							<td>#{rowNo}</td>\
							<td>#{affliateBndlDlvSeq}</td>\
							<td>#{clmReqCont}</td>\
							<td>#{clmReqQty}</td>\
							<td>#{clmReqRsn}</td>\
							<td>#{clmReqSeq}</td>\
							<td>#{clmStat}</td>\
							<td>#{optName}</td>\
							<td>#{ordNo}</td>\
							<td>#{ordPrdSeq}</td>\
							<td>#{prdNo}</td>\
							<td>#{reqDt}</td>\
					</tr>'

				}
				,
				GET_CLAIMRETURN_CANCEL_LIST : {
					head : '\
						<tr>\
							<th style="width:30px;"><input type="checkbox" onclick="chkBoxAll(document.getElementsByName(\'chk[]\'),\'rev\')" class="null"></th>\
							<th style="width:60px;">��ȣ</th>\
							<th style="width:60px;">Ŭ���� ����</th>\
							<th style="width:60px;">�����ǰ ����</th>\
							<th>��ǰ �����ڵ忡 ���� �󼼳���</th>\
							<th style="width:60px;">��ǰ ����</th>\
							<th style="width:60px;">��ǰ �����ڵ�</th>\
							<th style="width:60px;">�ܺθ� Ŭ���� ��ȣ</th>\
							<th style="width:60px;">�ɼǸ�</th>\
							<th style="width:80px;">11���� �ֹ���ȣ</th>\
							<th style="width:60px;">�ֹ�����</th>\
							<th style="width:80px;">��ǰ��ȣ</th>\
							<th style="width:100px;">Ŭ���� ��û �Ͻ�</th>\
						</tr>'
					,
					row : '\
						<tr>\
							<td class="noline"><input type="checkbox" name="chk[]" value="#{ordNo}" data="{ordNo : \'#{ordNo}\',ordPrdSeq : \'#{ordPrdSeq}\',addPrdYn : \'#{addPrdYn}\',addPrdNo : \'null\',dlvNo : \'#{dlvNo}\', ordPrdSeq : \'#{ordPrdSeq}\'}" onclick="iciSelect(this)"></td>\
							<td>#{rowNo}</td>\
							<td>#{affliateBndlDlvSeq}</td>\
							<td>#{clmReqCont}</td>\
							<td>#{clmReqQty}</td>\
							<td>#{clmReqRsn}</td>\
							<td>#{clmReqSeq}</td>\
							<td>#{clmStat}</td>\
							<td>#{optName}</td>\
							<td>#{ordNo}</td>\
							<td>#{ordPrdSeq}</td>\
							<td>#{prdNo}</td>\
							<td>#{reqDt}</td>\
					</tr>'
				}
				,
				GET_CLAIMEXCHANGE_REQUEST_LIST : {
					head : '\
						<tr>\
							<th style="width:30px;"><input type="checkbox" onclick="chkBoxAll(document.getElementsByName(\'chk[]\'),\'rev\')" class="null"></th>\
							<th style="width:60px;">��ȣ</th>\
							<th style="width:60px;">Ŭ���� ����</th>\
							<th style="width:60px;">���ᱳȯ ����</th>\
							<th>��ȯ �����ڵ忡 ���� �󼼳���</th>\
							<th style="width:60px;">��ȯ ����</th>\
							<th style="width:60px;">��ȯ �����ڵ�</th>\
							<th style="width:60px;">�ܺθ� Ŭ���� ��ȣ</th>\
							<th style="width:60px;">�ɼǸ�</th>\
							<th style="width:80px;">11���� �ֹ���ȣ</th>\
							<th style="width:60px;">�ֹ�����</th>\
							<th style="width:80px;">��ǰ��ȣ</th>\
							<th style="width:100px;">Ŭ���� ��û �Ͻ�</th>\
						</tr>'
					,
					row : '\
						<tr>\
							<td class="noline"><input type="checkbox" name="chk[]" value="#{ordNo}" data="{ordNo : \'#{ordNo}\',ordPrdSeq : \'#{ordPrdSeq}\',clmReqSeq : \'#{clmReqSeq}\'}" onclick="iciSelect(this)"></td>\
							<td>#{rowNo}</td>\
							<td>#{affliateBndlDlvSeq}</td>\
							<td>#{clmReqCont}</td>\
							<td>#{clmReqQty}</td>\
							<td>#{clmReqRsn}</td>\
							<td>#{clmReqSeq}</td>\
							<td>#{clmStat}</td>\
							<td>#{optName}</td>\
							<td>#{ordNo}</td>\
							<td>#{ordPrdSeq}</td>\
							<td>#{prdNo}</td>\
							<td>#{reqDt}</td>\
					</tr>'
					,
					buttons : 'CLAIMEXCHANGE'
				}
				,
				GET_CLAIMEXCHANGE_COMPLETE_LIST : {
					head : '\
						<tr>\
							<th style="width:30px;"><input type="checkbox" onclick="chkBoxAll(document.getElementsByName(\'chk[]\'),\'rev\')" class="null"></th>\
							<th style="width:60px;">��ȣ</th>\
							<th style="width:60px;">Ŭ���� ����</th>\
							<th style="width:60px;">���ᱳȯ ����</th>\
							<th>��ȯ �����ڵ忡 ���� �󼼳���</th>\
							<th style="width:60px;">��ȯ ����</th>\
							<th style="width:60px;">��ȯ �����ڵ�</th>\
							<th style="width:60px;">�ܺθ� Ŭ���� ��ȣ</th>\
							<th style="width:60px;">�ɼǸ�</th>\
							<th style="width:80px;">11���� �ֹ���ȣ</th>\
							<th style="width:60px;">�ֹ�����</th>\
							<th style="width:80px;">��ǰ��ȣ</th>\
							<th style="width:100px;">Ŭ���� ��û �Ͻ�</th>\
						</tr>'
					,
					row : '\
						<tr>\
							<td class="noline"><input type="checkbox" name="chk[]" value="#{ordNo}" data="{ordNo : \'#{ordNo}\',ordPrdSeq : \'#{ordPrdSeq}\',addPrdYn : \'#{addPrdYn}\',addPrdNo : \'null\',dlvNo : \'#{dlvNo}\', ordPrdSeq : \'#{ordPrdSeq}\'}" onclick="iciSelect(this)"></td>\
							<td>#{rowNo}</td>\
							<td>#{affliateBndlDlvSeq}</td>\
							<td>#{clmReqCont}</td>\
							<td>#{clmReqQty}</td>\
							<td>#{clmReqRsn}</td>\
							<td>#{clmReqSeq}</td>\
							<td>#{clmStat}</td>\
							<td>#{optName}</td>\
							<td>#{ordNo}</td>\
							<td>#{ordPrdSeq}</td>\
							<td>#{prdNo}</td>\
							<td>#{reqDt}</td>\
					</tr>'
				}
				,
				GET_CLAIMEXCHANGE_CANCEL_LIST : {
					head : '\
						<tr>\
							<th style="width:30px;"><input type="checkbox" onclick="chkBoxAll(document.getElementsByName(\'chk[]\'),\'rev\')" class="null"></th>\
							<th style="width:60px;">��ȣ</th>\
							<th style="width:60px;">Ŭ���� ����</th>\
							<th style="width:60px;">���ᱳȯ ����</th>\
							<th>��ȯ �����ڵ忡 ���� �󼼳���</th>\
							<th style="width:60px;">��ȯ ����</th>\
							<th style="width:60px;">��ȯ �����ڵ�</th>\
							<th style="width:60px;">�ܺθ� Ŭ���� ��ȣ</th>\
							<th style="width:60px;">�ɼǸ�</th>\
							<th style="width:80px;">11���� �ֹ���ȣ</th>\
							<th style="width:60px;">�ֹ�����</th>\
							<th style="width:80px;">��ǰ��ȣ</th>\
							<th style="width:100px;">Ŭ���� ��û �Ͻ�</th>\
						</tr>'
					,
					row : '\
						<tr>\
							<td class="noline"><input type="checkbox" name="chk[]" value="#{ordNo}" data="{ordNo : \'#{ordNo}\',ordPrdSeq : \'#{ordPrdSeq}\',addPrdYn : \'#{addPrdYn}\',addPrdNo : \'null\',dlvNo : \'#{dlvNo}\', ordPrdSeq : \'#{ordPrdSeq}\'}" onclick="iciSelect(this)"></td>\
							<td>#{rowNo}</td>\
							<td>#{affliateBndlDlvSeq}</td>\
							<td>#{clmReqCont}</td>\
							<td>#{clmReqQty}</td>\
							<td>#{clmReqRsn}</td>\
							<td>#{clmReqSeq}</td>\
							<td>#{clmStat}</td>\
							<td>#{optName}</td>\
							<td>#{ordNo}</td>\
							<td>#{ordPrdSeq}</td>\
							<td>#{prdNo}</td>\
							<td>#{reqDt}</td>\
					</tr>'
				}
			},
			_queue: [],
			_queueProcess : function() {
				if (this._queue.length > 0) this._send( this._queue.shift() );
			},
			init : function() {

				var self = this;

				self._list = $('oClaimList');

				self._form = $('frmListOption');
				self._form.onsubmit = self.reload.bindAsEventListener(this);
				self._form.observe("shople:submit", function(event) {

					if (self._form.page == undefined) {
						self._form.insert({
							top : '<input type="hidden" name="page" value="" />'
						});
					}
					self._form.page.value = (event.memo != undefined) ? event.memo.page : 3;
					self.reload();
				});
				self.load();

			}
			,
			_remove : function() {

				var self = this;

				try {
					Element.remove(self._list.down('thead').rows[0]);
				}
				catch (e) { }

				$A(self._list.down('tbody').rows).each(function(tr){
					Element.remove(tr);
				});

				$$('div.button-group').each(function(it){
					it.hide();
				});
			}
			,
			reload : function() {

				var self = this;

				self._remove();
				self.load();

				return false;
			}
			,
			load : function() {

				var self = this;

				var ajax = new Ajax.Request('../shople/ax.indb.claim.php', {
					method: "post",
					parameters: '&mode=list&'+self._form.serialize(),
					asynchronous: true,
					onComplete: function(response) { if (response.status == 200) {

						self._list.down('tbody').down('tr').remove();

						var json = response.responseText.evalJSON(true);

						if (json.result == true) {

							// ������ ������
								g_jsonData = json.body;

								var i,row,html,len, no;
								var _tpl_row	= new Template( self._layout[ self._form['method'].value ].row );
								var _tpl_head	= new Template( self._layout[ self._form['method'].value ].head );
								var _tpl_buttons	= self._layout[ self._form['method'].value ].buttons;

							// thead
								self._list.down('thead').insert({ bottom: _tpl_head.evaluate({}) });

							// tbody
								len = g_jsonData.length;

								for (i=0; i<len ;i++) {
									row = g_jsonData[i];

									row.ordPayAmt = comma(row.ordPayAmt);

									//row.ordCnRsnCd = self._ordCnRsnCdStr.get( row.ordCnRsnCd );
									row.ordCnStatCd = self._ordCnStatCdStr.get( row.ordCnStatCd );

									row.clmReqRsn = self._clmReqRsnStr.get( row.clmReqRsn );
									row.clmStat = self._clmStatStr.get( row.clmStat );
									row.affliateBndlDlvSeq = self._affliateBndlDlvSeqStr.get( row.affliateBndlDlvSeq );

									self._list.down('tbody').insert({ bottom: _tpl_row.evaluate(row) });
								}

							// paging draw.
								var pg = getPaging(json.page,json.pages);
								$('pageNavi').update(pg);

							// ��ư
								if (_tpl_buttons != undefined && _tpl_buttons != '') {

									$$('div.button-group').each(function(it){
										if (it.hasClassName( _tpl_buttons ))
											it.show();
										else
											it.hide();
									});

								}

							// grid ��������
								//nsGodogrid.refresh();

						}
						else {
							self._list.down('tbody').insert({ bottom: '<tr><td colspan="20" class="nodata">��ȸ����� �������� �ʽ��ϴ�.</td></tr>' });
							//self._list.down('tbody').insert({ bottom: '<tr><td colspan="20" class="nodata">' + json.body.strip() + '</td></tr>' });
						}

					}},
					onCreate : function(){
						self._list.down('tbody').insert({ bottom: '<tr><td colspan="20" class="loading"><img src="../img/loading.gif"></td></tr>' });
					}
				});

			}
			,
			download : function() {

				var self = this;
				var inputs = '<input type="hidden" name="mode" value="download" />';

				$A(self._form.serialize().split('&')).each(function(it){
					var pair = it.split('=');
					inputs+='<input type="hidden" name="'+ pair[0] +'" value="'+ pair[1] +'" />';

				});
				var f = new Element('form', {'method': 'post','action' : '../shople/ax.indb.claim.php','target':'ifrmHidden'}).update(inputs);
				document.body.appendChild(f);
				f.submit();
			}
			,
			cancel : {
				getReason : function(m) {

					if ($$('input[name="chk[]"]:checked').length < 1) {
						alert('ó�� ����� ������ �ּ���.');
						return;
					}
					else {
						var _url = '../shople/popup.claim.cancel.php?m='+m;
						popup(_url,600,420,0);
					}
				}
				,
				accept : function() {
					var parent = nsShople.claim;

					var data = {};

					$$('input[name="chk[]"]:checked').each(function(item){
						data = {
							object : item,
							mode : 'cancelaccept',
							param : Object.toQueryString(  item.readAttribute('data').evalJSON()  ) ,
							message : {
								start : '�ֹ� ��� ���� ��û���Դϴ�.',
								end : '�ֹ� ��� ���� �Ǿ����ϴ�.'
							}
						}
						parent._queue.push(data);
					});

					parent._queueProcess();

				}
				,
				reject : function(reason /* json */) {

					var parent = nsShople.claim;

					if ($$('input[name="chk[]"]:checked').length < 1) {
						alert('ó�� ����� ������ �ּ���.');
						return;
					}
					else if ($$('input[name="chk[]"]:checked').length > 1)
					{
						alert('��� �ź� ó���� ���� ������ �Ұ����մϴ�.');
						return;
					}
					else {

						$$('input[name="chk[]"]:checked').each(function(item){

							var row = item.readAttribute('data').evalJSON();
							row.sendDt		= reason.sendDt;
							row.dlvEtprsCd	= reason.dlvEtprsCd;
							row.dlvMthdCd	= reason.dlvMthdCd;
							row.invcNo		= reason.invcNo;

							data = {
								object : item,
								mode : 'cancelreject',
								param : Object.toQueryString(  row  ) ,
								message : {
									start : '��� �ź� ���� ��û���Դϴ�.',
									end : '��� �ź� �Ǿ����ϴ�.'
								}
							}
							parent._queue.push(data);
						});

						parent._queueProcess();

					}
				}

			}
			,
			return_ : {
				accept : function() {
					var parent = nsShople.claim;

					var data = {};

					$$('input[name="chk[]"]:checked').each(function(item){
						data = {
							object : item,
							mode : 'returnaccept',
							param : Object.toQueryString(  item.readAttribute('data').evalJSON()  ) ,
							message : {
								start : '��ǰ ���� ��û���Դϴ�.',
								end : '��ǰ ���� �Ǿ����ϴ�.'
							}
						}
						parent._queue.push(data);
					});

					parent._queueProcess();
				}
				,
				getReason : function(m) {

					if ($$('input[name="chk[]"]:checked').length < 1) {
						alert('ó�� ����� ������ �ּ���.');
						return;
					}
					else {
						var _url = '../shople/popup.claim.return.php?m='+m;
						popup(_url,600,420,0);
					}
				}
				,
				reject : function(reason /* json */) {

					var parent = nsShople.claim;

					var data = {};

					$$('input[name="chk[]"]:checked').each(function(item){
						var row = item.readAttribute('data').evalJSON();
						row.reasonCD		= reason.reasonCD;
						row.reasonCont	= reason.reasonCont;

						data = {
							object : item,
							mode : 'returnreject',
							param : Object.toQueryString(  row  ) ,
							message : {
								start : '��ǰ ���� �ź� ��û���Դϴ�.',
								end : '��ǰ ���� �ź� �Ǿ����ϴ�.'
							}
						}
						parent._queue.push(data);
					});

					parent._queueProcess();
				}

				,
				hold : function(reason /* json */) {

					var parent = nsShople.claim;

					var data = {};

					$$('input[name="chk[]"]:checked').each(function(item){

						var row = item.readAttribute('data').evalJSON();
						row.reasonCD		= reason.reasonCD;
						row.reasonCont	= reason.reasonCont;

						data = {
							object : item,
							mode : 'returnhold',
							param : Object.toQueryString( row ) ,
							message : {
								start : '��ǰ ���� �ź� ��û���Դϴ�.',
								end : '��ǰ ���� �ź� �Ǿ����ϴ�.'
							}
						}
						parent._queue.push(data);
					});

					parent._queueProcess();
				}

				,
				accepthold : function(reason /* json */) {
					var parent = nsShople.claim;

					var data = {};

					$$('input[name="chk[]"]:checked').each(function(item){

						var row = item.readAttribute('data').evalJSON();
						row.reasonCD	= reason.reasonCD;
						row.reasonCont	= reason.reasonCont;

						data = {
							object : item,
							mode : 'returnaccepthold',
							param : Object.toQueryString(  row  ) ,
							message : {
								start : '��ǰ �Ϸ� ���� ��û���Դϴ�.',
								end : '��ǰ �Ϸ� ���� �Ǿ����ϴ�.'
							}
						}
						parent._queue.push(data);
					});

					parent._queueProcess();
				}

			}
			,
			exchange : {
				getReason : function(m) {

					if ($$('input[name="chk[]"]:checked').length < 1) {
						alert('ó�� ����� ������ �ּ���.');
						return;
					}
					else {
						var _url = '../shople/popup.claim.exchange.php?m='+m;
						popup(_url,600,420,0);
					}
				}
				,
				accept : function(reason /* json */) {
					var parent = nsShople.claim;

					var data = {};

					if ($$('input[name="chk[]"]:checked').length < 1) {
						alert('ó�� ����� ������ �ּ���.');
						return;
					}
					else if ($$('input[name="chk[]"]:checked').length > 1)
					{
						alert('��ȯ ���� ó���� ���� ó���� �Ұ����մϴ�.');
						return;
					}
					else {

						$$('input[name="chk[]"]:checked').each(function(item){

							var row = item.readAttribute('data').evalJSON();
							row.dlvEtprsCd	= reason.dlvEtprsCd;
							row.invcNo		= reason.invcNo;

							data = {
								object : item,
								mode : 'exchangeaccept',
								param : Object.toQueryString(  row  ) ,
								message : {
									start : '��ȯ ���� ��û���Դϴ�.',
									end : '��ȯ ���� �Ǿ����ϴ�.'
								}
							}
							parent._queue.push(data);
						});
					}

					parent._queueProcess();
				}
				,
				reject : function() {
					var parent = nsShople.claim;

					var data = {};

					$$('input[name="chk[]"]:checked').each(function(item){
						data = {
							object : item,
							mode : 'exchangereject',
							param : Object.toQueryString(  item.readAttribute('data').evalJSON()  ) ,
							message : {
								start : '��ȯ �ź� ��û���Դϴ�.',
								end : '��ȯ �ź� �Ǿ����ϴ�.'
							}
						}
						parent._queue.push(data);
					});

					parent._queueProcess();
				}
			}
			,
			_send : function(data) {

				var self = this;
				var seq;

				seq = (data.seq != null) ? data.seq : data.object.getValue();

				var ex_param = (data.param) ? '&' + data.param : '';

				if (data.object == undefined) $$('input[name="chk[]"]').each(function(item) { if (item.value == seq) data.object = item; });

				var osd = nsShople.osd.make( data.object );

				var ajax = new Ajax.Request('../shople/ax.indb.claim.php', {
					method: "post",
					parameters: 'mode='+data.mode+'&seq='+seq + ex_param,
					//parameters: 'mode=test&seq='+seq + ex_param,
					asynchronous: true,
					onComplete: function(response) {

						if (response.status == 200) {

							var json = response.responseText.evalJSON(true);

							if (json.result == true) {
								osd.removeClassName('status-sending').addClassName('status-success').update( data.message.end );
								osd.fade({duration: 0.5,delay:2});
							}
							else {
								osd.removeClassName('status-sending').addClassName('status-error').update( json.body );
								osd.onclick = function() { this.fade({duration: 0.5});};
							}

							if (data.object) data.object.writeAttribute('checked',false);

						}

						// ť�� ���� �ִٸ� ����.
						self._queueProcess();
					},
					onCreate : function(){
						osd.addClassName('status-sending').update(data.message.start);
					}
				});
			}
		}
	 // eof Ŭ���Ӱ���
	}

} ();


function chkLen(obj, len, id)
{
	str = obj.value;
	if (str.length > len){
		alert(len +"�ڱ����� �Է��� �����մϴ�");
		obj.value = str.substring(0, len);
	}
	_ID(id).innerHTML = obj.value.length;
}