<?php /* Template_ 2.2.7 2016/07/27 11:57:35 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/proc/smartSearch.htm 000009913 */ 
if (is_array($TPL_VAR["colorList"])) $TPL_colorList_1=count($TPL_VAR["colorList"]); else if (is_object($TPL_VAR["colorList"]) && in_array("Countable", class_implements($TPL_VAR["colorList"]))) $TPL_colorList_1=$TPL_VAR["colorList"]->count();else $TPL_colorList_1=0;
if (is_array($TPL_VAR["searchList"])) $TPL_searchList_1=count($TPL_VAR["searchList"]); else if (is_object($TPL_VAR["searchList"]) && in_array("Countable", class_implements($TPL_VAR["searchList"]))) $TPL_searchList_1=$TPL_VAR["searchList"]->count();else $TPL_searchList_1=0;?>
<?php if($TPL_VAR["ssState"]=='y'){?>


<script src="/shop/data/js/nouislider.js"></script>
<script src="/shop/data/js/wNumb.js"></script>
<link rel="stylesheet" href="/shop/data/js/nouislider.css">

<script language="JavaScript">
//addOnloadEvent(function(){ ssPrcSetting() });
addOnloadEvent(function(){ color2Tag('selectedColor') });

</script>

<input type="hidden" name="searchID" id="searchID" />
<input type="hidden" name="queryString" id="queryString" value="<?php echo $GLOBALS["_SERVER"]['QUERY_STRING']?>" />
<input type="hidden" name="tempOption" id="tempOption" />

<div id="ssMoreSearchBox" class="ssMoreSearchBox" style="display:none;">
	<div class="title top_red"><div id="ssMoreSearchName"></div></div>
	<div style="padding:10px; background:#FFFFFF; text-align:left;">
		<div id="ssMoreOption" class="ssMoreOption"></div>
		<div style="clear:both;"></div>
		<div class="ssMoreButton"><a href="javascript:;" onclick="ssSubmitMoreOption()" style="margin-right:4px;"><img src="/shop/data/skin/freemart/img/banner/btn_array_apply.gif"></a><a href="javascript:;" onclick="ssCloseMoreOption()"><img src="/shop/data/skin/freemart/img/banner/btn_array_cancel.gif"></a></div>
	</div>
</div>
<?php if($TPL_colorList_1){foreach($TPL_VAR["colorList"] as $TPL_V1){?>
<?php echo $TPL_V1?>

<?php }}?>
<div class="smart-search-wrap">
<form name="sSearch" id="sSearch" method="get" style="margin:0px;">
	<input type="hidden" name="category" id="category" value="<?php echo $GLOBALS["_GET"]["category"]?>" />
	<input type="hidden" name="page" id="page" value="1" />
	<input type="hidden" name="sort" id="sort" value="<?php echo $GLOBALS["_GET"]["sort"]?>">
	<input type="hidden" name="page_num" id="page_num" value="<?php echo $GLOBALS["_GET"]["page_num"]?>">

	<div class="title top_red">Filter</div>

	<div class="form">
<?php if($TPL_searchList_1){$TPL_I1=-1;foreach($TPL_VAR["searchList"] as $TPL_V1){$TPL_I1++;?>
		<div id="searchListTab<?php echo $TPL_I1?>" class="searchListTab">
			<div class="searchListTab_title"><?php echo $TPL_V1["name"]?></div>
<?php if(count($TPL_V1["list"])>= 10){?>
				<div class="searchListTab_more">
					<img src="/shop/data/skin/freemart/img/banner/btn_b_skin_more.gif" onclick="ssShowMore('ssMoreSearchBox', '<?php echo $TPL_V1["name"]?>', '<?php echo $TPL_V1["id"]?>'); ssCheckedOption('<?php echo $TPL_V1["id"]?>');" title="<?php echo $TPL_V1["name"]?> 더보기" />
				</div>
				<div style="height:0px; clear:both; font-size:0px;"></div>
<?php }else{?>
				<div style="height:0px; clear:left; font-size:0px;"></div>
<?php }?>
		</div>
		
<?php if($TPL_V1["max"]){?>
			<div class="searchListBar">
				<div class="barArea">
					<!-- 
					<img src="/shop/data/skin/freemart/img/banner/scroll_point.png" id="minArrow" class="ssPrcArrow" style="display:none;" onmousedown="ssStart_move(event,'minArrow');" onmouseup="Moveing_stop();sSearch.submit();" />
					<img src="/shop/data/skin/freemart/img/banner/scroll_point.png" id="maxArrow" class="ssPrcArrow" style="display:none;" onmousedown="ssStart_move(event,'maxArrow');" onmouseup="Moveing_stop();sSearch.submit();" />
					-->
					
					<div class="price-box" >
						<div id="nonlinear"></div>
					</div>
				</div>
				
				<div class="inputArea">
					<input type="hidden" name="ssOriMinPrc" id="ssOriMinPrc" value="<?php echo $TPL_V1["min"]?>" />
					<input type="hidden" name="ssOriMaxPrc" id="ssOriMaxPrc" value="<?php echo $TPL_V1["max"]?>" />
					<input type="hidden" name="ssMinPrice" id="ssMinPrice" value="<?php if($GLOBALS["_GET"]["ssMinPrice"]||$GLOBALS["_GET"]["ssMinPrice"]=='0'){?><?php echo $GLOBALS["_GET"]["ssMinPrice"]?><?php }else{?><?php echo $TPL_V1["min"]?><?php }?>" />
					<input type="hidden" name="ssMaxPrice" id="ssMaxPrice" value="<?php if($GLOBALS["_GET"]["ssMaxPrice"]||$GLOBALS["_GET"]["ssMaxPrice"]=='0'){?><?php echo $GLOBALS["_GET"]["ssMaxPrice"]?><?php }else{?><?php echo $TPL_V1["max"]?><?php }?>" />
					
					<input type="text" name="rMinPrice" id="rMinPrice" value="<?php if($GLOBALS["_GET"]["ssMinPrice"]||$GLOBALS["_GET"]["ssMinPrice"]=='0'){?><?php echo $GLOBALS["_GET"]["ssMinPrice"]?><?php }else{?><?php echo $TPL_V1["min"]?><?php }?>" />원 ~
					<input type="text" name="rMaxPrice" id="rMaxPrice" value="<?php if($GLOBALS["_GET"]["ssMaxPrice"]||$GLOBALS["_GET"]["ssMaxPrice"]=='0'){?><?php echo $GLOBALS["_GET"]["ssMaxPrice"]?><?php }else{?><?php echo $TPL_V1["max"]?><?php }?>" />원
					
					
				</div>
				
				
				<div class="find-button">
					<div>
						<button class="button-dark button-medium" onclick="submitSmartSearch(sSearch, false);">Find</button>
					</div>
				</div>
					
					
				<script>
					var nonLinearSlider = document.getElementById('nonlinear');
					
					noUiSlider.create(nonLinearSlider, {
						connect: true,
						behaviour: 'tap',
						start: [ Math.floor10(<?php if($GLOBALS["_GET"]["ssMinPrice"]||$GLOBALS["_GET"]["ssMinPrice"]=='0'){?><?php echo $GLOBALS["_GET"]["ssMinPrice"]?><?php }else{?><?php echo $TPL_V1["min"]?><?php }?>), 
						         Math.round10(<?php if($GLOBALS["_GET"]["ssMaxPrice"]||$GLOBALS["_GET"]["ssMaxPrice"]=='0'){?><?php echo $GLOBALS["_GET"]["ssMaxPrice"]?><?php }else{?><?php echo $TPL_V1["max"]?><?php }?>)],
						range: {
							// Starting at 500, step the value by 500,
							// until 4000 is reached. From there, step by 1000.
							'min': [ Math.floor10(<?php echo $TPL_V1["min"]?>, 3) ],
							'10%': [ <?php echo $TPL_V1["min"]?>, 1000 ],
							'max': [ Math.round10(<?php echo $TPL_V1["max"]?>, 3) ]
						},
						format: wNumb ({
							decimals:0,
							thousand:',',
						})
					});
					
				</script>
				
				<script>
					// Write the CSS 'left' value to a span.
					function leftValue ( handle ) {
						return handle.parentElement.style.left;
					}
					
					var lowerValue = document.getElementById('rMinPrice'),
						//lowerOffset = document.getElementById('lower-offset'),
						upperValue = document.getElementById('rMaxPrice'),
						//upperOffset = document.getElementById('upper-offset'),
						handles = nonLinearSlider.getElementsByClassName('noUi-handle');
					
					// Display the slider value and how far the handle moved
					// from the left edge of the slider.
					nonLinearSlider.noUiSlider.on('update', function ( values, handle ) {
						if ( !handle ) {
							lowerValue.value = values[handle];
						} else {
							upperValue.value = values[handle];
						}
						
						
					});
				</script>

			</div>
<?php }else{?>
<?php if($TPL_V1["color_useyn"]){?>
				<input type="hidden" name="ssColor" id="ssColor" value="<?php echo $GLOBALS["_GET"]["ssColor"]?>" />
				<div>
					<table border="0" cellpadding="0" cellspacing="2" align="center">
					<tr>
						<td>
							<table border="0" cellpadding="0" cellspacing="2" bgcolor="#E9E9E9" style="margin:1px;">
								<tr>
<?php if((is_array($TPL_R2=$TPL_V1["colorList"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {$TPL_S2=count($TPL_R2);$TPL_I2=-1;foreach($TPL_R2 as $TPL_V2){$TPL_I2++;?>
<?php if($TPL_S2/ 2==$TPL_I2){?>
								</tr>
								<tr>
<?php }?>
									<td><div class="paletteColor" style="background-color:#<?php echo $TPL_V2?>;" onclick="ssSelectColor(this.style.backgroundColor)"></div></td>
<?php }}?>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td id="selectedColor" title="선택된 색은 더블클릭으로 삭제하실 수 있습니다."></td>
					</tr>
					</table>
				</div>
<?php }else{?>
				<div class="searchListBox" <?php if(count($TPL_V1["list"])>= 5){?> style="height:110px; overflow-y:auto;"<?php }?>>
					<ul>
<?php if((is_array($TPL_R2=$TPL_V1["list"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {$TPL_I2=-1;foreach($TPL_R2 as $TPL_V2){$TPL_I2++;?>
<?php if($TPL_I2< 10){?>
						<li><input type="checkbox" name="<?php echo $TPL_V1["id"]?>[]" id="<?php echo $TPL_V1["id"]?>_<?php echo $TPL_I2?>" value="<?php if($TPL_V1["valList"][$TPL_I2]){?><?php echo $TPL_V1["valList"][$TPL_I2]?><?php }else{?><?php echo $TPL_V2?><?php }?>" <?php echo $TPL_V1["chked"][$TPL_I2]?> onclick="javascript:submitSmartSearch(this.form, true);" /> <label for="<?php echo $TPL_V1["id"]?>_<?php echo $TPL_I2?>"><?php echo $TPL_V2?></label></li>
<?php }else{?>
						<li class="hidden"><input type="checkbox" name="<?php echo $TPL_V1["id"]?>[]" id="<?php echo $TPL_V1["id"]?>_<?php echo $TPL_I2?>" value="<?php if($TPL_V1["valList"][$TPL_I2]){?><?php echo $TPL_V1["valList"][$TPL_I2]?><?php }else{?><?php echo $TPL_V2?><?php }?>" <?php echo $TPL_V1["chked"][$TPL_I2]?> onclick="javascript:submitSmartSearch(this.form, true);" /></li>
<?php }?>
<?php }}?>
					</ul>
				</div>
<?php }?>
<?php }?>
<?php }}?>
		<div class="ssBtnArea"><a href="<?php echo $GLOBALS["_SERVER"]['PHP_SELF']?>?category=<?php echo $GLOBALS["_GET"]['category']?>"><img src="/shop/data/skin/freemart/img/banner/btn_b_skin_reset.gif" /></a></div>
	</div>
</form>
</div>



<?php }?>