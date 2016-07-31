var DesignHelper = function() {
	return {
		status : null, // 상태
		html_of : null, // html overflow
		$body : null,
		$dh_debug_window : null,
		$dh_debug_toolbar : null,
		$dh_debug_content_wrap : null,
		$dh_debug_content : null,
		$dh_debug_title : null,
		$dh_body_mb_box : null,
		$d_t_srcname : null,
		$dh_partsection : null,
		$dh_msg_box : null,

		$dh_partsection_wrap : null,
		$dh_captured_top : null, // 선택중인 영역 표시 div
		$dh_captured_left : null, // 선택중인 영역 표시 div
		$dh_captured_right : null, // 선택중인 영역 표시 div
		$dh_captured_bottom : null, // 선택중인 영역 표시 div

		$dh_focused_top : null, // 선택된 영역 표시 div
		$dh_focused_left : null, // 선택된 영역 표시 div
		$dh_focused_right : null, // 선택된 영역 표시 div
		$dh_focused_bottom : null, // 선택된 영역 표시 div
		_d_p_captured_bw : null, // captured border_width()

		max_zidx : 100, // 생성된 part 중 z-index 제일 큰 값. (dh_debug_window의 z-index)

		captured_parts : new Array(),
		captured_part_idx : null,

		xhr : null, // ajax object
		srcinfo : {}, // element별 소스정보(src:스킨파일명, realsrc:실제스킨파일명(미리보기), mtpl : main_tpl 파일(fid중복방지, include때문에 필요), min:시작라인, max:마지막라인) 저장.
		fidinfo : {}, // key : fid, value : 스킨파일명
		use : false, // 사용여부
		itv : null,

		// debug창 생성.
		create_debug_window : function() {
			var _dh = this;
			var html = "<div id='_dh_debug_window' style='position:fixed; left:0; bottom:0; _position:absolute; _top:expression(document.body.scrollTop+document.body.clientHeight-this.clientHeight); _left:expression(document.body.scrollLeft+document.body.clientWidth-this.clientWidth); width:100%;'><div style='height:41px; text-align:right;'><div id='_dh_msg_box' style='position:absolute; display:none; border:solid 2px #222222; background-color:#FF0000; color:#FFFFFF; padding:2px 5px; border-radius:5px;'></div><div style='height:27px; text-align:center;'><span id='_dh_debug_toolbar' style='cursor:pointer;'><img src='/shop/admin/img/codi/btn_dh_sourceview.png' /></span></div><div style='height:14px; background-color:#A9A9A9;'></div></div><div id='_dh_debug_title' style='display:none; height:24px; text-align:left; border-bottom:solid 1px; border-left:solid 14px #A9A9A9; border-right:solid 14px #A9A9A9; background-color:#FFFFFF;'><span id='_d_t_srcname' style='margin-left:10px; line-height:24px;'></span></div><div id='_dh_debug_content_wrap' style='display:none; background-color:#FFFFFF; height:175px; border-left:solid 14px #A9A9A9; border-right:solid 14px #A9A9A9;'><div style='overflow-x:scroll;overflow-y:scroll; width:100%; height:175px;'><div id='_dh_debug_content' style='margin:5px; font-size:12px; color:#000000;'></div></div></div></div><div id='_dh_body_mb_box' style='clear:both;'></div>";

			_dh.$body.append(html);
			_dh.$dh_debug_window = jQuery("#_dh_debug_window");
			_dh.$dh_debug_toolbar= jQuery("#_dh_debug_toolbar");
			_dh.$dh_debug_title = jQuery("#_dh_debug_title");
			_dh.$dh_debug_content_wrap = jQuery("#_dh_debug_content_wrap");
			_dh.$dh_debug_content = jQuery("#_dh_debug_content");
			_dh.$d_t_srcname = jQuery("#_d_t_srcname");
			_dh.$dh_body_mb_box = jQuery("#_dh_body_mb_box");
			_dh.$dh_msg_box = jQuery("#_dh_msg_box");

			// 버튼 설정
			if (_dh.use) {
				_dh.$dh_debug_toolbar.click(function() {
					if (_dh.status == "enabled") _dh.disable();
					else _dh.enable();
				});
			}
			else {
				_dh.$dh_debug_toolbar.click(function() {
					location.replace(location.href+"&gd_srcview=1");
				});
			}
		},
		init_html : function() {
			var _dh = this;

			var $els = jQuery("*:not(html, script, meta, title, link, head, body, style, col, colgroup, tr, #_dh_debug_window, #_dh_debug_window *)");
			var els_length = $els.length;
			var gd_dhseq = -1; // element별 적용될 seq에 들어가는 인덱스

			// 모든 element에 seq 부여.
			for(var n = 0; n < els_length; ++n) {
				var $self = jQuery($els[n]);
				if (_dh.use) {
					var time = new Date().getTime();
					$self.attr("gd_dhseq", "dhseq_"+time+"_"+(++gd_dhseq));
				}

				var zidx = Number($self.css("z-index"));
				if (zidx && !isNaN(zidx) && zidx > _dh.max_zidx) _dh.max_zidx = zidx;
			}

			if (_dh.use) {
				// 현재 html을 기준으로 element의 part구분.
				// element의 라인정보을 가져오고 위해 상위 element의 innerHTML을 가져와서
				// element의 dhseq(고유값)으로 split한후 라인정보을 정규식으로 반환 후, 마지막 라인정보를 클릭된 element의 라인정보로 규정.
				// (element의 시작 라인정보는 element 태그 밖에 있기 때문)
				// element의 innerHTML에 포함된 라인정보를 더하면 해당 element의 라인정보.
				_dh.$dh_partsection = jQuery("[gd_dhseq]");

				//for(var n = 0, nl = _dh.$dh_partsection.length; n < nl; ++n) {
				_dh.$dh_partsection.each(function() {
					var $self = jQuery(this);
					var self_dhseq = $self.attr("gd_dhseq");

					// 해당 element를 기준으로 위에 있는 소스 반환.
					var $parent = $self;
					var p_matches = null;
					do {
						$parent = $parent.parent();
						var p_html = String($parent.html());
						p_matches = p_html.split("gd_dhseq=\""+self_dhseq+"\"")[0].match(/<!--\s*gdline\s*.*?-->/gi);
					} while(!p_matches && $parent.length > 0);
					if ($parent.length == 0) return;

					var matches = $self.html().match(/<!--\s*gdline\s*.*?-->/gi);
					if (matches) matches.splice(0, 0, p_matches[p_matches.length-1]);
					else matches = new Array(p_matches[p_matches.length-1]);

					// 해당영역내에서 열리고 닫힌(part open/close)된 파일의 라인정보는 제거.
					var part_info = $self.html().match(/<!--\s*gdpart\s*.*?-->/gi);
					if (part_info) {
						var part_mode_regexp = new RegExp("<!--\s*gdpart\s*mode=\"\"\s*.*?-->", "gi");
						var part_fid_regexp = new RegExp("\s*fid=\"([^\"]*)\"\s*", "gi");
						for(var k = 0; k < part_info.length; ++k) {
							var mode = part_info[k].indexOf("mode=\"open\"") == -1 ? "close" : "open";
							var fid = part_info[k].match(part_fid_regexp)[0].replace(/^fid=\"([^\"]*)\"$/gi, "$1");
							if (mode == "open") {
								for(var l = k+1; l < part_info.length; ++l) {
									var mode2 = part_info[l].indexOf("mode=\"open\"") == -1 ? "close" : "open";
									var fid2 = part_info[l].match(part_fid_regexp)[0].replace(/^fid=\"([^\"]*)\"$/gi, "$1");
									if (mode2 == "close" && fid == fid2) {
										part_info.splice(l, 1);
										for(var m = 0; m < matches.length; ++m) {
											var src = matches[m].replace(/<!--\s*gdline\s*[0-9]*\"([^"]*)\".*?-->/gi, "$1").split('|');
											if (src[2] == fid) {
												matches.splice(m, 1);
												m--;
											}
										}
										break;
									}
								}
							}
						}
					}

					// line정보에 의한 영역설정
					var src_info = new Array();
					var partsrc = null;
					var before_partsrc = null;
					var idx = -1;
					for(var k = 0; k < matches.length; ++k) {
						// 라인정보(src:스킨파일명, realsrc:실제스킨파일명(미리보기), mtpl : main_tpl 파일(fid중복방지, include때문에 필요), min:시작라인, max:마지막라인) 저장.
						var line = matches[k].replace(/<!--\s*gdline\s*([0-9]*)\"[^"]*\"\s*?-->/gi, "$1");
						var src = matches[k].replace(/<!--\s*gdline\s*[0-9]*\"([^"]*)\"\s*-->/gi, "$1").split('|');
						var fid = src[2].split(" ");

						if (partsrc == null) partsrc = src[0];
						if (before_partsrc != src[0]) {
							before_partsrc = src[0];
							src_info[++idx] = { src : src[0], realsrc : src[1], mtpl : fid[0], min : null, max : null };
						}
						src_info[idx].min = (src_info[idx].min && src_info[idx].min < line)? src_info[idx].min : parseInt(line, 10);
						src_info[idx].max = (src_info[idx].max && src_info[idx].max > line)? src_info[idx].max : parseInt(line, 10);

						if (!_dh.fidinfo[fid[0]]) _dh.fidinfo[fid[0]] = {};
						fid[1] = fid[1].replace(/_[0-9]*$/g, "");
						if (!_dh.fidinfo[fid[0]][fid[1]]) _dh.fidinfo[fid[0]][fid[1]] = { path : src[0], realpath : src[1].substring(1) };
					}

					_dh.srcinfo[self_dhseq] = src_info;
					$self.attr("partsrc", partsrc);
				});

				// body에 part wrap div 생성.
				_dh.$dh_partsection_wrap = jQuery("<div id='_dh_partsection_wrap' class='_dh_partsection_wrap'></div>");
				_dh.$body.append(_dh.$dh_partsection_wrap);

				// 영역분리에 사용된 태그삭제.
				jQuery("*").each(function() {
					try {
						jQuery(this).contents().each(function() {
							if (this.nodeType == 8) jQuery(this).remove();
						});
					}
					catch(e) {
						var $self = jQuery(this);
						var gd_dhseq = $self.attr("gd_dhseq");
						var $access_denied_div = jQuery("<div style='position:absolute; width:"+$self.outerWidth()+"; height:"+$self.outerHeight()+"; background-color:#FFFFFF;'></div>");
						if (gd_dhseq) {
							$access_denied_div.attr("gd_dhseq", gd_dhseq);
							$self.removeAttr("gd_dhseq");
						}
						_dh.$dh_partsection_wrap.append($access_denied_div);
						var pos = _dh.get_object_pos(this);
						$access_denied_div.offset(pos);
						_dh.set_opacity($access_denied_div, 0.7);
					}
				});
				_dh.$dh_partsection = jQuery("[gd_dhseq]");
			}

			_dh.max_zidx += 10;
		},
		// 각 파트별 영역 생성
		create_part : function() {
			var _dh = this;

			_dh.$dh_captured_top = jQuery("<div class='_dh_captured' style='z-index:"+(_dh.max_zidx-1)+"; position:absolute; display:none; border-top:solid "+_dh._d_p_captured_bw+"px #FF0000; height:1px; line-height:1px;'></div>");
			_dh.$dh_captured_left = jQuery("<div class='_dh_captured' style='z-index:"+(_dh.max_zidx-1)+"; position:absolute; display:none; border-left:solid "+_dh._d_p_captured_bw+"px #FF0000; line-height:1px;'></div>");
			_dh.$dh_captured_right = jQuery("<div class='_dh_captured' style='z-index:"+(_dh.max_zidx-1)+"; position:absolute; display:none; border-right:solid "+_dh._d_p_captured_bw+"px #FF0000; line-height:1px;'></div>");
			_dh.$dh_captured_bottom = jQuery("<div class='_dh_captured' style='z-index:"+(_dh.max_zidx-1)+"; position:absolute; display:none; border-bottom:solid "+_dh._d_p_captured_bw+"px #FF0000; height:1px; line-height:1px;'></div>");

			_dh.$dh_focused_top = jQuery("<div style='z-index:"+(_dh.max_zidx-2)+"; position:absolute; display:none; border-top:solid "+_dh._d_p_captured_bw+"px #0000FF; height:1px; line-height:1px;'></div>");
			_dh.$dh_focused_left = jQuery("<div style='z-index:"+(_dh.max_zidx-2)+"; position:absolute; display:none; border-left:solid "+_dh._d_p_captured_bw+"px #0000FF; line-height:1px;'></div>");
			_dh.$dh_focused_right = jQuery("<div style='z-index:"+(_dh.max_zidx-2)+"; position:absolute; display:none; border-right:solid "+_dh._d_p_captured_bw+"px #0000FF; line-height:1px;'></div>");
			_dh.$dh_focused_bottom = jQuery("<div style='z-index:"+(_dh.max_zidx-2)+"; position:absolute; display:none; border-bottom:solid "+_dh._d_p_captured_bw+"px #0000FF; height:1px; line-height:1px;'></div>");

			_dh.$dh_partsection_wrap
				.append(_dh.$dh_captured_top).append(_dh.$dh_captured_left).append(_dh.$dh_captured_right).append(_dh.$dh_captured_bottom)
				.append(_dh.$dh_focused_top).append(_dh.$dh_focused_left).append(_dh.$dh_focused_right).append(_dh.$dh_focused_bottom)
				.find(">div").each(function() {
					jQuery(this).offset({top:0, left:0});
				}).end().show();


			_dh.$dh_captured_top.bind("mouseout", _dh.part_mouseout).bind("mouseup", _dh.part_mouseup).bind("mousedown", _dh.part_mousedown);
			_dh.$dh_captured_left.bind("mouseout", _dh.part_mouseout).bind("mouseup", _dh.part_mouseup).bind("mousedown", _dh.part_mousedown);
			_dh.$dh_captured_right.bind("mouseout", _dh.part_mouseout).bind("mouseup", _dh.part_mouseup).bind("mousedown", _dh.part_mousedown);
			_dh.$dh_captured_bottom.bind("mouseout", _dh.part_mouseout).bind("mouseup", _dh.part_mouseup).bind("mousedown", _dh.part_mousedown);

			_dh.$dh_debug_window.mouseover(function() {
				_dh.$dh_partsection_wrap.find("._dh_captured").hide().removeAttr("gd_dhseq");
			});

			_dh.max_zidx += 10;
		},
		// 초기화
		init : function(use) {
			this.use = use;
			this.$body = jQuery("body");
			jQuery("html body").css("width", "100%");
			jQuery("html").css("overflow", "scroll");
			this._d_p_captured_bw = 2;
			if (window.XMLHttpRequest) {
				this.xhr = new XMLHttpRequest();
			}
			else {
				this.xhr = new ActiveXObject("Microsoft.XMLHTTP");
			}
			this.init_html();
		},
		start : function() {
			this.create_debug_window();
			this.disable();
		},
		// 사용
		enable : function() {
			var _dh = this;

			// 비활성화.
			_dh.disable();

			// debug 창 설정.
			_dh.$dh_debug_toolbar.attr("disabled", true).find("img").attr("src", "/shop/admin/img/codi/btn_dh_sourceclose.png");  // 버튼 비활성화
			_dh.$dh_debug_title.show();
			_dh.$dh_debug_content_wrap.show();
			//_dh.$dh_msg_box.show();

			// 하단 툴바 크키만큼 띄우기
			_dh.$dh_body_mb_box.css("height", _dh.$dh_debug_window.height());

			if (!_dh.$dh_captured_top) _dh.create_part();
			else {
				_dh.$dh_partsection_wrap.show();
				_dh.redraw();
			}

			_dh.$dh_debug_window.css({
				"bottom" : 0,
				"z-index" : _dh.max_zidx+100
			});

			if (_dh.$dh_partsection) {
				_dh.$dh_partsection.each(function() {
					var $self = jQuery(this);
					// element에 event 바인딩.
					$self.bind("mouseover", _dh.part_mouseover).bind("mouseout", _dh.part_mouseout).bind("mousedown", _dh.part_mousedown).bind("mouseup", _dh.part_mouseup).bind("click", _dh.part_click).bind("focus", _dh.part_focus);
					try {
						if ($self.get(0).onclick) {
							$self.bind("click", $self.get(0).onclick);
							$self.get(0).onclick = function() { return false; };
						}
					}
					catch(e) { }
				});
			}

			// .5초마다/리사이즈,스크롤시 선택영역 다시 그림.(영역을 다시 잡아야함)
			if (_dh.itv) {
				clearInterval(_dh.itv);
				_dh.itv = null;
			}
			_dh.itv = setInterval(_dh.redraw, 500);
			jQuery(window).bind("resize scroll", _dh.redraw);

			_dh.$dh_debug_toolbar.attr("disabled", false); // 버튼 활성화
			_dh.status = "enabled";
		},
		// 중지
		disable : function() {
			var _dh = this;

			if(_dh.$dh_partsection_wrap) _dh.$dh_partsection_wrap.hide().find("._dh_captured").hide();
			_dh.$dh_debug_toolbar.find("img").attr("src", "/shop/admin/img/codi/btn_dh_sourceview.png");
			_dh.$dh_debug_title.hide();
			_dh.$dh_debug_content_wrap.hide();
			_dh.$dh_msg_box.hide();

			// 하단 툴바 크키만큼 띄우기
			jQuery("#_dh_body_mb_box").css("height", _dh.$dh_debug_window.height());
			_dh.$d_t_srcname.html("");

			if (_dh.$dh_partsection) {
				_dh.$dh_partsection.each(function() {
					jQuery(this).unbind("mouseover", _dh.part_mouseover).unbind("mouseout", _dh.part_mouseout).unbind("mousedown", _dh.part_mousedown).unbind("mouseup", _dh.part_mouseup).unbind("click", _dh.part_click).unbind("focus", _dh.part_focus);
				});
			}

			jQuery(window).bind("resize scroll", _dh.redraw)
			if (_dh.itv) {
				clearInterval(_dh.itv);
				_dh.itv = null;
			}

			_dh.status = "disabled";
		},
		// 투명도 설정
		set_opacity : function($obj, opacity) {
			jQuery($obj).css({
				"opacity" : opacity,
				"filter" : "alpha(opacity:"+(opacity*100)+")",
				"-moz-opacity" : opacity
			});
		},
		get_object_pos : function(obj) {
			if (obj.offsetParent) {
				var pos = this.get_object_pos(obj.offsetParent);
				return { top : obj.offsetTop + pos.top, left : obj.offsetLeft + pos.left };
			}
			else return { top : 0, left : 0 };
		},
		redraw : function() {
			var _dh = designhelper;
			if (_dh.$dh_captured_top && _dh.$dh_captured_top.length > 0 && _dh.$dh_captured_top.is(":visible")) {
				var $self = _dh.$dh_partsection.filter("[gd_dhseq='"+_dh.$dh_captured_top.attr("gd_dhseq")+"']");
				if ($self && $self.length > 0 && $self.is(":visible")) _dh.set_part($self, "captured");
			}
			if (_dh.$dh_focused_top && _dh.$dh_focused_top.length > 0 && _dh.$dh_focused_top.is(":visible")) {
				var $self = _dh.$dh_partsection.filter("[gd_dhseq='"+_dh.$dh_focused_top.attr("gd_dhseq")+"']");
				if ($self && $self.length > 0 && $self.is(":visible")) _dh.set_part($self, "focused");
			}

			//_dh.$dh_msg_box.html("! 창크기 변경시 소스(html)보기가 닫힙니다.").show();
		},
		set_part : function($src, desc) {
			var _dh = this;
			if (!$src || $src.length == 0) return;
			var size = {
				width : $src.outerWidth(),
				height : $src.outerHeight()
			};
			var pos = $src.offset();

			var $top = null;
			var $left = null;
			var $right = null;
			var $bottom = null;

			switch(desc) {
				case "captured" : {
					$top = _dh.$dh_captured_top;
					$left = _dh.$dh_captured_left;
					$right = _dh.$dh_captured_right;
					$bottom = _dh.$dh_captured_bottom;
					break;
				}
				case "focused" : {
					$top = _dh.$dh_focused_top;
					$left = _dh.$dh_focused_left;
					$right = _dh.$dh_focused_right;
					$bottom = _dh.$dh_focused_bottom;
					break;
				}
				default : return;
			}

			$top.show().css({
				"border-width" : 0,
				"width" : size.width+"px"
			}).offset(pos).css("border-top-width", _dh._d_p_captured_bw).attr("gd_dhseq", $src.attr("gd_dhseq"));

			$left.show().css({
				"border-width" : 0,
				"height" : size.height+"px"
			}).offset(pos).css("border-left-width", _dh._d_p_captured_bw).attr("gd_dhseq", $src.attr("gd_dhseq"));

			$right.show().css({
				"border-width" : 0,
				"height" : size.height+"px"
			}).offset({top:pos.top, left:pos.left+size.width-_dh._d_p_captured_bw-$right.width()}).css("border-right-width", _dh._d_p_captured_bw).attr("gd_dhseq", $src.attr("gd_dhseq"));

			$bottom.show().css({
				"border-width" : 0,
				"width" : size.width+"px"
			}).offset({top:pos.top+size.height-_dh._d_p_captured_bw-$bottom.height(), left:pos.left}).css("border-bottom-width", _dh._d_p_captured_bw).attr("gd_dhseq", $src.attr("gd_dhseq"));
		},
		// 선택중인 영역 표시
		capture_part : function($self) {
			var _dh = this;
			_dh.set_part($self, "captured");
		},
		// part_mouseover
		part_mouseover : function(event) {
			// 버블링 중지
			event.stopImmediatePropagation();
			event.stopPropagation();
			event.preventDefault();

			if (designhelper.captured_parts.length > 0) return;

			var $self = jQuery(this);
			try {
				if ($self.attr("gd_dhseq") == designhelper.$dh_captured_top.attr("gd_dhseq")) return;
			}
			catch(e) {}

			designhelper.set_part($self, "captured");
			designhelper.$d_t_srcname.html($self.attr("partsrc"));
		},
		// part_mouseout
		part_mouseout : function(event) {
			// 버블링 중지
			event.stopImmediatePropagation();
			event.stopPropagation();
			event.preventDefault();

			var $self = jQuery(this);
			try {
				if ($self.attr("gd_dhseq") == designhelper.$dh_captured_top.attr("gd_dhseq")) return;
			}
			catch(e) {}

			designhelper.$dh_partsection_wrap.find("._dh_captured").hide().removeAttr("gd_dhseq");
		},
		// part_mousedown
		part_mousedown : function(event) {
			// 버블링 중지
			event.stopImmediatePropagation();
			event.stopPropagation();
			event.preventDefault();

			if (event.which != 1) return;

			var $self = designhelper.$dh_partsection.filter("[gd_dhseq='"+jQuery(this).attr("gd_dhseq")+"']");

			var p_x = event.pageX;
			var p_y = event.pageY;

			var gd_dhseq = $self.attr("gd_dhseq");

			designhelper.$dh_partsection.each(function() {
				var $_d_p_item = jQuery(this);
				if (!$_d_p_item.is(":visible")) return ;

				var pos = designhelper.get_object_pos($_d_p_item[0]);
				var size = {
					width : $_d_p_item.outerWidth(),
					height : $_d_p_item.outerHeight()
				};

				// 현재 저장된 part영역에 element의 영역이 포함되는 경우(현재 element와 상위 element)
				if (p_y >= pos.top && p_x >= pos.left && p_x <= pos.left+size.width && p_y <= pos.top+size.height) {
					var item_dhseq = $_d_p_item.attr("gd_dhseq");
					designhelper.captured_parts[designhelper.captured_parts.length] = $_d_p_item;
					if (item_dhseq == gd_dhseq) {
						designhelper.captured_part_idx = designhelper.captured_parts.length - 1;
						designhelper.capture_part($_d_p_item);
					}
				}
			});

			jQuery("body, html").bind("mousewheel DOMMouseScroll", designhelper.scroll_part);
		},
		// part_mouseup
		part_mouseup : function(event) {
			event.preventDefault();
			event.stopImmediatePropagation();
			event.stopPropagation();

			if (event.which != 1) return;

			var $self = designhelper.captured_parts[designhelper.captured_part_idx];
			jQuery("body, html").unbind("mousewheel DOMMouseScroll", designhelper.scroll_part);
			designhelper.captured_parts = new Array();
			designhelper.captured_part_idx = null;
			designhelper.select_part($self);
		},
		part_click : function(event) {
			event.preventDefault();
			event.stopImmediatePropagation();
			event.stopPropagation();
			return false;
		},
		part_focus : function() {
			this.blur();
		},
		// scroll_part
		scroll_part : function(event) {
			event.preventDefault();
			event.stopImmediatePropagation();
			event.stopPropagation();
			var wheelDelta = event.originalEvent['wheelDelta'] || (event.originalEvent['detail'] * -1);
			designhelper.wheel_part(wheelDelta / Math.abs(wheelDelta));
		},
		// wheel_part
		wheel_part : function(n) {
			var _dh = this;

			// 선택 순환(최하위->최상위, 최상위->최하위)
			_dh.captured_part_idx += (n*-1);
			if (_dh.captured_part_idx > _dh.captured_parts.length - 1) _dh.captured_part_idx = 0;
			else if (_dh.captured_part_idx < 0) _dh.captured_part_idx = _dh.captured_parts.length - 1;

			_dh.capture_part(_dh.captured_parts[_dh.captured_part_idx]);
		},
		// select_part
		select_part : function($self) {
			var _dh = this;

			if (!$self || $self.length == 0) return;
			jQuery("#_dh_banner").remove();
			// 선택중인 영역(_dh.$_dh_captured)을 선택된 영역(_dh.$_dh_focused)으로 표시.
			_dh.set_part($self, "focused");
			_dh.$dh_captured_top.hide().removeAttr("gd_dhseq");
			_dh.$dh_captured_left.hide();
			_dh.$dh_captured_right.hide();
			_dh.$dh_captured_bottom.hide();

			_dh.$dh_debug_content.empty();

			// 선택영역의 소스정보 가져오기
			var dhseq = $self.attr("gd_dhseq");
			var src_info = _dh.srcinfo[dhseq];
			var senddata = new Array();
			try {
				for(var i = 0, il = src_info.length; i < il; ++i) {
					senddata[i] = src_info[i].src+":"+src_info[i].realsrc + ":" + src_info[i].min + ":" + src_info[i].max;
					_dh.$dh_debug_content.append("<div class='src_wrap src_result_"+i+"'><div style='color:#368AFF; margin:0; padding:0;'><span class='src_path' style='text-decoration:underline; cursor:pointer;'><img src='/shop/admin/img/codi/dh_node_open.gif' class='src_path_folding' /> 스킨파일명 : "+src_info[i].src+"</span> <a dh_src='"+src_info[i].src+"' style='cursor:pointer; font-weight:bold; color:#000000;'>[디자인하기]</a></div></div>");
				}
			}
			catch(e) {
				_dh.$dh_debug_content.html("스킨 소스를 가져올 수 없습니다.");
			}

			// ajax로 소스호출
			_dh.xhr.onreadystatechange = function() {
				if (_dh.xhr.readyState == 4 && _dh.xhr.status == 200) {
					var res = _dh.xhr.responseText.split("\n");
					var idx = -1;
					var src_html = new Array();
					var res_src = null;
					var skinmodedir = null;
					for(var i = 0, il = res.length; i < il; ++i) {
						// 파일명인 경우 src: 로 시작.
						var src_match = res[i].match(/^src\:/g);
						if (src_match || i == il - 1) {
							if (!src_match) src_html[src_html.length] = res[i];
							else {
								res_src = res[i].replace(/^src\:/g, "").split("|");
								skinmodedir = res_src[2];
								var realsrc = res_src[1];
								res_src = res_src[0];

								if (realsrc == res_src) {
									_dh.$dh_debug_content.find("[dh_src='"+res_src+"']").attr("href", "/shop/admin/"+skinmodedir+"/codi.php?design_file="+res_src.substring(1)).attr("target", "_blank");
								}
								else {
									_dh.$dh_debug_content.find("[dh_src='"+res_src+"']").click(function() {
										_dh.go_preview(skinmodedir, jQuery(this).attr("dh_src").substring(1));
									});
								}
							}

							if (src_html.length == 0) continue;

							// 스킨소스 중 define인 경우 링크 생성.
							src_html = src_html.join("\n");
							var matches = null;
							if (matches = src_html.match(/(<!--|)\{\s*#\s*[^\}]*\}(-->|)/g)) {
								for(var j = 0, jl = matches.length; j < jl; ++j) {
									var fid = matches[j].replace(/(<!--|){\s*#\s*([^\s]*)\s*[^}]*}(-->|)/g, "$2");
									var src_info = _dh.srcinfo[dhseq];
									var mtpl = null;
									for(var k = 0, kl = src_info.length; k < kl; ++k) {
										if (res_src == src_info[k].src) {
											mtpl = src_info[k].mtpl;
											break;
										}
									}

									if (!_dh.fidinfo[mtpl][fid]) continue;
									src_html = src_html.replace(new RegExp(matches[j], "gi"), "<a href='/shop/admin/"+skinmodedir+"/codi.php?design_file="+_dh.fidinfo[mtpl][fid].realpath+"' target='_blank' style='text-decoration:underline; color:#F15F5F; font-weight:bold; cursor:pointer;'>"+matches[j]+"</a>");
								}
							}

							_dh.$dh_debug_content.find(".src_result_"+(++idx)).append("<pre class='src_html' style='margin:0; padding-left:5px;'>"+src_html+"</pre>");
							var view_part_html = jQuery(".src_html").html().replace(/ /g, '');
							var view_part_banner = view_part_html.split("@dataBanner(");
							if(view_part_banner[1]){
								var part_banner = view_part_banner[1].split(")");
								var bannercode = part_banner[0];
								if(bannercode){
									designhelper.banner(bannercode,dhseq);
								}
							}

							src_html = new Array();
						}
						else src_html[src_html.length] = res[i];
					}

					_dh.$dh_debug_content.find(".src_path").click(function() {
						var $self = jQuery(this);
						var $src_html = $self.parent().parent().find(".src_html");
						var $src_path_folding = $self.find(".src_path_folding");
						if ($src_html.is(":visible")) {
							$src_path_folding.attr("src", "/shop/admin/img/codi/dh_node_close.gif");
							$src_html.hide();
						}
						else {
							$src_path_folding.attr("src", "/shop/admin/img/codi/dh_node_open.gif");
							$src_html.show();
						}
					});

					_dh.$dh_debug_content_wrap.find(">div:first").hide().show();
				}
			}

			var url = "/shop/lib/get_skin_source.php";
			var data = "srcinfo="+senddata.join(",")+"&skin_nm="+(__gd_dh_skin_nm__ ? __gd_dh_skin_nm__ : "");
			_dh.xhr.open("POST", url, true);
			_dh.xhr.setRequestHeader("Content-Type","application/x-www-form-urlencoded;charset=EUC-KR");
			_dh.xhr.setRequestHeader("Cache-Control","no-cache, must-revalidate");
 			_dh.xhr.setRequestHeader("Pragma","no-cache");
			_dh.xhr.send(data);

			$self.trigger("mouseover");
		},
		popup : function() {
			var _dh = this;
			var msg = new Array();
			msg[msg.length] = "* 화면보기(미리보기)용 페이지 입니다 *";
			msg[msg.length] = "";
			msg[msg.length] = "  @ 주의!";
			msg[msg.length] = "  - 해당 페이지에서는 디자인(html)편집이 제공되지 않으며, 자바스크립트가 정상적으로 작동하지 않을 수 있습니다.";
			msg[msg.length] = "";
			msg[msg.length] = "  @ 선택영역 디자인 편집하기";
			msg[msg.length] = "  - \"소스(html)보기\"에서 각 영역별 [디자인하기]를 클릭하시면, 편집작업이 가능한 페이지로 연결됩니다.";
			var html = "<div id='_dh_popup' style='position:absolute; width:594px; height:214px; left:50%; top:50%; margin-left:-300px; margin-top:-110px; border:solid 3px #A9A9A9; background-color:#FFFFFF; z-index:"+(_dh.max_zidx+10)+";'><div style='padding:30px 20px; line-height:20px; '><div id='_dh_popup_close' style='float:right; cursor:pointer; color:#000000; font-weight:bold; margin-top:-25px; margin-right:-8px;'><img src='/shop/admin/img/codi/btn_dh_popup_close.gif' /></div>"+msg.join("<br />")+"</div></div>";
			this.$body.append(html);
			jQuery("#_dh_popup_close").click(function() {
				jQuery("#_dh_popup").remove();
			});
		},
		banner : function(code,dhseq) {
			var _dh = this;
			var banner_img = jQuery("[gd_dhseq=" + dhseq + "]");
			var img_src = banner_img.attr("src");
			if(!img_src){
				banner_img = jQuery("[gd_dhseq=" + dhseq + "]").find("img:eq(0)");
				img_src = banner_img.attr("src");
			}
			var fileNameIndex = img_src.lastIndexOf("/") + 1;
			var imgname = img_src.substr(fileNameIndex);
			var imgmsg = '';
			if(!imgname){
				imgname = '';
				imgmsg = '등록된 이미지가 없습니다.';
			} else {
				imgmsg = imgname;
			}
			
			var msg = new Array();
			msg[msg.length] = "* 로고/배너관리 바로 연결 *";
			msg[msg.length] = "";
			msg[msg.length] = "  @ 수정 버튼을 클릭하시면 해당 이미지의 수정페이지가 열립니다.";
			msg[msg.length] = "";
			msg[msg.length] = "  <strong>치환코드 : {@dataBanner("+code+")}</strong>";
			msg[msg.length] = "";
			
			var html = "<div id='_dh_banner' style='position:fixed !important; position:absolute; width:400px; height:300px; left:50%; top:30%; margin-left:-200px; margin-top:-110px; border:solid 3px #3333ff; background-color:#FFFFFF; z-index:"+(_dh.max_zidx+10)+";'>";
			html += "<div style='padding:30px 20px; line-height:20px; '>";
			html += "<div id='_dh_banner_close' style='float:right; cursor:pointer; color:#000000; font-weight:bold; margin-top:-25px; margin-right:-8px;'>";
			html += "<img src='/shop/admin/img/codi/btn_dh_popup_close.gif' />";
			html += "</div>";
			html += msg.join("<br />");
			html += "<div id='_dh_banner_img' style='height:50px; padding:3px 0px; text-align:center;'><img src='"+img_src+"' height='50px' /></div>";
			html += "<div id='_dh_banner_imgname' style='height:15px; padding:5px 0px; text-align:center;'>"+imgmsg+"</div>";
			html += "<div id='_dh_banner_open' style='padding-top:15px; cursor:pointer; text-align:center;'><img src='/shop/admin/img/codi/btn_edit.gif' /></div>";
			html += "</div></div>";
			this.$body.append(html);
			
			jQuery("#_dh_banner_close").click(function() {
				jQuery("#_dh_banner").remove();
			});
			jQuery("#_dh_banner_open").click(function() {
				window.open('/shop/admin/design/design_banner_register.php?returnUrl=popup.banner.php&mode=modify&iname='+imgname+'&chgcode='+code,'','width=980,height=700,scrollbars=1,resizable=yes');
			});
		},
		go_preview : function(skinmodedir, preview_url) {
			var editing = false;
			if (opener.location.href.match(new RegExp("/shop/admin/"+skinmodedir+"/iframe.codi.php", "gi")) && opener.location.href.match(new RegExp("[?&]+design_file="+preview_url, "gi"))) editing = true;
			if (opener.location.href.match(/\/shop\/admin\/design\/iframe\.intro\.default\.php/gi) && preview_url == "main/intro.htm") editing = true;
			if (opener.location.href.match(new RegExp("/shop/admin/design/iframe.popup_register.php", "gi")) && opener.location.href.match(new RegExp("[?&]+file="+preview_url.replace(/^popup\//gi, ""), "gi"))) editing = true;

			if (editing) {
				alert("현재 편집중인 파일입니다.");
				opener.top.window.focus();
			}
			else window.open("/shop/admin/"+skinmodedir+"/codi.php?design_file="+preview_url);
		}
	};
}

var designhelper = null;
function set_designhelper() {
	try {
		if (top.location.href == location.href && designhelper == null && location.href.match(/\bgd_preview=1\b/g)) {
			if (location.href.match(/\bgd_srcview=1\b/gi)) {
				jQuery(window).bind("beforeunload", function() {
					return "페이지 이동시 소스(html)보기 기능이 해제됩니다.";
				});

				if (designhelper == null) {
					try {
						designhelper = new DesignHelper();
						designhelper.init(true);
					}
					catch(e) {
						designhelper = null;
						throw null;
					}
				}
				designhelper.start();
				designhelper.enable();
				designhelper.popup();
			}
			else {
				try {
					designhelper = new DesignHelper();
					designhelper.init(false);
				}
				catch(e) {
					designhelper = null;
					throw null;
				}
				designhelper.start();
			}
		}
	}
	catch(e) {
		throw null;
	}
}

function start_designhelper() {
	try {
		set_designhelper();
	}
	catch(e) {
		// $ 예약어 사용중 여부.
		var $_is_used = false;
		try {
			if ($) $_is_used = true;
			else throw null;
		}
		catch(e) { }

		var scriptEl = document.createElement("script");
		scriptEl.src = "/shop/lib/js/jquery-1.10.2.min.js";
		document.getElementsByTagName("head")[0].appendChild(scriptEl);

		var itv = setInterval(function() {
			try {
				if (jQuery) {
					// $ 예약어 사용시
					if ($_is_used) jQuery.noConflict();

					clearInterval(itv);
					itv = null;
					set_designhelper();
				}
			}
			catch(e) { }
		}, 500);
	}
}

try {
	window.attachEvent("onload", start_designhelper);
}
catch(e) {
	try {
		window.addEventListener("load", start_designhelper);
	}
	catch(e) {}
}
