<?php

/*##################################################################################################
'  가맹점에서 구현해야 하는 notiurl 페이지 이며
'  모빌리언스에서 결제성공시 결과전송을 위해 호출하는 페이지
'  'SUCCESS' 또는 'FAIL' 만 출력
'
'  - 결제결과를 받아 결과저장 성공시 'SUCCESS'
'  - 결과저장 실패시 'FAIL' 를 출력
'
'  주) 결제 결과에 따라 위의 두가지 값중 하나를 출력해야 합니다.
'      - 'FAIL' 출력시 모빌리언스에서 결제 결과를 재호출 합니다.
'
'      okurl 로도 결과를 전송하므로 notiurl에서 결과저장시 okurl 에서 중복 처리 주의
'      - 캐쉬 중복 충전 등 주의
'
'      notiurl 에 해당하는 파라메터가 존재하는 경우 notiurl 호출 후 okurl 호출
'
'      okurl 은 웹페이지 전환이므로 사용자 브라우져에 상황에 따라 결과전송 실패 가능성이 존재
'      notiurl 호출은 브라우져와 상관없이 페이지 호출하는 방식으로 실패시 다시 호출하는 방식으로
'      결제결과전송 단절을 최소화
'##################################################################################################*/

include dirname(__FILE__).'/../../../lib/library.php';

// 사용할 모듈 로드
$cart = Core::loader('cart', $_COOKIE['gd_isDirect']);
$naverNcash = Core::loader('naverNcash', true);
$cardCancel = Core::loader('cardCancel');
$mobilians = Core::loader('Mobilians');

// 파라미터 셋팅
$isMobilians = (isset($isEnamoo) && $isEnamoo === true) ? false : true;
$sender = ($isMobilians === true) ? 'mobilians' : 'enamoo';
$mrchid     = $_POST['Mrchid'    ]; // 상점아이디
$svcid      = $_POST['Svcid'     ]; // 서비스아이디
$mobilid    = $_POST['Mobilid'   ]; // 모빌리언스 거래번호
$signdate   = $_POST['Signdate'  ]; // 결제일자
$tradeid    = $_POST['Tradeid'   ]; // 상점거래번호
$prdtnm     = $_POST['Prdtnm'    ]; // 상품명
$prdtprice  = $_POST['Prdtprice' ]; // 상품가격
$commid     = $_POST['Commid'    ]; // 이통사
$no         = $_POST['No'        ]; // 폰번호
$resultCode = $_POST['Resultcd'  ]; // 결과코드
$resultMsg  = $_POST['Resultmsg' ]; // 결과메세지
$userid     = $_POST['Userid'    ]; // 사용자ID
$mstr       = $_POST['MSTR'      ]; // 가맹점 전달 콜백변수
//$userkey    = $_POST['USERKEY'   ]; // 자동결제KEY
//$easypay    = $_POST['EASYPAY'   ];

// 결제로그 셋팅
$orderSettlelog = '';
$orderSettlelog .= '[모빌리언스 결제정보('.$sender.')]'.PHP_EOL;
$orderSettlelog .= '거래결과 : '.$resultMsg.PHP_EOL;
$orderSettlelog .= '거래번호 : '.$mobilid.PHP_EOL;
$orderSettlelog .= '결제일자 : '.$signdate.PHP_EOL;
$orderSettlelog .= '결제 휴대폰 통신사 : '.$commid.PHP_EOL;
$orderSettlelog .= '결제 휴대폰 번호 : '.$no.PHP_EOL;

// 모빌리언스 로그 작성
$mobilians->writeLog(
	'Payment approval start'.PHP_EOL.
	'File : '.__FILE__.PHP_EOL.
	'Transaction ID : '.$tradeid.PHP_EOL.
	'Sender : '.$sender.PHP_EOL.
	'Receive data : '.http_build_query($_POST)
);

// 결제성공
if ($resultCode  == '0000') {

	/**
	 * C1. 주문 처리 전 유효성 및 외부 API 호출
	 */

	// C1-1. 주문정보 조회
	$orderData = $db->fetch("SELECT * FROM ".GD_ORDER." WHERE ordno=".$tradeid);

	// C1-2. 입금확인 이후 또는 중복 전달된 결제건인지 확인
	if ($orderData['step'] > 0 || $orderData['pgAppNo'] == $mobilid) {
		$mobilians->writeLog('WARNING : 결제정보가 중복 전달되었습니다.');

		// 이나무에서 수동으로 step을 바꿨을 상황을 대비해 모빌리언스로 부터 수신된 정보는 로그를 남긴다.
		if ($isMobilians === true) {
			// 수신된 정보 로깅
			$db->query("UPDATE ".GD_ORDER." SET settlelog=CONCAT(IFNULL(settlelog, ''), '".$orderSettlelog."') WHERE ordno='".$tradeid."'");
			exit('SUCCESS');
		}
		else {
			return 'SUCCESS';
		}
	}

	// C1-3. 상점 아이디 및 서비스 아이디 확인
	if ($mobilians->checkMerchantId($mrchid) === false || $mobilians->checkServiceId($svcid) === false) {
		$mobilians->writeLog('ERROR : 상점 정보 불일치');

		// 결제취소
		$paymentCancelResult = $mobilians->paymentCancel($tradeid, $mobilid, $prdtprice);
		if ($paymentCancelResult === '0000') {
			// 주문 및 주문상품 정보 업데이트
			$db->query("UPDATE ".GD_ORDER." SET step2='54', settlelog=CONCAT(IFNULL(settlelog, ''), '".$message.PHP_EOL."') WHERE ordno=".$tradeid." AND step2=50");
			$db->query("UPDATE ".GD_ORDER_ITEM." SET istep='54' WHERE ordno=".$tradeid." AND istep=50");
			$message .= '상점 정보가 일치하지 않아, 결제가 취소되었습니다.';
		}
		else {
			// 승인취소요망 처리
			$cardCancel->cancel_db_proc($tradeid);
			$message .= '상점 정보가 일치하지 않습니다.'.PHP_EOL.'자동결제에 실패한 관계로, 고객센터로 환불요청하여 주시기 바랍니다.';
		}

		if ($isMobilians === true) exit('FAIL');
		else return $message;
	}

	// C1-4. PG결제 위변조 체크 및 유효성 체크
	if (forge_order_check($tradeid, $prdtprice) === false) {
		$mobilians->writeLog('ERROR : 결제정보 불일치');

		// 결제취소
		$paymentCancelResult = $mobilians->paymentCancel($tradeid, $mobilid, $prdtprice);
		if ($paymentCancelResult === '0000') {
			// 주문 및 주문상품 정보 업데이트
			$db->query("UPDATE ".GD_ORDER." SET step2='54', settlelog=CONCAT(IFNULL(settlelog, ''), '".$message.PHP_EOL."') WHERE ordno=".$tradeid." AND step2=50");
			$db->query("UPDATE ".GD_ORDER_ITEM." SET istep='54' WHERE ordno=".$tradeid." AND istep=50");
			$message .= '주문 정보와 결제 정보가 맞지 않아, 결제가 취소되었습니다.';
		}
		else {
			// 승인취소요망 처리
			$cardCancel->cancel_db_proc($tradeid);
			$message .= '주문 정보와 결제 정보가 맞지 않습니다.'.PHP_EOL.'자동결제에 실패한 관계로, 고객센터로 환불요청하여 주시기 바랍니다.';
		}

		if ($isMobilians === true) exit('FAIL');
		else return $message;
	}

	// C1-5. 주문상품 재고 체크
	if ($cardCancel->chk_item_stock($tradeid) === false) {
		$mobilians->writeLog('ERROR : 재고부족');

		// 관리자처리
		if (false) {
			$message = '주문하신 상품의 재고가 부족합니다.'.PHP_EOL.'고객센터로 문의하여주시기 바랍니다.';
			$mobilians->writeLog($message);

			// 승인취소요망 처리
			$cardCancel->cancel_db_proc($tradeid);
		}
		// 자동 결제취소
		else {
			// 결제취소
			$paymentCancelResult = $mobilians->paymentCancel($tradeid, $mobilid, $prdtprice);
			if ($paymentCancelResult === '0000') {
				// 주문 및 주문상품 정보 업데이트
				$db->query("UPDATE ".GD_ORDER." SET step2='54', settlelog=CONCAT(IFNULL(settlelog, ''), '".$message.PHP_EOL."') WHERE ordno=".$tradeid." AND step2=50");
				$db->query("UPDATE ".GD_ORDER_ITEM." SET istep='54' WHERE ordno=".$tradeid." AND istep=50");

				// 결과메시지 셋팅
				$message = '주문하신 상품의 재고가 부족하여 결제가 취소되었습니다.';
				$mobilians->writeLog($message);
			}
			else {
				// 결과메시지 셋팅
				$message = '주문하신 상품의 재고가 부족합니다.'.PHP_EOL.'자동결제에 실패한 관계로, 고객센터로 환불요청하여 주시기 바랍니다.';
				$mobilians->writeLog($message);

				// 승인취소요망 처리
				$cardCancel->cancel_db_proc($tradeid);
			}
		}

		if ($isMobilians === true) exit('FAIL');
		else return $message;
	}

	// C1-6. 네이버 마일리지 결제 승인 API
	if ($naverNcash->useyn == 'Y') {
		$ncashResult = $naverNcash->payment_approval($tradeid, true);
		if ($ncashResult === false) {
			$mobilians->writeLog('ERROR : 네이버 마일리지 사용 실패');

			// 결제취소
			$paymentCancelResult = $mobilians->paymentCancel($tradeid, $mobilid, $prdtprice);
			if ($paymentCancelResult === '0000') {
				$db->query("UPDATE ".GD_ORDER." SET step2='54', settlelog=CONCAT(IFNULL(settlelog, ''), '".$message.PHP_EOL."') WHERE ordno=".$tradeid." AND step2=50");
				$db->query("UPDATE ".GD_ORDER_ITEM." SET istep='54' WHERE ordno=".$tradeid." AND istep=50");
				$message .= '네이버 마일리지 사용에 실패하여, 결제가 취소되었습니다.';
			}
			else {
				// 승인취소요망 처리
				$cardCancel->cancel_db_proc($tradeid);
				$message .= '네이버 마일리지 사용에 실패하였습니다.'.PHP_EOL.'자동결제에 실패한 관계로, 고객센터로 환불요청하여 주시기 바랍니다.';
			}

			if ($isMobilians === true) exit('FAIL');
			else return $message;
		}
	}

	/**
	 * C2. C1 프로세스 통과 시 정상처리
	 */

	// C2-1. 배송비 및 할인 적용
	$cart->chkCoupon();
	$cart->delivery = $orderData['delivery'];
	$cart->dc = isset($sess) ? $sess['dc'] : 0;
	$cart->calcu();
	$cart -> totalprice += $orderData['price'];

	// C2-2. 주문정보 저장
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

	// C2-3. 주문상품정보 저장
	$db->query("
	UPDATE ".GD_ORDER_ITEM." SET
		cyn = 'y',
		istep='1'
	WHERE ordno='".$tradeid."'
	");

	// C2-4. 주문로그 저장
	orderLog($tradeid, $r_step2[$orderData['step2']]." > ".$r_step[1]);

	// C2-5. 재고 처리
	setStock($tradeid);

	// C2-6. 상품구입시 적립금 사용
	if ($orderData['m_no'] && $orderData['emoney']) {
		setEmoney($orderData['m_no'], -$orderData['emoney'], '상품구입시 적립금 결제 사용', $tradeid);
	}

	// C2-7. 주문확인 및 입금확인 메일
	$sendMailData = $orderData;
	$sendMailData['cart'] = $cart;
	$sendMailData['str_settlekind'] = $r_settlekind[$sendMailData['settlekind']];
	sendMailCase($sendMailData['email'], 0, $sendMailData);
	sendMailCase($sendMailData['email'], 1, $sendMailData);
	unset($sendMailData);

	// C2-8. 입금확인SMS
	$GLOBALS['cfg'] = $cfg;
	$GLOBALS['dataSms'] = $orderData;
	sendSmsCase('incash', $orderData['mobileOrder']);

	// C2-9. 모빌리언스 로그작성
	$mobilians->writeLog('결제성공');

	if ($isMobilians === true) exit('SUCCESS');
	else return 'SUCCESS';

}
// 결제실패
else {
	// C3. 모빌리언스 로그 작성
	$mobilians->writeLog('결제실패');

	if ($isMobilians === true) exit('FAIL');
	else return '결제에 실패하였습니다.';
}

?>