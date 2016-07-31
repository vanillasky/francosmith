var imgCompare = [];	
var imgWidth = [];	
var selectImg = new Array();	
var dragImg = "";
var delImg = "";

function objectValue(obj){
	var info = "";
	
	for (var imsi in obj) { 
	   info += imsi + ' = ' + obj[imsi] + '<br>'; 
	   var title = imsi;
	   //alert(imsi+" : "+(typeof obj[imsi]).toString() );
		if(typeof obj[imsi] == 'object' && obj[imsi] != '[object Window]'){
			for (var imsi in obj[title]) {
			  info += "&nbsp;&nbsp;&nbsp;&nbsp;" + imsi + ' = ' + obj[title][imsi] + '<br>'; 
			}
		}
		
	}
	jQuery("#print").html(info);
}

function getInternetVersion(ver) {
	var rv = -1; // Return value assumes failure.
	var ua = navigator.userAgent;
	var re = null;

	if(ver == "MSIE"){
		re = new RegExp("MSIE ([0-9]{1,}[\.0-9]{0,})");
	}else{
		re = new RegExp(ver+"/([0-9]{1,}[\.0-9]{0,})");
	}

	if (re.exec(ua) != null){
		rv = parseFloat(RegExp.$1);
	}
	return rv;
}

// Check the Browser Type and Version
function browserCheck(){
	var ver = 0; // Browser Version
	var ret = new Array(2);

	if(navigator.appName.charAt(0) == "N"){
		if(navigator.userAgent.indexOf("Chrome") != -1){
			ver = getInternetVersion("Chrome");
			ret[0] = "Chrome"
			ret[1] = ver;

		}else if(navigator.userAgent.indexOf("Firefox") != -1){
			ver = getInternetVersion("Firefox");
			ret[0] = "Firefox";
			ret[1] = ver;

		}else if(navigator.userAgent.indexOf("Safari") != -1){
			ver = getInternetVersion("Safari");
			ret[0] = "Safari";
			ret[1] = ver;
		}
	}else if(navigator.appName.charAt(0) == "M"){
		ver = getInternetVersion("MSIE");
		ret[0] = "MSIE";	
		ret[1] = ver;
	}

	return ret;
}

function stringSpilt(str){
	var spiltdata =[];
	if(str.length > 0){
		 spiltdata = str.split("/");
	}
	return spiltdata[(spiltdata.length -1)];

}

function draggableScript(idVar){
	dragImg = idVar;
	jQuery( "#"+idVar ).draggable({cursor:"move", helper: "clone",opacity: "0.7",containment:"div"});	//{revert: "true"}
}

function iNdraggableScript(idVar){
	dragImg = idVar;
	
	jQuery( "#"+idVar ).draggable({cursor:"move"});	//{revert: "true"}
	jQuery( "#"+idVar ).addClass( "dropped-images" );
}

function imgClick(idVar,cpe){
	jQuery( "#"+idVar+"-images" ).addClass( "dropped-images-selection" );
	jQuery( "#scrollbar" ).css( "visibility","visible");
	jQuery( "#imgscrollbar" ).css( "visibility","visible");
	SelectSlider(idVar);
	SelectSizeSlider(idVar,cpe);
	delImg = idVar;
}

//드래그 이벤트
function droppableScript(idVar,cpe){
	
	jQuery( "#"+idVar ).droppable({
		drop: function( event, ui ) {		
			var name = event.originalEvent.target.name;
			
			if(name =="" || name == null) return
			
			var fileName = stringSpilt(name);
			var goodsNo = event['originalEvent']['target']['alt'];			
			var title = event['originalEvent']['target']['title'];			
			
			if(fileName != null){
				//레이어 사이즈를 구한다.
				var layerId = jQuery( this ).attr('id');
				var layerW = jQuery( '#'+layerId ).width();
				var layerH = jQuery( '#'+layerId ).height();
				
				var inHtml = "<img id='"+idVar+"-images' src='/shop/data/goods/"+fileName+"' title='"+title+"' alt='"+goodsNo+"' onclick=\"imgClick('"+idVar+"','"+cpe+"')\" onload=\"imageLoad('"+idVar+"','"+layerW+"','"+layerH+"','"+fileName+"','"+cpe+"')\"><scr"+"ipt>iNdraggableScript( '"+idVar+"-images' );</scr"+"ipt>";
				jQuery( '#'+layerId ).addClass( "dropped" ).html( inHtml );
				
				dragImg = "";		
			}	
		}
	});
}

// 이미지 위치 및 사이즈조정
function imageLoad(idVar, layerW, layerH, fileName,cpe){
	var orgImgW = jQuery( "#"+idVar+"-images" ).width();
	var orgImgH = jQuery( "#"+idVar+"-images" ).height();
	
	if(layerW <= orgImgW){
		jQuery( "#"+idVar+"-images" ).css('width',layerW-10);
		jQuery( "#"+idVar+"-images" ).css('height','');
	}
	
	if(layerH < jQuery( "#"+idVar+"-images" ).height()){
		jQuery( "#"+idVar+"-images" ).css('height',layerH-10);
		jQuery( "#"+idVar+"-images" ).css('width','');
	}

	//이미지 중간위치에 보여주기
	var layerImgL = (layerW/2) - jQuery( "#"+idVar+"-images" ).width()/2;
	var layerImgT = (layerH/2) - jQuery( "#"+idVar+"-images" ).height()/2;
	jQuery( "#"+idVar+"-images" ).css('left',layerImgL);
	jQuery( "#"+idVar+"-images" ).css('top',layerImgT);							
	
	//파일이름을 저장한다.
	imgCompare[cpe] = fileName;
	imgWidth[cpe] = jQuery( "#"+idVar+"-images" ).width();

	imgClick(idVar,cpe);
}


//선택이미지 회전
function SelectSlider(idVar){
	var browser =new Array(2);
	browser = browserCheck();
	if(browser[0] == 'MSIE' && browser[1] < 9 ){
		jQuery( "#slider-range-max" ).html("rotate기능은 익스플로러 9 버전 이상 지원." );
		return
	}
	var rto = "";
	var RotateAngle = jQuery( "#"+idVar+"-images" ).getRotateAngle();
	if(RotateAngle < 1) RotateAngle = 0;
	var ro = jQuery( "#"+idVar+"-images" ).attr('style');
	jQuery( "#slider-range-max" ).slider({
		//range: "min",
		min: 0,
		max: 360,
		value: RotateAngle,
		slide: function( event, ui ) {
			jQuery( "#amount" ).val( ui.value );
			jQuery( "#"+idVar+"-images" ).rotate(ui.value);									
		}
	});
	jQuery( "#amount" ).val( jQuery( "#slider-range-max" ).slider( "value" ) );
}

//선택이미지 사이즈 
function SelectSizeSlider(idVar,cpe){
	var realImgSizeW = jQuery( "#"+idVar+"-images" ).width();
	var realImgSizeH = jQuery( "#"+idVar+"-images" ).height();
	var orgImgSize = imgWidth[cpe];
	
	var maxSize = Number(orgImgSize)+Number(430);
	var minSize = Number(orgImgSize)-Number(430);
	if(minSize < 100) minSize = 100;

	jQuery( "#size-range-max" ).slider({
		//range: "min",
		min: 100,
		max: maxSize,
		value: realImgSizeW ,
		slide: function( event, ui ) {						
			jQuery( "#imgamount" ).val( ui.value );
			jQuery( "#"+idVar+"-images" ).width(ui.value);

			var imgh = realImgSizeH * ui.value / realImgSizeW;
			jQuery( "#"+idVar+"-images" ).height(imgh);

		}
	});
	jQuery( "#imgamount" ).val( jQuery( "#size-range-max" ).slider( "value" ) );
	
}

function sliderReset(){
	jQuery( "#amount" ).val('0');
	jQuery( "#imgamount" ).val('0');
	jQuery( "#scrollbar" ).css( "visibility","hidden");
	jQuery( "#imgscrollbar" ).css( "visibility","hidden");
}

function subit(){
	
	var $templateForm = jQuery( "#templateForm" ).find("img");
	var RotateAngle = 0;
	var rto = "";
	var imgnm = "";
	var imgNo = "";
	var imgRotate = "";
	var imgPosition = "";
	var templateArea = "";
	var divArea = "";
	var imgnmsize="";
	var divpos="";
	var campusSize="";
	var group = jQuery('#templateArea div').length;
	
	if ($templateForm['length'] != group) {
		alert('템플릿에 코디가 다 채워지지 않았습니다. \n템플릿을 변경하시거나, 나머지 코디를 채워주세요.');
		return	
	}

	for(var i=0;i < $templateForm['length'];i++){
		var imgname = $templateForm[i]['id'];
		var divname = imgname.split('-');

		RotateAngle = jQuery('#'+imgname).getRotateAngle();
		
		if(RotateAngle > 0 && RotateAngle !== "" ){
			rto = jQuery('#'+imgname).attr('style')+'-webkit-transform:rotate('+RotateAngle+'deg); -moz-transform:rotate('+RotateAngle+'deg); -ms-transform:rotate('+RotateAngle+'deg);';
			jQuery( "#"+imgname).attr('style',rto);						
		}
		
		divArea += jQuery( "#"+divname[0]).width() + ":" + jQuery( "#"+divname[0]).height() + "^";	//div 크기
		imgnm += jQuery( "#"+imgname).attr('src')+"^";												//파일명
		imgNo += jQuery( "#"+imgname).attr('alt')+"^";											//상품번호
		imgnmsize += jQuery( "#"+imgname).width()+":"+jQuery( "#"+imgname).height()+"^";			//크기
		imgRotate += RotateAngle+"^";																//회전각도
		imgPosition += jQuery( "#"+imgname).css('left').replace("px","")+":"+jQuery( "#"+imgname).css('top').replace("px","")+"^";	//시작위치
		divpos += jQuery( "#"+divname[0]).attr('pos') +"^";					
		jQuery( "#"+imgname).attr('onclick','');									//클릭시 클릭이벤트 변경
		jQuery( "#"+imgname).attr('onload','');										//onload 이벤트 삭제
		
		jQuery( "#"+divname).removeClass('Template dropped');								
		jQuery( "#"+divname).addClass('Template_change dropped_change');	
		jQuery( "#"+imgname).removeClass('dropped-images dropped-images-selection');								
		jQuery( "#"+imgname).addClass('dropped-images_chang');	
	}
	campusSize = jQuery("#templateArea").width()+'^'+jQuery("#templateArea").height();
	
	jQuery('#codyhtml').val(jQuery( "#templateForm" ).html());
	jQuery('#T_img_cnt').val($templateForm['length']);
	jQuery('#imgnm').val(imgnm);
	jQuery('#imgno').val(imgNo);
	jQuery('#imgnmsize').val(imgnmsize);
	jQuery('#imgRotate').val(imgRotate);
	jQuery('#divArea').val(divArea);
	jQuery('#imgPosition').val(imgPosition);				
	jQuery('#Divpos').val(divpos);
	jQuery('#campusSize').val(campusSize);

	jQuery('form').submit();
}

/*** 새로하기 **/
function newcody() {
	var $templateForm = jQuery( "#templateForm" ).find("img");

	for(var i=0;i < $templateForm['length'];i++){
		var imgname = $templateForm[i]['id'];

		jQuery('#'+imgname).remove();								
	}
}


/****************템플릿이미지 삭제****************/
function codyImagesDel(){
	if(delImg !=""){ 
		jQuery( "#"+delImg).removeClass('dropped');	
		jQuery( '#'+delImg ).addClass("Template").html( '' );
	}else{
		alert('삭제할 이미지가 없습니다.')
	}
}

/******************del키로 이미지삭제************/
document.onkeydown=myKeyPressHandler;
function myKeyPressHandler(){
	key = event.keyCode;
	if(key == 46){
		var fc = jQuery("*:focus").attr('id');
		if(fc != 'st'){
			codyImagesDel();	
		}
	}else if(key == 13){
		searchGoods('1');
		
	}
}

	
/*** 템플릿 로드 */
function template(fnm){
	if(jQuery('#templateArea div').length > 1){
		if(fnm != 'codytype1_1'){
			alert("템플릿 변경시 편집중인 코디 이미지가 초기화 됩니다.");		
			sliderReset();
		}
	}
	jQuery.ajax({
		type: 'get',
		contentType: 'text/html; charset=euc-kr',
		url: '../../data/Template/'+fnm+'.htm',
		dataType: "html",
		success: function(data){
			jQuery('#templateForm').html(data);
			jQuery('#TP_id').val(fnm);
		   
		}
	});
}


function searchGoods(pg){
	var svals = "";
	var gval;
	var sp;
	var st;
	
	/* 카테고리 만큼 루핑*/				
	for(var i=1;i<5;i++){
		gval = jQuery('select:eq(' + i + ') option:selected').val();		
		if(gval != '') svals = gval;	
	}
	
	sp = jQuery('#sp').val();
	st = jQuery('#st').val();
	
	if(svals == "" && st ==""){
		alert('검색어 또는 검색 카테고리를 선택하세요.');
		return
	}

	jQuery.ajax({
		type: 'get',
		contentType: 'text/html; charset=euc-kr',
		url: '../../admin/codyEditer/searchGoods.php',
		data:{'svals':svals,'sp':sp,'st':st,'pg':pg},
		dataType: "html",
		success: function(data){
			jQuery('#searchItemBox').html(data);
		}
	});		

}

function selectImage(index){
	if(jQuery('#bgimg'+index).css('display') == 'none'){
		jQuery('#bgimg'+index).css('display','block');
		selectImg += "'"+index+"',";
	}else{
		jQuery('#bgimg'+index).css('display','none');
		selectImg = selectImg.replace("'"+index+"',","");
	}				
}

function returnKey(arr){
	var keys = "";
	for(key in arr){
		keys += arr[key]+",";
	}
	alert(keys);
}	
function categoryBox(name,idx,val,type,formnm){
	var stimgtxt="";
	if (!idx) idx = 1;
	if (type=="multiple") type = "multiple style='width:160px;height:96'";
	for (i=0;i<idx;i++){
		document.write("<select " + type + " idx=" + i + " name='" + name + "' onchange='categoryBox_request(this)' class='selectOpt' ></select>");					
	}
	
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
				this.oCate[i].options[0] = new Option((i+1)+"차 분류","");
			}
		}
	}

}

function categoryBox_request(obj,val){
	if (!val) val = "";
	var idx = obj.getAttribute('idx');
	
	if ( document.location.href.indexOf("/admin") != -1 ){
		exec_script("/shop/lib/_categoryBox.script.php?mode=user&idx=" + idx + "&obj=" + obj.name + "&formnm=" + obj.form.name + "&val=" + val + "&category=" + obj.value);
	}			
}

function exec_script(src){
	var ret = "<scr"+"ipt src='"+src+"'></scr"+"ipt>";		
	
	jQuery('#dynamic').html(ret);
}


function searchTemplate(id){
	var val = jQuery('#'+id).val();
	
	jQuery.ajax({
		type: 'get',
		contentType: 'text/html; charset=euc-kr',
		url: '../../admin/codyEditer/index.php',
		data:{'val':val,'fn':'T'},
		dataType: "html",
		success: function(data){
			jQuery('#layerSelect').html(data);
		}
	});
}