<?php

$location = '회원관리 > 페이코 아이디 로그인 설정';

include '../_header.popup.php';
include '../../lib/SocialMember/SocialMemberServiceLoader.php';

$paycoConfig = $socialMemberService->loadPaycoConfig();

$checked['useyn'][$paycoConfig['useyn']] = ' checked="checked"';
$socialMember = SocialMemberService::getMember('PAYCO');
$paycoData = $socialMember->getServiceCode();
?>
<script type="text/javascript">
jQuery(window).load(function(){
	resizeFrame();
});
jQuery(document).ready(function(){
	jQuery("#payco-form").submit(function(){
		if (this.appID.value.trim().length < 1) {
			alert(this.appID.getAttribute("data-placeholder") + "를 입력해 주세요");
			this.appID.focus();
			return false;
		}
		if (this.appSecretCode.value.trim().length < 1) {
			alert(this.appSecretCode.getAttribute("data-placeholder") + "를 입력해 주세요");
			this.appSecretCode.focus();
			return false;
		}
	});

	jQuery("form input[name=useyn]:checked").trigger("click");
});

var IntervarId;
var resizeFrame = function()
{
	var oBody = document.body;
	var oFrame = parent.document.getElementById("snsifrm");
	var i_height = Math.min(document.documentElement.scrollHeight, document.body.scrollHeight);
	i_height = i_height + (oFrame.offsetHeight-oFrame.clientHeight);
	oFrame.style.height = i_height + "px";
	oFrame.height = i_height + "px";

	if (IntervarId) clearInterval(IntervarId);
};

function parentPopupLayer(s,w,h)
{
	if (!w) w = 600;
	if (!h) h = 485;

	var pixelBorder = 3;
	var titleHeight = 12;
	w += pixelBorder * 2;
	h += pixelBorder * 2 + titleHeight;

	var bodyW = parent.window.innerWidth || parent.document.documentElement.clientWidth || parent.document.body.clientWidth;
	var bodyH = parent.window.innerHeight || parent.document.documentElement.clientHeight || parent.document.body.clientHeight;

	var posX = (bodyW - w) / 2;
	var posY = (bodyH - h) / 2;

	hiddenSelectBox('hidden');

	/*** 백그라운드 레이어 ***/
	var obj = parent.document.createElement("div");
	with (obj.style){
		position = "absolute";
		left = 0;
		top = 0;
		width = "100%";
		height = parent.document.body.scrollHeight+'px';

		backgroundColor = "#000000";
		filter = "Alpha(Opacity=80)";
		opacity = "0.5";
	}
	obj.id = "objPopupLayerBg";
	parent.document.body.appendChild(obj);

	/*** 내용프레임 레이어 ***/
	var obj = parent.document.createElement("div");
	with (obj.style){
		position = "absolute";
		left = posX + parent.document.viewport.getScrollOffsets().left +'px';
		top = posY + parent.document.viewport.getScrollOffsets().top +'px';
		width = w;
		height = h;
		backgroundColor = "#ffffff";
		border = "3px solid #000000";
	}
	obj.id = "objPopupLayer";
	parent.document.body.appendChild(obj);

	/*** 타이틀바 레이어 ***/
	var bottom = parent.document.createElement("div");
	with (bottom.style){
		position = "absolute";
		width = w - pixelBorder * 2+'px';

		height = titleHeight +'px';
		left = 0;
		top = h - titleHeight - pixelBorder * 3 +'px';
		padding = "4px 0 0 0";
		textAlign = "center";
		backgroundColor = "#000000";
		color = "#ffffff";
		font = "bold 8pt tahoma; letter-spacing:0px";

	}
	bottom.innerHTML = "<a href='javascript:closeLayer()' class='white'>X close</a>";
	obj.appendChild(bottom);

	/*** 아이프레임 ***/
	var ifrm = parent.document.createElement("iframe");
	with (ifrm.style){
		width = w - 6 +'px';
		height = h - pixelBorder * 2 - titleHeight - 3 +'px';
		//border = "3 solid #000000";
	}
	ifrm.name = 'objPopupIframe';
	ifrm.frameBorder = 0;
	obj.appendChild(ifrm);
	ifrm.src = s;

}
</script>
<style type="text/css">
table.tb {
	width: 100%;
	border-collapse: collapse;
	border-color: #e6e6e6;
	
}
table.tb th {
	width: 160px;
	text-align: left;
	color: #333333;
}
table.tb th, table.tb td {
	padding: 8px;
	border: 1px solid #e6e6e6;
}
a.facebook-regist-guide {
	color: #627dce;
	font-weight: bold;
	text-decoration: underline;
}
.payco-guide {padding:10px; border:1px solid #ccc;}
</style>

<div class="title title_top">
	페이코 아이디 로그인 설정 <span>페이코 아이디를 이용하여 쇼핑몰에 로그인할 수 있는 기능을 제공합니다.</span>
	<a href="javascript:manual('<?php echo $guideUrl; ?>board/view.php?id=member&no=28')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a>
</div>

<form id="facebook-form" action="adm_member_social_login.payco.indb.php" method="post">
<input type="hidden" name="mode" value="modifyAppID">
	<div style="border:solid 4px #dce1e1; border-collapse:collapse; margin-bottom:20px; color:#666666; padding:10px 0 10px 10px;">
		<div class="g9" style="color:#0074BA"><b>페이코 아이디 로그인이란?</b></div>
		<div style="padding-top:7px;"><?=$socialMember->getPaycoContent()?></div>
	</div>

	<?if (PaycoMember::configExist() === true) {?>
	<table class="tb" style="margin-bottom:20px;">
		<colgroup>
			<col class="cellC"/>
			<col class="cellL"/>
		</colgroup>
		<tr>
			<th>사용 여부</th>
			<td class="noline">
				<input id="useyn-y" type="radio" name="useyn" value="y" <?php echo $checked['useyn']['y']; ?>/>
				<label for="useyn-y" style="margin-right: 10px;">사용함</label>
				<input id="useyn-n" type="radio" name="useyn" value="n" <?php echo $checked['useyn']['n']; ?>/>
				<label for="useyn-n">사용안함</label>
			</td>
		</tr>
		<tr id="app-id-field">
			<th>Client ID</th>
			<td>
				<input type="text" class="lline" name="appID" value="<?php echo $paycoData['clientId']; ?>" readonly disabled data-placeholder="Client ID"/>
			</td>
		</tr>
		<tr id="app-secret-code-field">
			<th>Client 시크릿 코드</th>
			<td>
				<input type="text" class="lline" name="appSecretCode" value="<?php echo $paycoData['clientSecret']; ?>" readonly disabled data-placeholder="Client 시크릿 코드"/>
			</td>
		</tr>
		<tr>
			<th>페이코 로그인 사용 신청정보</th>
			<td>
				쇼핑몰 이름: <?php echo $paycoData['serviceName']; ?><br />
				쇼핑몰 URL: <?php echo $paycoData['serviceURL']; ?><br />
				상호(회사)명: <?php echo $paycoData['consumerName']; ?><br />

				<div class="small1 extext">신청한 정보가 다를 경우 <a onclick="parentPopupLayer('adm_member_social_login.payco.apply.php');" class="hand extext"><b>[페이코 로그인 재신청]</b></a>을 클릭하여 재신청 해주시기 바랍니다.</div>
			</td>
		</tr>
	</table>

	<div class="payco-guide">
	페이코 로그인을 사용하려면, <br />
	<b style="color:#ff0000;">2016년 04월 28일 이전 제작 무료 스킨</b>을 사용하시는 경우 <u><b>반드시 스킨패치를 적용</b></u>해야 기능 사용이 가능합니다. <a href="http://www.godo.co.kr/customer_center/patch.php?sno=2382" target="_blank" class="extext"><b>[패치 바로가기]</b></a>
	</div>

	<div class="button"><input type="image" src="../img/btn_register.gif"/> <a href="javascript:history.back()"><img src="../img/btn_cancel.gif"/></a></div>
	<?} else {?>
	<div class="button"><a onclick="parentPopupLayer('adm_member_social_login.payco.apply.php');" class="hand"><img src="../img/btn_payco_apply.png" /></a></div>
	<?}?>
</form>

<?php include '../_footer.php'; ?>