<?
	# �˾�â������ �˻��� �ִ°�� �󼼰˻��� ����
	if($popupSearch == "Y"){
?>
<table class="tb">
<col class="cellC" /><col class="cellL" style="width:250" />
<col class="cellC" /><col class="cellL" />
<tr>
	<td class="noline" colspan="4">
	<input type="checkbox" name="popupDetail" value="Y" onClick="javascript:popupDetailDiv();" <?=$checked['popupDetail']['Y']?>> �� �˻��� üũ�� ���ּ���
	</td>
</tr>
<tr>
	<td>Ű����˻�</td>
	<td>
	<select name="skey">
	<option value="all" <?=$selected['skey']['all']?>> ���հ˻� </option>
	<option value="name" <?=$selected['skey']['name']?>> ȸ���� </option>
	<option value="nickname" <?=$selected['skey']['nickname']?>> �г��� </option>
	<option value="m_id" <?=$selected['skey']['m_id']?>> ���̵� </option>
	<option value="email" <?=$selected['skey']['email']?>> �̸��� </option>
	<option value="phone" <?=$selected['skey']['phone']?>> ��ȭ��ȣ </option>
	<option value="mobile" <?=$selected['skey']['mobile']?>> ������ȣ </option>
	<option value="recommid" <?=$selected['skey']['recommid']?>> ��õ�� </option>
	<option value="company" <?=$selected['skey']['company']?>> ȸ��� </option>
	</select> <input type="text" name="sword" value="<?=$_GET['sword']?>" class="line" />
	</td>
	<td>���ο���/�׷�</td>
	<td>
	<select name="sstatus">
	<option value="" <?=$selected['sstatus']['']?>> ��ü </option>
	<option value="1" <?=$selected['sstatus']['1']?>> ���� </option>
	<option value="0" <?=$selected['sstatus']['0']?>> �̽��� </option>
	</select>
	<select name="slevel">
	<option value="">==�׷켱��==</option>
	<option value="__null__" <? if($_GET['slevel']=='__null__')echo 'selected';?>>�׷����</option>
	<? foreach( member_grp() as $v ){ ?>
	<option value="<?=$v[level]?>" <?=$selected['slevel'][$v['level']]?>><?=$v['grpnm']?> - lv[<?=$v['level']?>]</option>
	<? } ?>
	</select>
	</td>
</tr>
</table>
<div style="padding-top:3;display:none;" id="searchDetail">
<?	}	?>
<table class="tb">
<col class="cellC" /><col class="cellL" style="width:250" />
<col class="cellC" /><col class="cellL" />
<?if(!$popupSearch){?>
<tr>
	<td>Ű����˻�</td>
	<td>
	<select name="skey">
	<option value="all" <?=$selected['skey']['all']?>> ���հ˻� </option>
	<option value="name" <?=$selected['skey']['name']?>> ȸ���� </option>
	<option value="nickname" <?=$selected['skey']['nickname']?>> �г��� </option>
	<option value="m_id" <?=$selected['skey']['m_id']?>> ���̵� </option>
	<option value="email" <?=$selected['skey']['email']?>> �̸��� </option>
	<option value="phone" <?=$selected['skey']['phone']?>> ��ȭ��ȣ </option>
	<option value="mobile" <?=$selected['skey']['mobile']?>> ������ȣ </option>
	<option value="recommid" <?=$selected['skey']['recommid']?>> ��õ�� </option>
	<option value="company" <?=$selected['skey']['company']?>> ȸ��� </option>
	</select> <input type="text" name="sword" value="<?=$_GET['sword']?>" class="line" />
	</td>
	<td>���ο���/�׷�</td>
	<td>
	<select name="sstatus">
	<option value="" <?=$selected['sstatus']['']?>> ��ü </option>
	<option value="1" <?=$selected['sstatus']['1']?>> ���� </option>
	<option value="0" <?=$selected['sstatus']['0']?>> �̽��� </option>
	</select>
	<select name="slevel">
	<option value="">==�׷켱��==</option>
	<option value="__null__" <? if($_GET['slevel']=='__null__')echo 'selected';?>>�׷����</option>
	<? foreach( member_grp() as $v ){ ?>
	<option value="<?=$v[level]?>" <?=$selected['slevel'][$v['level']]?>><?=$v['grpnm']?> - lv[<?=$v['level']?>]</option>
	<? } ?>
	</select>
	</td>
</tr>
<?	}	?>
<tr>
	<td>���ž�</td>
	<td>
	<input type="text" name="ssum_sale[]" value="<?=$_GET['ssum_sale'][0]?>" size="10" onkeydown="onlynumber();" class="rline" />�� ~
	<input type="text" name="ssum_sale[]" value="<?=$_GET['ssum_sale'][1]?>" size="10" onkeydown="onlynumber();" class="rline" />��
	</td>
	<td>������</td>
	<td>
	<input type="text" name="semoney[]" value="<?=$_GET['semoney'][0]?>" size="10" onkeydown="onlynumber();" class="rline" />�� ~
	<input type="text" name="semoney[]" value="<?=$_GET['semoney'][1]?>" size="10" onkeydown="onlynumber();" class="rline" />��
	</td>
</tr>
<tr>
	<td>������</td>
	<td colspan="3">
	<input type="text" name="sregdt[]" value="<?=$_GET['sregdt'][0]?>" size="10" onkeydown="onlynumber();" onclick="calendar(event);" class="cline" /> ~
	<input type="text" name="sregdt[]" value="<?=$_GET['sregdt'][1]?>" size="10" onkeydown="onlynumber();" onclick="calendar(event);" class="cline" />
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('sregdt[]')"><img src="../img/sicon_all.gif" align="absmiddle" /></a>
	</td>
</tr>
<tr>
	<td>�����α���</td>
	<td colspan="3">
	<input type="text" name="slastdt[]" value="<?=$_GET['slastdt'][0]?>" size="10" onkeydown="onlynumber();" onclick="calendar(event);" class="cline" /> ~
	<input type="text" name="slastdt[]" value="<?=$_GET['slastdt'][1]?>" size="10" onkeydown="onlynumber();" onclick="calendar(event);" class="cline" />
	<a href="javascript:setDate('slastdt[]',<?=date("Ymd")?>,<?=date("Ymd")?>)"><img src="../img/sicon_today.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('slastdt[]',<?=date("Ymd",strtotime("-7 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_week.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('slastdt[]',<?=date("Ymd",strtotime("-15 day"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twoweek.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('slastdt[]',<?=date("Ymd",strtotime("-1 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_month.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('slastdt[]',<?=date("Ymd",strtotime("-2 month"))?>,<?=date("Ymd")?>)"><img src="../img/sicon_twomonth.gif" align="absmiddle" /></a>
	<a href="javascript:setDate('slastdt[]')"><img src="../img/sicon_all.gif" align="absmiddle" /></a>
	</td>
</tr>
<tr>
	<td>����</td>
	<td class="noline">
	<input type="radio" name="sex" value="" <?=$checked['sex']['']?> />��ü
	<input type="radio" name="sex" value="m" <?=$checked['sex']['m']?> />����
	<input type="radio" name="sex" value="w" <?=$checked['sex']['w']?> />����
	</td>
	<td>������</td>
	<td>
	<select name="sage">
	<option value="" <?=$selected['sage']['']?>> ��ü </option>
	<option value="10" <?=$selected['sage']['10']?>> 10�� </option>
	<option value="20" <?=$selected['sage']['20']?>> 20�� </option>
	<option value="30" <?=$selected['sage']['30']?>> 30�� </option>
	<option value="40" <?=$selected['sage']['40']?>> 40�� </option>
	<option value="50" <?=$selected['sage']['50']?>> 50�� </option>
	<option value="60" <?=$selected['sage']['60']?>> 60���̻� </option>
	</select>
	</td>
</tr>
<tr>
	<td>�湮Ƚ��</td>
	<td>
	<input type="text" name="scnt_login[]" value="<?=$_GET[scnt_login][0]?>" size="10" onkeydown="onlynumber();" class="rline" />ȸ ~
	<input type="text" name="scnt_login[]" value="<?=$_GET[scnt_login][1]?>" size="10" onkeydown="onlynumber();" class="rline" />ȸ
	</td>
	<td>�޸�ȸ���˻�</td>
	<td>
	<input type="text" name="dormancy" value="<?=$_GET['dormancy']?>" size="8" maxlength="8" onkeydown="onlynumber();" class="rline" /> �� �̻� ������ ȸ���˻�
	</td>
</tr>
<tr>
	<td>���ϼ��ſ���</td>
	<td class="noline">
	<input type="radio" name="mailing" value="" <?=$checked['mailing']['']?> />��ü
	<input type="radio" name="mailing" value="y" <?=$checked['mailing']['y']?> />����
	<input type="radio" name="mailing" value="n" <?=$checked['mailing']['n']?> />���Űź�
	</td>
	<td>SMS���ſ���</td>
	<td class="noline">
	<input type="radio" name="smsyn" value="" <?=$checked['smsyn']['']?> />��ü
	<input type="radio" name="smsyn" value="y" <?=$checked['smsyn']['y']?> />����
	<input type="radio" name="smsyn" value="n" <?=$checked['smsyn']['n']?> />���Űź�
	</td>
</tr>
<tr>
	<td>�������</td>
	<td>
	<select name="birthtype">
	<option value="" <?=$selected['birthtype']['']?>> ��ü </option>
	<option value="s" <?=$selected['birthtype']['s']?>> ��� </option>
	<option value="l" <?=$selected['birthtype']['l']?>> ���� </option>
	</select>
	<input type="text" name="birthdate[]" value="<?=$_GET['birthdate'][0]?>" size="8" maxlength="8" onkeydown="onlynumber();" class="cline" /> -
	<input type="text" name="birthdate[]" value="<?=$_GET['birthdate'][1]?>" size="8" maxlength="8" onkeydown="onlynumber();" class="cline" />
	<div style="padding-left:53px"><font class="ver71" color="627dce">ex) 20080321 <font class="extext">�Ǵ�</font> 0321</font></div>
	</td>
	<td>��ȥ����/��ȥ�����</td>
	<td>
	<select name="marriyn">
	<option value="" <?=$selected['marriyn']['']?>> ��ü </option>
	<option value="n" <?=$selected['marriyn']['n']?>> ��ȥ </option>
	<option value="y" <?=$selected['marriyn']['y']?>> ��ȥ </option>
	</select>
	<input type="text" name="marridate[]" value="<?=$_GET['marridate'][0]?>" size="8" maxlength="8" onkeydown="onlynumber();" class="cline" /> -
	<input type="text" name="marridate[]" value="<?=$_GET['marridate'][1]?>" size="8" maxlength="8" onkeydown="onlynumber();" class="cline" />
	<div style="padding-left:53px"><font class="ver71" color="627dce">ex) 20080321 <font class="extext">�Ǵ�</font> 0321</font></div>
	</td>
</tr>
<tr>
	<? if (strpos($_SERVER['PHP_SELF'],'member/list.php') || strpos($_SERVER['PHP_SELF'],'member/batch.php')) { // ȸ������Ʈ / ȸ�����λ��� �ϰ����� ������ ����?>
	<td>ȸ������ ���� ���</td>
	<td class="noline" style="font-size:11px;">
	<input type="checkbox" name="inflow[naverCheckout]" id="inflow[naverCheckout]" value="naverCheckout" <?=$checked['inflow']['naverCheckout']?> /><label for="inflow[naverCheckout]"><img src="../img/memIcon_naverCheckout.gif" align="absmiddle" alt="���̹� üũ�ƿ�" title="���̹� üũ�ƿ�" />���̹� üũ�ƿ�</label>
	<input type="checkbox" name="inflow[mobileshop]" id="inflow[mobileshop]" value="mobileshop" <?=$checked['inflow']['mobileshop']?> /><label for="inflow[mobileshop]"><img src="../img/memIcon_mobileshop.gif" align="absmiddle" alt="����ϼ�" title="����ϼ�" />����ϼ�</label>
	</td>
	<td>ȸ�����Ա���</td>
	<td class="noline" style="font-size:11px;">
	<select name="sunder14">
	<option value="">==ȸ�����м���==</option>
	<option value="" <?=$selected['sunder14']['']?>> ��ü </option>
	<option value="1" <?=$selected['sunder14']['1']?>> ��14�� �̸� </option>
	</select>
	</td>
	<? } else { ?>
	<td>ȸ������ ���� ���</td>
	<td colspan="3" class="noline" style="font-size:11px;">
	<input type="checkbox" name="inflow[naverCheckout]" id="inflow[naverCheckout]" value="naverCheckout" <?=$checked['inflow']['naverCheckout']?> /><label for="inflow[naverCheckout]"><img src="../img/memIcon_naverCheckout.gif" align="absmiddle" alt="���̹� üũ�ƿ�" title="���̹� üũ�ƿ�" />���̹� üũ�ƿ�</label>
	<input type="checkbox" name="inflow[mobileshop]" id="inflow[mobileshop]" value="mobileshop" <?=$checked['inflow']['mobileshop']?> /><label for="inflow[mobileshop]"><img src="../img/memIcon_mobileshop.gif" align="absmiddle" alt="����ϼ�" title="����ϼ�" />����ϼ�</label>
	</td>
	<? } ?>
</tr>
<?if ($_GET['mobileYN'] == "y"){?>
<tr>
	<td colspan="4">������ SMS ������ ���ϻ�����, SMS ����, ��»���, �ڵ�����ȣ ��ϰ��� ���ؼ��� ������ �˴ϴ�.</td>
</tr>
<?}?>
</table>

<div style="margin: 3px 0px 0px 3px; color: red;">*������Ÿ����� ���� ���Űź��� ȸ�����Դ� <strong>���� ����</strong>�� �߼��� �� ������, ���� �� ���·ᰡ �ΰ��˴ϴ�.</div>

<? if($popupSearch == "Y"){	# �˾�â������ �˻��� �ִ°�� ?>
</div>
<script language="JavaScript" type="text/JavaScript">
function popupDetailDiv(){
	if(document.getElementsByName('popupDetail')[0].checked == true){
		document.getElementById('searchDetail').style.display = 'block';
	}else{
		document.getElementById('searchDetail').style.display = 'none';
	}
}
popupDetailDiv();
</script>
<? } ?>