<?
$location = "�����̼� > SMS ����";
include "../_header.php";

$todayShop = &load_class('todayshop', 'todayshop');

if (!$todayShop->auth()) {
	msg(' ���� ��û�ȳ��� ���� �����ͷ� �������ֽñ� �ٶ��ϴ�.', -1);
}


$tsCfg = $todayShop->cfg;

$arTitle['orderc'] = array('title'=>'������ǰ �ֹ��Ϸ�� �ڵ��߼�', 'desc'=>'(���� �Ϸ�� �߼۵Ǵ� �޽����Դϴ�.)');
$arTitle['salec'] = array('title'=>'������ǰ �Ǹż����� �ڵ��߼�', 'desc'=>'(�ǸŰ� �����Ǹ� �߼۵˴ϴ�.)');
$arTitle['giftc'] = array('title'=>'������ǰ �Ǹż����� �ڵ��߼�(�����ϱ�)', 'desc'=>'(������ �޴� ������� �߼۵˴ϴ�.)');
$arTitle['orderg'] = array('title'=>'�ǹ���ǰ �ֹ��Ϸ�� �ڵ��߼�', 'desc'=>'(���� �Ϸ�� �߼۵Ǵ� �޽����Դϴ�.)');
$arTitle['deliveryg'] = array('title'=>'�ǹ���ǰ ��۽� �ڵ��߼�', 'desc'=>'(�ǸŰ� �����ǰ� ���°� ��������� �ٲ� �� �߼۵Ǵ� �޼����Դϴ�.)');
$arTitle['cancel'] = array('title'=>'�ǸŽ��н� �ڵ��߼�', 'desc'=>'(��ǥ���ŷ��� �������� ���� ��� ���� ��� �޽��� �Դϴ�.)');

$spChr = array('��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��','��');

// SMS ����Ʈ ��������
$sms = &load_class('sms', 'sms');
$smsPt = preg_replace('/[^0-9-]*/', '', $sms->smsPt);
unset($sms);
?>
<style type="text/css">
img {border:none;}
</style>
<script type="text/javascript">
SMS = {
	insSpchr: function(n, str) {
		var obj = document.getElementById("smsMsg_" + n);
		if (!obj) return;
		obj.value = obj.value + str.replace(/\s/g, "");
		SMS.chkLength(n);
	},
	chkLength: function(n) {
		var obj = document.getElementById('smsMsg_'+n);
		var obj2 = document.getElementById('vLength_'+n);
		var str = obj.value;
		obj2.value = chkByte(str);
		if (chkByte(str)>80) {
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

<div style="width:100%">
	<form name="frmSMS" method="post" action="indb.sms_config.php" onsubmit="return SMS.chkForm(this);" target="ifrmHidden" />
		<div class="title title_top">SMS ���� <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=todayshop&no=7')"><img src="../img/btn_q.gif"></a></div>
		<div>���� SMS �ܿ�����Ʈ�� <b><?=number_format($smsPt)?>Point</b>�Դϴ�. <a href="../member/sms.pay.php" target="_blank"><font class=extext_l>[SMS ����Ʈ ���� �ٷΰ���]</font></a></div>
		<?
		foreach($arTitle as $key => $val) {
		?>
		<div style="padding:10px;">
			<div style="color:#0074ba; font-weight:bold;"><?=$val['title']?> <font class="small1"><?=$val['desc']?></font></div>
			<div style="width:90%; border:1px solid #cccccc; background:#f7f7f7; padding:5px; margin:5px 0px 5px 0px;">
				<? foreach($spChr as $chr) { ?>
				<div style="float:left; border:1px solid #dddddd; width:20px; height:20px; background:#ffffff;" align="center" onClick="SMS.insSpchr('<?=$key?>', this.innerHTML);" class="hand" onmouseover="this.style.background='#FFC0FF'" onmouseout="this.style.background='#ffffff'"><?=$chr?></div>
				<? } ?>
			</div>
			<div><textarea id="smsMsg_<?=$key?>" name="smsMsg_<?=$key?>" style="font:9pt ����ü;height:74px; width:90%;" onkeydown="SMS.chkLength('<?=$key?>');" onkeyup="SMS.chkLength('<?=$key?>');" onchange="SMS.chkLength('<?=$key?>');"><?=$tsCfg['smsMsg_'.$key]?></textarea></div>
			<div class="noline">
				<input type="checkbox" name="smsUse_<?=$key?>" value="y" <? if ($tsCfg['smsUse_'.$key]=='y') {?>checked="checked"<?}?> /> ������ �ڵ��߼�
				<font class="ver8" color="262626"><input id="vLength_<?=$key?>" type="text" style="width:40px;text-align:right;font-size:8pt;font-style:verdana;border:solid 1px;" value="0"> Bytes
			</div>
			<script type="text/javascript">SMS.chkLength('<?=$key?>');</script>
		</div>
		<? } ?>
		<div class="button">
			<input type=image src="../img/btn_register.gif">
			<a href="javascript:history.back()"><img src="../img/btn_cancel.gif"></a>
		</div>
		<div style="padding-top:15px"></div>
	</form>

	<div style="padding:10px;">
		<div style="color:#0074ba; font-weight:bold;">ġȯ�ڵ�</div>
		<table width=500 cellpadding=0 cellspacing=0 border=0>
		<tr><td class=rnd colSpan=4></td></tr>
		<tr class=rndbg>
			<th>ġȯ�ڵ��</th>
			<th>����</th>
			<th>ġȯ�ڵ��</th>
			<th>����</th>
		</tr>
		<tr><td class=rnd colSpan=4></td></tr>
		<tr><td height=4 colSpan=4></td></tr>
		<tr align="center">
			<td>{=shopName}</td>
			<td>���θ� ��</td>
			<td>{=memo}</td>
			<td>�޸�</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colSpan=4 class=rndline></td></tr>
		<tr><td height=4 colSpan=4></td></tr>
		<tr align="center">
			<td>{=goodsnm}</td>
			<td>��ǰ��</td>
			<td>{=nameOrder}</td>
			<td>�ֹ��ڸ�</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colSpan=4 class=rndline></td></tr>
		<tr><td height=4 colSpan=4></td></tr>
		<tr align="center">
			<td>{=couponNo}</td>
			<td>������ȣ</td>
			<td>{=deliverycomp}</td>
			<td>�ù��</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colSpan=4 class=rndline></td></tr>
		<tr><td height=4 colSpan=4></td></tr>
		<tr align="center">
			<td>{=option}</td>
			<td>�ɼ�����</td>
			<td>{=deliverycode}</td>
			<td>�����ȣ</td>
		</tr>
		<tr><td height=4></td></tr>
		<tr><td colSpan=4 class=rndline></td></tr>
		<tr><td height=4 colSpan=4></td></tr>
		<tr align="center">
			<td>{=usedt}</td>
			<td>��ȿ�Ⱓ</td>
			<td></td>
			<td></td>
		</tr>
		</table>
	</div>
</div>

<div style="margin-top:20px"></div>

<div style="clear:both;" id=MSG01>
	<table cellpadding=1 cellspacing=0 border=0 class="small_ex">
	<tr>
		<td>
			<div>������ǰ ������ �߼۵Ǵ� �ڵ� SMS�Դϴ�.</div>
			<div>�ֹ��Ϸ�� ������ȣ������ �߼۵Ǹ�, ��ǥ���� �޼����� ���� ��� ��� SMS�� �߼��մϴ�.
			<div>�Ϲݼ��θ��� �ߺ��Ǵ� ������ SMS ������ <a href="../member/sms.auto.php" target="_blank" style="color:#0074ba;">ȸ��/SMS/EMAIL>SMS����>SMS�ڵ��߼�/����</a> �޴����� �����մϴ�.</div>
		</td>
	</tr>
	</table>
</div>

<script type="text/javascript">
	cssRound('MSG01');
</script>
<? include "../_footer.php"; ?>