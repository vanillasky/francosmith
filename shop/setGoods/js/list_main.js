
var Align = {
    init : function(wrapper, div, img, width, space){
        this.wrapper = wrapper;
        this.div = div;
        this.img = img;
        this.width = width;
		this.height =0;
        this.space = space;
        this.cnt = 0;
        this.line = 0;
        
        this.wrapper[0].style.position = 'relative';
    },
    /* 넓이에 따라 가로 열의 수를 설정하고 해당 수 만큼 초기화함. */
    set : function(width){
        for(var i=0; i< this.img.length; i++)
            Align.changeWidth(this.img[i]);
        
        var line = parseInt(width/(this.width + this.space));
        this.wrapper[0].style.width = line * (this.width + this.space) - this.space + 'px';
		
        if(this.line != line){
            this.p = new Array();
            for(var i=0; i< line; i++){
                this.p[i] = {x:i*(this.width+this.space), y:0};
            }
        }
        
        for(var i=0; i< this.div.length; i++)
            this.setImage(i);
        this.cnt = this.div.length;
        
        /* wrapper 최종 높이 설정. */
        var max = this.p[0].y;
        for(var i=1; i< this.p.length; i++){
            if(this.p[i].y > max){
                max = this.p[i].y;
			}
		}

		var scHeight = max - this.space;
		if(scHeight < 0) scHeight = 0;
        this.wrapper[0].style.height = scHeight + "px";		
    },
    /* 이미지 배치. */
    setImage : function(n){
        this.div[n].style.position = 'absolute';
        this.div[n].style.width = this.width + 'px';
        
        var min = this.p[0].y;
        var v = 0;
        for(var i=1; i< this.p.length; i++){
            if(this.p[i].y < min){
                v = i;
                min = this.p[i].y;
            }
        }
        this.div[n].style.left = this.p[v].x + 'px';
        this.div[n].style.top = this.p[v].y + 'px';
        
        this.p[v].y += (this.div[n].offsetHeight + this.space)-15;
		
    },
    /* 이미지 넓이 조정. */
    changeWidth : function(img){
        var width = this.width - 3.5*this.space;
		   
        var w = img.offsetWidth;
        var h = img.offsetHeight;
        var aspect = h/w;
        
        img.style.width = width + 'px';
        img.style.height = width * aspect + 'px';
    }
}



function scrollsort(){
	Align.init(jQuery('#Images'),jQuery('.codyDiv'), jQuery('.Image'), listImgWidth, listImgMargin);
    Align.set(jQuery('#Wrapper')[0].offsetWidth);
}

function LoadPage(page, sh, sp, cody){
	jQuery.ajax({
		type: 'get',
		contentType: 'text/html;',
		url: '../setGoods/index.php',
		data:{	pg:page,
				sh:sh, 
				sp:sp,
				cody:cody,
				ll:ll
			 },
		dataType: "html",
		success: function(data){
			
			jQuery('#Images').append(data);
			scrollsort();
			jQuery('div.description').each(function(){
				jQuery(this).css('opacity', 0);
				jQuery(this).css('width', jQuery(this).siblings('img').width());
				jQuery(this).css('height', jQuery(this).siblings('img').height());
				jQuery(this).parent().css('width', jQuery(this).siblings('img').width());
				jQuery(this).parent().css('height', jQuery(this).siblings('img').height());
				jQuery(this).css('display', 'block');
			});
				
			jQuery('div.ImageDiv').hover(function(){
				//fadeTo(활성화시간, 투명도);									     
				jQuery(this).children('.description').stop().fadeTo(200, 1);
			},function(){
				jQuery(this).children('.description').stop().fadeTo(200, 0);
			});
		}
	});
}

