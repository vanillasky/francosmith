<?
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");

include "../lib/library.php";

$formatter = Core::loader('stringFormatter');


if (! $_POST['ts_subscribe_email_val'] && ! $_POST['ts_subscribe_sms_val']) {
	msg('이메일/핸드폰 정보 를 정확하게 입력해 주시길 바랍니다.');
	exit;
}

if (($_POST['ts_subscribe_sms']) && (($_POST['ts_subscribe_sms_val'] = $formatter->get($_POST['ts_subscribe_sms_val'], 'dial','-')) === false)) {
	msg('핸드폰 정보가 올바르지 않습니다.');
	exit;
}

if (($_POST['ts_subscribe_email']) && (($_POST['ts_subscribe_email_val'] = $formatter->get($_POST['ts_subscribe_email_val'], 'email')) === false)) {
	msg('이메일 정보가 올바르지 않습니다.');
	exit;
}

$where = array();
if ($_POST['ts_subscribe_sms_val']) $where[] = "phone = '".$_POST['ts_subscribe_sms_val']."'";
if ($_POST['ts_subscribe_email_val']) $where[] = "email = '".$_POST['ts_subscribe_email_val']."'";

list($cnt) = $db->fetch("SELECT COUNT(sno) FROM ".GD_TODAYSHOP_SUBSCRIBE." WHERE ".implode(" OR ", $where));

if ($cnt > 0) {
	msg('휴대폰 번호 또는 이메일 주소가 이미 사용중입니다.');
	exit;
}


// 회원인 경우 데이터가 있으면 업데이트 함.
if ($sess['m_id'] && (($subscribe = $db->fetch("SELECT sno FROM ".GD_TODAYSHOP_SUBSCRIBE." WHERE m_id = '".$sess['m_id']."'",1)) != false)) {
	// update..
	$query = "
	UPDATE ".GD_TODAYSHOP_SUBSCRIBE." SET
		email = '".($_POST['ts_subscribe_email'] && $_POST['ts_subscribe_email_val'] ? $_POST['ts_subscribe_email_val'] : '')."',
		phone = '".($_POST['ts_subscribe_sms'] && $_POST['ts_subscribe_sms_val'] ? $_POST['ts_subscribe_sms_val'] : '')."'
	";

	if (isset($_POST['interest_category'])) $query .= " , category = '".$_POST['interest_category']."'";

	$query .= "
	WHERE m_id = '".$sess['m_id']."'
	";
}
else {
	// insert..
	$query = "
	INSERT INTO ".GD_TODAYSHOP_SUBSCRIBE." SET
		m_id = '".$sess['m_id']."',
		email = '".($_POST['ts_subscribe_email'] && $_POST['ts_subscribe_email_val'] ? $_POST['ts_subscribe_email_val'] : '')."',
		phone = '".($_POST['ts_subscribe_sms'] && $_POST['ts_subscribe_sms_val'] ? $_POST['ts_subscribe_sms_val'] : '')."'
	";

	if (isset($_POST['interest_category'])) $query .= " , category = '".$_POST['interest_category']."'";

	$query .= "
		,regdt = NOW()
	";
}

$db->query($query);


msg('정기구독 신청이 완료되었습니다.');
?>
