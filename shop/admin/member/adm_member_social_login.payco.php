<?php

$location = 'ȸ������ > ������ ���̵� �α��� ����';

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
			alert(this.appID.getAttribute("data-placeholder") + "�� �Է��� �ּ���");
			this.appID.focus();
			return false;
		}
		if (this.appSecretCode.value.trim().length < 1) {
			alert(this.appSecretCode.getAttribute("data-placeholder") + "�� �Է��� �ּ���");
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

	/*** ��׶��� ���̾� ***/
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

	/*** ���������� ���̾� ***/
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

	/*** Ÿ��Ʋ�� ���̾� ***/
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

	/*** ���������� ***/
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
	������ ���̵� �α��� ���� <span>������ ���̵� �̿��Ͽ� ���θ��� �α����� �� �ִ� ����� �����մϴ�.</span>
	<a href="javascript:manual('<?php echo $guideUrl; ?>board/view.php?id=member&no=28')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a>
</div>

<form id="facebook-form" action="adm_member_social_login.payco.indb.php" method="post">
<input type="hidden" name="mode" value="modifyAppID">
	<div style="border:solid 4px #dce1e1; border-collapse:collapse; margin-bottom:20px; color:#666666; padding:10px 0 10px 10px;">
		<div class="g9" style="color:#0074BA"><b>������ ���̵� �α����̶�?</b></div>
		<div style="padding-top:7px;"><?=$socialMember->getPaycoContent()?></div>
	</div>

	<?if (PaycoMember::configExist() === true) {?>
	<table class="tb" style="margin-bottom:20px;">
		<colgroup>
			<col class="cellC"/>
			<col class="cellL"/>
		</colgroup>
		<tr>
			<th>��� ����</th>
			<td class="noline">
				<input id="useyn-y" type="radio" name="useyn" value="y" <?php echo $checked['useyn']['y']; ?>/>
				<label for="useyn-y" style="margin-right: 10px;">�����</label>
				<input id="useyn-n" type="radio" name="useyn" value="n" <?php echo $checked['useyn']['n']; ?>/>
				<label for="useyn-n">������</label>
			</td>
		</tr>
		<tr id="app-id-field">
			<th>Client ID</th>
			<td>
				<input type="text" class="lline" name="appID" value="<?php echo $paycoData['clientId']; ?>" readonly disabled data-placeholder="Client ID"/>
			</td>
		</tr>
		<tr id="app-secret-code-field">
			<th>Client ��ũ�� �ڵ�</th>
			<td>
				<input type="text" class="lline" name="appSecretCode" value="<?php echo $paycoData['clientSecret']; ?>" readonly disabled data-placeholder="Client ��ũ�� �ڵ�"/>
			</td>
		</tr>
		<tr>
			<th>������ �α��� ��� ��û����</th>
			<td>
				���θ� �̸�: <?php echo $paycoData['serviceName']; ?><br />
				���θ� URL: <?php echo $paycoData['serviceURL']; ?><br />
				��ȣ(ȸ��)��: <?php echo $paycoData['consumerName']; ?><br />

				<div class="small1 extext">��û�� ������ �ٸ� ��� <a onclick="parentPopupLayer('adm_member_social_login.payco.apply.php');" class="hand extext"><b>[������ �α��� ���û]</b></a>�� Ŭ���Ͽ� ���û ���ֽñ� �ٶ��ϴ�.</div>
			</td>
		</tr>
	</table>

	<div class="payco-guide">
	������ �α����� ����Ϸ���, <br />
	<b style="color:#ff0000;">2016�� 04�� 28�� ���� ���� ���� ��Ų</b>�� ����Ͻô� ��� <u><b>�ݵ�� ��Ų��ġ�� ����</b></u>�ؾ� ��� ����� �����մϴ�. <a href="http://www.godo.co.kr/customer_center/patch.php?sno=2382" target="_blank" class="extext"><b>[��ġ �ٷΰ���]</b></a>
	</div>

	<div class="button"><input type="image" src="../img/btn_register.gif"/> <a href="javascript:history.back()"><img src="../img/btn_cancel.gif"/></a></div>
	<?} else {?>
	<div class="button"><a onclick="parentPopupLayer('adm_member_social_login.payco.apply.php');" class="hand"><img src="../img/btn_payco_apply.png" /></a></div>
	<?}?>
</form>

<?php include '../_footer.php'; ?>