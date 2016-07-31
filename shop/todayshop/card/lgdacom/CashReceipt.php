<?php
if (isset($_GET['crno']) === false)
{
	include dirname(__FILE__).'/../../../lib/library.php';
	include dirname(__FILE__).'/../../../conf/config.pay.php';
}
include dirname(__FILE__).'/../../../conf/config.php';
//include dirname(__FILE__).'/../../../conf/pg.lgdacom.php';
require_once dirname(__FILE__)."/../../../lib/load.class.php";

// 투데이샵 사용중인 경우 PG 설정 교체
resetPaymentGateway();

$configPath						= $_SERVER['DOCUMENT_ROOT'].$cfg['rootDir']."/conf/lgdacom_today";		//LG데이콤에서 제공한 환경파일("/conf/lgdacom.conf") 위치 지정.

if(!$pg['serviceType']) $pg['serviceType'] = "service";
$CST_PLATFORM			  		= $pg['serviceType'];							//LG데이콤 결제 서비스 선택(test:테스트, service:서비스)
$CST_MID						= $pg['id'];									//상점아이디(LG데이콤으로 부터 발급받으신 상점아이디를 입력하세요)
$LGD_MID						= (("test" == $CST_PLATFORM)?"t":"").$CST_MID;	//상점아이디(자동생성),테스트 아이디는 't'를 반드시 제외하고 입력하세요.
$LGD_CUSTOM_MERTNAME			= $cfg['compName'];								//상점명
$LGD_CUSTOM_CEONAME 			= $cfg['ceoName'];								//상점 대표자명
$LGD_CUSTOM_BUSINESSNUM 		= str_replace("-","",$cfg['compSerial']);		//사업자등록번호
$LGD_CUSTOM_MERTPHONE 			= $cfg['compPhone'];							//상점 전화번호

if ($_POST['method'] == 'auth' && isset($_GET['crno']) === false)
{
	$ordno						= $_POST['ordno'];
	$method						= 'auth';

	$data = $db->fetch("select * from gd_order where ordno='".$ordno."' limit 1");

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
		$indata['amount'] = $data['prn_settleprice'];

		if ($set['receipt']['compType'] == '1'){ // 면세/간이사업자
			$indata['supply'] = $data['prn_settleprice'];
			$indata['surtax'] = 0;
			$LGD_TAXFREEAMOUNT	= $data["prn_settleprice"];						//면세금액
		}
		else { // 과세사업자
			$indata['supply'] = round($data['prn_settleprice'] / 1.1);
			$indata['surtax'] = $data['prn_settleprice'] - $indata['supply'];
		}

		$cashreceipt = new cashreceipt();
		$crno = $cashreceipt->putReceipt($indata);
	}

	//$LGD_TID					= $HTTP_POST_VARS["LGD_TID"];			 		//LG데이콤으로 부터 내려받은 거래번호(LGD_TID)
	$LGD_METHOD   				= "AUTH";										//메소드('AUTH':승인, 'CANCEL' 취소)
	$LGD_OID					= $_POST['ordno'];								//주문번호(상점정의 유니크한 주문번호를 입력하세요)
	$LGD_PAYTYPE				= "SC0100";										//결제수단 코드 (SC0030:계좌이체, SC0040:가상계좌, SC0100:무통장입금 단독)
	$LGD_AMOUNT	 				= $data['prn_settleprice'];						//금액("," 를 제외한 금액을 입력하세요)
	$LGD_CASHCARDNUM			= $_POST['ssn'];								//발급번호(주민등록번호,현금영수증카드번호,휴대폰번호 등등)
	$LGD_CASHRECEIPTUSE	 		= $_POST['usertype'];							//현금영수증발급용도('1':소득공제, '2':지출증빙)
	$LGD_PRODUCTINFO			= $goodsnm;										//상품명
}
else if ($crdata['method'] == 'auth')
{
	//$LGD_TID					= $_POST["LGD_TID"];			 				//LG데이콤으로 부터 내려받은 거래번호(LGD_TID)
	$LGD_METHOD   				= "AUTH";										//메소드('AUTH':승인, 'CANCEL' 취소)
	$LGD_OID					= $crdata['ordno'];								//주문번호(상점정의 유니크한 주문번호를 입력하세요)
	$LGD_PAYTYPE				= "SC0100";										//결제수단 코드 (SC0030:계좌이체, SC0040:가상계좌, SC0100:무통장입금 단독)
	$LGD_AMOUNT					= $crdata['amount'];							//금액("," 를 제외한 금액을 입력하세요)
	if ($set['receipt']['compType'] == '1'){ // 면세/간이사업자
		$LGD_TAXFREEAMOUNT		= $data["prn_settleprice"];						//면세금액
	}
	$LGD_CASHCARDNUM			= $crdata['certno'];		   					//발급번호(주민등록번호,현금영수증카드번호,휴대폰번호 등등)
	$LGD_CASHRECEIPTUSE			= ($crdata['useopt'] == '0' ? '1' : '2');		//현금영수증발급용도('1':소득공제, '2':지출증빙)
	$LGD_PRODUCTINFO			= $crdata['goodsnm'];							//상품명
	$ordno						= $crdata['ordno'];
	$method						= 'auth';
	$crno						= $_GET['crno'];
}
else if ($crdata['method'] == 'cancel')
{
	$LGD_TID					= $crdata['tid'];				 				//LG데이콤으로 부터 내려받은 거래번호(LGD_TID)
	$LGD_METHOD   				= "CANCEL";										//메소드('AUTH':승인, 'CANCEL' 취소)
	$LGD_OID					= $crdata['ordno'];								//주문번호(상점정의 유니크한 주문번호를 입력하세요)
	$LGD_PAYTYPE				= "SC0100";										//결제수단 코드 (SC0030:계좌이체, SC0040:가상계좌, SC0100:무통장입금 단독)
	$ordno						= $crdata['ordno'];
	$method						= 'cancel';
}

	require_once(dirname(__FILE__)."/XPayClient.php");
	$xpay = &new XPayClient($configPath, $CST_PLATFORM);
	$xpay->Init_TX($LGD_MID);
	$xpay->Set("LGD_TXNAME", "CashReceipt");
	$xpay->Set("LGD_METHOD", $LGD_METHOD);
	$xpay->Set("LGD_PAYTYPE", $LGD_PAYTYPE);

	if ($LGD_METHOD = "AUTH"){					// 현금영수증 발급 요청
		$xpay->Set("LGD_OID", $LGD_OID);
		$xpay->Set("LGD_AMOUNT", $LGD_AMOUNT);
		$xpay->Set("LGD_CASHCARDNUM", $LGD_CASHCARDNUM);
		$xpay->Set("LGD_CUSTOM_MERTNAME", $LGD_CUSTOM_MERTNAME);
		$xpay->Set("LGD_CUSTOM_CEONAME", $LGD_CUSTOM_CEONAME);
		$xpay->Set("LGD_CUSTOM_BUSINESSNUM", $LGD_CUSTOM_BUSINESSNUM);
		$xpay->Set("LGD_CUSTOM_MERTPHONE", $LGD_CUSTOM_MERTPHONE);
		$xpay->Set("LGD_CASHRECEIPTUSE", $LGD_CASHRECEIPTUSE);
		$xpay->Set("LGD_SEQNO", "001");

		if ($LGD_PAYTYPE = "SC0100"){			//무통장입금 단독건 발급요청
			$xpay->Set("LGD_PRODUCTINFO", $LGD_PRODUCTINFO);
		}else{									// 기결제된 계좌이체,가상계좌 현금영수증 발급요청
			$xpay->Set("LGD_TID", $LGD_TID);
		}
	}else {										// 현금영수증 취소 요청
		$xpay->Set("LGD_TID", $LGD_TID);
		$xpay->Set("LGD_SEQNO", "001");
	}

	/*
	 * 1. 현금영수증 발급/취소 요청 결과처리
	 *
	 * 결과 리턴 파라미터는 연동메뉴얼을 참고하시기 바랍니다.
	 */
	$xpay->TX();

	if($method == 'auth')
	{
		if( "0000" == $xpay->Response_Code() )
		{
			$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
			$settlelog .= '-----------------------------------'."\n";
			$settlelog .= '데이콤 현금영수증 발급에 대한 결과'."\n";
			$settlelog .= '결과코드 : '.$xpay->Response("LGD_RESPCODE",0)."\n";
			$settlelog .= '결과내용 : '.$xpay->Response("LGD_RESPMSG",0)."\n";
			$settlelog .= '주문번호 : '.$xpay->Response("LGD_OID",0)."\n";
			$settlelog .= '거래번호 : '.$xpay->Response("LGD_TID",0)."\n";
			$settlelog .= '-----------------------------------'."\n";
			echo nl2br($settlelog);

			if (empty($crno) === true)
			{
				$db->query("update gd_order set cashreceipt='".$xpay->Response("LGD_CASHRECEIPTNUM",0)."',settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
			}
			else {
				# 현금영수증신청내역 수정
				$db->query("update gd_cashreceipt set pg='lgdacom',tid='".$xpay->Response("LGD_TID",0)."',cashreceipt='".$xpay->Response("LGD_CASHRECEIPTNUM",0)."',receiptnumber='".$xpay->Response("LGD_CASHRECEIPTNUM",0)."',moddt=now(),status='ACK',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
				$db->query("update gd_order set cashreceipt='".$xpay->Response("LGD_CASHRECEIPTNUM",0)."' where ordno='{$ordno}'");
			}

			if (isset($_GET['crno']) === false)
			{
				msg('현금영수증이 정상발급되었습니다');
				echo '<script>parent.location.reload();</script>';
			}
			else {
				echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
			}
		}else{
			$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
			$settlelog .= '-----------------------------------'."\n";
			$settlelog .= '데이콤 현금영수증 발급 실패'."\n";
			$settlelog .= '결과코드 : '.$xpay->Response_Code()."\n";
			$settlelog .= '결과내용 : '.$xpay->Response_Msg()."\n";
			$settlelog .= '-----------------------------------'."\n";
			echo nl2br($settlelog);

			if (empty($crno) === true)
			{
				$db->query("update gd_order set settlelog=concat(if(settlelog is null,'',settlelog),'\n{$settlelog}') where ordno='{$ordno}'");
			}
			else {
				# 현금영수증신청내역 수정
				$db->query("update gd_cashreceipt set pg='lgdacom',errmsg='".$xpay->Response("LGD_RESPCODE",0).":".$xpay->Response("LGD_RESPMSG",0)."',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$crno}'");
			}

			if (isset($_GET['crno']) === false)
			{
				msg($xpay->Response("LGD_RESPMSG",0));
				exit;
			}
			else {
				echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
			}
		}
	}

	if($method == 'cancel')
	{
		if( "0000" == $xpay->Response_Code() )
		{
			$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
			$settlelog .= '-----------------------------------'."\n";
			$settlelog .= '데이콤 현금영수증 취소에 대한 결과'."\n";
			$settlelog .= '결과코드 : '.$xpay->Response("LGD_RESPCODE",0)."\n";
			$settlelog .= '결과내용 : '.$xpay->Response("LGD_RESPMSG",0)."\n";
			$settlelog .= '주문번호 : '.$xpay->Response("LGD_OID",0)."\n";
			$settlelog .= '거래번호 : '.$xpay->Response("LGD_TID",0)."\n";
			$settlelog .= '-----------------------------------'."\n";
			echo nl2br($settlelog);

			$db->query("update gd_cashreceipt set moddt=now(),status='CCR',errmsg='',receiptlog=concat(if(receiptlog is null,'',receiptlog),'{$settlelog}') where crno='{$_GET['crno']}'");
			echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
		}
		else {
			$settlelog = $ordno.' ('.date('Y:m:d H:i:s').')'."\n";
			$settlelog .= '-----------------------------------'."\n";
			$settlelog .= '데이콤 현금영수증 취소 실패'."\n";
			$settlelog .= '결과코드 : '.$xpay->Response_Code()."\n";
			$settlelog .= '결과내용 : '.$xpay->Response_Msg()."\n";
			$settlelog .= '-----------------------------------'."\n";
			echo nl2br($settlelog);

			$db->query("update gd_cashreceipt set errmsg='".$xpay->Response("LGD_RESPCODE",0).":".$xpay->Response("LGD_RESPMSG",0)."',moddt=now(),receiptlog=concat(if(receiptlog is null,'',receiptlog),'\n{$settlelog}') where crno='{$_GET['crno']}'");
			echo '<script>if(parent.opener == null) window.onload = function (){ parent.parent.location.reload(); }</script>';
		}
	}
?>
