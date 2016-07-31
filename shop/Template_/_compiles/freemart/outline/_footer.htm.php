<?php /* Template_ 2.2.7 2016/04/06 19:42:10 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/outline/_footer.htm 000001981 */ ?>
</tr>
</table>

<?php if($this->tpl_['footer_inc']){?>
<table width="99%" cellpadding=0 cellspacing=0 border=0>
<tr>
<td><?php $this->print_("footer_inc",$TPL_SCP,1);?></td>
</tr>
<?php }?>
</table>

<!-- 절대! 지우지마세요 : Start -->
<iframe name="ifrmHidden" src='<?php echo $GLOBALS["cfg"]["rootDir"]?>/blank.php' style="display:none;width:100%;height:600"></iframe>
<!-- 절대! 지우지마세요 : End -->

<script>
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

<script type="text/javascript">
	
	var $j = jQuery.noConflict();	


;(function(d){d.fn.DB_brandList=function(c){d.extend({},c);return this.each(function(){var g=d(this),e=g.find(".DB_btn li"),c=e.length,f=g.find(".DB_list").find("li"),h=f.length;(function(){for(var a=0;a<c;a++){var b=e.eq(a);b.data("data-key",b.find("a").text())}for(a=0;a<h;a++)b=f.eq(a),b.data("data-key",b.find("a").attr("data-key"))})();(function(){e.bind("click",function(){e.find("a").removeClass("DB_select");d(this).find("a").addClass("DB_select");var a=d(this).data("data-key");if("all"==a)f.show();
else for(var b=0;b<h;b++){var c=f.eq(b);a==c.data("data-key")?c.show():c.hide()}})})()})}})(jQuery);

	$j(function() {
    	$j('#DB_etc4').DB_brandList({

		})	

	});

	
</script>




</body>
</html>