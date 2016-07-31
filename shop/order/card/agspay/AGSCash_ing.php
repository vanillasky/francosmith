<?php
if (isset($_GET['crno']) === false)
{
	include dirname(__FILE__).'/../../../lib/library.php';
	include dirname(__FILE__).'/../../../conf/config.pay.php';
	@include_once(dirname(__FILE__).'/../../../lib/cashreceipt.class.php');

	$ordno = $_POST['Order_no'];
	if(!is_object($cashreceipt) && class_exists('cashreceipt')) $cashreceipt = new cashreceipt();

	### 금액 데이타 유효성 체크
	$data = $cashreceipt->getCashReceiptCalCulate($ordno);
	if ($data['supply']!=$_POST['deal_won'] || $data['vat']!=$_POST['Amttex']) msg('금액이 일치하지 않습니다',-1);
}
else {
	$ordno = $crdata['ordno'];
}
include dirname(__FILE__).'/../../../conf/pg.agspay.php';

if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
	$_POST = validation::xssCleanArray($_POST, array(
		validation::DEFAULT_KEY	=> 'text'
	));
}

/****************************************************************************
*
* $IsDebug : 1:수신,전송 메세지 Print 0:사용안함
* $LOCALADDR : PG서버와 통신을 담당하는 암호화Process가 위치해 있는 IP (220.85.12.74)
* $LOCALPORT : 포트
* $ENCRYPT : "C" 현금영수증
* $CONN_TIMEOUT : 암호화 데몬과 접속 Connect타임아웃 시간(초)
* $READ_TIMEOUT : 데이터 수신 타임아웃 시간(초)
*
****************************************************************************/

$IsDebug = 0;
$LOCALADDR = "220.85.12.74";
$LOCALPORT = "29760";
$ENCTYPE = 0;
$CONN_TIMEOUT = 10;
$READ_TIMEOUT = 30;

/****************************************************************************
*
* AGSCash.html 로 부터 넘겨받을 데이타
*
****************************************************************************/

$Retailer_id = trim($pg['id']); //상점아이디
$Cat_id = '7005037001'; //단말기번호(단말기 번호는 7005037001 셋팅함 (수정불가))
$Ord_No = trim($ordno); //주문번호

if ($_POST['Pay_kind'] == 'cash-appr' && isset($_GET['crno']) === false)
{
	$Pay_kind = 'cash-appr'; //결제종류
	$Pay_type = trim($_POST['Pay_type']); //결제방식 1.무통장임급, 2.계좌이체
	$Cust_no = trim($_POST['Cust_no']); //회원아이디
	$Amtcash = trim($_POST['Amtcash']); //거래금액
	$deal_won = trim($_POST['deal_won']); //공급가액
	$Amttex = trim($_POST['Amttex']); //부가가치세
	$Amtadd = '0'; //봉사료
	$prod_nm = trim($_POST['prod_nm']); //상품명
	$prod_set = ''; //상품갯수
	$Gubun_cd = trim($_POST['Gubun_cd']); //거래자구분
	$Confirm_no = trim($_POST['Confirm_no']); //신분확인번호

	// 발급상태체크(기존시스템고려)
	if ($data['cashreceipt'] != '' && file_exists(dirname(__FILE__).'/../../../lib/cashreceipt.class.php') === false) {
		msg('현금영수증 발행요청실패!! \\n['.$ordno.'] 주문은 이미 발행되었습니다.');
		exit;
	}

	### 현금영수증신청내역 추가
	if (is_object($cashreceipt))
	{
		// 발급상태체크
		list($crno) = $db->fetch("select crno from gd_cashreceipt where ordno='{$ordno}' and status='ACK' order by crno desc limit 1");
		if ($crno != '') {
			msg('현금영수증 발행요청실패!! \\n['.$ordno.'] 주문은 이미 발행되었습니다.');
			exit;
		}

		$indata = array();
		$indata['ordno'] = $ordno;
		$indata['goodsnm'] = $prod_nm;
		$indata['buyername'] = $Cust_no;
		$indata['useopt'] = ($Gubun_cd == '01' ? '0' : '1');
		$indata['certno'] = $Confirm_no;
		$indata['amount'] = $Amtcash;
		$indata['supply'] = $deal_won;
		$indata['surtax'] = $Amttex;

		$crno = $cashreceipt->putReceipt($indata);
	}
}
else if ($crdata['Pay_kind'] == 'cash-appr')
{
	$Pay_kind = 'cash-appr'; //결제종류
	$Pay_type = '1'; //결제방식 1.무통장임급, 2.계좌이체
	$Cust_no = trim($crdata['buyername']); //회원아이디
	$Amtcash = trim($crdata['amount']); //거래금액
	$deal_won = trim($crdata['supply']); //공급가액
	$Amttex = trim($crdata['surtax']); //부가가치세
	$Amtadd = '0'; //봉사료
	$prod_nm = trim($crdata['goodsnm']); //상품명
	$prod_set = ''; //상품갯수
	$Gubun_cd = ($crdata['useopt'] == '0' ? '01' : '02'); //거래자구분
	$Confirm_no = trim($crdata['certno']); //신분확인번호
	$crno = $_GET['crno'];
}
else if ($crdata['Pay_kind'] == 'cash-cncl')
{
	$Pay_kind = 'cash-cncl'; //결제종류
	$Pay_type = '1'; //결제방식 1.무통장임급, 2.계좌이체
	$Cust_no = trim($crdata['buyername']); //회원아이디
	$Amtcash = trim($crdata['amount']); //거래금액
	$Amttex = trim($crdata['surtax']); //부가가치세
	$Amtadd = '0'; //봉사료
	$Gubun_cd = ($crdata['useopt'] == '0' ? '01' : '02'); //거래자구분
	$Confirm_no = trim($crdata['certno']); //신분확인번호
	$Org_adm_no = trim($crdata['receiptnumber']); //취소시 승인번호
}

/*******************************************************************************************
*
* Pay_kind = cash-appr" 현금영수증 승인요청시
*
******************************************************************************************/

if( strcmp( $Pay_kind, "cash-appr" ) == 0 )
{

	/**************************************************************
	* 승인요청시
	**************************************************************/

	$ENCTYPE = "C";

	/****************************************************************************
	*
	* 전송 전문 Make
	*
	****************************************************************************/

	$sDataMsg = $ENCTYPE.
		$Pay_kind."|".
		$Pay_type."|".
		$Retailer_id."|".
		$Cust_no."|".
		$Ord_No."|".
		$Cat_id."|".
		$Amtcash."|".
		$Amttex."|".
		$Amtadd."|".
		$Gubun_cd."|".
		$Confirm_no."|".
		$Email."|".
		$prod_nm."|";

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

		$Success = "n";
		$rResMsg = "연결 실패로 인한 실패";
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

		$rRetailer_id = $RecvValArray[0];
		$rDealno = $RecvValArray[1];
		$rAdm_no = $RecvValArray[2];
		$rSuccess = $RecvValArray[3];
		$rResMsg = $RecvValArray[4];
		$rAlert_msg1 = $RecvValArray[5];
		$rAlert_msg2 = $RecvValArray[6];

	}
	else
	{
		/** 수신 데이터(길이) 체크 에러시 통신오류에 의한 승인 실패로 간주 **/

		$Success = "n";
		$rResMsg = "수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패";

	}

	/****************************************************************************
	*
	* 수신 결과 저장
	*
	****************************************************************************/
	if( !strcmp($rSuccess,"y") && strcmp($Success,"n") ) // rSuccess "y" 일때만 성공
	{
		$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
		$settlelog .= '-----------------------------------'."\n";
		$settlelog .= '현금영수증 발급 성공'."\n";
		$settlelog .= '결과코드 : '.$rSuccess."\n";
		$settlelog .= '결과내용 : '.$rResMsg."\n";
		$settlelog .= '업체ID   : '.$rRetailer_id."\n";
		$settlelog .= '주문번호 : '.$rDealno."\n";
		$settlelog .= '승인번호 : '.$rAdm_no."\n";
		$settlelog .= '-----------------------------------'."\n";

		if (empty($crno) === true)
		{
			$db->query("update gd_order set cashreceipt='{$rAdm_no}',settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
		}
		else {
			# 현금영수증신청내역 수정
			$db->query("update gd_cashreceipt set pg='agspay',cashreceipt='{$rAdm_no}',receiptnumber='{$rAdm_no}',moddt=now(),status='ACK',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
			$db->query("update gd_order set cashreceipt='{$rAdm_no}' where ordno='{$ordno}'");
		}

		if (isset($_GET['crno']) === false)
		{
			msg('현금영수증이 정상발급되었습니다');
			echo '<script>parent.location.reload();</script>';
		}
	}
	else { // rSuccess 가 "y" 아닐때는 에러, rResMsg 가 실패에 대한 메세지
		$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
		$settlelog .= '-----------------------------------'."\n";
		$settlelog .= '현금영수증 발급 실패'."\n";
		$settlelog .= '결과코드 : '.$rSuccess."\n";
		$settlelog .= '결과내용 : '.$rResMsg."\n";
		$settlelog .= '-----------------------------------'."\n";

		if (empty($crno) === true)
		{
			$db->query("update gd_order set settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
		}
		else {
			# 현금영수증신청내역 수정
			$db->query("update gd_cashreceipt set pg='agspay',errmsg='{$rSuccess}:{$rResMsg}',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
		}

		if (isset($_GET['crno']) === false)
		{
			msg($rResMsg);
			exit;
		}
	}
}

/*******************************************************************************************
*
* Pay_kind = "cash-cncl" 현금영수증 취소요청시
*
******************************************************************************************/

if( strcmp( $Pay_kind, "cash-cncl" ) == 0 )
{
	/**************************************************************
	* 취소요청시
	**************************************************************/

	$ENCTYPE = "C";

	/****************************************************************************
	*
	* 전송 전문 Make
	*
	****************************************************************************/

	$sDataMsg = $ENCTYPE.
		$Pay_kind."|".
		$Pay_type."|".
		$Retailer_id."|".
		$Cust_no."|".
		$Ord_No."|".
		$Cat_id."|".
		$Amtcash."|".
		$Amttex."|".
		$Amtadd."|".
		$Gubun_cd."|".
		$Confirm_no."|".
		$Org_adm_no."|".
		$Email."|".
		$prod_nm."|";


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

		$Success = "n";
		$rResMsg = "연결 실패로 인한 실패";
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

		$rRetailer_id = $RecvValArray[0];
		$rDealno = $RecvValArray[1];
		$rAdm_no = $RecvValArray[2];
		$rSuccess = $RecvValArray[3];
		$rResMsg = $RecvValArray[4];
		$rAlert_msg1 = $RecvValArray[5];
		$rAlert_msg2 = $RecvValArray[6];

	}
	else
	{
		/** 수신 데이터(길이) 체크 에러시 통신오류에 의한 승인 실패로 간주 **/

		$Success = "n";
		$rResMsg = "수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패";

	}

	/****************************************************************************
	*
	* 수신 결과 저장
	*
	****************************************************************************/
	if( !strcmp($rSuccess,"y") && strcmp($Success,"n") ) // rSuccess "y" 일때만 성공
	{
		$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
		$settlelog .= '-----------------------------------'."\n";
		$settlelog .= '현금영수증 취소 성공'."\n";
		$settlelog .= '결과코드 : '.$rSuccess."\n";
		$settlelog .= '결과내용 : '.$rResMsg."\n";
		$settlelog .= '업체ID   : '.$rRetailer_id."\n";
		$settlelog .= '주문번호 : '.$rDealno."\n";
		$settlelog .= '승인번호 : '.$rAdm_no."(".$Org_adm_no.")\n";
		$settlelog .= '-----------------------------------'."\n";

		$db->query("update gd_cashreceipt set moddt=now(),status='CCR',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'{$settlelog}') where crno='{$_GET['crno']}'");
	}
	else { // rSuccess 가 "y" 아닐때는 에러, rResMsg 가 실패에 대한 메세지
		$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
		$settlelog .= '-----------------------------------'."\n";
		$settlelog .= '현금영수증 취소 실패'."\n";
		$settlelog .= '결과코드 : '.$rSuccess."\n";
		$settlelog .= '결과내용 : '.$rResMsg."\n";
		$settlelog .= '-----------------------------------'."\n";

		$db->query("update gd_cashreceipt set errmsg='{$rSuccess}:{$rResMsg}',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$_GET['crno']}'");
	}
}

/*******************************************************************************************
*
* Pay_kind = cash-appr-temp" 현금영수증 임시승인저장요청시
*
******************************************************************************************/

if( strcmp( $Pay_kind, "cash-appr-temp" ) == 0 )
{

	/**************************************************************
	* 승인요청시
	**************************************************************/

	$ENCTYPE = "C";

	/****************************************************************************
	*
	* 전송 전문 Make
	*
	****************************************************************************/

	$sDataMsg = $ENCTYPE.
		$Pay_kind."|".
		$Pay_type."|".
		$Retailer_id."|".
		$Cust_no."|".
		$Ord_No."|".
		$Cat_id."|".
		$Amtcash."|".
		$Amttex."|".
		$Amtadd."|".
		$Gubun_cd."|".
		$Confirm_no."|".
		$Email."|".
		$prod_nm."|";


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

		$Success = "n";
		$rResMsg = "연결 실패로 인한 실패";
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

		$rRetailer_id = $RecvValArray[0];
		$rDealno = $RecvValArray[1];
		$rSuccess = $RecvValArray[2];
		$rResMsg = $RecvValArray[3];

	}
	else
	{
		/** 수신 데이터(길이) 체크 에러시 통신오류에 의한 승인 실패로 간주 **/

		$Success = "n";
		$rResMsg = "수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패";

	}
}

/*******************************************************************************************
*
* Pay_kind = "cash-cncl-temp" 현금영수증 취소요청시
*
******************************************************************************************/

if( strcmp( $Pay_kind, "cash-cncl-temp" ) == 0 )
{
	/**************************************************************
	* 취소요청시
	**************************************************************/

	$ENCTYPE = "C";

	/****************************************************************************
	*
	* 전송 전문 Make
	*
	****************************************************************************/

	$sDataMsg = $ENCTYPE.
		$Pay_kind."|".
		$Pay_type."|".
		$Retailer_id."|".
		$Cust_no."|".
		$Ord_No."|".
		$Cat_id."|".
		$Amtcash."|".
		$Amttex."|".
		$Amtadd."|".
		$Gubun_cd."|".
		$Confirm_no."|".
		$Org_adm_no."|".
		$Email."|".
		$prod_nm."|";


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

		$Success = "n";
		$rResMsg = "연결 실패로 인한 실패";
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

		$rRetailer_id = $RecvValArray[0];
		$rDealno = $RecvValArray[1];
		$rSuccess = $RecvValArray[2];
		$rResMsg = $RecvValArray[3];

	}
	else
	{
		/** 수신 데이터(길이) 체크 에러시 통신오류에 의한 승인 실패로 간주 **/

		$Success = "n";
		$rResMsg = "수신 데이터(길이) 체크 에러 통신오류에 의한 승인 실패";

	}
}
?>