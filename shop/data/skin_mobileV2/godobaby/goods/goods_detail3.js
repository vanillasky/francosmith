// ���ݿɼ� �и������� �ɼ�1 �����ϸ� �ɼ�2 ������
function subOption(obj)
{
	var el = document.getElementsByName('opt[]');
	var sub = opt[obj.selectedIndex];
	var selectLabel_double2 = $("#selectLabel_double2 span");
	var selectLayer_double2 = $("#selectLayer_double2 ul");

	//�ι�° select �ʱ�ȭ
	selectLayer_double2.empty();

	while (el[1].length>0) el[1].options[el[1].options.length-1] = null;
	for (i=0;i<sub.length;i++){
		var div = sub[i].replace("')","").split("','");
		eval("el[1].options[i] = new Option" + sub[i]);

		var data = sub[i].replace("('", "").replace("')", "").split("','");
		if(i === 0){
			selectLabel_double2.html(data[0]);
		}
		else {
			var styleSoldout = '';
			if (div[2]=="soldout"){
				styleSoldout = "class='liDisabled'";
			}
			selectLayer_double2.append("<li data-value="+data[1]+" "+styleSoldout+">"+data[0]+"</li>");
		}

		if (div[2]=="soldout"){
			el[1].options[i].style.color = "#808080";
			el[1].options[i].setAttribute('disabled','disabled');
		}
	}

	//�и��� �ʼ� �ɼ�2 �̺�Ʈ ���
	setDoubleOptionEvent();

	el[1].selectedIndex = el[1].preSelIndex = 0;
	if (el[0].selectedIndex == 0) chkOption(el[1]);
}

// ���ݿɼ� ǰ���׸� ���� �Ұ� ó��
function chkOption(obj)
{
	if (!selectDisabled(obj)) return false;
}

var coponlist_scroll;
var windowScroll = false;
var tabAreaOffsetTop = 0;
var selectLayerAdjust = false;
var defaultCloseBtnBottom = 0;
var integrationAreaHeight = 0;

$(document).ready(function(){

	//�ٸ���ǰ ������ data ��������
	getGoodsListDataOther();

	$(".goods-other-wrap").hide();
	getCouponlist_scroll();
	review_qna_tab();

	$(".goods-qna-certification").click(function(){
		var $this = $(this), sno = $this.attr("data-sno"), password = $("#goods-qna-password-"+sno).val();
		if (!password) {
			alert("��й�ȣ�� �Է����ּ���.");
			return false;
		}
		$.ajax({
			"url" : "ajaxAction.php",
			"type" : "post",
			"data" : "sno="+sno+"&password="+$("#goods-qna-password-"+sno).val()+"&mode=getGoodsQna",
			"dataType" : "json",
			"success" : function(responseData)
			{
				if (!responseData || !responseData.contents) alert("��й�ȣ�� ��ġ���� �ʽ��ϴ�.");
				else {
					var add_html = '';
					add_html +='<div class="qna-item-content-question">';
					add_html +='<div class="question-icon"></div>'+responseData.contents+'</div>';

					for(var i=0; i<responseData.reply.length; i++) {
						add_html +='<div class="qna-item-content-answer">';
						add_html +='<div class="answer-icon"></div>'+responseData.reply[i].contents+'</div>';
					}

					$this.parent().parent().html(add_html);
				}
			}
		});
		return false;
	});

	$(".speach-description-play").bind("click", function(){
		var $container = $(this).parent();
		var $player = $("#speach-description-player");
		if (!$player.length) return false;
		var $timer = $container.find(".speach-description-timer");
		$player.trigger("$play", [$container, $timer]);
	});

	$("#quick-buy-integration-area-close").bind("click", function(){
		$("#quick-buy-integration-area-close").css('display', 'none');
		$("#goods-detail-background-layer").hide();
		$("#quick-buy-integration-area").slideUp(100);
	});

	//top page �̵�
	$("#quick-buy-integration-area-move-top").bind("click", function(){
		$(window).scrollTop(0);
	});

	//layer selectbox
	$(".selectLabel").click(function(){
		var selectLayer = $(this).parent().find(".selectLayer");
		$(".selectLayer").not(selectLayer).hide();
		$(".selectLayer_addopt").hide();

		selectLayer.toggle();
		adjustSelectLayerHeight(selectLayer);
	});
	$(".selectLabel_addopt").click(function(){
		var selectLayer = $(this).parent().find(".selectLayer_addopt");
		$(".selectLayer").hide();
		$(".selectLayer_addopt").not(selectLayer).hide();

		selectLayer.toggle();
		adjustSelectLayerHeight(selectLayer);
	});

	//��ü�� �ʼ��ɼ�
	$("#selectLayer_single ul li").click(function(){
		var thisObj = $(this);
		var thisSelectArea = thisObj.closest(".selectArea");

		$("#opt_single").val(thisObj.attr("data-value"));
		thisSelectArea.find(".selectLabel span").html(thisObj.text());
		thisObj.closest(".selectLayer").hide();
		adjustSelectLayerHeight(thisObj.closest(".selectLayer"));
		$("#opt_single").trigger('change');
	});
	//�и��� �ʼ��ɼ�1
	$("#selectLayer_double1 ul li").click(function(){
		var thisObj = $(this);
		var thisSelectArea = thisObj.closest(".selectArea");

		$("#opt_double1").val(thisObj.attr("data-value"));
		thisSelectArea.find(".selectLabel span").html(thisObj.text());
		thisObj.closest(".selectLayer").hide();
		adjustSelectLayerHeight(thisObj.closest(".selectLayer"));
		$("#opt_double1").trigger('change');
	});
	//�߰��ɼ�
	$(".selectLayer_addopt ul li").click(function(){
		var thisObj = $(this);
		var thisSelectArea = thisObj.closest(".selectArea");
		var addoptObj = thisSelectArea.find("select[name='addopt[]']");

		addoptObj.val(thisObj.attr("data-value"));
		thisSelectArea.find(".selectLabel_addopt span").html(thisObj.text());
		thisObj.closest(".selectLayer_addopt").hide();
		adjustSelectLayerHeight(thisObj.closest(".selectLayer_addopt"));
		addoptObj.trigger('change');
	});
	//�Է¿ɼ� ��Ʈ��
	$(".inputable-addoption").bind("focus", function(){
		inputOptionEl = $(this);

		$(window).unbind('scroll touchmove');
		$(window).bind('scroll touchmove', function(event){
			event.preventDefault();
			return;
		});

		//��׶��� ��ġ ����
		fixedBackgroundLayer();

		//��׶��� ����
		$("#goods-detail-background-layer2").show();

		//�Է¿ɼ� ���̾� ����
		$("#inputOptionLayer").css('display', 'block');

		//Ű�е� ���� ����
		$(this).blur();

		$("#inputOptionLayerNameArea").html($(this).attr('label'));
		$("#inputOptionLayerInsert").val($(this).val());
		$("#inputOptionLayerInsert").focus();
	});

	//�Է¿ɼ� ���̾� - �Է½� (Ȯ�ν�)
	$("#inputOptionLayerInsertBtn").bind("click", function(){
		inputOptionEl.val($("#inputOptionLayerInsert").val());

		$("#inputOptionLayerCloseBtn").trigger("click");
	});

	$("#inputOptionLayerCloseBtn").bind("click", function(){
		//window scroll, touchmove �̺�Ʈ ����
		registEventTouchMove();

		//�Է¿ɼ� ���̾� - text �� �ʱ�ȭ
		$("#inputOptionLayerInsert").val('');
		//�Է¿ɼ� ���̾� - ���̾� none
		$("#inputOptionLayer").css('display', 'none');
		//�Է¿ɼ� ���̾� - ��� none
		$("#goods-detail-background-layer2").hide();
	});

	//�ɼǹڽ� ����, close ��ư ��ġ ����
	setQuickBuyIntegrationPosition();

	//window scroll, touchmove �̺�Ʈ ���
	registEventTouchMove();
});

$.fn.scrollView = function () {
    return this.each(function () {
        $('html, body').animate({
            scrollTop: $(this).offset().top
        }, 1000);
    });
}

function showReviewContent(review_sno) {
	if($("#review-item-content-" + review_sno).css("display") == "none") {
		$("#review-item-content-" + review_sno).slideDown(100);
	}
	else {
		$("#review-item-content-" + review_sno).slideUp(100);
	}

}

function showQnaContent(qna_sno) {
	if($("#qna-item-content-" + qna_sno).css("display") == "none") {
		$("#qna-item-content-" + qna_sno).slideDown(100);
	}
	else {
		$("#qna-item-content-" + qna_sno).slideUp(100);
	}
}

function showOtherGodds() {

	if($(".goods-other-wrap").css("display") == "none"){
		$(".goods-other-wrap").slideDown(100);
		$(".right_other_btn").addClass("right_other_btn2");
		$(".right_other_btn2").removeClass("right_other_btn");


	} else {
		$(".goods-other-wrap").slideUp(100);
		$(".right_other_btn2").addClass("right_other_btn");
		$(".right_other_btn").removeClass("right_other_btn2");
	}
}

function showCommonInfo(commoninfo_idx) {

	if($("#commoninfo-content-" + commoninfo_idx).css("display") == "none") {

		$("#commoninfo-content-" + commoninfo_idx).slideDown(100);
		$("#commoninfo-title-" + commoninfo_idx).addClass("active_title");
		$("#commoninfo-title-" + commoninfo_idx + " .down_arrow").addClass("up_arrow");
	}
	else {

		$("#commoninfo-content-" + commoninfo_idx).slideUp(100);
		$("#commoninfo-title-" + commoninfo_idx).removeClass("active_title");
		$("#commoninfo-title-" + commoninfo_idx + " .down_arrow").removeClass("up_arrow");

	}
}

function showCouponList() {
	$("#background").show();

	$(".couponlist-area").css("bottom", "-"+$(".couponlist-area").height()+"px");
	$(".couponlist-area").show();

	$(".couponlist-area").animate({bottom:0}, 300, function(){
		couponlist_scroll.refresh();
	});
}

function closeCouponList() {

	$(".couponlist-area").animate({bottom:$(".couponlist-area").height()-($(".couponlist-area").height()*2)}, 300, function(){
		$(".couponlist-area").hide();
		$("#background").hide();
	});

}

//���� ��ȿ�� üũ
function chkEAForm(obj_id) {
	if ($("#none_option").length){
		var $ea = $("form[name=frmView] [name=ea]");
		var ea = document.getElementsByName('ea');
		$ea.val(ea.value);
		if(isNaN($ea.val()) || $ea.val() < 1) {
			alert('������ ���ڷ� �Է��� �ּ���');
			$("[name=ea]").focus();
			return false;
		}
	}
	else{
		var $ea = $("form[name=frmView] [name=ea]");
		if(obj_id!="goodswish-hide") {
			if(isNaN($ea.val()) || $ea.val() < 1) {
				alert('������ ���ڷ� �Է��� �ּ���');
				$("[name=_multi_ea[]]").focus();
				return false;
			}
		}

		var multi_ea = document.getElementsByName('_multi_ea[]');
		if(!multi_ea.length) {
			alert('�ɼ��� ������ �ּ���');
			return false;
		}
	}
	return true;
}

//tab �����̵�
function moveTabArea()
{
	if($(window).scrollTop() > tabAreaOffsetTop){
		$(window).scrollTop(tabAreaOffsetTop);
	}
}
// ��׶��� ���÷��� �� ��ġ����
function fixedBackgroundLayer()
{
	$("#goods-detail-background-layer, #goods-detail-background-layer2").height($(document).height());
	$("#goods-detail-background-layer, #goods-detail-background-layer2").width($(window).width());
}
//�ɼǹڽ� ����, close ��ư ��ġ ����
function setQuickBuyIntegrationPosition()
{
	var heightGap = 100;
	defaultCloseBtnBottom = parseInt($("#quick-buy-integration-area-close").css('bottom').replace(/[^-\d\.]/g, ''));
	$(".other-settle-area").each(function() {
		if($(this).html() !== ""){
			defaultCloseBtnBottom += parseInt(heightGap);
			$("#quick-buy-integration-area").css('height', parseInt($("#quick-buy-integration-area").height() + heightGap) + 'px');
			$("#quick-buy-integration-area-close").css('bottom', defaultCloseBtnBottom + 'px');
		}
	});
	integrationAreaHeight = $("#quick-buy-integration-area").height();
}
// �ɼǹڽ� ����
function openQuickBuyIntegrationArea()
{
	fixedBackgroundLayer();
	$("#goods-detail-background-layer").show();
	$("#quick-buy-integration-area").slideDown(100, function(){
		$("#quick-buy-integration-area-close").css('display', 'block');
	});
}

//�и��� �ʼ��ɼ� �̺�Ʈ ��� (iPhone - naver app ȯ�� live ���۵�)
function setDoubleOptionEvent()
{
	//�и��� �ʼ��ɼ�2
	$("#selectLayer_double2 ul li").click(function(){
		var thisObj = $(this);
		var thisSelectArea = thisObj.closest(".selectArea");

		$("#opt_double2").val(thisObj.attr("data-value"));
		thisSelectArea.find(".selectLabel span").html(thisObj.text());
		thisObj.closest(".selectLayer").hide();
		adjustSelectLayerHeight(thisObj.closest(".selectLayer"));
		$("#opt_double2").trigger('change');
	});
}

//selectbox option ���̿� ���� �ɼ� ���̾� �ڽ� ���� ũ�� ����
function adjustSelectLayerHeight(selectLayer)
{
	var integrationArea = $('#quick-buy-integration-area');
	var standardHeight = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;
	var displayAreaHeight = $(window).scrollTop() + standardHeight - selectLayer.offset().top; //selectbox option �� ����� ���� ����

	if(selectLayer.css("display") != 'none'){
		selectLayerAdjust = false;
		if(displayAreaHeight <= selectLayer.height()){
			selectLayerAdjust = true;
			integrationArea.height(parseInt(integrationArea.height()+selectLayer.height()-displayAreaHeight));
			$("#quick-buy-integration-area-close").css('bottom', parseInt(integrationArea.height()+$("#quick-simple-button-area").height()) + 'px');
		}
	}
	else {
		if(selectLayerAdjust === true){
			integrationArea.height(integrationAreaHeight);
			$("#quick-buy-integration-area-close").css('bottom',defaultCloseBtnBottom + 'px');
		}
	}
}

//window scroll, touchmove �̺�Ʈ ���
function registEventTouchMove()
{
	$(window).unbind('scroll touchmove');
	$(window).bind('scroll touchmove', function(event){
		if(tabAreaOffsetTop > 0){
			if($(window).scrollTop() > tabAreaOffsetTop ){
				$(".tab-area").addClass("tab-area-fixed");
				$("#quick-buy-integration-area-move-top").css("display","block"); //top ��ư
			}else{
				$(".tab-area").removeClass("tab-area-fixed");
				$("#quick-buy-integration-area-move-top").css("display","none"); //top ��ư
			}
		}
		if(windowScroll === false){
			$.ajax({
				url : "ajaxGetGoodsDetail.php",
				type : "post",
				data : "goodsno="+$("form[name=frmView] [name=goodsno]").val(),
				success : function(responseData) {
					windowScroll = true;
					$("#content-detail").append(responseData);
					fixedBackgroundLayer();
				},
				async : false
			});
		}
	});
}

//���ϱ� ��ư�� ���̾� ���� üũ
function checkOtherSettleArea()
{
	$(".other-settle-area").each(function() {
		if($(this).html() != "") return true;
	});

	return false;
}

//�ٷα���/��ٱ���/���ϱ� ���� (�ٷα���:goodsorder-hide, ��ٱ���:goodscart-hide, ���ϱ�:goodswish-hide)
function indbAction2(obj_id) {
	if (strprice.length > 0) {
		$("[id=goodsres-hide] .text_msg").text("���ݴ�ü���� ��ǰ�Դϴ�");
		$("[id=goodsres-hide]").fadeIn(300);
		setTimeout( function() {
			$("[id=goodsres-hide]").fadeOut(300);
		}, 1000);
		return;
	}

	if(obj_id == 'goodsorder-hide' || obj_id == 'goodscart-hide' || (obj_id == 'goodswish-hide' && checkOtherSettleArea() === true)){
		if($("#quick-buy-integration-area").css("display") == "none"){
			openQuickBuyIntegrationArea();
			return;
		}
	}

	if(chkEAForm(obj_id)){

		var $frm = $("form[name=frmView]");
		var $mode =	$("form[name=frmView] [name=mode]");

		var opt_cnt = 0, data;

		nsGodo_MultiOption.clearField();

		for (var k in nsGodo_MultiOption.data) {
			data = nsGodo_MultiOption.data[k];
			if (data && typeof data == 'object') {
				nsGodo_MultiOption.addField(data, opt_cnt);
				opt_cnt++;
			}
		}

		switch(obj_id) {
			case 'goodsorder-hide' :
				$frm.attr("action", "../ord/order.php");

				$mode.val('addItem');

				$frm.submit();
				break;

			case 'goodscart-hide' :
				$mode.val('addCart');

				var serializedData = $("form[name=frmView]").serialize();
				$.ajax({
					type:"post",
					url:"./ajaxAction.php",
					dataType:"json",
					data: serializedData,
					success:function(result){
						showResMsg2(result);
					},
					error:function(xhr, ajaxOptions, thrownError){
						n1 = xhr.responseText.indexOf("<script>");
						n2 = xhr.responseText.indexOf("<\/script>");
						if (n1>0 && n2 >n1) {
							errmsg = xhr.responseText.substring(n1+"<script>".length, n2);
							errmsg = errmsg.replace(/alert/gi, "");
							alert(errmsg);
						} else {
							alert('��ٱ��� �߰�����!\n�ٽ� �õ��Ͽ��ֽñ� �ٶ��ϴ�.');
						}
					}
				});

				break;

			case 'goodswish-hide' :
				$mode.val('addWishlist');

				var serializedData = $("form[name=frmView]").serialize();

				$.ajax({
					type:"post",
					url:"./ajaxAction.php",
					dataType:"json",
					data: serializedData,
					success:function(result){
						showResMsg2(result);
					},
					error:function(){
						alert('�Ͻ����� ������ �߻��Ͽ����ϴ�.\n�ٽ� �õ��Ͽ��ֽñ� �ٶ��ϴ�.');
					}
				});

				break;
		}
	}
	else {
		if (obj_id == 'goodswish-hide' && $("#quick-buy-integration-area").css("display") == "none"){
			openQuickBuyIntegrationArea();
			return;
		}
	}
}

//��ٱ���/���ϱ� ��� �޽��� ���
function showResMsg2(obj) {
	var sec = 0;

	if(obj.sec == null || obj.sec == "undefined") {
		sec = 1000;
	}
	else {
		sec = obj.sec;
	}

	$("[id=goodsres-hide2] .text_msg").text(obj.msg);
	$("[id=goodsres-hide2]").fadeIn(300);

	setTimeout( function() {
		$("[id=goodsres-hide2]").fadeOut(300);

		if(obj.url && obj.url != "undefined") {
			document.location.href = obj.url;
		}

	}, sec);
}

$(window).load(function(){
	tabAreaOffsetTop = $("#tab-area").offset().top;

	if (document.location.hash === "#goods-qna") {
		$(".goods-info-area")[0].scrollIntoView(true);
		$(".goods-info-area .tab-qna").trigger("click");
	}
	else if (document.location.hash === "#goods-review") {
		$(".goods-info-area")[0].scrollIntoView(true);
		$(".goods-info-area .tab-review").trigger("click");
	}
	else if (document.location.hash === "#purchase") {
		$(".buy-info-area")[0].scrollIntoView(true);
	}
	else {
		// Nothing to do
	}
});

function show_price(price, min_ea)
{
	var order_price = 0;
	if(parseInt(min_ea) > 0){
		order_price = parseInt(price) * parseInt(min_ea);
	}

	return order_price;
}
function resetAreaPosition()
{
	integrationAreaHeight = 150;
	defaultCloseBtnBottom = 198;
	$("#quick-buy-integration-area").css('height', '150px');
	$("#quick-buy-integration-area-close").css('bottom', '198px');
}
$( window ).resize(function() {
	//��׶��� ��ġ ����
	fixedBackgroundLayer();

	//���θ�� px ����
	resetAreaPosition();
	if($(window).width() < $(window).height()){
		setQuickBuyIntegrationPosition();
	}
});
//��Ƽ�ɼ�
var nsGodo_MultiOption = function() {

	function size(e) {

		var cnt = 0;
		var type = '';

		for (var i in e) {
			cnt++;
		}

		return cnt;
	}

	return {
		_soldout : runout,
		data : [],
		data_size : 0,
		_optJoin : function(opt) {

			var a = [];

			for (var i=0,m=opt.length;i<m ;i++)
			{
				if (typeof opt[i] != 'undefined' && opt[i] != '')
				{
					a.push(opt[i]);
				}
			}

			return a.join(' / ');

		},
		_optAdd : function(opt) {

			var optAddText = '';
			if(typeof opt != 'undefined' && opt != ''){
				optAddText = " / " + opt;
			}

			return optAddText;

		},
		getFieldTag : function (name, value) {
			var el = document.createElement('input');
			el.type = "hidden";
			el.name = name;
			el.value = value;

			return el;

		},
		clearField : function() {

			var form = document.getElementsByName('frmView')[0];

			var el;

			for (var i=0,m=form.elements.length;i<m ;i++) {
				el = form.elements[i];

				if (typeof el == 'undefined' || el.tagName == "FIELDSET") continue;

				if (/^multi\_.+/.test(el.name)) {
					el.parentNode.removeChild(el);
					i--;
				}

			}

		},
		addField : function(obj, idx) {

			var _tag;
			var form = document.getElementsByName('frmView')[0];

			for(var k in obj) {

				if (typeof obj[k] == 'undefined' || typeof obj[k] == 'function' || (k != 'opt' && k != 'addopt' && k != 'ea' && k != 'addopt_inputable' && k != 'goodsno' && k != 'goodsCoupon')) continue;

				switch (k)
				{
					case 'ea':
						_tag = this.getFieldTag('multi_'+ k +'['+idx+']', obj[k]);
						form.appendChild(_tag);
						break;
					case 'addopt_inputable':
					case 'opt':
					case 'goodsno':
					case 'goodsCoupon':
					case 'addopt':
						//hasOwnProperty
						for(var k2 in obj[k]) {
							if (typeof obj[k][k2] == 'function') continue;
							_tag = this.getFieldTag('multi_'+ k +'['+idx+'][]', obj[k][k2]);
							form.appendChild(_tag);
						}

						break;
					default :
						continue;
						break;
				}
			}
		},
		set_input : function() {

			var add = true;
			var form = document.frmView;

			var opt = document.getElementsByName('opt[]');
			var addopt = document.getElementsByName('addopt[]');
			var addinput = document.getElementsByName('addopt_inputable[]');

			// ���ݿɼ� �ʼ� üũ
			for (var i=0,m=opt.length;i<m ;i++ )
			{
				if (typeof(opt[i])!="undefined") {
					if (opt[i].value == '') {
						alert(opt[i].getAttribute("msgR"));
						return;
					}
				}
			}

			// �߰� �ɼ�
			for (var i=0,m=addopt.length;i<m ;i++ )
			{
				if (typeof(addopt[i])!="undefined") {
					if (addopt[i].value == ''){
						alert(addopt[i].getAttribute("label") + " ������ ���ּ���");
						return;
					}
				}
			}

			//�Է� �ɼ�
			for (var i=0,m=addinput.length;i<m ;i++ )
			{
				if (typeof(addinput[i])!="undefined") {
					if(addinput[i].getAttribute('required') != null){
						if (addinput[i].value == '') {
							alert(addinput[i].getAttribute("label") + " �Է��� ���ּ���");
							return;
						}
					}
				}
			}

			// ��Ƽ�ɼ� �߰�
			this.add();

		},
		set : function() {

			// ���� �ɼ�
			var opt = document.getElementsByName('opt[]');
			var addopt = document.getElementsByName('addopt[]');
			var addinput = document.getElementsByName('addopt_inputable[]');

			if(!addinput.length){	//�Է¿ɼǾ�����
				// ���ݿɼ� �ʼ� üũ
				for (var i=0,m=opt.length;i<m ;i++ )
				{
					if (typeof(opt[i])!="undefined") {
						if (opt[i].value == '') {
							return;
						}
					}
				}
				// �߰� �ɼ� �ʼ� üũ
				for (var i=0,m=addopt.length;i<m ;i++ )
				{
					if (typeof(addopt[i])!="undefined") {
						if (addopt[i].value == ''){
							return;
						}
					}
				}

				// ��Ƽ�ɼ� �߰�
				this.add();
			}
			else {} // �Է¿ɼ��������� ��ư�� Ŭ���ؾ߸� ��Ƽ�ɼ� �߰�

		},
		del : function(key) {

			this.data[key] = null;
			var tr = document.getElementById(key);
			tr.parentNode.removeChild(tr);
			this.data_size--;

			// �� �ݾ�
			this.totPrice();
		},
		add : function() {
			var self = this;

			if (self._soldout)
			{
				alert("ǰ���� ��ǰ�Դϴ�.");
				return;
			}
			var form = document.frmView;

			var _data = {};

			_data.ea = document.frmView.ea.value;
			_data.sales_unit = document.frmView.ea.getAttribute('step') || 1;
			_data.opt = new Array;
			_data.addopt = new Array;
			_data.addopt_inputable = new Array;

			// ���ݿɼ�
			var opt = document.getElementsByName('opt[]');
			var key = '';	// ���ݿɼ� Ű

			if (opt.length > 0) {

				_data.opt[0] = opt[0].value;
				_data.opt[1] = '';
				if (typeof(opt[1]) != "undefined") _data.opt[1] = opt[1].value;

				key = _data.opt[0] + (_data.opt[1] != '' ? '|' + _data.opt[1] : '');

				// ����
				if (key == null) key = fkey;
				// ���ݿɼ� Ű ����
				if (key) {
					key = self.get_key(key);	// get_js_compatible_key ����
				}

				if (typeof(price[key])!="undefined"){

					_data.price = price[key];
					_data.reserve = reserve[key];
					_data.consumer = consumer[key];
					_data.realprice = realprice[key];
					_data.couponprice = couponprice[key];
					_data.coupon = coupon[key];
					_data.cemoney = cemoney[key];
					_data.memberdc = memberdc[key];
					_data.special_discount_amount = special_discount_amount[key];

				}
				else {
					alert('�߰��� �� ����.');
					return;
				}
			}
			else { // ���ÿɼ� ���� ��
				// key ����
				key = 'base';
				if (typeof(price[key])!="undefined"){

					_data.price = price[key];
					_data.reserve = reserve[key];
					_data.consumer = consumer[key];
					_data.realprice = realprice[key];
					_data.couponprice = couponprice[key];
					_data.coupon = coupon[key];
					_data.cemoney = cemoney[key];
					_data.memberdc = memberdc[key];
					_data.special_discount_amount = special_discount_amount[key];

				}
				else {
					alert('�߰��� �� ����.');
					return;
				}
			}

			var addopt = document.getElementsByName('addopt[]'); // �߰� �ɼ�
			var addopt_key = '';	// �߰��ɼ� Ű
			var tmp_arr_addopt	= [];	// �߰��ɼ� üũŰ
			for (var i=0,m=addopt.length;i<m ;i++ ) {

				if (typeof addopt[i] == 'object') {
					if (addopt[i].value != '' && addopt[i].value != '-1') { // �������̰ų� ���þ����� ��� ���� ó��
						_data.addopt.push(addopt[i].value);

						// �߰��ɼ� Ű ����
						tmp_arr_addopt	= addopt[i].value.split('^');
						addopt_key		= addopt_key + tmp_arr_addopt[0];
					}
				}

			}
			// �߰��ɼ� Ű ����
			if (addopt_key) {
				addopt_key	= self.get_key(addopt_key);
			}

			var addopt_inputable = document.getElementsByName('addopt_inputable[]');	// �Է� �ɼ�
			var addopt_input_key	= '';	// �Է¿ɼ� Ű
			for (var i=0,m=addopt_inputable.length;i<m ;i++ ) {

				if (typeof addopt_inputable[i] == 'object') {
					var v = addopt_inputable[i].value.trim();
					if (v) {
						var tmp = addopt_inputable[i].getAttribute("option-value").split('^');
						tmp[2] = v;
						_data.addopt_inputable.push(tmp.join('^'));

						// �Է¿ɼ� Ű ����
						addopt_input_key	= addopt_input_key + v;
					}

					// �ʵ尪 �ʱ�ȭ
					addopt_inputable[i].value = '';

				}

			}
			// �Է¿ɼ� Ű ����
			if (addopt_input_key) {
				addopt_input_key	= self.get_key(addopt_input_key);
			}

			// ��ǰŰ �缼��
			key	= key + (addopt_key != '' ? '^' + addopt_key : '') + (addopt_input_key != '' ? '^' + addopt_input_key : '');

			// �̹� �߰��� �ɼ�����
			if (self.data[key] != null)
			{
				alert('�̹� �߰��� �ɼ��Դϴ�.');
				return false;
			}

			// �ɼ� �ڽ� �ʱ�ȭ
			setTimeout( function() {
				for (var i=0,m=addopt.length;i<m ;i++ )
				{
					if (typeof addopt[i] == 'object') {
						addopt[i].selectedIndex = 0;
					}
				}
				//�߰��ɼ� ���̾� �ʱ�ȭ
				$(".selectLayer_addopt").each(function() {
					var thisSelectlabel_addopt = $(this).siblings('.selectLabel_addopt');
					thisSelectlabel_addopt.children('span').html(thisSelectlabel_addopt.attr('default-data-value'));
				});
			}, 100);

			document.getElementById('el-multi-option-display').style.display = 'block';

			// �� �߰�
			var childs = document.getElementById('el-multi-option-display').childNodes;
			for (var k in childs)
			{
				if (childs[k].tagName == 'TABLE') {
					var table = childs[k];
					break;
				}
			}
			var tr = table.insertRow(-1);

			var html = '';

			tr.id = key;

			// �ɼǸ�
			html += '<div class="order-contents-area"><div class="buy-info-title" style="font-size:11px;color:#010101;margin-right:4px;max-width: 100%;">';
			html += self._optJoin(_data.opt);

			// �߰� �ɼǸ�
			var tmp,tmp_addopt = [];
			for (var i=0,m=_data.addopt.length;i<m ;i++ )
			{
				tmp = _data.addopt[i].split('^');
				if (tmp[2]) tmp_addopt.push(tmp[2]);
			}
			html += self._optAdd(tmp_addopt);

			// �Է� �ɼǸ�
			var tmp,tmp_addopt = [];
			for (var i=0,m=_data.addopt_inputable.length;i<m ;i++ )
			{
				tmp = _data.addopt_inputable[i].split('^');
				if (tmp[2]) tmp_addopt.push(tmp[2]);
			}
			html += self._optAdd(tmp_addopt);
			html += '</div></div>';

			html += '<div class="order-contents-area"><div class="buy-info-title" style="float:left;">';
			html += '<div class="cnt_plus" onClick="nsGodo_MultiOption.ea(\'up\',\''+key+'\');" style="cursor:pointer"></div>';
			html += '<div class="cnt_minus" onClick="nsGodo_MultiOption.ea(\'down\',\''+key+'\');" style="cursor:pointer"></div>';
			html += '<input type=text name=_multi_ea[] id="el-ea-'+key+'" size=2 value='+ _data.ea +' style="border:1px solid #D3D3D3;width:50px;text-align:right;height:20px" onblur="nsGodo_MultiOption.ea(\'set\',\''+key+'\',this.value);">';
			html += '</div>';

			// �ɼǰ���
			_data.opt_price = _data.price;
			for (var i=0,m=_data.addopt.length;i<m ;i++ )
			{
				tmp = _data.addopt[i].split('^');
				if (tmp[3]) _data.opt_price = _data.opt_price + parseInt(tmp[3]);
			}
			for (var i=0,m=_data.addopt_inputable.length;i<m ;i++ )
			{
				tmp = _data.addopt_inputable[i].split('^');
				if (tmp[3]) _data.opt_price = _data.opt_price + parseInt(tmp[3]);
			}

			html += '<div class="buy-info-contents">';
			html += '<span id="el-price-'+key+'">'+comma( _data.opt_price *  _data.ea) + '��</span>';
			html += '<a href="javascript:void(0);" onClick="nsGodo_MultiOption.del(\''+key+'\');return false;"><img class="del_multi_opt"/></a></div>';
			tr.innerHTML = '<td>' + html + '</td>';

			self.data[key] = _data;
			self.data_size++;

			// �� �ݾ�
			self.totPrice();
		},
		ea : function(dir, key,val) {	// up, down

			var min_ea = 0, max_ea = 0, remainder = 0;

			if (document.frmView.min_ea) min_ea = parseInt(document.frmView.min_ea.value);
			if (document.frmView.max_ea) max_ea = parseInt(document.frmView.max_ea.value);

			if (dir == 'up') {
				this.data[key].ea = (max_ea != 0 && max_ea <= this.data[key].ea) ? max_ea : parseInt(this.data[key].ea) + parseInt(this.data[key].sales_unit);
			}
			else if (dir == 'down')
			{
				if ((parseInt(this.data[key].ea) - 1) > 0)
				{
					this.data[key].ea = (min_ea != 0 && min_ea >= this.data[key].ea) ? min_ea : parseInt(this.data[key].ea) - parseInt(this.data[key].sales_unit);
				}

			}
			else if (dir == 'set') {

				if (val && !isNaN(val))
				{
					val = parseInt(val);

					if (max_ea != 0 && val > max_ea)
					{
						val = max_ea;
					}
					else if (min_ea != 0 && val < min_ea) {
						val = min_ea;
					}
					else if (val < 1)
					{
						val = parseInt(this.data[key].sales_unit);
					}

					remainder = val % parseInt(this.data[key].sales_unit);

					if (remainder > 0) {
						val = val - remainder;
					}

					this.data[key].ea = val;

				}
				else {
					alert('������ 1 �̻��� ���ڷθ� �Է��� �ּ���.');
					return;
				}
			}

			document.getElementById('el-ea-'+key).value = this.data[key].ea;
			document.getElementById('el-price-'+key).innerHTML = comma(this.data[key].ea * this.data[key].opt_price) + '��';

			// �ѱݾ�
			this.totPrice();

		},
		totPrice : function() {
			var self = this;
			var totprice = 0;
			for (var i in self.data)
			{
				if (self.data[i] !== null && typeof self.data[i] == 'object') totprice += self.data[i].opt_price * self.data[i].ea;
			}

			document.getElementById('el-multi-option-total-price').innerHTML = comma(totprice) + '��';
		},
		get_key : function(str) {

			str = str.replace(/&/g, "&amp;").replace(/\"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');

			var _key = "";

			for (var i=0,m=str.length;i<m;i++) {
				_key += str.charAt(i) != '|' ? str.charCodeAt(i) : '|';
			}

			return _key.toUpperCase();
		}
	}
}();