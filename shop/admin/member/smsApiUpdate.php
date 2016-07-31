<?php
include '../lib.php';
if(!is_file('../../lib/smsAPI.class.php')){
	echo 'fail|smsAPI.class.php 파일이 존재하지 않습니다.';
	exit;
}
include '../../lib/smsAPI.class.php';

if(!$_POST['mode'] || !$_POST['sms_logNo']){
	msg('ajax error');
	exit;
}

//sms_log 정보
$smsLog = $db->fetch("SELECT * FROM " . GD_SMS_LOG . " WHERE sno = '" . $_POST['sms_logNo'] . "' LIMIT 1");

$smsAPI = new smsAPI();
$apiStart = $smsAPI->apiStartCheck($smsLog['status'], $smsLog['reservedt']);

if($apiStart == true)
{
	$smsAPI->setDefaultData();
	$resultMsg = $smsAPI->getApiData();
	echo $resultMsg;
}
exit;
?>