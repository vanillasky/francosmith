<?php

/*##################################################################################################
'  ���������� �����ؾ� �ϴ� notiurl ������ �̸�
'  ������𽺿��� ���������� ��������� ���� ȣ���ϴ� ������
'  'SUCCESS' �Ǵ� 'FAIL' �� ���
'
'  - ��������� �޾� ������� ������ 'SUCCESS'
'  - ������� ���н� 'FAIL' �� ���
'
'  ��) ���� ����� ���� ���� �ΰ��� ���� �ϳ��� ����ؾ� �մϴ�.
'      - 'FAIL' ��½� ������𽺿��� ���� ����� ��ȣ�� �մϴ�.
'
'      okurl �ε� ����� �����ϹǷ� notiurl���� �������� okurl ���� �ߺ� ó�� ����
'      - ĳ�� �ߺ� ���� �� ����
'
'      notiurl �� �ش��ϴ� �Ķ���Ͱ� �����ϴ� ��� notiurl ȣ�� �� okurl ȣ��
'
'      okurl �� �������� ��ȯ�̹Ƿ� ����� �������� ��Ȳ�� ���� ������� ���� ���ɼ��� ����
'      notiurl ȣ���� �������� ������� ������ ȣ���ϴ� ������� ���н� �ٽ� ȣ���ϴ� �������
'      ����������� ������ �ּ�ȭ
'##################################################################################################*/

include dirname(__FILE__).'/../../../lib/library.php';

// ����� ��� �ε�
$cart = Core::loader('cart', $_COOKIE['gd_isDirect']);
$naverNcash = Core::loader('naverNcash', true);
$cardCancel = Core::loader('cardCancel');
$mobilians = Core::loader('Mobilians');

// �Ķ���� ����
$isMobilians = (isset($isEnamoo) && $isEnamoo === true) ? false : true;
$sender = ($isMobilians === true) ? 'mobilians' : 'enamoo';
$mrchid     = $_POST['Mrchid'    ]; // �������̵�
$svcid      = $_POST['Svcid'     ]; // ���񽺾��̵�
$mobilid    = $_POST['Mobilid'   ]; // ������� �ŷ���ȣ
$signdate   = $_POST['Signdate'  ]; // ��������
$tradeid    = $_POST['Tradeid'   ]; // �����ŷ���ȣ
$prdtnm     = $_POST['Prdtnm'    ]; // ��ǰ��
$prdtprice  = $_POST['Prdtprice' ]; // ��ǰ����
$commid     = $_POST['Commid'    ]; // �����
$no         = $_POST['No'        ]; // ����ȣ
$resultCode = $_POST['Resultcd'  ]; // ����ڵ�
$resultMsg  = $_POST['Resultmsg' ]; // ����޼���
$userid     = $_POST['Userid'    ]; // �����ID
$mstr       = $_POST['MSTR'      ]; // ������ ���� �ݹ麯��
//$userkey    = $_POST['USERKEY'   ]; // �ڵ�����KEY
//$easypay    = $_POST['EASYPAY'   ];

// �����α� ����
$orderSettlelog = '';
$orderSettlelog .= '[������� ��������('.$sender.')]'.PHP_EOL;
$orderSettlelog .= '�ŷ���� : '.$resultMsg.PHP_EOL;
$orderSettlelog .= '�ŷ���ȣ : '.$mobilid.PHP_EOL;
$orderSettlelog .= '�������� : '.$signdate.PHP_EOL;
$orderSettlelog .= '���� �޴��� ��Ż� : '.$commid.PHP_EOL;
$orderSettlelog .= '���� �޴��� ��ȣ : '.$no.PHP_EOL;

// ������� �α� �ۼ�
$mobilians->writeLog(
	'Payment approval start'.PHP_EOL.
	'File : '.__FILE__.PHP_EOL.
	'Transaction ID : '.$tradeid.PHP_EOL.
	'Sender : '.$sender.PHP_EOL.
	'Receive data : '.http_build_query($_POST)
);

// ��������
if ($resultCode  == '0000') {

	/**
	 * C1. �ֹ� ó�� �� ��ȿ�� �� �ܺ� API ȣ��
	 */

	// C1-1. �ֹ����� ��ȸ
	$orderData = $db->fetch("SELECT * FROM ".GD_ORDER." WHERE ordno=".$tradeid);

	// C1-2. �Ա�Ȯ�� ���� �Ǵ� �ߺ� ���޵� ���������� Ȯ��
	if ($orderData['step'] > 0 || $orderData['pgAppNo'] == $mobilid) {
		$mobilians->writeLog('WARNING : ���������� �ߺ� ���޵Ǿ����ϴ�.');

		// �̳������� �������� step�� �ٲ��� ��Ȳ�� ����� ������𽺷� ���� ���ŵ� ������ �α׸� �����.
		if ($isMobilians === true) {
			// ���ŵ� ���� �α�
			$db->query("UPDATE ".GD_ORDER." SET settlelog=CONCAT(IFNULL(settlelog, ''), '".$orderSettlelog."') WHERE ordno='".$tradeid."'");
			exit('SUCCESS');
		}
		else {
			return 'SUCCESS';
		}
	}

	// C1-3. ���� ���̵� �� ���� ���̵� Ȯ��
	if ($mobilians->checkMerchantId($mrchid) === false || $mobilians->checkServiceId($svcid) === false) {
		$mobilians->writeLog('ERROR : ���� ���� ����ġ');

		// �������
		$paymentCancelResult = $mobilians->paymentCancel($tradeid, $mobilid, $prdtprice);
		if ($paymentCancelResult === '0000') {
			// �ֹ� �� �ֹ���ǰ ���� ������Ʈ
			$db->query("UPDATE ".GD_ORDER." SET step2='54', settlelog=CONCAT(IFNULL(settlelog, ''), '".$message.PHP_EOL."') WHERE ordno=".$tradeid." AND step2=50");
			$db->query("UPDATE ".GD_ORDER_ITEM." SET istep='54' WHERE ordno=".$tradeid." AND istep=50");
			$message .= '���� ������ ��ġ���� �ʾ�, ������ ��ҵǾ����ϴ�.';
		}
		else {
			// ������ҿ�� ó��
			$cardCancel->cancel_db_proc($tradeid);
			$message .= '���� ������ ��ġ���� �ʽ��ϴ�.'.PHP_EOL.'�ڵ������� ������ �����, �����ͷ� ȯ�ҿ�û�Ͽ� �ֽñ� �ٶ��ϴ�.';
		}

		if ($isMobilians === true) exit('FAIL');
		else return $message;
	}

	// C1-4. PG���� ������ üũ �� ��ȿ�� üũ
	if (forge_order_check($tradeid, $prdtprice) === false) {
		$mobilians->writeLog('ERROR : �������� ����ġ');

		// �������
		$paymentCancelResult = $mobilians->paymentCancel($tradeid, $mobilid, $prdtprice);
		if ($paymentCancelResult === '0000') {
			// �ֹ� �� �ֹ���ǰ ���� ������Ʈ
			$db->query("UPDATE ".GD_ORDER." SET step2='54', settlelog=CONCAT(IFNULL(settlelog, ''), '".$message.PHP_EOL."') WHERE ordno=".$tradeid." AND step2=50");
			$db->query("UPDATE ".GD_ORDER_ITEM." SET istep='54' WHERE ordno=".$tradeid." AND istep=50");
			$message .= '�ֹ� ������ ���� ������ ���� �ʾ�, ������ ��ҵǾ����ϴ�.';
		}
		else {
			// ������ҿ�� ó��
			$cardCancel->cancel_db_proc($tradeid);
			$message .= '�ֹ� ������ ���� ������ ���� �ʽ��ϴ�.'.PHP_EOL.'�ڵ������� ������ �����, �����ͷ� ȯ�ҿ�û�Ͽ� �ֽñ� �ٶ��ϴ�.';
		}

		if ($isMobilians === true) exit('FAIL');
		else return $message;
	}

	// C1-5. �ֹ���ǰ ��� üũ
	if ($cardCancel->chk_item_stock($tradeid) === false) {
		$mobilians->writeLog('ERROR : ������');

		// ������ó��
		if (false) {
			$message = '�ֹ��Ͻ� ��ǰ�� ��� �����մϴ�.'.PHP_EOL.'�����ͷ� �����Ͽ��ֽñ� �ٶ��ϴ�.';
			$mobilians->writeLog($message);

			// ������ҿ�� ó��
			$cardCancel->cancel_db_proc($tradeid);
		}
		// �ڵ� �������
		else {
			// �������
			$paymentCancelResult = $mobilians->paymentCancel($tradeid, $mobilid, $prdtprice);
			if ($paymentCancelResult === '0000') {
				// �ֹ� �� �ֹ���ǰ ���� ������Ʈ
				$db->query("UPDATE ".GD_ORDER." SET step2='54', settlelog=CONCAT(IFNULL(settlelog, ''), '".$message.PHP_EOL."') WHERE ordno=".$tradeid." AND step2=50");
				$db->query("UPDATE ".GD_ORDER_ITEM." SET istep='54' WHERE ordno=".$tradeid." AND istep=50");

				// ����޽��� ����
				$message = '�ֹ��Ͻ� ��ǰ�� ��� �����Ͽ� ������ ��ҵǾ����ϴ�.';
				$mobilians->writeLog($message);
			}
			else {
				// ����޽��� ����
				$message = '�ֹ��Ͻ� ��ǰ�� ��� �����մϴ�.'.PHP_EOL.'�ڵ������� ������ �����, �����ͷ� ȯ�ҿ�û�Ͽ� �ֽñ� �ٶ��ϴ�.';
				$mobilians->writeLog($message);

				// ������ҿ�� ó��
				$cardCancel->cancel_db_proc($tradeid);
			}
		}

		if ($isMobilians === true) exit('FAIL');
		else return $message;
	}

	// C1-6. ���̹� ���ϸ��� ���� ���� API
	if ($naverNcash->useyn == 'Y') {
		$ncashResult = $naverNcash->payment_approval($tradeid, true);
		if ($ncashResult === false) {
			$mobilians->writeLog('ERROR : ���̹� ���ϸ��� ��� ����');

			// �������
			$paymentCancelResult = $mobilians->paymentCancel($tradeid, $mobilid, $prdtprice);
			if ($paymentCancelResult === '0000') {
				$db->query("UPDATE ".GD_ORDER." SET step2='54', settlelog=CONCAT(IFNULL(settlelog, ''), '".$message.PHP_EOL."') WHERE ordno=".$tradeid." AND step2=50");
				$db->query("UPDATE ".GD_ORDER_ITEM." SET istep='54' WHERE ordno=".$tradeid." AND istep=50");
				$message .= '���̹� ���ϸ��� ��뿡 �����Ͽ�, ������ ��ҵǾ����ϴ�.';
			}
			else {
				// ������ҿ�� ó��
				$cardCancel->cancel_db_proc($tradeid);
				$message .= '���̹� ���ϸ��� ��뿡 �����Ͽ����ϴ�.'.PHP_EOL.'�ڵ������� ������ �����, �����ͷ� ȯ�ҿ�û�Ͽ� �ֽñ� �ٶ��ϴ�.';
			}

			if ($isMobilians === true) exit('FAIL');
			else return $message;
		}
	}

	/**
	 * C2. C1 ���μ��� ��� �� ����ó��
	 */

	// C2-1. ��ۺ� �� ���� ����
	$cart->chkCoupon();
	$cart->delivery = $orderData['delivery'];
	$cart->dc = isset($sess) ? $sess['dc'] : 0;
	$cart->calcu();
	$cart -> totalprice += $orderData['price'];

	// C2-2. �ֹ����� ����
	$db->query("
	UPDATE ".GD_ORDER." SET
		step = '1',
		step2 = '',
		escrowyn = 'n',
		escrowno = '',
		settlelog = CONCAT(IFNULL(settlelog, ''), '".$orderSettlelog."'),
		cyn = 'y',
		cdt = NOW(),
		cardtno = '".$mobilid."',
		pgAppNo = '".$mobilid."',
		pgAppDt = '".$signdate."'
	WHERE ordno='".$tradeid."'"
	);

	// C2-3. �ֹ���ǰ���� ����
	$db->query("
	UPDATE ".GD_ORDER_ITEM." SET
		cyn = 'y',
		istep='1'
	WHERE ordno='".$tradeid."'
	");

	// C2-4. �ֹ��α� ����
	orderLog($tradeid, $r_step2[$orderData['step2']]." > ".$r_step[1]);

	// C2-5. ��� ó��
	setStock($tradeid);

	// C2-6. ��ǰ���Խ� ������ ���
	if ($orderData['m_no'] && $orderData['emoney']) {
		setEmoney($orderData['m_no'], -$orderData['emoney'], '��ǰ���Խ� ������ ���� ���', $tradeid);
	}

	// C2-7. �ֹ�Ȯ�� �� �Ա�Ȯ�� ����
	$sendMailData = $orderData;
	$sendMailData['cart'] = $cart;
	$sendMailData['str_settlekind'] = $r_settlekind[$sendMailData['settlekind']];
	sendMailCase($sendMailData['email'], 0, $sendMailData);
	sendMailCase($sendMailData['email'], 1, $sendMailData);
	unset($sendMailData);

	// C2-8. �Ա�Ȯ��SMS
	$GLOBALS['cfg'] = $cfg;
	$GLOBALS['dataSms'] = $orderData;
	sendSmsCase('incash', $orderData['mobileOrder']);

	// C2-9. ������� �α��ۼ�
	$mobilians->writeLog('��������');

	if ($isMobilians === true) exit('SUCCESS');
	else return 'SUCCESS';

}
// ��������
else {
	// C3. ������� �α� �ۼ�
	$mobilians->writeLog('��������');

	if ($isMobilians === true) exit('FAIL');
	else return '������ �����Ͽ����ϴ�.';
}

?>