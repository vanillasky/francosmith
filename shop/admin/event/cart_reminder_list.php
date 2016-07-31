<?php
$location = "��ٱ��� �˸� > ��ٱ��� �˸� �ȳ�";
@include "../_header.php";
$cartReminder = Core::loader('CartReminder');

// ��ٱ��� �˸� ����Ʈ
$cartReminderList = $cartReminder->cartReminderList();
?>
<script type="text/javascript" src="../js/cart_reminder.js"></script>
<style>
    #alt_msg {
        border: solid 4px #dce1e1;
        border-collapse: collapse;
        margin-bottom: 20px;
        padding: 10px 0 10px 10px;
    }

    #alt_msg img {
        float: left;
        padding: 5px 15px;
    }

    .background-auto {
        background-color: #f2f2f2;
    }

    .cart-reminder-list {
        width: 100%;
    }

    .cart-reminder-once-add {
        position: relative;
        float: left;
        width: 410px;
        height: 320px;
        background: #edf8fe border-box;
        margin: 5px;
        border: #CACACA solid 2px;
    }

    .cart-reminder-once-add .img-center {
        position: absolute;
        width: 410px;
        height: 270px;
        line-height: 270px;
    }

    .cart-reminder-once-add .img-center img {
        position: absolute;
        top: 75px;
        left: 140px;
        width: 121px;
        height: 120px;
        vertical-align: middle;
    }

    .cart-reminder-once {
        position: relative;
        float: left;
        width: 410px;
        height: 320px;
        margin: 5px;
        border: #CACACA solid 2px;
    }

    .cart-reminder-btn a {
        position: absolute;
        bottom: 0px;
        line-height: 50px;
        width: 410px;
        height: 50px;
        vertical-align: middle;
        text-align: center;
        color: #fff;
        font-size: 16px;
        font-weight: bold;
        background: #3ba3ec;
    }

    .cart-reminder-btn span {
        padding-left: 10px;
        font-size: 12px;
    }

    .cart-reminder-once div {
        border-bottom: #CACACA solid 2px;
    }

    .cart-reminder-once div:last-child {
        border: none;
    }

    .cart-reminder-once .cart-reminder-title {
        width: 410px;
        height: 40px;
        display: table;
    }

    .cart-reminder-once .cart-reminder-title h3 {
        float: left;
        width: 310px;
        display: table-cell;
        vertical-align: middle;
        padding-left: 10px;
        color: #333;
    }

    .cart-reminder-once .cart-reminder-title img {
        float: right;
        display: table-cell;
        vertical-align: middle;
        padding-top: 10px;
        padding-right: 5px;
    }

    .cart-reminder-once .cart-reminder-conditions {
        height: 120px;
        padding-left: 10px;
        padding-bottom: 10px;
    }

    .cart-reminder-once .cart-reminder-conditions h4 span {
        padding-right: 10px;
        float: right;
    }

    .cart-reminder-once .cart-reminder-conditions li {
        list-style: decimal;
    }

    .cart-reminder-once .cart-reminder-lastsend {
        padding-left: 10px;
        padding-bottom: 10px;
    }

    .cart-reminder-once .cart-reminder-nextsend {
        position: absolute;
        bottom: 0px;
        width: 410px;
        height: 50px;
        line-height: 50px;
        color: #118adb;
        vertical-align: middle;
        font-size: 12px;
        font-weight: bold;
        background: #ecf8fe;
    }

    .cart-reminder-once .cart-reminder-nextsend h4 {
        margin: 0;
        padding-left: 10px;
        float: left;
    }

    .cart-reminder-once .cart-reminder-nextsend span {
        margin: 0;
        padding-left: 10px;
    }

    #sms_bar {
        width: 0;
        height: 10px;
        display: none;
    }
    .alert_msg {
        color: #ff2222;
    }
</style>
<!-- ��¥ bar - SMS ���۽� ��ũ��Ʈ ������ ���� ��¥ bar �� ǥ�� -->
<div id="sms_bar"></div>
<form name="cart_reminder_form" method="post" action="cart_reminder_indb.php">
    <input type="hidden" name="mode" value=""/>
    <input type="hidden" name="cart_reminder_no" value=""/>
</form>
<div class="title title_top">��ٱ��� �˸� �ȳ�<span>��ٱ��� �˸� ������ ����� �˸��� ���� �� �ֽ��ϴ�.</span>
    <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=22')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
</div>

<div id="alt_msg">
    <img src="../img/btn__icon.gif"/>
    <ul>
        <li>��ٱ��Ͽ� ��ǰ�� ������ ���鿡�� ������ ��ǰ�� �������ִ� �޼����� �߼��ϼ���.</li>
        <li>���θ��� ��湮 �� ��ٱ��Ͽ� ��� ��ǰ�� ���Ÿ� ������ �� �ֽ��ϴ�.</li>
    </ul>
</div>

<div class="title title_top">���� SMS ���� �Է�<span>���� SMS ������ �Է��ϼ���.</span>
    <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=22')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
</div>
<table class="tb">
    <col class="cellC">
    <col class="cellL">
    <tr>
        <td>ȸ����ȭ��ȣ</td>
        <td><?=$cfg['smsRecall']?>
            <p class="extext">*<strong>SMS �ڵ��߼�/���� �޴�</strong>���� �߽Ź�ȣ�� ������ �ּ���.
                <a href="../member/sms.auto.php">[�ٷΰ���]</a></p>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="padding:7px 0px 10px 10px">
            <table style="width: 700px;">
                <tr>
                    <td>
                        <? $sms = Core::loader('Sms'); ?>
                        �ܿ� SMS ����Ʈ :
                        <span style="font-weight:bold;color:#627DCE;"><?=number_format($sms->smsPt)?></span> ��
                    </td>
                    <td>
                        <div style="padding-top:7px; color:#666666" class="g9">SMS ����Ʈ�� ���� ��� SMS�� �߼۵��� �ʽ��ϴ�.</div>
                        <div style="padding-top:5px; color:#666666" class="g9">SMS����Ʈ�� �����Ͽ� �߼��Ͻñ� �ٶ��ϴ�.</div>
                    </td>
                    <td>
                        <a href="../member/sms.pay.php"><img src="../img/btn_point_pay.gif"/></a>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<div style="padding-top:15px"></div>

<div class="title title_top">��ٱ��� �˸� ����Ʈ<span>��ٱ��� �˸� ������ ����� �˸��� ���� �� �ֽ��ϴ�.</span>
    <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=22')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
</div>

<ol class="cart-reminder-list">
    <li class="cart-reminder-once-add">
        <div class="img-center">
            <a href="../event/cart_reminder_insert.php"><img src="../img/btn__add.gif" alt="�߰�"/></a></div>
        <div class="cart-reminder-btn">
            <a href="../event/cart_reminder_insert.php">��ٱ��� �˸� �߰��ϱ� ></a>
        </div>
    </li>
    <?php
    foreach($cartReminderList as $key => $value) {
        if($value['cart_reminder_type'] == 'A') {
            $background_auto = 'background-auto';
        } else {
            $background_auto = '';
        }
        ?>
        <li class="cart-reminder-once">
            <div class="cart-reminder-title <?=$background_auto?>">
                <h3><?=$value['cart_reminder_title']?></h3>
                <a href="javascript:cart_reminder_delete(<?=$value['cart_reminder_no']?>);"><img src="../img/btn__delete.gif" alt="����"/></a>
                <a href="../event/cart_reminder_insert.php?cart_reminder_no=<?=$value['cart_reminder_no']?>"><img src="../img/btn__edit.gif" alt="����"/></a>
            </div>
            <div class="cart-reminder-conditions">
                <h4>�˸�����<span><a href="javascript:show_cart_reminder_msg_layer(<?=$value['cart_reminder_no']?>);"><img src="../img/btn__msg.gif" alt="�޼�������"/></a></span>
                </h4>
                <?=$value['cart_reminder_setting']?>
            </div>
            <?php
            if($value['cart_reminder_last_send']) {
                ?>
                <div class="cart-reminder-lastsend">
                    <h4>�ֱ� �߼� ����</h4>
                    <?=$value['cart_reminder_last_send']?>
                </div>
                <?php
            }
            if($value['cart_reminder_next_send_date']) {
                ?>
                <div class="cart-reminder-nextsend">
                    <h4>���� �߼�</h4>
                    <?=$value['cart_reminder_next_send_date']?>
                    <?=$value['cart_reminder_send_point']?>
                </div>
                <?php
            }
            if($value['cart_reminder_send_btn']) {
                ?>
                <div class="cart-reminder-btn">
                    <?=$value['cart_reminder_send_btn']?>
                </div>
                <?php
            }
            ?>
        </li>
        <?php
    }
    ?>
</ol>
<? include "../_footer.php"; ?>
