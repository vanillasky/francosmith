<?php

include "../_header.popup.php";

$hpauth = Core::loader('Hpauth');

$hpauthConfig = $hpauth->loadConfig();
$mcertiConfig = $hpauth->loadServiceConfig('mcerti');
$currentHpauthConfig = $hpauth->loadCurrentServiceConfig();

if (strlen($mcertiConfig['cpid']) < 1) $disabled = true;

if (isset($mcertiConfig['useyn']) === false) $mcertiConfig['useyn'] = 'n'; 
if (isset($mcertiConfig['modyn']) === false) $mcertiConfig['modyn'] = 'y';
if (isset($mcertiConfig['moduseyn']) === false) $mcertiConfig['moduseyn'] = 'n';
if (isset($mcertiConfig['minoryn']) === false) $mcertiConfig['minoryn'] = 'n'; 

$checked = array(
    'useyn' => array($mcertiConfig['useyn'] => ' checked="checked"'),
    'modyn' => array($mcertiConfig['modyn'] => ' checked="checked"'),
	'moduseyn' => array($mcertiConfig['moduseyn'] => ' checked="checked"'),
	'minoryn' => array($mcertiConfig['minoryn'] => ' checked="checked"'),
);
?>
<script type="text/javascript">
window.onload = function()
{
	cssRound('MSG01');
	view_tb("<? echo $mcertiConfig['useyn']?>","<? echo ($mcertiConfig['useyn'] == 'y') ? '' : 'none'?>");
	resizeFrame();
};

var IntervarId;
var prevUseyn = "<?php echo $mcertiConfig['useyn']?>";
function resizeFrame()
{
    var oBody = document.body;
    var oFrame = parent.document.getElementById("pgifrm");
    var i_height = oBody.scrollHeight + (oFrame.offsetHeight-oFrame.clientHeight);
    oFrame.style.height = i_height;
    oFrame.height = i_height;

    if ( IntervarId ) clearInterval( IntervarId );
}
function checkForm(form)
{
	var currentServiceCode = "<?php echo $hpauthConfig['serviceCode']; ?>";
	var currentServiceName = "<?php echo $hpauthConfig['serviceName']; ?>";
	var currentServiceUseyn = "<?php echo $currentHpauthConfig['useyn']; ?>";
	if (form.useyn[0].checked && currentServiceCode !== "mcerti" && currentServiceUseyn === 'y') {
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
	padding: 8px;
	border: 1px solid #e6e6e6;
}
</style>
<div class="title title_top">
	Mcerti ����
	<span>�ݵ�� �޴����������� ���� ��ü�� ����� ������ �� �����Ͻñ� �ٶ��ϴ�.</span>
	<a href="javascript:manual('<?php echo $guideUrl; ?>board/view.php?id=member&no=21')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a>
</div>

<table border="4" bordercolor="#dce1e1" style="border-collapse: collapse; margin-bottom: 20px;" width="740">
	<tr>
		<td style="padding:7px 0px 10px 10px">
			<div style="padding-top:5px; color:#666666; font-weight:bold;" class="g9"><b>�� [�ʵ�] �޴��� ����Ȯ�μ���(Mcerti) �̿����� �ȳ�</b></div>
			<div style="padding-top:10px; color:#666666;" class="g9">��
				<a href="adm_member_auth.hpauthDream.info.php" target="_parent" style="font-weight: bold; color: #627dce;">[����Ȯ�� �������� > �޴�������Ȯ�μ��� �ȳ�]</a>��
				'Mcerti' ���񽺸� �¶��� ��û�մϴ�.
			</div>
			<div style="padding-top:3px; color:#666666;" class="g9">�� �̸��� �Ǵ� SMS�� ���� ���� ��û ����� Ȯ���մϴ�.<span style="color: #ff0000;">(������ �������� �� 3~4�� ���� �ҿ��)</span></div>
			<div style="padding-top:3px; color:#666666;" class="g9">�� �� �������� 'Mcerti'��� �ǿ��� '�޴��� ���� Ȯ�� ��� ����'�� '���'���� �����ϼ���</div>
			<div style="padding-top:3px; color:#666666;" class="g9">�� ���� ������ ����ϴ� ���θ��� ��� ���� ���� ��� ���θ� �� �������� �����ϼ���.</div>
			<div style="padding-top:3px; color:#666666;" class="g9">�� '���'��ư�� Ŭ���Ͽ� ������ �Ϸ��մϴ�.</div>
			<div style="padding-top:3px; color:#666666;" class="g9">�� ���θ����� ����Ȯ���� ���� �۵� �ϴ��� Ȯ�� �ϼ���.</div>
			<div style="padding-top:3px; color:#666666;" class="g9">
				�� �̿�Ⱓ ���� �� ��ݰ�����
				<a href="http://www.godo.co.kr/mygodo/certification_list.php" target="_blank" style="font-weight: bold; color: #627dce;">[���̰� > ���θ����� > �ΰ����񽺽�û���� > �޴��� ��������]</a>
				�� �̿��Ͻñ� �ٶ��ϴ�.
			</div>
		</td>
	</tr>
</table>

<form name="frmField" action="adm_member_auth.hpauth.mcerti.indb.php" method="post" onsubmit="return checkForm(this);" <?php echo $disabled ? 'disabled="disabled"' : ''; ?>>
	<table class="tb"<?php echo $disabled; ?>>
		<colgroup>
			<col class="cellC" style="width:310px;"/>
			<col class="cellL"/>
		</colgroup>
		<tr>
			<th>ȸ����Code(�����ο���)</th>
			<td id="service-id">
				<?php if (strlen($mcertiConfig['cpid']) < 1) { ?>
				<span style="color: #ff0000; font-weight: bold;">�̽���</span>
				<?php } else { ?>
				<?php echo $mcertiConfig['cpid']; ?> <span style="color: #00AA00; font-weight: bold;">(����)</span>
				<?php } ?>
			</td>
		</tr>
		<tr>
			<th>�޴��� ���� Ȯ�� ��� ����</th>
			<td class="noline">
				<input id="useyn-y" type="radio" name="useyn" value="y" <?php echo $checked['useyn']['y']; ?> onclick="view_tb('y','')"/>
				<label for="useyn-y" style="margin-right: 10px;">���</label>
				<input id="useyn-n" type="radio" name="useyn" value="n" <?php echo $checked['useyn']['n']; ?> onclick="view_tb('n','none')"/>
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
			<th>���� ���� ��� ����</th>
			<td class="noline">
				<input id="minoryn-y" type="radio" name="minoryn" value="y" <?php echo $checked['minoryn']['y']; ?>/>
				<label for="minoryn-y" style="margin-right: 10px;">��� <font class="extext">(19�� �̸� ȸ������ �Ұ�)</font></label>
				<input id="minoryn-n" type="radio" name="minoryn" value="n" <?php echo $checked['minoryn']['n']; ?>/>
				<label for="minoryn-n">������</label>
			</td>
		</tr>
	</table>
	<div class="button"><input type="image" src="../img/btn_register.gif"/> <a href="javascript:history.back()"><img src="../img/btn_cancel.gif"/></a></div>
</form>

<div id="MSG01">
	<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
		<tr>
			<td>
				<div>
					<img src="../img/icon_list.gif" align="absmiddle"> �޴��� ����Ȯ�μ��񽺰� �� �� �ֵ��� ���α׷��� �⺻ž�� �Ǿ� �ֽ��ϴ�.
				</div>
				<div>
					<img src="../img/icon_list.gif" align="absmiddle"> �޴��� ����Ȯ�μ��񽺸� �����ϴ� ��ü �� ������ ��ü�� �����Ͽ� ��û(�Ǵ� ���) �� �̿��Ͻ� �� �ֽ��ϴ�
				</div>
			</td>
		</tr>
	</table>
</div>