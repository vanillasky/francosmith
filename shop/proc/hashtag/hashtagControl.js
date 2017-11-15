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
		//ajax url �缳�� - ������� �Ͻ�
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
					//������� > �ؽ��±� ���� > ����Ʈ
					//������� > �ؽ��±� ���ü��� > ����ڼ��� �˾�
					case 'allList':
						var hashtagData = eval("("+dataArray[1]+")");
						if(hashtagData){
							jQuery.each(hashtagData, function( index, value ) {
								jQuery("#hashtagListBox").append(value);
							});
							page += 1;
						}
					break;

					//������� > �ؽ��±� ���� > ����Ʈ
					//������� > �ؽ��±� ���ü��� > ����ڼ��� �˾�
					case 'clickAllList':
						var hashtagData = eval("("+dataArray[1]+")");

						jQuery("#hashtagListBox").empty();
						if(hashtagData){
							jQuery.each(hashtagData, function( index, value ) {
								jQuery("#hashtagListBox").append(value);
							});
						}
					break;

					//������� > ��ǰ�� > ��ǰ �ؽ��±�
					case 'goodsList':
						var hashtagData = eval("("+dataArray[1]+")");

						if(hashtagData){
							jQuery.each(hashtagData, function( index, value ) {
								jQuery("#hashtagListBox").append(value);
							});
						}
					break;

					//input �ؽ��±� ����Ʈ [������ ���� ���]
					case 'inputList':
						var hashtagData = eval("("+dataArray[1]+")");

						if(hashtagData != '' && hashtagData != null && hashtagData != 'undefined'){
							hashtagInputListSearchObj.autocomplete({
								source: hashtagData
							});
						}
					break;

					//������� > ��ǰ�� > ��ǰ �ؽ��±�
					case 'addLayout':
						var hashtagData = eval("("+dataArray[1]+")");

						jQuery("#hashtagListBox").append(hashtagData);
						jQuery("#hashtag").val('');
					break;

					//������� > �ؽ��±� ���� > �߰�
					case 'addLive':
						var hashtagData = eval("("+dataArray[1]+")");

						jQuery("#hashtagListBox").prepend(hashtagData);
						jQuery("#hashtag").val('');
					break;

					//������� > �ؽ��±� ���� > ����
					case 'deleteLive':
						deleteHashtagSelector.remove();

						//�ؽ��±� ���� ����
						self.resetInfo();
					break;

					//������� > ���� �ؽ��±� ���� > ����
					case 'deleteManageLive':
						deleteHashtagSelector.remove();
					break;

					//������� > �ؽ��±� ���� ���� > ����� ���� �˾� ������
					case 'saveDisplay':
						alert("����Ǿ����ϴ�.");
						window.location.reload();
					break;

					//������� > ��ǰ�������� > �߰�
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

	//�ؽ��±� �� Ŭ����
	this.changeAction = function()
	{
		hashtagSelector = jQuery(this);

		//���󺯰�
		self.changeBackground();

		//�ؽ��±� ��������
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

	//�ؽ��±� ���� ����
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

	//�ؽ��±� ���� ����
	this.resetInfo = function()
	{
		jQuery("#hashtagWidgetUrl").html("�ؽ��±׸� �����Ͽ� �ּ���.");
		jQuery("#hashtagRegistGoodsCount").html(0);
	}

	this.deleteHashtag = function()
	{
		if(confirm("���� �� ��ǰ�� ��ϵ� �ؽ��±׵� �����˴ϴ�.\n����Ͻðڽ��ϱ�?")){
			deleteHashtagSelector = jQuery(this).closest("div");
			var hashtagName = deleteHashtagSelector.attr('data-name');

			self.ajaxHashtagList({mode:'deleteLive', hashtag:hashtagName});
		}
	};

	//���� > ���� �ؽ��±� ���� > ����
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

	//���̾ƿ��� ����
	this.deleteHashtagLayout = function()
	{
		jQuery(this).closest("div").remove();
	}

	//�ʰ� length ����
	this.cutMaxLength = function(thisObj)
	{
		var maxLength = 20;
		var inputValue = thisObj.val();
		thisObj.val(inputValue.substr(0, maxLength));
	}

	//Ư������ üũ
	this.checkSpecialCharacter = function(thisObj)
	{
		var thisObjValue = thisObj.val();
		var regPattern = /[~!@\#$%<>^&*\()\-=+\��\\\|\/\.\,]/gi;
		if(regPattern.test(thisObjValue)) {
			alert("Ư�����ڴ� �Է� �� �� �����ϴ�.");
			thisObj.val(thisObjValue.replace(regPattern, ""));
			return false;
		}

		return true;
	}
};

//�ؽ��±� ���� ������
var HashtagListController = function()
{
	var self = this;
	var CoreController = new HashtagCoreController();
	var searchTextConfirm = false;
	var pageNum = 150;

	var __construct = function()
	{
		//������������
		jQuery("#hashtagListConfig-save-btn").click(self.saveListConfigForm);
		//�ؽ��±� �˻� input listing
		jQuery(document).on('keyup','.hashtagInputListSearch',function(){
			var inputListParam = {
				mode: 'inputList',
				searchText: jQuery(this).val(),
				obj:jQuery(this)
			};
			CoreController.ajaxHashtagList(inputListParam);
		});
		//�ؽ��±� ���� ����
		jQuery("#hashtagListBox").on("click", "div", CoreController.changeAction);
		//�ؽ��±� ����
		jQuery("#hashtagListBox").on("click", "span", CoreController.deleteHashtag);
		//�ؽ��±� �ڵ����
		jQuery("#hashtagCreateCodeBtn").click(self.popupHashtagCreateCode);
		//�ؽ��±� �߰�
		jQuery("#hashtagAddBtn").click(self.addHashtag);
		//input keyup event
		jQuery("#hashtag").keyup(self.keyupEvent);
		//�ؽ��±� �˻�
		jQuery("#hashtagSearch").keyup(self.checkVoidSearch);
		jQuery("#hashtagSearchBtn").click(function(){
			self.clickHashtagListBox();
		});
		//�ؽ��±� ����Ʈ ��ũ�� ����¡
		jQuery("#hashtagListBox").scroll(function(){
			self.scrollHashtagListBox();
		});

		//listing
		CoreController.ajaxHashtagList({mode:'allList', page:1, pageNum:pageNum});
		//set add input
		jQuery('.hashtagInputListSearch').trigger("keyup");
	};

	//�ؽ��±� ��ǰ����Ʈ ���� ���� ����
	this.saveListConfigForm = function()
	{
		jQuery('#mode').val('listConfigSave');
		jQuery('#hashtagListConfigForm').submit();
	};

	//���϶� ��ü search
	this.checkVoidSearch = function()
	{
		if(jQuery(this).val() === '' && searchTextConfirm === true){
			self.clickHashtagListBox();
		}
	}

	//�˻� Ŭ�� ����Ʈ
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

	//�ؽ��±� ����Ʈ ����¡
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

	//�ؽ��±� �߰�
	this.addHashtag = function()
	{
		var hashtag = jQuery("#hashtag");
		if(!jQuery.trim(hashtag.val())){
			alert("�ؽ��±׸��� �Է��� �ּ���.");
			hashtag.focus();
			return;
		}
		if(hashtag.val().length > 20){
			alert("�ִ� ��� ���ڼ��� 20���� �Դϴ�.");
			CoreController.cutMaxLength(hashtag);
			return;
		}

		CoreController.ajaxHashtagList({ mode: 'addLive', hashtag: hashtag.val() });
	};

	this.keyupEvent = function(event)
	{
		//Ư������ üũ
		var checkCharacter = true;
		checkCharacter = CoreController.checkSpecialCharacter(jQuery(this));
		if(checkCharacter === false){
			return;
		}

		//�ؽ��±� �߰� - enter
		var event = event || window.event;
		var keyCode = event.keyCode || event.which;
		if(keyCode === 13){
			self.addHashtag();
		}
	}

	//�ؽ��±� �ڵ� ���� �˾�
	this.popupHashtagCreateCode = function()
	{
		if(!jQuery("#hashtagWidget_name").val()){
			alert("�ؽ��±׸� �����Ͽ� �ּ���.");
			return;
		}
		if(!jQuery("#hashtagWidget_width").val()){
			alert("���� ������ �Է��Ͽ� �ּ���.");
			return;
		}
		if(!jQuery("#hashtagWidget_height").val()){
			alert("���� ������ �Է��Ͽ� �ּ���.");
			return;
		}
		if(!jQuery("#hashtagWidget_iframeWidth").val()){
			alert("IFRAME ���� ����� �Է��Ͽ� �ּ���.");
			return;
		}
		if(!jQuery("#hashtagWidget_imageWidth").val()){
			alert("�̹��� ���� ����� �Է��Ͽ� �ּ���.");
			return;
		}

		var parameter = "?hashtag=" + jQuery("#hashtagWidget_name").val() + "&hashtagWidth=" + jQuery("#hashtagWidget_width").val() + "&hashtagHeight=" + jQuery("#hashtagWidget_height").val() + "&hashtagIframeWidth=" + jQuery("#hashtagWidget_iframeWidth").val() + "&hashtagImageWidth=" + jQuery("#hashtagWidget_imageWidth").val();
		window.open('./popup.hashtagCreateCode.php' + parameter,'hashtagCreateCode', 'width=1050px, height=800px, scrollbars=yes');
	}

	__construct();
};

//��ǰ ��������
var HashtagGoodsViewController = function()
{
	var self = this;
	var CoreController = new HashtagCoreController();

	var __construct = function()
	{
		jQuery("#hashtagListBox").on("click", "div", CoreController.changeAction);
		jQuery("#hashtagListBox").on("blur", "div", CoreController.changeBlurAction);
		//�ؽ��±� ����
		jQuery("#hashtagListBox").on("click", "span", self.deleteHashtagLayout);
		//�ؽ��±� �߰�
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
		//��������
		jQuery("#hashtagListBox").on("keydown", CoreController.moveTag);
	};

	this.keyupEvent = function()
	{
		//Ư������ üũ
		var checkCharacter = true;
		checkCharacter = CoreController.checkSpecialCharacter(jQuery(this));
		if(checkCharacter === false){
			return;
		}

		//�ؽ��±� �߰� input listing
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
			alert("�ؽ��±׸��� �Է��� �ּ���.");
			hashtag.focus();
			return;
		}
		if(hashtag.val().length > 20){
			alert("�ִ� ��� ���ڼ��� 20���� �Դϴ�.");
			CoreController.cutMaxLength(hashtag);
			return;
		}
		var hashtaglistBoxDiv = jQuery("#hashtagListBox div");
		if(hashtaglistBoxDiv.length >= 10){
			alert("�ִ� ��� ������ 10�� �Դϴ�.");
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
			alert("������ �ؽ��±װ� �����մϴ�.");
			return;
		}

		CoreController.ajaxHashtagList({mode:'addLayout', hashtag:hashtag.val()});
	};

	//����
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

//�˾� �ؽ��±� ��������Ʈ (�ڵ���� �̸�����) ������
var HashtagPopupCreateCodeController = function()
{
	var self = this;
	var CoreController = new HashtagCoreController();

	var __construct = function()
	{
		//�ҽ�����
		jQuery("#sourceCopy").click(self.copySource);
		//â�ݱ�
		jQuery("#popupClose").click(function(){
			window.close();
		});

		//��ũ ����
		jQuery(window).load(self.deleteHashtagWidgetListGoodsLink);
	};

	//�ҽ�����
	this.copySource = function()
	{
		var clipData = jQuery('#hashtag_previewSourceArea').text();
		if(window.clipboardData){
			alert("����Ǿ����ϴ�.\n���θ��� ��ǰ����Ʈ�� �����ų ��ġ�� �ҽ��� �ٿ��־� �ּ���.");
			window.clipboardData.setData("Text", clipData);
		}
		else {
			prompt("�ڵ带 Ŭ������� ����(Ctrl+C) �Ͻð�.\n���θ��� ��ǰ����Ʈ�� �����ų ��ġ�� �ҽ��� �ٿ��־� �ּ���.", clipData);
		}
		return;
	}

	//��ũ ����
	this.deleteHashtagWidgetListGoodsLink = function()
	{
		jQuery("#hashtag_previewLayout iframe").contents().find('a').removeAttr("onclick");
		jQuery("#hashtag_previewLayout iframe").contents().find('.hashtagSelector').removeAttr("onclick");
	}

	__construct();
};

//�ؽ��±� ���� ���� ������
var HashtagConfigController = function()
{
	var self = this;
	var CoreController = new HashtagCoreController();

	var __construct = function()
	{
		//���� ����
		jQuery("#hashtagConfig_submitImg").click(self.saveConfig);
		//�ؽ��±� ���̱׷��̼�
		jQuery("#hashtagMigragionBtn").click(self.migrationHashtag);
		jQuery(".hashtagDisplayPopupBtn").click(self.openDisplayPopup);
	};

	//���� ����
	this.saveConfig = function()
	{
		jQuery('#mode').val('configSave');
		jQuery('#hashtagConfigForm').submit();
	}

	//�ؽ��±� ���̱׷��̼�
	this.migrationHashtag = function()
	{
		var migrationParam = jQuery.param(jQuery("input[type='checkbox']:checked"));
		if(!migrationParam){
			alert("������ Ÿ���� �����Ͽ� �ּ���.");
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
				alert("�� " + comma(migrationGoodsCount) + "���� ��ǰ�� �ؽ��±װ� �����Ǿ����ϴ�." );
				document.location.reload();
			}
			else if(dataArray[0] === 'fail'){
				alert("�ؽ��±� ������ �����Ͽ����ϴ�.\n�����Ϳ� �����Ͽ� �ּ���.");
			}
			else { }

			return;
		})
		.always(function(){
			self.hiddenProgressBar();
		})
		.fail(function(){
			alert("�ؽ��±� ������ �����Ͽ����ϴ�.\n�����Ϳ� �����Ͽ� �ּ���.");
			self.hiddenProgressBar();
		});
	}

	//���α׷����� ����
	this.showProgressBar = function()
	{
		var progressImgMarginTop = Math.round((jQuery(window).height() - 116) / 2);
		jQuery("body").append('<div id="hashtagPrograssbar" style="position:absolute;top:0;left:0;background:#44515b;filter:alpha(opacity=80);opacity:0.8;width:100%;height:'+jQuery('body').height()+'px;cursor:progress;z-index:100000;margin:0 auto;text-align: center;"><img src="../img/admin_progress.gif" border="0" style="margin-top:'+progressImgMarginTop+'px;"/></div>');
	}

	//���α׷����� ����
	this.hiddenProgressBar = function()
	{
		jQuery("#hashtagPrograssbar").remove();
	}

	//���� �˾� ����
	this.openDisplayPopup = function()
	{
		var hashtagDisplayPopup = window.open('./popup.hashtagDisplay.php', 'hashtagDisplayPopup', 'width=1000px,height=800px,scrollbars=no,resizeable=no');
		if(hashtagDisplayPopup){
			hashtagDisplayPopup.focus();
		}
	}

	__construct();
};

//�ؽ��±� ����ڼ��� ���÷��� �˾� ������
var HashtagPopupDisplayController = function()
{
	var self = this;
	var CoreController = new HashtagCoreController();
	var addHashtagSelector;
	var searchTextConfirm = false;
	var pageNum = 250;

	var __construct = function()
	{
		//��ü����Ʈ �ؽ��±� Ŭ����
		jQuery("#hashtagListBox").on("click", "div", self.focusAllListDiv);
		//����Ǵ� �ؽ��±� ����Ʈ Ŭ����
		jQuery("#hashtagDisplayListBox").on("click", "div", CoreController.changeAction);
		//�ؽ��±� ����
		jQuery("#hashtagDisplayListBox").on("click", "span", self.deleteHashtagLayout);

		jQuery("#hashtagSearch").keyup(function(){
			//���϶� ��ü search
			if(jQuery(this).val() === '' && searchTextConfirm === true){
				self.clickHashtagListBox();
			}
			//�ؽ��±� input listing
			var inputListParam = {
				mode: 'inputList',
				searchText: jQuery("#hashtagSearch").val(),
				obj: jQuery("#hashtagSearch")
			};
			CoreController.ajaxHashtagList(inputListParam);
		});
		//�ؽ��±� ��ü����Ʈ �˻�
		jQuery("#hashtagSearchBtn").click(function(){
			self.clickHashtagListBox();
		});
		//�ؽ��±� ��ü����Ʈ ��ũ�� ����¡
		jQuery("#hashtagListBox").scroll(function(){
			self.scrollHashtagListBox();
		});
		//�߰�
		jQuery("#addHashtagBtn").click(self.addHashtagDisplay);
		//����
		jQuery("#saveHashtagDisplayBtn").click(self.saveHashtagDisplayList);
		//�ݱ�
		jQuery("#closeHashtagDisplayBtn").click(self.closeHashtagDisplayPopup);

		//�ؽ��±� ��ü����Ʈ �˻� INPUT
		jQuery("#hashtagSearch").trigger("keyup");

		//��ü �ؽ��±� ����Ʈ listing
		CoreController.ajaxHashtagList({mode:'allList', page:1, pageNum:pageNum});

		//��������
		jQuery(window).on("keydown", CoreController.moveTag);
	};

	//�˻� Ŭ�� ����Ʈ
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

	//�ؽ��±� ��ü ����Ʈ ����¡
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

	//��ü �ؽ��±� ����Ʈ Ŭ���� �׼�
	this.focusAllListDiv = function()
	{
		var thisObj = jQuery(this);

		thisObj.css('background-color', '');
		thisObj.toggleClass("hashtagLayout-focusDiv");
	}

	//�߰�
	this.addHashtagDisplay = function()
	{
		//���õ� �ؽ��±�
		var selectObj = jQuery(".hashtagLayout-focusDiv");
		if(selectObj.length < 1){
			alert("�߰��� �ؽ��±׸� �����Ͽ� �ּ���.");
			return;
		}

		alert("�ߺ��� �ؽ��±״� �����ϰ� �߰��˴ϴ�.");

		selectObj.each(function(){
			var duplicateHashtag = false; //�ߺ�����
			var cloneHashtag = jQuery(this).clone(); //����
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

	//����
	this.saveHashtagDisplayList = function()
	{
		var param = {
			mode : 'saveDisplay',
			hashtagNo : jQuery.param(jQuery("#hashtagDisplayForm input[name='hashtagNo[]']"))
		};
		CoreController.ajaxHashtagList(param);
	}

	//����
	this.deleteHashtagLayout = function()
	{
		jQuery(this).closest("div").remove();
	}

	//�ݱ�
	this.closeHashtagDisplayPopup = function()
	{
		window.close();
	}

	__construct();
};

//������� > ��ǰ��������
var HashtagUserViewController = function(mobile)
{
	var self = this;
	var CoreController = new HashtagCoreController();

	var __construct = function()
	{
		//�߰�
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
		//Ư������ üũ
		var checkCharacter = true;
		checkCharacter = CoreController.checkSpecialCharacter(jQuery("#hashtag"));

		return checkCharacter;
	}

	//�߰�
	this.saveHashtagLiveUser = function()
	{
		var hashtag = jQuery("#hashtag");
		var hashtaglistBoxDiv = jQuery("#hashtagListBox div");
		var hashtagValue = jQuery.trim(hashtag.val()).replace(/ /g, '_');

		if(!hashtagValue){
			alert("�ؽ��±׸��� �Է��� �ּ���.");
			hashtag.focus();
			return;
		}
		if(hashtagValue.length > 20){
			alert("�ִ� ��� ���ڼ��� 20���� �Դϴ�.");
			CoreController.cutMaxLength(hashtag);
			return;
		}
		if(hashtaglistBoxDiv.length >= 10){
			alert("�ִ� ��� ������ 10�� �Դϴ�.");
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
			alert("������ �ؽ��±װ� �����մϴ�.");
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

//������� > ��ǰ���� > ���� �ؽ��±� ���� ������
var HashtagManageListController = function()
{
	var self = this;
	var CoreController = new HashtagCoreController();

	var __construct = function()
	{
		//�ؽ��±� ����
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

//�ؽ��±� input list setting
//������� > ��ǰ���� > ��ǰ���� ����Ʈ ������
//������� > ��ǰ���� �˾�
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