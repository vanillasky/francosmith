<?php
include '../lib.php';
include '../../lib/sms_sendlist.class.php';
$sms_sendlist = new sms_sendlist();


if($_POST['mode'] == 'select'){
	if($_POST['m_no']){
		$_m_noArr = explode("|", $_POST['m_no']);
		$m_noArr = array_filter($_m_noArr);
		sort($m_noArr);
		
		$_query	= "SELECT mobile FROM " . GD_MEMBER . " WHERE m_no in ('".implode("','", $m_noArr)."') ";
	}
}
else if ($_POST['mode'] == 'query'){
	if($_POST['query']){
		$_POST['query'] =  (get_magic_quotes_gpc()) ? stripslashes($_POST['query']) : $_POST['query'];
		$_query = preg_replace('/\*/', 'mobile', $_POST['query']);
	}
}

$resCnt = $db->query($_query);
while($row = $db->fetch($resCnt, 1)){
	$phoneNumberArr[] = $row['mobile'];
}
$failSnoArr = smsFailCheck('array', $phoneNumberArr);

//전송실패 갯수
$smsFailCnt = count($failSnoArr);

//sms faillist pk list
$smsFailSnoList = implode("|", $failSnoArr);

if($smsFailCnt == 1){
	$smsFailCode = $sms_sendlist->getFailList_failCode($smsFailSnoList);
	$smsErrorCode = $sms_sendlist->errorCodeList();
	$errorType = $smsErrorCode[$smsFailCode];
}

echo $smsFailCnt . ',' . $smsFailSnoList . ',' . $errorType;
exit;
?>