<?
include "../_header.popup.php";
include "../../lib/page.class.php";

if($_GET['useyn'] == "") $_GET['useyn'] = 0;
$checked['useyn'][$_GET['useyn']] = " checked";

### �׷�� ��������
$query = "select * from ".GD_MEMBER_GRP;
$res = $db->query($query);
while ($data=$db->fetch($res)) $r_grp[$data['level']] = $data['grpnm'];

$query = "select a.*,b.level,c.coupon from ".GD_COUPON_APPLY." a left join ".GD_MEMBER_GRP." b on b.sno=a.member_grp_sno left join ".GD_COUPON." c on a.couponcd=c.couponcd where a.sno='".$_GET['applysno']."'";
$row = $db->fetch($query);

if(!$row[sno]){
	echo("<script>opener.location.reload();self.close();</script>");
}

$db_table = GD_MEMBER . " a left join ".GD_COUPON_ORDER . " b on b.applysno='".$_GET['applysno']."' and a.m_no=b.m_no";
if($row['membertype'] == 1){
	$where[] = "level='".$row['level']."'";
	$couponapplynm = "�׷�";
}else if($row['membertype'] == 0){
	$couponapplynm = "��ü";
}else if($row['membertype'] == 2){
	$db_table = GD_COUPON_APPLYMEMBER." c,".GD_MEMBER." a left join ".GD_COUPON_ORDER . " b on a.m_no=b.m_no and b.applysno='".$_GET['applysno']."'";
	$where[] = "c.applysno='".$_GET['applysno']."' and c.m_no = a.m_no";
	$couponapplynm = "����";
}

if ($_GET['skey'] && $_GET['sword']){
	if ( $_GET['skey']== 'all' ){
		$where[] = "( concat( m_id, name ) like '%".$_GET['sword']."%' or nickname like '%".$_GET['sword']."%' )";
	}
	else $where[] = $_GET['skey'] ." like '%".$_GET['sword']."%'";
}

if($_GET['useyn'] == "1") $where[] = "b.regdt is not null";
if($_GET['useyn'] == "2") $where[] = "b.regdt is null";

$pg = new Page($_GET['page']);
$pg -> field = "a.*,b.regdt";
$pg->setQuery($db_table,$where);

$pg->exec();
$res = $db->query($pg->query);
?>
<div class="title title_top">�����߱�/��볻�� : <?=$row[coupon]?> - <?=$couponapplynm?></div>
<form method=get>
<input type="hidden" name="applysno" value="<?=$_GET['applysno']?>">
<table class="tb">
<col class="cellC" /><col class="cellL" style="width:250" />
<tr>
	<td>Ű���� �˻�</td>
	<td><select name="skey">
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
</tr>
<tr>
	<td>��� ����</td>
	<td class="noline"><input type="radio" name="useyn" value="0"<?=$checked['useyn']['0']?> />��ü <input type="radio" name="useyn" value="1"<?=$checked['useyn']['1']?> />��� <input type="radio" name="useyn" value="2"<?=$checked['useyn']['2']?> />�̻�� </td>
</tr>
</table>
<div class="button_top"><input type="image" src="../img/btn_search2.gif" /></div>
</form>
<div style="font:0;padding-top:10"></div>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="12"></td></tr>
<tr class="rndbg">
	<th>��ȣ</th>
	<th>�̸�</th>
	<th>���̵�</th>
	<th>�׷�</th>
	<th>�����</th>
</tr>
<tr><td class="rnd" colspan="12"></td></tr>
<col width="50" align="center">
<col align="center">
<col width="50" align="center">
<col align="center">
<col align="center">
<?
if(is_resource($res)){
while ($data=$db->fetch($res)){
	$btn = "";
	if($row['membertype'] == 2) $btn = "&nbsp;<a href=\"javascript:delApply2(".$row['sno'].",".$data['m_no'].");\"><img src=\"../img/btn_coupon_cancel.gif\" align=\"absmiddle\"></a>";

	if($data['dormant_regDate'] != '0000-00-00 00:00:00'){
		$data['m_id'] = '�޸�ȸ��';
	}
?>
<tr height="30" align="center">
	<td><font class="ver81" color="#616161"><?=$pg->idx--?></font></td>
	<td>
	<font color="#0074ba"><b><?=$data['name']?></b></font>
	</td>
	<td><font class="ver81" color="#0074ba"><b><?=$data['m_id']?></b></font></td>
	<td><?=$r_grp[$data['level']]?></td>
	<td><font class="def"><?=(!$data['regdt'])?"�̻��".$btn:$data['regdt']?></font></td>
</tr>
<tr><td colspan="12" class="rndline"></td></tr>
<? } ?>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td width="20%"></td>
	<td width="60%" align="center"><font class="ver8"><?=$pg->page['navi']?></font></td>
	<td width="20%"></td>
</tr>
</table>
<form name=hiddenform method=post>
	<input type=hidden name=mode>
</form>
<? } ?>
<script>
function delApply2(sno,m_no){
	var f = document.hiddenform;
	f.mode.value = "delApply2";
	f.action = "indb.coupon.php?couponcd=<?=$_GET['couponcd']?>&sno="+sno+"&m_no="+m_no;
	f.submit();
}
table_design_load();
</script>