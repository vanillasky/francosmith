function chg_cart_reminder_url_link() {
    if (document.getElementsByName('cart_reminder_url_link')[0].checked == true) {
        document.getElementsByName('cart_reminder_lms_msg')[0].value = document.getElementsByName('cart_reminder_lms_msg')[0].value + "[장바구니링크]";
    } else {
        var msg_arr = document.getElementsByName('cart_reminder_lms_msg')[0].value.split('[장바구니링크]');
        document.getElementsByName('cart_reminder_lms_msg')[0].value = msg_arr[0];
    }
}
function eventStop(event) {
    if (event.preventDefault) {
        event.preventDefault();
    }
    else {
        event.returnValue = false;
    }
}
function show_cart_reminder_msg_layer(cart_reminder_no) {
    popupLayer('../event/cart_reminder_msg_layer.php?cart_reminder_no=' + cart_reminder_no, 178, 550);
}
function cart_reminder_delete(cart_reminder_no) {
    if (confirm("삭제하시겠습니까?")) {
        document.cart_reminder_form.mode.value = 'delete';
        document.cart_reminder_form.cart_reminder_no.value = cart_reminder_no;
        document.cart_reminder_form.submit();
    }
}
function cart_reminder_send_confirm(cart_reminder_no) {
    var ajax = new Ajax.Request('../event/cart_reminder_indb.php', {
        method: "post",
        parameters: 'mode=countsendmember&cart_reminder_no=' + cart_reminder_no,
        onSuccess: function (response) {
            var result = response.responseText;
            if (result == 'NO_SEND_MEMBER') {
                alert("검색된 회원이 없습니다.");
                return;
            } else if (result > 0) {
                if (confirm("총 " + result + " 명이 검색되었습니다.\n보내시겠습니까?")) {
                    //progress bar
                    nsCartReminderSMSLoading.init({
                        psObject: $$('iframe[name="ifrmHidden"]')[0]
                    });
                    nsCartReminderSMSLoading.show();
                    smsLoadingCount(0);

                    document.cart_reminder_form.mode.value = 'msend';
                    document.cart_reminder_form.cart_reminder_no.value = cart_reminder_no;
                    document.cart_reminder_form.target = 'ifrmHidden';
                    document.cart_reminder_form.submit();
                } else {
                    return;
                }
            }
        },
        onFailure: function () {
            alert('전송 오류입니다.\\n다시 시도해 주세요.');
            return;
        }
    });
}
function smsLoadingCount(PerValue) {
    nsCartReminderSMSLoading.gogosing('SMS 발송중<br /><span style="margin-left: 20px;">' + PerValue + '%</span>');
}
function clear_bg(obj) {
    var backimg = '';
    if (obj.name == 'cart_reminder_sms_msg') {
        backimg = "../img/long_message02_none.gif";
    } else {
        backimg = "../img/long_message01_none.gif";
    }
    obj.style.backgroundImage = "url('" + backimg + "')";
}
function chk_length(obj, tcode) {
    str = obj.value;
    if (tcode == 's') {
        if (document.getElementsByName('cart_reminder_send_type')[1].checked == true) {
            var specialChars = /[^\u3131-\u314e\uac00-\ud7a3a-zA-Z0-9]/g;
            if (str.match(specialChars)) {
                alert("특수 문자는 사용 할 수 없습니다.");
                obj.value = str.split(specialChars).join("");
                str = obj.value;
                document.getElementsByName('vLength')[0].value = parseInt(chkByte(str), 10) + parseInt(chkByte(document.getElementById("msg").value), 10);
                return;
            }
            var strByte = parseInt(chkByte(str), 10) + parseInt(chkByte(document.getElementById("msg").value), 10);
            if (strByte > 2000) {
                alert("제목과 내용의 메시지가 2000bytes를 넘을 수 없습니다.");
                var cutByte = 2000 - parseInt(chkByte(document.getElementById("msg").value), 10);
                obj.value = strCut(str, cutByte);
                str = obj.value;
                document.getElementsByName('vLength')[0].value = parseInt(chkByte(str), 10) + parseInt(chkByte(document.getElementById("msg").value), 10);
            }
            if (chkByte(str) > 40) {
                alert("제목은 40bytes까지 입니다.");
                obj.value = strCut(str, 40);
                str = obj.value;
                document.getElementsByName('vLength')[0].value = parseInt(chkByte(str), 10) + parseInt(chkByte(document.getElementById("msg").value), 10);
                return;
            } else {
                document.getElementsByName('vLength')[0].value = parseInt(chkByte(str), 10) + parseInt(chkByte(document.getElementById("msg").value), 10);
            }
        }
    } else if (tcode == 'm') {
        if (document.getElementsByName('cart_reminder_send_type')[0].checked == true) {
            document.getElementsByName('vLength')[0].value = chkByte(str);
            if (chkByte(str) > 90) {
                document.getElementsByName('cart_reminder_send_type')[1].checked = true;
                document.getElementById("img_sms_title").src = "../img/btn_sms_off.gif";
                document.getElementById("img_lms_title").src = "../img/btn_lms_on.gif";
                document.getElementById("sms_top").style.backgroundImage = "url('../img/lms_top.gif')";
                document.getElementById("tr_lms_subject").style.display = "";
                document.getElementById("td_msg").className = "td_lms_msg";
                document.getElementById("msg").className = "area_lms_msg";
                document.getElementById("msg").setAttribute("name", "cart_reminder_lms_msg");
                clear_bg(document.getElementById("msg"));
                document.getElementById("tr_lms_alert").style.display = "";
                document.getElementById("lms_subject").disabled = false;
                document.getElementsByName('vLength')[0].style.color = "#f00";
                document.getElementById("byte_limit").innerHTML = "2000 Byte";
                document.getElementsByName('vLength')[0].value = chkByte(str);
                document.getElementById("td_point").innerHTML = "LMS - 3포인트 차감";
                document.getElementsByName("cart_reminder_url_link")[0].disabled = false;
            }
        } else {
            if (chkByte(str) <= 90) {
                document.getElementsByName('cart_reminder_send_type')[0].checked = true;
                document.getElementById("img_sms_title").src = "../img/btn_sms_on.gif";
                document.getElementById("img_lms_title").src = "../img/btn_lms_off.gif";
                document.getElementById("sms_top").style.backgroundImage = "url('../img/sms_top.gif')";
                document.getElementById("td_msg").className = "td_sms_msg";
                document.getElementById("msg").className = "area_sms_msg";
                document.getElementById("msg").setAttribute("name", "cart_reminder_sms_msg");
                clear_bg(document.getElementById("msg"));
                document.getElementsByName('vLength')[0].style.color = "#000";
                document.getElementById("byte_limit").innerHTML = "90 Byte";
                document.getElementsByName('vLength')[0].value = chkByte(str);
                document.getElementById("td_point").innerHTML = "SMS - 1포인트 차감";
                document.getElementById("lms_subject").value = '';
                document.getElementById("tr_lms_subject").style.display = "none";
                document.getElementById("tr_lms_alert").style.display = "none";
                document.getElementById("lms_subject").disabled = true;
                document.getElementsByName("cart_reminder_url_link")[0].disabled = true;
                document.getElementsByName("cart_reminder_url_link")[1].checked = true;
            } else {
                var strByte = parseInt(chkByte(str), 10) + parseInt(chkByte(document.getElementById("lms_subject").value), 10);
                if (strByte > 2000) {
                    alert("메시지가 2000bytes를 초과할 수 없습니다.");
                    var cutByte = 2000 - parseInt(chkByte(document.getElementById("lms_subject").value), 10);
                    obj.value = strCut(str, cutByte);
                    str = obj.value;
                }
                //LMS 는 제목과 메세지 포함 2000byte
                document.getElementsByName('vLength')[0].value = parseInt(chkByte(str), 10) + parseInt(chkByte(document.getElementById("lms_subject").value), 10);
            }
        }
    }
}
function chk_member_grp_all() {
    var grpnum = document.getElementsByName('cart_reminder_member_grp[]').length;
    if (document.getElementsByName('cart_reminder_member_grp_all')[0].checked == true) {
        for (i = 0; i < grpnum; i++) {
            document.getElementsByName('cart_reminder_member_grp[]')[i].checked = true;
        }
    } else {
        for (i = 0; i < grpnum; i++) {
            document.getElementsByName('cart_reminder_member_grp[]')[i].checked = false;
        }
    }
}
function chk_member_grp_once() {
    var grpnum = document.getElementsByName('cart_reminder_member_grp[]').length;
    var chknum = 0;
    for (i = 0; i < grpnum; i++) {
        if (document.getElementsByName('cart_reminder_member_grp[]')[i].checked == true) {
            chknum++;
        }
    }
    if (chknum == grpnum) {
        document.getElementsByName('cart_reminder_member_grp_all')[0].checked = true;
    } else {
        document.getElementsByName('cart_reminder_member_grp_all')[0].checked = false;
    }
}
function chg_send_type() {
    if (document.getElementsByName('cart_reminder_type')[0].checked === true) {
        document.getElementById('tr_A_period').className = "display_none";
        document.getElementsByName('cart_reminder_period')[1].selectedIndex = 0;
        document.getElementsByName('cart_reminder_period')[1].disabled = true;
        document.getElementsByName('cart_reminder_period')[1].removeAttribute("required");
        document.getElementById('tr_A_send_time').className = "display_none";
        document.getElementsByName('cart_reminder_send_time')[0].selectedIndex = 0;
        document.getElementsByName('cart_reminder_send_time')[0].disabled = true;
        document.getElementsByName('cart_reminder_send_time')[0].removeAttribute("required");
        document.getElementById('tr_M_period').className = 'display_table_row';
        document.getElementsByName('cart_reminder_period')[0].disabled = false;
        document.getElementsByName('cart_reminder_period')[0].setAttribute("required", "required");
    } else if (document.getElementsByName('cart_reminder_type')[1].checked === true) {
        document.getElementById('tr_M_period').className = "display_none";
        document.getElementsByName('cart_reminder_period')[0].selectedIndex = 0;
        document.getElementsByName('cart_reminder_period')[0].disabled = true;
        document.getElementsByName('cart_reminder_period')[0].removeAttribute("required");
        document.getElementById('tr_A_period').className = 'display_table_row';
        document.getElementsByName('cart_reminder_send_time')[0].disabled = false;
        document.getElementsByName('cart_reminder_send_time')[0].setAttribute("required", "required");
        document.getElementById('tr_A_send_time').className = 'display_table_row';
        document.getElementsByName('cart_reminder_period')[1].disabled = false;
        document.getElementsByName('cart_reminder_period')[1].setAttribute("required", "required");
    }
}
function chg_M_period() {
    if (document.getElementsByName('cart_reminder_period')[0].selectedIndex === 5) {
        document.getElementsByName('cart_reminder_period_start_date')[0].disabled = false;
        document.getElementsByName('cart_reminder_period_start_date')[0].setAttribute("required", "required");
        document.getElementsByName('cart_reminder_period_end_date')[0].disabled = false;
        document.getElementsByName('cart_reminder_period_end_date')[0].setAttribute("required", "required");
        document.getElementById('div_M_period_self').style.display = "inline";
        document.getElementById('span_cart_period').innerText = '';
    } else {
        document.getElementsByName('cart_reminder_period_start_date')[0].value = '';
        document.getElementsByName('cart_reminder_period_start_date')[0].disabled = true;
        document.getElementsByName('cart_reminder_period_start_date')[0].removeAttribute("required");
        document.getElementsByName('cart_reminder_period_end_date')[0].value = '';
        document.getElementsByName('cart_reminder_period_end_date')[0].disabled = true;
        document.getElementsByName('cart_reminder_period_end_date')[0].removeAttribute("required");
        document.getElementById('div_M_period_self').style.display = "none";
        get_cart_reminder_date();
    }
}
function get_cart_reminder_date() {
    var sDate = 0;
    var eDate = 0;
    var startDate = new Date();
    var endDate = new Date();
    var cartStartDate = '';
    var cartEndDate = '';
    var cartPeriodText = '';
    var indexKey = document.getElementsByName('cart_reminder_period')[0].selectedIndex;
    if (indexKey == 1) {
        sDate = 3;
        eDate = 1;
        startDate.setDate(startDate.getDate() - sDate);
        endDate.setDate(endDate.getDate() - eDate);
        cartStartDate = yyyymmdd(startDate);
        cartEndDate = yyyymmdd(endDate);
        cartPeriodText = '설정기간 : ' + cartStartDate + ' ~ ' + cartEndDate;
    } else if (indexKey == 2) {
        sDate = 5;
        eDate = 3;
        startDate.setDate(startDate.getDate() - sDate);
        endDate.setDate(endDate.getDate() - eDate);
        cartStartDate = yyyymmdd(startDate);
        cartEndDate = yyyymmdd(endDate);
        cartPeriodText = '설정기간 : ' + cartStartDate + ' ~ ' + cartEndDate;
    } else if (indexKey == 3) {
        sDate = 7;
        eDate = 3;
        startDate.setDate(startDate.getDate() - sDate);
        endDate.setDate(endDate.getDate() - eDate);
        cartStartDate = yyyymmdd(startDate);
        cartEndDate = yyyymmdd(endDate);
        cartPeriodText = '설정기간 : ' + cartStartDate + ' ~ ' + cartEndDate;
    } else if (indexKey == 4) {
        sDate = 7;
        eDate = 5;
        startDate.setDate(startDate.getDate() - sDate);
        endDate.setDate(endDate.getDate() - eDate);
        cartStartDate = yyyymmdd(startDate);
        cartEndDate = yyyymmdd(endDate);
        cartPeriodText = '설정기간 : ' + cartStartDate + ' ~ ' + cartEndDate;
    }
    document.getElementById('span_cart_period').innerText = cartPeriodText;
    document.getElementById('sdate').value = cartStartDate;
    document.getElementById('edate').value = cartEndDate;
}
function link_date() {
    if (document.getElementsByName('cart_reminder_period')[0].selectedIndex === 5) {
        document.getElementById('span_cart_period').innerText = '';
        var cartStartDate = '';
        var cartEndDate = '';
        cartStartDate = document.getElementsByName('cart_reminder_period_start_date')[0].value;
        cartEndDate = document.getElementsByName('cart_reminder_period_end_date')[0].value;
        document.getElementById('sdate').value = cartStartDate;
        document.getElementById('edate').value = cartEndDate;
        document.cartproductfm1.submit();
    } else {
        document.cartproductfm1.submit();
    }
}
function yyyymmdd(dateData) {
    var yyyy = dateData.getFullYear().toString();
    var mm = (dateData.getMonth() + 1).toString(); // getMonth() is zero-based
    if (mm < 10) {
        mm = '0' + mm;
    }
    var dd = dateData.getDate().toString();
    if (dd < 10) {
        dd = '0' + dd;
    }
    return yyyy + mm + dd; // padding
};
var nsCartReminderSMSLoading = function () {
    return {
        bg: null,
        el: null,
        sc: null,
        warningMsg: null,
        infoMsg: null,
        option: {},
        init: function (opt) {

            var self = this;

            self.option = Object.extend({
                psObject: null,

                bgColor: '#000',
                bgOpacity: 0.8,

                elBgColor: 'transparent',
                elWidth: 118,
                elHeight: 116,
                elMsg: '<img src="../img/admin_progress_2.gif">',

                warningMsgBgColor: '#ffffff',
                warningMsgColor: 'red',
                warningMsgOpacity: 0.6,
                warningMsgWidth: 400,
                warningMsgHeight: 50,
                warningMsgMtop: 150,
                warningMsgAlign: 'center',
                warningMsgMent: '※ 주의 : SMS 발송중에 브라우저를 닫으면 발송이 완료 되지 않습니다.',

                infoMsgColor: '#ffffff',
                infoMsgBgColor: 'transparent',
                infoMsgMtop: 65,
                infoMsgMleft: 45,
                infoMsgWidth: 150,
                infoMsgHeight: 150
            }, opt || {});

            if (self.bg == null) {
                self.bg = new Element('div', {
                    style: 'position:absolute;top:0;left:0;background:' + self.option.bgColor + ';filter:alpha(opacity=' + (self.option.bgOpacity * 100) + ');opacity:' + self.option.bgOpacity + ';display:none;cursor:progress;',
                    id: 'el-godo-loading-sms'
                });
                $$('body')[0].insert(self.bg);
            }

            if (self.infoMsg == null) {
                self.infoMsg = new Element('div', {
                    style: 'position:absolute; color:' + self.option.infoMsgColor + '; background:' + self.option.infoMsgBgColor + '; display:none; cursor:progress; z-index:1;',
                    id: 'el-godo-loading-sms-infoMsg'
                });
                $$('body')[0].insert(self.infoMsg);
            }

            if (self.warningMsg == null) {
                self.warningMsg = new Element('div', {
                    style: 'position:absolute; text-align: ' + self.option.warningMsgAlign + ';line-height:' + self.option.warningMsgHeight + 'px; color:' + self.option.warningMsgColor + '; background:' + self.option.warningMsgBgColor + '; filter:alpha(opacity=' + (self.option.warningMsgOpacity * 100) + '); opacity:' + self.option.warningMsgOpacity + '; display:none; cursor:progress; z-index:1;',
                    id: 'el-godo-loading-sms-titleMsg'
                });
                $$('body')[0].insert(self.warningMsg);
            }

            if (self.el == null) {
                self.el = new Element('div', {
                    style: 'position:absolute;background:' + self.option.elBgColor + ';display:none;cursor:progress;',
                    id: 'el-godo-loading-sms-wrap'
                });
                $$('body')[0].insert(self.el);
            }

            if (self.option.psObject != null) {
                self.option.psObject.observe('load', function (e) {
                    window.onbeforeunload = '';
                    document.onkeydown = '';
                    self.hide();
                });
            }

        },
        show: function () {

            var self = this;

            self.sc = $$("body")[0].getStyle('overflow');
            $$("body")[0].setStyle({overflow: 'hidden'});

            self._drawBG();
            self._draw();
            self._drawWM();
            self._drawIM();
        },
        gogosing: function (getddd) {
            this.infoMsg.update(getddd);
        },

        _drawIM: function () {

            if (this.infoMsg == null) return;

            var w = this._getViewSize();

            var x = (w.width - this.option.infoMsgWidth) / 2;
            var y = (w.height - this.option.infoMsgHeight) / 2;

            this.infoMsg.setStyle({
                top: (y + (document.body.scrollTop || document.viewport.getScrollOffsets().top)) + this.option.infoMsgMtop + 'px',
                left: (x + (document.body.scrollLeft || document.viewport.getScrollOffsets().left)) + this.option.infoMsgMleft + 'px',
                width: this.option.infoMsgWidth + 'px',
                height: this.option.infoMsgHeight + 'px',
                display: 'block'
            });
        },
        _drawWM: function () {

            if (this.warningMsg == null) return;

            var w = this._getViewSize();

            var x = (w.width - this.option.warningMsgWidth) / 2;
            var y = (w.height - this.option.warningMsgHeight) / 2;

            this.warningMsg.setStyle({
                top: (y + (document.body.scrollTop || document.viewport.getScrollOffsets().top)) + this.option.warningMsgMtop + 'px',
                left: (x + (document.body.scrollLeft || document.viewport.getScrollOffsets().left)) + 'px',
                width: this.option.warningMsgWidth + 'px',
                height: this.option.warningMsgHeight + 'px',
                display: 'block'
            });

            this.warningMsg.update(this.option.warningMsgMent);

        },
        hide: function () {

            var self = this;

            if (self.bg != null) self.bg.setStyle({display: 'none'});
            if (self.el != null) self.el.setStyle({display: 'none'});
            if (self.infoMsg != null) self.infoMsg.setStyle({display: 'none'});
            if (self.warningMsg != null) self.warningMsg.setStyle({display: 'none'});

            if (self.sc != null)
                $$("body")[0].setStyle({overflow: self.sc});

        },
        _draw: function () {

            if (this.el == null) return;

            var w = this._getViewSize();

            var x = (w.width - this.option.elWidth) / 2;
            var y = (w.height - this.option.elHeight) / 2;

            this.el.setStyle({
                top: (y + (document.body.scrollTop || document.viewport.getScrollOffsets().top)) + 'px',
                left: (x + (document.body.scrollLeft || document.viewport.getScrollOffsets().left)) + 'px',
                width: this.option.elWidth + 'px',
                height: this.option.elHeight + 'px',
                display: 'block'
            });

            this.el.update(this.option.elMsg);

        },
        _drawBG: function () {

            if (this.bg == null) return;

            var bgSize = this._getWindowSize();

            this.bg.setStyle({
                width: bgSize.width + 'px',
                height: bgSize.height + 'px',
                display: 'block'

            });

        },
        _getWindowSize: function () {
            return {
                width: window.innerWidth + window.scrollLeft || (window.document.documentElement.clientWidth + window.document.documentElement.scrollLeft || window.document.body.clientWidth + window.document.body.scrollLeft),
                height: window.innerHeight + window.scrollTop || (window.document.documentElement.clientHeight + window.document.documentElement.scrollTop || window.document.body.clientHeight + window.document.body.scrollTop)
            }
        },
        _getViewSize: function () {
            return {
                width: window.innerWidth || (window.document.documentElement.clientWidth || window.document.body.clientWidth),
                height: window.innerHeight || (window.document.documentElement.clientHeight || window.document.body.clientHeight)
            }
        }

    }
}();
