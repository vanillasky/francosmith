<?
$location = "�⺻���� > ȸ������ ���� ���� > ��й�ȣ ã�� ����";
include "../_header.php";

$info_cfg = $config->load('member_info');

$info_cfg['finder_use_email'] = 1;	// ������ ���
if(!$info_cfg['finder_use_mobile']) $info_cfg['finder_use_mobile'] = 0;
if(!$info_cfg['finder_mobile_auth_message']) $info_cfg['finder_mobile_auth_message'] = '[{shopName}]'.PHP_EOL.'ȸ������ ������ȣ�� {authNum} �Դϴ�. ��Ȯ�� �Է����ּ���.';

$checked['finder_use_email'][$info_cfg['finder_use_email']] = " checked";
$checked['finder_use_mobile'][$info_cfg['finder_use_mobile']] = " checked";

$spChr = array('��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��');

?>

<script type="text/javascript">
SMS = {
	insSpchr: function(str) {
		var obj = document.getElementById("el-auth-message");
		if (!obj) return;
		obj.value = obj.value + str.replace(/\s/g, "");
		SMS.chkLength();
	},
	chkLength: function() {
		var obj = document.getElementById('el-auth-message');
		var obj2 = document.getElementById('el-auth-message-length');
		var str = obj.value;
		obj2.value = chkByte(str);
		if (chkByte(str)>90) {
			obj2.style.color = "#FF0000";
	//		SMS.chkLength(obj);
		}
		else {
			obj2.style.color = "";
		}
	}
}
</script>


<form method="post" action="indb.info.php">
<input type="hidden" name="mode" value="finder_pwd">

<!-- e-mail �ּҷ� ���� �� ��߱� -->
<div class="title title_top">
	e-mail �ּҷ� ���� �� ��߱�
	<span>ȸ�������� ��ϵǾ� �ִ� e-mail �ּҷ� ���� �� ��й�ȣ�� ��߱� �մϴ�.</span>
	<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=31')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a>
</div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>��� ����</td>
	<td class="noline">
	<input type="radio" name="finder_use_email" value="1" checked /> <label for="info_email1">���</label>
	<!--input type="radio" name="finder_use_email" value="0" <?=$checked['finder_use_email'][0]?> /> <label for="info_email0">��� ����</label-->
	<br />
	<div class="extext_t">e-mail �ּҷ� ��й�ȣ ��߱� ���񽺴� �⺻���� ���� �˴ϴ�.</div>
	</td>
</tr>
</table>
<div class="extext_t">* <a href="../member/email.cfg.php?mode=11" style="font-weight:bold;" class="extext">[ ȸ������>�ڵ����ϼ���>�����ȣã�� ��������,  �����ȣ���� �ȳ����� ]</a> ���� ���� ������ �����Ͻ� �� �ֽ��ϴ�.</div>

<!-- �޴��� ��ȣ�� ���� �� ��߱� -->
<div class="title">
	�޴��� ��ȣ�� ���� �� ��߱�
	<span>ȸ�������� ��ϵǾ� �ִ� �޴��� ��ȣ�� ���� �� ��й�ȣ�� ��߱� �մϴ�.</span>
	<a href="javascript:manual('<?=$guideUrl?>board/view.php?id=basic&no=31')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a>
</div>

<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>��� ����</td>
	<td class="noline">
	<input type="radio" name="finder_use_mobile" value="1" <?=$checked['finder_use_mobile'][1]?> /> <label for="info_mobile1">���</label>
	<input type="radio" name="finder_use_mobile" value="0" <?=$checked['finder_use_mobile'][0]?> /> <label for="info_mobile0">��� ����</label>
	<br />
	<div class="extext_t">���� ��� ���θ� �����մϴ�. ������� ������ ��й�ȣ ã�� ȭ�鿡�� ���� ���� �޴��� �߰� �����˴ϴ�.</div>
	</td>
</tr>
<tr>
	<td>�ܿ� SMS ����Ʈ</td>
	<td class="noline">
		<div>
			<span style="font-weight:bold"><font class="ver9" color="0074ba"><b><?=number_format((int) getSmsPoint())?></b></span><font color="262626">��</font>
			<a href="javascript:location.href='../member/sms.pay.php';"><img src="../img/btn_smspoint.gif" align="absmiddle"></a>
		</div>
		<div class="extext_t">SMS����Ʈ�� ���� ��� '�޴��� ��ȣ�� ���� �� ��߱ޡ� ���񽺸� ������� �����ϼŵ� ���񽺰� �������� �ʽ��ϴ�. (��й�ȣ ã�� ȭ�鿡�� ���� �޴��� �߰����� �ʽ��ϴ�.)</div>
	</td>
</tr>
<tr>
	<td>������ȣ �߼�<br>�޼���
</td>
	<td class="noline">

		<table border="0" width="100%">
		<tr>
			<td>
			<table width="146" cellpadding="0" cellspacing="0" border="0">
			<tr><td><img src="../img/sms_top.gif" /></td></tr>
			<tr>
				<td background="../img/sms_bg.gif" align="center" height="81"><textarea name="finder_mobile_auth_message" id="el-auth-message" style="font:9pt ����ü;overflow:hidden;border:0;background-color:transparent;width:98px;height:74px;" onkeydown="SMS.chkLength();" onkeyup="SMS.chkLength();" onchange="SMS.chkLength();" required msgR="�޼����� �Է����ּ���"><?=$info_cfg['finder_mobile_auth_message']?></textarea></td>
			</tr>
			<tr><td height="31" background="../img/sms_bottom.gif" align="center"><font class="ver8" color="262626"><input id="el-auth-message-length" type="text" style="width:20px;text-align:right;border:0;font-size:8pt;font-style:verdana;" value="0">/90 Bytes</td></tr>
			</table>

			</td>
			<td style="vertical-align:top;padding-top:20px;">
			Ư������
			<div style="width:100%; border:1px solid #cccccc; background:#f7f7f7; padding:5px; margin:5px 0px 5px 0px;">
				<? foreach($spChr as $chr) { ?>
				<div style="float:left; border:1px solid #dddddd; width:20px; height:20px; background:#ffffff;" align="center" onClick="SMS.insSpchr(this.innerHTML);" class="hand" onmouseover="this.style.background='#FFC0FF'" onmouseout="this.style.background='#ffffff'"><?=$chr?></div>
				<? } ?>
			</div>

			<div class="extext_t">
			������ ��й�ȣã��� �߼� �޽��� [ȸ������>SMS����> �ڵ��߼�/����] �� ����� �����Ǹ� �� �̻� �߼۵��� �ʽ��ϴ�. <br>
			{shopName} �� ���� �̸��� ġȯ�ڵ�<br>
			{authNum} �� ������ȣ�� ġȯ�ڵ�<br>
			{shopName}�� {authNum}�� �����Ͽ� ��ȯ�� ������ ���̰� 90 Bytes�� ������ �޴� ��� ���� �޼��� ȭ�鿡�� �Ϻ� ������ �߷����� �� �ֽ��ϴ�.
			</div>

			</td>
		</tr>
		</table>
		<script type="text/javascript">SMS.chkLength();</script>

	</td>
</tr>
</table>

<div class="button">
	<input type="image" src="../img/btn_regist.gif">
	<a href="javascript:history.back();" onclick=";"><img src="../img/btn_cancel.gif" /></a>
</div>
</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle">��й�ȣ ã�⿡ �ʿ��� �پ��� ������ �������θ� ���� �� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�޴��� ��ȣ�� ��߱��ϱ� ���񽺸� ������� ���� �Ͻ� ���</td></tr>
<tr><td>&nbsp;&nbsp;SMS �ܿ� ����Ʈ�� Ȯ���Ͽ� �ּ���. ����Ʈ �ܿ��� ���� ���, ���񽺰� �������� �ʽ��ϴ�.</td></tr>
<tr><td>&nbsp;&nbsp;������ ��й�ȣã��� �߼� �޽��� <a href="../member/sms.auto.php" style="font-weight:bold;"><font color="#ffffff">[ȸ������>SMS����> �ڵ��߼�/����]</font></a> �� ����� �����Ǹ� �� �̻� �߼۵��� �ʽ��ϴ�.</td></tr>
</table>
</div>
<script>
	window.onload = function() {
		cssRound('MSG01');
	}
</script>
<? include "../_footer.php"; ?>