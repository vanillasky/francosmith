<?php
include "../_header.popup.php";

include '../../conf/config.mobileAnimationBanner_'.$cfg['tplSkinMobileWork'].'.php';
include '../../Template_/tpl_plugin/function.mobileAnimationBanner.php';

$mobileAnimationBannerDir = '../../data/skin_mobileV2/'.$cfg['tplSkinMobileWork'].'/common/img/animation_banner';
$mobileAnimationBannerDataDir = $mobileAnimationBannerDir.'/banner';
$mobileAnimationBannerNaviDir = $mobileAnimationBannerDir.'/navi';

if($mobileAnimationBannerConfig['enable']) $checked['enable'][$mobileAnimationBannerConfig['enable']] = 'checked="checked"';
else $checked['enable']['false'] = 'checked="checked"';
if($mobileAnimationBannerConfig['type']) $checked['type'][$mobileAnimationBannerConfig['type']] = 'checked="checked"';
else $checked['type']['slide'] = 'checked="checked"';
if($mobileAnimationBannerConfig['directionAnchorDisplay']) $checked['directionAnchorDisplay'][$mobileAnimationBannerConfig['directionAnchorDisplay']] = 'checked="checked"';
else $checked['directionAnchorDisplay']['true'] = 'checked="checked"';
if($mobileAnimationBannerConfig['anchorDisplay']) $checked['anchorDisplay'][$mobileAnimationBannerConfig['anchorDisplay']] = 'checked="checked"';
else $checked['anchorDisplay']['false'] = 'checked="checked"';

$selected['duration'][$mobileAnimationBannerConfig['duration']] = 'selected="selected"';
$selected['shiftType'][$mobileAnimationBannerConfig['shiftType']] = 'selected="selected"';
$selected['interval'][$mobileAnimationBannerConfig['interval']] = 'selected="selected"';
foreach ($mobileAnimationBannerConfig['target'] as $index => $target) {
	$selected['target'][$index][$target] = 'selected="selected"';
}

?>
<script type="text/javascript" src="../../lib/js/jquery-1.10.2.min.js"></script>
<script type="text/javascript" src="../../lib/js/jquery.touchSwipe.min.js"></script>
<script type="text/javascript" src="../../lib/js/MobileAnimationBanner.js"></script>
<script type="text/javascript" src="../../lib/js/MobileAnimationBannerLoader.js"></script>
<script type="text/javascript">
	jQuery(document).ready(function(){
		window.previewImageCallback = function(inputName, index, imageName, imageWidth, imageHeight)
		{
			var animationBanner = $mobileAnimationBanner.data("wrapperClass");

			if(jQuery("input[name=directionAnchorDisplay]:checked").val() == 'true') {
				jQuery("button.prev-button, button.next-button").css("display", "");
			}
			else {
				jQuery("button.prev-button, button.next-button").css("display", "none");
			}

			if (inputName === "image") {
				if(index == 0) {
					$("[name='imageWidth']").val(imageWidth);
					$("[name='imageHeight']").val(imageHeight);
					$("[name='height']").val(Math.round(imageHeight * ($("[name='width']").val() / imageWidth)));
					$mobileAnimationBanner.trigger("init");
				}
				var $fileInput = jQuery(".image-form").find("input[name=" + inputName + "\\\[\\\]]").eq(index);
				$fileInput.replaceWith($fileInput.clone(true));
				animationBanner.setImage(index, "<?php echo $mobileAnimationBannerDataDir; ?>/" + imageName);
				animationBanner.setIndex(index);
				jQuery(".image-form").find("input[name=" + inputName + "URL\\\[\\\]]").eq(index).val(imageName);
				jQuery(".image-form").find("img.banner-preview").eq(index).css("display", "").attr("src", "<?php echo $mobileAnimationBannerDataDir; ?>/" + imageName);
			}
			if (inputName === "onAnchor") {
				
				var $fileInput = jQuery(".anchor-form").find("input[name=" + inputName + "\\\[\\\]]").eq(index);
				$fileInput.replaceWith($fileInput.clone(true));
				jQuery(".anchor-form").find("input[name=" + inputName + "URL\\\[\\\]]").eq(index).val(imageName);
				jQuery(".anchor-form").find(".on-anchor-preview").eq(index).css("display", "").attr("src", "<?php echo $mobileAnimationBannerNaviDir; ?>/" + imageName);
				jQuery("input[name=anchorDisplay]:checked").click();
			}
			if (inputName === "offAnchor") {
				var $fileInput = jQuery(".anchor-form").find("input[name=" + inputName + "\\\[\\\]]").eq(index);
				$fileInput.replaceWith($fileInput.clone(true));
				jQuery(".anchor-form").find("input[name=" + inputName + "URL\\\[\\\]]").eq(index).val(imageName);
				jQuery(".anchor-form").find(".on-anchor-preview").eq(index).css("display", "").attr("src", "<?php echo $mobileAnimationBannerNaviDir; ?>/" + imageName);
				jQuery("input[name=anchorDisplay]:checked").click();
			}
		};
		var $mobileAnimationBanner = jQuery(".animation-banner");
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
			$mobileAnimationBanner.data("wrapperClass").removeBanner(trIndex);
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
				$mobileAnimationBanner.data("wrapperClass").disableAutoShift();
				jQuery("select[name=interval]").css("display", "none");
			}
			else {
				$mobileAnimationBanner.data("wrapperClass").enableAutoShift();
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
			$mobileAnimationBanner.data("wrapperClass").appendBanner(div, {
				"on":"<?php echo $mobileAnimationBannerDir; ?>/navi_on.jpg",
				"off":"<?php echo $mobileAnimationBannerDir; ?>/navi_off.jpg"
			});
			jQuery(".animation-banner").trigger("init");
		});

		jQuery("#banner-image .remove").click(remove);

		jQuery("input[name=anchorDisplay]").click(function(){
			if (this.value === "custom") {
				var animationBanner = $mobileAnimationBanner.data("wrapperClass");
				var anchorList = new Array();
				jQuery(".anchor-container").css("display", "");
				jQuery(".anchor-form").removeAttr("style");
				jQuery(".anchor-form").each(function(index, element){
					var onImageURL = jQuery(element).find("input[name=onAnchorURL\\\[\\\]]").val();
					var offImageURL = jQuery(element).find("input[name=offAnchorURL\\\[\\\]]").val();
					var anchorURL = new Object();
					if (onImageURL) {
						anchorURL.on = "<?php echo $mobileAnimationBannerNaviDir.'/'; ?>"+onImageURL;
					}
					else  {
						anchorURL.on = "<?php echo $mobileAnimationBannerDir; ?>/navi_on.jpg";
					}
					if (offImageURL) {
						anchorURL.off = "<?php echo $mobileAnimationBannerNaviDir.'/'; ?>"+offImageURL;
					}
					else {
						anchorURL.off = "<?php echo $mobileAnimationBannerDir; ?>/navi_off.jpg";
					}
					anchorList.push(anchorURL);
				});
				animationBanner.setIndividualAnchor(anchorList);
				jQuery(".anchor-container > img").removeAttr("width").removeAttr("height");
				animationBanner.setIndex(animationBanner.getIndex());
			}
			else {
				jQuery(".anchor-form").css("display", "none");
				$mobileAnimationBanner.data("wrapperClass").setUnifiedAnchor();
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

		var $mobileAnimationBanner = jQuery(".animation-banner");

		$mobileAnimationBanner.bind("init", function(){
			var animationBanner = $mobileAnimationBanner.data("wrapperClass");
			animationBanner.setWidth(jQuery("select[name=width]").val());
			animationBanner.setHeight(jQuery("input[name=height]").val());
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
			table_design_load();
			setHeight_ifrmCodi();
		}).trigger("init");

		jQuery("input[name=type]").click(function(){
			$mobileAnimationBanner.trigger("init");
		});
		jQuery("select[name=duration]").change(function(){
			$mobileAnimationBanner.trigger("init");
		});
		jQuery("select[name=interval]").change(function(){
			$mobileAnimationBanner.trigger("init");
		});
		jQuery("input[name=width]").blur(function(){
			$mobileAnimationBanner.trigger("init");
		});
		jQuery("input[name=height]").blur(function(){
			$mobileAnimationBanner.trigger("init");
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
<form id="animation-banner-form" enctype="multipart/form-data" action="iframe.animation_banner.indb.php" method="post">
	<input type="hidden" name="mode" value="save"/>
	<input type="hidden" name="width" value="320"/>
	<input type="hidden" name="imageWidth" value="<?php echo $mobileAnimationBannerConfig['imageWidth']; ?>"/>
	<input type="hidden" name="imageHeight" value="<?php echo $mobileAnimationBannerConfig['imageHeight']; ?>"/>
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
			<td><?php echo $cfg['tplSkinMobileWork']; ?></td>
		</tr>
	</table>

	<?php mobileAnimationBanner(true); ?>

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
					<option value="3000" <?php echo $selected['interval'][3000]; ?>>3초</option>
					<option value="4000" <?php echo $selected['interval'][4000]; ?>>4초</option>
					<option value="5000" <?php echo $selected['interval'][5000]; ?>>5초</option>
					<option value="7000" <?php echo $selected['interval'][7000]; ?>>7초</option>
					<option value="10000" <?php echo $selected['interval'][10000]; ?>>10초</option>
				</select>
			</td>
		</tr>
	</table>

	<h2 class="title">배너이미지</h2>
	<table id="banner-image" class="admin-form-table">
		<tr>
			<th>배너넓이</th>
			<td>
				<span class="extext" style="font-size:11px;">디바이스의 넓이에 가득 채워져 화면에 출력되며, 상단의 미리보기용으로만 사용됩니다.</span>
			</td>
		</tr>
		<tr>
			<th>배너높이</th>
			<td>
				<input type="hidden" name="height" value="<?php echo $mobileAnimationBannerConfig['height']; ?>" style="width: 70px; text-align: center;"/>
				<span class="extext" style="font-size:11px;">디바이스의 가로사이즈에 따라 이미지가 자동 리사이징 됩니다.</span>
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
				<div class="extext" style="font-size:11px;">여러장의 이미지는 동일한 사이즈로 등록해주세요. 다른 사이즈의 이미지 등록 시  배너가 틀어져 보일 수 있습니다.</div>
				<img class="banner-preview" src="<?php echo $mobileAnimationBannerDataDir.'/'.$mobileAnimationBannerConfig['image'][0]; ?>" style="width: 80px; float: left; margin-right: 5px;" onerror="this.style.display='none';"/>
				<input type="hidden" name="imageURL[]" value="<?php echo $mobileAnimationBannerConfig['image'][0]; ?>"/>
				<div style="float: left;">
					<input type="file" name="image[]" style="display: block; margin-bottom: 5px;" accept="image/*"/>
					링크 URL : <input type="text" name="link[]" class="lline" value="<?php echo $mobileAnimationBannerConfig['link'][0]; ?>"/>
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
		<?php for ($index = 1; $index < count($mobileAnimationBannerConfig['image']); $index++) { ?>
		<tr class="image-form" data-index="<?php echo $index; ?>">
			<th>이미지<?php echo $index + 1; ?></th>
			<td>
				<img class="banner-preview" src="<?php echo $mobileAnimationBannerDataDir.'/'.$mobileAnimationBannerConfig['image'][$index]; ?>" style="width: 80px; float: left; margin-right: 5px;" onerror="this.style.display='none';"/>
				<input type="hidden" name="imageURL[]" value="<?php echo $mobileAnimationBannerConfig['image'][$index]; ?>"/>
				<div style="float: left;">
					<input type="file" name="image[]" style="display: block; margin-bottom: 5px;" accept="image/*"/>
					링크 URL : <input type="text" name="link[]" class="lline" value="<?php echo $mobileAnimationBannerConfig['link'][$index]; ?>"/>
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
				<img class="on-anchor-preview" src="<?php echo $mobileAnimationBannerNaviDir.'/'.$mobileAnimationBannerConfig['offAnchor'][$index]; ?>" width="75" onerror="this.style.display='none';"/>
				<span class="extext" style="font-size: 11px;">권장: 75x30</span>
			</td>
			<th></th>
			<td>
				<input type="file" name="offAnchor[]" style="width: 170px;" accept="image/*" disabled="disabled"/>
				<input type="hidden" name="offAnchorURL[]" value="" disabled="disabled"/>
				<img class="off-anchor-preview" src="<?php echo $mobileAnimationBannerNaviDir.'/'.$mobileAnimationBannerConfig['offAnchor'][$index]; ?>" width="75" onerror="this.style.display='none';"/>
				<span class="extext" style="font-size: 11px;">권장: 75x30</span>
			</td>
		</tr>
		<tr class="anchor-form" style="display: none;">
			<th>활성 버튼1</th>
			<td>
				<input type="file" name="onAnchor[]" style="width: 170px;" accept="image/*"/>
				<input type="hidden" name="onAnchorURL[]" value="<?php echo $mobileAnimationBannerConfig['onAnchor'][0]; ?>"/>
				<img class="on-anchor-preview" src="<?php echo $mobileAnimationBannerNaviDir.'/'.$mobileAnimationBannerConfig['onAnchor'][0]; ?>" width="75" onerror="this.style.display='none';"/>
				<span class="extext" style="font-size: 11px;">권장: 75x30</span>
			</td>
			<th>비활성 버튼1</th>
			<td>
				<input type="file" name="offAnchor[]" style="width: 170px;" accept="image/*"/>
				<input type="hidden" name="offAnchorURL[]" value="<?php echo $mobileAnimationBannerConfig['offAnchor'][0]; ?>"/>
				<img class="off-anchor-preview" src="<?php echo $mobileAnimationBannerNaviDir.'/'.$mobileAnimationBannerConfig['offAnchor'][0]; ?>" width="75" onerror="this.style.display='none';"/>
				<span class="extext" style="font-size: 11px;">권장: 75x30</span>
			</td>
		</tr>
		<?php for ($index = 1; $index < count($mobileAnimationBannerConfig['image']); $index++) { ?>
		<tr class="anchor-form" style="display: none;">
			<th>활성 버튼<?php echo $index + 1; ?></th>
			<td>
				<input type="file" name="onAnchor[]" style="width: 170px;" accept="image/*"/>
				<input type="hidden" name="onAnchorURL[]" value="<?php echo $mobileAnimationBannerConfig['onAnchor'][$index]; ?>"/>
				<img class="on-anchor-preview" src="<?php echo $mobileAnimationBannerNaviDir.'/'.$mobileAnimationBannerConfig['onAnchor'][$index]; ?>" width="75" onerror="this.style.display='none';"/>
				<span class="extext" style="font-size: 11px;">권장: 75x30</span>
			</td>
			<th>비활성 버튼<?php echo $index + 1; ?></th>
			<td>
				<input type="file" name="offAnchor[]" style="width: 170px;" accept="image/*"/>
				<input type="hidden" name="offAnchorURL[]" value="<?php echo $mobileAnimationBannerConfig['offAnchor'][$index]; ?>"/>
				<img class="off-anchor-preview" src="<?php echo $mobileAnimationBannerNaviDir.'/'.$mobileAnimationBannerConfig['offAnchor'][$index]; ?>" width="75" onerror="this.style.display='none';"/>
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
	디자인코디에서 사용가능한 치환코드 : &nbsp;  {=mobileAnimationBanner()}
</div>

<script>
table_design_load();
setHeight_ifrmCodi();
</script>