<?php /* Template_ 2.2.7 2016/01/09 10:46:02 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/goods/list/tpl_07.htm 000007758 */ ?>
<?php if(!$TPL_VAR["id"]){?><?  $TPL_VAR["id"] = "es_".md5(crypt('')); ?><?php }?>
<!-- 탭 스타일시트 -->
<style type="text/css">
<?php if($TPL_VAR["dpCfg"]['dOpt3']== 1){?>
	.mainTabStyle1_1	{ border-top:1px #DEDEDE solid; border-right:1px #DEDEDE solid; cursor:pointer; font-size:11px; } /* 기본 */
	.mainTabStyle1_2	{ border-left:1px #DEDEDE solid; } /* 1번탭 */
	.mainTabStyle1_3	{ border-bottom:1px #FFFFFF solid; background:#FFFFFF; font-weight:bold; } /* 선택탭 */
	.mainTabStyle1_4	{ border-bottom:1px #DEDEDE solid; background:#FAFAFA; font-weight:normal; } /* 미선택탭 */
<?php }else{?>
	.mainTabStyle2_1	{ border-left:1px #DEDEDE solid; border-bottom:1px #DEDEDE solid; cursor:pointer; font-size:11px; } /* 기본 */
	.mainTabStyle2_2	{ border-top:1px #DEDEDE solid; } /* 1번탭 */
	.mainTabStyle2_3	{ border-right:1px #FFFFFF solid; background:#FFFFFF; font-weight:bold; } /* 선택탭 */
	.mainTabStyle2_4	{ border-right:1px #DEDEDE solid; background:#FAFAFA; font-weight:normal; } /* 미선택탭 */
<?php }?>
</style>

<script language="JavaScript">
function <?php echo $TPL_VAR["id"]?>_tab(tabNo) {
	for(i = 0; i < <?php echo $TPL_VAR["dpCfg"]['tabNum']?>; i++) {
		if(tabNo == i) {
			try {
				document.getElementById("<?php echo $TPL_VAR["id"]?>_" + i).style.display = "";
				if(i == 0)document.getElementById("<?php echo $TPL_VAR["id"]?>_t" + i).className = "mainTabStyle<?php echo $TPL_VAR["dpCfg"]['dOpt3']?>_1 mainTabStyle<?php echo $TPL_VAR["dpCfg"]['dOpt3']?>_2 mainTabStyle<?php echo $TPL_VAR["dpCfg"]['dOpt3']?>_3";
				else document.getElementById("<?php echo $TPL_VAR["id"]?>_t" + i).className = "mainTabStyle<?php echo $TPL_VAR["dpCfg"]['dOpt3']?>_1 mainTabStyle<?php echo $TPL_VAR["dpCfg"]['dOpt3']?>_3";
			} catch(e) {}
		}
		else {
			try {
				document.getElementById("<?php echo $TPL_VAR["id"]?>_" + i).style.display = "none";
				if(i == 0)document.getElementById("<?php echo $TPL_VAR["id"]?>_t" + i).className = "mainTabStyle<?php echo $TPL_VAR["dpCfg"]['dOpt3']?>_1 mainTabStyle<?php echo $TPL_VAR["dpCfg"]['dOpt3']?>_2 mainTabStyle<?php echo $TPL_VAR["dpCfg"]['dOpt3']?>_4";
				else document.getElementById("<?php echo $TPL_VAR["id"]?>_t" + i).className = "mainTabStyle<?php echo $TPL_VAR["dpCfg"]['dOpt3']?>_1 mainTabStyle<?php echo $TPL_VAR["dpCfg"]['dOpt3']?>_4";
			} catch(e) {}
		}
	}
}
</script>

<!-- 상품 리스트 -->

<?php if($TPL_VAR["dpCfg"]['dOpt3']== 1){?>
<table width="100%" border="0" cellpadding="5" cellspacing="0">
<tr align="center" valign="middle">
<?php if((is_array($TPL_R1=$TPL_VAR["dpCfg"]['tabLoop'])&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {$TPL_S1=count($TPL_R1);$TPL_I1=-1;foreach($TPL_R1 as $TPL_V1){$TPL_I1++;?>
	<td width="<?php echo round( 100/$TPL_S1)?>%" id="<?php echo $TPL_VAR["id"]?>_t<?php echo $TPL_I1?>" class="mainTabStyle1_1<?php if($TPL_I1== 0){?> mainTabStyle1_2 mainTabStyle1_3<?php }else{?> mainTabStyle1_4<?php }?>" onclick="<?php echo $TPL_VAR["id"]?>_tab(<?php echo $TPL_I1?>);"><?php echo $TPL_VAR["dpCfg"]['tabName'.($TPL_I1+ 1)]?></td>
<?php }}?>
</tr>
</table>
<?php }?>

<?php if($TPL_VAR["dpCfg"]['dOpt3']== 2){?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr align="center" valign="top">
	<td>
		<table width="100" border="0" cellpadding="5" cellspacing="0">
<?php if((is_array($TPL_R1=$TPL_VAR["dpCfg"]['tabLoop'])&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {$TPL_I1=-1;foreach($TPL_R1 as $TPL_V1){$TPL_I1++;?>
		<tr align="left" valign="middle">
			<td id="<?php echo $TPL_VAR["id"]?>_t<?php echo $TPL_I1?>" class="mainTabStyle2_1<?php if($TPL_I1== 0){?> mainTabStyle2_2 mainTabStyle2_3<?php }else{?> mainTabStyle2_4<?php }?>" onclick="<?php echo $TPL_VAR["id"]?>_tab(<?php echo $TPL_I1?>);"><?php echo $TPL_VAR["dpCfg"]['tabName'.($TPL_I1+ 1)]?></td>
		</tr>
<?php }}?>
		</table>
	</td>
	<td>
<?php }?>

<?php if((is_array($TPL_R1=$TPL_VAR["dpCfg"]['tabLoop'])&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {$TPL_I1=-1;foreach($TPL_R1 as $TPL_K1=>$TPL_V1){$TPL_I1++;?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" id="<?php echo $TPL_VAR["id"]?>_<?php echo $TPL_I1?>"<?php if($TPL_I1!= 0){?> style="display:none;"<?php }?>>
<tr><td height="10"></td></tr>
<tr>
<?php if((is_array($TPL_R2=$TPL_VAR["dpCfg"]['tabLoop'][$TPL_K1])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_K2=>$TPL_V2){?>
<?php if($TPL_K2&&$TPL_K2%$TPL_VAR["cols"]== 0){?></tr><tr><td height="10"></td></tr><!-- <tr><td colspan=<?php echo $TPL_VAR["cols"]?> bgcolor=#E6E6E6 height=1></td></tr> --><tr><td height="10"></td></tr><tr><?php }?>
	<td align="center" valign="top" width="<?php echo  100/$TPL_VAR["cols"]?>%">

	<div><a href="<?php echo $TPL_V2["goods_view_url"]?>"><?php echo goodsimg($TPL_V2["img_s"],$TPL_VAR["size"],'class="'.$TPL_V2["css_selector"].'"')?></a></div>
<?php if($TPL_V2["soldout_icon"]){?><div style="padding:3px"><?php if($TPL_V2["soldout_icon"]=='custom'){?><img src="../data/goods/icon/custom/soldout_icon"><?php }else{?><img src="/shop/data/skin/freemart/img/icon/good_icon_soldout.gif"><?php }?></div><?php }?>
<?php if($TPL_V2["goodsnm"]){?><div style="padding:5"><a href="<?php echo $TPL_V2["goods_view_url"]?>"><?php echo $TPL_V2["goodsnm"]?></a></div><?php }?>
<?php if(!$TPL_V2["strprice"]){?>
<?php if($TPL_V2["goodsDiscountPrice"]){?>
<?php if($TPL_V2["oriPrice"]){?><strike><?php echo number_format($TPL_V2["oriPrice"])?></strike>↓<?php }?>
			<div style="padding-bottom:3px"><b><?php echo number_format($TPL_V2["goodsDiscountPrice"])?>원</b> <?php if($TPL_V1["special_discount_amount"]){?><img src="/shop/data/skin/freemart/img/icon/goods_special_discount.gif"><?php }?></div>
<?php }else{?>
<?php if($TPL_V2["price"]){?>
<?php if($TPL_V2["consumer"]){?><strike><?php echo number_format($TPL_V2["consumer"])?></strike>↓<?php }?>
			<div style="padding-bottom:3px"><b><?php echo number_format($TPL_V2["price"])?>원</b> <?php if($TPL_V1["special_discount_amount"]){?><img src="/shop/data/skin/freemart/img/icon/goods_special_discount.gif"><?php }?></div>
<?php }?>
<?php }?>
<?php if($TPL_V1["soldout_price_string"]){?><?php echo $TPL_V1["soldout_price_string"]?><?php }?>
<?php if($TPL_V1["soldout_price_image"]){?><?php echo $TPL_V1["soldout_price_image"]?><?php }?>
<?php }else{?><?php echo $TPL_V2["strprice"]?>

<?php }?>
<?php if($TPL_V2["icon"]){?><div><?php echo $TPL_V2["icon"]?></div><?php }?>
<?php if($TPL_V2["coupon"]){?><div class="eng"><b style="color:red"><?php echo $TPL_V2["coupon"]?><font class="small">원</font></b> <img src="/shop/data/skin/freemart/img/icon/good_icon_coupon.gif" align="absmiddle"></div><?php }?>
	</td>
<?php }}?>
</tr>
<tr><td height="10"></td></tr>
</table>
<?php }}?>

<?php if($TPL_VAR["dpCfg"]['dOpt3']== 2){?>
	</td>
</td>
</table>
<?php }?>


<!-- 품절상품 마스크 -->
<div id="el-goods-soldout-image-mask" style="display:none;position:absolute;top:0;left:0;background:url(<?php if($GLOBALS["cfg_soldout"]["display_overlay"]=='custom'){?>../data/goods/icon/custom/soldout_overlay<?php }else{?>../data/goods/icon/icon_soldout<?php echo $GLOBALS["cfg_soldout"]["display_overlay"]?><?php }?>) no-repeat center center;"></div>
<script>
addOnloadEvent(function(){ setGoodsImageSoldoutMask() });
</script>