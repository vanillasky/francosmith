<?
header("content-type:text/xml");
include "../lib/library.php";
include_once dirname(__FILE__)."/../conf/config.php";
include_once dirname(__FILE__)."/../conf/naverCheckout.cfg.php";
$naverCheckoutAPI = Core::loader('naverCheckoutAPI');
include_once dirname(__FILE__)."/../lib/httpSock.class.php";

$naverCheckoutAPI->ncLog('onclick_status', "START");

// 변수 재정의
$arrData = array(
	'SelectType'	=> trim($_GET['SelectType']),	// 상태조회 : 1; 중복조회 : 2
	'NCMallID'		=> trim($_GET['NCMallID']),		// 네이버 가맹점 ID
	'MallUserID'	=> trim($_GET['MallUserID']),	// 가맹점 회원 아이디
	'MallUserNo'	=> trim($_GET['MallUserNo']),	// 가맹점 회원 고유번호
	'NCUserSSN'		=> trim($_GET['NCUserSSN']),	// 네이버 회원 주민등록번호(13자리)
	'NCUserName'	=> trim($_GET['NCUserName']),	// 네이버 회원 이름
	'Timestamp'		=> trim($_GET['Timestamp']),	// 서비스 요청 시각
);

// 변수 복호화
$tmpData = array('SelectType' => $arrData['SelectType'], 'MallUserID' => $arrData['MallUserID'], 'MallUserNo' => $arrData['MallUserNo'], 'NCUserSSN' => $arrData['NCUserSSN'], 'NCUserName' => $arrData['NCUserName']);
foreach ($tmpData as $k => $v) {
	if ($v != '') {
		$temp_ar = explode('|||', $naverCheckoutAPI->ncCrypt('decrypt',$v,$arrData['Timestamp']));
		if($temp_ar[0] == "ERRO") $naverCheckoutAPI->memberStatusXML('ERROR', $temp_ar[1]);
		else $arrData[$k] = $temp_ar[1];
	}
}

// 임의데이터 EUC-KR로 변환
foreach ($arrData as $k => $v) {
	$arrData[$k] = iconv('UTF-8', 'EUC-KR', $v);
}

$naverCheckoutAPI->ncLog('onclick_status',
	'SelectType => '.$arrData['SelectType']
	.', NCMallID => '.$arrData['NCMallID']
	.', MallUserID => '.$arrData['MallUserID']
	.', MallUserNo => '.$arrData['MallUserNo']
	.', NCUserSSN => '.substr($arrData['NCUserSSN'],0,6).preg_replace('/\d/', '*', substr($arrData['NCUserSSN'],6))
	.', NCUserName => '.$arrData['NCUserName']
	.', Timestamp => '.$arrData['Timestamp']
);

// 변수 검증
if (in_array($arrData['SelectType'], array('1','2')) === false) {
	$naverCheckoutAPI->memberStatusXML('ERROR', '상태 조회(1)나 중복 조회(2)가 아닙니다.');
}
if ($arrData['NCMallID'] == '') {
	$naverCheckoutAPI->memberStatusXML('ERROR', '네이버 가맹점 아이디가 전달되지 않았습니다.');
}
if ($arrData['NCMallID'] <> $checkoutCfg['naverId']) {
	$naverCheckoutAPI->memberStatusXML('ERROR', '상점에 설정된 가맹점 아이디와 네이버 가맹점 아이디가 다릅니다.');
}
if ($arrData['SelectType'] == '1' && $arrData['MallUserID'] == '') {
	$naverCheckoutAPI->memberStatusXML('ERROR', '가맹점 회원 아이디가 전달되지 않았습니다.');
}
if ($arrData['SelectType'] == '1' && $arrData['MallUserNo'] == '') {
	$naverCheckoutAPI->memberStatusXML('ERROR', '가맹점 회원 고유번호가 전달되지 않았습니다.');
}
if ($arrData['SelectType'] == '2' && $arrData['NCUserSSN'] == '') {
	$naverCheckoutAPI->memberStatusXML('ERROR', '네이버 회원 주민등록번호가 전달되지 않았습니다.');
}
if ($arrData['SelectType'] == '2' && $arrData['NCUserName'] == '') {
	$naverCheckoutAPI->memberStatusXML('ERROR', '네이버 회원 이름가 전달되지 않았습니다.');
}

// 회원 상태 조회(1)
if ($arrData['SelectType'] == '1') {
	$query = $db->_query_print('select m_no from gd_member where m_id = [s] and m_no = [s]',$arrData['MallUserID'],$arrData['MallUserNo']);
	$result = $db->_select($query);
	$result = $result[0];
	$naverCheckoutAPI->memberStatusXML('SUCCESS', '', ($result['m_no'] != '' ? 'VALID' : 'INVALID'));
}

// 회원 중복 조회(2)
if ($arrData['SelectType'] == '2') {
	$resno1 = substr($arrData['NCUserSSN'], 0, 6);
	$resno2 = substr($arrData['NCUserSSN'], 6, 7);
	$query = $db->_query_print('select m_no from gd_member where resno1 = md5([s]) and resno2 = md5([s]) and name = [s]',$resno1,$resno2,$arrData['NCUserName']);
	$result = $db->_select($query);
	$result = $result[0];
	$naverCheckoutAPI->memberStatusXML('SUCCESS', '', ($result['m_no'] != '' ? 'VALID' : 'INVALID'));
}

$naverCheckoutAPI->ncLog('onclick_status', "END");
?>
