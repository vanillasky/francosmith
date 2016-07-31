var nsGodoLoadingIndicator = function() {
	return {
		bg : null,
		el : null,
		sc : null,
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
				elMsg : '<img src="../img/admin_progress.gif">'
			}, opt || { });

			if (self.bg == null) {
				self.bg = new Element('div', {style: 'position:absolute;top:0;left:0;background:'+self.option.bgColor+';filter:alpha(opacity='+ (self.option.bgOpacity * 100) +');opacity:'+self.option.bgOpacity+';display:none;cursor:progress;', id : 'el-godo-loading-indicator'});
				$$('body')[0].insert( self.bg );
			}

			if (self.el == null) {
				self.el = new Element('div', {style: 'position:absolute;background:'+self.option.elBgColor+';display:none;cursor:progress;', id : 'el-godo-loading-indicator-wrap'});
				$$('body')[0].insert( self.el );
			}

			if (self.option.psObject != null) {

				self.option.psObject.observe('load', function(e) {
					self.hide()
				});

			}

		},
		show : function() {

			var self = this;

			self.sc = $$("body")[0].getStyle('overflow');
			$$("body")[0].setStyle({overflow:'hidden'});

			self._drawBG();
			self._draw();

		},
		hide : function() {

			var self = this;

			if (self.bg != null) self.bg.setStyle({display:'none'});
			if (self.el != null) self.el.setStyle({display:'none'});

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

			this.bg.setStyle({
				width : '100%',
				height : document.body.scrollHeight + 'px',
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
