<?php

include '../../conf/config.animationBanner_'.$cfg['tplSkinWork'].'.php';
include '../../Template_/tpl_plugin/function.animationBanner.php';

$animationBannerDir = '../../data/skin/'.$cfg['tplSkinWork'].'/img/animation_banner';
$animationBannerDataDir = $animationBannerDir.'/banner';
$animationBannerNaviDir = $animationBannerDir.'/navi';

$checked['enable'][$animationBannerConfig['enable']] = 'checked="checked"';
$checked['type'][$animationBannerConfig['type']] = 'checked="checked"';
$checked['directionAnchorDisplay'][$animationBannerConfig['directionAnchorDisplay']] = 'checked="checked"';
$checked['anchorDisplay'][$animationBannerConfig['anchorDisplay']] = 'checked="checked"';

$selected['duration'][$animationBannerConfig['duration']] = 'selected="selected"';
$selected['shiftType'][$animationBannerConfig['shiftType']] = 'selected="selected"';
$selected['interval'][$animationBannerConfig['interval']] = 'selected="selected"';
foreach ($animationBannerConfig['target'] as $index => $target) {
	$selected['target'][$index][$target] = 'selected="selected"';
}

?>
<script type="text/javascript" src="../../lib/js/AnimationBanner.js"></script>
<script type="text/javascript" src="../../lib/js/AnimationBannerLoader.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function(){
		window.previewImageCallback = function(inputName, index, imageName)
		{
			var animationBanner = $animationBanner.data("wrapperClass");
			if (inputName === "image") {
				var $fileInput = jQuery(".image-form").find("input[name=" + inputName + "\\\[\\\]]").eq(index);
				$fileInput.replaceWith($fileInput.clone(true));
				animationBanner.setImage(index, "<?php echo $animationBannerDataDir; ?>/" + imageName);
				animationBanner.setIndex(index);
				jQuery(".image-form").find("input[name=" + inputName + "URL\\\[\\\]]").eq(index).val(imageName);
				jQuery(".image-form").find("img.banner-preview").eq(index).css("display", "").attr("src", "<?php echo $animationBannerDataDir; ?>/" + imageName);
			}
			if (inputName === "onAnchor") {
				
				var $fileInput = jQuery(".anchor-form").find("input[name=" + inputName + "\\\[\\\]]").eq(index);
				$fileInput.replaceWith($fileInput.clone(true));
				jQuery(".anchor-form").find("input[name=" + inputName + "URL\\\[\\\]]").eq(index).val(imageName);
				jQuery(".anchor-form").find(".on-anchor-preview").eq(index).css("display", "").attr("src", "<?php echo $animationBannerNaviDir; ?>/" + imageName);
				jQuery("input[name=anchorDisplay]:checked").click();
			}
			if (inputName === "offAnchor") {
				var $fileInput = jQuery(".anchor-form").find("input[name=" + inputName + "\\\[\\\]]").eq(index);
				$fileInput.replaceWith($fileInput.clone(true));
				jQuery(".anchor-form").find("input[name=" + inputName + "URL\\\[\\\]]").eq(index).val(imageName);
				jQuery(".anchor-form").find(".on-anchor-preview").eq(index).css("display", "").attr("src", "<?php echo $animationBannerNaviDir; ?>/" + imageName);
				jQuery("input[name=anchorDisplay]:checked").click();
			}
		};
		var $animationBanner = jQuery(".animation-banner");
		var remove = function()
		{
			var $tr = jQuery(this).parent().parent().parent();
			var trIndex = $tr.attr("data-index");
			$tr.remove();
			jQuery(".anchor-form").eq(trIndex).remove();
			jQuery(".image-form").each(function(index, element){
				jQuery(element).attr("data-index", index);
			});
			jQuery(".image-form th").each(function(index, element){
				jQuery(element).text("이미지" + (index + 1));
			});
			jQuery(".anchor-form").each(function(index, element){
				jQuery("th", element).eq(0).text("활성 버튼" + (index + 1));
				jQuery("th", element).eq(1).text("비활성 버튼" + (index + 1));
			});
			$animationBanner.data("wrapperClass").removeBanner(trIndex);
			jQuery(".animation-banner").trigger("init");
		};
		var getPreviewImage = function(fileInput)
		{
			// 선택파일 체크
			if (fileInput.files) {
				var files = fileInput.files;
				for (var index = 0; index < files.length; index++) {
					var file = files[index];
					if (file.name.split(".").pop().match(/jpg|jpeg|gif|png|bmp/i) === null) {
						alert("이미지가 아닌 파일이 선택되었습니다.");
						jQuery(fileInput).replaceWith(jQuery(fileInput).clone(true));
						return false;
					}
				}
			}
			else {
				if (fileInput.value.split(".").pop().match(/jpg|jpeg|gif|png|bmp/i) === null) {
					alert("이미지가 아닌 파일이 선택되었습니다.");
					jQuery(fileInput).replaceWith(jQuery(fileInput).clone(true));
					return false;
				}
			}

			// 미리보기
			var form = fileInput.form;
			form.mode.value = "previewUpload";
			form.target = "ifrmHidden";
			form.submit();
			form.mode.value = "save";
			form.target = "";
		};
		var previewImage = function()
		{
			getPreviewImage(this);
			jQuery(".animation-banner").trigger("init");
		};

		jQuery("#form-submit").click(function(){
			jQuery("#image-form").remove();
			jQuery("#anchor-form").remove();
			if (jQuery("input[name=anchorDisplay]:checked").val() !== "custom") {
				jQuery(".anchor-form").remove();
			}
			jQuery("#animation-banner-form").submit();
		});
		jQuery("select[name=shiftType]").change(function(){
			if (this.value === "manual") {
				$animationBanner.data("wrapperClass").disableAutoShift();
				jQuery("select[name=interval]").css("display", "none");
			}
			else {
				$animationBanner.data("wrapperClass").enableAutoShift();
				jQuery("select[name=interval]").css("display", "");
			}
		});
		jQuery("#append-image-form").click(function(){
			var $imageForm = jQuery("#image-form").clone();
			var $anchorForm = jQuery("#anchor-form").clone();
			$imageForm.removeAttr("id").removeAttr("style").addClass("image-form").find("input, select").removeAttr("disabled");
			$anchorForm.removeAttr("id").addClass("anchor-form").find("input").removeAttr("disabled");
			if (jQuery("input[name=anchorDisplay]:checked").val() === "custom") {
				$anchorForm.removeAttr("style");
			}
			$imageForm.find(".remove").click(remove);
			$imageForm.find("input[name=image\\\[\\\]]").change(previewImage);
			jQuery(".image-form:last").after($imageForm);
			jQuery(".image-form").each(function(index, element){
				jQuery(element).attr("data-index", index);
			});
			jQuery(".image-form > th").each(function(index, element){
				jQuery(element).text("이미지" + (index + 1));
			});
			jQuery(".anchor-form:last").after($anchorForm);
			jQuery(".anchor-form").each(function(index, element){
				jQuery("th", element).eq(0).text("활성 버튼" + (index + 1));
				jQuery("th", element).eq(1).text("비활성 버튼" + (index + 1));
				jQuery("input[name=onAnchor\\\[\\\]], input[name=offAnchor\\\[\\\]]", element).change(previewImage);
			});

			var div = document.createElement("div");
			div.className = "banner-image";
			$animationBanner.data("wrapperClass").appendBanner(div, {
				"on":"<?php echo $animationBannerDir; ?>/navi_on.jpg",
				"off":"<?php echo $animationBannerDir; ?>/navi_off.jpg"
			});
			jQuery(".animation-banner").trigger("init");
		});

		jQuery("#banner-image .remove").click(remove);

		jQuery("input[name=anchorDisplay]").click(function(){
			if (this.value === "custom") {
				var animationBanner = $animationBanner.data("wrapperClass");
				var anchorList = new Array();
				jQuery(".anchor-container").css("display", "");
				jQuery(".anchor-form").removeAttr("style");
				jQuery(".anchor-form").each(function(index, element){
					var onImageURL = jQuery(element).find("input[name=onAnchorURL\\\[\\\]]").val();
					var offImageURL = jQuery(element).find("input[name=offAnchorURL\\\[\\\]]").val();
					var anchorURL = new Object();
					if (onImageURL) {
						anchorURL.on = "<?php echo $animationBannerNaviDir.'/'; ?>"+onImageURL;
					}
					else  {
						anchorURL.on = "<?php echo $animationBannerDir; ?>/navi_on.jpg";
					}
					if (offImageURL) {
						anchorURL.off = "<?php echo $animationBannerNaviDir.'/'; ?>"+offImageURL;
					}
					else {
						anchorURL.off = "<?php echo $animationBannerDir; ?>/navi_off.jpg";
					}
					anchorList.push(anchorURL);
				});
				animationBanner.setIndividualAnchor(anchorList);
				jQuery(".anchor-container > img").removeAttr("width").removeAttr("height");
				animationBanner.setIndex(animationBanner.getIndex());
			}
			else {
				jQuery(".anchor-form").css("display", "none");
				$animationBanner.data("wrapperClass").setUnifiedAnchor();
				if (this.value === "circle") {
					jQuery(".anchor-container").css("display", "").removeClass("square").addClass("circle");
				}
				else if (this.value === "square") {
					jQuery(".anchor-container").css("display", "").removeClass("circle").addClass("square");
				}
				else {
					jQuery(".anchor-container").css("display", "none");
				}
			}
		});
		jQuery("input[name=directionAnchorDisplay]").click(function(){
			if (this.value === "true") {
				jQuery("button.prev-button, button.next-button").css("display", "");
			}
			else {
				jQuery("button.prev-button, button.next-button").css("display", "none");
			}
		});

		var $animationBanner = jQuery(".animation-banner");

		$animationBanner.bind("init", function(){
			var animationBanner = $animationBanner.data("wrapperClass");
			animationBanner.setWidth(jQuery("input[name=width]").val());
			animationBanner.setHeight (jQuery("input[name=height]").val());
			animationBanner.setDuration(jQuery("select[name=duration]").val());
			animationBanner.setInterval(jQuery("select[name=interval]").val());
			if (jQuery("select[name=shiftType]").val() === "manual") {
				animationBanner.disableAutoShift();
			}
			else {
				animationBanner.enableAutoShift();
			}
			animationBanner.setDirection(AnimationBanner.DIRECTION_HORIZONTAL);
			animationBanner.setAnchorPosition(AnimationBanner.POSITION_BOTTOM_RIGHT);
			switch (jQuery("input[name=type]:checked").val()) {
				case "slide":
					animationBanner.start(AnimationBanner.ANIMATE_SLIDE);
					break;
				case "fade":
					animationBanner.start(AnimationBanner.ANIMATE_FADE);
					break;
				case "swipe":
					animationBanner.start(AnimationBanner.ANIMATE_SWIPE);
					break;
				case "blind":
					animationBanner.start(AnimationBanner.ANIMATE_BLIND);
					break;
				default:
					animationBanner.start(AnimationBanner.ANIMATE_PLAIN);
					break;
			}
		}).trigger("init");

		jQuery("input[name=type]").click(function(){
			$animationBanner.trigger("init");
		});
		jQuery("select[name=duration]").change(function(){
			$animationBanner.trigger("init");
		});
		jQuery("select[name=interval]").change(function(){
			$animationBanner.trigger("init");
		});
		jQuery("input[name=width]").blur(function(){
			$animationBanner.trigger("init");
		});
		jQuery("input[name=height]").blur(function(){
			$animationBanner.trigger("init");
		});

		jQuery(".image-form input[name=image\\\[\\\]]").change(previewImage);
		jQuery(".anchor-form input[name=onAnchor\\\[\\\]]").change(previewImage);
		jQuery(".anchor-form input[name=offAnchor\\\[\\\]]").change(previewImage);
		jQuery("select[name=shiftType]").trigger("change");
		jQuery("input[name=anchorDisplay]:checked").trigger("click");
	});
</script>
<style type="text/css">
	.animation-banner {
		margin: 20px auto;
		text-align: left;
	}
	#append-image-form {
		width: 34px;
		height: 17px;
		background-image: url("../img/i_add.gif");
		background-color: #ffffff;
		background-repeat: no-repeat;
		background-size: cover;
		background-position: top left;
		display: block;
		text-indent: -10000px;
		border: none;
		cursor: pointer;
	}
	.image-form .remove {
		width: 34px;
		height: 17px;
		background-image: url("../img/i_del.gif");
		background-color: #ffffff;
		background-repeat: no-repeat;
		background-size: cover;
		background-position: top left;
		display: block;
		text-indent: -10000px;
		border: none;
		cursor: pointer;
	}
</style>
<form id="animation-banner-form" enctype="multipart/form-data" action="adm_design_animation_banner.indb.php" method="post">
	<input type="hidden" name="mode" value="save"/>
	<h2 class="title">배너 설정</h2>

	<table class="admin-form-table">
		<tr>
			<th>사용설정</th>
			<td>
				<input type="radio" id="enable-true" name="enable" value="true" <?php echo $checked['enable']['true']; ?>/>
				<label for="enable-true">사용함</label>
				<input type="radio" id="enable-false" name="enable" value="false" <?php echo $checked['enable']['false']; ?>/>
				<label for="enable-false">사용안함</label>
			</td>
		</tr>
		<tr>
			<th>대상스킨</th>
			<td><?php echo $cfg['tplSkinWork']; ?></td>
		</tr>
	</table>

<?php animationBanner(true); ?>

	<table class="admin-form-table">
		<tr>
			<th>효과선택</th>
			<td colspan="3">
				<div style="float: left; width: 100px;">
					<label for="type-slide" style="display: block; padding-left: 10px;" onclick="this.click();">
						<img src="../img/animation_banner/icon_slide.jpg"/>
					</label>
					<input type="radio" id="type-slide" name="type" value="slide" <?php echo $checked['type']['slide']; ?>/>
					<label for="type-slide">슬라이드</label>
				</div>
				<div style="float: left; width: 100px;">
					<label for="type-fade" style="display: block; padding-left: 10px;" onclick="this.click();">
						<img src="../img/animation_banner/icon_fade.jpg"/>
					</label>
					<input type="radio" id="type-fade" name="type" value="fade" <?php echo $checked['type']['fade']; ?>/>
					<label for="type-fade">페이드</label>
				</div>
				<div style="float: left; width: 100px;">
					<label for="type-blind" style="display: block; padding-left: 10px;" onclick="this.click();">
						<img src="../img/animation_banner/icon_blind.jpg"/>
					</label>
					<input type="radio" id="type-blind" name="type" value="blind" <?php echo $checked['type']['blind']; ?>/>
					<label for="type-blind">블라인드</label>
				</div>
				<div style="float: left; width: 100px;">
					<label for="type-plain" style="display: block; padding-left: 10px;" onclick="this.click();">
						<img src="../img/animation_banner/icon_default.jpg"/>
					</label>
					<input type="radio" id="type-plain" name="type" value="plain" <?php echo $checked['type']['plain']; ?>/>
					<label for="type-plain">효과없음</label>
				</div>
			</td>
		</tr>
		<tr>
			<th>전환속도 선택</th>
			<td>
				<select name="duration">
					<option value="200" <?php echo $selected['duration'][200]; ?>>빠르게</option>
					<option value="400" <?php echo $selected['duration'][400]; ?>>보통</option>
					<option value="600" <?php echo $selected['duration'][600]; ?>>느리게</option>
				</select>
			</td>
			<th>좌우 전환 버튼</th>
			<td>
				<input id="direction-anchor-display-true" type="radio" name="directionAnchorDisplay" value="true" <?php echo $checked['directionAnchorDisplay']['true']; ?>/>
				<label for="direction-anchor-display-true">표시함</label>
				<input id="direction-anchor-display-false" type="radio" name="directionAnchorDisplay" value="false" <?php echo $checked['directionAnchorDisplay']['false']; ?>/>
				<label for="direction-anchor-display-false">표시안함</label>
			</td>
		</tr>
		<tr>
			<th>전환방법 설정</th>
			<td colspan="3">
				<select name="shiftType">
					<option value="auto" <?php echo $selected['shiftType']['auto']; ?>>자동</option>
					<option value="manual" <?php echo $selected['shiftType']['manual']; ?>>마우스클릭으로</option>
				</select>
				<select name="interval">
					<option value="1000" <?php echo $selected['interval'][1000]; ?>>1초</option>
					<option value="2000" <?php echo $selected['interval'][2000]; ?>>2초</option>
					<option value="3000" <?php echo $selected['interval'][3000]; ?>>3초</option>
					<option value="4000" <?php echo $selected['interval'][4000]; ?>>4초</option>
					<option value="5000" <?php echo $selected['interval'][5000]; ?>>5초</option>
				</select>
			</td>
		</tr>
	</table>

	<h2 class="title">배너이미지</h2>
	<table id="banner-image" class="admin-form-table">
		<tr>
			<th>배너사이즈</th>
			<td>
				<input type="text" name="width" value="<?php echo $animationBannerConfig['width']; ?>" style="width: 70px; text-align: center;"/>
				x
				<input type="text" name="height" value="<?php echo $animationBannerConfig['height']; ?>" style="width: 70px; text-align: center;"/>
				<span class="extext" style="font-size:11px;">작업스킨으로 선택된 스킨의 권장 배너사이즈입니다.</span>
			</td>
		</tr>
		<tr id="image-form" style="display: none;">
			<th></th>
			<td>
				<img class="banner-preview" src="" style="width: 80px; float: left; margin-right: 5px;" onerror="this.style.display='none';"/>
				<input type="hidden" name="imageURL[]" value="" disabled="disabled"/>
				<div style="float: left;">
					<input type="file" name="image[]" style="display: block; margin-bottom: 5px;" accept="image/*" disabled="disabled"/>
					링크 URL : <input type="text" name="link[]" class="lline" disabled="disabled"/>
					<select name="target[]" disabled="disabled">
						<option value="_self">현재창</option>
						<option value="_blank">새창</option>
					</select>
				</div>
				<div style="float: right;">
					<button class="remove" type="button">삭제</button>
				</div>
			</td>
		</tr>
		<tr class="image-form" data-index="0">
			<th>이미지1</th>
			<td>
				<img class="banner-preview" src="<?php echo $animationBannerDataDir.'/'.$animationBannerConfig['image'][0]; ?>" style="width: 80px; float: left; margin-right: 5px;" onerror="this.style.display='none';"/>
				<input type="hidden" name="imageURL[]" value="<?php echo $animationBannerConfig['image'][0]; ?>"/>
				<div style="float: left;">
					<input type="file" name="image[]" style="display: block; margin-bottom: 5px;" accept="image/*"/>
					링크 URL : <input type="text" name="link[]" class="lline" value="<?php echo $animationBannerConfig['link'][0]; ?>"/>
					<select name="target[]">
						<option value="_self" <?php echo $selected['target'][0]['_self']; ?>>현재창</option>
						<option value="_blank" <?php echo $selected['target'][0]['_blank']; ?>>새창</option>
					</select>
				</div>
				<div style="float: right;">
					<button id="append-image-form" type="button">추가</button>
				</div>
			</td>
		</tr>
		<?php for ($index = 1; $index < count($animationBannerConfig['image']); $index++) { ?>
		<tr class="image-form" data-index="<?php echo $index; ?>">
			<th>이미지<?php echo $index + 1; ?></th>
			<td>
				<img class="banner-preview" src="<?php echo $animationBannerDataDir.'/'.$animationBannerConfig['image'][$index]; ?>" style="width: 80px; float: left; margin-right: 5px;" onerror="this.style.display='none';"/>
				<input type="hidden" name="imageURL[]" value="<?php echo $animationBannerConfig['image'][$index]; ?>"/>
				<div style="float: left;">
					<input type="file" name="image[]" style="display: block; margin-bottom: 5px;" accept="image/*"/>
					링크 URL : <input type="text" name="link[]" class="lline" value="<?php echo $animationBannerConfig['link'][$index]; ?>"/>
					<select name="target[]">
						<option value="_self" <?php echo $selected['target'][$index]['_self']; ?>>현재창</option>
						<option value="_blank" <?php echo $selected['target'][$index]['_blank']; ?>>새창</option>
					</select>
				</div>
				<div style="float: right;">
					<button class="remove" type="button">삭제</button>
				</div>
			</td>
		</tr>
		<?php } ?>
	</table>

	<h2 class="title">내비게이션</h2>
	<table id="anchor-image" class="admin-form-table">
		<tr>
			<th>종류</th>
			<td colspan="3">
				<input id="anchor-display-circle" type="radio" name="anchorDisplay" value="circle" style="vertical-align: middle;" <?php echo $checked['anchorDisplay']['circle']; ?>/>
				<label for="anchor-display-circle" onclick="this.click();">
					<img src="../img/animation_banner/navi_type1.jpg" width="70" style="vertical-align: middle;"/>
				</label>
				<input id="anchor-display-square" type="radio" name="anchorDisplay" value="square" style="vertical-align: middle;" <?php echo $checked['anchorDisplay']['square']; ?>/>
				<label for="anchor-display-square" onclick="this.click();">
					<img src="../img/animation_banner/navi_type2.jpg" width="70" style="vertical-align: middle;"/>
				</label>
				<input id="anchor-display-custom" type="radio" name="anchorDisplay" value="custom" style="vertical-align: middle;" <?php echo $checked['anchorDisplay']['custom']; ?>/>
				<label for="anchor-display-custom">직접등록</label>
				<input id="anchor-display-false" type="radio" name="anchorDisplay" value="false" style="vertical-align: middle;" <?php echo $checked['anchorDisplay']['false']; ?>/>
				<label for="anchor-display-false">표시안함</label>
			</td>
		</tr>
		<tr id="anchor-form" style="display: none;">
			<th></th>
			<td>
				<input type="file" name="onAnchor[]" style="width: 170px;" accept="image/*" disabled="disabled"/>
				<input type="hidden" name="onAnchorURL[]" value="" disabled="disabled"/>
				<img class="on-anchor-preview" src="<?php echo $animationBannerNaviDir.'/'.$animationBannerConfig['offAnchor'][$index]; ?>" width="75" onerror="this.style.display='none';"/>
				<span class="extext" style="font-size: 11px;">권장: 75x30</span>
			</td>
			<th></th>
			<td>
				<input type="file" name="offAnchor[]" style="width: 170px;" accept="image/*" disabled="disabled"/>
				<input type="hidden" name="offAnchorURL[]" value="" disabled="disabled"/>
				<img class="off-anchor-preview" src="<?php echo $animationBannerNaviDir.'/'.$animationBannerConfig['offAnchor'][$index]; ?>" width="75" onerror="this.style.display='none';"/>
				<span class="extext" style="font-size: 11px;">권장: 75x30</span>
			</td>
		</tr>
		<tr class="anchor-form" style="display: none;">
			<th>활성 버튼1</th>
			<td>
				<input type="file" name="onAnchor[]" style="width: 170px;" accept="image/*"/>
				<input type="hidden" name="onAnchorURL[]" value="<?php echo $animationBannerConfig['onAnchor'][0]; ?>"/>
				<img class="on-anchor-preview" src="<?php echo $animationBannerNaviDir.'/'.$animationBannerConfig['onAnchor'][0]; ?>" width="75" onerror="this.style.display='none';"/>
				<span class="extext" style="font-size: 11px;">권장: 75x30</span>
			</td>
			<th>비활성 버튼1</th>
			<td>
				<input type="file" name="offAnchor[]" style="width: 170px;" accept="image/*"/>
				<input type="hidden" name="offAnchorURL[]" value="<?php echo $animationBannerConfig['offAnchor'][0]; ?>"/>
				<img class="off-anchor-preview" src="<?php echo $animationBannerNaviDir.'/'.$animationBannerConfig['offAnchor'][0]; ?>" width="75" onerror="this.style.display='none';"/>
				<span class="extext" style="font-size: 11px;">권장: 75x30</span>
			</td>
		</tr>
		<?php for ($index = 1; $index < count($animationBannerConfig['image']); $index++) { ?>
		<tr class="anchor-form" style="display: none;">
			<th>활성 버튼<?php echo $index + 1; ?></th>
			<td>
				<input type="file" name="onAnchor[]" style="width: 170px;" accept="image/*"/>
				<input type="hidden" name="onAnchorURL[]" value="<?php echo $animationBannerConfig['onAnchor'][$index]; ?>"/>
				<img class="on-anchor-preview" src="<?php echo $animationBannerNaviDir.'/'.$animationBannerConfig['onAnchor'][$index]; ?>" width="75" onerror="this.style.display='none';"/>
				<span class="extext" style="font-size: 11px;">권장: 75x30</span>
			</td>
			<th>비활성 버튼<?php echo $index + 1; ?></th>
			<td>
				<input type="file" name="offAnchor[]" style="width: 170px;" accept="image/*"/>
				<input type="hidden" name="offAnchorURL[]" value="<?php echo $animationBannerConfig['offAnchor'][$index]; ?>"/>
				<img class="off-anchor-preview" src="<?php echo $animationBannerNaviDir.'/'.$animationBannerConfig['offAnchor'][$index]; ?>" width="75" onerror="this.style.display='none';"/>
				<span class="extext" style="font-size: 11px;">권장: 75x30</span>
			</td>
		</tr>
		<?php } ?>
	</table>

	<div class="button">
		<input id="form-submit" type="image" src="../img/btn_register.gif"/>
	</div>
</form>

<div style="border:1px solid #cccccc;background-color:#eeeeee;text-align:center;padding:10px 0px;font-size:13pt;font-weight:bold;">
	디자인코디에서 사용가능한 치환코드 : &nbsp;  {=animationBanner()}
</div>