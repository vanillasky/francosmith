<?php /* Template_ 2.2.7 2013/05/28 17:53:01 /www/francotr3287_godo_co_kr/shop/data/skin_mobileV2/default/goods/view_bigimg.htm 000002553 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<?php $this->print_("sub_header",$TPL_SCP,1);?>


<script type="text/javascript">
var strprice = "<?php echo $TPL_VAR["strprice"]?>";

$(document).ready(function(){
	$("meta[name=viewport]").attr("content", "user-scalable=yes, initial-scale=1.0, maximum-scale=10.0, minimum-scale=1.0, width=device-width, height=device-height");
});

</script>
<style type="text/css">
.goods_price2 {height:20px;line-height:20px;text-align:right;}
.goods_dc {height:20px;line-height:20px;text-align:right;color:#88eeff;}

section#goodsbigimg {background:#FFFFFF;}
section#goodsbigimg .top_title{clear:both; height:40px;background:url('/shop/data/skin_mobileV2/default/common/img/myp/name_bg.png') repeat-x; line-height:40px; padding-left:10px; color:#FFFFFF; font-size:16px; font-weight:bold; text-align:center;}
section#goodsbigimg .top_title .back_btn{float:left; background:url('/shop/data/skin_mobileV2/default/common/img/detailp/btn_back_off.png') no-repeat; width:45px; height:27px; margin-top:7px; position:absolute;}
section#goodsbigimg .top_title .back_btn:active{background:url('/shop/data/skin_mobileV2/default/common/img/detailp/btn_back_on.png') no-repeat;}
section#goodsbigimg .img-area {padding:0px 12px 12px 12px; }
section#goodsbigimg .img-area .thumbnail-area {border:solid 1px #d9d9d9;}
section#goodsbigimg .img-area .img-area-info{height:40px; text-align:center; font-size:12px; color:#353535; line-height:40px;}

</style>
<form name="frmView" method="post" onsubmit="return false;">
	<input type="hidden" name="mode" value="" />
	<input type="hidden" name="goodsno" value="<?php echo $TPL_VAR["goodsno"]?>" />
	<input type="hidden" name="goodsCoupon" value="<?php echo $TPL_VAR["coupon"]?>" />
	<input type="hidden" name="ea" value="" />
	<input type="hidden" name="opt[]" value="" />
	<input type="hidden" name="addopt[]" value="" />
</form>


<section id="goodsbigimg" class="content">
	<div class="top_title">
		<div class="back_btn" onClick="javascript:history.go(-1);"></div>
		<div class="goods_nm">상품이미지 확대보기</div>
	</div>
	<div class="img-area">
		<div class="img-area-info">
			상품이미지를 확대하실 수 있습니다.
		</div>
		<div class="thumbnail-area">
		<?php echo goodsimgMobile($TPL_VAR["l_img"][ 0])?>

		</div>

	</div>

</section>



<?php $this->print_("footer",$TPL_SCP,1);?>