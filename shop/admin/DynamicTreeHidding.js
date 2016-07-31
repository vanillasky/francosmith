DynamicTree.prototype.Hidding = function () {
	function hidding() {
		this.init = function () {
			var img = {
				"eyeOff": "i_cate_eye_off.gif",
				"eyeOn": "i_cate_eye_on.gif" };
			for (var p in img) {
				self.img[p] = self.path + img[p];
			}
			for (var p in img) {
				self.imgObjects.push(new Image());
				self.imgObjects.getLast().src = self.img[p];
				self.img[p] = self.imgObjects.getLast().src;
			}
			if(window.location.pathname.indexOf('/todayshop/category.php') > -1) {
				self.updateurl = "../todayshop/indb.category.php";
			}
			else if(window.location.pathname.indexOf('/shoptouch/shopTouch_category.php') > -1) {
				self.updateurl = "../shoptouch/indb.php";
			}
			else {
				self.updateurl = "../goods/indb.php";
			}
			//self.updateurl = (window.location.pathname.indexOf('/todayshop/category.php') > -1) ? "../todayshop/indb.category.php" : "../goods/indb.php";
		};
		this.eyeHtml = function (obj) {
			if (obj.etcPara.hidden == null) return '';
			if (obj.parentNode.etcPara != null) {
				if (obj.parentNode.etcPara.hidden == '1' || obj.parentNode.etcPara.opacitys == '1') obj.etcPara.opacitys = '1';
			}
			return '&nbsp;<img src="?" align="absmiddle" onload="?.hidding.eyeOpacity(this)">'.format((obj.etcPara.hidden == 1 || obj.etcPara.opacitys == 1 ? self.img.eyeOff : self.img.eyeOn), self.id);
		};
		this.eyeOpacity = function(iObj) {
			var id = iObj.parentNode.id;
			if (self.allNodes[id].parentNode.etcPara != null && (self.allNodes[id].parentNode.etcPara.hidden == '1' || self.allNodes[id].parentNode.etcPara.opacitys == '1')) {
				if ( iObj.src != self.img.eyeOff )iObj.src = self.img.eyeOff;
				self.allNodes[id].etcPara.opacitys = 1;
				if ( iObj['onclick'] != null ) {
					iObj['onclick'] = null;
					iObj.style.cursor='default';
				}
				with (iObj.style) {
					filter = "Alpha(Opacity=30)";
					opacity = "0.3";
				}
			} else {
				icon = (self.allNodes[id].etcPara.hidden == '1' ? self.img.eyeOff : self.img.eyeOn);
				if ( iObj.src != icon )iObj.src = icon;
				self.allNodes[id].etcPara.opacitys = 0;
				if ( iObj['onclick'] == null ) {
					iObj['onclick'] = function(e) { self.hidding.eyeChange(iObj, id, e?e:window.event); };
					iObj.style.cursor='pointer';
				}
				with (iObj.style) {
					filter = "Alpha(Opacity=100)";
					opacity = "1";
				}
			}
		};
		this.eyeChange = function(iObj, id, evt) {
			var hidden = (self.allNodes[id].etcPara.hidden == 1 ? 0 : 1);
			var query = '';
			query += '&hidden=' + hidden;
			query += '&category=' + iObj.parentNode.getElementsByTagName('input')[0].value;
			var urlStr = self.updateurl + "?mode=chgCategoryHidden" + query + "&dummy=" + new Date().getTime();

			var cDivLoad = self.loading(evt);
			var ajax = new Ajax.Request( urlStr,
			{
				method: "get",
				onComplete: function () {
					var req = ajax.transport;
					cDivLoad.parentNode.removeChild( cDivLoad );
					if ( req.status == 200 ) {
						var response = req.responseText;
						if (response == 'OK') {
							self.allNodes[id].etcPara.hidden = hidden;
							iObj.src = (hidden == 1 ? self.img.eyeOff : self.img.eyeOn);
							var imgs = document.getElementById(id+"-section").getElementsByTagName('img');
							for (var i = 0; i < imgs.length; i++) {
								if ( imgs[i].src.match(/eye/) ) self.hidding.eyeOpacity(imgs[i]);
							}
						} else {
							alert(response);
						}
					} else {
						var msg = req.getResponseHeader("Status");
						if ( msg == null || msg.length == null || msg.length <= 0 )
							alert( "Error! Request status is " + req.status );
						else
							alert( msg );
					}
				}
			} );
		};
	}

	var self = this;
	this.hidding = new hidding();
	this.hidding.init();
}