var nsAdminTab = function()
{
	return {
		currentContainerId : null,
		previousContainerId : null,
		tab : null,
		init : function(id)
		{
			var self = this;
			var a;

			self.tab = $(id).select('.navigation')[0];
			self.tab.select('li').each(function(li, idx)
			{

				a = li.select('a')[0];

				// bind event;
				a.observe('click', self.toggleTab.bind(self));

				// default container;
				if (idx == 0) {
					if (document.createEvent) {
						var e = document.createEvent('HTMLEvents');
						e.initEvent('click', true, true);

						a.dispatchEvent(e);
					}
					else if (a.fireEvent) {
						a.fireEvent('onclick');
					}
				}

			});

		},
		toggleTab : function(e)
		{
			var self = this;

			e.preventDefault();

			if (e.element().tagName == 'SPAN') {
				var a = e.element().up('a');
			}
			else {
				var a = e.element();
			}

			self.currentContainerId = 'container_' + a.readAttribute('href').substring(1);

			if (self.previousContainerId == self.currentContainerId) {
				return false;
			}

			$(self.currentContainerId).setStyle({
				display : 'block'
			});

			if (self.previousContainerId) {
				$(self.previousContainerId).setStyle({
					display : 'none'
				});
			}

			//
			self.tab.select('li').each(function(li)
			{
				li.removeClassName('active');
			});

			e.element().up('li').addClassName('active');

			self.previousContainerId = self.currentContainerId;

		}
	};
}();
