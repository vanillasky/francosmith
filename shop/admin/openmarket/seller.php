<?

$location = "오픈마켓 다이렉트 서비스 > 오픈마켓 판매관리";
include "../_header.php";
$godosno		= sprintf("GODO%05d",$godo[sno]);	# 상점아이디

?>


<iframe name="innaver" src="http://godosiom.godo.co.kr/gate.php?godosno=<?=$godosno?>" frameborder="0" marginwidth="0" marginheight="0" width="100%" height="1000"></iframe>


<? include "../_footer.php"; ?>