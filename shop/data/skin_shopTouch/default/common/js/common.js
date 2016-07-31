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
	if ( e==8 || e==9 || e==13 || e==37 || e==39) return; // tab, back, ¡ç,¡æ
	event.returnValue = false;
}

/**
 * isChked(El,msg)
 *
 * Ã¼Å©¹Ú½ºÀÇ Ã¼Å© À¯¹« ÆÇº°
 *
 * -msg		null	¹Ù·Î ÁøÇà
 *			msg		confirmÃ¢À» ¶ç¾î ½ÇÇà À¯¹« Ã¼Å© (msg - confirmÃ¢ÀÇ ÁúÀÇ ³»¿ë)
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
		alert ("¼±ÅÃµÈ »çÇ×ÀÌ ¾ø½À´Ï´Ù");
		return false;
	}
}

/**
 * chkBox(El,mode)
 *
 * µ¿ÀÏÇÑ ÀÌ¸§ÀÇ Ã¼Å©¹Ú½ºÀÇ Ã¼Å© »óÈ² ÄÁÆ®·Ñ
 *
 * -mode	true	ÀüÃ¼¼±ÅÃ
 *			false	¼±ÅÃÇØÁ¦
 *			'rev'	¼±ÅÃ¹ÝÀü
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
 * ÀÔ·Â¹Ú½ºÀÇ null À¯¹« Ã¼Å©¿Í ÆÐÅÏ Ã¼Å©
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
		if (currEl.getAttribute("required")!=null){
			if (currEl.type=="checkbox" || currEl.type=="radio"){
				if (!chkSelect(form,currEl,currEl.getAttribute("msgR"))) return false;
			} else {
				if (!chkText(currEl,currEl.value,currEl.getAttribute("msgR"))) return false;
			}
		}

		if (currEl.getAttribute("label")=='ÁÖ¹Îµî·Ï¹øÈ£'  && currEl.getAttribute("name") == 'resno[]' && currEl.value.length>0){
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
			alert("ºñ¹Ð¹øÈ£°¡ ÀÏÄ¡ÇÏÁö ¾Ê½À´Ï´Ù");
			form.password.value = "";
			form.password2.value = "";
			return false;
		}
	}
	if (reschk && !chkResno(form)) return false;

	if ((form.nickname) && (form.nickname != "undefined")){
		if (form.nickname.value.length > 1 && form.chk_nickname.value.length == 0){
			alert("´Ð³×ÀÓ Áßº¹À» Ã¼Å© ÇÏ¼Å¾ß ÇÕ´Ï´Ù");
			return false ;
		}
	}

	if (form.chkSpamKey) form.chkSpamKey.value = 1;
	if (document.getElementById('avoidDbl')) document.getElementById('avoidDbl').innerHTML = "--- µ¥ÀÌÅ¸ ÀÔ·ÂÁßÀÔ´Ï´Ù ---";
	return true;
}

function chkLength(field,len)
{
	text = field.value;
	if (text.trim().length<len){
		alert(len + "ÀÚ ÀÌ»ó ÀÔ·ÂÇÏ¼Å¾ß ÇÕ´Ï´Ù");
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
		if (!msg) msg = "[" + field.getAttribute("label") + "] ÇÊ¼öÀÔ·Â»çÇ×";
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
		var msg2 = "[" + field.getAttribute("label") + "] ÇÊ¼ö¼±ÅÃ»çÇ×";
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
	var regHangul		= /[°¡-ÆR]/;
	var regHangulEng	= /[°¡-ÆRa-zA-Z]/;
	var regHangulOnly	= /^[°¡-ÆR]*$/;
	var regId			= /^[a-zA-Z0-9]{1}[^"']{3,9}$/;
	var regPass			= /^[a-zA-Z0-9_-]{4,12}$/;

	patten = eval(patten);
	if (!patten.test(field.value)){
		var caption = field.parentNode.parentNode.firstChild.innerText;
		if (!field.getAttribute("label")) field.setAttribute("label",(caption)?caption:field.name);
		var msg2 = "[" + field.getAttribute("label") + "] ÀÔ·ÂÇü½Ä¿À·ù";
		if (msg) msg2 += "\n\n" + msg;
		alert(msg2);
		field.focus();
		return false;
	}
	return true;
}

/// ½ºÆ®¸µ °´Ã¼¿¡ ¸Þ¼Òµå Ãß°¡ ///
String.prototype.trim = function(str) {
	str = this != window ? this : str;
	return str.replace(/^\s+/g,'').replace(/\s+$/g,'');
}

// ½ºÆ®¸µ¹öÆÛ //
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
 * ¼¿·ºÆ®¹Ú½º¿¡ disabled ¿É¼ÇÃß°¡
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
 * ¼ýÀÚ Ç¥½Ã (3ÀÚ¸®¸¶´Ù ÄÞ¸¶Âï±â)
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

/* ¿ìÆí¹øÈ£ °Ë»ö */
function search_zipcode(){
	var form = document.frmOrder;
	var list = _ID('zipcode_list');
	var zipcode, address;

	if(form.dong.value==''){
		form.dong.focus();
	}
	else{

		// ¸®½ºÆ® ÃÊ±âÈ­
		$('#zipcode_list').show();
		$('#zipcode_list ul').remove();
		
		// ÀÎµðÄÉÀÌÅÍ Ãß°¡
		var indicator = document.createElement('div');
		indicator.className='indicator';
		indicator.style.display='block';
		$('#zipcode_list').append(indicator);

		// µ¥ÀÌÅÍ
		$.ajax({
			url:'../shopTouch_proc/zipcode_search.php',
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
						address = result.list[i].sido +' '+ result.list[i].gugun  +' '+ result.list[i].dong +' '+ result.list[i].bunji;
						$('#zipcode_list ul').append("<li><a href=\"javascript:zipcode('"+zipcode+"','"+address+"');\">("+ zipcode +") "+ address +"</a></li>");
					}
					$('#zipcode_list ul').slideDown();
				}
				else{
					alert("°Ë»ö °á°ú°¡ ¾ø½À´Ï´Ù.");
					$('#zipcode_list').hide();
				}
			},
			error: function(){
				$('#zipcode_list div.indicator').remove();
				alert("ÀÏ½ÃÀûÀÎ ¿À·ù°¡ ¹ß»ýÇÏ¿´½À´Ï´Ù.\n´Ù½Ã ½ÃµµÇØÁÖ½Ã±â ¹Ù¶ø´Ï´Ù.");
			}
		});
	}
}

/* ¿ìÆí¹øÈ£ ¼±ÅÃ */
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

/*** ÇÒÀÎ¾× °è»ê ***/
function getDcprice(price,dc,po)
{
	if(!po)po=100;
	if (!dc) return 0;
	var ret = (dc.match(/%$/g)) ? price * parseInt(dc.substr(0,dc.length-1)) / 100 : parseInt(dc);
	return parseInt(ret / po) * po;
}