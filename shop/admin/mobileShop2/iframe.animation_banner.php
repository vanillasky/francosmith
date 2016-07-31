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
				jQuery(element).text("�̹���" + (index + 1));
			});
			jQuery(".anchor-form").each(function(index, element){
				jQuery("th", element).eq(0).text("Ȱ�� ��ư" + (index + 1));
				jQuery("th", element).eq(1).text("��Ȱ�� ��ư" + (index + 1));
			});
			$mobileAnimationBanner.data("wrapperClass").removeBanner(trIndex);
			jQuery(".animation-banner").trigger("init");
		};
		var getPreviewImage = function(fileInput)
		{
			// �������� üũ
			if (fileInput.files) {
				var files = fileInput.files;
				for (var index = 0; index < files.length; index++) {
					var file = files[index];
					if (file.name.split(".").pop().match(/jpg|jpeg|gif|png|bmp/i) === null) {
						alert("�̹����� �ƴ� ������ ���õǾ����ϴ�.");
						jQuery(fileInput).replaceWith(jQuery(fileInput).clone(true));
						return false;
					}
				}
			}
			else {
				if (fileInput.value.split(".").pop().match(/jpg|jpeg|gif|png|bmp/i) === null) {
					alert("�̹����� �ƴ� ������ ���õǾ����ϴ�.");
					jQuery(fileInput).replaceWith(jQuery(fileInput).clone(true));
					return false;
				}
			}

			// �̸�����
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
				jQuery(element).text("�̹���" + (index + 1));
			});
			jQuery(".anchor-form:last").after($anchorForm);
			jQuery(".anchor-form").each(function(index, element){
				jQuery("th", element).eq(0).text("Ȱ�� ��ư" + (index + 1));
				jQuery("th", element).eq(1).text("��Ȱ�� ��ư" + (index + 1));
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
	<h2 class="title">��� ����</h2>

	<table class="admin-form-table">
		<tr>
			<th>��뼳��</th>
			<td>
				<input type="radio" id="enable-true" name="enable" value="true" <?php echo $checked['enable']['true']; ?>/>
				<label for="enable-true">�����</label>
				<input type="radio" id="enable-false" name="enable" value="false" <?php echo $checked['enable']['false']; ?>/>
				<label for="enable-false">������</label>
			</td>
		</tr>
		<tr>
			<th>���Ų</th>
			<td><?php echo $cfg['tplSkinMobileWork']; ?></td>
		</tr>
	</table>

	<?php mobileAnimationBanner(true); ?>

	<table class="admin-form-table">
		<tr>
			<th>ȿ������</th>
			<td colspan="3">
				<div style="float: left; width: 100px;">
					<label for="type-slide" style="display: block; padding-left: 10px;" onclick="this.click();">
						<img src="../img/animation_banner/icon_slide.jpg"/>
					</label>
					<input type="radio" id="type-slide" name="type" value="slide" <?php echo $checked['type']['slide']; ?>/>
					<label for="type-slide">�����̵�</label>
				</div>
				<div style="float: left; width: 100px;">
					<label for="type-fade" style="display: block; padding-left: 10px;" onclick="this.click();">
						<img src="../img/animation_banner/icon_fade.jpg"/>
					</label>
					<input type="radio" id="type-fade" name="type" value="fade" <?php echo $checked['type']['fade']; ?>/>
					<label for="type-fade">���̵�</label>
				</div>
				<div style="float: left; width: 100px;">
					<label for="type-blind" style="display: block; padding-left: 10px;" onclick="this.click();">
						<img src="../img/animation_banner/icon_blind.jpg"/>
					</label>
					<input type="radio" id="type-blind" name="type" value="blind" <?php echo $checked['type']['blind']; ?>/>
					<label for="type-blind">����ε�</label>
				</div>
				<div style="float: left; width: 100px;">
					<label for="type-plain" style="display: block; padding-left: 10px;" onclick="this.click();">
						<img src="../img/animation_banner/icon_default.jpg"/>
					</label>
					<input type="radio" id="type-plain" name="type" value="plain" <?php echo $checked['type']['plain']; ?>/>
					<label for="type-plain">ȿ������</label>
				</div>
			</td>
		</tr>
		<tr>
			<th>��ȯ�ӵ� ����</th>
			<td>
				<select name="duration">
					<option value="200" <?php echo $selected['duration'][200]; ?>>������</option>
					<option value="400" <?php echo $selected['duration'][400]; ?>>����</option>
					<option value="600" <?php echo $selected['duration'][600]; ?>>������</option>
				</select>
			</td>
			<th>�¿� ��ȯ ��ư</th>
			<td>
				<input id="direction-anchor-display-true" type="radio" name="directionAnchorDisplay" value="true" <?php echo $checked['directionAnchorDisplay']['true']; ?>/>
				<label for="direction-anchor-display-true">ǥ����</label>
				<input id="direction-anchor-display-false" type="radio" name="directionAnchorDisplay" value="false" <?php echo $checked['directionAnchorDisplay']['false']; ?>/>
				<label for="direction-anchor-display-false">ǥ�þ���</label>
			</td>
		</tr>
		<tr>
			<th>��ȯ��� ����</th>
			<td colspan="3">
				<select name="shiftType">
					<option value="auto" <?php echo $selected['shiftType']['auto']; ?>>�ڵ�</option>
					<option value="manual" <?php echo $selected['shiftType']['manual']; ?>>���콺Ŭ������</option>
				</select>
				<select name="interval">
					<option value="3000" <?php echo $selected['interval'][3000]; ?>>3��</option>
					<option value="4000" <?php echo $selected['interval'][4000]; ?>>4��</option>
					<option value="5000" <?php echo $selected['interval'][5000]; ?>>5��</option>
					<option value="7000" <?php echo $selected['interval'][7000]; ?>>7��</option>
					<option value="10000" <?php echo $selected['interval'][10000]; ?>>10��</option>
				</select>
			</td>
		</tr>
	</table>

	<h2 class="title">����̹���</h2>
	<table id="banner-image" class="admin-form-table">
		<tr>
			<th>��ʳ���</th>
			<td>
				<span class="extext" style="font-size:11px;">����̽��� ���̿� ���� ä���� ȭ�鿡 ��µǸ�, ����� �̸���������θ� ���˴ϴ�.</span>
			</td>
		</tr>
		<tr>
			<th>��ʳ���</th>
			<td>
				<input type="hidden" name="height" value="<?php echo $mobileAnimationBannerConfig['height']; ?>" style="width: 70px; text-align: center;"/>
				<span class="extext" style="font-size:11px;">����̽��� ���λ���� ���� �̹����� �ڵ� ������¡ �˴ϴ�.</span>
			</td>
		</tr>
		<tr id="image-form" style="display: none;">
			<th></th>
			<td>
				<img class="banner-preview" src="" style="width: 80px; float: left; margin-right: 5px;" onerror="this.style.display='none';"/>
				<input type="hidden" name="imageURL[]" value="" disabled="disabled"/>
				<div style="float: left;">
					<input type="file" name="image[]" style="display: block; margin-bottom: 5px;" accept="image/*" disabled="disabled"/>
					��ũ URL : <input type="text" name="link[]" class="lline" disabled="disabled"/>
					<select name="target[]" disabled="disabled">
						<option value="_self">����â</option>
						<option value="_blank">��â</option>
					</select>
				</div>
				<div style="float: right;">
					<button class="remove" type="button">����</button>
				</div>
			</td>
		</tr>
		<tr class="image-form" data-index="0">
			<th>�̹���1</th>
			<td>
				<div class="extext" style="font-size:11px;">�������� �̹����� ������ ������� ������ּ���. �ٸ� �������� �̹��� ��� ��  ��ʰ� Ʋ���� ���� �� �ֽ��ϴ�.</div>
				<img class="banner-preview" src="<?php echo $mobileAnimationBannerDataDir.'/'.$mobileAnimationBannerConfig['image'][0]; ?>" style="width: 80px; float: left; margin-right: 5px;" onerror="this.style.display='none';"/>
				<input type="hidden" name="imageURL[]" value="<?php echo $mobileAnimationBannerConfig['image'][0]; ?>"/>
				<div style="float: left;">
					<input type="file" name="image[]" style="display: block; margin-bottom: 5px;" accept="image/*"/>
					��ũ URL : <input type="text" name="link[]" class="lline" value="<?php echo $mobileAnimationBannerConfig['link'][0]; ?>"/>
					<select name="target[]">
						<option value="_self" <?php echo $selected['target'][0]['_self']; ?>>����â</option>
						<option value="_blank" <?php echo $selected['target'][0]['_blank']; ?>>��â</option>
					</select>
				</div>
				<div style="float: right;">
					<button id="append-image-form" type="button">�߰�</button>
				</div>
			</td>
		</tr>
		<?php for ($index = 1; $index < count($mobileAnimationBannerConfig['image']); $index++) { ?>
		<tr class="image-form" data-index="<?php echo $index; ?>">
			<th>�̹���<?php echo $index + 1; ?></th>
			<td>
				<img class="banner-preview" src="<?php echo $mobileAnimationBannerDataDir.'/'.$mobileAnimationBannerConfig['image'][$index]; ?>" style="width: 80px; float: left; margin-right: 5px;" onerror="this.style.display='none';"/>
				<input type="hidden" name="imageURL[]" value="<?php echo $mobileAnimationBannerConfig['image'][$index]; ?>"/>
				<div style="float: left;">
					<input type="file" name="image[]" style="display: block; margin-bottom: 5px;" accept="image/*"/>
					��ũ URL : <input type="text" name="link[]" class="lline" value="<?php echo $mobileAnimationBannerConfig['link'][$index]; ?>"/>
					<select name="target[]">
						<option value="_self" <?php echo $selected['target'][$index]['_self']; ?>>����â</option>
						<option value="_blank" <?php echo $selected['target'][$index]['_blank']; ?>>��â</option>
					</select>
				</div>
				<div style="float: right;">
					<button class="remove" type="button">����</button>
				</div>
			</td>
		</tr>
		<?php } ?>
	</table>

	<h2 class="title">������̼�</h2>
	<table id="anchor-image" class="admin-form-table">
		<tr>
			<th>����</th>
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
				<label for="anchor-display-custom">�������</label>
				<input id="anchor-display-false" type="radio" name="anchorDisplay" value="false" style="vertical-align: middle;" <?php echo $checked['anchorDisplay']['false']; ?>/>
				<label for="anchor-display-false">ǥ�þ���</label>
			</td>
		</tr>
		<tr id="anchor-form" style="display: none;">
			<th></th>
			<td>
				<input type="file" name="onAnchor[]" style="width: 170px;" accept="image/*" disabled="disabled"/>
				<input type="hidden" name="onAnchorURL[]" value="" disabled="disabled"/>
				<img class="on-anchor-preview" src="<?php echo $mobileAnimationBannerNaviDir.'/'.$mobileAnimationBannerConfig['offAnchor'][$index]; ?>" width="75" onerror="this.style.display='none';"/>
				<span class="extext" style="font-size: 11px;">����: 75x30</span>
			</td>
			<th></th>
			<td>
				<input type="file" name="offAnchor[]" style="width: 170px;" accept="image/*" disabled="disabled"/>
				<input type="hidden" name="offAnchorURL[]" value="" disabled="disabled"/>
				<img class="off-anchor-preview" src="<?php echo $mobileAnimationBannerNaviDir.'/'.$mobileAnimationBannerConfig['offAnchor'][$index]; ?>" width="75" onerror="this.style.display='none';"/>
				<span class="extext" style="font-size: 11px;">����: 75x30</span>
			</td>
		</tr>
		<tr class="anchor-form" style="display: none;">
			<th>Ȱ�� ��ư1</th>
			<td>
				<input type="file" name="onAnchor[]" style="width: 170px;" accept="image/*"/>
				<input type="hidden" name="onAnchorURL[]" value="<?php echo $mobileAnimationBannerConfig['onAnchor'][0]; ?>"/>
				<img class="on-anchor-preview" src="<?php echo $mobileAnimationBannerNaviDir.'/'.$mobileAnimationBannerConfig['onAnchor'][0]; ?>" width="75" onerror="this.style.display='none';"/>
				<span class="extext" style="font-size: 11px;">����: 75x30</span>
			</td>
			<th>��Ȱ�� ��ư1</th>
			<td>
				<input type="file" name="offAnchor[]" style="width: 170px;" accept="image/*"/>
				<input type="hidden" name="offAnchorURL[]" value="<?php echo $mobileAnimationBannerConfig['offAnchor'][0]; ?>"/>
				<img class="off-anchor-preview" src="<?php echo $mobileAnimationBannerNaviDir.'/'.$mobileAnimationBannerConfig['offAnchor'][0]; ?>" width="75" onerror="this.style.display='none';"/>
				<span class="extext" style="font-size: 11px;">����: 75x30</span>
			</td>
		</tr>
		<?php for ($index = 1; $index < count($mobileAnimationBannerConfig['image']); $index++) { ?>
		<tr class="anchor-form" style="display: none;">
			<th>Ȱ�� ��ư<?php echo $index + 1; ?></th>
			<td>
				<input type="file" name="onAnchor[]" style="width: 170px;" accept="image/*"/>
				<input type="hidden" name="onAnchorURL[]" value="<?php echo $mobileAnimationBannerConfig['onAnchor'][$index]; ?>"/>
				<img class="on-anchor-preview" src="<?php echo $mobileAnimationBannerNaviDir.'/'.$mobileAnimationBannerConfig['onAnchor'][$index]; ?>" width="75" onerror="this.style.display='none';"/>
				<span class="extext" style="font-size: 11px;">����: 75x30</span>
			</td>
			<th>��Ȱ�� ��ư<?php echo $index + 1; ?></th>
			<td>
				<input type="file" name="offAnchor[]" style="width: 170px;" accept="image/*"/>
				<input type="hidden" name="offAnchorURL[]" value="<?php echo $mobileAnimationBannerConfig['offAnchor'][$index]; ?>"/>
				<img class="off-anchor-preview" src="<?php echo $mobileAnimationBannerNaviDir.'/'.$mobileAnimationBannerConfig['offAnchor'][$index]; ?>" width="75" onerror="this.style.display='none';"/>
				<span class="extext" style="font-size: 11px;">����: 75x30</span>
			</td>
		</tr>
		<?php } ?>
	</table>

	<div class="button">
		<input id="form-submit" type="image" src="../img/btn_register.gif"/>
	</div>
</form>

<div style="border:1px solid #cccccc;background-color:#eeeeee;text-align:center;padding:10px 0px;font-size:13pt;font-weight:bold;">
	�������ڵ𿡼� ��밡���� ġȯ�ڵ� : &nbsp;  {=mobileAnimationBanner()}
</div>

<script>
table_design_load();
setHeight_ifrmCodi();
</script>