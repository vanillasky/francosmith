<?php /* Template_ 2.2.7 2014/10/05 11:41:27 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/proc/menuCategory.htm 000002691 */  $this->include_("dataCategory");?>
<div style="width:190px;">
	<table width="100%" cellpadding=0 cellspacing=0 border=0>
	<tr>
		<td><img src="/shop/data/images/category.png"></td>	
	</tr>
		
	</table>
<?php if($GLOBALS["cfg"]["subCategory"]!= 2){?>
	<table width="100%" cellpadding=0 cellspacing=0 border=0 id="menuLayer">
<?php if((is_array($TPL_R1=dataCategory($GLOBALS["cfg"]["subCategory"], 1))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
	<tr>
		<td class="catebar"><a href="<?php echo url("goods/goods_list.php?")?>&category=<?php echo $TPL_V1["category"]?>"><?php echo $TPL_V1["catnm"]?></a></td>
		<td style="z-index:100">
<?php if($TPL_V1["sub"]){?>
		<div style="position:relative"><div class=subLayer><table width=100% cellspacing=1>
<?php if((is_array($TPL_R2=$TPL_V1["sub"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {$TPL_S2=count($TPL_R2);$TPL_I2=-1;foreach($TPL_R2 as $TPL_V2){$TPL_I2++;?>
		<tr><td nowrap class="cate"><a href="<?php echo url("goods/goods_list.php?")?>&category=<?php echo $TPL_V2["category"]?>"><?php echo $TPL_V2["catnm"]?></a></td></tr>
<?php if($TPL_I2!=$TPL_S2- 1){?><tr><td height=1 bgcolor=#F3F2F0></td></tr><?php }?>
<?php }}?>
		</table>
		</div>
		</div>
<?php }?>
		</td>
	</tr>
<?php }}?>
	</table>
<?php if($GLOBALS["cfg"]["subCategory"]){?>
	<script>execSubLayer();</script>
<?php }?>

<?php }else{?>
	<table width=100% cellpadding=0 cellspacing=0 class="cateUnfold">
<?php if((is_array($TPL_R1=dataCategory($GLOBALS["cfg"]["subCategory"], 1))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
	<tr>
		<td class="catebar"><a href="<?php echo url("goods/goods_list.php?")?>&category=<?php echo $TPL_V1["category"]?>"><?php echo $TPL_V1["catnm"]?></a></td>
	</tr>
<?php if($TPL_V1["sub"]){?>
	<tr>
		<td class="catesub">
		<table>
<?php if((is_array($TPL_R2=$TPL_V1["sub"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
		<tr><td class="cate"><a href="<?php echo url("goods/goods_list.php?")?>&category=<?php echo $TPL_V2["category"]?>"><?php echo $TPL_V2["catnm"]?></a></td></tr>
<?php }}?>
		</table>
		</td>
	</tr>
<?php }?>
<?php }}?>
	</table>
<?php }?>

</div>