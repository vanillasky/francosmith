/**
 * ��ǰ �˻� �� ���� �� ��Ʈ�� ��ü
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
	 * ������
	 * @constructor
	 */
	var __construct = function()
	{
		// ���ø� ���
		registTemplate();

		// �⺻�� �� Ÿ���� ����Ʈ �� ����
		self.displayAsViewType("LIST");

		// ��ǰ �����̳ʿ� jQuery UI sortable ���� Ȱ��ȭ
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
				var $description = jQuery(document.createElement("div")).attr("id", "on-drag-description").text(selectionGoodsList.length+"�� ��ǰ ���� ��");
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
		
		// ����Ʈ ���� �ɼ� ������ ����
		self.displayAsViewType(goodsSearchForm.defaultViewType.value);
		self.setListImageSize(goodsSearchForm.defaultImageSize.value);
		$goodsSearchForm.find("[name=limitRows]").val(goodsSearchForm.defaultLimitRows.value);

		// �̺�Ʈ ���� ��� ���ε�
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
	 * ���ø� ���
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
	 * ������ URL�� HTTP��û�� ����
	 * @method httpRequest
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {String} mode ��û����
	 * @param {Object} dataObject ���޵�����
	 * @param {Object} option �߰������� ������ jQuery.ajax �ɼ�
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
	 * ��ǰ�˻����� ���õ� ī�װ��� ����Ͽ� ��ǰ��ȸ
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
		if (!category) alert("ī�װ��� ������ �ּ���.");

		self.fetchGoodsListByCategory(category, 1);
		return false;
	};

	/**
	 * ���� �� ����Ʈ ����
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
	 * ����Ʈ�� �޽��� ���
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
	 * ����Ʈ�� �Ǵ� ������������ ��Ÿ�� ����
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
	 * ����Ʈ������ ��Ÿ�� ����
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
	 * ������������ ��Ÿ�� ����
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
	 * ��ǰ�̹��� ����Ʈ�ڽ��� ���� ��ȯ
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
	 * ��ǰ��¼� ����Ʈ�ڽ��� ���� ��ȯ
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
	 * ����ϴ� ��Ų�� ��ȯ
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
	 * ��ǰ���ڵ带 �޾� ��ǰ����Ʈ ����
	 * @method makeGoodsList
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {Array} goodsRecord ��ǰ���ڵ�
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
	 * ��ǰ�����͸� �޾� HTML�� ����� ��ȯ
	 * @method makeGoodsHTML
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {Object} goodsRecord ��ǰ���ڵ�
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
	 * �ɼǸ�� �ɼǰ� ����Ʈ�� �޾� ��ǰ����Ʈ�� ����� �ɼ��ؽ�Ʈ�� ��ȯ
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
	 * ��������ü�� �޾� ������ �׺���̼��� ����
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
	 * ��ǰ���� ������ ����ȭ
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
				self.printMessage("["+self.getCategoryName()+"] ī�װ��� ��ǰ������ ����ȭ ���Դϴ�.");
			},
			"success" : function(responseObject)
			{
				if (responseObject.result === true) {
					alert("��ǰ���� ����ȭ�� �Ϸ�Ǿ����ϴ�.");
					self.refresh();
				}
				else if (responseObject.result.match(/^ERROR_/)) {
					alert("��ǰ������ ����ȭ�ϴ� ���� ������ �߻��Ͽ����ϴ�.\r\n�����ڵ� : "+responseObject.result);
					throw new Error();
				}
				else {
					alert("��ǰ������ ����ȭ�ϴ� ���� ������ �߻��Ͽ����ϴ�.");
					throw new Error();
				}
			},
			"error" : function(xmlHttpRequest)
			{
				self.printMessage("��ǰ������ ����ȭ�ϴ� ���� ������ �߻��Ͽ����ϴ�.");
			}
		});
	};

	/**
	 * ī�װ�, ������ ������ ���� ī�װ����ִ� ��ǰ����Ʈ�� ��ȸ �� callback�� ����
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
				self.printMessage("��ǰ����Ʈ�� �޾ƿ��� ���Դϴ�.");
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
					self.printMessage("��ϵ� ��ǰ�� �����ϴ�.");
					return true;
				}

				originalGoods = responseObject.record;
				self.makeGoodsList(responseObject.record);
				self.makePage(responseObject.page);

				if (callback.complete) callback.complete();
			},
			"error" : function(xmlHttpRequest)
			{
				self.printMessage("��ǰ����Ʈ�� �޾ƿ��� ���� ������ �߻��Ͽ����ϴ�.");
			}
		});
	};

	/**
	 * ���� �˻��� ī�װ� ������ ������ �������� �̵�
	 * @method movePage
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {String|Number} page
	 * @returns {Boolean}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.movePage = function(page)
	{
		if (self.checkModified()) {
			if(!confirm("�۾��� ������ �����Ͻ� �Ŀ� ���û�ǰ��\r\n�ٸ� �������� �̵� �� �� �ֽ��ϴ�.\r\n�۾��� ������ �����Ͻðڽ��ϱ�?")) return false;
			else self.applyModified();
		}
		self.fetchGoodsListByCategory(currentCategory, page);
	};

	/**
	 * ���� �˻��� ī�װ��� �̸��� ��ȯ
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
	 * �з������� ��ǰ���� ����Ʈ�� ������ ��ǰ���� Ÿ������ ���÷���
	 * @method displayAsSortType
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {String} sortType
	 * @param {String} manualSortOnLinkGoodsPosition �������� ī�װ��� ��ǰ���� �� ���� ��ġ FIRST : �� ��, LAST : �� ��
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-09-25
	 */
	this.displayAsSortType = function(sortType, manualSortOnLinkGoodsPosition)
	{
		if (sortType === "MANUAL") this.displayAsManualSort(manualSortOnLinkGoodsPosition);
		else this.displayAsAutoSort();
	};

	/**
	 * �з������� ��ǰ���� ����Ʈ�� �������� Ÿ������ ���÷���
	 * @method displayAsManualSort
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {String} manualSortOnLinkGoodsPosition �������� ī�װ��� ��ǰ���� �� ���� ��ġ FIRST : �� ��, LAST : �� ��
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
	 * �з������� ��ǰ���� ����Ʈ�� �ڵ����� Ÿ������ ���÷���
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
	 * ������ ��ǰ���� �� �������¸� ������ �ݿ�
	 * @method applyModified
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {Boolean}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.applyModified = function()
	{
		if (!currentCategory) {
			alert("�����۾��Ͻ� ī�װ��� ���� �� �˻��Ͽ��ֽñ� �ٶ��ϴ�.");
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
			alert("����� ������ �����ϴ�.");
			return false;
		}

		httpRequest("applyModified", {"category" : currentCategory, "sortSet" : modifiedSortSet, "openSet" : modifiedOpenSet}, {
			"async" : false,
			"success" : function(responseObject)
			{
				if (responseObject.result === true) {
					alert("���������� ����Ǿ����ϴ�.");
				}
				else if (responseObject.result.match(/^ERROR_/)) {
					alert("���������� ������� �ʾҽ��ϴ�.\r\n����Ʈ�� �����Ͽ� ��ǰ������ Ȯ���Ͽ��ֽñ� �ٶ��ϴ�.\r\n�����ڵ� : "+responseObject.result);
				}
				else {
					alert("���������� ������� �ʾҽ��ϴ�.\r\n����Ʈ�� �����Ͽ� ��ǰ������ Ȯ���Ͽ��ֽñ� �ٶ��ϴ�.");
				}
			}
		});
		return true;
	};

	/**
	 * ������ ��ǰ���� �� �������¸� ���
	 * @method cancelModified
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {undefined}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.cancelModified = function()
	{
		if (originalGoods && self.checkModified()) {
			if (confirm("������������ ���������� �����ϱ� �� ���·� �����Ͻðڽ��ϱ�?")) {
				self.cleanSelection();
				self.makeGoodsList(originalGoods);
			}
		}
		else {
			alert("����� ������ �����ϴ�.");
		}
	};

	/**
	 * ���� �˻��� ��ǰ����Ʈ�� �ֽŻ��·� ����
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
	 * ������ ��ǰ���� �� �������¸� modifiedSortSet, modifiedOpenSet�� �߰�
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
	 * ���� ��ǰ����Ʈ�� ������ ������ �ִ��� üũ�Ͽ� �������θ� ��ȯ
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
	 * ������Ʈ�� �޾� ������Ʈ�� ���õ� ���·� ����
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
	 * ���õ� ������Ʈ ���¸� ��� �ʱ�ȭ ��
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
	 * ���õ� ������Ʈ���� ���� iterator�� ����
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
	 * ���� ���õ� ������Ʈ���� ������ �ε����� �̵� �������� ���θ� ��ȯ
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
	 * ������ �ε����� ���� ���õ� ������Ʈ�κ��� ���ʹ������� �Ʒ��� �������� ��ȯ
	 * @method getDirection
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {String|Number} index
	 * @returns {String|Boolean} ������ UP, �Ʒ����� DOWN ���� �߻� �� false
	 * @date 2013-06-03, 2013-06-03
	 */
	this.getDirection = function(index)
	{
		if (selectionStartIndex > index) return "UP";
		else if (selectionStartIndex < index) return "DOWN";
		else return false;
	};

	/**
	 * ���õ� �����յ带 ������ �ε����� �̵�
	 * @method moveSelection
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {String|Number} index
	 * @returns {Boolean} �̵� ���� �� true �ƴϸ� �̿��� ��
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
	 * ������ �ε��� ���� �ִ� ������Ʈ���� data-sort, data-index���� �� �Ǵ� �Ʒ� �������� ����Ʈ ��
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
	 * ���� ���õ� ī�װ��� ����Ÿ���� ����
	 * @method changeCategorySortType
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {undefined}
	 * @date 2013-07-10, 2013-07-10
	 */
	this.changeCategorySortType = function(sortType)
	{
		if (self.checkModified()) {
			alert("��ǰ���� ����Ʈ�� ����� ������ �ֽ��ϴ�.\r\n���泻���� �����Ͻðų�, ����Ͻ� �� ��ȯ�Ͽ��ֽñ� �ٶ��ϴ�.");
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
				manualSortOnLinkGoodsPositionText = (manualSortOnLinkGoodsPosition === "FIRST" ? "�� ������" : "�� �ڷ�"),
				categoryName = self.getCategoryName().replace(/&lt;/g, "<").replace(/&gt;/g, ">");
				alert("\""+categoryName+"\" ī�װ��� ��ǰ ���� ���� �� "+manualSortOnLinkGoodsPositionText+" �����ǵ��� �����Ǿ����ϴ�.");
			}
		});
	};

	/**
	 * ���õ� ������Ʈ���� ��ĭ ���� �̵�
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
	 * ���õ� ������Ʈ���� ��ĭ �Ʒ��� �̵�
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
	 * ���õ� ������Ʈ���� ���� ������ ����Ʈ �� ���� �̵�
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
	 * ���õ� ������Ʈ���� ���� ������ ����Ʈ �� �Ʒ��� �̵�
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
	 * ���õ� ������Ʈ���� ������ �������� ������ ��ġ�� �̵�
	 * @method selectionMovePage
	 * @author workingparksee <parksee@godo.co.kr>
	 * @param {String|Number} targetPage
	 * @param {String} position �� ���� TOP, �� �Ʒ��� BOTTOM
	 * @returns {Boolean}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.movePageSelection = function(targetPage, position)
	{
		if (targetPage < 1 || targetPage > totalPage) {
			alert("�������� �ʴ� ������ �Դϴ�.");
			return false;
		}
		if (this.checkModified()) {
			if(!confirm("�۾��� ������ �����Ͻ� �Ŀ� ���û�ǰ��\r\n�ٸ� �������� �̵� �� �� �ֽ��ϴ�.\r\n�۾��� ������ �����Ͻðڽ��ϱ�?")) return false;
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
	 * movePageSelection�� ���� ��ǰ�� ������ �̵� �� �Ű��� ��ǰ�� �ٽ� ����Ʈ
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
			alert("�ű� �������� �ִ� ��ǰ�� ������ ���õ� ��ǰ�� �������� �۽��ϴ�.");
		}
		else if (responseObject.result.match(/^ERROR_/)) {
			alert("���������� ������� �ʾҽ��ϴ�.\r\n���ŵ� ����Ʈ�� Ȯ���Ͽ��ֽñ� �ٶ��ϴ�.\r\n�����ڵ� : "+responseObject.result);
			self.refresh();
		}
		else {
			alert("���������� ������� �ʾҽ��ϴ�.\r\n���ŵ� ����Ʈ�� Ȯ���Ͽ��ֽñ� �ٶ��ϴ�.");
			self.refresh();
		}
	};

	/**
	 * ���õ� ������Ʈ���� ���������� ������� �̵�
	 * @method moveNextTopSelection
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {Boolean}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.moveNextTopSelection = function()
	{
		if (currentPage >= totalPage) {
			alert("������������ �������� �ʽ��ϴ�.");
			return false;
		}
		this.movePageSelection(currentPage + 1, "TOP");
	};

	/**
	 * ���õ� ������Ʈ���� ���������� �ϴ����� �̵�
	 * @method moveNextBottomSelection
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {Boolean}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.moveNextBottomSelection = function()
	{
		if (currentPage >= totalPage) {
			alert("������������ �������� �ʽ��ϴ�.");
			return false;
		}
		this.movePageSelection(currentPage + 1, "BOTTOM");
	};

	/**
	 * ���õ� ������Ʈ���� ���������� ������� �̵�
	 * @method movePrevTopSelection
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {Boolean}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.movePrevTopSelection = function()
	{
		if (currentPage <= 1) {
			alert("������������ �������� �ʽ��ϴ�.");
			return false;
		}
		this.movePageSelection(currentPage - 1, "TOP");
	};

	/**
	 * ���õ� ������Ʈ���� ���������� �ϴ����� �̵�
	 * @method movePrevBottomSelection
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {Boolean}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.movePrevBottomSelection = function()
	{
		if (currentPage <= 1) {
			alert("������������ �������� �ʽ��ϴ�.");
			return false;
		}
		this.movePageSelection(currentPage - 1, "BOTTOM");
	};
	
	/**
	 * ����Ʈ ���� ������ ���������� ����
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
				if (responseObject.result === true) alert("������ ����Ǿ����ϴ�.");
				else throw new Error();
			},
			"error" : function()
			{
				alert("������ ���������� ������� �ʾҽ��ϴ�.");
			}
		});
	};

	/**
	 * ���� �˻��� ��ǰ����Ʈ�� �̹�������� ����
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
	 * ��ǰ ���� �ڵ鷯
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
	 * Ű �̺�Ʈ �ڵ鷯
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
	 * ���û�ǰ ������ �̵� ����Ʈ�ڽ� �ڵ鷯
	 * @method moveSelectionChangeHandler
	 * @author workingparksee <parksee@godo.co.kr>
	 * @returns {Boolean}
	 * @date 2013-06-03, 2013-06-03
	 */
	this.moveSelectionChangeHandler = function()
	{
		var value = jQuery(this).val().trim();
		if (value.length && selectionGoodsList.length < 1) {
			alert("���õ� ��ǰ�� �����ϴ�.");
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

if (!window.jQuery) alert("jQuery���̺귯���� �ε���� �ʾҽ��ϴ�.\r\n�����ͷ� �����Ͽ� �ֽñ� �ٶ��ϴ�.");

jQuery(document).ready(function(){
	var goodsSort = new GoodsSortController();
	goodsSort.printMessage("�����۾��Ͻ� ī�װ��� ���� �� �˻��Ͽ��ֽñ� �ٶ��ϴ�.");
});