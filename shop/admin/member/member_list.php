<?
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

$checked['func'][$_GET['func']]				= "checked";

### ���
if ($_GET['indicate'] == 'search'){
	$db_table = GD_MEMBER;

	if ($_GET['skey'] && $_GET['sword']){
		if ( $_GET['skey']== 'all' ){
			$where[] = "( concat( m_id, name ) like '%".$_GET['sword']."%' or nickname like '%".$_GET['sword']."%' )";
		}
		else $where[] = $_GET['skey'] ." like '%".$_GET['sword']."%'";
	}

	if ($_GET['sstatus']!='') $where[] = "status='".$_GET['sstatus']."'";
	if($_GET['slevel'] == '__null__'){
		$where[] = 'level not in ('.implode(',',array_keys($r_grp)).')';
	}
	else{
		if ($_GET['slevel']!='') $where[] = "level='".$_GET['slevel']."'";
	}

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
			if(strlen($_GET['birthdate'][0]) > 4 && strlen($_GET['birthdate'][1]) > 4) $where[] = "concat(birth_year, birth) between '".$_GET['birthdate'][0]." and ".$_GET['birthdate'][1]."'";
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
	$where[] = "m_id != 'godomall'";
	$where[] = MEMBER_DEFAULT_WHERE;

	$pg = new Page($_GET[page],$_GET[page_num]);
	$pg->setQuery($db_table,$where,$orderby);
	$pg->exec();

	$res = $db->query($pg->query);
}

?>

<script language="JavaScript" type="text/JavaScript">
function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
}
function veiwFunc(fObj)
{
	var func;
	var areas = new Array('emoney', 'level', 'status', 'sms', 'email');
	for (i=0; i < fObj['func'].length; i++){
		if (fObj['func'][i].checked === false) openLayer('obj' + areas[i],'none');
		else if (fObj['func'][i].checked === true){
			openLayer('obj' + areas[i],'block');
			func = fObj['func'][i].value;
		}
	}
	if (func == 'sms') document.ifrmSms.location.reload();
	if (func == 'email'){
		fObj.target = "ifrmEmail";
		fObj.action = "email.php?ifrmScroll=1";
		fObj.submit();
	}
}
function chkFuncForm(fObj)
{
	var func;
	for (i=0; i < fObj['func'].length; i++){
		if (fObj['func'][i].checked === true) func = fObj['func'][i].value;
	}
	if (func == 'email') return false;
	if (fObj['query'].value == ""){
		alert("�ϰ�ó���� ȸ���� ���� �˻��ϼ���.");
		return false;
	}
	if (fObj['type'].value == "select" && isChked(document.getElementsByName('chk[]')) === false){
		if (document.getElementsByName('chk[]').length) document.getElementsByName('chk[]')[0].focus();
		return false;
	}
	if (func == 'emoney' && fObj['emoney'].value == ''){
		alert("�������� �Է��ϼ���.");
		fObj['emoney'].focus();
		return false;
	}
	if (func == 'emoney' && fObj['memo'].value == ''){
		alert("���������� �����ϼ���.");
		fObj['memo'].focus();
		return false;
	}
	if (func == 'level' && fObj['level'].value == ''){
		alert("�׷��� �����ϼ���.");
		fObj['level'].focus();
		return false;
	}
	if (func == 'status' && fObj['status'][0].checked === false && fObj['status'][1].checked === false){
		alert("���ο��θ� �����ϼ���.");
		fObj['status'][0].focus();
		return false;
	}
	fObj.target = (func == 'sms' ? "ifrmHidden" : "_self");
	fObj.action = "../member/indb.php?mode=batch_" + func;
	return true;
}
</script>

<?
if( preg_match('/power.mail.php/',$_SERVER['PHP_SELF']) ){
	$title = "�Ŀ� ���Ϻ�����";
	$action_help = "�˻��� ȸ������ ������ �ϰ��߼��� �� �ֽ��ϴ�.".' <a href="javascript:manual(\''.$guideUrl.'board/view.php?id=member&no=15\')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a>';
	$action_msg = "���� �Ʒ����� ������ �߼��� ȸ���� �˻��մϴ�";
	$mode = 'powermail';
}else if( preg_match('/mail.php/',$_SERVER['PHP_SELF']) ){
	$title = "����/��ü ���Ϻ�����";
	$action_help = "�˻��� ȸ������ ������ �ϰ��߼��� �� �ֽ��ϴ�.".' <a href="javascript:manual(\''.$guideUrl.'board/view.php?id=member&no=11\')"><img src="../img/btn_q.gif" border="0" align="absmiddle"></a>';
	$action_msg = "���� �Ʒ����� ������ �߼��� ȸ���� �˻��մϴ�";
	$mode = 'individualEmail';
}
?>

<form>
<input type="hidden" name="func" value="<?=$_GET['func']?>" />
<input type="hidden" name="indicate" value="search" />

<div class="title title_top"><?=$title?><span><?=$action_help?></span></div>
<?if($freeEmail && $godo[webCode] != 'webhost_outside'){?>
<table border=1 bordercolor=cccccc style="border-collapse:collapse" cellpadding=4 cellspacing=0>
<tr><td>
<table border=3 bordercolor=#cccccc style="border-collapse:collapse">
 <tr>
  <td width=762 height=50 align=center bgcolor=ADFFFE>�ܿ� ���� �߼� : ���� <font face=���� size=5 color=#04062F><b><u><?=number_format($freeEmail)?></u></b></font></span> ��</td>
 </tr>
</table>
</td></tr></table>
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">����/��ü ���Ϻ����� ���񽺴� �Ѵ޿� 3,000�Ǹ� �߼��� �����մϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�Ѵ޿� 3,000�� �߼��Ŀ��� <b>�Ŀ����Ϲ߼��ϱ�</b>�� �̿����ּ���!</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>
<div style="padding-top:5px"></div>
<?}?>
<div style="padding:10 0 5 5"><font class="def1" color="#000000"><b><font size="3">��</font> <?=$action_msg?></b></font></div>
<?
### ȸ�� �˻���
include "./_listForm.php";
?>
<div class="button_top"><input type="image" src="../img/btn_search2.gif" /></div>
<table width="100%">
<tr>
	<td class="pageInfo">
	�� <font class="ver8"><b><?=number_format($total-1)?></b>��, �˻� <b><?=number_format($pg->recode['total'])?></b>��, <b><?=number_format($pg->page['now'])?></b> of <?=number_format($pg->page['total'])?> Pages
	</td>
	<td align="right">
	<select name="sort" onchange="this.form.submit();">
	<option value="regdt desc" <?=$selected['sort']['regdt desc']?>>- ������ ���ġ�</option>
	<option value="regdt asc" <?=$selected['sort']['regdt asc']?>>- ������ ���ġ�</option>
	<option value="last_login desc" <?=$selected['sort']['last_login desc']?>>- �����α��� ���ġ�</option>
	<option value="last_login asc" <?=$selected['sort']['last_login asc']?>>- �����α��� ���ġ�</option>
	<option value="cnt_login desc" <?=$selected['sort']['cnt_login desc']?>>- �湮�� ���ġ�</option>
	<option value="cnt_login asc" <?=$selected['sort']['cnt_login asc']?>>- �湮�� ���ġ�</option>
    <optgroup label="------------"></optgroup>
	<option value="name desc" <?=$selected['sort']['name desc']?>>- �̸� ���ġ�</option>
	<option value="name asc" <?=$selected['sort']['name asc']?>>- �̸� ���ġ�</option>
	<option value="m_id desc" <?=$selected['sort']['m_id desc']?>>- ���̵� ���ġ�</option>
	<option value="m_id asc" <?=$selected['sort']['m_id asc']?>>- ���̵� ���ġ�</option>
    <optgroup label="------------"></optgroup>
	<option value="emoney desc" <?=$selected['sort']['emoney desc']?>>- ������ ���ġ�</option>
	<option value="emoney asc" <?=$selected['sort']['emoney asc']?>>- ������ ���ġ�</option>
	<option value="sum_sale desc" <?=$selected['sort']['sum_sale desc']?>>- ���űݾ� ���ġ�</option>
	<option value="sum_sale asc" <?=$selected['sort']['sum_sale asc']?>>- ���űݾ� ���ġ�</option>
	</select>&nbsp;
	<select name="page_num" onchange="this.form.submit();">
	<?
	$r_pagenum = array(10,20,40,60,100);
	foreach ($r_pagenum as $v){
	?>
	<option value="<?=$v?>" <?=$selected['page_num'][$v]?>><?=$v?>�� ���</option>
	<? } ?>
	</select>
	</td>
</tr>
</table>
</form>

<form name="fmList" method="post" onsubmit="return chkFuncForm(this)">
<input type=hidden name=mode>
<input type=hidden name=query value="<?=substr($pg->query,0,strpos($pg->query,"limit"))?>">
<input type="hidden" name="receiveRefuseType" id="receiveRefuseType" value="">
<input type="hidden" name="receiveRefuseCount" id="receiveRefuseCount" value="" />

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td class=rnd colspan=13></td></tr>
<tr class=rndbg>
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev');getCountActReceiveRefuse('<?php echo $mode; ?>');" class=white>����</a></th>
	<th>��ȣ</th>
	<th>�̸�</th>
	<th>���̵�</th>
	<th>�׷�</th>
	<th>������</th>
	<th>���űݾ�</th>
	<th>�湮��</th>
	<th>������</th>
	<th>�����α���</th>
	<th>���ϸ�</th>
	<th>����</th>
</tr>
<tr><td class=rnd colspan=13></td></tr>
<col width=30 align=center>
<col width=60 align=center>
<col width=80 align=center span=3>
<col width=80 align=right span=2>
<col width=50 align=center>
<col width=80 align=center span=2>
<col width=50 align=center>
<col width=30 align=center>
<?
while (is_resource($res) && $data=$db->fetch($res)){
	$last_login = (substr($data[last_login],0,10)!=date("Y-m-d")) ? substr($data[last_login],0,10) : "<font color=#7070B8>".substr($data[last_login],11)."</font>";
	$status = ( $data[status] == '1' ? '����' : '�̽���' );
	$msg_mailing = ( $data[mailling] == 'y') ? '���' : '�ź�';
	if(empty($r_grp[$data['level']])){
		$r_grp[$data['level']] = '-';
	}
?>
<tr height=30 align="center">
	<td class="noline"><input type=checkbox name=chk[] value="<?=$data[m_no]?>" onclick="iciSelect(this);getCountActReceiveRefuse('<?php echo $mode; ?>');"></td>
	<td><font class=ver81 color=616161><?=$pg->idx--?></font></td>
	<td><span id="navig" name="navig" m_id="<?=$data[m_id]?>" m_no="<?=$data[m_no]?>"><font color=0074BA><b><?=$data[name]?></b></font></span></td>
	<td><span id="navig" name="navig" m_id="<?=$data[m_id]?>" m_no="<?=$data[m_no]?>"><font class=ver81 color=0074BA><b><?=$data[m_id]?></b></font></span></td>
	<td><font class=def><?=$r_grp[$data[level]]?></font></td>
	<td align=center><a href="javascript:popupLayer('../member/popup.emoney.php?m_no=<?=$data[m_no]?>',600,500)"><font class=ver81 color=0074BA><b><?=number_format($data[emoney])?></b>��</font></a></td>
	<td align=center><a href="javascript:popup('../member/orderlist.php?m_no=<?=$data[m_no]?>',500,600)"><font class=ver81 color=0074BA><b><?=number_format($data[sum_sale])?></b>��</font></a></td>
	<td><font class=ver81 color=616161><?=$data[cnt_login]?></font></td>
	<td><font class=ver81 color=616161><?=substr($data[regdt],0,10)?></font></td>
	<td><font class=ver81 color=616161><?=$last_login?></font></td>
	<td><font class=small color=616161><?=$msg_mailing?></font></td>
	<td><font class=small color=616161><?=$status?></font></td>
</tr>
<tr><td colspan=13 class=rndline></td></tr>
<? } ?>
</table>

<table width=100% cellpadding=0 cellspacing=0 border=0>
<tr><td width=6% style="padding-left:7"><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev');getCountActReceiveRefuse('powermail');"><img src="../img/btn_allchoice.gif"></a></td>
<td width=88% align=center><div class=pageNavi><font class=ver8><?=$pg->page[navi]?></font></div></td>
<td width=6%></td>
</tr></table>