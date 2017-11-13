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
    		msg('서버호스팅, 외부호스팅을 사용하는 솔루션에서는 자동 발송을 사용 할 수 없습니다.', -1);
    		exit;
    	}	
        $_POST['cart_reminder_stock_ea_updown'] = strtoupper($_POST['cart_reminder_stock_ea_updown']);
        $_POST['cart_reminder_send_type'] = strtoupper($_POST['cart_reminder_send_type']);
        $_POST['cart_reminder_member_grp'] = implode(G_STR_DIVISION, $_POST['cart_reminder_member_grp']);

        // POST로 넘어온값 정리
        // int 는 $cartReminder->cartReminderInsert에서 validation 처리
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
            msg('입력 값을 확인해주세요', -1);
            exit;
        } else if($result == 'NO_SMS_CART_URL_LINK') {
            msg('SMS 전송은 장바구니 링크를 설정할 수 없습니다.', -1);
            exit;
        } else if($result == 'MAX_ALLOW_DATA') {
            msg('장바구니 알림 등록 최대 개수를 넘을 수 없습니다.', -1);
            exit;
        } else if($result > 0) {
            msg('정상 등록되었습니다.');
            $_POST['returnUrl'] = "../event/cart_reminder_list.php";
        } else {
            //결과값이 위 3개가 아닌 경우
            msg('처리 도중 알 수 없는 문제가 발생하였습니다.\/n다시 등록해 확인해 주세요.', -1);
            exit;
        }
        break;

    case "modify" :
        $cart_reminder_no = (int)$_POST['cart_reminder_no'];
        $_POST['cart_reminder_stock_ea_updown'] = strtoupper($_POST['cart_reminder_stock_ea_updown']);
        $_POST['cart_reminder_send_type'] = strtoupper($_POST['cart_reminder_send_type']);
        $_POST['cart_reminder_member_grp'] = implode(G_STR_DIVISION, $_POST['cart_reminder_member_grp']);

        // POST로 넘어온값 정리
        // int 는 $cartReminder->cartReminderInsert에서 validation 처리
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
            msg('입력 값을 확인해주세요', -1);
            exit;
        } else if($result == 'NO_SMS_CART_URL_LINK') {
            msg('SMS 전송은 장바구니 링크를 설정할 수 없습니다.', -1);
            exit;
        } else if($result == 'MAX_ALLOW_DATA') {
            msg('등록가능한 장바구니 알림을 모두 등록하셨습니다.', -1);
            exit;
        } else if($result == 'OK') {
            msg('정상 수정되었습니다.');
            $_POST['returnUrl'] = "../event/cart_reminder_list.php";
        } else {
            //결과값이 위 3개가 아닌 경우
            msg('처리 도중 알 수 없는 문제가 발생하였습니다.\\n다시 수정해 확인해 주세요.', -1);
            exit;
        }
        break;

    case "delete" :
        $cart_reminder_no = (int)$_POST['cart_reminder_no'];
        $result = $cartReminder->cartReminderDelete($cart_reminder_no);
        if($result == 'OK') {
            msg('정상 삭제되었습니다.');
            $_POST['returnUrl'] = "../event/cart_reminder_list.php";
        } else {
            //결과값이 위 3개가 아닌 경우
            msg('처리 도중 알 수 없는 문제가 발생하였습니다.\\n다시 수정해 확인해 주세요.', -1);
            exit;
        }
        break;

    case "msend" :
        include "../../conf/config.php";
        $cart_reminder_no = (int)$_POST['cart_reminder_no'];
        $result = $cartReminder->cartReminderSend($cart_reminder_no);
        if($result == 'NO_SEND_MEMBER') {
            msg('전송할 회원이 없습니다.');
            exit;
        } else if($result == 'LOW_SMS_POINT') {
            msg('전송할 SMS포인트가 부족합니다.');
            exit;
        } else if($result == 'NOT_RESERVATION_10_MINUTES') {
            msg('자동발송은 현재시간 10분 이내로는 진행할 수 없습니다.');
            exit;
        } else if($result == 'NOT_RESERVATION_NOW_TIME') {
            msg('자동발송은 현재시간보다 이전시간으로 진행할 수 없습니다.');
            exit;
        } else if($result == 'NOT_CART_REMINDER_NO') {
            msg('처리할 장바구니 리마인드 정보가 없습니다.');
            exit;
        } else {
            $msg = "SMS 발송건수 : " . number_format(array_sum($result)) . "건 \\n ------------------- \\n 성공 : " . number_format($result['success']) . " / 실패 : " . number_format($result['fail']);
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
