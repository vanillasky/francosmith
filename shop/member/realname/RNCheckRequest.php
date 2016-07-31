<?php

	require_once( dirname(__FILE__)."/nice.nuguya.oivs.php" );
	include_once( dirname(__FILE__)."/../../conf/fieldset.php" );

	//#######################################################################################
	//#####
	//#####	개인실명확인 서비스 소스 (실명확인요청)						한국신용정보(주)
	//#####	( PHPScript 처리 )
	//#####
	//#####	================================================================================
	//#####
	//#####	* 본 페이지는 귀사의 서버에 복사해서 관리하십시오.
	//#####	  페이지를 수정하거나 변경하지 마십시오. (서비스 장애의 원인이 됩니다)
	//#####
	//#######################################################################################

	/****************************************************************************************
	 *****	▣ 회원사 ID 설정 : 계약시에 발급된 회원사 ID를 설정하십시오. ▣
	 ****************************************************************************************/

	$strNiceId = $realname[id];


	/****************************************************************************************
	 *****	▣  NiceCheck.htm 에서 넘겨 받은 SendInfo 값을 복호화 하여
	 *****		주민번호,성명 등 각각의 값을 세팅한다 ▣
	 ****************************************************************************************/
	$oivsObject->clientData = $_POST['SendInfo'];
	$oivsObject->desClientData();

	// 복호화 된 값은 아래 주석을 풀어 확인 가능합니다.
	// (기존 회원 체크는 이 부분에서 하시면 됩니다.)
	/*
	echo "<BR>성명 : " . $oivsObject->userNm ;
	echo "<BR>주민번호/외국인번호 : " . $oivsObject->resIdNo ;
	echo "<BR>조회사유코드 : " . $oivsObject->inqRsn ;
	echo "<BR>내/외국인 구분코드 : " . $oivsObject->foreigner ;
	*/

	/****************************************************************************************
	 *****	▣ 실명확인 서비스를 호출한다. ▣
	 ****************************************************************************************/

	$oivsObject->niceId = $strNiceId;
	$oivsObject->callService();

	/****************************************************************************************
	 *****	▣ 실명확인 서비스를 응답값을 처리한다. ▣

	 *****	strRetCd 와 strRetDtlCd를 이용하여 작업 하시면 됩니다.
	 *****	단! strRetDtlCd 가 Y,C인 경우는 개인의 설정에 의해 실명확인을 막아놓은 상태이므로
	 *****	관련스크립트는 수정하지 마시기 바랍니다.
	 ****************************************************************************************/

	//==================================================================================================================
	//				응답에 대한 결과 및 변수들에 대한 설명
	//------------------------------------------------------------------------------------------------------------------
	//
	//	< 한국신용정보 온라인 식별 서비스에서 제공하는 정보 >
	//
	//	oivsObject->message			: 오류 또는 정보성 메시지
	//	oivsObject->retCd			: 결과 코드(메뉴얼 참고) // cf. 한국신용정보 성명 등록 및 정정 페이지 : https://www.nuguya.com
	//	oivsObject->retDtlCd			: 결과 상세 코드(메뉴얼 참고)
	//	oivsObject->minor 			: 성인인증 결과 코드
	//									"1"	: 성인
	//									"2"	: 미성년
	//									"9"	: 확인 불가
	//
	//=================================================================================================================
$Protocol = $_SERVER['HTTPS']=='on'?'https://':'http://';

// 성인인증 처리
if ($_POST['mode']=="adult_guest") {

	if (($oivsObject->retCd == 1) && ($oivsObject->minor == 1)) {

		$_SESSION['adult'] = 1;

		msg("성인 인증되었습니다.");

		if (!$_POST['returnUrl']) $_POST['returnUrl'] = $_SERVER['HTTP_REFERER'];
		go($_POST['returnUrl']);

	}
	else {

		msg("성인 인증에 실패했습니다.");

		if (!$_POST['returnUrl']) $_POST['returnUrl'] = $_SERVER['HTTP_REFERER'];
		go($_POST['returnUrl']);
	}
	exit;
}
?>

<html>
	<head>
		<title>한국신용정보주식회사 개인실명확인 서비스 페이지</Title>
		<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">

		<!--	==========================================================	-->
		<!--	한국신용정보주식회사 처리 모듈 (수정 및 변경하지 마십시오)	-->
		<!--	==========================================================	-->
		<script type="text/javascript" src="<?=$Protocol?>secure.nuguya.com/nuguya/nice.nuguya.oivs.crypto.js"></script>
		<script type="text/javascript" src="<?=$Protocol?>secure.nuguya.com/nuguya/nice.nuguya.oivs.msg.js"></script>
		<script type="text/javascript" src="<?=$Protocol?>secure.nuguya.com/nuguya/nice.nuguya.oivs.util.js"></script>
	</head>

	<script type="text/javascript">

		function loadAction()
		{
			var strRetCd = "<? echo $oivsObject->retCd; ?>";
			var strRetDtlCd = "<? echo $oivsObject->retDtlCd; ?>";
			var strMsg = "<? echo $oivsObject->message; ?>";
			var strMinor = "<? echo $oivsObject->minor; ?>";
			var minoryn = "<? echo $realname[minoryn]; ?>";
			var userdi = "<?=$oivsObject->dupeinfo?>";

			//	한국신용정보로 부터 결과코드에 해당하는 메시지를 받아온다.
			//	(다른 메시지를 띄우려면 메뉴얼 파일을 참고하여  strRetCd, strRetDtlCd 별 메시지를 지정해 준다.
			strProcessMessage = getMessage( strRetCd, strRetDtlCd );

			if ( minoryn == 'y' && strRetCd == "1" && strMinor != '1' ){ // 실명인증성공 & 성인인증실패
				parent.document.frmAgree['name']. value = '';
				parent.document.frmAgree['resno[]'][0]. value = '';
				parent.document.frmAgree['resno[]'][1]. value = '';
				alert( '성인인증 실패' ); //결과 메시지 출력
			}
			else if ( strRetCd == "1" ) // 실명인증성공
			{
				alert( strProcessMessage ); //결과 메시지 출력
				parent.document.frmAgree.action = '';
				parent.document.frmAgree.target = '';
				if(parent.document.frmAgree.rncheck)parent.document.frmAgree.rncheck.value = 'realname';
				if(parent.document.frmAgree.dupeinfo)parent.document.frmAgree.dupeinfo.value = userdi;
				parent.document.frmAgree.submit();
			}
			else // 실명인증실패
			{
			//	결과에 따라서 실명안심차단과 명의도용 차단을 처리한다.
				parent.document.frmAgree['name']. value = '';
				parent.document.frmAgree['resno[]'][0]. value = '';
				parent.document.frmAgree['resno[]'][1]. value = '';
				if ( strRetDtlCd == "Y" )
				{
					//	처리 결과가 실명안심차단 상태인지를 확인한다.
					var retVal = confirm( strProcessMessage + "\n\n" + getCheckMessage( "S31" ) );
					if ( retVal == true )
					{
						goSafeBlockExpt();
						return;
					}
					else
					{
						return;
					}
				}
				else if ( strRetDtlCd == "C" )
				{
					//	처리 결과가 실명명의도용차단 상태인지를 확인한다.
					alert( strProcessMessage + "\n\n" + getCheckMessage( "S32" ) );
				}
				else
				{
					alert( strProcessMessage ); //결과 메시지 출력
				}
			}
		}

	</script>

	<body onload="javascript:loadAction();"></body>
</html>