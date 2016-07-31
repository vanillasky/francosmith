<?php /* Template_ 2.2.7 2014/11/03 14:24:29 /www/francotr3287_godo_co_kr/shop/data/skin/standard/proc/menuCategory.htm 000004929 */  $this->include_("dataCategory");?>
<?php if($TPL_VAR["page_cache_enabled"]){?>
<style type="text/css">
.layer-text-category {
	padding-right: 40px;
}
.menu-text-category {
	padding: 5px 19px 0 19px;
}
</style>
<div id="t_cate">
<?php if($GLOBALS["cfg"]["subCategory"]!= 2){?>
	<div id="r_cate">
		<table cellpadding=0 cellspacing=0 border=0 id=menuLayer align=center>
		<tr>
			<td id="template-category-layer" style="display: none;" class="catebar" data-callback="execSubLayerTop">
				<div><a href="<?php echo url("goods/goods_list.php?")?>&category={:category:}" class="cate1">{:catnm:}</a></div>
				<div class="template-category-sub-layer" style="display: none; position: relative; z-index: 20;">
					<div class="subLayer">
						<table width="100%" cellspacing=0 cellpadding="0" border="0" id="table_arrow">
						<tr>
							<td><img src="/shop/data/skin/standard/img/main/icon_arrow.gif"></td>
						</tr>
						</table>
						<table width="100%" cellspacing=0 cellpadding="0" border="0" id="table_cate">
						<tr class="template-category-sub" style="display: none;">
							<td align="left" nowrap class="cate"><a href="<?php echo url("goods/goods_list.php?")?>&category={:subCategory:}" class="cate2">{:subCatnm:}</a></td>
						</tr>
						</table>
					</div>
				</div>
			</td>
		</tr>
		</table>
	</div>
<?php }else{?>
	<table cellpadding=0 cellspacing=0 border=0 class="cateUnfold" align=center>
	<tr>
		<td id="template-category-menu" style="display: none;" class="catebar">
			<a href="<?php echo url("goods/goods_list.php?")?>&category={:category:}">{:catnm:}</a>
			<div class="catesub template-category-sub-container" style="display: none;">
				<table>
				<tr class="template-category-sub">
					<td class="cate"><a href="<?php echo url("goods/goods_list.php?")?>&category={:subCategory:}">- {:subCatnm:}</a></td>
				</tr>
				</table>
			</div>
		</td>
	</tr>
	</table>
<?php }?>
</div>
<?php }else{?>
<div id="t_cate">
<?php if($GLOBALS["cfg"]["subCategory"]!= 2){?>
	<div id="r_cate">
		<table cellpadding=0 cellspacing=0 border=0 id=menuLayer align=center>
		<tr>
<?php if((is_array($TPL_R1=dataCategory($GLOBALS["cfg"]["subCategory"], 1))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
			<td style="<?php if(strpos($TPL_V1["catnm"],'img')===false){?>padding-right:40px;<?php }?>" class="catebar"><div><a href="<?php echo url("goods/goods_list.php?")?>&category=<?php echo $TPL_V1["category"]?>" class="cate1"><?php echo $TPL_V1["catnm"]?></a></div>
<?php if($TPL_V1["sub"]){?>
				<div style="position:relative;z-index:20">
					<div class="subLayer">
						<table width="100%" cellspacing=0 cellpadding="0" border="0" id="table_arrow">
						<tr>
							<td><img src="/shop/data/skin/standard/img/main/icon_arrow.gif"></td>
						</tr>
						</table>
						<table width="100%" cellspacing=0 cellpadding="0" border="0" id="table_cate">
<?php if((is_array($TPL_R2=$TPL_V1["sub"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
						<tr>
							<td align="left" nowrap class="cate"><a href="<?php echo url("goods/goods_list.php?")?>&category=<?php echo $TPL_V2["category"]?>" class="cate2"><?php echo $TPL_V2["catnm"]?></a></td>
						</tr>
<?php }}?>
						</table>
					</div>
				</div>
<?php }?>
			</td>
<?php }}?>
		</tr>
		</table>
<?php if($GLOBALS["cfg"]["subCategory"]){?>
		<script>execSubLayerTop();</script>
<?php }?>
	</div>
<?php }else{?>
	<table cellpadding=0 cellspacing=0 border=0 class="cateUnfold" align=center>
	<tr>
<?php if((is_array($TPL_R1=dataCategory($GLOBALS["cfg"]["subCategory"], 1))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
		<td style="<?php if(strpos($TPL_V1["catnm"],'img')===false){?>padding:5px 19px 0 19px<?php }?>" class="catebar"><a href="<?php echo url("goods/goods_list.php?")?>&category=<?php echo $TPL_V1["category"]?>"><?php echo $TPL_V1["catnm"]?></a>
<?php if($TPL_V1["sub"]){?>
			<div class="catesub">
				<table>
<?php if((is_array($TPL_R2=$TPL_V1["sub"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
				<tr>
					<td class="cate"><a href="<?php echo url("goods/goods_list.php?")?>&category=<?php echo $TPL_V2["category"]?>">- <?php echo $TPL_V2["catnm"]?></a></td>
				</tr>
<?php }}?>
				</table>
			</div>
<?php }?>
		</td>
<?php }}?>
	</tr>
	</table>
<?php }?>
</div>
<?php }?>