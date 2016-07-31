<?

$location = "구매안전 서비스 > 구매안전 서비스 안내";
include "../_header.php";

?>
<script language="javascript">
var IntervarId;
function resizeFrame()
{
	var i_height = eval( window.status );
	var oFrame = document.getElementsByName("innaver")[0];
	i_height -= (oFrame.offsetHeight-oFrame.clientHeight);
	oFrame.style.height = i_height;
	window.status = "완료";

	if ( IntervarId ) clearInterval( IntervarId );
}
</script>


<div class="title title_top">구매안전 서비스 안내 <span>전자상거래등에서의 소비자 보호에 관한 법률에 따른 소비자피해보상보험 또는 에스크로를 편리하게 지원합니다.</span></div>


<iframe name="innaver" src="http://www.godo.co.kr/service/sub_06_consumer_care_service.php?iframe=yes&ifrParentDomain=<?=$_SERVER['SERVER_NAME']?>" onLoad="IntervarId = setInterval( 'resizeFrame()', 100 );" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="1000"></iframe>


<? include "../_footer.php"; ?>