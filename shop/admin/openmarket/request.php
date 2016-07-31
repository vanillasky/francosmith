<?

$location = "오픈마켓 다이렉트 서비스 > 서비스 신청";
include "../_header.php";
$godosno		= sprintf("GODO%05d",$godo[sno]);	# 상점아이디

?>

<div class="title title_top">서비스 신청 <span>오픈마켓 다이렉트 서비스 서비스를 신청 및 관리합니다.</span></div>


<iframe name="innaver" src="http://godosiom.godo.co.kr/gate.php?godosno=<?=$godosno?>&mode=request" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="1000"></iframe>


<? include "../_footer.php"; ?>