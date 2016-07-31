// DCTM override
DCTM.ori_write = DCTM.write;
DCTM.write = function(t_name, t_width, t_rows, t_property, tplFile) {
	DCTM.ori_write(t_name, t_width, t_rows, t_property, tplFile);
	setHeight_ifrmCodi();
};

// DCTM.codeBaseInput ������
DCTM.codeBaseInput = function( CObj, auto ) {
	var idObj = document.getElementById('resetting').getElementsByTagName("label")[0];

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

	this.textarea_view( document.getElementById(this.textarea_user_view) );
}
// DCTM override

// Design Codi Textarea Codemirror
DCTC = {
	cm_defopt : {
		mode : "text/html",
//		autoCloseTags: true,
//		autoCloseBrackets: true,
		indentUnit : 4,
		tabSize : 4,
		indentWithTabs : true,
		styleActiveLine: true,
		lineNumbers: true,
//		viewportMargin: Infinity,
		matchTags: true,
		matchBrackets : true
	},
	ed1 : null,
	ed2 : null,
	merge_ed : null,
	$code1_wrap : null,
	$code2_wrap : null,
	$merge_wrap : null,
	$merge_title : null,
	code1 : null,
	code2 : null,
	is_init_html : false,

	init : function(opts) {
		try {
			var _dctc = this;
			this.$code1_wrap = jQuery("#"+opts["code1_wrap"]);
			this.$code2_wrap = jQuery("#"+opts["code2_wrap"]);
			this.$merge_wrap = jQuery("#"+opts["merge_wrap"]);
			this.$merge_title = jQuery("#"+opts["merge_title"]);
			this.code1 = document.getElementById(opts["code1"]);
			this.code2 = document.getElementById(opts["code2"]);

			this.set_overrides();

			jQuery(this.code1).change(function() {
				if (_dctc.ed1) _dctc.ed1.setValue(this.value);
				if (_dctc.merge_ed) _dctc.merge_ed.editor().setValue(this.value);
			});

			jQuery(this.code2).change(function() {
				if (_dctc.ed2) _dctc.ed2.setValue(this.value);
				if (_dctc.merge_ed) _dctc.merge_ed.editor().setValue(this.value);
			});
		}
		catch(e) {
			if (DCTM.destroy) DCTM.destroy();

			throw null;
		}
	},

	destroy : function() {
		if (ori_DCTM) {
			DCTM.codeBaseInput = ori_DCTM.codeBaseInput;
			DCTM.textarea_view = ori_DCTM.textarea_view;
			DCTM.row_control = ori_DCTM.row_control;
			DCTM.row_direct = ori_DCTM.row_direct;
			DCTM.textarea_wrap = ori_DCTM.textarea_wrap;
		}

		jQuery(this.code1).unbind("change");
		jQuery(this.code2).unbind("change");

		if (this.ed1) {
			this.ed1.toTextArea();
			this.ed1 = null;
		}
		if (this.ed2) {
			this.ed2.toTextArea();
			this.ed2 = null;
		}
		if (this.merge_ed) {
			this.merge_ed = null;
		}
	},

	init_view : function(el, opt) {
		try {
			var defopt = this.cm_defopt;
			if (opt) for(var key in opt) defopt[key] = opt[key];

			return CodeMirror.fromTextArea(el, defopt);
		}
		catch(e) {
			if (DCTM.destroy) DCTM.destroy();

			throw null;
		}
	},

	view_code1 : function() {
		this.$code2_wrap.hide();
		this.$merge_wrap.hide();
		this.$merge_title.hide();
		this.$code1_wrap.show();

		if (this.ed1 == null) this.ed1 = this.init_view(this.code1, { readOnly : false });
		else {
			if (this.merge_ed) jQuery(this.code1).val(this.merge_ed.editor().getValue()).trigger("change");
			this.ed1.setOption("readOnly", false); // ����?
		}
		setHeight_ifrmCodi();
	},

	view_code2 : function() {
		var from_merge = false;
		if (this.merge_ed && this.$merge_wrap.is(":visible")) from_merge = true;

		this.$code1_wrap.hide();
		this.$merge_wrap.hide();
		this.$merge_title.hide();
		this.$code2_wrap.show();

		if (this.ed2 == null) {
			this.ed2 = this.init_view(this.code2, { readOnly : true });
			this.ed2.getWrapperElement().style.backgroundColor = "#DFDFDF";
		}
		else this.ed2.setOption("readOnly", true); // ����?

		if (from_merge) {
			jQuery(this.code1).val(this.merge_ed.editor().getValue()).trigger("change");
			if (this.ed1) this.ed1.setOption("readOnly", false); // ����?
		}
		setHeight_ifrmCodi();
	},

	view_merge : function(ed2_val) {
		// ���� �ҽ� ��������
		var ed1_val = null;
		if (this.ed1) ed1_val = this.ed1.getValue();
		else ed1_val = this.code1.value;

		// ���� �ҽ� ��������
		if (!ed2_val) ed2_val = (this.ed2)? this.ed2.getValue() : this.code2.value;

		this.$code1_wrap.hide();
		this.$code2_wrap.hide();
		this.$merge_wrap.show();
		this.$merge_title.show();

		if (this.merge_ed) {
			this.merge_ed.editor().setValue(ed1_val);
			this.merge_ed.right.orig.setValue(ed2_val);
		}
		else {
			this.$merge_wrap.empty();

			var defopt = this.cm_defopt;
			defopt['value'] = ed1_val;
			defopt['orig'] = ed2_val;
			defopt['readOnly'] = false;
			defopt['lineWrapping'] = true;
			defopt['highlightDifferences'] = true;

			this.merge_ed = CodeMirror.MergeView(this.$merge_wrap.get(0), defopt);
			this.merge_ed.right.orig.getWrapperElement().style.backgroundColor = "#DFDFDF";
			this.merge_ed.right.orig.setOption("styleActiveLine", false);
		}
		setHeight_ifrmCodi();
	},

	refresh_view : function() {
		if (this.$code1_wrap.is(":visible") && this.ed1 != null) this.ed1.refresh();
		if (this.$code2_wrap.is(":visible") && this.ed2 != null) this.ed2.refresh();
		if (this.$merge_wrap.is(":visible") && this.merge_ed != null) {
			this.merge_ed.editor().refresh();
			this.merge_ed.right.orig.refresh();
		}
	},

	set_overrides : function() {
		var _dctc = this;

		/*-------------------------------------
		 �����ҽ� �Է�
		-------------------------------------*/
		DCTM.codeBaseInput = function ( CObj, auto )
		{
			var idObj = document.getElementById('resetting').getElementsByTagName("label")[0];

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
					jQuery(userObj).val(baseObj.value).trigger("change");
					idObj.style.color = '#bf0000';
					idObj.style.fontWeight = 'bold';
				}
			}
			else
			{
				jQuery(userObj).val(codyObj.value).trigger("change");
				idObj.style.fontColor = '#000000';
				idObj.style.color = '#000000';
				idObj.style.fontWeight = 'normal';
			}

			if (this.textarea_view_id == this.textarea_base_body) this.textarea_view( jQuery("#"+this.textarea_user_view).get(0) );
		};

		/*-------------------------------------
		 �ҽ����� ����ó��
		-------------------------------------*/
		DCTM.textarea_view = function ( obj )
		{
			switch(obj.id) {
				case this.textarea_base_view : {
					if (this.textarea_view_id == this.textarea_base_body) return;
					this.textarea_view_id = this.textarea_base_body;

					_dctc.view_code2();

					document.getElementById( this.textarea_user_view ).style.color = '#FFFFFF';
					document.getElementById( this.textarea_user_view ).style.background = '#7F7F7F';

					document.getElementById( this.textarea_base_view ).style.color = '#222222';
					document.getElementById( this.textarea_base_view ).style.background = '#ECE9D8';

					document.getElementById( this.textarea_merge_view ).style.color = '#FFFFFF';
					document.getElementById( this.textarea_merge_view ).style.background = '#7F7F7F';
					document.getElementById("merge_source").disabled = true;

					break;
				}
				case this.textarea_user_view : {
					if (this.textarea_view_id == this.textarea_user_body) return;
					this.textarea_view_id = this.textarea_user_body;

					_dctc.view_code1();

					document.getElementById( this.textarea_user_view ).style.color = '#222222';
					document.getElementById( this.textarea_user_view ).style.background = '#ECE9D8';

					document.getElementById( this.textarea_base_view ).style.color = '#FFFFFF';
					document.getElementById( this.textarea_base_view ).style.background = '#7F7F7F';

					document.getElementById( this.textarea_merge_view ).style.color = '#FFFFFF';
					document.getElementById( this.textarea_merge_view ).style.background = '#7F7F7F';
					document.getElementById("merge_source").disabled = true;

					break;
				}
				case this.textarea_merge_view : {
					if (this.editor_type != "codemirror") return;
					if (this.textarea_view_id == this.textarea_merge_body) return;
					this.textarea_view_id = this.textarea_merge_body;

					//_dctc.view_merge();
					jQuery("#merge_source").attr("disabled", false).trigger("change");

					document.getElementById( this.textarea_user_view ).style.color = '#FFFFFF';
					document.getElementById( this.textarea_user_view ).style.background = '#7F7F7F';

					document.getElementById( this.textarea_base_view ).style.color = '#FFFFFF';
					document.getElementById( this.textarea_base_view ).style.background = '#7F7F7F';

					document.getElementById( this.textarea_merge_view ).style.color = '#222222';
					document.getElementById( this.textarea_merge_view ).style.background = '#ECE9D8';

					break;
				}
			}
		};

		/*-------------------------------------
		 TEXTAREA �ټ� ����
		-------------------------------------*/
		DCTM.row_control = function ( plug )
		{
			var $body = jQuery("#"+this.textarea_user_body+"_wrap").parent();
			var body_height = $body.height();

			if ( this.control_stop != 1 && ( plug == '+' || plug == '-' ) )
			{
				if ( plug == '+' && parseInt(body_height, 10) >= 740 )
				{
//					alert( "50���� ������ ������ �� �ֽ��ϴ�." );
					this.row_stop();
					return false;
				}
				else if ( plug == '-' && parseInt(body_height, 10) <= 40 )
				{
//					alert( "1���� ������ ������ �� �ֽ��ϴ�." );
					this.row_stop();
					return false;
				}

				$body.height((body_height + parseInt(plug+"20", 10)) + "px");
				DCTC.refresh_view();

				setHeight_ifrmCodi();
				setTimeout( "DCTM.row_control( '"  + plug + "' )", 100 );
			}
			else
			{
				this.row_stop();
				return false;
			}
		};

		/*-------------------------------------
		 TEXTAREA �ټ� ����
		-------------------------------------*/
		DCTM.row_direct = function ( num )
		{
			jQuery("#"+this.textarea_user_body+"_wrap").parent().height("740px");
			setHeight_ifrmCodi();
		};

		/*-------------------------------------
		 TEXTAREA �ٹٲ� ����/����
		-------------------------------------*/
		DCTM.textarea_wrap = function ()
		{
			if (DCTM.textarea_view_id == DCTM.textarea_merge_body) {
				alert("Diff ��忡�� �ٹٲ��� �������� �ʽ��ϴ�.");
				return; // merge_view ����� �ٹٲ޺Ұ�.
			}
			switch(DCTM.textarea_view_id) {
				case DCTM.textarea_user_body : {
					if (DCTC.ed1) DCTC.ed1.setOption("lineWrapping", !DCTC.ed1.getOption("lineWrapping"));
					break;
				}
				case DCTM.textarea_base_body : {
					if (DCTC.ed2) DCTC.ed2.setOption("lineWrapping", !DCTC.ed2.getOption("lineWrapping"));
					break;
				}
			}
		};
	}
}
// Design Codi Textarea Codemirror

// Design Codi LinedTextarea
DCLT = {
	is_init_html : false,

	init : function() {
		try {
			jQuery(window).on("resize", this.resize);

			this.set_overrides();
		}
		catch(e) {
			this.destroy();
			throw null;
		}
	},

	destroy : function() {
		if (ori_DCTM) {
			DCTM.codeBaseInput = ori_DCTM.codeBaseInput;
			DCTM.textarea_view = ori_DCTM.textarea_view;
			DCTM.row_control = ori_DCTM.row_control;
			DCTM.row_direct = ori_DCTM.row_direct;
			DCTM.textarea_wrap = ori_DCTM.textarea_wrap;
		}

		jQuery(window).off("resize", this.resize);
	},

	resize : function() {
		var $body = jQuery("#textarea >.body");
		var body_width = $body.width();
		$body.find(".linedwrap:visible").each(function() {
			var $linedwrap = jQuery(this);
			var $ta = $linedwrap.find("textarea");
			var $body_wrap = $linedwrap.parent();
			var originalTextAreaWidth = body_width;

			var sidebarWidth			= $linedwrap.find(".lines").outerWidth();
			var paddingHorizontal 		= parseInt($linedwrap.css("border-left-width"), 10) + parseInt($linedwrap.css("border-right-width"), 10) + parseInt($linedwrap.css("padding-left"), 10) + parseInt($linedwrap.css("padding-right"), 10);

			var linedWrapDivNewWidth 	= originalTextAreaWidth - paddingHorizontal;
			var textareaNewWidth		= originalTextAreaWidth - sidebarWidth - paddingHorizontal - 20 - (parseInt($ta.css("border-left-width"), 10) + parseInt($ta.css("border-right-width"), 10));

			$linedwrap.width(linedWrapDivNewWidth);
			$ta.width(textareaNewWidth);
		});
	},

	set_overrides : function() {

		/*-------------------------------------
		 �����ҽ� �Է�
		-------------------------------------*/
		DCTM.codeBaseInput = function ( CObj, auto )
		{
			var idObj = document.getElementById('resetting').getElementsByTagName("label")[0];

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
					jQuery(userObj).val(baseObj.value);
					idObj.style.color = '#bf0000';
					idObj.style.fontWeight = 'bold';
				}
			}
			else
			{
				jQuery(userObj).val(codyObj.value);
				idObj.style.fontColor = '#000000';
				idObj.style.color = '#000000';
				idObj.style.fontWeight = 'normal';
			}

			this.textarea_view( jQuery("#"+this.textarea_user_view).get(0) );
		};

		/*-------------------------------------
		 �ҽ����� ����ó��
		-------------------------------------*/
		DCTM.textarea_view = function ( obj )
		{
			switch(obj.id) {
				case this.textarea_base_view : {
					if (this.textarea_view_id == this.textarea_base_body) return;
					this.textarea_view_id = this.textarea_base_body;

					jQuery("."+this.textarea_user_body+"_wrap").hide();
					jQuery("."+this.textarea_base_body+"_wrap").show();
					jQuery("#"+this.textarea_base_body).show().trigger("refresh_show").trigger("refresh_height");
					jQuery(window).trigger("resize");

					document.getElementById( this.textarea_user_view ).style.color = '#FFFFFF';
					document.getElementById( this.textarea_user_view ).style.background = '#7F7F7F';

					document.getElementById( this.textarea_base_view ).style.color = '#222222';
					document.getElementById( this.textarea_base_view ).style.background = '#ECE9D8';

					break;
				}
				case this.textarea_user_view : {
					if (this.textarea_view_id == this.textarea_user_body) return;
					this.textarea_view_id = this.textarea_user_body;

					jQuery("."+this.textarea_base_body+"_wrap").hide();
					jQuery("."+this.textarea_user_body+"_wrap").show();
					jQuery("#"+this.textarea_user_body).show().trigger("refresh_show").trigger("refresh_height");
					jQuery(window).trigger("resize");

					document.getElementById( this.textarea_user_view ).style.color = '#222222';
					document.getElementById( this.textarea_user_view ).style.background = '#ECE9D8';

					document.getElementById( this.textarea_base_view ).style.color = '#FFFFFF';
					document.getElementById( this.textarea_base_view ).style.background = '#7F7F7F';

					break;
				}
			}
		};

		/*-------------------------------------
		 TEXTAREA �ټ� ����
		-------------------------------------*/
		DCTM.row_control = function ( plug )
		{
			var TObj = document.getElementById(this.textarea_user_body);

			if ( this.control_stop != 1 && ( plug == '+' || plug == '-' ) )
			{
				if ( plug == '+' && TObj.rows >= 50 )
				{
					alert( "50���� ������ ������ �� �ֽ��ϴ�." );
					this.row_stop();
					return false;
				}
				else if ( plug == '-' && TObj.rows <= 1 )
				{
					alert( "1���� ������ ������ �� �ֽ��ϴ�." );
					this.row_stop();
					return false;
				}

				TObj.rows = eval( "TObj.rows " + plug + " 1" );
				try {
					document.getElementById(this.textarea_base_body).rows = TObj.rows;
				}
				catch(e) {}
				try {
					jQuery("#user_body, #base_body").trigger("refresh_height");
				}
				catch(e) {}
				setHeight_ifrmCodi();
				setTimeout( "DCTM.row_control( '"  + plug + "' )", 100 );
			}
			else
			{
				this.row_stop();
				return;
			}
		};

		/*-------------------------------------
		 TEXTAREA �ټ� ����
		-------------------------------------*/
		DCTM.row_direct = function ( num )
		{
			var TObj = document.getElementById(this.textarea_user_body);
			TObj.rows = num;
			try {
				document.getElementById(this.textarea_base_body).rows = TObj.rows;
			}
			catch(e) {}
			try {
				jQuery("#user_body, #base_body").trigger("refresh_height");
			}
			catch(e) {}

			setHeight_ifrmCodi();
		};

		/*-------------------------------------
		 TEXTAREA �ٹٲ� ����/����
		-------------------------------------*/
		DCTM.textarea_wrap = function ()
		{
			return; // linedtextarea ����� �ٹٲ޺Ұ�.
		};
	}
}
// Design Codi LinedTextarea

if (typeof(jQuery) != "undefined") {
	// ��ü ����.
	function copy_ref_object(src) {
		var desc = {};
		for(var k in src) {
			desc[k] = src[k];
		}
		return desc;
	}

	// codemirror, linedtextarea js, css import
	var html = new Array();
	var n = -1;
	html[++n] = "<link rel='stylesheet' type='text/css' href='../../lib/js/codemirror/lib/codemirror.css' charset='UTF-8'>";
	html[++n] = "<link rel='stylesheet' type='text/css' href='../../lib/js/codemirror/addon/merge/merge.css' charset='UTF-8'>";
	html[++n] = "<script type='text/javascript' src='../../lib/js/codemirror/lib/codemirror.js' charset='UTF-8'></script>";
	html[++n] = "<script type='text/javascript' src='../../lib/js/codemirror/mode/xml/xml.js' charset='UTF-8'></script>";
	html[++n] = "<script type='text/javascript' src='../../lib/js/codemirror/mode/javascript/javascript.js' charset='UTF-8'></script>";
	html[++n] = "<script type='text/javascript' src='../../lib/js/codemirror/mode/css/css.js' charset='UTF-8'></script>";
	html[++n] = "<script type='text/javascript' src='../../lib/js/codemirror/mode/htmlmixed/htmlmixed.js' charset='UTF-8'></script>";
	html[++n] = "<script type='text/javascript' src='../../lib/js/codemirror/addon/merge/dep/diff_match_patch.js' charset='UTF-8'></script>";
	html[++n] = "<script type='text/javascript' src='../../lib/js/codemirror/addon/merge/merge.js' charset='UTF-8'></script>";
	//html[++n] = "<script type='text/javascript' src='../../lib/js/codemirror/addon/edit/closebrackets.js' charset='UTF-8'></script>";
	//html[++n] = "<script type='text/javascript' src='../../lib/js/codemirror/addon/edit/closetag.js' charset='UTF-8'></script>";
	html[++n] = "<script type='text/javascript' src='../../lib/js/codemirror/addon/edit/matchbrackets.js' charset='UTF-8'></script>";
	html[++n] = "<script type='text/javascript' src='../../lib/js/codemirror/addon/fold/xml-fold.js' charset='UTF-8'></script>";
	html[++n] = "<script type='text/javascript' src='../../lib/js/codemirror/addon/edit/matchtags.js' charset='UTF-8'></script>";
	html[++n] = "<script type='text/javascript' src='../../lib/js/codemirror/addon/selection/active-line.js' charset='UTF-8'></script>";
	html[++n] = "<link rel='stylesheet' type='text/css' href='../../lib/js/jquery-linedtextarea/jquery-linedtextarea.css'>";
	html[++n] = "<script type='text/javascript' src='../../lib/js/jquery-linedtextarea/jquery-linedtextarea.js'></script>";
	document.write(html.join("\n"));
	html = null;

	// DCTM�� ori_DCTM���� ���
	var ori_DCTM = copy_ref_object(DCTM);

	// �� DCTM ����.
	// DCTM.source ������
	DCTM.source = function (tplFile, body)
	{
		if (body != 'user_body' && body != 'base_body') return;

		var urlStr = "codi/_ajax.php?mode=getTextarea&body=" + body + "&tplFile=" + tplFile + "&dummy=" + new Date().getTime();
		jQuery.ajax({
			url : urlStr,
			type : "get",
			success : function (res) {
				try {
					jQuery("#"+body).val(res).trigger("change");
				}
				catch(e) {}
			}
		});
	};

	// DCTM.write ������
	DCTM.write = function(t_name, t_width, t_rows, t_property, tplFile) {
		this.ori_write(t_name, t_width, t_rows, t_property, tplFile);
		// textarea Ȯ��.
		try {
			var html = new Array();
			var n = -1;
			html[++n] = "<style>";
			html[++n] = "#textarea #merge_view { font:9pt tahoma; border-style:solid; border-width:0; margin:0; }";
			html[++n] = "#textarea .user_body_wrap .CodeMirror { height:100%; }";
			html[++n] = "#textarea .base_body_wrap .CodeMirror { height:100%; }";
			html[++n] = "#textarea .merge_body_wrap .CodeMirror-merge-copy { display:none; }";
			html[++n] = "#textarea .merge_body_wrap .CodeMirror-merge,";
			html[++n] = "#textarea .merge_body_wrap .CodeMirror-merge-pane,";
			html[++n] = "#textarea .merge_body_wrap .CodeMirror { height:100%; }";
			html[++n] = "#textarea .CodeMirror-matchingtag { background: rgba(255, 150, 0, .3); }";
			html[++n] = "#textarea .user_body_wrap textarea, #textarea .base_body_wrap textarea { border:solid 1px #CCCCCC; }";
			html[++n] = "</style>";
			document.write(html.join("\n"));
			html = null;

			this.textarea_merge_body = 'merge_body_wrap';
			this.textarea_merge_view = 'merge_view';
			this.editor_type = "textarea";

			jQuery("#resetting").append(" <span id='editortype' class='editortype'>���������� : <label><input type='radio' name='_et' value='textarea' onclick='DCTM.set_textarea();' checked='checked' />�⺻</label> <label><input type='radio' name='_et' value='linedtextarea' onclick='DCTM.set_linedtextarea();' />Ÿ��1(�ٹ�ȣ���)</label> <label><input type='radio' name='_et' value='codemirror' onclick='DCTM.set_codemirror();' />Ÿ��2(�ҽ���, IE8���� �����ȵ�)</label> </span>");

			var ck_et = getCookie("DCTM_et");

			var ie_ver = navigator.appVersion.match(/MSIE\s*[0-9\.]*\s*;/gi);
			if (ie_ver) {
				if(Number(ie_ver[0].replace(/[^0-9\.]*/g, "")) < 9) {
					jQuery("#editortype [name='_et'][value='codemirror']").attr("disabled", true);
					if (!ck_et) ck_et = "linedtextarea";
				}
			}

			switch(ck_et) {
				case "textarea" : {
					break;
				}
				case "linedtextarea" : {
					DCTM.set_linedtextarea();
					break;
				}
				default : {
					DCTM.set_codemirror();
					break;
				}
			}
		}
		catch(e) {
			if (DCTM.destroy) DCTM.destroy();
		}
	}

	// �� DCTM�� ���� DCTM���� ����
	DCTM.destroy = function() {
		this.set_textarea();
		try {
			document.getElementById("editortype").style.display = "none";
		}
		catch(e) {}
		DCTM = copy_ref_object(ori_DCTM);
	};

	// �⺻ Ȯ�� �Լ�
	DCTM.set_ex = function() {
		jQuery("#" + this.textarea_user_body).wrap("<div id='"+this.textarea_user_body+"_wrap' class='"+this.textarea_user_body+"_wrap' style='height:100%;'></div>");
		jQuery("#" + this.textarea_base_body).wrap("<div id='"+this.textarea_base_body+"_wrap' class='"+this.textarea_base_body+"_wrap' style='height:100%;'></div>");

		jQuery("#"+this.textare_user_body+",#"+this.textare_base_body).show();
	}

	// �⺻ Ȯ�� �Լ� ���
	DCTM.unset_ex = function() {
		if (jQuery("#" + this.textarea_user_body+"_wrap").length != 0) jQuery("#" + this.textarea_user_body).unwrap();
		if (jQuery("#" + this.textarea_base_body+"_wrap").length != 0) jQuery("#" + this.textarea_base_body).unwrap();
	}

	// codemirror ����
	DCTM.set_codemirror = function() {
		if (document.compatMode.toLowerCase() == 'backcompat') {
			alert("�������� �ʴ� �������Դϴ�.");
			jQuery("#textarea [name='_et'][value='"+this.editor_type+"']").attr("checked", true);
			return;
		}
		switch(this.editor_type) {
			case "codemirror" : return;
			case "linedtextarea" : {
				this.unset_linedtextarea();
				break;
			}
		};

		try {
			this.set_ex();
			this.editor_type = "codemirror";
			jQuery("#textarea [name='_et'][value='codemirror']").attr("checked", true);

			// merge_body_wrap(div), merge_view(��ư) �߰�
			jQuery("#textarea .head").append("<div id='merge_body_title' style='text-align:center; height:25px; line-height:25px; border-bottom:solid 1px #000000; display:none;'><div style='float:left; width:50%; background-color:#FFFFFF;'>����â</div><div style='width:50%; margin-left:50%; background-color:#DFDFDF;'>����â</div></div>");
			jQuery("#" + this.textarea_user_body + "_wrap").parent().append("<div id='merge_body_wrap' class='merge_body_wrap' style='height:100%;'></div>");
			jQuery("#" + this.textarea_user_view).parent().append("<div style='display:inline;' id='"+this.textarea_merge_view+"_wrap'><input type='button' ID='"+this.textarea_merge_view+"' value='�ҽ���' onclick='DCTM.textarea_view( this )'><select id='merge_source' onclick='DCTM.textarea_view( this )' disabled='disabled'><option value=''>�����ҽ�</option>"+((jQuery("#slt_history >option[value!='']").length > 0) ? jQuery("#slt_history").html() : "")+"</select></div>");
			jQuery("#merge_source").change(function() {
				if (DCTM.textarea_view_id != DCTM.textarea_merge_body) return;
				var merge_source_val = jQuery("#merge_source").val();
				if (!merge_source_val) DCTC.view_merge();
				else {
					jQuery.ajax({
						url : "codi/_ajax.php?mode=getTextarea&body=user_body&tplFile=" + merge_source_val + "&dummy=" + new Date().getTime(),
						type : "get",
						success : function (res) {
							try {
								DCTC.view_merge(res);
							}
							catch(e) {}
						}
					});
				}
			}).find(">option").each(function (){
				var $self = jQuery(this);
				$self.html("�����ҽ��� "+$self.html()+" ��");
			});

			DCTC.init({
				code1_wrap : "user_body_wrap",
				code2_wrap : "base_body_wrap",
				merge_wrap : "merge_body_wrap",
				merge_title : "merge_body_title",
				code1 : "user_body",
				code2 : "base_body"
			});

			var $ta_body = jQuery("#textarea .body");
			$ta_body.height($ta_body.outerHeight());

			var tmp_textarea_view_id = this.textarea_view_id;
			this.textarea_view_id = "";
			this.textarea_view(jQuery("#"+tmp_textarea_view_id.replace(/_body$/g, "_view")).get(0));

			jQuery("#"+this.textarea_user_body).trigger("change");

			// form onsubmit override
			var $frm = jQuery("#"+this.textarea_user_body).parents("form:first");
			this.def_submit = $frm.get(0).onsubmit;

			// ���� submit ����
			try {
				$frm.get(0).attachEvent("onsubmit", null);
			}
			catch(e) {
				try {
					$frm.get(0).addEventListener("submit", null);
				}
				catch(e) {}
			}
			$frm.get(0).setAttribute("onsubmit", "");

			// ���ο� submit ����
			$frm.submit(function(event) {
				try {
					if (DCTM.editor_type == "codemirror") DCTM.textarea_view(jQuery('#'+DCTM.textarea_user_view).get(0));
				}
				catch(e) {}

				return DCTM.def_submit.call(this);
			});
			// form onsubmit override

			setHeight_ifrmCodi();

			setCookie("DCTM_et", "codemirror");
		}
		catch(e) {
			this.unset_codemirror();

			throw null;
		}
	};

	// codemirror ����
	DCTM.unset_codemirror = function() {
		if (this.textarea_view_id == this.textarea_merge_body) {
			this.textarea_view(jQuery("#"+this.textarea_user_view).get(0));
		}

		DCTC.destroy();

		jQuery("#" + this.textarea_merge_view + "_wrap").remove();
		jQuery("#" + this.textarea_merge_body).remove();

		var $ta_body = jQuery("#textarea .body");
		$ta_body.height("100%");

		this.unset_ex();
	}

	// linedtextarea ����
	DCTM.set_linedtextarea = function() {
		switch(this.editor_type) {
			case "codemirror" : {
				this.unset_codemirror();
				break;
			}
			case "linedtextarea" : return;
		};

		try {
			this.set_ex();
			this.editor_type = "linedtextarea";
			jQuery("#textarea [name='_et'][value='linedtextarea']").attr("checked", true);

			jQuery("#"+this.textarea_user_body + ", #"+this.textarea_base_body).show();
			if(this.textarea_view_id == this.textarea_base_body) this.textarea_view(jQuery("#"+this.textarea_base_view).get(0));
			else this.textarea_view(jQuery("#"+this.textarea_user_view).get(0));

			DCLT.init();

			jQuery("#"+this.textarea_user_body+", #"+this.textarea_base_body).on("refresh_height", function() {
				// ���� ���� �缳��
				var $self = jQuery(this);
				if (!$self.is(":visible")) return;
				var $codeLinesDiv = $self.parent().parent().find(".lines");
				$codeLinesDiv.height( $self.height() + 6 );
				$self.trigger("scroll");
			}).on("refresh_show", function() {
				// init
				jQuery(this).filter("[setlta!=1]:visible").linedtextarea().attr("setlta", 1);
			}).on("focus", function() {
				this.style.border = "solid 1px #627DCE";
			}).on("blur", function() {
				this.style.border = "solid 1px #CCCCCC";
			}).filter(":visible").linedtextarea().attr("setlta", 1);

			setHeight_ifrmCodi();

			setCookie("DCTM_et", "linedtextarea");
		}
		catch(e) {
			this.unset_linedtextarea();

			throw null;
		}
	};

	// linedtextarea ����
	DCTM.unset_linedtextarea = function() {
		DCTC.destroy();

		jQuery("#"+this.textarea_user_body+",#"+this.textarea_base_body).removeAttr("setlta").width("100%").unbind("scroll").off("refresh_height").off("refresh_show").off("focus").off("blur").unwrap().parent().find(".lines").remove();

		this.unset_ex();

		this.textarea_view(jQuery("#"+this.textarea_view_id.replace(/_body$/g, "_view")).get(0));
	}

	// textare ����
	DCTM.set_textarea = function() {
		switch(this.editor_type) {
			case "codemirror" : {
				this.unset_codemirror();
				break;
			}
			case "linedtextarea" : {
				this.unset_linedtextarea();
				break;
			}
			default : return;
		};

		this.unset_ex();
		this.editor_type = "textarea";
		jQuery("#textarea [name='_et'][value='textarea']").attr("checked", true);

		jQuery("#"+this.textarea_user_body + ", #"+this.textarea_base_body).show();
		if(this.textarea_view_id == this.textarea_base_body) this.textarea_view(jQuery("#"+this.textarea_base_view).get(0));
		else this.textarea_view(jQuery("#"+this.textarea_user_view).get(0));

		setHeight_ifrmCodi();

		setCookie("DCTM_et", "textarea");
	}

	// DCTM override
}
