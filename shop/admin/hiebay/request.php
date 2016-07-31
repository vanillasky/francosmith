<?
$location = "하이! eBay > 서비스 안내";
include "../_header.php";
$ignoreToken = true;
include_once "./checker.php";
?>
<script language="JavaScript">
	win = window.open("http://www.godo.co.kr/userinterface/_forseller/service.php?sno=<?=$godo['sno']?>",'popup_forsellerRegister','width=985, height=700, toolbar=0, directories=0, status=1, menubar=0, scrollbars=1, resizable=0');
	if(win) history.back();
	else document.write("<div style=\"padding:30px; text-align:center; vertical-align:middle;  border:3px #DCE1E1 solid;\">팝업을 허용해주시기 바랍니다.</div>");
</script>
<?include "../_footer.php"; ?>
