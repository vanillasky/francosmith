<?php /* Template_ 2.2.7 2014/05/29 07:10:05 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/member/social_member_join.htm 000013594 */ ?>
<?php $this->print_("header",$TPL_SCP,1);?>


<style type="text/css">
.scroll	{
scrollbar-face-color: #FFFFFF;
scrollbar-shadow-color: #AFAFAF;
scrollbar-highlight-color: #AFAFAF;
scrollbar-3dlight-color: #FFFFFF;
scrollbar-darkshadow-color: #FFFFFF;
scrollbar-track-color: #F7F7F7;
scrollbar-arrow-color: #838383;
}
#boxScroll{width:96%; height:130px; overflow: auto; BACKGROUND: #ffffff; COLOR: #585858; font:9pt 돋움;border:1px #dddddd solid; overflow-x:hidden;text-align:left;margin-left:10px;}
</style>

<script type="text/javascript">
var checkedID;
window.onload = function()
{
	var socialMemberJoinForm = document.getElementById("form");
	var checkIDDuplicate = document.getElementById("check-id-duplicate");

	socialMemberJoinForm.submit = function()
	{
		this.action = "./social_member.php";
		var formSubmit = document.getElementById("form-submit");
		formSubmit.click();
	};
	checkIDDuplicate.onclick = function()
	{
		checkedID = socialMemberJoinForm.m_id.value;
		ifrmHidden.location.href="<?php echo url("member/indb.php?")?>&mode=chkId&m_id=" + socialMemberJoinForm.m_id.value;
	};

	// 본인확인 로직에서는 frmAgree를 사용
	document.frmAgree = document.frmMember;

	defaultRnCheckType();
};
var checkSubmit = function()
{
	var socialMemberJoinForm = document.getElementById("form");
	var rdo_ipin = document.getElementById("RnCheckType_ipin");
	var rdo_hpauthDream = document.getElementById("RnCheckType_hpauthDream");

	var result = chkForm2(socialMemberJoinForm);
	if (!result) {
		return;
	}
	else {
		if (checkedID !== socialMemberJoinForm.m_id.value) {
			socialMemberJoinForm.chk_id.value = "";
		}
		if (socialMemberJoinForm.chk_id.value !== "1") {
			alert("아이디 중복체크를 해주시기 바랍니다.");
			return;
		}
		if (!socialMemberJoinForm.m_id.value.match(/^[\xa1-\xfea-zA-Z0-9_-]{4,20}$/)) {
			alert('아이디 입력 형식 오류입니다');
			return;
		}
	}
	
	if (rdo_ipin && rdo_ipin.checked) {
		goIDCheckIpin();
	}
	else if (rdo_hpauthDream && rdo_hpauthDream.checked) {
		gohpauthDream();
	}
	else {
		if (chkagreement(socialMemberJoinForm)) {
			socialMemberJoinForm.submit();
		}
	}
};
var defaultRnCheckType = function()
{
	var authtype = document.getElementsByName("RnCheckType");

	if (authtype.item(0) != null) {
		var div_jumin = document.getElementById("div_RnCheck_jumin");
		var div_ipin = document.getElementById("div_RnCheck_ipin");
		var div_hpauthDream = document.getElementById("div_RnCheck_hpauthDream");

		if (authtype.item(0).value == "jumin") {
			div_jumin.style.display = "";
		}
		else if (authtype.item(0).value == "ipin") {
			div_ipin.style.display = "";
		}
		else if (authtype.item(0).value == "hpauthDream") {
			div_hpauthDream.style.display = "";
		}
		authtype.item(0).checked = true;
	}	
};
var selectRnCheckType = function()
{
	var div_jumin = document.getElementById("div_RnCheck_jumin");
	var div_ipin = document.getElementById("div_RnCheck_ipin");
	var div_hpauthDream = document.getElementById("div_RnCheck_hpauthDream");

	var rdo_jumin = document.getElementById("RnCheckType_jumin");
	var rdo_ipin = document.getElementById("RnCheckType_ipin");
	var rdo_hpauthDream = document.getElementById("RnCheckType_hpauthDream");

	if (rdo_jumin && rdo_jumin.checked == true) {
		if (div_jumin != null) {
			div_jumin.style.display = "";
		}
		if (div_ipin != null) {
			div_ipin.style.display = "none";
		}
		if (div_hpauthDream != null) {
			div_hpauthDream.style.display = "none";
		}
	}
	if (rdo_ipin && rdo_ipin.checked == true) {
		if (div_jumin != null) {
			div_jumin.style.display = "none";
		}
		if (div_ipin != null) {
			div_ipin.style.display = "";
		}
		if (div_hpauthDream != null) {
			div_hpauthDream.style.display = "none";
		}
	}
	if (rdo_hpauthDream && rdo_hpauthDream.checked == true) {
		if (div_jumin != null) {
			div_jumin.style.display = "none";
		}
		if (div_ipin != null) {
			div_ipin.style.display = "none";
		}
		if (div_hpauthDream != null) {
			div_hpauthDream.style.display = "";
		}
	}
};
var chkForm2 = function(fm)
{
	if (typeof(goIDCheck) != "undefined") {
		if (goIDCheck(fm) === false) {
			return false;
		}
	}

	return chkForm(fm);
};
var chkagreement = function(fm)
{
	if (chkRadioSelect(fm, "agree", "y", "[회원가입 이용약관]에 동의를 하셔야 회원가입이 가능합니다.") === false) {
		return false;
	}
	if (chkRadioSelect(fm, "private1", "y", "[개인정보보호를 위한 이용자 동의사항]에 동의를 하셔야 회원가입이 가능합니다.") === false) {
		return false;
	}

	return true;
};
</script>

<!-- 상단이미지 || 현재위치 -->
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td><img src="/shop/data/skin/campingyo/img/common/title_join.gif" border="0"></td>
	</tr>
	<tr>
		<td class="path">HOME > 회원가입 > <strong>SNS계정 회원가입</strong></td>
	</tr>
</table>


<div class="indiv"><!-- Start indiv -->

	<form id="form" name="frmMember" method="post" action="<?php echo url("member/social_member.php")?>&" target="ifrmHidden">
		<input type="hidden" name="MODE" value="join">
		<input type="hidden" name="SOCIAL_CODE" value="<?php echo $TPL_VAR["SOCIAL_CODE"]?>"/>
		<input type="hidden" name="chk_id" value=""/>
		<input type="hidden" name="email" value="<?php echo $TPL_VAR["email"]?>"/>
		<input type="hidden" name="mode" value="chkRealName">
		<input type="hidden" name="rncheck" value="none">
		<input type="hidden" name="nice_nm" value="">
		<input type="hidden" name="mobile" value="">
		<input type="hidden" name="dupeinfo" value="">
		<input type="hidden" name="birthday" value="">
		<input type="hidden" name="sex" value="">
		<input type="hidden" name="foreigner" value="">
		<input type="hidden" name="type">
<?php if($TPL_VAR["realnameyn"]=='y'||$TPL_VAR["ipinyn"]=='y'||$TPL_VAR["niceipinyn"]=='y'||$TPL_VAR["hpauthDreamyn"]=='y'){?>
		<input type="hidden" name="name" value="<?php echo $TPL_VAR["name"]?>"/>
<?php }?>

		<!-- 이용약관 -->
		<div><img src="/shop/data/skin/campingyo/img/common/join_txt_01.gif" border="0"></div>
		<div style="background-color: #f1f1f1; text-align: center; padding: 10px 10px 0 10px;">
			<textarea style="width: 100%; height: 190px; padding: 10px; background-color: #ffffff;" class="scroll" readonly><?php echo $this->define('tpl_include_file_1',"proc/_agreement.txt")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?></textarea>
			<div align="center" class="noline" style="height: 30px; margin-top: 10px;">
				<input type="radio" name="agree" value="y"> 동의합니다 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="radio" name="agree" value="n"> 동의하지 않습니다
			</div>
		</div>
		<p/>
		<!-- 개인정보취급방침 -->
		<div style="padding-left: 7px;"><strong style="color: #f7443f;">개인정보취급방침</strong></div>
		<div style="padding-top: 10px; background-color: #f1f1f1; text-align: center;">
			<div align="left" style="height: 26px; padding: 3px 0 0 10px;">
				<strong>● 개인정보보호를 위한 이용자 동의사항</strong> (자세한 내용은 “<a href="<?php echo url("service/private.php")?>&">개인정보취급방침</a>”을 확인하시기 바랍니다)
			</div>
			<div id="boxScroll" class="scroll">
				<?php echo $this->define('tpl_include_file_2',"/service/_private1.txt")?> <?php $this->print_("tpl_include_file_2",$TPL_SCP,1);?>

			</div>
			<div align="center" class=noline style="height: 30px; margin-top: 10px;">
				<input type="radio" name="private1" value="y"> 동의합니다 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="radio" name="private1" value="n"> 동의하지 않습니다
			</div>
		</div>

		<p/>

<?php if($GLOBALS["cfg"]['private2YN']=='Y'){?>
		<div style="padding-top: 10px; background-color: #f1f1f1; text-align: center;">
			<div align="left" style="height:26;padding:3px 0 0 10px;">
				<strong>● 개인정보 제3자 제공 관련</strong>
			</div>
			<div id="boxScroll" class="scroll">
			<?php echo $this->define('tpl_include_file_3',"/service/_private2.txt")?> <?php $this->print_("tpl_include_file_3",$TPL_SCP,1);?>

			</div>
			<div align=center class=noline style="height:30;margin-top:10px;" >
			<input type="radio" name="private2" value="y" required fld_esssential label="개인정보 제3자 제공 관련" msgR="동의 여부를 체크해주세요."> 동의합니다 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			<input type="radio" name="private2" value="n" required fld_esssential label="개인정보 제3자 제공 관련" msgR="동의 여부를 체크해주세요."> 동의하지 않습니다
			</div>
		</div>
		<p/>
<?php }?>

<?php if($GLOBALS["cfg"]['private3YN']=='Y'){?>
		<div style="padding-top: 10px; background-color: #f1f1f1; text-align: center;">
			<div align="left" style="height: 26px; padding: 3px 0 0 10px;">
				<strong>● 개인정보취급 위탁 관련</strong>
			</div>
			<div id="boxScroll" class="scroll">
			<?php echo $this->define('tpl_include_file_4',"/service/_private3.txt")?> <?php $this->print_("tpl_include_file_4",$TPL_SCP,1);?>

			</div>
			<div align=center class=noline style="height:30;margin-top:10px;" >
				<input type="radio" name="private3" value="y" required fld_esssential label="개인정보취급 위탁 관련" msgR="동의 여부를 체크해주세요."> 동의합니다 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<input type="radio" name="private3" value="n" required fld_esssential label="개인정보취급 위탁 관련" msgR="동의 여부를 체크해주세요."> 동의하지 않습니다
			</div>
		</div>
<?php }?>

		<div style="padding-left: 7px; color: #f7443f; font-weight: bold; margin-top: 20px; margin-bottom: 10px;">필수등록정보확인</div>
		<div style="border: 1px solid #dedede; margin-bottom: 20px;" class="hundred">
			<div style="border: 5px solid #f3f3f3; padding: 10px;">
				<table>
					<tr>
						<td>아이디</td>
						<td>
							<input type="text" name="m_id" value="<?php echo $TPL_VAR["email_id"]?>" label="아이디" fld_esssential/>
							<button id="check-id-duplicate" type="button" style="width: 79px; height: 17px; vertical-align: middle; background: url('/shop/data/skin/campingyo/img/common/btn_idcheck.gif') no-repeat; border: none; font-size: 0;">중복체크</button>
						</td>
					</tr>
<?php if($TPL_VAR["realnameyn"]!=='y'&&$TPL_VAR["ipinyn"]!=='y'&&$TPL_VAR["niceipinyn"]!=='y'&&$TPL_VAR["hpauthDreamyn"]!=='y'){?>
					<tr>
						<td>이름</td>
						<td>
							<input type="text" name="name" value="<?php echo $TPL_VAR["name"]?>" label="이름" fld_esssential/>
						</td>
					</tr>
<?php }?>
				</table>
			</div>
		</div>

		<!-- 본인확인 인증수단 -->
<?php if($TPL_VAR["realnameyn"]=='y'||$TPL_VAR["ipinyn"]=='y'||$TPL_VAR["niceipinyn"]=='y'||$TPL_VAR["hpauthDreamyn"]=='y'){?>
		<div style="padding-left:7"><font color=f7443f><b>본인확인 인증 수단</b></font></div>
		<table height="35">
			<tr>
<?php if($TPL_VAR["realnameyn"]=='y'){?>	
				<td class="noline">
					<label for="RnCheckType_jumin" style="width:130px;height:20px;display:inline-block;background:url('/shop/data/skin/campingyo/img/ipin/Regist_realName_title_1.gif') no-repeat 17px 3px;">
					<input type="radio" name="RnCheckType" value="jumin" id="RnCheckType_jumin" onclick="selectRnCheckType()">
					</label>
				</td>
				<td width="5">&nbsp;</td>				
<?php }?>
<?php if($TPL_VAR["ipinyn"]=='y'||$TPL_VAR["niceipinyn"]=='y'){?>
				<td class="noline">
					<label for="RnCheckType_ipin" style="width:150px;height:20px;display:inline-block;background:url('/shop/data/skin/campingyo/img/ipin/Regist_realName_title_2.gif') no-repeat 17px 3px;">
					<input type="radio" name="RnCheckType" value="ipin" id="RnCheckType_ipin" onclick="selectRnCheckType()">
					</label>
				</td>
				<td width="5">&nbsp;</td>
<?php }?>
<?php if($TPL_VAR["hpauthDreamyn"]=='y'){?>
				<td class="noline">
					<label for="RnCheckType_hpauthDream" style="width:150px;height:20px;display:inline-block;background:url('/shop/data/skin/campingyo/img/auth/hpauth_title_3.gif') no-repeat 17px 3px;">
					<input type="radio" name="RnCheckType" value="hpauthDream" id="RnCheckType_hpauthDream" onclick="selectRnCheckType()">
					</label>
				</td>
				<td width="5">&nbsp;</td>
<?php }?>		
			</tr>
		</table>
<?php }?>


<?php if($TPL_VAR["realnameyn"]=='y'){?>	
		<?php echo $this->define('tpl_include_file_5',"member/NiceCheck.htm")?> <?php $this->print_("tpl_include_file_5",$TPL_SCP,1);?>

<?php }?>

<?php if($TPL_VAR["ipinyn"]=='y'){?>
		<?php echo $this->define('tpl_include_file_6',"member/NiceIpin.htm")?> <?php $this->print_("tpl_include_file_6",$TPL_SCP,1);?>

<?php }else{?>
		<?php echo $this->define('tpl_include_file_7',"member/NewNiceIpin.htm")?> <?php $this->print_("tpl_include_file_7",$TPL_SCP,1);?>

<?php }?>

<?php if($TPL_VAR["hpauthDreamyn"]=='y'){?>
		<?php echo $this->define('tpl_include_file_8',"member/hpauthDream.htm")?> <?php $this->print_("tpl_include_file_8",$TPL_SCP,1);?>

<?php }?>

		<!-- 하단버튼 -->
		<div align="center" style="padding: 50px 0 20px 0" class="noline">
			<input id="form-submit" type="submit" style="display: none;"/>
			<a href="javascript:checkSubmit();"><img src="/shop/data/skin/campingyo/img/common/btn_join.gif"/></a>
			<a href="javascript:history.back();"><img src="/shop/data/skin/campingyo/img/common/btn_back.gif" border="0"/></a>
		</div>

	</form>

</div><!-- End indiv -->

<?php $this->print_("footer",$TPL_SCP,1);?>