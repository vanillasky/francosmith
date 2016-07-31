<?php /* Template_ 2.2.7 2016/07/30 17:37:27 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/goods/list/tpl_01.htm 000006158 */ 
if (is_array($TPL_VAR["loop"])) $TPL_loop_1=count($TPL_VAR["loop"]); else if (is_object($TPL_VAR["loop"]) && in_array("Countable", class_implements($TPL_VAR["loop"]))) $TPL_loop_1=$TPL_VAR["loop"]->count();else $TPL_loop_1=0;?>
<?php if(!$TPL_VAR["id"]){?><?  $TPL_VAR["id"] = "es_".md5(crypt('')); ?><?php }?>
<!-- 상품 리스트 -->
<script type="text/javascript">
function fnPreviewGoods_<?php echo $TPL_VAR["id"]?>(goodsno) {
	//popup('../goods/goods_view.php?goodsno='+goodsno+'&preview=y','800','450');
	wopen('../goods/goods_view.php?goodsno='+goodsno+'&preview=y', '860', '700', "QuickView");
}
</script>

<table id="goods-grid" width=100% cellpadding=0 cellspacing="0">
<tr>
<?php if($TPL_loop_1){$TPL_I1=-1;foreach($TPL_VAR["loop"] as $TPL_V1){$TPL_I1++;?>
<?php if($TPL_I1&&$TPL_I1%$TPL_VAR["cols"]== 0){?></tr><tr><?php }?>
	<td align=center valign=top width="<?php echo  100/$TPL_VAR["cols"]- 1?>%" >
		<div class="goods_grid">
			
    <?php if(strpos( $TPL_V1["icon"], 'icon_sale.gif') || strpos( $TPL_V1["icon"], 'my_icon_142458133510')) : ?>
		<div class="goods_dp_tag">
			<a href="<?php echo $TPL_V1["goods_view_url"]?>"><?php echo goodsimg($TPL_V1["img_s"],$TPL_VAR["size"],'class="'.$TPL_V1["css_selector"].' goods-circle-icon"')?><img src="/shop/data/skin/freemart/img/icon/sale-circle-icon.png" class="dp_tag-right" ></a>
		</div>
    <?php elseif(strpos( $TPL_V1["css_selector"], 'soldout-image'))  : ?>
    	<div class="goods_dp_tag"><a href="<?php echo $TPL_V1["goods_view_url"]?>"><?php echo goodsimg($TPL_V1["img_s"],$TPL_VAR["size"],'class="'.$TPL_V1["css_selector"].' goods-circle-icon"')?><img src="/shop/data/skin/freemart/img/icon/soldout-circle-icon.png" class="dp_tag-right" ></a></div>
    <?php elseif(strpos( $TPL_V1["icon"], 'icon_reserve'))  : ?>
		<div class="goods_dp_tag"><a href="<?php echo $TPL_V1["goods_view_url"]?>"><?php echo goodsimg($TPL_V1["img_s"],$TPL_VAR["size"],'class="'.$TPL_V1["css_selector"].' goods-circle-icon"')?><img src="/shop/data/skin/freemart/img/icon/reserve.png" class="dp_tag-right" ></a></div>
	<?php elseif(strpos( $TPL_V1["icon"], 'good_icon_new'))  : ?>
		<div class="goods_dp_tag"><a href="<?php echo $TPL_V1["goods_view_url"]?>"><?php echo goodsimg($TPL_V1["img_s"],$TPL_VAR["size"],'class="'.$TPL_V1["css_selector"].' goods-circle-icon"')?><img src="/shop/data/skin/freemart/img/icon/new-circle-icon.png" class="dp_tag-right" ></a></div>
	<?php elseif(strpos( $TPL_V1["icon"], 'good_icon_event'))  : ?>
		<div class="goods_dp_tag"><a href="<?php echo $TPL_V1["goods_view_url"]?>"><?php echo goodsimg($TPL_V1["img_s"],$TPL_VAR["size"],'class="'.$TPL_V1["css_selector"].' goods-circle-icon"')?><img src="/shop/data/skin/freemart/img/icon/event.png" class="dp_tag-right" ></a></div>	
	<?php else: ?>
		<div><a href="<?php echo $TPL_V1["goods_view_url"]?>"><?php echo goodsimg($TPL_V1["img_s"],$TPL_VAR["size"],'class="'.$TPL_V1["css_selector"].'"')?></a></div>
	<?php endif; ?>	
    
		<div class="goods-price"">
<?php if($TPL_V1["soldout_icon"]){?><div style="padding:3px 0;"><?php if($TPL_V1["soldout_icon"]=='custom'){?><img src="../data/goods/icon/custom/soldout_icon"><?php }else{?><img src="/shop/data/skin/freemart/img/icon/good_icon_soldout.gif"><?php }?></div><?php }?>
<?php if($TPL_V1["goodsnm"]){?><div class="list-goods-name"><a href="<?php echo $TPL_V1["goods_view_url"]?>"><?php echo $TPL_V1["goodsnm"]?></a></div><?php }?>
<?php if($TPL_V1["shortdesc"]){?><div><a href="<?php echo $TPL_V1["goods_view_url"]?>"><?php echo $TPL_V1["shortdesc"]?></a></div><?php }?>
<?php if(!$TPL_V1["strprice"]){?>
			
<?php if($TPL_V1["goodsDiscountPrice"]){?>
<?php if($TPL_V1["oriPrice"]){?><a href="<?php echo $TPL_V1["goods_view_url"]?>" class="pname"><strike style="font-size:11px;"><?php echo number_format($TPL_V1["oriPrice"])?></strike>↓</a><?php }?>
			<div style="padding-bottom:3px;"><a href="<?php echo $TPL_V1["goods_view_url"]?>" class="pprice"><b><?php echo number_format($TPL_V1["goodsDiscountPrice"])?></b></a> <?php if($TPL_V1["special_discount_amount"]){?><img src="/shop/data/skin/freemart/img/icon/goods_special_discount.gif"><?php }?></div>
<?php }else{?>
<?php if($TPL_V1["price"]){?>
<?php if($TPL_V1["consumer"]){?><a href="<?php echo $TPL_V1["goods_view_url"]?>" class="pname"><strike style="font-size:11px;"><?php echo number_format($TPL_V1["consumer"])?></strike>↓</a><?php }?>
				<div style="padding-bottom:3px;"><a href="<?php echo $TPL_V1["goods_view_url"]?>" class="pprice"><b><?php echo number_format($TPL_V1["price"])?></b></a> <?php if($TPL_V1["special_discount_amount"]){?><img src="/shop/data/skin/freemart/img/icon/goods_special_discount.gif"><?php }?></div>
<?php }?>
	
<?php }?>
<?php if($TPL_V1["soldout_price_string"]){?><?php echo $TPL_V1["soldout_price_string"]?><?php }?>
<?php if($TPL_V1["soldout_price_image"]){?><?php echo $TPL_V1["soldout_price_image"]?><?php }?>
<?php }else{?><?php echo $TPL_V1["strprice"]?>

<?php }?>
<?php if($TPL_V1["icon"]){?><!--<div><?php echo $TPL_V1["icon"]?></div>--><?php }?>
<?php if($TPL_V1["coupon"]){?><div class=eng><b style="color:red"><?php echo $TPL_V1["coupon"]?><font class=small>원</font></b> <img src="/shop/data/skin/freemart/img/icon/good_icon_coupon.gif" align=absmiddle></div><?php }?>
		</div>
		
		<div class="quick-buy">
				<button class="button-small button-grey" onclick="fnPreviewGoods_<?php echo $TPL_VAR["id"]?>(<?php echo $TPL_V1["goodsno"]?>);">Quick View</button>
		</div>
	</div>
	</td>
<?php }}?>
</tr>
</table>

<!-- 품절상품 마스크 -->
<div id="el-goods-soldout-image-mask" style="display:none;position:absolute;top:0;left:0;background:url(<?php if($GLOBALS["cfg_soldout"]["display_overlay"]=='custom'){?>../data/goods/icon/custom/soldout_overlay<?php }else{?>../data/goods/icon/icon_soldout<?php echo $GLOBALS["cfg_soldout"]["display_overlay"]?><?php }?>) no-repeat center center;"></div>
<script>
//addOnloadEvent(function(){ setGoodsImageSoldoutMask() });
</script>