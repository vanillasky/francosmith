<?

$location = "�⺻���� > �⺻��������";
include "../_header.php";
@include '../../conf/config.company.php'; //ȸ��Ұ�

if(!$cfg['counterYN']) $cfg['counterYN'] = 1;
$checked['counterYN'][$cfg['counterYN']] = 'checked';

$cfgCompany = @array_map('stripslashes', (array)$cfgCompany);
$cfg['customerHour'] = preg_replace("/<br \/>/","\r\n",$cfg['customerHour']);
?>
<script type="text/javascript" src="../../lib/meditor/mini_editor.js"></script>
<script>
function chkForm2(fm)
{
	if(form.adminEmail.value==''){
		alert('������ Email �ʼ� �Է�');
		form.adminEmail.focus();
		return false;
	}
}
</script>

<form name=form method=post action="indb.php" onsubmit="return chkForm2(this)">
<input type=hidden name=mode value="config">

<div class="title title_top">�⺻����<span>���θ� �⺻������ �Է����ּ���</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<tr>
	<td>���θ��̸�</td>
	<td><input type=text name=shopName value="<?=$cfg[shopName]?>" class=lline style="width:200"></td>
	<td>�����̸�</td>
	<td><input type=text name=shopEng value="<?=$cfg[shopEng]?>" class=lline style="width:200"></td>
</tr>
<tr>
	<td>������ Email</td>
	<td><input type=text name=adminEmail  class=lline value="<?=$cfg[adminEmail]?>" style="width:200"></td>
	<td>���θ� URL</td>
	<td colspan=3>http://<input type=text name=shopUrl style="width:163px" value="<?=$cfg[shopUrl]?>"  class=lline></td>
</tr>
</table>
<div style="margin-top:5px"><span class=small><font class=extext>�ذ����� Email�� �ʼ��׸��Դϴ�. ������ [ȸ������>�ڵ����ϼ���] ������ �߼۵��� �ʽ��ϴ�.</font></span></div>

<div class=title>ȸ������<span>���θ� ȭ���ϴ��� ī�Ƕ���Ʈ �κп� ǥ�õ˴ϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL><col class=cellC><col class=cellL>
<tr>
	<td>��ȣ��</td>
	<td colspan=3><input type=text name=compName  class=lline value="<?=$cfg[compName]?>" style="width:200"></td>
</tr>
<tr>
	<td>����</td>
	<td><input type=text name=service value="<?=$cfg[service]?>"  class=line></td>
	<td>����</td>
	<td><input type=text name=item value="<?=$cfg[item]?>"  class=line></td>
</tr>
<tr>
	<td>���������ȣ</td>
	<td colspan=3>
	<input type=text name=zipcode[] id="zipcode0" size=3 readonly value="<?=substr($cfg[zipcode],0,3)?>"  class=line> -
	<input type=text name=zipcode[] id="zipcode1" size=3 readonly value="<?=substr($cfg[zipcode],3)?>"  class=line>
	<a href="javascript:popup('../../proc/popup_address.php',500,432)"><img src="../img/btn_zipcode.gif" align=absmiddle></a>
	</td>
</tr>
<tr>
	<td>������ּ�</td>
	<td colspan=3>
	����	��: <input type=text name=address id="address" style="width:60%" value="<?=$cfg[address]?>"  class=line> <br />
	���θ�	 : <input type="text" name="road_address" id="road_address" style="width:60%" value="<?=$cfg['road_address']?>" class="line">
	</td>
</tr>
<tr>
	<td>����ڹ�ȣ</td>
	<td><input type=text name=compSerial value="<?=$cfg[compSerial]?>"  class=line ></td>
	<td>����ǸŽŰ��ȣ</td>
	<td><input type=text name=orderSerial value="<?=$cfg[orderSerial]?>"  class=line></td>
</tr>
<tr>
	<td>��ǥ�ڸ�</td>
	<td><input type=text name=ceoName value="<?=$cfg[ceoName]?>"  class=line></td>
	<td>�����ڸ�<br>(��������������)</td>
	<td><input type=text name=adminName value="<?=$cfg[adminName]?>"  class=line></td>
</tr>
<tr>
	<td>��ȭ��ȣ</td>
	<td><input type=text name=compPhone value="<?=$cfg[compPhone]?>"  class=line></td>
	<td>�ѽ���ȣ</td>
	<td><input type=text name=compFax value="<?=$cfg[compFax]?>"  class=line></td>
</tr>
</table>

<div class=title>ȸ��Ұ�<span>���θ��� ȸ��Ұ� �������� ����˴ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td colspan="2" style="background-color:white;"><textarea name="compIntroduce" style="width:100%;height:150px" type="editor"><?=$cfgCompany['compIntroduce']?></textarea></td>
</tr>
<tr>
	<td>�൵ �ҽ� �ڵ� ����</td>
	<td>
		<div class="pdv3"><a href="http://blog.daum.net/daummaps/482" target="_blank" onclick="javascript:alert('���� �൵����� API ���񽺰� ����Ǿ����ϴ�. ���������� �൵����� ���񽺸� �̿��Ͽ� �츮ȸ���� �൵�� �־��ּ���');"><img src="../img/btn_map.gif" border="0"></a></div>
		<div class="pdv5"><textarea name="compMap" class="line" style="width:100%; height:50px;"><?=$cfgCompany['compMap']?></textarea></div>
		<div class="pdv3 extext">�൵����� ����� �̿��Ͽ� ȸ��Ұ� �ϴܿ� ȸ�� �൵�� �߰��� �� �ֽ��ϴ�.<br>���൵����⡯ �Ϸ� �� ������� ���±� �ڵ塯�� �����Ͽ� ������ּ���.</div>
	</td>
</tr>
</table>

<div class="title">
	�����Ϳ����<span>���θ��� ���� �� ������ �������� ǥ�õ˴ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=2')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a>
	<span class="small" style="vertical-align:bottom;"><input type="checkbox" onclick="javascript:copy_field(this.checked);" style="border:0px;"> �⺻����, ȸ�������� �����մϴ�.</span>
</div>
<table class="tb">
<col class="cellC" /><col class="cellL" /><col class="cellC" /><col class="cellL" />
<tr>
	<td>�����ȭ��ȣ</td>
	<td><input type="text" name="customerPhone" class="line" value="<?=$cfg['customerPhone']?>" /></td>
	<td>�ѽ���ȣ</td>
	<td><input type="text" name="customerFax" class="line" value="<?=$cfg['customerFax']?>" /></td>
</tr>
<tr>
	<td>�̸����ּ�</td>
	<td colspan="3"><input type="text" name="customerEmail" class="line" style="width:100%;" value="<?=$cfg['customerEmail']?>" /></td>
</tr>
<tr>
	<td>��ð�</td>
	<td colspan="3"><textarea name="customerHour" class="line" style="width:100%; height:70px;"><?=$cfg['customerHour']?></textarea></td>
</tr>
</table>

<div class=title>���Ÿ��Ʋ<span>������ ���Ʋ�� ������ Ÿ��Ʋ�� �˻�����Ʈ���� ������ Ű���带 �Է��ϼ���</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>���θ�Ÿ��Ʋ</td>
	<td><input type=text name=title value="<?=$cfg[title]?>"  class=lline></td>
</tr>
<tr>
	<td>�˻����� Ű����</td>
	<td><input type=text name=keywords value="<?=$cfg[keywords]?>" style="width:100%"  class=line></td>
</tr>
</table>

<div class=title>��Ÿ����<span>�α׾ƿ�Ÿ�� �� ��Ÿ������ �����մϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=2')"><img src="../img/btn_q.gif" border=0 align=absmiddle></a></div>
<table class=tb>
<col class=cellC><col class=cellL>
<!--
<tr>
	<td>�ַ��������</td>
	<td><input type=text name=rootDir value="<?=$cfg[rootDir]?>">
	<span class=small><font color=#5B5B5B>���Ͻô� ���������� �����Ͻ��� �ݵ�� �� ������ ���� �����ϼ���</font></span>
	</td>
</tr>
-->
<tr>
	<td>�ڵ��α׾ƿ�</td>
	<td>
	�α��� �� <input type=text name=sessTime value="<?=$cfg[sessTime]?>" size=6 onkeydown="onlynumber()"  class="right line"> �а� Ŭ���� ������ �ڵ��α׾ƿ��˴ϴ�.
	<span class=small><font class=extext>�������� �θ� �ð����Ѿ���</font></span>
	</td>
</tr>
<tr>
	<td>�湮�м� ��뿩��</td>
	<td class="noline">
		<input type="radio" name="counterYN" value="1" <?=$checked['counterYN'][1]?> />��� <input type="radio" name="counterYN" value="2" <?=$checked['counterYN'][2]?> />�̻��
		<span class="small"><font class="extext">���/�����Ͱ����� �湮�м� ��뿩�θ� �����մϴ�.</font></span>
	</td>
</tr>
<tr>
	<td>�̹���/������<br/>������� ����</td>
	<td class="noline">
		<input type="radio" name="preventContentsCopy" value="1" <?=($cfg[preventContentsCopy] == '1') ? 'checked' : ''?> />��� <input type="radio" name="preventContentsCopy" value="0" <?=($cfg[preventContentsCopy] != '1') ? 'checked' : ''?> />�̻��

		<div class="extext" style="margin:3px 0 0 3px;line-height:150%">
		���θ��� ��ϵ� �̹��� �� ������ ������� ��뿩�θ� �����մϴ�. <br>
		���θ� ȭ�鿡�� �̹��� �� �������� ���콺�� ��� ������ Ŭ���� contextmenu �� ���� �ʽ��ϴ�.<br>
		���θ� ȭ�鿡�� �̹��� �� �������� ���콺 �巡�׽� Ű���� Ctrl+C ����� �ȵ˴ϴ�.<br>
		���θ� ȭ�鿡�� �̹��� �� ������ ������ ����� �ȵ˴ϴ�.<br>
		</div>
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
<tr><td><img src="../img/icon_list.gif" align="absmiddle">ȸ������ : Ȩ������ ī�Ƕ���Ʈ �� �̸��� ���� � ���Ǵ� �����Դϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">���θ�Ÿ��Ʋ : ������ �������� ����ٿ� ��Ÿ���� �����Դϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�˻�Ű���� : ����Ʈ �˻� Ű����� ����Ʈ ������ �˻�Ű���忡 �Է��մϴ�.</td></tr>
</table>
</div>
<script>
cssRound('MSG01');
mini_editor("../../lib/meditor/", false);
function copy_field(checkValue)
{
	var form = document.form;

	if(checkValue){
		form.customerFax.value		= form.compFax.value;
		form.customerEmail.value	= form.adminEmail.value;
		form.customerPhone.value	= form.compPhone.value;
	} else {
		form.customerFax.value		= "<?=$cfg['customerFax']?>";
		form.customerEmail.value	= "<?=$cfg['customerEmail']?>";
		form.customerPhone.value	= "<?=$cfg['customerPhone']?>";
	}
}
</script>
<? include "../_footer.php"; ?>