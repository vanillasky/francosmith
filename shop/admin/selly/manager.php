<?
	$location = "���� > ���¸��� �ǸŰ���";
	include "../_header.php";
	list($cust_seq) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_seq'");
	list($cust_cd) = $db->fetch("SELECT value FROM gd_env WHERE category = 'selly' AND name = 'cust_cd'");

	if($cust_seq && $cust_seq) {
?>
<script language="JavaScript">
	win = window.open("http://stdev24.godo.co.kr/enamooAPI/trans_shop.gm?cust_seq=<?=$cust_seq?>&cust_cd=<?=$cust_cd?>");
	if(win) history.back();
	else document.write("<div style=\"padding:30px; text-align:center; vertical-align:middle;  border:3px #DCE1E1 solid;\">�˾��� ������ֽñ� �ٶ��ϴ�.</div>");
</script>
<?
	}
	else {
		msg("������ ��û�ϰ� ���� ���� ��� �Ŀ� ��밡���� �����Դϴ�.");
		go("./setting.php");
	}

	include "../_footer.php";
?>

