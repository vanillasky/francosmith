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
					//input �ؽ��±� ����Ʈ
					case 'inputList':
						if(returnData != '' && returnData != null && returnData != 'undefined'){
							targetObj.autocomplete({
								source: returnData
							});
						}
					break;

					//�����߰� - ����
					case 'getLiveQuestion':
						jQuery('#guidedSelling-contents').append(returnData);

						self.setBackgroundColor();
					break;

					//�����߰� - ����
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

					//�亯�߰�
					case 'getLiveAnswer':
						var maxCount = self.displayMaxCount(param.unit_displayType);
						if(param.answerSortNum >= maxCount){
							self.displayHide(targetObj);
						}

						targetObj.before(returnData);
					break;

					//���÷��� ���� ����
					case 'changeAnswerDisplay':
						targetObj.html(returnData);

						self.setBackgroundImage(targetObj);
					break;

					//submit
					case 'checkBeforeSubmit':
						if(returnData !== ''){
							alert("�������� �ʴ� �ؽ��±װ� ���ԵǾ� �ֽ��ϴ�.");
							jQuery(".hashtagInputListSearch").eq(returnData).focus();
							return;
						}

						jQuery('#guidedSelling_form').submit();
					break;

					//��ǰȮ��
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

	//���α׷����� ����
	this.showProgressBar = function()
	{
		var progressImgMarginTop = Math.round((jQuery(window).height() - 116) / 2);
		var divHeight = (jQuery(window).height() > jQuery('body').height()) ? jQuery(window).height() : jQuery('body').height();

		jQuery("body").append('<div id="guidedSellingPrograssbar"><div style="position:absolute;top:0;left:0;background:#44515b;filter:alpha(opacity=80);opacity:0.8;width:100%;height:'+divHeight+'px;cursor:progress;z-index:999;margin:0 auto;text-align: center;"></div><div style="position:absolute;top:0;left:0;width:100%;height:'+divHeight+'px;margin:0 auto;text-align: center;z-index:1000;"><img src="../img/progress_bar2.gif" border="0" style="margin-top:'+progressImgMarginTop+'px;"/></div></div>');
	}

	//���α׷����� ����
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

//������� > ���̵弿�� ����� ������
var GuidedSellingController = function()
{
	var self = this;
	var CoreController = new GuidedSellingCoreController();

	var __construct = function()
	{
		//�����߰�
		jQuery('#guidedSelling_addQuestion').click(self.addQuestion);
		jQuery('#guidedSelling_addQuestion').trigger("click");

		//�亯�߰�
		jQuery(document).on('click', '.guidedSelling-addAnswer', self.addAnswer);

		//input �ؽ��±� ����Ʈ
		jQuery(document).on('keyup', '.hashtagInputListSearch', self.inputHashtagList);
		jQuery('.hashtagInputListSearch').trigger("keyup");

		//����ǥ
		jQuery('#guidedSelling_palette').click(openPallete);

		//submit
		jQuery('#guidedSelling_save').click(self.submitAction);

		//�̹��� ���� �˾�
		jQuery(document).on('click', '.guidedSelling_imageBtn, .guidedSelling_backgroundImageSaveBtn', self.openImagePopup);

		//���÷��� ���� ����
		jQuery(document).on('change', '.displayTypeSelector', self.changeAnswerDisplay);

		//���� ����
		jQuery(document).on('click', '.questionDeleteSelector', self.deleteQuestion);

		//�亯�׸� ����
		jQuery(document).on('click', '.answerDeleteSelector', self.deleteAnswer);

		//��� �̹��� ���
		jQuery(document).on('click', '.guidedSelling_backgroundImageSaveBtn', self.addBackgroundImage);

		//���� �̸�����
		jQuery("#guided_preview_backgroundColor").click(self.previewBackgroundColor);
		jQuery('#guided_preview_backgroundColor').trigger("click");

		//��ǰȮ�� - �ؽ��±� ������
		jQuery(document).on('click', '.guidedSelling_goodsConfirmSelector', self.openHashtagPage);

		//submit �� ���α׷����� ����
		jQuery('#guidedSelling_form').submit(CoreController.showProgressBar);
	};

	//�����߰�
	this.addQuestion = function(event)
	{
		var formMode = jQuery('#guidedSellingMode').val();
		if(event.originalEvent !== undefined){
			formMode = 'write';
		}

		if(jQuery('.guidedSelling-questionArea').length > 4){
			alert('�ִ� ���� ������ 5�� �Դϴ�.');
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

	//���� ����
	this.deleteQuestion = function()
	{
		var thisObj = jQuery(this);
		if(jQuery(".guidedSelling-questionArea").length <= 1){
			alert("������ 1�� �̻� ��ϵǾ�� �մϴ�.");
			return;
		}

		//������ input �����
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

	//�亯�׸� ����
	this.deleteAnswer = function()
	{
		var thisObj = jQuery(this);
		var answerAddButtonObj = thisObj.closest(".guidedSelling-itemArea").find(".guidedSelling-addAnswer");
		var answerCount = thisObj.closest(".guidedSelling-questionArea").find(".guidedSellingItemSelector").length;
		if(answerCount <= 2){
			alert("�亯�� 2�� �̻� ��ϵǾ�� �մϴ�.");
			return;
		}

		//������ input �����
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

	//���÷��� ���� ����
	this.changeAnswerDisplay = function()
	{
		var thisObj = jQuery(this);

		if(!confirm("�亯 ���� ���� �� �亯 ������ �ʱ�ȭ�˴ϴ�.\n����Ͻðڽ��ϱ�?")){
			var backDisplayValue = (thisObj.val() === 't' ) ? 'i' : 't';
			jQuery(this).val(backDisplayValue);
			return;
		}

		//�ȳ����� ����
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

			//change �� ���� ��׶��� �̹����� ���� ó��
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

	//�ȳ����� ����
	this.changeinfoMessage = function(thisObj)
	{
		var displayType = thisObj.val();
		var parentObj = thisObj.closest(".guidedSelling-selectArea");
		if(displayType === 'i'){
			var message1 = "�ؽ��±׸� �̿��� ������ �´� �亯�� ������ּ���. �̹����� �Բ� ��� �� ���� ȿ�����Դϴ�.";
			var message2 = "��ǰ�� ����� Ư¡�� �� ��Ÿ�� �̹����� �ؽ��±׸� ������ּ���.";
		}
		else if(displayType === 't'){
			var message1 = "�ؽ��±׸� �̿��� ������ �´� �亯�� ������ּ���.";
			var message2 = "��ǰ�� ����� Ư¡�� �� ��Ÿ�� ��� �̹����� �ؽ��±׸� ������ּ���. ����� PC���θ����� ����˴ϴ�.";
		}
		else {
			var message1 = '';
			var message2 = '';
		}

		parentObj.find(".guidedSelling-selectAreaInfo1").html(message1);
		parentObj.find(".guidedSelling-selectAreaInfo2").html(message2);
	}

	//�亯�߰�
	this.addAnswer = function()
	{
		var thisObj = jQuery(this);
		var parentQuestionArea = thisObj.closest(".guidedSelling-questionArea");
		var answerLength = parentQuestionArea.find('.guidedSellingItemSelector').length;
		var maxCount = CoreController.displayMaxCount(parentQuestionArea.find(".displayTypeSelector").val());

		if(answerLength >= maxCount){
			alert('�ִ� �亯 ������ '+maxCount+'�� �Դϴ�.');
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
			alert("���̵� ���� �̸��� �Է��� �ּ���.");
			jQuery("#guided_subject").focus();
			return;
		}

		if(!jQuery.trim(jQuery("#guided_backgroundColor").val())){
			alert("������ �Է��� �ּ���.");
			jQuery("#guided_backgroundColor").focus();
			return;
		}

		if(Number(jQuery.trim(jQuery("#guided_backgroundColor").val()).length) !== 6){
			alert("������ 6�ڸ� HTML ����ǥ�� �����Ͽ� �ּ���.");
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
			alert("������ �Է��� �ּ���.");
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
			alert("�ؽ��±׸� �Է��� �ּ���.");
			return;
		}

		if(jQuery("input[name='existCheckImageInput[]']").not("[value='y']").length > 0){
			alert("�亯 �̹����� ����� �ּ���.");
			return;
		}

		var Param = {
			mode : 'checkBeforeSubmit',
			hashtagName : jQuery(".hashtagInputListSearch").serialize(),
		};
		CoreController.getAjaxData(Param);
	};

	//�̹��� ���� �˾� ����
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

	//��׶��� �̹��� ���
	this.addBackgroundImage = function()
	{
		jQuery(this).closest(".guidedSelling-backgroundImageBtnArea").find('input[type="file"]').trigger('click');
	}

	//���� �̸�����
	this.previewBackgroundColor = function()
	{
		var backgroundColor = jQuery("#guided_backgroundColor").val();
		if(backgroundColor){
			backgroundColorPreview = backgroundColor;
			jQuery(".guidedSelling-questionArea").css("background-color", "#" + backgroundColor);
		}
	}

	//��ǰȮ�� - �ؽ��±� ������
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
			alert("�ؽ��±׸� �Է��� �ּ���.");
			hashtagInputObj.focus();
			return;
		}
	}

	__construct();
}

//������� > ���̵弿�� ����Ʈ ������
var GuidedSellingListController = function()
{
	var self = this;
	var CoreController = new GuidedSellingCoreController();

	var __construct = function()
	{
		//����
		jQuery('.guidedSellingModify').click(self.locateModify);
		//����
		jQuery('.guidedSellingDelete').click(self.locateDelete);
		//url����
		jQuery('.guidedSellingCopyUrl').click(function(){
			self.copyUrl('pc', jQuery(this).attr('data-no'));
		});
		//mobile url����
		jQuery('.guidedSellingCopyMobileUrl').click(function(){
			self.copyUrl('mobile', jQuery(this).attr('data-no'));
		});
		//��������
		jQuery('.guidedSellingCreateWidget').click(self.openCreateWidgetPopup);
	};

	this.locateModify = function()
	{
		window.location.href = './adm_goods_hashtag_guidedSelling_write.php?mode=modify&guided_no=' + jQuery(this).attr('data-no');
	};

	this.locateDelete = function()
	{
		if(confirm("���� �����Ͻðڽ��ϱ�?")){
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
			alert("����Ǿ����ϴ�.");
			window.clipboardData.setData("Text", clipData);
		}
		else {
			prompt("�ڵ带 Ŭ������� ����(Ctrl+C) �ϼ���.", clipData);
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

//������� > ���̵� ���� ���� ������
var GuidedSellingWidgetPopup = function()
{
	var self = this;
	var CoreController = new GuidedSellingCoreController();

	var __construct = function()
	{
		//�ҽ�����
		jQuery('#guidedSelling_sourceCopy').click(self.copySource);
		//�˾� â�ݱ�
		jQuery('#guidedSelling_popupClose').click(self.closePopup);
		//���� ������ ����
		jQuery("#widgetSize").keyup(self.getCreateiframeCode);
		jQuery('#widgetSize').trigger("keyup");
		//���� Ÿ��
		jQuery(":radio[name='widgetType']").click(self.getCreateiframeCode);
		//���� ����
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
			alert("����Ǿ����ϴ�.");
			window.clipboardData.setData("Text", clipData);
		}
		else {
			prompt("�ڵ带 Ŭ������� ����(Ctrl+C) �ϼ���.", clipData);
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
			alert("���� Ÿ���� ������ �ּ���.");
			return;
		}
		var widgetSrc = rootDir + "/proc/guidedSellingWidget.php?guided_no=" + jQuery("#guided_no").val() + "&guided_widgetId=" + widgetId;
		if(!jQuery.trim(widgetWidth)){
			alert("���� ����� �Է��� �ּ���.");
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

//������� > �̹��� ���ε� �˾�
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