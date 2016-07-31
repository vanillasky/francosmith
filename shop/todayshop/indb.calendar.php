<?
include "../_header.php";

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
}

if ($sess){
	$query = "
	SELECT * FROM
		".GD_MEMBER." a
		LEFT JOIN ".GD_MEMBER_GRP." b ON a.level=b.level
	WHERE
		m_no='$sess[m_no]'
	";
	$member = $db->fetch($query,1);
}
else exit;

$tgsno = $_POST['tgsno'];
$todayShop = Core::loader('todayshop');
$msg = $todayShop->encor($tgsno, $member['m_no']);
if ($msg) msg($msg);
else msg('앵콜추천되었습니다.');
?>
