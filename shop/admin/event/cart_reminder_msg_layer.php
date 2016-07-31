<?php
include "../_header.popup.php";

$cart_reminder = '';
$cart_reminder_no = (int)$_GET['cart_reminder_no'];
if($cart_reminder_no > 0) {
    $query = $db->_query_print("select cart_reminder_send_type, cart_reminder_lms_subject, cart_reminder_lms_msg, cart_reminder_sms_msg, cart_reminder_url_link from " . GD_CART_REMINDER . " where cart_reminder_no = [i]", $cart_reminder_no);
    $cart_reminder = $db->fetch($query);
    if($cart_reminder['cart_reminder_send_type'] == 'SMS') {
        $cart_reminder_subject = '';
        $cart_reminder_msg = strip_slashes($cart_reminder['cart_reminder_sms_msg']);
    } else {
        $cart_reminder_subject = strip_slashes($cart_reminder['cart_reminder_lms_subject']);
        $cart_reminder_msg = strip_slashes($cart_reminder['cart_reminder_lms_msg']);
    }
    if($cart_reminder['cart_reminder_url_link'] == 'Y') {
        $cart_reminder_url_link = '사용';
    } else {
        $cart_reminder_url_link = '사용안함';
    }
    $cart_reminder_reload = 'y';
}
?>
<script type="text/javascript" src="../js/cart_reminder.js"></script>
<style>
    #cart_reminder_msg {
        width: 170px;
    }

    .txt_center {
        text-align: center;
    }

    #sms_top {
        width: 146px;
        height: 56px;
        background: url('../img/sms_top.gif') no-repeat top left;
        text-align: right;
    }

    #img_special {
        margin-right: 15px;
        margin-bottom: 5px;
    }

    #td_lms_subject {
        background: url(../img/sms_subject_bg.gif) repeat-y;
        width: 146px;
        height: 38px;
        text-align: center;
    }

    #lms_subject {
        font: 9pt 굴림체;
        overflow: hidden;
        border: 0;
        width: 98px;
        height: 31px;
        background: url(../img/long_message01.gif) repeat-y;
    }

    .td_sms_msg {
        background: url(../img/sms_bg.gif) repeat-y;
        padding-top: 8px;
        width: 146px;
        height: 125px;
        text-align: center;
    }

    .td_lms_msg {
        background: url(../img/sms_long_bg.gif) repeat-y;
        padding-top: 8px;
        width: 146px;
        height: 170px;
        text-align: center;
    }

    .area_sms_msg {
        font: 9pt 굴림체;
        overflow: hidden;
        border: 0;
        width: 98px;
        height: 110px;
        background: url(../img/short_message01.gif) no-repeat;
    }

    .area_lms_msg {
        font: 9pt 굴림체;
        overflow: hidden;
        border: 0;
        width: 98px;
        height: 150px;
        background: url(../img/long_message02.gif) no-repeat;
    }

    #msg_byte {
        width: 26px;
        text-align: right;
        border: 0;
        font-size: 8pt;
        font-style: verdana;
    }

    #td_point {
        text-align: center;
        font-size: 8pt;
        font-style: verdana;
    }

    #tr_M_period {
        display: table-row;
    }

    #div_M_period_self {
        display: <?=$display['M_period_self']?>;
    }

    #div_cart_product {
        padding-top: 5px;
    }

    #tr_A_period {
        display: none;
    }

    #tr_A_send_time {
        display: none;
    }
</style>
<div id="cart_reminder_msg">
    <table class="tb">
    <col class="cellL">
    <tr>
        <td>
            <table>
            <tr>
                <td>
                    <table width="146px" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td height="30px">
                            <table width="146px" cellpadding="0" cellspacing="0" border="0">
                            <tr class="noline">
                                <td width="73px" align="right">
                                    <input type="radio" name="cart_reminder_send_type" value="sms" checked="checked" style="visibility: hidden;"><img id="img_sms_title" src="../img/btn_sms_on.gif"/>
                                </td>
                                <td width="73px" align="left">
                                    <input type="radio" name="cart_reminder_send_type" value="lms" style="visibility: hidden;"><img id="img_lms_title" src="../img/btn_lms_off.gif"/>
                                </td>
                            </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td id="sms_top"></td>
                    </tr>
                    <tr id="tr_lms_subject" style="display:none;">
                        <td id="td_lms_subject">
                            <textarea name="cart_reminder_lms_subject" id="lms_subject" onkeydown="chk_length(this,'s');" onkeyup="chk_length(this,'s');" onchange="chk_length(this,'s');" onFocus="clear_bg(this);" disabled><?=$cart_reminder_subject?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td id="td_msg" class="td_sms_msg">
                            <textarea name="cart_reminder_sms_msg" id="msg" class="area_sms_msg" onkeydown="chk_length(this,'m');" onkeyup="chk_length(this,'m');" onchange="chk_length(this,'m');" onFocus="clear_bg(this);" required="required" fld_esssential="fld_esssential" msgR="메세지를 입력해주세요."><?=$cart_reminder_msg?></textarea>
                        </td>
                    </tr>
                    <tr>
                        <td height="31px" background="../img/sms_bottom.gif" align="center">
                            <input name="vLength" type="text" id="msg_byte" value="0">/<font class="ver8" color="#262626" id="byte_limit">90 Bytes</font>
                        </td>
                    </tr>
                    <tr>
                        <td id="td_point">SMS - 1포인트 차감</td>
                    </tr>
                    <tr id="tr_lms_alert" style="display:none;">
                        <td style="padding: 5px 10px;width:126px; height:100px;" class="extext">※ 특수문자의 경우 제목에는 입력할 수 없으며, 내용에 입력하는 경우 통신사 정책에 의해 발송이 거절될 수 있습니다.</td>
                    </tr>
                    </table>
                </td>
            </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="txt_center">장바구니 링크 <?=$cart_reminder_url_link?></td>
    </tr>
    </table>
</div>
<script>
    var msgReload = '<?=$cart_reminder_reload?>';
    var reMsgType = '<?=$cart_reminder['cart_reminder_send_type']?>';

    if (msgReload == 'y') {
        if (reMsgType == 'LMS') {
            var lms_subject = document.getElementById("lms_subject");
            chk_length(lms_subject, 's');
            clear_bg(lms_subject);
        }

        var msg = document.getElementById("msg");
        chk_length(msg, 'm');
        clear_bg(msg);
    }

    table_design_load();
</script>
