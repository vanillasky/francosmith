<?
include "../_header.popup.php";

if($_POST['action'] == 'ok') {

	unset($_POST['action'], $_POST['x'], $_POST['y']);

	$choice_range = explode('_', $_POST['choice_range']);
	$_POST['range'] = $choice_range[0];
	$_POST['range_month'] = $choice_range[1];
	unset($_POST['choice_range']);

	$choice_collect = explode('_', $_POST['choice_collect']);
	$_POST['collect'] = $choice_collect[0];
	$_POST['collect_month'] = $choice_collect[1];
	unset($_POST['choice_collect']);

	require_once("../../lib/qfile.class.php");
	$qfile = new qfile();

	$qfile->open("../../conf/config.populate.php");
	$qfile->write("<? \n");
	$qfile->write("\$cfg_populate = array( \n");
	foreach ($_POST as $k=>$v) $qfile->write("'$k' => '$v', \n");
	$qfile->write(") \n;");
	$qfile->write("?>");
	$qfile->close();

	// ĳ�� �ʱ�ȭ
	@unlink('../../data/statistics/populate_goods_data.cached.txt');

	echo "
	<script>
	alert('����Ǿ����ϴ�');
	self.location.href='iframe.populate.setting.php';
	</script>
	";
	exit;

}

include "../../lib/populate.class.php";
$cfg_populate = populate::getConf();
?>
<script>
function copy_txt(val){
	window.clipboardData.setData('Text', val);
	alert( 'ġȯ�ڵ带 �����߽��ϴ�. \n���ϴ� ���� �ٿ��ֱ�(Ctrl+V)�� �Ͻø� �˴ϴ�.' );
}
function chkType() {
	var objTypeLabel = document.getElementById('typeLabel');
	var eleType = document.getElementsByName('type');
	if (eleType[0].checked) {
		objTypeLabel.innerHTML = '��ǰ �Ǹ� ������';
		document.getElementsByName('choice_collect')[0][0].disabled = false;
	}
	else {
		objTypeLabel.innerHTML = '�������� ������';
		document.getElementsByName('choice_collect')[0][0].disabled = true;
	}
}
</script>
<form id="frmPopulate" action="<?=$_SERVER['PHP_SELF']?>" method="post">
<input type="hidden" name="action" value="ok">

<div class="title title_top">�α��ǰ ���⼳��<span> ���θ��� ��ǰ �Ǹ� ���� �Ǵ� ���� ���� �� ��ǰ���� ������ �����մϴ�. </span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=design&no=20')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a></div>
<table class="tb">
<col class="cellC">
<col class="cellL">
<tr>
	<td>����Ÿ�� ����</td>
	<td>
		<label><input type="radio" name="type" onclick='chkType()' style="border:0px" <?=$cfg_populate['type'] == 'order' ? 'checked' : '' ?> value="order"> ��ǰ �Ǹ� ����</label>
		<label><input type="radio" name="type" onclick='chkType()' style="border:0px" <?=$cfg_populate['type'] == 'pageview' ? 'checked' : '' ?> value="pageview"> ��������(���̺� ��ǰ) ����</label>
	</td>
</tr>
<tr>
	<td>���� ���� ����</td>
	<td>
		1�� ~ <select name="limit"><? for ($i=1;$i<=20;$i++) { ?><option value="<?=$i?>" <?=$cfg_populate['limit'] == $i ? 'selected' : '' ?>><?=$i?></option><? } ?></select>
	</td>
</tr>
<tr>
	<td>�����ֱ� ����</td>
	<td>
		��� ������ ��������
		<select name="choice_range">
		<option <?=$cfg_populate['range'] == 'hour' ? 'selected' : '' ?> value="hour"> 1�ð�</option>
		<option <?=$cfg_populate['range'] == 'week' ? 'selected' : '' ?> value="week"> 1����</option>
		<? for ($i=1;$i<=12;$i++) { ?><option value="month_<?=$i?>" <?=$cfg_populate['range'] == 'month' && $cfg_populate['range_month'] == $i ? 'selected' : '' ?>><?=$i?>����</option><? } ?>
		</select>
		���� ������ ������.
	</td>
</tr>
<tr>
	<td>�����Ⱓ ����</td>
	<td>
		���� ������ ��������
		<select name="choice_collect">
		<option <?=$cfg_populate['collect'] == 'hour' ? 'selected' : '' ?> value="hour"> 1�ð�</option>
		<option <?=$cfg_populate['collect'] == 'week' ? 'selected' : '' ?> value="week"> 1����</option>
		<? for ($i=1;$i<=12;$i++) { ?><option value="month_<?=$i?>" <?=$cfg_populate['collect'] == 'month' && $cfg_populate['collect_month'] == $i ? 'selected' : '' ?>><?=$i?>����</option><? } ?>
		</select>
		������ '<span id="typeLabel" style="font-weight:bold;">��ǰ �Ǹ� ������</span>'�� �����Ͽ� ������ ������.
	</td>
</tr>
<tr>
	<td>ǰ����ǰ ����</td>
	<td>
		<label><input type="radio" name="include_soldout" style="border:0px" <?=$cfg_populate['include_soldout'] == '0' ? 'checked' : '' ?> value="0"> ǰ����ǰ ����</label>
		<label><input type="radio" name="include_soldout" style="border:0px"<?=$cfg_populate['include_soldout'] == '1' ? 'checked' : '' ?>  value="1"> ǰ����ǰ ����</label>
	</td>
</tr>

<tr>
	<td>���ø� ����</td>
	<td>
		<fieldset><legend><label><input type="radio" name="design" style="border:0px" <?=$cfg_populate['design'] == 'expand' ? 'checked' : '' ?> value="expand"> ��ħ��</label></legend>
		<span class="extext">Ÿ��Ʋ �κа� �����κ��� ������ ���·� �����Ǿ� ����˴ϴ�.</span>
		</fieldset>

		<fieldset><legend><label><input type="radio" name="design" style="border:0px" <?=$cfg_populate['design'] == 'rollover' ? 'checked' : '' ?> value="rollover"> �ѿ�����</label></legend>
		<span class="extext">Ÿ��Ʋ �κ��� ������ �ڵ����� ���ư��鼭 �������� Ÿ��Ʋ�� ���콺������ �����κ��� ���ĺ������ϴ�.</span>
		</fieldset>
	</td>
</tr>

<tr>
	<td>ġȯ�ڵ�</td>
	<td>
		<div style="padding-top:5;">{#populate} <img class="hand" src="../img/i_copy.gif" onclick="copy_txt('{#populate}')" alt="�����ϱ�" align="absmiddle"/></div>
		<div style="padding-top:5;" class="extext">ġȯ�ڵ带 �����Ͽ� ���ϴ� ������ ��ġ�� '�ٿ��ֱ�(Ctrl+V)'�Ͽ� ����Ͻø� ���մϴ�.</div>

	</td>
</tr>
</table>

<div id="MSG01">
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�α��ǰ ������ ���θ��� �����Ͽ�  �ַ»�ǰ�� ���� �ΰ���Ű�� ��������  �Ǹ����� ���� �� �ִ� ����Դϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��ǰȫ�� �� �����ý� Ȱ���Ͻø� ���� ȿ�����Դϴ�.</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���ø� �������� ������������ > ��Ÿ������ > �α��ǰ ���� ��ħ�� / �νð�ǰ ���� �ѿ����� ���������� ���� �� ������ ���� �մϴ�.</td></tr>
</table>
</div>
<script>
cssRound('MSG01')
</script>


<div class="button">
<input type=image src="../img/btn_register.gif">
</div>

</form>
<script>
chkType();
table_design_load();
setHeight_ifrmCodi();
</script>