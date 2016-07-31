<?php /* Template_ 2.2.7 2015/11/18 21:30:54 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/outline/_footer.htm 000013524 */  $this->include_("dataBanner");
if (is_array($GLOBALS["todayGoodsList"])) $TPL__todayGoodsList_1=count($GLOBALS["todayGoodsList"]); else if (is_object($GLOBALS["todayGoodsList"]) && in_array("Countable", class_implements($GLOBALS["todayGoodsList"]))) $TPL__todayGoodsList_1=$GLOBALS["todayGoodsList"]->count();else $TPL__todayGoodsList_1=0;?>
<!-- gdpart mode="open" fid="goods/goods_view.htm footer_5" --><!-- gdline 1"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><!-- gdline 2"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><div style="width:0;height:0;font-size:0"></div></td>
<!-- gdline 3"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><?php if($this->tpl_['side_inc']&&$GLOBALS["cfg"]['outline_sidefloat']=='right'){?>
<!-- gdline 4"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><td valign=top width=<?php echo $GLOBALS["cfg"]['shopSideSize']?> nowrap><?php $this->print_("side_inc",$TPL_SCP,1);?></td>
<!-- gdline 5"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><?php }?>
<!-- gdline 6"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><?php if($TPL_VAR["todayshop_cfg"]['shopMode']!='todayshop'&&!$TPL_VAR["todayshop_cfg"]['isTodayShopPage']){?>
<!-- gdline 7"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><td width=0 id=pos_scroll valign=top>
<!-- gdline 8"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->
<!-- gdline 9"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->	
<!-- gdline 10"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><!-- 스크롤 배너 -->
<!-- gdline 11"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><div id=scroll style="position:absolute; padding-top:10px; padding-left:10px;">
<!-- gdline 12"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><?php if($TPL_VAR["setState"]=='Y'&&$TPL_VAR["Banner"]!=''){?>
<!-- gdline 13"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><div style="text-align:left;"><?php echo $TPL_VAR["Banner"]?></div>
<!-- gdline 14"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><?php }else{?>
<!-- gdline 15"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><div><!-- 맨오른쪽_스크롤배너 (배너관리에서 수정가능) --><?php if((is_array($TPL_R1=dataBanner( 17))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></div>
<!-- gdline 16"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><?php }?>
<!-- gdline 17"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><?php if($GLOBALS["viewPageMoveList"]){?>
<!-- gdline 18"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><script src="/shop/lib/js/prototype.js"></script>
<!-- gdline 19"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><script language="JavaScript">
	//addOnloadEvent(function() { scrollCateList_ajax('<?php echo $GLOBALS["_SERVER"]['QUERY_STRING']?>') });
</script>
<!-- gdline 22"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><div id="scrollMoveList"></div>
<!-- gdline 23"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><div style="height:4px;"><!-- 여백 --></div>
<!-- gdline 24"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><?php }?>
<!-- gdline 25"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->
<!-- gdline 26"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><div style="width:90px;">
<!-- gdline 27"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->	<div><img src="/shop/data/skin/freemart/img/common/wing_bn_top_myview.jpg" border=0></div>
<!-- gdline 28"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->	<table width=100% border=0 cellpadding=0 cellspacing=0 style="border-style:solid;border-color:#E3E3E3;border-width:0px 1px 1px 1px;">
<!-- gdline 29"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->	<tr>
<!-- gdline 30"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->		<td align=center>
<!-- gdline 31"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->		<div id=gdscroll style="height:217px;overflow:hidden">
<!-- gdline 32"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->		<?php if($TPL__todayGoodsList_1){$TPL_I1=-1;foreach($GLOBALS["todayGoodsList"] as $TPL_V1){$TPL_I1++;?>
<!-- gdline 33"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->		<div><a href="<?php echo url("goods/goods_view.php?")?>&goodsno=<?php echo $TPL_V1["goodsno"]?>"><?php echo goodsimg($TPL_V1["img"], 70)?></a></div>
<!-- gdline 34"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->		<?php if($TPL_I1!=$TPL__todayGoodsList_1- 1){?><div style="height:3px;font-size:0"></div><?php }?>
<!-- gdline 35"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->		<?php }}?>
<!-- gdline 36"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->		</div>
<!-- gdline 37"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->		</td>
<!-- gdline 38"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->	</tr>
<!-- gdline 39"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->	<tr><td style="text-align:center;padding:4px 0"><a href="javascript:gdscroll(-107)" onfocus=blur()><img src="/shop/data/skin/freemart/img/common/sky_btn_up.gif" border=0></a> <a href="javascript:gdscroll(107)" onfocus=blur()><img src="/shop/data/skin/freemart/img/common/sky_btn_down.gif" border='0'></a></td></tr>
<!-- gdline 40"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->	</table>
<!-- gdline 41"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --></div>
<!-- gdline 42"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><div style="margin-bottom:5px;">
<!-- gdline 43"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->	<table cellpadding=0 cellspacing=0>
<!-- gdline 44"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->	<tr>
<!-- gdline 45"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->		<td><!-- 맨오른쪽_스크롤배너_top (배너관리에서 수정가능) --><?php if((is_array($TPL_R1=dataBanner( 28))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td>
<!-- gdline 46"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->		<td><!-- 맨오른쪽_스크롤배너_back (배너관리에서 수정가능) --><?php if((is_array($TPL_R1=dataBanner( 29))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></td>
<!-- gdline 47"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->	</tr>
<!-- gdline 48"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->	</table>
<!-- gdline 49"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --></div>
<!-- gdline 50"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->
<!-- gdline 51"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><div><!-- 맨오른쪽_스크롤배너 (배너관리에서 수정가능) --><?php if((is_array($TPL_R1=dataBanner( 18))&&!empty($TPL_R1)) || (is_object($TPL_R1) && in_array("Countable", class_implements($TPL_R1)) && $TPL_R1->count() > 0)) {foreach($TPL_R1 as $TPL_V1){?><?php echo $TPL_V1["tag"]?><?php }}?></div>
<!-- gdline 52"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><div align="center" style="padding-top:10px;"><a href="#top"><img src="/shop/data/skin/freemart/img/main/wing_bt_top.jpg" width="30" height="30"></a></div>
<!-- gdline 53"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --></div>
<!-- gdline 54"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->
<!-- gdline 55"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><!-- 스크롤 배너 활성화 -->
<!-- gdline 56"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><script>scrollBanner();</script>
<!-- gdline 57"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->
<!-- gdline 58"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --></td>
<!-- gdline 59"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><?php }?>
<!-- gdline 60"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --></tr>
<!-- gdline 61"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --></table>
<!-- gdline 62"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->
<!-- gdline 63"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --></td>
<!-- gdline 64"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --></tr>
<!-- gdline 65"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><?php if($this->tpl_['footer_inc']){?>
<!-- gdline 66"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><tr>
<!-- gdline 67"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><td><?php $this->print_("footer_inc",$TPL_SCP,1);?></td>
<!-- gdline 68"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --></tr>
<!-- gdline 69"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><?php }?>
<!-- gdline 70"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --></table>
<!-- gdline 71"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->
<!-- gdline 72"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><!-- 절대! 지우지마세요 : Start -->
<!-- gdline 73"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><iframe name="ifrmHidden" src='<?php echo $GLOBALS["cfg"]["rootDir"]?>/blank.php' style="display:none;width:100%;height:600"></iframe>
<!-- gdline 74"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><!-- 절대! 지우지마세요 : End -->
<!-- gdline 75"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->
<!-- gdline 76"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><script>
if (typeof nsGodo_cartTab == 'object' && '<?php echo $GLOBALS["cfg"]["cartTabUse"]?>' == 'y' && '<?php echo $TPL_VAR["todayshop_cfg"]['shopMode']?>' != 'todayshop') {
	nsGodo_cartTab.init({
		logged: <?php if(!$GLOBALS["sess"]){?>false<?php }else{?>true<?php }?>,
		skin  : '<?php echo $GLOBALS["cfg"]["tplSkin"]?>',
		tpl  : '<?php echo $GLOBALS["cfg"]["cartTabTpl"]?>',
		dir	: 'horizon',	// horizon or vertical
		width:'<?php echo $GLOBALS["cfg"]["shopSize"]?>'
	});
}
<?php if($GLOBALS["cfg"]["preventContentsCopy"]=='1'){?>
addOnloadEvent(function(){ preventContentsCopy() });
<?php }?>
</script>
<!-- gdline 90"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->
<!-- gdline 91"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --><script type="text/javascript">
	
	var $j = jQuery.noConflict();	


;(function(d){d.fn.DB_brandList=function(c){d.extend({},c);return this.each(function(){var g=d(this),e=g.find(".DB_btn li"),c=e.length,f=g.find(".DB_list").find("li"),h=f.length;(function(){for(var a=0;a<c;a++){var b=e.eq(a);b.data("data-key",b.find("a").text())}for(a=0;a<h;a++)b=f.eq(a),b.data("data-key",b.find("a").attr("data-key"))})();(function(){e.bind("click",function(){e.find("a").removeClass("DB_select");d(this).find("a").addClass("DB_select");var a=d(this).data("data-key");if("all"==a)f.show();
else for(var b=0;b<h;b++){var c=f.eq(b);a==c.data("data-key")?c.show():c.hide()}})})()})}})(jQuery);

	$j(function() {
    	$j('#DB_etc4').DB_brandList({

		})	

	});

</script>
<!-- gdline 107"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->
<!-- gdline 108"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->
<!-- gdline 109"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->
<!-- gdline 110"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" -->
<!-- gdline 111"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --></body>
<!-- gdline 112"/outline/_footer.htm|/outline/_footer.htm|goods/goods_view.htm footer_5" --></html><!-- gdpart mode="close" fid="goods/goods_view.htm footer_5" -->