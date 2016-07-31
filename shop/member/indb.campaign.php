<?
include "../lib/library.php";
include "../conf/config.php";

if( !$sess ){
	exit('0001');
}

error_reporting(0);

if (get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}


if ($_POST['mode'] == 'regard') {
	$info_cfg = $config->load('member_info');
	setcookie('campaign_disregarded_date', date('Y-m-d H:i:s'), time() + ($info_cfg['campaign_next_term'] * 86400), '/');
}
elseif ($_POST['mode'] == 'change') {

	// 현재 비번하고 같은지 체크.
	$_mb = $db->fetch( sprintf("select name, email, password, password('%s') as n_password, password('%s') as c_password from ".GD_MEMBER." where m_no = %d", $db->_escape($_POST['n_password']), $db->_escape($_POST['c_password']), $sess['m_no']) , 1);

	if ($_mb['password'] != $_mb['c_password']) exit('0002');	// 현재 비밀번호 불일치
	if ($_mb['password'] == $_mb['n_password']) exit('0003');	// 이전 비밀번호와 같은 새 비밀번호 사용 금지.

	//패스워드 입력형식
	if($_POST['passwordSkin'] === 'Y'){
		if(passwordPatternCheck($_POST['n_password']) === false) exit('0004');
	} else {
		// 사용 가능 여부 (6자 이상 21~7E 까지 ascii)
		if (!preg_match('/^[\x21-\x7E]{6,}$/',$_POST['n_password'])) exit('0004');
	}

	// 변경
	$query = sprintf("
	update ".GD_MEMBER." set
		password = '%s',
		password_moddt = NOW()
	where m_no = %d
	"
	,$_mb['n_password']
	,$sess['m_no']
	);

	if ($db->query($query)) {

		$msg = '|비밀번호 변경이 완료되었습니다.';

		// 회원정보 수정 이벤트
		$info_cfg = $config->load('member_info');

		if ($info_cfg['event_use'] && (int)$info_cfg['event_emoney'] > 0) {
			$now = date('Y-m-d H:i:s');
			if ( $now >= $info_cfg['event_start_date'] && $now <= $info_cfg['event_end_date'] ) {

				// 지급 내역
				$query = sprintf("SELECT count(sno) from ".GD_LOG_EMONEY." where m_no = %d and memo = '회원정보 수정 이벤트' and regdt between '%s' and '%s'",$sess['m_no'], $info_cfg['event_start_date'], $info_cfg['event_end_date'] );
				list($history) = $db->fetch($query);
				if ($history < 1) {

					$query = sprintf("update ".GD_MEMBER." set emoney = emoney + %d where m_no = %d", $info_cfg['event_emoney'], $sess['m_no']);
					$db->query($query);


					$query = sprintf("insert into ".GD_LOG_EMONEY." set m_no = %d, ordno = '', emoney = %d, memo = '회원정보 수정 이벤트', regdt = '%s'", $sess['m_no'], $info_cfg['event_emoney'], $now);
					$db->query($query);

					$msg = '|"회원정보수정 이벤트"'.PHP_EOL.PHP_EOL.number_format($info_cfg['event_emoney']).'원이 지급되었습니다.';
				}
			}
		}

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

		exit('0000'.$msg);
	}
	else exit('9999');


}
exit('0000');
?>
