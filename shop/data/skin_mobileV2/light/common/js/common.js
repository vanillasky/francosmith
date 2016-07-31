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
		o = $("[name=order_cnt]");
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
}
