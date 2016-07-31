<?php /* Template_ 2.2.7 2016/07/26 08:50:29 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/outline/side/category_side.htm 000001089 */  $this->include_("dataSubCategory");?>
<div id="left_cs" style="width:<?php echo $GLOBALS["cfg"]['shopSideSize']?>px;">

	<div class="smart-search-wrap">
		<div class="title top_red">Category</div>
		
		<div class="category-left">
			<ul>
<?php if((is_array($TPL_R1=dataSubCategory($GLOBALS["category"],true))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?>
				<li>
<?php if($GLOBALS["category"]==$TPL_V1["category"]){?><b><?php }?>
				<a href="?category=<?php echo $TPL_V1["category"]?>"><?php echo $TPL_V1["catnm"]?><font color=#777777>(<?php echo $TPL_V1["gcnt"]+ 0?>)</font></a>
<?php if($GLOBALS["category"]==$TPL_V1["category"]){?></b><?php }?>
				</li>
<?php }}?>	
			
			</ul>
		</div>
	</div>

<!-- SMART SEARCH -->	
<?php if($TPL_VAR["smartSearch_useyn"]=='y'){?>
<?php $this->print_("smartSearch",$TPL_SCP,1);?>

<?php }?>

</div>