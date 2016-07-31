<?php /* Template_ 2.2.7 2014/07/30 21:42:58 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/goods/list/tpl_10.htm 000003838 */ 
if (is_array($TPL_VAR["loop"])) $TPL_loop_1=count($TPL_VAR["loop"]); else if (is_object($TPL_VAR["loop"]) && in_array("Countable", class_implements($TPL_VAR["loop"]))) $TPL_loop_1=$TPL_VAR["loop"]->count();else $TPL_loop_1=0;?>
<?php if(!$TPL_VAR["id"]){?><?  $TPL_VAR["id"] = "es_".md5(crypt('')); ?><?php }?>

<style>
.godo-tooltip-1 {background:#000000;color:#ffffff;}
.godo-tooltip-2 {background:#ffffff;color:#000000;border:1px solid #DFDFDF}
</style>

<div id="el-godo-tooltip-<?php echo $TPL_VAR["id"]?>" style="z-index:1000;display:none;position:absolute;top:0;left:0;width:<?php echo $TPL_VAR["size"]?>px;padding:10px; -moz-opacity:.<?php echo $TPL_VAR["dpCfg"]['alphaRate']?>; filter:alpha(opacity=<?php echo $TPL_VAR["dpCfg"]['alphaRate']?>); opacity:.<?php echo $TPL_VAR["dpCfg"]['alphaRate']?>;line-height:140%;" class="godo-tooltip-<?php echo $TPL_VAR["dpCfg"]["dOpt10"]?>">
</div>


<script>
function fnGodoTooltipShow_<?php echo $TPL_VAR["id"]?>(obj) {
	var pos_x =0;
	var pos_y =0;
	var tooltip = document.getElementById('el-godo-tooltip-<?php echo $TPL_VAR["id"]?>');
	tooltip.innerText = obj.getAttribute('tooltip');
	
	if (document.documentElement.scrollTop > 0) {
		pos_x = event.clientX + document.documentElement.scrollLeft;
		pos_y = event.clientY + document.documentElement.scrollTop;
	} else {
		pos_x = event.clientX + document.body.scrollLeft;
		pos_y = event.clientY + document.body.scrollTop;
	}

	tooltip.style.top = (pos_y + 10) + 'px';
	tooltip.style.left = (pos_x + 10) + 'px';
	tooltip.style.display = 'block';
}

function fnGodoTooltipHide_<?php echo $TPL_VAR["id"]?>(obj) {
	var tooltip = document.getElementById('el-godo-tooltip-<?php echo $TPL_VAR["id"]?>');
	tooltip.innerText = '';
	tooltip.style.display = 'none';
}
</script>

<!-- 상품 리스트 -->
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
<?php if($TPL_loop_1){$TPL_I1=-1;foreach($TPL_VAR["loop"] as $TPL_V1){$TPL_I1++;?>
<?php if($TPL_I1&&$TPL_I1%$TPL_VAR["cols"]== 0){?></tr><tr><td height="10"></td></tr><tr><td height="10"></td></tr><tr><?php }?>
	<td align="center" valign="top" width="<?php echo  100/$TPL_VAR["cols"]?>%">
	<div><a href="<?php echo $TPL_V1["goods_view_url"]?>" onmouseover="fnGodoTooltipShow_<?php echo $TPL_VAR["id"]?>(this)" onmousemove="fnGodoTooltipShow_<?php echo $TPL_VAR["id"]?>(this)" onmouseout="fnGodoTooltipHide_<?php echo $TPL_VAR["id"]?>(this)" tooltip="<?php echo $TPL_V1["shortdesc"]?>"><?php echo goodsimg($TPL_V1["img_s"],$TPL_VAR["size"],'class="'.$TPL_V1["css_selector"].'"')?></a></div>
<?php if($TPL_V1["soldout_icon"]){?><div style="padding:3px"><?php if($TPL_V1["soldout_icon"]=='custom'){?><img src="../data/goods/icon/custom/soldout_icon"><?php }else{?><img src="/shop/data/skin/freemart/img/icon/good_icon_soldout.gif"><?php }?></div><?php }?>
<?php if($TPL_V1["icon"]){?><div><?php echo $TPL_V1["icon"]?></div><?php }?>
<?php if($TPL_V1["coupon"]){?><div class="eng"><b style="color:red"><?php echo $TPL_V1["coupon"]?><font class="small">원</font></b> <img src="/shop/data/skin/freemart/img/icon/good_icon_coupon.gif" align="absmiddle"></div><?php }?>
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