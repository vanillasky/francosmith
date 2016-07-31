// 쿠폰발급알림 레이어 박스
(function(updateData){
	if (updateData.myCouponLayer) {
		document.getElementsByClassName("MyCouponBox-name")[0].innerHTML = updateData.member.name;
		for (var index = 0; index < updateData.myCouponLayer.length; index++) {
			var myCouponLayer = _ID("MyCouponBox-info-template").cloneNode(true);
			myCouponLayer.removeAttribute("id");
			_ID("MyCouponBox-info-template").parentNode.appendChild(myCouponLayer);
			document.getElementsByClassName("MyCouponBox-coupon")[index + 1].innerHTML = updateData.myCouponLayer[index].coupon;
			document.getElementsByClassName("MyCouponBox-summary")[index + 1].innerHTML = updateData.myCouponLayer[index].summa;
			myCouponLayer.style.display = "";
		}
		_ID("MyCouponBox").style.display = "";
	}
})(updateData);