/* 세트 이미지 마우스 오버시 하일라이트 */
function image_highlight(index){
	jQuery('#droppable'+index).css("border","1px solid #9bca3e");		
	jQuery('#droppable'+index).css("opacity","0.7");		
}

/* 세트 이미지 마우스 오버시 하일라이트 제거*/
function UN_image_highlight(index){
	jQuery('#droppable'+index).css("border","1px solid #DDC5B5");		
	jQuery('#droppable'+index).css("opacity","1");
}

function load_comment(idx) {
	
	jQuery.ajax({
		type: 'GET',
		contentType: 'text/html; charset=utf-8',
		url: 'comment/comment.php',
		data:{idx:idx},
		dataType: "html",
		success: function(data){
				jQuery('#comment').append(data);
		}
	});
};

var array_idx = "";
var cody_idx = "";
var Load_popup = function(){
	return {
		set : function(gidx){
			array_idx = array_idx + gidx+",";			
		},
		action : function(){
			
			var gidxList = array_idx.substring(0,(array_idx.length-1));
			var ofHeight = window.screen.height -300;
			if(ofHeight < 350) ofHeight = '350'; 
			
			LpopUp('./goodsView/goodsView.php?gidx=' + gidxList,863,ofHeight,'yes');
		},
		one_action : function(gidx){
			
			LpopUp('./goodsView/goodsView.php?gidx=' + gidx,850,500,'yes');
		}
		
	}
	
}();

function relationCody(idx) {
	
	jQuery.ajax({
		type: 'GET',
		contentType: 'text/html; charset=utf-8', 
		url: './content.php',
		data:{'fn':'recody','idx':idx},
		dataType: "html",
		success: function(data){
				jQuery('#relationCody').html(data);				
		}
	});
};

function like_cnt(idx) {
	
	jQuery.ajax({
		type: 'GET',
		contentType: 'text/html; charset=utf-8',
		url: './content.php',
		data:{fn:'like',idx:idx},
		dataType: "html",
		success: function(data){
			alert('좋아요. 등록 되었습니다.');
			location.reload();				
		}
	});
};