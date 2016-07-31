Timer = {
	timers : new Array(),
	itv : new Array(),
	countImgs : new Array(),
	d_countImgs : new Array(),

	initImg: function(imgs,dimgs) {
		for(var i = 0; i < 10; i++) {
			Timer.countImgs[i] = new Image();
			Timer.countImgs[i].src = imgs[i];
		}

		// 디데이 숫자
		if (dimgs) {
			for(var i = 0; i < 10; i++) {
				Timer.d_countImgs[i] = new Image();
				Timer.d_countImgs[i].src = dimgs[i];
			}
		}
	},
	setTimer : function(idx, callback) {
		var rTm = --Timer.timers[idx];
		var rDay = Math.floor(rTm / (24 * 60 * 60));
		rTm -= rDay * (24 * 60 * 60);
		var rHour = Math.floor(rTm / (60 * 60));
		rTm -= rHour * (60 * 60);
		var rMin = Math.floor(rTm / (60));
		rTm -= rMin * (60);
		var rSec = rTm;

		if (rDay == 0 && rHour == 0 && rMin == 0 && rSec == 0) {
			Timer.stopTimer(idx);
			callback(idx, 'closed');
		}

		rHour = (rHour < 10)? "0"+rHour : rHour;
		rMin = (rMin < 10)? "0"+rMin : rMin;
		rSec = (rSec < 10)? "0"+rSec : rSec;
		
		var obj_d = document.getElementById("rTime_d"+idx);
		var obj_h = document.getElementById("rTime_h"+idx);
		var obj_m = document.getElementById("rTime_m"+idx);
		var obj_s = document.getElementById("rTime_s"+idx);
		if (obj_d) {
			var imgs = obj_d.getElementsByTagName("IMG");
			if (imgs.length < String(rDay).length) {
				for(var i = imgs.length; i < String(rDay).length; i++) obj_d.appendChild(document.createElement("IMG"));
			}
			else if (imgs.length > String(rDay).length) {
				for(var i = String(rDay).length; i < imgs.length; i++) imgs[i].removeNode(true);
			}
			for(var i = 0; i < String(rDay).length; i++) {
				var num = String(rDay).substring(i, i+1);
				if (isNaN(num)) {
					imgs[i].style.display = "none";
					continue;
				}
				num = parseInt(num);
				imgs[i].src = (Timer.d_countImgs[num]) ? Timer.d_countImgs[num].src : Timer.countImgs[num].src;
			}
		}
		if (obj_h) {
			var imgs = obj_h.getElementsByTagName("IMG");
			if (imgs.length < String(rHour).length) {
				for(var i = imgs.length; i < String(rHour).length; i++) obj_h.appendChild(document.createElement("IMG"));
			}
			else if (imgs.length > String(rHour).length) {
				for(var i = String(rHour).length; i < imgs.length; i++) imgs[i].removeNode(true);
			}
			for(var i = 0; i < String(rHour).length; i++) {
				var num = String(rHour).substring(i, i+1);
				if (isNaN(num)) {
					imgs[i].style.display = "none";
					continue;
				}
				num = parseInt(num);
				imgs[i].src = Timer.countImgs[num].src;
			}
		}
		if (obj_m) {
			var imgs = obj_m.getElementsByTagName("IMG");
			if (imgs.length < String(rMin).length) {
				for(var i = imgs.length; i < String(rMin).length; i++) obj_m.appendChild(document.createElement("IMG"));
			}
			else if (imgs.length > String(rMin).length) {
				for(var i = String(rMin).length; i < imgs.length; i++) imgs[i].removeNode(true);
			}
			for(var i = 0; i < String(rMin).length; i++) {
				var num = String(rMin).substring(i, i+1);
				if (isNaN(num)) {
					imgs[i].style.display = "none";
					continue;
				}
				num = parseInt(num);
				imgs[i].src = Timer.countImgs[num].src;
			}
		}
		if (obj_s) {
			var imgs = obj_s.getElementsByTagName("IMG");
			if (imgs.length < String(rSec).length) {
				for(var i = imgs.length; i < String(rSec).length; i++) obj_s.appendChild(document.createElement("IMG"));
			}
			else if (imgs.length > String(rSec).length) {
				for(var i = String(rSec).length; i < imgs.length; i++) imgs[i].removeNode(true);
			}
			for(var i = 0; i < String(rSec).length; i++) {
				var num = String(rSec).substring(i, i+1);
				if (isNaN(num)) {
					imgs[i].style.display = "none";
					continue;
				}
				num = parseInt(num);
				imgs[i].src = Timer.countImgs[num].src;
			}
		}
	},
	getTimer : function(idx, startDt, startTm, closeDt, closeTm, callback) {
		new Ajax.Request("../admin/todayshop/todayshop_timer.php", {
			method: "post",
			parameters: "startDt="+startDt+"&startTm="+startTm+"&closeDt="+closeDt+"&closeTm="+closeTm,
			onSuccess: function(req) {
				try {
					eval('var res = '+req.responseText);
					callback(idx, res.status);
					if (res.status == 'ing') {
						Timer.timers[idx] = ++res.remainTm;
						Timer.setTimer(idx, callback);
						if (Timer.itv[idx]) Timer.stopTimer(idx);
						Timer.itv[idx] = setInterval(function() {Timer.setTimer(idx, callback); }, 1000);
						setTimeout(function() {Timer.getTimer(idx, startDt, startTm, closeDt, closeTm, callback); }, 1000 * 60 * 2);
					}
				}
				catch(e) {
					//alert("Timer.getTimer() Exception");
					alert("상품 정보 로딩 중 오류가 발생하였습니다. (Timer.getTimer())");
				}
			},
			onFailure: function() { }
		});
	},
	stopTimer : function(idx) {
		clearInterval(Timer.itv[idx]);
		Timer.itv[idx] = null;
	}
}

MainImage = {
	defaultImgs : new Array(),
	overImgs : new Array(),
	initImg : function(imgs) {
		for(var i = 1; i < imgs.length; i++) {
			MainImage.defaultImgs[i] = new Image();
			MainImage.defaultImgs[i] = imgs[i].def;
			MainImage.overImgs[i] = new Image();
			MainImage.overImgs[i] = imgs[i].over;
		}
	},
	show : function(n) {
		var imgobjs = document.getElementById("goodsMainImg").getElementsByTagName("IMG");
		var numobjs = document.getElementById("goodsMainImgNum").getElementsByTagName("IMG");
		for(var i = 0; i < imgobjs.length; i++) {
			if (i == n) {
				imgobjs[i].style.display = "block";
				numobjs[i].src = MainImage.overImgs[i+1];
			}
			else {
				imgobjs[i].style.display = "none";
				numobjs[i].src = MainImage.defaultImgs[i+1];
			}
		}
	}
}

TodayShop = {
	getGoodsData : function(tgsno, callback) {
		new Ajax.Request("../todayshop/indb.pageinit.php", {
			method: "get",
			parameters: "mode=todaygoods&tgsno="+tgsno,
			onSuccess: function(req) {
				try {
					eval('var res = '+req.responseText);
					callback(res);
				}
				catch(e) {
					//alert("TodayShop.getGoodsData() Exception");
					alert("상품 정보 로딩 중 오류가 발생하였습니다. (TodayShop.getGoodsData())");
				}
			},
			onFailure: function() { }
		});
	},
	getListData : function(category, year, month, day, callback) {
		new Ajax.Request("../todayshop/indb.pageinit.php", {
			method: "get",
			parameters: "mode=todaylist&year="+year+"&month="+month+"&day="+day+"&category="+category,
			onSuccess: function(req) {
				try {
					eval('var res = '+req.responseText);
					callback(res);
				}
				catch(e) {
					//alert("TodayShop.getListData() Exception");
					alert("상품 정보 로딩 중 오류가 발생하였습니다. (TodayShop.getListData())");
				}
			},
			onFailure: function() { }
		});
	},
	getCalData : function(callback) {
		new Ajax.Request("../todayshop/indb.pageinit.php", {
			asynchronous: "false",
			method: "get",
			parameters: "mode=calendar",
			onSuccess: function(req) {
				try {
					eval('var res = '+req.responseText);
					callback(res);
				}
				catch(e) {
					//alert("TodayShop.getCalData() Exception");
					alert("상품 정보 로딩 중 오류가 발생하였습니다. (TodayShop.getCalData())");
				}
			},
			onFailure: function() { }
		});
	}
}