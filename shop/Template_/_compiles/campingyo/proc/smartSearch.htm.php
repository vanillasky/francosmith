<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/proc/smartSearch.htm 000010673 */ 
if (is_array($TPL_VAR["colorList"])) $TPL_colorList_1=count($TPL_VAR["colorList"]); else if (is_object($TPL_VAR["colorList"]) && in_array("Countable", class_implements($TPL_VAR["colorList"]))) $TPL_colorList_1=$TPL_VAR["colorList"]->count();else $TPL_colorList_1=0;
if (is_array($TPL_VAR["searchList"])) $TPL_searchList_1=count($TPL_VAR["searchList"]); else if (is_object($TPL_VAR["searchList"]) && in_array("Countable", class_implements($TPL_VAR["searchList"]))) $TPL_searchList_1=$TPL_VAR["searchList"]->count();else $TPL_searchList_1=0;?>
<?php if($TPL_VAR["ssState"]=='y'){?>
<style type="text/css">
.smart-search-wrap {width:190px;}
.smart-search-wrap div.title { width:100%; padding:5px 0px 4px 7px; font-family:dotum; background-color:#938E88; color:#FFFFFF; font-weight:bold;}
.smart-search-wrap div.form { padding:7px; border:1px solid #E0E0E0; }

.smart-search-wrap div.form .searchListTab { height:20px; border:1px #E4E4E4 solid; color:#666666; font-family:dotum; font-size:11px; font-weight:bold; margin:3px 0px 0px 0px; }

/* 검색 옵션 타이틀 */
.smart-search-wrap div.form .searchListTab .searchListTab_title { float:left; color:#666666; padding:4px 0px 0px 7px; }
.smart-search-wrap div.form .searchListTab .searchListTab_more { float:right; padding:4px 4px 0px 4px; cursor:pointer; }

.smart-search-wrap div.form .searchListBox { padding:3px 10px; border-right:#E0E0E0 1px solid; border-bottom:#E0E0E0 1px solid; border-left:#E0E0E0 1px solid; background-color:#F5F5F5; font-family:dotum; font-size:11px; color:#666666; }
.smart-search-wrap div.form .searchListBox ul {margin:0px;list-style:none;padding-left:0px;}
.smart-search-wrap div.form .searchListBox ul li {clear:both;}
.smart-search-wrap div.form .searchListBox ul li.hidden {display:none;}

.smart-search-wrap div.form .searchListBox { padding:3px 10px; border-right:#E0E0E0 1px solid; border-bottom:#E0E0E0 1px solid; border-left:#E0E0E0 1px solid; background-color:#F5F5F5; font-family:dotum; font-size:11px; color:#666666; }
.smart-search-wrap div.form .searchListBox ul {margin:0px;list-style:none;padding-left:0px;}
.smart-search-wrap div.form .searchListBox ul li {clear:both;}
.smart-search-wrap div.form .searchListBox ul li.hidden {display:none;}


.smart-search-wrap div.form .searchListBar { height:60px; margin:2px 0px; font-family:dotum; font-size:11px; color:#666666; }
.smart-search-wrap div.form .searchListBar .barArea { height:26px; background:url(/shop/data/skin/campingyo/img/banner/scroll_bar.gif); }
.smart-search-wrap div.form .searchListBar .barArea .ssPrcArrow { position:relative; cursor:pointer; }
.smart-search-wrap div.form .searchListBar .barArea #minArrow { top:0px; left:0px; }
.smart-search-wrap div.form .searchListBar .barArea #maxArrow { top:0px; left:156px; }
.smart-search-wrap div.form .searchListBar .inputArea { padding:6px 6px 0px 6px; }
.smart-search-wrap div.form .searchListBar .inputArea input { width:60px; height:18px; border:1px #CCCCCC solid; color:#666666; font-family:dotum; font-size:11px; font-weight:bold; text-align:right; }

.smart-search-wrap div.form .ssMoreSearchBox { width:600px; position:absolute; background-color:#9D9D9D; margin-left:190px; border:3px #9D9D9D solid; }
.smart-search-wrap div.form .ssMoreSearchBox .ssMoreOption { top:-1px; width:100%; height:200px; padding:6px; position:relative; border:1px #E0E0E0 solid; background:#F5F5F5; font-family:dotum; font-size:11px; overflow-y:scroll; }
.smart-search-wrap div.form .ssMoreSearchBox .ssMoreOption ul {margin:0px;list-style:none;padding-left:0px;width:100%;}
.smart-search-wrap div.form .ssMoreSearchBox .ssMoreOption ul li {width:33%;float:left;}

.smart-search-wrap div.form .ssMoreSearchBox .ssMoreButton { height:24px; overflow:hidden; background:#FFFFFF; text-align:center; margin-top:14px; }

.smart-search-wrap div.form .paletteColor { width:16px; height:16px; cursor:pointer; }
.smart-search-wrap div.form .paletteColor_selected { float:left; width:16px; height:16px; margin:1px; cursor:pointer; }

#selectedColor { background:#E9E9E9; border:1px solid #E0E0E0; margin:1px; padding-left:1px; }

.smart-search-wrap div.form .ssBtnArea { text-align:right; margin-top:10px; }
</style>

<script language="JavaScript">
addOnloadEvent(function(){ ssPrcSetting() });
addOnloadEvent(function(){ color2Tag('selectedColor') });
</script>

<input type="hidden" name="searchID" id="searchID" />
<input type="hidden" name="queryString" id="queryString" value="<?php echo $GLOBALS["_SERVER"]['QUERY_STRING']?>" />
<input type="hidden" name="tempOption" id="tempOption" />

<div id="ssMoreSearchBox" class="ssMoreSearchBox" style="display:none;">
	<div style="height:28px; background-color:#9D9D9D; color:#FFFFFF; font-family:Dotum; font-size:14px; font-weight:bold; "><div style="margin:5px 0px 0px 5px;" id="ssMoreSearchName"></div></div>
	<div style="padding:15px; background:#FFFFFF;">
		<div id="ssMoreOption" class="ssMoreOption"></div>
		<div style="clear:both;"></div>
		<div class="ssMoreButton"><a href="javascript:;" onclick="ssSubmitMoreOption()" style="margin-right:4px;"><img src="/shop/data/skin/campingyo/img/banner/btn_array_apply.gif"></a><a href="javascript:;" onclick="ssCloseMoreOption()"><img src="/shop/data/skin/campingyo/img/banner/btn_array_cancel.gif"></a></div>
	</div>
</div>
<?php if($TPL_colorList_1){foreach($TPL_VAR["colorList"] as $TPL_V1){?>
<?php echo $TPL_V1?>

<?php }}?>
<div class="smart-search-wrap">
<form name="sSearch" method="get" style="margin:0px;">
	<input type="hidden" name="category" id="category" value="<?php echo $GLOBALS["_GET"]["category"]?>" />
	<input type="hidden" name="page" id="page" value="1" />
	<input type="hidden" name="sort" id="sort" value="<?php echo $GLOBALS["_GET"]["sort"]?>">
	<input type="hidden" name="page_num" id="page_num" value="<?php echo $GLOBALS["_GET"]["page_num"]?>">

	<div class="title">SMART 검색</div>

	<div class="form">
<?php if($TPL_searchList_1){$TPL_I1=-1;foreach($TPL_VAR["searchList"] as $TPL_V1){$TPL_I1++;?>
		<div id="searchListTab<?php echo $TPL_I1?>" class="searchListTab"><div class="searchListTab_title"><?php echo $TPL_V1["name"]?></div><?php if(count($TPL_V1["list"])>= 10){?><div class="searchListTab_more"><img src="/shop/data/skin/campingyo/img/banner/btn_b_skin_more.gif" onclick="ssShowMore('ssMoreSearchBox', '<?php echo $TPL_V1["name"]?>', '<?php echo $TPL_V1["id"]?>'); ssCheckedOption('<?php echo $TPL_V1["id"]?>');" title="<?php echo $TPL_V1["name"]?> 더보기" /></div><div style="height:0px; clear:both; font-size:0px;"></div><?php }else{?><div style="height:0px; clear:left; font-size:0px;"></div><?php }?></div>
<?php if($TPL_V1["max"]){?>
			<div class="searchListBar">
				<div class="barArea"><img src="/shop/data/skin/campingyo/img/banner/scroll_point.png" id="minArrow" class="ssPrcArrow" style="display:none;" onmousedown="ssStart_move(event,'minArrow');" onmouseup="Moveing_stop();sSearch.submit();" /><img src="/shop/data/skin/campingyo/img/banner/scroll_point.png" id="maxArrow" class="ssPrcArrow" style="display:none;" onmousedown="ssStart_move(event,'maxArrow');" onmouseup="Moveing_stop();sSearch.submit();" /></div>
				<div class="inputArea">
					<input type="hidden" name="ssOriMinPrc" id="ssOriMinPrc" value="<?php echo $TPL_V1["min"]?>" />
					<input type="hidden" name="ssOriMaxPrc" id="ssOriMaxPrc" value="<?php echo $TPL_V1["max"]?>" />
					<input type="text" name="ssMinPrice" id="ssMinPrice" value="<?php if($GLOBALS["_GET"]["ssMinPrice"]||$GLOBALS["_GET"]["ssMinPrice"]=='0'){?><?php echo $GLOBALS["_GET"]["ssMinPrice"]?><?php }else{?><?php echo $TPL_V1["min"]?><?php }?>" />원 ~
					<input type="text" name="ssMaxPrice" id="ssMaxPrice" value="<?php if($GLOBALS["_GET"]["ssMaxPrice"]||$GLOBALS["_GET"]["ssMaxPrice"]=='0'){?><?php echo $GLOBALS["_GET"]["ssMaxPrice"]?><?php }else{?><?php echo $TPL_V1["max"]?><?php }?>" />원
				</div>
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
				<div class="searchListBox"<?php if(count($TPL_V1["list"])>= 5){?> style="height:105px; overflow-y:auto;"<?php }?>>
					<ul>
<?php if((is_array($TPL_R2=$TPL_V1["list"])&&!empty($TPL_R2)) || (is_object($TPL_R2) && in_array("Countable", class_implements($TPL_R2)) && $TPL_R2->count() > 0)) {$TPL_I2=-1;foreach($TPL_R2 as $TPL_V2){$TPL_I2++;?>
<?php if($TPL_I2< 10){?>
						<li><input type="checkbox" name="<?php echo $TPL_V1["id"]?>[]" id="<?php echo $TPL_V1["id"]?>_<?php echo $TPL_I2?>" value="<?php if($TPL_V1["valList"][$TPL_I2]){?><?php echo $TPL_V1["valList"][$TPL_I2]?><?php }else{?><?php echo $TPL_V2?><?php }?>" <?php echo $TPL_V1["chked"][$TPL_I2]?> onclick="this.form.submit();" /> <label for="<?php echo $TPL_V1["id"]?>_<?php echo $TPL_I2?>"><?php echo $TPL_V2?></label></li>
<?php }else{?>
						<li class="hidden"><input type="checkbox" name="<?php echo $TPL_V1["id"]?>[]" id="<?php echo $TPL_V1["id"]?>_<?php echo $TPL_I2?>" value="<?php if($TPL_V1["valList"][$TPL_I2]){?><?php echo $TPL_V1["valList"][$TPL_I2]?><?php }else{?><?php echo $TPL_V2?><?php }?>" <?php echo $TPL_V1["chked"][$TPL_I2]?> onclick="this.form.submit();" /></li>
<?php }?>
<?php }}?>
					</ul>
				</div>
<?php }?>
<?php }?>
<?php }}?>
		<div class="ssBtnArea"><a href="<?php echo $GLOBALS["_SERVER"]['PHP_SELF']?>?category=<?php echo $GLOBALS["_GET"]['category']?>"><img src="/shop/data/skin/campingyo/img/banner/btn_b_skin_reset.gif" /></a></div>
	</div>
</form>
</div>
<?php }?>