<?php /* Template_ 2.2.7 2014/07/23 17:03:45 /www/francotr3287_godo_co_kr/shop/data/skin/standard/goods/list/tpl_01.htm 000003290 */ 
if (is_array($TPL_VAR["loop"])) $TPL_loop_1=count($TPL_VAR["loop"]); else if (is_object($TPL_VAR["loop"]) && in_array("Countable", class_implements($TPL_VAR["loop"]))) $TPL_loop_1=$TPL_VAR["loop"]->count();else $TPL_loop_1=0;?>
<?php if(!$TPL_VAR["id"]){?><?  $TPL_VAR["id"] = "es_".md5(crypt('')); ?><?php }?>
<!-- 상품 리스트 -->
<table width=100% border=0 cellpadding="0" cellspacing="0">
<tr>
<?php if($TPL_loop_1){$TPL_I1=-1;foreach($TPL_VAR["loop"] as $TPL_V1){$TPL_I1++;?>
<?php if($TPL_I1&&$TPL_I1%$TPL_VAR["cols"]== 0){?></tr><tr><?php }?>
	<td align=center valign=top width="<?php echo  100/$TPL_VAR["cols"]?>%" style="padding-bottom:25px;">

	<div style="text-align:center;"><a href="<?php echo $TPL_V1["goods_view_url"]?>"><?php echo goodsimg($TPL_V1["img_s"],$TPL_VAR["size"],'class="'.$TPL_V1["css_selector"].'"')?></a></div>
	<div style="padding:14px 0 0 6px; text-align:left; width:<?php echo $TPL_VAR["size"]?>px;">
<?php if($TPL_V1["soldout_icon"]){?><div style="padding:3px 0;"><?php if($TPL_V1["soldout_icon"]=='custom'){?><img src="../data/goods/icon/custom/soldout_icon"><?php }else{?><img src="/shop/data/skin/standard/img/icon/good_icon_soldout.gif"><?php }?></div><?php }?>
<?php if($TPL_V1["goodsnm"]){?><div><a href="<?php echo $TPL_V1["goods_view_url"]?>" class="pname"><?php echo $TPL_V1["goodsnm"]?></a></div><?php }?>
<?php if($TPL_V1["shortdesc"]){?><div><a href="<?php echo $TPL_V1["goods_view_url"]?>" class="pname2"><?php echo $TPL_V1["shortdesc"]?></a></div><?php }?>
<?php if(!$TPL_V1["strprice"]){?>
<?php if($TPL_V1["price"]){?>
<?php if($TPL_V1["consumer"]){?><a href="<?php echo $TPL_V1["goods_view_url"]?>" class="pname"><strike style="font-size:11px;"><?php echo number_format($TPL_V1["consumer"])?></strike>↓</a><?php }?>
		<div style="padding-bottom:3px;"><a href="<?php echo $TPL_V1["goods_view_url"]?>" class="pprice"><b><?php echo number_format($TPL_V1["price"])?></b></a> <?php if($TPL_V1["special_discount_amount"]){?><img src="/shop/data/skin/standard/img/icon/goods_special_discount.gif"><?php }?></div>
<?php }?>
<?php if($TPL_V1["soldout_price_string"]){?><?php echo $TPL_V1["soldout_price_string"]?><?php }?>
<?php if($TPL_V1["soldout_price_image"]){?><?php echo $TPL_V1["soldout_price_image"]?><?php }?>
<?php }else{?><?php echo $TPL_V1["strprice"]?>

<?php }?>
<?php if($TPL_V1["icon"]){?><div><?php echo $TPL_V1["icon"]?></div><?php }?>
<?php if($TPL_V1["coupon"]){?><div class=eng><b style="color:red"><?php echo $TPL_V1["coupon"]?><font class=small>원</font></b> <img src="/shop/data/skin/standard/img/icon/good_icon_coupon.gif" align=absmiddle></div><?php }?>
	</div>
	</td>
<?php }}?>
</tr>
</table>

<!-- 품절상품 마스크 -->
<div id="el-goods-soldout-image-mask" style="display:none;position:absolute;top:0;left:0;background:url(<?php if($GLOBALS["cfg_soldout"]["display_overlay"]=='custom'){?>../data/goods/icon/custom/soldout_overlay<?php }else{?>../data/goods/icon/icon_soldout<?php echo $GLOBALS["cfg_soldout"]["display_overlay"]?><?php }?>) no-repeat center center;"></div>
<script>
addOnloadEvent(function(){ setGoodsImageSoldoutMask() });
</script>