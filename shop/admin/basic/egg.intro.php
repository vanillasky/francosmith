<?

$location = "���ž��� ���� > ���ž��� ���� �ȳ�";
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
	window.status = "�Ϸ�";

	if ( IntervarId ) clearInterval( IntervarId );
}
</script>


<div class="title title_top">���ž��� ���� �ȳ� <span>���ڻ�ŷ������ �Һ��� ��ȣ�� ���� ������ ���� �Һ������غ����� �Ǵ� ����ũ�θ� ���ϰ� �����մϴ�.</span></div>


<iframe name="innaver" src="http://www.godo.co.kr/service/sub_06_consumer_care_service.php?iframe=yes&ifrParentDomain=<?=$_SERVER['SERVER_NAME']?>" onLoad="IntervarId = setInterval( 'resizeFrame()', 100 );" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="1000"></iframe>


<? include "../_footer.php"; ?>