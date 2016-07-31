<?php
$location = "��ٱ��� �˸� > ��ٱ��� �˸� �����";
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
        msg("�������� �ʴ� ��ٱ��� �˸��Դϴ�.", -1);
        exit;
    }
} else {
    $cartReminder = Core::loader('CartReminder');
    $cartReminderMaxCount = $cartReminder->getCartReminderMaxCount();
    $query_count = "select count(cart_reminder_no) from " . GD_CART_REMINDER;
    list($cartReminderNowCount) = $db->fetch($query_count);
    if($cartReminderNowCount >= $cartReminderMaxCount) {
        msg('��ϰ����� ��ٱ��� �˸��� ��� ����ϼ̽��ϴ�.', -1);
        exit;
    }
    $checked['cart_reminder_type']['M'] = 'checked="checked"';
    $display['M_period'] = "display_table_row";
    $display['M_period_self'] = "display_none";
    $display['A_period'] = "display_none";
    $display['A_send_time'] = "display_none";
    $cart_reminder['cart_reminder_send_type'] = 'LMS';
    $cart_reminder_msg = '(����) [{shopName}] ������ ��ٱ��Ͽ� ��ǰ�� ��� �ֽ��ϴ�. �Ʒ� ��ٱ��� �ٷΰ��⸦ ���� �ٷ� Ȯ���Ͻ� �� �ֽ��ϴ�. [��ٱ��ϸ�ũ]';
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
        font: 9pt ����ü;
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
        font: 9pt ����ü;
        overflow: hidden;
        border: 0;
        width: 98px;
        height: 110px;
        background: url(../img/short_message01.gif) no-repeat;
    }

    .area_lms_msg {
        font: 9pt ����ü;
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
<div class="title title_top">��ٱ��� �˸� ���� ����<span>��ٱ��Ͽ� ��ǰ�� �����ϰ� �ִ� ȸ���� �� ������ ���ǿ� �´� ȸ������ ������� �˸��� �߼��մϴ�.</span>
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
            <td>��ٱ��� �˸� �̸�</td>
            <td>
                <input type="text" name="cart_reminder_title" value="<?=$cart_reminder['cart_reminder_title']?>" class="line" required="required" fld_esssential="fld_esssential" msgR="�̸��� ����ϼž� �մϴ�."/>
            </td>
        </tr>
        <tr>
            <td>�߼� ���</td>
            <td>
                <input type="radio" name="cart_reminder_type" value="M" <?=$checked['cart_reminder_type']['M']?> onclick="javascript:chg_send_type();" required="required" fld_esssential="fld_esssential" msgR="�ݵ�� �ϳ��� �����ϼž� �մϴ�."/> �����߼�
            </td>
        </tr>
        <tr id="tr_M_period" class="<?=$display['M_period']?>">
            <td>�߼� ���</td>
            <td>
                <select name="cart_reminder_period" onchange="javascript:chg_M_period();" class="line" required="required" fld_esssential="fld_esssential" msgO="�ݵ�� �ϳ��� �����ϼž� �մϴ�.">
                    <option value="">=����=</option>
                    <option value="1" <?=$selected['cart_reminder_period'][1]?>>�ֱ� 1��~3��</option>
                    <option value="2" <?=$selected['cart_reminder_period'][2]?>>�ֱ� 3��~5��</option>
                    <option value="3" <?=$selected['cart_reminder_period'][3]?>>�ֱ� 3��~7��</option>
                    <option value="4" <?=$selected['cart_reminder_period'][4]?>>�ֱ� 5��~7��</option>
                    <option value="5" <?=$selected['cart_reminder_period'][5]?>>��������</option>
                </select>
                <span id="div_M_period_self" class="<?=$display['M_period_self']?>">
                    <input type="text" name="cart_reminder_period_start_date" value="<?=$cart_reminder['cart_reminder_period_start_date']?>" onclick="calendar(event)" class="line" size="10"/>
                    ~
                    <input type="text" name="cart_reminder_period_end_date" value="<?=$cart_reminder['cart_reminder_period_end_date']?>" onclick="calendar(event)" class="line" size="10"/>
                </span>
                <span>���� ��ٱ��Ͽ� ��ǰ�� ���� ȸ���鿡�� �˸��� 1ȸ �߼��մϴ�.</span>

                <div id="div_cart_product">
                    <span id="span_cart_period"></span>
                    <a id="cart_product" href="javascript:link_date();"><img src="../img/btn__cartview.gif"></a></div>
                <p class="extext">* ��ٱ��Ͽ� ��Ҵ� ��ǰ�� ���� �� ������ ȸ���鿡�Դ� �˸��� �߼����� �ʽ��ϴ�.</p>

                <p class="extext">* �ڵ�����ȣ ������ �ְ�, SMS ���� ����� �� ȸ���� ������θ� �˸��� �߼��մϴ�.</p>
            </td>
        </tr>
        <tr id="tr_A_period" class="<?=$display['A_period']?>">
            <td>�߼� ���</td>
            <td>
                <select name="cart_reminder_period" disabled="disabled" class="line" fld_esssential="fld_esssential" msgO="�ݵ�� �ϳ��� �����ϼž� �մϴ�.">
                    <option value="">=����=</option>
                    <?php
                    for($p = 1; $p <= 7; $p++) {
                        ?>
                        <option value="<?=$p?>" <?=$selected['cart_reminder_period'][$p]?>><?=$p?>����</option>
                        <?php
                    }
                    ?>
                </select>

                <p class="extext">* ��ٱ��Ͽ� ��Ҵ� ��ǰ�� ���� �� ������ ȸ���鿡�Դ� �˸��� �߼����� �ʽ��ϴ�.</p>

                <p class="extext">* �ڵ�����ȣ ������ �ְ�, SMS ���� ����� �� ȸ���� ������θ� �˸��� �߼��մϴ�.</p>

                <p class="extext">* �˸��� ������ ���� ������ �˸��� �߼� �˴ϴ�.</p>
            </td>
        </tr>
        <tr id="tr_A_send_time" class="<?=$display['A_send_time']?>">
            <td>�߼� ����</td>
            <td>
                <select name="cart_reminder_send_time" fld_esssential="fld_esssential" class="line" msgO="�ݵ�� �ϳ��� �����ϼž� �մϴ�.">
                    <option value="">=����=</option>
                    <?php
                    for($s = 8; $s <= 21; $s++) {
                        ?>
                        <option value="<?=$s?>" <?=$selected['cart_reminder_send_time'][$s]?>>���� <?=$s?> ��</option>
                        <?php
                    }
                    ?>
                </select>
            </td>
        </tr>
        <tr>
            <td>��ǰ �ɼ�</td>
            <td>
                <input type="checkbox" name="cart_reminder_goods_show" value="Y" <?=$checked['cart_reminder_goods_show']['Y']?> /> ������ ��ǰ ����
                <input type="checkbox" name="cart_reminder_goods_soldout" value="Y" <?=$checked['cart_reminder_goods_soldout']['Y']?> /> ǰ�� ��ǰ ����
            </td>
        </tr>
        <tr>
            <td>���</td>
            <td>
                <input type="text" name="cart_reminder_stock_ea" size="5" maxlength="4" value="<?=$cart_reminder['cart_reminder_stock_ea']?>" onkeydown="onlynumber();" class="line"/> ��
                <select name="cart_reminder_stock_ea_updown" class="line">
                    <option value="UP" <?=$selected['cart_reminder_stock_ea_updown']['UP']?>>�̻�</option>
                    <option value="DOWN" <?=$selected['cart_reminder_stock_ea_updown']['DOWN']?>>����</option>
                </select>

                <p class="extext">* �������� �θ� ����� ������� �˸� �޽����� �߼۵˴ϴ�.</p>
                <p class="extext">* ��ǰ��Ͽ��� ����� ���Էµ� ��ǰ�� ����� 0���� ó���մϴ�.</p>
            </td>
        </tr>
        <tr>
            <td>ȸ���׷� ����</td>
            <td>
                <input type="checkbox" name="cart_reminder_member_grp_all" value="Y" onchange="javascript:chk_member_grp_all();" <?=$checked['cart_reminder_member_grp_all']['Y']?> /> ��ü<br>
                <? foreach(member_grp() as $v) { ?>
                    <input type="checkbox" name="cart_reminder_member_grp[]" value="<?=$v['level']?>" onchange="javascript:chk_member_grp_once();" <?=$checked['cart_reminder_member_grp'][$v['level']]?> /> <?=$v['grpnm']?>
                <? } ?>
            </td>
        </tr>
    </table>

    <div class="title title_top">�˸� �޼��� <span>��ٱ��� �˸� �޼����� �ۼ��մϴ�.</span>
        <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=event&no=22')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a>
    </div>
    <table class="tb">
        <col class="cellC">
        <col class="cellL">
        <tr>
            <td>����</td>
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
                                        <textarea name="cart_reminder_sms_msg" id="msg" class="area_sms_msg" onkeydown="chk_length(this,'m');" onkeyup="chk_length(this,'m');" onchange="chk_length(this,'m');" onFocus="clear_bg(this);" required="required" fld_esssential="fld_esssential" msgR="�޼����� �Է����ּ���."><?=$cart_reminder_msg?></textarea>
                                    </td>
                                </tr>
                                <tr>
                                    <td height="31px" background="../img/sms_bottom.gif" align="center">
                                        <input name="vLength" type="text" id="msg_byte" value="0">/<font class="ver8" color="#262626" id="byte_limit">90 Bytes</font>
                                    </td>
                                </tr>
                                <tr>
                                    <td id="td_point">SMS - 1����Ʈ ����</td>
                                </tr>
                                <tr id="tr_lms_alert" style="display:none;">
                                    <td style="padding: 5px 10px;width:126px; height:100px;" class="extext">�� Ư�������� ��� ���񿡴� �Է��� �� ������, ���뿡 �Է��ϴ� ��� ��Ż� ��å�� ���� �߼��� ������ �� �ֽ��ϴ�.</td>
                                </tr>
                            </table>
                        </td>
                        <td>
                            <div class="msg_sms_alert">
                                <h4>�� ��������� ���� ���� ���� ���� �ؼ������� �� Ȯ�����ּ���.</h4>
                                <a href="http://www.godo.co.kr/news/notice_view.php?board_idx=1237&page=2" target="_blank">[��������� ���� ���� ���� ���� ���� �ʼ� �ؼ����� �ȳ� �ٷΰ���]</a>
                            </div>
                            <div class="msg_sms_code">
                                <h4>�� SMS �ڵ��߼� ������ ���Ǵ� ġȯ�ڵ� �ȳ�</h4>
                                {shopName} : ���θ���
                            </div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td>��ٱ��� ��ũ <br>��� ����</td>
            <td>
                <input type="radio" name="cart_reminder_url_link" value="Y" <?=$checked['cart_reminder_url_link']['Y']?> onchange="javascript:chg_cart_reminder_url_link();" required="required" fld_esssential="fld_esssential" msgR="�ݵ�� �ϳ��� �����ϼž� �մϴ�."/> ���
                <input type="radio" name="cart_reminder_url_link" value="N" <?=$checked['cart_reminder_url_link']['N']?> onchange="javascript:chg_cart_reminder_url_link();" required="required" fld_esssential="fld_esssential" msgR="�ݵ�� �ϳ��� �����ϼž� �մϴ�."/> ������
                <p class="extext">* ȸ����ٱ��� ��ũ�� URL�� ��Ȱ�� ������ ���Ͽ� LMS �� ����� �����մϴ�.</p>
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
