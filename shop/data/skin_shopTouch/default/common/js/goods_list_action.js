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
						//alert('�� �̻� �ҷ��� ��ǰ�� �����ϴ�.');
					}
					else{
						listPage = parseInt(listPage) + 1;
						listingCnt = listingCnt + result.listingCnt;
						listingEnd = result.listingEnd;

						$.each(result.list, function(i, item){
							var _data = new StringBuffer();
							_data.append('<li>');
							_data.append('<dl>');
							_data.append('<dt class="hidden">��ǰ�̹���</dt>');
							_data.append('<dd class="gl_img">'+item.goodsImage+'</dd>');
							_data.append('<dt class="hidden">��ǰ��</dt>');
							_data.append('<dd class="gl_name">' + item.goodsnm + '</dd>');
							_data.append('<dt class="hidden">ª������</dt>');
							_data.append('<dd class="gl_shordesc">' + item.shortdesc + '</dd>');							
							_data.append('<dt class="gl_price_title blt">��ǰ���� : </dt>');
							_data.append('<dd class="gl_price">' + item.price + '</dd>');
							if(item.reserve){
							_data.append('<dt class="gl_reserve_title blt">������ : </dt>');
							_data.append('<dd class="gl_reserve">' + item.reserve + '</dd>');
							}
							_data.append('<dt class="hidden">��������</dt>');
							_data.append('<dd class="gl_detail"><a href="'+result.mobileShopRootDir+'/goods/view.php?goodsno='+item.goodsno+'&amp;category='+result.category+'"><span class="hidden">��ǰ ���������� ����</span></a></dd>');
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
				alert('�Ͻ����� ���� �߻��Ͽ����ϴ�. \n�ٽ� �õ��غ��ñ� �ٶ��ϴ�.');
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
