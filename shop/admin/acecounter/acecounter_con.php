<?

$location = "���/�����Ͱ��� > ���̽�ī���� ���� ����";
include "../_header.php";

## �۷ι����� $acecounter�� �о�´�. 
@include "../../conf/config.acecounter.php"; 

if (!$acecounter['status_apply']) $acecounter['status_apply'] = 'N'; 
if (!$acecounter['status_use']) $acecounter['status_use'] = 'N'; 
if (!$acecounter['use']) $acecounter['use'] = 'N'; 

$checked['use'][$acecounter['use']] = "checked"; 

if ($acecounter['status_use']!='Y' || !$acecounter['id'] || !$acecounter['pass']) 
	$disbled['use'] = "disabled"; 

$view_c_button = true; 	// ��Ŀ�ӽ� ��û��ư ���̱�
$view_m_button = true; 	// ������ ��û��ư ���̱� 
if ($acecounter['ver_use'] == 'm') 		$view_m_button = false; 
if ($acecounter['ver_use'] == 'c') 		$view_c_button = false; 
if ($acecounter['ver_apply'] == 'm' || $acecounter['ver_apply'] == 'c')	{
	$view_m_button = false; 
 	$view_c_button = false; 
 	$view_check_button = true; 
 } else {
 	$view_check_button = false; 
}

if ($acecounter['status_use'] == 'Y') {
	$acecounter_status = "���: ����("; 
	#
	if ($acecounter['ver_use']=='m') $version_msg = "������"; 
	else if ($acecounter['ver_use']=='c') $version_msg = "��Ŀ�ӽ�"; 
	else $version_msg = $acecounter['ver_apply']; 
	#
	$acecounter_status .= $version_msg.")";
} else {
	$acecounter_status = "�̵��"; 
}

$acecounter_status .= " ";
if ($acecounter['status_apply'] == 'Y') {
	$acecounter_status .= " ==> ��û��: ����("; 
	#
	if ($acecounter['ver_apply']=='m') $version_msg = "������"; 
	else if ($acecounter['ver_apply']=='c') $version_msg = "��Ŀ�ӽ�"; 
	else $version_msg = $acecounter['ver_apply']; 
	#
	$acecounter_status .= $version_msg.")";
} 
?>

<script>
function chkForm2(fm)
{
	/*
	if (fm.sessTime.value && fm.sessTime.value<20){
		alert("ȸ������ �����ð� ���ѽ� 20�� �̻� �����մϴ�");
		fm.sessTime.value = 20;
		fm.sessTime.focus();
		return false;
	}
	*/
}

function applyAceCounter(ver) 
{
	document.form.mode.value = 'apply'; 
	document.form.version.value = ver; 
	document.form.submit(); 
}

function getResultAceCounter() 
{
	document.form.mode.value = 'get_result'; 
	document.form.submit(); 
}

function getPayResult() 
{
	document.form.mode.value = 'pay_result'; 
	document.form.submit(); 
}
</script>

<form name=form method=post action="indb_acecounter.php" onsubmit="return chkForm2(this)">
<input type=hidden name='mode' value="acecounter">
<input type=hidden name='version' >

<div class="title title_top">���̽�ī���� ��û����<span>���̽�ī���� ���� ��û������ �Է����ּ���</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=data&no=23')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>�������</td>
	<td><?=$acecounter_status?></td>
</tr>
<? if ($acecounter['status_use'] != 'Y' || $acecounter['status_apply'] == 'Y' ) { ?>
<tr>
	<td>���� ��û</td>
	<td>
		<? if ($acecounter['status_use'] != 'Y') { ?>
	 		<input type='button' value='������ ��û' onclick="applyAceCounter('m')" />
	 	<? } ?>
	 	<? if ($acecounter['status_apply'] == 'Y') { ?>
			<input type='button' value='��û��� Ȯ��' onclick="getResultAceCounter()" />
		<? } ?>
	</td>
</tr>
<? } else { ?>
<tr>
	<td>���� ��������</td>
	<td> 
		���񽺹��� ������ ���̽�ī���� �����ڿ��� ��û�Ͻø� �˴ϴ�.
	</td>
</tr>
<? } ?>
<tr>
	<td>GCODE</td>
	<td><? if (!$acecounter['gcode']) echo "����"; else echo $acecounter['gcode']; ?></td>	
</tr>
<tr>
	<td>���� ��뿩��</td>
	<td class="noline">
		<input type="radio" name="acecounter[use]" value="Y" <?=$checked['use']['Y']?> <?=$disbled['use']?> />��� <input type="radio" name="acecounter[use]" value="N" <?=$checked['use']['N']?> <?=$disbled['use']?> />������
		<span class="small"><font class="extext">������ ���� �����Ͻø�, ���̽�ī���Ϳ� �αװ� ������ �ʽ��ϴ�.(Ʈ������ �߻����� ����.)</font></span>
	</td>
</tr>
<tr>
	<td>���Ⱓ</td>
	<td>
	<? if ($acecounter['status_use'] == 'Y') {
		?>
		<?=$acecounter['start']?>~<?=$acecounter['end']?>
		<?
	}
	?>
	</td>
</tr>
<tr>
	<td>���������ݾ�</td>
	<td>
		<? if (strlen($acecounter['recent_pay']) > 0) { echo number_format($acecounter['recent_pay'])." ��"; } ?>
		<input type='button' value='�ֱ� �����ݾ� ��ȸ' onclick="getPayResult()" />
	</td>
</tr>
</table>


<div class="button">
<input type=image src="../img/btn_register.gif">
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
</div>

</form>


<div id="MSG01">
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">ó�� ��û�Ϸ� ��, ������Ⱓ�� �Ҵ�˴ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">GCODE �� ���̽�ī���� ���� ��û�� �ڵ����� �ο��޴� ������ �ʿ��� ���Դϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���Ⱓ�� ����� ���, ���̽�ī���� �� �߰������� ���� �Ⱓ�� �����ؾ�  �մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���Ⱓ�� ����� ���, ���αװ� ���������� ������� ���� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���Ⱓ�� ������� ���� ��쿡��, ���α� Ʈ������ �������� �ʰ��� ���, ���αװ� ������������ ���̰ų� �߰����� �ݾ��� �߻��� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���̽�ī���� ���� ����߿���, [������뿩��]�� '������'���� ����/����ϸ�, ���̽�ī���ͷ� ���αװ� ������ �ʽ��ϴ�. ( �� �α� Ʈ���ȵ� �߻����� �ʽ��ϴ�.) </td></tr>
</table>
</div>
<script>
cssRound('MSG01')
</script>
<? include "../_footer.php"; ?>