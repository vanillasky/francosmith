<?

$location = "���¸��� ���̷�Ʈ ���� > ���� ��û";
include "../_header.php";
$godosno		= sprintf("GODO%05d",$godo[sno]);	# �������̵�

?>

<div class="title title_top">���� ��û <span>���¸��� ���̷�Ʈ ���� ���񽺸� ��û �� �����մϴ�.</span></div>


<iframe name="innaver" src="http://godosiom.godo.co.kr/gate.php?godosno=<?=$godosno?>&mode=request" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="1000"></iframe>


<? include "../_footer.php"; ?>