<?php /* Template_ 2.2.7 2014/07/30 21:43:01 /www/francotr3287_godo_co_kr/shop/data/skin/freemart/proc/member_social_status.htm 000003901 */ ?>
<?php if($TPL_VAR["SocialMemberEnabled"]){?>
<script type="text/javascript">
var socialMemberConnect = function(socialCode)
{
	switch (socialCode) {
		case "FACEBOOK":
			popup("<?php echo $TPL_VAR["FacebookSocialMemberConnectURL"]?>", 350, 200);
			break;
		default:
			return false;
	}
};
var socialMemberConnectCallback = function(socialCode, errorCode)
{
	var socialName;
	switch (socialCode) {
		case "FACEBOOK":
			socialName = "페이스북";
			break;
		default:
			return false;
	}
	if (errorCode === "ERR_ALREADY_EXISTS") {
		alert("이미 다른계정에 연결된 " + socialName + "계정입니다.");
		return false;
	}
	else {
		alert(socialName + "계정이 정상적으로 연결 되었습니다.");
		location.reload();
	}
};
var socialMemberDisconnect = function(socialCode)
{
	var socialName;
	switch (socialCode) {
		case "FACEBOOK":
			socialName = "페이스북";
			break;
		default:
			return false;
	}
	if (confirm(socialName + " 계정 연결을 해제하시겠습니까?")) {
		ifrmHidden.location.href = "./social_member.php?MODE=disconnect&SOCIAL_CODE=" + socialCode;
	}
};
var socialMemberDisconnectCallback = function(result)
{
	if (result === "SUCCESS") {
		alert("계정이 정상적으로 연결해제 되었습니다.");
		location.reload();
	}
	else if (result === "ERR_PASSWORD_NOT_EXISTS") {
		alert("SNS계정을 해제할 수 없습니다.\r\nSNS계정을 모두 해제하시려면 비밀번호를 등록하여 주시기 바랍니다.");
	}
	else {
		alert("연결해제에 실패하였습니다.\r\n고객센터로 문의하여 주시기 바랍니다.");
	}
};
</script>
<style type="text/css">
button.btn {
	border: none;
	font-size: 0;
	cursor: pointer;
}
div.sns-connect-status-title {
	margin-top: 15px;
	background: url("/shop/data/skin/freemart/img/meminfor_sns_01.gif") no-repeat;
	width: 82px;
	height: 23px;
	font-size: 0;
}
div.outer-border {
	border: 1px solid #dedede;
}
.inner-border {
	border: 5px solid #f3f3f3;
	padding: 10px;
}
div.sns-connect-status {
	overflow: hidden;
}
div.sns-profile-image {
	float: left;
}
div.sns-profile-image img {
	vertical-align: top;
}
div.sns-connect-controll {
	float: left;
	margin-left: 10px;
}
div.connected-sns img {
	vertical-align: middle;
}
div.connected-sns span.connect-status {
	font: 11px dotum;
	color: #666666;
	margin-left: 6px;
}
div.connected-facebook button.btn-facebook-disconnect {
	margin-left: 20px;
	background: url('/shop/data/skin/freemart/img/meminfor_btn_sns_clear.gif') no-repeat;
	width: 47px;
	height: 18px;
	vertical-align: middle;
}
div.disconnected-sns button.btn-facebook-connect {
	background: url('/shop/data/skin/freemart/img/meminfor_sns_facebook.gif') no-repeat;
	width: 37px;
	height: 37px;
}
</style>
<div class="sns-connect-status-title">SNS연결정보</div>
<div class="outer-border hundred">
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr>
	<td class="inner-border">
		<div class="sns-connect-ststus">
<?php if($TPL_VAR["SocialProfileImage"]){?>
			<div class="sns-profile-image">
				<img src="<?php echo $TPL_VAR["SocialProfileImage"]?>"/>
			</div>
<?php }?>
			<div class="sns-connect-controll">
				<div class="connected-sns">
<?php if($TPL_VAR["FacebookSocialMemberConnected"]){?>
					<div class="connected-facebook">
						<img src="/shop/data/skin/freemart/img/meminfor_sns_ico_facebook.gif" alt="페이스북"/>
						<span class="connect-status">연결됨</span>
						<button onclick="socialMemberDisconnect('FACEBOOK');" class="btn btn-facebook-disconnect">연결해제</button>
					</div>
<?php }?>
				</div>
				<div class="disconnected-sns">
<?php if(!$TPL_VAR["FacebookSocialMemberConnected"]&&$TPL_VAR["FacebookSocialMemberEnabled"]){?>
					<button onclick="socialMemberConnect('FACEBOOK');" class="btn btn-facebook-connect">페이스북 연결</button>
<?php }?>
				</div>
			</div>
		</div>
	</td>
</tr>
</table>
</div><p/>
<?php }?>