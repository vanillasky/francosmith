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
				alert(this.appID.getAttribute("data-placeholder") + "�� �Է��� �ּ���");
				this.appID.focus();
				return false;
			}
			if (this.appSecretCode.value.trim().length < 1) {
				alert(this.appSecretCode.getAttribute("data-placeholder") + "�� �Է��� �ּ���");
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
			<th>��� ����</th>
			<td class="noline">
				<input id="useyn-y" type="radio" name="useyn" value="y" <?php echo $checked['useyn']['y']; ?>/>
				<label for="useyn-y" style="margin-right: 10px;">�����</label>
				<input id="useyn-n" type="radio" name="useyn" value="n" <?php echo $checked['useyn']['n']; ?>/>
				<label for="useyn-n">������</label>
			</td>
		</tr>
		<tr id="use-advanced-field">
			<th>������ ����</th>
			<td class="noline">
				<input id="use-advanced-n" type="radio" name="useAdvanced" value="n" <?php echo $checked['useAdvanced']['n']; ?>/>
				<label for="use-advanced-n" style="margin-right: 10px;">������</label>
				<div class="extext" style="font-size: 9pt; margin-left: 23px; margin-top: 5px;">���� �����ϴ� �⺻ ��ID�� ��ũ���ڵ带 ����Ͽ� �Ҽȷα��� ����� ����մϴ�.</div>
				<input id="use-advanced-y" type="radio" name="useAdvanced" value="y" <?php echo $checked['useAdvanced']['y']; ?>/>
				<label for="use-advanced-y">��������</label>
				<div class="extext" style="font-size: 9pt; margin-left: 23px; margin-top: 5px;">���̽��Ͽ� ���θ��� ���� ����ϰ� �ο� ���� ��ID�� ��ũ���ڵ带 e������ �����Ͽ� ���������� �Ҽȷα��� ����� ����մϴ�.</div>
				<div class="extext" style="font-size: 9pt; margin-left: 23px; margin-top: 5px;"><a href="javascript:manual('<?php echo $guideUrl; ?>board/view.php?id=member&no=23')" class="facebook-regist-guide">[�������� �� ���̽��� ������� �ȳ� &gt;]</a></div>
			</td>
		</tr>
		<tr id="app-id-field">
			<th>��(Client) ID</th>
			<td>
				<input type="text" class="lline" name="appID" value="<?php echo $facebookConfig['appID']; ?>" data-placeholder="��(Client) ID"/>
			</td>
		</tr>
		<tr id="app-secret-code-field">
			<th>��(Client) ��ũ�� �ڵ�</th>
			<td>
				<input type="text" class="lline" name="appSecretCode" value="<?php echo $facebookConfig['appSecretCode']; ?>" data-placeholder="��(Client) ��ũ�� �ڵ�"/>
			</td>
		</tr>
	</table>
	<div class="extext" style="font-size: 9pt; margin-top: 15px;">
		�� �������� ��� �̿��� ���ؼ� �ݵ��
		<span style="text-decoration: underline;">��Ų ��ġ�� ����</span>
		���ֽñ� �ٶ��ϴ�.
		<a href="http://www.godo.co.kr/customer_center/patch.php?sno=2036" style="font-weight: bold;" target="_blank">[�ٷΰ���]</a>
	</div>
	<div class="button"><input type="image" src="../img/btn_register.gif"/> <a href="javascript:history.back()"><img src="../img/btn_cancel.gif"/></a></div>
</form>

<ul class="admin-simple-faq">
	<li style="margin-top: 10px;">
		* ������������ ���� ������ ���̽��� �����ڼ��� ��� ���� ���� �ս��� ���̽��� �Ҽȷα��� ����� ����� �� �ֽ��ϴ�. �ٸ�, ���̽��Ͽ��� �����ϴ� ��� ��ɿ� ������ �����Ƿ� ������������ �������������� �������� ���ð� ���θ��� ������ ���� ����� �̿��� �ֽñ� �ٶ��ϴ�.<br/>
		<img src="../img/def.png" style="margin: 5px 0;"/><br/>
		-> ���������������� ������ ��� �����̽��� ������� �ȳ����� ���Ͽ� �ս��� ����������������� �����ϰ� ������ �̸� Ŭ���Ͽ� �ڼ��� �ȳ��� �����ñ� �ٶ��ϴ�.
	</li>
	<li style="margin-top: 10px;">
		<strong>* �������� ����ؼ�</strong><br/>
		- ��(Client) ID : ���̽��Ͽ� ��ϵ� ���θ�(��)�� ������ ID�� �ǹ��մϴ�.<br/>
		- ��(Client) ��ũ���ڵ� : �� ID�� ��ȣȭ�� ��й�ȣ(�ڵ�)�� �ǹ��մϴ�.
	</li>
</ul>

<?php include '../_footer.popup.php'; ?>