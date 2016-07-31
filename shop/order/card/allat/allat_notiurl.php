<?php

//��������ÿ��� ��ũ��Ʈ ����
ignore_user_abort(true);

include '../../../lib/library.php';
include '../../../conf/config.php';
@include '../../../conf/pg.' . $cfg['settlePg'] . '.php';

/**
* @date 2014-05-29
* RESPONSE
* ó���Ϸ� �޼��� : 0000 (�̹� �ԱݿϷ� ó���� �ǿ� ���ؼ��� �Ϸ� �޼����� ǥ���� �ֽʽÿ�.)
* ó������ �޼��� : 9999 (ó�����п� ���� �޼��� ǥ�Ⱑ ������ ��� ���� �޼����� ǥ���� �ֽñ� �ٶ��ϴ�.)
* ��) ó���Ϸ�� : 0000 ����
* ó�����н� : 9999 ó�����л���
*/
function allatResponse($_code)
{
	$code = '9999';
	switch($_code) {
		case '0000':
			$code = '0000';
			$_msg = '������� �Ա�Ȯ���� ���������� ó���Ǿ����ϴ�.';
		break;

		case '0001':
			$_msg = '�þ� PG�� ������� ��ü�� �ƴմϴ�.';
		break;

		case '0002':
			$_msg = 'HASH DATA �� �����ʽ��ϴ�. �ùٸ��������� Ȯ���Ͽ� �ֽʽÿ�.';
		break;

		case '0003':
			$_msg = '�Ա��뺸�ð����� ���� 5���� �������ϴ�.';
		break;

		case '0004':
			$_msg = '��ǰ�� ��� �����մϴ�.';
		break;

		case '0005':
			$_msg = 'cardCancel class �� �������� �ʽ��ϴ�.';
		break;

		case '0006':
			$_msg = '�ֹ� DB ó���� ���� ���Ͽ����ϴ�.';
		break;

		case '0007':
			$_msg = '�ֹ���ǰ DB ó���� ���� ���Ͽ����ϴ�.';
		break;

		case '0008':
			$_msg = 'setStock �Լ��� �������� �ʽ��ϴ�';
		break;

		case '0009':
			$_msg = 'Ȯ�ε��� ���� �������� �Դϴ�.';
		break;

		default :
			$_msg = '���������� ó������ ���Ͽ����ϴ�.';
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

	//30���� �α� ����
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
$ALLAT_SHOP_ID						= trim($_POST['shop_id']);						//����ID Variable 20 (�þ����� ����ID)
$ALLAT_ORDER_NO						= trim($_POST['order_no']);						//�ֹ���ȣ Variable 70 ( ex : ORDER_00001 )
$ALLAT_TX_SEQ_NO					= trim($_POST['tx_seq_no']);					//�ŷ��Ϸù�ȣ Variable 10 ( ex : 1234567890 )
$ALLAT_ACCOUNT_NO					= trim($_POST['account_no']);					//������� ���¹�ȣ Variable 20 ( ex : 12345678901234 )
$ALLAT_BANK_CD						= trim($_POST['bank_cd']);						//������� �����ڵ� Fixed 2 ( ex : 11 )
$ALLAT_APPLY_YMDHMS					= trim($_POST['apply_ymdhms']);					//���ο�û�� Fixed 14 ( ex : 20100601123030 )
$ALLAT_APPROVAL_YMDHMS				= trim($_POST['approval_ymdhms']);				//������� ä���� Fixed 14 ( ex : 20100601123040 )
$ALLAT_INCOME_YMDHMS				= trim($_POST['income_ymdhms']);				//������� �Ա��� Fixed 14 ( ex : 20100601143010 )
$ALLAT_APPLY_AMT					= trim($_POST['apply_amt']);					//ä���ݾ� Variable 12 ( ex : 10000 )
$ALLAT_INCOME_AMT					= trim($_POST['income_amt']);					//�Աݱݾ� Variable 12 ( ex : 10000 )
$ALLAT_INCOME_ACCOUNT_NM			= trim($_POST['income_account_nm']);			//�Ա��ڸ� Variable 30 ( ex : ���� )
$ALLAT_RECEIPT_SEQ_NO				= trim($_POST['receipt_seq_no']);				//���ݿ����� �Ϸù�ȣ Variable 10 ( ex : 1234567890 )
$ALLAT_CASH_APPROVAL_NO				= trim($_POST['cash_approval_no']);				//���ݿ����� ���ι�ȣ Variable 10 ( ex : 1234567890 )
$ALLAT_NOTI_CURRENTTIMEMILLIS		= trim($_POST['noti_currenttimemillis']);		//�Ա��뺸��  Fixed 13 ( CurrentTimeMillis ���� )
$ALLAT_HASH_VALUE					= trim($_POST['hash_value']);					//HASH DATA  Variable ( ��ȿ�� üũ �ؽ�Data )

// CONFIG VALUE
$ALLAT_CROSSKEY						= trim($pg['crosskey']);						//cross key

//PG CHECK
if( $cfg['settlePg'] != 'allatbasic' && $cfg['settlePg'] != 'allat' ) allatResponse('0001');

/*
*	HASH DATA üũ
*	HASH DATA (����ID + ������ Cross Key + ������� �ŷ����� �ֹ���ȣ + �Ա��뺸��)
*/
$hashData = MD5(trim($pg['id']).$ALLAT_CROSSKEY.$ALLAT_ORDER_NO.$ALLAT_NOTI_CURRENTTIMEMILLIS);
if( $ALLAT_HASH_VALUE != $hashData ) allatResponse('0002');

//CurrentTime üũ
$currentPostTime = $ALLAT_NOTI_CURRENTTIMEMILLIS + ( 5 * 60 * 1000 );
$currentTime = current_millis();
if( $currentPostTime < $currentTime ) allatResponse('0003');

//�ֹ���ȣ
$ordno = $ALLAT_ORDER_NO;


/*
*  DB ó�� ����
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

//�ֹ�����
$query = "
	SELECT * FROM
		".GD_ORDER." a
		LEFT JOIN ".GD_LIST_BANK." b on a.bankAccount = b.sno
	WHERE
		a.ordno = '" . $ordno . "'
";
$data = $db->fetch($query);

//�Ա�Ȯ�� STEP
$step = 1;

//ORDER LOG
$_settlelog		= array();
$_settlelog[]	= chr(10);
$_settlelog[]	= '=================================';
$_settlelog[]	= '������� �Ա� �ڵ� Ȯ�� : ���� (' . date('Y-m-d H:i:s') . ')';
$_settlelog[]	= '=================================';
$_settlelog[]	= '�ֹ���ȣ : ' . $ALLAT_ORDER_NO;
$_settlelog[]	= '�ŷ��Ϸù�ȣ : ' . $ALLAT_TX_SEQ_NO;
$_settlelog[]	= '������� ���¹�ȣ : ' . $ALLAT_ACCOUNT_NO;
$_settlelog[]	= '������� �����ڵ� : ' . $ALLAT_BANK_CD;
$_settlelog[]	= '������� �Ա��� : ' . $ALLAT_INCOME_YMDHMS;
$_settlelog[]	= '������� �Աݱݾ� : ' . number_format($ALLAT_INCOME_AMT);
$_settlelog[]	= '�Ա��ڸ� : ' . $ALLAT_INCOME_ACCOUNT_NM;

if( $ALLAT_RECEIPT_SEQ_NO && $ALLAT_CASH_APPROVAL_NO ){
	$_settlelog[]	= '���ݿ����� �Ϸù�ȣ : ' . $ALLAT_RECEIPT_SEQ_NO;
	$_settlelog[]	= '���ݿ����� ���ι�ȣ : ' . $ALLAT_CASH_APPROVAL_NO;
}

$_settlelog[]	= chr(10);
$settlelog = @implode('\n',$_settlelog);

//���ݿ�����
if( $ALLAT_CASH_APPROVAL_NO ) $cashQuery = "cashreceipt	= '" . $ALLAT_CASH_APPROVAL_NO . "',";

//DBó��
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

//��¹���
ob_start();
	//�ֹ��α� ����
	if(function_exists('orderLog')){
		orderLog($ordno, $r_step[$data[step]].' > '.$r_step[$step]);
	}

	//��� ó��
	if(!function_exists('setStock')){ 
		allatResponse('0008');
	}

	setStock($ordno);

	//�Ա�Ȯ�θ���
	if(function_exists('sendMailCase')){
		sendMailCase($data[email], 1, $data);
	}

	//�Ա�Ȯ��SMS
	if(function_exists('sendSmsCase')){
		$dataSms = $data;
		sendSmsCase('incash', $data[mobileOrder]);
	}
	
	//Ncash �ŷ� Ȯ�� API
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
*  DB ó�� ��
*/

allatResponse('0009');
?>