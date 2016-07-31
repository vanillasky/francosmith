<script type="text/javascript">
if (window.jQuery) {
	jQuery(document).ready(function(){
		jQuery("button.admin-account-secure-guide-toggle").click(function(){
			jQuery(".admin-account-secure-guide").toggleClass("off");
		});;
	});
}
else {
	document.observe("dom:loaded", function(){
		$$("button.admin-account-secure-guide-toggle")[0].observe("click", function(){
			$$(".admin-account-secure-guide")[0].toggleClassName("off");
		});
	});
}
</script>
<style type="text/css">
.admin-account-secure-guide {
	margin-top: 20px;
	margin-bottom: 20px;
}
.admin-account-secure-guide.off button.admin-account-secure-guide-toggle {
	background-image: url(../img/btn_adminSecurity_off.gif);
}
button.admin-account-secure-guide-toggle {
	background-image: url(../img/btn_adminSecurity_on.gif);
	background-repeat: no-repeat;
	background-position: left top;
	background-size: 100% 100%;
	border: none;
	font-size: 0;
	text-indent: -1000px;
	display: block;
	width: 195px;
	height: 30px;
	cursor: pointer;
}
.admin-account-secure-guide-box {
	border: solid #cccccc 1px;
	padding: 23px;
	font-family: dotum;
	font-size: 12px;
	margin-top: 4px;
	line-height: 16px;
}
.admin-account-secure-guide.off .admin-account-secure-guide-box {
	display: none;
}
.admin-account-secure-guide-list {
	padding: 0 0 0 23px;
	margin: 0;
}
.admin-account-secure-guide-list li {
	list-style: decimal;
	padding: 3px 0;
}
.admin-account-secure-guide-list li a {
	font-weight: bold;
}
.admin-account-secure-subaccount-guide {
	border: #cccccc double 3px;
	margin-top: 20px;
	font-family: dotum;
	font-size: 11px;
	color: #627dce;
	line-height: 18px;
}
.admin-account-secure-subaccount-guide .subject {
	padding-left: 5px;
	margin: 7px 0 3px 7px;
	font-weight: bold;
	background-image: url(../img/icon_list.gif);
	background-repeat: no-repeat;
	background-position: left center;
}
.admin-account-secure-subaccount-guide-list {
	padding: 0 0 0 15px;
	margin: 0;
}
.admin-account-secure-subaccount-guide-list li {
	list-style: none;
	padding: 0;
}
.admin-account-secure-subaccount-guide-list li a {
	color: inherit;
	font-weight: bold;
}
.admin-account-secure-subaccount-guide-list li a:hover {
	color: #555555;
}
</style>
<div class="admin-account-secure-guide<?php echo $adminAccountSecureGuideInitStatus ? ' '.$adminAccountSecureGuideInitStatus : ''; ?>">
	<button class="admin-account-secure-guide-toggle">������ ���� ���� ���� ��Ģ</button>
	<div class="admin-account-secure-guide-box">
		<ol class="admin-account-secure-guide-list">
			<li>"������"������ ���θ� ������(�Ǵ� �ְ�����) �ܿ� ���� ���� ���� �ʽ��ϴ�.</li>
			<li>�����ڰ� ������ �ʿ��� ��� �ΰ����� ���� ������ ���̵� �����Ͽ� ���� ������ �ο� �մϴ�.</li>
			<li>"������"���� �Ǵ� �ϳ��� �ΰ����� ������ �������� ���ÿ� ������� �ʽ��ϴ�.</li>
			<li>
				�ΰ������� ������ ���� �޴� �� ���� ������ �����Ͽ� ��� �մϴ�.<br/>
				(ex,ȸ��/�ֹ�/��ǰ �� ������ �ʿ��� �޴�)
			</li>
			<li>�ʿ信 ���� <a href="../basic/adm_basic_login_cert.php" target="_blank">[�����ں��� ����]</a> <a href="../basic/adm_basic_ip_access.php" target="_blank">[������IP�������� ����]</a> ����� �Բ� ��� �մϴ�.</li>
		</ol>
		<div class="admin-account-secure-subaccount-guide">
			<div class="subject">�ΰ����� ���� ���� �� ���� ��� (�������)</div>
			<ul class="admin-account-secure-subaccount-guide-list">
				<li>- <a href="../basic/adminGroup.php" target="_blank">[�⺻���� > �����ڱ׷���Ѽ��� > ������ �׷��߰�]</a> Ŭ��</li>
				<li>- �����ڱ׷��߰� �˾� > �����ڸ� ���� > �׷췹�� 80~99 �� ���� > �������ѿ��� �޴� �� ���� �ο� > ����</li>
				<li>- �ΰ����� ���θ� �Ϲ� ȸ������ ����</li>
				<li>- <a href="../basic/adminGroup.php" target="_blank">[�⺻���� > �����ڱ׷���Ѽ���]</a> �Ǵ� <a href="../member/list.php" target="_blank">[ȸ������ > ȸ������Ʈ > ȸ�� ����]</a>�� ȸ������Ʈ���� ȸ�� ���� ����</li>
				<li>- ȸ������ > �׷� �׸� ������ �ΰ����� ���� > ����</li>
			</ul>
		</div>
	</div>
</div>