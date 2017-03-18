/* DOM Node Create */
document.createElement('header');
document.createElement('footer');
document.createElement('section');
document.createElement('aside');
document.createElement('nav');
document.createElement('article');

function listFill(id,liWidth){
	var obj = document.getElementById(id);
	var liLength = 0;
	var i, newLi;

	for(i=0;i<obj.childNodes.length;i++) {
		if(obj.childNodes[i]=='[object HTMLLIElement]' && obj.childNodes[i].className != 'blank') liLength++;
		if(obj.childNodes[i].className == 'blank')	obj.removeChild(obj.childNodes[i--]);
	}

	var cols = Math.floor((obj.offsetWidth)/liWidth);

	var newCols = cols-(liLength%cols);
	if(newCols==cols) newCols=0;

	for(i=0;i<newCols;i++){
		newLi = document.createElement("li");
		newLi.className = 'blank';
		obj.appendChild(newLi);
	}

}

function addListFillEvent(objID,liWidth){
	if(window.addEventListener) {
		window.addEventListener("load",function(){listFill(objID,liWidth)},false);
		if ("onorientationchange" in window) window.addEventListener("orientationchange",function(){listFill(objID,liWidth)},false);
		else							window.addEventListener("resize",function(){listFill(objID,liWidth)},false);
	}
	else if(window.attachEvent) {
		window.attachEvent("onload",function(){listFill(objID,liWidth)});
		window.attachEvent("onresize",function(){listFill(objID,liWidth)});
	}
}

function _ID(obj){return document.getElementById(obj)}

function onlynumber()
{
	if ( window.event == null ) return;

	var e = event.keyCode;

	if (e>=48 && e<=57) return;
	if (e>=96 && e<=105) return;
	if ( e==8 || e==9 || e==13 || e==37 || e==39) return; // tab, back, ←,→
	event.returnValue = false;
}

/**
 * isChked(El,msg)
 *
 * 체크박스의 체크 유무 판별
 *
 * -msg		null	바로 진행
 *			msg		confirm창을 띄어 실행 유무 체크 (msg - confirm창의 질의 내용)
 * @Usage	<input type=checkbox name=chk[]>
 *			<a href="javascript:void(0)" onClick="return isChked(document.formName.elements['chk[]'],null|msg)">del</a>
 */

function isChked(El,msg)
{
	if (!El) return;
	if (typeof(El)!="object") El = document.getElementsByName(El);
	if (El) for (i=0;i<El.length;i++) if (El[i].checked) var isChked = true;
	if (isChked){
		return (msg) ? confirm(msg) : true;
	} else {
		alert ("선택된 사항이 없습니다");
		return false;
	}
}

/**
 * chkBox(El,mode)
 *
 * 동일한 이름의 체크박스의 체크 상황 컨트롤
 *
 * -mode	true	전체선택
 *			false	선택해제
 *			'rev'	선택반전
 * @Usage	<input type=checkbox name=chk[]>
 *			<a href="javascript:void(0)" onClick="chkBox(document.getElementsByName('chk[]'),true|false|'rev')">chk</a>
 */

function chkBox(El,mode)
{
	if (!El) return;
	if (typeof(El)!="object") El = document.getElementsByName(El);
	for (i=0;i<El.length;i++) El[i].checked = (mode=='rev') ? !El[i].checked : mode;
}

/**
 * chkForm(form)
 *
 * 입력박스의 null 유무 체크와 패턴 체크
 *
 * @Usage	<form onSubmit="return chkForm(this)">
 */

function chkForm(form)
{
	if (typeof(mini_obj)!="undefined" || document.getElementById('_mini_oHTML')) mini_editor_submit();

	var reschk = 0;
	for (i=0;i<form.elements.length;i++){
		currEl = form.elements[i];
		if (currEl.disabled) continue;
		if (currEl.getAttribute("required")!=null || currEl.getAttribute("fld_esssential")!=null){
			if (currEl.type=="checkbox" || currEl.type=="radio"){
				if (!chkSelect(form,currEl,currEl.getAttribute("msgR"))) return false;
			} else {
				if (!chkText(currEl,currEl.value,currEl.getAttribute("msgR"))) return false;
			}
		}

		if (currEl.getAttribute("label")=='주민등록번호'  && currEl.getAttribute("name") == 'resno[]' && currEl.value.length>0){
			reschk = 1;

		}
		if (currEl.getAttribute("option")!=null && currEl.value.length>0){
			if (!chkPatten(currEl,currEl.getAttribute("option"),currEl.getAttribute("msgO"))) return false;
		}
		if (currEl.getAttribute("minlength")!=null){
			if (!chkLength(currEl,currEl.getAttribute("minlength"))) return false;
		}
	}
	if (form.password2){
		if (form.password.value!=form.password2.value){
			alert("비밀번호가 일치하지 않습니다");
			form.password.value = "";
			form.password2.value = "";
			return false;
		}
	}
	if (reschk && !chkResno(form)) return false;
	if (form.agreeyn){
		if (form.agreeyn[0].checked === false){
			alert("개인정보 수집 및 이용에 대한 안내에 동의 하셔야 작성이 가능합니다.");
			return false;
		}
	}

	if ((form.nickname) && (form.nickname != "undefined")){
		if (form.nickname.value.length > 1 && form.chk_nickname.value.length == 0){
			alert("닉네임 중복을 체크 하셔야 합니다");
			return false ;
		}
	}

	if (form.chkSpamKey) form.chkSpamKey.value = 1;
	if (document.getElementById('avoidDbl')) document.getElementById('avoidDbl').innerHTML = "--- 데이타 입력중입니다 ---";
	return true;
}

function chkLength(field,len)
{
	text = field.value;
	if (text.trim().length<len){
		alert(len + "자 이상 입력하셔야 합니다");
		field.focus();
		return false;
	}
	return true;
}

function chkText(field,text,msg)
{
	text = text.trim();
	if (text==""){
		var caption = field.parentNode.parentNode.firstChild.innerText;
		if (!field.getAttribute("label")) field.setAttribute("label",(caption)?caption:field.name);
		if (!msg) msg = "[" + field.getAttribute("label") + "] 필수입력사항";
		//if (msg) msg2 += "\n\n" + msg;
		alert(msg);
		if (field.tagName!="SELECT") field.value = "";
		if (field.type!="hidden" && field.style.display!="none") field.focus();
		return false;
	}
	return true;
}

function chkSelect(form,field,msg)
{
	var ret = false;
	fieldname = eval("form.elements['"+field.name+"']");
	if (fieldname.length){
		for (j=0;j<fieldname.length;j++) if (fieldname[j].checked) ret = true;
	} else {
		if (fieldname.checked) ret = true;
	}
	if (!ret){
		if (!field.getAttribute("label")) field.getAttribute("label") = field.name;
		var msg2 = "[" + field.getAttribute("label") + "] 필수선택사항";
		if (msg) msg2 += "\n\n" + msg;
		alert(msg2);
		field.focus();
		return false;
	}
	return true;
}

function chkPatten(field,patten,msg)
{
	var regNum			= /^[0-9]+$/;
	var regEmail		= /^[^"'@]+@[._a-zA-Z0-9-]+\.[a-zA-Z]+$/;
	var regUrl			= /^(http\:\/\/)*[.a-zA-Z0-9-]+\.[a-zA-Z]+$/;
	var regAlpha		= /^[a-zA-Z]+$/;
	var regHangul		= /[\uAC00-\uD7A3]/;
	var regHangulEng	= /[\uAC00-\uD7A3a-zA-Z]/;
	var regHangulOnly	= /^[\uAC00-\uD7A3]*$/;
	var regId			= /^[a-zA-Z0-9]{1}[^"']{3,9}$/;
	var regPass			= /^[a-zA-Z0-9_-]{4,12}$/;

	patten = eval(patten);
	if (!patten.test(field.value)){
		var caption = field.parentNode.parentNode.firstChild.innerText;
		if (!field.getAttribute("label")) field.setAttribute("label",(caption)?caption:field.name);
		var msg2 = "[" + field.getAttribute("label") + "] 입력형식오류";
		if (msg) msg2 += "\n\n" + msg;
		alert(msg2);
		field.focus();
		return false;
	}
	return true;
}

/// 스트링 객체에 메소드 추가 ///
String.prototype.trim = function(str) {
	str = this != window ? this : str;
	return str.replace(/^\s+/g,'').replace(/\s+$/g,'');
}

// 스트링버퍼 //
var StringBuffer = function() {
this.buffer = new Array();
}

StringBuffer.prototype.append = function(obj) {
this.buffer.push(obj);
}

StringBuffer.prototype.toString = function(){
return this.buffer.join("");
}

/**
 * selectDisabled(oSelect)
 *
 * 셀렉트박스에 disabled 옵션추가
 */
function selectDisabled(oSelect){
	var isOptionDisabled = oSelect.options[oSelect.selectedIndex].disabled;
    if (isOptionDisabled){
        oSelect.selectedIndex = oSelect.preSelIndex;
        return false;
    } else oSelect.preSelIndex = oSelect.selectedIndex;
    return true;
}

/**
 * comma(x), uncomma(x)
 *
 * 숫자 표시 (3자리마다 콤마찍기)
 *
 * @Usage	var money = 1000;
 *			money = comma(money);
 *			alert(money);
 *			alert(uncomma(money));
 */

function comma(x)
{
	var temp = "";
	var x = String(uncomma(x));

	num_len = x.length;
	co = 3;
	while (num_len>0){
		num_len = num_len - co;
		if (num_len<0){
			co = num_len + co;
			num_len = 0;
		}
		temp = ","+x.substr(num_len,co)+temp;
	}
	return temp.substr(1);
}

function uncomma(x)
{
	var reg = /(,)*/g;
	x = parseInt(String(x).replace(reg,""));
	return (isNaN(x)) ? 0 : x;
}

/* 우편번호 검색 */
function search_zipcode(){
	var form = document.frmOrder;
	var list = _ID('zipcode_list');
	var zipcode, address;

	if(form.dong.value==''){
		form.dong.focus();
	}
	else{

		// 리스트 초기화
		$('#zipcode_list').show();
		$('#zipcode_list ul').remove();

		// 인디케이터 추가
		var indicator = document.createElement('div');
		indicator.className='indicator';
		indicator.style.display='block';
		$('#zipcode_list').append(indicator);

		// 데이터
		$.ajax({
			url:'../proc/zipcode_search.php',
			data: ({dong:form.dong.value}),
			dataType: "json",
			success: function(result){
				$('#zipcode_list div.indicator').remove();

				if(result.list!=undefined){
					var ul = document.createElement('ul');
					ul.className = 'hidden';
					$('#zipcode_list').append(ul);
					for(var i=0;i<result.list.length;i++){
						zipcode = result.list[i].zipcode;
						address1 = result.list[i].sido +' '+ result.list[i].gugun  +' '+ result.list[i].dong;
						address2 = ' '+ result.list[i].bunji;
						$('#zipcode_list ul').append("<li><a href=\"javascript:zipcode('"+zipcode+"','"+address1+"');\">("+ zipcode +") "+ address1 + address2 +"</a></li>");
					}
					$('#zipcode_list ul').slideDown();
				}
				else{
					alert("검색 결과가 없습니다.");
					$('#zipcode_list').hide();
				}
			},
			error: function(){
				$('#zipcode_list div.indicator').remove();
				alert("일시적인 오류가 발생하였습니다.\n다시 시도해주시기 바랍니다.");
			}
		});
	}
}

/* 우편번호 선택 */
function zipcode(zipcode,address)
{
	$('#zipcode_list ul').remove();
	$('#zipcode_list').hide();

	var form = document.frmOrder;
	var r_zipcode = zipcode.split("-");
	form['zipcode[]'][0].value = r_zipcode[0];
	form['zipcode[]'][1].value = r_zipcode[1];
	form.dong.value = '';
	form.address.value = address;
	form.address_sub.focus();

	if(form.deliPoli != undefined){
		getDelivery();
	}
}

/* 우편번호 검색 */
function search_zipcode2(){
	var form = document.frmAgree;
	var list = _ID('zipcode_list');
	var zipcode, address;

	if(form.dong.value==''){
		form.dong.focus();
	}
	else{

		// 리스트 초기화
		$('#zipcode_list').show();
		$('#zipcode_list ul').remove();

		// 인디케이터 추가
		var indicator = document.createElement('div');
		indicator.className='indicator';
		indicator.style.display='block';
		$('#zipcode_list').append(indicator);

		// 데이터
		$.ajax({
			url:'../proc/zipcode_search.php',
			data: ({dong:form.dong.value}),
			dataType: "json",
			success: function(result){
				$('#zipcode_list div.indicator').remove();

				if(result.list!=undefined){
					var ul = document.createElement('ul');
					ul.className = 'hidden';
					$('#zipcode_list').append(ul);
					for(var i=0;i<result.list.length;i++){
						zipcode = result.list[i].zipcode;
						address1 = result.list[i].sido +' '+ result.list[i].gugun  +' '+ result.list[i].dong;
						address2 = ' '+ result.list[i].bunji;
						$('#zipcode_list ul').append("<li><a href=\"javascript:zipcode2('"+zipcode+"','"+address1+"');\">("+ zipcode +") "+ address1 + address2 +"</a></li>");
					}
					$('#zipcode_list ul').slideDown();
				}
				else{
					alert("검색 결과가 없습니다.");
					$('#zipcode_list').hide();
				}
			},
			error: function(){
				$('#zipcode_list div.indicator').remove();
				alert("일시적인 오류가 발생하였습니다.\n다시 시도해주시기 바랍니다.");
			}
		});
	}
}

/* 우편번호 선택 */
function zipcode2(zipcode,address)
{
	$('#zipcode_list ul').remove();
	$('#zipcode_list').hide();

	var form = document.frmAgree;
	var r_zipcode = zipcode.split("-");
	form['zipcode[]'][0].value = r_zipcode[0];
	form['zipcode[]'][1].value = r_zipcode[1];
	form.dong.value = '';
	form.address.value = address;
	form.address_sub.focus();

	if(form.deliPoli != undefined){
		getDelivery();
	}
}

/*** 할인액 계산 ***/
function getDcprice(price,dc,po)
{
	if(!po)po=100;
	if (!dc) return 0;
	var ret = (dc.match(/%$/g)) ? price * parseInt(dc.substr(0,dc.length-1)) / 100 : parseInt(dc);
	return parseInt(ret / po) * po;
}

function orderCntCalc(o, cnt, set) {
	if (typeof (o) == 'number') {
		set = cnt;
		cnt = o;
		o = $('#ea');
	}

	try {
		o = o.get(0);
	}
	catch (e) {}

	var step = parseInt(o.getAttribute('step')) || 1;
	var min = parseInt(o.getAttribute('min')) || 1;
	var max = parseInt(o.getAttribute('max')) || 0;

	var before_cnt = o.value;

	if(before_cnt == "") {
		before_cnt = min;
	}

	if (!set)
	{
		cnt = cnt * step;
		var cal_cnt = parseInt(before_cnt) + parseInt(cnt);
	}
	else {
		var cal_cnt = Math.abs(cnt) || min;
	}

	if (cal_cnt < min) {
		cal_cnt = min;
	}
	else if (max && cal_cnt > max) {
		cal_cnt = max;
	}

	var remainder = cal_cnt % step;
	if (remainder)
	{
		cal_cnt = cal_cnt - remainder;
	}

	if(cal_cnt < 0) {
		cal_cnt = 0;
	}
	o.value = cal_cnt;

	orderPriceCalc(o, cal_cnt);
}

function orderPriceCalc(o, cal_cnt) {
	var price = parseInt(o.getAttribute('data-price'));
	var order_price = cal_cnt * price;
	$('#order_price').text(comma(order_price));
}

/*** 스토리지 지원 여부 ***/
function supports_html5_storage() {
  try {
    return 'localStorage' in window && window['localStorage'] !== null;
  } catch (e) {
    return false;
  }
}

/*** 세션 스토리지 저장 ***/
function saveSession(control_key, control_value) {
	if (!supports_html5_storage()) {
		createCookie(control_key, control_value, 7);
	} else {
		sessionStorage[control_key] = control_value;
	}
};

/*** 세션 스토리지 로드 ***/
function loadSession(control_key) {
	var control_value;
	if (!supports_html5_storage()) {
		control_value = readCookie(control_key);
	} else {
		control_value = sessionStorage[control_key];
	}
	return control_value;
};

/*** 로컬 스토리지 저장 ***/
function saveVal(control_key, control_value) {
	if (!supports_html5_storage()) {
		createCookie(control_key, control_value, 7);
	} else {
		localStorage.setItem(control_key, control_value);
	}
};

/*** 로컬 스토리지 로드 ***/
function loadVal(control_key) {
	var control_value;
	if (!supports_html5_storage()) {
		control_value = readCookie(control_key);
	} else {
		control_value = localStorage.getItem(control_key);
	}
	return control_value;
};

/*** 쿠키 생성 ***/
function createCookie(name, value, days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
		var expires = "; expires=" + date.toGMTString();
	} else
		var expires = "";
	document.cookie = name + "=" + value  + "; path=/; expires=" + expires + ";";
};

/*** 쿠키 호출 ***/
function readCookie(name) {
	var result = "";
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) == 0) {
			result = c.substring(nameEQ.length, c.length);
		}
	}
	return result;
}

/*** 카테고리 출력 ***/
function showCateMenu(aTag, now_cate) {
	var menuBox = $(aTag).parent();
	var self = $(aTag);
	if (menuBox.find('ul').length <= 0) {
		var depth = (now_cate.length / 3 + 1);
		var data_param;
		data_param = "mode=get_category";
		data_param += "&now_cate=" + now_cate;

		try {
			$.ajax({
				type: "post",
				url: "/"+ mobile_root + "/proc/mAjaxAction.php",
				cache:false,
				async:false,
				data: data_param,
				success: function (res) {
					if (res.child_res != null) {
						if (res.child_res.length > 0) {
							makeCateList2(res.child_res, menuBox, depth, self);
							//	console.log(res.child_res);
							// if (depth == 1) {
								// var onoffbtn = '<button type="button" class="btn-reset gnb-arr"><span class="sprite-icon icon-arr-b-white"></span></button>';
							// 	menuBox.prepend(onoffbtn);
							// }
						}
					}
				},
				dataType:'json'
			});
		}
		catch(e) {
			alert(e);
		}
	}
	togglenav(self,menuBox);
}

/*** 카테고리 및 게시판 토글 ***/
function togglenav(self,currentItem){
	if(self.siblings('ul').length > 0){
		if(self.parent('.on').length>0){ // 열린 상태(자신)
			currentItem.find('>ul').hide();
			currentItem.find('>button').removeClass('block');
			currentItem.removeClass('on');
			currentItem.find('>a .icon-plus1').removeClass('open');
		}else{ // 닫힌 상태(자신+이외)
			currentItem.siblings('li').find('>ul').hide();
			currentItem.siblings('li').find('> button,.open').removeClass('block');
			currentItem.siblings('li').find('>a .icon-plus1').removeClass('open');
			currentItem.siblings('li').removeClass('on');
			currentItem.find('>button').addClass('block');
			currentItem.find('>ul').show();
			currentItem.find('>a .icon-plus1').addClass('open');
			currentItem.addClass('on');
		}
	event.preventDefault();
	}
}

/*** 카테고리 데이터로 HTML 출력 ***/
function makeCateList2(cate_data, menuBox, depth, self) {
	if (menuBox.find('ul').length <= 0) {
		// 분류 나열
		var item_html = '<ul class="dep'+(depth+1)+'">';
		for(var i=0; i<cate_data.length; i++) {
			var onoffbtn = '<button type="button" class="btn-reset gnb-arr" onClick="goCate(\'' + cate_data[i].category + '\')"><span class="sprite-icon icon-arr-b-white"></span></button>';
			if (cate_data[i].sub_count > 0) { // 하위 분류 있는 경우
				item_html += '<li>' + onoffbtn + '<a href="#" onClick="javascript:showCateMenu(this, \'' + cate_data[i].category + '\');" class="sub-icon"><span class="sprite-icon icon-plus1"></span>' + cate_data[i].catnm + '</a></li>';
			}
			else { // 하위 분류 없는 경우
				item_html += '<li>' + onoffbtn + '<a href="#" onClick="goCate(\'' + cate_data[i].category + '\')">' + cate_data[i].catnm + '</a></li>'; // 하위 분류가 없을 시 a 태그에 버튼과 같은 액션이 들어감.
			}
		}
		item_html += '</ul>';
		menuBox.append(item_html);
	}
}

// 상품뷰페이지의 url복사 스크립트
function goodsCopyUrl(){
	var _copyUrl = location.href;
	var copyUrlHtml = '<div id="copyUrlArea"><div id="copyUrlInnerArea"><div id="copyUrlAreaClose" onclick="copyUrlAreaClose()"></div><div style="position:relative;">주소를 길게 누르면<br>상품의 URL을 복사할 수 있습니다.</div><br><input type="text"></div></div>';

	$("body").append(copyUrlHtml);
	$("#background").fadeIn().attr({"onclick":""}).click(copyUrlAreaClose);
	$("#copyUrlArea").show();
	$("#copyUrlInnerArea input[type='text']").val(_copyUrl);
}

// 상품url복사 영역 제거
function copyUrlAreaClose(){
	$("#background").fadeOut();
	$("#copyUrlArea").hide().remove();
}

window.onmessage = function(event) {
	// 핸드폰 인증시 취소를 하면 부모을 새로고침
	if (event.data === "reloaded") {
		location.reload();
	}
};

/*
팝업창을 레이어로 변경
function frmMake(오픈 url,레이어 프레임 이름, 해당 프레임상단 타이틀, 아이핀여부(아이핀의 경우 화면가로사이즈를 device가로가 아닌 480으로 설정함으로 다른 레이어와는 다르게 화면 확대 비율이 다름, 따라서 아이핀일때와 아닐때의 폰트사이즈를 나눠서 설정함, 레이어팝업 형태, 레이어팝업 높이)){
*/
function frmMake(url,frmName,title,ipin,layer,height){
	var frmClassName = titleId = closeId = frameHeight = _font = _height = "";

	_height = $("#wrap").innerHeight() + $("#footer").innerHeight();
	_font = ipin === true?'28':'20';
	if (typeof(layer) != 'undefined' && layer === true){
		frmClassName = "layer-class";
		titleId = "layer_title";
		closeId = "cancel-btn";
		frameHeight = ((height) ? height : 200) + "px";
	} else {
		frmClassName = "";
		titleId = "frmTitle";
		closeId = "frmClose";
		frameHeight = _height + "px";
	}

	var _frm = "<div id='frmMask' style='height:" + _height + "px;' onclick=\"frmMaskRemove('" + frmName + "')\"></div>";
	_frm += "<section id='frmLayer' class='" + frmClassName + "'>";
	_frm += "<div id='" + frmName + "_area' class='mobileLayerArea' name='" + frmName + "_area'>";
	if (title)
		_frm += "<div id='" + titleId + "' name='frmTitle' style='font-size:" + _font + "px;'><div class='title'>" + title + "</div><div id='"+ closeId +"' onclick=\"frmMaskRemove('" + frmName + "')\"></div></div>";
	_frm += "<iframe id='" + frmName + "' class='mobileLayerFrame' name='" + frmName + "' style='height:" + frameHeight + ";' src='" + url + "'></iframe>";
	_frm += "</div>";
	_frm += "</section>";

	$("body").append(_frm);
	if (typeof(layer) != 'undefined' && layer === true){
		$(".layer-class").css("top",($(window).scrollTop() + 100) + "px");
	}else{
		$(window).scrollTop(0,0);
	}
}

/*
오픈한 레이어프레임을 닫음
function frmMaskRemove(레이어 프레임 이름){
*/
function frmMaskRemove(frmName){
	$("#frmMask, #frmLayer").remove();
	$("meta[name='viewport']").attr({"content":"user-scalable=yes, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width, height=device-height"});
}