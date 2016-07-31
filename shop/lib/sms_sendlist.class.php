<?php
/*
 * sms sendlist 처리 클래스
 *
 * @author sms_sendlist.class.php workingby <bumyul2000@godo.co.kr>
 * @version 1.0
 * @date 2014-11-20
 */
class sms_sendlist
{
	var $sms_logNo, $sms_mode;

	/*
	 * SMS SENDLIST array insert
	 * @param array
	 * @return void
	 * @date 2014-11-20
	 */
	function setListInsert($data)
	{
		global $db;

		$chunkNum	= 50;
		$idx		= 0;
		$total		= count($data);
		$dataArray  = array_chunk($data, $chunkNum);

		foreach($dataArray as $list){
			$query = "INSERT INTO " . GD_SMS_SENDLIST . " (sms_mode, sms_memNo, sms_logNo, sms_name, sms_phoneNumber, sms_phoneNumberApi, sms_status, sms_send_status, sms_regdt) VALUES ";
			foreach($list as $key => $value){
				$idx++;
				if(($chunkNum-1) == $key || $idx == $total){
					$queryEnd = ";";
				}
				else {
					$queryEnd = ",";
				}
				$query .= " ('". $this->sms_mode . "', '". $value['sms_memNo'] . "', '". $this->sms_logNo . "', '". $value['sms_name'] . "', '". $value['phone'] . "', '". $this->setPhoneNumberApi($value['phone']) . "' , 'r', 'n', now())" . $queryEnd;
			}
			$db->query($query);
		}
	}

	/*
	 * SMS SENDLIST simple insert
	 * @param string phoneNumber, sms log no, reserve
	 * @return void
	 * @date 2014-11-20
	 */
	function setSimpleInsert($sms_phoneNumber, $sms_logNo, $reserve){
		$list['sms_mode']			= $this->getSms_mode($reserve);
		$list['sms_phoneNumber']	= $sms_phoneNumber;
		list($list['sms_name'], $list['sms_memNo'])  = $this->getMember($sms_phoneNumber);
		$GLOBALS[db]->query("
			INSERT INTO " . GD_SMS_SENDLIST . " SET
				sms_mode			= '". $list['sms_mode'] . "',
				sms_memNo			= '". $list['sms_memNo'] . "',
				sms_logNo			= '". $sms_logNo . "',
				sms_name			= '". $list['sms_name'] . "',
				sms_phoneNumber		= '". trim($list['sms_phoneNumber']) . "',
				sms_phoneNumberApi	= '". trim($this->setPhoneNumberApi($list['sms_phoneNumber'])) . "',
				sms_status			= 'r',
				sms_send_status		= 'n',
				sms_regdt			= now()
		");
	}

	/*
	 * phonenumber setting
	 * @param string
	 * @return string
	 * @date 2014-11-20
	 */
	function setPhoneNumberApi($phoneNumber)
	{
		return str_replace("-", "", $phoneNumber);
	}

	/*
	 * sms send mode
	 * @param int
	 * @return string
	 * @date 2014-11-20
	 */
	function getSms_mode($reserve)
	{
		if($reserve == 1){
			//예약발송
			return 'r';
		}
		else{
			//즉시발송
			return 'i';
		}
	}

	/*
	 * get member info
	 * @param string phonenumber
	 * @return array name, m_no
	 * @date 2014-11-20
	 */
	function getMember($phoneNumber)
	{
		global $db;

		list($m_no, $name) = $db->fetch("SELECT m_no, name FROM " . GD_MEMBER . " WHERE mobile = '" . $phoneNumber . "' LIMIT 1");
		if(!$m_no && !$name){
			list($name) = $db->fetch("SELECT sms_name FROM " . GD_SMS_ADDRESS . " WHERE sms_mobile = '" . $phoneNumber . "' ORDER BY sno desc LIMIT 1");
		}
		
		return array($name, $m_no);
	}


	/*
	 * sendlist 접수여부 update
	 * @param string sms log 번호, 폰번호
	 * @return void
	 * @date 2014-11-20
	 */
	function updateSendStatus($sms_logNo, $phoneNumber)
	{
		$phoneNumberArr = explode(",", $phoneNumber);
		foreach($phoneNumberArr as $sms_phoneNumber){
			$GLOBALS[db]->query("
				UPDATE " . GD_SMS_SENDLIST . " SET 
					sms_send_status = 'y' 
				WHERE 
					sms_logNo = '".$sms_logNo."' and 
					sms_phoneNumber = '" . $sms_phoneNumber . "' and 
					sms_send_status = 'n' 
				LIMIT 1");
		}
	}

	/*
	 * sendlist sms_status
	 * @param string  접속상태, 발송상태, 즉시 or 예약, 예약시간
	 * @return string  c - 예약취소, r - 결과수신대기, y - 발송성공, n - 발송실패
	 * 즉시발송 step r -> y or n
	 * 예약발송 step r -> y or n or c
	 * @date 2014-11-20
	 */
	function getSendListStatus($acceptStatus, $sendStatus, $reserveType, $reserveDate)
	{
		if($acceptStatus == 'y'){
			switch($sendStatus){
				case 'c':
					return '예약취소';
				break;

				case 'r':
					if($reserveType == 'r'){
						if(strtotime($reserveDate) < strtotime('now')){
							return '결과수신대기';
						}
						else {
							return '발송대기';	
						}
					}
					else {
						return '결과수신대기';
					}
				break;

				case 'y':
					return '발송성공';
				break;

				case 'n':
					return '발송실패';
				break;
			}
		}
		else {
			return '발송요청실패';
		}
	}

	/*
	 * 실패 에러코드 리스트
	 * @param
	 * @return array
	 * @date 2014-11-20
	 */
	function errorCodeList()
	{
		$smsErrorCode = array(
			'1'=>'전송시간 초과',
			'2'=>'음영지역',
			'3'=>'전원 꺼짐',
			'4'=>'잘못된 전화번호',
			'5'=>'일시 서비스 정지',
			'6'=>'기타 단말기 문제',
			'7'=>'이통사 및 통신 문제',
			'8'=>'핸드폰 호 처리 중',
			'9'=>'서비스의 일시적인 에러',
			'10'=>'중복된 수신번호',
			'11'=>'발송지연으로 인한 실패',
			'12'=>'기타'
		);
		return $smsErrorCode;
	}

	/*
	 * sms log 접수실패여부 update
	 * @param void
	 * @return void
	 * @date 2014-11-20
	 */
	function updateSmsLogAcceptFail($smsLogInsertId)
	{
		global $db;

		list($cnt) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_logNo = '" . $smsLogInsertId . "' and sms_send_status = 'n'");
		if($cnt > 0){
			$db->query("UPDATE " . GD_SMS_LOG . " SET accept_fail_check = 'y' WHERE sno = '" . $smsLogInsertId . "' and accept_fail_check = 'n'");
		}
		else {
			$db->query("UPDATE " . GD_SMS_LOG . " SET accept_fail_check = 'n' WHERE sno = '" . $smsLogInsertId . "' and accept_fail_check = 'y'");
		}
	}

	/*
	 * sms log 발송실패여부 update
	 * @param void
	 * @return void
	 * @date 2014-11-20
	 */
	function updateSmsLogSendFail($smsLogInsertId)
	{
		global $db;

		list($cnt) = $db->fetch("SELECT COUNT(*) FROM " . GD_SMS_SENDLIST . " WHERE sms_logNo = '" . $smsLogInsertId . "' and sms_status = 'n'");
		if($cnt > 0){
			$db->query("UPDATE " . GD_SMS_LOG . " SET send_fail_check = 'y' WHERE sno = '" . $smsLogInsertId . "' and send_fail_check = 'n'");
		}
	}

	/*
	 * get failcode from sms_faillist
	 * @param int  (sms faillist sno)
	 * @return string
	 * @date 2014-11-20
	 */
	function getFailList_FailCode($sno)
	{
		list($failCode) = $GLOBALS['db']->fetch(" SELECT failCode FROM " . GD_SMS_FAILLIST . " WHERE sno = '" . $sno . "' LIMIT 1");

		return $failCode;
	}

	/**
	 * update status (reserve type)
	 * @param  void
	 * @return void
	 * @date 2014-11-20
	 */
	function updateReserveSendingAll()
	{
		global $db;

		if(preg_match('/sms.sendList.php/', $GLOBALS['_SERVER']['SCRIPT_NAME'])){
			$where = "reservedt < now() and reservedt != '0000-00-00 00:00:00' and reservedt != '' and status = '1'";

			//발송대기중인 예약발송건 확인
			list($updateCnt) = $db->fetch("SELECT COUNT(*) FROM ".GD_SMS_LOG." WHERE $where");
			if($updateCnt > 0){
				$GLOBALS['db']->query("UPDATE ".GD_SMS_LOG." SET status = '3' WHERE $where");
			}
		}
	}

	/**
	 * get faillist phonenumber
	 * @param string  (faillist sno)
	 * @return array
	 * @date 2014-11-20
	 */
	function getSmsFailListNumber($failListSno)
	{
		global $db;

		$_failListSnoArr = explode("|", $failListSno);
		$failListSnoArr = array_chunk($_failListSnoArr, 1000);
		foreach($failListSnoArr as $v){
			$where = implode("','", $v);
			$result = $db->query("SELECT phoneNumber FROM " . GD_SMS_FAILLIST . " WHERE sno IN( '" . $where . "') ");
			while($row = $db->fetch($result, 1)){
				$failListResult[] = $row['phoneNumber'];
			}
		}
		return $failListResult;
	}

	/*
	 * 예약발송건 중복확인
	 * @param string 날짜, 제목, 메시지
	 * @return int
	 * @date 2014-11-20
	 */
	function checkOverlapReserve($type, $date, $_msg, $subject='')
	{
		if(is_array($_msg)){
			$msg = implode("", $_msg);
		}
		else {
			$msg = $_msg;
		}
		
		if($type == 'lms'){
			$where = " and subject = '".$subject."'";
		}
		list($overLapCnt) = $GLOBALS['db']->fetch("SELECT COUNT(*) FROM " . GD_SMS_LOG . " WHERE reservedt = '".date("Y-m-d H:i:s", $date)."' and msg='". $msg . "' " . $where);

		return $overLapCnt;
	}
}
?>