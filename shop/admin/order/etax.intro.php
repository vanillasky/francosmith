<?

$location = "���ڼ��ݰ�꼭 ���� > ���ڼ��ݰ�꼭 ���� �ȳ�";
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
	window.status = "�Ϸ�";

	if ( IntervarId ) clearInterval( IntervarId );
}
</script>


<div class="title title_top">���ڼ��ݰ�꼭 ���� �ȳ� <span>���ݰ�꼭 ���񽺿� ���� �Ұ� / Ư¡ / ��û ���� �ȳ��� �帮�� ������ �������Դϴ�</span></div>


<iframe name="innaver" src="http://www.godo.co.kr/service/sub_06_web_tax_service.php?iframe=yes&ifrParentDomain=<?=$_SERVER['SERVER_NAME']?>" onLoad="IntervarId = setInterval( 'resizeFrame()', 100 );" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="1000"></iframe>


<? include "../_footer.php"; ?>