
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

