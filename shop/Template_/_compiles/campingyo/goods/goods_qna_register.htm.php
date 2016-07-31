<?php /* Template_ 2.2.7 2014/03/05 23:19:27 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/goods/goods_qna_register.htm 000008785 */ ?>
<html>
<head>
<title>상품문의작성</title>
<meta http-equiv="Content-Type" content="text/html; charset=euc-kr">
<script src="/shop/data/skin/campingyo/common.js"></script>
<link rel="styleSheet" href="/shop/data/skin/campingyo/style.css">
<script>
function fitwin()
{
	window.resizeTo(750,150);
	var borderY = document.body.clientHeight;

	width	= 750;
	height	= document.body.scrollHeight + borderY + 10;

	windowX = (window.screen.width-width)/2;
	windowY = (window.screen.height-height)/2;

	if(width>screen.width){
		width = screen.width;
		windowX = 0;
	}
	if(height>screen.height){
		height = screen.height;
		windowY = 0;
	}

	window.moveTo(windowX,windowY);
	window.resizeTo(width,height);
}

function chkForm2(form) {
	if (form.rcv_email.checked == true && form.email.value =='') {
		alert('이메일 주소를 입력해 주세요.');
		return false;
	}

	if (form.rcv_sms.checked == true && form.phone.value =='') {
		alert('문자메시지를 받으실 휴대폰 번호를 입력해 주세요.');
		return false;
	}

	form.subject.value = encodeURIComponent(form.subject.value);
	form.contents.value = encodeURIComponent(form.contents.value);
	form.encode.value = 'y';

	return chkForm(form);
}

/*------------------------------ 특정 한글 호환 2013-05-08 추가 ------------------------------*/
function htmlspecialchars (string) {
	return string.replace(/&/g, "&amp;").replace(/\"/g,'&quot;').replace(/</g,'&lt;').replace(/>/g,'&gt;');
}

function htmlEncodeContent(form) {

	if (form.rcv_email.checked == true && form.email.value =='') {
		alert('이메일 주소를 입력해 주세요.');
		return false;
	}

	if (form.rcv_sms.checked == true && form.phone.value =='') {
		alert('문자메시지를 받으실 휴대폰 번호를 입력해 주세요.');
		return false;
	}

	form.subject.value = htmlspecialchars(form.subject.value);
	form.contents.value = htmlspecialchars(form.contents.value);
	form.encode.value = 'htmlencode';
	return chkForm(form);
}
/*--------------------------------------------------------------------------------------------*/
</script>
</head>
<body onload="fitwin()">

<form name="frmQna" method=post action="<?php echo $TPL_VAR["goodsQNAActionUrl"]?>" onSubmit="return htmlEncodeContent(this)">
<input type=hidden name=mode value="<?php echo $GLOBALS["mode"]?>">
<input type=hidden name=goodsno value="<?php echo $GLOBALS["goodsno"]?>">
<input type=hidden name=sno value="<?php echo $GLOBALS["sno"]?>">
<input type=hidden name=encode value="">
<input type=hidden name=agree value="" required fld_esssential msgR="개인정보 수집 및 이용에 대한 안내에 동의 하셔야 작성이 가능합니다.">

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td height="500" style="border:10px solid #000000" valign="top">

	<div style="width:100%; background:#000000; border-bottom:2px solid #DDDDDD"><img src="/shop/data/skin/campingyo/img/common/popup_title_qna.gif"></div>

	<div style="margin-left:20px; margin-right:20px;">
	<div style="border-width:5; border-style:solid; border-color:#EFEEEE; margin-top:10px;" class="hundred">
	<table width="100%" cellpadding=0 cellspacing=0 border=0 align="center" style="margin-top:5; margin-bottom:5">
	<tr>
		<td width="50"><?php echo goodsimg($GLOBALS["goods"]["img_s"], 50)?></td>
		<td style="line-height:20px;padding-left:10">
		<b><?php echo $GLOBALS["goods"]["goodsnm"]?></b><br>
		<?php echo number_format($GLOBALS["goods"]["price"])?>원
		</td>
	</tr>
	</table>
	</div>

	<div style="border-width:1px; border-style:solid; border-color:#DEDEDE; margin-top:5px;" class="hundred">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="border:3px solid #F3F3F3; padding:5 5 5 5" align="center">

		<table id="form" cellpadding="5" cellspacing="0 border="0">
		<col width=60>
		<tr>
			<td class="input_txt" align="right">작성자</td>
			<td>
			<div style="float:left; width:50%;"><input type=text name=name style="width:100" required fld_esssential label="작성자" value="<?php echo $GLOBALS["data"]["name"]?>"></div>
<?php if(!$GLOBALS["sess"]&&empty($GLOBALS["data"]['m_no'])){?>
			<div style="float:left;"><span class="input_txt">비밀번호</span> <input type=password name=password style="width:100" required fld_esssential label="비밀번호"></div>
<?php }?>
			</td>
		</tr>
		<tr><td colspan=2 height=1 bgcolor="#DEDEDE" style="padding:0"></td></tr>
		<tr>
			<td class="input_txt" align="right">이메일</td>
			<td><input type=text name=email style="width:100" label="이메일" value="<?php echo $GLOBALS["data"]["email"]?>"> <label class="noline"><input type="checkbox" name="rcv_email" value="1"<?php if($GLOBALS["data"]["rcv_email"]){?> checked<?php }?>> 받습니다.</label></td>
		</tr>
		<tr><td colspan=2 height=1 bgcolor="#DEDEDE" style="padding:0"></td></tr>
		<tr>
			<td class="input_txt" align="right">문자메시지</td>
			<td><input type=text name=phone style="width:100" label="문자메시지" value="<?php echo $GLOBALS["data"]["phone"]?>">  <label class="noline"><input type="checkbox" name="rcv_sms" value="1"<?php if($GLOBALS["data"]["rcv_sms"]){?> checked<?php }?>> 받습니다.</label></td>
		</tr>
<?php if($GLOBALS["cfg"]["qnaSecret"]=='secret'){?>
		<tr><td colspan=2 height=1 bgcolor="#DEDEDE" style="padding:0"></td></tr>
		<tr>
			<td class="input_txt" align="right">비밀글</td>
			<td class="noline"><input type="checkbox" name="secret" value="1"<?php echo $GLOBALS["data"]["chksecret"]?>> 비밀글</td>
		</tr>
<?php }?>
		<tr><td colspan=2 height=1 bgcolor="#DEDEDE" style="padding:0"></td></tr>
		<tr>
			<td class="input_txt" align="right">제목</td>
			<td><input type=text name=subject style="width:100%" required fld_esssential label="제목" value="<?php echo $GLOBALS["data"]["subject"]?>"></td>
		</tr>
		<tr><td colspan=2 height=1 bgcolor="#DEDEDE" style="padding:0"></td></tr>
		<tr>
			<td class="input_txt" align=right>내용</td>
			<td>
			<div style="height:400px;padding-top:5px;position:relative;z-index:99">
			<!-- mini editor -->
			<textarea name=contents style="width:500px;height:350px" type=editor fld_esssential label="내용"><?php echo htmlspecialchars($GLOBALS["data"]["contents"])?></textarea>
			<script src=../lib/meditor/mini_editor.js></script>
			<script>mini_editor("../lib/meditor/")</script>
			</div>
			</td>
		</tr>
<?php if($GLOBALS["cfg"]["qnaSpamBoard"]& 2){?>
		<tr>
			<td align=right class=input_txt>자동등록방지</td>
			<td class=cell_L><?php echo $this->define('tpl_include_file_1',"proc/_captcha.htm")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?></td>
		</tr>
<?php }?>
		</table>

		</td>
	</tr>
	</table>
	</div>

	<div style="border-width:1px; border-style:solid; border-color:#DEDEDE; margin-top:5px;" class="hundred">
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td style="border:3px solid #F3F3F3; padding:5 5 5 5" align="center">

		<table width="100%">
		<col width="80">
		<col width="">
		<tr>
			<td colspan="2" style="padding-left:10px;" class="b_cate blue">개인정보 수집 및 이용에 대한 안내</td>
		</tr>
		<tr>
			<td class="input_txt" align="right">수집항목 :</td>
			<td class="stxt">
				이름, 전화번호, 이메일주소
			</td>
		</tr>
		<tr>
			<td class="input_txt" align="right">수집목적 :</td>
			<td class="stxt">
				상품문의 접수 및 결과 회신
			</td>
		</tr>
		<tr>
			<td class="input_txt" align="right" valign="top">이용기간 :</td>
			<td class="stxt">
				원칙적으로 개인정보 수집 및 이용목적이 달성된 후에는 해당 정보를 지체 없이 파기합니다.<br>
				단, 관계법령의 규정에 의하여 보전할 필요가 있는 경우 일정기간 동안 개인정보를 보관할 수 있습니다.
			</td>
		</tr>
		<tr>
			<td class="stxt" colspan="2" style="padding-left:30px;">
			그 밖의 사항은 <strong><?php echo $GLOBALS["cfg"]["shopName"]?> <a href="<?php echo url("service/private.php")?>&" class="blue" target="_blank">개인정보취급방침</a></strong>을 준수합니다.
			</td>
		</tr>
		</table>

		<div style="padding:8px;">
		<label><input type="radio" name="_agree" value="y" onClick="document.frmQna.agree.value='y';"> 동의합니다</label>
		&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		<label><input type="radio" name="_agree" value="n" onClick="document.frmQna.agree.value='';"> 동의하지 않습니다</label>
		</div>

		</td>
	</tr>
	</table>
	</div>
	</div>

	<TABLE width="100%">
	<tr>
		<td align="center" style="padding-top:5"><input type="image" src="/shop/data/skin/campingyo/img/common/btn_upload.gif"></td>
	</tr>
	</TABLE>
	<TABLE width="100%" cellpadding="0" cellspacing="0" border="0">
	<TR>
		<TD align=right><A HREF="javascript:this.close()" onFocus="blur()"><img src="/shop/data/skin/campingyo/img/common/popup_close.gif"></A></TD>
	</TR>
	</TABLE>

	</td>
</tr>
</table>

</form>

</body>
</html>