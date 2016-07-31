var Countdown = function()
{
	return {
		interval : 1000,
		timer : {},
		lang : {
			'sec' : '초',
			'min' : '분',
			'hour' : '시간',
			'day' : '일',
			'mon' : '월',
			'year' : '년'
		},
		init : function(from, displayId)
		{
			var self = this;

			self.timer[displayId] = {};
			self.timer[displayId]['date'] = self.getDate(from);
			self.timer[displayId]['element'] = document.getElementById(displayId);
			self.timer[displayId]['timer'] = setInterval(function()
			{
				self.display(displayId);
			}, self.interval);

			self.display(displayId);
		},
		display : function(displayId)
		{
			var self = this;

			var from = self.timer[displayId]['date'];
			var now = new Date();

			var diff = Math.floor(from.getTime() - now.getTime()) / 1000;
			var diffDay = Math.floor(diff / 24 / 60 / 60);
			if (diff <= 0) {
				location.reload();
				return;
			}
			diff -= diffDay * 24 * 60 * 60;
			var diffHour = Math.floor(diff / 60 / 60);
			diff -= diffHour * 60 * 60;
			var diffMin = Math.floor(diff / 60);
			diff -= diffMin * 60;
			var diffSec = Math.floor(diff);

			var ret = [];
			ret.push(diffSec + self.lang.sec);

			if (diffMin != 0)
				ret.unshift(diffMin + self.lang.min);
			if (diffHour != 0)
				ret.unshift(diffHour + self.lang.hour);
			if (diffDay != 0)
				ret.unshift(diffDay + self.lang.day);

			self.timer[displayId]['element'].innerText = ret.join(' ');
		},
		getDate : function(date)
		{
			var year = parseInt(date.substring(0, 4), 10);
			var mon = parseInt(date.substring(5, 7) - 1, 10);
			var day = parseInt(date.substring(8, 10), 10);
			var hour = parseInt(date.substring(11, 13), 10);
			var min = parseInt(date.substring(14, 16), 10);
			var sec = parseInt(date.substring(17, 19), 10);

			return new Date(year, mon, day, hour, min, sec);
		}
	};
}();
