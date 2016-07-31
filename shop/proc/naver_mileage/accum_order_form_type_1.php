<?php ob_start(); ?>
<dt class="naver-mileage-accum-place naver-mileage-accum">적립금<br/>적립위치</dt>
<dd class="naver-mileage-accum-place naver-mileage-accum">
	<?php if ($load_config_ncash['save_mode'] == 'ncash') { ?>
	<span>적립금이 네이버 마일리지로 적립됩니다.</span>
	<input type="hidden" name="save_mode" value="unused"/>
	<?php } else if ($load_config_ncash['save_mode'] == 'both') { ?>
	<span>
		적립금이 네이버 마일리지와 쇼핑몰<br/>
		둘다 적립됩니다.<br/>
		(네이버 마일리지를 적립받으시려면<br/>
		"적립 및 사용하기" 버튼을 눌러주세요.)
	</span>
	<input type="hidden" name="save_mode" value="both"/>
	<?php } else if ($load_config_ncash['save_mode'] == 'choice') { ?>
	<input type="radio" name="save_mode" id="save-mode-default" value="" required="required" label="적립금 적립 위치"/>
	<label for="save-mode-default"> 적립금을 쇼핑몰 적립금으로 적립합니다.</label><br/>
	<input type="radio" name="save_mode" id="save-mode-ncash" value="ncash"/>
	<label for="save-mode-ncash"> 적립금을 네이버 마일리지로 적립합니다.</label>
	<?php } ?>
</dd>
<dt class="naver-mileage-accum-set naver-mileage-accum">네이버<br/>마일리지 <img src="<?php echo $GLOBALS['cfg']['rootDir']; ?>/proc/naver_mileage/images/n_mileage_on.png"/></dt>
<dd class="naver-mileage-accum-set naver-mileage-accum">
	<div id="naver-mileage-container"></div>
	<input type="hidden" id="reqTxId<?php echo $load_config_ncash['api_id']; ?>" name="reqTxId<?php echo $load_config_ncash['api_id']; ?>" value="">
	<input type="hidden" id="mileageUseAmount<?php echo $load_config_ncash['api_id']; ?>" name="mileageUseAmount<?php echo $load_config_ncash['api_id']; ?>" value="0">
	<input type="hidden" id="cashUseAmount<?php echo $load_config_ncash['api_id']; ?>" name="cashUseAmount<?php echo $load_config_ncash['api_id']; ?>" value="0">
	<input type="hidden" id="totalUseAmount<?php echo $load_config_ncash['api_id']; ?>" name="totalUseAmount<?php echo $load_config_ncash['api_id']; ?>" value="0">
	<input type="hidden" id="exception_price" name="exception_price" value="<?php echo $load_config_ncash['exception_price']; ?>">
	<input type="hidden" id="baseAccumRate" name="baseAccumRate" value="">
	<input type="hidden" id="addAccumRate" name="addAccumRate" value="">
</dd>
<?php return ob_get_clean(); ?>