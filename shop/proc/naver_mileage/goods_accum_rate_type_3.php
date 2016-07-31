<?php ob_start(); ?>
<style type="text/css">
.naver-mileage-accum-rate{
	font-weight: bold;
	color: #1ec228;
}
</style>
<script type="text/javascript">
var naver_mileage_accum_list = new Object();
<?php foreach ($naverMileageAccumrateList as $key => $value) { ?>
naver_mileage_accum_list["<?php echo $key; ?>"] = true;
<?php } ?>
jQuery(".goods-nvmileage").each(function(index, element){
	var goodsno = jQuery(element).attr("data-goodsno");
	element.className = "naver-mileage-accum";
	if (naver_mileage_accum_list[goodsno]) {
		var
		naver_mileage = document.createElement("div"),
		naver_mileage_accum_rate = document.createElement("span"),
		item = jQuery(element).parent().parent().parent();
		naver_mileage_accum_rate.className = "naver-mileage-accum-rate";
		jQuery(naver_mileage).text("네이버 마일리지 : ").append(naver_mileage_accum_rate).append(" 적립 <img src=\"<?php echo $GLOBALS['cfg']['rootDir']; ?>/proc/naver_mileage/images/n_mileage_on.png\"/>");
		jQuery(element).append(naver_mileage);
		item.css({"height" : (item.height()+18).toString()+"px", "background-size" : "1px "+(item.height()+18).toString()+"px"});
	}
});
</script>
<?php return ob_get_clean(); ?>