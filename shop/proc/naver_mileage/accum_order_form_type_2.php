<?php ob_start(); ?>
<div class="naver-mileage-accum-place m_list naver-mileage-accum">
	<div class="m_title" style="line-height: 25px; padding: 0;">적립금<br/>적립위치</div>
	<div class="m_right" style="line-height: 18px; padding: 5px; font-size: 12px; position: absolute; left: 70px;">
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
		<label for="save-mode-default" style="overflow-x: hidden;"> 적립금을 쇼핑몰 적립금으로 적립합니다.</label><br/>
		<input type="radio" name="save_mode" id="save-mode-ncash" value="ncash"/>
		<label for="save-mode-ncash" style="overflow-x: hidden;"> 적립금을 네이버 마일리지로 적립합니다.</label>
		<?php } ?>
	</div>
</div>
<div class="naver-mileage-accum-set m_list naver-mileage-accum" style="height: 90px;">
	<div class="m_title" style="line-height: 25px; padding: 20px 0 20px 0;">네이버<br/>마일리지 <img src="<?php echo $GLOBALS['cfg']['rootDir']; ?>/proc/naver_mileage/images/n_mileage_on.png"/></div>
	<div class="m_right" style="padding: 5px;">
		<div id="naver-mileage-container"></div>
		<input type="hidden" id="reqTxId<?php echo $load_config_ncash['api_id']; ?>" name="reqTxId<?php echo $load_config_ncash['api_id']; ?>" value="">
		<input type="hidden" id="mileageUseAmount<?php echo $load_config_ncash['api_id']; ?>" name="mileageUseAmount<?php echo $load_config_ncash['api_id']; ?>" value="0">
		<input type="hidden" id="cashUseAmount<?php echo $load_config_ncash['api_id']; ?>" name="cashUseAmount<?php echo $load_config_ncash['api_id']; ?>" value="0">
		<input type="hidden" id="totalUseAmount<?php echo $load_config_ncash['api_id']; ?>" name="totalUseAmount<?php echo $load_config_ncash['api_id']; ?>" value="0">
		<input type="hidden" id="exception_price" name="exception_price" value="<?php echo $load_config_ncash['exception_price']; ?>">
		<input type="hidden" id="baseAccumRate" name="baseAccumRate" value="">
		<input type="hidden" id="addAccumRate" name="addAccumRate" value="">
	</div>
</div>
<script type="text/javascript">
(function(){
	// 네이버 마일리지 적립위치에 따라 안내문구 길이가 변경됨에 따라 엘리먼트 길이도 자동 맞춤
	var titleHeight = $('div.naver-mileage-accum-place div.m_title').height();
	var rightHeight = $('div.naver-mileage-accum-place div.m_right').height()+10;
	var maxHeight = (titleHeight > rightHeight) ? titleHeight : rightHeight;
	var minHeight = (titleHeight > rightHeight) ? rightHeight : titleHeight;
	if ($('div.naver-mileage-accum-place').height() < maxHeight) {
		$('div.naver-mileage-accum-place div.m_title').css('paddingTop', (maxHeight-minHeight)/2).css('paddingBottom', (maxHeight-minHeight)/2);
		$('div.naver-mileage-accum-place').height(maxHeight);
	}
})();
</script>
<?php return ob_get_clean(); ?>