<?php

/**
 * Copyright (c) 2014 GODO Co. Ltd
 * All right reserved.
 *
 * This software is the confidential and proprietary information of GODO Co., Ltd.
 * You shall not disclose such Confidential Information and shall use it only in accordance
 * with the terms of the license agreement  you entered into with GODO Co., Ltd
 *
 * Revision History
 * Author            Date              Description
 * ---------------   --------------    ------------------
 * workingparksee    2014.05.12        First Draft.
 */

/**
 * Description of SocialMember
 *
 * @author SocialMember.class.php workingparksee
 * @version 1.0
 * @date 2014-05-12, 2014-05-12
 */
abstract class SocialMember
{

	protected $_identifier, $_hasError = false, $_memberNo;

	private $_code;

	public function __construct($code)
	{
		$this->_code = $code;
		if ($_SESSION['sess']['m_no']) {
			$db = Core::loader('db');
			list($identifier) = $db->fetch('SELECT identifier FROM '.GD_SNS_MEMBER.' WHERE social_code="'.$this->_code.'" AND m_no="'.$_SESSION['sess']['m_no'].'"');
			$this->_identifier = $identifier;
		}
	}

	public function getCode()
	{
		return $this->_code;
	}

	public function getMemberNo()
	{
		if ($this->_memberNo) {
			return $this->_memberNo;
		}
		else {
			$db = Core::loader('db');
			list($snsMemberNo) = $db->fetch('SELECT m_no FROM '.GD_SNS_MEMBER.' WHERE social_code="'.$this->_code.'" AND identifier="'.$this->getIdentifier().'"');
			return (int)$snsMemberNo;
		}
	}

	public function saveExtraData($extraData)
	{
		$db = Core::loader('db');
		$query = $db->_query_print('UPDATE '.GD_SNS_MEMBER.' SET extra_data=[s] WHERE social_code="'.$this->_code.'" AND identifier="'.$this->getIdentifier().'"', array(
		    serialize($extraData)
		));
		$db->query($query);
	}

	public function loadExtraData()
	{
		$db = Core::loader('db');
		list($extraData) = $db->fetch('SELECT extra_data FROM '.GD_SNS_MEMBER.' WHERE social_code="'.$this->_code.'" AND identifier="'.$this->getIdentifier().'"');
		return unserialize($extraData);
	}

	public function isConnected()
	{
		$memberNo = $this->getMemberNo();
		return $memberNo > 0;
	}

	public function connect($memberNo)
	{
		$db = Core::loader('db');
		$db->query('UPDATE '.GD_MEMBER.' SET connected_sns = CONCAT_WS(",", IF(connected_sns = "", NULL, connected_sns), "'.$this->_code.'") WHERE m_no='.$memberNo);
		$query = $db->_query_print('INSERT INTO '.GD_SNS_MEMBER.' SET social_code = [s], identifier = [s], m_no = [i], regdt = [s]',
			$this->_code,
			$this->getIdentifier(),
			$memberNo,
			date('Y-m-d H:i:s'));
		$db->query($query);
	}

	public function disconnect($memberNo)
	{
		$db = Core::loader('db');
		$result1 = $db->query('UPDATE '.GD_MEMBER.' SET connected_sns = REPLACE(connected_sns, "'.$this->_code.'", "") WHERE m_no='.$memberNo);
		if (!$result1) {
			return $result1;
		}
		else {
			return $db->query('DELETE FROM '.GD_SNS_MEMBER.' WHERE social_code="'.$this->_code.'" AND identifier="'.$this->getIdentifier().'"');
		}
	}

	public function updateIdentifier($memberNo)
	{
		$db = Core::loader('db');
		$db->query('UPDATE '.GD_SNS_MEMBER.' SET identifier="'.$this->getIdentifier().'" WHERE social_code="'.$this->_code.'" AND m_no='.$memberNo);
	}

	public function login()
	{
		SocialMemberService::setPersistentData('social_code', $this->getCode());
		$session = Core::loader('session');
		$result = $session->socialLogin($this);
		if (function_exists('member_log')) {
			member_log($session->m_id);
		}
		return $result;
	}

	public function logout()
	{
		$session = Core::loader('session');
		$session->logout();
		SocialMemberService::expirePersistentData('user_identifier');
		SocialMemberService::expirePersistentData('social_code');
	}

	public function getCallbackURL($mode, $parameter = array())
	{
		$config = Core::loader('config');
		$shopConfig = $config->load('config');
		$scheme = $_SERVER['HTTPS'] ? 'https' : 'http';
		$port = (!$_SERVER['SERVER_PORT'] || $_SERVER['SERVER_PORT'] === '80') ? '' : ':'.$_SERVER['SERVER_PORT'];
		$callbackURL = $scheme.'://'.$_SERVER['SERVER_NAME'].$port.$shopConfig['rootDir'].'/member/social_member.php?MODE='.$mode.'&SOCIAL_CODE='.$this->getCode();
		if (count($parameter) > 0) {
			$callbackURL .= '&'.http_build_query($parameter);
		}
		return $callbackURL;
	}

	public function getIdentifier()
	{
		return $this->_identifier;
	}

	public function hasError()
	{
		return $this->_hasError;
	}

	abstract public function isSameMember();

	abstract public function getName();

	abstract public function getEmail();

	abstract public function getBirthday();

	abstract public function getLoginStatus();

	abstract public function getLoginURL($returnURL = null);

	abstract public function getLogoutURL($returnURL = null);

	abstract public function getConnectURL();

	abstract public function getDisconnectURL();

	abstract public function getConfirmMemberURL($sessionKey);

	abstract public function getProfileImageURL();

}

?>
