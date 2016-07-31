<?php
/**
 * Copyright (c) 2015 GODO Co. Ltd
 * All right reserved.
 *
 * This software is the confidential and proprietary information of GODO Co., Ltd.
 * You shall not disclose such Confidential Information and shall use it only in accordance
 * with the terms of the license agreement  you entered into with GODO Co., Ltd
 *
 * Revision History
 * Author            Date              Description
 * ---------------   --------------    ------------------
 *										First Draft.
 * workingby         2015.12.23         performance upgrade
 */
if (class_exists('member_grp', false)) return;

class member_grp {
	var $ruleset = array();
	var $prevent = false;

	var $member = null;
	var $m_id = null;
	var $m_no = null;

	var $db = null;
	var $queue = null;
	var $work = null;

	var $rangeArray = array();
	var $groups = array();
	var $memberFigureInfo = array();
	var $logForce = false;
	var $infoMode = false;
	var $memberLoginCountTable = '';
	var $memberTempTargetTable = '';

	function _microtime()
	{
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}

	function member_grp()
	{
		$cfg = dirname(__FILE__).'/../conf/config.member_group.php';
		if (is_file($cfg)) {
			include($cfg);
			$this->ruleset = & $member_grp_ruleset;
		}
		else {
			$this->ruleset = array(
				'automaticFl' => '',
				'apprSystem' => '',
				'apprPointTitle' => '',
				'apprPointLabel' => '',
				'apprPointOrderPriceUnit' => '',
				'apprPointOrderPricePoint' => '',
				'apprPointOrderRepeatPoint' => '',
				'apprPointReviewRepeatPoint' => '',
				'apprPointLoginRepeatPoint' => '',
				'calcPeriodFl' => '',
				'calcPeriodBegin' => '',
				'calcPeriodMonth' => '',
				'calcCycleMonth' => '',
				'calcCycleDay' => '',
				'calcKeep' => ''
			);

			$this->prevent = true;
		}

		$this->now = time();

		//임시회원 타겟테이블
		$this->memberTempTargetTable = 'gd_log_member_target_' . $this->now;
		//로그인횟수 정보저장 테이블
		$this->memberLoginCountTable = 'gd_log_loginCount_' . $this->now;

		$this->db = & $GLOBALS['db'];

		$this->_i_am_updated();
	}

	function _i_am_updated()
	{
		$_tpl = & $GLOBALS['tpl'];

		if (!is_object($_tpl)) return;

		if ($_SESSION['sess']['m_no'] && ($log = $this->db->fetch("SELECT * FROM ".GD_MEMBER_GRP_CHANGED_LOG." WHERE m_no = ".$_SESSION['sess']['m_no']." AND notified = 0 AND current_level != previous_level",1)) !== false) {
			$_tpl->assign('useMyLevelLayerBox', 'y');
		}
		else {
			$_tpl->assign('useMyLevelLayerBox', 'n');
		}
	}

	function checkUpdate()
	{
		if ($this->ruleset['automaticFl'] != 'y' || $this->prevent === true) return;

		// 최종 갱신일
		$row = $this->db->fetch("SELECT MAX(last_work) AS last_work FROM ".GD_MEMBER_GRP_SCHEDULE."",1);

		if (! $row['last_work']) {
			return $this->_setWork();
		}

		// 다음 갱신년월일. (해당 월에 일이 없으면 그냥 말일로)
		//*/
		$last_work = strtotime($row['last_work']);

		$_t = strtotime('+'.$this->ruleset['calcCycleMonth'].' month' ,$last_work);
		$_d = date('t' ,$_t);
		$next_work = strtotime(date('Y-m',$_t).'-'.($this->ruleset['calcCycleDay'] > $_d ? $_d : $this->ruleset['calcCycleDay']));
		/*
		// 이 코드가 0.0001 초 정도 더 빠름...
		$date = new DateTime($row['last_work']);
		$date->modify('+'.$this->ruleset['calcCycleMonth'].' month');
		$_y =  $date->format('Y');
		$_m =  $date->format('m');
		$_t =  $date->format('t');
		$date->setDate( $_y , $_m , ($this->ruleset['calcCycleDay'] > $_t ? $_t : $this->ruleset['calcCycleDay']) );
		$next_work = strtotime($date->format('Y-m-d'));
		*/

		if ($this->now >= $next_work) {
			$this->_setWork();
		}
	}

	function execUpdate($force = false)
	{
		//로그기록용
		$this->logForce = $force;

		if ($this->prevent === true) return false;

		//수동평가
		if ($force === true) {
			$this->_setQueue();

			if($this->_queueReady() === true){
				if (($this->work = $this->db->fetch("SELECT * FROM ".GD_MEMBER_GRP_SCHEDULE." WHERE excuted = 0",1)) === false) {
					$this->work['last_work'] = date('Y-m-d H:i:s',$this->now);	// 수동 평가 or 강제 실행시 지난 갱신일을 현재로 설정.
				}

				$this->_go();

				return true;
			}
		}

		if (($this->work = $this->db->fetch("SELECT * FROM ".GD_MEMBER_GRP_SCHEDULE." WHERE excuted = 0",1)) === false) return;	// 2개가 밀릴수도 있을까나..

		//자동평가
		if($this->ruleset['automaticFl'] == 'y'){
			$this->_setQueue();

			if ($this->_queueReady() === true) {
				$this->_go();
			}
		}

		if ($this->work) {
			$this->db->query("UPDATE ".GD_MEMBER_GRP_SCHEDULE." SET excuted = 1 WHERE sno = '".$this->work['sno']."'");
		}

		return true;
	}

	function _queueReady()
	{
		$query = "SELECT COUNT(*) FROM ".$this->memberTempTargetTable;
		list($count) = $this->db->fetch($query);

		if($count > 0){
			return true;
		}
		return false;
	}

	function _getQueueQuery()
	{
		return "SELECT * FROM " . $this->memberTempTargetTable;
	}

	function _setQueue()
	{
		// 메모리 임계치 재조정
		@ini_set('memory_limit', -1);
		@set_time_limit(0);

		$query = "
			CREATE TABLE IF NOT EXISTS ".$this->memberTempTargetTable."
				SELECT
					MB.m_no, MB.m_id, MB.level, MB.regdt, LOG.last_level_updated
				FROM
					".GD_MEMBER." AS MB
				LEFT JOIN
					".GD_MEMBER_GRP_CHANGED_LOG." AS LOG ON MB.m_no = LOG.m_no
				WHERE
					MB.level < 80 AND MB." . MEMBER_DEFAULT_WHERE;
		$this->db->query($query);
	}

	function _setWork()
	{
		$query = "
		INSERT INTO ".GD_MEMBER_GRP_SCHEDULE." SET
			`sno`			= '',
			`last_work`		= CURDATE(),
			`affected_rows` = 0,
			`excuted`		= 0
		";
		$this->db->query($query);
	}

	function _go()
	{
		//등급평가 처리 후 처리할 과정
		register_shutdown_function(array($this, 'shutdownProcess'));

		//산출기간 셋팅
		$this->_getCalcRange();

		//그룹 셋팅
		$this->_setGroups();

		//구매총액, 구매횟수, 후기 작성횟수 셋팅
		$this->_setMemberFigure();

		//회원 로그인 정보 셋팅
		if($this->ruleset['apprSystem'] === 'point'){
			$this->_setMemberLoginCount();
		}

		$query = $this->_getQueueQuery();
		$res = $this->db->query($query);
		while($mb = $this->db->fetch($res, 1)){
			//유지기간 체크
			if($this->_check_calculate_keep($mb) === true){
				if ($lv = $this->_get_level($mb)) {
					$mb['previous_level'] = $mb['level'];
					$mb['current_level'] = $lv;
					$this->_update($mb);
				}
			}
		}
	}

	function _get_level($mb = false)
	{
		$this->m_no = $this->m_id = null;

		if(is_array($mb)) {
			$this->m_no = $mb['m_no'];
			$this->m_id = $mb['m_id'];
		}
		else if(is_numeric($mb)) {	// m_no
			$this->m_no = $mb;
			$this->m_id = $this->_get_memberinfo('m_id');
		}
		else if(is_string($mb)) {	// m_id
			$this->m_id = $mb;
			$this->m_no = $this->_get_memberinfo('m_no');
		}
		else {
			return false;
		}

		if ($this->ruleset['apprSystem'] === 'point') {
			// 실적 점수제
			$point_or_figure = $this->_getMemberPoint();				//PC용
			$mobile_point_or_figure = $this->_getMobileMemberPoint();	//모바일용
		}
		else {
			// 실적 수치제
			$point_or_figure = $this->_getMemberFigure();					//PC용
			$mobile_point_or_figure = $this->_getMobileMemberFigure();	//모바일용
		}

		return $this->_getMemberGroup($point_or_figure, $mobile_point_or_figure);
	}

	function _check_calculate_keep($mb)
	{
		$checkDate = $mb['last_level_updated'] ? strtotime($mb['last_level_updated']) : strtotime(substr($mb['regdt'], 0, 10));
		$keepDate = strtotime('+'.$this->ruleset['calcKeep'].' month', $checkDate);

		if ((int)$this->ruleset['calcKeep']==0 || $this->now > $keepDate) {
			return true;
		}

		return false;
	}

	function _update($mb)
	{
		if($mb['previous_level'] != $mb['current_level']){
			$query = "
			UPDATE ".GD_MEMBER." SET
				level = '".$mb['current_level']."'
			WHERE m_no = '".$mb['m_no']."'
			";

			$this->db->query($query);
		}

		// 로그 기록
		$this->_log($mb);
	}

	function _log($mb)
	{
		list($m_no) = $this->db->fetch("SELECT m_no FROM ".GD_MEMBER_GRP_CHANGED_LOG." WHERE m_no = '".$mb['m_no']."'");
		if ($m_no) {
			$query = "
			UPDATE ".GD_MEMBER_GRP_CHANGED_LOG." SET
				current_level = '".$mb['current_level']."',
				previous_level = '".$mb['previous_level']."',
				last_level_updated = CURDATE(),
				notified = '0'
			WHERE m_no = '".$mb['m_no']."'
			";
		}
		else {
			$query = "
			INSERT INTO ".GD_MEMBER_GRP_CHANGED_LOG." SET
				m_no = '".$mb['m_no']."',
				current_level = '".$mb['current_level']."',
				previous_level = '".$mb['previous_level']."',
				last_level_updated = CURDATE(),
				notified = '0'
			";
		}
		$this->db->query($query);
	}

	function _get_memberinfo($key)
	{
		if (! $this->member) {
			$this->_get_member();
		}

		return ($this->member) ? $this->member[$key] : false;
	}

	function _get_member()
	{
		$query = "SELECT * FROM ".GD_MEMBER." WHERE " . MEMBER_DEFAULT_WHERE;

		if ($this->m_id !== null) $query .= " AND m_id = '".$this->m_id."'";
		elseif ($this->m_no !== null) $query .= " AND m_no = '".$this->m_no."'";
		else return false;

		$this->member = $this->db->fetch($query,1);
		return ($this->member) ? true : false;
	}

	function _getMemberGroup($point_or_figure, $mobile_point_or_figure=0)
	{
		if ($this->ruleset['apprSystem'] === 'point') {
			// 실적 점수제
			//PC용 포인트와 모바일용 포인트 비교하여 높은 포인트로 회원 그룹 산정
			$point_or_figure = ((int)$mobile_point_or_figure > (int)$point_or_figure) ? $mobile_point_or_figure : $point_or_figure;

			foreach ($this->groups as $group) {
				if ((int)$point_or_figure >= (int)$group['by_score_limit'] && (int)$point_or_figure  < (int)$group['by_score_max']) return $group['level'];
			}
		}
		else {
			// 실적 수치제 (머 몇번이상, 머는 몇번 이상 등)
			foreach ($this->groups as $group) {
				if($point_or_figure) {
					if (((int)$point_or_figure['OrderPrice'] >= (int)$group['by_number_buy_limit'] && (int)$point_or_figure['OrderPrice'] < (int)$group['by_number_buy_max']) && (int)$point_or_figure['ReviewCount'] >= (int)$group['by_number_review_require'] && (int)$point_or_figure['OrderCount'] >= (int)$group['by_number_order_require']) $pc_level = $group['level'];
				}
				if($mobile_point_or_figure) {
					if (((int)$mobile_point_or_figure['OrderPrice'] >= (int)$group['mobile_by_number_buy_limit'] && (int)$mobile_point_or_figure['OrderPrice'] < (int)$group['mobile_by_number_buy_max']) && (int)$mobile_point_or_figure['ReviewCount'] >= (int)$group['mobile_by_number_review_require'] && (int)$mobile_point_or_figure['OrderCount'] >= (int)$group['mobile_by_number_order_require'])
					$mobile_level = $group['level'];
				}

				$level = ($mobile_level > $pc_level) ? $mobile_level : $pc_level;

				if($level) return $level;
			}

		}

		return 1;	// 일반회원
	}

	function _getMemberFigure()
	{
		$returnFigureInfo = $this->memberFigureInfo[$this->m_no];
		$this->memberFigureInfo[$this->m_no] = null;

		return $returnFigureInfo;
	}

	function _getMemberPoint()
	{
		$point = 0;
		$point += ($this->ruleset['apprPointOrderPrice'])	? ($this->memberFigureInfo[$this->m_no]['OrderPrice'] * $this->ruleset['apprPointOrderPricePoint'] / $this->ruleset['apprPointOrderPriceUnit']) : 0;
		$point += ($this->ruleset['apprPointOrderRepeat'])	? ($this->memberFigureInfo[$this->m_no]['OrderCount'] * $this->ruleset['apprPointOrderRepeatPoint']) : 0;
		$point += ($this->ruleset['apprPointReviewRepeat'])	? ($this->memberFigureInfo[$this->m_no]['ReviewCount'] * $this->ruleset['apprPointReviewRepeatPoint']) : 0;
		if($this->ruleset['apprPointLoginRepeat']){
			$logcnt = 0;
			$logcnt = $this->_getMemberLoginCount('pc');
			$point += ($logcnt * $this->ruleset['apprPointLoginRepeatPoint']);
		}

		return floor($point);
	}

	function _getCalcRange()
	{
		switch($this->ruleset['calcPeriodBegin']) {
			case '-1d':
				$str_time = '-1 day';
				break;
			case '-1w':
				$str_time = '-1 week';
				break;
			case '-2w':
				$str_time = '-2 week';
				break;
			case '-1m':
				$str_time = '-1 month';
				break;
		}

		$_time1 = strtotime($str_time, strtotime($this->work['last_work']) ); // 시작
		$_time2 = strtotime('-'.$this->ruleset['calcPeriodMonth'].' month', $_time1 ); // 끝

		$this->rangeArray = array(
			'unlimited' => ($this->ruleset['calcPeriodFl'] == 'y' ? false : true),
			'begin' => date('Y-m-d', $_time2) . ' 00:00:00',
			'end' => date('Y-m-d', $_time1) . ' 23:59:59'
		);
	}

	function _getMobileMemberPoint()
	{
		$point = 0;
		$point += ($this->ruleset['apprPointOrderPrice'])	? (floor($this->memberFigureInfo[$this->m_no]['mobileOrderPrice'] / $this->ruleset['mobile_apprPointOrderPriceUnit']) * $this->ruleset['mobile_apprPointOrderPricePoint']) : 0;
		$point += ($this->ruleset['apprPointOrderRepeat'])	? ($this->memberFigureInfo[$this->m_no]['mobileOrderCount'] * $this->ruleset['mobile_apprPointOrderRepeatPoint']) : 0;
		$point += ($this->ruleset['apprPointReviewRepeat'])	? ($this->memberFigureInfo[$this->m_no]['mobileReviewCount'] * $this->ruleset['mobile_apprPointReviewRepeatPoint']) : 0;
		if($this->ruleset['apprPointLoginRepeat']){
			$logcnt = 0;
			$logcnt = $this->_getMemberLoginCount('mobile');
			$point += ($logcnt * $this->ruleset['mobile_apprPointLoginRepeatPoint']);
		}

		return floor($point);
	}

	function _getMobileMemberFigure()
	{
		$returnArray = array(
			'OrderPrice' => $this->memberFigureInfo[$this->m_no]['mobileOrderPrice'],
			'OrderCount'=> $this->memberFigureInfo[$this->m_no]['mobileOrderCount'],
			'ReviewCount'=> $this->memberFigureInfo[$this->m_no]['mobileReviewCount'],
		);

		return $returnArray;
	}

	function _getMemberLoginCount($type)
	{
		$count = 0;
		$queryAdd = '';

		if($type == 'mobile'){
			$queryAdd = " AND type='".$type."' ";
		}
		$query = "SELECT count FROM ".$this->memberLoginCountTable." WHERE m_id='".$this->m_id."' ".$queryAdd." LIMIT 1";
		list($count) = $this->db->fetch($query);

		return $count;
	}

	function _get_report($m_no)
	{
		$this->infoMode = true;

		//등급평가 처리 후 처리할 과정
		register_shutdown_function(array($this, 'shutdownProcess'));

		$this->m_no = $m_no;
		$this->m_id = $this->_get_memberinfo('m_id');

		$this->work['last_work'] = date('Y-m-d H:i:s',$this->now);

		//산출기간 셋팅
		$this->_getCalcRange();

		//구매총액, 구매횟수, 후기 작성횟수 셋팅
		$this->_setMemberFigure();

		$data = array();
		if ($this->ruleset['apprSystem'] === 'point') { // 실적 점수제
			//로그인 횟수 셋팅
			$this->_setMemberLoginCount();

			$point_or_figure = $this->_getMemberPoint();				//PC용
			$mobile_point_or_figure = $this->_getMobileMemberPoint();	//모바일용
		}
		else {
			// 실적 수치제 (머 몇번이상, 머는 몇번 이상 등)
			$point_or_figure = $this->_getMemberFigure();					//PC용
			$mobile_point_or_figure = $this->_getMobileMemberFigure();	//모바일용
		}

		$data[type] = $this->ruleset['apprSystem'];
		$data[pc] = $point_or_figure;
		$data[mobile] = $mobile_point_or_figure;

		return $data;
	}

	/**
	 * 로그인횟수 정보 셋팅
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return void
	 * @date 2015-12-23
	 */
	function _setMemberLoginCount()
	{
		$memberLoginCountArray = array();

		//로그인횟수 사용시
		if($this->ruleset['apprPointLoginRepeat']){
			//임시테이블 생성
			$this->_createMemberloginTable();

			$beg_time = $end_time = $beginFullTime = $endFullTime = null;
			if ($this->rangeArray['unlimited'] === false) {
				$beginFullTime = strtotime($this->rangeArray['begin']);
				$endFullTime = strtotime($this->rangeArray['end']);
				$beg_time = date("Ym", $beginFullTime);
				$end_time = date("Ym", $endFullTime);
			}

			$log_path = dirname(__FILE__).'/../log/';
			$fl = scandir($log_path);
			foreach ($fl as $filename) {
				$pcLog = $mobileLog = false;

				if (preg_match('/^login_([0-9]{6})\.log$/', $filename, $matches)) {
					$pcLog = true;
				}
				if(preg_match('/^mobile_login_([0-9]{6})\.log$/', $filename, $mobile_matches)){
					$mobileLog = true;
				}

				if($pcLog === true || $mobileLog === true){
					if (!is_null($beg_time) && !is_null($end_time)) {
						$log_time = ($pcLog === true) ? $matches[1] : $mobile_matches[1];
						if($log_time < $beg_time || $log_time > $end_time){
							continue;
						}
					}

					$loginCountMode = ($pcLog === true) ? 'pc' : 'mobile';
					$log = file($log_path.$filename);
					if(count($log) > 0){
						foreach($log as $value){
							$logDateTime = '';
							$loginInfo = array();
							$loginInfo = explode("\t", $value);

							if (!is_null($beginFullTime) && !is_null($endFullTime)) {
								$logDateTime = strtotime($loginInfo[0]);
								if($logDateTime < $beginFullTime || $logDateTime > $endFullTime){
									continue;
								}
							}
							$memberLoginCountArray[$loginCountMode][trim($loginInfo[2])] += 1;
						}
					}
				}
			}

			if(count($memberLoginCountArray['pc']) > 0) $this->_insertMemberLoginCount('pc', $memberLoginCountArray['pc']);
			if(count($memberLoginCountArray['mobile']) > 0) $this->_insertMemberLoginCount('mobile', $memberLoginCountArray['mobile']);
			$memberLoginCountArray = array();
		}
	}

	/**
	 * login 기록될 테이블 생성
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return void
	 * @date 2015-12-23
	 */
	function _createMemberloginTable()
	{
		$query = "
			CREATE TABLE IF NOT EXISTS ".$this->memberLoginCountTable." (
				`m_id` varchar(20) NOT NULL,
				`count` int(11) UNSIGNED NOT NULL DEFAULT 0,
				`type` varchar(6) NOT NULL,
			INDEX idx_select (`m_id`, `type`));
		";
		$this->db->query($query);
	}

	/**
	 * login 기록 DB 삽입
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $type, array $_dataArray
	 * @return void
	 * @date 2015-12-23
	 */
	function _insertMemberLoginCount($type, $_dataArray)
	{
		$dataArray = array();
		$dataArray = @array_chunk($_dataArray, 100, true);

		$query = "INSERT INTO " . $this->memberLoginCountTable . " (m_id, count, type) VALUES ";
		foreach($dataArray as $dataValues){
			$queryArray = array();
			$lastInsertQuery = '';

			foreach($dataValues as $key => $value){
				$queryArray[] = "('".$key."', '".$value."', '".$type."')";
			}
			$lastInsertQuery = $query . implode(", ", $queryArray);

			if($lastInsertQuery){
				$this->db->query($lastInsertQuery);
			}
		}
	}

	/**
	 * 회원 등급 셋팅
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return void
	 * @date 2015-12-23
	 */
	function _setGroups()
	{
		$query = "
		SELECT
			*
		FROM ".GD_MEMBER_GRP." AS GRP
		INNER JOIN ".GD_MEMBER_GRP_RULESET." AS RULE
		ON GRP.sno = RULE.sno
		WHERE
			GRP.level < 80
		ORDER BY GRP.level DESC
		";
		$rs = $this->db->query($query);

		while ($row = $this->db->fetch($rs,1)) {
			$this->groups[] = $row;
		}
	}

	/**
	 * 클래스 사용 후 처리
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return void
	 * @date 2015-12-23
	 */
	function shutdownProcess()
	{
		$this->db->query("DROP TABLE IF EXISTS " . $this->memberTempTargetTable);
		$this->db->query("DROP TABLE IF EXISTS " . $this->memberLoginCountTable);
		$this->memberFigureInfo = array();

		if($this->infoMode === true){ //정보검색 모드
			return;
		}

		$logPath = dirname(__FILE__) . '/../log/memberGroup/';
		if(!file_exists($logPath)) {
			@mkdir($logPath, 0707);
			@chmod($logPath, 0707);
		}
		$logFile = $logPath . 'memberGroup_' . date('Ymd') . '.log';

		$logMessage = '';
		$logMessage = $this->_getLogMessage();

		@error_log($logMessage, 3, $logFile);
		@chmod($logFile, 0707);
		$this->_deleteGarbageLog($logPath);
	}

	/**
	 * 6개월 전 접속 로그 삭제
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param string $logPath
	 * @return void
	 * @date 2015-12-23
	 */
	function _deleteGarbageLog($logPath)
	{
		$searchPath = realpath($logPath) . '/' . 'memberGroup_' . date("Ym", strtotime("-6 month")) . '*';
		foreach(glob($searchPath) as $logFile){
			if(strpos($logFile, 'memberGroup_') !== false){
				@unlink($logFile);
			}
		}
	}

	/**
	 * log  내용 작성
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return string $resultLogMessage
	 * @date 2015-12-23
	 */
	function _getLogMessage()
	{
		global $_SESSION, $_SERVER;

		$resultLogMessage = '';
		$logMessage = array();
		$logMessage['IP'] = $_SERVER['REMOTE_ADDR'];
		$logMessage['ID'] = $_SESSION['sess']['m_id'];
		$logMessage['TYPE'] = ($this->logForce == true) ? '수동평가' : '자동평가';
		$logMessage['CONF'] = print_r($this->ruleset, true);
		$logMessage['STARTTIME'] = date("Y-m-d H:i:s", $this->now);
		$logMessage['ENDTIME'] = date("Y-m-d H:i:s");

		switch (connection_status()) {
			case CONNECTION_NORMAL: // Connection 정상인 경우
				$logMessage['CONNECT_ERROR'] = 'CONNECTION_OK [Connection 정상]';
			break;

			case CONNECTION_TIMEOUT: // Connection TIMEOUT 종료
				$logMessage['CONNECT_ERROR'] = 'CONNECTION_TIMEOUT [Connection TIMEOUT 종료]';
			break;

			case CONNECTION_ABORTED: // Connection Client 절단(Client 절단 수용한 경우)
				$logMessage['CONNECT_ERROR'] = 'CONNECTION_ABORTED [Connection Client 절단]';
			break;

			case (CONNECTION_ABORTED & CONNECTION_TIMEOUT): // Connection Client 절단되고 TIMEOUT 종료(Client 절단 무시한 경우)
				$logMessage['CONNECT_ERROR'] = 'CONNECTION_TIMEOUT_ABORTED [Connection Client 절단되고 TIMEOUT 종료]';
			break;

			default: // Connection 알려지지 않은 에러
				$logMessage['CONNECT_ERROR'] = 'CONNECTION_UNKNOWN_ERROR [알려지지 않은 에러]';
			break;
		}

		$resultLogMessage = PHP_EOL . '===== LOG START ====='. PHP_EOL;
		foreach($logMessage as $key => $value){
			$resultLogMessage .= $key . ' = ' . $value . PHP_EOL;
		}
		$resultLogMessage .= '===== LOG END ====='. PHP_EOL. PHP_EOL;

		return $resultLogMessage;
	}

	/**
	 * 실적수치제 셋팅
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param void
	 * @return void
	 * @date 2015-12-23
	 */
	function _setMemberFigure()
	{
		if ($this->ruleset['apprSystem'] === 'point') {
			//구매금액, 구매횟수 사용시
			if($this->ruleset['apprPointOrderPrice'] || $this->ruleset['apprPointOrderRepeat']){
				$this->_setOrderMemberFigure(); //pc
				$this->_setOrderMemberFigure(true); //mobile
			}
			//구매후기 사용시
			if($this->ruleset['apprPointReviewRepeat']){
				$this->_setReviewMemberFigure(); //pc
				$this->_setReviewMemberFigure(true); //mobile
			}
		}
		else if($this->ruleset['apprSystem'] === 'figure'){
			//실적수치제
			$this->_setOrderMemberFigure(); //pc
			$this->_setOrderMemberFigure(true); //mobile
			$this->_setReviewMemberFigure(); //pc
			$this->_setReviewMemberFigure(true); //mobile
		}
		else {

		}
	}

	/**
	 * 주문 횟수, 주문금액 셋팅
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param boolean $isMobile true-mobile, false-pc
	 * @return void
	 * @date 2015-12-23
	 */
	function _setOrderMemberFigure($isMobile=false)
	{
		$whereAdd = $groupBy = '';
		$variableName_price = 'OrderPrice';
		$variableName_count = 'OrderCount';

		if($isMobile === true) {
			$whereAdd = " AND mobilepay = 'y' ";
			$variableName_price = 'mobileOrderPrice';
			$variableName_count = 'mobileOrderCount';
		}
		if($this->rangeArray['unlimited'] === false) $whereAdd .= " AND orddt >= '".$this->rangeArray['begin']."' AND orddt <= '".$this->rangeArray['end']."' ";
		if($this->infoMode === true) {
			$whereAdd .= " AND m_no = '".$this->m_no."' ";
			$groupBy = '';
		}
		else {
			$groupBy = " GROUP BY m_no ";
		}

		$query = "
			SELECT
				SUM(prn_settleprice) AS OrderPrice,
				COUNT(ordno) AS OrderCount,
				m_no
			FROM
				".GD_ORDER."
			WHERE
				step = 4 AND step2 = 0 AND m_no > 0 " . $whereAdd . $groupBy;
		$result = $this->db->query($query);
		while($row = $this->db->fetch($result,1)){
			$this->memberFigureInfo[$row['m_no']][$variableName_price] = $row['OrderPrice'];
			$this->memberFigureInfo[$row['m_no']][$variableName_count] = $row['OrderCount'];
		}
	}

	/**
	 * review 작성 횟수 셋팅
	 * @author workingby <bumyul2000@godo.co.kr>
	 * @param boolean $isMobile true-mobile, false-pc
	 * @return void
	 * @date 2015-12-23
	 */
	function _setReviewMemberFigure($isMobile=false)
	{
		$whereAdd = $groupBy = '';
		$variableName_count = 'ReviewCount';
		if($isMobile === true) {
			$whereAdd = " AND is_mobile = 'y' ";
			$variableName_count = 'mobileReviewCount';
		}
		if ($this->rangeArray['unlimited'] === false) $whereAdd .= " AND regdt >= '".$this->rangeArray['begin']."' AND regdt <= '".$this->rangeArray['end']."' ";
		if($this->infoMode === true) {
			$whereAdd .= " AND m_no = '".$this->m_no."' ";
			$groupBy = '';
		}
		else {
			$groupBy = " GROUP BY m_no ";
		}

		$query = "
			SELECT
				COUNT(sno) AS ReviewCount,
				m_no
			FROM
				".GD_GOODS_REVIEW."
			WHERE
				(1) " . $whereAdd . $groupBy;

		$result = $this->db->query($query);
		while($row = $this->db->fetch($result,1)){
			$this->memberFigureInfo[$row['m_no']][$variableName_count] = $row['ReviewCount'];
		}
	}
}
?>