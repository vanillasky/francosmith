<?
	$location = "셀리 > 오픈마켓 판매관리";
	include "../_header.php";
	list($cust_seq) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_seq'");
	list($cust_cd) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_cd'");

	if($cust_seq && $cust_seq) {
?>
<script language="JavaScript">
	win = window.open("http://stdev24.godo.co.kr/enamooAPI/trans_shop.gm?cust_seq=<?=$cust_seq?>&cust_cd=<?=$cust_cd?>");
	if(win) history.back();
	else document.write("<div style=\"padding:30px; text-align:center; vertical-align:middle;  border:3px #DCE1E1 solid;\">팝업을 허용해주시기 바랍니다.</div>");
</script>
<?
	}
	else {
		msg("셀리를 신청하고 상점 인증 등록 후에 사용가능한 서비스입니다.");
		go("./setting.php");
	}

	include "../_footer.php";
?>

