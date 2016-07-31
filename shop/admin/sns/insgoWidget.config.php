<?php
$location = 'SNS 서비스 > 인스고위젯 설정';
$SET_HTML_DEFINE = true;
include '../_header.php';

$sampleInsgoWidget_user = 'godomall';
$sampleInsgoWidget_tag = '고도몰';
?>
<style type="text/css">
.insgoWidget_layoutArea { width: 800px; }
.insgoWidget_layoutArea .inputText { width: 150px; height: 25px; }
.insgoWidget_layoutArea .inputText2 { width: 50px; height: 25px; }
.insgoWidget_layoutArea .sampleColor { color: #ACACAC; }
.insgoWidget_layoutArea .buttonArea { width: 100%; text-align: center; margin: 30px 0px 50px 0px; }
.insgoWidget_infoArea {
	font-family: dotum;
	font-size: 13px;
	width: 800px;
	height: 157px;
	background-image: url('../img/bg_dormant.jpg');
	background-repeat:no-repeat;
	background-size: auto;
	padding-bottom: 40px;
}
.insgoWidget_infoArea .insgoWidget_infoArea_subject {
	font-weight: bold;
	color: red;
	font-size: 16px;
	padding: 60px 0px 0px 100px;
	float: left;
}
.insgoWidget_infoArea .insgoWidget_infoArea_content {
	padding: 30px 50px 0px 0px;
	float: right;
}
</style>

<div class="insgoWidget_layoutArea">
	<form name="insgoWidgetForm" id="insgoWidgetForm" method="post">
	<div class="title title_top">인스고위젯 안내</div>

	<div class="insgoWidget_infoArea">
		<div class="insgoWidget_infoArea_subject">
			<div>인스고위젯을</div>
			<div>사용하려면?</div>
		</div>
		<div class="insgoWidget_infoArea_content">
			<div style="font-weight: bold;">1. 쇼핑몰의 <span style="color: blue;">인스타그램 계정을 생성</span>하고 컨텐츠를 등록해주세요.</div>
			<div style="margin-left: 20px;">컨텐츠를 등록할 때 사람들이 많이 사용하는 해시태그를</div>
			<div style="margin-left: 20px;">함께 등록해 주시면 효과적입니다.</div>
			<div style="font-weight: bold;">2. 인스고위젯 설정에서 쇼핑몰에 삽입될 <span style="color: blue;">위젯을 생성</span>해주세요.</div>
			<div style="font-weight: bold;">3. 미리보기를 통해 위젯을 확인하고 <span style="color: blue;">소스를 복사</span>해주세요.</div>
			<div style="font-weight: bold;">4. 디자인관리에서 위젯이 노출될 페이지에 <span style="color: blue;">복사된 소스를 삽입</span>해주세요.</div>
		</div>
	</div>

	<div class="title title_top">인스고위젯 설정 <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=24')"><img src="../img/btn_q.gif"   align=absmiddle hspace=2/></a> <span>쇼핑몰에 삽입될 인스고위젯 계정 or 해시태그 및 위젯 설정입니다</span></div>

	<table class="tb" border="0" width="100%">
	<colgroup>
		<col class="cellC" />
		<col class="cellL" />
	</colgroup>
	<tr>
		<td>계정 or 해시태그</td>
		<td>
			<div style="margin-bottom: 5px;">
				<input type="radio" name="insgoWidget_type" value="user" checked="checked" style="border: 0px;"/> 계정 @ <input type="text" class="inputText sampleColor" name="insgoWidget_user" id="insgoWidget_user" value="<?php echo $sampleInsgoWidget_user; ?>" />
				<span class="extext">입력한 계정에 업로드된 컨텐츠를 불러옵니다. 예) @Godomall</span>
			</div>
			<div>
				<input type="radio" name="insgoWidget_type" value="tag" style="border: 0px;" /> 해시태그 # <input type="text" class="inputText sampleColor" name="insgoWidget_tag" id="insgoWidget_tag" value="<?php echo $sampleInsgoWidget_tag; ?>" />
				<span class="extext">입력한 해시태그가 포함된 컨텐츠를 불러옵니다. 예) #고도몰</span>
			</div>
		</td>
	</tr>
	<tr>
		<td>위젯타입</td>
		<td>
			<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td class="center" width="100"><img src="../img/insgoWidgetType_grid.jpg" border="0"/></td>
			</tr>
			<tr>
				<td class="center"><input type="radio" name="insgoWidget_displayType" value="grid" checked="checked" style="border: 0px;" /></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>레이아웃</td>
		<td>
			<input type="text" class="inputText2" name="insgoWidget_WidthCount" id="insgoWidget_WidthCount" maxlength="2" value="6" /> X <input type="text" class="inputText2" name="insgoWidget_HeightCount" id="insgoWidget_HeightCount" maxlength="2" value="4" />
		</td>
	</tr>
	<tr>
		<td>썸네일 사이즈</td>
		<td>
			<div style="margin-bottom: 5px;">
				<input type="radio" name="insgoWidget_thumbnailSize" value="auto" checked="checked" style="border: 0px;" /> 페이지에 자동맞춤
				&nbsp;
				<input type="radio" name="insgoWidget_thumbnailSize" value="hand" style="border: 0px;" /> 수동설정
				&nbsp;
				<input type="text" class="inputText2" name="insgoWidget_thumbnailSizePx" id="insgoWidget_thumbnailSizePx" maxlength="3" /> PX
			</div>
			<div><span class="extext">페이지에 자동맞춤으로 설정 시 위젯이 삽입된 페이지에 맞게 썸네일 이미지 사이즈가 자동조절 됩니다.</span></div>
		</td>
	</tr>
	<tr>
		<td>이미지 테두리</td>
		<td>
			<input type="radio" name="insgoWidget_thumbnailBorder" value="n" checked="checked" style="border: 0px;" /> 표시안함
			&nbsp;
			<input type="radio" name="insgoWidget_thumbnailBorder" value="y" style="border: 0px;" /> 표시함
		</td>
	</tr>
	<tr>
		<td>위젯 배경색</td>
		<td>
			#<input type="text" class="inputText" name="insgoWidget_backgroundColor" id="insgoWidget_backgroundColor" maxlength="6" />
			<img src="../img/codi/btn_colortable_s.gif" border="0" alt="색상표 보기" align="absmiddle" id="insgoWidget_palette" class="hand" />
		</td>
	</tr>
	<tr>
		<td>이미지 간격</td>
		<td><input type="text" class="inputText2" name="insgoWidget_imageMargin" maxlength="3" value="1" /> PX</td>
	</tr>
	<tr>
		<td>마우스오버시 효과</td>
		<td>
			<input type="radio" name="insgoWidget_overEffect" value="n" checked="checked" style="border: 0px;" /> 효과없음
			&nbsp;
			<input type="radio" name="insgoWidget_overEffect" value="blurPoint" style="border: 0px;" /> 선택한 상품만 흐리게
			&nbsp;
			<input type="radio" name="insgoWidget_overEffect" value="blurException" style="border: 0px;" /> 선택한 나머지 상품 흐리게
		</td>
	</tr>
	</table>

	<div class="buttonArea"><img src="../img/btn_preview_createSource.jpg" class="hand" id="insgoWidgetMakeWidget" border="0" /></div>

	</form>
</div>
<?php include '../_footer.php'; ?>
<script type="text/javascript">
var palettePopup;
var sampleInsgoWidget_user = '<?php echo $sampleInsgoWidget_user; ?>';
var sampleInsgoWidget_tag = '<?php echo $sampleInsgoWidget_tag; ?>';

function insgoWidgetValueCheck()
{
	var $ = jQuery;
	var patt = /\s/g;
	var insgoWidget_type = $('input:radio[name="insgoWidget_type"]:checked').val();
	var insgoWidget_thumbnailSize = $('input:radio[name="insgoWidget_thumbnailSize"]:checked').val();

	//계정 or 해시태그 체크
	if(insgoWidget_type == 'user'){
		var typeObj = $('#insgoWidget_user');
	}
	else if(insgoWidget_type == 'tag'){
		var typeObj = $('#insgoWidget_tag');
	}
	else {
		alert("계정 또는 해시태그를 입력해 주세요.");
		return;
	}

	if(typeObj.val() == ''){
		alert("계정 또는 해시태그를 입력해 주세요.");
		typeObj.trigger('click');
		typeObj.focus();
		return;
	}
	if(patt.test(typeObj.val()) === true){
		alert("띄어쓰기는 허용되지 않습니다.");
		typeObj.focus();
		return;
	}

	//레이아웃 체크
	if($('#insgoWidget_WidthCount').val() == '' || $.isNumeric($('#insgoWidget_WidthCount').val()) == false){
		alert("레이아웃 가로 개수를 숫자로 입력해 주세요.");
		$('#insgoWidget_WidthCount').focus();
		return false;
	}
	if($('#insgoWidget_HeightCount').val() == '' || $.isNumeric($('#insgoWidget_HeightCount').val()) == false){
		alert("레이아웃 세로 개수를 숫자로 입력해 주세요.");
		$('#insgoWidget_HeightCount').focus();
		return false;
	}
	//썸네일 사이즈 체크
	if(insgoWidget_thumbnailSize == 'hand' && $('#insgoWidget_thumbnailSizePx').val() == '' &&  $.isNumeric($('#insgoWidget_thumbnailSizePx').val()) == false){
		alert("썸네일 사이즈를 숫자로 입력해 주세요.");
		$('#insgoWidget_thumbnailSizePx').focus();
		return false;
	}
	return true;
}

jQuery(document).ready(function($){
	$('#insgoWidgetMakeWidget').click(function(){
		if(insgoWidgetValueCheck()){
			window.open('./popup.insgoWidgetPreview.php','insgoWidget', 'width=1050px, height=800px, scrollbars=yes');
		}
	});
	$('#insgoWidget_user, #insgoWidget_tag').click(function(){
		var thisValue = $(this).val();

		$(this).removeClass('sampleColor');
		if(thisValue == sampleInsgoWidget_user || thisValue == sampleInsgoWidget_tag){
			$(this).val('');
		}
	});
	$('#insgoWidget_palette').click(function(){
		palettePopup = popup_return('../proc/help_colortable.php?btnCallback=insertBackgroundColor', 'colorTable', 400, 400, 600, 200, 0 );
		if(palettePopup){
			palettePopup.focus();
		}
	});
});
function insertBackgroundColor(colorValue)
{
	document.getElementById("insgoWidget_backgroundColor").value = colorValue;
	palettePopup.close();
}
</script>