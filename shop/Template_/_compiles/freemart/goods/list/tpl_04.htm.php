<?php /* Template_ 2.2.7 2016/04/08 03:38:43 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/goods/list/tpl_04.htm 000004482 */ 
if (is_array($TPL_VAR["loop"])) $TPL_loop_1=count($TPL_VAR["loop"]); else if (is_object($TPL_VAR["loop"]) && in_array("Countable", class_implements($TPL_VAR["loop"]))) $TPL_loop_1=$TPL_VAR["loop"]->count();else $TPL_loop_1=0;?>
<?php if(!$TPL_VAR["id"]){?><?  $TPL_VAR["id"] = "es_".md5(crypt('')); ?><?php }?>
<style>
#slide { width:505px; height:243px; float:left; }
#slide #slide_img { float:left; }
#slide #slide_text { float:left; padding:78px 0 0 23px; text-align:left; }
#slide #slide_text .goods_name { font-family:Tahoma, Geneva, sans-serif; font-size:12px; color:#333; }
#slide #slide_text .goods_shortdesc { font-size:11px; color:#a4a4a4; }
#slide #slide_text .goods_price { font-family:Tahoma, Geneva, sans-serif; font-size:11px; font-weight:bold; color:#ed5d55; padding-top:22px; }
</style>
<script>
var <?php echo $TPL_VAR["id"]?> = new eScroll;
<?php echo $TPL_VAR["id"]?>.id = "scrolling_<?php echo $TPL_VAR["id"]?>";
<?php echo $TPL_VAR["id"]?>.mode = "left";
<?php echo $TPL_VAR["id"]?>.line = <?php echo $TPL_VAR["cols"]+ 0?>;
<?php echo $TPL_VAR["id"]?>.width = 505;
<?php echo $TPL_VAR["id"]?>.height = 245;
<?php echo $TPL_VAR["id"]?>.align = "center";
<?php echo $TPL_VAR["id"]?>.valign = "top";
<?php echo $TPL_VAR["id"]?>.direction = ("<?php echo $TPL_VAR["dpCfg"]["dOpt4"]?>" === "1") ? 1 : -1;
<?php if($TPL_loop_1){foreach($TPL_VAR["loop"] as $TPL_V1){?>
var tmp = "\
<div id='slide'>\
<div id='slide_img'><a href='<?php echo $TPL_V1["goods_view_url"]?>' target=_parent><?php echo goodsimg($TPL_V1["img_s"],$TPL_VAR["size"],addslashes('class="'.$TPL_V1["css_selector"].'"'." style='border:1 solid #dfdfdf'"))?></a></div>\
<div id='slide_text'>\
<?php if($TPL_V1["goodsnm"]){?><div><a href='<?php echo $TPL_V1["goods_view_url"]?>' target=_parent class='goods_name'><?php echo addslashes($TPL_V1["goodsnm"])?></a></div><?php }?>\
<?php if($TPL_V1["shortdesc"]){?><div style='padding-top:10px;'><a href='<?php echo $TPL_V1["goods_view_url"]?>' target=_parent class='goods_shortdesc'><?php echo addslashes($TPL_V1["shortdesc"])?></a></div><?php }?>\
<?php if(!$TPL_V1["strprice"]){?><?php if($TPL_V1["price"]){?><div class='goods_price'><b><?php echo number_format($TPL_V1["price"])?>원</b><?php if($TPL_V1["special_discount_amount"]){?><img src='/shop/data/skin/freemart/img/icon/goods_special_discount.gif'><?php }?></div><?php }?><?php if($TPL_V1["soldout_price_string"]){?><?php echo addslashes($TPL_V1["soldout_price_string"])?><?php }?><?php if($TPL_V1["soldout_price_image"]){?><?php echo addslashes($TPL_V1["soldout_price_image"])?><?php }?><?php }else{?><?php echo $TPL_V1["strprice"]?><?php }?><?php if($TPL_V1["icon"]){?><div style='padding-right:5px;'><?php echo $TPL_V1["icon"]?></div><?php }?>\
<?php if($TPL_V1["coupon"]){?><div class=eng><b style='color:red'><?php echo $TPL_V1["coupon"]?><font class=small><b>원</b></font></b> <img src='/shop/data/skin/freemart/img/icon/good_icon_coupon.gif' align=absmiddle></div><?php }?>\
</div>\
</div>\
";
<?php echo $TPL_VAR["id"]?>.add(tmp);
<?php }}?>
</script>

<table width='659px' cellpadding='0' cellspacing='0' border='1' style="padding:48px 21px">
<tr align='center'>
<td valign="top" ><div style="position:relative;"><div style="position:absolute;left:-1px;top:90px;z-index:10"><img src="/shop/data/skin/freemart/img/main/arrow_left.gif" onmouseover="<?php echo $TPL_VAR["id"]?>.direct(-1)" onclick="<?php echo $TPL_VAR["id"]?>.go()" class=hand></div></div></td>
<td valign="top">
<script><?php echo $TPL_VAR["id"]?>.exec();</script>
</td>
<td valign="top"><div style="position:relative;"><div style="position:absolute;left:-54px;top:90px;z-index:10"><img src="/shop/data/skin/freemart/img/main/arrow_right.gif" onmouseover="<?php echo $TPL_VAR["id"]?>.direct(1)" onclick="<?php echo $TPL_VAR["id"]?>.go()" class=hand></div></div></td>
</tr>
</table>

<!-- 품절상품 마스크 -->
<div id="el-goods-soldout-image-mask" style="display:none;position:absolute;top:0;left:0;background:url(<?php if($GLOBALS["cfg_soldout"]["display_overlay"]=='custom'){?>../data/goods/icon/custom/soldout_overlay<?php }else{?>../data/goods/icon/icon_soldout<?php echo $GLOBALS["cfg_soldout"]["display_overlay"]?><?php }?>) no-repeat center center;"></div>
<script>
addOnloadEvent(function(){ setGoodsImageSoldoutMask() });
</script>