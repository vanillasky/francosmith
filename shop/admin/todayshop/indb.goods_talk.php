<?php
@require "../lib.php";
require_once("../../lib/todayshop_cache.class.php");

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
}

$mode = $_POST['mode'];
$tgsno = $_POST['tgsno'];
$ttsno = $_POST['ttsno'];
$comment = $_POST['comment'];
$notice = $_POST['notice'];
$allgoods = $_POST['allgoods'];
$member['m_no'] = $_SESSION['sess']['m_no'];
$member['level'] = $_SESSION['sess']['level'];
$member['name'] = $_POST['writer'];

$todayShop = &load_class('todayshop', 'todayshop');

switch($mode) {
	case 'regist' : 
	case 'reply' : 
	case 'edit' : {
		$msg = $todayShop->writeTalk($mode, $tgsno, $ttsno, $member, $comment, $notice, $allgoods);
		break;
	}
	case 'remove' : {
		$res = $todayShop->removeTalk($ttsno, $member);
		break;
	}
}

todayshop_cache::remove($tgsno,'todaytalk');

if ($msg) msg($msg);
else {
?>
<script type="text/javascript">parent.location.reload();</script>
<? } ?>