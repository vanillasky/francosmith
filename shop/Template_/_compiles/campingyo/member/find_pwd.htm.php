<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/member/find_pwd.htm 000005553 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<script language="javascript">
function goIDCheckIpin()
{
	var fm ;
	fm = document.getElementById("form");
	var popupWindow = window.open( "", "popupCertKey", "width=450, height=550, top=100, left=100, fullscreen=no, menubar=no, status=no, toolbar=no, titlebar=yes, location=no, scrollbar=no" );
<?php if($TPL_VAR["ipinyn"]=='y'){?>
		ifrmRnCheck.location.href="<?php echo url("member/ipin/IPINCheckRequest.php?")?>&callType=findpwd";
<?php }elseif($TPL_VAR["niceipinyn"]=='y'){?>
		ifrmRnCheck.location.href="<?php echo url("member/ipin/IPINMain.php?")?>&callType=findpwd";
<?php }?>
	return;
}

function gohpauthDream(){ //휴대폰본인인증
	var protocol = location.protocol;
	var callbackUrl = "<?php echo ProtocolPortDomain()?><?php echo $GLOBALS["cfg"]["rootDir"]?>/member/hpauthDream/hpauthDream_Result.php";
	ifrmHpauth.location.href=protocol+"//hpauthdream.godo.co.kr/module/NEW_hpauthDream_Main.php?callType=findpwd&shopUrl="+callbackUrl+"&cpid=<?php echo $TPL_VAR["hpauthDreamcpid"]?>";
	return;
}
</script>

<!-- 상단이미지 || 현재위치 -->
<TABLE width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td><img src="/shop/data/skin/campingyo/img/common/title_pwsearch.gif" border=0></td>
</tr>
<TR>
	<td class="path">HOME > 고객센터 > <B>비밀번호찾기</B></td>
</TR>
</TABLE>


<div class="indiv"><!-- Start indiv -->

<form method=post name=fm action="" onsubmit="return chkForm( this );" id=form>
<input type="hidden" name="act" value="Y">
<input type="hidden" name=rncheck value="none">
<input type="hidden" name=dupeinfo value="">

<div><img src="/shop/data/skin/campingyo/img/common/pw_01.gif" border=0></div>
<div style="border:1px solid #DEDEDE;" class="hundred">
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td style="border:5px solid #F3F3F3;padding:10;">

	<div style="float:left; width:130"><img src="/shop/data/skin/campingyo/img/common/pw_02.gif" border=0></div>

	<div style="float:right; width:480">
	<table cellpadding=2 cellspacing=0 border=0>
	<tr>
		<td style="text-align:right;padding-right:10;width:80;" class="input_txt">아이디</td>
		<td><input name="srch_id" type="text" size="29" required label="아이디" tabindex=1></td>
		<td rowspan=4 class=noline><input type="image" src="/shop/data/skin/campingyo/img/common/pw_btn.gif" tabindex=6></td>
	</tr>
	<tr>
		<td style="text-align:right;padding-right:10;width:80;" class="input_txt">이름</td>
		<td><input name="srch_name" type="text" size="29" required label="이름" tabindex=2></td>
	</tr>
<?php if($GLOBALS["checked"]["useField"]["resno"]){?>
	<tr>
		<td style="text-align:right;padding-right:10;width:80;" class="input_txt">주민등록번호</td>
		<td><input name="srch_num1" type="text" size="11" maxlength=6 required label="주민등록번호" onkeyup="if (this.value.length==6) this.nextSibling.nextSibling.focus()" onkeydown="onlynumber()" tabindex=3> - <input maxlength=7 name="srch_num2" type="password" size="11" required label="주민등록번호" onkeydown="onlynumber()" tabindex=4></td>
	</tr>
<?php }?>
<?php if($GLOBALS["checked"]["useField"]["email"]){?>
	<tr>
		<td style="text-align:right;padding-right:10;width:80;" class="input_txt">가입 메일주소</td>
		<td><input name="srch_mail" type="text" size="29" required label="메일주소" tabindex=5></td>
	</tr>
<?php }?>
	</table>
	</div>


	</td>
</tr>
</table>
</div>

<?php if($TPL_VAR["ipinyn"]=='y'||$TPL_VAR["niceipinyn"]=='y'||$TPL_VAR["hpauthDreamyn"]=='y'){?>
<div style="border:0px solid #DEDEDE; padding-top:20px;" class="hundred">
<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr>
	<td style="border:5px solid #F3F3F3;padding:10;">
		<div style="float:left; width:130"><img src="/shop/data/skin/campingyo/img/common/pw_02.gif" border=0></div>

		<div style="float:right; width:480">
		<table cellpadding=2 cellspacing=0 border=0 style="float:left;">
		<tr>
			<td><br>
			<img src="/shop/data/skin/campingyo/img/ipin/Regist_box_icon.gif"/> <font color='5d5d5d'>회원 가입시 사용 하신 본인확인 인증 수단을 선택하시고 필요한 사항을 입력하세요 </font>
		</td>
		</tr>
		<tr height="10">
			<td></td>
		</tr>
		<tr>			
			<td style="text-align:center;"  class=noline>
<?php if($TPL_VAR["ipinyn"]=='y'||$TPL_VAR["niceipinyn"]=='y'){?>
				<span style="padding-right:10px;">
				<img src="/shop/data/skin/campingyo/img/ipin/ipin_btn.gif" onclick="goIDCheckIpin();" style="cursor:pointer;">
				<iframe id="ifrmRnCheck" name="ifrmRnCheck" style="width:500px;height:500px;display:none;"></iframe>
				</span>
<?php }?>
<?php if($TPL_VAR["hpauthDreamyn"]=='y'){?>
				<span>
				<img src="/shop/data/skin/campingyo/img/auth/hp_btn.gif" alt="휴대폰본인인증" onclick="gohpauthDream();" style="cursor:pointer;">
				<iframe id="ifrmHpauth" name="ifrmHpauth" style="width:500px;height:500px;display:none;"></iframe>
				</span>
<?php }?>
			</td>
		</tr>
		</table>
		</div>
	</td>
</tr>
</table>
</div>
<?php }?>
</form>

<div align="center" style="padding-top:15">
<a href="<?php echo url("member/login.php")?>&"><img src="/shop/data/skin/campingyo/img/common/id_btn_login.gif"></a>&nbsp;
<a href="<?php echo url("member/find_id.php")?>&"><img src="/shop/data/skin/campingyo/img/common/pw_btn_id.gif"></a>
</div>

</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>