<?

$location = "ȸ������ > ȸ�����԰���";
include "../_header.php";
include "../../conf/fieldset.php";
@include "../../conf/mobile_fieldset.php";

$fld	= array(
	'def'	=> array(
		"m_id"			=> "���̵�",
		"password"		=> "��й�ȣ",
		"name"			=> "�̸�",
	),
	'per'	=> array(
		"nickname"		=> "�г���",
		"email"			=> "�̸���",
		"resno"			=> "�ֹε�Ϲ�ȣ",
		"sex"			=> "����",
		"birth"			=> "�������",
		"calendar"		=> "��/����",
		"address"		=> "�ּ�",
		"phone"			=> "��ȭ��ȣ",
		"mobile"		=> "�ڵ���",
		"fax"			=> "�ѽ���ȣ",
		"company"		=> "ȸ���",
		"service"		=> "����",
		"item"			=> "����",
		"busino"		=> "����ڹ�ȣ",
		"mailling"		=> "���ϸ�",
		"sms"			=> "SMS ����",
		"marriyn"		=> "��ȥ����",
		"marridate"		=> "��ȥ�����",
		"job"			=> "����",
		"interest"		=> "���ɺо�",
		"memo"			=> "����¸���",
		"recommid"		=> "��õ��",
		"ex1"			=> "�߰�1",
		"ex2"			=> "�߰�2",
		"ex3"			=> "�߰�3",
		"ex4"			=> "�߰�4",
		"ex5"			=> "�߰�5",
		"ex6"			=> "�߰�6",
	),
);

// 20130508 �� �ֹε�Ϲ�ȣ �������
if (date('Ymd') >= 20130508 && $checked['useField']['resno'] == '' && $checked_mobile['useField']['resno'] == '') {
	unset($fld['per']['resno']);
}
?>

<script>

function chkBox2(El,mode,mode2)
{
	if (!El) return;
	for (i=0;i<El.length;i++){
		El[i].checked = (mode=='rev') ? !El[i].checked : mode;
		chk(El[i].key,mode2);
	}
}

function chk(obj,mode)
{
	var objUse = document.frmField["useField["+obj+"]"];
	var objReq = document.frmField["reqField["+obj+"]"];
	if (objReq.checked && mode=='req') objUse.checked = true;
	else if (objUse.checked==false && mode=='use') objReq.checked = false;
}

function chkBox2_mobile(El,mode,mode2)
{
	if (!El) return;
	for (i=0;i<El.length;i++){
		El[i].checked = (mode=='rev') ? !El[i].checked : mode;
		chk_mobile(El[i].key,mode2);
	}
}

function chk_mobile(obj,mode)
{
	var objUse = document.frmField["useMobileField["+obj+"]"];
	var objReq = document.frmField["reqMobileField["+obj+"]"];
	if (objReq.checked && mode=='req') objUse.checked = true;
	else if (objUse.checked==false && mode=='use') objReq.checked = false;
}
</script>

<form name="frmField" method="post" action="indb.php">
<input type="hidden" name="mode" value="fieldset" />

<div class="title title_top">ȸ������ ��å����<span>ȸ�����Կ� �ʿ��� ���� ��å�� ���մϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=3')"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>
<table class="tb">
<col class="cellC"><col class="cellL">
<tr>
	<td>ȸ����������</td>
	<td class="noline">

	<div style="margin:5px 0px">
	<input type="radio" name="status" value="1" <?=( $joinset['status'] == '1' ? 'checked' : '' )?> />������������&nbsp;
	<input type="radio" name="status" value="0" <?=( $joinset['status'] != '1' ? 'checked' : '' )?> />������ ���� �� ����	<font class="extext">(������ ���� �� ����ó���� �� �ֽ��ϴ�)</font>
	</div>

	<div style="margin:5px 2px">
	<span class="extext">* ���̹� üũ�ƿ� �ΰ����񽺸� �̿����� ��� ȸ������������ ����Ͻ� �� �����ϴ�.</span>
	</div>

	</td>
</tr>
<tr>
	<td>ȸ���簡�ԱⰣ</td>
	<td>
	<div style="padding-top:5"></div>
	ȸ��Ż�� �� ȸ������ �� <input type="text" name="rejoin" value="<?=$joinset['rejoin']?>" size="4" class="rline" /> �� ���� �簡���� �� �����ϴ�

	<div style="padding-top:5"></div>

	<table cellpadding="0" cellspacing="0" border="0">
	<tr><td height="5"></td></tr>
	<tr><td><font class="extext">ȸ�� �簡�� �Ⱓ�� ������ �ʿ��� ��� �ݵ�� ����Ȯ������ ������ �����Ͽ��� �մϴ�.
  </font></td></tr>
	<tr><td style="padding: 2px 0px 0px 0px"><a href="realname_info.php"><font class="extext">[<u><b>������ �ٷΰ���</b></u>]</a><br/><a href="adm_member_auth.hpauthDream.info.php"><font class="extext">[<u><b>�޴����������� �ٷΰ���</b></u>]</font></a></td></tr>
	<tr><td height="5"></td></tr>
	</table>

	<div style="padding-top:5"></div>
	</td>
</tr>
<tr>
	<td>���ԺҰ� ID</td>
	<td>
	<textarea name="unableid" style="width:100%;height:60px" class="tline"><?=$joinset['unableid']?></textarea>

	<table cellpadding="0" cellspacing="0" border="0">
	<tr><td height="5"></td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><font class="extext">ȸ�������� ������ ID�� �Է��ϼ���. �ĸ��� �����մϴ�</font></td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" /><font class="extext">�ֿ� ���� ID : </font><font class=ver7 color=627dce>admin,administration,administrator,master,webmaster,manage,manager</font></td></tr>
	<tr><td height="5"></td></tr>
	</table>

	</td>
</tr>
<tr>
	<td width=125>ȸ�����Խ� ����������</td>
	<td><input type="text" name="emoney" value="<?=$joinset['emoney']?>" size="10" class="rline" onkeydown="onlynumber();" /> �� <font class="extext">(������� 0 ���� �Է�)</font></td>
</tr>
<tr>
	<td>ȸ�����Խ� ��������</td>
	<td>ȸ������������ �����ϰ� �ʹٸ� <a href="../event/coupon_register.php" target=_blank><font class=extext_l>[���������]</font></a> ���� ������ �����ϼ���. 'ȸ�������ڵ��߱�' ������� �߱��ϼ���</td>
</tr>
<tr>
	<td>���Խ� ȸ���׷�</td>
	<td>ȸ������ �� �ٷ� <select name="grp">
<?
foreach( member_grp() as $v ){
	echo '<option value="' . $v['level'] . '"' . ( $joinset['grp'] == $v['level'] ? 'selected' : '' ) . '>' . $v['grpnm'] . ' - lv[' . $v['level'] . ']</option>' . "\n";
}
?>
	</select> �׷쿡 ���ϵ��� �մϴ�. <font class="extext">('ȸ���׷����'���� �׷��� ���弼��) &nbsp;<a href="../member/group.php" target="_new"><font class="extext_l">[�׷�����ٷΰ���]</font></a></td>
</tr>
<tr>
	<td>��õ�� ����</td>
	<td>
	  <div>�ű԰��԰��� ������ ��õ�ο��� ������ <input type="text" name="recomm_emoney" value="<?=$joinset['recomm_emoney']?>" size="10" class="rline" onkeydown="onlynumber();" /> �� ���� <font class="extext">(������� 0 ���� �Է�)</font></div>
	  <div>�ű԰��Խ� ��õ���� �����ϸ� ������ <input type="text" name="recomm_add_emoney" value="<?=$joinset['recomm_add_emoney']?>" size="10" class="rline" onkeydown="onlynumber();" /> �� �߰� ���� <font class="extext">(������� 0 ���� �Է�)</font></div>

	</td>
</tr>
<tr>
	<td>��14�� �̸� ���� ����</td>
	<td class="noline">

	<div style="margin:5px 0px">
	<input type="radio" name="under14status" value="1" <?=( $joinset['under14status'] == '1' ? 'checked' : '' )?> />������ ���� �� ����&nbsp;
	<input type="radio" name="under14status" value="0" <?=( in_array($joinset['under14status'], array(1,2)) !== true ? 'checked' : '' )?> />���ξ��� ����&nbsp;
	<input type="radio" name="under14status" value="2" <?=( $joinset['under14status'] == '2' ? 'checked' : '' )?> />���ԺҰ�
	</div>

	<div style="margin:5px 2px" class="extext">
		<div><img src="../img/icon_list.gif" align="absmiddle" />������Ÿ��� ��31�� ��1�׿� ���� ��14�� �̸��� �Ƶ��� �����븮���� ���Ǹ� Ȯ�� �� ȸ������ �� �� �ֽ��ϴ�.
			<a href="http://www.law.go.kr/lsInfoP.do?lsiSeq=111970#0000" target="_blank" class="extext_l">[���ù��� ���� ����]</a>
		</div>
		<div><img src="../img/icon_list.gif" align="absmiddle" />'������ ���� �� ����'���� ���� �� ��14�� �̸��� �Ƶ��� ȸ������ �� ��� '�̽���'���·� ���ԵǹǷ� �����븮�� ���Ǹ� Ȯ�� ��
			<a href="../member/list.php" class="extext_l">[ȸ������Ʈ]</a> Ȥ�� <a href="../member/batch.php?func=status" class="extext_l">[ȸ�����λ��� �ϰ�����]</a> �޴��� ���� <br>
			&nbsp;&nbsp;&nbsp; ���� ���¸� ������ �ֽñ� �ٶ��ϴ�. <a href="http://guide.godo.co.kr/guide/doc/��14���̸�ȸ�����Ե��Ǽ�(����).docx" target="_blank" class="extext_l">[�����븮�� ���Ǽ� ���� �ٿ�ޱ�]</a>
		</div>
		<div><img src="../img/icon_list.gif" align="absmiddle" />'���ԺҰ�'�� �����ϸ� ��14�� �̸��� ȸ�������� �� �� �����ϴ�.</div>
		<div class="extext">
		�� '������ ���� �� ����' �� '���ԺҰ�'�� ���� �� <u>�������������� ����</u>�Ǿ� �־�� �ϸ�, ������������ �� ��� �ÿ��� <u>'�������'�� �ʼ��� �Է�</u> �����ž� �մϴ�.<br/>
		&nbsp;&nbsp;&nbsp;&nbsp; ������������ �Ǵ� ������� �ʼ� ������ ���� ��� ��14�� �̸� ȸ���� �Ǵ��� �� �����Ƿ� <u class="red">'�̽���'���·� ���Եǰų�(������ ���� �� ���� ���� ��), ������ �ȵǿ���(���ԺҰ� ���� ��) ����</u>���ֽñ� �ٶ��ϴ�.<br/>
		&nbsp;&nbsp;&nbsp;&nbsp; �����������ܼ��� �ٷΰ��� : <a href="../member/adm_member_auth.hpauth.php" class="extext_l">[�޴�������Ȯ�ΰ���]</a> <a href="../member/ipin_new.php" class="extext_l">[�����ɰ���]</a>
		</div>
	</div>
	</td>
</tr>
</table>




<div class="title">ȸ������ �׸����<span>ȸ�����Կ� �ʿ��� ���� �׸� �� �ɼ��� ���մϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=3')"><img src="../img/btn_q.gif" border="0" hspace="2" align="absmiddle" /></a></div>

<table border="4" bordercolor="#dce1e1" style="border-collapse:collapse;margin-bottom:20px;" width="100%">
<tr><td style="padding:7px 0 10px 10px; color:#666666;">
<div style="padding-top:5px;"><font class="g9" color="#0074BA"><b>�� �ֹε�Ϲ�ȣ ���� ���� �ȳ�</b></font></div>
<div style="padding-top:7px;"><b>������Ÿ��� ������ ���� �ű� �ֹε�Ϲ�ȣ ������ �����˴ϴ�.(�������� : 2012�� 8�� 18��)</b></div>
<ol style="margin:7px 0 0 25px;">
<li>ȸ������ �׸� �� ���ֹε�Ϲ�ȣ(resno)�� ��뿩�ΰ� ������� üũ�Ǿ� ������ ���,  üũ�� �����Ͻð� �̻������ ���/�����Ͽ� �ּž� �մϴ�.</li>
<li>���̵�/��й�ȣ ã��� �ֹε�Ϲ�ȣ ��üȮ�� ������ ���̸���(email)�� �׸��� �ݵ�� ��� �� �ʼ��������� �����Ͽ� ���/�����Ͽ� �ּ���!!</li>
</ol>
<div style="padding-top:10px;"><a href="http://www.godo.co.kr/news/notice_view.php?board_idx=725" target="_blank"><font class="small1" color="#0074BA"><b><u>[�ֹε�Ϲ�ȣ �̼������� �ȳ� �� ��ġ���� �ڼ��� ����]</u></b></font></a></div>
</table>

<table border="4" bordercolor="#dce1e1" style="border-collapse:collapse;margin-bottom:20px;" width="100%">
<tr>
	<td style="padding:7px 0 10px 10px; color:#666666;">
		<div style="padding-top:5px;"><font class="g9" color="#0074BA"><strong>�� ȸ�� ��й�ȣ �ۼ� ��Ģ �ȳ�</strong></font></div>
		<div style="padding-top:7px;">
			<strong>������Ÿ����� ���ؿ� ���� ȸ�� ��й�ȣ�� �ۼ� ��Ģ�� �Ʒ��� ���� �����մϴ�.</strong> (<a href="http://guide.godo.co.kr/guide/doc/(���������_��2012-50ȣ)_����������_�����.������_��ȣ��ġ_����_����.hwp" target="_blank"><font color="#0074BA"><strong>��������� ��2012-50ȣ ���� �ٿ�ε�</strong></font></a>)
		</div>
		<div>
			������ż��� ������ ���� �̿��ڰ� ������ ��й�ȣ�� �̿��� �� �ֵ��� ��й�ȣ �ۼ���Ģ�� �����ϰ� �����ؾ� �մϴ�. �̿� ���������� �����빮��(26��), �����ҹ���(26��), ����(10��), Ư������(32��) �� <font color="red"><u>2���� �̻��� �����Ͽ� �ּ� 10�ڸ� �̻� 16�ڸ� ���Ϸ� ��й�ȣ�� �����ϵ��� ����</u></font>�մϴ�.
		</div>

		<div style="padding-top:10px;"><strong>2014�� 7�� 3�� ����</strong>���� ����� ���θ��� ���,</div>
		<div>���� <strong>��Ų ��ġ�� �ݵ�� ����</strong>�ϼž� �մϴ�. <a href="http://www.godo.co.kr/customer_center/patch.php?sno=2037" target="_blank"><font color="#0074BA"><strong>[�ٷΰ���]</strong></font></a></div>
		<div><font color="#0074BA">�� ��ġ ���� ������ ������ ȸ���� ��� ������ ����������ȣ�� ���Ͽ� ��й�ȣ �ۼ� ��Ģ�� ���� ��й�ȣ�� ������ �� �ֵ��� ������ ��ġ�� ���Ͽ� �ֽñ� �ٶ��ϴ�.</font> (��й�ȣ ���� �ȳ� ��ü ����(SMS) ������, ��й�ȣ ���� �ȳ� ���� ��� ��� ��)</div>
	</td>
</tr>
</table>

<table width="100%" border="1" bordercolor="#efefef" style="border-collapse:collapse">
<col width=200px><col width=150px><col width=150px><col width=150px><col width=150px>
<tr height="25" bgcolor="#f7f7f7">
	<th rowspan=2>�ʵ��</th>
	<th colspan=2>PC��</th>
	<th colspan=2>����ϼ���<div><font class="g9" color="#0074BA"><b>����ϼ� ȸ�����Խÿ��� ȸ�������� �������� �ʽ��ϴ�.</b></div></th>
</tr>
<tr height="25" bgcolor="#f7f7f7">

	<th width=130px><a href="javascript:void(0)" onclick="chkBox2(document.frmField.elements['chkUse[]'],'rev','use');">��뿩��</a></th>
	<th width=130px><a href="javascript:void(0)" onclick="chkBox2(document.frmField.elements['chkReq[]'],'rev','req');">�ʼ�����</a></th>
	<th width=130px><a href="javascript:void(0)" onclick="chkBox2_mobile(document.frmField.elements['chkMobileUse[]'],'rev','use');">��뿩��</a></th>
	<th width=130px><a href="javascript:void(0)" onclick="chkBox2_mobile(document.frmField.elements['chkMobileReq[]'],'rev','req');">�ʼ�����</a></th>
</tr>

<col align="center" width="20%" bgcolor="#f7f7f7"><col align="center" width="15%" span="2">

<tbody style="height:25">

<? while (list($key,$value)=each($fld['def'])){ ?>
<tr class=noline>
<!-- 	<? if ($key=="m_id"){ ?><td rowspan=<?=count($fld['def'])?> valign="top" style="padding-top:4px;">�ʼ�����</td><? } ?> -->
	<td align=left style="padding-left:10px"><?=$value?></td>
	<td><input type="checkbox" name="useField[<?=$key?>]" checked disabled /> ���</td>
	<td><input type="checkbox" name="reqField[<?=$key?>]" checked disabled /> �ʼ�</td>
	<td><input type="checkbox" name="useMobileField[<?=$key?>]" checked disabled /> ���</td>
	<td><input type="checkbox" name="reqMobileField[<?=$key?>]" checked disabled /> �ʼ�</td>
</tr>
<? } ?>

<tr>
	<? $idx=0; while (list($key,$value)=each($fld['per'])){ ?>
	<? if (in_array( $key, array( 'ex1', 'ex2', 'ex3', 'ex4', 'ex5', 'ex6' ) ) ){?>
	<td><?=$value?> <input type="text" name="<?=$key?>" value="<?=$joinset[ $key ]?>" size="10" style="cline" /> <font class=ver7 color='3853a5'>(<?=$key?>)</font></td>
	<? } else { ?>
	<td align=left style="padding-left:10px"><?=$value?> (<font class=ver7 color='3853a5'><?=$key?></font>)</td>
	<? } ?>
	<td class="noline" width=130px><font class="def"><input type="checkbox" id="chkUse[]" name="useField[<?=$key?>]" <?=$checked['useField'][$key]?> key="<?=$key?>" onClick="chk('<?=$key?>','use');" /> ���</td>
	<td class="noline" width=130px><font class="def"><input type="checkbox" id="chkReq[]" name="reqField[<?=$key?>]" <?=$checked['reqField'][$key]?> key="<?=$key?>" onClick="chk('<?=$key?>','req');" /> �ʼ�</td>
	<td class="noline" width=130px><font class="def"><input type="checkbox" id="chkMobileUse[]" name="useMobileField[<?=$key?>]" <?=$checked_mobile['useField'][$key]?> key="<?=$key?>" onClick="chk_mobile('<?=$key?>','use');" /> ���</td>
	<td class="noline" width=130px><font class="def"><input type="checkbox" id="chkMobileReq[]" name="reqMobileField[<?=$key?>]" <?=$checked_mobile['reqField'][$key]?> key="<?=$key?>" onClick="chk_mobile('<?=$key?>','req');" /> �ʼ�</td>
	</tr><tr>
	<? } ?>
</tr>
</table>

<div class="button">
<input type="image" src="../img/btn_register.gif" />
<a href="javascript:history.back();"><img src="../img/btn_cancel.gif" /></a>
</div>

</form>

	<div id="MSG02">
	<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" />ȸ�����Խ� �Է��ϴ� �׸���� ���ϴ� ���Դϴ�.</td></tr>
	<tr><td><img src="../img/icon_list.gif" align="absmiddle" />���ϴ� �ʵ��׸��� üũ�ϰ�, �ʼ����������� ���θ� üũ�Ͻø� �˴ϴ�. �߰��� �׸��� ����� ���� �ֽ��ϴ�.</td></tr>
	</table>
	</div>
	<script>cssRound('MSG02');</script>

<? include "../_footer.php"; ?>