if (typeof Prototype=='object') {
// s

var godo_ui_tooltip = function() {
	return {
		elms : new Array,
		init : function() {

			var self = this;

			var i=0;

			$$('.godo-tooltip').each(function(item){

				i++;

				item.id = 'godo-tooltip-'+i;

				self.elms.push(item);

				Event.observe(item, 'mouseover',	function(event) {
					self.showTooltip(event);
				});

				Event.observe(item, 'mouseout',	function(event) {
					self.hideTooltip(event);
				});

			});

		},
		showTooltip : function(e) {

			var el = Event.element(e);
			var id = el.identify() + '-div';

			if (Object.isElement($(id))) {
				var tooltip = $(id);
			}
			else {

				var tooltip = new Element('div', {'class': 'tooltip','id' : id}).update('<div class="tooltip-shadow"><div class="tooltip-frame"><div class="tooltip-contents">' + el.readAttribute('tooltip') + '</div></div></div>');

				tooltip.setStyle({
					position:'absolute',
					backgroundColor:'#fff',
					//width:450+'px',
					zIndex : 10000
				});

				document.body.appendChild(tooltip);
			}

			var pos = this.getTooltipPosition(e, tooltip);

			tooltip.setStyle({
				display:'block',
				top : pos.y + 'px',
				left: pos.x + 'px'
			});

		},
		hideTooltip : function(e) {

			var el = Event.element(e);
			var id = el.identify() + '-div';

			if (Object.isElement($(id))) {
				var tooltip = $(id);

				tooltip.setStyle({
					display:'none'
				});
			}

		},
		getTooltipPosition : function(e, tooltip) {

			var pos = {x:0,y:0};

			var tooltipWidth = tooltip.getWidth() || 450;

			pos.x = Event.pointerX(e) + 5;
			pos.y = Event.pointerY(e) + 10;

			var br_width = (Prototype.Browser.IE) ? document.body.clientWidth : window.innerWidth;
			if (pos.x + tooltipWidth > br_width) pos.x = pos.x - tooltipWidth;

			return pos;

		}
	}
}();


function godo_ui_init() {
	godo_ui_tooltip.init();
}


Event.observe(document, 'dom:loaded', godo_ui_init, false);
// e
}
