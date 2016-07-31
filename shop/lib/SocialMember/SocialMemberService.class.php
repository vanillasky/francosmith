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
 * workingparksee    2014.04.21        First Draft.
 */

/**
 * Description of SocialLoginService
 *
 * @author SocialMemberService.class.php workingparksee
 * @version 1.0
 * @date 2014-04-21, 2014-04-21
 */
class SocialMemberService
{

	const FACEBOOK = 'FACEBOOK';

	public static function getMember($serviceCode = null)
	{
		if ($serviceCode === self::FACEBOOK) {
			return new FacebookMember();
		}
		else if ($serviceCode === null) {
			$db = Core::loader('db');
			list($SOCIAL_CODE) = $db->fetch('SELECT social_code FROM '.GD_SNS_MEMBER.' WHERE m_no='.$_SESSION['sess']['m_no'].' LIMIT 1');
			if (strlen($SOCIAL_CODE) > 0) {
				return self::getMember($SOCIAL_CODE);
			}
			else {
				return null;
			}
		}
		else {
			return null;
		}
	}

	public static function getServiceName($serviceCode)
	{
		switch ($serviceCode) {
			case self::FACEBOOK:
				return 'ÆäÀÌ½ººÏ';
				break;
			default:
				return false;
		}
	}

	public static function existsMember(SocialMember $socialMemeber)
	{
		$db = Core::loader('db');
		list($memberNo) = $db->fetch('SELECT m_no FROM '.GD_MEMBER.' WHERE m_no='.$socialMemeber->getMemberNo());
		return (int)$memberNo > 0;
	}

	public static function isConnectedSocialMember($socialCode, $memberNo)
	{
		$db = Core::loader('db');
		list($identifier) = $db->fetch('SELECT identifier FROM '.GD_SNS_MEMBER.' WHERE social_code="'.$socialCode.'" AND m_no="'.$memberNo.'"');
		return strlen($identifier) > 0;
	}

	public static function updateIdentifierIfChanged(SocialMember $socialMemeber)
	{
		if (strlen($socialMemeber->getEmail()) < 1) return false;
		$db = Core::loader('db');
		list($memberNo) = $db->fetch('SELECT m_no FROM '.GD_MEMBER.' WHERE email="'.$socialMemeber->getEmail().'"');
		list($identifier) = $db->fetch('SELECT identifier FROM '.GD_SNS_MEMBER.' WHERE social_code="'.$socialMemeber->getCode().'" AND m_no='.$memberNo);		
		if (strlen($identifier) < 1) {
			return false;
		}
		else if ($socialMemeber->getIdentifier() === $identifier) {
			return false;
		}
		else {
			$socialMemeber->updateIdentifier($memberNo);
			return true;
		}
	}

	public static function checkSkinPatch()
	{
		$config = Core::loader('config');
		$shopConfig = $config->load('config');
		$file1 = file_exists(dirname(__FILE__).'/../../data/skin/'.$shopConfig['tplSkin'].'/member/join_type.htm');
		$file2 = file_exists(dirname(__FILE__).'/../../data/skin/'.$shopConfig['tplSkin'].'/member/social_member_join.htm');
		$file3 = file_exists(dirname(__FILE__).'/../../data/skin/'.$shopConfig['tplSkin'].'/member/confirm_social_member.htm');
		$file4 = file_exists(dirname(__FILE__).'/../../data/skin/'.$shopConfig['tplSkin'].'/proc/member_social_status.htm');
		return $file1 && $file2 && $file3 && $file4;
	}

	public static function setPersistentData($name, $value)
	{
		$_SESSION['social_member_service_'.$name] = $value;
	}

	public static function getPersistentData($name)
	{
		return $_SESSION['social_member_service_'.$name];
	}

	public static function expirePersistentData($name)
	{
		unset($_SESSION['social_member_service_'.$name]);
	}

	private $_isEnabled = false;
	private $_enabledServiceList = array();

	public function __construct()
	{
		$facebookConfig = $this->loadFacebookConfig();
		if ($facebookConfig['useyn'] === 'y') {
			$this->_enabledServiceList[] = self::FACEBOOK;
		}

		$this->_isEnabled = (count($this->_enabledServiceList) > 0);
	}

	public function isEnabled()
	{
		return SocialMemberService::checkSkinPatch() && $this->_isEnabled;
	}

	public function getEnabledServiceList()
	{
		return $this->_enabledServiceList;
	}

	public function saveFacebookConfig($facebookConfig)
	{
		file_put_contents(dirname(__FILE__).'/../../conf/socialMember.facebook.php', serialize($facebookConfig), LOCK_EX);
	}

	public function loadFacebookConfig()
	{
		$configFile = file_get_contents(dirname(__FILE__).'/../../conf/socialMember.facebook.php');
		$facebookMember = unserialize($configFile);
		if (strlen($configFile) > 0 && $facebookMember) {
			return $facebookMember;
		}
		else {
			return array(
			    'useyn' => 'n',
			    'useAdvanced' => 'n',
			);
		}
	}

}
