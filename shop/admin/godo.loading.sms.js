var nsGodoLoadingSms = function() {
	return {
		bg : null,
		el : null,
		sc : null,
		warningMsg : null,
		infoMsg : null,
		option : {},
		init : function(opt) {

			var self = this;

			self.option = Object.extend({
				psObject : null,

				bgColor : '#44515b',
				bgOpacity : 0.8,

				elBgColor : 'transparent',
				elWidth : 118,
				elHeight: 116,
				elMsg : '<img src="../img/admin_progress_2.gif">',

				warningMsgBgColor : '#ffffff',
				warningMsgColor : 'red',
				warningMsgOpacity : 0.6,
				warningMsgWidth : 400,
				warningMsgHeight : 50,
				warningMsgMtop : 150,
				warningMsgAlign : 'center',
				warningMsgMent : '※ 주의 : SMS 발송중에 브라우저를 닫으면 발송이 완료 되지 않습니다.',

				infoMsgColor : '#ffffff',
				infoMsgBgColor : 'transparent',
				infoMsgMtop : 65,
				infoMsgMleft : 45,
				infoMsgWidth : 150,
				infoMsgHeight : 150
			}, opt || { });

			if (self.bg == null) {
				self.bg = new Element('div', {style: 'position:absolute;top:0;left:0;background:'+self.option.bgColor+';filter:alpha(opacity='+ (self.option.bgOpacity * 100) +');opacity:'+self.option.bgOpacity+';display:none;cursor:progress;', id : 'el-godo-loading-sms'});
				$$('body')[0].insert( self.bg );
			}

			if (self.infoMsg == null) {
				self.infoMsg = new Element('div', {style: 'position:absolute; color:'+self.option.infoMsgColor+'; background:'+self.option.infoMsgBgColor+'; display:none; cursor:progress; z-index:1;', id : 'el-godo-loading-sms-infoMsg'});
				$$('body')[0].insert( self.infoMsg );
			}

			if (self.warningMsg == null) {
				self.warningMsg = new Element('div', {style: 'position:absolute; text-align: '+self.option.warningMsgAlign+';line-height:'+self.option.warningMsgHeight+'px; color:'+self.option.warningMsgColor+'; background:'+self.option.warningMsgBgColor+'; filter:alpha(opacity='+ (self.option.warningMsgOpacity * 100) +'); opacity:'+self.option.warningMsgOpacity+'; display:none; cursor:progress; z-index:1;', id : 'el-godo-loading-sms-titleMsg'});
				$$('body')[0].insert( self.warningMsg );
			}

			if (self.el == null) {
				self.el = new Element('div', {style: 'position:absolute;background:'+self.option.elBgColor+';display:none;cursor:progress;', id : 'el-godo-loading-sms-wrap'});
				$$('body')[0].insert( self.el );
			}

			if (self.option.psObject != null) {
				self.option.psObject.observe('load', function(e) {
					window.onbeforeunload = '';
					document.onkeydown = '';
					self.hide();
				});
			}

		},
		show : function() {

			var self = this;

			self.sc = $$("body")[0].getStyle('overflow');
			$$("body")[0].setStyle({overflow:'hidden'});

			self._drawBG();
			self._draw();
			self._drawWM();
			self._drawIM();
		},
		gogosing : function(getddd) {
			this.infoMsg.update( getddd );
		},
		
		_drawIM : function() {

			if (this.infoMsg == null) return;

			var w = this._getWindowSize();

			var x = (w.width - this.option.infoMsgWidth) / 2;
			var y = (w.height - this.option.infoMsgHeight) / 2;

			this.infoMsg.setStyle({
				top : (y + (document.body.scrollTop || document.viewport.getScrollOffsets().top))  + this.option.infoMsgMtop + 'px',
				left : (x + (document.body.scrollLeft || document.viewport.getScrollOffsets().left)) + this.option.infoMsgMleft + 'px',
				width : this.option.infoMsgWidth + 'px',
				height : this.option.infoMsgHeight + 'px',
				display : 'block'
			});
		},
		_drawWM : function() {

			if (this.warningMsg == null) return;

			var w = this._getWindowSize();

			var x = (w.width - this.option.warningMsgWidth) / 2;
			var y = (w.height - this.option.warningMsgHeight) / 2;


			this.warningMsg.setStyle({
				top : (y + (document.body.scrollTop || document.viewport.getScrollOffsets().top))  + this.option.warningMsgMtop + 'px',
				left : (x + (document.body.scrollLeft || document.viewport.getScrollOffsets().left)) + 'px',
				width : this.option.warningMsgWidth + 'px',
				height : this.option.warningMsgHeight + 'px',
				display : 'block'
			});

			this.warningMsg.update( this.option.warningMsgMent );

		},
		hide : function() {

			var self = this;

			if (self.bg != null) self.bg.setStyle({display:'none'});
			if (self.el != null) self.el.setStyle({display:'none'});
			if (self.infoMsg != null) self.infoMsg.setStyle({display:'none'});
			if (self.warningMsg != null) self.warningMsg.setStyle({display:'none'});

			if (self.sc != null)
				$$("body")[0].setStyle({overflow:self.sc});

		},
		_draw : function() {

			if (this.el == null) return;

			var w = this._getWindowSize();

			var x = (w.width - this.option.elWidth) / 2;
			var y = (w.height - this.option.elHeight) / 2;

			this.el.setStyle({
				top : (y + (document.body.scrollTop || document.viewport.getScrollOffsets().top)) + 'px',
				left : (x + (document.body.scrollLeft || document.viewport.getScrollOffsets().left)) + 'px',
				width : this.option.elWidth + 'px',
				height : this.option.elHeight + 'px',
				display : 'block'
			});

			this.el.update( this.option.elMsg );

		},
		_drawBG : function() {

			if (this.bg == null) return;
			
			var bgSize = this._getWindowSize();

			this.bg.setStyle({
				width : bgSize.width + 'px',
				height : bgSize.height + 'px',
				display : 'block'

			});

		},
		_getWindowSize : function() {
			return {
				width : window.innerWidth	|| (window.document.documentElement.clientWidth	|| window.document.body.clientWidth),
				height: window.innerHeight	|| (window.document.documentElement.clientHeight|| window.document.body.clientHeight)
			}
		}
	}
}();
