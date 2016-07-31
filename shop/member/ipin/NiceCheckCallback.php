<?php
	include dirname(__FILE__)."/../../lib/library.php";
	include_once( dirname(__FILE__)."/../../conf/fieldset.php" );
	require_once( dirname(__FILE__)."/nice.nuguya.oivs.php" );

	//session_start();
	//========================================================================================
	//=====	▣ 키스트링 80자리 세팅 ▣
	//========================================================================================
	$athKeyStr = $ipin[athKeyStr];

	$oivsObject = new OivsObject();
	$oivsObject->athKeyStr = $athKeyStr;

	$strRecvData 	= $_POST[ "SendInfo" ];
	$blRcv 		= $oivsObject->resolveClientData( $strRecvData );
	// 해킹방지를 위해 세션에 저장된 값과 비교 ..

	$ssOrderNo = $_SESSION["sess_OrderNo"];

	if( $ssOrderNo != $oivsObject->ordNo){
		echo ("세션정보가 존재하지 않습니다.");
		exit;
	}
	$sess_OrderNo = "";
	session_register("sess_OrderNo");

	$ssCallType = $_SESSION["sess_callType"];

	// 회원가입시 가입경로가 모바일인지 체크, 모바일의 아이핀체크에서 세션에 저장한 값을 불러옴
	$joinGubun = $_SESSION["joinGubun"];

	$year = date('Y');

	//debug($oivsObject);
	//debug($ssCallType);

	list($chkCount) = $db->fetch("select count(*) from ".GD_MEMBER." where dupeinfo='".$oivsObject->dupeInfo."'");
	if($chkCount < 1){
		$dormant = Core::loader('dormant');
		$chkCount = $dormant->getCountDupeinfoFromDormant($oivsObject->dupeInfo);
	}

	$birth_year = substr($oivsObject->birthday, 0, 4);

	$age = $year-$birth_year;

	if($age >= 19) {
		$_SESSION['adult'] = 1;
	}
	else {
		unset($_SESSION['adult']);
	}

	// 본인인증에서 만14세 미만 회원가입 허용상태
	$mUnder14 = Core::loader('memberUnder14Join');
	$under14Code = $mUnder14->joinSelfCert($oivsObject->birthday);

	if($ssCallType == "adultcheck" || $ssCallType == "adultcheckmobile") {
		if($_SESSION['adult']) {
			$returnUrl = $_SESSION['ipin_requrlUrl'] ? $_SESSION['ipin_requrlUrl'] : "../../index.php";
			if($ssCallType == "adultcheckmobile"){
				$returnUrl = $_SESSION['ipin_requrlUrl'] ? $_SESSION['ipin_requrlUrl'] : "http://".$_SERVER['HTTP_HOST']."/m/";
			}
			unset($_SESSION['ipin_requrlUrl']); // 모바일샵 returnUrl 재정의 이후로 위치 변경
			msg("성인 인증되었습니다.");
			if($sess){
				setAdultAuthDate($session->m_id);
			}
			if($joinGubun == 'mobile'){
				echo "<script>
					 parent.location='$returnUrl';
					 </script>";
			}else{
				echo "<script>
					 opener.parent.location='$returnUrl';
					 window.close();
					 </script>";
			}
		}
		else {
			$returnUrl = $_SESSION['ipin_requrlUrl'] ? $_SESSION['ipin_requrlUrl'] : "../../index.php";
			unset($_SESSION['ipin_requrlUrl']);
			if($ssCallType == "adultcheckmobile"){
				$returnUrl = $_SESSION['ipin_requrlUrl'] ? $_SESSION['ipin_requrlUrl'] : "http://".$_SERVER['HTTP_HOST']."/m/";
			}
			unset($_SESSION['adult']);
			msg("성인 인증에 실패했습니다.");
			if($joinGubun == 'mobile'){
				echo "<script>
					 parent.location='$returnUrl';
					 </script>";
			}else{
				echo "<script>
					 opener.parent.location='$returnUrl';
					 window.close();
					 </script>";
			}
		}
		exit();
	}

//!!!!!!!!!!!!!!!!!주의!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!
//당사에서 전달하여 복호화된 정보를
//SSL이나 데이터 보안모듈이 적용되어 있지 않은 환경에서
//페이지간 전달하는 것은 보안에 심각한 문제를 야기할 수 있습니다.

//보안 환경이 갖추어지지 않은 업체에서는, 필요한 값을
//세션 저장등의 다른 수단을 이용하여 사용해 주시기 바랍니다.
//위 사항을 준수하지 않아서 발생하는 보안사고에 대해서는
//당사에서 책임을 지지 않사오니, 주의를 기울여 주시기 바랍니다.

?>

<html>
	<script type="text/javascript">
		var _joinGubun = "<? echo $joinGubun?>";
		function loadAction()
		{
			var strRetCd = "<? echo $oivsObject->retCd; ?>";		// '1' 이어야 한다.
			var strRetDtlCd = "<? echo $oivsObject->retDtlCd; ?>";	// 'A" 이어야 한다.
			var strMsg = "<? echo $oivsObject->message; ?>";

			var strName = "<? echo $oivsObject->niceNm; ?>";
			var birthday = "<? echo $oivsObject->birthday; ?>";
			var sex = "<? if ($oivsObject->sex == '1') echo 'M'; else 'W'; ?>";
			var dupeInfo = "<? echo $oivsObject->dupeInfo; ?>";
			var foreigner = "<? echo $oivsObject->foreigner; ?>";
			var paKey = "<? echo $oivsObject->paKey; ?>";

			var dupeCount = "<? echo $chkCount ?>";

			var minoryn = "<? echo $ipin[minoryn]; ?>";

			var year = "<? echo $year; ?>";

			var age = year-birthday.substring( 0, 4);
			// 호출유형을 찾는다.
			var callType = "<? echo $ssCallType; ?>";
			var under14Code = "<?=$under14Code?>";	// 만14세 미만 회원가입 허용상태코드

			//	한국신용정보로 부터 결과코드에 해당하는 메시지를 받아온다.
			//	(다른 메시지를 띄우려면 메뉴얼 파일을 참고하여  strRetCd, strRetDtlCd 별 메시지를 지정해 준다.
			//strProcessMessage = getMessage( strRetCd, strRetDtlCd );

			// 아이디 찾기에서 호출한 경우, opener.parent 에 act 엘레먼트가 있다.
			if (callType == "findid" || callType == "findpwd") {
				opener.parent.document.fm.action = '';
				opener.parent.document.fm.target = '';
				opener.parent.document.fm.rncheck.value = 'ipin';
				opener.parent.document.fm.dupeinfo.value = dupeInfo;
				opener.parent.document.fm.submit();
			}
			else {
				var _joinGubunHeader = (_joinGubun == "mobile") ? parent : opener.parent;
				// default 회원가입
				if (dupeCount > 0) {
				alert( "이미 가입이 되어 있습니다.");
				}
				else {
					if ( minoryn == 'y' && strRetCd == "1" && age < 20 ){ // 실명인증성공 & 성인인증실패
						_joinGubunHeader.document.frmAgree['name']. value = '';
						alert( '성인인증 실패' ); //결과 메시지 출력
					}
					else if ( under14Code == 'rejectJoin' ) { // 만14세 미만 회원가입 거부
						_joinGubunHeader.document.frmAgree['name']. value = '';
						alert( '만 14세 미만의 경우 회원가입을 허용하지 않습니다.' ); //결과 메시지 출력
					}
					else if ( under14Code == 'adminStatus' && confirm('만14세 미만 회원의 경우 관리자 승인 후 가입이 완료됩니다.\n계속 진행하시겠습니까?') === false ) {
						// 만14세 미만 회원가입 관리자 승인 후 가입
						_joinGubunHeader.document.frmAgree['name']. value = '';
					}
					else if ( strRetCd == "1" && strRetDtlCd == "A") // 아이핀인증성공
					{
						alert( "아이핀인증이 정상처리 되었습니다." ); //결과 메시지 출력
						_joinGubunHeader.document.frmAgree.action = '';
						_joinGubunHeader.document.frmAgree.target = '';
						_joinGubunHeader.document.frmAgree.rncheck.value = 'ipin';
						_joinGubunHeader.document.frmAgree.nice_nm.value = strName;
						_joinGubunHeader.document.frmAgree.pakey.value = paKey;
						_joinGubunHeader.document.frmAgree.birthday.value = birthday;
						_joinGubunHeader.document.frmAgree.sex.value = sex;
						_joinGubunHeader.document.frmAgree.dupeinfo.value = dupeInfo;
						_joinGubunHeader.document.frmAgree.foreigner.value = foreigner;
						_joinGubunHeader.document.frmAgree.submit();
					}
					else // 실명인증실패
					{
					//	결과에 따라서 실명안심차단과 명의도용 차단을 처리한다.
						_joinGubunHeader.document.frmAgree['name']. value = '';
						alert( '아이핀인증이 실패했습니다. ' + strMsg); //결과 메시지 출력
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

	<body onload="javascript:loadAction();"></body>
</html>
