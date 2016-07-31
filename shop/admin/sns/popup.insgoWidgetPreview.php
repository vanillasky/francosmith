<?php
$SET_HTML_DEFINE = true;
include '../_header.popup.php';
?>
<style type="text/css">
.insgoWidget_previewLayout { width: 1000px; min-height: 400px; }
.insgoWidget_previewSourceArea { width: 1000px; min-height: 200px; border: 1px #ACACAC solid; word-break: break-all; padding: 10px; font-size: 13px; background-color: #F6F6F6; }
</style>

<div class="title title_top">�ν������� �̸����� <span>������ �ν��������� �Ʒ��� ���� �������� ���Ե˴ϴ�.</span></div>
<div class="insgoWidget_previewLayout" id="insgoWidget_previewLayout"></div>


<div class="title title_top">�ν������� �ҽ��ڵ� <span>�ϴ��� �ҽ��� �����Ͽ� ���θ��� �������ּ���.</span></div>
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
			alert("["+responseData[1]+"]" + "\n��� �� �ٽ� �õ��Ͽ� �ּ���.");
			window.close();
		}
		else {
			$('#insgoWidget_previewLayout').html(responseData[1]);
			$('#insgoWidget_previewSourceArea').text("<!-- InsGoWidget --> " + responseData[1]);
		}
	}).fail(function() {
		alert("��ſ����� �߻��Ͽ����ϴ�.\n�ٽ��ѹ� �õ��� �ּ���.");
		window.close();
		return;
	});

	$('#sourceCopy').click(function(){
		var clipData = $('#insgoWidget_previewSourceArea').text();
		if ( window.clipboardData ) {
			alert("����Ǿ����ϴ�.\n���θ��� ������ ������ ��ġ�� �ҽ��� �ٿ��־� �ּ���.");
			window.clipboardData.setData("Text", clipData);
		} else {
			prompt("�ڵ带 Ŭ������� ����(Ctrl+C) �Ͻð�.\n�θ��� ������ ������ ��ġ�� �ҽ��� �ٿ��־� �ּ���.", clipData);
		}
		return;
	});
	$('#popupClose').click(function(){
		window.close();
	});
});
</script>