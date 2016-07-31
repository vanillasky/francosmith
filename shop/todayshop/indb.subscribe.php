<?
header ("Cache-Control: no-cache, must-revalidate");
header ("Pragma: no-cache");

include "../lib/library.php";

$formatter = Core::loader('stringFormatter');


if (! $_POST['ts_subscribe_email_val'] && ! $_POST['ts_subscribe_sms_val']) {
	msg('�̸���/�ڵ��� ���� �� ��Ȯ�ϰ� �Է��� �ֽñ� �ٶ��ϴ�.');
	exit;
}

if (($_POST['ts_subscribe_sms']) && (($_POST['ts_subscribe_sms_val'] = $formatter->get($_POST['ts_subscribe_sms_val'], 'dial','-')) === false)) {
	msg('�ڵ��� ������ �ùٸ��� �ʽ��ϴ�.');
	exit;
}

if (($_POST['ts_subscribe_email']) && (($_POST['ts_subscribe_email_val'] = $formatter->get($_POST['ts_subscribe_email_val'], 'email')) === false)) {
	msg('�̸��� ������ �ùٸ��� �ʽ��ϴ�.');
	exit;
}

$where = array();
if ($_POST['ts_subscribe_sms_val']) $where[] = "phone = '".$_POST['ts_subscribe_sms_val']."'";
if ($_POST['ts_subscribe_email_val']) $where[] = "email = '".$_POST['ts_subscribe_email_val']."'";

list($cnt) = $db->fetch("SELECT COUNT(sno) FROM ".GD_TODAYSHOP_SUBSCRIBE." WHERE ".implode(" OR ", $where));

if ($cnt > 0) {
	msg('�޴��� ��ȣ �Ǵ� �̸��� �ּҰ� �̹� ������Դϴ�.');
	exit;
}


// ȸ���� ��� �����Ͱ� ������ ������Ʈ ��.
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


msg('���ⱸ�� ��û�� �Ϸ�Ǿ����ϴ�.');
?>
