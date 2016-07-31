<?
include "../lib/library.php";
include_once dirname(__FILE__)."/../conf/config.php";
include_once dirname(__FILE__)."/../conf/naverCheckout.cfg.php";
$naverCheckoutAPI = Core::loader('naverCheckoutAPI');
include_once dirname(__FILE__)."/../lib/httpSock.class.php";

session_start();

function chk_referer($url=""){
	if( preg_match('/'.addslashes($_SERVER['HTTP_HOST']).'/',$_SERVER['HTTP_REFERER']) ) $ret = true;
	else $ret = false;

	if( $ret && $url){
		$url = str_replace('/','\/',$url);
		if( preg_match('/'.$url.'/',$_SERVER['HTTP_REFERER']) )	$ret = true;
		else $ret = false;
	}
	return $ret;
}

$mode = ($_POST['mode']) ? $_POST['mode'] : $_GET['mode'];

if(!$mode) {
	msg("처리할 작업의 타입이 정해지지 않았습니다.", -1);
}

switch($mode) {
	case "checkAccount" :
		// 받아온 값을 사용하기 편하게 가공
			$shopId			= ($_POST['shopId'])		? $_POST['shopId']			: $_GET['shopId'];			// 쇼핑몰 아이디
			$shopPassword	= ($_POST['shopPassword'])	? $_POST['shopPassword']	: $_GET['shopPassword'];	// 쇼핑몰 비밀번호
			$MallUserSSN1	= ($_POST['MallUserSSN1'])	? $_POST['MallUserSSN1']	: "";						// 쇼핑몰 가입자 주민번호 앞자리
			$MallUserSSN2	= ($_POST['MallUserSSN2'])	? $_POST['MallUserSSN2']	: "";						// 쇼핑몰 가입자 주민번호 뒷자리

		// 필수 입력 값 검사
			if(!$shopId) msg("쇼핑몰 아이디가 전송되지 않았습니다.", -1);
			if(!$shopPassword) msg("쇼핑몰 비밀번호가 전송되지 않았습니다.", -1);
			if(!$MallUserSSN1) msg("주민번호 앞자리가 전송되지 않았습니다.", -1);
			if(!$MallUserSSN2) msg("주민번호 뒷자리가 전송되지 않았습니다.", -1);

		// 회원이 존재하는지 확인
			list($cnt) = $db->fetch("SELECT COUNT(m_id) FROM ".GD_MEMBER." WHERE m_id = '$shopId' AND password = password('$shopPassword')");
			if(!$cnt) go("./naver_storemember_fail1.php");

		// 회원 이름 및 회원의 주민번호 검사 후 동일 인물인지 체크
			list($m_no, $MallUserName, $resno1, $resno2, $rncheck) = $db->fetch("SELECT m_no, name, resno1, resno2, rncheck FROM ".GD_MEMBER." WHERE m_id = '".$shopId."'");
			if(!$MallUserName) msg("회원정보 중 이름이 존재하지 않습니다.\\n이 과정을 수행하기 위해서 회원명은 필수사항입니다.\\n\\n쇼핑몰의 회원정보를 확인 후 다시 시도해 주세요.", -1);
			if(!$resno1 || !$resno2) {
				// 쇼핑몰 아이핀 사용 설정에 따라 오류메세지 따로 표기
				include_once dirname(__FILE__)."/../conf/fieldset.php";

				if($rncheck == "ipin" && $ipin['useyn'] == "y") go("./naver_storemember_fail3.php");
				else go("./naver_storemember_fail6.php");
			}
			if(md5($MallUserSSN1) != $resno1 || md5($MallUserSSN2) != $resno2) go("./naver_storemember_fail6.php");

		// 세션에 저장
			$_SESSION['NCINFO']['MallUserID'] = $shopId;
			$_SESSION['NCINFO']['MallUserNo'] = $m_no;
			$_SESSION['NCINFO']['MallUserName'] = $MallUserName;
			$_SESSION['NCINFO']['MallUserSSN'] = $MallUserSSN1.$MallUserSSN2;

		go("./naver_storemember_confirm.php");

		break;

	case "infoSupplyAgreement" :
		$MallUserID		= ($_SESSION['NCINFO']['MallUserID'])	? $_SESSION['NCINFO']['MallUserID']		: "";
		$NCUserNo		= ($_SESSION['NCINFO']['NCUserNo'])		? $_SESSION['NCINFO']['NCUserNo']		: "";
		$MallUserName	= ($_SESSION['NCINFO']['MallUserName'])	? $_SESSION['NCINFO']['MallUserName']	: "";
		$MallUserSSN	= ($_SESSION['NCINFO']['MallUserSSN'])	? $_SESSION['NCINFO']['MallUserSSN']	: "";
		$Timestamp		= ($_SESSION['NCINFO']['Timestamp'])	? $_SESSION['NCINFO']['Timestamp']		: "";

		// 필수 입력 값 검사
			if(!$MallUserID) msg("회원의 쇼핑몰 아이디 정보가 없습니다.\\n\\n처음부터 다시 진행해 주세요.", -1);
			if(!$MallUserSSN) msg("회원의 주민등록번호 정보가 없습니다.\\n\\n처음부터 다시 진행해 주세요.", -1);

		// 고유번호 복호화
			$temp_ar_NCUserNo = explode('|||', $naverCheckoutAPI->ncCrypt('decrypt', $NCUserNo, $Timestamp));
			if($temp_ar_NCUserNo[0] == "ERRO") msg("오류가 있습니다.\\n\\n".$temp_ar_NCUserNo[1], -1);
			else $temp_NCUserNo = $temp_ar_NCUserNo[1];

		// 네이버 체크아웃 회원인지 확인
			$apiResult = $naverCheckoutAPI->CompareMember($MallUserSSN,$MallUserName,$temp_NCUserNo);

			if($apiResult === false) {
				if($naverCheckoutAPI->error) msg("오류가 있습니다.\\n\\n".$naverCheckoutAPI->error);
			}
			else {
				if($apiResult == "Y") ;
				elseif($apiResult == "N") go("./naver_storemember_fail2.php"); // Error : 쇼핑몰 회원과 네이버 회원의 주민등록 or 이름이 다른 경우
				else go("./naver_storemember_fail5.php"); // Error : 접속오류
			}

			// 가맹점 회원 정보 송신
				if($checkoutCfg['testYn'] == "y") {
					$url4Send = "https://test-checkout.naver.com/customer/api/CP949/memberInfoRecieve.nhn"; // EUC-KR Test
					//$url4Send = "https://test-checkout.naver.com/customer/api/memberInfoRecieve.nhn"; // UTF-8 Test
				}
				else {
					$url4Send = "https://checkout.naver.com/customer/api/CP949/memberInfoRecieve.nhn"; // EUC-KR Service
					//$url4Send = "https://checkout.naver.com/customer/api/memberInfoRecieve.nhn"; // UTF-8 Service
				}

				// 가맹점 회원 정보 파라메터
					$NCMallID = $checkoutCfg['naverId']; // 가맹점 아이디
					$MallUserID = $naverCheckoutAPI->ncCrypt('encrypt', $_SESSION['NCINFO']['MallUserID'], $Timestamp); // 쇼핑몰 회원 아이디
					$MallUserNo = $naverCheckoutAPI->ncCrypt('encrypt', $_SESSION['NCINFO']['MallUserNo'], $Timestamp); // 쇼핑몰 회원 고유 번호

					$param4Send = "NCMallID=".urlencode($NCMallID)."&MallUserID=".urlencode($MallUserID)."&MallUserNo=".urlencode($MallUserNo)."&NCUserNo=".urlencode($NCUserNo)."&Timestamp=".urlencode($Timestamp);

				// 회원정보에 outLink 관련 기록
					list($outlink) = $db->fetch("SELECT outlink FROM ".GD_MEMBER." WHERE m_no = '".$_SESSION['NCINFO']['MallUserNo']."'");
					$db->query("UPDATE ".GD_MEMBER." SET outlink = '".modOutlink($outlink, "naverCheckout")."' WHERE m_no = '".$_SESSION['NCINFO']['MallUserNo']."'");

				// 고유식별 정보 제공 & 개인정보 제공 동의 저장
					$agree_CI = ($_POST['agree'] == "y") ? $_POST['agree'] : "n";
					$agree_info = ($_POST['agree2'] == "y") ? $_POST['agree2'] : "n";
					$query = "INSERT INTO ".GD_NAVERCHECKOUT_AGREEMENT." SET m_no = '".$_SESSION['NCINFO']['MallUserNo']."', agree_CI = '$agree_CI', agree_info = '$agree_info', regdt = NOW(), ip = '".$_SERVER['REMOTE_ADDR']."'";
					$db->query($query);

				unset($_SESSION['NCINFO']);
				go($url4Send."?".$param4Send);

		break;
}
?>
