<?

$location = "ȸ������ > ȸ������Ʈ";
include "../_header.php";
include "../../lib/page.class.php";
@include "../../conf/phone.php";

### �׷�� ��������
$query = "select * from ".GD_MEMBER_GRP;
$res = $db->query($query);
while ($data=$db->fetch($res)) $r_grp[$data['level']] = $data['grpnm'];

list ($total) = $db->fetch("select count(*) from ".GD_MEMBER." WHERE " . MEMBER_DEFAULT_WHERE); # �� ���ڵ��

if (!$_GET['page_num']) $_GET['page_num'] = 10;
$orderby = ($_GET['sort']) ? $_GET['sort'] : "regdt desc"; # ���� ����

### �����Ҵ�
$selected['page_num'][$_GET['page_num']]	= "selected";
$selected['sort'][$orderby]					= "selected";
$selected['skey'][$_GET['skey']]			= "selected";
$selected['sstatus'][$_GET['sstatus']]		= "selected";
$selected['slevel'][$_GET['slevel']]		= "selected";
$selected['sunder14'][$_GET['sunder14']]	= "selected";
$selected['sage'][$_GET['sage']]			= "selected";
$selected['birthtype'][$_GET['birthtype']]	= "selected";
$selected['marriyn'][$_GET['marriyn']]		= "selected";
$checked['sex'][$_GET['sex']]				= "checked";
$checked['mailing'][$_GET['mailing']]		= "checked";
$checked['smsyn'][$_GET['smsyn']]			= "checked";
if(is_array($_GET['inflow'])) foreach($_GET['inflow'] as $v) {
	$checked['inflow'][$v]					= "checked";
}

### ���
$db_table = GD_MEMBER;

if ($_GET['skey'] && $_GET['sword']){
	if ( $_GET['skey']== 'all' ){
		$where[] = "( concat( m_id, name, nickname, email, phone, mobile, recommid, company ) like '%".$_GET['sword']."%' or nickname like '%".$_GET['sword']."%' )";
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

if ($_GET['sunder14']!='') $where[] = "under14='".$_GET['sunder14']."'";

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

// ȸ������ ���� ���
if(is_array($_GET['inflow'])) foreach($_GET['inflow'] as $v) {
	if($inflow_where) $inflow_where .= " OR ";
	if($v) $inflow_where .= "inflow = '$v'";
}
if($inflow_where) $where[] = $inflow_where;

# ���ο��� ������ SMS Ȯ�ο�
if ($_GET['mobileYN'] == "y") $where[] = "mobile != ''";

$where[] = "m_id != 'godomall'";
$where[] = MEMBER_DEFAULT_WHERE;

$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->setQuery($db_table,$where,$orderby);
$pg->exec();

$res = $db->query($pg->query);
?>

<script>
function iciSelect(obj)
{
	var row = obj.parentNode.parentNode;
	row.style.background = (obj.checked) ? "#F0F4FF" :"#FFFFFF";
}

function delMember(fm)
{
	if (!isChked(document.getElementsByName('chk[]'))) return;
	if (!confirm('������ �Ͻðڽ��ϱ�?')) return;
	fm.target = "_self";
	fm.mode.value = "delete";
	fm.action = "indb.php";
	fm.submit();
}
</script>
<?getjskPc080();?>

<form>
<?
$tmp = explode('>',$location);
$title = trim($tmp[count($tmp)-1]);
?>
<div class="title title_top"><?=$title?><?if($title != 'ȸ������Ʈ'){?><span>���� �� ���θ��� ��üȸ���� �ľ��ϰ� ������ ���� �� �ֽ��ϴ�</span><?}else{?><span>���� �� ���θ��� ��üȸ���� �ľ��ϰ� �����Ͻ� �� �ֽ��ϴ�</span> <a href="javascript:manual('<?=$guideUrl?>board/view.php?id=member&no=2')"><img src="../img/btn_q.gif" border="0" align="absmiddle" /></a><?}?></div>
<?if($title != 'ȸ������Ʈ'){?><div>�� ���� �ܿ� ���� �߼� ����Ʈ : <?=(3000-getMailCnt())?></div><?}?>
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

<form name="fmList" method="post">
<input type="hidden" name="mode" />
<input type="hidden" name="query" value="<?=substr($pg->query,0,strpos($pg->query,"limit"))?>" />

<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="16"></td></tr>
<tr class="rndbg">
	<th><a href="javascript:chkBox(document.getElementsByName('chk[]'),'rev');" class="white">����</a></th>
	<th>��ȣ</th>
	<th>�̸�</th>
	<th>���̵�</th>
	<th>CRM</th>
	<th>�׷�</th>
	<th>������</th>
	<th>���űݾ�</th>
	<th>�湮��</th>
	<th>������</th>
	<th>����</th>
	<th>����</th>
	<th>�����α���</th>
	<th>���ϸ�</th>
	<th>����</th>
	<th>����</th>
</tr>
<tr><td class="rnd" colspan="16"></td></tr>
<col width="30" align="center">
<col width="30" align="center">
<col width="80" align="center" span="2">
<col width="30" align="center">
<col width="80" align="center">
<col width="80" align="right">
<col width="80" align="right">
<col width="50" align="center">
<col width="80" align="center">
<col width="30" align="center">
<col width="50" align="center">
<col width="80" align="center">
<col width="50" align="center">
<col width="30" align="center">
<col width="30" align="center">
<?
while ($data=$db->fetch($res)){
	$last_login = (substr($data['last_login'],0,10)!=date("Y-m-d")) ? substr($data['last_login'],0,10) : "<font color=#7070B8>".substr($data['last_login'],11)."</font>";
	$status = ( $data['status'] == '1' ? '����' : '�̽���' );
	$msg_mailing = ( $data['mailling'] == 'y') ? '���' : '�ź�';
	$inflow = ( $data['inflow'] ) ? "<img src=\"../img/memIcon_".$data['inflow'].".gif\" align=\"absmiddle\" />" : "";
	$icoUnder14 = ( $data['under14'] == '1' ) ? "<img src=\"../img/ico_under14.gif\" align=\"absmiddle\" title=\"��14�� �̸� ȸ������\" />" : "";
	if(empty($r_grp[$data['level']])){
		$r_grp[$data['level']] = '-';
	}
?>
<tr height=40 align="center">
	<td class="noline"><input type="checkbox" name="chk[]" value="<?=$data['m_no']?>" onclick="iciSelect(this);"></td>
	<td><font class="ver81" color="#616161"><?=$pg->idx--?></font></td>
	<td>
	<span id="navig" name="navig" m_id="<?=$data['m_id']?>" m_no="<?=$data['m_no']?>"><font class="small1" color="#0074ba"><b><?=$data['name']?></b></font></span>
	<div>
		<?php if (strlen($data['connected_sns']) > 0) { foreach(explode(',', $data['connected_sns']) as $socialCode) { ?>
		<img src="../img/ico_member_<?php echo strtolower($socialCode); ?>.gif" style="vertical-align: middle; margin: 0 5px;"/>
		<?php }} ?>
	</div>
	</td>
	<td><span id="navig" name="navig" m_id="<?=$data['m_id']?>" m_no="<?=$data['m_no']?>"><font class="ver81" color="#0074ba"><b><?=$data['m_id']?></b></font></span>
	<?if($data['nickname']){?><br />
	<div style="padding-top:2"><img src="../img/icon_nic.gif" align="absmiddle" /><font class="small1" color="#7070b8"><?=$data['nickname']?></font></div>
	<?}?>
	</td>
	<td><a href="javascript:popupLayer('../member/Crm_view.php?m_id=<?=$data['m_id']?>',780,600);"><img src="../img/icon_crmlist<?=$data['sex']?>.gif" /></a><?getlinkPc080($data['phone'],'phone')?><?getlinkPc080($data['mobile'],'mobile')?></td>
	<td><font class="def"><?=$r_grp[$data['level']]?></font></td>
	<td align="center"><a href="javascript:popupLayer('../member/popup.emoney.php?m_no=<?=$data['m_no']?>',600,500);"><font class="ver81" color="#0074ba"><b><?=number_format($data['emoney'])?></b>��</font></a></td>
	<td align="center"><a href="javascript:popup('../member/orderlist.php?m_no=<?=$data['m_no']?>',500,600);"><font class="ver81" color="#0074ba"><b><?=number_format($data['sum_sale'])?></b>��</font></a></td>
	<td><font class="ver81" color="#616161"><?=$data['cnt_login']?></font></td>
	<td><font class="ver81" color="#616161"><?=substr($data['regdt'],0,10)?></font></td>
	<td><font class="ver81" color="#616161"><?=$inflow?></font></td>
	<td><font class="ver81" color="#616161"><?=$icoUnder14?></font></td>
	<td><font class="ver81" color="#616161"><?=$last_login?></font></td>
	<td><font class="small" color="#616161"><?=$msg_mailing?></font></td>
	<td><font class="small" color="#616161"><?=$status?></font></td>
	<td><a href="info.php?m_id=<?=$data['m_id']?>"><img src="../img/i_edit.gif" /></a></td>
</tr>
<tr><td colspan="16" class="rndline"></td></tr>
<? } ?>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td width="20%" height=35 style="padding-left:13px"><a href="javascript:delMember(document.fmList);"><img src="../img/btn_member_del.gif" border="0" /></a></td>
	<td width="60%" align="center"><font class="ver8"><?=$pg->page['navi']?></font></td>
	<td width="20%"></td>
</tr>
</table>

</form>
<script>window.onload = function(){ UNM.inner();};</script>

<?php
$adminAccountSecureGuideInitStatus = 'off';
include '../basic/adminAccountSecureGuide.php';
?>

<? include "../_footer.php"; ?>