<?php

function mobileAnimationBanner($preview = false)
{
	global $cfg;
	if (!$cfg) {
		include dirname(__FILE__).'/../../conf/config.php';
	}

	if ($preview === true) $skin = $cfg['tplSkinMobileWork'];
	else $skin = $cfg['tplSkinMobile'];

	include dirname(__FILE__).'/../../conf/config.mobileAnimationBanner_'.$skin.'.php';

	if ($preview === true) {
		foreach ($mobileAnimationBannerConfig['link'] as $index => $value) {
			$mobileAnimationBannerConfig['link'][$index] = null;
			$mobileAnimationBannerConfig['target'][$index] = null;
		}
	}
	else {
		if ($mobileAnimationBannerConfig['enable'] === 'false') return false;
	}

	$mobileAnimationBannerDir = $cfg['rootDir'].'/data/skin_mobileV2/'.$skin.'/common/img/animation_banner';
	$mobileAnimationBannerDataDir = $mobileAnimationBannerDir.'/banner';
	$mobileAnimationBannerNaviDir = $mobileAnimationBannerDir.'/navi';

	if($mobileAnimationBannerConfig['width'] == '') $mobileAnimationBannerConfig['width'] = 320;
	if($mobileAnimationBannerConfig['height'] == '') $mobileAnimationBannerConfig['height'] = 0;
?>
<style type="text/css">
	.animation-banner {
		text-align: left;
	}
	.animation-banner .banner-image {
		background-color: #ffffff;
	}
	.animation-banner .prev-button {
		width: 36px;
		height: 36px;
		border: none;
		background-color: transparent;
		background-image: url('<?php echo $mobileAnimationBannerDir; ?>/btn_leftslide2.png');
		background-repeat: no-repeat;
		background-position: left top;
		background-size: 100% 100%;
		text-indent: -10000px;
		cursor: pointer;
	}
	.animation-banner .next-button {
		width: 36px;
		height: 36px;
		border: none;
		background-color: transparent;
		background-image: url('<?php echo $mobileAnimationBannerDir; ?>/btn_rightslide2.png');
		background-repeat: no-repeat;
		background-position: left top;
		background-size: 100% 100%;
		text-indent: -10000px;
		cursor: pointer;
	}
	.animation-banner .anchor-container {
		margin-right: 30px;
		margin-bottom: 20px;
	}
	.animation-banner .anchor-container .anchor-button {
		width: 10px;
		height: 10px;
		margin-left: 4px;
		border: none;
		background-color: transparent;
		background-repeat: no-repeat;
		background-position: left top;
		background-size: 100% 100%;
		text-indent: -10000px;
		padding: 0;
		display: block;
		cursor: pointer;
	}
	.animation-banner .anchor-container.circle .anchor-button {
		background-image: url('<?php echo $mobileAnimationBannerDir; ?>/circle_off.png');
	}
	.animation-banner .anchor-container.circle .anchor-button.active {
		background-image: url('<?php echo $mobileAnimationBannerDir; ?>/circle_on.png');
	}
	.animation-banner .anchor-container.square .anchor-button {
		background-image: url('<?php echo $mobileAnimationBannerDir; ?>/square_off.png');
	}
	.animation-banner .anchor-container.square .anchor-button.active {
		background-image: url('<?php echo $mobileAnimationBannerDir; ?>/square_on.png');
	}
</style>
<div class="animation-banner"
     data-method="<?php echo $mobileAnimationBannerConfig['type']; ?>"
     data-width="<?php echo $mobileAnimationBannerConfig['width']; ?>"
     data-height="<?php echo $mobileAnimationBannerConfig['height']; ?>"
     data-duration="<?php echo $mobileAnimationBannerConfig['duration']; ?>"
     data-interval="<?php echo $mobileAnimationBannerConfig['interval']; ?>"
     data-shift-type="<?php echo $mobileAnimationBannerConfig['shiftType']; ?>"
<?php if (isset($mobileAnimationBannerConfig['imageWidth']) === true && $mobileAnimationBannerConfig['imageWidth'] != '') {?>data-image-width="<?php echo $mobileAnimationBannerConfig['imageWidth']; ?>"<?php }?>
<?php if (isset($mobileAnimationBannerConfig['imageHeight']) === true && $mobileAnimationBannerConfig['imageHeight'] != '') {?>data-image-height="<?php echo $mobileAnimationBannerConfig['imageHeight']; ?>"<?php }?>
     data-anchor-position="bottom-right">
	<button class="prev-button" type="button" style="<?php if ($mobileAnimationBannerConfig['directionAnchorDisplay'] !== 'true') echo 'display: none;'; ?>">이전</button>
	<button class="next-button" type="button" style="<?php if ($mobileAnimationBannerConfig['directionAnchorDisplay'] !== 'true') echo 'display: none;'; ?>">다음</button>
	<div class="anchor-container <?php echo $mobileAnimationBannerConfig['anchorDisplay']; ?>">
<?php if ($mobileAnimationBannerConfig['anchorDisplay'] === 'custom') { foreach ($mobileAnimationBannerConfig['onAnchor'] as $index => $fileName) { ?>
		<div data-on="<?php echo $fileName ? $mobileAnimationBannerNaviDir.'/'.$fileName : $mobileAnimationBannerDir.'/navi_on.jpg'; ?>"
		     data-off="<?php echo $mobileAnimationBannerConfig['offAnchor'][$index] ? $mobileAnimationBannerNaviDir.'/'.$mobileAnimationBannerConfig['offAnchor'][$index] : $mobileAnimationBannerDir.'/navi_off.jpg'; ?>"></div>
<?php }} ?>
	</div>
	<button class="anchor-button" type="button"></button>
<?php 
	if(empty($mobileAnimationBannerConfig['image'])) { ?>
	<div class="banner-image"
	     data-image=""
	     data-link=""
	     data-target=""></div>
<?php
	} else {
		foreach ($mobileAnimationBannerConfig['image'] as $index => $image) { ?>
	<div class="banner-image"
	     data-image="<?php echo $mobileAnimationBannerDataDir.'/'.$image; ?>"
	     data-link="<?php echo $mobileAnimationBannerConfig['link'][$index]; ?>"
	     data-target="<?php echo $mobileAnimationBannerConfig['target'][$index]; ?>"></div>
<?php }} ?>
</div>
<?php

}