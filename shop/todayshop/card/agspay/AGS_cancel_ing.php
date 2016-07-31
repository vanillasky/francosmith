<?php
/**********************************************************************************************
*
* 파일명 : AGS_cancel_ing.php
* 작성일자 : 2006/08/03
* 
* 올더게이트 플러그인에서 리턴된 데이타를 받아서 소켓취소요청을 합니다.
*
* Copyright 2005-2006 AEGISHYOSUNG.Co.,Ltd. All rights reserved.
*
**********************************************************************************************/ 

/** Function Library **/ 
require "aegis_Func.php";


/****************************************************************************
*
* [1] 올더게이트 결제시 사용할 로컬 통신서버 IP/Port 번호
*
* $IsDebug : 1:수신,전송 메세지 Print 0:사용안함
* $LOCALADDR : PG서버와 통신을 담당하는 암호화Process가 위치해 있는 IP 
* $LOCALPORT : 포트
* $ENCRYPT : 0:안심클릭,일반결제 2:ISP
* $CONN_TIMEOUT : 암호화 데몬과 접속 Connect타임아웃 시간(초)
* $READ_TIMEOUT : 데이터 수신 타임아웃 시간(초)
*
****************************************************************************/

$IsDebug = 0;
$LOCALADDR = "220.85.12.3";
$LOCALPORT = "29760";
$ENCTYPE = 0;
$CONN_TIMEOUT = 10;
$READ_TIMEOUT = 30;


/****************************************************************************
*
* [2] AGS_cancel.html 로 부터 넘겨받을 데이타
*
****************************************************************************/

$AuthTy = trim($_POST["AuthTy"]);				//결제형태

$SubTy = trim($_POST["SubTy"]);					//서브결제형태

$StoreId = trim($_POST["StoreId"]);				//상점아이디

$ApprNo = trim($_POST["ApprNo"]);				//승인번호

$ApprTm = trim($_POST["ApprTm"]);				//승인시간

$DealNo = trim($_POST["DealNo"]);				//거래고유번호

$CardNo = trim($_POST["CardNo"]);				//카드번호

/****************************************************************************
* 
* SubTy = "isp" 안전결제ISP
* SubTy = "visa3d" 안심클릭
* SubTy = "normal" 일반결제
*
****************************************************************************/

if( strcmp( $SubTy, "isp" ) == 0 )
{
	/****************************************************************************
	*
	* [3] 신용카드승인취소 - ISP
	*
	* -- 이부분은 취소 승인 처리를 위해 PG서버Process와 Socket통신하는 부분이다.
	* 가장 핵심이 되는 부분이므로 수정후에는 실제 서비스전까지 적절한 테스트를 하여야 한다.
	* -- 데이터 길이는 매뉴얼 참고
	*	    
	* -- 취소 승인 요청 전문 포멧
	* + 데이터길이(6) + 암호화여부(1) + 데이터
	* + 데이터 포멧(데이터 구분은 "|"로 한다.
	* 결제종류(6)	| 업체아이디(20) 	| 승인번호(20) 	| 승인시간(8)	| 거래고유번호(6) |
	*
	* -- 취소 승인 응답 전문 포멧
	* + 데이터길이(6) + 데이터
	* + 데이터 포멧(데이터 구분은 "|"로 한다.
	* 업체ID(20)	| 승인번호(20)	| 승인시각(8)	| 전문코드(4)	| 거래고유번호(6)	| 성공여부(1)	|
	*		   
	****************************************************************************/
	
	$ENCTYPE = 2;
	
	/****************************************************************************
	* 
	* 전송 전문 Make
	* 
	****************************************************************************/
		
	$sDataMsg = $ENCTYPE.
		$AuthTy."|".
		$StoreId."|".
		$ApprNo."|".
		$ApprTm."|".
		$DealNo."|";

	$sSendMsg = sprintf( "%06d%s", strlen( $sDataMsg ), $sDataMsg );

	/****************************************************************************
	* 
	* 전송 메세지 프린트
	* 
	****************************************************************************/
	
	if( $IsDebug == 1 )	
	{
		print $sSendMsg."<br>";
	}
	
	/****************************************************************************
	* 
	* 암호화Process와 연결을 하고 승인 데이터 송수신
	* 
	****************************************************************************/
	
    	$fp = fsockopen( $LOCALADDR, $LOCALPORT , &$errno, &$errstr, $CONN_TIMEOUT );
	
	
	if( !$fp )
	{
		/** 연결 실패로 인한 승인실패 메세지 전송 **/
		
		$rSuccYn = "n";
		$rResMsg = "연결 실패로 인한 승인실패";
		
	}
	else 
	{
		/** 승인 전문을 암호화Process로 전송 **/
		
		fputs( $fp, $sSendMsg );

		socket_set_timeout($fp, $READ_TIMEOUT);

		/** 최초 6바이트를 수신해 데이터 길이를 체크한 후 데이터만큼만 받는다. **/
		
		$sRecvLen = fgets( $fp, 7 );
		$sRecvMsg = fgets( $fp, $sRecvLen + 1 );
		
		/****************************************************************************
		* 데이터 값이 정상적으러 넘어가지 않을 경우 이부분을 수정하여 주시기 바랍니다.
		* PHP 버전에 따라 수신 데이터 길이 체크시 페이지오류가 발생할 수 있습니다
		* 에러메세지:수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패
		* 데이터 길이 체크 오류시 아래와 같이 변경하여 사용하십시오
		* $sRecvLen = fgets( $fp, 6 );
		* $sRecvMsg = fgets( $fp, $sRecvLen );
		*
		****************************************************************************/
	
		/** 소켓 close **/
		
		fclose( $fp );
	}
	
	/****************************************************************************
	* 
	* 수신 메세지 프린트
	* 
	****************************************************************************/
	
	if( $IsDebug == 1 )	
	{
		print $sRecvMsg."<br>";
	}
	
	if( strlen( $sRecvMsg ) == $sRecvLen )
	{
		/** 수신 데이터(길이) 체크 정상 **/
		
		$RecvValArray = array();
		$RecvValArray = explode( "|", $sRecvMsg );
	
		/** null 또는 NULL 문자, 0 을 공백으로 변환
		for( $i = 0; $i < sizeof( $RecvValArray); $i++ )
		{
			$RecvValArray[$i] = trim( $RecvValArray[$i] );
			
			if( !strcmp( $RecvValArray[$i], "null" ) || !strcmp( $RecvValArray[$i], "NULL" ) )
			{
				$RecvValArray[$i] = "";
			}
			
			if( IsNumber( $RecvValArray[$i] ) )
			{
				if( $RecvValArray[$i] == 0 ) $RecvValArray[$i] = "";
			}
		} **/
		
		$rStoreId = $RecvValArray[0];
		$rApprNo = $RecvValArray[1];
		$rApprTm = $RecvValArray[2];
		$rBusiCd = $RecvValArray[3];
		$rDealNo = $RecvValArray[4];
		$rSuccYn = $RecvValArray[5];
		$rResMsg = $RecvValArray[6];
		
		/****************************************************************************
		*
		* 신용카드결제(ISP) 취소결과가 정상적으로 수신되었으므로 DB 작업을 할 경우 
		* 결과페이지로 데이터를 전송하기 전 이부분에서 하면된다.
		*
		* 여기서 DB 작업을 해 주세요.
		* 주의) $rSuccYn 값이 'y' 일경우 신용카드취소성공
		* 주의) $rSuccYn 값이 'n' 일경우 신용카드취소실패
		* DB 작업을 하실 경우 $rSuccYn 값이 'y' 또는 'n' 일경우에 맞게 작업하십시오. 
		*
		****************************************************************************/
			
			
		
		
		
		
		
	}
	else
	{
		/** 수신 데이터(길이) 체크 에러시 통신오류에 의한 승인 실패로 간주 **/
		
		$rSuccYn = "n";
		$rResMsg = "수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패";
		
	}
	
}
else if( ( strcmp( $SubTy, "visa3d" ) == 0 ) || ( strcmp( $SubTy, "normal" ) == 0 ) )
{
	/****************************************************************************
	*
	* [4] 신용카드승인취소 - VISA3D, 일반
	*
	* -- 이부분은 취소 승인 처리를 위해 암호화Process와 Socket통신하는 부분이다.
	* 가장 핵심이 되는 부분이므로 수정후에는 실제 서비스전까지 적절한 테스트를 하여야 한다.
	*
	* -- 취소 승인 요청 전문 포멧
	* + 데이터길이(6) + 암호화여부(1) + 데이터
	* + 데이터 포멧(데이터 구분은 "|"로 하며 카드번호,유효기간,비밀번호,주민번호는 암호화된다.)
	* 결제종류(6)	| 업체아이디(20) 	| 승인번호(8) 	| 승인시간(14) 	| 카드번호(16) 	|
	*
	* -- 취소 승인 응답 전문 포멧
	* + 데이터길이(6) + 데이터
	* + 데이터 포멧(데이터 구분은 "|"로 하며 암호화Process에서 해독된후 실데이터를 수신하게 된다.
	* 업체ID(20)	| 승인번호(8)	| 승인시각(14)	| 전문코드(4)	| 성공여부(1)	|
	* 주문번호(20)	| 할부개월(2)	| 결제금액(20)	| 카드사명(20)	| 카드사코드(4) 	|
	* 가맹점번호(15)	| 매입사코드(4)	| 매입사명(20)	| 전표번호(6)
	*		   
	****************************************************************************/
	
	$ENCTYPE = 0;
	
	/****************************************************************************
	* 
	* 전송 전문 Make
	* 
	****************************************************************************/
	
	$sDataMsg = $ENCTYPE.
		$AuthTy."|".
		$StoreId."|".
		$ApprNo."|".
		$ApprTm."|".
		encrypt_aegis($CardNo)."|";

	$sSendMsg = sprintf( "%06d%s", strlen( $sDataMsg ), $sDataMsg );
	
	/****************************************************************************
	* 
	* 전송 메세지 프린트
	* 
	****************************************************************************/
	
	if( $IsDebug == 1 )	
	{
		print $sSendMsg."<br>";
	}

	/****************************************************************************
	* 
	* 암호화Process와 연결을 하고 승인 데이터 송수신
	* 
	****************************************************************************/
	
    	$fp = fsockopen( $LOCALADDR, $LOCALPORT , &$errno, &$errstr, $CONN_TIMEOUT );
	
	
	if( !$fp )
	{
		/** 연결 실패로 인한 승인실패 메세지 전송 **/
		
		$rSuccYn = "n";
		$rResMsg = "연결 실패로 인한 승인실패";
		
	}
	else 
	{
		/** 승인 전문을 암호화Process로 전송 **/
		
		fputs( $fp, $sSendMsg );

		socket_set_timeout($fp, $READ_TIMEOUT);

		/** 최초 6바이트를 수신해 데이터 길이를 체크한 후 데이터만큼만 받는다. **/
		
		$sRecvLen = fgets( $fp, 7 );
		$sRecvMsg = fgets( $fp, $sRecvLen + 1 );
		
		/****************************************************************************
		* 데이터 값이 정상적으러 넘어가지 않을 경우 이부분을 수정하여 주시기 바랍니다.
		* PHP 버전에 따라 수신 데이터 길이 체크시 페이지오류가 발생할 수 있습니다
		* 에러메세지:수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패
		* 데이터 길이 체크 오류시 아래와 같이 변경하여 사용하십시오
		* $sRecvLen = fgets( $fp, 6 );
		* $sRecvMsg = fgets( $fp, $sRecvLen );
		*
		****************************************************************************/
		
		/** 소켓 close **/
		
		fclose( $fp );
	}
	
	/****************************************************************************
	* 
	* 수신 메세지 프린트
	* 
	****************************************************************************/
	
	if( $IsDebug == 1 )	
	{
		print $sRecvMsg."<br>";
	}
	
	if( strlen( $sRecvMsg ) == $sRecvLen )
	{
		/** 수신 데이터(길이) 체크 정상 **/
		
		$RecvValArray = array();
		$RecvValArray = explode( "|", $sRecvMsg );
	
		/** null 또는 NULL 문자, 0 을 공백으로 변환
		for( $i = 0; $i < sizeof( $RecvValArray); $i++ )
		{
			$RecvValArray[$i] = trim( $RecvValArray[$i] );
			
			if( !strcmp( $RecvValArray[$i], "null" ) || !strcmp( $RecvValArray[$i], "NULL" ) )
			{
				$RecvValArray[$i] = "";
			}
			
			if( IsNumber( $RecvValArray[$i] ) )
			{
				if( $RecvValArray[$i] == 0 ) $RecvValArray[$i] = "";
			}
		} **/
		
		$rStoreId = $RecvValArray[0];
		$rApprNo = $RecvValArray[1];
		$rApprTm = $RecvValArray[2];
		$rBusiCd = $RecvValArray[3];
		$rSuccYn = $RecvValArray[4];
		$rOrdNo = $RecvValArray[5];
		$rInstmt = $RecvValArray[6];
		$rAmt = $RecvValArray[7];
		$rCardNm = $RecvValArray[8];
		$rCardCd = $RecvValArray[9];
		$rMembNo = $RecvValArray[10];
		$rAquiCd = $RecvValArray[11];
		$rAquiNm = $RecvValArray[12];
		$rBillNo = $RecvValArray[13];
		
		/****************************************************************************
		*
		* 신용카드결제(안심클릭, 일반결제) 취소결과가 정상적으로 수신되었으므로 DB 작업을 할 경우 
		* 결과페이지로 데이터를 전송하기 전 이부분에서 하면된다.
		*
		* 여기서 DB 작업을 해 주세요.
		* 주의) $rSuccYn 값이 'y' 일경우 신용카드취소성공
		* 주의) $rSuccYn 값이 'n' 일경우 신용카드취소실패
		* DB 작업을 하실 경우 $rSuccYn 값이 'y' 또는 'n' 일경우에 맞게 작업하십시오. 
		*
		****************************************************************************/
			
			
		
		
		
		
		
	}
	else
	{
		/** 수신 데이터(길이) 체크 에러시 통신오류에 의한 승인 실패로 간주 **/
		
		$rSuccYn = "n";
		$rResMsg = "수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패";
		
	}
	
}
?>
<html>
<head>
</head>
<body onload="javascript:frmAGS_cancel_ing.submit();">
<form name=frmAGS_cancel_ing method=post action=AGS_cancel_result.php>
<input type=hidden name=rStoreId value="<?=$rStoreId?>">
<input type=hidden name=rApprNo value="<?=$rApprNo?>">
<input type=hidden name=rApprTm value="<?=$rApprTm?>">
<input type=hidden name=rBusiCd value="<?=$rBusiCd?>">
<input type=hidden name=rSuccYn value="<?=$rSuccYn?>">
<input type=hidden name=rResMsg value="<?=$rResMsg?>">
<input type=hidden name=rOrdNo value="<?=$rOrdNo?>">
<input type=hidden name=rInstmt value="<?=$rInstmt?>">
<input type=hidden name=rAmt value="<?=$rAmt?>">
<input type=hidden name=rCardNm value="<?=$rCardNm?>">
<input type=hidden name=rCardCd value="<?=$rCardCd?>">
<input type=hidden name=rMembNo value="<?=$rMembNo?>">
<input type=hidden name=rAquiCd value="<?=$rAquiCd?>">
<input type=hidden name=rAquiNm value="<?=$rAquiNm?>">
<input type=hidden name=rBillNo value="<?=$rBillNo?>">
</form>
</body>
</html>
