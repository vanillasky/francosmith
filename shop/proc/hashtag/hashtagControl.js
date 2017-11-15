/**
 * HASHTAG
 * @class hashtag
 * @author workingby <bumyul2000@godo.co.kr>
 * @version 1.0
 * @date 2016-10-05
 */
var page = 1;
var ajaxHashtagUrl = '../../proc/hashtag/ajax.getHashtagData.php';

var HashtagCoreController = function()
{
	var self = this;
	var hashtagSelector;
	var deleteHashtagSelector;

	this.ajaxHashtagList = function(param)
	{
		//ajax url 재설정 - 유저모드 일시
		if(param.ajaxUrl){
			ajaxHashtagUrl = param.ajaxUrl;
		}
		if(param.obj){
			var hashtagInputListSearchObj = param.obj;
			param.obj = null;
		}
		jQuery.post(ajaxHashtagUrl, param, function(res){
			var dataArray = new Array();
			dataArray = res.split("|");

			if(dataArray[0] === 'success'){
				switch(param.mode){
					//관리모드 > 해시태그 관리 > 리스트
					//관리모드 > 해시태그 관련설정 > 사용자설정 팝업
					case 'allList':
						var hashtagData = eval("("+dataArray[1]+")");
						if(hashtagData){
							jQuery.each(hashtagData, function( index, value ) {
								jQuery("#hashtagListBox").append(value);
							});
							page += 1;
						}
					break;

					//관리모드 > 해시태그 관리 > 리스트
					//관리모드 > 해시태그 관련설정 > 사용자설정 팝업
					case 'clickAllList':
						var hashtagData = eval("("+dataArray[1]+")");

						jQuery("#hashtagListBox").empty();
						if(hashtagData){
							jQuery.each(hashtagData, function( index, value ) {
								jQuery("#hashtagListBox").append(value);
							});
						}
					break;

					//관리모드 > 상품상세 > 상품 해시태그
					case 'goodsList':
						var hashtagData = eval("("+dataArray[1]+")");

						if(hashtagData){
							jQuery.each(hashtagData, function( index, value ) {
								jQuery("#hashtagListBox").append(value);
							});
						}
					break;

					//input 해시태그 리스트 [페이지 다중 사용]
					case 'inputList':
						var hashtagData = eval("("+dataArray[1]+")");

						if(hashtagData != '' && hashtagData != null && hashtagData != 'undefined'){
							hashtagInputListSearchObj.autocomplete({
								source: hashtagData
							});
						}
					break;

					//관리모드 > 상품상세 > 상품 해시태그
					case 'addLayout':
						var hashtagData = eval("("+dataArray[1]+")");

						jQuery("#hashtagListBox").append(hashtagData);
						jQuery("#hashtag").val('');
					break;

					//관리모드 > 해시태그 관리 > 추가
					case 'addLive':
						var hashtagData = eval("("+dataArray[1]+")");

						jQuery("#hashtagListBox").prepend(hashtagData);
						jQuery("#hashtag").val('');
					break;

					//관리모드 > 해시태그 관리 > 삭제
					case 'deleteLive':
						deleteHashtagSelector.remove();

						//해시태그 정보 리셋
						self.resetInfo();
					break;

					//관리모드 > 빠른 해시태그 수정 > 삭제
					case 'deleteManageLive':
						deleteHashtagSelector.remove();
					break;

					//관리모드 > 해시태그 관련 설정 > 사용자 설정 팝업 페이지
					case 'saveDisplay':
						alert("저장되었습니다.");
						window.location.reload();
					break;

					//유저모드 > 상품상세페이지 > 추가
					case 'addLiveUser':
						var hashtagData = eval("("+dataArray[1]+")");

						jQuery("#hashtagListBox").append(hashtagData);
						jQuery("#hashtag").val('');
					break;
				}
			}
			else if(dataArray[0] === 'fail'){
				if(param.mode !== 'inputList'){
					alert(dataArray[1]);
				}
			}
			else { }

			return;
		});
	};

	this.moveTag = function(event)
	{
		var keyMove = false;
		var event = event || window.event;
		var keyCode = event.keyCode || event.which;

		switch (keyCode) {
			case 37 : case 38 :
				self.moveUp();
				keyMove = true;
			break;
			case 39 : case 40 :
				self.moveDown();
				keyMove = true;
			break;
		}

		if(keyMove === true){
			if (event.stopPropagation) {
				event.stopPropagation();
			}
			else {
				event.cancelBubble = true;
			}
			return false;
		}
	};

	this.moveUp = function()
	{
		if(hashtagSelector){
			hashtagSelector.insertBefore(hashtagSelector.prev());
			hashtagSelector.focus();
		}
	};

	this.moveDown = function()
	{
		if(hashtagSelector){
			hashtagSelector.insertAfter(hashtagSelector.next());
			hashtagSelector.focus();
		}
	};

	//해시태그 명 클릭시
	this.changeAction = function()
	{
		hashtagSelector = jQuery(this);

		//색상변경
		self.changeBackground();

		//해시태그 정보변경
		self.changeInfo();
	};

	this.changeBlurAction = function()
	{
		if(hashtagSelector){
			hashtagSelector = null;
		}
		jQuery(this).css("background-color", "white");
	}

	this.changeBackground = function()
	{
		hashtagSelector.siblings().css("background-color", "white");
		hashtagSelector.css("background-color", "#e8f5bb");
	};

	//해시태그 정보 변경
	this.changeInfo = function()
	{
		if (jQuery("#hashtagWidget_name").length > 0 && jQuery("#hashtagWidgetUrl").length > 0 && jQuery("#hashtagRegistGoodsCount").length > 0){
			var hashtagDataName = hashtagSelector.attr("data-name");
			var hashtagWidgetUrl = jQuery("#cfgRootDir").val() + "/goods/goods_hashtag_list.php?hashtag=" + hashtagDataName;
			jQuery("#hashtagWidget_name").val(hashtagDataName);
			jQuery("#hashtagWidgetUrl").html(hashtagWidgetUrl);
			jQuery("#hashtagRegistGoodsCount").html(hashtagSelector.attr("data-goodsCount"));
		}
	};

	//해시태그 정보 리셋
	this.resetInfo = function()
	{
		jQuery("#hashtagWidgetUrl").html("해시태그를 선택하여 주세요.");
		jQuery("#hashtagRegistGoodsCount").html(0);
	}

	this.deleteHashtag = function()
	{
		if(confirm("삭제 시 상품에 등록된 해시태그도 삭제됩니다.\n계속하시겠습니까?")){
			deleteHashtagSelector = jQuery(this).closest("div");
			var hashtagName = deleteHashtagSelector.attr('data-name');

			self.ajaxHashtagList({mode:'deleteLive', hashtag:hashtagName});
		}
	};

	//관리 > 빠른 해시태그 수정 > 삭제
	this.deleteManageHashtag = function()
	{
		deleteHashtagSelector = jQuery(this).closest("div");
		var hashtagName = deleteHashtagSelector.attr('data-name');
		var goodsno = deleteHashtagSelector.closest("td").attr('area-data-goodsno');
		var param = {
			mode: 'deleteManageLive',
			hashtag: hashtagName,
			goodsno : goodsno
		};

		self.ajaxHashtagList(param);
	};

	//레이아웃만 삭제
	this.deleteHashtagLayout = function()
	{
		jQuery(this).closest("div").remove();
	}

	//초과 length 삭제
	this.cutMaxLength = function(thisObj)
	{
		var maxLength = 20;
		var inputValue = thisObj.val();
		thisObj.val(inputValue.substr(0, maxLength));
	}

	//특수문자 체크
	this.checkSpecialCharacter = function(thisObj)
	{
		var thisObjValue = thisObj.val();
		var regPattern = /[~!@\#$%<>^&*\()\-=+\’\\\|\/\.\,]/gi;
		if(regPattern.test(thisObjValue)) {
			alert("특수문자는 입력 할 수 없습니다.");
			thisObj.val(thisObjValue.replace(regPattern, ""));
			return false;
		}

		return true;
	}
};

//해시태그 관리 페이지
var HashtagListController = function()
{
	var self = this;
	var CoreController = new HashtagCoreController();
	var searchTextConfirm = false;
	var pageNum = 150;

	var __construct = function()
	{
		//설정정보저장
		jQuery("#hashtagListConfig-save-btn").click(self.saveListConfigForm);
		//해시태그 검색 input listing
		jQuery(document).on('keyup','.hashtagInputListSearch',function(){
			var inputListParam = {
				mode: 'inputList',
				searchText: jQuery(this).val(),
				obj:jQuery(this)
			};
			CoreController.ajaxHashtagList(inputListParam);
		});
		//해시태그 정보 변경
		jQuery("#hashtagListBox").on("click", "div", CoreController.changeAction);
		//해시태그 삭제
		jQuery("#hashtagListBox").on("click", "span", CoreController.deleteHashtag);
		//해시태그 코드생성
		jQuery("#hashtagCreateCodeBtn").click(self.popupHashtagCreateCode);
		//해시태그 추가
		jQuery("#hashtagAddBtn").click(self.addHashtag);
		//input keyup event
		jQuery("#hashtag").keyup(self.keyupEvent);
		//해시태그 검색
		jQuery("#hashtagSearch").keyup(self.checkVoidSearch);
		jQuery("#hashtagSearchBtn").click(function(){
			self.clickHashtagListBox();
		});
		//해시태그 리스트 스크롤 페이징
		jQuery("#hashtagListBox").scroll(function(){
			self.scrollHashtagListBox();
		});

		//listing
		CoreController.ajaxHashtagList({mode:'allList', page:1, pageNum:pageNum});
		//set add input
		jQuery('.hashtagInputListSearch').trigger("keyup");
	};

	//해시태그 상품리스트 공통 설정 저장
	this.saveListConfigForm = function()
	{
		jQuery('#mode').val('listConfigSave');
		jQuery('#hashtagListConfigForm').submit();
	};

	//빈값일때 전체 search
	this.checkVoidSearch = function()
	{
		if(jQuery(this).val() === '' && searchTextConfirm === true){
			self.clickHashtagListBox();
		}
	}

	//검색 클릭 리스트
	this.clickHashtagListBox = function()
	{
		searchTextConfirm = true;
		page = 1;
		var param = {
			mode : 'clickAllList',
			page : page,
			searchText : jQuery("#hashtagSearch").val(),
			pageNum : pageNum
		};
		CoreController.ajaxHashtagList(param);
	}

	//해시태그 리스트 페이징
	this.scrollHashtagListBox = function()
	{
		if(jQuery("#hashtagListBox").outerHeight() + jQuery("#hashtagListBox").scrollTop() >= jQuery("#hashtagListBox").prop("scrollHeight")){
			if(page < 2) page = 2;
			var param = {
				mode : 'allList',
				page : page,
				searchText : jQuery("#hashtagSearch").val(),
				pageNum : pageNum
			};
			CoreController.ajaxHashtagList(param);
		}
	};

	//해시태그 추가
	this.addHashtag = function()
	{
		var hashtag = jQuery("#hashtag");
		if(!jQuery.trim(hashtag.val())){
			alert("해시태그명을 입력해 주세요.");
			hashtag.focus();
			return;
		}
		if(hashtag.val().length > 20){
			alert("최대 등록 글자수는 20글자 입니다.");
			CoreController.cutMaxLength(hashtag);
			return;
		}

		CoreController.ajaxHashtagList({ mode: 'addLive', hashtag: hashtag.val() });
	};

	this.keyupEvent = function(event)
	{
		//특수문자 체크
		var checkCharacter = true;
		checkCharacter = CoreController.checkSpecialCharacter(jQuery(this));
		if(checkCharacter === false){
			return;
		}

		//해시태그 추가 - enter
		var event = event || window.event;
		var keyCode = event.keyCode || event.which;
		if(keyCode === 13){
			self.addHashtag();
		}
	}

	//해시태그 코드 생성 팝업
	this.popupHashtagCreateCode = function()
	{
		if(!jQuery("#hashtagWidget_name").val()){
			alert("해시태그를 선택하여 주세요.");
			return;
		}
		if(!jQuery("#hashtagWidget_width").val()){
			alert("가로 개수를 입력하여 주세요.");
			return;
		}
		if(!jQuery("#hashtagWidget_height").val()){
			alert("세로 개수를 입력하여 주세요.");
			return;
		}
		if(!jQuery("#hashtagWidget_iframeWidth").val()){
			alert("IFRAME 가로 사이즈를 입력하여 주세요.");
			return;
		}
		if(!jQuery("#hashtagWidget_imageWidth").val()){
			alert("이미지 가로 사이즈를 입력하여 주세요.");
			return;
		}

		var parameter = "?hashtag=" + jQuery("#hashtagWidget_name").val() + "&hashtagWidth=" + jQuery("#hashtagWidget_width").val() + "&hashtagHeight=" + jQuery("#hashtagWidget_height").val() + "&hashtagIframeWidth=" + jQuery("#hashtagWidget_iframeWidth").val() + "&hashtagImageWidth=" + jQuery("#hashtagWidget_imageWidth").val();
		window.open('./popup.hashtagCreateCode.php' + parameter,'hashtagCreateCode', 'width=1050px, height=800px, scrollbars=yes');
	}

	__construct();
};

//상품 상세페이지
var HashtagGoodsViewController = function()
{
	var self = this;
	var CoreController = new HashtagCoreController();

	var __construct = function()
	{
		jQuery("#hashtagListBox").on("click", "div", CoreController.changeAction);
		jQuery("#hashtagListBox").on("blur", "div", CoreController.changeBlurAction);
		//해시태그 삭제
		jQuery("#hashtagListBox").on("click", "span", self.deleteHashtagLayout);
		//해시태그 추가
		jQuery("#hashtagAddBtn").click(self.addHashtag);
		//input keyup event
		jQuery("#hashtag").keyup(self.keyupEvent);
		//set add input
		jQuery("#hashtag").trigger("keyup");
		//goods hashtag lisging
		var goodsno = jQuery("#goods-form input[name='goodsno']").val();
		if(goodsno > 0){
			CoreController.ajaxHashtagList({mode:'goodsList', goodsno: goodsno});
		}
		//순서변경
		jQuery("#hashtagListBox").on("keydown", CoreController.moveTag);
	};

	this.keyupEvent = function()
	{
		//특수문자 체크
		var checkCharacter = true;
		checkCharacter = CoreController.checkSpecialCharacter(jQuery(this));
		if(checkCharacter === false){
			return;
		}

		//해시태그 추가 input listing
		var inputListParam = {
			mode: 'inputList',
			searchText: jQuery("#hashtag").val(),
			obj: jQuery("#hashtag")
		};
		CoreController.ajaxHashtagList(inputListParam);
	}

	this.addHashtag = function()
	{
		var hashtag = jQuery("#hashtag");
		if(!jQuery.trim(hashtag.val())){
			alert("해시태그명을 입력해 주세요.");
			hashtag.focus();
			return;
		}
		if(hashtag.val().length > 20){
			alert("최대 등록 글자수는 20글자 입니다.");
			CoreController.cutMaxLength(hashtag);
			return;
		}
		var hashtaglistBoxDiv = jQuery("#hashtagListBox div");
		if(hashtaglistBoxDiv.length >= 10){
			alert("최대 등록 개수는 10개 입니다.");
			return;
		}
		var hashtagReplaceValue = jQuery.trim(hashtag.val()).replace(/ /g, '_');
		var duplicateHashtag = false;
		hashtaglistBoxDiv.each(function(){
			if(hashtagReplaceValue === jQuery.trim(jQuery(this).attr('data-name'))){
				duplicateHashtag = true;
				return false;
			}
		});
		if(duplicateHashtag === true){
			alert("동일한 해시태그가 존재합니다.");
			return;
		}

		CoreController.ajaxHashtagList({mode:'addLayout', hashtag:hashtag.val()});
	};

	//삭제
	this.deleteHashtagLayout = function()
	{
		var deleteObj = jQuery(this).closest("div");
		if(deleteObj.find("input[name='hashtagNo[]']").val() !== ''){
			var inputParam = {
				type:"hidden",
				name:"hashtagDelName[]",
				value:deleteObj.attr('data-name')
			};
			jQuery("<input></input>").attr(inputParam).appendTo(jQuery("#hashtagListBox"));
		}
		deleteObj.remove();
	}

	__construct();
};

//팝업 해시태그 위젯리스트 (코드생성 미리보기) 페이지
var HashtagPopupCreateCodeController = function()
{
	var self = this;
	var CoreController = new HashtagCoreController();

	var __construct = function()
	{
		//소스복사
		jQuery("#sourceCopy").click(self.copySource);
		//창닫기
		jQuery("#popupClose").click(function(){
			window.close();
		});

		//링크 삭제
		jQuery(window).load(self.deleteHashtagWidgetListGoodsLink);
	};

	//소스복사
	this.copySource = function()
	{
		var clipData = jQuery('#hashtag_previewSourceArea').text();
		if(window.clipboardData){
			alert("복사되었습니다.\n쇼핑몰내 상품리스트를 노출시킬 위치에 소스를 붙여넣어 주세요.");
			window.clipboardData.setData("Text", clipData);
		}
		else {
			prompt("코드를 클립보드로 복사(Ctrl+C) 하시고.\n쇼핑몰내 상품리스트를 노출시킬 위치에 소스를 붙여넣어 주세요.", clipData);
		}
		return;
	}

	//링크 삭제
	this.deleteHashtagWidgetListGoodsLink = function()
	{
		jQuery("#hashtag_previewLayout iframe").contents().find('a').removeAttr("onclick");
		jQuery("#hashtag_previewLayout iframe").contents().find('.hashtagSelector').removeAttr("onclick");
	}

	__construct();
};

//해시태그 관련 설정 페이지
var HashtagConfigController = function()
{
	var self = this;
	var CoreController = new HashtagCoreController();

	var __construct = function()
	{
		//설정 저장
		jQuery("#hashtagConfig_submitImg").click(self.saveConfig);
		//해시태그 마이그레이션
		jQuery("#hashtagMigragionBtn").click(self.migrationHashtag);
		jQuery(".hashtagDisplayPopupBtn").click(self.openDisplayPopup);
	};

	//설정 저장
	this.saveConfig = function()
	{
		jQuery('#mode').val('configSave');
		jQuery('#hashtagConfigForm').submit();
	}

	//해시태그 마이그레이션
	this.migrationHashtag = function()
	{
		var migrationParam = jQuery.param(jQuery("input[type='checkbox']:checked"));
		if(!migrationParam){
			alert("생성할 타입을 선택하여 주세요.");
			return;
		}

		var param = {
			mode: 'migrationHashtag',
			checkboxParam: migrationParam
		};
		self.showProgressBar();
		jQuery.post(ajaxHashtagUrl, param, function(res){
			var dataArray = new Array();
			dataArray = res.split("|");

			if(dataArray[0] === 'success'){
				var migrationGoodsCount = eval("("+dataArray[1]+")");
				alert("총 " + comma(migrationGoodsCount) + "개의 상품에 해시태그가 설정되었습니다." );
				document.location.reload();
			}
			else if(dataArray[0] === 'fail'){
				alert("해시태그 생성을 실패하였습니다.\n고객센터에 문의하여 주세요.");
			}
			else { }

			return;
		})
		.always(function(){
			self.hiddenProgressBar();
		})
		.fail(function(){
			alert("해시태그 생성을 실패하였습니다.\n고객센터에 문의하여 주세요.");
			self.hiddenProgressBar();
		});
	}

	//프로그레스바 노출
	this.showProgressBar = function()
	{
		var progressImgMarginTop = Math.round((jQuery(window).height() - 116) / 2);
		jQuery("body").append('<div id="hashtagPrograssbar" style="position:absolute;top:0;left:0;background:#44515b;filter:alpha(opacity=80);opacity:0.8;width:100%;height:'+jQuery('body').height()+'px;cursor:progress;z-index:100000;margin:0 auto;text-align: center;"><img src="../img/admin_progress.gif" border="0" style="margin-top:'+progressImgMarginTop+'px;"/></div>');
	}

	//프로그레스바 감춤
	this.hiddenProgressBar = function()
	{
		jQuery("#hashtagPrograssbar").remove();
	}

	//설정 팝업 오픈
	this.openDisplayPopup = function()
	{
		var hashtagDisplayPopup = window.open('./popup.hashtagDisplay.php', 'hashtagDisplayPopup', 'width=1000px,height=800px,scrollbars=no,resizeable=no');
		if(hashtagDisplayPopup){
			hashtagDisplayPopup.focus();
		}
	}

	__construct();
};

//해시태그 사용자설정 디스플레이 팝업 페이지
var HashtagPopupDisplayController = function()
{
	var self = this;
	var CoreController = new HashtagCoreController();
	var addHashtagSelector;
	var searchTextConfirm = false;
	var pageNum = 250;

	var __construct = function()
	{
		//전체리스트 해시태그 클릭시
		jQuery("#hashtagListBox").on("click", "div", self.focusAllListDiv);
		//노출되는 해시태그 리스트 클릭시
		jQuery("#hashtagDisplayListBox").on("click", "div", CoreController.changeAction);
		//해시태그 삭제
		jQuery("#hashtagDisplayListBox").on("click", "span", self.deleteHashtagLayout);

		jQuery("#hashtagSearch").keyup(function(){
			//빈값일때 전체 search
			if(jQuery(this).val() === '' && searchTextConfirm === true){
				self.clickHashtagListBox();
			}
			//해시태그 input listing
			var inputListParam = {
				mode: 'inputList',
				searchText: jQuery("#hashtagSearch").val(),
				obj: jQuery("#hashtagSearch")
			};
			CoreController.ajaxHashtagList(inputListParam);
		});
		//해시태그 전체리스트 검색
		jQuery("#hashtagSearchBtn").click(function(){
			self.clickHashtagListBox();
		});
		//해시태그 전체리스트 스크롤 페이징
		jQuery("#hashtagListBox").scroll(function(){
			self.scrollHashtagListBox();
		});
		//추가
		jQuery("#addHashtagBtn").click(self.addHashtagDisplay);
		//저장
		jQuery("#saveHashtagDisplayBtn").click(self.saveHashtagDisplayList);
		//닫기
		jQuery("#closeHashtagDisplayBtn").click(self.closeHashtagDisplayPopup);

		//해시태그 전체리스트 검색 INPUT
		jQuery("#hashtagSearch").trigger("keyup");

		//전체 해시태그 리스트 listing
		CoreController.ajaxHashtagList({mode:'allList', page:1, pageNum:pageNum});

		//순서변경
		jQuery(window).on("keydown", CoreController.moveTag);
	};

	//검색 클릭 리스트
	this.clickHashtagListBox = function()
	{
		searchTextConfirm = true;
		page = 1;
		var param = {
			mode : 'clickAllList',
			page : page,
			searchText : jQuery("#hashtagSearch").val(),
			pageNum : pageNum
		}
		CoreController.ajaxHashtagList(param);
	}

	//해시태그 전체 리스트 페이징
	this.scrollHashtagListBox = function()
	{
		if(jQuery("#hashtagListBox").outerHeight() + jQuery("#hashtagListBox").scrollTop() >= jQuery("#hashtagListBox").prop("scrollHeight")){
			if(page < 2) page = 2;
			var param = {
				mode : 'allList',
				page : page,
				searchText : jQuery("#hashtagSearch").val(),
				pageNum : pageNum
			}
			CoreController.ajaxHashtagList(param);
		}
	};

	//전체 해시태그 리스트 클릭시 액션
	this.focusAllListDiv = function()
	{
		var thisObj = jQuery(this);

		thisObj.css('background-color', '');
		thisObj.toggleClass("hashtagLayout-focusDiv");
	}

	//추가
	this.addHashtagDisplay = function()
	{
		//선택된 해시태그
		var selectObj = jQuery(".hashtagLayout-focusDiv");
		if(selectObj.length < 1){
			alert("추가할 해시태그를 선택하여 주세요.");
			return;
		}

		alert("중복된 해시태그는 제외하고 추가됩니다.");

		selectObj.each(function(){
			var duplicateHashtag = false; //중복여부
			var cloneHashtag = jQuery(this).clone(); //복제
			jQuery("#hashtagDisplayListBox div").each(function(){
				if(jQuery(this).attr('data-name') === cloneHashtag.attr('data-name')){
					duplicateHashtag = true;
					return false;
				}
			});

			jQuery(this).removeClass("hashtagLayout-focusDiv");
			if(duplicateHashtag === false){
				cloneHashtag.css("background-color", "white");
				jQuery("#hashtagDisplayListBox").append(cloneHashtag);
			}
		});
	}

	//저장
	this.saveHashtagDisplayList = function()
	{
		var param = {
			mode : 'saveDisplay',
			hashtagNo : jQuery.param(jQuery("#hashtagDisplayForm input[name='hashtagNo[]']"))
		};
		CoreController.ajaxHashtagList(param);
	}

	//삭제
	this.deleteHashtagLayout = function()
	{
		jQuery(this).closest("div").remove();
	}

	//닫기
	this.closeHashtagDisplayPopup = function()
	{
		window.close();
	}

	__construct();
};

//유저모드 > 상품상세페이지
var HashtagUserViewController = function(mobile)
{
	var self = this;
	var CoreController = new HashtagCoreController();

	var __construct = function()
	{
		//추가
		jQuery('#hashtagAddBtn').click(self.saveHashtagLiveUser);
		//input keyup event
		jQuery("#hashtag").keyup(function(){
			var checkCharacter = true;
			checkCharacter = self.keyupEvent();
			if(checkCharacter === false){
				return;
			}

			if(mobile === true){
				var ajaxUrl = jQuery("#cfgRootDir").attr('rootDir') + '/proc/hashtag/ajax.getHashtagData.php';
			}
			else {
				var ajaxUrl = '../proc/hashtag/ajax.getHashtagData.php';
			}
			var inputListParam = {
				mode:'inputList',
				searchText: jQuery("#hashtag").val(),
				ajaxUrl: ajaxUrl,
				obj: jQuery("#hashtag"),
			};
			CoreController.ajaxHashtagList(inputListParam);
		});
		jQuery("#hashtag").trigger("keyup");
	};

	this.keyupEvent = function()
	{
		//특수문자 체크
		var checkCharacter = true;
		checkCharacter = CoreController.checkSpecialCharacter(jQuery("#hashtag"));

		return checkCharacter;
	}

	//추가
	this.saveHashtagLiveUser = function()
	{
		var hashtag = jQuery("#hashtag");
		var hashtaglistBoxDiv = jQuery("#hashtagListBox div");
		var hashtagValue = jQuery.trim(hashtag.val()).replace(/ /g, '_');

		if(!hashtagValue){
			alert("해시태그명을 입력해 주세요.");
			hashtag.focus();
			return;
		}
		if(hashtagValue.length > 20){
			alert("최대 등록 글자수는 20글자 입니다.");
			CoreController.cutMaxLength(hashtag);
			return;
		}
		if(hashtaglistBoxDiv.length >= 10){
			alert("최대 등록 개수는 10개 입니다.");
			return;
		}
		var duplicateHashtag = false;
		hashtaglistBoxDiv.each(function(){
			if(hashtagValue === jQuery.trim(jQuery(this).attr('data-name'))){
				duplicateHashtag = true;
				return false;
			}
		});
		if(duplicateHashtag === true){
			alert("동일한 해시태그가 존재합니다.");
			return;
		}

		var mobilePath = '';
		if(mobile === true){
			var mobileRootDir = new Array();
			var showPath = document.location.pathname;
			if (showPath.charAt(0) == '/')	{
				showPath = showPath.substring(1);
			}
			mobileRootDir = showPath.split("/");
			mobilePath = '/' + mobileRootDir[0] + '/';
		}

		var param = {
			mode : 'addLiveUser',
			hashtag : hashtagValue,
			goodsno : jQuery("input[name='goodsno']").val(),
			mobilePath : mobilePath
		};
		CoreController.ajaxHashtagList(param);
	};

	__construct();
};

//관리모드 > 상품관리 > 빠른 해시태그 수정 페이지
var HashtagManageListController = function()
{
	var self = this;
	var CoreController = new HashtagCoreController();

	var __construct = function()
	{
		//해시태그 삭제
		jQuery("#admin-list-table").on("click", "span", CoreController.deleteManageHashtag);

		jQuery(document).on('keyup','.hashtagInputListSearch',function(){
			var inputListParam = {
				mode:'inputList',
				searchText: jQuery(this).val(),
				obj: jQuery(this),
			};
			CoreController.ajaxHashtagList(inputListParam);
		});

		jQuery('.hashtagInputListSearch').trigger("keyup");
	};

	__construct();
};

//해시태그 input list setting
//관리모드 > 상품관리 > 상품관리 리스트 페이지
//관리모드 > 상품선택 팝업
var HashtagInputListController = function()
{
	var self = this;
	var CoreController = new HashtagCoreController();

	var __construct = function()
	{
		jQuery(document).on('keyup','.hashtagInputListSearch',function(){
			var inputListParam = {
				mode:'inputList',
				searchText: jQuery(this).val(),
				obj: jQuery(this),
			};
			CoreController.ajaxHashtagList(inputListParam);
		});

		jQuery('.hashtagInputListSearch').trigger("keyup");
	};

	__construct();
};