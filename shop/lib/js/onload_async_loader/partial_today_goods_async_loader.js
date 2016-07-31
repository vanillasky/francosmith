// 오늘본 상품
(function(updateData){
	if (updateData.todayGoods) {
		for (var index = 0; index < updateData.todayGoods.length; index++) {
			if (!updateData.todayGoods[index].goodsno) continue;
			var todayGoods = _ID("template-today-goods").cloneNode(true);
			todayGoods.removeAttribute("id");
			todayGoods.children[0].href += updateData.todayGoods[index].goodsno;
			todayGoods.children[0].innerHTML = updateData.todayGoods[index].img;
			if (index === updateData.todayGoods.length - 1) {
				todayGoods.children[1].style.display = "none";
			}
			_ID("gdscroll").appendChild(todayGoods);
			todayGoods.style.display = "";
		}
	}
})(updateData);