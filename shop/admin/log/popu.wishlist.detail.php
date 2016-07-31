<?
include "../_header.popup.php";
include "../../lib/page.class.php";

$goodsno = $_GET[goodsno];

### ���� ����
$_GET[sword] = trim($_GET[sword]);

$year = ($_GET[year]) ? $_GET[year] : date("Y");
$month = ($_GET[month]) ? sprintf("%02d",$_GET[month]) : date("m");

$stype = ($_GET[stype]) ? $_GET[stype] : 'm';
$sdate_s = ($_GET[regdt][0]) ? $_GET[regdt][0] : date('Ymd',strtotime('-7 day'));
$sdate_e = ($_GET[regdt][1]) ? $_GET[regdt][1] : date('Ymd');

if (checkStatisticsDateRange($sdate_s, $sdate_e) > 365) {
	msg('��ȸ�Ⱓ ������ �ִ� 1���� ���� ���մϴ�. �Ⱓ Ȯ���� �缳�� ���ּ���.',$_SERVER['PHP_SELF']);exit;
}

$srunout = ($_GET[srunout]) ? $_GET[srunout] : '';

$date = $year."-".sprintf("%02d",$month);
$last = date("t",strtotime($date."-01"));

if ($stype == 'm') {
	$where[] = " DATE_FORMAT(WS.regdt, '%Y-%m') = '$date' ";
}
else if ($sdate_s & $sdate_e){
	$where[] = " ( DATE_FORMAT(WS.regdt,'%Y%m%d') >= '".($sdate_s)."' and DATE_FORMAT(WS.regdt,'%Y%m%d') <= '".($sdate_e)."')";
}

### �׷�� ��������
$query = "select * from ".GD_MEMBER_GRP;
$res = $db->query($query);
while ($data=$db->fetch($res)) $r_grp[$data['level']] = $data['grpnm'];

$pg = new Page($_GET[page],$_GET[page_num]);
$where[] = "WS.goodsno = '$goodsno'";
$pg->field = " DISTINCT MB.m_id, MB.* ";
$db_table = "
gd_member_wishlist AS WS
INNER JOIN gd_member AS MB
ON WS.m_no = MB.m_no
";

$orderby = " MB.regdt DESC";
$groupby = " GROUP BY WS.m_no";
$pg->setQuery($db_table,$where,$orderby,$groupby);

$pg->exec();

$res = $db->query($pg->query);

?>
<div class="title title_top">�� ��ǰ�� ��ǰ�����Կ� ���� �� ����Ʈ</div>
<table width=100% cellpadding=0 cellspacing=0>
<tr>
	<td class=pageInfo><font class=ver8>
	�� <b><?=$pg->recode[total]?></b>��, <b><?=$pg->page[now]?></b> of <?=$pg->page[total]?> Pages
	</td>
</tr>
</table>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
<tr><td class="rnd" colspan="15"></td></tr>
<tr class="rndbg">
	<th>��ȣ</th>
	<th>�̸�</th>
	<th>���̵�</th>
	<th>�׷�</th>
	<th>���űݾ�</th>
	<th>�湮��</th>
	<th>������</th>
	<th>�����α���</th>
</tr>
<tr><td class="rnd" colspan="15"></td></tr>
<col width="30" align="center">
<col width="80" align="center" span="2">
<col width="30" align="center">
<col width="80" align="center">
<col width="80" align="center">
<col width="80" align="center">
<col width="80" align="center">
<col width="80" align="center">

<?
while ($data=$db->fetch($res)){
	$last_login = (substr($data['last_login'],0,10)!=date("Y-m-d")) ? substr($data['last_login'],0,10) : "<font color=#7070B8>".substr($data['last_login'],11)."</font>";
	$status = ( $data['status'] == '1' ? '����' : '�̽���' );
	$msg_mailing = ( $data['mailling'] == 'y') ? '���' : '�ź�';
	$inflow = ( $data['inflow'] ) ? "<img src=\"../img/memIcon_".$data['inflow'].".gif\" align=\"absmiddle\" />" : "";

	if($data['dormant_regDate'] == '0000-00-00 00:00:00'){
		$name = "<span id='navig' name='navig' m_id='".$data['m_id']."' m_no='".$data['m_no']."'><font class='small1' color='#0074ba'><strong>".$data['name']."</strong></font></span>";
		$m_id = "<span id='navig' name='navig' m_id='".$data['m_id']."' m_no='".$data['m_no']."'><font class='ver81' color='#7070b8'><strong>".$data['m_id']."</strong></font><img src='../img/icon_crmlist".$data['sex'].".gif' /></span>";
	}
	else {
		$name = "";
		$m_id = "<font class='ver81' color='#7070b8'><strong>�޸�ȸ��</strong></font>";
	}
?>
<tr height=40 align="center">
	<td><font class="ver81" color="#616161"><?=$pg->idx--?></font></td>
	<td><?php echo $name; ?></td>
	<td><?php echo $m_id; ?>
	<?if($data['nickname']){?><br />
	<div style="padding-top:2"><img src="../img/icon_nic.gif" align="absmiddle" /><font class="small1" color="#7070b8"><?=$data['nickname']?></font></div>
	<?}?>
	</td>
	<td><font class="def"><?=$r_grp[$data['level']]?></font></td>
	<td align="center"><a href="javascript:popup('../member/orderlist.php?m_no=<?=$data['m_no']?>',500,600);"><font class="ver81" color="#0074ba"><b><?=number_format($data['sum_sale'])?></b>��</font></a></td>
	<td><font class="ver81" color="#616161"><?=$data['cnt_login']?></font></td>
	<td><font class="ver81" color="#616161"><?=substr($data['regdt'],0,10)?></font></td>
	<td><font class="ver81" color="#616161"><?=$last_login?></font></td>
</tr>
<tr><td colspan="15" class="rndline"></td></tr>
<? } ?>
</table>

<table cellpadding="0" cellspacing="0" border="0" width="100%">
<tr>
	<td align="center"><font class="ver8"><?=$pg->page['navi']?></font></td>
</tr>
</table>

</form>


<script>window.onload = function(){ UNM.inner();};</script>
