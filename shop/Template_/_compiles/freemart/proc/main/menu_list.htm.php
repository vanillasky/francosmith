<?php /* Template_ 2.2.7 2016/05/19 18:57:49 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/proc/main/menu_list.htm 000002597 */  $this->include_("dataCategory","dataSubCategory");?>
<?php if((is_array($TPL_R1=dataCategory($GLOBALS["cfg"]["subCategory"], 1))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
<li class="menu-container">
	<p class="lv1"><a href="<?php echo url("goods/goods_list.php?")?>&category=<?php echo $TPL_V1["category"]?>"><?php echo $TPL_V1["catnm"]?></a></p>
	<div class="dropdown" style="width:<?php echo $GLOBALS["cfg"]['shopSize']?>px;">
<?php if($TPL_V1["sub"]){?>
<?php if((is_array($TPL_R2=$TPL_V1["sub"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
		<div class="menu_lv2">
			<p><a href="<?php echo url("goods/goods_list.php?")?>&category=<?php echo $TPL_V2["category"]?>"><span class="mlink boldf"><?php echo $TPL_V2["catnm"]?></span></a>
				<div class="menu_lv3">
					<ul>
<?php if((is_array($TPL_R3=dataSubCategory($TPL_V2["category"],true))&&!empty($TPL_R3)) || (is_object($TPL_R3) && in_array("Countable", class_implements($TPL_R3)) && $TPL_R3->count() > 0)) {foreach($TPL_R3 as $TPL_V3){?>
						<li><a href="/shop/goods/goods_list.php?&category=<?php echo $TPL_V3["category"]?>"><span class="mlink"><?php echo $TPL_V3["catnm"]?></span></a></li>
<?php }}?>
					</ul>
				</div>
			</p>
		</div>	
<?php }}?>
<?php }?>
	</div>
</li>	
<?php }}?>



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