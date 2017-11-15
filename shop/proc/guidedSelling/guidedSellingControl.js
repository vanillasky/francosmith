/**
 * GUIDED SELLING
 * @author workingby <bumyul2000@godo.co.kr>
 * @version 1.0
 * @date 2016-11-24
 */
var ajaxUrl = '../../proc/guidedSelling/ajax.getGuidedSellingData.php';
var displayType_maxcount_i = 5;
var displayType_maxcount_t = 10;
var backgroundColorPreview = '';

var GuidedSellingCoreController = function()
{
	var self = this;

	this.getAjaxData = function(param)
	{
		if(param.obj){
			var targetObj = param.obj;
			param.obj = null;
		}
		if(param.ajaxUrl){
			ajaxUrl = param.ajaxUrl;
		}

		jQuery.post(ajaxUrl, param, function(res){
			var dataArray = new Array();
			dataArray = res.split("|");

			if(dataArray[0] === 'success'){
				var returnData = eval("("+dataArray[1]+")");

				switch(param.mode){
					//input 해시태그 리스트
					case 'inputList':
						if(returnData != '' && returnData != null && returnData != 'undefined'){
							targetObj.autocomplete({
								source: returnData
							});
						}
					break;

					//질문추가 - 쓰기
					case 'getLiveQuestion':
						jQuery('#guidedSelling-contents').append(returnData);

						self.setBackgroundColor();
					break;

					//질문추가 - 수정
					case 'getLiveQuestionModify':
						jQuery.each(returnData, function(index){
							jQuery('#guidedSelling-contents').append(returnData[index]);
						});
						jQuery.each(jQuery(".guidedSelling-questionArea"), function(index, el){
							var thisObj = jQuery(el);
							var maxCount = self.displayMaxCount(thisObj.find(".displayTypeSelector").val());
							if(thisObj.find(".guidedSellingItemSelector").length >= maxCount){
								self.displayHide(thisObj.find(".guidedSelling-addAnswer"));
							}
						});
					break;

					//답변추가
					case 'getLiveAnswer':
						var maxCount = self.displayMaxCount(param.unit_displayType);
						if(param.answerSortNum >= maxCount){
							self.displayHide(targetObj);
						}

						targetObj.before(returnData);
					break;

					//디스플레이 유형 변경
					case 'changeAnswerDisplay':
						targetObj.html(returnData);

						self.setBackgroundImage(targetObj);
					break;

					//submit
					case 'checkBeforeSubmit':
						if(returnData !== ''){
							alert("존재하지 않는 해시태그가 포함되어 있습니다.");
							jQuery(".hashtagInputListSearch").eq(returnData).focus();
							return;
						}

						jQuery('#guidedSelling_form').submit();
					break;

					//상품확인
					case 'openHashtagPage':
						window.open('../../goods/goods_hashtag_list.php?hashtag=' + param.hashtagName,'_blank');
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

	//프로그레스바 노출
	this.showProgressBar = function()
	{
		var progressImgMarginTop = Math.round((jQuery(window).height() - 116) / 2);
		var divHeight = (jQuery(window).height() > jQuery('body').height()) ? jQuery(window).height() : jQuery('body').height();

		jQuery("body").append('<div id="guidedSellingPrograssbar"><div style="position:absolute;top:0;left:0;background:#44515b;filter:alpha(opacity=80);opacity:0.8;width:100%;height:'+divHeight+'px;cursor:progress;z-index:999;margin:0 auto;text-align: center;"></div><div style="position:absolute;top:0;left:0;width:100%;height:'+divHeight+'px;margin:0 auto;text-align: center;z-index:1000;"><img src="../img/progress_bar2.gif" border="0" style="margin-top:'+progressImgMarginTop+'px;"/></div></div>');
	}

	//프로그레스바 감춤
	this.hiddenProgressBar = function()
	{
		if(jQuery("#guidedSellingPrograssbar").length > 0){
			jQuery("#guidedSellingPrograssbar").remove();
		}
	}

	this.setBackgroundColor = function()
	{
		jQuery(".guidedSelling-questionArea:last-child").css('background-color', '#' + backgroundColorPreview);
	};

	this.setBackgroundImage = function(targetObj)
	{
		if(targetObj.closest(".guidedSelling-questionArea").find(".displayTypeSelector").val() === 't'){
			targetObj.css("background-image", "url('../img/background_image_sample.png')");
		}
		else {
			targetObj.css("background-image", "");
		}
	};

	this.displayMaxCount = function(objValue)
	{
		if(objValue === 'i'){
			var maxCount = displayType_maxcount_i;
		}
		else if(objValue=== 't'){
			var maxCount = displayType_maxcount_t;
		}
		else {
			var maxCount = 0;
		}

		return maxCount;
	};

	this.displayHide = function(obj)
	{
		obj.css('display', 'none');
	};

	this.displayShow = function(obj)
	{
		obj.css('display', '');
	};
}

//관리모드 > 가이드셀링 만들기 페이지
var GuidedSellingController = function()
{
	var self = this;
	var CoreController = new GuidedSellingCoreController();

	var __construct = function()
	{
		//질문추가
		jQuery('#guidedSelling_addQuestion').click(self.addQuestion);
		jQuery('#guidedSelling_addQuestion').trigger("click");

		//답변추가
		jQuery(document).on('click', '.guidedSelling-addAnswer', self.addAnswer);

		//input 해시태그 리스트
		jQuery(document).on('keyup', '.hashtagInputListSearch', self.inputHashtagList);
		jQuery('.hashtagInputListSearch').trigger("keyup");

		//색상표
		jQuery('#guidedSelling_palette').click(openPallete);

		//submit
		jQuery('#guidedSelling_save').click(self.submitAction);

		//이미지 저장 팝업
		jQuery(document).on('click', '.guidedSelling_imageBtn, .guidedSelling_backgroundImageSaveBtn', self.openImagePopup);

		//디스플레이 유형 변경
		jQuery(document).on('change', '.displayTypeSelector', self.changeAnswerDisplay);

		//질문 삭제
		jQuery(document).on('click', '.questionDeleteSelector', self.deleteQuestion);

		//답변항목 삭제
		jQuery(document).on('click', '.answerDeleteSelector', self.deleteAnswer);

		//배경 이미지 등록
		jQuery(document).on('click', '.guidedSelling_backgroundImageSaveBtn', self.addBackgroundImage);

		//배경색 미리보기
		jQuery("#guided_preview_backgroundColor").click(self.previewBackgroundColor);
		jQuery('#guided_preview_backgroundColor').trigger("click");

		//상품확인 - 해시태그 페이지
		jQuery(document).on('click', '.guidedSelling_goodsConfirmSelector', self.openHashtagPage);

		//submit 시 프로그레스바 노출
		jQuery('#guidedSelling_form').submit(CoreController.showProgressBar);
	};

	//질문추가
	this.addQuestion = function(event)
	{
		var formMode = jQuery('#guidedSellingMode').val();
		if(event.originalEvent !== undefined){
			formMode = 'write';
		}

		if(jQuery('.guidedSelling-questionArea').length > 4){
			alert('최대 질문 개수는 5개 입니다.');
			return;
		}

		if(formMode === 'write'){
			var param = {
				mode : 'getLiveQuestion',
				formMode : formMode,
				unit_displayType : 'i',
			};
		}
		else if(formMode === 'modify'){
			var param = {
				mode : 'getLiveQuestionModify',
				formMode : formMode,
				guided_no : jQuery('#guided_no').val(),
			};
		}
		else {}

		CoreController.getAjaxData(param);
	};

	//질문 삭제
	this.deleteQuestion = function()
	{
		var thisObj = jQuery(this);
		if(jQuery(".guidedSelling-questionArea").length <= 1){
			alert("질문은 1개 이상 등록되어야 합니다.");
			return;
		}

		//삭제시 input 남기기
		var dataNo = thisObj.closest(".guidedSelling-questionArea").attr('data-no');
		if(dataNo > 0){
			var inputParam = {
				type: "hidden",
				name: "question_deleteNo[]",
				value: dataNo
			};
			jQuery("<input></input>").attr(inputParam).appendTo(jQuery("#guidedSelling_form"));
		}
		thisObj.closest(".guidedSelling-questionArea").remove();
	};

	//답변항목 삭제
	this.deleteAnswer = function()
	{
		var thisObj = jQuery(this);
		var answerAddButtonObj = thisObj.closest(".guidedSelling-itemArea").find(".guidedSelling-addAnswer");
		var answerCount = thisObj.closest(".guidedSelling-questionArea").find(".guidedSellingItemSelector").length;
		if(answerCount <= 2){
			alert("답변은 2개 이상 등록되어야 합니다.");
			return;
		}

		//삭제시 input 남기기
		var dataNo = thisObj.closest(".guidedSellingItemSelector").attr('data-no');
		if(dataNo > 0){
			var inputParam = {
				type: "hidden",
				name: "answer_deleteNo[]",
				value: dataNo,
			};
			jQuery("<input></input>").attr(inputParam).appendTo(jQuery("#guidedSelling_form"));
		}
		thisObj.closest(".guidedSellingItemSelector").remove();
		CoreController.displayShow(answerAddButtonObj);
	};

	//디스플레이 유형 변경
	this.changeAnswerDisplay = function()
	{
		var thisObj = jQuery(this);

		if(!confirm("답변 유형 변경 시 답변 내용이 초기화됩니다.\n계속하시겠습니까?")){
			var backDisplayValue = (thisObj.val() === 't' ) ? 'i' : 't';
			jQuery(this).val(backDisplayValue);
			return;
		}

		//안내문구 변경
		self.changeinfoMessage(thisObj);

		if(jQuery("#guidedSellingMode").val() === 'modify'){
			var guidedSellingItemSelector = thisObj.closest(".guidedSelling-questionArea").find(".guidedSellingItemSelector");
			var dataNo = 0;
			var inputParam = new Array();
			jQuery(guidedSellingItemSelector).each(function(index, element){
				dataNo = jQuery(element).attr('data-no');
				if(dataNo > 0){
					inputParam = {
						type: "hidden",
						name: "answer_deleteNo[]",
						value: dataNo,
					};
					jQuery("<input></input>").attr(inputParam).appendTo(jQuery("#guidedSelling_form"));
				}
			});

			//change 시 기존 백그라운드 이미지는 삭제 처리
			var dataQuestionNo = thisObj.closest(".guidedSelling-questionArea").attr('data-no');
			if(dataQuestionNo > 0){
				var inputParam = {
					type: "hidden",
					name: "unit_backgroundImageDeleteNo["+dataQuestionNo+"]",
					value: 'y'
				};
				jQuery("<input></input>").attr(inputParam).appendTo(jQuery("#guidedSelling_form"));
			}
		}

		var parentQuestionArea = thisObj.closest(".guidedSelling-questionArea");
		var param = {
			mode : 'changeAnswerDisplay',
			uniqueKey : parentQuestionArea.attr('data-uniqueKey'),
			unit_displayType : thisObj.val(),
			obj : parentQuestionArea.find('.guidedSelling-itemArea'),
		};

		CoreController.getAjaxData(param);
	};

	//안내문구 변경
	this.changeinfoMessage = function(thisObj)
	{
		var displayType = thisObj.val();
		var parentObj = thisObj.closest(".guidedSelling-selectArea");
		if(displayType === 'i'){
			var message1 = "해시태그를 이용해 질문에 맞는 답변을 만들어주세요. 이미지와 함께 등록 시 더욱 효과적입니다.";
			var message2 = "상품에 공통된 특징이 잘 나타난 이미지와 해시태그를 등록해주세요.";
		}
		else if(displayType === 't'){
			var message1 = "해시태그를 이용해 질문에 맞는 답변을 만들어주세요.";
			var message2 = "상품에 공통된 특징이 잘 나타난 배경 이미지와 해시태그를 등록해주세요. 배경은 PC쇼핑몰에만 적용됩니다.";
		}
		else {
			var message1 = '';
			var message2 = '';
		}

		parentObj.find(".guidedSelling-selectAreaInfo1").html(message1);
		parentObj.find(".guidedSelling-selectAreaInfo2").html(message2);
	}

	//답변추가
	this.addAnswer = function()
	{
		var thisObj = jQuery(this);
		var parentQuestionArea = thisObj.closest(".guidedSelling-questionArea");
		var answerLength = parentQuestionArea.find('.guidedSellingItemSelector').length;
		var maxCount = CoreController.displayMaxCount(parentQuestionArea.find(".displayTypeSelector").val());

		if(answerLength >= maxCount){
			alert('최대 답변 개수는 '+maxCount+'개 입니다.');
			return;
		}
		var param = {
			mode : 'getLiveAnswer',
			uniqueKey : parentQuestionArea.attr('data-uniqueKey'),
			unit_displayType : parentQuestionArea.find('.displayTypeSelector').val(),
			answerSortNum : answerLength+1,
			obj : thisObj,
		};
		CoreController.getAjaxData(param);
	};

	//input Hashtag List
	this.inputHashtagList = function()
	{
		var thisObj = jQuery(this);
		var inputListParam = {
			mode : 'inputList',
			searchText : thisObj.val(),
			obj : thisObj,
		};
		CoreController.getAjaxData(inputListParam);
	};

	//submit
	this.submitAction = function()
	{
		if(!jQuery.trim(jQuery("#guided_subject").val())){
			alert("가이드 셀링 이름을 입력해 주세요.");
			jQuery("#guided_subject").focus();
			return;
		}

		if(!jQuery.trim(jQuery("#guided_backgroundColor").val())){
			alert("배경색을 입력해 주세요.");
			jQuery("#guided_backgroundColor").focus();
			return;
		}

		if(Number(jQuery.trim(jQuery("#guided_backgroundColor").val()).length) !== 6){
			alert("배경색은 6자리 HTML 색상표로 기재하여 주세요.");
			jQuery("#guided_backgroundColor").focus();
			return;
		}

		var questionValueCheck = true;
		jQuery(".questionSelector").each(function(){
			if(!jQuery.trim(jQuery(this).val())){
				questionValueCheck = false;
				jQuery(this).focus();
				return;
			}
		});
		if(questionValueCheck === false){
			alert("질문을 입력해 주세요.");
			return;
		}

		var hashtagNameValueCheck = true;
		jQuery(".hashtagInputListSearch").each(function(){
			if(!jQuery.trim(jQuery(this).val())){
				hashtagNameValueCheck = false;
				jQuery(this).focus();
				return;
			}
		});
		if(hashtagNameValueCheck === false){
			alert("해시태그를 입력해 주세요.");
			return;
		}

		if(jQuery("input[name='existCheckImageInput[]']").not("[value='y']").length > 0){
			alert("답변 이미지를 등록해 주세요.");
			return;
		}

		var Param = {
			mode : 'checkBeforeSubmit',
			hashtagName : jQuery(".hashtagInputListSearch").serialize(),
		};
		CoreController.getAjaxData(Param);
	};

	//이미지 저장 팝업 오픈
	this.openImagePopup = function()
	{
		var thisObj = jQuery(this);
		var parentItemAreaObj = thisObj.closest(".guidedSelling-itemArea");
		var displayType = parentItemAreaObj.siblings(".guidedSelling-selectArea").find(".displayTypeSelector").val();
		var uniqueKey = thisObj.attr('data-uniqueKey');
		if(displayType === 'i'){
			var mode = 'saveTempImage';
			var thisIndex = parentItemAreaObj.find(".guidedSelling_imageBtn").index(jQuery(this));
			var height = 600;
		}
		else if(displayType === 't'){
			var mode = 'saveTempBackgroundImage';
			var thisIndex = 0;
			var height = 550;
		}
		else {
			return;
		}

		popupLayer('./popup.guidedSellingImage.php?mode='+mode+'&uniqueKey=' + uniqueKey + '&index=' + thisIndex, 590, height);
	};

	//백그라운드 이미지 등록
	this.addBackgroundImage = function()
	{
		jQuery(this).closest(".guidedSelling-backgroundImageBtnArea").find('input[type="file"]').trigger('click');
	}

	//배경색 미리보기
	this.previewBackgroundColor = function()
	{
		var backgroundColor = jQuery("#guided_backgroundColor").val();
		if(backgroundColor){
			backgroundColorPreview = backgroundColor;
			jQuery(".guidedSelling-questionArea").css("background-color", "#" + backgroundColor);
		}
	}

	//상품확인 - 해시태그 페이지
	this.openHashtagPage = function()
	{
		var hashtagInputObj = jQuery(this).closest(".guidedSellingItemSelector").find(".hashtagInputListSearch");
		var hashtag = jQuery.trim(hashtagInputObj.val()).replace(/ /g, '_');
		if(hashtag){
			var Param = {
				mode : 'openHashtagPage',
				hashtagName : hashtag,
			};
			CoreController.getAjaxData(Param);
		}
		else {
			alert("해시태그를 입력해 주세요.");
			hashtagInputObj.focus();
			return;
		}
	}

	__construct();
}

//관리모드 > 가이드셀링 리스트 페이지
var GuidedSellingListController = function()
{
	var self = this;
	var CoreController = new GuidedSellingCoreController();

	var __construct = function()
	{
		//수정
		jQuery('.guidedSellingModify').click(self.locateModify);
		//삭제
		jQuery('.guidedSellingDelete').click(self.locateDelete);
		//url복사
		jQuery('.guidedSellingCopyUrl').click(function(){
			self.copyUrl('pc', jQuery(this).attr('data-no'));
		});
		//mobile url복사
		jQuery('.guidedSellingCopyMobileUrl').click(function(){
			self.copyUrl('mobile', jQuery(this).attr('data-no'));
		});
		//위젯생성
		jQuery('.guidedSellingCreateWidget').click(self.openCreateWidgetPopup);
	};

	this.locateModify = function()
	{
		window.location.href = './adm_goods_hashtag_guidedSelling_write.php?mode=modify&guided_no=' + jQuery(this).attr('data-no');
	};

	this.locateDelete = function()
	{
		if(confirm("정말 삭제하시겠습니까?")){
			window.location.href = './adm_goods_hashtag_guidedSelling_indb.php?mode=delete&guided_no=' + jQuery(this).attr('data-no');
		}
	};

	this.copyUrl = function(copyType, dataNo)
	{
		if(copyType === 'pc'){
			var root = jQuery('#shopRootDir').val();
		}
		else if(copyType === 'mobile'){
			var root = jQuery('#mobileShopRootDir').val();
		}
		else {
			return;
		}

		var clipData = root + '/goods/goods_guidedSelling_list.php?guided_no=' + dataNo + '&step=1';

		if(window.clipboardData){
			alert("복사되었습니다.");
			window.clipboardData.setData("Text", clipData);
		}
		else {
			prompt("코드를 클립보드로 복사(Ctrl+C) 하세요.", clipData);
		}
		return;
	}

	this.openCreateWidgetPopup = function()
	{
		var guided_no = jQuery(this).attr('data-no');
		window.open("./popup.guidedSellingCreateWidget.php?guided_no=" + guided_no, "guidedSelling", "scrollbars=yes, width=1050px, height=800px");
	}

	__construct();
}

//관리모드 > 가이드 셀링 위젯 페이지
var GuidedSellingWidgetPopup = function()
{
	var self = this;
	var CoreController = new GuidedSellingCoreController();

	var __construct = function()
	{
		//소스복사
		jQuery('#guidedSelling_sourceCopy').click(self.copySource);
		//팝업 창닫기
		jQuery('#guidedSelling_popupClose').click(self.closePopup);
		//위젯 사이즈 설정
		jQuery("#widgetSize").keyup(self.getCreateiframeCode);
		jQuery('#widgetSize').trigger("keyup");
		//위젯 타입
		jQuery(":radio[name='widgetType']").click(self.getCreateiframeCode);
		//위젯 생성
		jQuery("#guidedSelling_createWidget").click(self.getAdminWidgetLaout);
	};

	this.closePopup = function()
	{
		window.close();
	};

	this.copySource = function()
	{
		var clipData = jQuery('#guidedSellingWidget-sourceCodeArea').text();
		if(window.clipboardData){
			alert("복사되었습니다.");
			window.clipboardData.setData("Text", clipData);
		}
		else {
			prompt("코드를 클립보드로 복사(Ctrl+C) 하세요.", clipData);
		}
		return;
	};

	this.getCreateiframeCode = function()
	{
		var widgetName = 'guidedSellingWidget';
		var widgetId = 'guidedSellingWidget_' + jQuery.now();
		var widgetType = jQuery(":radio[name='widgetType']:checked").val();
		if(widgetType === 'pc'){
			var rootDir = jQuery("#shopRootDir").val();
			jQuery("#guidedSelling_widgetSizeArea").css("display", "");
			var widgetWidth = jQuery("#widgetSize").val();
			if(!jQuery.trim(widgetWidth)){
				widgetWidth = '1000px';
			}
			else {
				widgetWidth = widgetWidth + 'px';
			}
		}
		else if(widgetType === 'mobile'){
			var rootDir = jQuery("#mobileRootDir").val();
			jQuery("#guidedSelling_widgetSizeArea").css("display", "none");
			var widgetWidth = '100%';
		}
		else {
			alert("위젯 타입을 선택해 주세요.");
			return;
		}
		var widgetSrc = rootDir + "/proc/guidedSellingWidget.php?guided_no=" + jQuery("#guided_no").val() + "&guided_widgetId=" + widgetId;
		if(!jQuery.trim(widgetWidth)){
			alert("위젯 사이즈를 입력해 주세요.");
			return;
		}

		var iframeHtml = "<iframe name='"+widgetName+"' id='"+widgetId+"' src='"+widgetSrc+"' allowTransparency='true' frameborder='0' scrolling='no' style='border:none; overflow:hidden;width:"+widgetWidth+";'></iframe>";

		jQuery("#guidedSellingWidget-sourceCodeArea").text(iframeHtml);
	};

	this.getAdminWidgetLaout = function()
	{
		var previewText = jQuery("#guidedSellingWidget-sourceCodeArea").text().replace("guided_no", "preview=y&guided_no");
		jQuery("#guidedSellingWidget-previewArea").html(previewText);
	}

	__construct();
}

//관리모드 > 이미지 업로드 팝업
var GuidedSellingImageLayerController = function()
{
	var self = this;
	var CoreController = new GuidedSellingCoreController();

	var __construct = function()
	{
	};

	this.showProgressBar = function()
	{
		CoreController.showProgressBar();
	};

	this.hiddenProgressBar = function()
	{
		CoreController.hiddenProgressBar();
	};

	__construct();
}