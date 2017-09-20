<?
include "../_header.popup.php";

$socialMember = SocialMemberService::getMember('PAYCO');
$paycoData = $socialMember->getServiceCode();

$url = explode('//',$paycoData['serviceURL']);
?>
<style>
.tb td {position:relative;}
label {
	position:absolute;
	left:15px;
	top:8px;
	color:#cfcfcf;
	cursor:text;
	font-size:16px;
}
#confirm-area {
	width:100%;
	height:160px;
	margin:10px 0;
	border:1px solid #b8d6f0;
	overflow-y:scroll;
}
#confirm-area div {
	width:98%;
	margin:5px auto 0;
}
#confirm-area div ol {
	margin:0; padding:0 0 0 20px
}
#confirm-area div ol li {
	padding:0 0 10px 0;
}
</style>
<div class="title title_top"><font face="굴림" color="black"><b>페이코 로그인 사용 신청</b></font><span>페이코 아이디 서비스를 이용하시려면 먼저 사용신청을 해주세요.</span></div>

<form name="fm" action="adm_member_social_login.payco.indb.php" method="post" onsubmit="return chkfrm()">
<input type="hidden" name="mode" value="getServiceCode">
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>쇼핑몰이름</td>
		<td>
			<label for="serviceName" id="lbl_serviceName">예) 고도몰</label>
			<input type="text" class="lline" name="serviceName" id="serviceName" class="serviceName" value='<?=$paycoData['serviceName']?>' onfocus="focusEvent(this, 'lbl_serviceName', 'on')" onfocusout="focusEvent(this, 'lbl_serviceName', 'out')" required />
		</td>
	</tr>
	<tr>
		<td>쇼핑몰URL</td>
		<td>
			<label for="serviceURL" id="lbl_serviceURL" style="left:48px;">예) www.godo.co.kr</label>
			http://<input type="text" class="lline" name="serviceURL" id="serviceURL" class="serviceURL" value="<?=$url[1]?>" onfocus="focusEvent(this, 'lbl_serviceURL', 'on')" onfocusout="focusEvent(this, 'lbl_serviceURL', 'out')" required />
		</td>
	</tr>
	<tr>
		<td>상호(회사)명</td>
		<td>
			<label for="consumerName" id="lbl_consumerName">예) 주식회사 고도소프트</label>
			<input type="text" class="lline" name="consumerName" id="consumerName" class="consumerName" value='<?=$paycoData['consumerName']?>' onfocus="focusEvent(this, 'lbl_consumerName', 'on')" onfocusout="focusEvent(this, 'lbl_consumerName', 'out')" required />
		</td>
	</tr>
	</table>
	
	<div id="confirm-area">
		<div><?=$socialMember->getPaycoContent('terms')?></div>
	</div>
	<p class="center"><input type="checkbox" name="confirm" value="Y"> 위 내용에 모두 동의합니다.</p>
	
	<div class="button">
		<input type="image" src="../img/btn_payco_apply_ok.png" />
		<a onclick="parent.closeLayer()" class="hand"><img src="../img/btn__cancel.gif" /></a>
	</div>
</form>
<script type="text/javascript">
function focusEvent(obj, id, opt) {
	if(opt == 'on') {
		document.getElementById(id).style.display='none';
	} else {
		if(obj.value != '') {
			document.getElementById(id).style.display='none';
		} else {
			document.getElementById(id).style.display='block';
		}
	}
}

function chkfrm() {
	var pattern = /[';<>]/gi;

	if (pattern.test(document.fm.serviceName.value) === true || pattern.test(document.fm.consumerName.value) === true) {
		alert('쇼핑몰이름과 상호명에는 특수문자를 사용할 수 없습니다');
		return false;
	}
	if (document.fm.confirm.checked == false) {
		alert('페이코 로그인을 사용하려면 이용정책에 동의해 주세요.');
		document.fm.confirm.focus();
		return false;
	}
	<?if ($paycoData['clientId']) {?>
	if (!confirm('페이코 로그인 사용을 재신청 하는 경우, 기존 페이코 아이디가 연동된 회원들에게 개인정보 제공 재동의를 받게 됩니다. 계속하시겠습니까?\n(재동의 외에는 별다른 절차없이 연동된 페이코 아이디로 쇼핑몰 이용이 가능합니다.)')) {
		return false;
	}
	<?}?>
}

if (document.getElementById('serviceName').value) focusEvent(document.getElementById('serviceName'), 'lbl_serviceName', 'on');
if (document.getElementById('serviceURL').value) focusEvent(document.getElementById('serviceURL'), 'lbl_serviceURL', 'on');
if (document.getElementById('consumerName').value) focusEvent(document.getElementById('consumerName'), 'lbl_consumerName', 'on');
table_design_load();
</script>