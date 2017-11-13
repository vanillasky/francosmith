function _ID(obj){return document.getElementById(obj)}

function iciScroll(obj)
{
	if (event.wheelDelta >= 120) obj.scrollTop -= 40;
	else if (event.wheelDelta <= -120) obj.scrollTop += 40;
	//obj.scrollBy(0,event.wheelDelta / -3);
	return false;
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
		if (currEl.getAttribute("option")!=null && currEl.value.length>0){
			if (!chkPatten(currEl,currEl.getAttribute("option"),currEl.getAttribute("msgO"))) return false;
		}
		if (currEl.getAttribute("minlength")!=null){
			if (!chkLength(currEl,currEl.getAttribute("minlength"))) return false;
		}
		if (currEl.getAttribute("maxlen")!=null){
			if(!chkMaxLength(currEl,currEl.getAttribute("maxlen"))) return false;
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

	if (form['resno[]'] && !chkResno(form)) return false;
	if (form.chkSpamKey) form.chkSpamKey.value = 1;
	if (document.getElementById('avoidDbl')) document.getElementById('avoidDbl').innerHTML = "--- 데이타 입력중입니다 ---";
	return true;
}

function chkMaxLength(field,len){
	if (chkByte(field.value) > len){
		if (!field.getAttribute("label")) field.setAttribute("label", field.name);
		alert("["+field.getAttribute("label") + "]은 "+ len +"Byte 이하 여야 합니다.");
		return false;
	}
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
	text = text.replace("　", "");
	text = text.replace(/\s*/, "");
	if (text==""){
		var caption = field.parentNode.parentNode.firstChild.innerText;
		if (!field.getAttribute("label")) field.setAttribute("label",(caption)?caption:field.name);
		if (!msg) msg = "[" + field.getAttribute("label") + "] 필수입력사항";
		alert(msg);
		if (field.tagName!="SELECT") field.value = "";
		if (field.type!="hidden") field.focus();
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
		if (!field.getAttribute("label")) field.setAttribute("label", field.name);
		if (!msg) msg = "[" + field.getAttribute("label") + "] 필수선택사항";
		alert(msg);
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
	var regId			= /^[a-zA-Z0-9]{1}[^"']{3,15}$/;
	var regPass			= /^[a-zA-Z0-9_-]{4,12}$/;
	var regPNum			= /^[0-9]*(,[0-9]+)*$/;

	patten = eval(patten);
	if (!patten.test(field.value)){
		if (!field.getAttribute("label")) field.setAttribute("label", field.name);
		if (!msg) msg = "[" + field.getAttribute("label") + "] 입력형식오류";
		alert(msg);
		field.focus();
		return false;
	}
	return true;
}

function formOnly(form){
	var i,idx = 0;
	var rForm = document.getElementsByTagName("form");
	for (i=0;i<rForm.length;i++) if (rForm[i].name==form.name) idx++;
	return (idx==1) ? form : form[0];
}

function chkResno(form)
{
	var resno = form['resno[]'][0].value + form['resno[]'][1].value;

	fmt = /^\d{6}[1234]\d{6}$/;
	if (!fmt.test(resno)) {
		alert('잘못된 주민등록번호입니다.'); return false;
	}

	birthYear = (resno.charAt(6) <= '2') ? '19' : '20';
	birthYear += resno.substr(0, 2);
	birthMonth = resno.substr(2, 2) - 1;
	birthDate = resno.substr(4, 2);
	birth = new Date(birthYear, birthMonth, birthDate);

	if ( birth.getYear()%100 != resno.substr(0, 2) || birth.getMonth() != birthMonth || birth.getDate() != birthDate) {
		alert('잘못된 주민등록번호입니다.');
		return false;
	}

	buf = new Array(13);
	for (i = 0; i < 13; i++) buf[i] = parseInt(resno.charAt(i));

	multipliers = [2,3,4,5,6,7,8,9,2,3,4,5];
	for (i = 0, sum = 0; i < 12; i++) sum += (buf[i] *= multipliers[i]);

	if ((11 - (sum % 11)) % 10 != buf[12]) {
		alert('잘못된 주민등록번호입니다.');
		return false;
	}
	return true;
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
	for (i=0;i<El.length;i++) {
		if (El[i].disabled == true) continue;
		El[i].checked = (mode=='rev') ? !El[i].checked : mode;
	}
}

/**
 * isChked(El,msg)
 *
 * 체크박스의 체크 유무 판별
 *
 * -msg		null	바로 진행
 *			msg		confirm창을 띄어 실행 유무 체크 (msg - confirm창의 질의 내용)
 * @Usage	<input type=checkbox name=chk[]>
 *			<a href="javascript:void(0)" onClick="return isChked(document.getElementsByName('chk[]'),null|msg)">del</a>
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

/**
 * tab(El)
 *
 * textarea 입력 박스에서 tab키로 공백 띄우기 기능 추가
 *
 * @Usage	<textarea onkeydown="return tab(this)"></textarea>
 */

function tab(El)
{
	if ((document.all)&&(event.keyCode==9)){
		El.selection = document.selection.createRange();
		document.all[El.name].selection.text = String.fromCharCode(9)
		document.all[El.name].focus();
		return false;
	}
}

function enter()
{
    if (event.keyCode == 13){
        if (event.shiftKey == false){
            var sel = document.selection.createRange();
            sel.pasteHTML('<br>');
            event.cancelBubble = true;
            event.returnValue = false;
            sel.select();
            return false;
        } else {
            return event.keyCode = 13;
		}
    }
}

/**
 * strip_tags(str)
 *
 * 태그안의 문자만 가져오는 함수
 */

function strip_tags(str)
{
	var reg = /<\/?[^>]+>/gi;
	str = str.replace(reg,"");
	return str;
}

/**
 * miniResize(obj)
 *
 * 이미지 테이블 크기에 맞추어서 리사이즈
 */

function miniResize(obj)
{
	fix_w = obj.clientWidth;
	var imgs = obj.getElementsByTagName("img");
	for (i=0;i<imgs.length;i++){
		//document.write("["+i+"] "+imgs[i].width+" - "+imgs[i].height+"<br>");
		if (imgs[i].width > fix_w){
			imgs[i].width = fix_w;
			imgs[i].style.cursor = "pointer";
			imgs[i].title = "view original size";
			imgs[i].onclick = popupImg;
		}
	}
}

function miniSelfResize(contents,obj)
{
	fix_w = contents.clientWidth;
	if (obj.width > fix_w){
		obj.width = fix_w;
		obj.title = "popup original size Image";
	} else obj.title = "popup original Image";
	obj.style.cursor = "pointer";
	obj.onclick = popupImg;
}

function popupImg(src,base)
{
	if (!src) src = this.src;
	if (!base) base = "";
	window.open(base+'../board/viewImg.php?src='+escape(src),'','width=1,height=1');
}

/**
 * 문자열 Byte 체크 (한글 2byte)
 */
function chkByte(str)
{
	var length = 0;
	for(var i = 0; i < str.length; i++)
	{
		if(escape(str.charAt(i)).length >= 4)
			length += 2;
		else
			if(escape(str.charAt(i)) != "%0D")
				length++;
	}
	return length;
}

/**
 * 문자열 자르기 (한글 2byte)
 */
function strCut(str, max_length)
{
	var str, msg;
	var length = 0;
	var tmp;
	var count = 0;
	length = str.length;

	for (var i = 0; i < length; i++){
		tmp = str.charAt(i);
		if(escape(tmp).length > 4) count += 2;
		else if(escape(tmp) != "%0D") count++;
		if(count > max_length) break;
	}
	return str.substring(0, i);
}

/**
 * etc..
 */

function get_objectTop(obj){
	if (obj.offsetParent == document.body) return obj.offsetTop;
	else return obj.offsetTop + get_objectTop(obj.offsetParent);
}

function get_objectLeft(obj){
	if (obj.offsetParent == document.body) return obj.offsetLeft;
	else return obj.offsetLeft + get_objectLeft(obj.offsetParent);
}

function mv_focus(field,num,target)
{
	len = field.value.length;
	if (len==num && event.keyCode!=8) target.focus();
}

function onlynumber()
{
	var e = event.keyCode;
	window.status = e;
	if (e>=48 && e<=57) return;
	if (e>=96 && e<=105) return;
	if (e>=37 && e<=40) return;
	if (e==8 || e==9 || e==13 || e==46) return;
	event.returnValue = false;
}

function explode(divstr,str)
{
	var temp = str;
	var i;
	temp = temp + divstr;
	i = -1;
	while(1){
		i++;
		this.length = i + 1;
		this[i] = temp.substring(0, temp.indexOf( divstr ) );
		temp = temp.substring(temp.indexOf( divstr ) + 1, temp.length);
		if (temp=="") break;
	}
}

function getCookie( name )
{
	var nameOfCookie = name + "=";
	var x = 0;
	while ( x <= document.cookie.length )
	{
		var y = (x+nameOfCookie.length);
		if ( document.cookie.substring( x, y ) == nameOfCookie ) {
			if ( (endOfCookie=document.cookie.indexOf( ";", y )) == -1 )
				endOfCookie = document.cookie.length;
			return unescape( document.cookie.substring( y, endOfCookie ) );
		}
		x = document.cookie.indexOf( " ", x ) + 1;
		if ( x == 0 )
			break;
	}
	return "";
}

function setCookie( name, value, expires, path, domain, secure ){

	var curCookie = name + "=" + escape( value ) +
		( ( expires ) ? "; expires=" + expires.toGMTString() : "" ) +
		( ( path ) ? "; path=" + path : "" ) +
		( ( domain ) ? "; domain=" + domain : "" ) +
		( ( secure ) ? "; secure" : "" );

	document.cookie = curCookie;
}

String.prototype.trim = function()
{
	return this.replace(/(^\s*)|(\s*$)/g, "");
}

/**
 * chg_cart_ea(obj,str)
 *
 * 카트 수량 변경하기
 *
 * -obj		카드 수량 입력박스 아이디
 * -str		up|dn
 * -idx		인덱스 번호 (생략 가능)
 */
function chg_cart_ea(obj,str,idx)
{
	if (obj.length) obj = obj[idx];

	var step = parseInt(obj.getAttribute('step')) || 1;
	var min = parseInt(obj.getAttribute('min')) || 1;
	var max = parseInt(obj.getAttribute('max')) || 0;

	if (isNaN(obj.value) || obj.value == '') {
		alert ("구매수량은 숫자만 가능합니다");
		obj.value=step;
		obj.focus();
	} else {

		var ea = parseInt(obj.value);

		if (str=='up') {
			ea = ea + step
		}
		else if (str == 'set') {
			// nothing to do.
		}
		else {
			ea = ea - step
		}

		if (ea < min) {
			ea = min;
		}
		else if (max && ea > max) {
			ea = max;
		}

		var remainder = ea % step

		if (remainder > 0) {
			ea = ea - remainder;
		}

		if (ea < 0) ea=step;

		obj.value = ea;

	}
}

function buttonX(str,action,width)
{
	if (!width) width	= 100;
	if (action) action	= " onClick=\"" + action + "\"";
	ret = "<button style='width:" + width + ";background-color:transparent;color:transparent;border:0;cursor:default' onfocus=this.blur()" + action + ">";
	ret += "<table width=" + (width-1) + " cellpadding=0 cellspacing=0>";
	ret += "<tr height=22><td><img src='../img/btn_l.gif'></td>";
	ret += "<td width=100% background='../img/btn_bg.gif' align=center style='font:8pt tahoma' nowrap>" + str + "</td>";
	ret += "<td><img src='../img/btn_r.gif'></td></tr></table></button>";
	document.write(ret);
}

/**
 * selectDisabled(oSelect)
 *
 * 셀렉트박스에 disabled 옵션추가
 */
function selectDisabled(oSelect)
{
	var isOptionDisabled = oSelect.options[oSelect.selectedIndex].disabled;
	if (isOptionDisabled){
		oSelect.selectedIndex = oSelect.preSelIndex;
		return false;
	} else oSelect.preSelIndex = oSelect.selectedIndex;
	return true;
}

/** prototype **/

String.prototype.trim = function(){ return this.replace(/(^\s*)|(\s*$)/g, ""); }

/** 추가 스크립 **/

function viewSub(obj)
{
	var obj = obj.parentNode.childNodes[1].childNodes[0];
	obj.style.display = "block";
}

function hiddenSub(obj)
{
	var obj = obj.parentNode.childNodes[1].childNodes[0];
	obj.style.display = "none";
}

function execSubLayer()
{
	var obj = document.getElementById('menuLayer');
	for (i=0;i<obj.rows.length;i++){
		if (typeof(obj.rows[i].cells[1].childNodes[0])!="undefined"){
			obj.rows[i].cells[0].onmouseover = function(){ viewSub(this) }
			obj.rows[i].cells[0].onmouseout = function(){ hiddenSub(this) }
			obj.rows[i].cells[1].style.position = "relative";
			obj.rows[i].cells[1].style.verticalAlign = "top";
			obj.rows[i].cells[1].childNodes[0].onmouseover = function(){ viewSub(this.parentNode.parentNode.childNodes[0]) };
			obj.rows[i].cells[1].childNodes[0].onmouseout = function(){ hiddenSub(this.parentNode.parentNode.childNodes[0]) };
		}
	}
}

function popup(src,width,height)
{
	window.open(src,'','width='+width+',height='+height+',scrollbars=1,resizable=yes');
}

function popup2(src,width,height,scrollbars)
{
	if ( scrollbars=='' ) scrollbars=1;
	window.open(src,'','width='+width+',height='+height+',scrollbars='+scrollbars);
}

/*-------------------------------------
 공용 - 윈도우 팝업창 호출 / 리턴
-------------------------------------*/
function popup_return( theURL, winName, Width, Height, left, top, scrollbars ){

	if ( !Width ) Width=500;
	if ( !Height ) Height=415;
	if ( !left ) left=200;
	if ( !top ) top=10;
	if ( scrollbars=='' ) scrollbars=0;
	features = "loaction=no, directories=no, Width="+Width+", Height="+Height+", left="+left+", top="+top+", scrollbars="+scrollbars;
	var win = window.open( theURL, winName, features );

	return win;
}

/*** 할인액 계산 ***/
function getDcprice(price,dc)
{
	if (!dc) return 0;
	var ret = (dc.match(/%$/g)) ? price * parseInt(dc.substr(0,dc.length-1)) / 100 : parseInt(dc);
	return parseInt(ret / 100) * 100;
}

/*** 플래시 출력 ***/
function embed(src,width,height)
{
	document.write('\
	<OBJECT classid="clsid:D27CDB6E-AE6D-11cf-96B8-444553540000" codebase="http://download.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=6,0,0,0" WIDTH="'+width+'" HEIGHT="'+height+'"  ALIGN="" name="flashProdnodep">\
	<PARAM NAME=movie VALUE="'+src+'">\
	<PARAM NAME=quality VALUE=high>\
	<PARAM NAME=wmode VALUE=transparent>\
	<PARAM NAME=bgcolor VALUE=#FFFFFF>\
	<EMBED src="'+src+'" quality=high bgcolor=#FFFFFF WIDTH="'+width+'" HEIGHT="'+height+'" NAME="flashProdnodep" ALIGN="" TYPE="application/x-shockwave-flash" PLUGINSPAGE="http://www.macromedia.com/go/getflashplayer"></EMBED>\
	</OBJECT>\
	');
}

/*** 관리자 페이지 관련 스크립트 ***/

function showSub(obj)
{
	var obj = obj.parentNode.getElementsByTagName('ul');
	obj[0].style.display = (obj[0].style.display!="block") ? "block" : "none";
}

function showSubAll(mode)
{
	var obj = _ID('navi');
	el = obj.getElementsByTagName('ul');
	for (i=0;i<el.length;i++) el[i].style.display = mode;
}

function iniLocation()
{
	var div = _ID('location').childNodes[0].nodeValue.split(" > ");
	var navi = document.getElementsByName('navi');

	for (i=0;i<navi.length;i++){
		var spot = navi[i].innerHTML;
		if (spot==div[2]) navi[i].style.fontWeight = "bold";
	}
}

function table_design_load()
{
	var tb = document.getElementsByTagName('table');
	for (i=0;i<tb.length;i++){
		if (tb[i].className=="tb"){
			with (tb[i]){
				setAttribute('border', 1);
				setAttribute('borderColor', "#e6e6e6");
				//setAttribute('rules', 'none');
				setAttribute('cellPadding',5);
				//frame = "hsides";
				//rules = "rows";
				//cellPadding = "4";
			}
			with (tb[i].style){

				width = "100%";
				borderCollapse = "collapse";
			}
		}
	}
}

function hiddenLeft()
{
	if(_ID('leftMenu').style.display!="none"){
		_ID('leftMenu').style.display = "none";
		_ID('btn_menu').style.display = "block";
		_ID('sub_left_menu').style.display = "block";
		document.getElementById('leftfooter').style.background = "url(../img/icon_menuon_bg.gif) repeat-y";

	}else{
		_ID('sub_left_menu').style.display = "none";
		_ID('btn_menu').style.display = "none";
		_ID('leftMenu').style.display = "block";
		document.getElementById('leftfooter').style.background = "url('../img/footer_left.gif') no-repeat";
	}
}

function openLayer(obj,mode)
{
	obj = _ID(obj);
	if (mode) obj.style.display = mode;
	else obj.style.display = (obj.style.display!="none") ? "none" : "block";
}

/*** 레이어 팝업창 띄우기 ***/
function popupLayer(s,w,h)
{
	// 레이어 팝업으로 뜨는 페이지 중 팝업으로 변경된 특정 url만 검색하여 팝업으로 뜨게 변경
	if (s.search('Crm_view.php')>-1 || s.search('popup.coupon.php')>-1 || s.search('orderlist.php')>-1 || s.search('popup.list.php')>-1 || s.search('popup.emoney.php')>-1){
		var popupWin = window.open(s,'CRM','width='+w+',height='+h+',scrollbars=1,resizable=yes');
		if (popupWin) popupWin.focus();
		return;
	}
	
	if (!w) w = 600;
	if (!h) h = 400;

	var pixelBorder = 3;
	var titleHeight = 12;
	w += pixelBorder * 2;
	h += pixelBorder * 2 + titleHeight;

	var bodyW = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
	var bodyH = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;

	var posX = (bodyW - w) / 2;
	var posY = (bodyH - h) / 2;

	hiddenSelectBox('hidden');

	/*** 백그라운드 레이어 ***/
	var obj = document.createElement("div");
	with (obj.style){
		position = "absolute";
		left = 0;
		top = 0;
		width = "100%";
		height = document.body.scrollHeight+'px';

		backgroundColor = "#000000";
		filter = "Alpha(Opacity=80)";
		opacity = "0.5";
	}
	obj.id = "objPopupLayerBg";
	document.body.appendChild(obj);

	/*** 내용프레임 레이어 ***/
	var obj = document.createElement("div");
	with (obj.style){
		position = "absolute";
		left = posX + document.viewport.getScrollOffsets().left +'px';
		top = posY + document.viewport.getScrollOffsets().top +'px';
		width = w;
		height = h;
		backgroundColor = "#ffffff";
		border = "3px solid #000000";
	}
	obj.id = "objPopupLayer";
	document.body.appendChild(obj);

	/*** 타이틀바 레이어 ***/
	var bottom = document.createElement("div");
	with (bottom.style){
		position = "absolute";
		width = w - pixelBorder * 2+'px';

		height = titleHeight +'px';
		left = 0;
		top = h - titleHeight - pixelBorder * 3 +'px';
		padding = "4px 0 0 0";
		textAlign = "center";
		backgroundColor = "#000000";
		color = "#ffffff";
		font = "bold 8pt tahoma; letter-spacing:0px";

	}
	bottom.innerHTML = "<a href='javascript:closeLayer()' class='white'>X close</a>";
	obj.appendChild(bottom);

	/*** 아이프레임 ***/
	var ifrm = document.createElement("iframe");
	with (ifrm.style){
		width = w - 6 +'px';
		height = h - pixelBorder * 2 - titleHeight - 3 +'px';
		//border = "3 solid #000000";
	}
	ifrm.name = 'objPopupIframe';
	ifrm.frameBorder = 0;
	obj.appendChild(ifrm);
	ifrm.src = s;

}
function closeLayer()
{
	hiddenSelectBox('visible');
	_ID('objPopupLayer').parentNode.removeChild( _ID('objPopupLayer') );
	_ID('objPopupLayerBg').parentNode.removeChild( _ID('objPopupLayerBg') );
}
function hiddenSelectBox(mode)
{
	var obj = document.getElementsByTagName('select');
	for (i=0;i<obj.length;i++){
		obj[i].style.visibility = mode;
	}
}

/*-------------------------------------
자바스크립트 동적 로딩
-------------------------------------*/
function exec_script(src)
{
	var scriptEl = document.createElement("script");
	scriptEl.src = src;
	_ID('dynamic').appendChild(scriptEl);
}

/*-------------------------------------
 CSS 라운드 테이블
 ------------------------------------*/
function cssRound(id,color,bg,dept)
{
	if(!dept) dept = "../";
	if (!bg) bg = '#ffffff';
	color = '#93a0a6';
	var obj = _ID(id);
	obj.style.backgroundColor = color;
	with (obj.style){
		margin = "5px 0 0 0";
		color = "#4c4c4c";
		font = "8pt dotum";
	}
	obj.innerHTML = "<div style='padding:8px 13px;'><img src='"+dept+"img/icn_chkpoint.gif'><br>" + obj.innerHTML + "</div>";

	cssRound_top(obj,bg,color);
	cssRound_bottom(obj,bg,color);
}

function cssRound_top(el,bg,color)
{
	var d=document.createElement("b");
	d.className="rOut";
	d.style.fontSize = 0;
	d.style.backgroundColor=bg;
	for(i=1;i<=4;i++){
		var x=document.createElement("b");
		x.className="r" + i;
		x.style.backgroundColor=color;
		d.appendChild(x);
	}
	el.style.paddingTop=0;
	el.insertBefore(d,el.firstChild);
}

function cssRound_bottom(el,bg,color){
	var d=document.createElement("b");
	d.className="rOut";
	d.style.fontSize = 0;
	d.style.backgroundColor=bg;
	for(i=4;i>0;i--){
		var x=document.createElement("b");
		x.className="r" + i;
		x.style.backgroundColor=color;
		d.appendChild(x);
	}
	el.style.paddingBottom=0;
	el.appendChild(d);
}

/*-------------------------------------
 색상표 보기
-------------------------------------*/
function colortable(){

	var hrefStr = '../proc/help_colortable.php';

	var win = popup_return( hrefStr, 'colortable', 400, 400, 200, 200, 0 );
	win.focus();
}

/*-------------------------------------
 WebFTP
-------------------------------------*/
function webftp(){

	var hrefStr = '../design/popup.webftp.php';

	var win = popup_return( hrefStr, 'webftp', 900, 800, 50, 50, 1 );
	win.focus();
}

/*-------------------------------------
 WebFTP Fileinfo
-------------------------------------*/
function webftpinfo( file_root ){

	if ( file_root == '' ){
		alert( '업로드된 이미지가 없습니다.' );
		return;
	}

	var hrefStr = '../design/webftp/webftp_info.php?file_root=' + file_root;

	var win = popup_return( hrefStr, '', 190, 300, 50, 50, 0 );
	win.focus();
}

/*-------------------------------------
 Stylesheet
-------------------------------------*/
function stylesheet(){

	var hrefStr = '../design/iframe.css.php';

	var win = popup_return( hrefStr, 'stylesheet', 900, 650, 100, 100, 1 );
	win.focus();
}

/*-------------------------------------
 manual
-------------------------------------*/
function manual(src){
	var win = window.open(src,'manual','width=1050,height=800,scrollbars=1');
	win.focus();
}

/*-------------------------------------
 공용 - 체크박스 체크
 ckFlag : select, reflect, deselect
 CObj : checkbox object
-------------------------------------*/
function PubAllSordes( ckFlag, CObj ){

	if ( !CObj ) return;
	var ckN = CObj.length;

	if ( ckN != null ){

		if ( ckFlag == "select" ){
			for ( jumpchk = 0; jumpchk < ckN; jumpchk++ ) CObj[jumpchk].checked = true;
		}
		else if ( ckFlag=="reflect" ){
			for ( jumpchk = 0; jumpchk < ckN; jumpchk++ ){
				if ( CObj[jumpchk].checked == false ) CObj[jumpchk].checked = true; else CObj[jumpchk].checked = false;
			}
		}
		else{
			for ( jumpchk = 0; jumpchk < ckN; jumpchk++ ) CObj[jumpchk].checked = false;
		}
	}
	else {

		if ( ckFlag == "select" ) CObj.checked = true;
		else if ( ckFlag == "reflect" ){
			if ( CObj.checked == false ) CObj.checked = true; else CObj.checked = false;
		}
		else CObj.checked = false;
	}
}

/*-------------------------------------
 공용 - 체크박스 한개이상 체크여부
 CObj : checkbox object
-------------------------------------*/
function PubChkSelect( CObj ){

	if ( !CObj ) return;
	var ckN = CObj.length;

	if ( ckN != null ){

		var sett = 0;
		for ( jumpchk = 0; jumpchk < ckN; jumpchk++ ){
			if ( CObj[jumpchk].checked == false ) sett++;
		}

		if ( sett == ckN ) return false;
		else return true;
	}
	else{

		if ( CObj.checked == true ) return true;
		else return false;
	}
}

function setDate(obj,from,to)
{
	var obj = document.getElementsByName(obj);
	obj[0].value = (from) ? from : "";
	obj[1].value = (from) ? to : "";
}

/**********************
 * categoryBox
 *
 * @name	category 폼객체명
 * @idx		category 박스 갯수
 */

function categoryBox(name,idx,val,type,formnm)
{
	if (!idx) idx = 1;
	if (type=="multiple") type = "multiple style='width:160px;height:96px'";
	if (type=="naver") type = "multiple style='width:325px;height:160px'";
	for (i=0;i<idx;i++) document.write("<select " + type + " idx=" + i + " name='" + name + "' onchange='categoryBox_request(this)' class='select'></select>");

	oForm = eval("document.forms['" + formnm + "']");

	if ( oForm == null ) this.oCate = eval("document.forms[0]['" + name + "']");
	else{ this.oCate = eval("document." + oForm.name + "['" + name + "']"); }

	if (idx==1) this.oCate = new Array(this.oCate);

	this.categoryBox_init = categoryBox_init;
	this.categoryBox_build = categoryBox_build;
	this.categoryBox_init();

	function categoryBox_init()
	{
		this.categoryBox_build();
		categoryBox_request(this.oCate[0],val);
	}

	function categoryBox_build()
	{
		for (i=0;i<4;i++){
			if (this.oCate[i]){
				this.oCate[i].options[0] = new Option("= "+(i+1)+"차 분류 =","");
			}
		}
	}

}

function categoryBox_request(obj,val)
{
	if (!val) val = "";
	var idx = obj.getAttribute('idx');

	if ( document.location.href.indexOf("/admin") == -1 ){
		exec_script("../lib/_categoryBox.script.php?mode=user&idx=" + idx + "&obj=" + obj.name + "&formnm=" + obj.form.name + "&val=" + val + "&category=" + obj.value);
	}
	else if (document.location.href.indexOf("/naver/partner") > 0) {
		exec_script("../../lib/_categoryBox.script.php?mode=naver&idx=" + idx + "&obj=" + obj.name + "&formnm=" + obj.form.name + "&val=" + val + "&category=" + obj.value);
	}
	else {
		exec_script("../../lib/_categoryBox.script.php?mode=admin&idx=" + idx + "&obj=" + obj.name + "&formnm=" + obj.form.name + "&val=" + val + "&category=" + obj.value);
	}
}

/**
 * Calendar Script
 * @usage	<input type=text onclick="calendar(event)">
 */

var now			= new Date();
var static_now	= new Date();
var week		= new Array("SUN","MON","TUE","WED","THU","FRI","SAT");
var weekNum		= new Array(1,2,3,4,5,6,7);

var tagNm		= "";
var thisObj		= "";
var eventElement= "";
var dy_calOpen	= "n";

function calendar(e,gubun)
{
	if(!gubun){
		gubun = '';
	}
	var event = e || window.event;
	if( !appname ){
		var appname = navigator.appName.charAt(0);
	}

	if( appname == "M" ){
		eventElement = event.srcElement;
		tagNm = eventElement.tagName;
	}else{
		eventElement = event.target;
		tagNm = eventElement.tagName;
	}

	var dy_x = event.clientX+(document.body.scrollLeft || document.documentElement.scrollLeft);
	var dy_y = event.clientY+(document.body.scrollTop || document.documentElement.scrollTop);

	// target element's position;
	try {
		var pos = eventElement.positionedOffset();
		dy_x = pos.left;
		dy_y = pos.top + eventElement.getHeight();
	} catch (e) {}

	if( dy_calOpen == 'n' ){
		var NewElement = document.createElement("div");
		with (NewElement.style){
			position	= "absolute";
			left		= dy_x + 'px';
			top			= dy_y + 'px';
			width		= "205px";
			Height		= "170px";
			background	= "#ffffff";
			border		= "0px";
			zIndex		= "10000";
		}
		NewElement.id = "Dynamic_CalendarID";
		document.body.appendChild(NewElement);
		thisObj = NewElement;
		dy_calOpen = 'y';
	}else{
		thisObj.style.left	= dy_x + 'px';
		thisObj.style.top	= dy_y + 'px';
	}

	//달력 출력하기!!
	var calCont = calendarSet('',gubun);
}

function calendarSet(val,gubun){

	var now_date	= new Date();

	var p;
	var z=0;

	switch(val){
		case 1:now.setFullYear(now.getFullYear()-1);break;
		case 2:now.setMonth(now.getMonth()-1);break;
		case 3:now.setMonth(now.getMonth()+1);break;
		case 4:now.setFullYear(now.getFullYear()+1);break;
		case 5:now=now_date;break;
	}

	var NowYear = now.getFullYear();
	var NowMonth = now.getMonth();
	var m_infoDate = NowYear+'/'+NowMonth;

	last_date = new Date(now.getFullYear(),now.getMonth()+1,1-1);	//해당월 마지막 일자
	first_date= new Date(now.getFullYear(),now.getMonth(),1);		//해당월 처음일자 요일

	var now_scY = now.getFullYear()+"";
	var calendar_area = "<table border=\"0\" cellpadding=\"0\" cellspacing=\"0\" style=\"border:4px #ffffff solid;\"><tr><td><table width=\"245\" border=\"0\" cellpadding=\"0\" cellspacing=\"1\" bgcolor=\"ffffff\" style=\"border:6px #78b300 solid;\"><tr height=\"26\" bgcolor=\"ffffff\" align=\"center\"><td style=\"padding-top:3px; padding-left:10px; \"> \n";
	calendar_area += "<div class=\"calendarTitleY\">";
	calendar_area += "<span onclick=\"calendarSet(1,'"+gubun+"')\" style='cursor:pointer;'>◀ </span>";
	calendar_area += now_scY;
	calendar_area += "<span onclick=\"calendarSet(4,'"+gubun+"')\" style='cursor:pointer;'> ▶</span>";
	calendar_area += "</div> \n";
	calendar_area += "<div class=\"calendarTitleM\">";
	calendar_area += "<span onclick=\"calendarSet(2,'"+gubun+"')\" style='cursor:pointer;'>◀ </span>";
	calendar_area += (now.getMonth()+1) +"";
	calendar_area += "<span onclick=\"calendarSet(3,'"+gubun+"')\" style='cursor:pointer;'> ▶</span>";
	calendar_area += "</div> \n";
	for(i=0;i<week.length;i++){
		if( weekNum[i] == 1 ) {
			calendar_area += "<div class=\"calendarWeekS\">"+week[i]+"</div> \n";
		} else if( weekNum[i] == 7 ) {
			calendar_area += "<div class=\"calendarWeekT\">"+week[i]+"</div> \n";
		} else {
			calendar_area += "<div class=\"calendarWeek\">"+week[i]+"</div> \n";
		}
	}

	calendar_area +="<div class=\"clearboth\"></div> \n";

	for(i=1;i<=first_date.getDay();i++){
		calendar_area+="<div class=\"calendarNoDay\">&nbsp;</div> \n";
	}

	z=(i-1);
	var clickDay;
	var weekCnt = 1;

	for (i=1;i<=last_date.getDate();i++){
		z++;
		p=z%7;
		var pmonth=now.getMonth()+1;
		if(i<10){var ii="0"+i;}else{var ii=i;}
		if(pmonth<10){pmonth="0"+pmonth;}

		clickDay = now.getFullYear() + gubun + pmonth + gubun + ii;

		// 날짜 출력
		if(i == now.getDate() && now.getFullYear()==static_now.getFullYear() && now.getMonth()==static_now.getMonth()){
			calendar_area += "<div class=\"calendarToDay\" onclick=\"calendarPrint('"+clickDay+"');\">"+ii+"</div> \n";
		}else if( p == 0 ){	//토요일
			calendar_area += "<div class=\"calendarDayT\" onclick=\"calendarPrint('"+clickDay+"');\">"+ii+"</div> \n";
		}else if( p == 1 ){	//일요일
			calendar_area += "<div class=\"calendarDayS\" onclick=\"calendarPrint('"+clickDay+"');\">"+ii+"</div> \n";
		}else{				//평일
			calendar_area += "<div class=\"calendarDay\" onclick=\"calendarPrint('"+clickDay+"');\">"+ii+"</div> \n";
		}
		if(p==0 && last_date.getDate() != i){
			calendar_area +="<div class=\"clearboth\"></div> \n";
			weekCnt++;
		}
	}

	if(p !=0){
		for(i=p;i<7;i++){
			calendar_area+="<div class=\"calendarNoDay\">&nbsp;</div> \n";
		}
	}

	var addtable1;
	var addtable2;
	if( weekCnt != 6){
		for(addtable1=weekCnt; addtable1 < 6; addtable1++){
			calendar_area +="<div class=\"clearboth\"></div> \n";
			for(addtable2=0; addtable2 < 7; addtable2++){
				calendar_area+="<div class=\"calendarNoDay\">&nbsp;</div> \n";
			}
		}
	}

	var nowDate	= now_date.getFullYear() + "-" + (100+( now_date.getMonth() + 1)).toString(10).substr(1) + "-" + (100+now_date.getDate()).toString(10).substr(1);

	calendar_area += "<div class=\"clearboth\"></div> \n";
	calendar_area += "<div class=\"calendarNow\" onclick=\"calendarSet(5,'"+gubun+"')\" align=\"left\">Today : "+nowDate+" </div> \n";
	calendar_area += "<div class=\"calendarClose\" onclick=\"calendarClose();\" align=\"right\"><font class=ver8><b>X</b></font></div> \n";
	calendar_area += "</td></tr></table></td></tr></table> \n";

	thisObj.innerHTML = calendar_area;

}

function calendarClose()
{
	dy_calOpen = 'n';
	thisObj.parentNode.removeChild(thisObj);
}

function calendarPrint(date)
{
	if( tagNm == "INPUT" ) eventElement.value = date;
	else eventElement.innerHTML = date;
	calendarClose();
}

function calendar_get_objectTop(obj){
	if (obj.offsetParent == document.body) return obj.offsetTop;
	else return obj.offsetTop + get_objectTop(obj.offsetParent);
}

function calendar_get_objectLeft(obj){
	if (obj.offsetParent == document.body) return obj.offsetLeft;
	else return obj.offsetLeft + get_objectLeft(obj.offsetParent);
}

/*** onLoad 이벤트에 함수 할당 ***/
function addOnloadEvent(fnc)
{
	if ( typeof window.addEventListener != "undefined" )
		window.addEventListener( "load", fnc, false );
	else if ( typeof window.attachEvent != "undefined" ) {
		window.attachEvent( "onload", fnc );
	}
	else {
		if ( window.onload != null ) {
			var oldOnload = window.onload;
			window.onload = function ( e ) {
				oldOnload( e );
				window[fnc]();
			};
		}
		else window.onload = fnc;
	}
}

function order_print(frmp_nm, frml_nm)
{
	var frmp = document.forms[frmp_nm];
	var frml = document.forms[frml_nm];
	if ( frmp['list_type'][0].checked != true && frmp['list_type'][1].checked != true ) return;

	if ( frmp['list_type'][0].checked == true && frmp['list_type'][0].value == 'list' ){
		if ( PubChkSelect( frml['chk[]'] ) == false ){
			alert( "선택한 내역이 없습니다." );
			return;
		}

		var cds = new Array();
		var idx = 0;
		var count=frml['chk[]'].length;

		if ( count == undefined ){
			if ( frml['chk[]'].getAttribute("ordno") != null ) cds[ idx++ ] = frml['chk[]'].getAttribute("ordno");
			else cds[ idx++ ] = frml['chk[]'].value;
		}
		else
			for ( i = 0; i < count ; i++ )
				if ( frml['chk[]'][i].checked )
					if ( frml['chk[]'][i].getAttribute("ordno") != null ) cds[ idx++ ] = frml['chk[]'][i].getAttribute("ordno");
					else cds[ idx++ ] = frml['chk[]'][i].value;

		frmp['ordnos'].value = cds.join( ";" );
	}

	var orderPrint = window.open("","orderPrint","width=750,height=600,menubar=yes,scrollbars=yes" );
	frmp.target='orderPrint';
	frmp.action='../order/_paper.php';
	frmp.submit();
	orderPrint.focus();
}

/* 브라우저별 이벤트 처리*/
function addEvent(obj, evType, fn){
	if (obj.addEventListener) {
		obj.addEventListener(evType, fn, false);
		return true;
	} else if (obj.attachEvent) {
		var r = obj.attachEvent("on"+evType, fn);
		return r;
	} else {
		return false;
	}
}

function delEvent(obj, evType, fn){
	if (obj.removeEventListener) {
		obj.removeEventListener(evType, fn, false);
		return true;
	} else if (obj.detachEvent) {
		var r = obj.detachEvent("on"+evType, fn);
		return r;
	} else {
		return false;
	}
}

function getTargetElement(evt)
{
	if ( evt.srcElement ) return target_Element = evt.srcElement; // 익스
	else return target_Element = evt.target; // 익스외
}

function popupEgg(asMallId, asOrderId){
	//창을 화면의 중앙에 위치
	iXPos = (window.screen.width - 700) / 2;
	iYPos = (window.screen.height - 600) / 2;
	var egg = window.open("https://gateway.usafe.co.kr/esafe/InsuranceView.asp?mall_id="+asMallId + "&order_id=" + asOrderId, "egg", "width=700, height=600, scrollbars=yes, left=" + iXPos + ", top=" + iYPos);
	egg.focus();
}

function inArray( needle, haystack )
{
	for ( i = 0; i < haystack.length; i++ )
		if ( haystack[i] == needle ) return true;
	return false;
}

/*** AJAX GRAPH METHOD (AGM) ***/
AGM = {
	bMsg : new Array(),
	iobj : new Array(),
	articles: new Array(),
	running: new Array(),
	interverID: '',

	act: function (c)
	{
		if (c && typeof(c.onStart) == 'function'){
			this.func = c;
			this.func.onStart(this);
			this.start();
		}
		else return;
	},

	start: function ()
	{
		this.running = new Array();
		this.clearinterverid();

		this.layout = "\
		<div id=report>\
			<h1>{title}</h1>\
			<table><tr><th>전송상태</th><td><div id=briefing><ul><li>브리핑 메시지 샘플.</li></ul></div></td></tr></table>\
			<h2 id=report_step>준비중..</h2>\
			<div id=report_line><div id=report_white><div id=report_graph></div></div></div>\
			<p><!--점선--></p>\
			<div id=report_btn><a href='javascript:;'><img src='../img/btn_confirm_s.gif' alt=닫기></a></div>\
		</div>\
		";
		this.layout = this.layout.replace(/{title}/,this.layoutTitle);
		popupLayer('',550,300);
		document.getElementById('objPopupLayer').innerHTML = this.layout;
		document.getElementById('report_graph').style.width = "0%";

		if (this.articles.length < 1){
			this.briefing(this.bMsg['chkEmpty'], true);
			this.closeBtn();
			return;
		}

		this.briefing(this.bMsg['chkCount'].replace(/__count__/, this.articles.length), true);
		this.briefing(this.bMsg['start']);
		this.request();
	},

	request: function ()
	{
		if (this.running.length < this.articles.length) // 전송중
		{
			var idx = this.articles[ this.running.length ];
			var tmp = new Array(); tmp.push(idx);
			this.running.push(tmp);
			document.getElementById('report_step').innerHTML = '[' + this.iobj[0][idx].getAttribute('subject') + '] 내역 처리중';
			this.func.onRequest(this, idx);
			this.setIntervalId("AGM.graph()", 500);
		}
		else if (this.running.length == this.articles.length){ // 전송완료
			this.clearinterverid();
			this.done();
		}
	},

	complete: function (req)
	{
		this.running[(this.running.length - 1)].push(true);
		var idx = this.running[(this.running.length - 1)][0];
		var subObj = this.iobj[0][idx];
		var response = req.responseText.replace(/{subject}/, subObj.getAttribute('subject'));
		this.briefing(response);
		this.setIntervalId("AGM.graph('continue')", 30);
	},

	error: function (req)
	{
		this.running[(this.running.length - 1)].push(false);
		var idx = this.running[(this.running.length - 1)][0];
		var subObj = this.iobj[0][idx];
		var msg = req.getResponseHeader("Status").replace(/{subject}/, subObj.getAttribute('subject'));
		if (msg == null || msg.length == null || msg.length <= 0)
		{
			this.briefing("Error! Request status is " + req.status);
			this.setIntervalId("AGM.graph('continue')", 30);
		}
		else
		{
			var remsg = '';
			var tmp = msg.split("^");
			for (i = 0; i < tmp.length; i++)
			{
				if (i == 1) remsg += '<ol type="1" style="margin-bottom:10px;">';
				if (i == 0) remsg += tmp[i];
				else remsg += '<li>' + tmp[i] + '</li>';
				if (i > 0 && (i+1) == tmp.length) remsg += '</ol>';
			}
			this.briefing(remsg, false, 'red');

			if (req.status == 600) this.done();
			else this.setIntervalId("AGM.graph('continue')", 30);
		}
	},

	done: function ()
	{
		this.briefing(this.bMsg['end']);
		document.getElementById('report_step').innerHTML = '완료';
		this.closeBtn();
		this.clearinterverid();
	},

	closeBtn: function ()
	{
		var btnDiv = document.getElementById('report_btn');
		btnDiv.childNodes[0].href = "javascript:closeLayer();";
		btnDiv.style.display = "block";
		if (this.func && typeof(this.func.onCloseBtn) == 'function') this.func.onCloseBtn(this, btnDiv);
	},

	setIntervalId: function (func, interval)
	{
		this.clearinterverid();
		this.interverID = setInterval(func.toString(), interval);
	},

	clearinterverid: function ()
	{
		clearInterval(this.interverID);
		this.interverID = '';
	},

	briefing: function (str, emtpy, color)
	{
		var briefing = document.getElementById('briefing').childNodes[(document.getElementById('briefing').childNodes[0].nodeType == 1 ? 0 : 1)];
		if (emtpy == true) while (briefing.childNodes.length > 0) briefing.removeChild(briefing.lastChild);
		var liNode = document.createElement('LI');
		briefing.appendChild(liNode);
		liNode.innerHTML = str;
		if (color != '') liNode.style.color = color;
	},

	graph: function (code)
	{
		var limitPercent = eval(this.running.length) / eval(this.articles.length) * 100;
		var nowPercent = eval(document.getElementById('report_graph').style.width.replace( /%/, ''));
		if (limitPercent > nowPercent) document.getElementById('report_graph').style.width = ++nowPercent + '%';
		else if (code == 'continue') this.request();
	}
}

/**
 * extComma(x), extUncomma(x)
 *
 * 숫자 표시 (3자리마다 콤마찍기, 마이너스 및 소수점 유지)
 *
 * @Usage	var money = -1000.12;
 *			money = extComma(money);
 *			alert(money);	// -1,000.12
 *			alert(extUncomma(money));	// -1000.12
 */
function extComma(x){
	var head = '', tail = '', minus = '';
	if (x < 0){
		minus = '-';
		x = x * (-1) + "";
	}
    if ( x.indexOf(".") >= 0 ) {
        head = comma(x.substring ( 0 , x.indexOf(".") ));
        tail = uncomma(x.substring ( x.indexOf(".") + 1, x.length ));
    }
    else head = comma(x);
	x = minus + head;
    if ( tail.toString().length > 0 ) x += "." + tail;
	return x;
}

function extUncomma(x){
	var head = '', tail = '', minus = '';
	if (x < 0){
		minus = '-';
		x = x * (-1) + "";
	}
    if ( x.indexOf(".") >= 0 ) {
        head = uncomma(x.substring ( 0 , x.indexOf(".") ));
        tail = uncomma(x.substring ( x.indexOf(".") + 1, x.length ));
    }
    else head = uncomma(x);
	x = minus + head;
    if ( tail.toString().length > 0 ) x += "." + tail;
	return x;
}

/*** UI NAVIGATION METHOD (UNM) ***/
UNM = {
	m_no: "",
	m_id: "",
	isOver: false,
	agoMenuNo: "",
	overBgcolor: "#e4ff75",
	outBgcolor: "#ffffff",
	popup: "",

	inner: function ()
	{
		document.onclick = this.mouseDown;
		var navigs = document.getElementsByName('navig');

		for (no = 0; no < navigs.length; no++)
		{
			navigs[no].style.position = "relative";
			content = navigs[no].innerHTML;
			navigs[no].innerHTML = '';
			navigs[no].style.zIndex = 0;

			var va = navigs[no].appendChild(document.createElement('A'));
			va.href = "javascript:UNM.callMenu('" + navigs[no].getAttribute('m_no') + "', '" + navigs[no].getAttribute('m_id') + "', '" + navigs[no].getAttribute('popup') + "');";
			va['onmouseover'] = function(e) { UNM.evtHandler(); };
			va.innerHTML = content;
			va.setAttribute('no', no);

			var vDiv = navigs[no].insertBefore(document.createElement('DIV'), navigs[no].childNodes[0]);
			vDiv.setAttribute('id', 'menuarent' + no);
			with (vDiv.style){
				position = 'absolute';
				display = 'none';
				top = -3;
				left = -132;
			}

			var menu = '';
				menu += '<table width="127" border="0" cellspacing="0" cellpadding="0" bgcolor="#FFFFFF" style="border:solid 3px #5f5f5f">';
				menu += '<tr> ';
				menu += '<td style="padding:3px 0 0 10px" height="23" onmouseout="UNM.menuOut(this);" onmouseover="UNM.menuOver(event, this);" onclick="UNM.exec(\'EVENT1\');" class=small1><font color=808080>&#149;</font>&nbsp;<font color=404040>CRM (고객관리) 보기</td>';
			    menu += '</tr><tr> ';
				menu += '<td height=1 bgcolor=ebebeb></td>';
				menu += '</tr><tr> ';
				menu += '<td style="padding:2px 0 0 10px" height="22" onmouseout="UNM.menuOut(this);" onmouseover="UNM.menuOver(event, this);" onclick="UNM.exec(\'EVENT2\');" class=small1><font color=808080>&#149;</font>&nbsp;<font color=404040>SMS 보내기</td>';
				menu += '</tr><tr> ';
				menu += '<td height=1 bgcolor=ebebeb></td>';
				menu += '</tr><tr> ';
				menu += '<td style="padding:2px 0 0 10px" height="22" onmouseout="UNM.menuOut(this);" onmouseover="UNM.menuOver(event, this);" onclick="UNM.exec(\'EVENT3\');" class=small1><font color=808080>&#149;</font>&nbsp;<font color=404040>메일 보내기</td>';
				menu += '</tr><tr> ';
				menu += '<td height=1 bgcolor=ebebeb></td>';
				menu += '</tr><tr> ';
				menu += '<td style="padding:2px 0 0 10px" height="22" onmouseout="UNM.menuOut(this);" onmouseover="UNM.menuOver(event, this);" onclick="UNM.exec(\'EVENT4\');" class=small1><font color=808080>&#149;</font>&nbsp;<font color=404040>주문내역 보기</td>';
				menu += '</tr><tr> ';
				menu += '<td height=1 bgcolor=ebebeb></td>';
				menu += '</tr><tr> ';
				menu += '<td style="padding:2px 0 0 10px" height="22" onmouseout="UNM.menuOut(this);" onmouseover="UNM.menuOver(event, this);" onclick="UNM.exec(\'EVENT5\');" class=small1><font color=808080>&#149;</font>&nbsp;<font color=404040>적립금내역 보기</td>';
				menu += '</tr></table>';
			vDiv.innerHTML = menu;
		}
	},

	callMenu: function (m_no, m_id, popup)
	{
		this.m_no = m_no;
		this.m_id = m_id;
		this.popup = popup;
	},

	evtHandler: function ()
	{
		if (window.navigator.appName.indexOf("Explorer") == -1) return;
		document.onclick = this.mouseDown;
	},

	mouseDown: function (e)
	{
		var event = e || window.event;
		var evtTarget = event.target || event.srcElement;
		if (evtTarget.toString().indexOf("javascript:UNM.callMenu(") && evtTarget.parentNode != null) evtTarget = evtTarget.parentNode;
		if (evtTarget.toString().indexOf("javascript:UNM.callMenu(") && evtTarget.parentNode != null) evtTarget = evtTarget.parentNode;

		if (!UNM.isOver) UNM.hideAll();
		if (!evtTarget.toString().indexOf("javascript:UNM.callMenu(") && evtTarget.getAttribute('no') != null){
			UNM.agoMenuNo = evtTarget.getAttribute('no');
			_ID('menuarent' + evtTarget.getAttribute('no')).style.display = 'block';
			_ID('menuarent' + evtTarget.getAttribute('no')).parentNode.style.zIndex = document.getElementsByName('navig').length;
		}
		else return;
	},

	menuOver: function (e, obj)
	{
		var event = e || window.event;
		this.isOver = true;
		this.chgBgcolor(obj);
	},

	menuOut: function (obj)
	{
		this.isOver = false;
		this.chgBgcolor(obj);
	},

	chgBgcolor: function (obj)
	{
		if (typeof obj.painted == 'undefined') obj.painted = false;
		obj.style.backgroundColor = obj.painted?this.outBgcolor:this.overBgcolor;
		obj.painted = !obj.painted;
	},

	hideAll: function ()
	{
		try {
			document.getElementById("menuarent" + this.agoMenuNo).style.display = 'none';
			document.getElementById("menuarent" + this.agoMenuNo).parentNode.style.zIndex = 0;
		} catch(e) { return; }
	},

	exec: function (key)
	{
		this.hideAll();
		switch(key) {
		case "EVENT1" :
			(this.popup == '1' ? popup :popupLayer)('../member/Crm_view.php?m_id=' + this.m_id,780,600);
		break;
		case "EVENT2" :
			(this.popup == '1' ? popup :popupLayer)('../member/popup.sms.php?m_id=' + this.m_id,780,600);
		break;
		case "EVENT3" :
			(this.popup == '1' ? popup :popupLayer)('../member/email.php?type=direct&m_id=' + this.m_id,780,600);
		break;
		case "EVENT4" :
			(this.popup == '1' ? popup :popupLayer)('../member/orderlist.php?m_no=' + this.m_no,500,600);
		break;
		case "EVENT5" :
			(this.popup == '1' ? popup :popupLayer)('../member/popup.emoney.php?m_no=' + this.m_no,600,500);
		break;
		}
	}
}

function panel(idnm, section)
{
	if (document.getElementById(idnm) == null) return;
	var ajax = new Ajax.Request( "../proc/indb.php",
	{
		method: "post",
		parameters: "mode=getPanel&idnm=" + idnm + "&section=" + section,
		onComplete: function ()
		{
			var req = ajax.transport;
			if (req.status != 200) return;
			if (req.responseText =='') return;
			var obj = document.getElementById(idnm);
			if (idnm == 'paneltop')
			{
				obj.parentNode.style.textAlign = 'right';
				obj.parentNode.style.width = 808;
			}
			obj.innerHTML = req.responseText;
			if(idnm == 'maxlicense'){
				popupLayerAgree(idnm,530,430);
			}
			if(idnm == 'maxagree'){
				window.onload=function(){popupLayerAgree(idnm,530,430);}
			}
			setHeight_ifrmCodi();
		}
	} );
}

/*** license ***/
function popupLayerAgree(s,w,h)
{
	if (!w) w = 600;
	if (!h) h = 400;

	var pixelBorder = 3;
	var titleHeight = 12;
	w += pixelBorder * 2;
	h += pixelBorder * 2 + titleHeight;

	var bodyW = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
	var bodyH = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;

	var posX = (bodyW - w) / 2;
	var posY = (bodyH - h) / 2;

	hiddenSelectBox('hidden');

	/*** 백그라운드 레이어***/
	var obj = document.createElement("div");
	with (obj.style){
		position = "absolute";
		left = 0;
		top = 0;
		width = "100%";
		height = document.body.scrollHeight + 'px';
		backgroundColor = "#000000";
		filter = "Alpha(Opacity=70)";
		opacity = "0.5";
	}
	obj.id = "objPopupLayerBg";
	document.body.appendChild(obj);

	/*** 내용프레임 레이어 ***/
	var obj = document.createElement("div");
	with (obj.style){
		position = "absolute";
		left = posX + document.viewport.getScrollOffsets().left +'px';
		top = posY + document.viewport.getScrollOffsets().top +'px';
		width = w + 'px';
		height = h + 'px';
	}
	obj.id = "objPopupLayer";
	obj.innerHTML = document.getElementById(s).innerHTML;
	document.body.appendChild(obj);
}

function inFocus1(i) {
	(i).style.border='2px solid #627dce';
}

function outFocus1(i) {
	(i).style.border='1px solid #cccccc';
}

function LogininFocus(i) {
	(i).style.border='3px solid #00a8ff';
	(i).style.height='22px';
}

function LoginoutFocus(i) {
	(i).style.border='1px solid #242425';
	(i).style.height='22px';
}

/*** 포커스 테두리 넣기 ***/
function linecss(){
	var obj = document.getElementsByTagName('input');
	var obj_txa = document.getElementsByTagName('textarea');
	for( e =0; e < obj.length; e++ ){
		var type = obj[e].getAttribute('type');
		if( type == 'text' || type == 'password' || type == 'file'){
			var isClsnm = false;
			var clsnm = obj[e].className.toString().split(' ');
			for (c = 0; c < clsnm.length; c++){
				if (inArray(clsnm[c], Array('lline', 'line', 'rline', 'cline', 'loginline'))) isClsnm = true;
			}
			if (isClsnm === true){
				addEvent(obj[e], 'focus', function(e) { inFocus1(getTargetElement(e)); });
				addEvent(obj[e], 'blur', function(e) { outFocus1(getTargetElement(e)); });
			}
		}
	}

	for( t =0; t < obj_txa.length; t++ ){
		var clsnm = obj_txa[t].className.toString().split(' ');
		if (inArray("tline", clsnm)){
			addEvent(obj_txa[t], 'focus', function(e) { inFocus1(getTargetElement(e)); });
			addEvent(obj_txa[t], 'blur', function(e) { outFocus1(getTargetElement(e)); });
		}
	}
}

/*** 디자인코디 IFRAME HEIGHT 재설정 ***/
var IfrmCodi = function() {
	return {
		loaded : false,
		setHeight : function() {
			if (this.loaded == false) document.body.style.margin = '0';

			if (parent._ID('ifrmCodi')){
				parent._ID('ifrmCodi').style.height = document.body.scrollHeight+"px";
				if (top.location.href != document.location.href) document.body.style.overflow = "hidden";
			}
			this.loaded = true;
		}
	};
}

var _ifrmcodi = null;
function setHeight_ifrmCodi(){
	if (_ifrmcodi === null) _ifrmcodi = new IfrmCodi();
	if (parent._ID('ifrmCodi')){
		if (_ifrmcodi.loaded) _ifrmcodi.setHeight();
		else addEvent(window, 'load', function() { _ifrmcodi.setHeight() });
		return true;
	}
}
// 디자인 IFRAME HEIGHT 재설정

function moveFontBanner() {
	top.location.href = '../admin/design/codi.php?ifrmCodiHref=iframe.webfont.buy.php?code=4';
}

// 디자인관리 히스토리
function get_design_history() {
	try {
		var slt_obj = document.getElementById("slt_history");
		var hx = slt_obj.options[slt_obj.selectedIndex].value;
		if (!hx) return;
		if (DCTM.textarea_view_id == DCTM.textarea_base_body) {
			DCTM.textarea_view(document.getElementById(DCTM.textarea_user_view));
		}
		DCTM.source(hx, "user_body");
	}
	catch(e) { }
}

//나머지주소 수정시, 도로명/지번 나머지 주소가 같아지도록
function SameAddressSub(text) {
	var div_road_address	 = document.getElementById('div_road_address');
	var m_div_road_address	 = document.getElementById('m_div_road_address');
	var div_road_address_sub = document.getElementById('div_road_address_sub');

	if(div_road_address && div_road_address.innerHTML == "") {
		div_road_address_sub.style.display="none";
	} else if(m_div_road_address && m_div_road_address.innerHTML == "") {
		div_road_address_sub.style.display="none";
	}else {
		div_road_address_sub.style.display="";
		div_road_address_sub.innerHTML = text.value;
	}
}

// 디자인관리 미리보기
DCPV = {
	design_preview : null,
	convert : function(design_file) {
		var dfile_path = String(design_file).split("/");
		var new_design_file = dfile_path.join("/");

		return new_design_file;
	},

	// 미리보기 팝업
	preview_popup: function(linkurl, preview_file) {
		var url = linkurl+"&gd_preview=1&gd_preview_file="+preview_file;
		try {
			this.design_preview.location.replace(url);
		}
		catch(e) {
			window.open(url);
		}
	}
}

// 디자인관리 배너에디터 팝업
function popup_bannereditor(num){
	var purl = '';
	if(num){
		purl = '?bannerkey='+num;
	} else {
		purl = '';
	}
	var bannereditor = popup_return('popup.bannereditor.php'+purl,'bannereditor',820,750,0,0,0);
	//bannereditor.focus();
}

/*** 레이어 공지 팝업창 띄우기 ***/
function popupLayerNotice(titleText, s, w, h)
{
	if (!w) w = 600;
	if (!h) h = 400;

	var pixelBorder = 3;
	var titleHeight = 12;
	w += pixelBorder * 2;
	h += pixelBorder * 2 + titleHeight;

	var bodyW = window.innerWidth || document.documentElement.clientWidth || document.body.clientWidth;
	var bodyH = window.innerHeight || document.documentElement.clientHeight || document.body.clientHeight;

	var posX = (bodyW - w) / 2;
	var posY = (bodyH - h) / 2;

	hiddenSelectBox('hidden');

	/*** 백그라운드 레이어 ***/
	var obj = document.createElement("div");
	with (obj.style){
		position = "absolute";
		left = 0;
		top = 0;
		width = "100%";
		height = document.body.scrollHeight+'px';

		backgroundColor = "#000000";
		filter = "Alpha(Opacity=80)";
		opacity = "0.5";
	}
	obj.id = "objPopupLayerBg";
	document.body.appendChild(obj);

	/*** 내용프레임 레이어 ***/
	var obj = document.createElement("div");
	with (obj.style){
		position = "absolute";
		left = posX + document.viewport.getScrollOffsets().left +'px';
		top = posY + document.viewport.getScrollOffsets().top +'px';
		width = w;
		height = h;
		backgroundColor = "#ffffff";
		border = "3px solid #000000";
	}
	obj.id = "objPopupLayer";
	document.body.appendChild(obj);

	/*** 타이틀바 레이어 ***/
	var topDiv = document.createElement("div");
	var topDivHtml;
	with (topDiv.style){
		position = "relative";
		width = w+'px';
		height = '40px';
		left = '0px';
		top = '0px';
		textAlign = "center";
		backgroundColor = "#000000";
		color = "#ffffff";
		font = "bold 14px tahoma; letter-spacing:0px; line-height:14px; vertical-align:top";
	}
	topDivHtml	= '<div style="float:left;padding:13px 0px 0px 19px;">'+titleText+'</div>';
	topDivHtml	+= '<div style="float:right;padding:10px 19px 0px 0px;"><a href="javascript:closeLayer()"><img src=\"../img/btn_popup_layer_notice_close.gif\"></a></div>';

	topDiv.innerHTML = topDivHtml;
	obj.appendChild(topDiv);

	/*** 아이프레임 ***/
	var ifrm = document.createElement("iframe");
	with (ifrm.style){
		width = w - 6 +'px';
		height = h - pixelBorder * 2 - titleHeight - 3 +'px';
		//border = "3 solid #000000";
	}
	ifrm.name = 'objPopupLayerNoticeIframe';
	ifrm.frameBorder = 0;
	obj.appendChild(ifrm);
	ifrm.src = s;
}

function popupGoodschoice(eHiddenName, displayName)
{
	var pathName = window.location.pathname;
	var fileName = pathName.substring(pathName.lastIndexOf('/') + 1).replace(/\.|\_/g, "");
	var goodsChoice = window.open('../proc/popup.goodsChoice.php?eHiddenName='+eHiddenName+'&displayName='+displayName+'&fileName='+fileName, 'goodsChoice', 'width=1200px,height=700px,scrollbars=no,resizeable=no');
	if(goodsChoice){
		goodsChoice.focus();
	}
}

/*
* set parameter
* mode -	smsPopup [SMS 보내기, 회원 주소록, SMS 발송결과 재발송 등등]
*			smsBatch [SMS 일괄발송]
*			powermail [파워메일]
*			individualEmail [개별/전체 메일보내기]
*/
function getReceiveRefuseAjaxParameter(mode)
{
	var form = document.fmList;
	var paramArray = new Array();
	var parameter = '';

	if(form.type.value == 'select'){
		var checkbox = document.getElementsByName('chk[]');
		var checkboxLength = checkbox.length;
		if (checkbox.length > 0){
			for(var i=0; i<checkboxLength; i++){
				if (checkbox[i].checked == true) {
					paramArray[i] = checkbox[i].value;
				}
			}
		}
		if(mode == 'smsPopup' || mode == 'smsBatch'){
			if(paramArray.length < 1) {
				smsReceiptRefuseInfoDisplay('none');
				form.totalCount.value = 0;
				form.smsReceiveRefuseCount.value = 0;
				return '';
			}
		}
		else if(mode == 'powermail' || mode == 'individualEmail'){
			if(paramArray.length < 1){
				form.receiveRefuseCount.value = 0;
				return '';
			}
		}
		else {
			return '';
		}
		parameter = paramArray.join("|");
	}
	else if(form.type.value == 'query'){
		parameter = form.query.value;
	}
	else {
		return '';
	}

	return parameter;
}

/*
* 수신거부 제외 개수 확인
* mode -	smsPopup [SMS 보내기, 회원 주소록, SMS 발송결과 재발송 등등]
*			smsBatch [SMS 일괄발송]
*			powermail [파워메일]
*			individualEmail [개별/전체 메일보내기]
*/
function getCountActReceiveRefuse(mode)
{
	var form = document.fmList;
	var parameter = '';
	parameter = getReceiveRefuseAjaxParameter(mode);
	if(!parameter){
		return;
	}

	var ajax = new Ajax.Request("./ajaxReceiveRefuseCount.php",
	{
		method: "post",
		parameters: "mode=" + mode + "&type=" + form.type.value + "&parameter=" + encodeURIComponent(parameter),
		onComplete: function (req)
		{
			var totalCount = receiveRefuseCount = 0;
			var returnArray = new Array();
			var req = ajax.transport;
			if (req.status != 200 || req.responseText =='') {
				return;
			}
			returnArray = req.responseText.split(",");
			totalCount = returnArray[0]; // 총 발송대상
			receiveRefuseCount = returnArray[1]; // 수신거부된 회원 수

			switch(mode){
				case 'smsPopup': case 'smsBatch':
					document.getElementById('smsReceiveRefuseMsg').innerHTML = receiveRefuseCount;
					document.getElementById('smsReceiveRefuseCount').value = receiveRefuseCount;
					document.getElementById('totalCount').value = totalCount;

					if(receiveRefuseCount > 0){
						smsReceiptRefuseInfoDisplay('inline-block');
					}
					else {
						smsReceiptRefuseInfoDisplay('none');
					}
				break;

				case 'powermail': case 'individualEmail':
					form.receiveRefuseCount.value = receiveRefuseCount;
				break;
			}
		},
		onFailure :function(){
			alert("통신을 실패하였습니다.\n다시한번 시도하여 주세요.");
			return;
		}
	});
}

/*
* 수신거부 제외 레이어 팝업
* mode -	smsPopup [SMS 보내기, 회원 주소록, SMS 발송결과 재발송 등등]
*			smsBatch [SMS 일괄발송]
*			powermail [파워메일]
*			individualEmail [개별/전체 메일보내기]
*/
function openLayerPopupReceiveRefuse(mode)
{
	// 수신거부 제외 레이어 팝업 오픈 [mode - 모드, totalCount - 총발송대상, receiveRefuseCount - 수신거부 대상]
	var openLayerPopup = function(mode, totalCount, receiveRefuseCount){
		popupLayer('popup.receiveRefuse.php?mode=' + mode + '&totalCount=' + totalCount + '&receiveRefuseCount=' + receiveRefuseCount, 500, 200);
	}

	var form = document.fmList;
	var totalCount = receiveRefuseCount = 0; // 총 개수, 수신거부 개수

	switch(mode){
		case 'smsPopup': case 'smsBatch':
			if(document.getElementById('totalCount')) totalCount = document.getElementById('totalCount').value;
			if(document.getElementById('smsReceiveRefuseCount')) receiveRefuseCount = document.getElementById('smsReceiveRefuseCount').value;
			if(totalCount > 0){
				openLayerPopup(mode, totalCount, receiveRefuseCount);
			}
			else {
				alert('발송 대상이 없습니다.');
			}
		break;

		case 'powermail': case 'individualEmail':
			var parameter = '';
			parameter = getReceiveRefuseAjaxParameter(mode);
			if(!parameter){
				alert('발송 대상이 없습니다.');
				return;
			}
			var ajax = new Ajax.Request( "ajaxReceiveRefuseCount.php",
			{
				method: "post",
				parameters: "mode=" + mode + "&type=" + form.type.value + "&parameter=" + encodeURIComponent(parameter),
				onComplete: function(){
					var returnArray = new Array();
					var req = ajax.transport;
					if (req.status != 200 || req.responseText =='') {
						return;
					}
					returnArray = req.responseText.split(",");
					totalCount = returnArray[0];
					receiveRefuseCount = returnArray[1];

					openLayerPopup(mode, totalCount, receiveRefuseCount);
				},
				onFailure :function(){
					alert("통신을 실패하였습니다.\n다시한번 시도하여 주세요.");
					return;
				}
			});
		break;
	}
}

/*
* 수신거부된 회원 수 노출 여부
*/
function smsReceiptRefuseInfoDisplay(val)
{
	document.getElementById("smsReceiveRefuse").style.display = val;
}

/**
 * smsRecallColor(target, smsRecall, callNumber)
 *
 * 발신번호 색상 변경 (발신번호 리스트에 있을 시 black, 없을 시 red)
 * - target 발신번호 필드명
 * - smsRecall 발신번호
 * - callNumber 발신번호리스트 (구분자 : ',')
 */
function smsRecallColor(target, smsRecall, callNumber)
{
	var callNumberArray = callNumber.split(',');
	var msg = "";

	if (!smsRecall){
		msg = "* 사전 등록된 발신번호가 없습니다. 사전 등록 처리 후 발신번호를 선택해주세요. [<a href='http://www.godo.co.kr/news/notice_view.php?board_idx=1247' target='_blank'><u class='red'>발신번호사전등록제란?</u></a>]";
	} else {
		if (inArray(smsRecall,callNumberArray)){
			document.getElementsByName(target)[0].style.color = "#000000";
			msg = "";
		} else {
			document.getElementsByName(target)[0].style.color = "#ff0000";
			msg = "* 설정된 번호는 사전 등록된 번호가 아닙니다. 발신번호 사전 등록 후 재설정 해주세요. [<a href='http://www.godo.co.kr/news/notice_view.php?board_idx=1247' target='_blank'><u class='red'>발신번호사전등록제란?</u></a>]";
		}
	}
	document.getElementById('smsRecallText').innerHTML = msg;
}