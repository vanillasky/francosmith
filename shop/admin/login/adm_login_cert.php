<?
include "../../lib/library.php";

// ������� ������ �����ں��� ���� �α��λ��� üũ
$alCert = Core::loader('adminLoginCert');
if ($alCert->inStatus() == 'failure') {
	unset($ici_admin);
}

// ������ üũ
if ($ici_admin) go("../index.php");

// OTP ����ó
$contacts = $alCert->getOtpContants('Y');

// ��ū ����
$otp = Core::loader('gd_otp');
$_token = $otp->getToken();
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<script src="../common.js"></script>

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

		sendOTP : function(mobileAocSno,token) {
			gd_ajax({
				url : '../login/indb.adm_login_cert.php',
				type : 'POST',
				param : '&mode=sendLoginOtp&mobileAocSno='+mobileAocSno+'&token='+token,
				success : function(rst) {
					if (rst == '0000') {
						alert('�޴������� ������ȣ�� �����Ͽ����ϴ�. \n���۵� ������ȣ�� Ȯ�� �� �Է��� �ּ���.');
					}
					else {
						return otpCert.raiseError(rst);
					}
				}
			});
		},

		compareOTP : function(otp,mobileAocSno,token) {
			gd_ajax({
				url : '../login/indb.adm_login_cert.php',
				type : 'POST',
				param : '&mode=compareLoginOtp&otp='+otp+'&mobileAocSno='+mobileAocSno+'&token='+token,
				success : function(rst) {
					if (rst == '0000') {
						window.location.replace('../login/login.php');
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
	var mobileAocSno = _ID('mobileAocSno').value;

	otpCert.sendOTP(mobileAocSno, token);
}

function callCompareOtp(f) {
	var token = f.token.value;
	var mobileAocSno = f.mobileAocSno.value;
	var certKey = f.certKey.value;

	if (!certKey || certKey.length < 8) {
		alert('������ȣ�� �Է��� �ּ���.');
		f.certKey.focus();
		return false;
	}

	otpCert.compareOTP(certKey, mobileAocSno, token);
	return false;
}
</script>
</head>

<body style="margin:0; background-color:#ffffff;">
<form name="certForm" method="post" action="" onsubmit="return callCompareOtp(this)">
<input type="hidden" name="token" id="token" value="<?=$_token?>" />

<div style="text-align:center;">
	<div style="background:url(../img/login_cert/logo.gif) no-repeat 14px 9px; height:31px; width:1002px; margin:0 auto;"></div>

	<div style="background-color:#2fade7; height:100px;">
		<div style="background:url(../img/login_cert/tit_cp_security.png) no-repeat 14px 20px; height:78px; width:1002px; margin:0 auto;"></div>
	</div>

	<div style="width:670px; margin:0 auto;text-align:left;">
		<div style="background:url(../img/login_cert/txt_security_login.gif) no-repeat; margin-top:80px; height:44px;"></div>

		<div style="font:12px Dotum; color:#767676; line-height:22px; padding:15px 0;">
			1) ���� �޴������� ����� �޴�����ȣ�� ���� �� <b>'������ȣ ��û'</b>�� Ŭ���մϴ�.<br/>
			2) �޴������� ���۵� ������ȣ�� �Է��ϰ� <b>'Ȯ��'</b> ��ư�� Ŭ���մϴ�.
		</div>

		<div style="border:solid 1px #e0e0e0; height:158px;">
		<table width="410" cellpadding="0" cellspacing="0" border="0" style="margin-top:40px; margin-left:130px;">
		<tr>
			<td width="83px"><img src="../img/login_cert/txt_cp.gif" alt="�޴�����ȣ"/></td>
			<td>
				<select name="mobileAocSno" id="mobileAocSno" style="width:218px;height:34px;font:14px dotum;color:#767676;border:solid 1px #e0e0e0;padding-left:3px;" label="�޴�����ȣ">
					<? foreach ($contacts as $data) echo '<option value="'.$data['aoc_sno'].'">'.$data['aoc_mobile'].'</option>'; ?>
				</select>
				<a href="javascript:void(0);" onClick="callSendOtp();"><img src="../img/login_cert/btn_code.gif" align="absmiddle" alt="������ȣ ��û" /></a>
			</td>
		</tr>
		<tr><td height="11px" colspan="2"></td></tr>
		<tr>
			<td width="83px"><img src="../img/login_cert/txt_code.gif" alt="������ȣ"/></td>
			<td><input type="text" name="certKey" id="certKey" maxlength="8" onkeydown="onlynumber()" style="width:218px;height:34px;font:14px dotum;color:#767676;border:solid 1px #e0e0e0;padding-left:3px;" required label="������ȣ" /></td>
		</tr>
		</table>
		</div>

		<div style="font:12px Dotum; color:#9e9e9e; line-height:22px; padding:15px 0 25px;">
			�� ������ȣ�� ��ȿ�ð��� <b>3��</b>�Դϴ�.<br/>
			�� ������ȣ ��û �� <b>SMS 1����Ʈ�� ����</b>�˴ϴ�.
		</div>

		<div style="text-align:center"><input type="image" src="../img/login_cert/btn_confirm.gif" border="0"></div>
	</div>
</div>
</form>
</body>
</html>