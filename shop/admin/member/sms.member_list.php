<?

$location = "SMS���� > SMS ȸ�� �ּҷ�";
include "../_header.php";
include "../../lib/page.class.php";
@include "../../conf/phone.php";

### �׷�� ��������
$query = "select * from ".GD_MEMBER_GRP;
$res = $db->query($query);
while ($data=$db->fetch($res)) $r_grp[$data['level']] = $data['grpnm'];

list ($total) = $db->fetch("select count(*) from ".GD_MEMBER . " WHERE " . MEMBER_DEFAULT_WHERE); # �� ���ڵ��

if (!$_GET['page_num']) $_GET['page_num'] = 10;
$orderby = ($_GET['sort']) ? $_GET['sort'] : "regdt desc"; # ���� ����

### �����Ҵ�
$selected['page_num'][$_GET['page_num']]	= "selected";
$selected['sort'][$orderby]					= "selected";
$selected['skey'][$_GET['skey']]			= "selected";
$selected['sstatus'][$_GET['sstatus']]		= "selected";
$selected['slevel'][$_GET['slevel']]		= "selected";
$selected['sage'][$_GET['sage']]			= "selected";
$selected['birthtype'][$_GET['birthtype']]	= "selected";
$selected['marriyn'][$_GET['marriyn']]		= "selected";
$checked['sex'][$_GET['sex']]				= "checked";
$checked['mailing'][$_GET['mailing']]		= "checked";
$checked['smsyn'][$_GET['smsyn']]			= "checked";

### ���
$db_table = GD_MEMBER;

if ($_GET['skey'] && $_GET['sword']){
	if ( $_GET['skey']== 'all' ){
		$where[] = "( concat( m_id, name, nickname, email, phone, mobile, recommid, company ) like '%".$_GET['sword']."%' or nickname like '%".$_GET['sword']."%' )";
	}
	else $where[] = $_GET['skey'] ." like '%".$_GET['sword']."%'";
}

if ($_GET['sstatus']!='') $where[] = "status='".$_GET['sstatus']."'";
if ($_GET['slevel']!='') $where[] = "level='".$_GET['slevel']."'";

if ($_GET['ssum_sale'][0] != '' && $_GET['ssum_sale'][1] != '') $where[] = "sum_sale between ".$_GET['ssum_sale'][0]." and ".$_GET['ssum_sale'][1];
else if ($_GET['ssum_sale'][0] != '' && $_GET['ssum_sale'][1] == '') $where[] = "sum_sale >= ".$_GET['ssum_sale'][0];
else if ($_GET['ssum_sale'][0] == '' && $_GET['ssum_sale'][1] != '') $where[] = "sum_sale <= ".$_GET['ssum_sale'][1];

if ($_GET['semoney'][0] != '' && $_GET['semoney'][1] != '') $where[] = "emoney between ".$_GET['semoney'][0]." and ".$_GET['semoney'][1];
else if ($_GET['semoney'][0] != '' && $_GET['semoney'][1] == '') $where[] = "emoney >= ".$_GET['semoney'][0];
else if ($_GET['semoney'][0] == '' && $_GET['semoney'][1] != '') $where[] = "emoney <= ".$_GET['semoney'][1];

if ($_GET['sregdt'][0] && $_GET['sregdt'][1]) $where[] = "regdt between date_format(".$_GET['sregdt'][0].",'%Y-%m-%d 00:00:00') and date_format(".$_GET['sregdt'][1].",'%Y-%m-%d 23:59:59')";
if ($_GET['slastdt'][0] && $_GET['slastdt'][1]) $where[] = "last_login between date_format(".$_GET['slastdt'][0].",'%Y-%m-%d 00:00:00') and date_format(".$_GET['slastdt'][1].",'%Y-%m-%d 23:59:59')";

if ($_GET['sex']) $where[] = "sex = '".$_GET['sex']."'";
if ($_GET['sage']!=''){
	$age[] = date('Y') + 1 - $_GET['sage'];
	$age[] = $age[0] - 9;
	foreach ($age as $k => $v) $age[$k] = substr($v,2,2);
	if ($_GET['sage'] == '60') $where[] = "right(birth_year,2) <= ".$age[1];
	else $where[] = "right(birth_year,2) between ".$age[1]." and ".$age[0];
}

if ($_GET['scnt_login'][0] != '' && $_GET['scnt_login'][1] != '') $where[] = "cnt_login between ".$_GET['scnt_login'][0]." and ".$_GET['scnt_login'][1];
else if ($_GET['scnt_login'][0] != '' && $_GET['scnt_login'][1] == '') $where[] = "cnt_login >= ".$_GET['scnt_login'][0];
else if ($_GET['scnt_login'][0] == '' && $_GET['scnt_login'][1] != '') $where[] = "cnt_login <= ".$_GET['scnt_login'][1];

if ($_GET['dormancy']){
	$dormancyDate	= date("Ymd",strtotime("-{$_GET['dormancy']} day"));
	$where[] = " date_format(last_login,'%Y%m%d') <= '".$dormancyDate."'";
}

if ($_GET['mailing']) $where[] = "mailling = '".$_GET['mailing']."'";
if ($_GET['smsyn']) $where[] = "sms = '".$_GET['smsyn']."'";

if( $_GET['birthtype'] ) $where[] = "calendar = '".$_GET['birthtype']."'";
if( $_GET['birthdate'][0] ){
	if( $_GET['birthdate'][1] ){
		if(strlen($_GET['birthdate'][0]) > 4 && strlen($_GET['birthdate'][1]) > 4) $where[] = "concat(birth_year, birth) between '".$_GET['birthdate'][0]."' and '".$_GET['birthdate'][1]."'";
		else $where[] = "birth between '".$_GET['birthdate'][0]."' and '".$_GET['birthdate'][1]."'";
	}else{
		$where[] = "birth = '".$_GET['birthdate'][0]."'";
	}
}

if( $_GET['marriyn'] ) $where[] = "marriyn = '".$_GET['marriyn']."'";
if( $_GET['marridate'][0] ){
	if( $_GET['marridate'][1] ){
		if(strlen($_GET['marridate'][0]) > 4 && strlen($_GET['marridate'][1]) > 4) $where[] = "marridate between '".$_GET['marridate'][0]."' and '".$_GET['marridate'][1]."'";
		else $where[] = "substring(marridate,5,4) between '".$_GET['marridate'][0]."' and '".$_GET['marridate'][1]."'";
	}else{
		$where[] = "substring(marridate,5,4) = '".$_GET['marridate'][0]."'";
	}
}

# ���ο��� ������ SMS Ȯ�ο�
if ($_GET['mobileYN'] == "y") $where[] = "mobile != ''";

$where[] = "m_id != 'godomall'";
$where[] = MEMBER_DEFAULT_WHERE;

$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->setQuery($db_table,$where,$orderby);
$pg->exec();

$res = $db->query($pg->query);
?>
<script language="JavaScript" type="text/JavaScript">
function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
}

function delSMSAddress(fm)
{
	if (!isChked(document.getElementsByName('chk[]'))) return;
	if (!confirm('SMS �ּҷϿ��� ������ ȸ���� �����Ͻðڽ��ϱ�?')) return;
	fm.target = "_self";
	fm.mode.value = "sms_address_del";
	fm.action = "indb.php";
	fm.submit();

}

function sendSMS(m_id) {

	var x = (window.screen.width - 800) / 2;
	var y = (window.screen.height - 600) / 2;

	var smswin = window.open('about:blank', "smswin", "width=800, height=600, scrollbars=yes, left=" + x + ", top=" + y);

	var f = document.fmList;
	f.target = 'smswin';
	f.action = '../member/popup.sms.php';

	if (m_id)	// ���� �߼�
	{
		f.m_id.value = m_id;
		f.type.value = 1;
	}
	else {
		f.type.value = f.target_type.value;
	}
	f.submit();

}

</script>

<form>

<div class="title title_top">SMS ȸ�� �ּҷ�<span>�˻��� ȸ������ SMS�� �߼��� �� �ֽ��ϴ�.</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=17')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a></div>
<!-- ȸ�� �˻� �� -->
	<table class="tb">
	<col class="cellC" /><col class="cellL" style="width:250" />
	<col class="cellC" /><col class="cellL" />
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
		<? foreach( member_grp() as $v ){ ?>
		<option value="<?=$v[level]?>" <?=$selected['slevel'][$v['level']]?>><?=$v['grpnm']?> - lv[<?=$v['level']?>]</option>
		<? } ?>
		</select>
		</td>
	</tr>
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
		<td>SMS���ſ���</td>
		<td class="noline">
		<input type="radio" name="smsyn" value="" <?=$checked['smsyn']['']?> />��ü
		<input type="radio" name="smsyn" value="y" <?=$checked['smsyn']['y']?> />����
		<input type="radio" name="smsyn" value="n" <?=$checked['smsyn']['n']?> />���Űź�
		</td>
		<td>���� SMS �Ǽ�</td>
		<td>
			<span style="font-weight:bold"><font class="ver9" color="0074ba"><b id="span_sms2"><?=number_format(getSmsPoint())?></b></span><font color="262626">��</font>
			<a href="javascript:location.href='../member/sms.pay.php';"><img src="../img/btn_smspoint.gif" align="absmiddle"></a>
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
	</table>
<!-- ȸ�� �˻� �� -->
<div style="margin: 3px 0px 0px 3px; color: red;">*������Ÿ����� ���� ���Űź��� ȸ�����Դ� <strong>���� ����</strong>�� �߼��� �� ������, ���� �� ���·ᰡ �ΰ��˴ϴ�.</div>
<div class="button_top"><input type="image" src="../img/btn_search2.gif" /></div>


<table width="100%">
<tr>
	<td class="pageInfo">
	�� <font class="ver8"><b><?=number_format($total)?></b>��, �˻� <b><?=number_format($pg->recode['total'])?></b>��, <b><?=number_format($pg->page['now'])?></b> of <?=number_format($pg->page['total'])?> Pages
	</td>
	<td align="right">
	<select name="sort" onchange="this.form.submit();">
	<option value="regdt desc" <?=$selected['sort']['regdt desc']?>>- ������ ���ġ�</option>
	<option value="regdt asc" <?=$selected['sort']['regdt asc']?>>- ������ ���ġ�</option>
	<option value="cnt_login desc" <?=$selected['sort']['cnt_login desc']?>>- �湮�� ���ġ�</option>
	<option value="cnt_login asc" <?=$selected['sort']['cnt_login asc']?>>- �湮�� ���ġ�</option>
    <optgroup label="------------"></optgroup>
	<option value="name desc" <?=$selected['sort']['name desc']?>>- �̸� ���ġ�</option>
	<option value="name asc" <?=$selected['sort']['name asc']?>>- �̸� ���ġ�</option>
	<option value="m_id desc" <?=$selected['sort']['m_id desc']?>>- ���̵� ���ġ�</option>
	<option value="m_id asc" <?=$selected['sort']['m_id asc']?>>- ���̵� ���ġ�</option>
    <optgroup label="------------"></optgroup>
	<option value="sum_sale desc" <?=$selected['sort']['sum_sale desc']?>>- ���űݾ� ���ġ�</option>
	<option value="sum_sale asc" <?=$selected['sort']['sum_sale asc']?>>- ���űݾ� ���ġ�</option>
	</select>&nbsp;
	<select name="page_num" onchange="this.form.submit();">
	<?
	$r_pagenum = array(10,20,40,60,100);
	foreach ($r_pagenum as $v){
	?>
	<option value="<?=$v?>" <?=$selected[page_num][$v]?> /><?=$v?>�� ���
	<? } ?>
	</select>
	</td>
</tr>
</table>
</form>

<form name="fmList" id="fmList" method="post">
<input type="hidden" name="mode" value="addressbook" />
<input type="hidden" name="query" value="<?=substr($pg->query,0,strpos($pg->query,"limit"))?>" />
<input type="hidden" name="type" value="" />
<input type="hidden" name="m_id" value="" /><!-- ȸ�� m_id -->
<input type="hidden" name="level" value="<?=$_GET[slevel]?>" /><!-- �ּҷ� �׷� -->

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="14"></td></tr>
<tr class="rndbg">
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev');" class="white">����</a></th>
	<th>��ȣ</th>
	<th>�̸�</th>
	<th>���̵�</th>
	<th>�׷�</th>
	<th>������</th>
	<th>���űݾ�</th>
	<th>�湮Ƚ��</th>
	<th>������</th>
	<th>����</th>
	<th>SMS������</th>
</tr>
<tr><td class="rnd" colspan="14"></td></tr>
<col width="5%" align="center">
<col width="5%" align="center">
<col width="10%" align="center">
<col width="10%" align="center">
<col width="15%" align="left">
<col width="12%" align="center">
<col width="13%" align="left">
<col width="10%" align="center">
<col width="8%" align="center">
<col width="4%" align="center">
<col width="8%" align="center">


<?
while ($data=$db->fetch($res)) {
	$msg_sms = ( $data['sms'] == 'y' ? '����' : '�ź�' );
?>
<tr height=30 align="center">
	<td class="noline"><input type="checkbox" name="chk[]" value="<?=$data['m_no']?>" onclick="iciSelect(this);"></td>
	<td><font class="ver81" color="#616161"><?=$pg->idx--?></font></td>
	<td><span id="navig" name="navig" m_id="<?=$data['m_id']?>" m_no="<?=$data['m_no']?>"><font class="small1" color="#0074ba"><b><?=$data['name']?></b></font></span></td>
	<td><span id="navig" name="navig" m_id="<?=$data['m_id']?>" m_no="<?=$data['m_no']?>"><font class="ver81" color="#0074ba"><b><?=$data['m_id']?></b></font></span><?if($data['nickname']){?><br /><div style="padding-top:2"><img src="../img/icon_nic.gif" align="absmiddle" /><font class="small1" color="#7070b8"><?=$data['nickname']?></font></div><?}?></td>
	<td><font class="def"><?=$r_grp[$data['level']]?></font></td>
	<td align="center"><a href="javascript:popupLayer('../member/popup.emoney.php?m_no=<?=$data['m_no']?>',600,500);"><font class="ver81" color="#0074ba"><b><?=number_format($data['emoney'])?></b>��</font></a></td>
	<td align="center"><a href="javascript:popup('../member/orderlist.php?m_no=<?=$data['m_no']?>',500,600);"><font class="ver81" color="#0074ba"><b><?=number_format($data['sum_sale'])?></b>��</font></a></td>
	<td><font class="ver81" color="#616161"><?=$data['cnt_login']?></font></td>
	<td><font class="ver81" color="#616161"><?=substr($data['regdt'],0,10)?></font></td>

	<td><font class="small" color="#616161"><?=$msg_sms?></font></td>
	<td><a href="javascript:void(0);" onClick="sendSMS('<?=$data['m_id']?>');"><img src="../img/btn_smsmailsend.gif" align="absmiddle" /></a></td>
</tr>
<tr><td colspan="14" class="rndline"></td></tr>
<? } ?>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td width="20%" height="35" style="padding-left:13px">
<a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev');" class="white"><img src="../img/btn_allchoice.gif" border="0" /></a>
</td>
<td width="60%" align="center"><font class="ver8"><?=$pg->page[navi]?></font></td>
<td width="20%"></td>
</tr></table>

<div style='font:0;height:10'></div>
<div align=center>
<table bgcolor=F7F7F7 width=100%>
<tr>
	<td class=noline width=57% align=right>
	<select name=target_type>
		<option value="3">������ ��󿡰� SMS ������</option>
		<option value="2">�˻��� ��󿡰� SMS ������</option>
	</select>
	</td>
	<td width=43% style="padding-left:10px">
	<a href="javascript:void(0)" onClick="sendSMS()"><img src="../img/btn_today_email_sm.gif"></a>
	</td>
</tr>
</table>
</div>


</form>

<div id="MSG01">
<table cellpadding="1" cellspacing="0" border="0" class="small_ex">
<tr><td><img src="../img/icon_list.gif" align="absmiddle" />�⺻���� ȸ�� �����̿ܿ� ��ü,����ó, ģ������ �ڵ�����ȣ�� ���� �Ҽ� ������, ����, �˻��� ���ؼ� SMS�� ���� �� �ֽ��ϴ�.</td></tr>
</table>
</div>
<script language="JavaScript" type="text/JavaScript">cssRound('MSG01');</script>

<script language="JavaScript" type="text/JavaScript">window.onload = function(){ UNM.inner();};</script>

<? include "../_footer.php"; ?>