<?
include_once "../lib.php";

if (is_file("../../conf/config.stocked_noti.php")) include "../../conf/config.stocked_noti.php";
else {
	// �⺻ ���� ��
	$stocked_noti_cfg = array(
		'msg' => '[{shopName}]
{goodsnm}- {goodsopt} ���԰� �Ǿ����ϴ�',
		'short_name' => false
		);
	$stocked_noti_cfg['msgOpt'] = "fix";
}


$spChr = array('��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��');
?>
<script type="text/javascript">
SMS = {
	insSpchr: function(str) {
		var obj = document.getElementById("stockedSMS");
		if (!obj) return;
		obj.value = obj.value + str.replace(/\s/g, "");
		SMS.chkLength();
	},
	chkLength: function() {
		var obj = document.getElementById('stockedSMS');
		var obj2 = document.getElementById('stockedSMSLen');
		var str = obj.value;
		obj2.value = chkByte(str);
		if (chkByte(str)>90) {
			obj2.style.color = "#FF0000";
	//		SMS.chkLength(obj);
		}
		else {
			obj2.style.color = "";
		}
	},
	chkForm: function(fobj) {
		if (!fobj.smsMsg.value) {
			alert("�޼����� �Է��ϼ���.");
			fobj.smsMsg.focus();
			return false;
		}
		if (!fobj.smsCallback.value) {
			alert("�޼����� �Է��ϼ���.");
			fobj.smsCallback.focus();
			return false;
		}
	}
}
</script>
<? if(!$popup){ ?>
<form name="frmStockedNotiConfig" method="post" action="./indb.stocked_noti_config.php" target="ifrmHidden">
<? } ?>
	<table border="0" width="100%">
	<tr>
		<td>
		<table width="146" cellpadding="0" cellspacing="0" border="0">
		<tr><td><img src="../img/sms_top.gif" /></td></tr>
		<tr>
			<td background="../img/sms_bg.gif" align="center" height="81"><textarea name="msg" id="stockedSMS" style="font:9pt ����ü;overflow:hidden;border:0;background-color:transparent;width:98px;height:74px;" onkeydown="SMS.chkLength();" onkeyup="SMS.chkLength();" onchange="SMS.chkLength();" required msgR="�޼����� �Է����ּ���"><?=$stocked_noti_cfg['msg']?></textarea></td>
		</tr>
		<tr><td height="31" background="../img/sms_bottom.gif" align="center"><font class="ver8" color="262626"><input name="stockedSMSLen" id="stockedSMSLen" type="text" style="width:20px;text-align:right;border:0;font-size:8pt;font-style:verdana;" value="0">/90 Bytes</td></tr>
		</table>

		</td>
		<td style="vertical-align:top;padding-top:20px;">
		Ư������
		<div style="width:100%; border:1px solid #cccccc; background:#f7f7f7; padding:5px; margin:5px 0px 5px 0px;">
			<? foreach($spChr as $chr) { ?>
			<div style="float:left; border:1px solid #dddddd; width:20px; height:20px; background:#ffffff;" align="center" onClick="SMS.insSpchr(this.innerHTML);" class="hand" onmouseover="this.style.background='#FFC0FF'" onmouseout="this.style.background='#ffffff'"><?=$chr?></div>
			<? } ?>
		</div>
		<div style="clear:both">���԰�˸� SMS�߼۽�, ����Ͻ� �޽��� ������ �ڵ����� �ԷµǾ� �߼۵˴ϴ�.</div>
		<strong>ġȯ�ڵ� �ȳ�</strong>
		<table class="tb">
			<tr>
				<td>������ : {shopName}</td>
				<td>ȸ���� : {name}</td>
				<td>��ǰ�� : {goodsnm}</td>
				<td>��ǰ�ɼǸ� : {goodsopt}</td>
			</tr>
		</table>
		</td>
	</tr>
	</table>
	<table class="tb">
		<col class="cellC" style="width:150px"><col class="cellL">
		<col class="cellC"><col class="cellL">
		<tr>
			<td rowspan="2">�޽��� ���� �ɼ�</td>
			<td class="noline">
				<p><input type="radio" name="msgOpt" value="fix" id="fix" <? if($stocked_noti_cfg['msgOpt'] == "fix"){ echo "checked"; } ?> /><label for="fix">�ܹ�(90byte) ����</label><br /><span style="color:#6D6D6D; margin-left: 20px">���۵Ǵ� ������ �� ���̰� 90Bytes�� �ʰ���, ��ǰ��� �ɼǸ��� ���� 10Bytes�� ��ҵǸ�</span><br /><span style="color:#6D6D6D; margin-left: 20px">��ҵ� ���� ���� 90Bytes �ʰ� ������ �߷��� ���� �˴ϴ�.</span></p>
				<p style="margin-top:-10px"><input type="checkbox" name="shortGoodsNm" value="y" id="shortGoodsNm" style="margin-left:50px;" <? if($stocked_noti_cfg['shortGoodsNm'] == "y"){ echo "checked"; } ?> /><label for="shortGoodsNm">��ǰ���� ª�� ǥ��</label><br /><span style="color:#6D6D6D; margin-left: 70px">90Bytes ���ؿ� ���߾� ��ǰ��� �ɼǸ��� �ּ� �� ���ڱ��� ��ҵǾ� ���۵� �� �ֽ��ϴ�.</span></p>
			</td>
		</tr>
		<tr>
			<td class="noline">
				<p><input type="radio" name="msgOpt" value="separate" id="separate" <? if($stocked_noti_cfg['msgOpt'] == "separate"){ echo "checked"; } ?> /><label for="separate">�幮(90byte �̻�) ��������</label><br /><span style="color:#6D6D6D; margin-left: 20px">90Bytes �̻��� ��� SMS �߼۰Ǽ��� 2�� �̻����� ������ ���۵˴ϴ�.</span><br /><span style="color:#6D6D6D; margin-left: 20px">�幮(90Bytes �̻�) ������������ �����Ͽ��� 90Bytes�� ���� ���� ���, 1�� ���� �߼۵˴ϴ�.</span></p>
			</td>
		</tr>
	</table>
	<script type="text/javascript">SMS.chkLength();</script>
<? if(!$popup){ ?>
	<div class="button">
		<input type=image src="../img/btn_register.gif">
		<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
	</div>
</form>
<? } ?>
