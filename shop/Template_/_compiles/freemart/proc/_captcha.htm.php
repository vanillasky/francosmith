<?php /* Template_ 2.2.7 2014/07/30 21:43:01 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/proc/_captcha.htm 000002014 */ ?>
<table cellpadding=0 cellspacing=0>
<tr>
<td>
	<IMG src="../proc/captcha.php" align="absmiddle" id="el-captcha-text">
</td>
<td width=10></td>
<td><div class=stxt>���̴� ������� ���� �� ���ڸ� ��� �Է��� �ּ���. <a href="javascript:void(0);" onClick="fnRefreshCaptchaText();"><img src="/shop/data/skin/freemart/img/common/btn_img_click.gif" align="absmiddle"></a></div>
<div><input name=captcha_key style="width:120;" maxlength=5 style="ime-mode:disabled;text-transform:uppercase;" onKeyUp="javascript:this.value=this.value.toUpperCase();" onfocus="this.select()" label="�ڵ���Ϲ�������" class=linebg required></div></td>
</tr></table>

<script type="text/javascript">
var chkFormSubExist = false;
if (typeof(chkFormSub) == 'function') {
	chkFormSubExist = true;
}
if (chkFormSubExist === false) {
	var funStr = chkForm.toString().replace('chkForm','chkFormSub');
	eval(funStr);
}
</script>
<script type="text/javascript">
if (chkFormSubExist === false) {
	function chkForm(form)
	{
		if (typeof(form['captcha_key']) == 'object') {
			if (form['captcha_key'].value == '') {
				alert('[�ڵ���Ϲ���] �ʼ��Է»���');
				return false;
			}

			// �ڵ���Ϲ��� ����
			if (window.XMLHttpRequest)
				xmlHttp = new XMLHttpRequest();
			else if (window.ActiveXObject)
				xmlHttp = new ActiveXObject("Microsoft.XMLHTTP");

			var url = "../proc/captcha_indb.php";
			xmlHttp.open("POST", url, false);
			xmlHttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			xmlHttp.send("mode=chkBoardCaptcha&id=" + form['id'].value+"&captcha_key=" + form['captcha_key'].value);
			if (xmlHttp.responseText != 'true') {
				alert(xmlHttp.responseText);
				return false;
			}
		}

		return chkFormSub(form);
	}
}

function fnRefreshCaptchaText() {
	var img = document.getElementById('el-captcha-text');
	img.src = "../proc/captcha.php?" + new Date().getTime();
}
</script>