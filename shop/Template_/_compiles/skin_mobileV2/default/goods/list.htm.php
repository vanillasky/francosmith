<?php /* Template_ 2.2.7 2013/05/27 11:58:31 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/default/goods/list.htm 000008115 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<?php if($TPL_VAR["page_title"]){?>
<?php $this->print_("sub_header",$TPL_SCP,1);?>

<?php }?>

<style type="text/css">
section#goodslist .goods-sort-area {height:40px; padding:0px 12px; background:#f5f4f5;}
section#goodslist .goods-sort-area .goods-sort {float:left;}
section#goodslist .goods-sort-area .goods-sort select{height:26px; margin-top:7px;}
section#goodslist .goods-sort-area .goods-view-type {float:right; margin-top:7px;}
section#goodslist .goods-sort-area .goods-view-type .view-list{width:25px; height:27px; background:url('/shop/data/skin_mobileV2/default/common/img/nlist/btn_list_off.png'); float:left;}
section#goodslist .goods-sort-area .goods-view-type .view-list-disable {background:url('/shop/data/skin_mobileV2/default/common/img/nlist/btn_list_on.png');float:left;}
section#goodslist .goods-sort-area .goods-view-type .view-gallery{width:25px; height:27px; background:url('/shop/data/skin_mobileV2/default/common/img/nlist/btn_gallery_off.png');float:left;}
section#goodslist .goods-sort-area .goods-view-type .view-gallery-disable {background:url('/shop/data/skin_mobileV2/default/common/img/nlist/btn_gallery_on.png');float:left;}


section#goodslist {background:#FFFFFF; }
section#goodslist .goods-area { height:100%;}
section#goodslist .goods-area .goods-row{clear:both; margin:auto; min-width:296px; padding:12px;}
section#goodslist .goods-area .goods-item{width:30%; margin-bottom:18px; display:block; float:left; min-width:87px;}
section#goodslist .goods-area .goods-item .goods-img{width:100%; text-align:center;}
section#goodslist .goods-area .goods-item .goods-img img{width:100%; margin:auto; border:solid 1px #dbdbdb;}
section#goodslist .goods-area .goods-item .goods-nm {width:100%; height:28px; font-size:12px; color:#353535; margin-top:5px;overflow:hidden;}
section#goodslist .goods-area .goods-item .goods-nm a{font-size:12px; color:#353535;}
section#goodslist .goods-area .goods-item .goods-price {width:100%; height:18px; font-weight:bold; font-size:14px; color:#f03c3c; line-height:18px;}
section#goodslist .goods-area .goods-item .goods-price a{font-weight:bold; font-size:14px; color:#f03c3c;}
section#goodslist .goods-area .goods-item .goods-dc {width:100%; height:18px; font-size:12px; font-weight:bold; color:#436693; line-height:15px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
section#goodslist .goods-area .goods-item .goods-dc a{font-size:12px; font-weight:bold; color:#436693;}
section#goodslist .goods-area .goods-item .goods-btn {width:100%; height:21px; }
section#goodslist .goods-area .goods-item .goods-btn .del-btn{width:100%; height:21px; width:31px; background:url("/shop/data/skin_mobileV2/default/common/img/nlist/btn_delete_off.png") no-repeat; float:left;}
section#goodslist .goods-area .goods-item .goods-btn .del-btn:active{background:url("/shop/data/skin_mobileV2/default/common/img/nlist/btn_delete_on.png") no-repeat;}
section#goodslist .goods-area .goods-item .goods-btn .cart-order-btn{width:100%; height:21px; width:56px; background:url("/shop/data/skin_mobileV2/default/common/img/nlist/btn_order_off.png") no-repeat; float:right;}
section#goodslist .goods-area .goods-item .goods-btn .cart-order-btn:active{background:url("/shop/data/skin_mobileV2/default/common/img/nlist/btn_order_on.png") no-repeat;}

section#goodslist .right-margin {margin-right:5%;}
section#goodslist .goods-area .more-btn {width:296px; margin:auto; background:url("/shop/data/skin_mobileV2/default/common/img/nlist/btn_more_off.png") no-repeat; text-align:center; height:38px; color:#ffffff; line-height:38px; font-size:14px; margin-top:18px; margin-bottom:18px;}
section#goodslist .goods-area .more-btn:active {background:url("/shop/data/skin_mobileV2/default/common/img/nlist/btn_more_on.png") no-repeat; }

section#goodslist .goods-area .goods-list-item {height:100px;padding:12px;}
section#goodslist .goods-area .goods-list-item-gray {background:#f5f4f5;}
section#goodslist .goods-area .goods-list-item .goods-list-img{width:100px; height:100px; border:solid 1px #dbdbdb; float:left;}
section#goodslist .goods-area .goods-list-item .goods-list-img img{width:100%; height:100%;}
section#goodslist .goods-area .goods-list-item .goods-list-info{float:left; margin-left:10px;max-width:53%;}
section#goodslist .goods-area .goods-list-item .goods-list-info .goods-nm{color:#353535; font-weight:bold; fonst-size:14px; margin-bottom:5px; overflow:hidden;text-overflow:ellipsis;white-space:nowrap; }
section#goodslist .goods-area .goods-list-item .goods-list-info .goods-price{color:#666666; font-size:12px; margin-bottom:2px;}
section#goodslist .goods-area .goods-list-item .goods-list-info .goods-price .red{color:#f03c3c; font-size:14px; font-weight:bold;}
section#goodslist .goods-area .goods-list-item .goods-list-info .goods-dc{color:#666666; font-size:12px; margin-bottom:2px;}
section#goodslist .goods-area .goods-list-item .goods-list-info .goods-dc .blue{color:#436693; font-size:12px; font-weight:bold;}
section#goodslist .goods-area .goods-list-item .goods-list-info .goods-reserve{color:#666666; font-size:12px; margin-bottom:2px;}
section#goodslist .goods-area .goods-list-item .goods-list-info .goods-nvmileage{color:#666666; font-size:12px; margin-bottom:2px;}
section#goodslist .goods-area .goods-list-item .goods-list-arrow{width:15px; height:21px; background:url("/shop/data/skin_mobileV2/default/common/img/nlist/arrow01.png") no-repeat; float:right; margin-top:40px;}

</style>
<script type="text/javascript">
function setViewType(view_type) {
	$.cookie('goods_view_type', view_type);
	document.location.reload();
}

function setSortType(sort_type) {
	$.cookie('sort_type', sort_type);
	document.location.reload();
}

$(document).ready(function(){
	var view_type = $.cookie('goods_view_type');

	if(view_type == 'gallery') {
		$(".view-list").addClass("view-list-disable");
	}
	else {
		$.cookie('goods_view_type', 'list');
		$(".view-gallery").addClass("view-gallery-disable");
	}

	var sort_type = $.cookie('sort_type');
	
	if(sort_type == "undefined" || sort_type == "" || sort_type == null) {
		sort_type = 'sort';
	}

	$("[name=goods_sort]").val(sort_type);

});

</script>
<section id="goodslist" class="content">
	<input type="hidden" name="category" value="<?php echo $TPL_VAR["category"]?>" />
	<input type="hidden" name="kw" value="<?php echo $TPL_VAR["kw"]?>" />
	<input type="hidden" name="item_cnt" value="0" />
	<div class="goods-sort-area">
		<div class="goods-sort">
			<select name="goods_sort" onChange="javascript:setSortType(this.value);">
				<option value="sort">상품정렬</option>
				<option value="regdt">등록순</option>
				<option value="low_price">낮은가격순</option>
				<option value="high_price">높은가격순</option>
			</select>
		</div>
		<div class="goods-view-type">
			<div class="view-list" onClick="javascript:setViewType('list');"></div>
			<div class="view-gallery" onClick="javascript:setViewType('gallery');"></div>
		</div>
	</div>
	<ul class="goods_item_list" id="goods-item-list">
		<li class="more">검색 결과가 없습니다</li>
	</ul>
	<div class="goods-area">
		<div class="goods-content">
			<!--
			<div class="goods-list-item ">
				<div class="goods-list-img"><img src="" /></div>
				<div class="goods-list-info">
					<div class="goods-nm">가슴주머니 반팔T</div>
					<div class="goods-price">상품가격 : <span class="red">28,000원</span></div>
					<div class="goods-dc">모바일할인 : <span class="blue">adfasfd</span></div>
					<div class="goods-reserve">적립금 : 0원</div>
					<div class="goods-nvmileage"></div>
				</div>
				<div class="goods-list-arrow"></div>
			</div>
			-->
		</div>
		<div class="more-btn" onclick="javascript:getGoodsListData();">더보기</div>
	</div>
</section>

<div class="indicator"></div>


<?php $this->print_("footer",$TPL_SCP,1);?>