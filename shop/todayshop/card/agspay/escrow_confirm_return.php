<?php
/**********************************************************************************************
*
* 파일명 : AGS_escrow_ing.php
* 작성일자 : 2009/3/20
*
* 리턴된 데이타를 받아서 소켓결제요청을 합니다.
*
* Copyright AEGIS ENTERPRISE.Co.,Ltd. All rights reserved.
*
**********************************************************************************************/

include "../../../lib/library.php";
include "../../../conf/config.php";
//include "../../../conf/pg.$cfg[settlePg].php";
include "../../../conf/pg.escrow.php";

// 투데이샵 사용중인 경우 PG 설정 교체
resetPaymentGateway();

// 주문정보
$ordno = $_POST['ordno'];
$query = "
select
	orddt,settlekind,escrowno
from
	".GD_ORDER." a
	left join ".GD_LIST_DELIVERY." b on a.deliveryno = b.deliveryno
where
	a.ordno = '$ordno'
";
$data = $db->fetch($query);
$pg_settlekind	= array(
	'c'	=> '01',
	'o'	=> '02',
	'v'	=> '03',
);

/** Function Library **/
require "aegis_Func.php";


/****************************************************************************
*
* [1] 올더게이트 에스크로 결제시 사용할 로컬 통신서버 IP/Port 번호
*
* $IsDebug : 1:수신,전송 메세지 Print 0:사용안함
* $LOCALADDR : 올더게이트 서버와 통신을 담당하는 암호화Process가 위치해 있는 IP (220.85.12.74)
* $LOCALPORT : 포트
* $ENCTYPE : E : 올더게이트 에스크로
* $CONN_TIMEOUT : 암호화 데몬과 접속 Connect타임아웃 시간(초)
* $READ_TIMEOUT : 데이터 수신 타임아웃 시간(초)
*
****************************************************************************/

$IsDebug = 0;
$LOCALADDR  = "220.85.12.74";
$LOCALPORT  = "29760";
$ENCTYPE    = "E";
$CONN_TIMEOUT = 10;
$READ_TIMEOUT = 30;


/****************************************************************************
*
* [2] AGS_escrow.html 로 부터 넘겨받을 데이타
*
****************************************************************************/
$TrCode = trim('E200');												//거래코드
$PayKind = trim($pg_settlekind[$data['settlekind']]);				//결제종류
$RetailerId = trim($pg['id']);										//상점ID
$DealTime = trim(str_replace('-','',substr($data['orddt'],0,10)));	//결제일자
$SendNo = trim($data['escrowno']);									//거래고유번호
$IdNo = $_POST['id_no'];											//주민등록번호

/****************************************************************************
*
* [3] 데이타의 유효성을 검사합니다.
*
****************************************************************************/

$ERRMSG = "";

if( empty( $TrCode ) || $TrCode == "" )
{
	$ERRMSG .= "거래코드 입력여부 확인요망 <br>";		//거래코드
}

if( empty( $PayKind ) || $PayKind == "" )
{
	$ERRMSG .= "결제종류 입력여부 확인요망 <br>";		//결제종류
}

if( empty( $RetailerId ) || $RetailerId == "" )
{
	$ERRMSG .= "상점아이디 입력여부 확인요망 <br>";		//상점아이디
}

if( empty( $DealTime ) || $DealTime == "" )
{
	$ERRMSG .= "결제일자 입력여부 확인요망 <br>";		//결제시간
}

if( empty( $SendNo ) || $SendNo == "" )
{
	$ERRMSG .= "거래고유번호 입력여부 확인요망 <br>";	//거래고유번호
}


if( strlen($ERRMSG) == 0 )
{
	/****************************************************************************
	* TrCode = "E100" 발송완료
	* TrCode = "E200" 구매확인
	* TrCode = "E300" 구매거절
	* TrCode = "E400" 결제취소
	****************************************************************************/

	/****************************************************************************
	*
	* [4] 발송완료/구매확인/구매거절/결제취소요청 (E100/E101)/(E200/E201)/(E300/E301)/(E400/E401)
	*
	* -- 데이터 길이는 매뉴얼 참고
	*
	* -- 발송완료 요청 전문 포멧
	* + 데이터길이(6) + 자체 ESCROW 구분(1) + 데이터
	* + 데이터 포멧(데이터 구분은 "|"로 한다.)
	* 거래코드(10)	| 결제종류(2)	| 업체ID(20)	| 주민등록번호(13) |
	* 결제일자(8)	| 거래고유번호(6)	|
	*
	* -- 발송완료 응답 전문 포멧
	* + 데이터길이(6) + 데이터
	* + 데이터 포멧(데이터 구분은 "|"로 한다.
	* 거래코드(10)	|결제종류(2)	| 업체ID(20)	| 결과코드(2)	| 결과 메시지(100)	|
	*
	*****************************************************************************/

	$ENCTYPE = "E";

	/****************************************************************************
	* 전송 전문 Make
	****************************************************************************/

	$sDataMsg = $ENCTYPE.
		$TrCode."|".
		$PayKind."|".
		$RetailerId."|".
		$IdNo."|".
		$DealTime."|".
		$SendNo."|";

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
		/** 연결 실패로 인한 거래실패 메세지 전송 **/

		$rSuccYn = "n";
		$rResMsg = "연결 실패로 인한 거래실패";
	}
	else
	{
		/** 연결에 성공하였으므로 데이터를 받는다. **/

		$rResMsg = "연결에 성공하였으므로 데이터를 받는다.";


		/** 승인 전문을 암호화Process로 전송 **/

		fputs( $fp, $sSendMsg );

		socket_set_timeout($fp, $READ_TIMEOUT);

		/** 최초 6바이트를 수신해 데이터 길이를 체크한 후 데이터만큼만 받는다. **/

		$sRecvLen = fgets( $fp, 7 );
		$sRecvMsg = fgets( $fp, $sRecvLen + 1 );

		/****************************************************************************
		*
		* 데이터 값이 정상적으로 넘어가지 않을 경우 이부분을 수정하여 주시기 바랍니다.
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

		$rTrCode        = $RecvValArray[0];
		$rPayKind       = $RecvValArray[1];
		$rRetailerId    = $RecvValArray[2];
		$rSuccYn        = $RecvValArray[3];
		$rResMsg        = $RecvValArray[4];

		/****************************************************************************
		*
		* 에스크로 통신 결과가 정상적으로 수신되었으므로 DB 작업을 할 경우
		* 결과페이지로 데이터를 전송하기 전 이부분에서 하면된다.
		*
		* TrCode = "E101" 발송완료응답
		* TrCode = "E201" 구매확인응답
		* TrCode = "E301" 구매거절응답
		* TrCode = "E401" 취소요청응답
		*
		* 여기서 DB 작업을 해 주세요.
		* 주의) $rSuccYn 값이 'y' 일경우 에스크로배송등록및구매확인성공
		* 주의) $rSuccYn 값이 'n' 일경우 에스크로배송등록및구매확인실패
		* DB 작업을 하실 경우 $rSuccYn 값이 'y' 또는 'n' 일경우에 맞게 작업하십시오.
		*
		****************************************************************************/

		// 정상처리되었을때 DB 처리
		$db->query("update ".GD_ORDER." set escrowconfirm=2 where ordno='$ordno'");
	}
	else
	{
		/** 수신 데이터(길이) 체크 에러시 통신오류에 의한 승인 실패로 간주 **/

		$rSuccYn = "n";
		$rResMsg = "수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패";
	}
}
else
{
	$rSuccYn = "n";
	$rResMsg = $ERRMSG;
}

?>
<html>
<head>
<title>올더게이트</title>
<style type="text/css">
<!--
body { font-family:"돋움"; font-size:9pt; color:#000000; font-weight:normal; letter-spacing:0pt; line-height:180%; }
td { font-family:"돋움"; font-size:9pt; color:#000000; font-weight:normal; letter-spacing:0pt; line-height:180%; vertical-align:top; }
th { font-family:"돋움"; font-size:9pt; color:#000000; letter-spacing:0pt; line-height:180%; vertical-align:top; }
.clsleft { padding:0 10px; text-align:left; }
-->
</style>
</head>
<body topmargin="0" leftmargin="0" rightmargin="0" bottommargin="0">
<table width="100%" border="0" cellpadding="0" cellspacing="0">
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td><hr></td>
</tr>
<tr>
	<td align="center"><b>올더게이트 에스크로 거래 구매확인 요청 결과</b></td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
<tr>
	<td align="center">
	<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<th class="clsleft">☞ 에스크로 유형</th>
		<td>구매확인</td>
	</tr>
	<tr>
		<th class="clsleft">☞ 결과내용</th>
		<td><?php echo $rResMsg; ?></td>
	</tr>
	<tr>
		<th class="clsleft">☞ 결과코드</th>
		<td><?php echo $rSuccYn; ?></td>
	</tr>
	</table>
	</td>
</tr>
<tr>
	<td><hr></td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
</table>
</body>
</html>