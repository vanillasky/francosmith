<?
include "../_header.popup.php";

$mode = ($_GET['mode']) ? trim($_GET['mode']) : "";

switch($mode) {
	case "qnaReply":
		$db_table = GD_GOODS_QNA;
		$type = "qna";
		$title = "��ǰ����";

		break;

	case "reviewReply":
		$db_table = GD_GOODS_REVIEW;
		$type = "review";
		$title = "��ǰ�ı�";

		break;

	case "memberQnaReply":
		$db_table = GD_MEMBER_QNA;
		$type = "memberQna";
		$title = "1:1 ����";

		break;

	default :
		echo "<script>alert('�߸��� �����Դϴ�.');self.close();</script>";
		break;
}

if($cfg[$type.'FavoriteReplyUse'] == "y" && $cfg[$type.'FavoriteReplyNo']) list($rdata['subject'], $rdata['contents']) = $db->fetch("SELECT subject, contents FROM ".GD_GOODS_FAVORITE_REPLY." WHERE sno = '".$cfg[$type.'FavoriteReplyNo']."' AND customerType = '$type'");

$data = $db->fetch("SELECT C.*, MB.m_id, MB.dormant_regDate FROM ".$db_table." AS C LEFT JOIN ".GD_MEMBER." AS MB ON C.m_no = MB.m_no where C.sno='" . $_GET['sno'] . "'", 1);
$checked['secret'] = "";
if($data['secret'])$checked['secret'] = " checked";

$query = "SELECT b.goodsnm, b.img_s, c.price FROM ".GD_GOODS." b LEFT JOIN ".GD_GOODS_OPTION." c ON b.goodsno = c.goodsno AND link and go_is_deleted <> '1' and go_is_display = '1' WHERE b.goodsno = '" . $data['goodsno'] . "'";
list( $data['goodsnm'], $data['img_s'], $data['price'] ) = $db->fetch($query);
if($mode != 'qnaReply') $data['contents'] = nl2br($data['contents']);
?>
<script>
	function fnOpenSMSSelector(el) {

		if(el.checked == true) {
			var fname = 'form';
			var fld = 'sms';
			var mode = 'popup';
			var mobile = document.form.phone.value;

			popup('../member/sms_selector.php?mode=' + mode + '&fname=' + fname + '&fld=' + fld + '&mobile=' + mobile, 800, 600);
		}
		else {
			document.form.sms.value = '';
			document.getElementById('sms_ready').style.display = 'none';
		}
	}

	//
	function fnToggleSms(s) {
		var f = document.form;

		if(f.sms.value != '' && f.snd_sms.checked == true) {
			document.getElementById('sms_ready').style.display = 'inline';
		}
	}

	// SMS ���� ����
	function chkLength(obj) {
		str = obj.value;
		document.getElementById('vLength').innerHTML = chkByte(str);
		if(chkByte(str) > 90) {
			alert("90byte������ �Է��� �����մϴ�");
			obj.value = strCut(str, 90);
		}
	}
</script>
<form name=form method=post action="<?=$sitelink->link('admin/board/customer_indb.php','ssl');?>" onsubmit="return chkForm(this)">
<input type=hidden name=mode value="<?=$mode?>">
<input type=hidden name=sno value="<?=$_GET['sno']?>">
<input type=hidden name=goodsno value="<?=$data['goodsno']?>">
<input type=hidden name='page' value="<?=$_GET['page']?>">
<input type=hidden name='sms' value="">

<div class="title title_top"><?=$title?></div>

<table class="tb">
	<col class="cellC"><col class="cellL">
<? if($mode != "memberQnaReply") { ?>
	<tr height="26">
		<td>��ǰ</td>
		<td>
			<?php if ($data['goodsno']) { ?>
			<div style="float:left"><?=goodsimg($data['img_s'], 40, "style='border:1px solid #EFEFEF; margin-right:10px;'", 1)?></div>
			<div style="float:left; color:#0074BA;" class="def"><?=$data['goodsnm']?></div>
			<?php } else { ?>
			��������
			<?php } ?>
		</td>
	</tr>
<? } ?>

	<tr>
		<td>�ۼ���</td>
		<td><?=$data['m_id']?> <?php if($data['m_id'] && $data['dormant_regDate'] != '0000-00-00 00:00:00'){ ?>(�޸�ȸ��)<?php } ?></td>
	</tr>

	<tr>
		<td>�ۼ���</td>
		<td><font class="ver8"><?=$data['regdt']?> &nbsp;&nbsp;IP: (<?=$data['ip']?>)</td>
	</tr>

<?
	if($mode == 'memberQnaReply') {
		$itemcds = codeitem( 'question' ); # ��������
?>
	<tr>
		<td>��������</td>
		<td><font class="ver8"><?=$itemcds[$data['itemcd']]?></td>
	</tr>
	<tr>
		<td>�ֹ���ȣ</td>
		<td><a href="javascript:popup('../order/popup.order.php?ordno=<?=$data['ordno']?>',800,600)"><font class="ver8"><?=$data['ordno']?></font></a></td>
	</tr>
	<? if($data['email']) { ?>
	<tr>
		<td>�̸���</td>
		<td><font class="ver8"><?=$data['email']?></td>
	</tr>
	<? } ?>
	<? if($data['mobile'] != "--" && $data['mobile']) { ?>
	<tr>
		<td>Mobile</td>
		<td><font class="ver8"><?=($data['mobile'] != "--" && $data['mobile']) ? $data['mobile'] : ""?></td>
	</tr>
	<? } ?>
<? } ?>

<? if($mode == 'reviewReply') { ?>
	<tr>
		<td>���޵� ������</td>
		<td><font class="ver8" color="EF6D00"><span style="margin-right:10;"><?=number_format($data['emoney'])?> ��</span></td>
	</tr>
	<tr>
		<td>����</td>
		<td><?=str_repeat( "��", $data['point'] )?></td>
	</tr>
<? } ?>

	<tr>
		<td>����</td>
		<td><?=$data['subject']?></td>
	</tr>
	<tr>
		<td>����</td>
		<td class="editorArea"><?=$data['contents']?></td>
	</tr>
</table>

<div class="title title_top" style="margin-top:10px;">�亯</div>

<table class="tb">
	<col class="cellC"><col class="cellL">
	<tr>
		<td>�ۼ���</td>
		<td>
			<select name="m_no">
<?
	$res = $db->query( "SELECT m_no, m_id, name FROM ".GD_MEMBER." WHERE m_id != 'godomall' AND level = 100 ORDER BY m_id" );
	while( $row = $db->fetch( $res ) ){
?>
				<option value="<?=$row['m_no']?>"><?=$row['m_id']." [".$row['name']."]"?></option>';
<? } ?>
			</select>
		</td>
	</tr>
<? if($data['sno'] == $data['parent'] && $mode == "reviewReply" && $data['dormant_regDate'] == '0000-00-00 00:00:00') { ?>
	<tr>
		<td>��������������</td>
		<td>
		<select name="memo" required fld_esssential label="��������" onchange="openLayer('direct', (this.value=='direct' ? 'block' : 'none') )" style="float:left;">
			<option value="">- �����ϼ��� -</option>
<?
			foreach(codeitem('point') as $v) {
				$selected = "";
				if($v == "��ǰ�ı� �ۼ� ����Ʈ ����") $selected = "selected";
				echo "<option value=\"$v\" $selected>$v</option>\n";
			}
?>
			<option value="direct">�� �����Է�</option>
		</select>
		<div id="direct" style="display:none;"><input type="text" name="direct_memo" size="30" /></div>
		</td>
	</tr>
	<tr>
		<td>����������</td>
		<td>
		<input type="hidden" name="emoneyPut" value="Y" />
		<input type="hidden" name="writer_m_no" value="<?=$data['m_no']?>" />
		<input type="text" name="emoney" value="0" size="6" class="rline" onkeydown="onlynumber();" />��
		�� <? echo $data['name'] . "[".$data['m_id']."]";?> ȸ������ ����
		</td>
	</tr>
<?
	}

	if ($data['m_no'] && $mode == "reviewReply"  && $data['dormant_regDate'] == '0000-00-00 00:00:00') {
		if( getSmsPoint() < 1) $disabled = "disabled";
		list($data['name'], $data['mobile']) = $db->fetch("SELECT name, mobile FROM ".GD_MEMBER." WHERE m_no = '".$data['m_no']."'");
?>
	<tr>
		<td>SMS ����<br /><font class="small1">[�ܿ��Ǽ� <span id="span_sms" style="font-weight:bold"><font class="ver9" color="0074ba"><b><?=number_format(getSmsPoint())?></b></font></span><font color="262626">��</font>]</font></td>
		<td>
		<div class="noline"><input type="checkbox" name="smsSendYN" value="Y" <?=$disabled?> /> üũ�� <? echo $data['name'] . "[".$data['m_id']."]";?> ȸ������ SMS ����</div>
		<div>
		<input type="hidden" name="type" value="1" />
		<input type="hidden" name="name" value="<?=$data['name']?>" />
		<input type="hidden" name="phone" value="<?=str_replace("-","",$data['mobile'])?>" />
		<input type="hidden" name="callback" value="<?=str_replace("-","",$cfg['smsRecall'])?>" />
		<input type="text" name="msg" value="" style="width:80%;" class="line" onkeydown="chkLength(this);" onkeyup="chkLength(this);" onchange="chkLength(this);" <?=$disabled?> />
		<span id="vLength">0</span>/90 Bytes
		</div>
		</td>
	</tr>
<? } ?>

<? if($data['rcv_email'] && $data['email']) { ?>
	<tr>
		<td>�̸���</td>
		<td><label class="noline"><input type="checkbox" name="snd_email" value="1"> ������ �亯������ ���ÿ� �����Ͻðڽ��ϱ�?</label></td>
	</tr>
<? } ?>

<? if($data['rcv_sms'] && (($data['mobile'] != "--" && $data['mobile']) || $data['phone'])) { ?>
	<tr>
		<td>���ڸ޽���</td>
		<td><label class="noline"><input type="checkbox" name="snd_sms" value="1" onClick="fnOpenSMSSelector(this);"> üũ�� <?=$data['name']?>[<?=$data['m_id'] ? $data['m_id'] : '��ȸ��' ?>] �Կ��� �亯SMS�� ���ÿ� �����Ͻðڽ��ϱ�?</label>
		<img src="../img/icon_sms_ready.gif" align="absmiddle" id="sms_ready" style="display:">
		<input type="hidden" name="phone" value="<?=$data['phone']?>">
		</td>
	</tr>
<? } ?>

<?
	if($mode == "memberQnaReply") {
		if(!$data['email'] || $data['mailling'] != "y") $disabled['mailling'] = "disabled";
		if(!str_replace("-", "", $data['mobile']) || $data['sms'] != "y") $disabled['sms'] = "disabled";
?>
	<? if($data['email'] && ($data['dormant_regDate'] == '0000-00-00 00:00:00' || !$data['dormant_regDate'])) { ?>
	<tr>
		<td>�̸���</td>
		<td><label class="noline"><input type="checkbox" name="mailyn" value="Y" <?=$disabled['mailling']?> /> ������ �亯������ ���ÿ� �����Ͻðڽ��ϱ�?</label></td>
	</tr>
	<? } ?>
	<? if($data['mobile'] != "--" && $data['mobile'] && ($data['dormant_regDate'] == '0000-00-00 00:00:00' || !$data['dormant_regDate'])) { ?>
	<tr>
		<td>SMS����</td>
		<td><label class="noline"><input type="checkbox" name="smsyn" value="Y" <?=$disabled['sms']?> /> ������ �亯SMS�� ���ÿ� �����Ͻðڽ��ϱ�?</label></td>
	</tr>
	<? } ?>
<? } ?>

<? if($cfg['qnaSecret'] && $mode == "qnaReply") { ?>
	<tr>
		<td class="input_txt">��б�</td>
		<td class="noline"><input type="checkbox" name="secret" value="1"<?=$checked['secret']?>> ��б�</td>
	</tr>
<? } ?>

	<tr>
		<td>����</td>
		<td><input type="text" name="subject" id="subject" style="width:70%;" class="line" value="<?=$rdata['subject']?>" required fld_esssential label="����"> <a href="javascript:popup2('../board/customer_reply.php?type=<?=$type?>&selType=rForm',800,800,1)"><img src="../img/icon_repeatqna.gif" align="absmiddle" /></a></td>
	</tr>
	<tr>
		<td>����</td>
		<td>
<? if($mode == "qnaReply") { ?>
		<textarea name="contents" id="contents" style="width:550px;height:250px" type="editor" fld_esssential label="����"><?=$rdata['contents']?></textarea>
		<script src="../../lib/meditor/mini_editor.js"></script>
		<script>mini_editor("../../lib/meditor/")</script>
<? } else { ?>
		<textarea name="contents" id="contents" style="width:550px;height:250px" required fld_esssential label="����"><?=$rdata['contents']?></textarea>
<? } ?>
		</td>
	</tr>
</table>

<div class="button_popup">
	<input type="image" src="../img/btn_confirm_s.gif">
	<a href="javascript:window.close()"><img src="../img/btn_cancel_s.gif"></a>
</div>

</form>

<script>
	linecss();
	table_design_load();
</script>