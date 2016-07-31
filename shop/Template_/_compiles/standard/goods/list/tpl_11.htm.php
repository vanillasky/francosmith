<?php /* Template_ 2.2.7 2014/07/23 17:03:45 /www/francotr3287_godo_co_kr/shop/data/skin/standard/goods/list/tpl_11.htm 000002474 */ 
if (is_array($TPL_VAR["loop"])) $TPL_loop_1=count($TPL_VAR["loop"]); else if (is_object($TPL_VAR["loop"]) && in_array("Countable", class_implements($TPL_VAR["loop"]))) $TPL_loop_1=$TPL_VAR["loop"]->count();else $TPL_loop_1=0;?>
<script>
function fnPreviewGoods_<?php echo $TPL_VAR["id"]?>(goodsno) {
	popup('../goods/goods_view.php?goodsno='+goodsno+'&preview=y','800','450');

}
</script>

<!-- 상품 리스트 -->
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<?php if($TPL_loop_1){$TPL_I1=-1;foreach($TPL_VAR["loop"] as $TPL_V1){$TPL_I1++;?>
<?php if($TPL_I1&&$TPL_I1%$TPL_VAR["cols"]== 0){?></tr><tr><td height="10"></td></tr><tr><td height="10"></td></tr><tr><?php }?>
	<td align="center" valign="top" width="<?php echo  100/$TPL_VAR["cols"]?>%">
<?php if($TPL_V1["goodsnm"]){?><div><a href="<?php echo $TPL_V1["goods_view_url"]?>" title="<?php echo $TPL_V1["goodsnm"]?>"><?php echo goodsimg($TPL_V1["img_s"],$TPL_VAR["size"],'class="'.$TPL_V1["css_selector"].'"')?></a></div><?php }?>
	<div>
		<a href="javascript:void(0);" onClick="fnPreviewGoods_<?php echo $TPL_VAR["id"]?>(<?php echo $TPL_V1["goodsno"]?>);"><img src="/shop/data/skin/standard/img/common/icon_basket.gif"></a>
	</div>

<?php if($TPL_V1["soldout_icon"]){?><div style="padding:3px"><?php if($TPL_V1["soldout_icon"]=='custom'){?><img src="../data/goods/icon/custom/soldout_icon"><?php }else{?><img src="/shop/data/skin/standard/img/icon/good_icon_soldout.gif"><?php }?></div><?php }?>
<?php if($TPL_V1["icon"]){?><div><?php echo $TPL_V1["icon"]?></div><?php }?>
<?php if($TPL_V1["coupon"]){?><div class="eng"><b style="color:red"><?php echo $TPL_V1["coupon"]?><font class="small">원</font></b> <img src="/shop/data/skin/standard/img/icon/good_icon_coupon.gif" align="absmiddle"></div><?php }?>
	</td>
<?php }}?>
</tr>
<tr><td height="10"></td></tr>
</table>


<!-- 품절상품 마스크 -->
<div id="el-goods-soldout-image-mask" style="display:none;position:absolute;top:0;left:0;background:url(<?php if($GLOBALS["cfg_soldout"]["display_overlay"]=='custom'){?>../data/goods/icon/custom/soldout_overlay<?php }else{?>../data/goods/icon/icon_soldout<?php echo $GLOBALS["cfg_soldout"]["display_overlay"]?><?php }?>) no-repeat center center;"></div>
<script>
addOnloadEvent(function(){ setGoodsImageSoldoutMask() });
</script>