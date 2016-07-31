//<script type="text/javascript">
(function(pageUpdater){
	if (window.jQuery) {
		jQuery(document).ready(pageUpdater);
	}
	else {
		addOnloadEvent(pageUpdater);
	}
})(function(){
	var shopRoot = _ID("page-updater").getAttribute("data-shop-root");
	var referer = _ID("page-updater").getAttribute("data-referer");
	var scriptName = _ID("page-updater").getAttribute("data-script");

	if (!document.getElementsByClassName) {
		document.getElementsByClassName = function(className)
		{
			var allElements;
			if (document.all) {
				allElements = document.all;
			}
			else {
				allElements = document.getElementsByTagName("*");
			}
			var foundElements = new Array();
			for (var index = 0; index < allElements.length; index++) {
				if (allElements[index].className === className) {
					foundElements.push(allElements[index]);
				}
			}
			return foundElements;
		};
	}

	var findElementsByClassName = function(element, className)
	{
		var foundElements = new Array();
		for (var index = 0; index < element.children.length; index++) {
			if (element.children[index].className.match(new RegExp("\s*(" + className + ")\s*"))) {
				foundElements.push(element.children[index]);
			}
			if (element.children[index].children.length > 0) {
				var _foundElements = findElementsByClassName(element.children[index], className);
				for (var _index = 0; _index < _foundElements.length; _index++) {
					foundElements.push(_foundElements[_index]);
				}
			}
		}
		return foundElements;
	};

	// 비동기 처리가 필요한 항목 리스팅
	var asyncList = new Array();
	var asyncParameter = new Array();
	if (!_ID("naver-common-inflow-script")) {
		asyncList.push("naverCommonInflowScriptParam");
	}
	if (_ID("template-popup-layer")) {
		asyncList.push("popupLayer");
	}
	if (_ID("template-popup-move-layer")) {
		asyncList.push("popupMoveLayer");
	}
	if (_ID("template-mobile-popup")) {
		asyncList.push("popupMobile");
	}
	if (document.getElementsByClassName("user-status-login").length || document.getElementsByClassName("user-status-logout").length) {
		asyncList.push("userStatus");
	}
	if (_ID("MypageLayerBox-grpnm")) {
		asyncList.push("mypageLayerGroupName");
	}
	if (_ID("MypageLayerBox-sum-sale")) {
		asyncList.push("mypageLayerSumSale");
	}
	if (_ID("MypageLayerBox-emoney")) {
		asyncList.push("mypageLayerEmoney");
	}
	if (_ID("MypageLayerBox-coupon-count")) {
		asyncList.push("mypageLayerCouponCount");
	}
	if (_ID("MypageLayerBox-cart-count")) {
		asyncList.push("mypageLayerCartCount");
	}
	if (_ID("MypageLayerBox-wish-count")) {
		asyncList.push("mypageLayerWishCount");
	}
	if (_ID("MyLevelLayerBox") && _ID("MyLevelLayerBox").style.display === "none") {
		asyncList.push("myLevelLayer");
	}
	if (_ID("MyCouponBox") && _ID("MyCouponBox").style.display === "none" && getCookie("cache_csno")) {console.log(getCookie("cache_csno"));
		asyncList.push("myCouponLayer");
	}
	if (_ID("template-today-goods")) {
		asyncList.push("todayGoods");
		asyncParameter.push("todayGoods[imageSize]=" + _ID("template-today-goods").getAttribute("data-size"));
	}
	if (_ID("template-category-layer") || _ID("template-category-menu")) {
		asyncList.push("category");
	}

	// 리스팅된 항목들 일괄처리
	if (asyncList.length > 0) {
		(function(ajaxParam){
			if (window.jQuery) {
				jQuery.ajax(ajaxParam);
			}
			else {
				ajaxParam.param = ajaxParam.data;
				gd_ajax(ajaxParam);
			}
		})({
			"url" : shopRoot + "/proc/onload_async_loader.php",
			"type" : "get",
			"data" : "rf=" + encodeURIComponent(referer) + "&sn=" + encodeURIComponent(scriptName) + "&schedule=" + asyncList.join(",") + "&" + asyncParameter.join("&"),
			"success" : function(responseText)
			{
				var updateData = eval("(" + responseText + ")");

				<?php
				// 네이버 공통유입 스크립트
				@include dirname(__FILE__).'/onload_async_loader/partial_naver_common_inflow_script_async_loader.js';

				// 메인 팝업창
				include dirname(__FILE__).'/onload_async_loader/partial_popup_async_loader.js';

				// 회원 로그인상태에 따른 메뉴표시
				include dirname(__FILE__).'/onload_async_loader/partial_user_status_async_loader.js';

				// 마이페이지 레이어박스
				include dirname(__FILE__).'/onload_async_loader/partial_mybox_async_loader.js';

				// 쿠폰발급알림 레이어 박스
				include dirname(__FILE__).'/onload_async_loader/partial_coupon_layer_async_loader.js';

				// 등급변경알림 레이어 박스
				include dirname(__FILE__).'/onload_async_loader/partial_level_layer_async_loader.js';

				// 오늘본 상품
				include dirname(__FILE__).'/onload_async_loader/partial_today_goods_async_loader.js';

				// 카테고리 리스트
				include dirname(__FILE__).'/onload_async_loader/partial_category_async_loader.js';
				?>
			}
		});
	}
});
//</script>