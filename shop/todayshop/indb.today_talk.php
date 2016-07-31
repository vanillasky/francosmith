<?
require_once('../lib/todayshop_cache.class.php');

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

if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
	$_POST = validation::xssCleanArray($_POST, array(
		validation::DEFAULT_KEY	=> 'text',
	));
}

$mode = $_POST['mode'];
$tgsno = $_POST['tgsno'];
$ttsno = $_POST['ttsno'];
$comment = $_POST['comment'];

$todayShop = Core::loader('todayshop');

switch($mode) {
	case 'regist' : 
	case 'reply' : 
	case 'edit' : {
		$msg = $todayShop->writeTalk($mode, $tgsno, $ttsno, $member, $comment);
		break;
	}
	case 'remove' : {
		$res = $todayShop->removeTalk($ttsno, $member);
		break;
	}
}
if ($msg) msg($msg);
else {
?>
<script type="text/javascript">parent.location.reload();</script>
<? } ?>
