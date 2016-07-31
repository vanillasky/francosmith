function addMoreGoodsToList(){
	if(!listingEnd){
		$('.indicator').show();
		listLoading = true;
		
		$.ajax({
			url:"list.add.php",
			dataType:"json",
			data: { category: listCategory, page: listPage, listingCnt: listingCnt, listSort: listSort, kw: listKW },
			success:function(result){
				if(result.success){
					if(!result.listingCnt || listingEnd){
						listingEnd = result.listingEnd;
						//alert('더 이상 불러올 상품이 없습니다.');
					}
					else{
						listPage = parseInt(listPage) + 1;
						listingCnt = listingCnt + result.listingCnt;
						listingEnd = result.listingEnd;

						$.each(result.list, function(i, item){
							var _data = new StringBuffer();
							_data.append('<li>');
							_data.append('<dl>');
							_data.append('<dt class="hidden">상품이미지</dt>');
							_data.append('<dd class="gl_img">'+item.goodsImage+'</dd>');
							_data.append('<dt class="hidden">상품명</dt>');
							_data.append('<dd class="gl_name">' + item.goodsnm + '</dd>');
							_data.append('<dt class="hidden">짧은설명</dt>');
							_data.append('<dd class="gl_shordesc">' + item.shortdesc + '</dd>');							
							_data.append('<dt class="gl_price_title blt">상품가격 : </dt>');
							_data.append('<dd class="gl_price">' + item.price + '</dd>');
							if(item.reserve){
							_data.append('<dt class="gl_reserve_title blt">적립금 : </dt>');
							_data.append('<dd class="gl_reserve">' + item.reserve + '</dd>');
							}
							_data.append('<dt class="hidden">상세페이지</dt>');
							_data.append('<dd class="gl_detail"><a href="'+result.mobileShopRootDir+'/goods/view.php?goodsno='+item.goodsno+'&amp;category='+result.category+'"><span class="hidden">상품 상세페이지로 가기</span></a></dd>');
							_data.append('</dl>');
							_data.append('</li>');
							$('#goods_list').append(_data.toString());
						});

						if(listingEnd){
							
						}
					}
				}
				$('.indicator').hide();
				listLoading = false;
			},
			error:function(){
				alert('일시적인 오류 발생하였습니다. \n다시 시도해보시기 바랍니다.');
				$('.indicator').hide();
				listLoading = false;
			}
			
		});
	}
}

function chkListScrollEnd(){
	
	if(!listingEnd){

		if (navigator.userAgent.indexOf("MSIE 5.5")!=-1) {
			var sheight = document.body.scrollHeight;
		} else {
			var sheight = document.documentElement.scrollHeight;
		} 

		var cheight = document.body.clientHeight;

		if(($(window).scrollTop()+cheight)>=sheight){
			return true;
		}
		else{
			return false;
		}
	}
}

function goodsListPageShown(evt)
{
	if (evt.persisted) window.onscroll = goodsListScrollEvent; 
}

function goodsListScrollEvent() {
	if(!listLoading) addMoreGoodsToList();
} 
