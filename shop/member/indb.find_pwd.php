<?
include "../lib/library.php";
include "../conf/config.php";

$dormant = Core::loader('dormant');

error_reporting(0);

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}

$info_cfg = $config->load('member_info');

$query = sprintf("select mb.mobile, mb.email, mb.name, mb.m_id, otp.token, otp.otp, otp.expire, otp.auth from ".GD_OTP." as otp inner join ".GD_MEMBER." as mb on otp.m_id = mb.m_id where otp.m_id = '%s' AND otp.token > '' AND otp.token = '%s' AND dormant_regDate = '0000-00-00 00:00:00' ", $db->_escape($_POST['m_id']), $db->_escape($_POST['token']));
$_mb = $db->fetch($query, 1);

//휴면회원 조회
if(!$_mb){
	$_mb = $dormant->findPasswordUser('send', $_POST);
}

if (!$_mb['m_id']) {
	exit('0001');
}

// otp 전송
if ($_POST['mode'] == 'send') {

	$otp = Core::loader('gd_otp');

	if ($_POST['token'] != $otp->getToken()) {
		exit('0002');
	}

	if ($_mb['expire'] < date('Y-m-d H:i:s')) {

		$db->query(sprintf("delete from ".GD_OTP." where m_id = '%s'", $db->_escape($_mb['m_id'])));

		exit('0003');
	}

	$authNum = $otp->getOTP();

	// otp 전송
	$sf = Core::loader('stringFormatter');

	switch ((int) $_GET['type']) {
		case 1: // 이메일 전송

			if (($_email = $sf->get($_mb['email'], 'email')) !== false) {
				ob_start();
				$modeMail = 13;
				include "../lib/automail.class.php";
				include "../conf/config.php";
				$automail = new automail();
				$automail->_set($modeMail, $_email, $cfg);
				$automail->_assign('name', $_mb['name']);
				$automail->_assign('id', $_mb['m_id']);
				$automail->_assign('authNum', $authNum);
				$automail->_send();
				ob_end_clean();

			}
			else {
				exit('0004');
			}

			break;
		case 2: // 휴대폰 전송
			if ($_mb['mobile']) {

				// 발송건수
				$sms = Core::loader('Sms');
				$sms_sendlist = $sms->loadSendlist();
				if ((int)$sms->smsPt < 1) exit('0008');

				$GLOBALS['dataSms']['authNum'] = $authNum;

				$msg = parseCode($info_cfg['finder_mobile_auth_message']);

				$sms->log($msg,$_mb['mobile'],'',1);
				$sms_sendlist->setSimpleInsert($_mb['mobile'], $sms->smsLogInsertId, '');
				$sms->send($msg,$_mb['mobile'],$cfg['smsRecall']);
				$sms->update_ok_eNamoo = true;
				$sms->update();

			}
			else {
				exit('0005');
			}
			break;
	}

	// 업데이트
	$query = sprintf("update ".GD_OTP." set otp = '%s', auth = 0 where m_id = '%s' AND token = '%s'", $authNum, $db->_escape($_mb['m_id']), $db->_escape($_mb['token']));
	$db->query($query);

	exit('0000');
}
elseif ($_POST['mode'] == 'compare') {
	if ($_mb['otp'] == $_POST['otp']) {
		$query = sprintf("update ".GD_OTP." set auth = 1 where m_id = '%s' AND token = '%s'", $db->_escape($_mb['m_id']), $db->_escape($_mb['token']));
		$db->query($query);
		exit('0000');
	}
	else {
		exit('0006');
	}
	exit;
}
elseif ($_POST['mode'] == 'change') {

	//패스워드 입력형식
	if($_POST['passwordSkin'] === 'Y'){
		if(passwordPatternCheck($_POST['pwd']) === false) exit('0009');
	} else {
		// 사용 가능 여부 (6자 이상 21~7E 까지 ascii)
		if (!preg_match('/^[\x21-\x7E]{6,}$/',$_POST['pwd'])) exit('0009');
	}

	$dormantMember = false;
	$dormantMember = $dormant->checkDormantMember($_POST, 'm_id');
	if($dormantMember === true){
		$dormantChangePasswordResult = false;
		$dormantChangePasswordResult = $dormant->findPasswordUser('change', $_POST);
		//휴면회원 비밀번호 변경 실패
		if($dormantChangePasswordResult == false){
			exit('0007');
		}
	}

	$query = sprintf("
	update ".GD_MEMBER." as mb
	inner join ".GD_OTP." as otp
	on mb.m_id = otp.m_id AND otp.expire > '%s'
	set mb.password = password('%s'), mb.password_moddt = NOW()
	where otp.m_id = '%s' and otp.token = '%s'
	", date('Y-m-d H:i:s'), $db->_escape($_POST['pwd']), $db->_escape($_POST['m_id']), $db->_escape($_POST['token']));

	if ($db->query($query)) {
		session_regenerate_id();
		$db->query(sprintf("delete from ".GD_OTP." where m_id = '%s'", $db->_escape($_POST['m_id'])));

		// 비밀번호 변경 안내 메일 전송
		$sf = Core::loader('stringFormatter');

		if (($_email = $sf->get($_mb['email'], 'email')) !== false && $cfg['mailyn_14'] == 'y') {

			ob_start();
			$modeMail = 14;
			include "../lib/automail.class.php";
			include "../conf/config.php";
			$automail = new automail();
			$automail->_set($modeMail, $_email, $cfg);
			$automail->_assign('name', $_mb['name']);
			$automail->_assign('moddt', date('Y-m-d H:i:s'));
			$automail->_send();
			ob_end_clean();

		}

		exit('0000');
	}
	else {
		exit('0007');
	}
}
?>
9999
