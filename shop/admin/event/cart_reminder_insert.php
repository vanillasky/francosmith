<?php
$location = "장바구니 알림 > 장바구니 알림 만들기";
include "../_header.php";
$cart_reminder = '';
$cart_reminder_no = (int)$_GET['cart_reminder_no'];
if($cart_reminder_no > 0) {
    $query = $db->_query_print("select * from " . GD_CART_REMINDER . " where cart_reminder_no = [i]", $cart_reminder_no);
    $cart_reminder = $db->fetch($query);
    if($cart_reminder) {
        $cart_reminder['cart_reminder_no'] = $cart_reminder_no;
        $checked['cart_reminder_type'][$cart_reminder['cart_reminder_type']] = 'checked="checked"';
        $selected['cart_reminder_period'][$cart_reminder['cart_reminder_period']] = 'selected="selected"';
        if($cart_reminder['cart_reminder_type'] == 'A') {
            $display['A_period'] = "display_table_row";
            $display['A_send_time'] = "display_table_row";
            $display['M_period'] = "display_none";
        } else {
            $display['M_period'] = "display_table_row";
            $display['M_period_self'] = ($cart_reminder['cart_reminder_period']) == 5 ? "inline" : "none";
            $display['A_period'] = "display_none";
            $display['A_send_time'] = "display_none";
        }
        $selected['cart_reminder_send_time'][$cart_reminder['cart_reminder_send_time']] = 'selected="selected"';
        $checked['cart_reminder_goods_show'][$cart_reminder['cart_reminder_goods_show']] = 'checked="checked"';
        $checked['cart_reminder_goods_soldout'][$cart_reminder['cart_reminder_goods_soldout']] = 'checked="checked"';
        $selected['cart_reminder_stock_ea_updown'][$cart_reminder['cart_reminder_stock_ea_updown']] = 'selected="selected"';
        $cart_reminder_member_grp = explode(G_STR_DIVISION, $cart_reminder['cart_reminder_member_grp']);
        foreach($cart_reminder_member_grp as $value) {
            $checked['cart_reminder_member_grp'][$value] = 'checked="checked"';
        }
        if($cart_reminder['cart_reminder_send_type'] == 'SMS') {
            $cart_reminder_subject = '';
            $cart_reminder_msg = strip_slashes($cart_reminder['cart_reminder_sms_msg']);
        } else {
            $cart_reminder_subject = strip_slashes($cart_reminder['cart_reminder_lms_subject']);
            $cart_reminder_msg = strip_slashes($cart_reminder['cart_reminder_lms_msg']);
        }
        $checked['cart_reminder_url_link'][$cart_reminder['cart_reminder_url_link']] = 'checked="checked"';


        $cart_reminder_reload = 'y';
        $cart_reminder['mode'] = 'modify';
    } else {
        msg("존재하지 않는 장바구니 알림입니다.", -1);
        exit;
    }
} else {
    $cartReminder = Core::loader('CartReminder');
    $cartReminderMaxCount = $cartReminder->getCartReminderMaxCount();
    $query_count = "select count(cart_reminder_no) from " . GD_CART_REMINDER;
    list($cartReminderNowCount) = $db->fetch($query_count);
    if($cartReminderNowCount >= $cartReminderMaxCount) {
        msg('등록가능한 장바구니 알림을 모두 등록하셨습니다.', -1);
        exit;
    }
    $checked['cart_reminder_type']['M'] = 'checked="checked"';
    $display['M_period'] = "display_table_row";
    $display['M_period_self'] = "display_none";
    $display['A_period'] = "display_none";
    $display['A_send_time'] = "display_none";
    $cart_reminder['cart_reminder_send_type'] = 'LMS';
    $cart_reminder_msg = '(광고) [{shopName}] 고객님의 장바구니에 상품이 담겨 있습니다. 아래 장바구니 바로가기를 통해 바로 확인하실 수 있습니다. [장바구니링크]';
    $checked['cart_reminder_url_link']['Y'] = 'checked="checked"';
    $cart_reminder_reload = 'y';
    $cart_reminder['mode'] = 'insert';
    $checked['cart_reminder_member_grp_all']['Y'] = 'checked="checked"';
    foreach(member_grp() as $v) {
        $checked['cart_reminder_member_grp'][$v['level']] = 'checked="checked"';
    }
}
?>
<script type="text/javascript" src="../js/cart_reminder.js"></script>
<style>
    .display_none {
        display: none;
    }

    .display_table_row {
        display: table-row;
        *display: inline-block;
        _display: inline-block;
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

    #div_cart_product {
        padding-top: 5px;
    }

    .msg_sms_alert {
        height: 100px;
    }
    .msg_sms_alert h4 {
        color: #ff2222;
    }
    .msg_sms_alert a {
        color: #0000ff;
    }
    .msg_sms_alert a:hover {
        text-decoration: underline;
    }
    .msg_sms_code{
        height: 200px;
    }
</style>
<div class="title title_top">장바구니 알림 조건 설정<span>장바구니에 상품을 보관하고 있는 회원들 중 설정한 조건에 맞는 회원들을 대상으로 알림을 발송합니다.</span>
    <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=22')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
</div>

<form name="cartproductfm1" method="get" action="../log/popu.cart.php" target="_blank">
    <input type="hidden" name="stype" value="d"/>
    <input type="hidden" id="sdate" name="regdt[]" value=""/>
    <input type="hidden" id="edate" name="regdt[]" value=""/>
</form>
<form name="smsfm1" method="post" action="../event/cart_reminder_indb.php" onsubmit="chkForm(this)">
    <input type="hidden" name="mode" value="<?=$cart_reminder['mode']?>"/>
    <input type="hidden" name="cart_reminder_no" value="<?=$cart_reminder['cart_reminder_no']?>"/>
    <table class="tb">
        <col class="cellC">
        <col class="cellL">
        <tr>
            <td>장바구니 알림 이름</td>
            <td>
                <input type="text" name="cart_reminder_title" value="<?=$cart_reminder['cart_reminder_title']?>" class="line" required="required" fld_esssential="fld_esssential" msgR="이름을 등록하셔야 합니다."/>
            </td>
        </tr>
        <tr>
            <td>발송 방법</td>
            <td>
                <input type="radio" name="cart_reminder_type" value="M" <?=$checked['cart_reminder_type']['M']?> onclick="javascript:chg_send_type();" required="required" fld_esssential="fld_esssential" msgR="반드시 하나는 선택하셔야 합니다."/> 수동발송
            </td>
        </tr>
        <tr id="tr_M_period" class="<?=$display['M_period']?>">
            <td>발송 대상</td>
            <td>
                <select name="cart_reminder_period" onchange="javascript:chg_M_period();" class="line" required="required" fld_esssential="fld_esssential" msgO="반드시 하나는 선택하셔야 합니다.">
                    <option value="">=선택=</option>
                    <option value="1" <?=$selected['cart_reminder_period'][1]?>>최근 1일~3일</option>
                    <option value="2" <?=$selected['cart_reminder_period'][2]?>>최근 3일~5일</option>
                    <option value="3" <?=$selected['cart_reminder_period'][3]?>>최근 3일~7일</option>
                    <option value="4" <?=$selected['cart_reminder_period'][4]?>>최근 5일~7일</option>
                    <option value="5" <?=$selected['cart_reminder_period'][5]?>>직접설정</option>
                </select>
                <span id="div_M_period_self" class="<?=$display['M_period_self']?>">
                    <input type="text" name="cart_reminder_period_start_date" value="<?=$cart_reminder['cart_reminder_period_start_date']?>" onclick="calendar(event)" class="line" size="10"/>
                    ~
                    <input type="text" name="cart_reminder_period_end_date" value="<?=$cart_reminder['cart_reminder_period_end_date']?>" onclick="calendar(event)" class="line" size="10"/>
                </span>
                <span>동안 장바구니에 상품을 담은 회원들에게 알림을 1회 발송합니다.</span>

                <div id="div_cart_product">
                    <span id="span_cart_period"></span>
                    <a id="cart_product" href="javascript:link_date();"><img src="../img/btn__cartview.gif"></a></div>
                <p class="extext">* 장바구니에 담았던 상품을 구매 및 삭제한 회원들에게는 알림을 발송하지 않습니다.</p>

                <p class="extext">* 핸드폰번호 정보가 있고, SMS 수신 허용을 한 회원을 대상으로만 알림을 발송합니다.</p>
            </td>
        </tr>
        <tr id="tr_A_period" class="<?=$display['A_period']?>">
            <td>발송 대상</td>
            <td>
                <select name="cart_reminder_period" disabled="disabled" class="line" fld_esssential="fld_esssential" msgO="반드시 하나는 선택하셔야 합니다.">
                    <option value="">=선택=</option>
                    <?php
                    for($p = 1; $p <= 7; $p++) {
                        ?>
                        <option value="<?=$p?>" <?=$selected['cart_reminder_period'][$p]?>><?=$p?>일전</option>
                        <?php
                    }
                    ?>
                </select>

                <p class="extext">* 장바구니에 담았던 상품을 구매 및 삭제한 회원들에게는 알림을 발송하지 않습니다.</p>

                <p class="extext">* 핸드폰번호 정보가 있고, SMS 수신 허용을 한 회원을 대상으로만 알림을 발송합니다.</p>

                <p class="extext">* 알림을 설정한 다음 날부터 알림이 발송 됩니다.</p>
            </td>
        </tr>
        <tr id="tr_A_send_time" class="<?=$display['A_send_time']?>">
            <td>발송 시점</td>
            <td>
                <select name="cart_reminder_send_time" fld_esssential="fld_esssential" class="line" msgO="반드시 하나는 선택하셔야 합니다.">
                    <option value="">=선택=</option>
                    <?php
                    for($s = 8; $s <= 21; $s++) {
                        ?>
                        <option value="<?=$s?>" <?=$selected['cart_reminder_send_time'][$s]?>>매일 <?=$s?> 시</option>
                        <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>상품 옵션</td>
            <td>
                <input type="checkbox" name="cart_reminder_goods_show" value="Y" <?=$checked['cart_reminder_goods_show']['Y']?> /> 미진열 상품 포함
                <input type="checkbox" name="cart_reminder_goods_soldout" value="Y" <?=$checked['cart_reminder_goods_soldout']['Y']?> /> 품절 상품 포함
            </td>
        </tr>
        <tr>
            <td>재고량</td>
            <td>
                <input type="text" name="cart_reminder_stock_ea" size="5" maxlength="4" value="<?=$cart_reminder['cart_reminder_stock_ea']?>" onkeydown="onlynumber();" class="line"/> 개
                <select name="cart_reminder_stock_ea_updown" class="line">
                    <option value="UP" <?=$selected['cart_reminder_stock_ea_updown']['UP']?>>이상</option>
                    <option value="DOWN" <?=$selected['cart_reminder_stock_ea_updown']['DOWN']?>>이하</option>
                </select>

                <p class="extext">* 공란으로 두면 재고량에 관계없이 알림 메시지가 발송됩니다.</p>
                <p class="extext">* 상품등록에서 재고량이 미입력된 상품은 재고량을 0으로 처리합니다.</p>
            </td>
        </tr>
        <tr>
            <td>회원그룹 선택</td>
            <td>
                <input type="checkbox" name="cart_reminder_member_grp_all" value="Y" onchange="javascript:chk_member_grp_all();" <?=$checked['cart_reminder_member_grp_all']['Y']?> /> 전체<br>
                <? foreach(member_grp() as $v) { ?>
                    <input type="checkbox" name="cart_reminder_member_grp[]" value="<?=$v['level']?>" onchange="javascript:chk_member_grp_once();" <?=$checked['cart_reminder_member_grp'][$v['level']]?> /> <?=$v['grpnm']?>
                <? } ?>
            </td>
        </tr>
    </table>

    <div class="title title_top">알림 메세지 <span>장바구니 알림 메세지를 작성합니다.</span>
        <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=22')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
    </div>
    <table class="tb">
        <col class="cellC">
        <col class="cellL">
        <tr>
            <td>내용</td>
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
                        <td>
                            <div class="msg_sms_alert">
                                <h4>※ 정통망법에 따른 광고성 정보 전송 준수사항을 꼭 확인해주세요.</h4>
                                <a href="http://www.godo.co.kr/news/notice_view.php?board_idx=1237&page=2" target="_blank">[정통망법에 따른 광고성 정보 전송 관련 필수 준수사항 안내 바로가기]</a>
                            </div>
                            <div class="msg_sms_code">
                                <h4>※ SMS 자동발송 문구에 사용되는 치환코드 안내</h4>
                                {shopName} : 쇼핑몰명
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>장바구니 링크 <br>사용 설정</td>
            <td>
                <input type="radio" name="cart_reminder_url_link" value="Y" <?=$checked['cart_reminder_url_link']['Y']?> onchange="javascript:chg_cart_reminder_url_link();" required="required" fld_esssential="fld_esssential" msgR="반드시 하나는 선택하셔야 합니다."/> 사용
                <input type="radio" name="cart_reminder_url_link" value="N" <?=$checked['cart_reminder_url_link']['N']?> onchange="javascript:chg_cart_reminder_url_link();" required="required" fld_esssential="fld_esssential" msgR="반드시 하나는 선택하셔야 합니다."/> 사용안함
                <p class="extext">* 회원장바구니 링크는 URL의 원활한 전송을 위하여 LMS 만 사용이 가능합니다.</p>
            </td>
        </tr>
    </table>

    <div class="button">
        <input type=image src="../img/btn__save.gif"/>
        <a href="../event/cart_reminder_list.php"><img src="../img/btn__cancel.gif"/></a>
    </div>
    <div style="padding-top:15px"></div>
</form>
<script>
    var cartType = '<?=$cart_reminder['cart_reminder_type']?>';
    var msgReload = '<?=$cart_reminder_reload?>';
    var reMsgType = '<?=$cart_reminder['cart_reminder_send_type']?>';

    if (msgReload == 'y') {
        chg_send_type();
        if (cartType == 'M') {
            chg_M_period();
        }
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
<? include "../_footer.php"; ?>
