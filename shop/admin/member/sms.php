<?

$location = "SMS���� > SMS ������";
include "../_header.php";

### ��ü ȸ�� �ο��� üũ (���� ����ȸ��)
$query = "SELECT count(m_no) FROM ".GD_MEMBER." WHERE sms='y' AND mobile!='' AND " . MEMBER_DEFAULT_WHERE;
list($tot_member) = $db->fetch($query);

### ��ü �ּҷ� �ο��� üũ
$query = "SELECT count(sno) FROM ".GD_SMS_ADDRESS."";
list($tot_address) = $db->fetch($query);
?>
<script language="JavaScript" type="text/JavaScript">
var nsTable_selector = function() {
return {

	last_clicked_el : null,
	data : [],
	table : null,
	_id : null,
	init : function(_id) {

		var self = this;

		self._id = _id;

		self.table = $(self._id);
		var idx = 0;
		$A(self.table.down('tbody').rows).each(function(tr) {
			tr.id = self._id + '-tr-'+ idx;
			self.data[tr.id] = false;
			idx++;
		});

		Event.observe(self.table,'click', nsTable_selector._onClick , false);

		Event.observe(document,'selectstart', function(){
			Event.stop(event);
		}, false);

		self = null;

	},
	_getIdx : function(el) {
		return (el.id) ? parseInt( el.id.replace(this._id + '-tr-','') ) : 0;
	},
	_onClick : function(event) {

		var self = nsTable_selector;

		var el = Element.up(event.srcElement,'tr');

		if (event.shiftKey) {

			if (self.last_clicked_el == null) self.last_clicked_el = self.table.down('tbody').rows[0];

			var c_idx = self._getIdx(el);
			var l_idx = self._getIdx(self.last_clicked_el);

			var _start = _end = _idx = 0;

			if (c_idx > l_idx)
			{
				_start = l_idx;
				_end = c_idx;
			}
			else {
				_start = c_idx;
				_end = l_idx;
			}

			$A(self.table.down('tbody').rows).each(function(tr){

				_idx = self._getIdx(tr);

				if (_idx >= _start && _idx <= _end) {
					tr.style.backgroundColor = '#3399FF';
					self.data[tr.id] = true;
				}
				else {
					tr.style.backgroundColor = '';
					self.data[tr.id] = false;
				}

			});
		}
		else if (event.ctrlKey) {

			self.last_clicked_el = el;

			if (!self.data[el.id]) {
				el.style.backgroundColor = '#3399FF';
			}
			else {
				el.style.backgroundColor = '';
			}
			self.data[el.id] = !self.data[el.id];
		}
		else {

			self.last_clicked_el = el;

			$A(self.table.down('tbody').rows).each(function(tr){
				if (tr == el) {
					tr.style.backgroundColor = '#3399FF';
					self.data[tr.id] = true;
				}
				else {
					tr.style.backgroundColor = '';
					self.data[tr.id] = false;
				}
			});
		}
		self = null;
	}
}
}();

function sendNumber(str,silent)
{
	if (silent == null) silent = false;

	// �޴��� ��ȣ �˻�
	var pattern = /^([0]{1}1[0-9]{1})-?([1-9]{1}[0-9]{2,3})-?([0-9]{4})$/;
	if (pattern.test(str)){

		var _id = 'phone'+str;

		if ($(_id)) {
			if (silent == false) alert('�̹� �߰��� ��ȣ �Դϴ�.');
			return;
		}

		var oTr = $('el-seperate-phonenumber-list').down('tbody').insertRow();
		oTr.id = 'el-seperate-phonenumber-list-tr-' + $('el-seperate-phonenumber-list').down('tbody').rows.length;
		var oTd = oTr.insertCell();
		oTd.innerHTML = '<span id="'+_id+'">'+str+'</span>';

		$('el-seperate-phonenumber-list-count').update(  $('el-seperate-phonenumber-list').down('tbody').rows.length  );
	}
	else {
		if (silent == false) alert('�޴��� ��ȣ ������ �ƴմϴ�.');
	}
}


function fnPhoneInputer() {

	if (event.keyCode == 13) {

		var fld = event.srcElement;
		sendNumber(fld.value);
		event.returnValue = false;
	}

}


function fnSmsSearchFormTab(nm) {

	if (nm == 'member') {
		$('el-search-form-member').setStyle({display:'block'});
		$('el-search-form-address').setStyle({display:'none'});
		$('el-search-btn-member').writeAttribute('src','../img/teb_sms01_on.gif');
		$('el-search-btn-address').writeAttribute('src','../img/teb_sms02_off.gif');
		$('el-search-phonenumber-list-count').update('<?=number_format($tot_member)?>');
	}
	else {
		$('el-search-form-member').setStyle({display:'none'});
		$('el-search-form-address').setStyle({display:'block'});
		$('el-search-btn-member').writeAttribute('src','../img/teb_sms01_off.gif');
		$('el-search-btn-address').writeAttribute('src','../img/teb_sms02_on.gif');
		$('el-search-phonenumber-list-count').update('<?=number_format($tot_address)?>');
	}

}

function fnSetSmsType(t) {
	document.fmList.type.value = t;

}

function sendSMS() {

	var f = document.fmList;
	f.target = 'smswin';
	f.action = '../member/popup.sms.php';

	if (f.type.value == 1)
	{
	// ���� �߼� �� ���
		if ($('el-seperate-phonenumber-list').down('tbody').rows.length < 1) {
			alert('�߼� ����� �Է��� �ּ���');
			return;
		}
		var mobile = '';
		$('el-seperate-phonenumber-list').childElements().each(function(el){
			mobile += el.innerText + "\r\n";
		});

		f.mobile.value = mobile;
	}
	else {
	// ��ü �߼��� ���
		//
		if ($('el-search-form-member').getStyle('display') != 'none') {
			// ȸ��
			f.type.value = 6;

		}
		else {
			f.type.value = 7;
		}
	}

	var x = (window.screen.width - 800) / 2;
	var y = (window.screen.height - 600) / 2;

	var smswin = window.open('about:blank', "smswin", "width=800, height=600, scrollbars=yes, left=" + x + ", top=" + y);
	f.submit();

}

function addSMSAddress() {
	if ($('el-search-form-member').getStyle('display') == 'block') {
		var iframe = $('el-search-form-member').down('iframe');
	}
	else {
		var iframe = $('el-search-form-address').down('iframe');
	}

	iframe.contentWindow.nsTable_selector.add();

}


function delSMSAddress() {
	$A($('el-seperate-phonenumber-list').down('tbody').rows).each(function(tr){
		if (nsTable_selector.data[tr.id]) tr.remove();
	});
	$('el-seperate-phonenumber-list-count').update(  $('el-seperate-phonenumber-list').down('tbody').rows.length  );
}

Event.observe(document, 'dom:loaded', function(){
	nsTable_selector.init('el-seperate-phonenumber-list');
}, false);
</script>
<style>
#el-seperate-phonenumber-list {list-style:none;padding:8px;margin:0;width:100%;}
#el-seperate-phonenumber-list td {cursor:pointer;padding:3px;height:22px;}
</style>
<div class="title title_top"><font face="����" color="black"><b>SMS</b></font> ������<span>���� �޼����� �߼��� ����� �˻��Ͽ� �߼��մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=20');"><img src="../img/btn_q.gif" align="absmiddle" hspace="2" /></a></div>


<table border="4" bordercolor="#dce1e1" style="border-collapse:collapse; margin-bottom:10px" width="700">
<tr><td style="padding:7 0 10 10">

	<table width="100%">
	<tr>
		<td>
		<? $sms = Core::loader('Sms');?>
		�ܿ� SMS ����Ʈ : <span style="font-weight:bold;color:#627DCE;"><?=number_format($sms->smsPt)?></span> ��
		</td>
		<td>
		<div style="padding-top:7px; color:#666666" class="g9">SMS ����Ʈ�� ���� ��� SMS�� �߼۵��� �ʽ��ϴ�.</div>
		<div style="padding-top:5px; color:#666666" class="g9">SMS����Ʈ�� �����Ͽ� �߼��Ͻñ� �ٶ��ϴ�.</div>
		</td>
		<td>
		<a href="../member/sms.pay.php"><img src="../img/btn_point_pay.gif" /></a>
		</td>
	</tr>

	</table>


</td></tr>
</table>


<form name="fmList" id="fmList" method="post">
<input type="hidden" name="type" value="1" />
<input type="hidden" name="mobile" value="" />
<input type="hidden" name="query" value="" />
</form>

<table border="0" width="100%" cellspacing="0">
<tr>
<td width="50%">
<!-- ��� & �˻� �� -->
	<table class="tb" height="100%">
	<tr><td class="cellC">ȸ��/�Ϲ� �ּҷ�</td></tr>
	<tr><td class="cellL">

		<div style="margin-top:10px;height:26px;background:url(../img/bg_teb_sms.gif) repeat-x top left;">
			<a href="javascript:void(0);" onClick="fnSmsSearchFormTab('member');"><img src="../img/teb_sms01_on.gif" id="el-search-btn-member"></a>
			<a href="javascript:void(0);" onClick="fnSmsSearchFormTab('address');"><img src="../img/teb_sms02_off.gif" id="el-search-btn-address"></a>
		</div>

		<p class="extext">
		�ڵ��� ��ȣ�� Ŭ���ϰų� ���� ������ �� �߰� �ϱ� ��ư�� Ŭ���ϸ� �߼۴�� ��ϵ˴ϴ�. <br>
		"��ü����"��ư�� Ŭ���ϸ� ���� ����Ʈ�� �ڵ��� ��ȣ�� ��ϵ˴ϴ�.
		</p>

		<!-- ȸ�� �˻� -->
		<div id="el-search-form-member">
			<iframe src="./popup.srch_member.php" width="100%" height="350" frameborder="0" border="0"></iframe>
		</div>

		<!-- �Ϲ� �ּҷ� �˻� -->
		<div id="el-search-form-address" style="display:none;">
			<iframe src="./popup.srch_address.php?foo=1" width="100%" height="350" frameborder="0" border="0"></iframe>
		</div>

		<!-- �˻� ����Ʈ -->

	</td></tr>
	</table>
<!-- ��� & �˻� �� -->
</td>
<td>
<a href="javascript:void(0);" onClick="addSMSAddress()"><img src="../img/btn_add_list.gif"></a>
<a href="javascript:void(0);" onClick="delSMSAddress()"><img src="../img/btn_del_list.gif"></a>

</td>
<td width="50%">
<!-- ���� ��� -->
	<table class="tb" height="100%" style="height:100%;width:150px;">
	<tr><td class="cellC" style="width:200px;"><label class="noline"><input type="radio" name="target_type" value="seperate" checked onClick="fnSetSmsType(1);">���� �߼� ��� <span id="el-seperate-phonenumber-list-count">0</span>��</label></td></tr>
	<tr id="for_sms_target_seperate"><td class="cellL">
	<input type="text" name="_phone" value="" class="line" style="overflow:visible;width:126px;" onKeyPress="fnPhoneInputer();">
	<span class="extext">�޴��� ��ȣ�� ����Ű�� ������ �߼� ��� �߰��˴ϴ�</span>
	<div style="margin-top:5px;">
		<div style="float:left;background:#F2F2F2;border:1px solid #D9D9D9;width:200px;height:382px;overflow-y:auto;">
		<table cellpadding="0" cellspacing="0" border="0" id="el-seperate-phonenumber-list">
		<thead>
		</thead>
		<tbody>
		</tbody>
		</table>
		</div>
	</div>
	</td></tr>

	<tr><td class="cellC" style="width:200px;"><label class="noline"><input type="radio" name="target_type" value="query" onClick="fnSetSmsType(6);">��ü�߼�( <span id="el-search-phonenumber-list-count"><?=number_format($tot_member)?></span>��)</label></td></tr>
	<tr id="for_sms_target_query" style="display:none;height:425px;"><td class="cellL">

	</td></tr>
	</table>
<!-- ���� ��� -->
</td>
</tr>
</table>

<div class="button" style="text-align:center;padding:10px;">
	<a href="javascript:void(0)" onClick="sendSMS()"><img src="../img/btn_today_email_sm.gif"></a>
</div>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />�����߼۽� �޴� ��� �Է¶��� ��ȣ�� �ְ� EnterŰ�� ������ ��ȭ��ȣ�� �߰� �� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />SMS �߼۽� SMS �߼���Ȳ�� ��������� �������ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />������ �߼� �ý��ۻ� ������ �ƴ� ��Ż� ������å �� ��Ÿ ������ ���� ���ڹ߼� ���п� ���� ������ å���� ������, �� ��Ż翡 ����Ȯ���� ���θ��� �����մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />�߽Ź�ȣ�� ���� ��ϵ��� ������ SMS�� �߼۵��� �ʽ��ϴ�. <a href="http://www.godo.co.kr/news/notice_view.php?board_idx=1247&page=2
" target="_blank"><font color=white><u>�߽Ź�ȣ ��������� �ȳ�</u></font></a></td></tr>
</table>
</div>
<script language="JavaScript" type="text/JavaScript">cssRound('MSG01');</script>

<? include "../_footer.php"; ?>
