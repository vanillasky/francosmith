<?
	include dirname(__FILE__)."/../../lib/library.php";
	include_once( dirname(__FILE__)."/../../conf/config.php" );
	include_once( dirname(__FILE__)."/../../conf/fieldset.php" );

	/********************************************************************************************************************************************
		NICE신용평가정보 Copyright(c) KOREA INFOMATION SERVICE INC. ALL RIGHTS RESERVED

		서비스명 : 가상주민번호서비스 (IPIN) 서비스
		페이지명 : 가상주민번호서비스 (IPIN) 사용자 인증 정보 결과 페이지

				   수신받은 데이터(인증결과)를 복호화하여 사용자 정보를 확인합니다.
	*********************************************************************************************************************************************/

	$sSiteCode					= $ipin['code'];			// IPIN 서비스 사이트 코드		(NICE신용평가정보에서 발급한 사이트코드)
	$sSitePw					= $ipin['password'];			// IPIN 서비스 사이트 패스워드	(NICE신용평가정보에서 발급한 사이트패스워드)

	$sEncData					= "";			// 암호화 된 사용자 인증 정보
	$sDecData					= "";			// 복호화 된 사용자 인증 정보

	$sRtnMsg					= "";			// 처리결과 메세지



	/*
	┌ sType 변수에 대한 설명  ─────────────────────────────────────────────────────
		데이타를 추출하기 위한 구분값.

		SEQ : 요청번호 생성
		REQ : 요청 데이타 암호화
		RES : 요청 데이타 복호화
	└────────────────────────────────────────────────────────────────────
	*/
	$sType						= "RES";


	/*
	┌ sModulePath 변수에 대한 설명  ─────────────────────────────────────────────────────
		모듈 경로설정은, '/절대경로/모듈명' 으로 정의해 주셔야 합니다.

		+ FTP 로 모듈 업로드시 전송형태를 'binary' 로 지정해 주시고, 권한은 755 로 설정해 주세요.

		+ 절대경로 확인방법
		  1. Telnet 또는 SSH 접속 후, cd 명령어를 이용하여 모듈이 존재하는 곳까지 이동합니다.
		  2. pwd 명령어을 이용하면 절대경로를 확인하실 수 있습니다.
		  3. 확인된 절대경로에 '/모듈명'을 추가로 정의해 주세요.
	└────────────────────────────────────────────────────────────────────
	*/
	$self_filename = basename($_SERVER['PHP_SELF']);
	$loc = strpos($_SERVER['PHP_SELF'], $self_filename);
	$loc = substr($_SERVER['PHP_SELF'], 0, $loc);
	$sModulePath = $_SERVER['DOCUMENT_ROOT'].$loc."IPINClient";
	// $sModulePath = $_SERVER['DOCUMENT_ROOT']."/shop/member/ipin/IPINClient";

	// ipin_main.php 에서 저장한 세션 정보를 추출합니다.
	// 데이타 위변조 방지를 위해 확인하기 위함이므로, 필수사항은 아니며 보안을 위한 권고사항입니다.
	$sCPRequest = $_SESSION['CPREQUEST'];

	// 회원가입시 가입경로가 모바일인지 체크, 모바일의 아이핀체크에서 세션에 저장한 값을 불러옴
	$joinGubun = $_SESSION['joinGubun'];


	// ipin_process.php 에서 리턴받은 암호화 된 사용자 인증 정보
	$sEncData = $_POST['enc_data'];

	//////////////////////////////////////////////// 문자열 점검///////////////////////////////////////////////
    if(preg_match('~[^0-9a-zA-Z+/=]~', $sEncData, $match)) {echo "입력 값 확인이 필요합니다"; exit;}
    if(base64_encode(base64_decode($sEncData))!= $sEncData) {echo " 입력 값 확인이 필요합니다"; exit;}
    ///////////////////////////////////////////////////////////////////////////////////////////////////////////

	if ($sEncData != "") {

		// 사용자 정보를 복호화 합니다.
		// 실행방법은 싱글쿼터(`) 외에도, 'exec(), system(), shell_exec()' 등등 귀사 정책에 맞게 처리하시기 바랍니다.
		$sDecData = exec("$sModulePath $sType $sSiteCode $sSitePw $sEncData");

		if ($sDecData == -9) {
			$sRtnMsg = "입력값 오류 : 복호화 처리시, 필요한 파라미터값의 정보를 정확하게 입력해 주시기 바랍니다.";
		} else if ($sDecData == -12) {
			$sRtnMsg = "NICE신용평가정보에서 발급한 개발정보가 정확한지 확인해 보세요.";
		} else {

			// 복호화된 데이타 구분자는 ^ 이며, 구분자로 데이타를 파싱합니다.
			/*
				- 복호화된 데이타 구성
				가상주민번호확인처리결과코드^가상주민번호^성명^중복확인값(DupInfo)^연령정보^성별정보^생년월일(YYYYMMDD)^내외국인정보^고객사 요청 Sequence
			*/
			$arrData = split("\^", $sDecData);
			$iCount = count($arrData);

			if ($iCount >= 5) {

				/*
					다음과 같이 사용자 정보를 추출할 수 있습니다.
					사용자에게 보여주는 정보는, '이름' 데이타만 노출 가능합니다.

					사용자 정보를 다른 페이지에서 이용하실 경우에는
					보안을 위하여 암호화 데이타($sEncData)를 통신하여 복호화 후 이용하실것을 권장합니다. (현재 페이지와 같은 처리방식)

					만약, 복호화된 정보를 통신해야 하는 경우엔 데이타가 유출되지 않도록 주의해 주세요. (세션처리 권장)
					form 태그의 hidden 처리는 데이타 유출 위험이 높으므로 권장하지 않습니다.
				*/

				$strResultCode	= $arrData[0];			// 결과코드
				if ($strResultCode == 1) {
					$strCPRequest	= $arrData[8];			// CP 요청번호

					if ($sCPRequest == $strCPRequest) {

						$sRtnMsg = "사용자 인증 성공";

						$strVno      		= $arrData[1];	// 가상주민번호 (13자리이며, 숫자 또는 문자 포함)
						$strUserName		= $arrData[2];	// 이름
						$strDupInfo			= $arrData[3];	// 중복가입 확인값 (64Byte 고유값)
						$strAgeInfo			= $arrData[4];	// 연령대 코드 (개발 가이드 참조)
						$strGender			= $arrData[5];	// 성별 코드 ()
						$strBirthDate		= $arrData[6];	// 생년월일 (YYYYMMDD)
						$strNationalInfo	= $arrData[7];	// 내/외국인 정보 (0:내국인; 1:외국인)

					} else {
						$sRtnMsg = "CP 요청번호 불일치 : 세션에 넣은 $sCPRequest 데이타를 확인해 주시기 바랍니다.";
					}
				} else {
					$sRtnMsg = "리턴값 확인 후, NICE신용평가정보 개발 담당자에게 문의해 주세요. [$strResultCode]";
				}

			} else {
				$sRtnMsg = "리턴값 확인 후, NICE신용평가정보 개발 담당자에게 문의해 주세요.";
			}

		}
	} else {
		$sRtnMsg = "처리할 암호화 데이타가 없습니다.";
	}

	list($chkCount) = $db->fetch("SELECT count(m_id) FROM ".GD_MEMBER." WHERE dupeinfo = '$strDupInfo'");
	if($chkCount < 1){
		$dormant = Core::loader('dormant');
		$chkCount = $dormant->getCountDupeinfoFromDormant($strDupInfo);
	}

	$sess_OrderNo = "";
	session_register("sess_OrderNo");

	$ssCallType = $_SESSION["sess_callType"];

	if($strAgeInfo >= 6 ) {
		$_SESSION['adult'] = 1;
	}
	else {
		unset($_SESSION['adult']);
	}

	// 본인인증에서 만14세 미만 회원가입 허용상태
	$mUnder14 = Core::loader('memberUnder14Join');
	$under14Code = $mUnder14->joinSelfCert($strBirthDate);

	if($ssCallType == "adultcheck" || $ssCallType == "adultcheckmobile") {
		if($_SESSION['adult']) {
			msg("성인 인증되었습니다.");
			if($sess){
				setAdultAuthDate($session->m_id);
			}
			if($ssCallType == "adultcheckmobile"){
				$returnUrl = $_POST['param_r1'] ? urldecode($_POST['param_r1']) : "http://".$_SERVER['HTTP_HOST']."/m/";
			} else {
				$returnUrl = $_POST['param_r1'] ? urldecode($_POST['param_r1']) : "../../index.php";
			}
			go($returnUrl, "parent");
		}
		else {
			unset($_SESSION['adult']);
			msg("성인 인증에 실패했습니다.");
			if($ssCallType == "adultcheckmobile"){
				$returnUrl = $_POST['param_r1'] ? urldecode($_POST['param_r1']) : "http://".$_SERVER['HTTP_HOST']."/m/";
			} else {
				$returnUrl = $_POST['param_r1'] ? urldecode($_POST['param_r1']) : "../../index.php";
			}
			go($returnUrl, "parent");
		}
		exit();
	}
?>
<html>
<? if($strResultCode == 1 && $sCPRequest == $strCPRequest) { ?>
<script language="JavaScript">
	var _joinGubun = "<? echo $joinGubun?>";
	function loadAction() {
		var strRetCd = "<?=$strResultCode?>";		// '1' 이어야 한다.
		var strMsg = "<?=$sRtnMsg?>";

		var strName = "<?=$strUserName?>";					// 이름
		var birthday = "<?=$strBirthDate?>";				// 생년월일
		var sex = "<?=($strGender) ? 'M' : 'W'?>";			// 성별
		var dupeInfo = "<?=$strDupInfo?>";					// 아이핀 중복 조회 코드
		var ageInfo = "<?=$strAgeInfo?>";					// 아이핀 중복 조회 코드
		var foreigner = "<?=$strNationalInfo?>";			// 내국인/외국인 정보
		var dupeCount = "<?=$chkCount?>";					// 가입된 횟수
		var nice_minoryn = "<?=$ipin['nice_minoryn']?>";	// 성인인증
		var year = "<?=substr($strBirthDate, 0, 4)?>";
		var under14Code = "<?=$under14Code?>";				// 만14세 미만 회원가입 허용상태코드

		// 호출유형을 찾는다.
		var callType = "<?=$ssCallType?>";

		// 아이디 찾기에서 호출한 경우, parent 에 act 엘레먼트가 있다.
		if (callType == "findid" || callType == "findpwd") {
			parent.document.fm.action = '';
			parent.document.fm.target = '';
			parent.document.fm.rncheck.value = 'ipin';
			parent.document.fm.dupeinfo.value = dupeInfo;
			parent.document.fm.submit();
		}
		else {
			// default 회원가입
			if (dupeCount > 0) {
			alert( "이미 가입이 되어 있습니다.");
			}
			else {
				if ( nice_minoryn == 'y' && ageInfo < "6" ) { // 실명인증성공 & 성인인증실패
					parent.document.frmAgree['name']. value = '';
					alert( '성인인증 실패' ); //결과 메시지 출력
				}
				else if ( under14Code == 'rejectJoin' ) { // 만14세 미만 회원가입 거부
					parent.document.frmAgree['name']. value = '';
					alert( '만 14세 미만의 경우 회원가입을 허용하지 않습니다.' ); //결과 메시지 출력
				}
				else if ( under14Code == 'adminStatus' && confirm('만14세 미만 회원의 경우 관리자 승인 후 가입이 완료됩니다.\n계속 진행하시겠습니까?') === false ) {
					// 만14세 미만 회원가입 관리자 승인 후 가입
					parent.document.frmAgree['name']. value = '';
				}
				else if ( strRetCd == "1" ) // 아이핀인증성공
				{
					alert( "아이핀인증이 정상처리 되었습니다." ); //결과 메시지 출력
					parent.document.frmAgree.action = '';
					parent.document.frmAgree.target = '';
					parent.document.frmAgree.rncheck.value = 'ipin';
					parent.document.frmAgree.nice_nm.value = strName;
					parent.document.frmAgree.birthday.value = birthday;
					parent.document.frmAgree.sex.value = sex;
					parent.document.frmAgree.dupeinfo.value = dupeInfo;
					parent.document.frmAgree.foreigner.value = foreigner;
					parent.document.frmAgree.submit();
				}
				else // 실명인증실패
				{
				//	결과에 따라서 실명안심차단과 명의도용 차단을 처리한다.
					parent.document.frmAgree['name']. value = '';
					alert( '아이핀인증이 실패했습니다.\n\n' + strMsg); //결과 메시지 출력
				}
			}
		}
		if (_joinGubun == 'mobile'){
			if (typeof(parent.frmMaskRemove) != 'undefined') parent.frmMaskRemove('popupCertKey');
			else self.close();
		}
		else self.close();
	}
</script>
<? } ?>
	<body onload="javascript:loadAction();"></body>
</html>
