<?
include "../_header.php";


// 변수 받고
	$ordno = isset($_REQUEST['ordno']) ? $_REQUEST['ordno'] : '';

	$action = isset($_POST['action']) ? $_POST['action'] : '';
	$message = isset($_POST['message']) ? $_POST['message'] : '';

	$rcvphone0 = isset($_POST['rcvphone0']) ? $_POST['rcvphone0'] : '';
	$rcvphone1 = isset($_POST['rcvphone1']) ? $_POST['rcvphone1'] : '';
	$rcvphone2 = isset($_POST['rcvphone2']) ? $_POST['rcvphone2'] : '';


// 주문번호를 이용, 쿠폰 정보를 가져옴
$query = "
	SELECT
		A.ordno, A.m_no, A.nameOrder, A.nameReceiver,A.mobileReceiver,

		C.goodsnm,

		D.cp_sno,
		D.cp_num,
		D.cp_publish,
		D.cp_sms_cnt,
		D.cp_ea

	FROM ".GD_ORDER." AS A

	INNER JOIN ".GD_ORDER_ITEM." AS B
	ON A.ordno = B.ordno

	INNER JOIN ".GD_GOODS." AS C
	ON B.goodsno = C.goodsno

	INNER JOIN ".GD_TODAYSHOP_ORDER_COUPON." AS D
	ON A.ordno = D.ordno

	WHERE A.ordno = '$ordno'
";
$data = $db->fetch($query,1);

// 데이터 없으믄 쿠폰구매 아닌거임.
if (!$data) msg("해당 주문이 존재하지 않습니다",'close');


### 권한 체크
if ($sess[m_no]){
	if ($data[m_no]!=$sess[m_no]) msg("접근권한이 없습니다",'close');
} else {
	if ($data[nameOrder]!=$_COOKIE[guest_nameOrder] || $data[m_no]) msg("접근권한이 없습니다",'close');
}



// 발송 처리


	$error = '';

	if ($data['cp_sms_cnt'] < 3) {

		$sms = Core::loader('sms');
		$formatter = Core::loader('stringFormatter');

		$mobileReceiver = $rcvphone0.$rcvphone1.$rcvphone2;
		if (($mobileReceiver = $formatter->get($mobileReceiver,'dial','-')) == false) {	// 수신자 전화번호 형식이 안맞으면..
			$error = '휴대폰번호를 올바르게 입력해 주세요.';
			msg($error , -1);
			exit;
		}


		if ($sms->smsPt > 0) {	// 잔여 SMS 체크

			if (! $sms->send($message, $data['mobileReceiver'], $cfg['smsRecall'])) {
				$error = 'SMS 서비스를 사용할 수 없습니다.';
			}
			else {
				$sms->update();
			}

		}
		else {
			$error = 'SMS 서비스를 사용할 수 없습니다.';
		}	// if

	}
	else {
		$error = '쿠폰 문자 전송은 최대 3회 까지만 가능합니다. 고객님은 이미 3회 전송하셨습니다';
	}

	if ($error) {
		$msg = $error;
	}
	else {
		$db->query("UPDATE ".GD_TODAYSHOP_ORDER_COUPON." SET cp_sms_cnt = cp_sms_cnt + 1 WHERE cp_sno = '".$data['cp_sno']."'");
		$msg = '쿠폰번호 '.$data['cp_num'].' 을 '.(++$data['cp_sms_cnt']).'회 재전송 하였습니다';
	}

	msg($msg , 'close');
?>
