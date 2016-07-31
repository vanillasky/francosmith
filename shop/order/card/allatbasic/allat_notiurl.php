<?php

//접속종료시에도 스크립트 실행
ignore_user_abort(true);

include '../../../lib/library.php';
include '../../../conf/config.php';
@include '../../../conf/pg.' . $cfg['settlePg'] . '.php';

/**
* @date 2014-05-29
* RESPONSE
* 처리완료 메세지 : 0000 (이미 입금완료 처리된 건에 대해서도 완료 메세지로 표기해 주십시오.)
* 처리실패 메세지 : 9999 (처리실패에 대한 메세지 표기가 가능한 경우 실패 메세지도 표기해 주시기 바랍니다.)
* 예) 처리완료시 : 0000 정상
* 처리실패시 : 9999 처리실패사유
*/
function allatResponse($_code)
{
	$code = '9999';
	switch($_code) {
		case '0000':
			$code = '0000';
			$_msg = '가상계좌 입금확인이 정상적으로 처리되었습니다.';
		break;

		case '0001':
			$_msg = '올앳 PG를 사용중인 업체가 아닙니다.';
		break;

		case '0002':
			$_msg = 'HASH DATA 가 맞지않습니다. 올바른접근인지 확인하여 주십시요.';
		break;

		case '0003':
			$_msg = '입금통보시간으로 부터 5분이 지났습니다.';
		break;

		case '0004':
			$_msg = '상품의 재고가 부족합니다.';
		break;

		case '0005':
			$_msg = 'cardCancel class 가 존재하지 않습니다.';
		break;

		case '0006':
			$_msg = '주문 DB 처리가 되지 못하였습니다.';
		break;

		case '0007':
			$_msg = '주문상품 DB 처리가 되지 못하였습니다.';
		break;

		case '0008':
			$_msg = 'setStock 함수가 존재하지 않습니다';
		break;

		case '0009':
			$_msg = '확인되지 않은 로직오류 입니다.';
		break;

		default :
			$_msg = '정상적으로 처리되지 못하였습니다.';
		break;
	}

	$msg = $code . ' ' . $_msg;

	allatLogWrite('END', $msg);

	echo $msg;
	exit;
}

/**
* @date 2014-05-29
* LOG WRITE
* $type - START or END 
*/
function allatLogWrite($type, $param)
{	
	$logDir				= dirname(__FILE__) . '/log/';
	$nowDate			= date('Ymd');
	$logDateInterval	= date("Ymd",strtotime('-30 day'));	

	$_log[] = '------------------------------------------------------------------------------------';
	$_log[] = $type;
	$_log[] = 'TIME : ' . date('Y-m-d H:i:s');
	
	switch($type){
		case 'START' :
			$_log[] = 'IP : ' . $_SERVER['REMOTE_ADDR'];
			foreach( $param as $key => $value){
				$_log[] = $key . ' : ' . $value;
			}
		break;

		case 'END' :
			 $_log[] = 'RESULT : ' . $param;
		break;
	}
	$_log[] = '------------------------------------------------------------------------------------' . chr(10);
	$log = @implode(chr(10), $_log);
	$logFile = $logDir . 'allat_log_notiurl_' . $nowDate . '.log';

	error_log($log, 3, $logFile);
	@chmod($logFile, 0707);

	//30일전 로그 삭제
	$logDirResource = @opendir($logDir);
	while($fileName = @readdir($logDirResource)){
		if(@preg_match('/allat_log_notiurl_/', $fileName)){
			@preg_match('/[0-9]{8}/', $fileName, $fileDate);	
			if((int)$fileDate[0] < $logDateInterval){
				@unlink($logDir.$fileName);
			}
		}
	}
}

/**
* @date 2014-05-29
* currenttimemillis
* 
*/
function current_millis() 
{ 
    list($usec, $sec) = explode(' ', microtime()); 
    return (int)round(((float)$usec + (float)$sec) * (int)1000);
}

//START LOG
allatLogWrite('START', $_POST);

// ALLAT PARAMETER
$ALLAT_SHOP_ID						= trim($_POST['shop_id']);						//상점ID Variable 20 (올앳제공 상점ID)
$ALLAT_ORDER_NO						= trim($_POST['order_no']);						//주문번호 Variable 70 ( ex : ORDER_00001 )
$ALLAT_TX_SEQ_NO					= trim($_POST['tx_seq_no']);					//거래일련번호 Variable 10 ( ex : 1234567890 )
$ALLAT_ACCOUNT_NO					= trim($_POST['account_no']);					//가상계좌 계좌번호 Variable 20 ( ex : 12345678901234 )
$ALLAT_BANK_CD						= trim($_POST['bank_cd']);						//가상계좌 은행코드 Fixed 2 ( ex : 11 )
$ALLAT_APPLY_YMDHMS					= trim($_POST['apply_ymdhms']);					//승인요청일 Fixed 14 ( ex : 20100601123030 )
$ALLAT_APPROVAL_YMDHMS				= trim($_POST['approval_ymdhms']);				//가상계좌 채번일 Fixed 14 ( ex : 20100601123040 )
$ALLAT_INCOME_YMDHMS				= trim($_POST['income_ymdhms']);				//가상계좌 입금일 Fixed 14 ( ex : 20100601143010 )
$ALLAT_APPLY_AMT					= trim($_POST['apply_amt']);					//채번금액 Variable 12 ( ex : 10000 )
$ALLAT_INCOME_AMT					= trim($_POST['income_amt']);					//입금금액 Variable 12 ( ex : 10000 )
$ALLAT_INCOME_ACCOUNT_NM			= trim($_POST['income_account_nm']);			//입금자명 Variable 30 ( ex : 윤고도 )
$ALLAT_RECEIPT_SEQ_NO				= trim($_POST['receipt_seq_no']);				//현금영수증 일련번호 Variable 10 ( ex : 1234567890 )
$ALLAT_CASH_APPROVAL_NO				= trim($_POST['cash_approval_no']);				//현금영수증 승인번호 Variable 10 ( ex : 1234567890 )
$ALLAT_NOTI_CURRENTTIMEMILLIS		= trim($_POST['noti_currenttimemillis']);		//입금통보일  Fixed 13 ( CurrentTimeMillis 형식 )
$ALLAT_HASH_VALUE					= trim($_POST['hash_value']);					//HASH DATA  Variable ( 유효성 체크 해쉬Data )

// CONFIG VALUE
$ALLAT_CROSSKEY						= trim($pg['crosskey']);						//cross key

//PG CHECK
if( $cfg['settlePg'] != 'allatbasic' && $cfg['settlePg'] != 'allat' ) allatResponse('0001');

/*
*	HASH DATA 체크
*	HASH DATA (상점ID + 상점의 Cross Key + 가상계좌 거래건의 주문번호 + 입금통보일)
*/
$hashData = MD5(trim($pg['id']).$ALLAT_CROSSKEY.$ALLAT_ORDER_NO.$ALLAT_NOTI_CURRENTTIMEMILLIS);
if( $ALLAT_HASH_VALUE != $hashData ) allatResponse('0002');

//CurrentTime 체크
$currentPostTime = $ALLAT_NOTI_CURRENTTIMEMILLIS + ( 5 * 60 * 1000 );
$currentTime = current_millis();
if( $currentPostTime < $currentTime ) allatResponse('0003');

//주문번호
$ordno = $ALLAT_ORDER_NO;


/*
*  DB 처리 시작
*/

//item check stock
$cardCancelExists = false;
if(is_file('../../../lib/cardCancel.class.php')){
	include '../../../lib/cardCancel.class.php';
	if(class_exists('cardCancel')){	
		$cancel = new cardCancel();
	}

	if(method_exists($cancel, 'chk_item_stock') && method_exists($cancel, 'cancel_db_proc')){
		$cardCancelExists = true;
		if(!$cancel->chk_item_stock($ordno) && $cfg['stepStock'] == 1 ){
			$cancel->cancel_db_proc($ordno, $ALLAT_TX_SEQ_NO);
			allatResponse('0004');
		}
	}
}

if($cardCancelExists === false){
	allatResponse('0005');
}

//주문정보
$query = "
	SELECT * FROM
		".GD_ORDER." a
		LEFT JOIN ".GD_LIST_BANK." b on a.bankAccount = b.sno
	WHERE
		a.ordno = '" . $ordno . "'
";
$data = $db->fetch($query);

//입금확인 STEP
$step = 1;

//ORDER LOG
$_settlelog		= array();
$_settlelog[]	= chr(10);
$_settlelog[]	= '=================================';
$_settlelog[]	= '가상계좌 입금 자동 확인 : 성공 (' . date('Y-m-d H:i:s') . ')';
$_settlelog[]	= '=================================';
$_settlelog[]	= '주문번호 : ' . $ALLAT_ORDER_NO;
$_settlelog[]	= '거래일련번호 : ' . $ALLAT_TX_SEQ_NO;
$_settlelog[]	= '가상계좌 계좌번호 : ' . $ALLAT_ACCOUNT_NO;
$_settlelog[]	= '가상계좌 은행코드 : ' . $ALLAT_BANK_CD;
$_settlelog[]	= '가상계좌 입금일 : ' . $ALLAT_INCOME_YMDHMS;
$_settlelog[]	= '가상계좌 입금금액 : ' . number_format($ALLAT_INCOME_AMT);
$_settlelog[]	= '입금자명 : ' . $ALLAT_INCOME_ACCOUNT_NM;

if( $ALLAT_RECEIPT_SEQ_NO && $ALLAT_CASH_APPROVAL_NO ){
	$_settlelog[]	= '현금영수증 일련번호 : ' . $ALLAT_RECEIPT_SEQ_NO;
	$_settlelog[]	= '현금영수증 승인번호 : ' . $ALLAT_CASH_APPROVAL_NO;
}

$_settlelog[]	= chr(10);
$settlelog = @implode('\n',$_settlelog);

//현금영수증
if( $ALLAT_CASH_APPROVAL_NO ) $cashQuery = "cashreceipt	= '" . $ALLAT_CASH_APPROVAL_NO . "',";

//DB처리
$res = $db->query("
	UPDATE " . GD_ORDER . " SET
		$cashQuery
		cyn			= 'y', 
		cdt			= now(),
		step		= '1',
		step2		= '',
		settlelog	= concat(settlelog,'$settlelog')
	WHERE ordno='" . $ordno . "'"
);
if(!$res) allatResponse('0006');

$res = $db->query("
	UPDATE " . GD_ORDER_ITEM . " SET 
		cyn		= 'y', 
		istep	= '1'
	WHERE
		ordno='$ordno'
");
if(!$res) allatResponse('0007');

//출력방지
ob_start();
	//주문로그 저장
	if(function_exists('orderLog')){
		orderLog($ordno, $r_step[$data[step]].' > '.$r_step[$step]);
	}

	//재고 처리
	if(!function_exists('setStock')){ 
		allatResponse('0008');
	}

	setStock($ordno);

	//입금확인메일
	if(function_exists('sendMailCase')){
		sendMailCase($data[email], 1, $data);
	}

	//입금확인SMS
	if(function_exists('sendSmsCase')){
		$dataSms = $data;
		sendSmsCase('incash', $data[mobileOrder]);
	}
	
	//Ncash 거래 확정 API
	if(is_file('../../../lib/naverNcash.class.php')){
		include '../../../lib/naverNcash.class.php';
		if(class_exists('naverNcash')){
			$naverNcash = new naverNcash();
		}

		if(method_exists($naverNcash, 'deal_done')){
			$naverNcash->deal_done($ordno);
		}
	}
ob_end_clean();

allatResponse('0000');
/*
*  DB 처리 끝
*/

allatResponse('0009');
?>