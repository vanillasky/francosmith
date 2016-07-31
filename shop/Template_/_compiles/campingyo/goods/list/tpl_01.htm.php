<?php /* Template_ 2.2.7 2014/03/05 23:19:27 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/goods/list/tpl_01.htm 000003026 */ 
if (is_array($TPL_VAR["loop"])) $TPL_loop_1=count($TPL_VAR["loop"]); else if (is_object($TPL_VAR["loop"]) && in_array("Countable", class_implements($TPL_VAR["loop"]))) $TPL_loop_1=$TPL_VAR["loop"]->count();else $TPL_loop_1=0;?>
<?php if(!$TPL_VAR["id"]){?><?  $TPL_VAR["id"] = "es_".md5(crypt('')); ?><?php }?>
<!-- 상품 리스트 -->
<table width=100% border=0 cellpadding=0 cellspacing=0>
<tr><td height=10></td></tr>
<tr>
<?php if($TPL_loop_1){$TPL_I1=-1;foreach($TPL_VAR["loop"] as $TPL_V1){$TPL_I1++;?>
<?php if($TPL_I1&&$TPL_I1%$TPL_VAR["cols"]== 0){?></tr><tr><td height=10></td></tr><!-- <tr><td colspan=<?php echo $TPL_VAR["cols"]?> bgcolor=#E6E6E6 height=1></td></tr> --><tr><td height=10></td></tr><tr><?php }?>
	<td align=center valign=top width="<?php echo  100/$TPL_VAR["cols"]?>%">

	<div><a href="<?php echo $TPL_V1["goods_view_url"]?>"><?php echo goodsimg($TPL_V1["img_s"],$TPL_VAR["size"],'class="'.$TPL_V1["css_selector"].'"')?></a></div>
<?php if($TPL_V1["soldout_icon"]){?><div style="padding:3px"><?php if($TPL_V1["soldout_icon"]=='custom'){?><img src="../data/goods/icon/custom/soldout_icon"><?php }else{?><img src="/shop/data/skin/campingyo/img/icon/good_icon_soldout.gif"><?php }?></div><?php }?>
<?php if($TPL_V1["goodsnm"]){?><div style="padding:5"><a href="<?php echo $TPL_V1["goods_view_url"]?>"><?php echo $TPL_V1["goodsnm"]?></a></div><?php }?>
<?php if(!$TPL_V1["strprice"]){?>
<?php if($TPL_V1["price"]){?>
<?php if($TPL_V1["consumer"]){?><strike><?php echo number_format($TPL_V1["consumer"])?></strike>↓<?php }?>
		<div style="padding-bottom:3px"><b><?php echo number_format($TPL_V1["price"])?>원</b> <?php if($TPL_V1["special_discount_amount"]){?><img src="/shop/data/skin/campingyo/img/icon/goods_special_discount.gif"><?php }?></div>
<?php }?>
<?php if($TPL_V1["soldout_price_string"]){?><?php echo $TPL_V1["soldout_price_string"]?><?php }?>
<?php if($TPL_V1["soldout_price_image"]){?><?php echo $TPL_V1["soldout_price_image"]?><?php }?>
<?php }else{?><?php echo $TPL_V1["strprice"]?>

<?php }?>
<?php if($TPL_V1["icon"]){?><div><?php echo $TPL_V1["icon"]?></div><?php }?>
<?php if($TPL_V1["coupon"]){?><div class=eng><b style="color:red"><?php echo $TPL_V1["coupon"]?><font class=small>원</font></b> <img src="/shop/data/skin/campingyo/img/icon/good_icon_coupon.gif" align=absmiddle></div><?php }?>
	</td>
<?php }}?>

</tr>
<tr><td height=10></td></tr>
</table>

<!-- 품절상품 마스크 -->
<div id="el-goods-soldout-image-mask" style="display:none;position:absolute;top:0;left:0;background:url(<?php if($GLOBALS["cfg_soldout"]["display_overlay"]=='custom'){?>../data/goods/icon/custom/soldout_overlay<?php }else{?>../data/goods/icon/icon_soldout<?php echo $GLOBALS["cfg_soldout"]["display_overlay"]?><?php }?>) no-repeat center center;"></div>
<script>
addOnloadEvent(function(){ setGoodsImageSoldoutMask() });
</script>