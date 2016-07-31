<?php
	include dirname(__FILE__)."/../../lib/library.php";
	include_once( dirname(__FILE__)."/../../conf/config.php" );
	include_once( dirname(__FILE__)."/../../conf/fieldset.php" );

	$sSiteCode	= $ipin['code'];			// IPIN 서비스 사이트 코드		(NICE신용평가정보에서 발급한 사이트코드)
	$sSitePw	= $ipin['password'];			// IPIN 서비스 사이트 패스워드	(NICE신용평가정보에서 발급한 사이트패스워드)
	$sEncData	= "";			// 암호화 된 데이타
	$sRtnMsg	= "";			// 처리결과 메세지
	
	
	
	/*
	┌ sType 변수에 대한 설명  ─────────────────────────────────────────────────────
		데이타를 추출하기 위한 구분값.
		
		SEQ : 요청번호 생성
		REQ : 요청 데이타 암호화
		RES : 요청 데이타 복호화
	└────────────────────────────────────────────────────────────────────
	*/
	$sType						= "";
	
	
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
	
	/*
	┌ sReturnURL 변수에 대한 설명  ─────────────────────────────────────────────────────
		NICE신용평가정보 팝업에서 인증받은 사용자 정보를 암호화하여 귀사로 리턴합니다.
		따라서 암호화된 결과 데이타를 리턴받으실 URL 정의해 주세요.
		
		* URL 은 http 부터 입력해 주셔야하며, 외부에서도 접속이 유효한 정보여야 합니다.
		* 당사에서 배포해드린 샘플페이지 중, ipin_process.jsp 페이지가 사용자 정보를 리턴받는 예제 페이지입니다.
		
		아래는 URL 예제이며, 귀사의 서비스 도메인과 서버에 업로드 된 샘플페이지 위치에 따라 경로를 설정하시기 바랍니다.
		예 - http://www.test.co.kr/ipin_process.jsp, https://www.test.co.kr/ipin_process.jsp, https://test.co.kr/ipin_process.jsp
	└────────────────────────────────────────────────────────────────────
	*/
	if($_SERVER['SERVER_PORT'] == 80) {
		$Port = "";
	} elseif($_SERVER['SERVER_PORT'] == 443) {
		$Port = "";
	} else {
		$Port = $_SERVER['SERVER_PORT'];
	}
	if (strlen($Port) > 0) $Port = ":".$Port;
	$Protocol = ($_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';

	$host = parse_url($_SERVER['HTTP_HOST']);
	if ($host['path']) {
		$Host = $host['path'];
	} else {
		$Host = $host['host'];
	}

	$sReturnURL = $Protocol.$Host.$Port.$loc."IPINProcess.php";
	if($_GET['callType'] == 'applyipin') $sReturnURL = $Protocol.$Host.$Port.$loc."IPINApply.php";
	
	
	/*
	┌ sCPRequest 변수에 대한 설명  ─────────────────────────────────────────────────────
		[CP 요청번호]로 귀사에서 데이타를 임의로 정의하거나, 당사에서 배포된 모듈로 데이타를 생성할 수 있습니다. (최대 30byte 까지만 가능)
		
		CP 요청번호는 인증 완료 후, 암호화된 결과 데이타에 함께 제공되며
		데이타 위변조 방지 및 특정 사용자가 요청한 것임을 확인하기 위한 목적으로 이용하실 수 있습니다.
		
		따라서 귀사의 프로세스에 응용하여 이용할 수 있는 데이타이기에, 필수값은 아닙니다.
	└────────────────────────────────────────────────────────────────────
	*/
	$sCPRequest					= "";
	
	
	
	
	
	$sType		= "SEQ";			// CP 요청번호 구분값
	
	// 앞서 설명드린 바와같이, CP 요청번호는 배포된 모듈을 통해 아래와 같이 생성할 수 있습니다.
	// 실행방법은 싱글쿼터(`) 외에도, 'exec(), system(), shell_exec()' 등등 귀사 정책에 맞게 처리하시기 바랍니다.
	$sCPRequest = exec("$sModulePath $sType $sSiteCode");
	
	
	// CP 요청번호를 세션에 저장합니다.
	// 현재 예제로 저장한 세션은 ipin_result.php 페이지에서 데이타 위변조 방지를 위해 확인하기 위함입니다.
	// 필수사항은 아니며, 보안을 위한 권고사항입니다.
    $_SESSION['CPREQUEST'] = $sCPRequest;
    
    
    
    $sType		= "REQ";			// 데이타 암호화 구분값
    
    // 리턴 결과값에 따라, 프로세스 진행여부를 파악합니다.
    // 실행방법은 싱글쿼터(`) 외에도, 'exec(), system(), shell_exec()' 등등 귀사 정책에 맞게 처리하시기 바랍니다.
    $sEncData	= exec("$sModulePath $sType $sSiteCode $sSitePw $sCPRequest $sReturnURL");
    
    // 리턴 결과값에 따른 처리사항
    if ($sEncData == -9)
    {
    	$sRtnMsg = "입력값 오류 : 암호화 처리시, 필요한 파라미터값의 정보를 정확하게 입력해 주시기 바랍니다.";
    } else {
    	$sRtnMsg = "$sEncData 변수에 암호화 데이타가 확인되면 정상, 정상이 아닌 경우 리턴코드 확인 후 NICE신용평가정보 개발 담당자에게 문의해 주세요.";
    }

	$strOrderNo = date("Ymd") . rand(100000000000,999999999999); //주문번호 20자리 .. 매 요청마다 중복되지 않도록 유의

	// 해킹방지를 위해 요청정보 세션에 저장
	//session_start();		//library에서 함.
	$sess_OrderNo = $strOrderNo;
	session_register("sess_OrderNo");
	$_SESSION['sess_OrderNo'] = $strOrderNo;
	session_register("sess_callType");
	$_SESSION['sess_callType'] = $_GET['callType'];

	// 회원가입시 가입경로가 모바일인지 체크, 모바일의 아이핀체크에서 GET으로 가져옴
	session_register("joinGubun");
	$_SESSION['joinGubun'] = $_GET['joinGubun'];

	// returnUrl 정의
	if ($_REQUEST['returnUrl'] != '') {
		$returnUrl = $_REQUEST['returnUrl'];
	}
	else {
		parse_str(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY), $output);
		$returnUrl = $output['returnUrl'];
	}
?>

<html>
<head>
	<title>NICE신용평가정보 가상주민번호 서비스</title>
	
	<script language='javascript'>
	window.name ="Parent_window";
	
	function fnPopup(){
		document.form_ipin.target = "popupCertKey";
		document.form_ipin.action = "https://cert.vno.co.kr/ipin.cb";
		document.form_ipin.submit();
	}
	</script>
</head>

<body onload="fnPopup();">

<!-- 가상주민번호 서비스 팝업을 호출하기 위해서는 다음과 같은 form이 필요합니다. -->
<form name="form_ipin" method="post">
	<input type="hidden" name="m" value="pubmain">						<!-- 필수 데이타로, 누락하시면 안됩니다. -->
    <input type="hidden" name="enc_data" value="<?= $sEncData ?>">		<!-- 위에서 업체정보를 암호화 한 데이타입니다. -->
    
    <!-- 업체에서 응답받기 원하는 데이타를 설정하기 위해 사용할 수 있으며, 인증결과 응답시 해당 값을 그대로 송신합니다.
    	 해당 파라미터는 추가하실 수 없습니다. -->
    <input type="hidden" name="param_r1" value="<?=urlencode($returnUrl)?>">
    <input type="hidden" name="param_r2" value="">
    <input type="hidden" name="param_r3" value="">
</form>

<!-- 가상주민번호 서비스 팝업 페이지에서 사용자가 인증을 받으면 암호화된 사용자 정보는 해당 팝업창으로 받게됩니다.
	 따라서 부모 페이지로 이동하기 위해서는 다음과 같은 form이 필요합니다. -->
<form name="vnoform" method="post">
	<input type="hidden" name="enc_data">								<!-- 인증받은 사용자 정보 암호화 데이타입니다. -->
	
	<!-- 업체에서 응답받기 원하는 데이타를 설정하기 위해 사용할 수 있으며, 인증결과 응답시 해당 값을 그대로 송신합니다.
    	 해당 파라미터는 추가하실 수 없습니다. -->
    <input type="hidden" name="param_r1" value="<?=urlencode($returnUrl)?>">
    <input type="hidden" name="param_r2" value="">
    <input type="hidden" name="param_r3" value="">
</form>

</body>
</html>
