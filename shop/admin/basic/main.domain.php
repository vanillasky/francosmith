<!-- ������ Start -->
<script>
// ���� ������ üũ �κ�
function check_reg_eng_form(){
	var f = document.regist_engine;
	var alpha		= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	var numeric		= '1234567890';
	var special		= ' ~!@#$%^&*()_=+|\\{}[];:"\'<>,.?\/';
	var i;
	var chk_checkbox = false;
	var gtld		= document.regist_engine['gtld[]'];
	var cctld		= document.regist_engine['cctld[]'];
	
	if (f.eng_domain.value.charAt(f.eng_domain.value.length-1) == '.') {
		f.eng_domain.value = f.eng_domain.value.substr(0, f.eng_domain.value.length-1);
	}
	
	if (f.eng_domain.value.charAt(0) == '-') {
		alert ("������ ���� '-'�� ������ �� �����ϴ�.");
		f.eng_domain.focus();
		return false;
	}
	if (f.eng_domain.value.charAt(f.eng_domain.value.length-1) == '-') {
		alert ("������ ���� '-'�� ���� �� �����ϴ�.");
		f.eng_domain.focus();
		return false;
	}
	if (f.eng_domain.value.length < 2 || f.eng_domain.value.length > 63) {
		alert ("������ ���� 2�� �̻� 63�� ���Ϸ� �����˴ϴ�.");
		f.eng_domain.focus();
		return false;
	}
	
	if (checknorm_nomsg(f.eng_domain,  '�����θ�', alpha, 63) == false) {
		alert("������ ���� �ҹ��ڷθ� �־��ֽñ� �ٶ��ϴ�.");
		f.eng_domain.focus();
		return false;
	}
	
	if (checknorm_nomsg(f.eng_domain,  '�����θ�', special, 63) == false) {
		alert("������ �� Ư�����ڴ� '-' �ܿ��� ������� �ʽ��ϴ�");
		f.eng_domain.focus();
		return false;
	}

	if(f.eng_domain.value.length > 3) {
		if(f.eng_domain.value.substring(0, 4).indexOf('bq--') >= 0) {
			alert("bq--�� ����� �����θ� �Դϴ�.");
			f.eng_domain.focus();
			return false;
		}
		if(f.eng_domain.value.substring(0, 4).indexOf('xn--') >= 0) {
			alert("xn--�� �������� �����θ� �Դϴ�.");
			f.eng_domain.focus();
			return false;
		}
	}
	
	for(i = 0; i < gtld.length ; i++) {
		if (gtld[i].checked)
		{
			chk_checkbox = true;
		}
	}
	for(i = 0; i < cctld.length ; i++) {
		if (cctld[i].checked)
		{
			chk_checkbox = true;
		}
	}
	
	if(chk_checkbox == true) {
		f.action = "http://domain.godo.co.kr/regist/domain_search.php";
		f.target = "_blank";
	} else {
		alert("����� ���ϴ� ������ ������ ������ �ֽñ� �ٶ��ϴ�..");
		return false;
	}
}

// ��ȿ�� üũ
function checknorm_nomsg(target, cmt, astr, lmax) {
	var i;
	var t = target.value;
	
	if (t.length >= 1) {
		for (i=0; i<astr.length; i++){
			if(t.indexOf(astr.charAt(i)) >= 0) {
				return false;
			}
		}
	}
	return true;
}

// �����θ� �Է��ϵ��� üũ�ϴ� �Լ�(KEY EVENT)
function onlyDom(){
	if( (event.keyCode>=65 && event.keyCode<=90) || (event.keyCode>=48 && event.keyCode<=57) || (event.keyCode>=96 && event.keyCode<=105) || (event.keyCode==8) || (event.keyCode==9) || (event.keyCode==37) || (event.keyCode==39) || (event.keyCode==46) || (event.keyCode==13) || (event.keyCode==108) || (event.keyCode==189) || (event.keyCode==109));
	else event.returnValue=false;
}

function reset_a(obj)
{
	if (obj.value=="���� ������ �Է�") obj.value = "";
}

function domain_hidden(id){
	var obj =  document.getElementById(id);
	if( obj.style.display == 'block' ) obj.style.display = 'none';
	else obj.style.display = 'block';
}
</script>
<form name="regist_engine" METHOD="post" ACTION="" onsubmit="return check_reg_eng_form()">
<input type="hidden" name="mode" value="search" />
<input type="hidden" name="domT" value="eng" />
<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
<td align="center" bgcolor="white" style="padding-bottom:8px">
	
    <table cellpadding="0" cellspacing="0" border="0">
	<tr>
	<td align="center" colspan="3" style="padding-bottom:5px"><img src="../img/text_domain.gif"></td>
	</tr>
	<tr>
	<td><img src="../img/domain_www.gif"></td>
	<td>	<input type="text" name="eng_domain" style="border:4px #719cce solid; height:31px; width:250px; padding-left:4px; paddint-top:5px; font:16px tahoma;" tabindex='2' onfocus="domain_hidden('domainID');">
			<div style="position:relative;"><div id='domainID' onmouseup='document.regist_engine.eng_domain.focus();' style="display:block;position:absolute;top:-27px;left:6px;z-index=2;"><img src="../img/domain_textbg.gif" border="0" align="absmiddle"></div></div></td>
	<td><input type="image" src="../img/domain_btn.gif" class="null" /></td>
	</tr>
	<tr>
	<td align="center" colspan="3" height="5"></td>
	</tr>
	</table>
	
	<table border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td class="noline"><input type="checkbox" name="gtld[]" value="com" checked /></td>
	<td width="42">.com</td>
	<td class="noline"><input type="checkbox" name="gtld[]" value="net" checked /></td>
	<td width="42">.net</td>
	<td class="noline"><input type="checkbox" name="cctld[]" value="kr" checked /></td>
	<td width="42">.kr</td>
	<td class="noline"><input type="checkbox" name="cctld[]" value="co.kr" checked /></td>
	<td width="42">.co.kr</td>
	</tr>
	</table>

</td>
</tr>
</table>
</form>