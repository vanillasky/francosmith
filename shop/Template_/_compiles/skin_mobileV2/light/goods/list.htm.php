<?php /* Template_ 2.2.7 2013/07/22 16:59:57 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/light/goods/list.htm 000008274 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<?php if($TPL_VAR["page_title"]){?>
<?php $this->print_("sub_header",$TPL_SCP,1);?>

<?php }?>

<style type="text/css">
section#goodslist .goods-sort-area {height:51px; padding:0px 10px; background:#FFFFFF;border-bottom:solid 1px #dbdcde;}
section#goodslist .goods-sort-area .goods-sort {float:left;}
section#goodslist .goods-sort-area .goods-sort select{height:26px; margin-top:12px;}
section#goodslist .goods-sort-area .goods-view-type {float:right; margin-top:11px;}
section#goodslist .goods-sort-area .goods-view-type .view-list{width:30px; height:29px; background:url('/shop/data/skin_mobileV2/light/common/img/new/btn_category_list_on.png'); background-size:30px 29px;float:left;}
section#goodslist .goods-sort-area .goods-view-type .view-list-disable {background:url('/shop/data/skin_mobileV2/light/common/img/new/btn_category_list.png');background-size:30px 29px; float:left; }
section#goodslist .goods-sort-area .goods-view-type .view-gallery{width:30px; height:29px; background:url('/shop/data/skin_mobileV2/light/common/img/new/btn_category_gallery_on.png');background-size:30px 29px;float:left;}
section#goodslist .goods-sort-area .goods-view-type .view-gallery-disable {background:url('/shop/data/skin_mobileV2/light/common/img/new/btn_category_gallery.png');background-size:30px 29px;float:left;}


section#goodslist {background:#FFFFFF; }
section#goodslist .goods-area { height:100%;}
section#goodslist .goods-area .goods-row{clear:both; margin:auto; min-width:296px; padding:12px;}
section#goodslist .goods-area .goods-item{width:30%; margin-bottom:18px; display:block; float:left; min-width:87px;}
section#goodslist .goods-area .goods-item .goods-img{width:100%; text-align:center;}
section#goodslist .goods-area .goods-item .goods-img img{width:100%; margin:auto; border:solid 1px #dbdbdb;}
section#goodslist .goods-area .goods-item .goods-nm {width:100%; font-size:12px; color:#929ca8; margin-top:5px;text-align:center; word-break:break-all;}
section#goodslist .goods-area .goods-item .goods-nm a{font-size:12px; color:#929ca8;}
section#goodslist .goods-area .goods-item .goods-price {font-weight:bold;width:100%; height:18px; font-size:13px; color:#222222; line-height:18px; text-align:center;}
section#goodslist .goods-area .goods-item .goods-price a{font-weight:bold; font-size:13px; color:#222222;}
section#goodslist .goods-area .goods-item .goods-dc {width:100%; height:18px; font-size:12px; font-weight:bold; color:#436693; line-height:15px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;}
section#goodslist .goods-area .goods-item .goods-dc a{font-size:12px; font-weight:bold; color:#436693;}
section#goodslist .goods-area .goods-item .goods-btn {width:100%; height:21px; }
section#goodslist .goods-area .goods-item .goods-btn .del-btn{width:100%; height:21px; width:31px; background:url("/shop/data/skin_mobileV2/light/common/img/nlist/btn_delete_off.png") no-repeat; float:left;}
section#goodslist .goods-area .goods-item .goods-btn .del-btn:active{background:url("/shop/data/skin_mobileV2/light/common/img/nlist/btn_delete_on.png") no-repeat;}
section#goodslist .goods-area .goods-item .goods-btn .cart-order-btn{width:100%; height:21px; width:56px; background:url("/shop/data/skin_mobileV2/light/common/img/nlist/btn_order_off.png") no-repeat; float:right;}
section#goodslist .goods-area .goods-item .goods-btn .cart-order-btn:active{background:url("/shop/data/skin_mobileV2/light/common/img/nlist/btn_order_on.png") no-repeat;}

section#goodslist .right-margin {margin-right:5%;}
section#goodslist .goods-area .more-btn {width:300px; margin:auto; text-align:center; height:35px; color:#ffffff; line-height:35px; font-size:15px; font-weight:bold; background:#808591; border-radius:3px; font-family:dotum; margin-top:15px;}

section#goodslist .goods-area .goods-list-item {height:100px;padding:10px; height:87px; border-bottom:solid 1px #e5e5e5;}
section#goodslist .goods-area .goods-list-item-gray {background:#FFFFFF;}
section#goodslist .goods-area .goods-list-item .goods-list-img{width:100px; height:100px; border:solid 1px #dbdbdb; float:left; width:80px; height:80px;}
section#goodslist .goods-area .goods-list-item .goods-list-img img{width:100%; height:100%;}
section#goodslist .goods-area .goods-list-item .goods-list-info{float:left; margin-left:10px;max-width:53%;}
section#goodslist .goods-area .goods-list-item .goods-list-info .goods-nm{color:#222222; font-weight:bold; fonst-size:13px; margin-bottom:5px; overflow:hidden;text-overflow:ellipsis;white-space:nowrap; height:18px;}
section#goodslist .goods-area .goods-list-item .goods-list-info .goods-price{color:#666666; font-size:12px; margin-bottom:2px; height:18px;}
section#goodslist .goods-area .goods-list-item .goods-list-info .goods-price .red{color:#f03c3c; font-size:12px; font-weight:bold;}
section#goodslist .goods-area .goods-list-item .goods-list-info .goods-dc{color:#61656d; font-size:12px; margin-bottom:2px; height:18px;}
section#goodslist .goods-area .goods-list-item .goods-list-info .goods-dc .blue{color:#436693; font-size:12px; font-weight:bold;}
section#goodslist .goods-area .goods-list-item .goods-list-info .goods-reserve{color:#61656d; font-size:12px; margin-bottom:2px; height:18px;}
section#goodslist .goods-area .goods-list-item .goods-list-info .goods-nvmileage{color:#61656d; font-size:12px; margin-bottom:2px;}
section#goodslist .goods-area .goods-list-item .goods-list-arrow{width:12px; height:20px; background:url("/shop/data/skin_mobileV2/light/common/img/new/category_list_next.png") no-repeat; background-size:12px 20px;float:right; margin-top:34px; margin-right:10px;}

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