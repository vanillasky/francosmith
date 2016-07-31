<?
include "../lib.php";

$sno = (int) $_GET['sno'];

$query ="select * from gd_offline_paper where coupon_sno='$sno' order by sno";
$res = $db->query($query);

header( 'Content-type: application/vnd.ms-excel' );
header( 'Content-Disposition: attachment; filename=['. strftime( '%y년%m월%d일' ) .'] 쿠폰번호.xls' );
header( 'Content-Description: PHP4 Generated Data' );
?>
<html>
<head>
<title>list</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<style>.xl31{mso-number-format:"0_\)\;\\\(0\\\)";}</style>
</head>
<body>
<table border="1">
<tr><td>쿠폰번호</td></tr>
<?while($data = $db->fetch($res)):?>
<tr><td><?=$data['number']?></td></tr>
<?endwhile;?>
</table>
</body>
</html>
