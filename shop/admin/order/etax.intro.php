<?

$location = "전자세금계산서 관리 > 전자세금계산서 서비스 안내";
include "../_header.php";

?>
<script language="javascript">
var IntervarId;
function resizeFrame()
{
	var i_height = eval( window.status );
	var oFrame = document.getElementById("innaver");
	i_height -= (oFrame.offsetHeight-oFrame.clientHeight);
	oFrame.style.height = i_height;
	window.status = "완료";

	if ( IntervarId ) clearInterval( IntervarId );
}
</script>


<div class="title title_top">전자세금계산서 서비스 안내 <span>세금계산서 서비스에 대한 소개 / 특징 / 신청 등을 안내해 드리는 컨텐츠 페이지입니다</span></div>


<iframe name="innaver" src="http://www.godo.co.kr/service/sub_06_web_tax_service.php?iframe=yes&ifrParentDomain=<?=$_SERVER['SERVER_NAME']?>" onLoad="IntervarId = setInterval( 'resizeFrame()', 100 );" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="1000"></iframe>


<? include "../_footer.php"; ?>