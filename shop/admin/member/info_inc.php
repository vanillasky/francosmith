<?
$data = $db->fetch("select MB.*, SC.category from ".GD_MEMBER." AS MB LEFT JOIN ".GD_TODAYSHOP_SUBSCRIBE." AS SC ON MB.m_id = SC.m_id where MB.m_id='".$_GET['m_id']."'");

$zipcode	= explode("-",$data['zipcode']);
$phone		= explode("-",$data['phone']);
$mobile		= explode("-",$data['mobile']);
$fax		= explode("-",$data['fax']);

foreach( codeitem('like') as $k => $v ){
	if ($data['interest']&pow(2,$k)) $checked['interest'][$k] = "checked";
}

include "../../conf/fieldset.php";
foreach ($checked['reqField'] as $k=>$v) $required[$k] = "required";

// ȸ������ ���� ��� ������
$memIcon_inflow = ($data['inflow']) ? " <img src=\"../img/memIcon_".$data['inflow'].".gif\" align=\"absmiddle\" />" : "";

//SMS �߼� ���� ����
$smsFailCheck = smsFailCheck('single', $data['mobile']);
?>

<script language="javascript"><!--
function chkFormMember( fobj ){

	if ( fobj['mod_pass'].checked ){

		if ( fobj['password'].value == '' ){
			alert( "[��й�ȣ] �ʼ��Է»���" );
			fobj['password'].focus();
			return false;
		}

		if ( fobj['password'].value != fobj['password2'].value ){
			alert( "[����й�ȣ]�� [��й�ȣȮ��] ����Ÿ�� �ٸ��ϴ�." );
			fobj['password'].value = fobj['password2'].value = '';
			fobj['password'].focus();
			return false;
		}
	}

	if ( !chkForm(fobj) ) return false;
}
--></script>

<form name="frmMember" method="post" action="<?=$sitelink->link('admin/member/indb.php','ssl');?>" onsubmit="return chkFormMember(this);">
<input type="hidden" name="mode" value="modify" />
<?if ($crm_view == true){?><input type="hidden" name="crmyn" value="Y"><?}?>
<input type="hidden" name="m_id" value="<?=$_GET['m_id']?>" />
<input type="hidden" name="returnUrl" value="<?=$returnUrl?>" />

<div class="title title_top">ȸ������</div>

<table class="tb">
<col class="cellC" /><col style="padding-left:10px;width:250;" />
<col class="cellC" /><col style="padding-left:10px" />
<tr>
	<td>���̵�</td>
	<td>
		<b><?=$data['m_id']?></b>
		<?=$memIcon_inflow?>
		<?php if (strlen($data['connected_sns']) > 0) { foreach(explode(',', $data['connected_sns']) as $socialCode) { ?>
		<img src="../img/ico_member_<?php echo strtolower($socialCode); ?>.gif" style="vertical-align: middle; margin: 0;"/>
		<?php }} ?>
	</td>
	<td>����</td>
	<td class="noline">
	<input type="radio" name="status" value="1" <?=( "1" == $data[status] ? 'checked' : '' )?> /> ����
	<input type="radio" name="status" value="0" <?=( "1" != $data[status] ? 'checked' : '' )?> /> �̽���
	</td>
</tr>
<tr>
	<td>�̸�</td>
	<td><input type="text" name="name" value="<?=$data[name]?>" required label="�̸�" class="line" /></td>
	<td>�׷�</td>
	<td>
<?
$garr = member_grp();
if($data['level']  > 79 && $sess['level'] < 100){?>
	<input type="hidden" name="level" value="<?=$data['level']?>" />
	<?foreach( $garr as $v )if($v['level'] == $data['level'])echo $v['grpnm'];?>
<?}else{?>
	<select name="level" required>
	<option value="">��׷켱��</option>
	<?
	foreach( $garr as $v ){
		if($sess['level'] == 100 || $v['level'] < 80) echo '<option value="' . $v['level'] . '"' . ( $v['level'] == $data['level'] ? 'selected' : '' ) . '>' . $v['grpnm'] . '</option>' . "\n";
	}
	?>
	</select>
<?}?>
<?
$inGroup = false;
foreach($garr as $val){ if($val['level'] == $data['level']) $inGroup = true;}
if($inGroup === false) echo "<span style='color:#ff0000;font-weight:bold'>�� ���� �׷��� ����!</span>";
?>
	</td>
</tr>
<tr>
	<td>�г���</td>
	<td colspan="3"><input type="text" name="nickname" value="<?=$data[nickname]?>" <?=$required['nickname']?> label="�г���" class="line" /></td>
</tr>
<tr>
	<td>��й�ȣ</td>
	<td colspan="3">
	<div style="float:left;" class="noline"><input type="checkbox" name="mod_pass" value="Y" onclick="openLayer('pass');" class="line" /> ����</div>
	<div style="float:left;margin-left:10;display:none;" id="pass">
	����й�ȣ : <input type="password" name="password" class="line" /> &nbsp;&nbsp;
	��й�ȣȮ�� : <input type="password" name="password2" class="line" />
	</div>
	</td>
</tr>
<tr>
	<td>����</td>
	<td>
	<div style="float:left;" class="noline"><input type="checkbox" name="mod_sex" value="Y" onclick="openLayer('sex');" /> ����</div>
	<div style="float:left;margin-left:10;padding-top:3px;"><?=$data['sex'] == 'm' ? '����' : '����'?></div>
	<div style="float:left;margin-left:10;display:none;" class="noline" id="sex">
	<input type="radio" name="sex" value="m" /> ����
	<input type="radio" name="sex" value="w" /> ����
	</div>
	</td>
	<td>�������</td>
	<td>
	<input type="text" name="birth_year" value="<?=$data[birth_year]?>" size="4" maxlength="4" <?=$required['birth']?> class="line" />��
	<input type="text" name="birth[]" value="<?=substr($data['birth'],0,2)?>" size="2" maxlength="2" <?=$required['birth']?> class="line" />��
	<input type="text" name="birth[]" value="<?=substr($data['birth'],2)?>" size="2" maxlength="2" <?=$required['birth']?> class="line" />��

	<span class="noline">( <input type="radio" name="calendar" value="s" <?=( "s" == $data['calendar'] ? 'checked' : '' )?> /> ��
	<input type="radio" name="calendar" value="l" <?=( "l" == $data['calendar'] ? 'checked' : '' )?> /> �� )</span>
	</td>
</tr>
<tr>
	<td>�̸���</td>
	<td colspan="3"><input type="text" name="email" value="<?=$data[email]?>" size=50 <?=$required['email']?> label="�̸���" class="line" />
	<span class="noline">( <input type="radio" name="mailling" value="y" <?=( "y" == $data['mailling'] ? 'checked' : '' )?> /> ���ϸ� ����
	<input type="radio" name="mailling" value="n" <?=( "n" == $data['mailling'] ? 'checked' : '' )?> /> ���ϸ� �ź� )</span>
	<div style="color: #0074BA; margin-top: 3px;"> *���ŵ��Ǽ��� �ȳ������� �ڵ��߼ۿ��ο� ���� ȸ�������� ���ŵ��Ǽ��� ���� �� �ش� ȸ������ �ȳ������� �ڵ� �߼۵˴ϴ�.</div>
	</td>
</tr>
<tr>
	<td>�ּ�</td>
	<td colspan="3">

	<table border="0" cellpadding="0" cellspacing="0">
	<tr>
		<td>
		<input type="text" name="zonecode" id="zonecode" size="5" readonly value="<?=$data['zonecode']?>" class="line" />
		( <input type="text" name="zipcode[]" id="zipcode0" size="3" readonly value="<?=$zipcode[0]?>" class="line" /> -
		<input type="text" name="zipcode[]" id="zipcode1" size="3" readonly value="<?=$zipcode[1]?>" class="line" /> )
		<a href="javascript:popup('../../proc/popup_address.php',500,432)"><img src="../img/btn_zipcode.gif" align="absmiddle" /></a>
		</td>
	</tr>
	<tr>
		<td>
		<input type="text" name="address" id="address" value="<?=$data['address']?>" readonly size="50" <?=$required['address']?> label="�ּ�" class="line" />
		<input type="text" name="address_sub" id="address_sub" value="<?=$data['address_sub']?>" size="30" label="�ּ�" onkeyup="SameAddressSub(this)" oninput="SameAddressSub(this)" class="line" /><br/>
		<input type="hidden" name="road_address" id="road_address" value="<?=$data['road_address']?>">
		<div style="padding:5px 5px 0 5px;font:12px dotum;color:#999;float:left;" id="div_road_address"><?=$data['road_address']?></div>
		<div style="padding:5px 0 0 1px;font:12px dotum;color:#999;" id="div_road_address_sub"><? if ($data['road_address']) { echo $data['address_sub']; } ?></div>
		</td>
	</tr>
	</table>

	</td>
</tr>
<tr>
	<td>�ڵ���</td>
	<td colspan="3">
	<input type="text" name="mobile[]" value="<?=$mobile[0]?>" size="4" maxlength="4" <?=$required['mobile']?> label="�ڵ���" class="line" /> -
	<input type="text" name="mobile[]" value="<?=$mobile[1]?>" size="4" maxlength="4" <?=$required['mobile']?> label="�ڵ���" class="line" /> -
	<input type="text" name="mobile[]" value="<?=$mobile[2]?>" size="4" maxlength="4" <?=$required['mobile']?> label="�ڵ���" class="line" />
	<span class="noline">( <input type="radio" name="sms" value="y" <?=( "y" == $data['sms'] ? 'checked' : '' )?> /> SMS ����
	<input type="radio" name="sms" value="n" <?=( "n" == $data['sms'] ? 'checked' : '' )?> /> SMS �ź� )</span>
	<?php if($smsFailCheck === true){ ?>
	<script type="text/javascript" src="../godo_ui.js"></script>
	<style>div.tooltip {width:260px;padding:0;margin:0;}</style>
	<br /><br /><font color="red"> SMS �߼۽��� ��ȣ</font> <img src="../img/icons/icon_qmark.gif" style="vertical-align:bottom; cursor:pointer; border: 0px;" class="godo-tooltip" tooltip="<span style=&quot;color: red;&quot;>SMS �߼۽��й�ȣ</span>�� &quot;�߸��� ��ȭ��ȣ&quot; ���� ������ SMS �߼۽��� �̷��� �ִ� ��ȣ�Դϴ�."> &nbsp;
	<?php } ?>
	<img src="../img/btn_sms_sendinfo.gif" style="vertical-align:middle; cursor:pointer; border: 0px;" onclick="javascript:popup('./popup.sms.sendView.php?sms_phoneNumber=<?php echo $data['mobile']; ?>', '700', '500');" />
	<div style="color: #0074BA; margin-top: 3px;"> *���ŵ��Ǽ��� �ȳ������� �ڵ��߼ۿ��ο� ���� ȸ�������� ���ŵ��Ǽ��� ���� �� �ش� ȸ������ �ȳ������� �ڵ� �߼۵˴ϴ�.</div>
	</td>
</tr>
<tr>
	<td>��ȭ��ȣ</td>
	<td>
	<input type="text" name="phone[]" value="<?=$phone[0]?>" size="4" maxlength="4" <?=$required['phone']?> label="��ȭ��ȣ" class="line" /> -
	<input type="text" name="phone[]" value="<?=$phone[1]?>" size="4" maxlength="4" <?=$required['phone']?> label="��ȭ��ȣ" class="line" /> -
	<input type="text" name="phone[]" value="<?=$phone[2]?>" size="4" maxlength="4" <?=$required['phone']?> label="��ȭ��ȣ" class="line" />
	</td>
	<td>�ѽ���ȣ</td>
	<td>
	<input type="text" name="fax[]" value="<?=$fax[0]?>" size="4" maxlength="4" <?=$required['fax']?> label="�ѽ�" class="line" /> -
	<input type="text" name="fax[]" value="<?=$fax[1]?>" size="4" maxlength="4" <?=$required['fax']?> label="�ѽ�" class="line" /> -
	<input type="text" name="fax[]" value="<?=$fax[2]?>" size="4" maxlength="4" <?=$required['fax']?> label="�ѽ�" class="line" />
	</td>
</tr>
<tr>
	<td>ȸ���</td>
	<td><input type="text" name="company" value="<?=$data[company]?>" class="line" /></td>
	<td>����</td>
	<td><input type="text" name="service" value="<?=$data[service]?>" class="line" /></td>
</tr>
<tr>
	<td>����ڹ�ȣ</td>
	<td><input type="text" name="busino" value="<?=$data[busino]?>" class="line" /></td>
	<td>����</td>
	<td><input type="text" name="item" value="<?=$data[item]?>" class="line" /></td>
</tr>
<tr>
	<td>����</td>
	<td>
	<select name="job">
	<option value="">����������</option>
	<?
	foreach( codeitem('job') as $k => $v ){
		echo '<option value="' . $k . '"' . ( $k == $data['job'] ? 'selected' : '' ) . '>' . $v . '</option>' . "\n";
	}
	?>
	</select>
	</td>
	<td>��ȥ�����</td>
	<td>
	<div style="float:left;" class="noline">
	<input type="radio" name="marriyn" value="n" <?=( "n" == $data['marriyn'] ? 'checked' : '' )?> onclick="openLayer('marri','none')" /> ��ȥ
	<input type="radio" name="marriyn" value="y" <?=( "y" == $data['marriyn'] ? 'checked' : '' )?> onclick="openLayer('marri','block')" /> ��ȥ
	</div>
	<div style="float:left;margin-left:5;display:none;" id="marri">
	<input type="text" name="marridate[]" value="<?=substr($data['marridate'],0,4)?>" size="4" maxlength="4" />��
	<input type="text" name="marridate[]" value="<?=substr($data['marridate'],4,2)?>" size="2" maxlength="2" />��
	<input type="text" name="marridate[]" value="<?=substr($data['marridate'],6,2)?>" size="2" maxlength="2" />��
	</div>
	<script>if( frmMember.marriyn[1].checked ) openLayer('marri','block');</script>
	</td>
</tr>
<tr>
	<td>���ɺо�<br><a href="../data/data_code.php?sgroupcd=like" target="_new"><img src="../img/btn_editinterest.gif" vspace="2" /></a></td>
	<td colspan="3" class="noline">
	<table><tr>
	<? $idx=0; foreach( codeitem('like') as $k => $v ){ ?>
	<td nowrap><input type="checkbox" name="interest[]" value="<?=$k?>" <?=$checked['interest'][$k]?>> <?=$v?></td>
	<? if (++$idx%4==0){ ?></tr><tr><? } ?>
	<? } ?>
	</tr></table>

	</td>
</tr>
<tr>
	<td>���ɺз�</td>
	<td colspan="3" class="noline">
	<?
	$todayshop = Core::loader('todayshop');
	$ts_category_all = $todayshop->getCategory(true);
	?>
	<select name="interest_category">
	<option value=""> - ���ɺз��� ������ �ּ��� - </option>
	<? foreach ($ts_category_all as $v) { ?>
	<option value="<?=$v['category']?>" <?=$v['category'] == $data['category'] ? 'selected' : ''?>><?=$v['catnm']?></option>
	<? } ?>
	</select>
	</td>
</tr>
<tr>
	<td>����� ����</td>
	<td colspan="3"><textarea name="memo" style="width:100%;height:80px" class="tline"><?=$data['memo']?></textarea></td>
</tr>
<tr>
	<td title="�߰�����1"><?=( $joinset['ex1'] ? $joinset['ex1'] : '�߰�����1' )?></td>
	<td><input type="text" name="ex1" value="<?=$data['ex1']?>" style="width:90%" class="line" /></td>
	<td title="�߰�����4"><?=( $joinset['ex4'] ? $joinset['ex4'] : '�߰�����4' )?></td>
	<td><input type="text" name="ex4" value="<?=$data['ex4']?>" style="width:90%" class="line" /></td>
</tr>
<tr>
	<td title="�߰�����2"><?=( $joinset['ex2'] ? $joinset['ex2'] : '�߰�����2' )?></td>
	<td><input type="text" name="ex2" value="<?=$data['ex2']?>" style="width:90%" class="line" /></td>
	<td title="�߰�����5"><?=( $joinset['ex5'] ? $joinset['ex5'] : '�߰�����5' )?></td>
	<td><input type="text" name="ex5" value="<?=$data['ex5']?>" style="width:90%" class="line" /></td>
</tr>
<tr>
	<td title="�߰�����3"><?=( $joinset['ex3'] ? $joinset['ex3'] : '�߰�����3' )?></td>
	<td><input type="text" name="ex3" value="<?=$data['ex3']?>" style="width:90%" class="line" /></td>
	<td title="�߰�����6"><?=( $joinset['ex6'] ? $joinset['ex6'] : '�߰�����6' )?></td>
	<td><input type="text" name="ex6" value="<?=$data['ex6']?>" style="width:90%" class="line" /></td>
</tr>
<tr>
	<td>��õ��</td>
	<td colspan="3"><input type="text" name="recommid" value="<?=$data['recommid']?>" class="line" /> <?if( $data['recommid'] ){?>&nbsp;&nbsp; <a href="javascript:popupLayer('../member/Crm_view.php?m_id=<?=$data['recommid']?>',780,600)" style="color:#616161;" class="ver8">[��������]</a><?}?></td>
</tr>
<tr>
	<td>����������޹�ħ</td>
	<td colspan="3">
	<font class="ver8">�̿��ڵ���:<?=( $data['private1'] == "y" ? '<font color=0074BA>������</font>' : '<font color=EA0095>���Ǿ���</font>' )?></font>
	<?if($cfg['private2YN'] == "Y"){?> , <font class="ver8">��3������:<?=( $data['private2'] == "y" ? '<font color=0074BA>������</font>' : '<font color=EA0095>���Ǿ���</font>' )?></font><?}?>
	<?if($cfg['private3YN'] == "Y"){?> , <font class="ver8">��Ź����:<?=( $data['private3'] == "y" ? '<font color=0074BA>������</font>' : '<font color=EA0095>���Ǿ���</font>' )?></font><?}?>
	(�� �ش�κ��� �����ڰ� ���� ������ �Ұ����մϴ�.)
	</td>
</tr>
<?
// �߰������׸񳻿�
$result = $db->query("SELECT * FROM ".GD_CONSENT." WHERE useyn = 'y' ORDER BY sno");
$consentCnt = $db->count_($result);

if ($consentCnt > 0){
?>
<tr>
	<td>�߰��׸� ���ǿ���</td>
	<td colspan="3">
	<?
	while($consent = $db->fetch($result)){
		list($consentyn) = $db->fetch("SELECT consentyn FROM ".GD_MEMBER_CONSENT." WHERE m_no = '".$data['m_no']."' AND consent_sno = '".$consent['sno']."'");
	?>
		<div><font class="ver8"><?echo $consent['title']?>: <?=( $consentyn == "y" ? '<font color=0074BA class=ver811>������</font>' : '<font color=EA0095>���Ǿ���</font>' )?></font></div>
	<? } ?>
	</td>
</tr>
<? } ?>
<tr>
	<td>ȸ��������</td>
	<td><font class="ver8"><?=$data['regdt']?></font></td>
	<td>������</td>
	<td><?=number_format($data['emoney'])?> ��<?if ($crm_view != true){?> &nbsp;&nbsp; <a href="javascript:popupLayer('../member/popup.emoney.php?m_no=<?=$data['m_no']?>',600,500)"><img src="../img/btn_detailview.gif" align="absmiddle" /></a><?}?></td>
</tr>
<tr>
	<td>�����α���</td>
	<td><font class="ver8"><?=$data['last_login']?> &nbsp;&nbsp; �湮 <?=number_format($data['cnt_login'])?> ȸ</font></td>
	<td>�����α���IP</td>
	<td><?=$data['last_login_ip']?></td>
</tr>
<tr>
	<td>����������</td>
	<td><font class="ver8"><?=$data['last_sale']?></font></td>
	<td>���Աݾ�</td>
	<td><?=number_format( $data['sum_sale'] )?> �� &nbsp;&nbsp; �ֹ� <?=number_format($data['cnt_sale'])?> ��<?if ($crm_view != true){?> &nbsp;&nbsp; <a href="javascript:popup('../member/orderlist.php?m_no=<?=$data['m_no']?>',500,600)"><img src="../img/btn_detailview.gif" align="absmiddle" /></a><?}?></td>
</tr>
</table>

<div class="button">
<input type="image" src="../img/btn_modify.gif" />
<?if ($crm_view != true){?><a href='<?=$listUrl?>'><img src="../img/btn_list.gif" /></a><?}?>
</div>

</form>