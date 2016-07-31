/*
	기본적이 스크립트 정의
*/

//이미지에 URL을 전달한다.
function inURL(url,index){
	jQuery('#droppable'+index+'-images').attr("onclick","window.open('"+url+"')");		
}

var popupStatus = 0;

function loadPopup(){
	if(popupStatus==0){
		jQuery("#backgroundPopup").css({"opacity": "0.7"});
		jQuery("#backgroundPopup").fadeIn("slow");
		jQuery("#popupContact").fadeIn("slow");
		popupStatus = 1;
	}
}


function disablePopup(){
	if(popupStatus==1){
		jQuery("#backgroundPopup").fadeOut("slow");
		jQuery("#popupContact").fadeOut("slow");
		popupStatus = 0;
	}
	
	jQuery("body").css("overflow","");
}


function centerPopup(URL,w,h){
	
	var windowWidth = document.documentElement.offsetWidth; //document.body.clientWidth;
	var windowHeight = document.documentElement.offsetHeight;
	
	jQuery("#DynamicPopup").attr("src",URL);
	jQuery("#DynamicPopup").css("width",w+"px");
	jQuery("#DynamicPopup").css("height",h+"px");

	var popupHeight = jQuery("#popupContact").height();
	var popupWidth = jQuery("#popupContact").width();
	
	
	jQuery("#popupContact").css({
		"position": "absolute",
		"top": windowHeight/2-popupHeight/2,
		"left": windowWidth/2-popupWidth/2
	});
	
	jQuery("#backgroundPopup").css({
		"height": windowHeight
	});

	jQuery("body").css("overflow","hidden");
	
}

function LpopUp(URL,w,h,s){	
	
	Newopen(URL,w,h,s);
}


jQuery(document).ready(function(){
	jQuery('#sh_button').click(function(){		
		var value = encodeURI(jQuery("#sh").val());
		if(value != ""){
			jQuery(location).attr('href','./?sh='+value);
		}else{
			alert("검색어를 입력하세요");
		}

	})
					
	jQuery("#popupContactClose").click(function(){
		disablePopup();
	});
	
	jQuery("#backgroundPopup").click(function(){
		//disablePopup();
	});
	
	jQuery(document).keypress(function(e){
		if(e.keyCode==27 && popupStatus==1){
			disablePopup();
		}
	});

})


function Newopen(URL,w,h,s){
	var wsize=w;
	var hsize=h;
	var posx=0;
	var posy=0;
	posx = (screen.width-wsize)/2-1;
	posy = (screen.height-hsize)/2-1;
	window.open(URL,"edit","scrollbars="+s+",toolbar=no,location=no,directories=no,status=no,width="+wsize+",height="+hsize+",resizable=no,menubar=no,top="+posy+",left="+posx+",topmargin=0,leftmargin=0");
}