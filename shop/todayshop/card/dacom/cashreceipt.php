<?php
if (isset($_GET['crno']) === false)
{
	include dirname(__FILE__).'/../../../lib/library.php';
	include dirname(__FILE__).'/../../../conf/config.pay.php';
}
include dirname(__FILE__).'/../../../conf/config.php';
include dirname(__FILE__).'/../../../conf/pg.dacom.php';

/********************************************************************************************************

 * 현금영수증 발급/취소 PHP 연동설명 및 예제


 1, 현금영수증 발급/취소 요청 파라미터
 ============ ====================================================================
  파라미터명		설명
 ============ ====================================================================
 mid			데이콤에서 발급한 상점아이디
 oid			주문번호
 paytype		결제수단 : SC0030(계좌이체), SC0040(무통장입금), SC0100(단독)
 usertype		용도 : 1(소득공제용), 2(지출증빙용)
 ssn			현금영수증 발급 정보 , 주민등록번호 또는 사업자번호 또는 전화번호등
 amount			발급(취소)금액
 bussinessno	현금영수증 발급 사업자번호
 method			종류 : auth(발급), cancel(취소)
 ret_url	    defaul(고정) : NONE
 hashdata		해쉬데이타(무결성검증필드)  :   md5($mid.$oid.$mertkey)
 ============ ====================================================================

 2. 결과 파라미터
 ============ ====================================================================
 파라미터명			설명
 ============ ====================================================================
 mid				데이콤에서 발급한 상점아이디
 oid				주문번호
 paytype			결제수단 - SC0030(계좌이체), SC0040(무통장입금), SC0100(단독)
 receiptnumber		현금영수증 승인번호
 respcode			결과코드 ('0000' : 성공,  그외 : 실패)
 respmsg			결과메시지
 ============ ====================================================================
 ==> 요청 결과 형식
   형 식 )  name|value^name|value^name|value^name|value
   예) mid|tdacomts1^oid|20080306-1^paytype|SC0030^receiptnumber|null^respcode|0000^respmsg|성공


 주의) 1. 현금영수증 발급 (단독만 가능)
          결제수단은 단독 발급만 가능 합니다. (SC0100)

       2. 현금영수증 취소
          필수: 주문번호(oid), 금액(amount), 상점아이디(mid), paytype, hashdata, ret_url, method

******************************************************************************************************/

// 결제 요청 URL
// 서비스용 : http://pg.dacom.net/common/cashreceipt.jsp
// 테스트용 : http://pg.dacom.net:7080/common/cashreceipt.jsp

$service_url = 'http://pg.dacom.net/common/cashreceipt.jsp';
$mid = $pg['id']; //데이콤에서 발급한 상점아이디
$mertkey = $pg['mertkey']; //데이콤에서 발급(상점관리자 > 계약정보 > 상점정보 관리 에서 mertkey 확인)
$ret_url = 'NONE'; // defaul : NONE

if ($_POST['method'] == 'auth' && isset($_GET['crno']) === false)
{
	$method = 'auth';
	$paytype = 'SC0100';
	$ordno = $_POST['ordno'];
	$data = $db->fetch("select * from gd_order where ordno='".$ordno."' limit 1");
	$amount = $data['prn_settleprice'];
	$usertype = $_POST['usertype'];
	$ssn = $_POST['ssn'];
	$bussinessno = $cfg['compSerial'];

	// 발급상태체크(기존시스템고려)
	if ($data['cashreceipt'] != '' && file_exists(dirname(__FILE__).'/../../../lib/cashreceipt.class.php') === false) {
		msg('현금영수증 발행요청실패!! \\n['.$ordno.'] 주문은 이미 발행되었습니다.');
		exit;
	}

	### 현금영수증신청내역 추가
	@include dirname(__FILE__).'/../../../lib/cashreceipt.class.php';
	if (class_exists('cashreceipt'))
	{
		// 발급상태체크
		list($crno) = $db->fetch("select crno from gd_cashreceipt where ordno='{$ordno}' and status='ACK' order by crno desc limit 1");
		if ($crno != '') {
			msg('현금영수증 발행요청실패!! \\n['.$ordno.'] 주문은 이미 발행되었습니다.');
			exit;
		}

		## 상품명
		list($icnt) = $db->fetch("select count(*) from gd_order_item where istep < 40 and ordno='{$ordno}'");
		list($goodsnm) = $db->fetch("select goodsnm from gd_order_item where istep < 40 and ordno='{$ordno}' order by sno");

		$cutLen = 30;
		if ($icnt > 1){
			$cntStr = ' 외 '.($icnt-1).'건';
			$cutLen -= strlen($cntStr) + 2;
		}
		$goodsnm = strcut($goodsnm,$cutLen) . $cntStr;

		$indata = array();
		$indata['ordno'] = $_POST['ordno'];
		$indata['goodsnm'] = $goodsnm;
		$indata['buyername'] = $data['nameOrder'];
		$indata['useopt'] = ($_POST['usertype'] == '1' ? '0' : '1');
		$indata['certno'] = $_POST['ssn'];
		$indata['amount'] = $amount;

		if ($set['receipt']['compType'] == '1'){ // 면세/간이사업자
			$indata['supply'] = $amount;
			$indata['surtax'] = 0;
		}
		else { // 과세사업자
			$indata['supply'] = round($amount / 1.1);
			$indata['surtax'] = $amount - $indata['supply'];
		}

		$cashreceipt = new cashreceipt();
		$crno = $cashreceipt->putReceipt($indata);
	}
}
else if ($crdata['method'] == 'auth')
{
	$method = 'auth';
	$paytype = 'SC0100';
	$ordno = $crdata['ordno'];
	$amount = $crdata['amount'];
	$usertype = ($crdata['useopt'] == '0' ? '1' : '2');
	$ssn = $crdata['certno'];
	$bussinessno = $cfg['compSerial'];
	$crno = $_GET['crno'];
}
else if ($crdata['method'] == 'cancel')
{
	$method = 'cancel';
	$paytype = 'SC0100';
	$ordno = $crdata['ordno'];
	$amount = $crdata['amount'];
	$bussinessno = $cfg['compSerial'];
}

$oid = $ordno; //주문번호 (취소시 원거래 주문번호)
$hashdata = md5($mid.$oid.$mertkey); // 인증키

// 데이콤의 배송결과등록페이지를 호출하여 배송정보등록함
$str_url = $service_url.'?mid='.$mid.'&oid='.$oid.'&paytype='.$paytype.'&usertype='.$usertype.'&ssn='.$ssn.'&amount='.$amount.'&bussinessno='.$bussinessno.'&method='.$method.'&ret_url='.$ret_url.'&hashdata='.$hashdata;

/*
*	fsockopen 방식
*	php 4.3 버전 이전에서 사용가능
*/
$res = readurl($str_url);
if(!$res)
{
	msg('현금영수증 연결실패!!');
}
else
{
	/*<!--***************************************************
	  #요청 결과 응답 형식
	   형 식 )  name|value^name|value^name|value^name|value
	   예) mid|tdacomts1^oid|20080306-1^paytype|SC0030^receiptnumber|null^respcode|0000^respmsg|성공
	****************]*************************************-->*/
	$tmp = explode('^',trim($res));
	foreach($tmp as $v){
		unset($rtmp);
		$rtmp = explode('|',$v);
		if( $rtmp[0] )$res_arr[$rtmp[0]] = $rtmp[1];
	}

	if($method == 'auth')
	{
		if($res_arr['respcode'] == '0000')
		{
			$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
			$settlelog .= '-----------------------------------'."\n";
			$settlelog .= '현금영수증 발급 성공'."\n";
			$settlelog .= '결과코드 : '.$res_arr['respcode']."\n";
			$settlelog .= '결과내용 : '.$res_arr['respmsg']."\n";
			$settlelog .= '승인번호 : '.$res_arr['receiptnumber']."\n";
			$settlelog .= '-----------------------------------'."\n";
			echo nl2br($settlelog);

			if (empty($crno) === true)
			{
				$db->query("update gd_order set cashreceipt='{$res_arr['receiptnumber']}',settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
			}
			else {
				# 현금영수증신청내역 수정
				$db->query("update gd_cashreceipt set pg='dacom',cashreceipt='{$res_arr['receiptnumber']}',receiptnumber='{$res_arr['receiptnumber']}',moddt=now(),status='ACK',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
				$db->query("update gd_order set cashreceipt='{$res_arr['receiptnumber']}' where ordno='{$ordno}'");
			}

			if (isset($_GET['crno']) === false)
			{
				msg('현금영수증이 정상발급되었습니다');
				echo '<script>parent.location.reload();</script>';
			}
			else {
				echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
			}
		}
		else {
			$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
			$settlelog .= '-----------------------------------'."\n";
			$settlelog .= '현금영수증 발급 실패'."\n";
			$settlelog .= '결과코드 : '.$res_arr['respcode']."\n";
			$settlelog .= '결과내용 : '.$res_arr['respmsg']."\n";
			$settlelog .= '-----------------------------------'."\n";
			echo nl2br($settlelog);

			if (empty($crno) === true)
			{
				$db->query("update gd_order set settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
			}
			else {
				# 현금영수증신청내역 수정
				$db->query("update gd_cashreceipt set pg='dacom',errmsg='{$res_arr['respcode']}:{$res_arr['respmsg']}',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
			}

			if (isset($_GET['crno']) === false)
			{
				msg($res_arr['respmsg']);
				exit;
			}
			else {
				echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
			}
		}
	}
	else if ($method == 'cancel')
	{
		if($res_arr['respcode'] == '0000')
		{
			$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
			$settlelog .= '-----------------------------------'."\n";
			$settlelog .= '현금영수증 취소 성공'."\n";
			$settlelog .= '결과코드 : '.$res_arr['respcode']."\n";
			$settlelog .= '결과내용 : '.$res_arr['respmsg']."\n";
			$settlelog .= '승인번호 : '.$res_arr['receiptnumber']."\n";
			$settlelog .= '-----------------------------------'."\n";
			echo nl2br($settlelog);

			$db->query("update gd_cashreceipt set moddt=now(),status='CCR',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'{$settlelog}') where crno='{$_GET['crno']}'");
			echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
		}
		else {
			$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
			$settlelog .= '-----------------------------------'."\n";
			$settlelog .= '현금영수증 취소 실패'."\n";
			$settlelog .= '결과코드 : '.$res_arr['respcode']."\n";
			$settlelog .= '결과내용 : '.$res_arr['respmsg']."\n";
			$settlelog .= '-----------------------------------'."\n";
			echo nl2br($settlelog);

			$db->query("update gd_cashreceipt set errmsg='{$res_arr['respcode']}:{$res_arr['respmsg']}',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$_GET['crno']}'");
			echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
		}
	}
}

?>