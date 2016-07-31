<?

include '../_header.popup.php';

$dreamsecurity = Core::loader('Dreamsecurity');
$hpauth = Core::loader('Hpauth');

$hpauthConfig = $hpauth->loadConfig();
$hpauthDreamCfg = $hpauth->loadServiceConfig('dream');
$currentHpauthConfig = $hpauth->loadCurrentServiceConfig();
$dreamsecurityPrefix = $dreamsecurity->lookupPrefix();

if (isset($hpauthDreamCfg['useyn']) === false) $hpauthDreamCfg['useyn'] = 'n'; 
if (isset($hpauthDreamCfg['modyn']) === false) $hpauthDreamCfg['modyn'] = 'y';
if (isset($hpauthDreamCfg['moduseyn']) === false) $hpauthDreamCfg['moduseyn'] = 'n';
if (isset($hpauthDreamCfg['minoryn']) === false) $hpauthDreamCfg['minoryn'] = 'n'; 

$checked = array(
    'useyn' => array($hpauthDreamCfg['useyn'] => ' checked="checked"'),
	'modyn' => array($hpauthDreamCfg['modyn'] => ' checked="checked"'),
	'moduseyn' => array($hpauthDreamCfg['moduseyn'] => ' checked="checked"'),
    'minoryn' => array($hpauthDreamCfg['minoryn'] => ' checked="checked"'),
);
?>

<script type="text/javascript">
window.onload = function()
{
	cssRound('MSG01');
	view_tb("<? echo $hpauthDreamCfg['useyn']?>","<? echo ($hpauthDreamCfg['useyn'] == 'y') ? '' : 'none'?>");
	resizeFrame();
};

var IntervarId;
var prevUseyn = "<?php echo $hpauthDreamCfg['useyn']?>";
function resizeFrame()
{

    var oBody = document.body;
    var oFrame = parent.document.getElementById("pgifrm");
    var i_height = oBody.scrollHeight + (oFrame.offsetHeight-oFrame.clientHeight);
    oFrame.style.height = i_height;
    oFrame.height = i_height;

    if ( IntervarId ) clearInterval( IntervarId );
}
function checkForm(form) {
	if($('useyn-y').checked) {
		if(!$('cpid').value) {
			alert("ȸ���� Id�� �Է��ϼž� ��� ������ �����Դϴ�.");
			return false;
		}
	}
	var currentServiceCode = "<?php echo $hpauthConfig['serviceCode']; ?>";
	var currentServiceName = "<?php echo $hpauthConfig['serviceName']; ?>";
	var currentServiceUseyn = "<?php echo $currentHpauthConfig['useyn']; ?>";
	if (form.useyn[0].checked && currentServiceCode !== "dream" && currentServiceUseyn === 'y') {
		return confirm("���� " + currentServiceName + "�� '���'���� �����Ǿ��ֽ��ϴ�.\r\n���� �����Ͻðڽ��ϱ�?");
	}
	else {
		return true;
	}
}
function view_tb(val,disp){
	if(!disp && val != prevUseyn){
		document.frmField.modyn[0].checked = true;
		document.frmField.moduseyn[1].checked = true;
	}
	document.getElementById('useyn_tbl').style.display = disp;
	prevUseyn = val;
	resizeFrame();
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
	padding: 5px;
	border: 1px solid #e6e6e6;
}
</style>

<div class="title title_top">
	�帲��ť��Ƽ ����<span>�ݵ�� �޴����������� ���� ��ü�� ����� ������ �� �����Ͻñ� �ٶ��ϴ�.</span>
	<a href="javascript:manual('<?php echo $guideUrl; ?>board/view.php?id=member&no=21')"><img src="../img/btn_q.gif" border="0" align="absmiddle"/></a>
</div>

<table border="4" bordercolor="#dce1e1" style="border-collapse: collapse; margin-bottom: 20px;" width="730">
	<tr>
		<td style="padding:7px 0px 10px 10px">
			<div style="padding-top:5px; color:#666666; font-weight:bold;" class="g9"><b>�� [�ʵ�] �޴��� ����Ȯ�μ���(�帲��ť��Ƽ) �̿����� �ȳ�</b></div>
			<div style="padding-top:10px; color:#666666;" class="g9">
				�� <a href="adm_member_auth.hpauthDream.info.php" target="_parent" style="font-weight: bold; color: #627dce;">[����Ȯ�� �������� > �޴�������Ȯ�μ��� �ȳ�]</a>
				�� '�帲��ť��Ƽ'�� ��û���� �ۼ��Ͽ� �߼��մϴ�.<br/>
				(��)�帲��ť��Ƽ�� �ּ� "����� ���ı� ������ 150-28 ������� 5�� (��)�帲��ť��Ƽ ���� ���� ����� ��"
			</div>
			<div style="padding-top:3px; color:#666666;" class="g9">�� ���� �� ���� �� ������ ���� �� ��ǰ������ ���� �ȳ� ��ȭ�� �帳�ϴ�.</div>
			<div style="padding-top:3px; color:#666666;" class="g9">�� �߱� ������ ȸ����Code�� �� ������ �Ʒ� ���Զ��� �Է��մϴ�.</div>
			<div style="padding-top:3px; color:#666666;" class="g9">�� ���� ������ ����ϴ� ���θ��� ��� ���� ���� ��� ���θ� �� �������� �����ϼ���.</div>
			<div style="padding-top:3px; color:#666666;" class="g9">�� '���'��ư�� Ŭ���Ͽ� ������ �Ϸ��մϴ�.</div>
			<div style="padding-top:3px; color:#666666;" class="g9">�� ���θ����� ����Ȯ���� ���� �۵� �ϴ��� Ȯ�� �ϼ���.</div>
			<div style="padding-top:3px; color:#666666; margin-top: 10px;" class="g9">
				�� ���� �� �̿� ���� : 1899-4134 (��)�帲��ť��Ƽ<br/>
				��û���� <a href="adm_member_auth.hpauthDream.info.php" target="_parent" style="font-weight: bold; color: #627dce;">[����Ȯ�� �������� > �޴�������Ȯ�μ��� �ȳ�]</a>
				����  ��û��  �ٿ�ε� ���ֽø� �˴ϴ�.
			</div>
		</td>
	</tr>
</table>

<form name="frmField" method="post" action="adm_member_auth.hpauth.dream.indb.php" onsubmit="return checkForm(this);" style="margin-top: 10px;">
	<table class="tb">
		<colgroup>
			<col class="cellC" style="width:310px;"/>
			<col class="cellL"/>
		</colgroup>
		<tr>
			<th>ȸ���� Code</th>
			<td>
				<input type="text" name="cpid" id="cpid" class="line" value="<?php echo $hpauthDreamCfg['cpid']; ?>"/> 
				<span class="extext">�帲��ť��Ƽ���� �������� �߱޵Ǵ� ���̵� �Դϴ�.(<?php echo implode(',', $dreamsecurityPrefix); ?>�� ���۵Ǿ�� ��)</span>
			</td>
		</tr>
		<tr>
			<th style="width:150;">�޴��� ���� Ȯ�� ��� ����</th>
			<td class="noline">
				<input id="useyn-y" type="radio" name="useyn" id="use_y" value="y" <?php echo $checked['useyn']['y']; ?> onclick="view_tb('y','')">
				<label for="useyn-y" style="margin-right: 10px;">���</label>
				<input id="useyn-n" type="radio" name="useyn" id="use_n" value="n" <?php echo $checked['useyn']['n']; ?> onclick="view_tb('n','none')">
				<label for="useyn-n">������</label>
			</td>
		</tr>
	</table>
	<table id="useyn_tbl" class="tb">
		<colgroup>
			<col class="cellC" style="width:310px;"/>
			<col class="cellL"/>
		</colgroup>
		<tr>
			<th style="border-top:none;">ȸ�� ���� �� �޴��� ��ȣ ���� ����</th>
			<td class="noline" style="border-top:none;">
				<input id="modyn-y" type="radio" name="modyn" value="y" <?php echo $checked['modyn']['y']; ?>/>
				<label for="modyn-y" style="margin-right: 10px;">����</label>
				<input id="modyn-n" type="radio" name="modyn" value="n" <?php echo $checked['modyn']['n']; ?>/>
				<label for="modyn-n">�Ұ���</label>
				<div class="extext">ȸ�� ���� �� �޴��� ���� Ȯ�� ��� �ÿ��� �޴��� ��ȣ�� ���� �� �� ������ ������ �� �ֽ��ϴ�.</div>
			</td>
		</tr>
		<tr>
			<th>ȸ�� �޴�����ȣ ���� �� �޴�������Ȯ�� ��� ����</th>
			<td class="noline">
				<input id="moduseyn-y" type="radio" name="moduseyn" value="y" <?php echo $checked['moduseyn']['y']; ?>/>
				<label for="moduseyn-y" style="margin-right: 10px;">���</label>
				<input id="moduseyn-n" type="radio" name="moduseyn" value="n" <?php echo $checked['moduseyn']['n']; ?>/>
				<label for="moduseyn-n">������</label>
				<div class="extext">���θ� > ����������> ȸ���������� > �޴�����ȣ ���� �� �޴��� ����Ȯ�� ��� ���θ� �����մϴ�.</div>
			</td>
		</tr>
		<tr>
			<th>���� ���� ����</th>
			<td class="noline">
				<input id="minoryn-y" type="radio" name="minoryn" value="y" <?php echo $checked['minoryn']['y']; ?>>
				<label for="minoryn-y" style="margin-right: 10px;">��� <font class="extext">(19�� �̸� ȸ������ �Ұ�)</font></label>
				<input id="minoryn-n" type="radio" name="minoryn" value="n" <?php echo $checked['minoryn']['n']; ?>>
				<label for="minoryn-n">������</label>
			</td>
		</tr>
	</table>
	<div class="button"><input type="image" src="../img/btn_register.gif"/> <a href="javascript:history.back()"><img src="../img/btn_cancel.gif"/></a></div>
</form>

<div id="MSG01">
	<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
		<tr><td><img src="../img/icon_list.gif" align="absmiddle">�޴��� ����Ȯ�μ��񽺰� �� �� �ֵ��� ���α׷��� �⺻ž�� �Ǿ� �ֽ��ϴ�. </td></tr>
		<tr><td><img src="../img/icon_list.gif" align="absmiddle">�޴��� ����Ȯ�μ��񽺸� �ϱ� ���ؼ��� (��)�帲��ť��Ƽ�� ��ุ �����Ͻø� �˴ϴ�. </td></tr>
		<tr>
			<td>
				<img src="../img/icon_list.gif" align="absmiddle">�޴��� ����Ȯ�μ��� ������ü:
				<a href="http://www.dreamsecurity.com" target="_new" style="color: #ffffff;">(��)�帲��ť��Ƽ <span class="ver7">(http://www.dreamsecurity.com)</span></a>
			</td>
		</tr>
	</table>
	<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
		<tr><td height="20"></td></tr>
		<tr><td><font class="def1" color="white">[�ʵ�] �޴��� ����Ȯ�μ��� ���� </b></font></td></tr>
		<tr><td><font class="def1" color="white">��</font> �޴��� ����Ȯ�� ���񽺸� �����ϴ� (��)�帲��ť��Ƽ�� ��û���� �ۼ��Ͽ� �߼��մϴ�.</td></tr>
		<tr><td>(��)�帲��ť��Ƽ�� �ּ� "����� ���ı� ������ 150-28 ������� 5�� (��)�帲��ť��Ƽ ���� ���� ����� ��"</td></tr>
		<tr><td><font class="def1" color="white">��</font> (��)�帲��ť��Ƽ�� ����ڷκ��� ȸ����Code�� �߱� �����ð� �˴ϴ�.</td></tr>
		<tr><td><font class="def1" color="white">��</font> �߱� ������ ȸ����Code�� �� �������� �Է��ϼ���.</td></tr>
		<tr><td><font class="def1" color="white">��</font> ���θ����� ����Ȯ���� ���� �۵� �ϴ��� Ȯ�� �ϼ���.</td></tr>
	</table>
</div>