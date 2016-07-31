<?php
$SET_HTML_DEFINE = true;
include '../_header.popup.php';
?>
<style type="text/css">
.insgoWidget_previewLayout { width: 1000px; min-height: 400px; }
.insgoWidget_previewSourceArea { width: 1000px; min-height: 200px; border: 1px #ACACAC solid; word-break: break-all; padding: 10px; font-size: 13px; background-color: #F6F6F6; }
</style>

<div class="title title_top">인스고위젯 미리보기 <span>설정된 인스고위젯은 아래와 같이 페이지에 삽입됩니다.</span></div>
<div class="insgoWidget_previewLayout" id="insgoWidget_previewLayout"></div>


<div class="title title_top">인스고위젯 소스코드 <span>하단의 소스를 복사하여 쇼핑몰에 삽입해주세요.</span></div>
<div class="insgoWidget_previewSourceArea" id="insgoWidget_previewSourceArea"></div>

<div class="center pdv10">
	<img src="../img/btn_copySource.jpg" class="hand" id="sourceCopy" border="0" />
	<img src="../img/btn_closePopup.jpg" class="hand" id="popupClose" border="0" />
</div>

<?php include '../_footer.popup.php'; ?>
<script type="text/javascript">
jQuery(document).ready(function($){
	$.post('./_ajax.getInsgoWidget.php', $('#insgoWidgetForm', opener.document).serialize(), function(data){
		var responseData = new Array();
		responseData = eval('('+data+')');

		if(responseData[0] == 'ERROR'){
			alert("["+responseData[1]+"]" + "\n잠시 후 다시 시도하여 주세요.");
			window.close();
		}
		else {
			$('#insgoWidget_previewLayout').html(responseData[1]);
			$('#insgoWidget_previewSourceArea').text("<!-- InsGoWidget --> " + responseData[1]);
		}
	}).fail(function() {
		alert("통신에러가 발생하였습니다.\n다시한번 시도해 주세요.");
		window.close();
		return;
	});

	$('#sourceCopy').click(function(){
		var clipData = $('#insgoWidget_previewSourceArea').text();
		if ( window.clipboardData ) {
			alert("복사되었습니다.\n쇼핑몰내 위젯을 삽입할 위치에 소스를 붙여넣어 주세요.");
			window.clipboardData.setData("Text", clipData);
		} else {
			prompt("코드를 클립보드로 복사(Ctrl+C) 하시고.\n핑몰내 위젯을 삽입할 위치에 소스를 붙여넣어 주세요.", clipData);
		}
		return;
	});
	$('#popupClose').click(function(){
		window.close();
	});
});
</script>