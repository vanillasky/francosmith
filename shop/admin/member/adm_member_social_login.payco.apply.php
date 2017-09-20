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
<div class="title title_top"><font face="����" color="black"><b>������ �α��� ��� ��û</b></font><span>������ ���̵� ���񽺸� �̿��Ͻ÷��� ���� ����û�� ���ּ���.</span></div>

<form name="fm" action="adm_member_social_login.payco.indb.php" method="post" onsubmit="return chkfrm()">
<input type="hidden" name="mode" value="getServiceCode">
	<table class=tb>
	<col class=cellC><col class=cellL>
	<tr>
		<td>���θ��̸�</td>
		<td>
			<label for="serviceName" id="lbl_serviceName">��) ����</label>
			<input type="text" class="lline" name="serviceName" id="serviceName" class="serviceName" value='<?=$paycoData['serviceName']?>' onfocus="focusEvent(this, 'lbl_serviceName', 'on')" onfocusout="focusEvent(this, 'lbl_serviceName', 'out')" required />
		</td>
	</tr>
	<tr>
		<td>���θ�URL</td>
		<td>
			<label for="serviceURL" id="lbl_serviceURL" style="left:48px;">��) www.godo.co.kr</label>
			http://<input type="text" class="lline" name="serviceURL" id="serviceURL" class="serviceURL" value="<?=$url[1]?>" onfocus="focusEvent(this, 'lbl_serviceURL', 'on')" onfocusout="focusEvent(this, 'lbl_serviceURL', 'out')" required />
		</td>
	</tr>
	<tr>
		<td>��ȣ(ȸ��)��</td>
		<td>
			<label for="consumerName" id="lbl_consumerName">��) �ֽ�ȸ�� ������Ʈ</label>
			<input type="text" class="lline" name="consumerName" id="consumerName" class="consumerName" value='<?=$paycoData['consumerName']?>' onfocus="focusEvent(this, 'lbl_consumerName', 'on')" onfocusout="focusEvent(this, 'lbl_consumerName', 'out')" required />
		</td>
	</tr>
	</table>
	
	<div id="confirm-area">
		<div><?=$socialMember->getPaycoContent('terms')?></div>
	</div>
	<p class="center"><input type="checkbox" name="confirm" value="Y"> �� ���뿡 ��� �����մϴ�.</p>
	
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
		alert('���θ��̸��� ��ȣ���� Ư�����ڸ� ����� �� �����ϴ�');
		return false;
	}
	if (document.fm.confirm.checked == false) {
		alert('������ �α����� ����Ϸ��� �̿���å�� ������ �ּ���.');
		document.fm.confirm.focus();
		return false;
	}
	<?if ($paycoData['clientId']) {?>
	if (!confirm('������ �α��� ����� ���û �ϴ� ���, ���� ������ ���̵� ������ ȸ���鿡�� �������� ���� �絿�Ǹ� �ް� �˴ϴ�. ����Ͻðڽ��ϱ�?\n(�絿�� �ܿ��� ���ٸ� �������� ������ ������ ���̵�� ���θ� �̿��� �����մϴ�.)')) {
		return false;
	}
	<?}?>
}

if (document.getElementById('serviceName').value) focusEvent(document.getElementById('serviceName'), 'lbl_serviceName', 'on');
if (document.getElementById('serviceURL').value) focusEvent(document.getElementById('serviceURL'), 'lbl_serviceURL', 'on');
if (document.getElementById('consumerName').value) focusEvent(document.getElementById('consumerName'), 'lbl_consumerName', 'on');
table_design_load();
</script>