// 카테고리 리스트
(function(updateData){
	if (updateData.category) {
		if (_ID("template-category-layer")) {
			var subCategory = false;
			for (var index = 0; index < updateData.category.length; index++) {
				var categoryLayer = _ID("template-category-layer").cloneNode(true);
				categoryLayer.removeAttribute("id");
				if (!updateData.category[index].catnm.match(/img/)) {
					categoryLayer.className = categoryLayer.className + " layer-text-category";
				}
				categoryLayer.style.display = "";
				categoryLayer.innerHTML = categoryLayer.innerHTML.replace("{:category:}", updateData.category[index].category).replace("{:catnm:}", updateData.category[index].catnm);
				var categorySubLayer = findElementsByClassName(categoryLayer, "template-category-sub-layer");
				if (updateData.category[index].sub) {
					subCategory = true;
					categorySubLayer[0].style.display = "";
					var categorySubTemplate = findElementsByClassName(categorySubLayer[0], "template-category-sub");
					for (var _index = 0; _index < updateData.category[index].sub.length; _index++) {
						var categorySub = categorySubTemplate[0].cloneNode(true);
						categorySub.style.display = "";
						categorySub.innerHTML = categorySub.innerHTML.replace("{:subCategory:}", updateData.category[index].sub[_index].category).replace("{:subCatnm:}", updateData.category[index].sub[_index].catnm);
						categorySubTemplate[0].parentNode.appendChild(categorySub);
					}
					categorySubTemplate[0].parentNode.removeChild(categorySubTemplate[0]);
				}
				else {
					categorySubLayer[0].parentNode.removeChild(categorySubLayer[0]);
				}
				_ID("template-category-layer").parentNode.appendChild(categoryLayer);
			}
			if (subCategory) {
				try {
					window[_ID("template-category-layer").getAttribute("data-callback")]();
				}
				catch (e) {}
			}
		}
		if (_ID("template-category-menu")) {
			for (var index = 0; index < updateData.category.length; index++) {
				var categoryMenu = _ID("template-category-menu").cloneNode(true);
				categoryMenu.removeAttribute("id");
				if (!updateData.category[index].catnm.match(/img/)) {
					categoryMenu.className = categoryMenu.className + " menu-text-category";
				}
				categoryMenu.style.display = "";
				categoryMenu.innerHTML = categoryMenu.innerHTML.replace("{:category:}", updateData.category[index].category).replace("{:catnm:}", updateData.category[index].catnm);
				var categorySubMenu = findElementsByClassName(categoryMenu, "template-category-sub-container")[0];
				// standard, one_fine_day 스킨
				if (categorySubMenu) {
					if (updateData.category[index].sub) {
						categorySubMenu.style.display = "";
						var categorySubTemplate = findElementsByClassName(categorySubMenu, "template-category-sub")[0];
						for (var _index = 0; _index < updateData.category[index].sub.length; _index++) {
							var categorySub = categorySubTemplate.cloneNode(true);
							categorySub.style.display = "";
							categorySub.innerHTML = categorySub.innerHTML.replace("{:subCategory:}", updateData.category[index].sub[_index].category).replace("{:subCatnm:}", updateData.category[index].sub[_index].catnm);
							categorySubTemplate.parentNode.appendChild(categorySub);
						}
						categorySubTemplate.parentNode.removeChild(categorySubTemplate);
					}
					else {
						categorySubMenu.parentNode.removeChild(categorySubMenu);
					}
					_ID("template-category-menu").parentNode.appendChild(categoryMenu);
				}
				// 나머지 스킨
				else {
					_ID("template-category-menu").parentNode.appendChild(categoryMenu);
					var _categorySubMenu = findElementsByClassName(document.body, "template-category-sub-container")[0];
					if (_categorySubMenu) {
						categorySubMenu = _categorySubMenu.cloneNode(true);
						if (updateData.category[index].sub) {
							categorySubMenu.style.display = "";
							var categorySubTemplate = findElementsByClassName(categorySubMenu, "template-category-sub")[0];
							for (var _index = 0; _index < updateData.category[index].sub.length; _index++) {
								var categorySub = categorySubTemplate.cloneNode(true);
								categorySub.style.display = "";
								categorySub.innerHTML = categorySub.innerHTML.replace("{:subCategory:}", updateData.category[index].sub[_index].category).replace("{:subCatnm:}", updateData.category[index].sub[_index].catnm);
								categorySubTemplate.parentNode.appendChild(categorySub);
							}
							categorySubTemplate.parentNode.removeChild(categorySubTemplate);
							_categorySubMenu.parentNode.appendChild(categorySubMenu);
						}
					}
				}
			}
		}
	}
})(updateData);