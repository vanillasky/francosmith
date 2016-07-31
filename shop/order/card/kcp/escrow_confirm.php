<?
/**
 * KCP PG 에스크로 구매 확인 페이지
 */
include "../../../lib/library.php";
include "../../../conf/config.php";
include "../../../conf/pg.$cfg[settlePg].php";
include "../../../conf/pg.escrow.php";

$ordno = $_GET['ordno'];

$query = "
SELECT
	escrowno
FROM
	".GD_ORDER."
WHERE
	ordno = '$ordno'
";
$data = $db->fetch($query);

// real url : admin.kcp.co.kr , test url : testadmin8.kcp.co.kr
header("location:https://admin.kcp.co.kr/Modules/Sale/ESCROW/n_order_confirm.jsp?site_cd=".$pg['id']."&tno=".$data['escrowno']."&order_no=".$ordno);
?>