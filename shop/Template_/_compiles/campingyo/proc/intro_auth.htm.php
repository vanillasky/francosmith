<?php /* Template_ 2.2.7 2014/03/05 23:19:40 /www/francotr3287_godo_co_kr/shop/data/skin/campingyo/proc/intro_auth.htm 000004532 */ ?>
<script language="javascript">
<!--
function goIDCheckIpin(){
<?php if($TPL_VAR["ipinyn"]=='y'){?>
		var popupWindow = window.open( "../member/ipin/IPINCheckRequest.php?callType=adultcheck", "popupCertKey", "top=100, left=200, status=0, width=417, height=490" );
<?php }elseif($TPL_VAR["niceipinyn"]=='y'){?>
		var popupWindow = window.open( "", "popupCertKey", "width=450, height=550, top=100, left=100, fullscreen=no, menubar=no, status=no, toolbar=no, titlebar=yes, location=no, scrollbar=no" );
		ifrmRnCheck.location.href="<?php echo url("member/ipin/IPINMain.php?")?>&callType=adultcheck";
<?php }?>
	return;
}

function frmChk(){
	var frm = document.form1;

	if(frm.name.value ==""){
		alert("이름을 입력하여 주십시오.");
		frm.name.focus();
		return false;
	}else if(frm['resno[]'][0].value ==""){
		alert("주민번호를 입력하여 주십시오.");
		frm['resno[]'][0].focus();
		return false;
	}else if(frm['resno[]'][1].value ==""){
		alert("주민번호를 입력하여 주십시오.");
		frm['resno[]'][1].focus();
		return false;
	}

	/*
		strForeigner : 1(내국인), 2(외국인)
		strRsn : 10(회원가입), 20(기존회원확인), 30(성인인증), 40(비회원확인), 90(기타사유)
	*/
	var strForeigner = '1';
	var strRsn = '30';
	var strNm = frm['name'].value;
	var strNo = frm['resno[]'][0].value + frm['resno[]'][1].value;

	frm.SendInfo.value = makeSendInfo( frm.name.value, strNo, strRsn, strForeigner );

	return true;
}

function rnWayChk(){
	var ryn = document.getElementById("realname");
	var ayn = document.getElementById("auth");

	var realname	= document.getElementById("realnameyn");

	if(realname && realname.checked == true) {
		ryn.style.display = "block";
		ayn.style.display = "none";
	} else {
		ryn.style.display = "none";
		ayn.style.display = "block";
	} 
}

function gohpauthDream(){ //휴대폰본인인증
	var protocol = location.protocol;
	var callbackUrl = "<?php echo ProtocolPortDomain()?><?php echo $GLOBALS["cfg"]["rootDir"]?>/member/hpauthDream/hpauthDream_Result.php";
	ifrmHpauth.location.href=protocol+"//hpauthdream.godo.co.kr/module/NEW_hpauthDream_Main.php?callType=adultcheck&shopUrl="+callbackUrl+"&cpid=<?php echo $TPL_VAR["hpauthDreamcpid"]?>";
	return;
}
//-->
</script>

<?php if($TPL_VAR["realnameyn"]=='y'||$TPL_VAR["ipinyn"]=='y'||$TPL_VAR["niceipinyn"]=='y'||$TPL_VAR["hpauthDreamyn"]=='y'){?>
<form method=post action="<?php echo $TPL_VAR["loginActionUrl"]?>" id="form1" name="form1" onsubmit="return frmChk();">
<div class="form">	

		<h4>인증수단선택</h4>	
<?php if($TPL_VAR["realnameyn"]=='y'){?>
		<input type="checkbox" name="rnWay" id="realnameyn" value="realnameyn" onclick="rnWayChk();" />실명확인			
<?php }?>


	
		<input type="hidden" name="returnUrl" value="<?php echo $GLOBALS["returnUrl"]?>">
		<input type="hidden" name="mode" value="adult_guest">
		<input type="hidden" name="SendInfo" value="">					


		<!-- 실명확인 -->
		<div id="realname" style="display:none;">
			<table>
			<tr>
				<th>이름</th>
				<td><input type=text class="fld" name=name size=20 tabindex=1></td>
				<td rowspan=2 class=noline><input type=image src="/shop/data/skin/campingyo/img/btn_ok.gif" tabindex=4></td>
			</tr>
			<tr>
				<th>주민번호</th>
				<td>
				<input type=text name=resno[] maxlength=6 size=6 required label="주민등록번호" onkeyup="if (this.value.length==6) this.nextSibling.nextSibling.focus()" onkeydown="onlynumber()"  class="fld"> -
				<input type=password name=resno[] maxlength=7 size=10 required label="주민등록번호" onkeydown="onlynumber()" class="fld">
				</td>
			</tr>
			</table>
		</div>

		<!-- 인증수단 -->
		<div id="auth">
<?php if($TPL_VAR["ipinyn"]=='y'||$TPL_VAR["niceipinyn"]=='y'){?>
			<img src="/shop/data/skin/campingyo/img/auth/bt_26_ipin.gif" onclick="goIDCheckIpin();" style="margin:8px;cursor:pointer" />
<?php }?>
<?php if($TPL_VAR["hpauthDreamyn"]=='y'){?>
			<img src="/shop/data/skin/campingyo/img/auth/bt_26_hp.gif" alt="휴대폰인증" onclick="gohpauthDream();" style="margin:8px;cursor:pointer" />
<?php }?>
		</div>
	
</div>
</form>
<?php }else{?>
<style>div.body div.forms-wrap {width:330px;}</style>		
<?php }?>

<iframe id="ifrmRnCheck" name="ifrmRnCheck" style="width:500px;height:500px;display:none;"></iframe>
<iframe id="ifrmHpauth" name="ifrmHpauth" style="width:300px;height:200px;display:none;"></iframe>