<?php
include "../lib.php";
$cartReminder = Core::loader('CartReminder');
require_once("../../lib/qfile.class.php");
$qfile = new qfile();
if(get_magic_quotes_gpc()) {
    stripslashes_all($_POST);
    stripslashes_all($_GET);
}
$_POST = add_slashes($_POST);

if(!$_POST['returnUrl']) $_POST['returnUrl'] = $_SERVER['HTTP_REFERER'];

switch($_POST['mode']) {
    case "insert" :
    	if(($godo['webCode'] == 'webhost_outside' || $godo['webCode'] == 'webhost_server') && $_POST['cart_reminder_type'] == 'A'){
    		msg('����ȣ����, �ܺ�ȣ������ ����ϴ� �ַ�ǿ����� �ڵ� �߼��� ��� �� �� �����ϴ�.', -1);
    		exit;
    	}	
        $_POST['cart_reminder_stock_ea_updown'] = strtoupper($_POST['cart_reminder_stock_ea_updown']);
        $_POST['cart_reminder_send_type'] = strtoupper($_POST['cart_reminder_send_type']);
        $_POST['cart_reminder_member_grp'] = implode(G_STR_DIVISION, $_POST['cart_reminder_member_grp']);

        // POST�� �Ѿ�°� ����
        // int �� $cartReminder->cartReminderInsert���� validation ó��
        $dataArr = array(
            'cart_reminder_title' => (string)$_POST['cart_reminder_title'],
            'cart_reminder_type' => (string)$_POST['cart_reminder_type'],
            'cart_reminder_period' => $_POST['cart_reminder_period'],
            'cart_reminder_period_start_date' => $_POST['cart_reminder_period_start_date'],
            'cart_reminder_period_end_date' => $_POST['cart_reminder_period_end_date'],
            'cart_reminder_send_time' => $_POST['cart_reminder_send_time'],
            'cart_reminder_goods_show' => (string)$_POST['cart_reminder_goods_show'],
            'cart_reminder_goods_soldout' => (string)$_POST['cart_reminder_goods_soldout'],
            'cart_reminder_stock_ea' => $_POST['cart_reminder_stock_ea'],
            'cart_reminder_stock_ea_updown' => (string)$_POST['cart_reminder_stock_ea_updown'],
            'cart_reminder_member_grp' => (string)$_POST['cart_reminder_member_grp'],
            'cart_reminder_send_type' => (string)$_POST['cart_reminder_send_type'],
            'cart_reminder_lms_subject' => (string)$_POST['cart_reminder_lms_subject'],
            'cart_reminder_lms_msg' => (string)$_POST['cart_reminder_lms_msg'],
            'cart_reminder_sms_msg' => (string)$_POST['cart_reminder_sms_msg'],
            'cart_reminder_url_link' => (string)$_POST['cart_reminder_url_link'],
            'cart_reminder_insert_date' => G_CONST_NOW,
        );
        $result = $cartReminder->cartReminderInsert($dataArr);

        if($result == 'NOT_VALID_DATA') {
            msg('�Է� ���� Ȯ�����ּ���', -1);
            exit;
        } else if($result == 'NO_SMS_CART_URL_LINK') {
            msg('SMS ������ ��ٱ��� ��ũ�� ������ �� �����ϴ�.', -1);
            exit;
        } else if($result == 'MAX_ALLOW_DATA') {
            msg('��ٱ��� �˸� ��� �ִ� ������ ���� �� �����ϴ�.', -1);
            exit;
        } else if($result > 0) {
            msg('���� ��ϵǾ����ϴ�.');
            $_POST['returnUrl'] = "../event/cart_reminder_list.php";
        } else {
            //������� �� 3���� �ƴ� ���
            msg('ó�� ���� �� �� ���� ������ �߻��Ͽ����ϴ�.\/n�ٽ� ����� Ȯ���� �ּ���.', -1);
            exit;
        }
        break;

    case "modify" :
        $cart_reminder_no = (int)$_POST['cart_reminder_no'];
        $_POST['cart_reminder_stock_ea_updown'] = strtoupper($_POST['cart_reminder_stock_ea_updown']);
        $_POST['cart_reminder_send_type'] = strtoupper($_POST['cart_reminder_send_type']);
        $_POST['cart_reminder_member_grp'] = implode(G_STR_DIVISION, $_POST['cart_reminder_member_grp']);

        // POST�� �Ѿ�°� ����
        // int �� $cartReminder->cartReminderInsert���� validation ó��
        $dataArr = array(
            'cart_reminder_title' => (string)$_POST['cart_reminder_title'],
            'cart_reminder_type' => (string)$_POST['cart_reminder_type'],
            'cart_reminder_period' => $_POST['cart_reminder_period'],
            'cart_reminder_period_start_date' => $_POST['cart_reminder_period_start_date'],
            'cart_reminder_period_end_date' => $_POST['cart_reminder_period_end_date'],
            'cart_reminder_send_time' => $_POST['cart_reminder_send_time'],
            'cart_reminder_goods_show' => (string)$_POST['cart_reminder_goods_show'],
            'cart_reminder_goods_soldout' => (string)$_POST['cart_reminder_goods_soldout'],
            'cart_reminder_stock_ea' => $_POST['cart_reminder_stock_ea'],
            'cart_reminder_stock_ea_updown' => (string)$_POST['cart_reminder_stock_ea_updown'],
            'cart_reminder_member_grp' => (string)$_POST['cart_reminder_member_grp'],
            'cart_reminder_send_type' => (string)$_POST['cart_reminder_send_type'],
            'cart_reminder_lms_subject' => (string)$_POST['cart_reminder_lms_subject'],
            'cart_reminder_lms_msg' => (string)$_POST['cart_reminder_lms_msg'],
            'cart_reminder_sms_msg' => (string)$_POST['cart_reminder_sms_msg'],
            'cart_reminder_url_link' => (string)$_POST['cart_reminder_url_link'],
            'cart_reminder_update_date' => G_CONST_NOW,
        );

        $result = $cartReminder->cartReminderModify($cart_reminder_no, $dataArr);
        if($result == 'NOT_VALID_DATA') {
            msg('�Է� ���� Ȯ�����ּ���', -1);
            exit;
        } else if($result == 'NO_SMS_CART_URL_LINK') {
            msg('SMS ������ ��ٱ��� ��ũ�� ������ �� �����ϴ�.', -1);
            exit;
        } else if($result == 'MAX_ALLOW_DATA') {
            msg('��ϰ����� ��ٱ��� �˸��� ��� ����ϼ̽��ϴ�.', -1);
            exit;
        } else if($result == 'OK') {
            msg('���� �����Ǿ����ϴ�.');
            $_POST['returnUrl'] = "../event/cart_reminder_list.php";
        } else {
            //������� �� 3���� �ƴ� ���
            msg('ó�� ���� �� �� ���� ������ �߻��Ͽ����ϴ�.\\n�ٽ� ������ Ȯ���� �ּ���.', -1);
            exit;
        }
        break;

    case "delete" :
        $cart_reminder_no = (int)$_POST['cart_reminder_no'];
        $result = $cartReminder->cartReminderDelete($cart_reminder_no);
        if($result == 'OK') {
            msg('���� �����Ǿ����ϴ�.');
            $_POST['returnUrl'] = "../event/cart_reminder_list.php";
        } else {
            //������� �� 3���� �ƴ� ���
            msg('ó�� ���� �� �� ���� ������ �߻��Ͽ����ϴ�.\\n�ٽ� ������ Ȯ���� �ּ���.', -1);
            exit;
        }
        break;

    case "msend" :
        include "../../conf/config.php";
        $cart_reminder_no = (int)$_POST['cart_reminder_no'];
        $result = $cartReminder->cartReminderSend($cart_reminder_no);
        if($result == 'NO_SEND_MEMBER') {
            msg('������ ȸ���� �����ϴ�.');
            exit;
        } else if($result == 'LOW_SMS_POINT') {
            msg('������ SMS����Ʈ�� �����մϴ�.');
            exit;
        } else if($result == 'NOT_RESERVATION_10_MINUTES') {
            msg('�ڵ��߼��� ����ð� 10�� �̳��δ� ������ �� �����ϴ�.');
            exit;
        } else if($result == 'NOT_RESERVATION_NOW_TIME') {
            msg('�ڵ��߼��� ����ð����� �����ð����� ������ �� �����ϴ�.');
            exit;
        } else if($result == 'NOT_CART_REMINDER_NO') {
            msg('ó���� ��ٱ��� �����ε� ������ �����ϴ�.');
            exit;
        } else {
            $msg = "SMS �߼۰Ǽ� : " . number_format(array_sum($result)) . "�� \\n ------------------- \\n ���� : " . number_format($result['success']) . " / ���� : " . number_format($result['fail']);
            msg($msg, '../event/cart_reminder_list.php', 'parent');
            exit;
        }
        break;

    case "countsendmember" :
        $cart_reminder_no = (int)$_POST['cart_reminder_no'];
        $result = $cartReminder->getCartReminderSendMobileNumber($cart_reminder_no);
        if($result) {
            echo count($result);
            exit;
        } else {
            echo "NO_SEND_MEMBER";
            exit;
        }
        break;

    case "getsendmember" :
        $cart_reminder_no = (int)$_POST['cart_reminder_no'];
        $result = $cartReminder->getCartReminderSendMobileNumberQuery($cart_reminder_no);
        echo $result;
        exit;
        break;

    default :
        break;
}
go($_POST['returnUrl']);
exit;
?>
