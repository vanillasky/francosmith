<?php ob_start(); ?>
<style type="text/css">
.gs_naver_mileage_title {
	float: left;
}
.gs_naver_mileage{
	vertical-align: middle;
	margin-left: 6px;
}
.naver-mileage-accum {
	display: none;
}
.naver-mileage-accum-rate{
	font-weight: bold;
	color: #1ec228;
}
</style>
<dt class="naver-mileage-accum gs_naver_mileage_title blt">네이버 마일리지 : </dt>
<dd class="naver-mileage-accum gs_naver_mileage">
	<?php if ($N_ex) { ?>
	<?php echo $N_ex; ?>
	<?php } else { ?>
	<span class="naver-mileage-accum-rate"></span> 적립 <img src="<?php echo $GLOBALS['cfg']['rootDir']; ?>/proc/naver_mileage/images/n_mileage_on.png"/>
	<?php } ?>
</dd>
<?php return ob_get_clean(); ?>