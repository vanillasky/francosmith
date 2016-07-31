<?php
/*
 * IP 접속 제한 설정 (관리자 IP 접속 제한, 쇼핑몰 IP 접속 제한 설정)
 * @author artherot @ godosoft development team.
 */
class IPAccessRestriction
{
	public		$_thisIP;								// 현재 접속자 아이피
	private		$_adminConfigFile;						// 관리자 IP 접속 설정 파일
	private		$_userConfigFile;						// 쇼핑몰 IP 접속제한 설정 파일
	private		$_exceptionConfigFile;					// 예외 처리
	private		$_accessKey;							// 체크를 위한 키 생성
	private		$_cookieExpire			= 60;			// 만료 시간 (분)
	private		$_qfile;
	public		$_cookieDebug			= array();		// 디버그

	/**
	 * 생성자
	 */
	public function __construct()
	{
		$this->_thisIP					= $_SERVER['REMOTE_ADDR'];
		$this->_adminConfigFile			= SHOPROOT.'/conf/config.admin_access_ip.php';
		$this->_userConfigFile			= SHOPROOT.'/conf/config.user_ip_access_restriction.php';
		$this->_exceptionConfigFile		= SHOPROOT.'/lib/login_ok_manager.php';
		$this->_accessKey				= encode($this->_thisIP.'^'.time(), 1);
	}

	/**
	 * 관리자 IP 접속제한 설정 정보
	 * @return  array $getData 설정 정보
	 */
	public function getAdminAccessIP()
	{
		// 기본 설정값
		if (is_file($this->_adminConfigFile)) {
			include $this->_adminConfigFile;
			$getData['set_ip_permit']		= $set_ip_permit;
			$getData['set_regist_ip']		= $set_regist_ip;
			$getData['get_file_time']		= filemtime($this->_adminConfigFile);
		} else {
			$getData['set_ip_permit']		= '0';
			$getData['set_regist_ip']		= array();
			$getData['get_file_time']		= 0;
		}

		return $getData;
	}

	/**
	 * 쇼핑몰 IP 접속제한 설정 정보
	 * @return  array $getData 설정 정보
	 */
	public function getUserAccessIP()
	{
		// 기본 설정값
		if (is_file($this->_userConfigFile)) {
			include $this->_userConfigFile;
			$getData['user_ip_access_restriction']	= $user_ip_access_restriction;
			$getData['user_ip_address']				= $user_ip_address;
			$getData['restriction_page']			= $restriction_page;
			$getData['restriction_page_url']		= $restriction_page_url;
			$getData['restriction_page_skin']		= $restriction_page_skin;
			$getData['get_file_time']				= filemtime($this->_userConfigFile);
		} else {
			$getData['user_ip_access_restriction']	= 'N';
			$getData['user_ip_address']				= array();
			$getData['restriction_page']			= '404';
			$getData['restriction_page_url']		= '';
			$getData['restriction_page_skin']		= '';
			$getData['get_file_time']				= 0;
		}

		return $getData;
	}

	/**
	 * IP 접속제한 설정 정보 저장
	 * @return  string $result (NO_DATA , NO_IP , true)
	 */
	public function saveAccessIP()
	{
		// 파일 저장을 위한 Class
		$this->_qfile	= Core::loader('qfile');

		$adminResult	= $this->saveAdminAccessIP();
		if($adminResult !== true ) {
			return $adminResult;
		}

		$userResult		= $this->saveUserAccessIP();
		if($userResult !== true ) {
			return $userResult;
		}

		return true;
	}

	/**
	 * 관리자 IP 접속제한 설정 정보 저장
	 * @return  string $result (NO_DATA , NO_IP , true)
	 */
	private function saveAdminAccessIP()
	{
		//설정값이 빈 값 혹은 0,1이 아닌경우 경고창 띄움
		if ($_POST['set_ip_permit'] == '' || ($_POST['set_ip_permit'] != 0 && $_POST['set_ip_permit'] != 1) ) {
			return 'NO_DATA';
		}

		// 관리자IP접속제한을 사용함으로 선택하고 IP는 등록하지 않았을때 경고창 띄움
		if($_POST['set_ip_permit'] == '1' && count($_POST['admin_ip_address']) < 1){
			return 'NO_IP';
		}

		// 파일에 저장
		$this->_qfile->open($this->_adminConfigFile);
		$this->_qfile->write('<?php'.PHP_EOL);
		$this->_qfile->write('$set_ip_permit	= \''.$_POST['set_ip_permit'].'\';'.PHP_EOL);

		if ($_POST['set_ip_permit'] == '1') {
			$set_ip = array();

			if (is_array($_POST['admin_ip_address'])){
				$_POST['admin_ip_address']	= array_filter($_POST['admin_ip_address']);		//배열의 빈값 제거.
				$_POST['admin_ip_address']	= array_unique($_POST['admin_ip_address']);		//중복IP제거

				foreach($_POST['admin_ip_address'] as $ip_address)
				{
					if (!$ip_address == trim($ip_address)){
						continue;
					}
					$set_ip[]	= $ip_address;
				}
			}

			$this->_qfile->write('$set_regist_ip	= array('.PHP_EOL);

			foreach ($set_ip as $key => $val )
			{
				$this->_qfile->write($key.'	=> \''.$val.'\','.PHP_EOL);
			}

			$this->_qfile->write(');'.PHP_EOL);
		}

		$this->_qfile->write('?>');
		$this->_qfile->close();
		chmod($this->_adminConfigFile, 0707);

		return true;
	}

	/**
	 * 쇼핑몰 IP 접속제한 설정 정보 저장
	 * @return  string $result (NO_DATA , NO_IP , true)
	 */
	private function saveUserAccessIP()
	{
		//설정값이 빈 값 혹은 0,1이 아닌경우 경고창 띄움
		if (empty($_POST['user_ip_access_restriction']) === true || in_array($_POST['user_ip_access_restriction'], array('Y','N')) === false ) {
			return 'NO_DATA';
		}

		// 관리자IP접속제한을 사용함으로 선택하고 IP는 등록하지 않았을때 경고창 띄움
		if($_POST['user_ip_access_restriction'] == 'Y' && count($_POST['user_ip_address']) < 1){
			return 'NO_IP';
		}

		// 파일에 저장
		$this->_qfile->open($this->_userConfigFile);
		$this->_qfile->write('<?php'.PHP_EOL);
		$this->_qfile->write('$user_ip_access_restriction	= \''.$_POST['user_ip_access_restriction'].'\';'.PHP_EOL);
		$this->_qfile->write('$restriction_page	= \''.$_POST['restriction_page'].'\';'.PHP_EOL);
		$this->_qfile->write('$restriction_page_url	= \''.$_POST['restriction_page_url'].'\';'.PHP_EOL);
		$this->_qfile->write('$restriction_page_skin	= \''.$_POST['restriction_page_skin'].'\';'.PHP_EOL);

		if ($_POST['user_ip_access_restriction'] == 'Y') {
			$set_ip	= array();

			if (is_array($_POST['user_ip_address'])){
				$_POST['user_ip_address']	= array_filter($_POST['user_ip_address']);		//배열의 빈값 제거.
				$_POST['user_ip_address']	= array_unique($_POST['user_ip_address']);		//중복IP제거

				foreach($_POST['user_ip_address'] as $ip_address)
				{
					if (!$ip_address == trim($ip_address) || $ip_address == $this->_thisIP) {
						continue;
					}
					$set_ip[]	= $ip_address;
				}
			}

			$this->_qfile->write('$user_ip_address	= array('.PHP_EOL);

			foreach ($set_ip as $key => $val )
			{
				$this->_qfile->write($key.'	=> \''.$val.'\','.PHP_EOL);
			}

			$this->_qfile->write(');'.PHP_EOL);
		}

		$this->_qfile->write('?>');
		$this->_qfile->close();
		chmod($this->_userConfigFile, 0707);

		return true;
	}

	/**
	 * 관리자 IP 접속제한 처리
	 * @return  boolean $result (true , false)
	 */
	public function setAdminAccessIP()
	{
		$checkResult	= $this->setAccessIP('admin');	// $checkResult 가 true 인 경우에만 접속 허용

		if ($checkResult === false) {
			msg('현재 접속하신 IP는 쇼핑몰 접근이 불가합니다.\r\n쇼핑몰 관리자에게 문의 하시기 바랍니다.','../../member/logout.php');
			exit();
		}

		return true;
	}

	/**
	 * 쇼핑몰 IP 접속제한 처리
	 * @return  boolean $result (true , false)
	 */
	public function setUserAccessIP()
	{
		$checkResult	= $this->setAccessIP('user');	// $checkResult 가 true 인 경우에만 접속 허용

		if ($checkResult === false) {
			// 쇼핑몰 IP접속제한 설정값
			$getData	= $this->getUserAccessIP();

			if ($getData['restriction_page'] === 'skin' && empty($getData['restriction_page_skin']) === false) {
				$redirectUrl	= $getData['restriction_page_skin'];
			}
			else if ($getData['restriction_page'] === 'url' && empty($getData['restriction_page_url']) === false) {
				$redirectUrl	= $getData['restriction_page_url'];
			}
			else {
				$redirectUrl	= '/404';
			}
			header('location:'.$redirectUrl);
			exit();
		}

		return true;
	}

	/**
	 * 접속 정보에 대한 쿠키 생성
	 * @param1  string $thisMode 모드 (admin , user)
	 * @return  string $result (쿠키정보)
	 */
	private function setAccessCookie($thisMode)
	{
		// 쿠키의 키이름
		$cookieKey		= 'gd_'.$thisMode.'_enamooPass';

		setcookie($cookieKey, $this->_accessKey, time() + ($this->_cookieExpire * 60), '/');
	}

	/**
	 * 접속 정보에 대한 쿠키 삭제
	 * @param1  string $thisMode 모드 (admin , user)
	 * @return  string $result (쿠키정보)
	 */
	private function delAccessCookie($thisMode)
	{
		// 쿠키의 키이름
		$cookieKey		= 'gd_'.$thisMode.'_enamooPass';

		setcookie($cookieKey, '', time() - 3600, '/');
	}

	/**
	 * 접속 정보에 대한 쿠키값 체크
	 * @param1  string $thisMode 모드 (admin , user)
	 * @return  boolean $result (true , false)
	 */
	private function checkAccessCookie($thisMode, $UnixTime)
	{
		// 쿠키의 키이름
		$cookieKey		= 'gd_'.$thisMode.'_enamooPass';

		// 접속 정보 쿠키가 없는 경우 false return
		if (empty($_COOKIE[$cookieKey]) === true) {
			// 디버그 저장
			$this->_cookieDebug[]	= $thisMode.' - no cookie';

			return false;
		}

		$tmp			= explode('^', decode($_COOKIE[$cookieKey],1));
		$accessIP		= $tmp[0];
		$accessTime		= $tmp[1];

		if ($accessIP === $this->_thisIP && $accessTime > $UnixTime) {
			// 디버그 저장
			$this->_cookieDebug[]	= $thisMode.' - ok pass';

			return true;
		} else {
			// 기존의 접속 정보 쿠키 삭제
			$this->delAccessCookie();

			// 디버그 저장
			$this->_cookieDebug[]	= $thisMode.' - expire';

			return false;
		}
	}

	/**
	 * IP 접속제한 처리
	 * @param1  string $thisMode 모드 (admin , user)
	 * @return  boolean $result (true , false)
	 */
	private function setAccessIP($thisMode)
	{
		if ($thisMode == 'admin') {
			// 관리자 IP접속제한 설정값
			$getData	= $this->getAdminAccessIP();

			if ($getData['set_ip_permit'] == 1) {
				$getData['ip_check']	= 'Y';
			}
			$getData['ip_address']		= $getData['set_regist_ip'];
		} else {
			// 쇼핑몰 IP접속제한 설정값
			$getData	= $this->getUserAccessIP();

			$getData['ip_check']		= $getData['user_ip_access_restriction'];
			$getData['ip_address']		= $getData['user_ip_address'];
		}

		// 설정이 안되어 있다면 패스
		if ($getData['ip_check'] !== 'Y') {
			// 디버그 저장
			$this->_cookieDebug[]	= $thisMode.' - no config';

			return true;
		}

		// 접속 정보 쿠키 체크
		if ($this->checkAccessCookie($thisMode, $getData['get_file_time']) === true) {
			return true;
		}

		// 예외 처리
		if (is_file($this->_exceptionConfigFile)) {
			include $this->_exceptionConfigFile;
			foreach ($m_ip as $cVal) {
				$tmp	= explode('.', $cVal);									// IP를 .으로 구분해서 배열처리
				$dTmp	= explode('~', $tmp[3]);								// D Class 를 ~ 구분해서 대역처리
				$cTmp	= trim($tmp[0]).'.'.trim($tmp[1]).'.'.trim($tmp[2]);	// C Class 까지의 IP 주소

				if (count($dTmp) == 1) {
					$dTmp[0]	= $tmp[3];
					$dTmp[1]	= $tmp[3];
				}

				for ($i = $dTmp[0]; $i <= $dTmp[1]; $i++) {
					$exceptionData	= $cTmp.'.'.trim($i);

					// 현재 접속자의 IP가 있다면 예외처리
					if ($this->_thisIP === $exceptionData) {

						// 접속 정보 쿠키 생성
						$this->setAccessCookie($thisMode);

						// 디버그 저장
						$this->_cookieDebug[]	= $thisMode.' - exception';

						return true;											// 무조건 접속 허용
					}
				}
				unset($tmp, $cTmp, $dTmp, $exceptionData, $i);
			}
			unset($m_ip, $cVal);
		}

		// IP를 C Class 까지만 처리 (C Class 까지 배열키로 사용하고 D Class 는 값으로 사용함)
		foreach ($getData['ip_address'] as $cVal) {
			$tmp	= explode('.', $cVal);									// IP를 .으로 구분해서 배열처리
			$dTmp	= explode('~', $tmp[3]);								// D Class 를 ~ 구분해서 대역처리
			$cTmp	= trim($tmp[0]).'.'.trim($tmp[1]).'.'.trim($tmp[2]);	// C Class 까지의 IP 주소

			if (count($dTmp) == 1) {
				$dTmp[0]	= $tmp[3];
				$dTmp[1]	= $tmp[3];
			}

			for ($i = $dTmp[0]; $i <= $dTmp[1]; $i++) {
				$checkData	= $cTmp.'.'.trim($i);

				// 현재 접속자의 IP가 있다면 예외처리
				if ($this->_thisIP === $checkData) {
					// 모드에 따라 처리
					if ($thisMode == 'admin') {

						// 접속 정보 쿠키 생성
						$this->setAccessCookie($thisMode);

						// 디버그 저장
						$this->_cookieDebug[]	= $thisMode.' - access';

						return true;										// 관리자 IP 접속제한인 경우 접속 허용
					} else {
						return false;										// 쇼핑몰 IP 접속제한인 경우 접속 불가
					}
				}
			}
			unset($tmp, $cTmp, $dTmp, $checkData, $i);
		}
		unset($cVal);

		// 해당 되는 IP가 없는 경우 모드에 따라 처리
		if ($thisMode == 'admin') {
			return false;										// 관리자 IP 접속제한인 경우 접속 불가
		} else {
			// 접속 정보 쿠키 생성
			$this->setAccessCookie($thisMode);

			// 디버그 저장
			$this->_cookieDebug[]	= $thisMode.' - access';

			return true;										// 쇼핑몰 IP 접속제한인 경우 접속 허용
		}
	}


	/**
	 * 스킨내 service 폴더의 HTML을 불러옴
	 * @param1  string $skinName 스킨명
	 * @param2  string $folderName 폴더명
	 * @return  array $result 파일 배열
	 */
	public function getSkinFolderFile($skinName, $folderName)
	{
		// 폴더 경로
		$skinFolder	= dirname(__FILE__) . "/../data/skin/".$skinName."/".$folderName."/";

		// 추출할 데이타
		$getData	= array();

		// 폴더 검사 후 데이타 추출
		if (is_dir($skinFolder)) {
			if ($dh = opendir($skinFolder)) {
				while (($file = readdir($dh)) !== false)
				{
					if (filetype($skinFolder . $file) === 'file' && substr($file, -4) === '.htm') {
						$getData[]	= $file;
					}
				}
				closedir($dh);

				// 추출한 파일명을 정렬
				sort($getData);
			}
		}

		return $getData;
	}
}
?>