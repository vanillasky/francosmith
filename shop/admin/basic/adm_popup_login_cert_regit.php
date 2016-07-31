<?
include '../_header.popup.php';

### �׷�� ��������
$query = "select * from ".GD_MEMBER_GRP;
$res = $db->query($query);
while ($data=$db->fetch($res)) $r_grp[$data['level']] = $data['grpnm'];

$res = $db->query("select m_no, m_id, name, level from ".GD_MEMBER." where level >= 80 order by regdt desc");

// ��ū ����
$otp = Core::loader('gd_otp');
$_token = $otp->getToken();

?>
<script language="JavaScript">
/* AJAX */
function gd_ajax(obj) {
	try {
		var ajax = new XMLHttpRequest();
	}
	catch (e) {
		var ajax = new ActiveXObject("Microsoft.XMLHTTP");
	}

	if (! obj.param ) obj.param = '';
	if (! obj.type ) obj.type = 'post';

	ajax.onreadystatechange = function() {
		// oncomplete
		if(ajax.readyState==4) {if(ajax.status==200){
			if (obj.success) {
				obj.success(ajax.responseText);
			}
		}}
	}

	obj.type = obj.type.toLowerCase();

	// send
	if (obj.type != 'post') {
		obj.url = obj.url + "?" + obj.param;
		obj.param = null;
	}

	ajax.open(obj.type, obj.url, true);
	ajax.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	ajax.setRequestHeader("Connection", "Keep-Alive");
	ajax.send(obj.param);
}

var otpCert = function() {
	return {
		raiseError : function(code) {
			switch (code) {
				case '0001':
					alert('����� ������ �������� �ʽ��ϴ�.');
					break;
				case '0002':
					alert('�߸��� �����Դϴ�. �ٽ� �õ��� �ּ���.');
					break;
				case '0003':
					alert('������ȣ �Է� �ð��� �ʰ��Ǿ����ϴ�. ������ȣ�� �ٽ� ��û�� �ּ���.');
					break;
				case '0004':
					alert('������ȣ ����� ����Ǿ����ϴ�. ������ȣ�� �ٽ� ��û�� �ּ���.');
					break;
				case '0005':
					alert('�޴������� ������ȣ�� �������� ���߽��ϴ�.\n������ �Ұ����� �޴��� ��ȣ �Դϴ�.');
					break;
				case '0006':
					alert('������ȣ�� ��ġ���� �ʽ��ϴ�.');
					break;
				case '0008':
					alert('�޴������� ������ȣ�� �������� ���߽��ϴ�.\nSMS ����Ʈ�� �����մϴ�.');
					break;
				case '9999':
					alert('�����ں��� ������ �̿��ϰ� ���� �ʽ��ϴ�.');
					break;
				default:
					alert('��Ÿ ����');
					break;
			}
			return false;
		},

		sendOTP : function(mobile,token) {
			gd_ajax({
				url : '../basic/indb.login_cert.php',
				type : 'POST',
				param : '&mode=sendRegitOtp&mobile='+mobile+'&token='+token,
				success : function(rst) {
					if (rst == '0000') {
						_ID('mobileCompared').value = '';
						_ID('mobileClone').value = _ID('mobile').value;
						alert('�޴������� ������ȣ�� �����Ͽ����ϴ�. \n���۵� ������ȣ�� Ȯ�� �� �Է��� �ּ���.');
					}
					else {
						return otpCert.raiseError(rst);
					}
				}
			});
		},

		compareOTP : function(otp,token) {
			gd_ajax({
				url : '../basic/indb.login_cert.php',
				type : 'POST',
				param : '&mode=compareRegitOtp&otp='+otp+'&token='+token,
				success : function(rst) {
					if (rst == '0000') {
						_ID('mobileCompared').value = 'Y';
						alert('�޴�����ȣ ������ �Ϸ� �Ǿ����ϴ�.');
					}
					else {
						return otpCert.raiseError(rst);
					}
				}
			});
		}
	}
}();

function callSendOtp() {
	var token = _ID('token').value;
	var mobile = _ID('mobile').value;

	if (!mobile) {
		alert('�޴�����ȣ�� �Է��� �ּ���.');
		_ID('mobile').focus();
		return;
	}

	otpCert.sendOTP(mobile, token);
}

function callCompareOtp() {
	var token = _ID('token').value;
	var certKey = _ID('certKey').value;
	var mobile = _ID('mobile').value;
	var mobileClone = _ID('mobileClone').value;

	if (!certKey || certKey.length < 8) {
		alert('������ȣ�� �Է��� �ּ���.');
		_ID('certKey').focus();
		return;
	}

	if (mobile != mobileClone) {
		alert('������ȣ ��û�� �ڵ�����ȣ�� �ٸ��ϴ�.\n������ȣ�� �ٽ� ��û�� �ּ���.');
		_ID('mobile').focus();
		return;
	}

	otpCert.compareOTP(certKey, token);
}

/*** ��üũ ***/
function chkForm2(obj)
{
	if (obj.mobileCompared.value != 'Y') {
		alert('�޴��������� �Ϸ���� �ʾҽ��ϴ�.\n������ ���� ������ �ּ���.');
		obj.certKey.focus();
		return false;
	}

	if (obj.mobile.value != obj.mobileClone.value) {
		alert('������ȣ ��û�� �ڵ�����ȣ�� �ٸ��ϴ�.\n������ȣ�� �ٽ� ��û�� �ּ���.');
		obj.mobile.focus();
		return false;
	}

	var isChked = false;
	var El = document.getElementsByName('mno');
	if (El) for (i=0;i<El.length;i++) if (El[i].checked) isChked = true;
	if (isChked != true) {
		alert ('������ID�� ������ �ּ���.');
		return false;
	}

	if (!chkForm(obj)) return false;

	return true;
}
</script>

<form action="indb.login_cert.php" method=post onsubmit="return chkForm2(this)">
<input type="hidden" name="mode" value="regitContact" />
<input type="hidden" name="token" id="token" value="<?=$_token?>" />
<input type="hidden" name="mobileClone" id="mobileClone" value="" />
<input type="hidden" name="mobileCompared" id="mobileCompared" value="" />

<!-- �޴�����ȣ ���� : Start -->
<div class="title title_top">�޴�����ȣ �߰�<span>���� �޴������� ����� �޴�����ȣ�� �Է� �� �������ּ���.</span></div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>�޴�����ȣ</td>
	<td>
		<input type="text" name="mobile" id="mobile" value="" required fld_esssential class="line" label="�޴�����ȣ">
		<a href="javascript:void(0);" onClick="callSendOtp();"><img src="../img/login_cert/btn_get_authnum.gif" align="absmiddle" /></a>
	</td>
</tr>
<tr>
	<td>������ȣ</td>
	<td>
		<input type="text" name="certKey" id="certKey" maxlength="8" onkeydown="onlynumber()" value="" class="line" label="������ȣ">
		<a href="javascript:void(0);" onClick="callCompareOtp();"><img src="../img/login_cert/btn_form_confirm.gif" align="absmiddle" /></a>
	</td>
</tr>
</table>

<div style="margin-top:5px"><span class="small"><font class="extext">�� ������ȣ�� ��ȿ�ð��� <span class="red"><b>3��</b></span>�̸�, ������ȣ�� �Է� �� �ݵ�� <b>'Ȯ��'</b> ��ư�� Ŭ���ϼž� �մϴ�.</font></span></div>
<!-- �޴�����ȣ ���� : End -->

<!-- ������ID ��Ī : Start -->
<div style="padding-top:20px"></div>
<div class="title title_top">������ID ��Ī<span>����� �޴����� ��Ī�� ������ID�� �������ּ���..</span></div>

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="3"></td></tr>
<tr class="rndbg">
	<th>����</th>
	<th>������ID</th>
	<th>�̸�(�׷�)</th>
</tr>
<tr><td class="rnd" colspan="3"></td></tr>
<col width="30" align="center">
<? while ($data=$db->fetch($res)){ ?>
<tr height="30" align="center">
	<td class="noline"><input type="radio" name="mno" value="<?=$data['m_no']?>"></td>
	<td><?=$data['m_id']?></td>
	<td><?=$data['name']?>(<?=$r_grp[$data['level']]?>)</td>
</tr>
<tr><td colspan="3" class="rndline"></td></tr>
<? } ?>
</table>

<div style="margin-top:5px"><span class="small"><font class="extext">�� <a href="../basic/adminGroup.php" target="_top" class="extext_l">'�����ڱ��� ����'</a> �޴��� <b>'�����ڱ��ѱ׷�'</b>���� ��ϵ� ������ID�� ��Ī�Ͻ� �� �ֽ��ϴ�.</font></span></div>
<!-- ������ID ��Ī : End -->

<div style="padding:15px 0 0 0px" align=center><input type=image src="../img/btn_regist.gif" class=null></div>

</form>

<script>table_design_load();</script>