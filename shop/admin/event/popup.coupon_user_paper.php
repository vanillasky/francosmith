<?
include "../_header.popup.php";
include "../../lib/page.class.php";

if($_GET['useyn'] == "") $_GET['useyn'] = 0;
$checked['useyn'][$_GET['useyn']] = "checked='checked'";
$selected['skey'][$_GET['skey']] = " selected='selected'";

### ������ ��������
$query = "SELECT sno, coupon_name FROM gd_offline_coupon WHERE sno=".$_GET['sno'];
$row = $db->fetch($query);

if(!$row['sno']){
	echo("<script>opener.location.reload();self.close();</script>");
}

##������Ϲ� ���ȸ�� ����Ʈ
$db_table = 'gd_offline_coupon AS a LEFT JOIN gd_offline_download AS b ON (a.sno = b.coupon_sno) ';
$db_table .= '                      LEFT JOIN gd_coupon_order AS c ON (b.sno = c.downloadsno AND b.m_no = c.m_no) ';
$db_table .= '                      LEFT JOIN gd_offline_paper AS p ON (a.sno = p.coupon_sno AND b.paper_sno = p.sno) ';
$db_table .= '						JOIN gd_member AS d ON (b.m_no = d.m_no) ';
$db_table .= '						LEFT JOIN gd_member_grp AS e ON (d.level = e.level) ';

$where[] = 'a.sno= '.$_GET['sno'];
if ($_GET['skey'] && $_GET['sword']){
	if ( $_GET['skey']== 'all' ){
		$where[] = "( concat( d.m_id, d.name ) like '%".$_GET['sword']."%' or d.nickname like '%".$_GET['sword']."%' )";
	}
	else $where[] = 'd.'.$_GET['skey'] ." like '%".$_GET['sword']."%'";
}

if($_GET['useyn'] == "1") $where[] = "c.regdt is not null";
if($_GET['useyn'] == "2") $where[] = "c.regdt is null";


$pg = new Page($_GET['page']);
$pg -> field = " a.sno, a.coupon_name, b.regdt as down_date, d.name, d.m_id, e.grpnm, c.regdt, p.number, d.dormant_regDate ";
$pg->setQuery($db_table,$where);
$pg->exec();

$res = $db->query($pg->query);
?>
<div class="title title_top">�����߱�/��볻�� : <?=$row['coupon_name']?></div>
<form method=get>
<input type="hidden" name="sno" value="<?= $_GET['sno']?>" />
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
	<th>������ȣ</th>
	<th>�׷�</th>
	<th>�����</th>
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
	if($data['dormant_regDate'] != '0000-00-00 00:00:00') $data['m_id'] = '�޸�ȸ��';
?>
<tr height="30" align="center">
	<td><font class="ver81" color="#616161"><?=$pg->idx--?></font></td>
	<td>
	<font color="#0074ba"><b><?=$data['name']?></b></font>
	</td>
	<td><font class="ver81" color="#0074ba"><b><?=$data['m_id']?></b></font></td>
	<td><?=$data['number']?></td>
	<td><?=$data['grpnm']?></td>
	<td><?=$data['down_date']?></td>
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
<? } ?>
<script>
table_design_load();
</script>