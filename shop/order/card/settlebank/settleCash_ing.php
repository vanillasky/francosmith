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

include dirname(__FILE__).'/../../../conf/pg.settlebank.php';

if (class_exists('validation') && method_exists('validation', 'xssCleanArray')) {
	$_POST = validation::xssCleanArray($_POST, array(
		validation::DEFAULT_KEY	=> 'text'
	));
}
/****************************************************************************
*
* $IsDebug : 1:수신,전송 메세지 Print 0:사용안함
* $ENCRYPT : "C" 현금영수증
*
****************************************************************************/

$IsDebug = 0;
$ENCTYPE = 0;

/****************************************************************************
*
* 넘겨받을 데이타
*
****************************************************************************/

$Retailer_id = trim($pg['id']); //상점아이디
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
	$ordnm = iconv('euc-kr','utf-8',trim($_POST['Cust_no'])); //주문자명

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
	$Amtcash = trim($crdata['amount']); //거래금액
	$deal_won = trim($crdata['supply']); //공급가액
	$Amttex = trim($crdata['surtax']); //부가가치세
	$Amtadd = '0'; //봉사료
	$prod_nm = trim($crdata['goodsnm']); //상품명
	$prod_set = ''; //상품갯수
	$Gubun_cd = ($crdata['useopt'] == '0' ? '01' : '02'); //거래자구분
	$Confirm_no = trim($crdata['certno']); //신분확인번호
	$crno = $_GET['crno'];
	$ordnm = iconv('euc-kr','utf-8',$crdata['buyername']);	//주문자명
}
else if ($crdata['Pay_kind'] == 'cash-cncl')
{
	$Pay_kind = 'cash-cncl'; //결제종류
	$Pay_type = '1'; //결제방식 1.무통장임급, 2.계좌이체
	$Amtcash = trim($crdata['amount']); //거래금액
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

	$ENCTYPE = "0";

	/****************************************************************************
	*
	* 전송 전문 Make
	*
	****************************************************************************/
	if ($Gubun_cd == "01"){
		$purpose = "0";
		$identityGb = "4";
	}else{
		$purpose = "1";
		$identityGb = "3";
	}

	if($data['settlekind'] == 'a') {
		$transNo = $data['ordno'];
	} else {
		$transNo = $crdata['ordno'];
	}

	$sDataMsg  = "&mid=".$Retailer_id ;
	$sDataMsg .= "&assort=".$ENCTYPE;
	$sDataMsg .= "&trDt=".date('YmdHis');
	$sDataMsg .= "&trAmt=".$Amtcash;
	$sDataMsg .= "&purpose=".$purpose;
	$sDataMsg .= "&ordNm=".$ordnm;
	$sDataMsg .= "&identityGb=".$identityGb;
	$sDataMsg .= "&identity=".$Confirm_no;
	$sDataMsg .= "&transNo=".$transNo;
	$sDataMsg .= "&amt=".$deal_won;
	$sDataMsg .= "&taxYn=".(($set['receipt']['compType'] == '1')?'Y':'N');
	$sDataMsg .= "&vat=".$Amttex;

	/****************************************************************************
	*
	* 전송 메세지 프린트
	*
	****************************************************************************/

	$url="http://www.settlebank.co.kr/pgtrans/CashReceiptMultiAction.do?_method=insertReceiptInfo".$sDataMsg;

	if( $IsDebug == 1 )
	{
		echo $url."<br>";
	}

	/****************************************************************************
	*
	* 현금영수증 발급 작업
	*
	****************************************************************************/

	$ch = curl_init();
	if(!$ch) {
		/****연결 실패 ***/
		$Success = "n";
		$rResMsg = "연결 실패로 인한 실패";
	} else {
		/****연결 성공 ***/
		$rResMsg = "연결에 성공하였으므로 데이터를 받는다.";

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER,  0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);

		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//정보 조회
		$ret = curl_exec($ch);

		//에러 처리
		if( curl_error($ch)){
			$Success = "n";
			$rResMsg = "현금영수증 작업중 문제가 발생하였습니다. 관리자에게 문의하세요.";
		}

		//curl 세션닫기
		curl_close($ch);

	}

	/****************************************************************************
	*
	* 수신 메세지 프린트
	*
	****************************************************************************/

	if( $IsDebug == 1 )
	{
		print $ret."<br>";
	}

	$json = new Services_JSON();
	$sRecvMsg = get_object_vars($json->decode(stripslashes($ret)));

	if( $sRecvMsg['resultCd'] == '0000' )
	{
		/** 수신 데이터(길이) 체크 정상 **/

		$RecvValArray = array();
		$RecvValArray = explode( "|", $sRecvMsg );

		$rRetailer_id = $Retailer_id;
		$rDealno = $transNo;
		$rAdm_no = $sRecvMsg['authNo'];
		$rSuccess = $sRecvMsg['resultCd'];
		$rResMsg = iconv('utf-8','euc-kr',$sRecvMsg['resultMsg']);
	}
	else
	{
		/** 수신 데이터(길이) 체크 에러시 통신오류에 의한 승인 실패로 간주 **/

		$rSuccess = $sRecvMsg['resultCd'];
		$rResMsg = iconv('utf-8','euc-kr',$sRecvMsg['resultMsg']);

	}

	/****************************************************************************
	*
	* 수신 결과 저장 ,tid='{$rAdm_no}'
	*
	****************************************************************************/
	if( !strcmp($rSuccess,"0000")) // rSuccess "0000" 일때만 성공
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
			$db->query("update gd_cashreceipt set pg='settlebank',cashreceipt='{$rAdm_no}',receiptnumber='{$rAdm_no}',moddt=now(),status='ACK',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
			$db->query("update gd_order set cashreceipt='{$rAdm_no}' where ordno='{$ordno}'");
		}

		if (isset($_GET['crno']) === false)
		{
			msg('현금영수증이 정상발급되었습니다');
			echo '<script>parent.location.reload();</script>';
		}
	}
	else { // rSuccess 가 "0000" 아닐때는 에러, rResMsg 가 실패에 대한 메세지
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
			$db->query("update gd_cashreceipt set pg='settlebank',errmsg='{$rSuccess}:{$rResMsg}',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
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
	$ENCTYPE = "1";

	if ($Gubun_cd == "01"){
		$purpose = "0";
		$identityGb = "4";
	}else{
		$purpose = "1";
		$identityGb = "3";
	}

	if($data['settlekind'] == 'a') {
		$transNo = $data['ordno'];
	} else {
		$transNo = $crdata['ordno'];
	}

	$sDataMsg  = "&mid=".$Retailer_id ;
	$sDataMsg .= "&assort=".$ENCTYPE;
	$sDataMsg .= "&trDt=".date('YmdHis');
	$sDataMsg .= "&trAmt=".$Amtcash;
	$sDataMsg .= "&purpose=".$purpose;
	$sDataMsg .= "&identityGb=".$identityGb;
	$sDataMsg .= "&identity=".$Confirm_no;
	$sDataMsg .= "&transNo=".$transNo;

	/****************************************************************************
	*
	* 전송 메세지 프린트
	*
	****************************************************************************/

	$url="http://www.settlebank.co.kr/pgtrans/CashReceiptMultiAction.do?_method=insertReceiptInfo".$sDataMsg;


	if( $IsDebug == 1 )
	{
		print $url."<br>";
	}

	$ch = curl_init();
	if(!$ch) {
		/****연결 실패 ***/
		$Success = "n";
		$rResMsg = "연결 실패로 인한 실패";
	} else {
		/****연결 성공 ***/
		$rResMsg = "연결에 성공.";

		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER,  0);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

		//정보 조회
		$ret = curl_exec($ch);

		//에러 처리
		if( curl_error($ch)){
			$Success = "n";
			$rResMsg = "현금영수증 작업중 문제가 발생하였습니다. 관리자에게 문의하세요.";
		}

		//curl 세션닫기
		curl_close($ch);

	}

	/****************************************************************************
	*
	* 수신 메세지 프린트
	*
	****************************************************************************/

	if( $IsDebug == 1 )
	{
		print $ret."<br>";
	}

	$json = new Services_JSON();
	$sRecvMsg = get_object_vars($json->decode(stripslashes($ret)));

	if( $sRecvMsg['resultCd'] == '0000' )
	{
		/** 수신 데이터(길이) 체크 정상 **/

		$RecvValArray = array();
		$RecvValArray = explode( "|", $sRecvMsg );

		$rRetailer_id = $Retailer_id;
		$rDealno = $transNo;
		$rAdm_no = $sRecvMsg['authNo'];
		$rSuccess = $sRecvMsg['resultCd'];
		$rResMsg = iconv('utf-8','euc-kr',$sRecvMsg['resultMsg']);

	}
	else
	{
		/** 수신 데이터(길이) 체크 에러시 통신오류에 의한 승인 실패로 간주 **/

		$rSuccess = $sRecvMsg['resultCd'];
		$rResMsg = iconv('utf-8','euc-kr',$sRecvMsg['resultMsg']);

	}

	/****************************************************************************
	*
	* 수신 결과 저장
	*
	****************************************************************************/
	if( !strcmp($rSuccess,"0000")) // rSuccess "0000" 일때만 성공
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

?>