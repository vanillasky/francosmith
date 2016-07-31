// PC팝업 출력
(function(updateData){
	if (updateData.popup) {
		for (var index = 0; index < updateData.popup.length; index++) {
			var popup = updateData.popup[index];
			if (popup.type === "layer") {
				var popupLayer = _ID("template-popup-layer").cloneNode(true);
				popupLayer.setAttribute("id", "blnCookie_" + popup.code);
				popupLayer.style.width = popup.width;
				popupLayer.style.height = popup.height;
				popupLayer.style.left = popup.left;
				popupLayer.style.top = popup.top;
				popupLayer.style.display = "";
				popupLayer.innerHTML = popup.content;
				document.body.appendChild(popupLayer);
			}
			if (popup.type === "layerMove") {
				var popupMoveLayer = _ID("template-popup-move-layer").cloneNode(true);
				popupMoveLayer.setAttribute("id", "blnCookie_" + popup.code);
				popupMoveLayer.style.width = popup.width;
				popupMoveLayer.style.height = popup.height;
				popupMoveLayer.style.left = popup.left;
				popupMoveLayer.style.top = popup.top;
				popupMoveLayer.style.display = "";
				popupMoveLayer.innerHTML = popup.content;
				popupMoveLayer.onmousedown = function(event)
				{
					event = event || window.event;
					Start_move(event, "blnCookie_" + popup.code);
				};
				popupMoveLayer.onmouseup = Moveing_stop;
				document.body.appendChild(popupMoveLayer);
			}
			if (!popup.type) {
				var property = "width=" + popup.width + ", height=" + popup.height + ", top=" + popup.top + ", left=" + popup.left + ", scrollbars=no, toolbar=no";
				var win = window.open("./html.php?htmid=" + popup.file + "&code=blnCookie_" + popup.code, popup.code, property);
				if(win) win.focus();
			}
		}
	}
})(updateData);

// 모바일 팝업 출력
(function(updateData){
	if (updateData.mobilePopup) {
		if (!$.cookie("popup_" + updateData.mobilePopup.mpopup_no)) {
			var mobilePopup = jQuery("#template-mobile-popup").clone();
			mobilePopup.attr("id", "popup");
			if (updateData.mobilePopup.link_url) {
				jQuery(mobilePopup).find(".popup_content").click(function(){
					location.href = "http://" + updateData.mobilePopup.link_url;
				});
			}
			if (updateData.mobilePopup.popup_type === "0") {
				jQuery(mobilePopup).find(".popup_content").html(updateData.mobilePopup.popup_img);
			}
			else {
				jQuery(mobilePopup).find(".popup_content").html(updateData.mobilePopup.popup_body);
			}
			jQuery(mobilePopup).find(".btn-today-close").click(function(){
				closeTodayPop(updateData.mobilePopup.mpopup_no);
			});
			jQuery(mobilePopup).find(".btn-close").click(function(){
				closePop();
			});
			mobilePopup.css("display", "");
			jQuery("#template-mobile-popup").after(mobilePopup);
			jQuery("#background").css("display", "");
		}
	}
})(updateData);