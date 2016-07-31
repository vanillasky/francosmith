<?
class Lms
{
	var $msgOn, $smsPass, $smsPt, $r_data, $regdt, $smsLogInsertId, $countNum;

	function Lms($msgOn=false)
	{
		$this->msgOn = $msgOn;
		$this -> getSno();
		$this -> r_data = array();
		$this->regdt	= date('Y-m-d H:i:s');
	}

	function getSno()
	{
		// get godo's serial
		$file = dirname(__FILE__)."/../conf/godomall.cfg.php";
		if (!is_file($file)) return false;
		$file = file($file);
		$this->godo = decode($file[1],1);
		if (!$this->godo[sno]){
			if ($this->msgOn) msg("업체고유번호가 지정되어 있지 않습니다",1);
			return false;
		}
		// get smsPassword
		$file = dirname(__FILE__)."/../conf/config.php";
		if (!is_file($file)) return false;
		@include($file);
		$this -> smsPass = $cfg[smsPass];
		if(!$this -> smsPass)$this -> smsPass = "1111";
		// get smsPoint
		$tmp = array(	 'type'=>'search', 'sno' => $this->godo[sno], 'pass' => $this->smsPass );
		$res = $this -> lms_socket($tmp);
		/*
		if(!preg_match('/result=\[[0-9]+\]/',$res)){
			return false;
		}
		*/
		$res = str_replace(array('result','=','[',']'),'',$res);
		$this -> smsPt = $res;
		return true;
	}

	function lms_socket($arr){
		$host = "godosms.godo.co.kr";
		$port = 1686;

		foreach($arr as $k => $v) {
			$strSms .= $k.":".$v."\n";
		}

		if($strSms){
			$strSms = base64_encode($strSms);
			$sock = @fsockopen($host, $port, $errno, $errstr, 30);
			@fputs($sock, $strSms);
			$response = fread($sock, 256);
			fclose($sock);
		}
		if($response)return base64_decode($response);
	}

	function send($tran_msg,$tran_phone,$tran_callback='',$send_date='',$alram_etc='',$tran_type='send',$tran_subject='')
	{
		if ( $this->smsPt <= 0 ){
			if ($this->msgOn) {
				msg("SMS 잔여콜수가 부족합니다. 추가 충전하셔서 사용하세요");
				exit;
			}
			return false;
		}
		if(trim(str_replace('-', '', $tran_phone)) == '') return false;
		//if (!trim($tran_msg) || !trim($tran_phone)) return false;

		$tran_callback = str_replace("-","",$tran_callback);
		$tran_subject = $this->setSubjectProcess($tran_subject, '');
		$tran_msg = $this->setMsgProcess($tran_msg, '');
		$add = false;
		$tp = count($this -> r_data) - 1;
		if(!$send_date) {
			$send_date = $this->regdt;
		}
		$res_etc = substr($send_date,0,4).substr($send_date,5,2).substr($send_date,8,2);

		if($tp > -1 && ($tran_type == 'send' || $tran_type == 'res_send')){
			if($this -> r_data[$tp][msg] == $tran_msg){
				$tmp = explode(',',$this -> r_data[$tp][hp]);
				if(count($tmp) < 30) $add= true;
				else $this -> update();
			}
		}

		##set msg
		if($add){
			$this -> r_data[$tp][hp] .= ",".$tran_phone;
		}else{
			$this -> r_data[] = array(
				'type' => 'res_send',
				'sno' => $this->godo[sno],
				'pass' => $this -> smsPass,
				'callback' => $tran_callback,
				'hp' => $tran_phone,
				'res_date' => $send_date,
				'res_etc' => $res_etc,
				'subject' => $tran_subject,
				'__head__' => '__body__',
				'msg' => $tran_msg
			);
		}

		$this->smsPt = $this->smsPt - 3;
		return true;
	}

	function update()
	{
		if($this -> r_data){
			$sms_sendlist = $this->loadSendlist();
			foreach($this -> r_data as $v) {
				$sms_phoneNumber = $v['hp'];

				//(-) 제거
				$v['hp'] = $sms_sendlist->setPhoneNumberApi($v['hp']);
				// 발송되는 번호 갯수 카운팅
				$hpCount = count(explode(',', $v['hp']));

				$res = $this -> lms_socket($v);
				$res = str_replace(array('result','=','[',']'),'',$res);
				if($res == 0){
					//접수상태 update
					$sms_sendlist->updateSendStatus($this->smsLogInsertId , $sms_phoneNumber);
					$this->countNum['success'] += $hpCount;
				}else{
					$this->countNum['fail'] += $hpCount;
				}
			}
			//실패시 sms log update
			$sms_sendlist->updateSmsLogAcceptFail($this->smsLogInsertId);
		}
		$this -> r_data = array();
		$file = dirname(__FILE__)."/../conf/sms.cfg.php";
		if(is_file($file)) unlink($file);
		$fp = fopen($file,"w");
		fwrite($fp,"<?/* \n");
		fwrite($fp,$this->smsPt."\n");
		fwrite($fp,"*/?>");
		fclose($fp);
		@chmod($file,0707);

	}

	function log($msg,$to_tran,$type,$cnt,$reserve='',$subject='')
	{
		if($subject) {
			$subject = $this->setSubjectProcess($subject, 'y');
		}
		$msg = $this->setMsgProcess($msg, 'y');
		$status = $this->getStatus($reserve);
		$to_tran = str_replace("-","",$to_tran);
		$query = "
		insert into ".GD_SMS_LOG." set
			sms_type	= 'lms',
			msg		= '".$msg."',
			subject		= '".$subject."',
			type	= '$type',
			to_tran	= '$to_tran',
			cnt		= '$cnt',
			status	= '$status',
			reservedt = '$reserve',
			regdt	= '".$this->regdt."'
		";
		$GLOBALS[db]->query($query);
		$this->smsLogInsertId =  $GLOBALS[db]->_last_insert_id();
	}

	function lms_Control($phone,$msg,$alram_Date,$etc_data,$type,$subject){
		if( $type == "new" ){
			$this->send($msg,$phone,$phone,$alram_Date,$etc_data,'res_send',$subject);
			$this->update_ok_eNamoo = true;
			$this->update();
		}else if( $type == "delete" ){
			$this->send($msg,$phone,$phone,$alram_Date,$etc_data,'res_delete',$subject);
			$this->update_ok_eNamoo = true;
			$this->update();
			$this->smsPt = $this->smsPt + 2;
		}else{
			$this->send($msg,$phone,$phone,$alram_Date,$etc_data,'res_delete',$subject);
			$this->send($msg,$phone,$phone,$alram_Date,$etc_data,'res_send',$subject);
			$this->update_ok_eNamoo = true;
			$this->update();
			$this->smsPt = $this->smsPt + 2;
		}
		return true;
	}

	/*
	 * get sendlist instance
	 * @param
	 * @return instance
	 * @date 2014-11-20
	 */
	function loadSendlist()
	{
		include_once(dirname(__FILE__) . '/../lib/sms_sendlist.class.php');
		$sms_sendlist = new sms_sendlist();

		return $sms_sendlist;
	}

	/*
	* SMS_LOG 즉시발송 status 3-결과조회대기, 4-발송완료
	*		  예약발송 status 1-발송대기, 2-예약취소, 3-결과수신대기, 4-발송완료
	*/
	function getLogStatus($status)
	{
		switch((int)$status){
			case 1:
				return '발송대기';
			break;

			case 2:
				return '예약취소';
			break;

			case 3:
				return '결과수신대기';
			break;

			case 4:
				return '발송완료';
			break;
		}
	}

	/*
	 * 초기 status 셋팅
	 * @param string
	 * @return int
	 * @date 2014-11-20
	 */
	function getStatus($reserve)
	{
		if($reserve){
			return 1;
		}
		else {
			return 3;
		}
	}

	/*
	 * 제목 가공
	 * @param string
	 * @return string
	 * @date 2014-11-20
	 */
	function setSubjectProcess($_subject, $parseCode='')
	{
		if($parseCode == 'y'){
			$_subject = parseCode($_subject);
		}

		$subject = addslashes($_subject);

		return $subject;
	}

	/*
	 * 메세지 가공
	 * @param string
	 * @return string
	 * @date 2014-11-20
	 */
	function setMsgProcess($_msg, $parseCode='')
	{
		if($parseCode == 'y'){
			$_msg = parseCode($_msg);
		}

		$msg = addslashes($_msg);

		return $msg;
	}

	function registerSmsShutdownLog()
	{
		if(function_exists('date_default_timezone_set')){
			date_default_timezone_set('Asia/Seoul');
		}

		$errorMessage = '';
		$logPath = dirname(__FILE__) . '/../log/smsAccess/';
		$logFileNamePrepix = 'smsAccessShutdownLog_';
		$logFileName = $logFileNamePrepix . date('Ymd') . '.log';
		$saveLogFileName = $logPath . $logFileName;
		if(!file_exists($logPath)) {
			@mkdir($logPath, 0707);
			@chmod($logPath, 0707);
		}

		$errorMessage = $this->getSmsShutdownLogMessage();

		@error_log($errorMessage, 3, $saveLogFileName);
		@chmod($saveLogFileName, 0707);

		$this->deleteSmsShutdownGarbageLog($logFileNamePrepix, $logPath);
	}

	function deleteSmsShutdownGarbageLog($logFileNamePrepix, $logPath)
	{
		$searchPath = realpath($logPath) . '/' . $logFileNamePrepix . date("Ym", strtotime("-3 month")) . '*';
		foreach(glob($searchPath) as $logFile){
			if(strpos($logFile, $logFileNamePrepix) !== false){
				@unlink($logFile);
			}
		}
	}

	function getSmsShutdownLogMessage()
	{
		global $_SESSION, $_SERVER, $_POST;

		$resultErrorMessage = '';
		$errorMessage = array();
		$errorMessage['SCRIPT_START_TIME'] = $this->regdt;
		$errorMessage['SCRIPT_END_TIME'] = date("Y-m-d H:i:s");
		$errorMessage['ID'] = $_SESSION['sess']['m_id'];
		$errorMessage['IP'] = $_SERVER['REMOTE_ADDR'];
		$errorMessage['SMS_SUBJECT'] = $_POST['lms_subject'];
		$errorMessage['SMS_MESSAGE'] = $_POST['lms_msg'];
		$errorMessage['SMS_SEND_TYPE'] = 'LMS';
		$errorMessage['SMS_TYPE'] = $_POST['type'];
		$errorMessage['MEMORY_ERROR'] = 'MEMORY 정상';
		$errorMessage['CONNECT_ERROR'] = '';

		if(function_exists('error_get_last')){
			$error = array();
			$error = error_get_last();
			if(strpos($error['message'], 'Allowed memory size') === 0){
				$errorMessage['MEMORY_ERROR'] = $error['message'];
			}
		}

		switch (connection_status()) {
			case CONNECTION_NORMAL: // Connection 정상인 경우
				$errorMessage['CONNECT_ERROR'] = 'CONNECTION_OK [Connection 정상]';
			break;

			case CONNECTION_TIMEOUT: // Connection TIMEOUT 종료
				$errorMessage['CONNECT_ERROR'] = 'CONNECTION_TIMEOUT [Connection TIMEOUT 종료]';
			break;

			case CONNECTION_ABORTED: // Connection Client 절단(Client 절단 수용한 경우)
				$errorMessage['CONNECT_ERROR'] = 'CONNECTION_ABORTED [Connection Client 절단]';
			break;

			case (CONNECTION_ABORTED & CONNECTION_TIMEOUT): // Connection Client 절단되고 TIMEOUT 종료(Client 절단 무시한 경우)
				$errorMessage['CONNECT_ERROR'] = 'CONNECTION_TIMEOUT_ABORTED [Connection Client 절단되고 TIMEOUT 종료]';
			break;

			default: // Connection 알려지지 않은 에러
				$errorMessage['CONNECT_ERROR'] = 'CONNECTION_UNKNOWN_ERROR [알려지지 않은 에러]';
			break;
		}

		$resultErrorMessage = PHP_EOL . '===== SMS ACCESS SHUTDOWN LOG START ====='. PHP_EOL;
		foreach($errorMessage as $key => $value){
			$resultErrorMessage .= $key . ' = ' . $value . PHP_EOL;
		}
		$resultErrorMessage .= '===== SMS ACCESS SHUTDOWN LOG END ====='. PHP_EOL. PHP_EOL;

		return $resultErrorMessage;
	}
}
?>