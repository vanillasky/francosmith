<?php

function animationBanner($preview = false)
{
	global $cfg;
	if (!$cfg) {
		include dirname(__FILE__).'/../../conf/config.php';
	}

	if ($preview === true) $skin = $cfg['tplSkinWork'];
	else $skin = $cfg['tplSkin'];

	include dirname(__FILE__).'/../../conf/config.animationBanner_'.$skin.'.php';

	if ($preview === true) {
		foreach ($animationBannerConfig['link'] as $index => $value) {
			$animationBannerConfig['link'][$index] = null;
			$animationBannerConfig['target'][$index] = null;
		}
	}
	else {
		if ($animationBannerConfig['enable'] === 'false') return false;
	}

	$animationBannerDir = $cfg['rootDir'].'/data/skin/'.$skin.'/img/animation_banner';
	$animationBannerDataDir = $animationBannerDir.'/banner';
	$animationBannerNaviDir = $animationBannerDir.'/navi';
?>
<style type="text/css">
	.animation-banner {
		text-align: left;
	}
	.animation-banner .banner-image {
		background-color: #ffffff;
	}
	.animation-banner .prev-button {
		width: 46px;
		height: 103px;
		border: none;
		background-color: transparent;
		background-image: url('<?php echo $animationBannerDir; ?>/btn_leftslide.jpg');
		background-repeat: no-repeat;
		background-position: left top;
		background-size: 100% 100%;
		text-indent: -10000px;
		cursor: pointer;
	}
	.animation-banner .next-button {
		width: 46px;
		height: 103px;
		border: none;
		background-color: transparent;
		background-image: url('<?php echo $animationBannerDir; ?>/btn_rightslide.jpg');
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
		width: 16px;
		height: 16px;
		margin-left: 8px;
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
		background-image: url('<?php echo $animationBannerDir; ?>/circle_off.png');
	}
	.animation-banner .anchor-container.circle .anchor-button.active {
		background-image: url('<?php echo $animationBannerDir; ?>/circle_on.png');
	}
	.animation-banner .anchor-container.square .anchor-button {
		background-image: url('<?php echo $animationBannerDir; ?>/square_off.png');
	}
	.animation-banner .anchor-container.square .anchor-button.active {
		background-image: url('<?php echo $animationBannerDir; ?>/square_on.png');
	}
</style>
<div class="animation-banner"
     data-method="<?php echo $animationBannerConfig['type']; ?>"
     data-width="<?php echo $animationBannerConfig['width']; ?>"
     data-height="<?php echo $animationBannerConfig['height']; ?>"
     data-duration="<?php echo $animationBannerConfig['duration']; ?>"
     data-interval="<?php echo $animationBannerConfig['interval']; ?>"
     data-shift-type="<?php echo $animationBannerConfig['shiftType']; ?>"
     data-anchor-position="bottom-right">
	<button class="prev-button" type="button" style="<?php if ($animationBannerConfig['directionAnchorDisplay'] !== 'true') echo 'display: none;'; ?>">이전</button>
	<button class="next-button" type="button" style="<?php if ($animationBannerConfig['directionAnchorDisplay'] !== 'true') echo 'display: none;'; ?>">다음</button>
	<div class="anchor-container <?php echo $animationBannerConfig['anchorDisplay']; ?>">
<?php if ($animationBannerConfig['anchorDisplay'] === 'custom') { foreach ($animationBannerConfig['onAnchor'] as $index => $fileName) { ?>
		<div data-on="<?php echo $fileName ? $animationBannerNaviDir.'/'.$fileName : $animationBannerDir.'/navi_on.jpg'; ?>"
		     data-off="<?php echo $animationBannerConfig['offAnchor'][$index] ? $animationBannerNaviDir.'/'.$animationBannerConfig['offAnchor'][$index] : $animationBannerDir.'/navi_off.jpg'; ?>"></div>
<?php }} ?>
	</div>
	<button class="anchor-button" type="button"></button>
<?php foreach ($animationBannerConfig['image'] as $index => $image) { ?>
	<div class="banner-image"
	     data-image="<?php echo $animationBannerDataDir.'/'.$image; ?>"
	     data-link="<?php echo $animationBannerConfig['link'][$index]; ?>"
	     data-target="<?php echo $animationBannerConfig['target'][$index]; ?>"></div>
<?php } ?>
</div>
<?php

}