<?
include "../../lib/library.php";

// �α��ο��� �����ں��� �������� üũ
$alCert = Core::loader('adminLoginCert');
$alStat = $alCert->loginStatus();
if ($alStat == 'failure') {
	go("../login/adm_login_cert.php");
}
else if ($alStat == 'success') {
	unset($ici_admin);
}

// ������ üũ
if ($ici_admin) go("../index.php");

setCookie('Xtime',time(),0,'/');

### ���ȼ����� �α�url
if ($cfg['ssl'] == "1") { //���ȼ����� ����ϸ�...
	$loginActionUrl = $sitelink->link('member/login_ok.php','ssl');
} else {
	$loginActionUrl = "../../member/login_ok.php";
}
?>
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<script src="../common.js"></script>
</head>

<body onload="document.getElementsByName('m_id')[0].focus();" style="margin:0; background:url(../img/login/intro_bg_back.jpg) top left repeat-x #ffffff;">
<form method="post" action="<?=$loginActionUrl?>" onsubmit="return chkForm(this);">
<input type="hidden" name="returnUrl" value="../admin/index.php">
<input type="hidden" name="adm_login" value="1" />

<div style="background:url(../img/login/intro_logo.jpg) no-repeat left top; height:33px"></div>
<div style="background:url(../img/login/intro_bg.jpg) center -33px no-repeat; height:790px;padding-top:362px; text-align:center;">
	<div style="width:435px;margin:0 auto;text-align:left;">
		<table cellpadding="0" cellspacing="0" border="0" style="margin-left:67px;">
		<tr>
			<td width="240">
				<div><input type=text name="m_id" style="width:230px;height:22px;font:10pt tahoma;border-width:0px;padding-left:3px;background:url(../img/login/bg_id.gif) no-repeat 4px left;" required label="���̵�" value="<?=$_GET[m_id]?>" onKeyUp="if(this.value != '') this.style.backgroundImage='';"></div>
				<div style="padding-top:5px"><input type="password" name="password" style="width:230px;height:22px;font:10pt tahoma;border-width:0px;padding-left:3px;background:url(../img/login/bg_pass.gif) no-repeat 4px left;" required label="��й�ȣ" value="<?=$_GET[password]?>" onKeyUp="if(this.value != '') this.style.backgroundImage='';"></div>
			</td>
			<td style="padding-left:10px"><input type="image" src="../img/login/btn_login.gif" border="0"></td>
		</tr>
		</table>

		<? if($cfg[ssl] == "1" ) { ?>
		<script type="text/javascript">
		<!--
		function checkedSSL(chkObj) {
			var form = chkObj.form;
			if(chkObj.checked) { //��������üũ
				form.action="<?=$loginActionUrl?>";
			} else { //��������üũ����
				form.action="../../member/login_ok.php";
			}
		}
		//-->
		</script>
		<div style="margin:8px 0 0 60px;">
			<label><input type="checkbox" name="sslcheck" value="1" checked onclick="checkedSSL(this)" /><img src="../img/login/login_security.gif" style="cursor:pointer;" onmouseover="openLayer('ssltooltip','block')" onmouseout="openLayer('ssltooltip','none')" /></label>
			<div style="position:relative"><div id="ssltooltip" style="display:none;position:absolute;left:68px;top:-42px"><img src="../img/login/login_security_info.png"/></div></div>
		</div>
		<? } ?>
		<div style="margin:8px 0 0 60px;font:8pt Dotum;">
			<span style="cursor:pointer; color:#555555;" onmouseover="openLayer('logintooltip','block')" onmouseout="openLayer('logintooltip','none')">������ �α��� ���� ã��</span>
			<div style="position:relative"><div id="logintooltip" style="display:none;position:absolute;width:470px;left:140px;top:-42px;background-color:#ffffff;border:solid 1px #808080;padding:5px; color:#555555;">
				[ ������ �α��� ���� ã�� ]<br>
				<br>
				1. ���θ� ��û�� ������ �α��� ������ ������ ���� ���� ���<br>
				- ���θ� ���� ������ ���Ϸ� �ٽ� �޾� Ȯ�� �� �� �ֽ��ϴ�.<br>
				- Ȯ�ι�� :<br>
				godo.co.kr > ��ȸ�� �α��� > ���̰� > ����� ���θ����� > ��������<br>
				���θ� �ּ� ������ [����]��ư Ŭ�� > ���θ� �⺻������ [���ø��� �ޱ�] ��������<br>
				[���Ϻ�����] ��ư Ŭ���Ͽ� ���������� ���Ϸ� �ٽ� �޾� Ȯ�� �� �� �ֽ��ϴ�.<br>
				<br>
				2. ������ �α��� ������ ������ ���� �ִ� ���<br>
				- ���ϴ� ������ ���̵�� ��й�ȣ�� �缳�� ��û �� �� �ֽ��ϴ�.<br>
				- ��û��� :<br>
				godo.co.kr > ��ȸ�� �α��� > [1:1����] ��ư Ŭ�� > ���� ���뿡 ������ �α��� ������<br>
				�ؾ���ȴٰ� ���ϴ� ������ ���̵�� ��й�ȣ�� �����Ͽ� �缳�� �ش޶�� �����ֽø�<br>
				������ �缳���� �ص帳�ϴ�.
			</div></div>
		</div>
	</div>
</div>
</form>
</body>
</html>