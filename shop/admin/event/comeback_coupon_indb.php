<?php
include "../lib.php";
$comebackCoupon = Core::loader('comebackCoupon');
if(get_magic_quotes_gpc()) {
	stripslashes_all($_POST);
	stripslashes_all($_GET);
}
$_POST = add_slashes($_POST);
$mode = $_POST['mode'] ? $_POST['mode'] : $_GET['mode'];

if(!$_POST['returnUrl']) $_POST['returnUrl'] = $_SERVER['HTTP_REFERER'];

switch ($mode) {
	case "insert" :
		$dataArr = array(
			'sno' => $_POST['sno'],
			'title' => $_POST['title'],
			'type' => $_POST['type'],
			'step' => $_POST['step'][$_POST['type']],
			'date' => $_POST['date'][$_POST['type']],
			'price' => @implode(',',$_POST['price']),
			'goodsno' => @implode(',',$_POST['e_step']),
			'couponyn' => $_POST['couponyn'],
			'couponcd' => $_POST['couponcd'],
			'smsyn' => $_POST['smsyn'],
			'sms_type' => $_POST['sms_type'],
			'lms_subject' => $_POST['lms_subject'],
			'msg' => $_POST[$_POST['sms_type'].'_msg'],
			'linkyn' => $_POST['linkyn'],
		);
	
		$result = $comebackCoupon->comebackCouponInsert($dataArr);

		if ($result == 'NOT_VALID_DATA') {
			msg('�Է� ���� Ȯ�����ּ���', -1);
			exit;
        } else if ($result > 0) {
			msg('���� ��ϵǾ����ϴ�.');
			$_POST['returnUrl'] = "./comeback_coupon_list.php";
		}
		break;
	case "copy":
		$query = $db->_query_print("SELECT * FROM ".GD_COMEBACK_COUPON." WHERE sno = '[i]'", $_GET['sno']);
		$data = $db->fetch($query);
		if (!$data) {
			msg('������ �Ĺ�����/SMS ������ �����ϴ�.',-1);
			exit;
		} else {
			$cnt_query = $db->_query_print("SELECT COUNT(*) FROM ".GD_COMEBACK_COUPON." WHERE copysno = '[i]'", $data['sno']);
			list($copyNum) = $db->fetch($cnt_query);
			$copyNum++;
		}

		$dataArr = array(
			'copysno' => $data['sno'],
			'title' => $data['title'] . " (".$copyNum.")",
			'type' => $data['type'],
			'step' => $data['step'],
			'date' => $data['date'],
			'price' => $data['price'],
			'goodsno' => $data['goodsno'],
			'couponyn' => $data['couponyn'],
			'couponcd' => $data['couponcd'],
			'smsyn' => $data['smsyn'],
			'sms_type' => $data['sms_type'],
			'lms_subject' => $data['lms_subject'],
			'msg' => $data['msg'],
			'linkyn' => $data['linkyn'],
		);

		$result = $comebackCoupon->comebackCouponInsert($dataArr);
		if ($result == 'NOT_VALID_DATA') {
			msg('�Է� ���� Ȯ�����ּ���', -1);
			exit;
        } else if ($result > 0) {
			msg('���� ��ϵǾ����ϴ�.');
			$_POST['returnUrl'] = "./comeback_coupon_list.php";
		}
		break;
	case "send":
		if (!$_GET['sno']) {
			msg('',-1);
			exit;
		}
		$result = $comebackCoupon->comebackCouponSend($_GET['sno']);
		if ($result == 'OK') {
			msg('���������� �߼۵Ǿ����ϴ�.', -1);
			$_POST['returnUrl'] = "./comeback_coupon_list.php";
		} else if ($result == 'NO_TARGET') {
			msg('�߼� ����� �����ϴ�.', -1);
			exit;
		} else if ($result == 'DISABLED_COUPON') {
			msg('����Ͻ� ������ ���Ⱓ�� ����Ǿ� ������ �߱��� �� �����ϴ�. ����/��� �� �߼��Ͻñ� �ٶ��ϴ�.', -1);
			exit;
		} else if ($result == 'LOW_SMS_POINT') {
			msg('�ܿ� SMS ����Ʈ�� �����Ͽ� SMS�� �߼��� �� �����ϴ�. SMS ����Ʈ ���� �� �߼��Ͻñ� �ٶ��ϴ�.', -1);
			exit;
		}
		break;
	case "delete":
		$result = $comebackCoupon->comebackCouponDelete($_GET['sno']);
        if($result == 'OK') {
            msg('���� �����Ǿ����ϴ�.');
            $_POST['returnUrl'] = "./comeback_coupon_list.php";
        } else {
            msg('ó�� ���� �� �� ���� ������ �߻��Ͽ����ϴ�.\\n�ٽ� ������ Ȯ���� �ּ���.', -1);
            exit;
        }
		break;
}

go($_POST['returnUrl']);