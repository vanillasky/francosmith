/**
 * ��ǰ���� ��Ʈ��
 *
 * @author bumyul2000@godo.co.kr
 * @version 1.0
 * @date 2015-06-25
 *
*/
var GoodsChoiceController = function()
{
	var $ = jQuery;
	var self = this;
	var eHiddenName = $('#eHiddenName').val(); //hidden input name
	var displayName = $('#displayName').val(); //image display area
	var ajaxUrl = '../proc/_ajaxGoodsChoiceList.php';
	var registeredTableID = 'goodsRegisteredTableArea'; //��ϻ�ǰ ����Ʈ ���̺� ID
	var registeredDivID = 'goodsChoice_registerdOutlineDiv'; //��ϻ�ǰ ����Ʈ div ID
	var goodsChoiceIframeID = 'iframe_goodsChoiceList'; //��ǰ���� iframe ID

	/**
	 * requeset paremeter ����
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.getGoodsChoicePostParam = function(mode)
	{
		var goodsnoPostParam;
		switch(mode){
			case 'setting' :
				goodsnoPostParam = this.getGoodsChoicePostParam_setting();
			break;

			case 'add' :
				goodsnoPostParam = this.getGoodsChoicePostParam_add();
			break;

			case 'confirm' :
				goodsnoPostParam = this.getGoodsChoicePostParam_confirm();
			break;
		}

		return goodsnoPostParam;
	}

	/**
	 * get html ���
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.getGoodsChoiceHtml = function(mode, objThis)
	{
		var goodsnoPostParam;
		var registeredGoodsno;

		//��ϵ� ��ǰ��ȣ
		if(mode == 'dbclick_add'){
			goodsnoPostParam = this.getGoodsChoicePostParam_dbclick_add(objThis);
		}
		else {
			goodsnoPostParam = this.getGoodsChoicePostParam(mode);
		}

		//��ϵǾ��ִ� ��ǰ��ȣ
		registeredGoodsno = this.getRegisteredGoodsnoParam();

		//�ִ��ϰ��� ��ǰüũ
		if(mode == 'add' || mode == 'dbclick_add'){
			if((goodsnoPostParam.split(",").length + registeredGoodsno.split(",").length) > $('#maxLimit').val()){
				if(!confirm("������ ��ǰ�� �߰��� ��Ͻ� �ִ� ��� ��ǰ��("+$('#maxLimit').val()+"��)�� �ʰ��Ǿ� �Ϻ� ��ǰ�� ����Ʈ���� �����˴ϴ�.\n����Ͻðڽ��ϱ�?")){
					return false;
				}
			}
		}
		else if(mode == 'addAll'){
			if(!confirm("�߰� ��ǰ������� ���Ͽ� �ִ� ��� ��ǰ��("+$('#maxLimit').val()+"��)�� �ʰ��Ǹ� �Ϻ� ��ǰ�� ����Ʈ���� �����˴ϴ�.\n����Ͻðڽ��ϱ�?")){
				return false;
			}
		}
		else {

		}

		//�˻���ǰ ��ü��� query
		queryString = this.getQueryString(mode);

		//���α׷�����
		self.progressBar();

		$.post(ajaxUrl, {
			mode : mode,
			maxLimit : $('#maxLimit').val(), //��ǰ �ִ��� ����
			eHiddenName : eHiddenName, //input hidden��
			goodsArr : goodsnoPostParam, //��ǰ���� - ��ǰ
			registeredGoodsno : registeredGoodsno, //��ϻ�ǰ����Ʈ- ��ǰarray
			queryString : queryString //��ǰ���� - query
		}, function(data){
				/*
				* data : code, data, msg
				*	code - OK or ERROR
				*	data - html data
				*	msg - alert message
				*/
				var responseData = new Array();
				responseData = eval('(' + data + ')');

				if(responseData['code'] != 'OK'){
					alert(responseData['msg']);
				}
				else {
					if(responseData['msg']){
						alert(responseData['msg']);
					}
					switch(mode){
						//�ʱ����
						case 'setting':
							$('#' + registeredDivID).html(responseData['data']);
							self.setVoidBackground();
							self.registeredGoodsCountMsg();
						break;

						//��ǰ�߰�, �˻���ǰ ��ü���
						case 'add': case 'addAll': case 'dbclick_add':
							$(responseData['data']).insertAfter($('#' + registeredTableID + ' tr').first());
							self.removeOverRow();
							self.setVoidBackground();
							self.setIdxAll();
							self.registeredGoodsCountMsg();
						break;

						//����
						case 'confirm':
							$('#' + displayName, opener.document).html('');
							$('#' + displayName, opener.document).html(responseData['data']);
							window.close();
						break;
					}
				}
		}).fail(function() {
			self.progressBarNone();
			alert("��ſ����� �߻��Ͽ����ϴ�.\n�����Ϳ� �����Ͽ� �ּ���.");
			return false;
		}).always(function() {
			self.progressBarNone();
		});

		if(mode == 'add' || mode == 'addAll' || mode == 'dbclick_add'){
			//�ε��� ������
			this.setIdxAll();
		}
	}

	/**
	 * row ����
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.delGoodsRow = function()
	{
		var checkRow = this.getRegisteredCheckRow();
		checkRow.remove();

		this.setVoidBackground();
		this.registeredGoodsCountMsg();
	}

	/**
	 * �ʱ� ��ǰ ���õ� paremeter ����
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.getGoodsChoicePostParam_setting = function()
	{
		var goodsnoPostParam;

		goodsnoPostParam = $($('input[name="' + eHiddenName + '"]', opener.document)).map(function() {
			return this.value;
		}).get().join(',');

		return goodsnoPostParam;
	}

	/**
	 * �߰� ��ǰ paremeter ����
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.getGoodsChoicePostParam_add = function()
	{
		var goodsnoPostParam;

		goodsnoPostParam = $(this.getGoodsChoiceCheckRow()).map(function() {
			return this.value;
		}).get().join(',');

		return goodsnoPostParam;
	}

	/**
	 * ���� ��ǰ paremeter ����
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.getGoodsChoicePostParam_confirm = function()
	{
		var goodsnoPostParam;

		goodsnoPostParam = $(this.getregisteredGoodsno()).map(function() {
			return this.value;
		}).get().join(',');

		return goodsnoPostParam;
	}

	/**
	 * üũ��ǰ �ǾƷ��� �̵�
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.moveRowArrow_downArrowMore = function()
	{
		var lastRow;
		var checkRow = this.getRegisteredCheckRow();
		$(checkRow).insertBefore($('#' + registeredTableID + ' tr').last());

		lastRow = $('#' + registeredTableID + ' tr').last();
		if(lastRow.find('input:checkbox[name="goodsno[]"]').prop('checked') === false){
			$(lastRow).insertBefore($("#" + registeredTableID).find('input:checkbox[name="goodsno[]"]:checked').closest('tr').eq(0));
		}
		self.setOrderChangeMark(checkRow);
	}

	/**
	 * üũ��ǰ �Ѵܰ� �Ʒ��� �̵�
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.moveRowArrow_downArrow = function()
	{
		var checkRow = this.getRegisteredCheckRow();
		if($("#" + registeredTableID).find('input:checkbox[name="goodsno[]"]').last().prop('checked') === true){
			return false;
		}
		else {
			for(var i = checkRow.length; i >= 0; i--){
				$(checkRow.eq(i)).insertAfter(checkRow.eq(i).next());
				self.setOrderChangeMark(checkRow);
			}
		}
	}

	/**
	 * üũ��ǰ �Ѵܰ� ���� �̵�
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.moveRowArrow_upArrow = function()
	{
		var checkRow = this.getRegisteredCheckRow();

		$.each(checkRow, function(i){
			if($(this).prev().attr('id') == 'goodsRegisteredTrArea'){
				return false;
			}
			$($(this)).insertBefore($(this).prev());
			self.setOrderChangeMark($(this));
		});
	}

	/**
	 * üũ��ǰ �� ���� �̵�
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.moveRowArrow_upArrowMore = function()
	{
		var checkRow = this.getRegisteredCheckRow();
		$(checkRow).insertAfter($('#' + registeredTableID + ' tr').first());
		self.setOrderChangeMark(checkRow);
	}

	/**
	 * ȭ��ǥ ��ǰ�̵�
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.moveRowArrow = function(mode)
	{
		switch(mode){
			case 'downArrowMore' :
				this.moveRowArrow_downArrowMore();
			break;

			case 'downArrow' :
				this.moveRowArrow_downArrow();
			break;

			case 'upArrow' :
				this.moveRowArrow_upArrow();
			break;

			case 'upArrowMore' :
				this.moveRowArrow_upArrowMore();
			break;
		}

		//üũ�ڽ��� focus
		this.scrollFocus();
	}

	/**
	 * üũ�Ǿ��ִ� üũ�ڽ� row
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.getRegisteredCheckRow = function()
	{
		return $("#" + registeredTableID).find('input:checkbox[name="goodsno[]"]:checked').closest('tr');
	}

	/**
	 * üũ�Ǿ����� ���� üũ�ڽ� row
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.getRegisteredNotCheckRow = function()
	{
		return $("#" + registeredTableID).find('input:checkbox[name="goodsno[]"]:not(:checked)').closest('tr');
	}

	/**
	 * '��ǰ����' �� üũ�� üũ�ڽ�
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.getGoodsChoiceCheckRow = function()
	{
		return $('#' + goodsChoiceIframeID).contents().find('input[name="goodsno[]"]:checked');
	}

	/**
	 * '��ǰ����' �� üũ���� ���� üũ�ڽ�
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.getGoodsChoiceNotCheckRow = function()
	{
		return $('#' + goodsChoiceIframeID).contents().find('input[name="goodsno[]"]:not(:checked)');
	}

	/**
	 * �ؽ�Ʈ �������� �� ���� (��,�Ʒ�)
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.exchangeText = function(objValue)
	{
		$('input[name="goodsChoice_sortText"]').each(function(){
			$(this).val(objValue);
		});
	}

	/**
	 * �������� �ؽ�Ʈ�ڽ��� ������ ���� üũ
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.checkInteager = function(obj)
	{
		var intPattern = /^[0-9]+$/;

		if(!intPattern.test(obj.val())){
			alert("������ ���ڸ� �Է��� �ּ���.");
			obj.val('');
			return false;
		}

		return true;
	}

	/**
	 * �̵������� ��ġ üũ
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.checkRowLength = function(obj)
	{
		var goodsnoArray = this.getregisteredGoodsno();
		if(goodsnoArray.length < 1 || goodsnoArray.length < parseInt(obj.val()) || parseInt(obj.val()) == 0){
			alert("�̵��� �� ���� ��ġ�Դϴ�.");
			obj.val('');
			return false;
		}

		return true;
	}

	/**
	 * �ؽ�Ʈ ��������
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.moveRowText = function(moveRowState)
	{
		var startRow = moveRowState-1;
		var checkRow = this.getRegisteredCheckRow();
		var totalRowCount = $("#" + registeredTableID + " tr").length - 1;
		var remainCount = totalRowCount - checkRow.length;
		if(remainCount < moveRowState){
			startRow = remainCount;
		}

		checkRow.remove();

		$(checkRow).each(function(){
			$($(this)).insertAfter($("#" + registeredTableID + " tr").eq(startRow));
			startRow++;

			self.setOrderChangeMark($(this));
		});

		$('.goodsChoice_sortText').val('');
		this.scrollFocus();
	}

	/**
	 * �ؽ�Ʈ �������� �� Ȯ��
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.checkSortText = function()
	{
		if($('.goodsChoice_sortText').eq(0).val() != ''){
			return true;
		}

		return false;
	}

	/**
	 * ��ϵ� ��ǰ
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.getregisteredGoodsno = function()
	{
		return $('input[name="goodsno[]"]');
	}

	/**
	 * ��ϵ� ��ǰ�� goodsno �� �迭
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.getRegisteredGoodsnoParam = function()
	{
		var registeredGoodsno;

		registeredGoodsno = $(this.getregisteredGoodsno()).map(function() {
			return this.value;
		}).get().join(',');

		return registeredGoodsno;
	}

	/**
	 * �̺�Ʈ ����
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.eventStop = function(e)
	{
		var event = e || window.event;
		if(event.preventDefault){
			event.preventDefault();
		}
		else {
			event.returnValue = false;
		}
	}

	/**
	 * ��ǰ��ü��� get query
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.getQueryString = function(mode)
	{
		if(mode == 'addAll'){
			return $('#' + goodsChoiceIframeID).contents().find('input[name="searchParam"]').val();
		}
		else {
			return '';
		}
	}

	/**
	 * ��ϵ� ��ǰ����Ʈ ��׶���
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.setVoidBackground = function()
	{
		if($("#" + registeredTableID + " tr").eq(1).length < 1){
			$('#' + registeredDivID).css({'background-image':'url(../img/no-product.gif)', 'background-repeat':'no-repeat', 'background-position':'50% 50%', 'background-color':'#dbdbdb'});
		}
		else {
			$('#' + registeredDivID).css({'background-image':'', 'background-color':'#FFFFFF'});
		}
	}

	/**
	 * ������� ��ġ ����
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.setOrderChangeMark = function(obj)
	{
		for(var i=0; i < obj.length; i++){
			var moveGoodsIdxLen = $(obj).eq(i).children('td:eq(1)').find('.moveGoodsIdx').length;
			if(moveGoodsIdxLen < 1){
				$(obj).eq(i).children('td:eq(1)').append('<span class="moveGoodsIdx"></span>');
			}
		}

		this.setOrderChangeMsg();
	}

	/**
	 * ������� ����
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.setOrderChangeMsg = function()
	{
		var moveGoodsIdxRow = $('.moveGoodsIdx');
		for(var i=0; i<moveGoodsIdxRow.length; i++){
			var thisMsgArea = moveGoodsIdxRow.eq(i);
			var insertMsg = '��<span class="moveGoodsIdxInt">' + thisMsgArea.closest('tr').index() + '</span>';
			thisMsgArea.html(insertMsg);
		}
	}

	/**
	 * ������� ��ü �����
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.setIdxAll = function()
	{
		$($('#' + registeredTableID + ' tr')).each(function(){
			$(this).children('td:eq(1)').html($(this).index());
		});
	}

	/**
	 * ���� row ����
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.setRowHighlight = function(type)
	{
		if(type == 'registered'){
			//��ǰ���� ����
			this.registeredCheckedGoodsCountMsg();
			var checkRow = this.getRegisteredCheckRow();
			var notCheckRow = this.getRegisteredNotCheckRow();
		}
		else if(type == 'goodsChoice'){
			var checkRow = this.getGoodsChoiceCheckRow();
			checkRow = checkRow.closest('tr');
			var notCheckRow = this.getGoodsChoiceNotCheckRow();
			notCheckRow = notCheckRow.closest('tr');
		}
		else {
			return false;
		}

		checkRow.css("background-color", "#FFF4E6");
		notCheckRow.css("background-color", "#FFFFFF");
	}

	/**
	 * üũ�ڽ� ��Ŀ�� - DIV ��ũ���̵�
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.scrollFocus = function()
	{
		var checkRow = this.getRegisteredCheckRow();
		checkRow.eq(0).find('input[name="goodsno[]"]').focus();
	}

	/**
	 * ��ϵ� ��ǰ ����üũ
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.checkboxLength_regestered = function(checkType)
	{
		var msg = '';
		var moveLength = $("#" + registeredTableID).find('input:checkbox[name="goodsno[]"]:checked').length;


		if(moveLength < 1){
			msg = '��ǰ�� ������ �ּ���.';
		}
		if(checkType != 'delete'){
			if(moveLength > 20){
				msg = '�� ���� �̵��� �� �ִ� �ִ� ��ǰ������ 20�� �Դϴ�.';
			}
		}

		return msg;
	}

	/**
	 * ����� ��ǰ ����üũ
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.checkboxLength_choice = function()
	{
		var msg = '';
		var goodsChoiceRow = this.getGoodsChoiceCheckRow();
		if(goodsChoiceRow.length < 1){
			msg = '��ǰ�� ������ �ּ���.';
		}

		return msg;
	}

	/**
	 * ����ǳ��
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.getGoodsChoiceTooltip = function(objThis){
		var objPosition = objThis.offset();

		var posLeft = objPosition.left + 20;
		var posTop = objPosition.top + 20;

		$("body").append('<div class="goodsChoiceTooltipDiv" style="position: absolute; background-color: #ffffff; z-index: 10000; width: 80px; height: 18px; top: '+posTop+'px; left: '+posLeft+'px; border: 1px black solid; text-align: center;">'+objThis.attr('tooltipContent')+'</div>');
	}

	/**
	 * ����ǳ�� ����
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.removeGoodsChoiceToolTop = function()
	{
		$('.goodsChoiceTooltipDiv').remove();
	}

	/**
	 * ���α׷�����
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.progressBar = function()
	{
		var marginTop = ($(document).height() - 116) /2;
		$("body").append('<div id="progressDiv" style="position:absolute;top:0;left:0;background:#44515b;filter:alpha(opacity=80);opacity:0.8;width:100%;height:100%;cursor:progress;z-index:100000;margin:0 auto;text-align: center;"><img src="../img/admin_progress.gif" border="0" style="margin-top:'+marginTop+'px;" /></div>');
	}

	/**
	 * ���α׷����� ����
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.progressBarNone = function()
	{
		$('#progressDiv').remove();
	}

	/**
	 * üũ�ڽ� üũ
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-07-30
	*/
	this.setCheckboxCheck = function(obj, type, e)
	{
		var event = e || window.event;
		var target = event.target ? event.target : event.srcElement;

		if (target.type == 'checkbox') return;

		var checkbox = $(obj).contents().find(':checkbox');

		if(checkbox.prop('checked') === true){
			checkbox.prop('checked', false);
		}
		else {
			checkbox.prop('checked', true);
		}

		this.setRowHighlight(type);
	}

	/**
	 * shift + mouse click select checkbox
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-07-30
	*/
	this.shiftSelection = function(obj, type, e)
	{
		var event = e || window.event;
		var tableElement = '';

		if(type == 'goodsChoice'){
			tableElement = $('#' + goodsChoiceIframeID).contents();
		}
		else {
			tableElement = $('#' + registeredTableID);
		}

		if(e.shiftKey){
			if(startCheckBoxIdx > 0){
				var endCheckBoxIdx = $(obj).index();
				$(tableElement).find(':checkbox').slice(Math.min(startCheckBoxIdx, endCheckBoxIdx), Math.max(startCheckBoxIdx, endCheckBoxIdx)).prop('checked', true);
				startCheckBoxIdx = 0;
			}
		}
		else {
			startCheckBoxIdx = (type == 'goodsChoice') ? $(obj).index()+1 : $(obj).index();
		}

		this.setRowHighlight(type);
	}

	/**
	 * �ʰ� �� ����
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-07-30
	*/
	this.removeOverRow = function()
	{
		var maxLimit = $('#maxLimit').val();
		if(($('#' + registeredTableID + ' tr').length - 1) > maxLimit){
			$('#' + registeredTableID + ' tr:gt('+(maxLimit)+')').remove();
		}
	}

	/**
	 * text �巡�� ����
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-07-30
	*/
	this.disableSelect = function(obj)
	{
		$(obj).attr('unselectable', 'on').css('user-select', 'none').on('selectstart', false);
	}

	/**
	 * ��� ��ǰ���� ����
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-07-30
	*/
	this.registeredGoodsCountMsg = function()
	{
		$('#registeredGoodsCountMsg').html(this.getregisteredGoodsno().length);
	}

	/**
	 * ���� ��ǰ���� ����
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-07-30
	*/
	this.registeredCheckedGoodsCountMsg = function()
	{
		$('#registeredCheckedGoodsCountMsg').html(this.getRegisteredCheckRow().length);
	}

	/**
	 * ����Ŭ�� �߰� ��ǰ parameter
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-07-30
	*/
	this.getGoodsChoicePostParam_dbclick_add = function(obj)
	{
		var goodsnoPostParam = new Array();
		goodsnoPostParam = $(obj).contents().find('input:checkbox[name="goodsno[]"]').val();

		return goodsnoPostParam;
	}

	/**
	 * ����Ŭ�� ��ǰ ����
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-07-30
	*/
	this.removeRegisteredGoodsRow = function(obj)
	{
		$(obj).remove();
	}
}

var goodsChoiceFunc;
var startCheckBoxIdx = 0;

jQuery(document).ready(function(){
	var goodsChange = false;
	var goodsChoice = new GoodsChoiceController();
	var doubleClick = false;

	goodsChoiceFunc = goodsChoice;

	//�ƿ����� ����
	goodsChoice.getGoodsChoiceHtml('setting', '');

	//�߰�
	jQuery('#addGoods').click(function(){
		goodsChange = true;
		var errorMsg = goodsChoice.checkboxLength_choice();
		if(errorMsg){
			alert(errorMsg);
			return false;
		}
		goodsChoice.getGoodsChoiceHtml('add', '');
	});
	//����
	jQuery('#delGoods').click(function(){
		goodsChange = true;
		var errorMsg = goodsChoice.checkboxLength_regestered('delete');
		if(errorMsg){
			alert(errorMsg);
			return false;
		}
		goodsChoice.delGoodsRow();
		goodsChoice.registeredCheckedGoodsCountMsg();
	});
	//����
	jQuery('#goodsChoiceConfirm,#goodsChoiceConfirmSmall').click(function(){
		goodsChoice.progressBar();
		goodsChoice.getGoodsChoiceHtml('confirm', '');
	});
	//���
	jQuery('#goodsChoiceCancel').click(function(){
		if(goodsChange === true){
			if(confirm("��ҽ� ������ ������ �ݿ����� �ʽ��ϴ�.\n����Ͻðڽ��ϱ�?")){
				window.close();
			}
		}
		else {
			window.close();
		}
	});
	//�ǾƷ���
	jQuery('.goodsChoice_downArrowMore').click(function(){
		goodsChange = true;
		var errorMsg = goodsChoice.checkboxLength_regestered('move');
		if(errorMsg){
			alert(errorMsg);
			return false;
		}
		goodsChoice.moveRowArrow('downArrowMore');
	});
	//�Ʒ���
	jQuery('.goodsChoice_downArrow').click(function(){
		goodsChange = true;
		var errorMsg = goodsChoice.checkboxLength_regestered('move');
		if(errorMsg){
			alert(errorMsg);
			return false;
		}
		goodsChoice.moveRowArrow('downArrow');
	});
	//����
	jQuery('.goodsChoice_upArrow').click(function(){
		goodsChange = true;
		var errorMsg = goodsChoice.checkboxLength_regestered('move');
		if(errorMsg){
			alert(errorMsg);
			return false;
		}
		goodsChoice.moveRowArrow('upArrow');
	});
	//������
	jQuery('.goodsChoice_upArrowMore').click(function(){
		goodsChange = true;
		var errorMsg = goodsChoice.checkboxLength_regestered('move');
		if(errorMsg){
			alert(errorMsg);
			return false;
		}
		goodsChoice.moveRowArrow('upArrowMore');
	});
	//�����̵� text
	jQuery('.goodsChoice_sortText').bind('change',function (e){
		var thisObj = jQuery(this);

		if(thisObj.val() != ''){
			if(goodsChoice.checkInteager(thisObj) == false || goodsChoice.checkRowLength(thisObj) == false){
				goodsChoice.eventStop(e);
				return false;
			}

			goodsChoice.exchangeText(thisObj.val());
		}
	});
	jQuery('.goodsChoice_moveBtn').click(function(e){
		goodsChange = true;
		if(goodsChoice.checkSortText() == false) {
			alert("�̵��� ��ġ�� �Է��Ͽ� �ּ���");
			return false;
		}
		var errorMsg = goodsChoice.checkboxLength_regestered('move');
		if(errorMsg){
			alert(errorMsg);
			return false;
		}

		goodsChoice.moveRowText(parseInt(jQuery('.goodsChoice_sortText').eq(0).val()));
	});

	jQuery('.goodsChoice_tooltip').mouseover(function(){
		goodsChoice.getGoodsChoiceTooltip(jQuery(this));
	});
	jQuery('.goodsChoice_tooltip').mouseout(function(){
		goodsChoice.removeGoodsChoiceToolTop();
	});

	//iframe �ε�
	jQuery('#iframe_goodsChoiceList').load(function(){
		var goodsChoiceListContents = jQuery(this).contents();
		goodsChoice.disableSelect(goodsChoiceListContents.find('body'));
		//row highlight event
		jQuery(goodsChoiceListContents.find('input[name="goodsno[]"]')).add(goodsChoiceListContents.find('#goodsChoiceCheckBoxAll')).click(function(){
			goodsChoice.setRowHighlight('goodsChoice');
		});
		//click & dbclick event
		jQuery(goodsChoiceListContents.find('tbody > tr')).bind({
			click: function (e) {
				var objThis = jQuery(this);
				var event = e || window.event;
				var target = event.target ? event.target : event.srcElement;
				if (target.type == 'checkbox') return;
				setTimeout(function(){
					if(doubleClick == false){
						goodsChoice.setCheckboxCheck(objThis, 'goodsChoice', event);
						goodsChoice.shiftSelection(objThis, 'goodsChoice', event);
					}
				}, 200);
			},
			dblclick: function () {
				doubleClick = true;
				setTimeout(function(){
					doubleClick = false;
				}, 300);
				goodsChoice.getGoodsChoiceHtml('dbclick_add', jQuery(this));
			}
		});

		//goods register
		goodsChoiceListContents.find('#goodSerchAll').click( function(){
			goodsChoice.getGoodsChoiceHtml('addAll', '');
		});
	});

	goodsChoice.disableSelect(jQuery('body'));

	//���ΰ�ħ����
	jQuery(this).keydown(function(e){
		var event = e || window.event;
		if(event.keyCode == 116){
			if(!confirm("���ΰ�ħ�� ���泻���� ������� �ʽ��ϴ�.\n����Ͻðڽ��ϱ�?")){
				goodsChoice.eventStop(event);
				return false;
			}
		}
	});
});