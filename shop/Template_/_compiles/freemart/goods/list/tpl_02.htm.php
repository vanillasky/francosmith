<?php /* Template_ 2.2.7 2016/05/03 15:15:10 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/goods/list/tpl_02.htm 000003987 */ 
if (is_array($TPL_VAR["loop"])) $TPL_loop_1=count($TPL_VAR["loop"]); else if (is_object($TPL_VAR["loop"]) && in_array("Countable", class_implements($TPL_VAR["loop"]))) $TPL_loop_1=$TPL_VAR["loop"]->count();else $TPL_loop_1=0;?>
<!-- 상품 리스트 -->
<table width=100%>
<col width=10>
<col style="padding:10px">
<col width=80px align=right>
<col width=100px align=right>
<tr>
<?php if($TPL_loop_1> 0&&$TPL_VAR["page_type"]=='search'){?>
<th colspan="2" style="padding:0;height:20px;" bgcolor="#9e9e9e"><font color="#FFFFFF">상품정보</font></th>
<th style="height:20px; text-align:center" bgcolor="#9e9e9e"><font color="#FFFFFF">적립금</font></th>
<th style="height:20px; text-align:center" bgcolor="#9e9e9e"><font color="#FFFFFF">판매가</font></th>
<?php }else{?>
<td colspan="4"></td>
<?php }?>
</tr>
<?php if($TPL_loop_1){$TPL_I1=-1;foreach($TPL_VAR["loop"] as $TPL_V1){$TPL_I1++;?>
<tr>
	<td><a href="<?php echo $TPL_V1["goods_view_url"]?>"><?php echo goodsimg($TPL_V1["img_s"],$TPL_VAR["size"],'class="'.$TPL_V1["css_selector"].'"')?></a></td>
	<td style="text-align:left">
<?php if($TPL_V1["coupon"]){?><div class=eng><b style="color:red"><?php echo $TPL_V1["coupon"]?></b> <img src="/shop/data/skin/freemart/img/icon/good_icon_coupon.gif" align=absmiddle></div><?php }?>
<?php if($TPL_V1["brandnm"]){?><div style="padding:5px 0" class=stxt><b><?php echo $TPL_V1["brandnm"]?></b></div><?php }?>
<?php if($TPL_V1["goodsnm"]){?><div style="padding:5px 0;text-align:left;"><a href="<?php echo $TPL_V1["goods_view_url"]?>"><?php echo $TPL_V1["goodsnm"]?></a></div><?php }?>
<?php if($TPL_V1["shortdesc"]){?><div style="padding-bottom:5px" class=stxt><?php echo nl2br($TPL_V1["shortdesc"])?></div><?php }?>
	
<?php if($TPL_V1["soldout_icon"]){?><div style="padding:3px"><?php if($TPL_V1["soldout_icon"]=='custom'){?><img src="../data/goods/icon/custom/soldout_icon"><?php }else{?><img src="/shop/data/skin/freemart/img/icon/good_icon_soldout.gif"><?php }?></div><?php }?>
<?php if($TPL_V1["icon"]){?><?php echo $TPL_V1["icon"]?><?php }?>
	</td>
	<td><?php echo number_format($TPL_V1["reserve"])?>원</td>
	<td>
<?php if(!$TPL_V1["strprice"]){?>
<?php if($TPL_V1["goodsDiscountPrice"]){?>
<?php if($TPL_V1["oriPrice"]){?><div style="color:gray"><strike><?php echo number_format($TPL_V1["oriPrice"])?></strike> ↓</div><?php }?>
		<b style="color:#C94C00"><?php echo number_format($TPL_V1["goodsDiscountPrice"])?>원</b> <?php if($TPL_V1["special_discount_amount"]){?><img src="/shop/data/skin/freemart/img/icon/goods_special_discount.gif"><?php }?>
<?php }else{?>
<?php if($TPL_V1["price"]){?>
<?php if($TPL_V1["consumer"]){?><div style="color:gray"><strike><?php echo number_format($TPL_V1["consumer"])?></strike> ↓</div><?php }?>
			<b style="color:#C94C00"><?php echo number_format($TPL_V1["price"])?>원</b> <?php if($TPL_V1["special_discount_amount"]){?><img src="/shop/data/skin/freemart/img/icon/goods_special_discount.gif"><?php }?>
<?php }?>
<?php }?>
<?php if($TPL_V1["soldout_price_string"]){?><?php echo $TPL_V1["soldout_price_string"]?><?php }?>
<?php if($TPL_V1["soldout_price_image"]){?><?php echo $TPL_V1["soldout_price_image"]?><?php }?>
<?php }else{?><?php echo $TPL_V1["strprice"]?>

<?php }?>
	</td>
</tr>
<?php if($TPL_I1!=$TPL_loop_1- 1){?><tr><td height=1 bgcolor=#efefef colspan=10></td></tr><?php }?>
<?php }}?>
</table>

<!-- 품절상품 마스크 -->
<div id="el-goods-soldout-image-mask" style="display:none;position:absolute;top:0;left:0;background:url(<?php if($GLOBALS["cfg_soldout"]["display_overlay"]=='custom'){?>../data/goods/icon/custom/soldout_overlay<?php }else{?>../data/goods/icon/icon_soldout<?php echo $GLOBALS["cfg_soldout"]["display_overlay"]?><?php }?>) no-repeat center center;"></div>
<script>
addOnloadEvent(function(){ setGoodsImageSoldoutMask() });
</script>