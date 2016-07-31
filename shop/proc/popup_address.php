<?php
@include "../lib/library.php";
$godojuso = Core::loader('godojuso');
$gubun = $_GET['gubun'];
if (isset($_GET['isMobile'])) $godojuso->setMobile();

echo $godojuso->getCurlData($gubun);

/* 예) 별도 Bridge 파일 구성하는 경우
 * $returnurl = ProtocolPortDomain().$cfg['rootDir']."/proc/popup_address_bridge.php";
 * $godojuso->getCurlData($gubun, $returnurl);
 */
?>

<!-- 모바일에서 레이어로 파일 오픈시 실행 -->
<?if($_GET['isMobile']=="true"){?>
<script type="text/javascript">
$(function(){
	// 주소검색후 검색된 주소를 선택했을 때 페이지 맨 위로 이동
	$(document).on("click",".select",function(){
		$(window.parent.document).scrollTop(0,0);
	});
	//id가 close인 인자를 클릭했을 때 레이어 제거
	$(document).on("click","#close",function(){
		$(window.parent.document).find("#frmMask").remove();
		$(window.parent.document).find("#searchZipcode_area").remove();
	});
})
</script>
<?}?>