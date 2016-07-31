if (updateData.naverCommonInflowScriptParam) {

	var NCISP = updateData.naverCommonInflowScriptParam;

	if (!window.wcs_add) window.wcs_add = new Object();
	window.wcs_add["wa"] = NCISP.AccountID;

	if (NCISP.WhiteList) {
		wcs.checkoutWhitelist = NCISP.WhiteList;
		wcs.mileageWhitelist = NCISP.WhiteList;
	}

	wcs.setReferer(decodeURIComponent(NCISP.Referer));

	if (NCISP.Inflow) wcs.inflow(NCISP.Inflow);
	else wcs.inflow();

	if (NCISP.Path==="goods/goods_view.php" || NCISP.Path==="goods/view.php" || NCISP.Path==="goods/view_detail.php" || NCISP.Path==="order/order.php") {
		var
		naverMileageBaseAccumRate = wcs.getBaseAccumRate().toString().trim(),
		naverMileageAddAccumRate = wcs.getAddAccumRate().toString().trim();
		if (parseFloat(naverMileageBaseAccumRate)>0) {
			if (document.getElementById("naver-mileage-base-accum-rate")) document.getElementById("naver-mileage-base-accum-rate").innerHTML = naverMileageBaseAccumRate.replace(/\.0$/, "")+"%";
			if (parseFloat(naverMileageAddAccumRate)>0) {
				if (document.getElementById("naver-mileage-add-accum-rate")) document.getElementById("naver-mileage-add-accum-rate").innerHTML = "&nbsp;+&nbsp;추가&nbsp;"+naverMileageAddAccumRate.replace(/\.0$/, "")+"%";
			}
		}
		// 2013-09-02, 네이버ON 패치
		var naverMileageAccumElement = document.getElementById("naver-mileage-accum");
		if (wcs.getMileageInfo()) {
			var
			naverMileageAccumRate = (parseFloat(naverMileageBaseAccumRate) + parseFloat(naverMileageAddAccumRate)).toString(),
			naverMileageAccumRateElement = document.getElementById("naver-mileage-accum-rate");
			if (naverMileageAccumElement) {
				naverMileageAccumElement.style.display = "";
				if (naverMileageAccumRateElement) {
					naverMileageAccumRateElement.innerHTML = naverMileageAccumRate.replace(/\.0$/, "")+"%";
				}
			}
		}
		else {
			if (naverMileageAccumElement) {
				naverMileageAccumElement.parentNode.removeChild(naverMileageAccumElement);
			}
		}
	}

	if (NCISP.Path==="setGoods/goodsView/goodsView.php" || NCISP.Path==="goods/list.php" || NCISP.Path==="goods/cart.php" || NCISP.Path==="goods/view.php" || NCISP.Path==="ord/order.php") {console.log("ok!");
		var
		naverMileageBaseAccumRate = wcs.getBaseAccumRate().toString().trim(),
		naverMileageAddAccumRate = wcs.getAddAccumRate().toString().trim();
		if (parseFloat(naverMileageBaseAccumRate)>0) {
			jQuery(".naver-mileage-base-accum-rate").each(function(index, element){
				element.innerHTML = naverMileageBaseAccumRate.replace(/\.0$/, "")+"%";
			});
			if (parseFloat(naverMileageAddAccumRate)>0) {
				jQuery(".naver-mileage-add-accum-rate").each(function(index, element){
					element.innerHTML = "&nbsp;+&nbsp;추가&nbsp;"+naverMileageAddAccumRate.replace(/\.0$/, "")+"%";
				});
			}
		}
		// 2013-09-02, 네이버ON 패치
		if (wcs.getMileageInfo()) {
			var
			naverMileageAccumRate = (parseFloat(naverMileageBaseAccumRate) + parseFloat(naverMileageAddAccumRate)).toString();
			jQuery(".naver-mileage-accum").each(function(index, accumElement){
				jQuery(accumElement).show().find(".naver-mileage-accum-rate").each(function(index, element){
					element.innerHTML = naverMileageAccumRate.replace(/\.0$/, "")+"%";
				});
			});
		}
		else {
			jQuery(".naver-mileage-accum").remove();
		}
	}

	if(NCISP.AccountID && wcs.isCPA)
	{
		// CPA 스크립트(주문완료 페이지에서만 실행)
		if (NCISP.Path==="order/order_end.php" || NCISP.Path==="ord/order_end.php") {
			var
			_nao = new Object(),
			commonScriptOrderItem = document.getElementsByName("naver-common-inflow-script-order-item");

			// CPA로 전송할 상품이 있다면(CPA 수집동의시 자동생성)
			if (commonScriptOrderItem.length>0) {
				_nao["order"] = new Array();
				for (var a=0; a<commonScriptOrderItem.length; a++) {
					var
					orderItem = eval("("+commonScriptOrderItem[a].value+")"),
					cpaOrderItem = new Object();

					cpaOrderItem["oid"] = orderItem.ordno;
					cpaOrderItem["poid"] = orderItem.sno;
					if (orderItem.is_parent) {
						cpaOrderItem["pid"] = orderItem.goodsno;
						cpaOrderItem["parpid"] = null;
					}
					else {
						cpaOrderItem["pid"] = orderItem.goodsno.toString()+"-"+orderItem.optno.toString();
						cpaOrderItem["parpid"] = orderItem.goodsno;
					}
					cpaOrderItem["name"] = orderItem.goodsnm;
					cpaOrderItem["cnt"] = orderItem.ea;
					cpaOrderItem["price"] = orderItem.price;

					_nao["order"].push(cpaOrderItem);
				}
				_nao["chn"] = "AD";
				wcs.CPAOrder(_nao);
			}
		}
	}

	if (NCISP.AccountID) {
		if (NCISP.Path === "order/order_end.php" || NCISP.Path === "ord/order_end.php") {
			var commonScriptOrder = document.getElementById("naver-common-inflow-script-order");
			if (commonScriptOrder && commonScriptOrder !== null) {
				var
				saNao = new Object(),
				serviceType = "1",
				orderInfo = eval("("+commonScriptOrder.value+")");
				saNao["cnv"] = wcs.cnv(serviceType, orderInfo.goodsprice);
				wcs_do(saNao);
			}
		}
		else {
			wcs_do();
		}
	}
}