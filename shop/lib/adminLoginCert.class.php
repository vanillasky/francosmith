<?
/**
 * Admin Login Cert 라이브러리
 * @author pr
 */
class adminLoginCert
{
	var $useLoginCert;

	function adminLoginCert()
	{
		// 관리자보안 인증여부
		$cfgfile = dirname(__FILE__).'/../conf/config.admin_login_cert.php';
		if(file_exists($cfgfile)) @include $cfgfile;
		if ($admLoginCertCfg['use'] == 'Y') {
			$this->useLoginCert = true;
		}
		else {
			$this->useLoginCert = false;
		}

		// 접속 IP 체크
		if ($admLoginCertCfg['unCheckGdip'] != 'Y') {
			if ($this->checkGdip() === true) {
				$this->useLoginCert = false;
			}
		}
	}

	/**
	 * 접속 IP 체크
	 * @return bool
	 */
	function checkGdip()
	{
		// 1. IP 정의
		$myip = $_SERVER['REMOTE_ADDR'];
		$ex_myip = explode('.', $myip);
		$myip_ABC = $ex_myip[0] . '.' . $ex_myip[1] . '.' . $ex_myip[2] . '.'; // 접속IP의 ABC클래스
		$myip_Dclass = $ex_myip[3]; // 접속IP의 D클래스

		// 2. IP 검증
		$result = '';
		$mipFile = dirname(__FILE__).'/../lib/login_ok_manager.php';
		if(file_exists($mipFile)) @include $mipFile;
		$m_ip = (array)$m_ip;

		if (in_array($myip, $m_ip) === true) { // 동일한 IP 확인
			$result = 'ok';
		}
		else { // 허용IP 대역 확인
			$m_cnt = count($m_ip);
			for ($i = 0; $i < $m_cnt; $i++) {
				$tmp = explode('~', $m_ip[$i]);
				$regIP_Dclass_end = trim($tmp[1]); // 대역대 번호

				if ($regIP_Dclass_end != '') { // 대역대인 경우
					$ex_regip = explode('.', trim($tmp[0]));
					$regip_ABC = $ex_regip[0] . '.' . $ex_regip[1] . '.' . $ex_regip[2] . '.'; // 허용IP의 ABC클래스
					$regip_Dclass = $ex_regip[3]; // 허용IP의 D클래스

					if ($result != 'ok' ){
						if ($myip_ABC == $regip_ABC) { // 허용IP와 접속IP의 C클래스까지 비교
							if( ($regip_Dclass <= $myip_Dclass) && ($myip_Dclass <= $regIP_Dclass_end) ) { // 허용IP 대역 비교
								$result = 'ok';
								break;
							}
						}
					}
				}
			}
		}

		// 3. 요청결과에 따라 결과값 반환
		if ($result === 'ok') {
			return true;
		}
		else {
			return false;
		}
	}

	/**
	 * 로그인에서 관리자보안 인증상태
	 *
	 * @return string (상태코드 : unused : 미사용, success : 인증됨, failure : 인증 안됨)
	 *
	 */
	function loginStatus()
	{
		// 1. 관리자보안 인증 사용여부
		if ($this->useLoginCert !== true) return 'unused';

		// 2. OTP 수신처 목록 없으면
		if (count($this->getOtpContants()) < 1) return 'unused';

		// 3. SMS 포인트 잔여(최소 1포인트) 체크
		if (isset($_SESSION['alcCompare']) !== true) {
			$sms = Core::loader('Sms');
			if ((int)$sms->smsPt < 1) return 'unused';
		}

		// 4. 인증확인은 하고 인증로그인 안한 경우
		if (isset($_SESSION['alcCompare']) === true && isset($_SESSION['alcAccess']) !== true) {
			return 'success';
		}
		else {
			return 'failure';
		}
	}

	/**
	 * 관리모드 내에서 관리자보안 인증 로그인상태
	 *
	 * @return string (상태코드 : unused : 미사용, success : 인증 로그인됨, failure : 인증 로그인 안됨)
	 *
	 */
	function inStatus()
	{
		// 1. 관리자보안 인증 사용여부
		if ($this->useLoginCert !== true) return 'unused';

		// 2. OTP 수신처 목록 없으면
		if (count($this->getOtpContants()) < 1) return 'unused';

		// 3. SMS 포인트 잔여(최소 1포인트) 체크
		if (isset($_SESSION['alcCompare']) !== true) {
			$sms = Core::loader('Sms');
			if ((int)$sms->smsPt < 1) return 'unused';
		}

		// 4. 인증확인 및 인증로그인한 경우
		if (isset($_SESSION['alcCompare']) === true && isset($_SESSION['alcAccess']) === true) {
			return 'success';
		}
		else {
			unset($_SESSION['alcCompare'], $_SESSION['alcAccess']);
			return 'failure';
		}
	}

	/**
	 * OTP 수신처 목록
	 *
	 * @param string $asterisk 별표여부
	 * @return array
	 *
	 */
	function getOtpContants($asterisk='N')
	{
		global $db;

		$dormant = Core::loader('dormant');

		$contacts = array();
		$res = $db->query("select aoc.aoc_sno, aoc.aoc_mobile, aoc.aoc_m_no, mb.level, mb.dormant_regDate from gd_admin_otp_contact as aoc inner join ".GD_MEMBER." as mb on aoc.aoc_m_no = mb.m_no order by aoc_regdt asc");

		while( $row = $db->fetch($res, 1) ) {
			if($row['dormant_regDate'] != '0000-00-00 00:00:00'){
				$row['level'] = $dormant->getDormantInfo('level', $row);
			}
			if($row['level'] < 80){
				continue;
			}
			if ($asterisk == 'Y') {
				$len = strlen(substr($row['aoc_mobile'], 3, -4));
				$row['aoc_mobile'] = substr_replace($row['aoc_mobile'], str_repeat('*', $len), 3, -4);
			}
			$contacts[] = $row;
		}
		return $contacts;
	}

	/**
	 * OTP 수신처 정보
	 *
	 * @param string $aocSno Contact키
	 * @return array or bool
	 *
	 */
	function getOtpContact($aocSno)
	{
		global $db;

		$dormant = Core::loader('dormant');

		$query = sprintf("select aoc.aoc_mobile, mb.name, mb.m_id, mb.m_no from gd_admin_otp_contact as aoc inner join ".GD_MEMBER." as mb on aoc.aoc_m_no = mb.m_no where mb.level >= 80 and aoc.aoc_sno = '%s'", $db->_escape($aocSno));
		if (($_aoc = $db->fetch($query, 1)) === false) {
			$query = $dormant->getOtpContactQuery($aocSno);
			if (($_aoc = $db->fetch($query, 1)) === false) {
				return false;
			}
		}
		return $_aoc;
	}

	/**
	 * Login OTP 전송
	 *
	 * @param string $aocSno Contact키
	 * @param string $token 토큰
	 * @return string
	 *
	 */
	function sendLoginOtp($aocSno, $token)
	{
		global $db, $cfg;
		$gdOtp = Core::loader('gd_otp');
		$_token = $gdOtp->getToken(); // 토큰

		// 토큰 검증
		if ($token != $_token) {
			return '0002'; // 잘못된 접근
		}

		// OTP 수신처 정보
		if (($_aoc = $this->getOtpContact($aocSno)) === false) {
			return '0001'; // 사용자 정보 미존재
		}

		// 토큰 저장
		$now = time();
		$query = sprintf("select mb.m_id, otp.token, otp.expire from ".GD_MEMBER." AS mb left join ".GD_OTP." as otp ON mb.m_id = otp.m_id AND otp.expire > '%s' where mb.m_no = '%s'", date('Y-m-d H:i:s', $now), $db->_escape($_aoc['m_no']));
		if (($_mb = $db->fetch($query, 1)) !== false) {
			if (empty($_mb['token']) || $_token != $_mb['token']) {
				$db->query(sprintf("delete from ".GD_OTP." where m_id = '%s'", $db->_escape($_mb['m_id'])));

				$_mb['token'] = $_token;

				$query = sprintf("INSERT INTO ".GD_OTP." SET m_id = '%s', token = '%s', expire = '%s'", $_mb['m_id'], $_mb['token'], date('Y-m-d H:i:s', strtotime('+3 minute', $now))); // 유효기간 3분
				$db->query($query);
			}
		}
		else {
			return '0001'; // 사용자 정보 미존재
		}

		// OTP 전송
		$authNum = $gdOtp->getOTP();
		$sf = Core::loader('stringFormatter');

		// 휴대폰 전송
		if ($_aoc['aoc_mobile']) {
			// 발송건수
			$sms = Core::loader('Sms');
			$sms_sendlist = $sms->loadSendlist();
			if ((int)$sms->smsPt < 1) {
				return '0008'; // SMS 잔여 포인트 부족
			}

			$GLOBALS['dataSms']['authNum'] = $authNum;

			$msg = parseCode('[{shopName}]'."\n".'관리모드 인증번호는 {authNum} 입니다. 정확히 입력해주세요.');
			
			$sms->log($msg,$_aoc['aoc_mobile'],'',1);
			$sms_sendlist->setSimpleInsert($_aoc['aoc_mobile'], $sms->smsLogInsertId, '');
			$sms->send($msg,$_aoc['aoc_mobile'],$cfg['smsRecall']);
			$sms->update_ok_eNamoo = true;
			$sms->update();
		}
		else {
			return '0005'; // 휴대폰번호 포맷 불일치
		}

		// 업데이트
		$query = sprintf("update ".GD_OTP." set otp = '%s', auth = 0 where m_id = '%s' AND token = '%s'", $authNum, $db->_escape($_mb['m_id']), $db->_escape($_mb['token']));
		$db->query($query);

		return '0000';
	}

	/**
	 * Login OTP 확인
	 *
	 * @param string $otp OTP
	 * @param string $aocSno Contact키
	 * @param string $token 토큰
	 * @return string
	 *
	 */
	function compareLoginOtp($otp, $aocSno, $token)
	{
		global $db;
		$gdOtp = Core::loader('gd_otp');
		$_token = $gdOtp->getToken(); // 토큰

		// 토큰 검증
		if ($token != $_token) {
			return '0002'; // 잘못된 접근
		}

		// OTP 수신처 정보
		if (($_aoc = $this->getOtpContact($aocSno)) === false) {
			return '0001'; // 사용자 정보 미존재
		}

		// 체크
		$query = sprintf("select mb.name, mb.m_id, otp.token, otp.otp, otp.expire, otp.auth from ".GD_OTP." as otp inner join ".GD_MEMBER." as mb on otp.m_id = mb.m_id where mb.m_no = '%s' AND otp.token > '' AND otp.token = '%s'", $db->_escape($_aoc['m_no']), $db->_escape($token));
		if (($_mb = $db->fetch($query, 1)) !== false) {
			// 유효기간(3분) 체크
			if ($_mb['expire'] < date('Y-m-d H:i:s')) {
				$db->query(sprintf("delete from ".GD_OTP." where m_id = '%s'", $db->_escape($_mb['m_id'])));
				return '0003';
			}

			// 인증번호 기사용여부 체크
			if ($_mb['auth'] == '1') {
				$db->query(sprintf("delete from ".GD_OTP." where m_id = '%s'", $db->_escape($_mb['m_id'])));
				return '0004';
			}

			// OTP 체크
			if ($_mb['otp'] == $otp) {
				$query = sprintf("update ".GD_OTP." set auth = 1 where m_id = '%s' AND token = '%s'", $db->_escape($_mb['m_id']), $db->_escape($_mb['token']));
				$db->query($query);
				$_SESSION['alcCompare'] = md5(crypt(''));
				return '0000';
			}
			else {
				return '0006'; // 인증번호 상이
			}
		}
		else {
			return '0001'; // 사용자 정보 미존재
		}
	}

	/**
	 * Regit OTP 전송
	 *
	 * @param string $mobile 휴대폰번호
	 * @param string $token 토큰
	 * @return string
	 *
	 */
	function sendRegitOtp($mobile, $token)
	{
		global $db, $cfg, $sess;
		$gdOtp = Core::loader('gd_otp');
		$_token = $gdOtp->getToken(); // 토큰

		// 토큰 검증
		if ($token != $_token) {
			return '0002'; // 잘못된 접근
		}

		// 토큰 저장
		$now = time();
		$query = sprintf("select mb.m_id, mb.mobile, otp.token, otp.expire from ".GD_MEMBER." AS mb left join ".GD_OTP." as otp ON mb.m_id = otp.m_id AND otp.expire > '%s' where mb.m_id = '%s'", date('Y-m-d H:i:s', $now), $db->_escape($sess['m_id']));
		if (($_mb = $db->fetch($query, 1)) !== false) {
			if (empty($_mb['token']) || $_token != $_mb['token']) {
				$db->query(sprintf("delete from ".GD_OTP." where m_id = '%s'", $db->_escape($_mb['m_id'])));

				$_mb['token'] = $_token;

				$query = sprintf("INSERT INTO ".GD_OTP." SET m_id = '%s', token = '%s', expire = '%s'", $_mb['m_id'], $_mb['token'], date('Y-m-d H:i:s', strtotime('+3 minute', $now))); // 유효기간 3분
				$db->query($query);
			}
		}
		else {
			return '0001'; // 사용자 정보 미존재
		}

		// OTP 전송
		$authNum = $gdOtp->getOTP();
		$sf = Core::loader('stringFormatter');

		// 휴대폰 전송
		if ($mobile) {
			// 발송건수
			$sms = Core::loader('Sms');
			$sms_sendlist = $sms->loadSendlist();
			if ((int)$sms->smsPt < 1) {
				return '0008'; // SMS 잔여 포인트 부족
			}

			$GLOBALS['dataSms']['authNum'] = $authNum;

			$msg = parseCode('[{shopName}]'."\n".'관리모드 인증번호는 {authNum} 입니다. 정확히 입력해주세요.');

			$sms->log($msg,$mobile,'',1);
			$sms_sendlist->setSimpleInsert($mobile, $sms->smsLogInsertId, '');
			$sms->send($msg,$mobile,$cfg['smsRecall']);
			$sms->update_ok_eNamoo = true;
			$sms->update();
		}
		else {
			return '0005'; // 휴대폰번호 포맷 불일치
		}

		// 업데이트
		$query = sprintf("update ".GD_OTP." set otp = '%s', auth = 0 where m_id = '%s' AND token = '%s'", $authNum, $db->_escape($_mb['m_id']), $db->_escape($_mb['token']));
		$db->query($query);

		return '0000';
	}

	/**
	 * Regit OTP 확인
	 *
	 * @param string $otp OTP
	 * @param string $token 토큰
	 * @return string
	 *
	 */
	function compareRegitOtp($otp, $token)
	{
		global $db, $sess;
		$gdOtp = Core::loader('gd_otp');
		$_token = $gdOtp->getToken(); // 토큰

		// 토큰 검증
		if ($token != $_token) {
			return '0002'; // 잘못된 접근
		}

		// 체크
		$query = sprintf("select mb.name, mb.m_id, otp.token, otp.otp, otp.expire, otp.auth from ".GD_OTP." as otp inner join ".GD_MEMBER." as mb on otp.m_id = mb.m_id where otp.m_id = '%s' AND otp.token > '' AND otp.token = '%s'", $db->_escape($sess['m_id']), $db->_escape($token));
		if (($_mb = $db->fetch($query, 1)) !== false) {
			// 유효기간(3분) 체크
			if ($_mb['expire'] < date('Y-m-d H:i:s')) {
				$db->query(sprintf("delete from ".GD_OTP." where m_id = '%s'", $db->_escape($_mb['m_id'])));
				return '0003';
			}

			// 인증번호 기사용여부 체크
			if ($_mb['auth'] == '1') {
				$db->query(sprintf("delete from ".GD_OTP." where m_id = '%s'", $db->_escape($_mb['m_id'])));
				return '0004';
			}

			// OTP 체크
			if ($_mb['otp'] == $otp) {
				$query = sprintf("update ".GD_OTP." set auth = 1 where m_id = '%s' AND token = '%s'", $db->_escape($_mb['m_id']), $db->_escape($_mb['token']));
				$db->query($query);
				return '0000';
			}
			else {
				return '0006'; // 인증번호 상이
			}
		}
		else {
			return '0001'; // 사용자 정보 미존재
		}
	}

	/**
	 * 설정 저장
	 *
	 * @param array $data 사용여부 등
	 *
	 */
	function setAdminLoginCert($data)
	{
		$val = array(
			'use' =>($data['use']=='Y') ? 'Y' : 'N'
		);

		$qfile = Core::loader('qfile');
		$qfile->open(dirname(__FILE__).'/../conf/config.admin_login_cert.php');
		$qfile->write("<? \n");
		$qfile->write("\$admLoginCertCfg = array( \n");
		foreach ($val as $k=>$v){
			if($v!='')$qfile->write("'$k' => '$v', \n");
		}
		$qfile->write(") \n;");
		$qfile->write("?>");
		$qfile->close();
		@chmod(dirname(__FILE__).'/../conf/config.admin_login_cert.php',0707);

		if ($data['use'] == 'Y') { // '사용함'으로 설정시 로그아웃 방지 목적으로 인증처리
			$_SESSION['alcAccess'] = md5(crypt(''));
			$_SESSION['alcCompare'] = md5(crypt(''));
		}
	}

	/**
	 * OTP 수신처 삭제
	 *
	 * @param array $aocSnos Contact키
	 *
	 */
	function delContact($aocSnos)
	{
		global $db;
		foreach ($aocSnos as $aoc_sno) {
			$db->query("delete from gd_admin_otp_contact WHERE aoc_sno='$aoc_sno'");
		}
	}

	/**
	 * OTP 수신처 등록
	 *
	 * @param array $data 데이터
	 *
	 */
	function regitContact($data)
	{
		global $db;
		$data['mobile'] = trim(str_replace('-', '', $data['mobile'])); // 하이픈(-) 제거
		$query = sprintf("INSERT INTO gd_admin_otp_contact SET aoc_m_no = '%s', aoc_mobile = '%s', aoc_regdt = now()", $data['mno'], $data['mobile']);
		$db->query($query);

		if ($this->useLoginCert === true) { // 사용여부가 '사용함'인 경우 로그아웃 방지 목적으로 인증처리
			$_SESSION['alcAccess'] = md5(crypt(''));
			$_SESSION['alcCompare'] = md5(crypt(''));
		}
	}
}
?>