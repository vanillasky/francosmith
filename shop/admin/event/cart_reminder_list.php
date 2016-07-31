<?php
$location = "장바구니 알림 > 장바구니 알림 안내";
@include "../_header.php";
$cartReminder = Core::loader('CartReminder');

// 장바구니 알림 리스트
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
<!-- 가짜 bar - SMS 전송시 스크립트 오류로 인해 가짜 bar 를 표시 -->
<div id="sms_bar"></div>
<form name="cart_reminder_form" method="post" action="cart_reminder_indb.php">
    <input type="hidden" name="mode" value=""/>
    <input type="hidden" name="cart_reminder_no" value=""/>
</form>
<div class="title title_top">장바구니 알림 안내<span>장바구니 알림 조건을 만들고 알림을 보낼 수 있습니다.</span>
    <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=22')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
</div>

<div id="alt_msg">
    <img src="../img/btn__icon.gif"/>
    <ul>
        <li>장바구니에 상품을 보관한 고객들에게 보관한 상품을 상기시켜주는 메세지를 발송하세요.</li>
        <li>쇼핑몰의 재방문 및 장바구니에 담긴 상품의 구매를 유도할 수 있습니다.</li>
    </ul>
</div>

<div class="title title_top">상점 SMS 정보 입력<span>상점 SMS 정보를 입력하세요.</span>
    <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=22')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
</div>
<table class="tb">
    <col class="cellC">
    <col class="cellL">
    <tr>
        <td>회신전화번호</td>
        <td><?=$cfg['smsRecall']?>
            <p class="extext">*<strong>SMS 자동발송/설정 메뉴</strong>에서 발신번호를 설정해 주세요.
                <a href="../member/sms.auto.php">[바로가기]</a></p>
        </td>
    </tr>
    <tr>
        <td colspan="2" style="padding:7px 0px 10px 10px">
            <table style="width: 700px;">
                <tr>
                    <td>
                        <? $sms = Core::loader('Sms'); ?>
                        잔여 SMS 포인트 :
                        <span style="font-weight:bold;color:#627DCE;"><?=number_format($sms->smsPt)?></span> 건
                    </td>
                    <td>
                        <div style="padding-top:7px; color:#666666" class="g9">SMS 포인트가 없는 경우 SMS가 발송되지 않습니다.</div>
                        <div style="padding-top:5px; color:#666666" class="g9">SMS포인트를 충전하여 발송하시길 바랍니다.</div>
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

<div class="title title_top">장바구니 알림 리스트<span>장바구니 알림 조건을 만들고 알림을 보낼 수 있습니다.</span>
    <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=22')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
</div>

<ol class="cart-reminder-list">
    <li class="cart-reminder-once-add">
        <div class="img-center">
            <a href="../event/cart_reminder_insert.php"><img src="../img/btn__add.gif" alt="추가"/></a></div>
        <div class="cart-reminder-btn">
            <a href="../event/cart_reminder_insert.php">장바구니 알림 추가하기 ></a>
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
                <a href="javascript:cart_reminder_delete(<?=$value['cart_reminder_no']?>);"><img src="../img/btn__delete.gif" alt="삭제"/></a>
                <a href="../event/cart_reminder_insert.php?cart_reminder_no=<?=$value['cart_reminder_no']?>"><img src="../img/btn__edit.gif" alt="수정"/></a>
            </div>
            <div class="cart-reminder-conditions">
                <h4>알림조건<span><a href="javascript:show_cart_reminder_msg_layer(<?=$value['cart_reminder_no']?>);"><img src="../img/btn__msg.gif" alt="메세지내용"/></a></span>
                </h4>
                <?=$value['cart_reminder_setting']?>
            </div>
            <?php
            if($value['cart_reminder_last_send']) {
                ?>
                <div class="cart-reminder-lastsend">
                    <h4>최근 발송 정보</h4>
                    <?=$value['cart_reminder_last_send']?>
                </div>
                <?php
            }
            if($value['cart_reminder_next_send_date']) {
                ?>
                <div class="cart-reminder-nextsend">
                    <h4>다음 발송</h4>
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
