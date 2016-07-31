// 마이페이지 레이어박스
(function(updateData){
	if (updateData.member) {
		if (_ID("MypageLayerBox-name") && updateData.member.name) _ID("MypageLayerBox-name").innerHTML = updateData.member.name;
		if (_ID("MypageLayerBox-grpnm") && updateData.member.grpnm) _ID("MypageLayerBox-grpnm").innerHTML = updateData.member.grpnm;
		if (_ID("MypageLayerBox-sum-sale") && updateData.member.sumSale) _ID("MypageLayerBox-sum-sale").innerHTML = updateData.member.sumSale;
		if (_ID("MypageLayerBox-emoney") && updateData.member.emoney) _ID("MypageLayerBox-emoney").innerHTML = updateData.member.emoney;
		if (_ID("MypageLayerBox-coupon-count") && updateData.member.couponCount) _ID("MypageLayerBox-coupon-count").innerHTML = updateData.member.couponCount;
		if (_ID("MypageLayerBox-cart-count") && updateData.member.cartCount) _ID("MypageLayerBox-cart-count").innerHTML = updateData.member.cartCount;
		if (_ID("MypageLayerBox-wish-count") && updateData.member.wishCount) _ID("MypageLayerBox-wish-count").innerHTML = updateData.member.wishCount;
	}
})(updateData);