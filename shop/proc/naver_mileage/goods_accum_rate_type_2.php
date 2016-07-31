<?php ob_start(); ?>
<style type="text/css">
.gs_naver_mileage{
	margin: 10px 0;
}
.gs_naver_mileage dt{
	float: left;
}
.gs_naver_mileage dd{
	vertical-align: middle;
}
#naver-mileage-accum-rate{
	font-weight: bold;
	color: #1ec228;
}
</style>
<dl id="naver-mileage-accum" class="gs_naver_mileage">
	<dt class="blt">네이버 마일리지 : </dt>
	<dd>
		<?php if ($N_ex) { ?>
		<?php echo $N_ex; ?>
		<?php } else { ?>
		<span id="naver-mileage-accum-rate"></span> 적립 <img src="<?php echo $GLOBALS['cfg']['rootDir']; ?>/proc/naver_mileage/images/n_mileage_on.png"/>
		<?php } ?>
	</dd>
</dl>
<?php return ob_get_clean(); ?>