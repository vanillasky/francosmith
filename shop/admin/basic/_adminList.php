<?

### �׷�� ��������
unset($r_grp,$where);
$query = "select * from ".GD_MEMBER_GRP;
$res = $db->query($query);
while ($data=$db->fetch($res)) $r_grp[$data['level']] = $data['grpnm'];

if (!$_GET['page_num']) $_GET['page_num'] = 10;
$orderby = ($_GET['sort']) ? $_GET['sort'] : "regdt desc"; # ���� ����

### �����Ҵ�
if(!$_GET['grpType'])$_GET['grpType']=0;
$selected['page_num'][$_GET['page_num']]	= "selected";
$selected['sort'][$orderby]					= "selected";
$selected['skey'][$_GET['skey']]			= "selected";
$checked['grpType'][$_GET['grpType']]		=" checked";

### ���
$db_table = GD_MEMBER;

if ($_GET['skey'] && $_GET['sword']){
	if ( $_GET['skey']== 'all' ){
		$where[] = "( concat( m_id, name ) like '%".$_GET['sword']."%' or nickname like '%".$_GET['sword']."%' )";
	}
	else $where[] = $_GET['skey'] ." like '%".$_GET['sword']."%'";
}

if(!$_GET['grpType']){
	$where[] = "level >= 80";
}else{
	$where[] = "level < 80";
}

$where[] = "m_id != 'godomall'";


$pg = new Page($_GET['page'],$_GET['page_num']);
$pg->setQuery($db_table,$where,$orderby);
$pg->exec();

$res = $db->query($pg->query);

list ($total) = $db->fetch("select count(*) from ".GD_MEMBER." where ".implode(' and ',$where)); # �� ���ڵ��
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
	if (!confirm('������ ���� �Ͻðڽ��ϱ�?')) return;
	fm.target = "_self";
	fm.mode.value = "delete";
	fm.action = "../member/indb.php";
	fm.submit();
}
function modiMember(fm)
{

	if (!confirm('������ ���� �Ͻðڽ��ϱ�?')) return;
	fm.target = "_self";
	fm.mode.value = "adminModify";
	fm.action = "../member/indb.php";
	fm.submit();
}
</script>

<form>

<div style="padding:10 0 5 5;color:#fe5400;"><font color="000000"><b>2. �����ڷ� ������ ȸ���� �˻��ϰ� �����ڱ׷����� �����մϴ�. </b></font><font class="extext" color="#fe5400">(�����ڷ� ������ ����� �ݵ�� �̸� ȸ������ ���ԵǾ� �־�� �մϴ�)</font></div>

<table class="tb">
<col class="cellC" /><col class="cellL" style="width:330" />
<col class="cellC" /><col class="cellL" />
<tr height="30">
	<td>����</td>
	<td>
	<input type="radio" name="grpType" value="0" class="null" <?=$checked['grpType'][0]?> />�����ڱ׷쿡�� �˻�
	&nbsp;&nbsp;&nbsp;<input type="radio" name="grpType" value="1" class="null" <?=$checked['grpType'][1]?> />�Ϲ�ȸ���׷쿡�� �˻�
	</td>
	<td>Ű����</td>
	<td>
	<select name="skey">
	<option value="all" <?=$selected['skey']['all']?>> ���հ˻� </option>
	<option value="name" <?=$selected['skey']['name']?>> ȸ���� </option>
	<option value="m_id" <?=$selected['skey']['m_id']?>> ���̵� </option>
	<option value="email" <?=$selected['skey']['email']?>> �̸��� </option>
	<option value="phone" <?=$selected['skey']['phone']?>> ��ȭ��ȣ </option>
	<option value="mobile" <?=$selected['skey']['mobile']?>> ������ȣ </option>
	</select> <input type="text" name="sword" value="<?=$_GET['sword']?>" style="width:200px" class="line" />
	</td>
</tr>

</table>
<div class="button_top"><input type="image" src="../img/btn_search2.gif" /></div>

<table width="100%">
<tr>
	<td class="pageInfo"><font class=small color=777777>
	�� <b><?=number_format($total)?></b>��, �˻� <b><?=number_format($pg->recode[total])?></b>��, <b><?=number_format($pg->page[now])?></b> of <?=number_format($pg->page[total])?> Pages
	</td>
	<td align=right>
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
	<select name=page_num onchange="this.form.submit()">
	<?
	$r_pagenum = array(10,20,40,60,100);
	foreach ($r_pagenum as $v){
	?>
	<option value="<?=$v?>" <?=$selected['page_num'][$v]?>><?=$v?>�� ���
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
<tr><td class=rnd colspan=14></td></tr>
<tr class=rndbg style="padding-top:2">
	<th width=60><font class="small1"><b>��ȣ</th>
	<th width=100><font class="small1"><b>�̸�</th>
	<th><font class="small1"><b>���̵�</th>
	<th width=60><font class="small1"><b>CRM</th>
	<th><font class="small1"><b>�׷�</th>
	<th><font class="small1"><b>�湮��</th>
	<th><font class="small1"><b>������</th>
	<th><font class="small1"><b>�ֱٷα���</th>
	<th><font class="small1"><b>����</th>
	<th><font class="small1"><b>����</th>
</tr>
<tr><td class=rnd colspan=14></td></tr>

<?
while ($data=$db->fetch($res)){
	$last_login = (substr($data[last_login],0,10)!=date("Y-m-d")) ? substr($data[last_login],0,10) : "<font color=#f54500>".substr($data[last_login],11)."</font>";
	$status = ( $data[status] == '1' ? '����' : '�̽���' );
?>
<tr height="30" align="center">
	<td><font class=ver71 color=616161><?=$pg->idx--?></font></td>
	<td><span id="navig" name="navig" m_id="<?=$data[m_id]?>" m_no="<?=$data[m_no]?>"><font class="small1" color=0074BA><b><?=$data[name]?></b></font></span></td>
	<td><span id="navig" name="navig" m_id="<?=$data[m_id]?>" m_no="<?=$data[m_no]?>"><font class=ver811 color=0074BA><b><?=$data[m_id]?></b></font></span></td>
	<td><a href="javascript:popupLayer('../member/Crm_view.php?m_id=<?=$data['m_id']?>',780,600)"><img src="../img/icon_crmlist<?=$data['sex']?>.gif"></a></td>
	<td><font class=def><select name='level[<?=$data[m_no]?>]' ><?foreach($r_grp as $k => $v){?><option value='<?=$k?>'<?=($k==$data[level])?" selected":""?>><?=$v?></option><?}?></select></font></td>
	<td><font class=ver81 color=616161><?=$data[cnt_login]?></font></td>
	<td><font class=ver81 color=616161><?=substr($data[regdt],0,10)?></font></td>
	<td><font class=ver81 color=616161><?=$last_login?></font></td>
	<td><font class=small color=616161><?=$status?></font></td>
	<td><a href="../member/info.php?m_id=<?=$data[m_id]?>"><img src="../img/i_edit.gif"></a></td>
</tr>
<tr><td colspan=14 class=rndline></td></tr>
<? } ?>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr><td align=center style="padding-top:10"><font class=ver8><?=$pg->page[navi]?></font></td></tr>
<tr><td align=center style="padding:25 0 20 0"><a href="javascript:modiMember(document.fmList)"><img src="../img/btn_save.gif"></a></td>
</tr></table>
<div id=MSG01>
<table cellpadding=1 cellspacing=0 border=0 class=small_ex>
<tr><td><img src="../img/icon_list.gif" align="absmiddle">�̸��� ���̵� Ŭ���ϸ� ȸ�������� �� �� �ֽ��ϴ�.</td></tr>
<tr><td><img src="../img/icon_list.gif" align=absmiddle>�����ڴ� �⺻������ ���θ��� ȸ���� �Ǹ�, �����ڸ� �߰��Ϸ��� ȸ�������� �ش�ȸ���� ������ �׷����� �����Ͻø� �˴ϴ�.</td></tr>
</table>
</div>
<script>cssRound('MSG01')</script>

</form>

<script>window.onload = function(){ UNM.inner();};</script>