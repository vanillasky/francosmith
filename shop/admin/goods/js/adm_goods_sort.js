/**
 * 상품 검색 및 진열 폼 컨트롤 객체
 * @class GoodsSortController
 * @author adm_goods_sort.js workingparksee <parksee@godo.co.kr>
 * @version 1.0
 * @date 2013-06-03, 2013-09-25
 */
var GoodsSortController = function()
{
	// Object
	var self = this;

	// Goods search form
	var
	$goodsSearchForm = jQuery("#goods-search-form"),
	goodsSearchForm = $goodsSearchForm[0],
	$imageSize = $goodsSearchForm.find("[name=imageSize]"),
	$limitRows = $goodsSearchForm.find("[name=limitRows]"),
	$listDisplayOption = jQuery("#list-display-option");

	// Goods sort form
	var
	$goodsSortForm = jQuery("#goods-sort-form"),
	goodsSortForm = $goodsSortForm[0],
	$cancelModified = jQuery("#cancel-modified");

	// Goods content
	var
	$goodsContent = jQuery("#goods-content"),
	goodsViewType = null,
	goodsSortType = null;

	// Goods list
	var
	$goodsContainer = $goodsContent.find(".body"),
	goodsContainer = $goodsContainer[0],
	goodsList = goodsContainer.children;

	// Page
	var
	$pageRow = $goodsContent.find(".foot"),
	pageRow = $pageRow[0],
	currentCategory = null,
	currentPage = 1,
	nextPage = null,
	previousPage = null,
	totalPage = null,
	pageOffset = null;

	// Template
	var
	resultRowTemplate = null,
	goodsRowTemplate = null,
	goodsRowOptionTemplate = null,
	soldoutImageTemplate = null,
	soldoutImage = null,
	pageAnchorTemplate = null,
	prevPageAnchorTemplate = null,
	nextPageAnchorTemplate = null,
	activePageAnchorTemplate = null,
	goodsSortTypeDescriptionTemplate = null,
	moveSelectionBoxTemplate = null;

	// Data
	var
	originalGoods = null,
	modifiedSortSet = new Object(),
	modifiedOpenSet = new Object();

	// Selection
	var
	selectionGoodsList = new Array(),
	selectionStartIndex = null,
	selectionEndIndex = null,
	selectionType = null;

	/**
	 * 생성자
	 * @constructor
	 */
	var __construct = function()
	{
		// 템플릿 등록
		registTemplate();

		// 기본적 뷰 타입은 리스트 형 보기
		self.displayAsViewType("LIST");

		// 상품 컨테이너에 jQuery UI sortable 위젯 활성화
		$goodsContainer.sortable({
			"items" : "li",
			"cancel" : "li:not(.selected)",
			"cursor" : "move",
			"cursorAt" : {
				"left" : 5,
				"top" : 5
			},
			"start" : function(event, ui)
			{
				var $description = jQuery(document.createElement("div")).attr("id", "on-drag-description").text(selectionGoodsList.length+"개 상품 선택 됨");
				jQuery(ui.item[0]).addClass("on-drag").append($description);
				for(var index = 0; index < selectionGoodsList.length; index++) {
					if (ui.item[0] !== selectionGoodsList[index]) selectionGoodsList[index].style.display = "none";
				}
			},
			"stop" : function(event, ui)
			{
				jQuery("#on-drag-description").remove();
				jQuery(ui.item[0]).removeClass("on-drag");
				var position = parseInt(ui.item.index()), originPosition;
				$goodsContainer.sortable("cancel");
				originPosition = parseInt(ui.item.index());
				if (position === originPosition) {
					for(var index = 0; index < selectionGoodsList.length; index++) {
						if (ui.item[0] !== selectionGoodsList[index]) selectionGoodsList[index].style.display = "";
					}
					return false;
				}
				else {
					if (position > originPosition) position -= (selectionGoodsList.length - 1);
					self.moveSelection(position);
					for(var index = 0; index < selectionGoodsList.length; index++) {
						if (ui.item[0] !== selectionGoodsList[index]) selectionGoodsList[index].style.display = "";
					}
				}
			}
		});
		
		// 리스트 보기 옵션 설정값 선택
		self.displayAsViewType(goodsSearchForm.defaultViewType.value);
		self.setListImageSize(goodsSearchForm.defaultImageSize.value);
		$goodsSearchForm.find("[name=limitRows]").val(goodsSearchForm.defaultLimitRows.value);

		// 이벤트 별로 기능 바인딩
		goodsSearchForm.onsubmit = self.searchGoods;
		goodsSortForm.onsubmit = self.save;
		$goodsSearchForm.find("[name=viewType]").each(function(index, element){
			element.onclick = function()
			{
				self.displayAsViewType(this.value);
			};
		});
		$limitRows.bind("change", function(){
			if (currentCategory) self.fetchGoodsListByCategory(currentCategory, 1);
		});
		$imageSize.bind("change", function(){
			self.setListImageSize(jQuery(this).val());
		});
		$cancelModified.bind("click", self.cancelModified);
		jQuery(".move-selection-box").html(moveSelectionBoxTemplate.evaluate()).find(".move-selection").bind("change", self.moveSelectionChangeHandler);
		jQuery("#optimize-manual-sort").bind("click", self.optimizeManualSort);
		jQuery("#optimize-manual-sort-button").bind("click", self.optimizeManualSort);
		jQuery("#save-list-display-option").bind("click", self.saveConfig);
		jQuery(document.body).bind("keydown", self.keyHandler);
	};

	/**
	 * 템플릿 등록
	 * @method registTemplate
	 * @author workingparksee <parksee@godo.co.kr>
	 * @date 2013-06-03, 2013-06-03
	 */
	var registTemplate = function()
	{
		resultRowTemplate = new Template(document.getElementById("result-row-template").innerHTML);
		goodsRowTemplate = new Template(document.getElementById("goods-row-template").innerHTML.replace(/>\s+</g, "><").replace(/\s+</, "<").replace(/>\s+/, ">"));
		goodsRowOptionTemplate = new Template(document.getElementById("goods-row-option-template").innerHTML);
		soldoutImageTemplate = new Template(document.getElementById("soldout-image-template").innerHTML);
		pageAnchorTemplate = new Template(document.getElementById("page-anchor-template").innerHTML.replace(/\s+</, "<").replace(/>\s+/, ">"));
		prevPageAnchorTemplate = new Template(document.getElementById("prev-page-anchor-template").innerHTML.replace(/\s+</, "<").replace(/>\s+/, ">"));
		nextPageAnchorTemplate = new Template(document.getElementById("next-page-anchor-template").innerHTML.replace(/\s+</, "<").replace(/>\s+/, ">"));
		activePageAnchorTemplate = new Template(document.getElementById("active-page-anchor-template").innerHTML.replace(/\s+</, "<").replace(/>\s+/, ">"));
		goodsSortTypeDescriptionTemplate = new Template(document.getElementById("goods-sort-type-description-template").innerHTML);
		soldoutImage = soldoutImageTemplate.evaluate({"tplSkin" : self.getTplSkin()});
		moveSelectionBoxTemplate = new Template(document.getElementById("move-selection-box-template").innerHTML);
	};

	/**
	 * 지정된 URL에 HTTP요청을 보냄
	 * @method httpRequest
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {String} mode 요청종류
	 * @param {Object} dataObject 전달데이터
	 * @param {Object} option 추가적으로 지정할 jQuery.ajax 옵션
	 * @date 2013-06-03, 2013-06-03
	 */
	var httpRequest = function(mode, dataObject, option)
	{
		dataObject.mode = mode;
		option = option || new Object();
		option.url = "adm_goods_sort.indb.php";
		option.data = dataObject;
		option.type = "post";
		option.dataType = "json";
		jQuery.ajax(option);
	};

	/**
	 * 상품검색폼에 선택된 카테고리를 사용하여 상품조회
	 * @method searchGoods
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {Boolean}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.searchGoods = function()
	{
		var category = this["category[]"][0].value;
		for (var index = 0; index < this["category[]"].length; index++) {
			if (this["category[]"][index].value.length) category = this["category[]"][index].value;
		}
		if (!category) alert("카테고리를 선택해 주세요.");

		self.fetchGoodsListByCategory(category, 1);
		return false;
	};

	/**
	 * 저장 후 리스트 갱신
	 * @method save
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {Boolean}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.save = function()
	{
		self.applyModified() && self.refresh();
		return false;
	};

	/**
	 * 리스트에 메시지 출력
	 * @method printMessage
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {String} message
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.printMessage = function(message)
	{
		goodsContainer.innerHTML = resultRowTemplate.evaluate({"message" : message});
		pageRow.innerHTML = "";
	};

	/**
	 * 리스트형 또는 갤러리형으로 뷰타입 지정
	 * @method displayAsViewType
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {String} viewType LIST, GALLERY
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.displayAsViewType = function(viewType)
	{
		if (viewType === "LIST") this.displayAsViewList();
		else this.displayAsViewGallery();
	};

	/**
	 * 리스트형으로 뷰타입 지정
	 * @method displayAsViewList
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.displayAsViewList = function()
	{
		goodsViewType = "LIST";
		jQuery("#view-type-list")[0].checked = true;
		$goodsContent.removeClass("view-gallery").addClass("view-list");
		$goodsContainer.find(".data .image a").css({"cursor" : "auto"}).each(function(index, element){
			element.onclick = function(event)
			{
				event = event || window.event;
				if (event.stopPropagation) event.stopPropagation();
				else event.cancelBubble = true;
			};
		});
		$listDisplayOption.addClass("list");
		self.setListImageSize(self.getListImageSize());
	};

	/**
	 * 갤러리형으로 뷰타입 지정
	 * @method displayAsViewGallery
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.displayAsViewGallery = function()
	{
		goodsViewType = "GALLERY";
		jQuery("#view-type-gallery")[0].checked = true;
		$goodsContent.removeClass("view-list").addClass("view-gallery");
		$goodsContainer.find(".data .image a").css({"cursor" : "default"}).each(function(index, element){
			element.onclick = function(event)
			{
				event = event || window.event;
				event.cancelBubble = false;
				return false;
			};
		});
		$listDisplayOption.removeClass("list");
	};

	/**
	 * 상품이미지 셀렉트박스의 값을 반환
	 * @method getListImageSize
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {String}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.getListImageSize = function()
	{
		return $imageSize.val();
	};

	/**
	 * 상품출력수 셀렉트박스의 값을 반환
	 * @method getLimitRows
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {String}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.getLimitRows = function()
	{
		return $limitRows.val();
	};

	/**
	 * 사용하는 스킨명 반환
	 * @method getTplSkin
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {String}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.getTplSkin = function()
	{
		return goodsSortForm.tplSkin.value;
	};

	/**
	 * 상품레코드를 받아 상품리스트 생성
	 * @method makeGoodsList
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {Array} goodsRecord 상품레코드
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.makeGoodsList = function(goodsRecord)
	{
		if (goodsRecord[0]) pageOffset = parseInt(goodsRecord[0]._no);
		else pageOffset = 1;
		goodsContainer.setAttribute("start", pageOffset);
		goodsContainer.innerHTML = this.makeGoodsHTML(goodsRecord);
		$goodsContainer.children().bind("click", this.selectHandler).find(".name a, select").bind("click", function(event){
			event = event || window.event;
			if (event.stopPropagation) event.stopPropagation();
			else event.cancelBubble = true;
		});
	};

	/**
	 * 상품데이터를 받아 HTML로 만들어 반환
	 * @method makeGoodsHTML
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {Object} goodsRecord 상품레코드
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.makeGoodsHTML = function(goodsData)
	{
		var goodsHTML = "";
		for (var index = 0; index < goodsData.length; index++) {
			goodsHTML += goodsRowTemplate.evaluate({
				"_no" : goodsData[index]._no,
				"index" : index,
				"sno" : goodsData[index].sno,
				"goodsno" : goodsData[index].goodsno,
				"imageTag" : goodsData[index].imageTag,
				"goodsnm" : goodsData[index].goodsnm,
				"option" : this.makeGoodsOptionText(goodsData[index].optnm, goodsData[index].option),
				"soldoutImage" : goodsData[index].soldout === "1" ? soldoutImage : "",
				"open" : goodsData[index].open,
				"open1Selected" : (goodsData[index].open === "1" ? ' selected="selected"' : ""),
				"open2Selected" : (goodsData[index].open === "0" ? ' selected="selected"' : ""),
				"sellstock" : comma(goodsData[index].totstock),
				"realstock" : "",
				"price" : comma(goodsData[index].price),
				"sort" : goodsData[index].sort
			});
		}
		return goodsHTML;
	};

	/**
	 * 옵션명과 옵션값 리스트를 받아 상품리스트에 사용할 옵션텍스트를 반환
	 * @method makeGoodsOptionText
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {String} optionName
	 * @param {Array} option
	 * @returns {String}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.makeGoodsOptionText = function(optionName, option)
	{
		var optionText = new Array();
		if (option) {
			for (var index = 0; index < option.length; index++) {
				if (option[index].length) optionText.push(goodsRowOptionTemplate.evaluate({"optionName" : optionName[index], "optionValues" : option[index].join(", ")}));
			}
		}
		return optionText.join();
	};

	/**
	 * 페이지객체를 받아 페이지 네비게이션을 생성
	 * @method makePage
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {Object} pageList
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.makePage = function(pageList)
	{
		totalPage = pageList.totalpage;
		pageRow.innerHTML = "";

		if (pageList.prev) {
			pageRow.innerHTML += prevPageAnchorTemplate.evaluate({"pageNum" : pageList.prev});
			if (currentPage - 1 === pageList.prev) previousPage = currentPage - 1;
		}

		for (var index = 0; index < pageList.page.length; index++) {
			if (pageList.page[index] === pageList.nowpage) {
				pageRow.innerHTML += activePageAnchorTemplate.evaluate({"pageNum" : pageList.page[index]});
			}
			else {
				pageRow.innerHTML += pageAnchorTemplate.evaluate({"pageNum" : pageList.page[index]});
			}

			if (currentPage - 1 === pageList.page[index]) previousPage = currentPage - 1;
			if (currentPage + 1 === pageList.page[index]) nextPage = currentPage + 1;
		}

		if (pageList.next) {
			pageRow.innerHTML += nextPageAnchorTemplate.evaluate({"pageNum" : pageList.next});
			if (currentPage + 1 === pageList.next) nextPage = currentPage + 1;
		}

		$pageRow.find(".page").click(function(){
			var pageNum = parseInt(this.getAttribute("data-page"));
			self.movePage(pageNum);
		});
	};

	/**
	 * 상품진열 순서를 최적화
	 * @method optimizeManualSort
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.optimizeManualSort = function()
	{
		httpRequest("optimizeManualSort", {"category" : currentCategory}, {
			"beforeSend" : function()
			{
				self.printMessage("["+self.getCategoryName()+"] 카테고리의 상품진열을 최적화 중입니다.");
			},
			"success" : function(responseObject)
			{
				if (responseObject.result === true) {
					alert("상품진열 최적화가 완료되었습니다.");
					self.refresh();
				}
				else if (responseObject.result.match(/^ERROR_/)) {
					alert("상품진열을 최적화하는 동안 에러가 발생하였습니다.\r\n에러코드 : "+responseObject.result);
					throw new Error();
				}
				else {
					alert("상품진열을 최적화하는 동안 에러가 발생하였습니다.");
					throw new Error();
				}
			},
			"error" : function(xmlHttpRequest)
			{
				self.printMessage("상품진열을 최적화하는 동안 에러가 발생하였습니다.");
			}
		});
	};

	/**
	 * 카테고리, 페이지 정보를 통해 카테고리에있는 상품리스트를 조회 후 callback을 실행
	 * @method fetchGoodsListByCategory
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {String} category
	 * @param {String|Number} page
	 * @param {Function} callback
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-09-25
	 */
	this.fetchGoodsListByCategory = function(category, page, callback)
	{
		currentCategory = category, currentPage = parseInt(page), previousPage = null, nextPage = null, totalPage = null;
		modifiedSortSet = new Object(), modifiedOpenSet = new Object();
		this.cleanSelection();
		httpRequest("fetchGoodsListByCategory", {"category" : category, "page" : page, "limit" : self.getLimitRows()}, {
			"beforeSend" : function()
			{
				self.printMessage("상품리스트를 받아오는 중입니다.");
			},
			"success" : function(responseObject)
			{
				if (!callback) callback = new Object();
				if (callback.success && !callback.success(responseObject)) {
					return false;
				}

				self.displayAsSortType(responseObject.category.sortType, responseObject.category.manualSortOnLinkGoodsPosition);
				self.displayAsViewType(goodsViewType);

				if (responseObject.record.length < 1) {
					self.printMessage("등록된 상품이 없습니다.");
					return true;
				}

				originalGoods = responseObject.record;
				self.makeGoodsList(responseObject.record);
				self.makePage(responseObject.page);

				if (callback.complete) callback.complete();
			},
			"error" : function(xmlHttpRequest)
			{
				self.printMessage("상품리스트를 받아오는 동안 에러가 발생하였습니다.");
			}
		});
	};

	/**
	 * 현재 검색된 카테고리 내에서 지정된 페이지로 이동
	 * @method movePage
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {String|Number} page
	 * @returns {Boolean}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.movePage = function(page)
	{
		if (self.checkModified()) {
			if(!confirm("작업된 내용을 저장하신 후에 선택상품을\r\n다른 페이지로 이동 할 수 있습니다.\r\n작업된 내용을 저장하시겠습니까?")) return false;
			else self.applyModified();
		}
		self.fetchGoodsListByCategory(currentCategory, page);
	};

	/**
	 * 현재 검색된 카테고리의 이름을 반환
	 * @method getCategoryName
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {String}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.getCategoryName = function()
	{
		var categoryLocation = new Array();
		$goodsSearchForm.find("[name=category\\[\\]]").each(function(index, element){
			for (var optionIndex = 0; optionIndex < element.children.length; optionIndex++) {
				if (element.children[optionIndex].selected && element.children[optionIndex].value) {
					categoryLocation.push(element.children[optionIndex].innerHTML);
				}
			}
		});
		return categoryLocation.join(" > ");
	};

	/**
	 * 분류페이지 상품진열 리스트를 지정된 상품진열 타입으로 디스플레이
	 * @method displayAsSortType
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {String} sortType
	 * @param {String} manualSortOnLinkGoodsPosition 수동진열 카테고리에 상품연결 시 진열 위치 FIRST : 맨 앞, LAST : 맨 끝
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-09-25
	 */
	this.displayAsSortType = function(sortType, manualSortOnLinkGoodsPosition)
	{
		if (sortType === "MANUAL") this.displayAsManualSort(manualSortOnLinkGoodsPosition);
		else this.displayAsAutoSort();
	};

	/**
	 * 분류페이지 상품진열 리스트를 수동진열 타입으로 디스플레이
	 * @method displayAsManualSort
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {String} manualSortOnLinkGoodsPosition 수동진열 카테고리에 상품연결 시 진열 위치 FIRST : 맨 앞, LAST : 맨 끝
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-09-25
	 */
	this.displayAsManualSort = function(manualSortOnLinkGoodsPosition)
	{
		$goodsSearchForm.removeClass("auto").addClass("manual");
		$goodsSortForm.removeClass("auto").addClass("manual");
		jQuery("#goods-sort-type-description").html(goodsSortTypeDescriptionTemplate.evaluate({
			"categoryLocation" : this.getCategoryName(),
			"category" : currentCategory
		}));
		this.displayAsViewType(goodsViewType);
		goodsSortType = "MANUAL";
		jQuery("#change-category-sort-type-auto").bind("click", function(){
			self.changeCategorySortType("AUTO");
		});
		jQuery("#manual-sort-on-link-goods-position").bind("change", function(){
			self.changeManualSortOnLinkGoodsPosition(jQuery(this).val());
		}).children("[value="+manualSortOnLinkGoodsPosition+"]").attr("selected", "selected");
	};

	/**
	 * 분류페이지 상품진열 리스트를 자동진열 타입으로 디스플레이
	 * @method displayAsAutoSort
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.displayAsAutoSort = function()
	{
		$goodsSearchForm.removeClass("manual").addClass("auto");
		$goodsSortForm.removeClass("manual").addClass("auto");
		jQuery("#goods-sort-type-description").html(goodsSortTypeDescriptionTemplate.evaluate({
			"categoryLocation" : this.getCategoryName(),
			"category" : currentCategory
		}));
		this.displayAsViewList();
		goodsSortType = "AUTO";
		jQuery("#change-category-sort-type-manual").bind("click", function(){
			self.changeCategorySortType("MANUAL");
		});
	};

	/**
	 * 수정된 상품순서 및 진열상태를 서버에 반영
	 * @method applyModified
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {Boolean}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.applyModified = function()
	{
		if (!currentCategory) {
			alert("진열작업하실 카테고리를 선택 후 검색하여주시기 바랍니다.");
			return false;
		}

		self.updateData();
		var getKeys = Object.keys || function(object)
		{
			var keys = new Array();
			for (var key in object) keys.push(key);
			return keys;
		};

		if (!getKeys(modifiedSortSet).length && !getKeys(modifiedOpenSet).length) {
			alert("변경된 사항이 없습니다.");
			return false;
		}

		httpRequest("applyModified", {"category" : currentCategory, "sortSet" : modifiedSortSet, "openSet" : modifiedOpenSet}, {
			"async" : false,
			"success" : function(responseObject)
			{
				if (responseObject.result === true) {
					alert("정상적으로 저장되었습니다.");
				}
				else if (responseObject.result.match(/^ERROR_/)) {
					alert("정상적으로 저장되지 않았습니다.\r\n리스트를 갱신하여 상품진열을 확인하여주시기 바랍니다.\r\n에러코드 : "+responseObject.result);
				}
				else {
					alert("정상적으로 저장되지 않았습니다.\r\n리스트를 갱신하여 상품진열을 확인하여주시기 바랍니다.");
				}
			}
		});
		return true;
	};

	/**
	 * 수정된 상품순서 및 진열상태를 취소
	 * @method cancelModified
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.cancelModified = function()
	{
		if (originalGoods && self.checkModified()) {
			if (confirm("현재페이지의 진열순서를 변경하기 전 상태로 복원하시겠습니까?")) {
				self.cleanSelection();
				self.makeGoodsList(originalGoods);
			}
		}
		else {
			alert("변경된 사항이 없습니다.");
		}
	};

	/**
	 * 현재 검색된 상품리스트를 최신상태로 갱신
	 * @method refresh
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.refresh = function()
	{
		this.fetchGoodsListByCategory(currentCategory, currentPage);
	};

	/**
	 * 수정된 상품순서 및 진열상태를 modifiedSortSet, modifiedOpenSet에 추가
	 * @method updateData
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.updateData = function()
	{
		$goodsContainer.children(".data").each(function(index, element){
			var
			dataGoodsno = element.getAttribute("data-goodsno"),
			dataSort = parseInt(element.getAttribute("data-sort")),
			dataOriginSort = parseInt(element.getAttribute("data-origin-sort")),
			dataOpen = jQuery(element).find("select[name=open]").val(),
			dataOriginOpen = element.getAttribute("data-origin-open");
			if (dataSort !== dataOriginSort) modifiedSortSet[dataGoodsno] = dataSort;
			if (dataOpen !== dataOriginOpen) modifiedOpenSet[dataGoodsno] = dataOpen;
		});
	};

	/**
	 * 현재 상품리스트에 수정된 사항이 있는지 체크하여 수정여부를 반환
	 * @method checkModified
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {Boolean}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.checkModified = function()
	{
		for (var index = 0; index < goodsContainer.children.length; index++) {
			var $children = jQuery(goodsContainer.children[index]);
			if ($children.attr("data-sort") !== $children.attr("data-origin-sort")) {
				return true;
			}
			if ($children.find("[name=open]").val() !== $children.attr("data-origin-open")) {
				return true;
			}
		}
		return false;
	};

	/**
	 * 엘리먼트를 받아 엘리먼트를 선택된 상태로 변경
	 * @method addSelection
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {HTMLElement|jQuery.Selector} element
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.addSelection = function(element)
	{
		var $element = jQuery(element).addClass("selected");
		$element.each(function(index, e){
			var dataIndex = parseInt(e.getAttribute("data-index"));
			if (selectionStartIndex === null || selectionStartIndex > dataIndex) selectionStartIndex = dataIndex;
			if (selectionEndIndex === null || selectionEndIndex < dataIndex) selectionEndIndex = dataIndex;
			selectionGoodsList.push(element);
			selectionGoodsList.sort(function(compareFrom, compareTo){
				var
				dataIndexFrom = parseInt(compareFrom.getAttribute("data-index")),
				dataIndexTo = parseInt(compareTo.getAttribute("data-index"));
				if (dataIndexFrom < dataIndexTo) return -1;
				else if (dataIndexFrom > dataIndexTo) return 1;
				else return 0;
			});
		});
	};

	/**
	 * 선택된 엘리먼트 상태를 모두 초기화 함
	 * @method cleanSelection
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.cleanSelection = function()
	{
		for (var index = 0; index < selectionGoodsList.length; index++) {
			jQuery(selectionGoodsList[index]).removeClass("selected");
		}
		selectionType = null;
		selectionStartIndex = null;
		selectionEndIndex = null;
		selectionGoodsList = new Array();
	};

	/**
	 * 선택된 엘리먼트들을 돌며 iterator를 실행
	 * @method eachSelection
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {Function} iterator
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.eachSelection = function(iterator)
	{
		for (var index = 0; index < selectionGoodsList.length; index++) {
			iterator({
				"index" : index,
				"elementIndex" : parseInt(selectionGoodsList[index].getAttribute("data-index")),
				"element" : selectionGoodsList[index]
			});
		}
	};

	/**
	 * 현재 선택된 엘리먼트들이 지정된 인덱스로 이동 가능한지 여부를 반환
	 * @method isMovable
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {String|Number} index
	 * @returns {Boolean}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.isMovable = function(index)
	{
		if (selectionGoodsList.length < 1) return false;
		else if (selectionGoodsList.length >= goodsList.length) return false;
		else if (index < 0) return false;
		else if (index > goodsList.length - 1) return false;
		else if (index === selectionStartIndex) return false;
		else if (index + selectionGoodsList.length > goodsList.length) return false;
		else return true;
	};

	/**
	 * 지정된 인덱스가 현재 선택된 엘리먼트로부터 위쪽방향인지 아래쪽 방향인지 반환
	 * @method getDirection
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {String|Number} index
	 * @returns {String|Boolean} 위쪽은 UP, 아래쪽은 DOWN 에러 발생 시 false
	 * @date 2013-06-03, 2013-06-03
	 */
	this.getDirection = function(index)
	{
		if (selectionStartIndex > index) return "UP";
		else if (selectionStartIndex < index) return "DOWN";
		else return false;
	};

	/**
	 * 선택된 엘리먼드를 지정된 인덱스로 이동
	 * @method moveSelection
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {String|Number} index
	 * @returns {Boolean} 이동 성공 시 true 아니면 이외의 값
	 * @date 2013-06-03, 2013-06-03
	 */
	this.moveSelection = function(index)
	{
		var
		pointElement,
		direction = this.getDirection(index),
		gap = (selectionStartIndex - index) * -1;

		if (!self.isMovable(index)) return false;
		if (direction === false) return false;

		if (direction === "UP") pointElement = goodsList[index];
		else pointElement = goodsList[index + selectionGoodsList.length - 1];

		self.eachSelection(function(data){
			if (direction === "UP") {
				jQuery(data.element).insertBefore(pointElement);
				var startIndex = selectionStartIndex + gap + data.index;
				self.dataShift(startIndex, startIndex - gap, direction);
			}
			else {
				jQuery(data.element).insertAfter(pointElement);
				var endIndex = selectionEndIndex - gap + data.index;
				self.dataShift(selectionEndIndex, selectionEndIndex + gap, direction);
				pointElement = data.element;
			}
		});

		selectionStartIndex += gap;
		selectionEndIndex += gap;
		document.body.focus();
		return true;
	};

	/**
	 * 지정된 인덱스 내에 있는 엘리먼트들의 data-sort, data-index값을 위 또는 아래 방향으로 쉬프트 함
	 * @method dataShift
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {String|Number} startIndex
	 * @param {String|Number} endIndex
	 * @param {String} direction
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.dataShift = function(startIndex, endIndex, direction)
	{
		if (direction === "UP") {
			var
			startDataIndex = goodsList[startIndex].getAttribute("data-index"),
			startDataSort = goodsList[startIndex].getAttribute("data-sort");
			for (var index = startIndex; index < endIndex; index++) {
				var
				dataIndex = parseInt(goodsList[index + 1].getAttribute("data-index")),
				dataSort = parseInt(goodsList[index + 1].getAttribute("data-sort"));
				jQuery(goodsList[index]).attr("data-index", dataIndex).attr("data-sort", dataSort).find(".no").text(parseInt(dataIndex) + pageOffset);
			}
			jQuery(goodsList[endIndex]).attr("data-index", startDataIndex).attr("data-sort", startDataSort).find(".no").text(parseInt(startDataIndex) + pageOffset);
		}
		else {
			var
			endDataIndex = goodsList[endIndex].getAttribute("data-index"),
			endDataSort = goodsList[endIndex].getAttribute("data-sort");
			for (var index = endIndex; index > startIndex; index--) {
				var
				dataIndex = goodsList[index - 1].getAttribute("data-index"),
				dataSort = goodsList[index - 1].getAttribute("data-sort");
				jQuery(goodsList[index]).attr("data-index", dataIndex).attr("data-sort", dataSort).find(".no").text(parseInt(dataIndex) + pageOffset);
			}
			jQuery(goodsList[startIndex]).attr("data-index", endDataIndex).attr("data-sort", endDataSort).find(".no").text(parseInt(endDataIndex) + pageOffset);
		}
	};

	/**
	 * 현재 선택된 카테고리의 진열타입을 수정
	 * @method changeCategorySortType
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {undefined}
	 * @date 2013-07-10, 2013-07-10
	 */
	this.changeCategorySortType = function(sortType)
	{
		if (self.checkModified()) {
			alert("상품진열 리스트에 변경된 사항이 있습니다.\r\n변경내역을 저장하시거나, 취소하신 뒤 전환하여주시기 바랍니다.");
			return;
		}
		httpRequest("changeCategorySortType", {"category" : currentCategory, "sortType" : sortType}, {
			"success" : function()
			{
				self.refresh();
			}
		});
	};

	this.changeManualSortOnLinkGoodsPosition = function(manualSortOnLinkGoodsPosition)
	{
		httpRequest("changeManualSortOnLinkGoodsPosition", {"category" : currentCategory, "manualSortOnLinkGoodsPosition" : manualSortOnLinkGoodsPosition}, {
			"success" : function()
			{
				var
				manualSortOnLinkGoodsPositionText = (manualSortOnLinkGoodsPosition === "FIRST" ? "맨 앞으로" : "맨 뒤로"),
				categoryName = self.getCategoryName().replace(/&lt;/g, "<").replace(/&gt;/g, ">");
				alert("\""+categoryName+"\" 카테고리에 상품 새로 연결 시 "+manualSortOnLinkGoodsPositionText+" 진열되도록 설정되었습니다.");
			}
		});
	};

	/**
	 * 선택된 엘리먼트들을 한칸 위로 이동
	 * @method moveUpSelection
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.moveUpSelection = function()
	{
		this.moveSelection(selectionStartIndex - 1);
	};

	/**
	 * 선택된 엘리먼트들을 한칸 아래로 이동
	 * @method moveDownSelection
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.moveDownSelection = function()
	{
		this.moveSelection(selectionStartIndex + 1);
	};

	/**
	 * 선택된 엘리먼트들을 현재 페이지 리스트 맨 위로 이동
	 * @method moveCurrentTopSelection
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.moveCurrentTopSelection = function()
	{
		if (selectionStartIndex > 0) this.moveSelection(0);
	};

	/**
	 * 선택된 엘리먼트들을 현재 페이지 리스트 맨 아래로 이동
	 * @method moveCurrentBottomSelection
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.moveCurrentBottomSelection = function()
	{
		if (selectionStartIndex < goodsList.length - selectionGoodsList.length) this.moveSelection(goodsList.length - selectionGoodsList.length);
	};

	/**
	 * 선택된 엘리먼트들을 지정한 페이지의 지정한 위치로 이동
	 * @method selectionMovePage
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {String|Number} targetPage
	 * @param {String} position 맨 위는 TOP, 맨 아래는 BOTTOM
	 * @returns {Boolean}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.movePageSelection = function(targetPage, position)
	{
		if (targetPage < 1 || targetPage > totalPage) {
			alert("존재하지 않는 페이지 입니다.");
			return false;
		}
		if (this.checkModified()) {
			if(!confirm("작업된 내용을 저장하신 후에 선택상품을\r\n다른 페이지로 이동 할 수 있습니다.\r\n작업된 내용을 저장하시겠습니까?")) return false;
			else this.applyModified();
		}

		var selectedSortSet = new Object();
		for (var index = 0; index < selectionGoodsList.length; index++) {
			selectedSortSet[selectionGoodsList[index].getAttribute("data-sno")] = selectionGoodsList[index].getAttribute("data-sort");
		}
		httpRequest("selectionMovePage", {
			"category" : currentCategory,
			"currentPage" : currentPage,
			"targetPage" : targetPage,
			"selectedSortSet" : selectedSortSet,
			"limit" : $limitRows.val(),
			"position" : position
		}, {
			"success" : function(responseObject)
			{
				self.movePageSelectionCallBack(targetPage, responseObject, selectedSortSet);
			}
		});
	};

	/**
	 * movePageSelection을 통해 상품의 페이지 이동 후 옮겨진 상품을 다시 셀렉트
	 * @method movePageSelectionCallBack
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {String|Number} targetPage
	 * @param {Object} responseObject
	 * @param {Array} selectedSortSet
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.movePageSelectionCallBack = function(targetPage, responseObject, selectedSortSet)
	{
		if (responseObject.result === true) {
			self.fetchGoodsListByCategory(currentCategory, targetPage, {
				"complete" : function()
				{
					for (var sno in selectedSortSet) jQuery("#data-"+sno).trigger("click");
				}
			});
		}
		else if (responseObject.result === "ERROR_NOT_ENOUGH_TARGET_RECORD") {
			alert("옮길 페이지에 있는 상품의 갯수가 선택된 상품의 갯수보다 작습니다.");
		}
		else if (responseObject.result.match(/^ERROR_/)) {
			alert("정상적으로 저장되지 않았습니다.\r\n갱신된 리스트를 확인하여주시기 바랍니다.\r\n에러코드 : "+responseObject.result);
			self.refresh();
		}
		else {
			alert("정상적으로 저장되지 않았습니다.\r\n갱신된 리스트를 확인하여주시기 바랍니다.");
			self.refresh();
		}
	};

	/**
	 * 선택된 엘리먼트들을 다음페이지 상단으로 이동
	 * @method moveNextTopSelection
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {Boolean}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.moveNextTopSelection = function()
	{
		if (currentPage >= totalPage) {
			alert("다음페이지가 존재하지 않습니다.");
			return false;
		}
		this.movePageSelection(currentPage + 1, "TOP");
	};

	/**
	 * 선택된 엘리먼트들을 다음페이지 하단으로 이동
	 * @method moveNextBottomSelection
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {Boolean}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.moveNextBottomSelection = function()
	{
		if (currentPage >= totalPage) {
			alert("다음페이지가 존재하지 않습니다.");
			return false;
		}
		this.movePageSelection(currentPage + 1, "BOTTOM");
	};

	/**
	 * 선택된 엘리먼트들을 이전페이지 상단으로 이동
	 * @method movePrevTopSelection
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {Boolean}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.movePrevTopSelection = function()
	{
		if (currentPage <= 1) {
			alert("이전페이지가 존재하지 않습니다.");
			return false;
		}
		this.movePageSelection(currentPage - 1, "TOP");
	};

	/**
	 * 선택된 엘리먼트들을 이전페이지 하단으로 이동
	 * @method movePrevBottomSelection
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {Boolean}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.movePrevBottomSelection = function()
	{
		if (currentPage <= 1) {
			alert("이전페이지가 존재하지 않습니다.");
			return false;
		}
		this.movePageSelection(currentPage - 1, "BOTTOM");
	};
	
	/**
	 * 리스트 보기 설정의 설정정보를 저장
	 * @method saveConfig
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {undefined}
	 * @date 2013-07-10, 2013-07-10
	 */
	this.saveConfig = function()
	{
		httpRequest("saveConfig", {
			"viewType" : jQuery(goodsSearchForm).find("input:radio[name=viewType]:checked").val(),
			"imageSize" : goodsSearchForm["imageSize"].value,
			"limitRows" : goodsSearchForm["limitRows"].value
		}, {
			"success" : function(responseObject)
			{
				if (responseObject.result === true) alert("설정이 저장되었습니다.");
				else throw new Error();
			},
			"error" : function()
			{
				alert("설정이 정상적으로 저장되지 않았습니다.");
			}
		});
	};

	/**
	 * 현재 검색된 상품리스트의 이미지사이즈를 셋팅
	 * @method setListImageSize
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {type} imageSize
	 * @returns {undefined}
	 * @date 2013-07-10, 2013-07-10
	 */
	this.setListImageSize = function(imageSize)
	{
		var originImageSize = $goodsContent.attr("data-image-size");
		$imageSize.val(imageSize);
		$goodsContent.removeClass("image-"+originImageSize).addClass("image-"+imageSize).attr("data-image-size", imageSize);
	};

	/**
	 * 상품 선택 핸들러
	 * @method selectHandler
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {Boolean}
	 * @date 2013-06-03, 2013-10-02
	 */
	this.selectHandler = function()
	{
		if (goodsSortType !== "MANUAL") return false;
		var index = parseInt(this.getAttribute("data-index"));

		if (selectionType === null && selectionStartIndex === null && selectionEndIndex === null) {
			self.addSelection(this);
			selectionType = "SINGLE";
		}
		else if (selectionType === "SINGLE" && index < selectionStartIndex) {
			for (var procIndex = selectionStartIndex; procIndex > index; procIndex--) {
				self.addSelection(goodsList[procIndex - 1]);
			}
			selectionType = "RANGED";
		}
		else if (selectionType === "SINGLE" && index > selectionEndIndex) {
			for (var procIndex = selectionEndIndex + 1; procIndex <= index; procIndex++) {
				self.addSelection(goodsList[procIndex]);
			}
			selectionType = "RANGED";
		}
		else {
			self.cleanSelection();
			selectionType = null;
		}
	};

	/**
	 * 키 이벤트 핸들러
	 * @method keyHandler
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {Event} event
	 * @returns {Boolean}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.keyHandler = function(event)
	{
		event = event || window.event;
		var keyCode = event.keyCode || event.which;
		switch (keyCode) {
			case 37 : case 38 :
				self.moveUpSelection();
				if (event.stopPropagation) event.stopPropagation();
				else event.cancelBubble = true;
				return false;
			case 39 : case 40 :
				self.moveDownSelection();
				if (event.stopPropagation) event.stopPropagation();
				else event.cancelBubble = true;
				return false;
			case 35 :
				self.moveCurrentBottomSelection();
				return false;
			case 36 :
				self.moveCurrentTopSelection();
				return false;
		}
	};

	/**
	 * 선택상품 페이지 이동 셀렉트박스 핸들러
	 * @method moveSelectionChangeHandler
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {Boolean}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.moveSelectionChangeHandler = function()
	{
		var value = jQuery(this).val().trim();
		if (value.length && selectionGoodsList.length < 1) {
			alert("선택된 상품이 없습니다.");
			jQuery(this).val("");
			return false;
		}
		else {
			switch (value) {
				case "firstTop":
					self.movePageSelection(1, "TOP");
					break;
				case "nextTop":
					self.moveNextTopSelection();
					break;
				case "nextBottom":
					self.moveNextBottomSelection();
					break;
				case "prevTop":
					self.movePrevTopSelection();
					break;
				case "prevBottom":
					self.movePrevBottomSelection();
					break;
				case "lastBottom":
					self.movePageSelection(totalPage, "BOTTOM");
					break;
			}
			jQuery(this).val("");
		}
		document.body.focus();
	};

	__construct();

};

if (!window.jQuery) alert("jQuery라이브러리가 로드되지 않았습니다.\r\n고객센터로 문의하여 주시기 바랍니다.");

jQuery(document).ready(function(){
	var goodsSort = new GoodsSortController();
	goodsSort.printMessage("진열작업하실 카테고리를 선택 후 검색하여주시기 바랍니다.");
});