/**
 * 상품선택 컨트롤
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
	var registeredTableID = 'goodsRegisteredTableArea'; //등록상품 리스트 테이블 ID
	var registeredDivID = 'goodsChoice_registerdOutlineDiv'; //등록상품 리스트 div ID
	var goodsChoiceIframeID = 'iframe_goodsChoiceList'; //상품선택 iframe ID

	/**
	 * requeset paremeter 가공
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
	 * get html 통신
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.getGoodsChoiceHtml = function(mode, objThis)
	{
		var goodsnoPostParam;
		var registeredGoodsno;

		//등록될 상품번호
		if(mode == 'dbclick_add'){
			goodsnoPostParam = this.getGoodsChoicePostParam_dbclick_add(objThis);
		}
		else {
			goodsnoPostParam = this.getGoodsChoicePostParam(mode);
		}

		//등록되어있는 상품번호
		registeredGoodsno = this.getRegisteredGoodsnoParam();

		//최대등록가능 상품체크
		if(mode == 'add' || mode == 'dbclick_add'){
			if((goodsnoPostParam.split(",").length + registeredGoodsno.split(",").length) > $('#maxLimit').val()){
				if(!confirm("선택한 상품을 추가로 등록시 최대 등록 상품수("+$('#maxLimit').val()+"개)가 초과되어 일부 상품이 리스트에서 삭제됩니다.\n계속하시겠습니까?")){
					return false;
				}
			}
		}
		else if(mode == 'addAll'){
			if(!confirm("추가 상품등록으로 인하여 최대 등록 상품수("+$('#maxLimit').val()+"개)가 초과되면 일부 상품이 리스트에서 삭제됩니다.\n계속하시겠습니까?")){
				return false;
			}
		}
		else {

		}

		//검색상품 전체등록 query
		queryString = this.getQueryString(mode);

		//프로그레스바
		self.progressBar();

		$.post(ajaxUrl, {
			mode : mode,
			maxLimit : $('#maxLimit').val(), //상품 최대등록 개수
			eHiddenName : eHiddenName, //input hidden명
			goodsArr : goodsnoPostParam, //상품선택 - 상품
			registeredGoodsno : registeredGoodsno, //등록상품리스트- 상품array
			queryString : queryString //상품선택 - query
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
						//초기셋팅
						case 'setting':
							$('#' + registeredDivID).html(responseData['data']);
							self.setVoidBackground();
							self.registeredGoodsCountMsg();
						break;

						//상품추가, 검생상품 전체등록
						case 'add': case 'addAll': case 'dbclick_add':
							$(responseData['data']).insertAfter($('#' + registeredTableID + ' tr').first());
							self.removeOverRow();
							self.setVoidBackground();
							self.setIdxAll();
							self.registeredGoodsCountMsg();
						break;

						//적용
						case 'confirm':
							$('#' + displayName, opener.document).html('');
							$('#' + displayName, opener.document).html(responseData['data']);
							window.close();
						break;
					}
				}
		}).fail(function() {
			self.progressBarNone();
			alert("통신에러가 발생하였습니다.\n고객센터에 문의하여 주세요.");
			return false;
		}).always(function() {
			self.progressBarNone();
		});

		if(mode == 'add' || mode == 'addAll' || mode == 'dbclick_add'){
			//인덱스 재정렬
			this.setIdxAll();
		}
	}

	/**
	 * row 삭제
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
	 * 초기 상품 셋팅될 paremeter 가공
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
	 * 추가 상품 paremeter 가공
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
	 * 적용 상품 paremeter 가공
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
	 * 체크상품 맨아래로 이동
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
	 * 체크상품 한단계 아래로 이동
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
	 * 체크상품 한단계 위로 이동
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
	 * 체크상품 맨 위로 이동
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
	 * 화살표 상품이동
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

		//체크박스로 focus
		this.scrollFocus();
	}

	/**
	 * 체크되어있는 체크박스 row
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.getRegisteredCheckRow = function()
	{
		return $("#" + registeredTableID).find('input:checkbox[name="goodsno[]"]:checked').closest('tr');
	}

	/**
	 * 체크되어있지 않은 체크박스 row
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.getRegisteredNotCheckRow = function()
	{
		return $("#" + registeredTableID).find('input:checkbox[name="goodsno[]"]:not(:checked)').closest('tr');
	}

	/**
	 * '상품선택' 의 체크된 체크박스
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.getGoodsChoiceCheckRow = function()
	{
		return $('#' + goodsChoiceIframeID).contents().find('input[name="goodsno[]"]:checked');
	}

	/**
	 * '상품선택' 의 체크되지 않은 체크박스
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.getGoodsChoiceNotCheckRow = function()
	{
		return $('#' + goodsChoiceIframeID).contents().find('input[name="goodsno[]"]:not(:checked)');
	}

	/**
	 * 텍스트 순서변경 값 공유 (위,아래)
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
	 * 순서변경 텍스트박스의 정수형 숫자 체크
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.checkInteager = function(obj)
	{
		var intPattern = /^[0-9]+$/;

		if(!intPattern.test(obj.val())){
			alert("정수형 숫자를 입력해 주세요.");
			obj.val('');
			return false;
		}

		return true;
	}

	/**
	 * 이동가능한 위치 체크
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.checkRowLength = function(obj)
	{
		var goodsnoArray = this.getregisteredGoodsno();
		if(goodsnoArray.length < 1 || goodsnoArray.length < parseInt(obj.val()) || parseInt(obj.val()) == 0){
			alert("이동할 수 없는 위치입니다.");
			obj.val('');
			return false;
		}

		return true;
	}

	/**
	 * 텍스트 순서변경
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
	 * 텍스트 순서변경 빈값 확인
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
	 * 등록된 상품
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.getregisteredGoodsno = function()
	{
		return $('input[name="goodsno[]"]');
	}

	/**
	 * 등록된 상품의 goodsno 값 배열
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
	 * 이벤트 정지
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
	 * 상품전체등록 get query
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
	 * 등록된 상품리스트 백그라운드
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
	 * 변경순서 위치 셋팅
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
	 * 변경순서 노출
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.setOrderChangeMsg = function()
	{
		var moveGoodsIdxRow = $('.moveGoodsIdx');
		for(var i=0; i<moveGoodsIdxRow.length; i++){
			var thisMsgArea = moveGoodsIdxRow.eq(i);
			var insertMsg = '→<span class="moveGoodsIdxInt">' + thisMsgArea.closest('tr').index() + '</span>';
			thisMsgArea.html(insertMsg);
		}
	}

	/**
	 * 변경순서 전체 재셋팅
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
	 * 선택 row 강조
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.setRowHighlight = function(type)
	{
		if(type == 'registered'){
			//상품선택 갯수
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
	 * 체크박스 포커스 - DIV 스크롤이동
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.scrollFocus = function()
	{
		var checkRow = this.getRegisteredCheckRow();
		checkRow.eq(0).find('input[name="goodsno[]"]').focus();
	}

	/**
	 * 등록된 상품 갯수체크
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.checkboxLength_regestered = function(checkType)
	{
		var msg = '';
		var moveLength = $("#" + registeredTableID).find('input:checkbox[name="goodsno[]"]:checked').length;


		if(moveLength < 1){
			msg = '상품을 선택해 주세요.';
		}
		if(checkType != 'delete'){
			if(moveLength > 20){
				msg = '한 번에 이동할 수 있는 최대 상품개수는 20개 입니다.';
			}
		}

		return msg;
	}

	/**
	 * 등록할 상품 갯수체크
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.checkboxLength_choice = function()
	{
		var msg = '';
		var goodsChoiceRow = this.getGoodsChoiceCheckRow();
		if(goodsChoiceRow.length < 1){
			msg = '상품을 선택해 주세요.';
		}

		return msg;
	}

	/**
	 * 간편말풍선
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
	 * 간편말풍선 해제
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.removeGoodsChoiceToolTop = function()
	{
		$('.goodsChoiceTooltipDiv').remove();
	}

	/**
	 * 프로그레스바
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.progressBar = function()
	{
		var marginTop = ($(document).height() - 116) /2;
		$("body").append('<div id="progressDiv" style="position:absolute;top:0;left:0;background:#44515b;filter:alpha(opacity=80);opacity:0.8;width:100%;height:100%;cursor:progress;z-index:100000;margin:0 auto;text-align: center;"><img src="../img/admin_progress.gif" border="0" style="margin-top:'+marginTop+'px;" /></div>');
	}

	/**
	 * 프로그레스바 삭제
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-06-25
	*/
	this.progressBarNone = function()
	{
		$('#progressDiv').remove();
	}

	/**
	 * 체크박스 체크
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
	 * 초과 행 삭제
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
	 * text 드래그 방지
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-07-30
	*/
	this.disableSelect = function(obj)
	{
		$(obj).attr('unselectable', 'on').css('user-select', 'none').on('selectstart', false);
	}

	/**
	 * 등록 상품개수 노출
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-07-30
	*/
	this.registeredGoodsCountMsg = function()
	{
		$('#registeredGoodsCountMsg').html(this.getregisteredGoodsno().length);
	}

	/**
	 * 선택 상품개수 노출
	 * @author bumyul2000@godo.co.kr
	 * @date 2015-07-30
	*/
	this.registeredCheckedGoodsCountMsg = function()
	{
		$('#registeredCheckedGoodsCountMsg').html(this.getRegisteredCheckRow().length);
	}

	/**
	 * 더블클릭 추가 상품 parameter
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
	 * 더블클릭 상품 삭제
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

	//아웃라인 셋팅
	goodsChoice.getGoodsChoiceHtml('setting', '');

	//추가
	jQuery('#addGoods').click(function(){
		goodsChange = true;
		var errorMsg = goodsChoice.checkboxLength_choice();
		if(errorMsg){
			alert(errorMsg);
			return false;
		}
		goodsChoice.getGoodsChoiceHtml('add', '');
	});
	//삭제
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
	//적용
	jQuery('#goodsChoiceConfirm,#goodsChoiceConfirmSmall').click(function(){
		goodsChoice.progressBar();
		goodsChoice.getGoodsChoiceHtml('confirm', '');
	});
	//취소
	jQuery('#goodsChoiceCancel').click(function(){
		if(goodsChange === true){
			if(confirm("취소시 수정된 내역은 반영되지 않습니다.\n취소하시겠습니까?")){
				window.close();
			}
		}
		else {
			window.close();
		}
	});
	//맨아래로
	jQuery('.goodsChoice_downArrowMore').click(function(){
		goodsChange = true;
		var errorMsg = goodsChoice.checkboxLength_regestered('move');
		if(errorMsg){
			alert(errorMsg);
			return false;
		}
		goodsChoice.moveRowArrow('downArrowMore');
	});
	//아래로
	jQuery('.goodsChoice_downArrow').click(function(){
		goodsChange = true;
		var errorMsg = goodsChoice.checkboxLength_regestered('move');
		if(errorMsg){
			alert(errorMsg);
			return false;
		}
		goodsChoice.moveRowArrow('downArrow');
	});
	//위로
	jQuery('.goodsChoice_upArrow').click(function(){
		goodsChange = true;
		var errorMsg = goodsChoice.checkboxLength_regestered('move');
		if(errorMsg){
			alert(errorMsg);
			return false;
		}
		goodsChoice.moveRowArrow('upArrow');
	});
	//맨위로
	jQuery('.goodsChoice_upArrowMore').click(function(){
		goodsChange = true;
		var errorMsg = goodsChoice.checkboxLength_regestered('move');
		if(errorMsg){
			alert(errorMsg);
			return false;
		}
		goodsChoice.moveRowArrow('upArrowMore');
	});
	//수기이동 text
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
			alert("이동할 위치를 입력하여 주세요");
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

	//iframe 로드
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

	//새로고침방지
	jQuery(this).keydown(function(e){
		var event = e || window.event;
		if(event.keyCode == 116){
			if(!confirm("새로고침시 변경내역이 저장되지 않습니다.\n계속하시겠습니까?")){
				goodsChoice.eventStop(event);
				return false;
			}
		}
	});
});