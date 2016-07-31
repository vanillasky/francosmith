
function _viewSubTop(sobj)
{
	sobj.style.backgroundColor = "#376e88";
	sobj.children[0].style.color = "#eee3c5";
	//$('#level1').addClass('menuWhite');
	//alert($('#level1'));
	var obj = sobj.children[1].children[0];
	obj.style.display = "block";
    							
}

function _hiddenSubTop(sobj)
{
	
	sobj.style.backgroundColor = "#eee3c5";
	sobj.children[0].style.color = "#691414";
	
	var obj = sobj.children[1].children[0];
	obj.style.display = "none";
}


function _execSubLayerTop()
{
	
	var obj = document.getElementById('menuLayer');
									 
	for(var i=0;i<obj.rows[0].cells.length;i++){
		if (typeof(obj.rows[0].cells[i].children[1])!="undefined"){
			obj.rows[0].cells[i].onmouseover = function(){ _viewSubTop(this) }
			obj.rows[0].cells[i].onmouseout = function(){ _hiddenSubTop(this) }
		}
	}
	
	$(".cate").css({'cursor':'pointer'});
	$(".cate").mouseover(function() {
		//$(this).attr('class', 'cate_mouse_over');
		this.style.backgroundColor = "#ececea";
    });
	$(".cate").mouseout(function() {
		//$(this).attr('class', 'cate');
		this.style.backgroundColor = "#ffffff";
		
    });
}

function bindTopMenu() {
	var jq = jQuery.noConflict();
	
	jq(document).ready( function() {
		jq("#top_cate li a").css("border", "0");
		
		jq("#top_cate #brand_link").click(function() {
			
			jq("#top_cate li.menu-container").removeClass("menu_hover");
			jq("#top_cate li.menu-container").children("p").children("a").css("color", "#ffffff");
			jq("#top_cate li.menu-container").children(".dropdown").hide();
			
			if (jq("#top_cate li.brand-container").hasClass("menu_hover")) {
				jq("#top_cate li.brand-container").children("p").children("a").css("color", "#ffffff");
				jq("#top_cate li.brand-container").children(".dropdown").fadeOut(200);
				jq("#top_cate li.icon").children("p").children("a").css("background", "url(/shop/data/skin/freemart/img/icon/list_white_16.png) no-repeat center left");
				jq("#top_cate li.brand-container").removeClass("menu_hover");
				
			} else {
				jq("#top_cate li.brand-container").addClass("menu_hover");
				jq("#top_cate li.brand-container").children("p").children("a").css("color", "#1d1d1d");
				jq("#top_cate li.brand-container").children(".dropdown").fadeIn(200);
				
				jq("#top_cate li.brand-container").children("p").children("a").css("background", "url(/shop/data/skin/freemart/img/icon/list_black_16.png) no-repeat center left");
				jq("#top_cate li.brand-container").children("p").children("a").css("margin-left", "20px");
				jq("#top_cate li.brand-container").children("p").children("a").css("padding-left", "20px");
				
			}
			
		});
		
		jq("#top_cate li.menu-container").click(function() {
			
			jq("#top_cate li.menu-container").not(this).removeClass("menu_hover");
			jq("#top_cate li.menu-container").not(this).children("p").children("a").css("color", "#ffffff");
			jq("#top_cate li.menu-container").not(this).children(".dropdown").hide();
			
			jq("#top_cate li.brand-container").removeClass("menu_hover");
			jq("#top_cate li.brand-container").children("p").children("a").css("color", "#ffffff");
			jq("#top_cate li.brand-container").not(this).children(".dropdown").hide();
			jq("#top_cate li.icon").children("p").children("a").css("background", "url(/shop/data/skin/freemart/img/icon/list_white_16.png) no-repeat center left");
			
			if (jq(this).hasClass("menu_hover")) {
				jq(this).children("p").children("a").css("color", "#ffffff");
				jq(this).children(".dropdown").fadeOut(200);
				jq(this).removeClass("menu_hover");
				
			} else {
				jq(this).addClass("menu_hover");
				jq(this).children("p").children("a").css("color", "#1d1d1d");
				jq(this).children(".dropdown").fadeIn(200);
			}
			
//			if (jq(this).hasClass("menu_hover")) {
//				
//				jq("#top_cate li[class*='menu_hover']").children("p").children("a").css("color", "#ffffff");
//				jq("#top_cate li[class*='menu_hover']").children(".dropdown").hide();
//				jq("#top_cate li.icon").children("p").children("a").css("background", "url(/shop/data/skin/freemart/img/icon/list_white_16.png) no-repeat center left");
//				jq("#top_cate li[class*='menu_hover']").removeClass("menu_hover");
//				
////				jq("#top_cate li").siblings().removeClass("menu_hover");
////				jq("#top_cate li").siblings().children("p").children("a").css("color", "#ffffff");
////				jq("#top_cate li").siblings().children(".dropdown").hide();
////				jq("#top_cate li.icon").children("p").children("a").css("background", "url(/shop/data/skin/freemart/img/icon/list_white_16.png) no-repeat center left");
//				
//				if(jq(this).hasClass("icon")) {
//					jq(this).children("p").children("a").css("background", "url(/shop/data/skin/freemart/img/icon/list_white_16.png) no-repeat center left");
//					jq(this).children("p").children("a").css("margin-left", "20px");
//					jq(this).children("p").children("a").css("padding-left", "20px");
//				} 
//				
//				jq(this).removeClass("menu_hover");
//				jq(this).children("p").children("a").css("color", "#ffffff");
//				jq(this).children(".dropdown").hide();
//				
//				
//				
//			} else {
//				
////				jq("#top_cate li").siblings().removeClass("menu_hover");
////				jq("#top_cate li").siblings().children("p").children("a").css("color", "#ffffff");
////				jq("#top_cate li").siblings().children(".dropdown").hide();
////				jq("#top_cate li.icon").children("p").children("a").css("background", "url(/shop/data/skin/freemart/img/icon/list_white_16.png) no-repeat center left");
//				
//				if(jq(this).hasClass("icon")) {
//					jq(this).children("p").children("a").css("background", "url(/shop/data/skin/freemart/img/icon/list_black_16.png) no-repeat center left");
//					jq(this).children("p").children("a").css("margin-left", "20px");
//					jq(this).children("p").children("a").css("padding-left", "20px");
//				}
//				
//				jq(this).addClass("menu_hover");
//				jq(this).children("p").children("a").css("color", "#1d1d1d");
//				jq(this).children(".dropdown").show();
//			}
			
			
		}
// hover 사용 시		
//		, function() {
//					
//			if(jq(this).hasClass("icon")) {
//				jq(this).children("p").children("a").css("background", "url(/shop/data/skin/freemart/img/icon/list_white_16.png) no-repeat center left");
//				jq(this).children("p").children("a").css("margin-left", "20px");
//				jq(this).children("p").children("a").css("padding-left", "20px");
//						
//				}
//			
//				jq(this).removeClass("menu_hover");
//				jq(this).children("p").children("a").css("color", "#ffffff");
//				jq(this).children(".dropdown").fadeOut(200);
//					
//		}
		);
	});
}

function toggleDiv(divId) {
   $("#"+divId).toggle();
}

function showonlyone(thechosenone) {
	 
	 var jq = jQuery.noConflict();
	 var divs = jq(".main_navi");

	for( var i=0; i < divs.length; i++) {
		if(divs[i].id == thechosenone) {
			if(jq(divs[i]).is(':visible')) {
				jq(divs[i]).hide();
			} else {
				jq(divs[i]).show();
			}
		}
		else {
			jq(divs[i]).hide();
		}
	 }
	 
//     jq('.main_navi').each(function(index) {
//          if (jq(this).attr("id") == thechosenone) {
//		   	if(jq(this).is(':visible')) {
//				jq(this).hide(200);	
//			} 
//			else {
//			   jq(this).show(200);
//			}
//          }
//          else {
//               jq(this).hide(600);
//          }
//     });

}

function _closeLayer(lid) {
	showonlyone(lid);
}


function toggle_collapse(cate_id) {
    var	jq = jQuery.noConflict();
	
	if(jq("#"+cate_id).attr("class") == "img-swap") {
		jq("#"+cate_id).attr("src", jq("#"+cate_id).attr("src").replace("_expanded", "_collapsed"));
		jq("#"+cate_id+"_1").show();
	} else {
		jq("#"+cate_id).attr("src", jq("#"+cate_id).attr("src").replace("_collapsed", "_expanded"));
		jq("#"+cate_id+"_1").hide();
		
	}
	
	jq("#"+cate_id).toggleClass("on");
}

function wopen(url, w, h, wname) {
	
	var center_left = (screen.width/2) - (w / 2);
	var center_top = (screen.height/2) - (h/2);
	var option_str = "scrollbars=1, width="+w+", height="+h+", left="+center_left+", top="+center_top + ",resizable=1";
	var window_name = arguments.length == 4 ? wname : "Win";
	
	window.open(url, window_name, option_str);
}

var sb_links = [];
sb_links['facebook'] = "//www.facebook.com/sharer/sharer.php?u=[url]&t=[text]";
sb_links['twitter'] = "//twitter.com/share?text=[text]&url=[url]";
sb_links['googleplus'] = "//plus.google.com/share?url=[url]";
sb_links['pinterest'] = "//pinterest.com/pin/create/button/?media=[media]&url=[url]";
sb_links['reddit'] = "//reddit.com/submit?url=[url]";


function bind_share_button (spanId, goods_url, goods_name, media) {
	var	jq = jQuery.noConflict();
	var link = sb_links[spanId.replace("sb_", "")];
	link = encodeURI("http:"+ link.replace('[url]', goods_url).replace('[text]', goods_name).replace('[media]', media));
	
	jq("#"+spanId).click(function() {
		wopen(link, 800, 600, "SHARE");						  
	});
	
}

