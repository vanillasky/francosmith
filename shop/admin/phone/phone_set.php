<?
$location = "�̳����� ���� > �̳����� ��û �� ����";
include "../_header.php";

@include "../../conf/phone.php";
$mode = "setting";
$set = $set['phone'];


if($set['pc080_id']) $checked['register']['1'] = "checked";
else $checked['register']['0'] = "checked";

$settingyn = 0;
if($set['user_id'] && $set['coop_id'] && $set['pc080_id'])$settingyn = 1;

if(!$settingyn){
	msg("�̳����� ���񽺴� ����Ǿ����ϴ�.","phone_guide.php");
}
?>
<script>
function chkdown(){
	var f = document.forms[0];
	if( f.settingyn.value != '1' ){
		alert('�ٸ��� ��û���� �ʾҽ��ϴ�.!');
		return;
	}
	popup("../../partner/pc080/download.php?mode=1",500,200);
}
function chkregister(){
	document.getElementById('layerid0').style.display =	document.getElementById('layerid1').style.display = 'none';
	if( document.getElementsByName('mode')[0].checked == true ){
		document.getElementById('layerid0').style.display = 'block';
		document.getElementById('download_id').style.display = 'none';
	}
	if( document.getElementsByName('mode')[1].checked == true ){
		document.getElementById('layerid1').style.display = 'block';
		document.getElementById('download_id').style.display = 'block';
	}
}
</script>
<div class="title title_top">�̳����� ��û �� ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=marketing&no=28')"><img src="../img/btn_q.gif" border=0 align=absmiddle hspace=2></a></div>
<div style="font-weight:bold">1. �̳����� ���� ��û �ϰų� �������� �ùٸ��� �ʾ� ���񽺰� �������� ������ �������� �����Ͻʽÿ�!</div>
<div style="font:0;height:5"></div>
<form method="post" action="indb.php" onsubmit="return chkForm(this)">
<input type="hidden" name="settingyn" value="<?=$settingyn?>">
<table class=tb>
<col class=cellC><col class=cellL>
<tr>
	<td>��û����</td>
	<td>
	<input type="radio" value="register" name="mode" class="null" <?=$checked['register']['0']?> onclick="chkregister()" <?if($settingyn){?>disabled<?}?> />���� ��� ��û �� �̰ų� ���� ��û�մϴ�.
	<input type="radio" value="setting" name="mode" class="null" <?=$checked['register']['1']?> onclick="chkregister()" <?if(!$settingyn){?>disabled<?}?> />���� ��� �Ϸ� �� �����մϴ�.
	</td>
</tr>
<tr>
	<td><div id="layerid0" style="display:none">�̸���</div><div id="layerid1" style="display:none">�̳����� ���̵�</div></td>
	<td>
	<input type="text" name="email" value="<?=$set['email']?>" class="line" size="40" required>
	<div class=small><font class=extext>(100�� �̳� �Է�) ���� �Ұ��� E�����ּ� ��� ��, �̳�����(PC080) �̿��� ���� �� �� �ֽ��ϴ�. </font></div>
	</td>
</tr>
<tr>
	<td>�̸�</td>
	<td>
	<input type="text" name="user_name" value="<?=$set['user_name']?>" class="line" size="40" required>
	</td>
</tr>
<tr>
	<td>��ȭ��ȣ</td>
	<td>
	<input type="text" name="tel" value="<?=$set['user_tel']?>" class="line" label="��ȭ��ȣ" required>
	<span class=small><font class=extext>(-���� ���ڷθ� �Է�)</font></span>
	</td>
</tr>
<tr>
	<td>��й�ȣ</td>
	<td>
	<input type="password" name="pwd" value="<?=$set['pwd']?>" class="line" size='12' maxlength='12' label="��й�ȣ" required>
	<span class=small><font class=extext>(4~12�� �Է�)</font></span>
	</td>
</tr>
</table>
<div style="font:0;height:5"></div>
<div style="background-color:#f6f6f6;text-align:center;height:30px;padding-top:7px;color:red;">2011�� 11�� 30�� ���� ���� �ű� ������ ����˴ϴ�. <a href="phone_guide.php"><font color="#0000ff" style="font-weight:bold;">�ڼ��� ����</font></a></div>
<div style="border-bottom:3px #efefef solid;padding-top:20px"></div>
<div style="font:0;height:5"></div>
<div align="center"><input type="image" src="../img/btn_register.gif" />
<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a></div>
</form>

<div id="download_id" style="display:none">
<p />
<div style="font-weight:bold">2. ���� ���� ���� ä������ ��� �Ʒ��� �ٿ�ε� ��ư�� Ŭ���Ͽ� �޽��� ���� �ٿ�ε� �ް� ��ġ�մϴ�. </div>
<div style="font:0;height:5"></div>
<table width="100%" border="0" cellpadding="10" cellspacing="0" style="border:1px #dddddd solid;">
<tr>
	<td align="center" bgcolor="#f6f6f6" style="font:16pt tahoma;"><img src="../img/icon_down.gif" border="0" align="absmiddle"><b>download</b></td>
</tr>
<tr>
	<td align="center"><a href="javascript:chkdown();">�ٿ�ε�</a></td>
</tr>
</table>
<p />
<div style="font-weight:bold">3. ���θ��� �̳������޽��� ��ȭ�ϱ��ư(����)�� �������ݴϴ�. </div>
<div style="font:0;height:5"></div>
<table width="100%" border="0" cellpadding="5" cellspacing="0" style="border:1px #dddddd solid;">
<tr>
	<td align="left" style="padding:10 10 10 10">
	<div>������ �������� �̳����� �������� �߰��ϰ� ������ ���� <b>�Ʒ��� '��� ������ ����'�� �����Ǿ��ִ� ������ġȯ�ڵ�</b>�� �Է��մϴ�. <font class=extext>- ��) { =dataIconPhone(0) }</font></div>
	<table class="null" cellPadding="2" border="1" borderColor="#e6e6e6">
	<col class=cellC align="center"><col style="padding-left:5" align="center"><col style="padding-left:5" align="center"><col style="padding-left:5" align="center">
	<tr>
		<td>ON������</td>
		<td><img src="<?=$cfg[rootDir]?>/data/skin/<?=$cfg[tplSkin]?>/img/banner/banner_phone.gif"><div>banner_phone.gif</div></td>
		<td><img src="<?=$cfg[rootDir]?>/data/skin/<?=$cfg[tplSkin]?>/img/banner/banner_phone1.gif"><div>banner_phone1.gif</div></td>
		<td><img src="<?=$cfg[rootDir]?>/data/skin/<?=$cfg[tplSkin]?>/img/banner/banner_phone2.gif"><div>banner_phone2.gif</div></td>
	</tr>
	<tr>
		<td>OFF������</td>
		<td><img src="<?=$cfg[rootDir]?>/data/skin/<?=$cfg[tplSkin]?>/img/banner/banner_phone_off.gif"><div>banner_phone_off.gif</div></td>
		<td><img src="<?=$cfg[rootDir]?>/data/skin/<?=$cfg[tplSkin]?>/img/banner/banner_phone1_off.gif"><div>banner_phone1_off.gif</div></td>
		<td><img src="<?=$cfg[rootDir]?>/data/skin/<?=$cfg[tplSkin]?>/img/banner/banner_phone2_off.gif"><div>banner_phone2_off.gif</div></td>
	</tr>
	<tr>
		<td>ġȯ�ڵ�</td>
		<td>{ =dataIconPhone(0) }</td>
		<td>{ =dataIconPhone(1) }</td>
		<td>{ =dataIconPhone(2) }</td>
	</tr>

	</table>
	<div><b>���̹��� ������ ������̽� ��Ų�� img/banner/�������� �ش������� ã�� �����ϽǼ� �ֽ��ϴ�.</div>
	</td>
</tr>
</table>
</div>
<script>chkregister();</script>
<? include "../_footer.php"; ?>
