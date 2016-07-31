<?php

include '../_header.popup.php';
include '../../lib/SocialMember/SocialMemberServiceLoader.php';

$facebookConfig = $socialMemberService->loadFacebookConfig();

$checked['useyn'][$facebookConfig['useyn']] = ' checked="checked"';
$checked['useAdvanced'][$facebookConfig['useAdvanced']] = ' checked="checked"';

?>
<script type="text/javascript">
jQuery(window).load(function(){
	resizeFrame();
});
jQuery(document).ready(function(){

	jQuery("form input[name=useyn]").click(function(){
		if (jQuery(this).val() === "y") {
			jQuery("#use-advanced-field input").removeAttr("disabled");
			jQuery("form input[name=useAdvanced]:checked").trigger("click");
		}
		else {
			jQuery("#use-advanced-field input").attr("disabled", "disabled");
			jQuery("#app-id-field input").attr("disabled", "disabled");
			jQuery("#app-secret-code-field input").attr("disabled", "disabled");
		}
		resizeFrame();
	});

	jQuery("form input[name=useAdvanced]").click(function(){
		if (jQuery(this).val() === "y") {
			jQuery("#app-id-field input").removeAttr("disabled");
			jQuery("#app-secret-code-field input").removeAttr("disabled");
		}
		else {
			jQuery("#app-id-field input").attr("disabled", "disabled");
			jQuery("#app-secret-code-field input").attr("disabled", "disabled");
		}
		resizeFrame();
	});

	jQuery("#facebook-form").submit(function(){
		if (jQuery(this).find("[name=useAdvanced]:checked").val() === "advanced") {
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
</style>
<form id="facebook-form" action="adm_member_social_login.facebook.indb.php" method="post">
	<table class="tb">
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
		<tr id="use-advanced-field">
			<th>적용방식 선택</th>
			<td class="noline">
				<input id="use-advanced-n" type="radio" name="useAdvanced" value="n" <?php echo $checked['useAdvanced']['n']; ?>/>
				<label for="use-advanced-n" style="margin-right: 10px;">간편설정</label>
				<div class="extext" style="font-size: 9pt; margin-left: 23px; margin-top: 5px;">고도몰 제공하는 기본 앱ID와 시크릿코드를 사용하여 소셜로그인 기능을 사용합니다.</div>
				<input id="use-advanced-y" type="radio" name="useAdvanced" value="y" <?php echo $checked['useAdvanced']['y']; ?>/>
				<label for="use-advanced-y">개별설정</label>
				<div class="extext" style="font-size: 9pt; margin-left: 23px; margin-top: 5px;">페이스북에 쇼핑몰을 직접 등록하고 부여 받은 앱ID와 시크릿코드를 e나무와 연동하여 독립적으로 소셜로그인 기능을 사용합니다.</div>
				<div class="extext" style="font-size: 9pt; margin-left: 23px; margin-top: 5px;"><a href="javascript:manual('<?php echo $guideUrl; ?>board/view.php?id=member&no=23')" class="facebook-regist-guide">[개별설정 시 페이스북 등록절차 안내 &gt;]</a></div>
			</td>
		</tr>
		<tr id="app-id-field">
			<th>앱(Client) ID</th>
			<td>
				<input type="text" class="lline" name="appID" value="<?php echo $facebookConfig['appID']; ?>" data-placeholder="앱(Client) ID"/>
			</td>
		</tr>
		<tr id="app-secret-code-field">
			<th>앱(Client) 시크릿 코드</th>
			<td>
				<input type="text" class="lline" name="appSecretCode" value="<?php echo $facebookConfig['appSecretCode']; ?>" data-placeholder="앱(Client) 시크릿 코드"/>
			</td>
		</tr>
	</table>
	<div class="extext" style="font-size: 9pt; margin-top: 15px;">
		※ 정상적인 기능 이용을 위해서 반드시
		<span style="text-decoration: underline;">스킨 패치를 적용</span>
		해주시기 바랍니다.
		<a href="http://www.godo.co.kr/customer_center/patch.php?sno=2036" style="font-weight: bold;" target="_blank">[바로가기]</a>
	</div>
	<div class="button"><input type="image" src="../img/btn_register.gif"/> <a href="javascript:history.back()"><img src="../img/btn_cancel.gif"/></a></div>
</form>

<ul class="admin-simple-faq">
	<li style="margin-top: 10px;">
		* “간편설정”을 통해 별도의 페이스북 개발자센터 등록 절차 없이 손쉽게 페이스북 소셜로그인 기능을 사용할 수 있습니다. 다만, 페이스북에서 제공하는 몇가지 기능에 제한이 있으므로 “간편설정＂과 “개별설정＂의 차이점을 보시고 쇼핑몰에 적절한 설정 방법을 이용해 주시기 바랍니다.<br/>
		<img src="../img/def.png" style="margin: 5px 0;"/><br/>
		-> “개별설정”으로 선택할 경우 ‘페이스북 등록절차 안내‘를 통하여 손쉬운 “개별설정“방법을 제공하고 있으니 이를 클릭하여 자세한 안내를 받으시기 바랍니다.
	</li>
	<li style="margin-top: 10px;">
		<strong>* 개별설정 용어해설</strong><br/>
		- 앱(Client) ID : 페이스북에 등록된 쇼핑몰(앱)의 고유한 ID를 의미합니다.<br/>
		- 앱(Client) 시크릿코드 : 앱 ID의 암호화된 비밀번호(코드)를 의미합니다.
	</li>
</ul>

<?php include '../_footer.popup.php'; ?>