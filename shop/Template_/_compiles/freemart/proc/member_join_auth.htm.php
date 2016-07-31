<?php /* Template_ 2.2.7 2016/04/12 05:34:02 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/proc/member_join_auth.htm 000009687 */ ?>
<script type="text/javascript">
function checkSubmit() {
	var oForm = document.getElementById("form");

	var rdo_jumin		= document.getElementById("RnCheckType_jumin");
	var rdo_ipin		= document.getElementById("RnCheckType_ipin");
	var rdo_hpauthDream = document.getElementById("RnCheckType_hpauthDream");
	
	if (rdo_ipin && rdo_ipin.checked)  {
		goIDCheckIpin();
	} else if (rdo_hpauthDream && rdo_hpauthDream.checked) {
		gohpauthDream();
	} else if (rdo_jumin && rdo_jumin.checked) {
		if(chkagreement(oForm)) {
			if (chkForm2(oForm)) {
				oForm.submit();
			}
		}
	}else {
		if (chkagreement(oForm)) oForm.submit();

	}
}
</script>

<!-- 상단이미지 || 현재위치 -->
<div class="page_title_div">
	<div class="page_title">회원가입</div>
	<div class="page_path"><a href="/shop/">HOME</a> &gt; 회원가입 &gt; <span class='bold'>이용약관</span></div>
</div>
<div class="page_title_line"></div>

<div class="indiv"><!-- Start indiv -->

<form id=form name=frmAgree method=post action="<?php echo url("member/indb.php")?>&" target="ifrmHidden" onSubmit="return chkForm2(this)">
<input type=hidden name=mode value="chkRealName">
<input type=hidden name=rncheck value="none">
<input type=hidden name=nice_nm value="">
<input type=hidden name=pakey value="">
<input type=hidden name=birthday value="">
<input type=hidden name=mobile value="">
<input type=hidden name=sex value="">
<input type=hidden name=dupeinfo value="">
<input type=hidden name=foreigner value="">
<input type=hidden name=phone value="">
<input type=hidden name=type>

<!-- 네이버체크아웃(회원연동) -->
<?php echo $TPL_VAR["naverCheckout_oneclickStep"]?>


<!-- 이용약관 -->
<div><img src="/shop/data/skin/freemart/img/common/join_txt_01.gif" border=0></div>
<div style="background:#F1F1F1; text-align:center; padding:10px 10px">
	<textarea style="width:100%; height:190px; padding:10; background:#FFFFFF" class=scroll readonly><?php echo $TPL_VAR["termsAgreement"]?></textarea>
	<div align=center class=noline style="height:30;margin-top:10px;" >
	<input type="radio" name="agree" value="y"> 동의합니다 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<input type="radio" name="agree" value="n"> 동의하지 않습니다
	</div>
</div>
<p>

<!-- 개인정보취급방침 -->
<div style="padding-left:7"><font color=f7443f><b>개인정보취급방침</b></font></div>
<div style="padding-top:10; background:#F1F1F1;  text-align:center;">
<div align="left" style="height:26;padding:3px 0 0 10px;">
<b>● 개인정보보호를 위한 이용자 동의사항</b> (자세한 내용은 “<a href="<?php echo url("service/private.php")?>&">개인정보취급방침</a>”을 확인하시기 바랍니다)
</div>
<div id="boxScroll" class="scroll">
<?php echo $TPL_VAR["termsPolicyCollection2"]?>

</div>
<div align=center class=noline style="height:30;margin-top:10px; padding-bottom:10px" >
<input type="radio" name="private1" value="y"> 동의합니다 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" name="private1" value="n"> 동의하지 않습니다
</div>
</div>
<p>
<?php if($GLOBALS["cfg"]['private2YN']=='Y'){?>
<div style="padding-top:10; background:#F1F1F1;  text-align:center;">
<div align="left" style="height:26;padding:3px 0 0 10px;">
<b>● 개인정보 제3자 제공 관련</b>
</div>
<div id="boxScroll" class="scroll">
<?php echo $TPL_VAR["termsThirdPerson"]?>

</div>
<div align=center class=noline style="height:30;margin-top:10px;" >
<input type="radio" name="private2" value="y" required fld_esssential label="개인정보 제3자 제공 관련" msgR="동의 여부를 체크해주세요."> 동의합니다 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" name="private2" value="n" required fld_esssential label="개인정보 제3자 제공 관련" msgR="동의 여부를 체크해주세요."> 동의하지 않습니다
</div>
</div>
<p>
<?php }?>
<?php if($GLOBALS["cfg"]['private3YN']=='Y'){?>
<div style="padding-top:10; background:#F1F1F1;  text-align:center;">
<div align="left" style="height:26;padding:3px 0 0 10px;">
<b>● 개인정보취급 위탁 관련</b>
</div>
<div id="boxScroll" class="scroll">
<?php echo $TPL_VAR["termsEntrust"]?>

</div>
<div align=center class=noline style="height:30;margin-top:10px;" >
<input type="radio" name="private3" value="y" required fld_esssential label="개인정보취급 위탁 관련" msgR="동의 여부를 체크해주세요."> 동의합니다 &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<input type="radio" name="private3" value="n" required fld_esssential label="개인정보취급 위탁 관련" msgR="동의 여부를 체크해주세요."> 동의하지 않습니다
</div>
</div>
<?php }?>

<!-- 본인확인 인증수단 -->
<?php if($TPL_VAR["realnameyn"]=='y'||$TPL_VAR["ipinyn"]=='y'||$TPL_VAR["niceipinyn"]=='y'||$TPL_VAR["hpauthDreamyn"]=='y'){?>
<div style="padding-left:7"><font color=f7443f><b>본인확인 인증 수단</b></font></div>
<table height="35">
	<tr>
<?php if($TPL_VAR["realnameyn"]=='y'){?>	
		<td class="noline">
			<label for="RnCheckType_jumin" style="width:130px;height:20px;display:inline-block;background:url('/shop/data/skin/freemart/img/ipin/Regist_realName_title_1.gif') no-repeat 17px 3px;">
			<input type="radio" name="RnCheckType" value="jumin" id="RnCheckType_jumin" onclick="selectRnCheckType()">
			</label>
		</td>
		<td width="5">&nbsp;</td>				
<?php }?>
<?php if($TPL_VAR["ipinyn"]=='y'||$TPL_VAR["niceipinyn"]=='y'){?>
		<td class="noline">
			<label for="RnCheckType_ipin" style="width:150px;height:20px;display:inline-block;background:url('/shop/data/skin/freemart/img/ipin/Regist_realName_title_2.gif') no-repeat 17px 3px;">
			<input type="radio" name="RnCheckType" value="ipin" id="RnCheckType_ipin" onclick="selectRnCheckType()">
			</label>
		</td>
		<td width="5">&nbsp;</td>
<?php }?>
<?php if($TPL_VAR["hpauthDreamyn"]=='y'){?>
		<td class="noline">
			<label for="RnCheckType_hpauthDream" style="width:150px;height:20px;display:inline-block;background:url('/shop/data/skin/freemart/img/auth/hpauth_title_3.gif') no-repeat 17px 3px;">
			<input type="radio" name="RnCheckType" value="hpauthDream" id="RnCheckType_hpauthDream" onclick="selectRnCheckType()">
			</label>
		</td>
		<td width="5">&nbsp;</td>
<?php }?>		
</tr>
</table>
<?php }?>


<?php if($TPL_VAR["realnameyn"]=='y'){?>	
<?php echo $this->define('tpl_include_file_1',"member/NiceCheck.htm")?> <?php $this->print_("tpl_include_file_1",$TPL_SCP,1);?>

<?php }?>

<?php if($TPL_VAR["ipinyn"]=='y'){?>
<?php echo $this->define('tpl_include_file_2',"member/NiceIpin.htm")?> <?php $this->print_("tpl_include_file_2",$TPL_SCP,1);?>

<?php }else{?>
<?php echo $this->define('tpl_include_file_3',"member/NewNiceIpin.htm")?> <?php $this->print_("tpl_include_file_3",$TPL_SCP,1);?>

<?php }?>

<?php if($TPL_VAR["hpauthDreamyn"]=='y'){?>
<?php echo $this->define('tpl_include_file_4',"member/hpauthDream.htm")?> <?php $this->print_("tpl_include_file_4",$TPL_SCP,1);?>

<?php }?>

<!-- 하단버튼 -->
<div align=center style="padding:50px 0 20px 0" class=noline>
	<button class="button-red button-big" onclick="checkSubmit();return false;">회원가입</button>
	<button class="button-dark button-big" onclick="history.back()">이전으로</button>
</div>

</form>

</div><!-- End indiv -->

<script type="text/javascript">
function defaultRnCheckType() {
	var authtype = document.getElementsByName("RnCheckType");

	if (authtype.item(0) != null) {
		var div_jumin		= document.getElementById("div_RnCheck_jumin");
		var div_ipin		= document.getElementById("div_RnCheck_ipin");
		var div_hpauthDream = document.getElementById("div_RnCheck_hpauthDream");

		if (authtype.item(0).value == 'jumin')
		{
			div_jumin.style.display='';
		} else if(authtype.item(0).value == 'ipin') {
			div_ipin.style.display='';
		} else if(authtype.item(0).value == 'hpauthDream') {
			div_hpauthDream.style.display='';
		}
		authtype.item(0).checked = true;
	}	
}

function selectRnCheckType(){

	var div_jumin		= document.getElementById("div_RnCheck_jumin");
	var div_ipin		= document.getElementById("div_RnCheck_ipin");
	var div_hpauthDream = document.getElementById("div_RnCheck_hpauthDream");

	var rdo_jumin		= document.getElementById("RnCheckType_jumin");
	var rdo_ipin		= document.getElementById("RnCheckType_ipin");
	var rdo_hpauthDream = document.getElementById("RnCheckType_hpauthDream");

	if(rdo_jumin && rdo_jumin.checked == true){
		if (div_jumin != null) { div_jumin.style.display=''; }
		if (div_ipin != null) { div_ipin.style.display='none'; }
		if (div_hpauthDream != null) { div_hpauthDream.style.display='none'; }
	}
	if(rdo_ipin && rdo_ipin.checked == true){
		if (div_jumin != null)	{ div_jumin.style.display='none'; }
		if (div_ipin != null)	{ div_ipin.style.display=''; }
		if (div_hpauthDream != null) { div_hpauthDream.style.display='none'; }
	}
	if(rdo_hpauthDream && rdo_hpauthDream.checked == true){
		if (div_jumin != null)	{ div_jumin.style.display='none'; }
		if (div_ipin != null)	{ div_ipin.style.display='none'; }
		if (div_hpauthDream != null) { div_hpauthDream.style.display=''; }
	}
}

function chkForm2(fm)
{
	if (typeof(goIDCheck) != "undefined"){
		if (goIDCheck(fm) === false) return false;
	}

	return chkForm(fm);
}

function chkagreement(fm){
	if (chkRadioSelect(fm,'agree','y','[회원가입 이용약관]에 동의를 하셔야 회원가입이 가능합니다.') === false) return false;
	if (chkRadioSelect(fm,'private1','y','[개인정보보호를 위한 이용자 동의사항]에 동의를 하셔야 회원가입이 가능합니다.') === false) return false;

	return true;
}
</script>
<script>defaultRnCheckType();</script>