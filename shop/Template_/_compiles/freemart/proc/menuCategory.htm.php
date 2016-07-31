<?php /* Template_ 2.2.7 2016/06/23 12:26:39 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/proc/menuCategory.htm 000002258 */  $this->include_("dataCategory","dataSubCategory");?>
<div id="top_cate_wrapper" class="top_red">
	<div id="top_cate" style="width:<?php echo $GLOBALS["cfg"]['shopSize']?>px;">
		<ul class="top_cate_ul">
			<li class="brand-container icon">
				<p class="lv1"><a id="brand_link" href="#">Brands</a></p>
				<div class="dropdown" style="width:<?php echo $GLOBALS["cfg"]['shopSize']?>px;">
				<?php echo $this->define('tpl_include_file_1','proc/main/brand_list.htm')?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?>

				</div>
			</li>
			
<?php if((is_array($TPL_R1=dataCategory($GLOBALS["cfg"]["subCategory"], 1))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
			<li class="menu-container">
				<p class="lv1"><a href="#"><?php echo $TPL_V1["catnm"]?></a></p>
				<div class="dropdown" style="width:<?php echo $GLOBALS["cfg"]['shopSize']?>px;">
<?php if($TPL_V1["sub"]){?>
<?php if((is_array($TPL_R2=$TPL_V1["sub"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {foreach($TPL_R2 as $TPL_V2){?>
					<div class="menu_lv2">
					
						<p><a href="<?php echo url("goods/goods_list.php?")?>&category=<?php echo $TPL_V2["category"]?>"><span class="mlink boldf"><?php echo $TPL_V2["catnm"]?></span></a>
							<div class="menu_lv3">
								<ul>
<?php if((is_array($TPL_R3=dataSubCategory($TPL_V2["category"],false))&&!empty($TPL_R3)) || (is_object($TPL_R3) && in_array("Countable", class_implements($TPL_R3)) && $TPL_R3->count() > 0)) {foreach($TPL_R3 as $TPL_V3){?>
<?php if($TPL_V1["sub"]){?>
									<li><a href="/shop/goods/goods_list.php?&category=<?php echo $TPL_V3["category"]?>"><span class="mlink"><?php echo $TPL_V3["catnm"]?></span></a></li>
<?php }?>
<?php }}?>
								</ul>
							</div>
						</p>
					</div>	
<?php }}?>
<?php }?>
					<div class="dd_bottom">
										
					</div>
				</div>
			</li>	
<?php }}?>
			
		</ul>
	</div>	
</div>
<script>
	bindTopMenu();
</script>