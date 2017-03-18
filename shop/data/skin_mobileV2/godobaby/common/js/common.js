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
	if ( e==8 || e==9 || e==13 || e==37 || e==39) return; // tab, back, ��,��
	event.returnValue = false;
}

/**
 * isChked(El,msg)
 *
 * üũ�ڽ��� üũ ���� �Ǻ�
 *
 * -msg		null	�ٷ� ����
 *			msg		confirmâ�� ��� ���� ���� üũ (msg - confirmâ�� ���� ����)
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
		alert ("���õ� ������ �����ϴ�");
		return false;
	}
}

/**
 * chkBox(El,mode)
 *
 * ������ �̸��� üũ�ڽ��� üũ ��Ȳ ��Ʈ��
 *
 * -mode	true	��ü����
 *			false	��������
 *			'rev'	���ù���
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
 * �Է¹ڽ��� null ���� üũ�� ���� üũ
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

		if (currEl.getAttribute("label")=='�ֹε�Ϲ�ȣ'  && currEl.getAttribute("name") == 'resno[]' && currEl.value.length>0){
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
			alert("��й�ȣ�� ��ġ���� �ʽ��ϴ�");
			form.password.value = "";
			form.password2.value = "";
			return false;
		}
	}
	if (reschk && !chkResno(form)) return false;
	if (form.agreeyn){
		if (form.agreeyn[0].checked === false){
			alert("�������� ���� �� �̿뿡 ���� �ȳ��� ���� �ϼž� �ۼ��� �����մϴ�.");
			return false;
		}
	}

	if ((form.nickname) && (form.nickname != "undefined")){
		if (form.nickname.value.length > 1 && form.chk_nickname.value.length == 0){
			alert("�г��� �ߺ��� üũ �ϼž� �մϴ�");
			return false ;
		}
	}

	if (form.chkSpamKey) form.chkSpamKey.value = 1;
	if (document.getElementById('avoidDbl')) document.getElementById('avoidDbl').innerHTML = "--- ����Ÿ �Է����Դϴ� ---";
	return true;
}

function chkLength(field,len)
{
	text = field.value;
	if (text.trim().length<len){
		alert(len + "�� �̻� �Է��ϼž� �մϴ�");
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
		if (!msg) msg = "[" + field.getAttribute("label") + "] �ʼ��Է»���";
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
		var msg2 = "[" + field.getAttribute("label") + "] �ʼ����û���";
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
		var msg2 = "[" + field.getAttribute("label") + "] �Է����Ŀ���";
		if (msg) msg2 += "\n\n" + msg;
		alert(msg2);
		field.focus();
		return false;
	}
	return true;
}

/// ��Ʈ�� ��ü�� �޼ҵ� �߰� ///
String.prototype.trim = function(str) {
	str = this != window ? this : str;
	return str.replace(/^\s+/g,'').replace(/\s+$/g,'');
}

// ��Ʈ������ //
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
 * ����Ʈ�ڽ��� disabled �ɼ��߰�
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
 * ���� ǥ�� (3�ڸ����� �޸����)
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

/* �����ȣ �˻� */
function search_zipcode(){
	var form = document.frmOrder;
	var list = _ID('zipcode_list');
	var zipcode, address;

	if(form.dong.value==''){
		form.dong.focus();
	}
	else{

		// ����Ʈ �ʱ�ȭ
		$('#zipcode_list').show();
		$('#zipcode_list ul').remove();

		// �ε������� �߰�
		var indicator = document.createElement('div');
		indicator.className='indicator';
		indicator.style.display='block';
		$('#zipcode_list').append(indicator);

		// ������
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
					alert("�˻� ����� �����ϴ�.");
					$('#zipcode_list').hide();
				}
			},
			error: function(){
				$('#zipcode_list div.indicator').remove();
				alert("�Ͻ����� ������ �߻��Ͽ����ϴ�.\n�ٽ� �õ����ֽñ� �ٶ��ϴ�.");
			}
		});
	}
}

/* �����ȣ ���� */
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

/* �����ȣ �˻� */
function search_zipcode2(){
	var form = document.frmAgree;
	var list = _ID('zipcode_list');
	var zipcode, address;

	if(form.dong.value==''){
		form.dong.focus();
	}
	else{

		// ����Ʈ �ʱ�ȭ
		$('#zipcode_list').show();
		$('#zipcode_list ul').remove();

		// �ε������� �߰�
		var indicator = document.createElement('div');
		indicator.className='indicator';
		indicator.style.display='block';
		$('#zipcode_list').append(indicator);

		// ������
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
					alert("�˻� ����� �����ϴ�.");
					$('#zipcode_list').hide();
				}
			},
			error: function(){
				$('#zipcode_list div.indicator').remove();
				alert("�Ͻ����� ������ �߻��Ͽ����ϴ�.\n�ٽ� �õ����ֽñ� �ٶ��ϴ�.");
			}
		});
	}
}

/* �����ȣ ���� */
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

/*** ���ξ� ��� ***/
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

/*** ���丮�� ���� ���� ***/
function supports_html5_storage() {
  try {
    return 'localStorage' in window && window['localStorage'] !== null;
  } catch (e) {
    return false;
  }
}

/*** ���� ���丮�� ���� ***/
function saveSession(control_key, control_value) {
	if (!supports_html5_storage()) {
		createCookie(control_key, control_value, 7);
	} else {
		sessionStorage[control_key] = control_value;
	}
};

/*** ���� ���丮�� �ε� ***/
function loadSession(control_key) {
	var control_value;
	if (!supports_html5_storage()) {
		control_value = readCookie(control_key);
	} else {
		control_value = sessionStorage[control_key];
	}
	return control_value;
};

/*** ���� ���丮�� ���� ***/
function saveVal(control_key, control_value) {
	if (!supports_html5_storage()) {
		createCookie(control_key, control_value, 7);
	} else {
		localStorage.setItem(control_key, control_value);
	}
};

/*** ���� ���丮�� �ε� ***/
function loadVal(control_key) {
	var control_value;
	if (!supports_html5_storage()) {
		control_value = readCookie(control_key);
	} else {
		control_value = localStorage.getItem(control_key);
	}
	return control_value;
};

/*** ��Ű ���� ***/
function createCookie(name, value, days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
		var expires = "; expires=" + date.toGMTString();
	} else
		var expires = "";
	document.cookie = name + "=" + value  + "; path=/; expires=" + expires + ";";
};

/*** ��Ű ȣ�� ***/
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

/*** ī�װ� ��� ***/
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

/*** ī�װ� �� �Խ��� ��� ***/
function togglenav(self,currentItem){
	if(self.siblings('ul').length > 0){
		if(self.parent('.on').length>0){ // ���� ����(�ڽ�)
			currentItem.find('>ul').hide();
			currentItem.find('>button').removeClass('block');
			currentItem.removeClass('on');
			currentItem.find('>a .icon-plus1').removeClass('open');
		}else{ // ���� ����(�ڽ�+�̿�)
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

/*** ī�װ� �����ͷ� HTML ��� ***/
function makeCateList2(cate_data, menuBox, depth, self) {
	if (menuBox.find('ul').length <= 0) {
		// �з� ����
		var item_html = '<ul class="dep'+(depth+1)+'">';
		for(var i=0; i<cate_data.length; i++) {
			var onoffbtn = '<button type="button" class="btn-reset gnb-arr" onClick="goCate(\'' + cate_data[i].category + '\')"><span class="sprite-icon icon-arr-b-white"></span></button>';
			if (cate_data[i].sub_count > 0) { // ���� �з� �ִ� ���
				item_html += '<li>' + onoffbtn + '<a href="#" onClick="javascript:showCateMenu(this, \'' + cate_data[i].category + '\');" class="sub-icon"><span class="sprite-icon icon-plus1"></span>' + cate_data[i].catnm + '</a></li>';
			}
			else { // ���� �з� ���� ���
				item_html += '<li>' + onoffbtn + '<a href="#" onClick="goCate(\'' + cate_data[i].category + '\')">' + cate_data[i].catnm + '</a></li>'; // ���� �з��� ���� �� a �±׿� ��ư�� ���� �׼��� ��.
			}
		}
		item_html += '</ul>';
		menuBox.append(item_html);
	}
}

// ��ǰ���������� url���� ��ũ��Ʈ
function goodsCopyUrl(){
	var _copyUrl = location.href;
	var copyUrlHtml = '<div id="copyUrlArea"><div id="copyUrlInnerArea"><div id="copyUrlAreaClose" onclick="copyUrlAreaClose()"></div><div style="position:relative;">�ּҸ� ��� ������<br>��ǰ�� URL�� ������ �� �ֽ��ϴ�.</div><br><input type="text"></div></div>';

	$("body").append(copyUrlHtml);
	$("#background").fadeIn().attr({"onclick":""}).click(copyUrlAreaClose);
	$("#copyUrlArea").show();
	$("#copyUrlInnerArea input[type='text']").val(_copyUrl);
}

// ��ǰurl���� ���� ����
function copyUrlAreaClose(){
	$("#background").fadeOut();
	$("#copyUrlArea").hide().remove();
}

window.onmessage = function(event) {
	// �ڵ��� ������ ��Ҹ� �ϸ� �θ��� ���ΰ�ħ
	if (event.data === "reloaded") {
		location.reload();
	}
};

/*
�˾�â�� ���̾�� ����
function frmMake(���� url,���̾� ������ �̸�, �ش� �����ӻ�� Ÿ��Ʋ, �����ɿ���(�������� ��� ȭ�鰡�λ���� device���ΰ� �ƴ� 480���� ���������� �ٸ� ���̾�ʹ� �ٸ��� ȭ�� Ȯ�� ������ �ٸ�, ���� �������϶��� �ƴҶ��� ��Ʈ����� ������ ������, ���̾��˾� ����, ���̾��˾� ����)){
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
������ ���̾��������� ����
function frmMaskRemove(���̾� ������ �̸�){
*/
function frmMaskRemove(frmName){
	$("#frmMask, #frmLayer").remove();
	$("meta[name='viewport']").attr({"content":"user-scalable=yes, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0, width=device-width, height=device-height"});
}